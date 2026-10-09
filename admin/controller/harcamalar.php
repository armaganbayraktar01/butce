<?php

// =========================================================================
// 🎯 HARCAMA ANALİZ CONTROLLER KATMANI (BANKA KIRILIMLI YAN YANA MOTORU)
// =========================================================================
if (!defined('BASEPATH') && !isset($db)) {
    die("Emniyet Bariyeri: Bu alana doğrudan erişim yetkiniz bulunmuyor.");
}

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. DÖNEM VE ZAMAN YÖNETİMİ
if (!isset($_SESSION['aktif_donem']) || empty($_SESSION['aktif_donem'])) {
    $en_guncel_sorgu = $db->query("SELECT aktif_donem FROM butce_akis ORDER BY aktif_donem DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $_SESSION['aktif_donem'] = ($en_guncel_sorgu && !empty($en_guncel_sorgu['aktif_donem'])) ? $en_guncel_sorgu['aktif_donem'] : date('Y-m');
}

$aktif_donem = $_SESSION['aktif_donem'];

// 2. HAFIZA KÖPRÜSÜ: Mevcut tüm kategorileri isim eşlemesi için çekiyoruz
$kat_sorgu = $db->query("SELECT id, kategori_adi FROM kategoriler");
$veritabanindaki_ham_kategoriler = $kat_sorgu->fetchAll(PDO::FETCH_ASSOC);

$kategori_isim_to_id_haritasi = [];
$kategori_haritasi = [];
foreach ($veritabanindaki_ham_kategoriler as $k) {
    $kategori_haritasi[$k['id']] = $k['kategori_adi'];
    $temiz_isim = str_replace([' ', ',', '/'], ['', '', ''], strtolower(trim($k['kategori_adi'])));
    $kategori_isim_to_id_haritasi[$temiz_isim] = intval($k['id']);
} 

// SİZİN 8 ANA GRUP SIRALAMA CETVELİNİZ
$ana_gruplar_mimari = [
    'Gıda Harcamaları'       => ['Market', 'Manav', 'Fırın', 'Şarküteri', 'Kuruyemiş', 'Yeme İçme'],
    'Ulaşım Harcamaları'     => ['izmirimkart'],
    'Alışveriş Harcamaları'  => ['Giyim', 'E-Ticaret', 'Kadın'],
    'Ev Harcamaları'         => ['Abonelik', 'Mobilya', 'Hobi,Kırtasiye, Teknoloji', 'Ev ve Dekorasyon'],    
    'Seyahat Harcamaları'    => ['Seyahat', 'Sigorta', 'Dutty Free'],
    'Can Dostumuz'           => ['Petshop', 'Veteriner'],
    'Sağlık Harcamaları'     => ['Sağlık'],
    'Vergi Ödemeleri'        => ['Vergi Ödemeleri']
];

$sirali_kategori_listesi = [];
foreach ($ana_gruplar_mimari as $grup_adi => $alt_kategoriler) {
    foreach ($alt_kategoriler as $alt_kat_adi) {
        $temiz_alt = str_replace([' ', ',', '/'], ['', '', ''], strtolower(trim($alt_kat_adi)));
        $gercek_id = $kategori_isim_to_id_haritasi[$temiz_alt] ?? 0;
        if ($gercek_id > 0) {
            $sirali_kategori_listesi[] = ['id' => $gercek_id, 'kategori_adi' => $alt_kat_adi, 'ust_grup' => $grup_adi];
        }
    }
}

// Dinamik Dönem Listesi Toplama
$mizan_donemleri_listesi = [];
try {
    $donem_sorgu = $db->query("SELECT DISTINCT `donem` FROM `harcama_gecmisi` WHERE `donem` != '' AND `donem` IS NOT NULL ORDER BY `donem` DESC");
    $mizan_donemleri_listesi = $donem_sorgu->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) { }
if (empty($mizan_donemleri_listesi)) { $mizan_donemleri_listesi[] = date('Y-m'); }
// =========================================================================
// 🎯 INTERAKTİF BÜTÇE İSTİHBARAT MOTORU - AJAX JSON SAĞLAYICI APİ KATMANI
// =========================================================================
if (isset($_GET['aksiyon'])) {
    header('Content-Type: application/json; charset=utf-8');
    $aksiyon = $_GET['aksiyon'];

        // 🛠️ AKSİYON 1: KATEGORİ DETAY VE EN ÇOK HARCANAN NOKTALAR MOTORU (GÜNCEL)
    if ($aksiyon === 'get_kategori_analiz') {
        $kategori_id = intval($_GET['kategori_id'] ?? 0);
        $secilen_donem = trim($_GET['donem'] ?? 'tum');

        // 1. Trend Sorgusu: Grafik her zaman geçmiş tüm dönemlerin seyrini (trendini) çizmelidir.
        $trend_sorgu = $db->prepare("
            SELECT donem, SUM(tutar / (LENGTH(kategori_id) - LENGTH(REPLACE(kategori_id, ',', '')) + 1)) as donem_toplam 
            FROM harcama_gecmisi 
            WHERE FIND_IN_SET(?, kategori_id) AND tutar != 0
            GROUP BY donem ORDER BY donem ASC
        ");
        $trend_sorgu->execute([$kategori_id]);

        // 2. Firma Sorgusu: Seçilen döneme ait EN ÇOK HARCANAN 5 FİRMAYI çekecek şekilde dinamik yapıldı 🚀
        $sql_firma = "SELECT isletme_adi, SUM(tutar / (LENGTH(kategori_id) - LENGTH(REPLACE(kategori_id, ',', '')) + 1)) as firma_toplam
                      FROM harcama_gecmisi 
                      WHERE FIND_IN_SET(?, kategori_id) AND isletme_adi != '' AND tutar != 0";
        $firma_parametreler = [$kategori_id];
        
        if ($secilen_donem !== 'tum') {
            $sql_firma .= " AND donem = ?";
            $firma_parametreler[] = $secilen_donem;
        }
        
        $sql_firma .= " GROUP BY isletme_adi ORDER BY firma_toplam DESC LIMIT 5";
        $firma_sorgu = $db->prepare($sql_firma);
        $firma_sorgu->execute($firma_parametreler);

        // 3. Detay Liste Sorgusu: Seçilen döneme ait hesap dökümü listesi
        $sql_liste = "SELECT islem_tarihi, isletme_adi, kart_turu, tutar, donem 
                      FROM harcama_gecmisi 
                      WHERE FIND_IN_SET(?, kategori_id) AND tutar != 0";
        $parametreler = [$kategori_id];
        
        if ($secilen_donem !== 'tum') {
            $sql_liste .= " AND donem = ?";
            $parametreler[] = $secilen_donem;
        }
        
        $sql_liste .= " ORDER BY islem_tarihi DESC";
        $liste_sorgu = $db->prepare($sql_liste);
        $liste_sorgu->execute($parametreler);

        echo json_encode([
            'status' => true,
            'trend'  => $trend_sorgu->fetchAll(PDO::FETCH_ASSOC),
            'top_firmalar' => $firma_sorgu->fetchAll(PDO::FETCH_ASSOC),
            'liste'  => $liste_sorgu->fetchAll(PDO::FETCH_ASSOC)
        ]);
        exit;
    }


    // // 🛠️ AKSİYON 1: KATEGORİ DETAY VE EN ÇOK HARCANAN NOKTALAR MOTORU
    // if ($aksiyon === 'get_kategori_analiz') {
    //     $kategori_id = intval($_GET['kategori_id'] ?? 0);
    //     $secilen_donem = trim($_GET['donem'] ?? 'tum');

    //     $trend_sorgu = $db->prepare("
    //         SELECT donem, SUM(tutar / (LENGTH(kategori_id) - LENGTH(REPLACE(kategori_id, ',', '')) + 1)) as donem_toplam 
    //         FROM harcama_gecmisi 
    //         WHERE FIND_IN_SET(?, kategori_id) AND tutar != 0
    //         GROUP BY donem ORDER BY donem ASC
    //     ");
    //     $trend_sorgu->execute([$kategori_id]);

    //     $firma_sorgu = $db->prepare("
    //         SELECT isletme_adi, SUM(tutar / (LENGTH(kategori_id) - LENGTH(REPLACE(kategori_id, ',', '')) + 1)) as firma_toplam
    //         FROM harcama_gecmisi
    //         WHERE FIND_IN_SET(?, kategori_id) AND isletme_adi != '' AND tutar != 0
    //         GROUP BY isletme_adi ORDER BY firma_toplam DESC LIMIT 5
    //     ");
    //     $firma_sorgu->execute([$kategori_id]);

    //     $sql_liste = "SELECT islem_tarihi, isletme_adi, kart_turu, tutar, donem FROM harcama_gecmisi WHERE FIND_IN_SET(?, kategori_id) AND tutar != 0";
    //     $parametreler = [$kategori_id];
    //     if ($secilen_donem !== 'tum') {
    //         $sql_liste .= " AND donem = ?";
    //         $parametreler[] = $secilen_donem;
    //     }
    //     $sql_liste .= " ORDER BY islem_tarihi DESC";
    //     $liste_sorgu = $db->prepare($sql_liste);
    //     $liste_sorgu->execute($parametreler);

    //     echo json_encode([
    //         'status' => true,
    //         'trend'  => $trend_sorgu->fetchAll(PDO::FETCH_ASSOC),
    //         'top_firmalar' => $firma_sorgu->fetchAll(PDO::FETCH_ASSOC),
    //         'liste'  => $liste_sorgu->fetchAll(PDO::FETCH_ASSOC)
    //     ]);
    //     exit;
    // }

    // 🛠️ AKSİYON 2: TİCARİ FİRMA DETAY VE STRATEJİK HESAP EKSTRESİ MOTORU
    if ($aksiyon === 'get_firma_analiz') {
        $firma_adi = trim($_GET['firma_adi'] ?? '');

        $firma_trend = $db->prepare("SELECT donem, SUM(tutar) as donem_toplam FROM harcama_gecmisi WHERE isletme_adi = ? AND tutar != 0 GROUP BY donem ORDER BY donem ASC");
        $firma_trend->execute([$firma_adi]);

        $metrik_sorgu = $db->prepare("
            SELECT COUNT(*) as toplam_islem, AVG(tutar) as ortalama_sepet, SUM(tutar) as toplam_ciro
            FROM harcama_gecmisi WHERE isletme_adi = ? AND tutar != 0
        ");
        $metrik_sorgu->execute([$firma_adi]);

        $ekstre_sorgu = $db->prepare("SELECT islem_tarihi, kart_turu, tutar, kategori_id FROM harcama_gecmisi WHERE isletme_adi = ? AND tutar != 0 ORDER BY islem_tarihi DESC");
        $ekstre_sorgu->execute([$firma_adi]);

        echo json_encode([
            'status'   => true,
            'trend'    => $firma_trend->fetchAll(PDO::FETCH_ASSOC),
            'metrikler'=> $metrik_sorgu->fetch(PDO::FETCH_ASSOC),
            'ekstre'   => $ekstre_sorgu->fetchAll(PDO::FETCH_ASSOC)
        ]);
        exit;
    }

    // 🛠️ AKSİYON 3: DÖNEMLİK MAKRO KAPANIŞ VE PASTA SÜZGECİ UYUMLU BİLANÇO MOTORU
    if ($aksiyon === 'get_donem_analiz') {
        $donem_kod = trim($_GET['donem_kod'] ?? '');
        $secilen_kategori_adi = trim($_GET['kategori_adi'] ?? ''); 

        // Kategori adından veritabanı ID'sini çözme ters haritası
        $kategori_adi_to_id_sozlugu = [];
        $kat_ara_sorgu = $db->query("SELECT id, kategori_adi FROM kategoriler");
        while ($r = $kat_ara_sorgu->fetch(PDO::FETCH_ASSOC)) {
            $kategori_adi_to_id_sozlugu[trim($r['kategori_adi'])] = intval($r['id']);
        }
        $hedef_kategori_id = $kategori_adi_to_id_sozlugu[$secilen_kategori_adi] ?? 0;

        // Donut Pasta Grafiği her zaman tam resmi göstermelidir
        $pasta_sorgu = $db->prepare("SELECT kategori_id, SUM(tutar) as kategori_toplam FROM harcama_gecmisi WHERE donem = ? AND tutar != 0 GROUP BY kategori_id");
        $pasta_sorgu->execute([$donem_kod]);
        $ham_pasta = $pasta_sorgu->fetchAll(PDO::FETCH_ASSOC);

        // 🍕 PASTA DİLİMİ FİLTRE KALKANI ENJEKSİYONU
        $kategori_sartı = "";
        $sorgu_parametreleri = [$donem_kod];
        if ($hedef_kategori_id > 0) {
            $kategori_sartı = " AND FIND_IN_SET(?, kategori_id) ";
            $sorgu_parametreleri[] = $hedef_kategori_id;
        }

        $banka_sorgu = $db->prepare("
            SELECT 
                SUM(CASE WHEN LOWER(kart_turu) LIKE '%vakif%' OR LOWER(kart_turu) LIKE '%world%' THEN tutar ELSE 0 END) as vakif_toplam,
                SUM(CASE WHEN LOWER(kart_turu) NOT LIKE '%vakif%' AND LOWER(kart_turu) NOT LIKE '%world%' THEN tutar ELSE 0 END) as isbank_toplam
            FROM harcama_gecmisi WHERE donem = ? AND tutar != 0 " . $kategori_sartı
        );
        $banka_sorgu->execute($sorgu_parametreleri);
        $banka_ozet = $banka_sorgu->fetch(PDO::FETCH_ASSOC);

        $isbank_ekstre_sorgu = $db->prepare("SELECT islem_tarihi, isletme_adi, tutar, kategori_id FROM harcama_gecmisi WHERE donem = ? AND (LOWER(kart_turu) NOT LIKE '%vakif%' AND LOWER(kart_turu) NOT LIKE '%world%') AND tutar != 0 " . $kategori_sartı . " ORDER BY islem_tarihi DESC");
        $isbank_ekstre_sorgu->execute($sorgu_parametreleri);
        
        $vakif_ekstre_sorgu = $db->prepare("SELECT islem_tarihi, isletme_adi, tutar, kategori_id FROM harcama_gecmisi WHERE donem = ? AND (LOWER(kart_turu) LIKE '%vakif%' OR LOWER(kart_turu) LIKE '%world%') AND tutar != 0 " . $kategori_sartı . " ORDER BY islem_tarihi DESC");
        $vakif_ekstre_sorgu->execute($sorgu_parametreleri);


        // -------------------------------------------------------------------------
        // 🆕 3. PART REFORMU: GERÇEK MİLATLI HAFTALIK VE GÜNLÜK MİKRO DAĞITIM HAVUZU
        // -------------------------------------------------------------------------
        $haftalik_trend = [
            'Hafta 1' => ['isbank' => 0.00, 'vakif' => 0.00, 'toplam' => 0.00, 'label' => '', 'gunler' => [], 'firmalar' => []],
            'Hafta 2' => ['isbank' => 0.00, 'vakif' => 0.00, 'toplam' => 0.00, 'label' => '', 'gunler' => [], 'firmalar' => []],
            'Hafta 3' => ['isbank' => 0.00, 'vakif' => 0.00, 'toplam' => 0.00, 'label' => '', 'gunler' => [], 'firmalar' => []],
            'Hafta 4' => ['isbank' => 0.00, 'vakif' => 0.00, 'toplam' => 0.00, 'label' => '', 'gunler' => [], 'firmalar' => []]
        ];
        $gunluk_islem_matrisi = ['Hafta 1' => [], 'Hafta 2' => [], 'Hafta 3' => [], 'Hafta 4' => []];

        // 1. Her kartın o dönemdeki ilk harcama gününü kesim miladı kabul ediyoruz
        $milat_sorgu = $db->prepare("
            SELECT 
                MIN(CASE WHEN LOWER(kart_turu) NOT LIKE '%vakif%' AND LOWER(kart_turu) NOT LIKE '%world%' THEN islem_tarihi END) as isbank_milat,
                MIN(CASE WHEN LOWER(kart_turu) LIKE '%vakif%' OR LOWER(kart_turu) LIKE '%world%' THEN islem_tarihi END) as vakif_milat
            FROM harcama_gecmisi WHERE donem = ? AND tutar != 0
        ");
        $milat_sorgu->execute([$donem_kod]);
        $dinamik_milatlar = $milat_sorgu->fetch(PDO::FETCH_ASSOC);

        $isbank_base_milat = $dinamik_milatlar['isbank_milat'] ? date('Y-m-d', strtotime($dinamik_milatlar['isbank_milat'])) : null;
        $vakif_base_milat  = $dinamik_milatlar['vakif_milat'] ? date('Y-m-d', strtotime($dinamik_milatlar['vakif_milat'])) : null;

        // 2. Dönemin Tüm Harcamalarını Kronolojik Olarak Parçalıyoruz
        $donem_harcamalari_sorgu = $db->prepare("SELECT islem_tarihi, tutar, isletme_adi, kart_turu, kategori_id FROM harcama_gecmisi WHERE donem = ? AND tutar != 0 " . $kategori_sartı . " ORDER BY islem_tarihi ASC");
        $donem_harcamalari_sorgu->execute($sorgu_parametreleri);
        $donem_harcamalari = $donem_harcamalari_sorgu->fetchAll(PDO::FETCH_ASSOC);

        foreach ($donem_harcamalari as $h) {
            $tutar = floatval($h['tutar']);
            $islem_tarihi_ham = $h['islem_tarihi'];
            $tarih_short = date('d.m.Y', strtotime($islem_tarihi_ham));
            $tarih_formatli = date('Y-m-d', strtotime($islem_tarihi_ham));
            $firma = !empty($h['isletme_adi']) ? htmlspecialchars(trim($h['isletme_adi'])) : "FİRMASIZLAR (Ham Metin)";

            $ham_kart = strtolower(trim($h['kart_turu']));
            $kart_kod = (strpos($ham_kart, 'vakif') !== false || strpos($ham_kart, 'world') !== false) ? 'vakif' : 'isbank';
            $kart_badge_adi = ($kart_kod === 'vakif') ? 'VAKIF' : 'İŞ BANK';

            $aktif_milat = ($kart_kod === 'vakif') ? $vakif_base_milat : $isbank_base_milat;
            if (!$aktif_milat) continue;

            $gun_farki = floor((strtotime($tarih_formatli) - strtotime($aktif_milat)) / (60 * 60 * 24));
            if ($gun_farki < 0) $gun_farki = 0;

            if ($gun_farki < 7) { $h_indis = 'Hafta 1'; $t1 = date('d.m', strtotime($aktif_milat)); $t2 = date('d.m', strtotime($aktif_milat . " +6 day")); } 
            elseif ($gun_farki < 14) { $h_indis = 'Hafta 2'; $t1 = date('d.m', strtotime($aktif_milat . " +7 day")); $t2 = date('d.m', strtotime($aktif_milat . " +13 day")); } 
            elseif ($gun_farki < 21) { $h_indis = 'Hafta 3'; $t1 = date('d.m', strtotime($aktif_milat . " +14 day")); $t2 = date('d.m', strtotime($aktif_milat . " +20 day")); } 
            else { $h_indis = 'Hafta 4'; $t1 = date('d.m', strtotime($aktif_milat . " +21 day")); $t2 = date('d.m', strtotime($aktif_milat . " +30 day")); }

            $satir_tagleri = array_filter(explode(',', $h['kategori_id'] ?? ''));
            if (empty($satir_tagleri)) { $satir_tagleri = array(0); }
            $etiket_basina_tutar = $tutar / count($satir_tagleri);

            foreach ($satir_tagleri as $tag_id) {
                $haftalik_trend[$h_indis][$kart_kod] += $etiket_basina_tutar;
                $haftalik_trend[$h_indis]['toplam']   += $etiket_basina_tutar;
                $haftalik_trend[$h_indis]['label']    = "($t1-$t2)";

                // Hafta içi Günlük Yük Dağılım biriktiricisi
                if (!isset($haftalik_trend[$h_indis]['gunler'][$tarih_short])) { $haftalik_trend[$h_indis]['gunler'][$tarih_short] = 0.00; }
                $haftalik_trend[$h_indis]['gunler'][$tarih_short] += $etiket_basina_tutar;

                // Hafta içi Ticari Müessese Ciro Sadakat biriktiricisi
                if (!isset($haftalik_trend[$h_indis]['firmalar'][$firma])) { $haftalik_trend[$h_indis]['firmalar'][$firma] = 0.00; }
                $haftalik_trend[$h_indis]['firmalar'][$firma] += $etiket_basina_tutar;

                $gunluk_islem_matrisi[$h_indis][] = [
                    'tarih' => $tarih_short, 'firma' => $firma, 'kart' => $kart_badge_adi, 'tutar' => $etiket_basina_tutar
                ];
            }
        }

        // Grafik görsel sadeliği adına her haftanın en yüksek harcanan TOP 5 müessesesini süzüyoruz
        foreach ($haftalik_trend as $indis => $veri) {
            arsort($haftalik_trend[$indis]['firmalar']);
            $haftalik_trend[$indis]['firmalar'] = array_slice($haftalik_trend[$indis]['firmalar'], 0, 5, true);
        }

        echo json_encode([
            'status'               => true,
            'kategoriler'          => $ham_pasta,
            'bankalar'             => $banka_ozet,
            'isbank_ekstre'        => $isbank_ekstre_sorgu->fetchAll(PDO::FETCH_ASSOC),
            'vakif_ekstre'         => $vakif_ekstre_sorgu->fetchAll(PDO::FETCH_ASSOC),
            'haftalik_trend'       => $haftalik_trend,
            'gunluk_islem_matrisi' => $gunluk_islem_matrisi
        ]);
        exit;
    }



}


// =========================================================================
// 🏟️ ANAMERKEZ LABORATUVARI: ANA SAYFA MİZAN TABLOLARI HESAP AKIŞI
// =========================================================================
// Çapraz Rapor Matris İskeletinin Sıfırlanması
$yillik_matris_raporu = []; $vakif_matris_raporu = []; $isbank_matris_raporu = [];
foreach ($mizan_donemleri_listesi as $donem_kod) {
    foreach ($sirali_kategori_listesi as $s_kat) {
        $yillik_matris_raporu[$donem_kod][$s_kat['id']] = 0.00;
        $vakif_matris_raporu[$donem_kod][$s_kat['id']]  = 0.00;
        $isbank_matris_raporu[$donem_kod][$s_kat['id']] = 0.00;
    }
}

$donemlik_analiz = []; $kategori_analiz = []; $firma_analiz = [];
$toplam_genel_hacim = 0.00; $toplam_vakif_hacmi = 0.00; $toplam_isbank_hacmi = 0.00;

$harcama_sorgu = $db->query("SELECT * FROM harcama_gecmisi WHERE tutar != 0 ORDER BY islem_tarihi DESC");
$tum_harcamalar = $harcama_sorgu->fetchAll(PDO::FETCH_ASSOC);

foreach ($tum_harcamalar as $h) {
    $tutar = floatval($h['tutar']); $donem = $h['donem']; 
    if (empty($donem)) continue;

    $ham_kart = strtolower(trim($h['kart_turu']));
    $kart = (strpos($ham_kart, 'vakif') !== false || strpos($ham_kart, 'world') !== false) ? 'vakifbank' : 'isbank';
    $firma = !empty($h['isletme_adi']) ? trim($h['isletme_adi']) : "FİRMASIZLAR (Ham Metin)";
    
    $toplam_genel_hacim += $tutar;
    if ($kart === 'vakifbank') { $toplam_vakif_hacmi += $tutar; } else { $toplam_isbank_hacmi += $tutar; }

    $satir_tagleri = array_filter(explode(',', $h['kategori_id'] ?? ''));
    if (empty($satir_tagleri)) { $satir_tagleri = array(0); }
    $etiket_basina_tutar = $tutar / count($satir_tagleri);

    if (!isset($donemlik_analiz[$donem])) { $donemlik_analiz[$donem] = ['isbank' => 0, 'vakif' => 0, 'toplam' => 0]; }
    if ($kart === 'isbank') { $donemlik_analiz[$donem]['isbank'] += $tutar; } else { $donemlik_analiz[$donem]['vakif'] += $tutar; }
    $donemlik_analiz[$donem]['toplam'] += $tutar;

    if (!isset($firma_analiz[$firma])) { $firma_analiz[$firma] = ['isbank' => 0, 'vakif' => 0, 'toplam' => 0]; }
    if ($kart === 'isbank') { $firma_analiz[$firma]['isbank'] += $tutar; } else { $firma_analiz[$firma]['vakif'] += $tutar; }
    $firma_analiz[$firma]['toplam'] += $tutar;

    foreach ($satir_tagleri as $tag_id) {
        $tag_id = intval($tag_id);
        if (!isset($kategori_analiz[$tag_id])) { $kategori_analiz[$tag_id] = ['isbank' => 0, 'vakif' => 0, 'toplam' => 0]; }
        if ($kart === 'isbank') { $kategori_analiz[$tag_id]['isbank'] += $etiket_basina_tutar; } else { $kategori_analiz[$tag_id]['vakif'] += $etiket_basina_tutar; }
        $kategori_analiz[$tag_id]['toplam'] += $etiket_basina_tutar;

        if (isset($yillik_matris_raporu[$donem][$tag_id])) { $yillik_matris_raporu[$donem][$tag_id] += $etiket_basina_tutar; }
        if ($kart === 'vakifbank' && isset($vakif_matris_raporu[$donem][$tag_id])) { $vakif_matris_raporu[$donem][$tag_id] += $etiket_basina_tutar; } 
        elseif ($kart === 'isbank' && isset($isbank_matris_raporu[$donem][$tag_id])) { $isbank_matris_raporu[$donem][$tag_id] += $etiket_basina_tutar; }
    }
}

krsort($donemlik_analiz);
uasort($firma_analiz, function($a, $b) { return $b['toplam'] <=> $a['toplam']; });

$sirali_kategori_analiz = [];
foreach ($sirali_kategori_listesi as $s_kat) {
    $cat_id = $s_kat['id'];
    $sirali_kategori_analiz[$cat_id] = $kategori_analiz[$cat_id] ?? ['isbank' => 0, 'vakif' => 0, 'toplam' => 0];
}

// Görünüm katmanı yüklemesi
require_once 'admin/view/harcamalar.php';

