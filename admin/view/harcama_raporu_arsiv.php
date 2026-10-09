<!-- C:\wamp64\www\butce\admin\view\harcama_raporu_arsiv.php -->
<?php 
    ## Tags Bilgileri
    $meta = [
        'site_title' => 'Geçmiş Dönem Finansal Raporlar | Bütçe Yönetimi',
        'site_desc' => 'Bütçe Yönetimi',
        'site_keyw' => 'Bütçe Yönetimi'
    ];
    # Tags Bilgileri

    require admin_view("static/header"); 

?>

<style>

    /* Genel Kart Yapısı (Sıfır Gölge) */
    .excel-dashboard-wrapper .excel-ana-kart {
        box-shadow: none !important;
        border: 1px solid #bdc3c7 !important;
    }
    .excel-dashboard-wrapper .excel-baslik { background-color: #2b303a; border: 0; }
    .excel-dashboard-wrapper .excel-baslik-metin { font-size: 12px; line-height: 1; }
    .excel-dashboard-wrapper .excel-baslik-ikon { font-size: 13px; }
    .excel-dashboard-wrapper .excel-baslik-rozet { font-size: 9px; background-color: #198754; color: #ffffff; }
    .excel-dashboard-wrapper .bg-temiz-taban { background-color: #fafafa; }

    /* Net Excel Kenarlık (Border) ve Yazı Boyutu Standartları */
    .excel-table { border-collapse: collapse !important; width: 100% !important; }
    .excel-table th, .excel-table td {
        box-shadow: none !important;
        vertical-align: middle !important;
        font-weight: 600;
        font-size: 9px !important; /* Küçük ve şık font nizamı */
        border: 1px solid #bdc3c7 !important; /* Gerçek net Excel border çizgileri */
        padding: 4px 6px !important;
        line-height: 1.2 !important;
    }
    .excel-table thead th { font-weight: 700; font-size: 9px !important; }

    /* Başlık Satırları Nizamı */
    .excel-blok-toplam-satir { height: 28px; font-weight: 800; }
    .excel-ana-grup-satir { font-size: 9px; height: 24px; }
    .excel-alt-baslik-satir { height: 24px; font-size: 9px; background-color: #f1f3f5; color: #495057; }
    .excel-mikro-toplam-satir { height: 24px; background-color: #f8f9fa; color: #495057; }

    /* Hücre Sınıfları ve Pastel Renk Atamaları */
    .excel-table .bg-koyu-kemik { background-color: #212529; }
    .excel-table .bg-gri-kemik { background-color: #495057; }
    .excel-table .faiz-baslik-hucre { background-color: #d4edda; color: #155724; }
    .excel-table .fatura-nakit-baslik-hucre { background-color: #cff4fc; color: #055160; }
    
    .excel-table .excel-group-gelir-th { background-color: #e8f5e9; color: #2e7d32; }
    .excel-table .excel-group-gider-th { background-color: #ffebee; color: #c62828; }
    .excel-table .excel-group-nakit-th { background-color: #fff3e0; color: #e65100; }
    .excel-table .excel-group-tasarruf-th { background-color: #e3f2fd; color: #1565c0; }
    .excel-table .excel-group-ktasarruf-th { background-color: #c6dff1; color: #1565c0; }
    .excel-table .excel-group-yatirim-th { background-color: #f3e5f5; color: #6a1b9a; }
    .excel-table .excel-group-kyatirim-th { background-color: #ebd8ed; color: #6a1b9a; }
    .excel-table .excel-gelir-hucre { background-color: #e8f5e9; color: #2e7d32; }
    .excel-table .excel-faiz-hucre { background-color: #d4edda; color: #155724; }
    .excel-table .excel-gider-hucre { background-color: #ffebee; color: #c62828; }
    .excel-table .excel-fatura-nakit-toplam-hucre { background-color: #dfdad0; color: #664d03; font-weight: bold; }
    .excel-table .excel-fatura-detay-th-hucre { background-color: #dfdad0; color: #664d03; }

    /* Döngü Satırları ve Dinamik Durumlar */
    .excel-table .excel-govde-satir { height: 24px; }
    .excel-table .excel-v-ortali { vertical-align: middle !important; }
    .excel-table .excel-donem-baslik-hucre { background-color: #f1f3f5; vertical-align: middle; }
    .excel-table .excel-govde-faiz-baskisi { background-color: #f4fbf7; }
    .excel-table .excel-fatura-nakit-govde-hucre { background-color: #f0fbfc; }
    .excel-table .excel-fatura-detay-hucre { font-size: 8px !important; }
    
    .excel-table .excel-karsilastirma-hucre { background-color: #f4f9ff; }
    .excel-table .excel-yatirim-hucre { background-color: #faf5fc; }
    
    .excel-table .excel-pozitif-tutar { color: #0d6efd !important; }
    .excel-table .excel-negatif-tutar { color: #dc3545 !important; }
    .excel-table .excel-mor-tutar { color: #6a1b9a !important; }

    /* Aktif Dönem Satırı Pastel Vurgusu */
    .excel-table .excel-aktif-satir { background-color: #fff9db !important; font-weight: 600; }
    .excel-table .excel-aktif-satir td { color: #495057 !important; }
   
    

</style>

<div class="container-fluid py-3 solsutunstyle">
    <!-- Üst Kontrol Barı -->
    <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-2 rounded shadow-sm border border-light">
        <h5 class="m-0 fw-bold text-dark d-flex align-items-center" style="font-size: 14px;">
            <i class="bi bi-calendar-check text-primary me-2"></i> 
            Dönem Finansal Takip 
            <span class="badge bg-primary ms-2 font-monospace" style="font-size: 11px;">
                <?= htmlspecialchars($aktif_yil) ?> Yılı Arşivi
            </span>
        </h5>
        <div>
            <!-- ÖZELLİK 2: Eski butonlar kaldırıldı, yerine GET tetikleyen temiz Selectbox eklendi -->
            <div class="input-group input-group-sm">
                <label class="input-group-text bg-light fw-bold text-secondary" for="yil-secimi" style="font-size: 11px;">
                    <i class="bi bi-filter me-1"></i> Yıl Seç:
                </label>
                <select class="form-select form-select-sm fw-bold text-dark" id="yil-secimi" style="font-size: 11px; width: 100px;" onchange="location.href='?donem=' + this.value;">
                    <?php
                    foreach ($selectbox_years as $donem) {
                        $selected = ($donem == $aktif_yil) ? 'selected' : '';
                        echo "<option value='{$donem}' {$selected}>{$donem}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <!-- 🎯 PARÇA 1: SIFIR INLINE CSS - EXCEL RAPORU BAŞLIK VE BLOK TOPLAMLARI -->
    <div class="row g-3 mt-2 mb-4 excel-dashboard-wrapper">
        <div class="col-12">
            <div class="card excel-ana-kart bg-white">
                
                <!-- Başlık Alanı -->
                <div class="card-header excel-baslik text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold d-inline-flex align-items-center excel-baslik-metin">
                        <i class="fas fa-th text-success me-2 excel-baslik-ikon"></i> Finansal Dönemler Arşiv Raporu
                    </span>
                    <span class="badge excel-baslik-rozet font-monospace"><?= number_format(($yillik_toplam['genel_tasarruf']-$yillik_toplam['genel_yatirim']), 2, ',', '.') ?> ₺</span>
                </div>

                <div class="card-body p-2 bg-temiz-taban">
                    <div class="table-responsive excel-temiz-responsive">
                        <table class="table table-sm table-bordered align-middle text-center mb-0 excel-table">
                            <thead>

                                <!-- SATIR 1: EXCEL ÜST BÜTÜNLEŞİK BLOK TOPLAMLARI (PHP Hesaplamaları) -->
                                <tr class="excel-blok-toplam-satir">
                                    <th class="excel-group-tasarruf-th text-center"></th>
                                    <th colspan="5" class="excel-group-gelir-th text-center"></th>
                                    <th colspan="5" class="excel-group-gider-th text-center"></th>
                                    <th colspan="6" class="excel-group-ktasarruf text-end"><?= $aktif_yil . " Yıl Sonu İtibariyle Devredecek Tasarruf Miktarı"?></th>
                                    <th colspan="6" class="excel-group-ktasarruf text-center"><?= number_format($kumulatif_net_tasarruf + $yillik_toplam['butun_tasarruf'] - ($kumulatif_net_yatirim + $yillik_toplam['butun_yatirim']), 2, ',', '.') ?> ₺</th>

                                </tr>
                                
                                <!-- SATIR 1: EXCEL ÜST BÜTÜNLEŞİK BLOK TOPLAMLARI (PHP Hesaplamaları) -->
                                <tr class="excel-blok-toplam-satir">
                                    <th class="excel-group-ktasarruf-th text-center"></th>
                                    <th colspan="5" class="excel-group-gelir-th text-center"><?= number_format($yillik_toplam['butun_gelir'], 2, ',', '.') ?> ₺</th>
                                    <th colspan="5" class="excel-group-gider-th text-center"><?= number_format($yillik_toplam['butun_gider'], 2, ',', '.') ?> ₺</th>
                                    <th colspan="6" class="excel-group-ktasarruf text-end"><?= $aktif_yil . " Dönem Sonu Verileri (Kümülatif)" ?></th>
                                    <th class="excel-group-ktasarruf text-center"><?= number_format($kumulatif_net_tasarruf + $yillik_toplam['butun_tasarruf'], 2, ',', '.') ?> ₺</th>
                                    <th class="excel-group-kyatirim text-center"><?= number_format($kumulatif_net_yatirim + $yillik_toplam['butun_yatirim'], 2, ',', '.') ?> ₺</th>
                                </tr>

                                <!-- SATIR 2: ANA GRUP BAŞLIKLARI -->
                                <tr class="fw-bold text-uppercase excel-ana-grup-satir">
                                    <th rowspan="1" class="excel-group-ktasarruf-th"><?= $aktif_yil - 1 . " - DEVİR"?></th>
                                    <th colspan="5" class="excel-group-gelir-th">GELİRLER TOPLAMI</th>
                                    <th colspan="5" class="excel-group-gider-th">GİDERLER TOPLAMI</th>
                                    <th colspan="6" class="excel-group-ktasarruf-th text-end"><?=$aktif_yil - 1 . " ve Öncesi Dönem Verileri (Kümülatif)" ?></th>
                                    <th class="excel-group-tasarruf-th text-center"><?= number_format($kumulatif_net_tasarruf, 2, ',', '.') ?> ₺</th>
                                    <th class="excel-group-yatirim-th text-center"><?= number_format($kumulatif_net_yatirim, 2, ',', '.') ?> ₺</th>
                                </tr>
                                
                                <!-- SATIR 3: SÜTUN ALT BAŞLIKLARI -->
                                <tr class="excel-head-title fw-bold excel-alt-baslik-satir">
                                    <th class=""></th>
                                    <th>MAAŞ (Ö)</th>
                                    <th>MAAŞ (A)</th>
                                    <th>MAKTU MESAİ</th>
                                    <th>EK GELİR</th>
                                    <th class="faiz-baslik-hucre">FAİZ G.</th>
                                    <th>KREDİ KARTI (Ö)</th>
                                    <th>KREDİ KARTI (A)</th>
                                    <th>KREDİ</th>
                                    <th>AİDAT</th>
                                    <th>NAKİT HAR.</th>
                                    <th class="fatura-nakit-baslik-hucre">F. TOPLAM</th>
                                    <th>ELEKTRİK</th>
                                    <th>SU</th>
                                    <th>DOĞALGAZ</th>
                                    <th>TELEFON</th>
                                    <th>İNTERNET</th>
                                    <th class="excel-group-ktasarruf-th">TASARRUF</th>
                                    <th class="excel-group-kyatirim-th">YATIRIM</th>
                                </tr>


                                <!-- 🎯 PARÇA 2: SÜTUN BAZLI MİKRO TOPLAM KUTUCUKLARI VE 12 AYLIK DÖNGÜ -->
                                <tr class="tepe-kutu text-end font-monospace fw-bold excel-mikro-toplam-satir">
                                    <td class="text-center"></td>
                                    <td class="excel-gelir-hucre"><?= number_format($tepe_toplamlar['maas_o'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-gelir-hucre"><?= number_format($tepe_toplamlar['maas_a'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-gelir-hucre"><?= number_format($tepe_toplamlar['maktu_mesai'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-gelir-hucre"><?= number_format($tepe_toplamlar['ek_gelir'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-faiz-hucre"><?= number_format($tepe_toplamlar['faiz_gelir'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-gider-hucre"><?= number_format($tepe_toplamlar['kredi_karti_o'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-gider-hucre"><?= number_format($tepe_toplamlar['kredi_karti_a'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-gider-hucre"><?= number_format($tepe_toplamlar['ev_kredisi'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-gider-hucre"><?= number_format($tepe_toplamlar['aidat'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-gider-hucre"><?= number_format($tepe_toplamlar['harcama_nakit'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-fatura-nakit-toplam-hucre"><?= number_format($tepe_toplamlar['fatura_nakit'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-fatura-detay-th-hucre"><?= number_format($tepe_toplamlar['elektrik'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-fatura-detay-th-hucre"><?= number_format($tepe_toplamlar['su'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-fatura-detay-th-hucre"><?= number_format($tepe_toplamlar['dogalgaz'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-fatura-detay-th-hucre"><?= number_format($tepe_toplamlar['telefon'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-fatura-detay-th-hucre"><?= number_format($tepe_toplamlar['internet'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-group-tasarruf-th"><?= number_format($yillik_toplam['butun_tasarruf'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-group-yatirim-th"><?= number_format($yillik_toplam['butun_yatirim'], 2, ',', '.') ?> ₺</td>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php for ($i = 1; $i <= 12; $i++): 
                                    $satir_var_mi = isset($yillik_matris[$i]);
                                    $s_veri = $satir_var_mi ? $yillik_matris[$i] : [];
                                    $is_aktif_ay = ($aktif_donem === sprintf("{$aktif_yil}-%02d", $i));

                                    $s_canli_faiz = floatval($s_veri['faiz_gelir'] ?? 0);
                                    $s_fatura_nakit_toplami = floatval($s_veri['elektrik'] ?? 0) + floatval($s_veri['su'] ?? 0) + floatval($s_veri['dogalgaz'] ?? 0) + floatval($s_veri['telefon'] ?? 0) + floatval($s_veri['internet'] ?? 0);

                                    $s_gelir = floatval($s_veri['maas_o'] ?? 0) + floatval($s_veri['maas_a'] ?? 0) + floatval($s_veri['maktu_mesai'] ?? 0) + floatval($s_veri['ek_gelir'] ?? 0) + $s_canli_faiz;
                                    $s_gider = floatval($s_veri['kredi_karti_o'] ?? 0) + floatval($s_veri['kredi_karti_a'] ?? 0) + floatval($s_veri['ev_kredisi'] ?? 0) + floatval($s_veri['aidat'] ?? 0) + floatval($s_veri['harcama_nakit'] ?? 0) + $s_fatura_nakit_toplami;
                                    
                                    $s_tasarruf = $s_gelir - $s_gider;
                                    $s_yatirim = floatval($s_veri['yatirim_tutari'] ?? 0);

                                    
                                    
                                    $satir_sinifi = $is_aktif_ay ? 'excel-aktif-satir' : 'excel-standart-satir';
                                ?>
                                    <tr class="border-bottom border-light excel-govde-satir <?= $satir_sinifi ?>">
                                        <td class="text-start ps-2 fw-bold text-secondary excel-donem-baslik-hucre">
                                            <?= $ay_isimleri[$i] ?> <?=$aktif_yil;?>
                                        </td>
                                        <td class="text-end font-monospace text-success pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['maas_o'] ?? 0) > 0 ? number_format($s_veri['maas_o'], 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        <td class="text-end font-monospace text-success pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['maas_a'] ?? 0) > 0 ? number_format($s_veri['maas_a'], 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        <td class="text-end font-monospace text-success pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['maktu_mesai'] ?? 0) > 0 ? number_format($s_veri['maktu_mesai'], 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        <td class="text-end font-monospace text-success pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['ek_gelir'] ?? 0) > 0 ? number_format($s_veri['ek_gelir'], 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        <td class="text-end font-monospace text-success pe-2 excel-v-ortali excel-govde-faiz-baskisi"><?= $s_canli_faiz > 0 ? number_format($s_canli_faiz, 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        
                                        <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['kredi_karti_o'] ?? 0) > 0 ? number_format($s_veri['kredi_karti_o'], 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['kredi_karti_a'] ?? 0) > 0 ? number_format($s_veri['kredi_karti_a'], 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['ev_kredisi'] ?? 0) > 0 ? number_format($s_veri['ev_kredisi'], 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['aidat'] ?? 0) > 0 ? number_format($s_veri['aidat'], 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['harcama_nakit'] ?? 0) > 0 ? number_format($s_veri['harcama_nakit'], 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        <!-- 🎯 PARÇA 3: FATURA NAKİT TOPLAMI, TABLO KAPANIŞI VE MERKEZİ CSS ALANI -->
                                        <td class="text-end font-monospace text-dark fw-bold text-center excel-fatura-nakit-govde-hucre"><?= $satir_var_mi && $s_fatura_nakit_toplami > 0 ? number_format($s_fatura_nakit_toplami, 2, ',', '.') . ' ₺' : '0,00 ₺' ?></td>
                                        
                                        <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['elektrik'] ?? 0) > 0 ? number_format($s_veri['elektrik'], 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['su'] ?? 0) > 0 ? number_format($s_veri['su'], 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['dogalgaz'] ?? 0) > 0 ? number_format($s_veri['dogalgaz'], 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['telefon'] ?? 0) > 0 ? number_format($s_veri['telefon'], 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['internet'] ?? 0) > 0 ? number_format($s_veri['internet'], 2, ',', '.'). ' ₺' : '0,00 ₺'?></td>
                                        
                                        <?php $t_color_class = ($s_tasarruf >= 0) ? 'excel-pozitif-tutar' : 'excel-negatif-tutar'; ?>
                                        <td class="text-end font-monospace fw-bold pe-2 excel-karsilastirma-hucre <?= $t_color_class ?>">
                                            <?= $satir_var_mi && $s_gelir > 0 ? number_format($s_tasarruf, 2, ',', '.') . ' ₺' : '0,00 ₺' ?>
                                        </td>
                                        
                                        <?php $y_color_class = ($s_yatirim >= 0) ? 'excel-mor-tutar' : 'excel-negatif-tutar'; ?>
                                        <td class="text-end font-monospace fw-bold pe-2 excel-yatirim-hucre <?= $y_color_class ?>">
                                            <?= $satir_var_mi && $s_gelir > 0 ? number_format($s_yatirim, 2, ',', '.') . ' ₺' : '0,00 ₺' ?>
                                        </td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>     
            </div>
        </div>
    </div>
</div>
<?php require admin_view('static/footer'); ?>
