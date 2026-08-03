
<?php

if (!isset($_SESSION)) {
    session_start();
}

$faiz_stopaj = 17.5;

// 🎯 CONTROLLER EN TEPE SİGORTASI: Ekstra yazılan tüm projeksiyon verilerini oturumdan çağır
$isbank_ekstra_donem_ici   = $_SESSION['last_isbank_ekstra_donem_ici']   ?? 0.00;
$isbank_ekstra_provizyon   = $_SESSION['last_isbank_ekstra_provizyon']   ?? 0.00;
$isbank_ekstra_doviz       = $_SESSION['last_isbank_ekstra_doviz']       ?? 0.00;
$isbank_ekstra_harcama_limiti = $_SESSION['last_isbank_ekstra_harcama_limiti'] ?? 0.00; // 🎯 İSİM KİLİDİ DÜZELTİLDİ

$vakif_ekstra_donem_ici    = $_SESSION['last_vakif_ekstra_donem_ici']    ?? 0.00;
$vakif_ekstra_provizyon    = $_SESSION['last_vakif_ekstra_provizyon']    ?? 0.00;
$vakif_ekstra_doviz        = $_SESSION['last_vakif_ekstra_doviz']        ?? 0.00;
$vakif_ekstra_harcama_limiti  = $_SESSION['last_vakif_ekstra_harcama_limiti']  ?? 0.00; // 🎯 İSİM KİLİDİ DÜZELTİLDİ


// 🎯 PROJEKSİYON ZAMAN MOTORU: Aktif dönemi oku ve otomatik olarak 1 ay sonrasını hesapla
$aktif_donem = $_SESSION['aktif_donem'] ?? '2026-07'; 
list($current_year, $current_month) = explode('-', $aktif_donem);

$next_month_int = intval($current_month) + 1;
$next_year_int = intval($current_year);
if ($next_month_int > 12) {
    $next_month_int = 1;
    $next_year_int++;
}
// Planlanan Projeksiyon Dönemi (Örn: 2026-08)
$projeksiyon_donemi = $next_year_int . '-' . sprintf("%02d", $next_month_int);
$projeksiyon_ay_index = $next_month_int;

// Manuel Form Parametrelerinin Yakalanması (Yalnızca Projeksiyon Oturumunda Saklanır)
$faiz_orani          = isset($_POST['faiz_orani']) ? floatval($_POST['faiz_orani']) : ($_SESSION['last_faiz_orani'] ?? 30.00);
$faiz_gun_sayisi     = 0; // Monitör sayfasında gün sayısı 0'a sabitlenerek kafa karışıklığı önlenir
$kredi_input_butcesi = isset($_POST['faiz_kredi_butcesi']) ? floatval($_POST['faiz_kredi_butcesi']) : ($_SESSION['last_saf_kredi_input_butcesi'] ?? 53200.00);

$_SESSION['last_faiz_orani']              = $faiz_orani;
$_SESSION['last_faiz_gun_sayisi']         = $faiz_gun_sayisi;
$_SESSION['last_saf_kredi_input_butcesi'] = $kredi_input_butcesi;




// 🎯 2. MONİTÖR ASENKRON PLAN GÜNCELLEME VE TASLAK SAKLAMA MOTORU (POST İSTEKLERİ)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $islem = $_POST['islem'] ?? '';

    // 🎯 REFORM: Tek bir ortak asenkron istek ile gelen tüm hücre girdilerini yakala ve oturumda mühürle
    if (isset($_POST['projeksiyon_kart_bileseni'])) {
        $kart = $_POST['kart_turu'];  // 'isbank' veya 'vakif'
        $alan = $_POST['alan_turu'];  // 'donem_ici', 'provizyon', 'doviz', 'harcama_limiti'
        $val  = floatval($_POST['deger']);
        
        $_SESSION['last_' . $kart . '_ekstra_' . $alan] = $val;
        
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'success']);
        exit;
    }

    // // 🎯 REFORM: 6 bağımsız kart hücresinden (Dönem içi, provizyon, döviz) gelen canlı simülasyon girdilerini yakala
    // if (isset($_POST['projeksiyon_kart_bileseni'])) {
    //     $kart = $_POST['kart_turu'];  // 'isbank' veya 'vakif'
    //     $alan = $_POST['alan_turu'];  // 'donem_ici', 'provizyon', 'doviz'
    //     $val  = floatval($_POST['deger']);
        
    //     $_SESSION['last_' . $kart . '_ekstra_' . $alan] = $val;
        
    //     while (ob_get_level() > 0) { ob_end_clean(); }
    //     header('Content-Type: application/json; charset=utf-8');
    //     echo json_encode(['status' => 'success']);
    //     exit;
    // }
    
    // Projeksiyon faiz simülasyonu inputları değiştiğinde parametreleri oturumda kilitle
    if ($islem === 'last_faiz_guncelle') {
        if (isset($_POST['faiz_orani']))         $_SESSION['last_faiz_orani'] = floatval($_POST['faiz_orani']);
        if (isset($_POST['faiz_gun_sayisi']))     $_SESSION['last_faiz_gun_sayisi'] = intval($_POST['faiz_gun_sayisi']);
        if (isset($_POST['faiz_kredi_butcesi']))  $_SESSION['last_saf_kredi_input_butcesi'] = floatval($_POST['faiz_kredi_butcesi']);
        
        echo json_encode(['status' => 'success']);
        exit;
    }
    
    // Projeksiyon ekranından geleceğe yönelik taslak kalem ekleme (butce_akis_last tablosuna)
    if ($islem === 'yeni_taslak_kalem_ekle') {
        $adi   = trim($_POST['kalem_adi'] ?? ''); 
        $tutar = floatval($_POST['tutar'] ?? 0); 
        $turu  = $_POST['turu'] === 'gelir' ? 'gelir' : 'gider';
        
        if ($adi !== '' && $tutar > 0) {
            $db->prepare("INSERT INTO butce_akis_last (aktif_donem, kalem_adi, tutar, odenen_tutar, turu) VALUES (:donem, :adi, :tutar, 0.00, :turu)")
               ->execute(['donem' => $projeksiyon_donemi, 'adi' => $adi, 'tutar' => $tutar, 'turu' => $turu]);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
        exit;
    }
    
    // Projeksiyondan taslak bütçe kalemi silme motoru
    if ($islem === 'taslak_kalem_sil') {
        $t_id = intval($_POST['id'] ?? 0);
        if ($t_id > 0) {
            $db->prepare("DELETE FROM butce_akis_last WHERE id = :id")->execute(['id' => $t_id]);
            echo json_encode(['status' => 'success']);
        }
        exit;
    }
}
// 🎯 3. GELECEK AYIN TASLAK ŞABLONUNUN OTOMATİK HAZIRLANMASI (DRAFT GENERATOR)
$stmt_last = $db->prepare("SELECT * FROM butce_akis_last WHERE aktif_donem = :donem ORDER BY id ASC");
$stmt_last->execute(['donem' => $projeksiyon_donemi]);
$taslak_kalemler = $stmt_last->fetchAll(PDO::FETCH_ASSOC);

// Eğer butce_akis_last tablosu tamamen boşsa, ana tablodaki kalıcı şablonları buraya kopyala (İlk Hazırlık)
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

// 🎯 4. PROJEKSİYON BLOKE VE FATURA SENSÖRLERİ SÜRECİ
$isbank_a_anapara  = 0.00; $isbank_o_anapara  = 0.00;
$vakif_o_anapara   = 0.00; $nakit_anapara     = 0.00;
$vakif_bloke       = 0.00; $nakit_bloke       = 0.00;

foreach ($taslak_kalemler as $t_kalem) {
    $isim    = trim($t_kalem['kalem_adi']);
    $tutar   = floatval($t_kalem['tutar']);
    $turu    = $t_kalem['turu'];
    $k_kucuk = mb_strtolower($isim, 'UTF-8');
    
    if ($turu === 'gider') {
        if ($isim === 'Aidat') { 
            $nakit_bloke += $tutar; 
        }
        elseif (strpos($k_kucuk, 'damacana') !== false || strpos($k_kucuk, 'içme suyu') !== false) { 
            $nakit_bloke += $tutar; 
        }
        else {
            $is_fatura = false;
            foreach (['faturası', 'izsu', 'elektrik', 'su', 'doğalgaz', 'telefon', 'internet'] as $sk) {
                if (strpos($k_kucuk, $sk) !== false) { $is_fatura = true; break; }
            }
            if ($is_fatura) { $vakif_bloke += $tutar; }
        }
    }
}


// =================================================================================
// 🎯 5. GELECEK AYIN KREDİ KARTI VE KÜRESEL TAKSİT AYRIŞTIRMA MOTORU
// =================================================================================
$db_taksitler = $db->query("SELECT * FROM cc_taksitler ORDER BY ay_index ASC")->fetchAll(PDO::FETCH_ASSOC);

$isbank_aktif_taksit = 0.00; $isbank_kalan_taksitler_toplami = 0.00;
$vakif_aktif_taksit  = 0.00; $vakif_kalan_taksitler_toplami  = 0.00;

foreach ($db_taksitler as $t_satir) {
    $t_ay = intval($t_satir['ay_index']);
    
    if ($t_ay === $projeksiyon_ay_index) {
        // A. Projeksiyon ayına ait olan taksit CARİ borçtur
        $isbank_aktif_taksit = floatval($t_satir['isbank_tutar']);
        $vakif_aktif_taksit  = floatval($t_satir['vakif_tutar']);
    } 
    elseif ($t_ay > $projeksiyon_ay_index) {
        // B. 🎯 REFORM: Projeksiyon ayı HARİÇ sonraki tüm aylar KALAN TAKSİT toplamıdır!
        $isbank_kalan_taksitler_toplami += floatval($t_satir['isbank_tutar']);
        $vakif_kalan_taksitler_toplami  += floatval($t_satir['vakif_tutar']);
    }
}

// Bankadaki canlı provizyon harcamalarını oku
$cc_last = $db->query("SELECT * FROM kredi_kartlari_last")->fetchAll(PDO::FETCH_ASSOC);
$cc_meta_last = [];
foreach ($cc_last as $kart) {
    $k_adi = ($kart['kart_adi'] === 'vakifbank') ? 'vakif' : 'isbank';
    $cc_meta_last[$k_adi . '_donem_ici'] = floatval($kart['donem_ici']);
    $cc_meta_last[$k_adi . '_provizyon'] = floatval($kart['provizyon']);
    $cc_meta_last[$k_adi . '_doviz']     = floatval($kart['doviz_borcu']);
}


// =================================================================================
// 🎯 6. YENİ FİNANSAL ANAYASA KURAL MOTORU (TAM SIRALI KÜMÜLATİF SENSÖRLER)
// =================================================================================

// A. Kümülatif toplam taslak gelirlerin sensörü (Hataları önlemek için döngü en tepeye mühürlendi)
$taslak_toplam_gelir = 0.00;
foreach ($taslak_kalemler as $t_kalem) {
    if ($t_kalem['turu'] === 'gelir') {
        $taslak_toplam_gelir += floatval($t_kalem['tutar']);
    }
}

// B. Kümülatif toplam taslak gider sensörleri (Sabit bloke bütçelerinin okunması)
$sabit_faturalar_toplami = floatval($vakif_bloke); 
$nakit_su_aidat_toplami  = floatval($nakit_bloke); 

// 🎯 KART BORCU PROJEKSİYON DENKLEMLERİ (6 BİLEŞEN + 2 HARCAMA LİMİTİ DOĞRUDAN SENKRON)
$isbank_card_borcu = $cc_meta_last['isbank_donem_ici'] + $isbank_ekstra_donem_ici 
                   + $cc_meta_last['isbank_provizyon'] + $isbank_ekstra_provizyon 
                   + $cc_meta_last['isbank_doviz']     + $isbank_ekstra_doviz 
                   + $isbank_aktif_taksit + $isbank_ekstra_harcama_limiti; // 🎯 DEĞİŞKEN GÜNCELLENDİ

$vakif_card_borcu  = $cc_meta_last['vakif_donem_ici']  + $vakif_ekstra_donem_ici 
                   + $cc_meta_last['vakif_provizyon']  + $vakif_ekstra_provizyon 
                   + $cc_meta_last['vakif_doviz']      + $vakif_ekstra_doviz 
                   + $vakif_aktif_taksit + $vakif_ekstra_harcama_limiti; // 🎯 DEĞİŞKEN GÜNCELLENDİ


// D. Sarkan Kalan Taksitlerin Limit ve Tahmini Yük Hesaplarına Enjeksiyonu
$isbank_nihai_toplam_borc = $isbank_card_borcu + $isbank_kalan_taksitler_toplami;
$vakif_nihai_toplam_borc  = $vakif_card_borcu + $vakif_kalan_taksitler_toplami;

$total_kk_borc_yuku = $isbank_card_borcu + $vakif_card_borcu;

// E. Toplam Projeksiyon Gider Matrisi (Yatırım cüzdanını eritecek olan cari bütçe yüküdür)
$taslak_toplam_gider = $kredi_input_butcesi + $sabit_faturalar_toplami + $nakit_su_aidat_toplami + $total_kk_borc_yuku;


// =================================================================================
// 🎯 7. HAVUZ ANAPARALARININ REEL MÜHÜRLERİ (SABİT VE ERİYEN HAVUZLAR)
// =================================================================================

// Kredi Ö havuzu faiz inputundan gelen bütçeyle çakılı kalır
$isbank_o_faizsiz_ana_para = $kredi_input_butcesi; 

// Yatırım A havuzu her şey düşüldükten sonra kalan net hacme kuruşu kuruşuna kilitlenir!
$isbank_k_faizsiz_ana_para = max(0, $taslak_toplam_gelir - $taslak_toplam_gider); 


// 🎯 8. PROJEKSİYON GELECEĞE YÖNELİK NEMALANDIRMA SİMÜLASYONU
$ana_havuz_faiz    = ($isbank_k_faizsiz_ana_para > 0) ? ($isbank_k_faizsiz_ana_para * ($faiz_orani / 100) / 365) * $faiz_gun_sayisi : 0;
$kredi_havuzu_faiz = ($isbank_o_faizsiz_ana_para > 0) ? ($isbank_o_faizsiz_ana_para * ($faiz_orani / 100) / 365) * $faiz_gun_sayisi : 0;

$isbank_a_nema = $ana_havuz_faiz * (1 - ($faiz_stopaj / 100));
$isbank_o_nema = $kredi_havuzu_faiz * (1 - ($faiz_stopaj / 100));

// Nihai Ön Yüz Bakiyeleri
$isbank_a_bakiye  = $isbank_k_faizsiz_ana_para + $isbank_a_nema;
$isbank_o_bakiye  = $isbank_o_faizsiz_ana_para + $isbank_o_nema;

// Arayüz listesi için taslak dizilerini ayrıştır
$gelirler = []; $giderler = [];
foreach ($taslak_kalemler as $t_kalem) {
    if ($t_kalem['turu'] === 'gelir') { $gelirler[] = $t_kalem; } else { $giderler[] = $t_kalem; }
}


// =================================================================================
// 🛡️ VIEW KATMANI ENTEGRASYON KÖPRÜLERİ (GÜVENLİK REFORMLARI)
// =================================================================================
$aktif_yil     = $next_year_int;
$aktif_ay_str  = sprintf("%02d", $next_month_int);

$vakifbank_bakiye            = $vakif_bloke;
$zorunlu_fatura_bloke_tutari = $vakif_bloke;
$zorunlu_su_bloke_tutari     = $nakit_bloke;

$cc_meta = [
    'isbank_donem_ici'  => $cc_meta_last['isbank_donem_ici'] + $isbank_ekstra_donem_ici,
    'isbank_provizyon'  => $cc_meta_last['isbank_provizyon'] + $isbank_ekstra_provizyon,
    'isbank_doviz'      => $cc_meta_last['isbank_doviz'] + $isbank_ekstra_doviz,
    'isbank_ekstre'     => 0.00, 
    'vakif_donem_ici'   => $cc_meta_last['vakif_donem_ici'] + $vakif_ekstra_donem_ici,
    'vakif_provizyon'   => $cc_meta_last['vakif_provizyon'] + $vakif_ekstra_provizyon,
    'vakif_doviz'       => $cc_meta_last['vakif_doviz'] + $vakif_ekstra_doviz,
    'vakif_ekstre'      => 0.00  
];

// 🏛️ GELECEK DÖNEM MONİTÖR GÖRÜNÜMÜNÜN YÜKLENMESİ
require admin_view('management_last');









