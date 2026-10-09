
<?php


if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// ===============================================================================
// 1. DÖNEM VE ZAMAN YÖNETİMİ
// ===============================================================================
if (!isset($_SESSION['aktif_donem']) || empty($_SESSION['aktif_donem'])) {
    $en_guncel_sorgu = $db->query("SELECT aktif_donem FROM butce_akis ORDER BY aktif_donem DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $_SESSION['aktif_donem'] = ($en_guncel_sorgu && !empty($en_guncel_sorgu['aktif_donem'])) ? $en_guncel_sorgu['aktif_donem'] : date('Y-m');
}
$aktif_donem = $_SESSION['aktif_donem'];
$ay_isimleri = [1=>'OCAK', 2=>'ŞUBAT', 3=>'MART', 4=>'NİSAN', 5=>'MAYIS', 6=>'HAZİRAN', 7=>'TEMMUZ', 8=>'AĞUSTOS', 9=>'EYLÜL', 10=>'EKİM', 11=>'KASIM', 12=>'ARALIK'];
list($current_year, $current_month) = explode('-', $aktif_donem);
$next_month_int = intval($current_month) + 1;
$next_year_int = intval($current_year);
if ($next_month_int > 12) {
    $next_month_int = 1;
    $next_year_int++;
}

// PROJEKSİYON ZAMAN MOTORU
$projeksiyon_donemi = $next_year_int . '-' . sprintf("%02d", $next_month_int);
$projeksiyon_ay_index = $next_month_int;

// Faiz ve Nemalandırma için Fabrika Ayarları Sabitleri
$faiz_orani = 30.00;
$faiz_gun_sayisi = 0; 
$faiz_stopaj = 17.5;
$kredi_input_butcesi = 53200.00; 

// Canlı Döviz Kurlarını Çekme Motoru
$kur_servis_management = ForexExchange::getLiveRates();
$live_kurlar = $kur_servis_management['kurlar'] ?? []; 
$usd_kur = floatval($live_kurlar['USD_SATIS'] ?? 34.50);
$eur_kur = floatval($live_kurlar['EUR_SATIS'] ?? 37.80);

// ===============================================================================
// 2. MONİTÖR ASENKRON PLAN GÜNCELLEME VE TASLAK SAKLAMA MOTORU (POST İSTEKLERİ)
// ===============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $islem = $_POST['islem'] ?? '';

    // REFORM: Gelecek dönem kart hücre girdilerini 'kredi_kartlari_last' tablosunda mühürle
    if (isset($_POST['projeksiyon_kart_bileseni'])) {
        $kart = trim($_POST['kart_turu']); // 'isbank' veya 'vakifbank'
        $alan = trim($_POST['alan_turu']); // 'donem_ici', 'eu_borcu', 'usd_borcu', 'doviz_borcu' vb.
        $val = floatval($_POST['deger']);

        // Sütun ve Kart Beyaz Listesi REFORMU (Yeni döviz alanları eklenerek SQL Injection Bariyeri genişletildi)
        $izin_verilen_alanlar = [
            'donem_ici', 'provizyon', 'doviz_borcu', 'kesinlesen_ekstre', 
            'kart_limiti', 'kullanilabilir_limit', 'harcanabilir_limit',
            'eu_borcu', 'usd_borcu'
        ];
        $izin_verilen_kartlar = ['isbank', 'vakifbank'];

        if (!in_array($alan, $izin_verilen_alanlar) || !in_array($kart, $izin_verilen_kartlar)) {
            while (ob_get_level() > 0) { ob_end_clean(); }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'message' => 'Geçersiz parametre.']);
            exit;
        }

        if ($val < 0) { $val = 0.00; } // Negatif veri giriş bariyeri

        try {
            // 1. Adım: İlgili hücreyi doğrudan güncelle (Giriş Yapılan Hücreyi Mühürle)
            $stmt = $db->prepare("UPDATE kredi_kartlari_last SET {$alan} = :val WHERE kart_adi = :kart");
            $stmt->execute(['val' => $val, 'kart' => $kart]);
            $_SESSION['last_' . $kart . '_ekstra_' . $alan] = $val;

            // 2. Adım: Eğer girilen veri Euro veya Dolar borcu ise, doviz_borcu sütununu otomatik yeniden hesapla
            if ($alan === 'eu_borcu' || $alan === 'usd_borcu') {
                // Güncel satırı veritabanından oku
                $stmt_read = $db->prepare("SELECT eu_borcu, usd_borcu FROM kredi_kartlari_last WHERE kart_adi = :kart");
                $stmt_read->execute(['kart' => $kart]);
                $kart_data = $stmt_read->fetch(PDO::FETCH_ASSOC);

                $hesapli_usd = floatval($kart_data['usd_borcu'] ?? 0);
                $hesapli_eur = floatval($kart_data['eu_borcu'] ?? 0);
                
                // Canlı kurlarla TL Karşılığı Konsolide döviz hesabı
                $yeni_tl_karsiligi = ($hesapli_usd * $usd_kur) + ($hesapli_eur * $eur_kur);

                // doviz_borcu alanını otomatik güncelle
                $stmt_calc = $db->prepare("UPDATE kredi_kartlari_last SET doviz_borcu = :yeni_tl WHERE kart_adi = :kart");
                $stmt_calc->execute(['yeni_tl' => $yeni_tl_karsiligi, 'kart' => $kart]);
                $_SESSION['last_' . $kart . '_ekstra_doviz_borcu'] = $yeni_tl_karsiligi;
            }

            while (ob_get_level() > 0) { ob_end_clean(); }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'success', 'message' => 'Last table synchronized.']);
            exit;
        } catch (PDOException $e) {
            while (ob_get_level() > 0) { ob_end_clean(); }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'message' => 'DB Hatası.']);
            exit;
        }
    }
}

// ===============================================================================
// 3. BÜTÇE AKIŞ LAST TABLOSUNUN VERİLERİNİN ÇEKİMİ (DRAFT GENERATOR)
// ===============================================================================
$stmt_last = $db->prepare("SELECT * FROM butce_akis_last WHERE aktif_donem = :donem ORDER BY id ASC");
$stmt_last->execute(['donem' => $projeksiyon_donemi]);
$taslak_kalemler = $stmt_last->fetchAll(PDO::FETCH_ASSOC);

if (empty($taslak_kalemler)) {
    $stmt_sabitle = $db->prepare("SELECT * FROM butce_akis WHERE aktif_donem = :donem AND kalem_adi != 'Evdeki Nakit Girişi' AND kalem_adi NOT LIKE '%Havuz Transfer Fonu%' AND is_locked = 0");
    $stmt_sabitle->execute(['donem' => $aktif_donem]);
    $ana_sabitler = $stmt_sabitle->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($ana_sabitler as $s_kalem) {
        $db->prepare("INSERT INTO butce_akis_last (aktif_donem, kalem_adi, tutar, odenen_tutar, turu, is_locked) VALUES (:donem, :adi, :tutar, 0.00, :turu, 0)")
           ->execute(['donem' => $projeksiyon_donemi, 'adi' => $s_kalem['kalem_adi'], 'tutar' => $s_kalem['tutar'], 'turu' => $s_kalem['turu']]);
    }
    
    $stmt_last->execute(['donem' => $projeksiyon_donemi]);
    $taslak_kalemler = $stmt_last->fetchAll(PDO::FETCH_ASSOC);
}

// ===============================================================================
// 4. PROJEKSİYON BLOKE VE FATURA SENSÖRLERİ SÜRECİ
// ===============================================================================
$isbank_a_anapara = 0.00; $isbank_o_anapara = 0.00;
$vakif_o_anapara = 0.00; $nakit_anapara = 0.00;
$vakif_bloke = 0.00; $nakit_bloke = 0.00;

foreach ($taslak_kalemler as $t_kalem) {
    $isim = trim($t_kalem['kalem_adi']);
    $tutar = floatval($t_kalem['tutar']);
    $turu = $t_kalem['turu'];
    $k_kucuk = mb_strtolower($isim, 'UTF-8');
    
    if ($turu === 'gider') {
        if ($isim === 'Aidat') { 
            $nakit_bloke += $tutar; 
        } elseif (strpos($k_kucuk, 'damacana') !== false || strpos($k_kucuk, 'içme suyu') !== false) { 
            $nakit_bloke += $tutar; 
        } else {
            $is_fatura = false;
            foreach (['faturası', 'izsu', 'elektrik', 'su', 'doğalgaz', 'telefon', 'internet'] as $sk) {
                if (strpos($k_kucuk, $sk) !== false) { $is_fatura = true; break; }
            }
            if ($is_fatura) { $vakif_bloke += $tutar; }
        }
    }
}

// ===============================================================================
// 5. GELECEK AYIN KREDİ KARTI VE KÜRESEL TAKSİT AYRIŞTIRMA MOTORU
// ===============================================================================
$db_taksitler = $db->query("SELECT * FROM cc_taksitler ORDER BY ay_index ASC")->fetchAll(PDO::FETCH_ASSOC);
$isbank_aktif_taksit = 0.00; $isbank_kalan_taksitler_toplami = 0.00;
$vakif_aktif_taksit = 0.00; $vakif_kalan_taksitler_toplami = 0.00;

foreach ($db_taksitler as $t_satir) {
    $t_ay = intval($t_satir['ay_index']);
    
    if ($t_ay === $projeksiyon_ay_index) {
        $isbank_aktif_taksit = floatval($t_satir['isbank_tutar']);
        $vakif_aktif_taksit = floatval($t_satir['vakif_tutar']);
    } elseif ($t_ay > $projeksiyon_ay_index) {
        $isbank_kalan_taksitler_toplami += floatval($t_satir['isbank_tutar']);
        $vakif_kalan_taksitler_toplami += floatval($t_satir['vakif_tutar']);
    }
}

// ===============================================================================
// 6. KKART MATRİS OKUMASI VE FİNANSAL ANAYASA KURAL MOTORU
// ===============================================================================
$kredikarti = $db->query("SELECT * FROM kredi_kartlari_last")->fetchAll(PDO::FETCH_ASSOC);

// REFORM: Sayfa açılışında döviz borçlarını canlı kurlarla kontrol et ve veritabanını güncelle
foreach ($kredikarti as $index => $kart) {
    $k_usd = floatval($kart['usd_borcu']);
    $k_eur = floatval($kart['eu_borcu']);
    
    // Otomatik hesaplanan güncel kur karşılığı
    $otomatik_tl_karsiligi = ($k_usd * $usd_kur) + ($k_eur * $eur_kur);
    
    // HATA ÖNLEME BARİYERİ: Sayfa açılışında (GET sürecinde) döviz borcu eşitsizliğini 
    // sadece mevcut istek bir POST güncellemesi DEĞİLSE denetle.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        if (floatval($kart['doviz_borcu']) != $otomatik_tl_karsiligi) {
            $stmt_sync = $db->prepare("UPDATE kredi_kartlari_last SET doviz_borcu = :otomatik_tl WHERE id = :id");
            $stmt_sync->execute(['otomatik_tl' => $otomatik_tl_karsiligi, 'id' => $kart['id']]);
            
            // Çalışma zamanındaki mevcut dizi hücresini de senkronize et
            $kredikarti[$index]['doviz_borcu'] = $otomatik_tl_karsiligi;
        }
    }
}

// Güncellenmiş temiz verilerle $kkart matrisini mühürleme adımı
$kkart = [];
foreach ($kredikarti as $kart) {
    $k_anahtar = ($kart['kart_adi'] === 'vakifbank') ? 'vakifbank' : 'isbank';
    
    $kkart[$k_anahtar] = [
        'id'                   => (int)$kart['id'],
        'donem_ici'            => floatval($kart['donem_ici']),
        'provizyon'            => floatval($kart['provizyon']),
        'usd_borcu'            => floatval($kart['usd_borcu']),
        'eu_borcu'             => floatval($kart['eu_borcu']),
        'doviz_borcu'          => floatval($kart['doviz_borcu']), 
        'kesinlesen_ekstre'    => floatval($kart['kesinlesen_ekstre']),
        'kart_limiti'          => floatval($kart['kart_limiti']),
        'kullanilabilir_limit' => floatval($kart['kullanilabilir_limit']),
        'harcanabilir_limit'   => floatval($kart['harcanabilir_limit']),
        'usd_borc_tl'          => floatval($kart['usd_borcu']) * $usd_kur,
        'eu_borc_tl'           => floatval($kart['eu_borcu']) * $eur_kur
    ];
}


// A. Kümülatif toplam taslak gelirlerin sensörü
$taslak_toplam_gelir = 0.00;
foreach ($taslak_kalemler as $t_kalem) {
    if ($t_kalem['turu'] === 'gelir') {
        $taslak_toplam_gelir += floatval($t_kalem['tutar']);
    }
}

// B. Kümülatif toplam taslak gider sensörleri
$sabit_faturalar_toplami = floatval($vakif_bloke); 
$nakit_su_aidat_toplami = floatval($nakit_bloke); 

// C. Saf $kkart matrisi üzerinden kümülatif kart yükü denklemleri
$isbank_card_borcu = ($kkart['isbank']['donem_ici'] ?? 0.00) 
 + ($kkart['isbank']['provizyon'] ?? 0.00) 
 + ($kkart['isbank']['doviz_borcu'] ?? 0.00) 
 + floatval($isbank_aktif_taksit) 
 + ($kkart['isbank']['kesinlesen_ekstre'] ?? 0.00);

$vakif_card_borcu = ($kkart['vakifbank']['donem_ici'] ?? 0.00) 
 + ($kkart['vakifbank']['provizyon'] ?? 0.00) 
 + ($kkart['vakifbank']['doviz_borcu'] ?? 0.00) 
 + floatval($vakif_aktif_taksit) 
 + ($kkart['vakifbank']['kesinlesen_ekstre'] ?? 0.00);

// $isbank_harcanabilir_bakiye = floatval($kkart['isbank']['harcanabilir_limit']) - $isbank_card_borcu;
// $vakif_harcanabilir_bakiye = floatval($kkart['vakifbank']['harcanabilir_limit']) - $vakif_card_borcu;

$isbank_nihai_toplam_borc = $isbank_card_borcu + $isbank_kalan_taksitler_toplami;
$vakif_nihai_toplam_borc = $vakif_card_borcu + $vakif_kalan_taksitler_toplami;
$total_kk_borc_yuku = $isbank_card_borcu + $vakif_card_borcu;

// D. Toplam Projeksiyon Gider Matrisi
$taslak_toplam_gider = $kredi_input_butcesi + $sabit_faturalar_toplami + $nakit_su_aidat_toplami + $total_kk_borc_yuku;
// ===============================================================================
// 7. HAVUZ ANAPARALARININ REEL MÜHÜRLERİ VE NEMALANDIRMA SİMÜLASYONU
// ===============================================================================
$isbank_o_faizsiz_ana_para = $kredi_input_butcesi; 
$isbank_k_faizsiz_ana_para = max(0, $taslak_toplam_gelir - $taslak_toplam_gider); 

// Faiz Getirisi Denklemleri
$ana_havuz_faiz = ($isbank_k_faizsiz_ana_para > 0) ? ($isbank_k_faizsiz_ana_para * ($faiz_orani / 100) / 365) * $faiz_gun_sayisi : 0;
$kredi_havuzu_faiz = ($isbank_o_faizsiz_ana_para > 0) ? ($isbank_o_faizsiz_ana_para * ($faiz_orani / 100) / 365) * $faiz_gun_sayisi : 0;

// Stopaj Kesintisi Sonrası Net Nemalandırma Sinyalleri
$isbank_a_nema = $ana_havuz_faiz * (1 - ($faiz_stopaj / 100));
$isbank_o_nema = $kredi_havuzu_faiz * (1 - ($faiz_stopaj / 100));

// Nihai Havuz Bakiyeleri
$isbank_a_bakiye = $isbank_k_faizsiz_ana_para + $isbank_a_nema;
$isbank_o_bakiye = $isbank_o_faizsiz_ana_para + $isbank_o_nema;

// Taslak Kalemlerin Arayüz Gruplarına Ayrıştırılması
$gelirler = []; 
$giderler = [];
foreach ($taslak_kalemler as $t_kalem) {
    if ($t_kalem['turu'] === 'gelir') { 
        $gelirler[] = $t_kalem; 
    } else { 
        $giderler[] = $t_kalem; 
    }
}

// GELECEK DÖNEM MONİTÖR GÖRÜNÜMÜNÜN YÜKLENMESİ 🏛
require admin_view('management_next');












