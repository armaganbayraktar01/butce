<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// =================================================================================
// 1. DÖNEM VE ZAMAN YÖNETİMİ
// =================================================================================
if (!isset($_SESSION['aktif_donem']) || empty($_SESSION['aktif_donem'])) {
    $en_guncel_sorgu = $db->query("SELECT aktif_donem FROM butce_akis ORDER BY aktif_donem DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $_SESSION['aktif_donem'] = ($en_guncel_sorgu && !empty($en_guncel_sorgu['aktif_donem'])) ? $en_guncel_sorgu['aktif_donem'] : date('Y-m');
}

$aktif_donem = $_SESSION['aktif_donem'];

// A. Kredi Kartları Gelecek Dönem Verileri ($kkart Matrisi)
$cc_last = $db->query("SELECT * FROM kredi_kartlari_last")->fetchAll(PDO::FETCH_ASSOC);
$kkart = [];
foreach ($cc_last as $kartname) {
    $k_key = ($kartname['kart_adi'] === 'vakifbank') ? 'vakifbank' : 'isbank';
    $kkart[$k_key] = [
        'id'          => (int)$kartname['id'],
        'usd_borcu'   => floatval($kartname['usd_borcu']),
        'eu_borcu'    => floatval($kartname['eu_borcu']),
        'kart_limiti' => floatval($kartname['kart_limiti']),
        'donem_ici'   => floatval($kartname['donem_ici'])
    ];
}

// B. Finansal Havuzlar (🎯 REFORM: İşbank A, İşbank Ö ve Nakit Ev havuzları birlikte listelenir)
$havuz_sorgu = $db->query("SELECT * FROM finansal_havuzlar WHERE havuz_kodu IN ('isbank_a', 'isbank_o', 'nakit') ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);


// =================================================================================
// 2. KREDİ KARTI DÖVİZ BORCU ÖDEME MOTORU (POST İSTEKLERİ)
// =================================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'pay_currency_debt') {
    
    $havuz_id   = (int)($_POST['havuz_id'] ?? 0);
    $kart_id    = (int)($_POST['kart_id'] ?? 0);
    $doviz_turu = trim($_POST['doviz_turu'] ?? ''); // 'USD' veya 'EUR'
    $odenecek   = floatval($_POST['miktar'] ?? 0);

    // Güvenlik Duvarı: Eksik veya hatalı parametre koruması
    if ($havuz_id <= 0 || $kart_id <= 0 || !in_array($doviz_turu, ['USD', 'EUR']) || $odenecek <= 0) {
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz parametre girdisi sağlandı.']);
        exit;
    }

    try {
        // Finansal Sızdırmazlık Kalkanı (Transaction Başlat)
        $db->beginTransaction();

        // 1. Seçilen Havuzun Döviz Bakiyesini ve Bilgilerini Çek (Güncel Durum Kilidi)
        $chk_havuz = $db->prepare("SELECT * FROM finansal_havuzlar WHERE id = :id FOR UPDATE");
        $chk_havuz->execute(['id' => $havuz_id]);
        $havuz_data = $chk_havuz->fetch(PDO::FETCH_ASSOC);

        // 2. Kredi Kartı Gelecek Dönem Satırını Çek
        $chk_kart = $db->prepare("SELECT * FROM kredi_kartlari_last WHERE id = :id FOR UPDATE");
        $chk_kart->execute(['id' => $kart_id]);
        $kart_data = $chk_kart->fetch(PDO::FETCH_ASSOC);

        if (!$havuz_data || !$kart_data) {
            throw new Exception('Seçilen hesap veya kredi kartı veritabanında bulunamadı.');
        }

        // Döviz birimine göre dinamik sütun eşlemesi
        $havuz_sutun = ($doviz_turu === 'USD') ? 'usd_bakiye' : 'eu_bakiye';
        $kart_sutun  = ($doviz_turu === 'USD') ? 'usd_borcu' : 'eu_borcu';

        $mevcut_havuz_bakiye = floatval($havuz_data[$havuz_sutun]);
        $mevcut_kart_borcu   = floatval($kart_data[$kart_sutun]);

        // 3. Emniyet Bariyerleri: Aşım ve Yetersiz Bakiye Kontrolleri
        if ($odenecek > $mevcut_havuz_bakiye) {
            throw new Exception('Seçilen havuz hesabında yeterli döviz bakiyesi bulunmuyor.');
        }
        if ($odenecek > $mevcut_kart_borcu) {
            throw new Exception('Ödemek istediğiniz miktar, güncel kart borcundan büyük olamaz.');
        }

        // 4. Havuz Hesabından Döviz Düşümü (Banka veya Nakit fark etmeksizin ilgili sütun eritilir)
        $yeni_havuz_bakiye = $mevcut_havuz_bakiye - $odenecek;
        $up_havuz = $db->prepare("UPDATE finansal_havuzlar SET {$havuz_sutun} = :yeni WHERE id = :id");
        $up_havuz->execute(['yeni' => $yeni_havuz_bakiye, 'id' => $havuz_id]);

        // 5. Kredi Kartı Last Tablosunda Döviz Borcunu Azalt
        $yeni_kart_borcu = $mevcut_kart_borcu - $odenecek;
        
        // Kartın konsolide dövizi (TL Karşılığı) sütununu canlı kurlarla yeniden hesapla
        $usd_kur = floatval($live_kurlar['USD_SATIS'] ?? 34.50);
        $eur_kur = floatval($live_kurlar['EUR_SATIS'] ?? 37.80);
        
        $hesapli_usd = ($doviz_turu === 'USD') ? $yeni_kart_borcu : floatval($kart_data['usd_borcu']);
        $hesapli_eur = ($doviz_turu === 'EUR') ? $yeni_kart_borcu : floatval($kart_data['eu_borcu']);
        $yeni_tl_karsiligi = ($hesapli_usd * $usd_kur) + ($hesapli_eur * $eur_kur);

        $up_kart = $db->prepare("UPDATE kredi_kartlari_last SET {$kart_sutun} = :yeni, doviz_borcu = :tl_karsilik WHERE id = :id");
        $up_kart->execute(['yeni' => $yeni_kart_borcu, 'tl_karsilik' => $yeni_tl_karsiligi, 'id' => $kart_id]);

        // 6. Forex İşlemler Defterine "Ödeme (1)" Olarak Mühürle
        $guncel_kur_maliyeti = ($doviz_turu === 'USD') ? $usd_kur : $eur_kur;
        $toplam_tl_maliyet   = $odenecek * $guncel_kur_maliyeti;

        $ins_forex = $db->prepare("INSERT INTO forex_islemler (doviz_cinsi, miktar, islem_turu, doviz_kuru, tutar, islem_tarihi) VALUES (:dcinsi, :miktar, 1, :kur, :tutar, NOW())");
        $ins_forex->execute([
            'dcinsi' => $doviz_turu,
            'miktar' => $odenecek,
            'kur'    => $guncel_kur_maliyeti,
            'tutar'  => $toplam_tl_maliyet
        ]);

        // İşlemleri Onayla ve Kilidi Kaldır
        $db->commit();

        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'success', 'message' => 'Döviz borç ödeme işlemi başarıyla deftere işlendi.']);
        exit;

    } catch (Exception $e) {
        $db->rollBack();
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

// 🏛️ GELECEK DÖNEM DÖVİZ BORÇ YÖNETİMİ GÖRÜNÜMÜNÜN YÜKLENMESİ
require admin_view('currency_debt');
