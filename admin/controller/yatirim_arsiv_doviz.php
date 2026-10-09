<?php
// =========================================================================
// --- BÖLÜM 1: ÇEKİRDEK TANIMLAMALAR VE MİZAN SAYAÇLARI ---
// =========================================================================
$aktif_donem = $_SESSION['aktif_donem'] ?? date('Y-m');

// Sadece EUR ve USD emtialarını işleme alıyoruz
$emtialar = ['USD', 'EUR'];

$yatirim_araci = [
    'USD' => [],
    'EUR' => []
];

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
            "Döviz Döngü Hatası (" . $kod . "): " .
            $e->getMessage()
        );

        $gecici_liste = [];
    }

    if (!empty($gecici_liste)) {

        // Yürüyen bakiye hesabı için eskiden yeniye kronolojik sıralama
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
            $is_pozitif = (
                $tur_ham === 2 ||
                $tur_ham === 4
            );

            if ($is_pozitif) {

                $yuruyen_miktar += $miktar;
                $kumulatif_tl += ($tutar + $vergi);

            } else {

                $yuruyen_miktar -= $miktar;
                $kumulatif_tl -= $tutar;
            }

            $kronolojik[$key]['anlik_bakiye'] =
                $yuruyen_miktar;

            $kronolojik[$key]['kumulatif_tl'] =
                $kumulatif_tl;
        }

        // Tablo görünümü için yeniden günümüzden geçmişe döndürme
        $yatirim_araci[$kod] =
            array_reverse($kronolojik);
    }
}

function hesaplaDovizFifoMetrikleri($islem_listesi)
{
    $toplam_alis_miktar  = 0;
    $toplam_alis_maliyet = 0;

    $toplam_satis_miktar = 0;
    $toplam_satis_gelir  = 0;
    $toplam_satis_vergi  = 0;

    $realize_kar_zarar = 0;

    // FIFO alış lotları
    $alis_havuzu = [];

    if (empty($islem_listesi)) {
        return [
            'alis_miktari'      => 0,
            'alis_maliyeti'     => 0,
            'satis_miktari'     => 0,
            'satis_geliri'      => 0,
            'satis_vergi'       => 0,
            'realize_kar'       => 0,
            'kalan_miktar'      => 0,
            'kalan_maliyet'    => 0,
            'ort_alis'         => 0,
            'ort_satis'        => 0
        ];
    }

    /*
     * FIFO mutlaka eskiden yeniye çalışmalı.
     *
     * $islem_listesi şu anda görünüm için
     * yeniden eskiye sıralı olduğu için reverse ediyoruz.
     */
    $kronolojik = array_reverse($islem_listesi);

    foreach ($kronolojik as $islem) {

        $miktar = (float)($islem['miktar'] ?? 0);
        $m      = abs($miktar);

        $tutar  = (float)($islem['tutar'] ?? 0);
        $vergi  = (float)($islem['vergi'] ?? 0);

        $tur = (int)($islem['islem_turu'] ?? 2);

        /*
         * ---------------------------------------------------------
         * ALIŞ
         * ---------------------------------------------------------
         */
        if ($tur === 2 || $tur === 4) {

            if ($m <= 0) {
                continue;
            }

            /*
             * Alış maliyeti:
             *
             * Tutar + kambiyo vergisi
             */
            $alis_maliyeti = abs($tutar) + abs($vergi);

            $toplam_alis_miktar  += $m;
            $toplam_alis_maliyet += $alis_maliyeti;

            $birim_maliyet =
                $m > 0
                    ? ($alis_maliyeti / $m)
                    : 0;

            $alis_havuzu[] = [
                'miktar'       => $m,
                'maliyet'      => $alis_maliyeti,
                'birim_maliyet'=> $birim_maliyet
            ];

        /*
         * ---------------------------------------------------------
         * SATIŞ / ÖDEME
         * ---------------------------------------------------------
         */
        } elseif ($tur === 0 || $tur === 1) {

            if ($m <= 0) {
                continue;
            }

            /*
             * Satış hasılatı:
             * Tutar alanının mutlak değeri.
             */
            $satis_geliri = abs($tutar);

            /*
             * Satış vergisi
             */
            $satis_vergi = abs($vergi);

            $toplam_satis_miktar += $m;
            $toplam_satis_gelir  += $satis_geliri;
            $toplam_satis_vergi  += $satis_vergi;

            /*
             * Satılacak miktarın FIFO maliyetini bul.
             */
            $kalan_satilacak = $m;

            $bu_satis_fifo_maliyeti = 0;

            foreach ($alis_havuzu as &$lot) {

                if ($lot['miktar'] <= 0) {
                    continue;
                }

                /*
                 * Bu lottan kullanılacak miktar
                 */
                $kullanilacak =
                    min(
                        $kalan_satilacak,
                        $lot['miktar']
                    );

                /*
                 * FIFO maliyeti
                 */
                $lot_maliyeti =
                    $kullanilacak *
                    $lot['birim_maliyet'];

                $bu_satis_fifo_maliyeti +=
                    $lot_maliyeti;

                /*
                 * Lottan düş
                 */
                $lot['miktar'] -=
                    $kullanilacak;

                $lot['maliyet'] -=
                    $lot_maliyeti;

                $kalan_satilacak -=
                    $kullanilacak;

                /*
                 * Satılacak miktar tamamen karşılandı.
                 */
                if ($kalan_satilacak <= 0) {
                    break;
                }
            }

            unset($lot);

            /*
             * Eğer elde yeterli stok yoksa,
             * eksik miktar için FIFO maliyeti
             * üretme.
             */
            if ($kalan_satilacak > 0) {

                /*
                 * Burada negatif stok oluştuğunu
                 * açıkça işaretlemek daha güvenli.
                 */
                $realize_kar_zarar +=
                    (
                        $satis_geliri
                        - $satis_vergi
                        - $bu_satis_fifo_maliyeti
                    );

            } else {

                /*
                 * Net realize kâr:
                 *
                 * Satış geliri
                 * - satış vergisi
                 * - FIFO alış maliyeti
                 */
                $realize_kar_zarar +=
                    (
                        $satis_geliri
                        - $satis_vergi
                        - $bu_satis_fifo_maliyeti
                    );
            }
        }
    }

    /*
     * ---------------------------------------------------------
     * KALAN STOK
     * ---------------------------------------------------------
     */

    $kalan_miktar  = 0;
    $kalan_maliyet = 0;

    foreach ($alis_havuzu as $lot) {

        if ($lot['miktar'] > 0) {

            $kalan_miktar +=
                $lot['miktar'];

            $kalan_maliyet +=
                $lot['maliyet'];
        }
    }

    return [

        'alis_miktari' =>
            $toplam_alis_miktar,

        'alis_maliyeti' =>
            $toplam_alis_maliyet,

        'satis_miktari' =>
            $toplam_satis_miktar,

        'satis_geliri' =>
            $toplam_satis_gelir,

        'satis_vergi' =>
            $toplam_satis_vergi,

        'realize_kar' =>
            $realize_kar_zarar,

        'kalan_miktar' =>
            $kalan_miktar,

        'kalan_maliyet' =>
            $kalan_maliyet,

        'ort_alis' =>
            $toplam_alis_miktar > 0
                ? (
                    $toplam_alis_maliyet /
                    $toplam_alis_miktar
                )
                : 0,

        'ort_satis' =>
            $toplam_satis_miktar > 0
                ? (
                    $toplam_satis_gelir /
                    $toplam_satis_miktar
                )
                : 0
    ];
}

// =========================================================================
// --- BÖLÜM 4: METRİKLERİ PARALEL OLARAK HAFIZAYA BAĞLAMA ---
// =========================================================================
$doviz_analiz_sonuclari = [];

foreach (['USD', 'EUR'] as $para_birimi) {

    $doviz_analiz_sonuclari[$para_birimi] =
        hesaplaDovizFifoMetrikleri(
            $yatirim_araci[$para_birimi]
        );
}

// =========================================================================
// --- GÖRÜNÜM (VIEW) ÇAĞRISI ---
// =========================================================================
require admin_view("yatirim_arsiv_doviz");