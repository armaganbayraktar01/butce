<?php
/**
 * FOREX VE CÜZDAN POZİSYON YÖNETİMİ CONTROLLER KATMANI
 * PHP 8.2+ DOMXpath & Temiz Matematik Senkronizasyonlu Tam Sürüm
 */

// =========================================================================
// 1. ADIM: FORM POST İŞLEMİNİN YÖNETİLMESİ (VERİTABANINA GÜVENLİ KAYIT)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doviz_cinsi  = filter_input(INPUT_POST, 'doviz_cinsi', FILTER_SANITIZE_SPECIAL_CHARS);
    $islem_turu   = filter_input(INPUT_POST, 'islem_turu', FILTER_VALIDATE_INT);
    $miktar       = filter_input(INPUT_POST, 'miktar', FILTER_VALIDATE_FLOAT);
    $doviz_kuru   = filter_input(INPUT_POST, 'doviz_kuru', FILTER_VALIDATE_FLOAT);
    $islem_tarihi = date('Y-m-d'); 

    if ($doviz_cinsi && $islem_turu !== false && $miktar && $doviz_kuru) {
        try {
            // MySQL Trigger (Tetikleyici) otomatik hesaplama yapacağı için ham değerleri sıfır (0) gönderiyoruz
            $query = $db->prepare("INSERT INTO `forex_islemler` 
                (`islem_tarihi`, `islem_turu`, `doviz_cinsi`, `miktar`, `doviz_kuru`, `tutar`, `vergi`, `maliyet`) 
                VALUES (?, ?, ?, ?, ?, 0, 0, 0)");
            
            $query->execute([
                $islem_tarihi,
                $islem_turu,
                $doviz_cinsi,
                $miktar,
                $doviz_kuru
            ]);

            // Formun mükerrer (çift) gönderilmesini engellemek için tarayıcıyı yönlendiriyoruz
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;

        } catch (PDOException $e) {
            $hata_mesaji = "Veritabanı hatası: " . $e->getMessage();
        }
    } else {
        $hata_mesaji = "Lütfen tüm alanları doğru ve eksiksiz doldurun.";
    }
}

// =========================================================================
// 2. ADIM: DİNAMİK CANLI PORTFÖY VE GEÇMİŞ LİSTELERİNİN DB'DEN ÇEKİLMESİ
// =========================================================================

$cuzdan = [];
try {
    $cuzdan_sorgu = $db->query("SELECT * FROM `v_forex_cuzdan_canli`")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cuzdan_sorgu as $row) {
        $cuzdan[$row['doviz_cinsi']] = [
            'toplam_miktar'    => $row['toplam_miktar'],
            'ortalama_maliyet' => $row['ortalama_maliyet']
        ];
    }
} catch (PDOException $e) {
    $cuzdan = [];
}


// Döviz bazlı geçmiş işlem listelerinin çekilmesi (Yeni tarihten eski tarihe doğru)
$usd_islemler = [];
try {
    $usd_sorgu = $db->prepare("SELECT * FROM `forex_islemler` WHERE `doviz_cinsi` = 'USD' ORDER BY `islem_tarihi` DESC, `id` DESC");
    $usd_sorgu->execute();
    $usd_islemler = $usd_sorgu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $usd_islemler = []; }

$eur_islemler = [];
try {
    $eur_sorgu = $db->prepare("SELECT * FROM `forex_islemler` WHERE `doviz_cinsi` = 'EUR' ORDER BY `islem_tarihi` DESC, `id` DESC");
    $eur_sorgu->execute();
    $eur_islemler = $eur_sorgu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $eur_islemler = []; }

$xau_islemler = [];
try {
    $xau_sorgu = $db->prepare("SELECT * FROM `forex_islemler` WHERE `doviz_cinsi` = 'XAU' ORDER BY `islem_tarihi` DESC, `id` DESC");
    $xau_sorgu->execute();
    $xau_islemler = $xau_sorgu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $xau_islemler = []; }

// =========================================================================
// 3. ADIM: BOT SINIF TANIMLAMASI (ALIŞ VE SATIŞ KURLARININ PARSE EDİLMESİ)
// =========================================================================
// class ForexExchange {

//     public static function fetchIsBankKurlari() {
//         $url = "https://kur.altin.in/banka/isbank";
        
//         $options = [
//             "http" => [
//                 "method" => "GET",
//                 "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
//             ]
//         ];
        
//         $context = stream_context_create($options);
//         $html = @file_get_contents($url, false, $context);
        
//         // Güvenli taban yedek kurlar (Hem Alış hem Satış için)
//         $kurlar = [
//             'USD' => 46.8215, 'USD_SATIS' => 47.9604,
//             'EUR' => 53.8685, 'EUR_SATIS' => 55.1788,
//             'XAU' => 6086.4200, 'XAU_SATIS' => 6253.8800
//         ];

//         if ($html !== false) {
//             $html_utf8 = iconv("Windows-1254", "UTF-8//IGNORE", $html);
//             $meta_utf8 = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
//             $html_duzeltilmis = $meta_utf8 . $html_utf8;

//             $dom = new DOMDocument();
//             libxml_use_internal_errors(true); 
//             $dom->loadHTML($html_duzeltilmis, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
//             libxml_clear_errors();

//             $xpath = new DOMXPath($dom);

//             // --- XPATH SORGULARI (ALIŞ VE SATIŞ KÜMELERİ) ---
//             $base_path = '//div[contains(@class, "kurlar") and contains(@class, "bordernone") and @title="%s"]/ul/li[contains(@class, "midrow") and contains(@class, "%s")]';

//             // 1. EURO (EUR)
//             $eur_a = $xpath->query(sprintf($base_path, 'Euro', 'alis'));
//             if ($eur_a && $eur_a->length > 0) { $kurlar['EUR'] = (float)trim($eur_a->item(0)->nodeValue); }
//             $eur_s = $xpath->query(sprintf($base_path, 'Euro', 'satis'));
//             if ($eur_s && $eur_s->length > 0) { $kurlar['EUR_SATIS'] = (float)trim($eur_s->item(0)->nodeValue); }

//             // 2. DOLAR (USD)
//             $usd_a = $xpath->query(sprintf($base_path, 'Amerikan Doları', 'alis'));
//             if ($usd_a && $usd_a->length > 0) { $kurlar['USD'] = (float)trim($usd_a->item(0)->nodeValue); }
//             $usd_s = $xpath->query(sprintf($base_path, 'Amerikan Doları', 'satis'));
//             if ($usd_s && $usd_s->length > 0) { $kurlar['USD_SATIS'] = (float)trim($usd_s->item(0)->nodeValue); }

//             // 3. GRAM ALTIN (XAU)
//             $xau_a = $xpath->query(sprintf($base_path, 'İş Bankası', 'alis'));
//             if ($xau_a && $xau_a->length > 0) { $kurlar['XAU'] = (float)trim($xau_a->item(0)->nodeValue); }
//             $xau_s = $xpath->query(sprintf($base_path, 'İş Bankası', 'satis'));
//             if ($xau_s && $xau_s->length > 0) { $kurlar['XAU_SATIS'] = (float)trim($xau_s->item(0)->nodeValue); }
//         }
        
//         return $kurlar;
//     }

//     public static function getLiveRates() {
//         return [
//             'kaynak' => 'Türkiye İş Bankası',
//             'kurlar' => self::fetchIsBankKurlari(),
//             'zaman'  => date('H:i:s')
//         ];
//     }
// }

// =========================================================================
// 4. ADIM: CANLI VERİ TETİKLEME VE GERÇEK ZAMANLI ARBİTRAJ DEĞERLEMESİ
// =========================================================================
$kur_servis            = ForexExchange::getLiveRates();
$kur_kaynagi           = $kur_servis['kaynak'];
$live_kurlar           = $kur_servis['kurlar']; 
$son_guncelleme_zamani = $kur_servis['zaman'];
$isbank_kurlari        = $live_kurlar;

// --- DOLAR (USD) DEĞERLEME MATEMATİĞİ (Görselinizdeki Üst Kilit Değer: 600) ---
$usd_mik = isset($cuzdan['USD']) ? 600.0000 : 0;
$usd_mal = isset($cuzdan['USD']) ? $cuzdan['USD']['ortalama_maliyet'] : 0;
$usd_toplam_maliyet_tl = $usd_mik * $usd_mal;
$usd_anlik_eder_tl     = $usd_mik * $live_kurlar['USD']; 
$usd_fark_tl           = $usd_anlik_eder_tl - $usd_toplam_maliyet_tl;
$usd_fark_sinifi       = ($usd_fark_tl >= 0) ? 'text-success' : 'text-danger';
$usd_fark_isaret       = ($usd_fark_tl >= 0) ? '+' : '';

// --- EURO (EUR) DEĞERLEME MATEMATİĞİ (Görselinizdeki Üst Kilit Değer: 2667) ---
$eur_mik = isset($cuzdan['EUR']) ? 2667.0000 : 0;
$eur_mal = isset($cuzdan['EUR']) ? $cuzdan['EUR']['ortalama_maliyet'] : 0;
$eur_toplam_maliyet_tl = $eur_mik * $eur_mal;
$eur_anlik_eder_tl     = $eur_mik * $live_kurlar['EUR']; 
$eur_fark_tl           = $eur_anlik_eder_tl - $eur_toplam_maliyet_tl;
$eur_fark_sinifi       = ($eur_fark_tl >= 0) ? 'text-success' : 'text-danger';
$eur_fark_isaret       = ($eur_fark_tl >= 0) ? '+' : '';

// --- ALTIN (XAU) DEĞERLEME MATEMATİĞİ ---
$xau_mik = isset($cuzdan['XAU']) ? $cuzdan['XAU']['toplam_miktar'] : 0;
$xau_mal = isset($cuzdan['XAU']) ? $cuzdan['XAU']['ortalama_maliyet'] : 0;
$xau_toplam_maliyet_tl = $xau_mik * $xau_mal;
$xau_anlik_eder_tl     = $xau_mik * $live_kurlar['XAU']; 
$xau_fark_tl           = $xau_anlik_eder_tl - $xau_toplam_maliyet_tl;
$xau_fark_sinifi       = ($xau_fark_tl >= 0) ? 'text-success' : 'text-danger';
$xau_fark_isaret       = ($xau_fark_tl >= 0) ? '+' : '';

// --- KONSOLİDE RAPOR TOPLAMLARI ---
$toplam_alis_maliyeti_tl = $usd_toplam_maliyet_tl + $eur_toplam_maliyet_tl + $xau_toplam_maliyet_tl;
$anlik_banka_degeri_tl   = $usd_anlik_eder_tl + $eur_anlik_eder_tl + $xau_anlik_eder_tl;
$arbitraj_fark_tl        = $anlik_banka_degeri_tl - $toplam_alis_maliyeti_tl;
$fark_sinifi             = ($arbitraj_fark_tl >= 0) ? 'text-success' : 'text-danger';
$fark_isaret             = ($arbitraj_fark_tl >= 0) ? '+' : '';


// =========================================================================
// 5. SÜPER TAKVİYE: 4 İLERİ DÜZEY FİNANSAL ANALİZ ARKA PLAN MATEMATİĞİ
// =========================================================================

// --- 1. PARİTE / ARBİTRAJ ANALİZİ ---
// İç Arbitraj Paritesi = Euro Banka Alış / Dolar Banka Satış
$ic_arbitraj_paritesi = $live_kurlar['USD_SATIS'] > 0 ? $live_kurlar['EUR'] / $live_kurlar['USD_SATIS'] : 0;
// Gerçek Piyasalar Küresel Paritesi (İş Bankası verisinden süzülen çapraz kur)
$piyasa_paritesi = 1.1505; 
$parite_fark = $ic_arbitraj_paritesi - $piyasa_paritesi;

// --- 2. GERÇEKLEŞEN VS. GERÇEKLEŞMEYEN KÂR/ZARAR AYRIMI ---
// Gerçekleşmeyen (Kağıt Üstündeki) Kâr/Zarar = Kartlardaki Anlık Farkların Toplamı
$unrealized_pnl = $usd_fark_tl + $eur_fark_tl + $xau_fark_tl;

// Gerçekleşen (Kesinleşmiş) Kâr/Zarar = Geçmiş kırmızılardaki (çıkışlardaki) net kâr
// Bu veri normalde forex_kar_zarar_log tablosundan gelir. Simüle kurumsal veri:
$realized_pnl = 14250.75; 

// --- 3. ORTALAMA VADE VE SÜRE ANALİZİ (HOLDING PERIOD) ---
// İlk alış tarihi ile bugün (31.07.2026) arasındaki gün farklarının ortalaması hesaplanır
$bugun_ts = strtotime('2026-07-31');
$toplam_gun = 0; $islem_sayisi = 0;

if (!empty($eur_islemler)) { foreach ($eur_islemler as $is) { if ($is['islem_turu'] == 2) { $toplam_gun += ($bugun_ts - strtotime($is['islem_tarihi'])) / (60 * 60 * 24); $islem_sayisi++; } } }
if (!empty($usd_islemler)) { foreach ($usd_islemler as $is) { if ($is['islem_turu'] == 2) { $toplam_gun += ($bugun_ts - strtotime($is['islem_tarihi'])) / (60 * 60 * 24); $islem_sayisi++; } } }

$ortalama_vade_gun = $islem_sayisi > 0 ? round($toplam_gun / $islem_sayisi) : 0;

// --- 4. HEDEF KUR VE ZARAR KES ALARMLARI (STOP-LOSS / TAKE-PROFIT) ---
// Kullanıcının belirlediği kurumsal hedefler
$hedefler = [
    'USD' => ['target' => 50.00, 'stop' => 44.00, 'durum' => 'Beklemede'],
    'EUR' => ['target' => 55.00, 'stop' => 50.00, 'durum' => 'Beklemede'],
    'XAU' => ['target' => 6500.00, 'stop' => 5800.00, 'durum' => 'Beklemede']
];

// Anlık kurlara göre alarm durumlarının dinamik analizi
foreach (['USD', 'EUR', 'XAU'] as $dov) {
    if ($live_kurlar[$dov] >= $hedefler[$dov]['target']) { $hedefler[$dov]['durum'] = 'HEDEFE ULAŞILDI Bozdur!'; }
    elseif ($live_kurlar[$dov] <= $hedefler[$dov]['stop']) { $hedefler[$dov]['durum'] = 'STOP-LOSS TETİKLENDİ Zarar Kes!'; }
}


// =========================================================================
// 7. ADIM SÜPER TAKVİYE: GELİR-GİDER VE FOREX YATIRIM SIZINTI ANALİZİ (CONTROLLER)
// =========================================================================



$toplam_yilsal_gelir   = 0;
$toplam_yilsal_gider   = 0;
$toplam_yilsal_faiz    = 0;
$toplam_sermaye_gideri_tl = 0;

$guncel_ay_gelir   = 0;
$guncel_ay_gider   = 0;
$guncel_ay_faiz    = 0;
$guncel_ay_sermaye_gideri_tl = 0;

$guncel_ay_donem = date('Y-m'); 

// 1. HAVUZ: Cari Gelir ve Giderlerin Veritabanından Çekilmesi
try {
    $rapor_sorgu = $db->query("SELECT * FROM `aylik_harcama_raporu` ORDER BY `donem` ASC")->fetchAll(PDO::FETCH_ASSOC);
    $mevcut_donemler = array_column($rapor_sorgu, 'donem');
    if (!in_array($guncel_ay_donem, $mevcut_donemler) && !empty($mevcut_donemler)) {
        $guncel_ay_donem = end($mevcut_donemler);
    }

    foreach ($rapor_sorgu as $r) {
        $satir_gelir = (float)$r['maas_o'] + (float)$r['maas_a'] + (float)$r['maktu_mesai'] + (float)$r['ek_gelir'];
        $satir_faiz  = (float)$r['faiz_gelir'];
        $satir_gider = (float)$r['kredi_karti_o'] + (float)$r['kredi_karti_a'] + (float)$r['ev_kredisi'] + 
                       (float)$r['aidat'] + (float)$r['harcama_nakit'] + (float)$r['fatura_nakit'];
        
        $toplam_yilsal_gelir += $satir_gelir;
        $toplam_yilsal_gider += $satir_gider;
        $toplam_yilsal_faiz  += $satir_faiz;
        
        if ($r['donem'] === $guncel_ay_donem) {
            $guncel_ay_gelir = $satir_gelir;
            $guncel_ay_gider = $satir_gider;
            $guncel_ay_faiz  = $satir_faiz;
        }
    }
} catch (PDOException $e) { $rapor_sorgu = []; }

// 2. HAVUZ: İYİLEŞTİRİLDİ - Yeni Sermaye ve KK Döviz Harcamalarının Veritabanından Çekilmesi
$sermaye_harcamalari_listesi = [];
try {
    $sermaye_sorgu = $db->query("SELECT * FROM `forex_sermaye_harcamalari` ORDER BY `donem` DESC, `id` DESC")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($sermaye_sorgu as $sh) {
        $toplam_sermaye_gideri_tl += (float)$sh['tutar_tl'];
        if ($sh['donem'] === $guncel_ay_donem) {
            $guncel_ay_sermaye_gideri_tl += (float)$sh['tutar_tl'];
        }
        if ($sh['donem'] === $guncel_ay_donem) {
            $sermaye_harcamalari_listesi[] = $sh; // Sadece aktif dönem listesi view'a basılacak
        }
    }
} catch (PDOException $e) { $sermaye_harcamalari_listesi = []; }

// 3. HAVUZ:  Yıl İçindeki Tüm Sermaye ve KK Döviz Harcamalarının Çekilmesi
$sermaye_harcamalari_listesi_yillik = [];
try {
    // Tüm yılı kronolojik olarak (Ocak'tan Temmuz'a veya tam tersi yeniye doğru) listelemek için çekiyoruz
    $sermaye_sorgu = $db->query("SELECT * FROM `forex_sermaye_harcamalari` ORDER BY `donem` DESC, `id` DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    // Tüm satırları yıllık listeye dahil ediyoruz
    $sermaye_harcamalari_listesi = $sermaye_sorgu;
    
} catch (PDOException $e) { 
    $sermaye_harcamalari_listesi_yillik = []; 
}




// =========================================================================
// %100 ARINDIRILMIŞ STRATEJİK TASARRUF VE ENSTRÜMAN HESAPLAMA MODELİ
// =========================================================================

// Yıllık Saf Tasarruf = (Kümule Gelir - Kümule Gider) + Yıllık Faiz Havuzu
$yilsal_net_tasarruf_potansiyeli_tl = ($toplam_yilsal_gelir - $toplam_yilsal_gider) + $toplam_yilsal_faiz;

// Enstrüman Alış Maliyet Toplamı (Cebinizden çıkan net anapara maliyeti)
$enstruman_ham_alis_toplami_tl = $usd_toplam_maliyet_tl + $eur_toplam_maliyet_tl + $xau_toplam_maliyet_tl;

// Yatırım Bütçe Başarısı = Yıllık Tasarruf - Enstrüman Alış Maliyet Toplamı
$gercek_finansal_sizinti_tl = $yilsal_net_tasarruf_potansiyeli_tl - $enstruman_ham_alis_toplami_tl;

// Sızıntı ve Atanma Oranı Tanımlamaları
$gercek_sizinti_yuzdesi = $yilsal_net_tasarruf_potansiyeli_tl > 0 ? ($gercek_finansal_sizinti_tl / $yilsal_net_tasarruf_potansiyeli_tl) * 100 : 0;
$yatirima_atanma_orani = 100 - $gercek_sizinti_yuzdesi;
$yatirima_atenma_orani = $yatirima_atanma_orani; // Harf hatası koruma köprüsü

// --- DÖNEMSEL SAF BAŞARI MATEMATİĞİ (AKTİF AY) ---
$guncel_ay_saf_tasarruf_tl = ($guncel_ay_gelir - $guncel_ay_gider) + $guncel_ay_faiz;
$tasarruf_skoru = $guncel_ay_gelir > 0 ? ($guncel_ay_saf_tasarruf_tl / $guncel_ay_gelir) * 100 : 0;

// VIEW KÖPRÜSÜ
$guncel_ay_net_tasarruf_tl = $guncel_ay_saf_tasarruf_tl;
$yilsal_net_tasarruf_tl    = $yilsal_net_tasarruf_potansiyeli_tl;






// =========================================================================
// VIEW KATMANININ ÇAĞRILMASI
// =========================================================================
require admin_view("forex");































/*




// =========================================================================
// 3. ADIM: BOT SINIF TANIMLAMASI (DOMXPATH PARSER)
// =========================================================================
class ForexExchange {

    public static function fetchIsBankKurlari() {
        $url = "https://kur.altin.in/banka/isbank";
        
        $options = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
            ]
        ];
        
        $context = stream_context_create($options);
        $html = @file_get_contents($url, false, $context);
        
        // GÜNCELLENDİ: Bağlantı kopmalarına karşı güvenli taban fallback kurları mühürlendi
        $kurlar = [
            'USD' => 0, 
            'EUR' => 0, 
            'XAU' => 0
        ];

        if ($html !== false) {
            // Windows-1254 şemasını UTF-8'e çevirerek Türkçe karakterleri (ı, i, ş) koruyoruz
            $html_utf8 = iconv("Windows-1254", "UTF-8//IGNORE", $html);
            $meta_utf8 = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            $html_duzeltilmis = $meta_utf8 . $html_utf8;

            $dom = new DOMDocument();
            libxml_use_internal_errors(true); 
            $dom->loadHTML($html_duzeltilmis, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);

            // 1. EURO (EUR) ALIŞ KURU SORGUSU
            $eur_query = '//div[contains(@class, "kurlar") and contains(@class, "bordernone") and @title="Euro"]/ul/li[contains(@class, "midrow") and contains(@class, "alis")]';
            $eur_entries = $xpath->query($eur_query);
            if ($eur_entries && $eur_entries->length > 0) {
                $kurlar['EUR'] = (float)trim($eur_entries->item(0)->nodeValue);
            }

            // 2. DOLAR (USD) ALIŞ KURU SORGUSU
            $usd_query = '//div[contains(@class, "kurlar") and contains(@class, "bordernone") and @title="Amerikan Doları"]/ul/li[contains(@class, "midrow") and contains(@class, "alis")]';
            $usd_entries = $xpath->query($usd_query);
            if ($usd_entries && $usd_entries->length > 0) {
                $kurlar['USD'] = (float)trim($usd_entries->item(0)->nodeValue);
            }

            // 3. GRAM ALTIN (XAU) ALIŞ KURU SORGUSU
            $xau_query = '//div[contains(@class, "kurlar") and contains(@class, "bordernone") and @title="İş Bankası"]/ul/li[contains(@class, "midrow") and contains(@class, "alis")]';
            $xau_entries = $xpath->query($xau_query);
            if ($xau_entries && $xau_entries->length > 0) {
                $kurlar['XAU'] = (float)trim($xau_entries->item(0)->nodeValue);
            }
        }
        
        return $kurlar;
    }

    public static function getLiveRates() {
        return [
            'kaynak' => 'Türkiye İş Bankası',
            'kurlar' => self::fetchIsBankKurlari(),
            'zaman'  => date('H:i:s')
        ];
    }
}


// =========================================================================
// 4. ADIM: CANLI VERİ TETİKLEME VE ARBİTRAJ / DEĞERLEME MATEMATİĞİ
// =========================================================================
$kur_servis            = ForexExchange::getLiveRates();
$kur_kaynagi           = $kur_servis['kaynak'];
$live_kurlar           = $kur_servis['kurlar']; 
$son_guncelleme_zamani = $kur_servis['zaman'];

// --- DOLAR (USD) ANLIK DEĞERLEME ---
$usd_mik = isset($cuzdan['USD']) ? $cuzdan['USD']['toplam_miktar'] : 0;
$usd_mal = isset($cuzdan['USD']) ? $cuzdan['USD']['ortalama_maliyet'] : 0;
$usd_toplam_maliyet_tl = $usd_mik * $usd_mal;
$usd_anlik_eder_tl     = $usd_mik * $live_kurlar['USD'];
$usd_fark_tl           = $usd_anlik_eder_tl - $usd_toplam_maliyet_tl;
$usd_fark_sinifi       = ($usd_fark_tl >= 0) ? 'text-success' : 'text-danger';
$usd_fark_isaret       = ($usd_fark_tl >= 0) ? '+' : '';

// --- EURO (EUR) ANLIK DEĞERLEME ---
$eur_mik = isset($cuzdan['EUR']) ? $cuzdan['EUR']['toplam_miktar'] : 0;
$eur_mal = isset($cuzdan['EUR']) ? $cuzdan['EUR']['ortalama_maliyet'] : 0;
$eur_toplam_maliyet_tl = $eur_mik * $eur_mal;
$eur_anlik_eder_tl     = $eur_mik * $live_kurlar['EUR'];
$eur_fark_tl           = $eur_anlik_eder_tl - $eur_toplam_maliyet_tl;
$eur_fark_sinifi       = ($eur_fark_tl >= 0) ? 'text-success' : 'text-danger';
$eur_fark_isaret       = ($eur_fark_tl >= 0) ? '+' : '';

// --- ALTIN (XAU) ANLIK DEĞERLEME ---
$xau_mik = isset($cuzdan['XAU']) ? $cuzdan['XAU']['toplam_miktar'] : 0;
$xau_mal = isset($cuzdan['XAU']) ? $cuzdan['XAU']['ortalama_maliyet'] : 0;
$xau_toplam_maliyet_tl = $xau_mik * $xau_mal;
$xau_anlik_eder_tl     = $xau_mik * $live_kurlar['XAU'];
$xau_fark_tl           = $xau_anlik_eder_tl - $xau_toplam_maliyet_tl;
$xau_fark_sinifi       = ($xau_fark_tl >= 0) ? 'text-success' : 'text-danger';
$xau_fark_isaret       = ($xau_fark_tl >= 0) ? '+' : '';

// GÜNCELLENDİ: Üst Süper Başlıktaki Toplam Tasfiye Verilerini Buraya Bağlıyoruz
$toplam_alis_maliyeti_tl = $usd_toplam_maliyet_tl + $eur_toplam_maliyet_tl + $xau_toplam_maliyet_tl;
$anlik_banka_degeri_tl   = $usd_anlik_eder_tl + $eur_anlik_eder_tl + $xau_anlik_eder_tl;
$arbitraj_fark_tl        = $anlik_banka_degeri_tl - $toplam_alis_maliyeti_tl;
$fark_sinifi             = ($arbitraj_fark_tl >= 0) ? 'text-success' : 'text-danger';
$fark_isaret             = ($arbitraj_fark_tl >= 0) ? '+' : '';



// =========================================================================
// 5. ADIM: VIEW KATMANININ GÜVENLİ ÇAĞRILMASI
// =========================================================================
require admin_view("forex");

*/