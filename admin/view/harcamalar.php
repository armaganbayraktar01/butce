
<?php 
    ## Tags Bilgileri
    $meta = [
        'site_title' => 'Harcamalar | Bütçe Yönetimi',
        'site_desc' => 'Bütçe Yönetimi',
        'site_keyw' => 'Bütçe Yönetimi'
    ];
    # Tags Bilgileri

    require admin_view("static/header"); 

?>
<!-- =========================================================================
// 📑 HARCAMA ANALİZ VİEW KATMANI (4/4) - CSS TASARIM VE KAPANIŞ
// ========================================================================= -->

<!-- HARCAMA ANALİZ ÖZEL CSS ŞABLON PANELİ -->
<style>
/* 📊 Tablo Kaydırma Çubuğu Estetiği */
.table-responsive::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}
.table-responsive::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 4px;
}
.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #b3b3b3;
}

/* 🎯 Satır Üzerine Gelindiğinde Parlama Efekti */
.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.03) !important;
}

/* 📱 Yazıcı / Çıktı Alırken Tablo Koruma Kalkanı (A4 Yatay Baskı Formatı) */
@media print {
    body * {
        visibility: hidden;
    }
    .card-header bg-dark, .table-responsive, .table-responsive * {
        visibility: visible;
    }
    .table-responsive {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        overflow: visible !important;
    }
    table {
        border-collapse: collapse !important;
        width: 100% !important;
    }
    th, td {
        border: 1px solid #000 !important;
        padding: 4px !important;
        font-size: 9px !important;
        color: #000 !important;
    }
    .text-muted {
        opacity: 0.5 !important;
    }
}
</style>



<!-- =========================================================================
// 📑 REFORM: TEK SATIR 3 SÜTUN - İÇ İÇE BANKA DAĞILIM PANELDEN BAŞLANGIÇ (2/4)
// ========================================================================= -->

<!-- 🎯 KÜMÜLATİF ÖZET ÜST KARTLARI -->
<div class="row g-3 mb-4" style="font-size: 11px;">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-dark" style="border-radius: 6px; background-color: #f3e5f5; border-left: 4px solid #4a148c !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold m-0 font-monospace text-dark" style="font-size: 14px;"><?= number_format($toplam_genel_hacim, 2, ',', '.') ?> ₺</h6>
                    <span class="text-secondary small fw-bold text-uppercase" style="font-size: 9px; color: #4a148c !important;"><i class="fas fa-calculator me-1"></i> Kümülâtif Genel Hacim</span>
                </div>
                <i class="fas fa-wallet fs-4" style="color: #4a148c; opacity: 0.4;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-dark" style="border-radius: 6px; background-color: #fffde7; border-left: 4px solid #f57f17 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold m-0 font-monospace text-dark" style="font-size: 14px;"><?= number_format($toplam_vakif_hacmi, 2, ',', '.') ?> ₺</h6>
                    <span class="text-secondary small fw-bold text-uppercase" style="font-size: 9px; color: #f57f17 !important;"><i class="fas fa-university me-1"></i> VakıfBank Kümülatif Gideri</span>
                </div>
                <i class="fas fa-building-columns fs-4" style="color: #f57f17; opacity: 0.4;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-dark" style="border-radius: 6px; background-color: #e3f2fd; border-left: 4px solid #0d47a1 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold m-0 font-monospace text-dark" style="font-size: 14px;"><?= number_format($toplam_isbank_hacmi, 2, ',', '.') ?> ₺</h6>
                    <span class="text-secondary small fw-bold text-uppercase" style="font-size: 9px; color: #0d47a1 !important;"><i class="fas fa-credit-card me-1"></i> İş Bankası Kümülatif Gideri</span>
                </div>
                <i class="fas fa-credit-card fs-4" style="color: #0d47a1; opacity: 0.4;"></i>
            </div>
        </div>
    </div>
</div>

<!-- 🎯 REFORM 2: TEK SATIRDA YAN YANA 3 BÜYÜK ANALİZ SÜTUNU -->
<div class="row g-3 mb-5">
    
    <!-- 1. SÜTUN: Dönemlik Bütçe Akışı (İş + Vakıf + Toplam) -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius: 6px; overflow: hidden;">
            <div class="card-header bg-dark text-white p-2.5 border-0 fw-bold d-flex justify-content-between align-items-center" style="font-size: 11px;">
                <span><i class="fas fa-calendar-days text-warning me-1.5"></i> DÖNEMLİK BÜTÇE AKIŞI</span>
                <span class="badge bg-secondary bg-opacity-30 font-monospace" style="font-size: 8.5px;">Banka Dağılımlı</span>
            </div>
            <div class="table-responsive bg-white" style="max-height: 380px; overflow-y: auto;">
                <table class="table table-hover table-striped table-bordered m-0 align-middle text-end font-monospace" style="font-size: 9.5px; border-color: #eee !important;">
                    <thead class="table-light text-uppercase fw-bold text-secondary text-center" style="font-size: 9px;">
                        <tr>
                            <th class="text-start" style="width: 25%;">DÖNEM</th>
                            <th style="background-color: #f2f9ff; color: #0d47a1; width: 25%;">İŞ BANK</th>
                            <th style="background-color: #fffdf2; color: #f57f17; width: 25%;">VAKIF</th>
                            <th style="background-color: #fdf7ff; color: #4a148c; width: 25%;">TOPLAM</th>
                        </tr>
                    </thead>
                    <tbody class="fw-bold text-dark">
                        <?php foreach ($donemlik_analiz as $d_isim => $d_v): ?>
                            <tr>
                                <!-- Dönem hücresini tıklanabilir yapıyoruz -->
                                <td class="text-start text-secondary fw-bold donem-tetikleyici" 
                                    style="cursor: pointer;" 
                                    data-donem-kod="<?= $d_isim ?>"
                                    data-donem-adi="<?= date('m/Y', strtotime($d_isim . "-01")) ?>"
                                    title="Dönem mali raporu için tıklayın">
                                    <i class="fas fa-calendar-day me-1 text-muted small"></i> <?= date('m/Y', strtotime($d_isim . "-01")) ?>
                                </td>
                                <td style="background-color: #f2f9ff; color: #0d47a1;"><?= number_format($d_v['isbank'], 2, ',', '.') ?></td>
                                <td style="background-color: #fffdf2; color: #f57f17;"><?= number_format($d_v['vakif'], 2, ',', '.') ?></td>
                                <td style="background-color: #fdf7ff; color: #4a148c;"><?= number_format($d_v['toplam'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. SÜTUN: Kategori Gider Dağılımı (İş + Vakıf + Toplam) -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius: 6px; overflow: hidden;">
            <div class="card-header bg-dark text-white p-2.5 border-0 fw-bold d-flex justify-content-between align-items-center" style="font-size: 11px;">
                <span><i class="fas fa-tags text-info me-1.5"></i> KATEGORİ GİDER DAĞILIMI</span>
                <span class="badge bg-secondary bg-opacity-30 font-monospace" style="font-size: 8.5px;">Sıralı Rehber</span>
            </div>
            <div class="table-responsive bg-white" style="max-height: 380px; overflow-y: auto;">
                <table class="table table-hover table-striped table-bordered m-0 align-middle text-end font-monospace" style="font-size: 9.5px; border-color: #eee !important;">
                    <thead class="table-light text-uppercase fw-bold text-secondary text-center" style="font-size: 9px;">
                        <tr>
                            <th class="text-start" style="width: 25%;">KATEGORİ</th>
                            <th style="background-color: #f2f9ff; color: #0d47a1; width: 25%;">İŞ BANK</th>
                            <th style="background-color: #fffdf2; color: #f57f17; width: 25%;">VAKIF</th>
                            <th style="background-color: #fdf7ff; color: #4a148c; width: 25%;">TOPLAM</th>
                        </tr>
                    </thead>
                    <tbody class="fw-bold text-dark">
                        <?php foreach ($sirali_kategori_analiz as $tag_id => $k_v): 
                            $k_ismi = $kategori_haritasi[$tag_id] ?? 'Belirlenmedi';
                        ?>
                            <tr>
                                <!-- Kategori adı hücresini tıklanabilir yapıyoruz -->
                                <td class="text-start text-secondary fw-bold text-truncate matris-hucre-tetikleyici" 
                                    style="max-width: 85px; cursor: pointer;" 
                                    data-kat-id="<?= $tag_id ?>" 
                                    data-kat-adi="<?= htmlspecialchars($k_ismi) ?>"
                                    title="Analiz için tıklayın">
                                    <i class="fas fa-chart-pie me-1 text-muted small"></i> <?= htmlspecialchars($k_ismi) ?>
                                </td>

                                <td style="background-color: #f2f9ff; color: #0d47a1;"><?= number_format($k_v['isbank'], 2, ',', '.') ?></td>
                                <td style="background-color: #fffdf2; color: #f57f17;"><?= number_format($k_v['vakif'], 2, ',', '.') ?></td>
                                <td style="background-color: #fdf7ff; color: #4a148c;"><?= number_format($k_v['toplam'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. SÜTUN: Ticari Firma Dağılımı İç Veri Döngüsü -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius: 6px; overflow: hidden;">
            <div class="card-header bg-dark text-white p-2.5 border-0 fw-bold d-flex justify-content-between align-items-center" style="font-size: 11px;">
                <span><i class="fas fa-store text-success me-1.5"></i> TİCARİ FİRMA DAĞILIMI</span>
                <span class="badge bg-secondary bg-opacity-30 font-monospace" style="font-size: 8.5px;">Ciro Sıralı</span>
            </div>
            <div class="table-responsive bg-white" style="max-height: 380px; overflow-y: auto;">
                <table class="table table-hover table-striped table-bordered m-0 align-middle text-end font-monospace" style="font-size: 9.5px; border-color: #eee !important;">
                    <thead class="table-light text-uppercase fw-bold text-secondary text-center" style="font-size: 9px;">
                        <tr>
                            <th class="text-start" style="width: 25%;">FİRMA</th>
                            <th style="background-color: #f2f9ff; color: #0d47a1; width: 25%;">İŞ BANK</th>
                            <th style="background-color: #fffdf2; color: #f57f17; width: 25%;">VAKIF</th>
                            <th style="background-color: #fdf7ff; color: #4a148c; width: 25%;">TOPLAM</th>
                        </tr>
                    </thead>
                    <tbody class="fw-bold text-dark">
                        <?php foreach ($firma_analiz as $f_isim => $f_v): ?>
                            <tr>
                                <!-- Firma adı hücresini tıklanabilir yapıyoruz -->
                                <td class="text-start text-dark fw-bold text-truncate firma-tetikleyici" 
                                    style="max-width: 85px; cursor: pointer;" 
                                    data-firma-adi="<?= htmlspecialchars($f_isim) ?>"
                                    title="Firma detayları için tıklayın">
                                    <i class="fas fa-store me-1 text-muted small"></i> <?= htmlspecialchars($f_isim) ?>
                                </td>

                                <td style="background-color: #f2f9ff; color: #0d47a1; border: 1px solid #dee2e6 !important; font-weight: 700;"><?= number_format($f_v['isbank'], 2, ',', '.') ?> ₺</td>
                                <td style="background-color: #fffdf2; color: #f57f17; border: 1px solid #dee2e6 !important; font-weight: 700;"><?= number_format($f_v['vakif'], 2, ',', '.') ?> ₺</td>
                                <td style="background-color: #fdf7ff; color: #4a148c; border: 1px solid #dee2e6 !important; font-weight: 800;"><?= number_format($f_v['toplam'], 2, ',', '.') ?> ₺</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- =========================================================================
// 📑 HARCAMA ANALİZ VİEW KATMANI (3/4) - 3'LÜ BANKA MATRİS MOTORU (NİHAİ)
// ========================================================================= -->
<?php
    // Üst başlıklar ve dikey sütunlar için ortak renk palet şablon matrisi
    $grup_stil_haritasi = [
        'Gıda Harcamaları'       => ['bg' => '#e8f5e9', 'color' => '#1b5e20', 'soft' => '#f4fbf7'],
        'Ulaşım Harcamaları'     => ['bg' => '#f3e5f5', 'color' => '#4a148c', 'soft' => '#faf4fb'],
        'Alışveriş Harcamaları'  => ['bg' => '#fce4ec', 'color' => '#880e4f', 'soft' => '#fff5f7'],
        'Ev Harcamaları'         => ['bg' => '#e3f2fd', 'color' => '#0d47a1', 'soft' => '#f2f9ff'],
        'Seyahat Harcamaları'    => ['bg' => '#fffde7', 'color' => '#f57f17', 'soft' => '#fffdf2'],
        'Can Dostumuz'           => ['bg' => '#e0f7fa', 'color' => '#006064', 'soft' => '#f2fdfd'],
        'Sağlık Harcamaları'     => ['bg' => '#efebe9', 'color' => '#3e2723', 'soft' => '#fbfaf9'],
        'Vergi Ödemeleri'        => ['bg' => '#f5f5f5', 'color' => '#212529', 'soft' => '#fafafa']
    ];

    // 🎯 MAKRO MOTOR: 3 Tabloyu da Aynı Kusursuz Düzenle Basan Fonksiyon
    function mizanMatrisTablosuBas($tablo_basligi, $header_badge, $matris_verisi, $mizan_donemleri, $sirali_kategoriler, $stil_haritasi) {
?>
<div class="card border-0 shadow-sm mb-5" style="border-radius: 6px; overflow: hidden;">
    <div class="card-header bg-dark text-white p-3 border-0 d-flex align-items-center justify-content-between">
        <h6 class="card-title m-0 fw-bold d-inline-flex align-items-center" style="font-size: 12px; letter-spacing: 0.5px;">
            <i class="fas fa-table text-warning me-2"></i> <?= $tablo_basligi ?>
        </h6>
        <span class="badge px-2 py-1 font-monospace" style="font-size: 10px; background-color: rgba(255,255,255,0.15);"><?= $header_badge ?></span>
    </div>
    <div class="table-responsive bg-white">
        <table class="table table-bordered table-hover align-middle m-0 text-center mizan-border" style="font-size: 10.5px;">
            <thead class="table-light text-uppercase text-secondary fw-bold" style="font-size: 9.5px; letter-spacing: 0.2px;">
                <tr>
                    <th rowspan="2" style="width: 12%; background-color: #f1f3f5; color:#212529;" class="text-start align-middle">BÜTÇE DÖNEMİ</th>
                    <th rowspan="2" style="width: 12%; background-color: #f3e5f5; color: #4a148c;" class="text-end align-middle font-monospace border-lila-v">DÖNEM TOPLAMI</th>
                    <th colspan="6" style="background-color: #e8f5e9; color: #1b5e20;">🍔 Gıda Harcamaları</th>
                    <th colspan="1" style="background-color: #f3e5f5; color: #4a148c;">🚇 Ulaşım Harcamaları</th>
                    <th colspan="3" style="background-color: #fce4ec; color: #880e4f;">🛍️ Alışveriş Harcamaları</th>
                    <th colspan="4" style="background-color: #e3f2fd; color: #0d47a1;">🏠 Ev Harcamaları</th>
                    <th colspan="3" style="background-color: #fffde7; color: #f57f17;">✈️ Seyahat Harcamaları</th>
                    <th colspan="2" style="background-color: #e0f7fa; color: #006064;">🐾 Can Dostumuz</th>
                    <th colspan="1" style="background-color: #efebe9; color: #3e2723;">🩺 Sağlık Harcamaları</th>
                    <th colspan="1" style="background-color: #f5f5f5; color: #212529;">⚖️ Vergi Ödemeleri</th>
                </tr>
                <tr>
                    <?php 
                    $dikey_sutun_toplamlari = [];
                    foreach ($sirali_kategoriler as $s_kat): 
                        $dikey_sutun_toplamlari[$s_kat['id']] = 0.00;
                        $stil = $stil_haritasi[$s_kat['ust_grup']] ?? ['bg' => '#f5f5f5', 'color' => '#333'];
                    ?>
                        <th style="font-size: 9px; min-width: 80px; background-color: <?= $stil['bg'] ?>; color: <?= $stil['color'] ?>;" class="fw-bold"><?= htmlspecialchars($s_kat['kategori_adi']) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class="fw-medium text-dark">
                <?php 
                $genel_capraz_toplam = 0.00;
                foreach ($mizan_donemleri as $donem_kod): 
                    $satir_yatay_toplami = 0.00;
                    foreach ($sirali_kategoriler as $s_kat) { $satir_yatay_toplami += $matris_verisi[$donem_kod][$s_kat['id']] ?? 0.00; }
                    $parcali_tarih = strtotime($donem_kod . "-01");
                    $donem_baslik_metni = date('m / Y', $parcali_tarih);
                ?>
                    <tr>
                        <td class="text-start fw-bold text-secondary font-monospace" style="background-color: #fcfcfc; border: 1px solid #ddd !important;">
                            <?= $donem_baslik_metni ?>
                        </td>
                        <td class="text-end font-monospace fw-bold text-dark border-lila-v" style="font-size: 11px; background-color: #fdf7ff; border: 1px solid #ddd !important;">
                            <?= number_format($satir_yatay_toplami, 2, ',', '.') ?> ₺
                        </td>
                        
                        <?php foreach ($sirali_kategoriler as $s_kat): 
                            $hucre_tutari = $matris_verisi[$donem_kod][$s_kat['id']] ?? 0.00;
                            $dikey_sutun_toplamlari[$s_kat['id']] += $hucre_tutari;
                            $stil = $stil_haritasi[$s_kat['ust_grup']] ?? ['soft' => '#ffffff'];
                        ?>
                        <td class="font-monospace matris-hucre-tetikleyici <?= $hucre_tutari > 0 ? 'text-dark fw-bold' : 'text-muted opacity-30' ?>" 
                            style="font-size: 10px; background-color: <?= $stil['soft'] ?>; border: 1px solid #ddd !important; cursor: <?= $hucre_tutari > 0 ? 'pointer' : 'default' ?>;"
                            data-kat-id="<?= $s_kat['id'] ?>"
                            data-kat-adi="<?= htmlspecialchars($s_kat['kategori_adi']) ?>"
                            data-donem-kod="<?= $donem_kod ?>"
                            title="<?= $hucre_tutari > 0 ? $donem_kod.' '.$s_kat['kategori_adi'].' analizi için tıklayın' : '' ?>">
                            <?= $hucre_tutari > 0 ? number_format($hucre_tutari, 2, ',', '.') . ' ₺' : '0,00 ₺' ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                <?php 
                    $genel_capraz_toplam += $satir_yatay_toplami;
                endforeach; 
                ?>
            </tbody>
            <tfoot class="font-monospace text-dark" style="font-size: 10px; background-color: #f1f3f5 !important; font-weight: 800 !important;">
                <tr style="border-top: 2.5px solid #212529 !important;">
                    <td class="text-start text-uppercase text-dark" style="background-color: #e2e6ea; border: 1px solid #b8c1c5 !important; font-weight: 800;">TOPLAM</td>
                    <td class="text-end font-monospace fw-bold" style="font-size: 11.5px; background-color: #f3e5f5 !important; color: #000000 !important; border: 1px solid #b8c1c5 !important; font-weight: 800 !important;">
                        <?= number_format($genel_capraz_toplam, 2, ',', '.') ?> ₺
                    </td>
                    <?php foreach ($sirali_kategoriler as $s_kat): 
                        $dikey_toplam_degeri = $dikey_sutun_toplamlari[$s_kat['id']] ?? 0.00;
                        $stil = $stil_haritasi[$s_kat['ust_grup']] ?? ['bg' => '#fff', 'color' => '#000'];
                    ?>
                        <td style="font-size: 10.5px; color: <?= $stil['color'] ?>; background-color: <?= $stil['bg'] ?>; border: 1px solid #b8c1c5 !important; font-weight: 800;"><?= number_format($dikey_toplam_degeri, 2, ',', '.') ?> ₺</td>
                    <?php endforeach; ?>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php
    }

    // 🔥 🎯 ARTIK 3 TABLOYU SIRAYLA ALT ALTA BASIYORUZ
    mizanMatrisTablosuBas("YILLIK GENEL DÖNEM HARCAMA MATRİSİ (KÜMÜLATİF)", "Tüm Kartlar Birleşik", $yillik_matris_raporu, $mizan_donemleri_listesi, $sirali_kategori_listesi, $grup_stil_haritasi);
    mizanMatrisTablosuBas("VAKIFBANK EKSTRE DÖNEM HARCAMA MATRİSİ", "Sadece VakıfBank", $vakif_matris_raporu, $mizan_donemleri_listesi, $sirali_kategori_listesi, $grup_stil_haritasi);
    mizanMatrisTablosuBas("İŞ BANKASI (MAXIMUM) DÖNEM HARCAMA MATRİSİ", "Sadece İş Bankası", $isbank_matris_raporu, $mizan_donemleri_listesi, $sirali_kategori_listesi, $grup_stil_haritasi);
?>

<!-- =========================================================================
// 📥 VIEW KATMANI - İNTERAKTİF BÜTÇE İSTİHBARAT MODALLARI (1/2)
// ========================================================================= -->

<!-- 1. KATEGORİ DETAY VE TREND ANALİZ MODALI -->
<div class="modal fade" id="modalKategoriAnaliz" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px;">
            <div class="modal-header bg-dark text-white p-3">
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-light me-3 px-2 py-0.5" id="btnKategoriGeriDon" style="font-size: 9.5px; display: none;"><i class="fas fa-arrow-left me-1"></i> Geri Dön</button>
                    <h5 class="modal-title fw-bold" id="katModalBaslik" style="font-size: 13px;"><i class="fas fa-tags text-info me-2"></i> Kategori Analiz Raporu</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body bg-light p-3" style="font-size: 11px;">
                <div class="row g-3">
                    <div class="col-md-7">
                        <div class="card border-0 shadow-sm p-3 h-100 bg-white">
                            <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3" style="font-size: 11px;"><i class="fas fa-chart-bar text-warning me-1"></i> YILLIK DÖNEM TRENDİ</h6>
                            <div style="position: relative; height: 220px; width: 100%;"><canvas id="chartKategoriTrend"></canvas></div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card border-0 shadow-sm p-3 h-100 bg-white">
                            <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3" style="font-size: 11px;"><i class="fas fa-store text-success me-1"></i> EN ÇOK HARCANAN NOKTALAR</h6>
                            <ul class="list-group list-group-flush font-monospace" id="katTopFirmalarListesi" style="font-size: 11px;"></ul>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm p-3 bg-white">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <h6 class="fw-bold text-secondary m-0" style="font-size: 11px;"><i class="fas fa-list text-primary me-1"></i> KATEGORİ HARCAMA GEÇMİŞİ</h6>
                                <select class="form-select form-select-sm font-monospace fw-bold p-1 text-dark" id="selectKatDonemFiltre" style="font-size: 10px; width: 130px; border-radius: 4px;">
                                    <option value="tum">TÜM DÖNEMLER</option>
                                    <?php foreach($mizan_donemleri_listesi as $d_filtre): ?><option value="<?= $d_filtre ?>"><?= date('m / Y', strtotime($d_filtre . "-01")) ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                                <table class="table table-hover table-striped table-bordered m-0 align-middle text-end font-monospace" style="font-size: 10px;">
                                    <thead class="table-light text-center fw-bold text-secondary"><tr><th class="text-start">İşlem Tarihi</th><th class="text-start">Ticari Müessese</th><th>Kanal</th><th>Tutar</th></tr></thead>
                                    <tbody id="katHarcamaListesiGovde" class="fw-bold text-dark"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. TİCARİ FİRMA İSTİHBARAT VE DETAY MODALI -->
<div class="modal fade" id="modalFirmaAnaliz" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px;">
            <div class="modal-header bg-dark text-white p-3">
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-light me-3 px-2 py-0.5" id="btnFirmaGeriDon" style="font-size: 9.5px; display: none;"><i class="fas fa-arrow-left me-1"></i> Geri Dön</button>
                    <h5 class="modal-title fw-bold" id="firmaModalBaslik" style="font-size: 13px;"><i class="fas fa-store text-success me-2"></i> Firma İstihbarat Raporu</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body bg-light p-3" style="font-size: 11px;">
                <div class="row g-2 mb-3 text-center">
                    <div class="col"><div class="card border-0 p-2 shadow-sm bg-white" style="border-left: 3px solid #1b5e20 !important;"><div class="text-muted text-uppercase fw-bold mb-0.5" style="font-size: 8px;">Toplam Hacim</div><span class="fw-bold font-monospace text-dark" id="mFirmaToplamCiro" style="font-size: 12px;">0,00 ₺</span></div></div>
                    <div class="col"><div class="card border-0 p-2 shadow-sm bg-white" style="border-left: 3px solid #0d47a1 !important;"><div class="text-muted text-uppercase fw-bold mb-0.5" style="font-size: 8px;">Harcama Frekansı</div><span class="fw-bold font-monospace text-dark" id="mFirmaToplamIslem" style="font-size: 12px;">0 İşlem</span></div></div>
                    <div class="col"><div class="card border-0 p-2 shadow-sm bg-white" style="border-left: 4px solid #f57f17 !important;"><div class="text-muted text-uppercase fw-bold mb-0.5" style="font-size: 8px;">Ortalama Sepet</div><span class="fw-bold font-monospace text-dark" id="mFirmaOrtalamaSepet" style="font-size: 12px;">0,00 ₺</span></div></div>
                </div>
                <div class="row g-3">
                    <div class="col-md-12"><div class="card border-0 shadow-sm p-3 bg-white"><h6 class="fw-bold text-secondary border-bottom pb-2 mb-2" style="font-size: 11px;"><i class="fas fa-chart-line text-info me-1"></i> FİRMA TÜKETİM VE REKABET TRENDİ</h6><div style="position: relative; height: 160px; width: 100%;"><canvas id="chartFirmaTrend"></canvas></div></div></div>
                    <div class="col-md-12"><div class="card border-0 shadow-sm p-3 bg-white"><h6 class="fw-bold text-secondary border-bottom pb-2 mb-2" style="font-size: 11px;"><i class="fas fa-receipt text-warning me-1"></i> KRONOLOJİK HESAP EKSTRESİ</h6><div class="table-responsive" style="max-height: 180px; overflow-y: auto;"><table class="table table-hover table-striped table-bordered m-0 align-middle text-end font-monospace" style="font-size: 10px;"><thead class="table-light text-center fw-bold text-secondary"><tr><th class="text-start">İşlem Tarihi</th><th>Ödeme Aracı</th><th>Tutar</th></tr></thead><tbody id="firmaEkstreListesiGovde" class="fw-bold text-dark"></tbody></table></div></div></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 3. DÖNEMLİK MAKRO BÜTÇE KAPANIŞ BİLANÇO MODALI -->
<div class="modal fade" id="modalDonemAnaliz" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px;">
            <div class="modal-header bg-dark text-white p-3">
                <h5 class="modal-title fw-bold" id="donemModalBaslik" style="font-size: 13px;"><i class="fas fa-calendar-days text-warning me-2"></i> Dönem Mali Kapanış Bilançosu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body bg-light p-3" style="font-size: 11px;">
                <div class="row g-3">
                                        <!-- Sol Grafik: Kategorisel Dağılım Donut -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm p-3 bg-white h-100">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <h6 class="fw-bold text-secondary m-0" style="font-size: 11px;">
                                    <i class="fas fa-chart-pie text-danger me-1"></i> KATEGORİSEL GİDER DAĞILIMI (PASTASI)
                                </h6>
                                <!-- 🆕 TÜMÜ / FİLTREYİ SIFIRLA LİNK KÖPRÜSÜ -->
                                <a href="javascript:void(0);" id="btnDonemFiltreSifirla" class="badge bg-secondary text-white font-monospace text-decoration-none" style="font-size: 8px; display: none; padding: 3px 6px;">
                                    <i class="fas fa-rotate-left me-1"></i> TÜMÜNÜ GÖSTER
                                </a>
                            </div>
                            <div style="position: relative; height: 230px; width: 100%;" class="d-flex justify-content-center align-items-center">
                                <canvas id="chartDonemPasta"></canvas>
                            </div>
                        </div>
                    </div>

      
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm p-3 bg-white h-100"><h6 class="fw-bold text-secondary border-bottom pb-2 mb-2" style="font-size: 11px;"><i class="fas fa-scale-balanced text-primary me-1"></i> BANKA KART HAKİMİYET KIYASI</h6><div style="position: relative; height: 230px; width: 100%;"><canvas id="chartDonemBankaBar"></canvas></div></div>
                    </div>
                    
                    <!-- ========================================================================= -->
                    <!-- 🆕 BÜYÜK REFORM: HAFTALIK GRAFİK ODALARI VE GÜNLÜK İŞLEM MATRİSİ -->
                    <!-- ========================================================================= -->
                    <!-- SOL KÜTLE (%40): 4 Ana Haftanın Karşılaştırmalı Makro Dağılımı -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm p-3 bg-white h-100" style="border-radius: 6px;">
                            <h6 class="fw-bold text-secondary border-bottom pb-2 mb-2" style="font-size: 11px;">
                                <i class="fas fa-chart-column text-success me-1"></i> DÖNEM İÇİ HAFTALIK DAĞILIM TRENDİ <span class="badge bg-dark font-monospace text-warning" style="font-size:7.5px;">Barlara Tıklayın</span>
                            </h6>
                            <div style="position: relative; height: 210px; width: 100%;"><canvas id="chartDonemHaftalikBar"></canvas></div>
                        </div>
                    </div>

                    <!-- ORTA KÜTLE (%40): Seçilen Haftanın Kendi İçindeki Günlük ve Firmasal İstihbarat Grafikleri -->
                    <div class="col-md-5">
                        <div class="card border-0 shadow-sm p-2 bg-white h-100" style="border-radius: 6px; background-color: #fafafa !important;">
                            <div class="row g-2">
                                <!-- Grafik A: Haftalık Gün Bazlı Dağılım (Mavi) -->
                                <div class="col-md-12">
                                    <div class="p-2 bg-white border" style="border-radius:4px;">
                                        <h6 class="fw-bold text-dark m-0 pb-1 font-monospace" style="font-size: 9px;"><i class="fas fa-chart-area text-primary me-1"></i> HAFTALIK GÜN BAZLI DAĞILIM TRENDİ</h6>
                                        <div style="position: relative; height: 100px; width: 100%;"><canvas id="chartHaftaIciGunlukBar"></canvas></div>
                                    </div>
                                </div>
                                <!-- Grafik B: Firma Sadakat Dağılımı Yatay (Yeşil) -->
                                <div class="col-md-12">
                                    <div class="p-2 bg-white border" style="border-radius:4px;">
                                        <h6 class="fw-bold text-dark m-0 pb-1 font-monospace" style="font-size: 9px;"><i class="fas fa-chart-bar text-success me-1"></i> FİRMA / MARKA SADAKAT DAĞILIMI (CİRO)</h6>
                                        <div style="position: relative; height: 100px; width: 100%;"><canvas id="chartHaftaIciFirmaYatayBar"></canvas></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SAĞ KÜTLE (%3): Seçilen Haftanın Günlük Ham Veri Akış Matrisi -->
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm p-3 bg-white h-100" style="border-radius: 6px;">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <h6 class="fw-bold text-secondary m-0" style="font-size: 10px;"><i class="fas fa-calendar-day text-primary me-1"></i> İŞLEM MATRİSİ</h6>
                                <select class="form-select form-select-sm font-monospace fw-bold p-0 px-1 text-dark" id="selectHaftaSecici" style="font-size: 9px; width: 85px; border-radius: 4px; height: 20px;"><option value="Hafta 1">1. HAFTA</option><option value="Hafta 2">2. HAFTA</option><option value="Hafta 3">3. HAFTA</option><option value="Hafta 4">4. HAFTA</option></select>
                            </div>
                            <div class="table-responsive" style="max-height: 190px; overflow-y: auto;">
                                <table class="table table-hover table-striped table-bordered m-0 align-middle text-end font-monospace" style="font-size: 10px; border-color: #eee !important;">
                                    <tbody id="tbodyHaftalikGunlukGovde" class="fw-bold text-dark"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- ========================================================================= -->

                </div>
            </div>
        </div>
    </div>
</div>

<!-- PREMIUM KATEGORİ VE DÖNEM DETAY ANALİZ MODALI (BI INTEGRATED) -->
<div class="modal fade" id="kategoriDonemAnalizModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 90% !important;">
        <div class="modal-content border-0 shadow-lg text-secondary" style="border-radius: 8px; background-color: #f8fafc;">
            <!-- Modal Başlık Şeridi (Koyu Kömür Tonu) -->
            <div class="modal-header bg-dark text-white p-3 border-0 d-flex align-items-center justify-content-between">
                <h6 class="modal-title m-0 fw-bold d-inline-flex align-items-center" style="font-size: 13px; letter-spacing: 0.5px;">
                    <i class="fas fa-chart-pie text-warning me-2" style="font-size: 14px;"></i> 
                    [<span id="analizModalKategoriAdi">KATEGORİ</span>] KATEGORİSİ DETAY ANALİZİ (<span id="analizModalSeciliDonem">DÖNEM</span>)
                </h6>
                <button type="button" class="btn-close btn-close-white shadow-none m-0 p-1" data-bs-dismiss="modal" style="font-size: 10px;"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- SOL PANEL: SÜTUN GRAFİK ALANI -->
                    <div class="col-md-7">
                        <div class="bg-white p-3 rounded shadow-sm border border-light h-100">
                            <span class="fw-bold text-dark d-block mb-3" style="font-size: 11px; letter-spacing: 0.2px;"><i class="fas fa-chart-bar text-muted me-1.5"></i> YILLIK DÖNEM TRENDİ</span>
                            <div style="position: relative; height: 260px; width: 100%;">
                                <!-- Grafik kütüphanesi için canvas çekirdeği -->
                                <canvas id="analizTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SAĞ PANEL: EN ÇOK HARCANAN TOP 5 NOKTA -->
                    <div class="col-md-5">
                        <div class="bg-white p-3 rounded shadow-sm border border-light h-100">
                            <span class="fw-bold text-dark d-block mb-3" style="font-size: 11px; letter-spacing: 0.2px;"><i class="fas fa-store text-muted me-1.5"></i> EN ÇOK HARCANAN NOKTALAR</span>
                            <div id="analizTopFirmalarKonteyner" class="d-flex flex-column gap-2">
                                <!-- Dinamik olarak listelenecektir -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- ALT PANEL: DETAYLI İŞLEM GEÇMİŞİ LİSTESİ -->
                    <div class="col-12 mt-4">
                        <div class="bg-white rounded shadow-sm border border-light overflow-hidden">
                            <div class="p-3 bg-white border-bottom border-light d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-dark" style="font-size: 11px; letter-spacing: 0.2px;"><i class="fas fa-list text-muted me-1.5"></i> KATEGORİ HARCAMA GEÇMİŞİ</span>
                                <span class="badge bg-secondary bg-opacity-10 text-dark font-monospace fw-bold" id="analizTabloDonemRozet" style="font-size: 10px;">-</span>
                            </div>
                            <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                <table class="table table-hover align-middle m-0" style="font-size: 11px;">
                                    <thead class="table-light text-uppercase text-secondary font-monospace" style="font-size: 10px; font-weight: 700; position: sticky; top: 0; z-index: 10;">
                                        <tr>
                                            <th style="width: 15%;" class="ps-3">İşlem Tarihi</th>
                                            <th style="width: 35%;">Ticari Müessese / Açıklama</th>
                                            <th style="width: 20%;">Kanal</th>
                                            <th style="width: 15%;">Harcama Detay</th>
                                            <th style="width: 15%;" class="text-end pe-3">Tutar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="analizDokumTableBody" class="text-dark fw-medium">
                                        <!-- Dinamik veri satırları -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!-- /.row -->
            </div><!-- /.modal-body -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>




<!-- 🎨 SCROLLBAR VE GÖRSEL DESTEK CSS EKİ -->
<style>
    .table-responsive::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #fdfdfd;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #dddddd;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #b5b5b5;
    }
    .mizan-border, .mizan-border th, .mizan-border td { border: 1px solid #cccccc !important; }
    .border-lila-v { border-right: 2.5px solid #d8b4fe !important; border-left: 2.5px solid #d8b4fe !important; }
</style>

<script>

    // =========================================================================
    // 🎯 BÜTÇE İSTİHBARAT KOKPİTİ - AKILLI JAVASCRIPT MOTORU (1/4)
    // =========================================================================
    $(document).ready(function() {
        // 🛡️ Global Grafik Nesne Önbellek Kalkanları (Bellek Sızıntı Koruyucuları)
        let chartKatObj = null; 
        let chartFirmaObj = null;
        let chartDonemPastaObj = null; 
        let chartDonemBankaObj = null; 
        let chartDonemHaftalikBarObj = null;
        
        // 📊 Hafta İçi Görsel Analitik Mikro Grafik Nesne Kalkanları
        let chartHaftaIciGunlukBarObj = null; 
        let chartHaftaIciFirmaYatayBarObj = null;

        // 🔄 Dinamik Filtreleme ve Çapraz Geçiş İzleme Hafıza Havuzları
        let globalGunlukMatris = null;
        let globalHaftalikTrendHavuzu = null;
        let aktifIncelenenDonemKod = null; // Pasta süzgecinde dönemi kaybetmeme kalkanı
        let gecisHafizaIzleyici = { kaynakTipi: null, kaynakId: null, kaynakAdi: null };
        
        // Controller Katmanından Gelen PHP Kategori Haritasını JS Sözlüğüne Aktarma Köprüsü
        const kategoriIsimSozlugu = <?= json_encode($kategori_haritasi ?? []) ?>;

        // 🛠️ YARDIMCI SİHİRBAZ 1: Finansal Türk Lirası Formatlama Yardımcısı
        function formatMoneyTR(val, simge = true) {
            if (val === null || val === undefined || isNaN(val)) return simge ? '0,00 ₺' : '0,00';
            let f = parseFloat(val).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            return simge ? f + ' ₺' : f;
        }

        // 🛠️ YARDIMCI SİHİRBAZ 2: Kronolojik Tarih Formatlama (YYYY-MM-DD -> DD.MM.YYYY)
        function formatTarihTR(tarihHam) {
            if (!tarihHam) return '-';
            let sadeceTarih = tarihHam.split(' ')[0];
            return sadeceTarih.split('-').reverse().join('.');
        }
        // =========================================================================
        // 🔍 MOTOR A: KATEGORİ ANALİZ MODAL AKIŞI VE DİNAMİK FİLTRELEME
        // =========================================================================
        $('.kategori-tetikleyici').on('click', function() {
            let katId = $(this).data('kat-id'); 
            let katAdi = $(this).data('kat-adi');
            
            // Geçiş izleme havuzuna kaydet
            gecisHafizaIzleyici = { kaynakTipi: 'ana_tablo', kaynakId: katId, kaynakAdi: katAdi };
            $('#btnKategoriGeriDon').hide(); // Doğrudan açıldığı için geri dön butonunu gizle
            
            kategoriArayuzunuAc(katId, katAdi, 'tum');
        });

        // Kategori İçi Dinamik Dönem Süzgeci Değiştiğinde
        $('#selectKatDonemFiltre').on('change', function() {
            if (gecisHafizaIzleyici.kaynakId) {
                kategoriArayuzunuAc(gecisHafizaIzleyici.kaynakId, gecisHafizaIzleyici.kaynakAdi, $(this).val());
            }
        });

        function kategoriArayuzunuAc(katId, katAdi, donem) {
            $('#katModalBaslik').html('<i class="fas fa-tags text-info me-2"></i> [' + katAdi.toUpperCase() + '] KATEGORİSİ DETAY ANALİZİ');
            
            $.ajax({
                url: '?aksiyon=get_kategori_analiz',
                type: 'GET',
                data: { kategori_id: katId, donem: donem },
                dataType: 'json',
                success: function(res) {
                    if (!res.status) return;

                    // 1. Grafik: Yıllık Dönem Trendi (Bar Grafik)
                    let labels = []; let veriSeti = [];
                    res.trend.forEach(function(item) {
                        labels.push(item.donem.split('-').reverse().join('/'));
                        veriSeti.push(parseFloat(item.donem_toplam));
                    });
                    
                    if (chartKatObj) chartKatObj.destroy();
                    chartKatObj = new Chart(document.getElementById('chartKategoriTrend').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{ label: 'Harcama', data: veriSeti, backgroundColor: 'rgba(13, 110, 253, 0.65)', borderColor: '#0d6efd', borderWidth: 1, borderRadius: 4 }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
                    });

                    // 2. Sağ Sütun: Ciro Bazlı En Çok Harcanan Noktalar (Zincirlenebilir Link Yapısı)
                    let firmaHtml = '';
                    res.top_firmalar.forEach(function(f, idx) {
                        firmaHtml += `
                        <li class="list-group-item d-flex justify-content-between align-items-center p-2 text-dark border-bottom fw-bold">
                            <div>
                                <span class="badge bg-secondary me-2" style="font-size:9px;">${idx+1}</span>
                                <span class="link-firma-gecis text-primary" style="cursor:pointer; text-decoration:underline;" data-f-adi="${f.isletme_adi}">
                                    ${f.isletme_adi}
                                </span>
                            </div>
                            <span class="text-success">${formatMoneyTR(f.firma_toplam)}</span>
                        </li>`;
                    });
                    $('#katTopFirmalarListesi').html(firmaHtml || '<div class="text-muted text-center p-3">Veri Yok</div>');

                    // 3. Alt Tablo: Detaylı Harcama Geçmişi Listesi
                    let listeHtml = '';
                    res.liste.forEach(function(h) {
                        let kRenk = (h.kart_turu.toLowerCase().indexOf('vakif') !== -1 || h.kart_turu.toLowerCase().indexOf('world') !== -1) ? 'text-warning' : 'text-primary';
                        listeHtml += `<tr><td class="text-start text-secondary">${formatTarihTR(h.islem_tarihi)}</td><td class="text-start text-dark fw-bold">${h.isletme_adi}</td><td class="${kRenk} text-center"><i class="fas fa-credit-card me-1"></i>${h.kart_turu}</td><td class="fw-bold text-dark">${formatMoneyTR(h.tutar)}</td></tr>`;
                    });
                    $('#katHarcamaListesiGovde').html(listeHtml || '<tr><td colspan="4" class="text-center text-muted">Kayıt Yok</td></tr>');
                    
                    $('#modalKategoriAnaliz').modal('show');
                }
            });
        }

        // =========================================================================
        // 🔄 MOTOR B: İNTERAKTİF ZİNCİRLEME GEÇİŞ VE AKILLI REKURSİF GERİ TUŞU
        // =========================================================================
        $(document).on('click', '.link-firma-gecis', function() {
            let fAdi = $(this).data('f-adi');
            let aktifModalId = $(this).closest('.modal').attr('id');
            
            // Kullanıcının hangi ekrandan sıçradığını hafızaya kilitle
            if (aktifModalId === 'modalKategoriAnaliz') {
                gecisHafizaIzleyici.kaynakTipi = 'kategori_modal';
            } else if (aktifModalId === 'modalDonemAnaliz') {
                gecisHafizaIzleyici.kaynakTipi = 'donem_modal';
            }
            
            $('#' + aktifModalId).modal('hide');
            setTimeout(function() { firmaArayuzunuAc(fAdi, true); }, 400); // Animasyon senkron toleransı
        });

        // Ana Tablodan Doğrudan Firma İsmine Tıklanınca
        $('.firma-tetikleyici').on('click', function() {
            gecisHafizaIzleyici.kaynakTipi = 'ana_tablo';
            firmaArayuzunuAc($(this).data('firma-adi'), false);
        });

        // Stratejik Geri Dönüş Köprüsü Tetikleyicisi
        $('#btnFirmaGeriDon').on('click', function() {
            $('#modalFirmaAnaliz').modal('hide');
            setTimeout(function() {
                if (gecisHafizaIzleyici.kaynakTipi === 'kategori_modal') {
                    $('#modalKategoriAnaliz').modal('show');
                } else if (gecisHafizaIzleyici.kaynakTipi === 'donem_modal') {
                    // Dönem modalına geri dönerken mevcut süzgeci bozmamak için null kontrolüyle koruyoruz
                    $('#modalDonemAnaliz').modal('show');
                }
            }, 400);
        });
        // =========================================================================
        // 📊 MOTOR C: FİRMA DETAY VE STRATEJİK HESAP EKSTRESİ SAĞLAYICI
        // =========================================================================
        function firmaArayuzunuAc(firmaAdi, geriDonusGoster = false) {
            if (geriDonusGoster) $('#btnFirmaGeriDon').show(); else $('#btnFirmaGeriDon').hide();
            $('#firmaModalBaslik').html('<i class="fas fa-store text-success me-2"></i> [' + firmaAdi.toUpperCase() + '] TİCARİ ANALİZ ÖZETİ');
            
            $.ajax({
                url: '?aksiyon=get_firma_analiz',
                type: 'GET',
                data: { firma_adi: firmaAdi },
                dataType: 'json',
                success: function(res) {
                    if (!res.status) return;
                    
                    // Finansal Metrikleri Kartlara Giydirme
                    $('#mFirmaToplamCiro').text(formatMoneyTR(res.metrikler.toplam_ciro));
                    $('#mFirmaToplamIslem').text((res.metrikler.toplam_islem || 0) + ' İşlem');
                    $('#mFirmaOrtalamaSepet').text(formatMoneyTR(res.metrikler.ortalama_sepet || 0));

                    // Firma Trend Çizgi Grafiği
                    let labels = []; let veriSeti = [];
                    res.trend.forEach(function(item) {
                        labels.push(item.donem.split('-').reverse().join('/'));
                        veriSeti.push(parseFloat(item.donem_toplam));
                    });
                    
                    if (chartFirmaObj) chartFirmaObj.destroy();
                    chartFirmaObj = new Chart(document.getElementById('chartFirmaTrend').getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{ data: veriSeti, borderColor: '#2e7d32', backgroundColor: 'rgba(46, 125, 50, 0.05)', fill: true, tension: 0.15 }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
                    });

                    // Kronolojik Ekstre Listesi
                    let html = '';
                    res.ekstre.forEach(function(e) {
                        let kRenk = (e.kart_turu.toLowerCase().indexOf('vakif') !== -1 || e.kart_turu.toLowerCase().indexOf('world') !== -1) ? 'text-warning' : 'text-primary';
                        html += `<tr><td class="text-start text-secondary">${formatTarihTR(e.islem_tarihi)}</td><td class="${kRenk} text-center fw-bold"><i class="fas fa-credit-card me-1"></i>${e.kart_turu}</td><td class="fw-bold text-dark">${formatMoneyTR(e.tutar)}</td></tr>`;
                    });
                    $('#firmaEkstreListesiGovde').html(html || '<tr><td colspan="3" class="text-center text-muted">Kayıt Yok</td></tr>');
                    
                    $('#modalFirmaAnaliz').modal('show');
                }
            });
        }

        // =========================================================================
        // 📊 MOTOR D: DÖNEMLİK MAKRO KAPANIŞ VE İNTERAKTİF PASTA SÜZGECİ MOTORU
        // =========================================================================
        $('.donem-tetikleyici').on('click', function() {
            let dKod = $(this).data('donem-kod'); 
            let dAdi = $(this).data('donem-adi');
            
            aktifIncelenenDonemKod = dKod; // Dilim süzgeci için hafızaya kilitle
            $('#donemModalBaslik').html('<i class="fas fa-calendar-days text-warning me-2"></i> [' + dAdi + '] MALİ DÖNEM BÜTÇE KAPANIŞ BİLANÇOSU');
            
            donemBilancosunuYukle(dKod, ''); // İlk açılışta süzgeç kategorisi boş
        });

        function donemBilancosunuYukle(dKod, filtreKategoriAdi = '') {
            $.ajax({
                url: '?aksiyon=get_donem_analiz',
                type: 'GET',
                data: { donem_kod: dKod, kategori_adi: filtreKategoriAdi },
                dataType: 'json',
                success: function(res) {
                    if (!res.status) return;
                    
                    globalGunlukMatris = res.gunluk_islem_matrisi;
                    globalHaftalikTrendHavuzu = res.haftalik_trend;

                    // 🍕 1. REFORM: PASTA GRAFİK VE ADAPTİF DİLİM TIKLAMA AJANI
                    let pLabels = []; let pData = [];
                    res.kategoriler.forEach(function(item) {
                        pLabels.push(kategoriIsimSozlugu[parseInt(item.kategori_id)] || 'Grup');
                        pData.push(parseFloat(item.kategori_toplam));
                    });

                    // Önemli: Pasta grafik sadece ilk yüklemede veya filtre sıfırlandığında çizilir,
                    // böylece dilime basıldığında pasta grafiğin kendisi kaybolmaz, sabit kalır.
                    if (filtreKategoriAdi === '') {
                        $('#btnDonemFiltreSifirla').hide(); // Filtre yoksa sıfırlama linkini gizle
                        
                        if (chartDonemPastaObj) chartDonemPastaObj.destroy();
                        let ctxPasta = document.getElementById('chartDonemPasta').getContext('2d');
                        chartDonemPastaObj = new Chart(ctxPasta, {
                            type: 'doughnut',
                            data: { labels: pLabels, datasets: [{ data: pData, backgroundColor: ['#4a148c', '#0d47a1', '#1b5e20', '#f57f17', '#880e4f', '#212529', '#0d6efd', '#dc3545'] }] },
                            options: { 
                                responsive: true, maintainAspectRatio: false, 
                                plugins: { legend: { position: 'right', labels: { boxWidth: 8, font: { size: 9 } } } },
                                onClick: function(evt, element) {
                                    // 🛡️ CRITICAL FIX: Güncel Chart.js sürümleriyle tam uyumlu dizi elemanı kontrolü
                                    if (element && element.length > 0) {
                                        let activePoints = chartDonemPastaObj.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                                        if (activePoints.length > 0) {
                                            let firstPoint = activePoints[0];
                                            let tiklananKatAdi = chartDonemPastaObj.data.labels[firstPoint.index];
                                            
                                            // Modala dinamik süzgeç kırılım başlığı ekleme
                                            let anaBaslik = $('#donemModalBaslik').html().split(' -> ');
                                            $('#donemModalBaslik').html(anaBaslik[0] + ' -> <span class="text-info font-monospace">[' + tiklananKatAdi.toUpperCase() + ' SÜZGECİ]</span>');
                                            
                                            // Tüm ekranı o kategori kırılımına göre süzerek baştan aşağı yeniden akıt
                                            donemBilancosunuYukle(aktifIncelenenDonemKod, tiklananKatAdi);
                                        }
                                    }
                                }
                            }
                        });
                    } else {
                        // Eğer aktif bir süzgeç varsa "Tümünü Göster" butonunu görünür kıl
                        $('#btnDonemFiltreSifirla').show();
                    }

                    // 2. Grafik: Banka Kıyaslama Bar (Süzgeli)
                    if (chartDonemBankaObj) chartDonemBankaObj.destroy();
                    chartDonemBankaObj = new Chart(document.getElementById('chartDonemBankaBar').getContext('2d'), {
                        type: 'bar',
                        data: { labels: ['İŞ BANKASI', 'VAKIFBANK'], datasets: [{ label: 'Hacim', data: [parseFloat(res.bankalar.isbank_toplam || 0), parseFloat(res.bankalar.vakif_toplam || 0)], backgroundColor: ['rgba(13, 71, 161, 0.75)', 'rgba(245, 127, 23, 0.75)'] }] },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
                    });

                    // 3. Grafik: Makro Haftalık Sütun Dağılımı ve 🎯 BAR TIKLAMA HATA DÜZELTMESİ
                    if (chartDonemHaftalikBarObj) chartDonemHaftalikBarObj.destroy();
                    chartDonemHaftalikBarObj = new Chart(document.getElementById('chartDonemHaftalikBar').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: ['1. Hafta '+(res.haftalik_trend['Hafta 1'].label||''), '2. Hafta '+(res.haftalik_trend['Hafta 2'].label||''), '3. Hafta '+(res.haftalik_trend['Hafta 3'].label||''), '4. Hafta '+(res.haftalik_trend['Hafta 4'].label||'')],
                            datasets: [
                                { label: 'İş Bankası', data: [res.haftalik_trend['Hafta 1'].isbank, res.haftalik_trend['Hafta 2'].isbank, res.haftalik_trend['Hafta 3'].isbank, res.haftalik_trend['Hafta 4'].isbank], backgroundColor: 'rgba(13, 71, 161, 0.8)' },
                                { label: 'VakıfBank', data: [res.haftalik_trend['Hafta 1'].vakif, res.haftalik_trend['Hafta 2'].vakif, res.haftalik_trend['Hafta 3'].vakif, res.haftalik_trend['Hafta 4'].vakif], backgroundColor: 'rgba(245, 127, 23, 0.8)' }
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom', labels: { boxWidth: 8, font: { size: 9 } } } },
                            onClick: function(evt, element) {
                                // 🛡️ CRITICAL FIX 2: Sütun barlarına basınca selectbox'ı ve listeyi çalıştıran düzeltilmiş indis yakalayıcı
                                if (element && element.length > 0) {
                                    let firstBarPoint = element[0]; 
                                    let index = firstBarPoint.index; // Tıklanan barın tam sırasını güvenle aldık (0,1,2,3)
                                    let haftaAnahtari = 'Hafta ' + (index + 1);
                                    
                                    // 🌟 Selectbox değerini değiştir ve change olayını tetikle
                                    $('#selectHaftaSecici').val(haftaAnahtari).change();
                                }
                            }
                        }
                    });

                    // Varsayılan açılışta 1. haftanın tüm mikro yapılarını giydirme
                    $('#selectHaftaSecici').val('Hafta 1'); 
                    haftaninGunlukVerileriniBas('Hafta 1');
                    typeof haftaIciMikroGrafikleriCiz === 'function' && haftaIciMikroGrafikleriCiz('Hafta 1');

                    // Sekme Altı Ayrıştırılmış Banka Ekstre Döngüleri
                    let isHtml = ''; res.isbank_ekstre.forEach(function(h) { isHtml += `<tr><td class="text-start text-secondary">${formatTarihTR(h.islem_tarihi)}</td><td class="text-start text-dark fw-bold">${h.isletme_adi}</td><td class="text-primary fw-bold">${formatMoneyTR(h.tutar)}</td></tr>`; });
                    $('#donemIsbankEkstreGovde').html(isHtml || '<tr><td colspan="3" class="text-center p-3 text-muted">İşlem Yok</td></tr>');
                    
                    let vkHtml = ''; res.vakif_ekstre.forEach(function(h) { vkHtml += `<tr><td class="text-start text-secondary">${formatTarihTR(h.islem_tarihi)}</td><td class="text-start text-dark fw-bold">${h.isletme_adi}</td><td class="text-warning fw-bold">${formatMoneyTR(h.tutar)}</td></tr>`; });
                    $('#donemVakifEkstreGovde').html(vkHtml || '<tr><td colspan="3" class="text-center p-3 text-muted">İşlem Yok</td></tr>');
                    
                    $('#modalDonemAnaliz').modal('show');
                }
            });
        }

        // =========================================================================
        // 🆕 TÜMÜNÜ GÖSTER / SÜZGECİ KALDIR TETİKLEME DİNLEYİCİSİ
        // =========================================================================
        $(document).on('click', '#btnDonemFiltreSifirla', function() {
            if (aktifIncelenenDonemKod) {
                // Başlığı süzgeçsiz orjinal haline geri getir
                let anaBaslik = $('#donemModalBaslik').html().split(' -> ');
                $('#donemModalBaslik').html(anaBaslik[0]);
                
                // Kategori filtresini boş göndererek dönemin tam bilançosunu geri çek
                donemBilancosunuYukle(aktifIncelenenDonemKod, '');
            }
        });

        // Selectbox manuel değiştirildiğinde alt kütleleri senkronize etme motoru
        $('#selectHaftaSecici').on('change', function() { 
            let hAnahtar = $(this).val();
            haftaninGunlukVerileriniBas(hAnahtar); 
            typeof haftaIciMikroGrafikleriCiz === 'function' && haftaIciMikroGrafikleriCiz(hAnahtar);
        });

        // =========================================================================
        // 📊 KATEGORİ & DÖNEM MATRİS DETAY ANALİZ MOTORU ENGINE (PART 3/3)
        // =========================================================================
        let analizGrafikObjesi = null; // Grafik nesnesini hafızada tutma kalkanı

        /**
         * Matristeki herhangi bir kategori/dönem hücresine tıklandığında bu fonksiyon çağrılmalıdır.
         * Örnek tetikleme: analizModaliniAtesle(4, '2026-07');
         */
        function analizModaliniAtesle(kategoriId, hedefDonem) {
            if (!kategoriId || !hedefDonem) return;

            // Form veri hazırlığı yapıyoruz
            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: {
                    islem: 'kategori_donem_analiz_ajax',
                    kategori_id: parseInt(kategoriId),
                    donem: hedefDonem
                },
                dataType: 'json',
                success: function(res) {
                    if (res && res.success) {
                        // 1. Başlıkları ve Rozetleri Giydirme
                        $('#analizModalKategoriAdi').text(res.kategori_adi);
                        
                        // Tarihi AA / YYYY formatına getirip süslüyoruz
                        let parca = hedefDonem.split('-');
                        let formatliDonemBaslik = parca.length === 2 ? `${parca[1]} / ${parca[0]}` : hedefDonem;
                        $('#analizModalSeciliDonem, #analizTabloDonemRozet').text(formatliDonemBaslik);

                        // 2. EN ÇOK HARCANAN NOKTALAR (TOP 5) ALANINI BESLEME
                        let topFirmalarHtml = '';
                        if (res.top_firmalar && res.top_firmalar.length > 0) {
                            $.each(res.top_firmalar, function(index, f) {
                                let siraNo = index + 1;
                                let formatliFirmaTutari = parseFloat(f.toplam_harcama).toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + ' ₺';
                                topFirmalarHtml += `
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background-color: #f8fafc; border: 1px solid #f1f5f9;">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary bg-opacity-20 text-dark d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 10px; border-radius:50%; font-weight:700;">${siraNo}</span>
                                            <span class="fw-bold text-uppercase font-monospace text-truncate" style="max-width: 220px; font-size:11px; color:#334155;">${f.isletme_adi}</span>
                                        </div>
                                        <span class="fw-bold text-dark font-monospace" style="font-size:11.5px;">${formatliFirmaTutari}</span>
                                    </div>`;
                            });
                        } else {
                            topFirmalarHtml = '<div class="text-center p-4 text-muted font-monospace"><i class="fas fa-ghost d-block mb-1 opacity-50"></i> Veri Yok</div>';
                        }
                        $('#analizTopFirmalarKonteyner').html(topFirmalarHtml);

                        // 3. DETAYLI TABLO DÖKÜMÜNÜ BESLEME
                        let dokumHtml = '';
                        if (res.dokum && res.dokum.length > 0) {
                            $.each(res.dokum, function(index, d) {
                                let is_isbank = (d.kart_turu === 'isbank');
                                let kartBadge = is_isbank ? 'bg-primary bg-opacity-10 text-primary' : 'bg-warning bg-opacity-10 text-warning';
                                let kartAdi = is_isbank ? 'İş Bankası' : 'VakıfBank';
                                let formatliTutar = parseFloat(d.tutar).toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + ' ₺';
                                
                                // İşlem tarihi düzenleme (YYYY-MM-DD -> DD.MM.YYYY)
                                let tParca = d.islem_tarihi.split('-');
                                let formatliTarih = tParca.length === 3 ? `${tParca[2]}.${tParca[1]}.${tParca[0]}` : d.islem_tarihi;

                                dokumHtml += `
                                    <tr>
                                        <td class="font-monospace text-muted ps-3">${formatliTarih}</td>
                                        <td class="fw-bold text-uppercase font-monospace text-dark" style="font-size:10.5px;">${d.isletme_adi || 'BELİRLENMEDİ'}</td>
                                        <td><span class="badge ${kartBadge} px-2 py-0.5 fw-bold" style="font-size: 9px; border-radius:4px;"><i class="fas fa-credit-card me-1"></i>${kartAdi}</span></td>
                                        <td class="text-muted text-uppercase text-truncate font-monospace" style="max-width: 180px; font-size:10px;">${d.aciklama || '-'}</td>
                                        <td class="text-end font-monospace fw-bold text-dark pe-3" style="font-size:11.5px;">${formatliTutar}</td>
                                    </tr>`;
                            });
                        } else {
                            dokumHtml = '<tr><td colspan="5" class="text-center p-4 text-muted font-monospace">Bu döneme ait harcama kaydı bulunamadı.</td></tr>';
                        }
                        $('#analizDokumTableBody').html(dokumHtml);

                        // 4. CHART.JS SÜTUN GRAFİK ÇEKİRDEĞİ VE SEÇİLİ AY VURGULAMA REFORMU 📊
                        let grafikEtiketleri = [];
                        let grafikVerileri = [];
                        let grafikArkaPlanRenkleri = [];
                        let grafikKenarlikRenkleri = [];
                        let grafikSutunGenislikleri = [];

                        $.each(res.trend, function(index, item) {
                            let dParca = item.donem.split('-');
                            let etiketIsmi = dParca.length === 2 ? `${dParca[1]}/${dParca[0]}` : item.donem;
                            grafikEtiketleri.push(etiketIsmi);
                            grafikVerileri.push(parseFloat(item.toplam_tutar));

                            // ⚡ BURASI KRİTİK: Eğer çizilen ay, kullanıcının matriste tıkladığı ay ise rengini büyük ve opak yapıyoruz!
                            if (item.donem === hedefDonem) {
                                grafikArkaPlanRenkleri.push('rgba(59, 130, 246, 1)'); // Canlı Opak Mavi (Seçili Dönem)
                                grafikKenarlikRenkleri.push('#2563eb');
                            } else {
                                grafikArkaPlanRenkleri.push('rgba(59, 130, 246, 0.35)'); // Hafif Şeffaf Mat Mavi (Diğer Dönemler)
                                grafikKenarlikRenkleri.push('rgba(59, 130, 246, 0.5)');
                            }
                        });

                        // Eğer hafızada eski bir grafik nesnesi varsa DOM sızıntısını önlemek için yok ediyoruz
                        if (analizGrafikObjesi !== null) { analizGrafikObjesi.destroy(); }

                        let ctx = document.getElementById('analizTrendChart').getContext('2d');
                        analizGrafikObjesi = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: grafikEtiketleri,
                                datasets: [{
                                    label: 'Aylık Toplam Harcama ',
                                    data: grafikVerileri,
                                    backgroundColor: grafikArkaPlanRenkleri,
                                    borderColor: grafikKenarlikRenkleri,
                                    borderWidth: 1,
                                    borderRadius: 4,
                                    barPercentage: 0.65 // Sütunların şıklık kalınlığı
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 9, family: 'monospace' } } },
                                    x: { grid: { display: false }, ticks: { font: { size: 9, family: 'monospace' } } }
                                }
                            }
                        });

                        // Her şey başarıyla beslendi, şimdi modalı ekranda patlatıyoruz!
                        $('#kategoriDonemAnalizModal').modal('show');
                    } else {
                        alert('Analiz verileri yüklenirken hata oluştu: ' + res.message);
                    }
                },
                error: function() { alert('Analiz motoru sunucuyla bağlantı kuramadı!'); }
            });
        }

        // =========================================================================
        // 🎯 KÜMÜLATİF MATRİS HÜCRE TIKLAMA VE GÜVENLİK BARİYERİ BAĞLANTISI
        // =========================================================================

        $(document).on('click', '.matris-hucre-tetikleyici', function() {
            let katId = $(this).data('kat-id');
            let katAdi = $(this).data('kat-adi');
            let donemKod = $(this).data('donem-kod');
            let icerikTutari = $(this).text().trim();

            // Eğer hücre boşsa veya sıfırsa işlem yapma
            if (icerikTutari === "0,00 ₺" || icerikTutari === "0.00 ₺" || icerikTutari === "") return;

            // Geçiş izleme havuzuna kaynakları kilitle
            gecisHafizaIzleyici = { kaynakTipi: 'matris_hucre', kaynakId: katId, kaynakAdi: katAdi };
            $('#btnKategoriGeriDon').hide();

            // Düzeltilmiş fonksiyon çağrısı (Boşluk kaldırıldı 🛠️)
            matrisHucreAnaliziniAc(katId, katAdi, donemKod);
        });

        function matrisHucreAnaliziniAc(katId, katAdi, donemKod) {
            $('#katModalBaslik').html('<i class="fas fa-tags text-info me-2"></i> [' + katAdi.toUpperCase() + '] KATEGORİSİ - ' + donemKod + ' DETAY ANALİZİ');
            
            // Açılan modaldaki selectbox süzgecini otomatik o döneme getir
            $('#selectKatDonemFiltre').val(donemKod);

            $.ajax({
                url: window.location.pathname + '?aksiyon=get_kategori_analiz',
                type: 'GET',
                data: { kategori_id: katId, donem: donemKod },
                dataType: 'json',
                error: function(xhr, status, error) {
                    console.error("Kritik Hata Detayı:", xhr.responseText);
                    alert("Sunucuyla bağlantı kurulamadı. Lütfen F12 konsol kayıtlarını inceleyin.");
                },
                success: function(res) {
                    if (!res.status) return;

                    // 1. GRAFİK MOTORU: Seçilen dönemi koyu renkle parlatma 📊
                    let labels = []; 
                    let veriSeti = [];
                    let renkPaleti = [];

                    res.trend.forEach(function(item) {
                        let formatliDonem = item.donem.split('-').reverse().join('/');
                        labels.push(formatliDonem);
                        veriSeti.push(parseFloat(item.donem_toplam));

                        // Grafik çubuğu bizim tıkladığımız döneme eşitse kalın ve koyu renk yap
                        if (item.donem === donemKod) {
                            renkPaleti.push('rgba(13, 110, 253, 0.95)'); // Aktif Dönem Barı (Büyük ve Koyu)
                        } else {
                            renkPaleti.push('rgba(13, 110, 253, 0.20)'); // Diğer Dönemler (Mat ve Sönük)
                        }
                    });

                    if (chartKatObj) chartKatObj.destroy();
                    chartKatObj = new Chart(document.getElementById('chartKategoriTrend').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Harcama',
                                data: veriSeti,
                                backgroundColor: renkPaleti, // Dinamik adaptif palet enjekte edildi
                                borderColor: '#0d6efd',
                                borderWidth: 1,
                                borderRadius: 4
                            }]
                        },
                        options: { 
                            responsive: true, 
                            maintainAspectRatio: false, 
                            plugins: { legend: { display: false } } 
                        }
                    });

                    // 2. SAĞ SÜTUN: Firma Bazlı Dönemsel Harcama Listesi 🏢
                    let firmaHtml = '';
                    res.top_firmalar.forEach(function(f, idx) {
                        firmaHtml += `
                        <li class="list-group-item d-flex justify-content-between align-items-center p-2 text-dark border-bottom fw-bold">
                            <div>
                                <span class="badge bg-secondary me-2" style="font-size:9px;">${idx+1}</span>
                                <span class="link-firma-gecis text-primary" style="cursor:pointer; text-decoration:underline;" data-f-adi="${f.isletme_adi}">
                                    ${f.isletme_adi}
                                </span>
                            </div>
                            <span class="text-success">${formatMoneyTR(f.firma_toplam)}</span>
                        </li>`;
                    });
                    $('#katTopFirmalarListesi').html(firmaHtml || '<div class="text-muted text-center p-3">Bu ay firma kaydı yok.</div>');

                    // 3. ALT TABLO: Döneme Ait Kronolojik Ekstre Hesap Dökümü 📑
                    let listeHtml = '';
                    res.liste.forEach(function(h) {
                        let kRenk = (h.kart_turu.toLowerCase().indexOf('vakif') !== -1 || h.kart_turu.toLowerCase().indexOf('world') !== -1) ? 'text-warning' : 'text-primary';
                        listeHtml += `
                        <tr>
                            <td class="text-start text-secondary">${formatTarihTR(h.islem_tarihi)}</td>
                            <td class="text-start text-dark fw-bold">${h.isletme_adi}</td>
                            <td class="${kRenk} text-center"><i class="fas fa-credit-card me-1"></i>${h.kart_turu}</td>
                            <td class="fw-bold text-dark">${formatMoneyTR(h.tutar)}</td>
                        </tr>`;
                    });
                    $('#katHarcamaListesiGovde').html(listeHtml || '<tr><td colspan="4" class="text-center text-muted">Bu aya ait ham ekstre hareketi bulunamadı.</td></tr>');

                    // Tüm mimari dolduktan sonra modalı güvenle fırlat
                    $('#modalKategoriAnaliz').modal('show');
                }
            });
        }

        function haftaninGunlukVerileriniBas(haftaAnahtar) {
            if (!globalGunlukMatris || !globalGunlukMatris[haftaAnahtar]) return;
            let html = '';
            globalGunlukMatris[haftaAnahtar].forEach(function(islem) {
                let kRenk = (islem.kart.indexOf('VAKIF') !== -1) ? 'text-warning' : 'text-primary';
                html += `<tr><td class="text-start text-secondary font-monospace" style="font-size:9px; width:20%;">${islem.tarih.substring(0,5)}</td><td class="text-start text-dark text-truncate fw-bold link-firma-gecis" style="max-width:115px; width:55%; cursor:pointer; text-decoration:underline;" data-f-adi="${islem.firma}" title="Firma Analizine Git"><span style="font-size:8px;" class="${kRenk} me-1">●</span>${islem.firma}</td><td class="fw-bold text-dark font-monospace" style="width:25%; font-size:10px;">${formatMoneyTR(islem.tutar)}</td></tr>`;
            });
            $('#tbodyHaftalikGunlukGovde').html(html || '<tr><td colspan="3" class="text-center text-muted p-3">Kayıt Yok</td></tr>');
        }

        function haftaIciMikroGrafikleriCiz(haftaAnahtar) {
            if (!globalHaftalikTrendHavuzu || !globalHaftalikTrendHavuzu[haftaAnahtar]) return;
            let hVeri = globalHaftalikTrendHavuzu[haftaAnahtar];

            // 🔵 Mikro Grafik 1: Mavi Günlük Yük Dağılım Grafiği (Görsel Sol Taraf)
            let gunLabels = Object.keys(hVeri.gunler || {});
            let gunData = Object.values(hVeri.gunler || {});

            if (chartHaftaIciGunlukBarObj) chartHaftaIciGunlukBarObj.destroy();
            chartHaftaIciGunlukBarObj = new Chart(document.getElementById('chartHaftaIciGunlukBar').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: gunLabels.map(g => g.substring(0, 5)),
                    datasets: [{ label: 'Günlük Yük (₺)', data: gunData, backgroundColor: '#0d8ed9', borderRadius: 2 }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { font: { size: 8, family: 'monospace' } } },
                        x: { ticks: { font: { size: 8, weight: 'bold' } } }
                    }
                }
            });

            // 🟢 Mikro Grafik 2: Yeşil Yatay Ciro Sadakat Dağılım Grafiği (Görsel Sağ Taraf)
            let firmaLabels = Object.keys(hVeri.firmalar || {});
            let firmaData = Object.values(hVeri.firmalar || {});

            if (chartHaftaIciFirmaYatayBarObj) chartHaftaIciFirmaYatayBarObj.destroy();
            chartHaftaIciFirmaYatayBarObj = new Chart(document.getElementById('chartHaftaIciFirmaYatayBar').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: firmaLabels,
                    datasets: [{ label: 'Firma Cirosu (₺)', data: firmaData, backgroundColor: '#2e7d32', borderRadius: 2 }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, ticks: { font: { size: 8, family: 'monospace' } } },
                        y: { ticks: { font: { size: 8, weight: 'bold' } } }
                    }
                }
            });
        }

    });


</script>




<?php require admin_view('static/footer'); ?>