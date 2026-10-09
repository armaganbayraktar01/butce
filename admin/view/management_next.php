<?php 

    ## Tags Bilgileri
    $meta = [
        'site_title' => 'Gelecek Dönem Planlama Monitörü | Bütçe Yönetimi',
        'site_desc' => 'Bütçe Yönetimi',
        'site_keyw' => 'Bütçe Yönetimi'
    ];
    # Tags Bilgileri

    require admin_view("static/header"); 



    // Sabit kelime filtresi tanımı (Arayüz kontrolü için)
    $sabit_kelimeler = ['Maaş', 'Mesai', 'Maktu', 'KK', 'Kredi', 'Faturası', 'Damacana', 'İçme Suyu', 'Aidat', 'Nergiz Cep'];



    // 1. Kartların Harcanabilir Limitlerinin Varlık Kontrolü (Sıfır mı?)
    $isbank_limit_var_mi = floatval($kkart['isbank']['kart_limiti'] ?? 0) > 0;
    $vakif_limit_var_mi  = floatval($kkart['vakifbank']['kart_limiti'] ?? 0) > 0;


    // 2. İŞBANKASI MANTIKSAL BARİYERİ
    if (!$isbank_limit_var_mi) {
        $isbank_harcanabilir = 0.00;
    } else {
        $isbank_harcanabilir = ($kkart['isbank']['harcanabilir_limit'] ?? 0) - ($kkart['isbank']['donem_ici'] ?? 0) - $isbank_aktif_taksit;
    }

    // 3. VAKIFBANK MANTIKSAL BARİYERİ
    if (!$vakif_limit_var_mi) {
        $vakif_harcanabilir = 0.00;
    } else {
        $vakif_harcanabilir  = ($kkart['vakifbank']['harcanabilir_limit'] ?? 0) - ($kkart['vakifbank']['donem_ici'] ?? 0) - $vakif_aktif_taksit;
    }

    // Negatif bakiye koruması (Ekstra Sigorta: Hesaplama sonucu sıfırın altına düşerse 0 yap)
    if ($isbank_harcanabilir < 0) { $isbank_harcanabilir = 0.00; }
    if ($vakif_harcanabilir < 0)  { $vakif_harcanabilir = 0.00; }


?>

<style>
    /* Ortak CSS Mimarisi ve Tipografi Standartları */
    .butcestyle, .solsutunstyle, .monitorstyle { font-size: 11px !important; }
    .butcestyle .card-header, .solsutunstyle .card-header, .monitorstyle .card-header { font-size: 12px !important; font-weight: bold; }
    
    .butcestyle input.form-control-sm, .solsutunstyle input.form-control-sm { 
        font-size: 11px !important; 
        font-weight: 600; 
        background: transparent; 
        border: none; 
        padding: 2px 4px;
        box-shadow: none !important;
    }
    
    .butcestyle .row-odendi input { text-decoration: line-through; color: #6c757d !important; opacity: 0.6; }
    .butcestyle .btn-mikro { height: 22px; line-height: 1; font-size: 10px; width: 45px; padding: 2px; font-weight: bold; }
    .butcestyle .input-ekle { border: 1px solid #dee2e6 !important; background: #fff !important; border-radius: 4px; padding: 4px 8px !important; }
    
    .solsutunstyle table { font-size: 11px !important; }
    .solsutunstyle table thead tr th { font-size: 11px !important; }
    .solsutunstyle .card-footer { font-size: 11px !important; }
    
    .monitorstyle .text-uppercase { font-size: 11px !important; font-weight: bold; }
    .monitorstyle .font-monospace { font-size: 11px !important; }
    
    /* Dinamik Satır Vurguları */
    .excel-aktif-satir { background-color: #fff3cd !important; font-weight: 600; border-top: 1.5px solid #ffc107 !important; border-bottom: 1.5px solid #ffc107 !important; }
</style>

<div class="container-fluid py-3 solsutunstyle">
    <!-- Üst Kontrol Barı -->
    <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-2 rounded shadow-sm border border-light">
        <h5 class="m-0 fw-bold text-dark d-flex align-items-center" style="font-size: 14px;">
            <i class="bi bi-eye-fill text-warning me-2"></i> 
            Gelecek Dönem Planlama Monitörü 
            <span class="badge bg-warning text-dark ms-2 font-monospace" style="font-size: 11px;">
                <?= htmlspecialchars($projeksiyon_donemi) ?>
            </span>
        </h5>
        <div>
            <span class="badge bg-light text-danger border border-danger-subtle px-3 py-2 fw-bold" style="font-size: 11px;">
                <i class="bi bi-shield-lock-fill me-1"></i> SALT OKUNUR PROJEKSİYON (ASIL DÖNEM KORUMALI)
            </span>
        </div>
    </div>

    <!-- ANA GÖVDE PANELİ -->
    <div class="row g-3 mb-3">

        <!-- ==================== SOL SÜTUN: TAKSİT VE KART GİRDİLERİ ==================== -->
        <div class="col-md-3">
            
            <!-- KART 1: Projeksiyon Taksitleri Matrisi -->
            <form id="taksit-formu" onsubmit="return false;">
                <div class="card shadow-sm border-0 bg-white mb-2">
                    <div class="card-header bg-white border-bottom border-light py-2 px-3 d-flex justify-content-between align-items-center">
                        <span class="text-dark fw-bold">
                            <i class="bi bi-calendar-range text-secondary me-1"></i> Projeksiyon Taksitleri
                        </span>
                        <span class="badge bg-light text-secondary font-monospace" style="font-size: 9px;">Kilitli</span>
                    </div>
                    <div class="card-body p-2">
                        <div style="max-height: 340px; overflow-y: auto;">
                            <table class="table table-sm table-borderless align-middle mb-0" style="table-layout: fixed;">
                                <thead>
                                    <tr class="border-bottom border-light text-center fw-bold text-muted">
                                        <th style="width: 32%; text-align: left;" class="p-0 pb-1">DÖNEM</th>
                                        <th style="width: 34%; color: #004b87;" class="p-0 pb-1">İŞ BANK (A)</th>
                                        <th style="width: 34%; color: #b38f00;" class="p-0 pb-1">VAKIF (Ö)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($db_taksitler as $t_satir): 
                                        $is_hedef_ay = (intval($t_satir['ay_index']) === $projeksiyon_ay_index);
                                    ?>
                                    <tr class="border-bottom border-light <?= $is_hedef_ay ? 'excel-aktif-satir' : '' ?>" style="height: 22px;">
                                        <td class="p-0 text-secondary fw-semibold ps-1">
                                            <?= sprintf("%02d", $t_satir['ay_index']) ?>.<?= $next_year_int; ?> <?= $is_hedef_ay ? '🎯' : '' ?>
                                        </td>
                                        <td class="p-0 px-1">
                                            <input type="number" class="form-control form-control-sm border-0 bg-transparent p-0 text-end text-dark fw-bold" value="<?= number_format($t_satir['isbank_tutar'], 2, '.', '') ?>" disabled style="cursor: not-allowed;">
                                        </td>
                                        <td class="p-0 px-1">
                                            <input type="number" class="form-control form-control-sm border-0 bg-transparent p-0 text-end text-dark fw-bold" value="<?= number_format($t_satir['vakif_tutar'], 2, '.', '') ?>" disabled style="cursor: not-allowed;">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer py-1 px-2 border-0 d-flex align-items-center justify-content-between" style="background-color: #f1f3f5;">
                        <span class="text-secondary fw-bold" style="font-size:9.5px;">Kalan Taksitler Toplamı</span>
                        <div class="d-flex gap-1" style="width: 60%;">
                            <input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-dark font-monospace" readonly value="<?= number_format($isbank_kalan_taksitler_toplami, 2, ',', '.') ?> ₺">
                            <input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-dark font-monospace" readonly value="<?= number_format($vakif_kalan_taksitler_toplami, 2, ',', '.') ?> ₺">
                        </div>
                    </div>
                </div>
            </form>

            <!-- KART 2: Cari Ay Girdileri Simülasyonu -->
            <form id="cari-girdiler-formu" onsubmit="return false;">
                <div class="card shadow-sm border-0 bg-white mb-2">
                    <div class="card-header bg-white border-bottom border-light py-1 px-3 d-flex justify-content-between align-items-center">
                        <span class="text-dark fw-bold">
                            <i class="bi bi-calculator text-secondary me-1"></i> Projeksiyon Kart Girdileri
                        </span>
                        <span class="badge bg-light text-warning-emphasis font-monospace" style="font-size: 9px;">Simülasyon</span>
                    </div>
                    <div class="card-body p-2">
                        <table class="table table-sm table-borderless align-middle mb-0" style="table-layout: fixed;">
                            <tbody>
                                <tr class="border-bottom border-light" style="height: 22px; background-color: #fcfcfc;">
                                    <td class="p-0 ps-1 text-muted fw-semibold" style="font-size:10px; vertical-align: middle;">Gelecek Dönem Taksit</td>
                                    <td class="p-0 px-1"><input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-dark font-monospace" readonly value="<?= number_format($isbank_aktif_taksit ?? 0, 2, ',', '.') ?> ₺" style="background-color: #e9ecef !important; font-size:10px !important;"></td>
                                    <td class="p-0 px-1"><input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-dark font-monospace" readonly value="<?= number_format($vakif_aktif_taksit ?? 0, 2, ',', '.') ?> ₺" style="background-color: #e9ecef !important; font-size:10px !important;"></td>
                                </tr>
                                <tr class="border-bottom border-light" style="height: 24px;">
                                    <td class="p-0 ps-1 text-muted" style="font-size:10px; vertical-align: middle;">Dönem İçi Harc.</td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="isbank-donem-ici-input" class="form-control form-control-sm text-end p-0 pe-1 fw-bold text-danger font-monospace border rounded bg-white" value="<?= number_format($kkart['isbank']['donem_ici'] ?? 0, 2, '.', '') ?>" style="height: 19px; font-size:10px !important;" oninput="gelecekDonemKartGuncelle('isbank', 'donem_ici', this.value)">
                                    </td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="vakif-donem-ici-input" class="form-control form-control-sm text-end p-0 pe-1 fw-bold text-danger font-monospace border rounded bg-white" value="<?= number_format($kkart['vakifbank']['donem_ici'] ?? 0, 2, '.', '') ?>" style="height: 19px; font-size:10px !important;" oninput="gelecekDonemKartGuncelle('vakifbank', 'donem_ici', this.value)">
                                    </td>
                                </tr>
                                <tr class="border-bottom border-light" style="height: 24px;">
                                    <td class="p-0 ps-1 text-muted" style="font-size:10px; vertical-align: middle;">Provizyonda Bek.</td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="isbank-provizyon-input" class="form-control form-control-sm text-end p-0 pe-1 fw-bold text-dark font-monospace border rounded bg-white" value="<?= number_format($kkart['isbank']['provizyon'] ?? 0, 2, '.', '') ?>" style="height: 19px; font-size:10px !important;" oninput="gelecekDonemKartGuncelle('isbank', 'provizyon', this.value)">
                                    </td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="vakif-provizyon-input" class="form-control form-control-sm text-end p-0 pe-1 fw-bold text-warning-emphasis font-monospace border rounded bg-white" value="<?= number_format($kkart['vakifbank']['provizyon'] ?? 0, 2, '.', '') ?>" style="height: 19px; font-size:10px !important;" oninput="gelecekDonemKartGuncelle('vakifbank', 'provizyon', this.value)">
                                    </td>
                                </tr>
                                <!-- REFORM: Konsolide Döviz Borcu (Sıfırlanmaya Duyarlı Dinamik Hücreler) -->
                                <tr class="border-bottom border-light" style="height: 24px;">
                                    <td class="p-0 ps-1 text-muted" style="font-size:10px; vertical-align: middle;">Döviz Borcu</td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="isbank-doviz-input" 
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-medium text-dark font-monospace border rounded bg-white" 
                                            value="<?= number_format($kkart['isbank']['doviz_borcu'] ?? 0, 2, '.', '') ?>" 
                                            style="height: 19px; font-size:10px !important;" 
                                            oninput="gelecekDonemKartGuncelle('isbank', 'doviz_borcu', this.value)">
                                    </td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="vakif-doviz-input" 
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-medium text-dark font-monospace border rounded bg-white" 
                                            value="<?= number_format($kkart['vakifbank']['doviz_borcu'] ?? 0, 2, '.', '') ?>" 
                                            style="height: 19px; font-size:10px !important;" 
                                            oninput="gelecekDonemKartGuncelle('vakifbank', 'doviz_borcu', this.value)">
                                    </td>
                                </tr>
                                <!-- REFORM: Euro Borcu Giriş Hücreleri -->
                                <tr class="border-bottom border-light" style="height: 24px;">
                                    <td class="p-0 ps-1 text-muted" style="font-size:10px; vertical-align: middle;">Euro Borcu</td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="isbank-eu-input" readonly
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-medium text-dark font-monospace border rounded bg-white" 
                                            value="<?= number_format($kkart['isbank']['eu_borcu'] ?? 0, 2, '.', '') ?>" 
                                            style=" background-color: #e9ecef !important; cursor: not-allowed; height: 19px; font-size:10px !important;" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top"
                                                data-bs-title="<?= number_format($kkart['isbank']['eu_borc_tl'] ?? 0, 2, ',', '.') ?> ₺"
                                            oninput="gelecekDonemKartGuncelle('isbank', 'eu_borcu', this.value)">
                                    </td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="vakif-eu-input" readonly
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-medium text-dark font-monospace border rounded bg-white" 
                                            value="<?= number_format($kkart['vakifbank']['eu_borcu'] ?? 0, 2, '.', '') ?>" 
                                            style=" background-color: #e9ecef !important; cursor: not-allowed; height: 19px; font-size:10px !important;" 
                                            data-bs-toggle="tooltip" 
                                            data-bs-placement="top"
                                            data-bs-title="<?= number_format($kkart['vakifbank']['eu_borc_tl'] ?? 0, 2, ',', '.') ?> ₺"
                                            oninput="gelecekDonemKartGuncelle('vakifbank', 'eu_borcu', this.value)">
                                    </td>
                                </tr>
                                <!-- REFORM: Dolar Borcu Giriş Hücreleri -->
                                <tr class="border-bottom border-light" style="height: 24px;">
                                    <td class="p-0 ps-1 text-muted" style="font-size:10px; vertical-align: middle;">Dolar Borcu</td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="isbank-usd-input" readonly
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-medium text-dark font-monospace border rounded bg-white" 
                                            value="<?= number_format($kkart['isbank']['usd_borcu'] ?? 0, 2, '.', '') ?>" 
                                            style=" background-color: #e9ecef !important; cursor: not-allowed; height: 19px; font-size:10px !important;" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top"
                                                data-bs-title="<?= number_format($kkart['isbank']['usd_borc_tl'] ?? 0, 2, ',', '.') ?> ₺"
                                            oninput="gelecekDonemKartGuncelle('isbank', 'usd_borcu', this.value)">
                                    </td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="vakif-usd-input" readonly
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-medium text-dark font-monospace border rounded bg-white" 
                                            value="<?= number_format($kkart['vakifbank']['usd_borcu'] ?? 0, 2, '.', '') ?>" 
                                            style=" background-color: #e9ecef !important; cursor: not-allowed; height: 19px; font-size:10px !important;" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top"
                                                data-bs-title="<?= number_format($kkart['vakifbank']['usd_borc_tl'] ?? 0, 2, ',', '.') ?> ₺"
                                            oninput="gelecekDonemKartGuncelle('vakifbank', 'usd_borcu', this.value)">
                                    </td>
                                </tr>


                                 <tr class="border-bottom border-light" style="height: 24px; background-color: #fffaf0;">
                                    <td class="p-0 ps-1 text-dark fw-bold" style="font-size:10px; vertical-align: middle;">Harcama Limiti</td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="isbank-harcama-limiti-input" class="form-control form-control-sm text-end p-0 pe-1 fw-bold text-dark font-monospace border rounded bg-white"
                                            value="<?= number_format($kkart['isbank']['harcanabilir_limit'] ?? 0, 2, '.', '') ?>"
                                            style="height: 19px; font-size:10px !important;" oninput="gelecekDonemKartGuncelle('isbank', 'harcanabilir_limit', this.value)"
                                        >
                                    </td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="vakif-harcama-limiti-input" class="form-control form-control-sm text-end p-0 pe-1 fw-bold text-dark font-monospace border rounded bg-white"
                                            value="<?= number_format($kkart['vakifbank']['harcanabilir_limit'] ?? 0, 2, '.', '') ?>"
                                            style="height: 19px; font-size:10px !important;" oninput="gelecekDonemKartGuncelle('vakifbank', 'harcanabilir_limit', this.value)"
                                        >
                                    </td>
                                </tr>
                                <tr class="border-bottom border-light" style="height: 24px; background-color: #f4fbf7;">
                                    <td class="p-0 ps-1 text-success fw-bold" style="font-size:10px; vertical-align: middle;">Harcanabilir Bakiye</td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 pe-1 text-end fw-bold text-success font-monospace" readonly value="<?= number_format($isbank_harcanabilir, 2, ',', '.') ?> ₺" style="background-color: #e9ecef !important; font-size:10px !important; cursor: default;">
                                    </td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 pe-1 text-end fw-bold text-success font-monospace" readonly value="<?= number_format($vakif_harcanabilir, 2, ',', '.') ?> ₺" style="background-color: #e9ecef !important; font-size:10px !important; cursor: default;">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer py-2 px-2 border-0 d-flex align-items-center justify-content-between" style="background-color: #f1f3f5;">
                        <span class="text-secondary fw-bold" style="font-size:9.5px;">Öngörülen Dönem Yükü</span>
                        <div class="d-flex gap-1" style="width: 60%;">
                            <input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-dark font-monospace" readonly value="<?= number_format($isbank_card_borcu, 2, ',', '.') ?> ₺">
                            <input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-dark font-monospace" readonly value="<?= number_format($vakif_card_borcu, 2, ',', '.') ?> ₺">
                        </div>
                    </div>
                </div>

                <!-- ALT KART: LİMİT VE TOPLAM BORÇ ARŞİV PANELİ -->
                <div class="card shadow-sm border-0 bg-white mb-1">
                    <div class="card-body p-2">
                        <table class="table table-sm table-borderless align-middle mb-0" style="table-layout: fixed;">
                            <tbody>
                                <tr style="height: 22px; background-color: #f8f9fa;">
                                    <td class="p-0 ps-1 text-secondary fw-semibold">Kredi Kartı Limiti</td>
                                    <td class="p-0 px-1"><input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-secondary font-monospace" readonly value="321.100,00 ₺" style="font-size:10px !important;"></td>
                                    <td class="p-0 px-1"><input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-secondary font-monospace" readonly value="263.500,00 ₺" style="font-size:10px !important;"></td>
                                </tr>
                                <tr class="border-bottom border-light" style="font-size:10px !important; height: 24px; background-color: #fdf2f2;">
                                    <td class="p-0 ps-1 text-danger-emphasis fw-bold">Toplam Borç</td>
                                    <td class="p-0 px-1"><input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-danger font-monospace" readonly value="<?= number_format($isbank_nihai_toplam_borc, 2, ',', '.') ?> ₺"></td>
                                    <td class="p-0 px-1"><input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-danger font-monospace" readonly value="<?= number_format($vakif_nihai_toplam_borc, 2, ',', '.') ?> ₺"></td>
                                </tr>
                                <tr style="font-size:10px !important; height: 24px; background-color: #e3f2fd;">
                                    <td class="p-0 ps-1 text-primary-emphasis fw-bold">Kalan Limit</td>
                                    <td class="p-0 px-1"><input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-primary font-monospace" readonly value="<?= number_format(max(0, 321100.00 - $isbank_nihai_toplam_borc), 2, ',', '.') ?> ₺"></td>
                                    <td class="p-0 px-1"><input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-primary font-monospace" readonly value="<?= number_format(max(0, 263500.00 - $vakif_nihai_toplam_borc), 2, ',', '.') ?> ₺"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>

            <!-- 📱 BAĞIMSIZ MOBİL PROVİZYON İSTASYONU KARTI (ERKEN POST ENGELLEMELİ) -->
            <div class="card shadow-sm border-0 bg-white mb-1 mt-2">
                <div class="card-header bg-dark text-white py-1 px-3 d-flex justify-content-between align-items-center" style="font-size:11px !important;">
                    <span class="text-white fw-bold"><i class="bi bi-phone-vibrate me-1 text-warning"></i> Mobil Provizyon İstasyonu</span>
                    <span class="badge bg-secondary font-monospace" style="font-size:8px;">Canlı Matris</span>
                </div>
                <div class="card-body p-2" style="background-color: #fdfbf7;">
                    <table class="table table-sm table-borderless align-middle mb-0" style="table-layout: fixed;">
                        <thead>
                            <tr class="text-center text-muted fw-bold" style="font-size:9px;">
                                <th style="width:32%; text-align:left;" class="p-0">ALAN</th>
                                <th style="width:34%; color:#004b87;" class="p-0">İŞBANK (A)</th>
                                <th style="width:34%; color:#b38f00;" class="p-0">VAKIF (Ö)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Mobil Uygulama Kullanılabilir Limit Girişleri (Erken post hatası üreten oninput kaldırıldı) -->
                            <tr style="height: 24px;">
                                <td class="p-0 ps-1 text-dark fw-semibold" style="font-size:10px;">Kullanılabilir Limit</td>
                                <td class="p-0 px-1">
                                    <input type="number" step="0.01" id="mobil_isbank_kull_limit" class="form-control form-control-sm text-end fw-bold text-dark font-monospace mobil-hesap-input" placeholder="0.00" style="background:#ffffff; border:1px solid #ced4da; border-radius:3px; height:19px; padding:0 4px; font-size:10px;">
                                </td>
                                <td class="p-0 px-1">
                                    <input type="number" step="0.01" id="mobil_vakif_kull_limit" class="form-control form-control-sm text-end fw-bold text-dark font-monospace mobil-hesap-input" placeholder="0.00" style="background:#ffffff; border:1px solid #ced4da; border-radius:3px; height:19px; padding:0 4px; font-size:10px;">
                                </td>
                            </tr>
                            <!-- Hesaplanan Canlı Provizyon Sonuç Göstergeleri -->
                            <tr style="height: 26px; background-color: #e8f4fd; border-top: 1px dashed #74c0fc;">
                                <td class="p-0 ps-1 text-primary-emphasis fw-bold text-uppercase" style="font-size:9px !important;">Provizyon</td>
                                <td class="p-0 px-1"><input type="text" id="isbank_hesaplanan_provizyon" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-primary font-monospace" readonly value="0,00 ₺" style="font-size:11px;"></td>
                                <td class="p-0 px-1"><input type="text" id="vakif_hesaplanan_provizyon" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-primary font-monospace" readonly value="0,00 ₺" style="font-size:11px;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        
        <!-- ==================== ORTA SÜTUN: TASLAK GELİR VE GİDER YÖNETİMİ ==================== -->
        <div class="col-md-5 butcestyle">
            
            <!-- GELİR AKIŞ PROJEKSİYON KARTI --> 
            <div class="card shadow-sm border-0 bg-white mb-3">
                <div class="card-header bg-white border-bottom border-light py-2 px-3 text-success d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-wallet2 me-1"></i> Projeksiyon Gelir Akış Planı</span>
                    <span class="badge bg-light text-success font-monospace" style="font-size: 9px;">Taslak Modu</span>
                </div>
                <form id="gelir-butce-formu" onsubmit="return false;">
                    <div class="card-body p-2">
                        <?php foreach ($gelirler as $kalem): 
                            $gosterilecek_gelir = floatval($kalem['tutar']); 
                            $is_sabit = false;
                            foreach($sabit_kelimeler as $sk) { 
                                if(strpos($kalem['kalem_adi'], $sk) !== false) { $is_sabit = true; break; } 
                            }
                        ?>
                        <div class="d-flex align-items-center border-bottom border-light py-1 row-kalem" data-kalem-id="<?= $kalem['id'] ?>">
                            <input type="text" class="form-control form-control-sm text-dark fw-semibold" value="<?= htmlspecialchars($kalem['kalem_adi']) ?>" disabled style="cursor: not-allowed;">
                            <div class="d-flex align-items-center ms-auto" style="width: 140px;">
                                <?php if (!$is_sabit): ?>
                                <button type="button" class="btn btn-xs btn-link text-danger p-0 me-2" onclick="tekilTaslakKalemSil(<?= $kalem['id'] ?>)" title="Bu taslağı kaldır">
                                    <i class="bi bi-trash3-fill" style="font-size: 11px;"></i>
                                </button>
                                <?php endif; ?>
                                <input type="number" class="form-control form-control-sm text-end pe-2 text-success" value="<?= number_format($gosterilecek_gelir, 2, '.', '') ?>" disabled style="cursor: not-allowed;">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </form>
                <div class="card-footer bg-light border-top border-light p-2">
                    <div class="d-flex align-items-center gap-1">
                        <input type="text" id="yeni-taslak-gelir-adi" class="form-control form-control-sm text-dark fw-semibold input-ekle" placeholder="Ağustos Projeksiyon Geliri.." style="font-size: 11px !important;">
                        <div class="d-flex align-items-center ms-auto" style="width: 185px;">
                            <input type="number" step="0.01" id="yeni-taslak-gelir-tutar" class="form-control form-control-sm text-end text-success input-ekle" placeholder="0.00" style="font-size: 11px !important;">
                            <button type="button" class="btn btn-sm btn-success fw-bold text-white ms-1" style="height: 24px; font-size: 10px; padding: 2px 8px;" onclick="hizliTaslakEkle('gelir')">
                                <i class="bi bi-plus-lg"></i> EKLE
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PROJEKSİYON GİDER VE FATURA CETVELİ KARTI --> 
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-header bg-white border-bottom border-light py-2 px-3 text-danger d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-cart-dash me-1"></i> Projeksiyon Gider ve Fatura Cetveli</span>
                    <span class="badge bg-light text-danger font-monospace" style="font-size: 9px;">Planlama</span>
                </div>
                <div class="card-body p-2" style="max-height: 410px; overflow-y: auto;">
                    <form id="gider-butce-formu" onsubmit="return false;">
                        <?php foreach ($giderler as $kalem): 
                            $isim = trim($kalem['kalem_adi']);
                            $is_isbank_kk = (strpos($isim, 'İşbank') !== false && strpos($isim, 'KK') !== false);
                            $is_vakif_kk = (strpos($isim, 'Vakıfbank') !== false && strpos($isim, 'KK') !== false);
                            
                            $gosterilecek_tutar = $kalem['tutar'];
                            if ($is_isbank_kk) $gosterilecek_tutar = $isbank_card_borcu;
                            if ($is_vakif_kk) $gosterilecek_tutar = $vakif_card_borcu;

                            $is_sabit = false;
                            foreach($sabit_kelimeler as $sk) { 
                                if(strpos($kalem['kalem_adi'], $sk) !== false) { $is_sabit = true; break; } 
                            }
                        ?>
                        <div class="d-flex align-items-center border-bottom border-light py-1 row-kalem" data-kalem-id="<?= $kalem['id'] ?>">
                            <input type="text" class="form-control form-control-sm text-dark fw-semibold" value="<?= htmlspecialchars($kalem['kalem_adi']) ?>" disabled style="cursor: not-allowed;">
                            <div class="d-flex align-items-center ms-auto" style="width: 140px;">
                                <?php if (!$is_sabit): ?>
                                <button type="button" class="btn btn-xs btn-link text-danger p-0 me-2" onclick="tekilTaslakKalemSil(<?= $kalem['id'] ?>)" title="Bu taslağı kaldır">
                                    <i class="bi bi-trash3-fill" style="font-size: 11px;"></i>
                                </button>
                                <?php endif; ?>
                                <input type="number" class="form-control form-control-sm text-end text-danger fw-bold" value="<?= number_format($gosterilecek_tutar, 2, '.', '') ?>" disabled style="background: transparent; font-weight: 700; cursor: not-allowed;">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </form>
                </div>
                <div class="card-footer bg-light border-top border-light p-2">
                    <div class="d-flex align-items-center row-kalem">
                        <input type="text" id="yeni-taslak-gider-adi" class="form-control form-control-sm text-dark fw-semibold input-ekle" placeholder="Ağustos Taslak Gider.." style="font-size: 11px !important;">
                        <div class="d-flex align-items-center ms-auto" style="width: 185px;">
                            <input type="number" step="0.01" id="yeni-taslak-gider-tutar" class="form-control form-control-sm text-end pe-2 text-danger input-ekle" placeholder="0.00">
                            <button type="button" class="btn btn-sm btn-danger fw-bold text-white ms-1" style="height: 24px; font-size: 10px; padding: 2px 8px;" onclick="hizliTaslakEkle('gider')">
                                <i class="bi bi-plus-lg"></i> EKLE
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ==================== SAĞ SÜTUN: PLANLAMA MONİTÖRÜ VE RISK ANALİZİ ==================== -->
        <div class="col-md-4 monitorstyle" id="monitor-ekrani">
            
            <!-- LİKİDİTE HAVUZLARI MATRİSİ --> 
            <div class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-header bg-white border-bottom border-light py-2 px-3 text-dark fw-bold">
                    <i class="bi bi-bank2 text-primary me-1"></i> Öngörülen Likidite Havuzları
                </div> 
                <div class="card-body p-2">
                    <div class="row g-2 text-center mb-2">
                        <div class="col-6">
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #f8f9fa; min-height: 72px; cursor: not-allowed;">
                                <span class="d-block text-uppercase fw-bold text-secondary" style="font-size: 9px !important; line-height: 1.2;">İş Bankası (Ö)<br><span class="text-muted fw-normal" style="font-size: 8px;">Kredi Bütçesi</span></span>
                                <span class="d-block text-muted" style="font-size: 8px; line-height: 1.1;">Ana Para: <span class="fw-semibold text-dark"><?= number_format($kredi_input_butcesi, 2, ',', '.') ?> ₺</span></span>
                                <span class="fw-bold d-block mt-1 font-monospace text-secondary" style="font-size: 11px !important;"><?= number_format($isbank_o_bakiye, 2, ',', '.') ?> ₺</span>
                                <span class="text-muted d-block mt-1" style="font-size: 8px;">Brüt Nema: <span class="text-success fw-bold">+<?= number_format($isbank_o_nema, 2, ',', '.') ?> ₺</span></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #e1f5fe; min-height: 72px; cursor: not-allowed;">
                                <span class="d-block text-uppercase fw-bold text-primary" style="font-size: 9px !important; line-height: 1.2;">İşbankası (A)<br><span class="text-muted fw-normal" style="font-size: 8px;">Yatırım Ana Havuz</span></span>
                                <span class="d-block text-muted" style="font-size: 8px; line-height: 1.1;">Ana Para: <span class="fw-semibold text-dark"><?= number_format($isbank_k_faizsiz_ana_para, 2, ',', '.') ?> ₺</span></span>
                                <span class="fw-bold d-block mt-1 font-monospace text-primary" style="font-size: 11px !important;"><?= number_format($isbank_a_bakiye, 2, ',', '.') ?> ₺</span>
                                <span class="text-muted d-block mt-1" style="font-size: 8px;">Brüt Nema: <span class="text-success fw-bold">+<?= number_format($isbank_a_nema, 2, ',', '.') ?> ₺</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #fff9e6; min-height: 72px;">
                                <span class="d-block text-uppercase fw-bold text-warning-emphasis" style="font-size: 9px !important; line-height: 1.2;">Vakıfbank (Ö)<br><span class="text-muted fw-normal" style="font-size: 8px;">Sabit Faturalar</span></span>
                                <span class="fw-bold d-block mt-1 font-monospace text-warning-emphasis" style="font-size: 11px !important;"><?= number_format($vakif_bloke, 2, ',', '.') ?> ₺</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded shadow-xs text-start position-relative" style="background-color: #e8f5e9; min-height: 72px; cursor: not-allowed;"> 
                                <span class="d-block text-uppercase fw-bold text-success" style="font-size: 9px !important; line-height: 1.2; padding-right: 15px;">Nakit (Ev)<br><span class="text-muted fw-normal" style="font-size: 8px;">Su + Aidat Planı</span></span>
                                <span class="fw-bold d-block mt-1 font-monospace text-success" style="font-size: 11px !important;"><?= number_format($nakit_bloke, 2, ',', '.') ?> ₺</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KREDİ KARTLARI DETAY ANALİZ MATRİSİ -->
            <div class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-header bg-white border-bottom border-light py-1.5 px-3 text-dark fw-bold" style="font-size:11px;">
                    <i class="bi bi-credit-card-2-front-fill text-secondary me-1"></i> Kart Dönem Tahmini Harcamaları
                </div>
                <div class="card-body p-2">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #ebf3fc; border: 1px solid #b8daff !important; color: #004085; min-height: 94px; border-radius: 5px !important;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-uppercase font-monospace" style="font-size: 8.5px; color: #004085; letter-spacing:0.5px;">MAXIMUM KART</span>
                                    <i class="bi bi-alt text-primary" style="font-size:10px;"></i>
                                </div>
                                <span class="d-block text-secondary" style="font-size: 7.5px; line-height: 1.1;">Kesinleşen Dönem Yükü:</span>
                                <span class="fw-bold d-block mt-0.5 font-monospace text-dark" style="font-size: 12px !important; letter-spacing:-0.2px;"><?= number_format($isbank_card_borcu, 2, ',', '.') ?> ₺</span>
                                <div class="mt-1 pt-1 border-top border-white-10" style="font-size: 7.5px; line-height: 1.25; color: #004085;">
                                    Dönem İçi: <span class="fw-bold font-monospace text-secondary"><?= number_format($kkart['isbank']['donem_ici'], 2, ',', '.') ?></span><br>
                                    Provizyon: <span class="fw-bold font-monospace text-primary"><?= number_format($kkart['isbank']['provizyon'], 2, ',', '.') ?></span><br>
                                    Taksit: <span class="fw-bold font-monospace text-secondary"><?= number_format($isbank_aktif_taksit, 2, ',', '.') ?></span><br>
                                    Döviz Borcu: <span class="fw-semibold font-monospace text-muted" style="font-size: 8px;"><?= number_format($kkart['isbank']['eu_borc_tl'] + $kkart['isbank']['usd_borc_tl'], 2, ',', '.') ?> ₺</span><br>
                                                <span class="fw-semibold font-monospace text-muted" style="font-size: 6px;">($<?= number_format($kkart['isbank']['usd_borcu'], 2) ?> / €<?= number_format($kkart['isbank']['eu_borcu'], 2) ?>)</span><br>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #fff9db; border: 1px solid #ffe3af !important; color: #856404; min-height: 94px; border-radius: 5px !important;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-uppercase font-monospace" style="font-size: 8.5px; color: #856404; letter-spacing:0.5px;">VAKIFBANK WORLD</span>
                                    <i class="bi bi-globe text-warning" style="font-size:10px;"></i>
                                </div>
                                <span class="d-block text-secondary" style="font-size: 7.5px; line-height: 1.1;">Kesinleşen Dönem Yükü:</span>
                                <span class="fw-bold d-block mt-0.5 font-monospace text-dark" style="font-size: 12px !important; letter-spacing:-0.2px;"><?= number_format($vakif_card_borcu, 2, ',', '.') ?> ₺</span>
                                <div class="mt-1 pt-1 border-top border-white-10" style="font-size: 7.5px; line-height: 1.25; color: #856404;">
                                    Dönem İçi: <span class="fw-bold font-monospace text-secondary"><?= number_format($kkart['vakifbank']['donem_ici'], 2, ',', '.') ?></span><br>
                                    Provizyon: <span class="fw-bold font-monospace text-warning-emphasis" style="color: #b58105 !important;"><?= number_format($kkart['vakifbank']['provizyon'], 2, ',', '.') ?></span><br>
                                    Taksit: <span class="fw-bold font-monospace text-secondary"><?= number_format($vakif_aktif_taksit, 2, ',', '.') ?></span><br>
                                    Döviz Borcu: <span class="fw-semibold font-monospace text-muted" style="font-size: 8px;"><?= number_format($kkart['vakifbank']['eu_borc_tl'] + $kkart['vakifbank']['usd_borc_tl'], 2, ',', '.') ?> ₺</span><br>
                                                <span class="fw-semibold font-monospace text-muted" style="font-size: 6px;">($<?= number_format($kkart['vakifbank']['usd_borcu'], 2) ?> / €<?= number_format($kkart['vakifbank']['eu_borcu'], 2) ?>)</span><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ÖNGÖRÜLEN YATIRIM HACMİ (TASARRUF) --> 
            <div class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-body p-2" style="background-color: #fdfafd; border-left: 3.5px solid #8e24aa; border-radius: 4px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 8.5px; letter-spacing: 0.5px;">ÖNGÖRÜLEN YATIRIM HACMİ</span>
                            <span class="text-muted" style="font-size: 8px;">İşbank (A) Gelecek Ay Serbest Nakit</span>
                        </div>
                        <h5 class="fw-bold mb-0 font-monospace" style="color: #6a1b9a; font-size: 13.5px;"><?= number_format($isbank_a_bakiye, 2, ',', '.') ?> ₺</h5>
                    </div>
                </div>
            </div>

               <!-- TOTAL GELİRLER VE AKILLI FAİZ LİKİDİTESİ --> 
            <div class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-body p-2" style="background-color: #f4fbf7; border-left: 3.5px solid #2e7d32; border-radius: 4px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-1 mb-1">
                        <span class="fw-bold text-success text-uppercase" style="font-size: 8.5px; letter-spacing: 0.5px;">TAHMİNİ PROJEKSİYON BAKİYESİ</span>
                        <span class="fw-bold font-monospace text-success" style="font-size: 13.5px;"><?= number_format($isbank_a_bakiye + $isbank_o_bakiye, 2, ',', '.') ?> ₺</span>
                    </div>
                    <div class="row g-0 text-center" style="font-size: 9px; line-height: 1.2;">
                        <div class="col-6 border-end border-light text-start ps-1">
                            <span class="text-muted d-block" style="font-size: 7.5px;">ANAPARALAR TOPLAMI</span>
                            <span class="fw-bold text-dark font-monospace"><?= number_format($isbank_k_faizsiz_ana_para + $isbank_o_faizsiz_ana_para, 2, ',', '.') ?> ₺</span>
                        </div>
                        <div class="col-6 text-end pe-1">
                            <span class="text-muted d-block" style="font-size: 7.5px;">ÖNGÖRÜLEN NET NEMA</span>
                            <span class="fw-bold text-warning-emphasis font-monospace">+<?= number_format($isbank_a_nema + $isbank_o_nema, 2, ',', '.') ?> ₺</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DİĞER GİDERLER -->
            <div class="card shadow-sm border-0 bg-white mb-3">
                <div class="card-body p-2" style="background-color: #fff5f5; border-left: 3.5px solid #c92a2a; border-radius: 4px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-1 mb-1">
                        <span class="fw-bold text-danger text-uppercase" style="font-size: 8.5px; letter-spacing: 0.5px;">PROJEKSİYON GİDERLER MATRİSİ</span>
                        <span class="fw-bold font-monospace text-danger" style="font-size: 13.5px;"><?= number_format($vakif_bloke + $nakit_bloke + $kredi_input_butcesi, 2, ',', '.') ?> ₺</span>
                    </div>
                    <div class="row g-1 text-center pt-1" style="font-size: 9px; line-height: 1.2;">
                        <div class="col-4 border-end border-light text-start ps-1">
                            <span class="text-muted d-block" style="font-size: 7.5px; font-weight: 500;">FATURA PLAN (VAKIF)</span>
                            <span class="fw-bold text-primary font-monospace" style="font-size: 9.5px;"><?= number_format($vakif_bloke, 2, ',', '.') ?> ₺</span>
                        </div>
                        <div class="col-4 border-end border-light px-1">
                            <span class="text-muted d-block" style="font-size: 7.5px; font-weight: 500;">SU / AİDAT PLAN</span>
                            <span class="fw-bold font-monospace text-danger-emphasis" style="font-size: 9.5px;"><?= number_format($nakit_bloke, 2, ',', '.') ?> ₺</span>
                        </div>
                        <div class="col-4 text-end pe-1">
                            <span class="text-muted d-block" style="font-size: 7.5px; font-weight: 500;">KREDİ BLOKE (Ö)</span>
                            <span class="fw-bold font-monospace text-dark" style="font-size: 9.5px;"><?= number_format($kredi_input_butcesi, 2, ',', '.') ?> ₺</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DETAYLI DÖNEM RISK ANALİZİ --> 
            <div class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-body p-2" style="background-color: #fff9f6; border-left: 3.5px solid #fd7e14; border-radius: 4px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-1 mb-1">
                        <span class="fw-bold text-uppercase" style="color: #d9480f; font-size: 8.5px; letter-spacing: 0.5px;">PROJEKSİYON KK DÖNEM YÜKÜ</span>
                        <span class="fw-bold font-monospace" style="color: #d9480f; font-size: 13.5px;"><?= number_format($isbank_card_borcu + $vakif_card_borcu, 2, ',', '.') ?> ₺</span>
                    </div>
                    <div class="row g-1 text-center" style="font-size: 8.5px; line-height: 1.15;">
                        <div class="col-3 border-end border-light text-start ps-1">
                            <span class="text-muted d-block" style="font-size: 7px;">DÖNEM İÇİ</span>
                            <span class="fw-bold text-dark font-monospace"><?= number_format($kkart['isbank']['donem_ici'] + $kkart['vakifbank']['donem_ici'], 2, ',', '.') ?> ₺</span>
                        </div>
                        <div class="col-3 border-end border-light px-0.5">
                            <span class="text-muted d-block" style="font-size: 7px;">PROVİZYON</span>
                            <span class="fw-bold text-primary font-monospace"><?= number_format($kkart['isbank']['provizyon'] + $kkart['vakifbank']['provizyon'], 2, ',', '.') ?> ₺</span>
                        </div>
                        <div class="col-3 border-end border-light px-0.5">
                            <span class="text-muted d-block" style="font-size: 7px;">GELECEK TAKSİT</span>
                            <span class="fw-bold font-monospace" style="color: #d9480f;"><?= number_format($isbank_aktif_taksit + $vakif_aktif_taksit, 2, ',', '.') ?> ₺</span>
                        </div>
                        <div class="col-3 text-end pe-1">
                            <span class="text-muted d-block" style="font-size: 7px;">DÖVİZ BORCU</span>
                            <span class="fw-bold text-danger font-monospace"><?= number_format($kkart['isbank']['doviz_borcu'] + $kkart['vakifbank']['doviz_borcu'], 2, ',', '.') ?> ₺</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TO-DO GELECEK PLAN TRANSFERLERİ -->
            <div class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-header bg-white border-bottom border-light py-1.5 px-2">
                    <span class="text-dark fw-bold text-uppercase d-block" style="font-size: 9px; letter-spacing: 0.5px;">
                        <i class="bi bi-clipboard-check-fill text-secondary me-1"></i> Öngörülen Gelecek Ay Transferleri (Rehber) 📜
                    </span>
                </div>
                <div class="card-body p-1" style="background-color: #fafbfc;">
                    <div class="todo-item p-2 border rounded bg-white" style="line-height: 1.25; opacity: 0.85;">
                        <div class="d-flex align-items-start font-monospace text-muted" style="font-size: 9px;">
                            <span class="badge bg-secondary me-1.5 px-1 py-0.5" style="font-size: 8px;">PLAN</span>
                            <div>Dönem devredildiğinde taksitler ve tüm 6 bileşenli simülasyon provizyonları ana bütçeye otomatik mühürlenecektir.</div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="js_su_bloke_miktari" value="<?= $nakit_bloke ?>">
            <input type="hidden" id="js_fatura_bloke_miktari" value="<?= $vakif_bloke ?>">
        </div>
    </div>
</div>

<script>

    window.gelecekDonemAbortController = null;
    window.gelecekDonemZamanlayici = null;

    // 1. PARA FONKSİYON VE FORMATLAR

    const TR_PARA_FORMATI = new Intl.NumberFormat('tr-TR', { 
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2 
    });

    // 🎯 SIZDIRMAZ PARA TEMİZLEME FONKSİYONU
    function paraTemizle(deger) {
        if (deger === null || deger === undefined) return 0;
        
        // Girdiyi stringe çevir ve sağındaki/solundaki boşlukları temizle
        let temiz = deger.toString().trim();
        if (!temiz) return 0;

        // Sadece rakamlar, virgül, nokta ve eksi işaretini tut
        temiz = temiz.replace(/[^0-9,.-]/g, '');

        // Eğer sayı standart Türkçe formatındaysa (Binlik ayraç nokta, kuruş virgül):
        // Örn: "1.250,50" -> Noktaları sil, virgülü noktaya çevir
        if (temiz.includes(',') && temiz.includes('.')) {
            temiz = temiz.replace(/\./g, '').replace(',', '.');
        } 
        // Eğer sadece tek bir bilyon/kuruş ayracı olarak virgül kullanıldıysa:
        // Örn: "1250,50" -> Virgülü noktaya çevir
        else if (temiz.includes(',')) {
            temiz = temiz.replace(',', '.');
        }

        const sonuc = parseFloat(temiz);
        return isNaN(sonuc) ? 0 : sonuc; // NaN çökme koruması
    }

    // 🎯 STANDART PARA FORMATLAMA FONKSİYONU
    function paraFormatla(sayi) {
        const guvenliSayi = isNaN(parseFloat(sayi)) ? 0 : parseFloat(sayi);
        return TR_PARA_FORMATI.format(guvenliSayi);
    }

    // 🎯 TÜRK LİRASI SİMGELİ FORMATLAMA FONKSİYONU
    function paraFormatlaTL(val) {
        return paraFormatla(val) + " ₺"; // Kod tekrarı önlendi, yukarıdaki motora bağlandı
    }

    function gelecekDonemKartGuncelle(kartTuru, alanTuru, deger) {
        clearTimeout(window.gelecekDonemZamanlayici);
        
        window.gelecekDonemZamanlayici = setTimeout(() => {
            // Çevrimdışı kontrol koruması (Yerel sunucuda değilse ve internet yoksa kes)
            if (navigator.onLine === false && !window.location.hostname.includes('localhost')) return;
            
            // Önceki asenkron istek hala devam ediyorsa iptal et (Ağ trafiği şişme bariyeri)
            if (window.gelecekDonemAbortController) window.gelecekDonemAbortController.abort();
            
            window.gelecekDonemAbortController = new AbortController();
            const signal = window.gelecekDonemAbortController.signal;
            
            // Veritabanı mühürleme motoruna gönderilecek paket
            const formData = new FormData();
            formData.append('projeksiyon_kart_bileseni', '1');
            formData.append('kart_turu', kartTuru);
            formData.append('alan_turu', alanTuru);
            formData.append('deger', parseFloat(deger) || 0);
            
            // Aktif girdi odağını ve imleç (seçim) konumunu hafızaya al (Re-render sonrası odak kaybolma sigortası)
            const aktifOdakId = document.activeElement ? document.activeElement.id : null;
            const secimBaslangic = document.activeElement ? document.activeElement.selectionStart : null;
            const secimBitis = document.activeElement ? document.activeElement.selectionEnd : null;
            fetch('/butce/admin/management_next', { method: 'POST', body: formData, signal: signal })
            .then(res => {
                if (!res.ok) throw new Error(`HTTP Hata: ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (data && data.status === 'success') {
                    // DOM yapısını yenile ve ardından odağı geri kazandır
                    window.monitorLastGuncelle(() => {
                        setTimeout(() => {
                            if (aktifOdakId) {
                                const odakEl = document.getElementById(aktifOdakId);
                                if (odakEl) {
                                    odakEl.focus();
                                    // Input tipleri sayı (number) olduğunda setSelectionRange hatasını engelleme bariyeri
                                    if (secimBaslangic !== null && secimBitis !== null && odakEl.tagName === 'INPUT' && odakEl.type !== 'number') {
                                        odakEl.setSelectionRange(secimBaslangic, secimBitis);
                                    }
                                }
                            }
                        }, 20); // 20ms mikrosaniye gecikmeyle odağı mühürle
                    });
                }
            })
            .catch(err => {
                if (err.name === 'AbortError') return; // İptal edilen istekleri konsola basma
                if (err.message.includes('Failed to fetch') || err.name === 'TypeError') {
                    console.warn(`[AĞ KOPUKLUĞU] Yerel sunucuya şu an erişilemiyor. Lütfen servislerinizi kontrol edin.`);
                } else {
                    console.error("Gelecek Dönem Motor Mantık Hatası:", err);
                }
            });
        }, 1500); 
    }
    
    // B. MOBİL LİMİT GİRİŞLERİ (Anlık Provizyon Tetikleyicileri)
    const inputIsbank = document.getElementById('mobil_isbank_kull_limit');
    if (inputIsbank) {
        inputIsbank.addEventListener('input', function() {
            if (typeof hariciProvizyonHesaplaNihai === 'function') {
                hariciProvizyonHesaplaNihai('isbank');
            }
        });
    }
    const inputVakif = document.getElementById('mobil_vakif_kull_limit');
    if (inputVakif) {
        inputVakif.addEventListener('input', function() {
            if (typeof hariciProvizyonHesaplaNihai === 'function') {
                // Orijinal kodunuzdaki isimlendirmeye ('vakit' veya 'vakif') göre kontrol edin
                hariciProvizyonHesaplaNihai('vakif'); 
            }
        });
    }

    /** * PROJEKSİYON CANLI RE-RENDER MOTORU (FRIENDLY URL UYUMLU)  */

    window.monitorLastGuncelle = function(callbackGeriCagir) {
        const guvenliUrl = window.location.pathname + '?t=' + new Date().getTime();
        fetch(guvenliUrl)
        .then(res => {
            if (!res.ok) throw new Error(`HTTP Hatası! Durum: ${res.status}`);
            return res.text();
        })
        .then(html => {
            const parser = new DOMParser(); 
            const doc = parser.parseFromString(html, 'text/html');
            // Yeni döviz girdilerini barındıran form ve ekran seçicileri
            const seciciler = ['#monitor-ekrani', '#gelir-butce-formu', '#gider-butce-formu', '#cari-girdiler-formu', '#taksit-formu'];
            

            // window.monitorLastGuncelle fonksiyonunun en sonundaki .then(html => { ... }) bloğunun içi:
            seciciler.forEach(secici => {
                const yeniEl = doc.querySelector(secici); 
                const mevcutEl = document.querySelector(secici);
                if (yeniEl && mevcutEl) {
                    mevcutEl.innerHTML = yeniEl.innerHTML;
                }
            });

            // RE-RENDER SONRASI BOOTSTRAP BALONLARINI YENİDEN TETİKLE
            bootstrapTooltipYenile();

            if (typeof callbackGeriCagir === 'function') {
                callbackGeriCagir();
            }
        })
        .catch(err => console.warn("Canlı re-render başarısız:", err.message));
    }

    function hariciParaOkuVeTemizle(elementId) {
        const el = document.getElementById(elementId);
        if (!el) return 0;
        
        // Değer input ise .value, düz metin/span/td ise innerText okur
        let hamMetin = el.value || el.innerText || "0";
        if (!hamMetin) return 0;

        let temiz = hamMetin.toString().replace(/[^0-9,.-]/g, '');
        temiz = temiz.replace(/\./g, '');
        temiz = temiz.replace(',', '.');
        return parseFloat(temiz) || 0;
    }

    function hariciProvizyonHesaplaNihai(kartKey) {
        let kartLimiti = 0;
        let girilenMobilLimit = 0;
        let kesinlesenDonemBorcu = 0;
        let kalanTaksitlerToplam = 0;
        let gostergeId = '';


        if (kartKey === 'isbank') {
            kartLimiti = 321100; // Güncel İşbank KK Limiti
            
            // Kullanıcının provizyon istasyonuna yazdığı sayı ($a)
            girilenMobilLimit = parseFloat(document.getElementById('mobil_isbank_kull_limit')?.value) || 0;

            
            // Cari formdaki ve matristeki ID'lerden canlı verileri cımbızlıyoruz
            kesinlesenDonemBorcu = hariciParaOkuVeTemizle('isbank_kesinlesen_donem');
            dovizTL     = parseFloat(document.getElementById('isbank-doviz-input')?.value) || 0;
            
            // 🎯 TALEBİNİZ: Gelecek aylara sarkan toplam taksit yükü ID'si hesaba dahil edildi
            kalanTaksitlerToplam = hariciParaOkuVeTemizle('isbank_kalan_taksitler');
            
            gostergeId = 'isbank_hesaplanan_provizyon';
        } 
        else if (kartKey === 'vakif') {
            kartLimiti = 263500; // Güncel Vakıfbank KK Limiti
            
            // Kullanıcının provizyon istasyonuna yazdığı sayı ($v)
            girilenMobilLimit = parseFloat(document.getElementById('mobil_vakif_kull_limit')?.value) || 0;
            
            // Cari formdaki ve matristeki ID'lerden canlı verileri cımbızlıyoruz
            kesinlesenDonemBorcu = hariciParaOkuVeTemizle('vakif_kesinlesen_donem');
            dovizTL     = parseFloat(document.getElementById('vakif-doviz-input')?.value) || 0;
            
            // 🎯 TALEBİNİZ: Gelecek aylara sarkan toplam taksit yükü ID'si hesaba dahil edildi
            kalanTaksitlerToplam = hariciParaOkuVeTemizle('vakif_kalan_taksitler');
            
            gostergeId = 'vakif_hesaplanan_provizyon';
        }

        // 🎯 GÜNCEL BANKACILIK FORMÜLÜ MATRİSİ
        let netProvizyon = kartLimiti - girilenMobilLimit - kesinlesenDonemBorcu - kalanTaksitlerToplam;

        // Sıfırın altına düşme koruması ve input boşsa sıfırlama nizamı
        if (netProvizyon < 0 || girilenMobilLimit <= 0) { 
            netProvizyon = 0; 
        }

        // Sonucu kuruşu kuruşuna biçimlendirip yansıtma
        const gosterge = document.getElementById(gostergeId);
        if (gosterge) {
            if (typeof paraFormatla === 'function') {
                gosterge.value = paraFormatla(netProvizyon);
            } else {
                gosterge.value = new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(netProvizyon) + ' ₺';
            }
        }
    }

    /** Yeni taslak planlama kalemi ekleme işlevi */
    function hizliTaslakEkle(turu) {
        const adiInput = document.getElementById(turu === 'gelir' ? 'yeni-taslak-gelir-adi' : 'yeni-taslak-gider-adi');
        const tutarInput = document.getElementById(turu === 'gelir' ? 'yeni-taslak-gelir-tutar' : 'yeni-taslak-gider-tutar');
        if (!adiInput || !tutarInput) return;
        
        const adi = adiInput.value.trim();
        const tutar = parseFloat(tutarInput.value) || 0;
        if (adi === '' || tutar <= 0) return;
        
        const formData = new FormData();
        formData.append('islem', 'yeni_taslak_kalem_ekle');
        formData.append('kalem_adi', adi);
        formData.append('tutar', tutar);
        formData.append('turu', turu);
        
        fetch(window.location.pathname, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data && data.status === 'success') {
                adiInput.value = ''; 
                tutarInput.value = '';
                window.monitorLastGuncelle();
            }
        });
    }

    /**  * Belirli bir taslak planlama kalemini kaldırma işlevi */
    function tekilTaslakKalemSil(id) {
        if (!id || id <= 0) return;
        if (!confirm('Bu taslak planlama kalemini kaldırmak istediğinize emin misiniz?')) return;
        
        const formData = new FormData();
        formData.append('islem', 'taslak_kalem_sil');
        formData.append('id', id);
        
        fetch(window.location.pathname, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data && data.status === 'success') {
                window.monitorLastGuncelle();
            }
        });
    }

    /** * Bootstrap Tooltip Balonlarını Başlatma ve Re-Render Senkronizasyon Motoru  */
    function bootstrapTooltipYenile() {
        // Eski açık kalmış veya bozulmuş tooltip örneklerini temizle
        const mevcutTooltipler = document.querySelectorAll('.tooltip');
        mevcutTooltipler.forEach(el => el.remove());

        // Sayfadaki tüm döviz tooltip'lerini yeniden keşfet ve Bootstrap ile bağla
        const tooltipTetikleyiciler = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTetikleyiciler.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: 'hover' // Sadece fare üzerine gelince tetiklensin, tıklayınca kalmasın
            });
        });
    }

    // Sayfa ilk defa tarayıcıya yüklendiğinde çalıştır
    document.addEventListener("DOMContentLoaded", bootstrapTooltipYenile);

</script>

<?php require admin_view("static/footer"); ?>