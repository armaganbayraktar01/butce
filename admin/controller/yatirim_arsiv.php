<?php


// =========================================================================
// --- BÖLÜM 1: AKTİF DÖNEM VE ÇEKİRDEK TANIMLAMALAR ---
// =========================================================================
$aktif_donem = $_SESSION['aktif_donem'] ?? date('Y-m');
list($aktif_yil, $aktif_ay_str) = explode('-', $aktif_donem);
$aktif_yil = trim($aktif_yil);
$aktif_ay_index = intval($aktif_ay_str);

// Veritabanınızdaki 4 ana varlık kilitlendi
$emtialar = ['XAU', 'MEV'];

$yatirim_araci = [
    'XAU' => [],
    'MEV' => []
];

// Üst kartlar ve tablo toplamları için küresel mizan sayaçları 🌟
$mev_toplam_anapara = 0.00;
$mev_toplam_brut = 0.00;
$mev_toplam_vergi = 0.00;
$mev_toplam_net = 0.00;
$mev_toplam_eklenen = 0.00;

// =========================================================================
// --- BÖLÜM 2: VERİTABANI SORGULAMA VE KRONOLOJİK HESAP MOTORU ---
// =========================================================================
foreach ($emtialar as $kod) {
    $gecici_liste = [];

    try {
        $sorgu = $db->prepare("
            SELECT * FROM `forex_islemler_arsiv` 
            WHERE `doviz_cinsi` = :kod 
            ORDER BY `islem_tarihi` DESC, `id` DESC
        ");

        $sorgu->execute([':kod' => $kod]);
        $gecici_liste = $sorgu->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log(
            "Döviz Arşiv Hatası (" . $kod . "): " . $e->getMessage()
        );

        $gecici_liste = [];
    }

    if (!empty($gecici_liste)) {

        // Doğru yürüyen bakiye hesabı için kronolojik
        // (eskiden yeniye) sıralama
        $kronolojik = array_reverse($gecici_liste);

        $yuruyen_miktar = 0;
        $kumulatif_tl = 0;

        foreach ($kronolojik as $key => $islem) {

            $miktar = (float)($islem['miktar'] ?? 0);
            $tutar = (float)($islem['tutar'] ?? 0);
            $vergi = (float)($islem['vergi'] ?? 0);
            $tur_ham = (int)($islem['islem_turu'] ?? 2);

            // Alış (2) veya Mevduat Giriş (4) pozitif;
            // Satış (0) veya Ödeme (1) negatif
            $is_pozitif = ($tur_ham === 2 || $tur_ham === 4);

            if ($is_pozitif) {
                $yuruyen_miktar += $miktar;
                $kumulatif_tl += ($tutar + $vergi);
            } else {
                $yuruyen_miktar += $miktar;
                $kumulatif_tl += $tutar;
            }

            // Mevduat (MEV) için arayüzde kullanılan
            // matematiksel hücreleri burada türetiyoruz 🎯
            if ($kod === 'MEV') {

                $faiz_gun = 0;

                if (
                    !empty($islem['vade_basi']) &&
                    !empty($islem['islem_tarihi'])
                ) {
                    $tarih_basi = new DateTime($islem['vade_basi']);
                    $tarih_sonu = new DateTime($islem['islem_tarihi']);

                    $faiz_gun = $tarih_basi
                        ->diff($tarih_sonu)
                        ->days;
                }

                $kronolojik[$key]['faiz_gun'] =
                    $faiz_gun;

                $kronolojik[$key]['anapara'] =
                    $miktar;

                $kronolojik[$key]['faiz_orani'] =
                    (float)($islem['doviz_kuru'] ?? 0);

                $kronolojik[$key]['brut_faiz'] =
                    $tutar;

                $kronolojik[$key]['vergi'] =
                    $vergi;

                $kronolojik[$key]['net_faiz'] =
                    $tutar - $vergi;

                $kronolojik[$key]['eklenen_tutar'] =
                    (float)($islem['eklenen_tutar'] ?? 0);

                $kronolojik[$key]['son_bakiye'] =
                    (float)($islem['maliyet'] ?? 0)
                    + (float)($islem['eklenen_tutar'] ?? 0);

                // Global Üst Kart Değişkenlerini
                // kuruşu kuruşuna toplama akümülatörü 🌟
                $mev_toplam_anapara +=
                    $kronolojik[$key]['anapara'];

                $mev_toplam_brut +=
                    $kronolojik[$key]['brut_faiz'];

                $mev_toplam_vergi +=
                    $kronolojik[$key]['vergi'];

                $mev_toplam_net +=
                    $kronolojik[$key]['net_faiz'];

                $mev_toplam_eklenen +=
                    $kronolojik[$key]['eklenen_tutar'];
            }

            $kronolojik[$key]['anlik_bakiye'] =
                $yuruyen_miktar;

            $kronolojik[$key]['kumulatif_tl'] =
                $kumulatif_tl;
        }

        // Tablo görünümü için yeniden günümüzden geçmişe
        // (yeniden eskiye) döndürme
        $yatirim_araci[$kod] =
            array_reverse($kronolojik);
    }
}

// =========================================================================
// --- BÖLÜM 3: MİLİMETRİK ALTIN (XAU) FIFO HESAP MOTORU ---
// =========================================================================
function hesaplaFifoMetrikleri($islem_listesi)
{
    $toplam_alis_miktar = 0;
    $toplam_alis_maliyet = 0;
    $toplam_satis_gelir = 0;
    $realize_kar_zarar = 0;
    $alis_havuzu = [];

    if (empty($islem_listesi)) {
        return [
            'alis_maliyeti' => 0,
            'satis_geliri' => 0,
            'realize_kar' => 0,
            'ort_alis' => 0,
            'ort_satis' => 0
        ];
    }

    $kronolojik = array_reverse($islem_listesi);

    foreach ($kronolojik as $islem) {

        $m = (float)abs($islem['miktar'] ?? 0);
        $mal = (float)abs($islem['maliyet'] ?? 0);
        $tur = (int)($islem['islem_turu'] ?? 2);

        if ($tur === 2 || $tur === 4) {

            $toplam_alis_miktar += $m;
            $toplam_alis_maliyet += $mal;

            $alis_havuzu[] = [
                'miktar' => $m,
                'maliyet' => $mal,
                'birim_fiyat' => $m > 0
                    ? ($mal / $m)
                    : 0
            ];

        } elseif ($tur === 0 || $tur === 1) {

            $toplam_satis_gelir += $mal;
            $kalan_satilacak = $m;
            $bu_satis_fifo_maliyeti = 0;

            foreach ($alis_havuzu as &$lot) {

                if ($lot['miktar'] <= 0) {
                    continue;
                }

                if ($kalan_satilacak >= $lot['miktar']) {

                    $kalan_satilacak -= $lot['miktar'];

                    $bu_satis_fifo_maliyeti +=
                        $lot['maliyet'];

                    $lot['miktar'] = 0;
                    $lot['maliyet'] = 0;

                } else {

                    $alinan_maliyet =
                        $kalan_satilacak *
                        $lot['birim_fiyat'];

                    $bu_satis_fifo_maliyeti +=
                        $alinan_maliyet;

                    $lot['miktar'] -=
                        $kalan_satilacak;

                    $lot['maliyet'] -=
                        $alinan_maliyet;

                    $kalan_satilacak = 0;

                    break;
                }
            }

            unset($lot);

            $realize_kar_zarar +=
                ($mal - $bu_satis_fifo_maliyeti);
        }
    }

    return [
        'alis_maliyeti' => $toplam_alis_maliyet,
        'satis_geliri' => $toplam_satis_gelir,
        'realize_kar' => $realize_kar_zarar,

        'ort_alis' => $toplam_alis_miktar > 0
            ? (
                $toplam_alis_maliyet /
                $toplam_alis_miktar
            )
            : 0,

        'ort_satis' => $toplam_alis_miktar > 0
            ? (
                $toplam_satis_gelir /
                $toplam_alis_miktar
            )
            : 0
    ];
}

// =========================================================================
// --- ALTIN VERİLERİ ANA MOTOR KANCASINA TAKILDI ---
// =========================================================================
$xau_analiz =
    hesaplaFifoMetrikleri($yatirim_araci['XAU']);

$xau_toplam_maliyet =
    $xau_analiz['alis_maliyeti'];

$xau_toplam_satis_geliri =
    $xau_analiz['satis_geliri'];

$xau_kesinlesen_kar =
    $xau_analiz['realize_kar'];

$xau_ort_alis_kuru =
    $xau_analiz['ort_alis'];

$xau_ort_satis_kuru =
    $xau_analiz['ort_satis'];


// =========================================================================
// --- XAU DÖNEMSEL ALIMLAR ---
// --- Görünüm Katmanındaki Kart 2 İçin Esnek Matris ---
// =========================================================================
$xau_donemsel_alimlar = [];

if (!empty($yatirim_araci['XAU'])) {

    foreach (
        array_reverse($yatirim_araci['XAU'])
        as $islem
    ) {

        $tur = (int)($islem['islem_turu'] ?? 2);

        if ($tur !== 2 && $tur !== 4) {
            continue;
        }

        $islem_time =
            strtotime($islem['islem_tarihi']);

        $yil =
            (int)date('Y', $islem_time);

        $ay =
            (int)date('m', $islem_time);

        $gun =
            (int)date('d', $islem_time);

        if ($gun < 15) {

            $ay--;

            if ($ay === 0) {
                $ay = 12;
                $yil--;
            }
        }

        $donem_anahtar =
            sprintf('%04d-%02d', $yil, $ay);

        if (
            !isset(
                $xau_donemsel_alimlar[$donem_anahtar]
            )
        ) {
            $xau_donemsel_alimlar[$donem_anahtar] = [
                'miktar' => 0.0,
                'maliyet' => 0.0
            ];
        }

        $xau_donemsel_alimlar[$donem_anahtar]['miktar'] +=
            (float)abs($islem['miktar']);

        $xau_donemsel_alimlar[$donem_anahtar]['maliyet'] +=
            (float)abs($islem['maliyet']);
    }

    krsort($xau_donemsel_alimlar);
}


// =========================================================================
// --- MEV DÖNEMSEL AKIŞLAR ---
// --- Görünüm Katmanındaki Kart 4 İçin Matris ---
// =========================================================================
$mev_aylik_akislar = [];

if (!empty($yatirim_araci['MEV'])) {

    foreach ($yatirim_araci['MEV'] as $islem) {

        $tarih_baz =
            !empty($islem['vade_basi'])
                ? $islem['vade_basi']
                : $islem['islem_tarihi'];

        if (empty($tarih_baz)) {
            continue;
        }

        $ay_yil_anahtar =
            date('Y-m', strtotime($tarih_baz));

        if (
            !isset(
                $mev_aylik_akislar[$ay_yil_anahtar]
            )
        ) {
            $mev_aylik_akislar[$ay_yil_anahtar] = [
                'donem' => $ay_yil_anahtar,
                'eklenen' => 0.0,
                'net_faiz' => 0.0,
                'toplam' => 0.0
            ];
        }

        $mev_aylik_akislar[$ay_yil_anahtar]['eklenen'] +=
            (float)($islem['eklenen_tutar'] ?? 0);

        $mev_aylik_akislar[$ay_yil_anahtar]['net_faiz'] +=
            (float)($islem['net_faiz'] ?? 0);

        $mev_aylik_akislar[$ay_yil_anahtar]['toplam'] +=
            (
                (float)($islem['eklenen_tutar'] ?? 0)
                +
                (float)($islem['net_faiz'] ?? 0)
            );
    }

    krsort($mev_aylik_akislar);
}


// =========================================================================
// --- YILLIK KONSOLİDE KAZANÇ MOTORU ---
// --- Görünüm Katmanındaki Kart 5 İçin ---
// =========================================================================
$yillik_konsolide_kazanc = [];

if (!empty($yatirim_araci['MEV'])) {

    foreach ($yatirim_araci['MEV'] as $islem) {

        $tarih_baz =
            !empty($islem['islem_tarihi'])
                ? $islem['islem_tarihi']
                : $islem['vade_basi'];

        if (empty($tarih_baz)) {
            continue;
        }

        $yil =
            date('Y', strtotime($tarih_baz));

        if (
            !isset(
                $yillik_konsolide_kazanc[$yil]
            )
        ) {
            $yillik_konsolide_kazanc[$yil] = 0.00;
        }

        $yillik_konsolide_kazanc[$yil] +=
            (float)$islem['net_faiz'];
    }
}

if (!empty($yatirim_araci['XAU'])) {

    $yillik_konsolide_kazanc[date('Y')] =
        (
            $yillik_konsolide_kazanc[date('Y')] ?? 0.00
        )
        +
        $xau_kesinlesen_kar;
}

krsort($yillik_konsolide_kazanc);


// =========================================================================
// --- VIEW GÜVENLİ ÇAĞRILMA BARİYERİ ---
// =========================================================================
require admin_view("yatirim_arsiv");