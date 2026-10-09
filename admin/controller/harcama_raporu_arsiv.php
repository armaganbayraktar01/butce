<?php

 
// 1. DÖNEM VE ZAMAN YÖNETİMİ
if (!isset($_SESSION['aktif_donem']) || empty($_SESSION['aktif_donem'])) {
    $en_guncel_sorgu = $db->query("SELECT aktif_donem FROM butce_akis ORDER BY aktif_donem DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $_SESSION['aktif_donem'] = ($en_guncel_sorgu && !empty($en_guncel_sorgu['aktif_donem'])) ? $en_guncel_sorgu['aktif_donem'] : date('Y-m');
}

$aktif_donem = $_SESSION['aktif_donem'];

$ay_isimleri = [1=>'OCAK', 2=>'ŞUBAT', 3=>'MART', 4=>'NİSAN', 5=>'MAYIS', 6=>'HAZİRAN', 7=>'TEMMUZ', 8=>'AĞUSTOS', 9=>'EYLÜL', 10=>'EKİM', 11=>'KASIM', 12=>'ARALIK'];
list($aktif_yil, $aktif_ay_str) = explode('-', $aktif_donem);
$aktif_ay_index = intval($aktif_ay_str);


if (isset($_GET['donem'])){
    $secilen_donem = $_GET['donem'] ? $_GET['donem'] : 2025;
} else {
    $secilen_donem = 2025;
}

// ÖZELLİK 1: GET ile yıl geliyorsa onu al, gelmiyorsa SESSION'daki yılı baz al
// $secilen_donem = $_GET['donem'] ? $_GET['donem'] : 2025;
$aktif_yil = (isset($secilen_donem) && !empty($secilen_donem)) ? intval($secilen_donem) : intval($aktif_yil);


// 🔄 AKTİF YILDAN ÖNCEKİ TÜM YILLARIN DEVİR HESABI
$secilen_yil = $aktif_yil;

$devir_sorgu = $db->prepare("
    SELECT
        CAST(SUBSTRING(`donem`,1,4) AS UNSIGNED) AS yil,
        SUM(
            IFNULL(maas_o,0) +
            IFNULL(maas_a,0) +
            IFNULL(maktu_mesai,0) +
            IFNULL(ek_gelir,0) +
            IFNULL(faiz_gelir,0) -
            IFNULL(kredi_karti_o,0) -
            IFNULL(kredi_karti_a,0) -
            IFNULL(ev_kredisi,0) -
            IFNULL(aidat,0) -
            IFNULL(harcama_nakit,0) -
            IFNULL(elektrik,0) -
            IFNULL(su,0) -
            IFNULL(dogalgaz,0) -
            IFNULL(telefon,0) -
            IFNULL(internet,0)
        ) AS net_tasarruf,
        SUM(IFNULL(yatirim_tutari,0)) AS net_yatirim
    FROM `aylik_harcama_raporu_arsiv`
    WHERE CAST(SUBSTRING(`donem`,1,4) AS UNSIGNED) < :secilen_yil
    GROUP BY CAST(SUBSTRING(`donem`,1,4) AS UNSIGNED)
    ORDER BY yil ASC
");

$devir_sorgu->execute(['secilen_yil' => $secilen_yil]);
$devir_yillari = $devir_sorgu->fetchAll(PDO::FETCH_ASSOC);

$kumulatif_net_tasarruf = 0;
$kumulatif_net_yatirim = 0;

foreach ($devir_yillari as $yil) {
    $kumulatif_net_tasarruf += (float)$yil['net_tasarruf'];
    $kumulatif_net_yatirim += (float)$yil['net_yatirim'];
}



// 📊 TABLO VERİ ÇEKME VE TEPE TOPLAMI HESAPLAMA
$db_raporlar = $db->query("SELECT * FROM `aylik_harcama_raporu_arsiv` WHERE `donem` LIKE '{$aktif_yil}-%'")->fetchAll(PDO::FETCH_ASSOC);

$yillik_matris = [];
foreach ($db_raporlar as $r_satir) { $yillik_matris[intval(substr($r_satir['donem'], 5, 2))] = $r_satir; }

$tepe_toplamlar = ['devir_yatirim'=>0,'devir_bakiye'=>0,'maas_o'=>0,'maas_a'=>0,'maktu_mesai'=>0,'ek_gelir'=>0,'faiz_gelir'=>0,'kredi_karti_o'=>0,'kredi_karti_a'=>0,'ev_kredisi'=>0,'aidat'=>0,'harcama_nakit'=>0,'fatura_nakit'=>0,'elektrik'=>0,'su'=>0,'dogalgaz'=>0,'telefon'=>0,'internet'=>0,'yatirim_tutari'=>0];

for ($m = 1; $m <= 12; $m++) {
    if (isset($yillik_matris[$m])) {
                
        foreach ($tepe_toplamlar as $s_key => $v) { 
            $tepe_toplamlar[$s_key] += floatval($yillik_matris[$m][$s_key] ?? 0); 
        }
    }
}


// Önce temel hesaplamaları değişkenlere atayalım
$butun_gelir = $tepe_toplamlar['maas_o'] + $tepe_toplamlar['maas_a'] + $tepe_toplamlar['maktu_mesai'] + $tepe_toplamlar['ek_gelir'] + $tepe_toplamlar['faiz_gelir'];
$butun_gider = $tepe_toplamlar['kredi_karti_o'] + $tepe_toplamlar['kredi_karti_a'] + $tepe_toplamlar['ev_kredisi'] + $tepe_toplamlar['aidat'] + $tepe_toplamlar['harcama_nakit'] + $tepe_toplamlar['elektrik'] + $tepe_toplamlar['su'] + $tepe_toplamlar['dogalgaz'] + $tepe_toplamlar['telefon'] + $tepe_toplamlar['internet'];
$yillik_butun_yatirim_toplami = $tepe_toplamlar['yatirim_tutari'] ?? 0;

// Şimdi temiz bir şekilde array yapısına aktaralım
$yillik_toplam = [
    "butun_gelir"    => $butun_gelir,
    "butun_gider"    => $butun_gider,
    "butun_tasarruf" => $butun_gelir - $butun_gider,
    "genel_tasarruf" => $butun_gelir - $butun_gider + $tepe_toplamlar['devir_bakiye'],
    "butun_yatirim"  => $yillik_butun_yatirim_toplami,
    "genel_yatirim"  => $yillik_butun_yatirim_toplami + $tepe_toplamlar['devir_yatirim']
];


// SELECTBOX TARİH: Güncel dönemden selectbox yıl listesini dinamik üretme
$yeardb = $db->query("SELECT donem FROM aylik_harcama_raporu_arsiv ORDER BY donem DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if ($yeardb && !empty($yeardb['donem'])) {
    list($en_guncel_arsiv_yili) = explode('-', $yeardb['donem']);
    $bitis_yili = intval($en_guncel_arsiv_yili);
} else {
    $bitis_yili = intval(date('Y'));
}
$selectbox_years = range(2021, $bitis_yili);


// Görünüm katmanı yüklemesi
require_once 'admin/view/harcama_raporu_arsiv.php';
