<!-- C:\wamp64\www\hfa\admin\view\management_last.php -->
<?php require admin_view("static/header"); ?>

<style>
    .butcestyle { font-size: 11px !important; }
    .butcestyle .card-header { font-size: 12px !important; font-weight: bold; }
    .butcestyle input.form-control-sm { 
        font-size: 11px !important; 
        font-weight: 600; 
        background: transparent; 
        border: none; 
        padding: 2px 4px;
        box-shadow: none !important;
    }
    .butcestyle .row-odendi input { text-decoration: line-through; color: #6c757d !important; opacity: 0.6; }
    .butcestyle .btn-mikro { height: 22px; line-height: 1; font-size: 10px; width: 45px; padding: 2px; font-weight: bold; }
    .butcestyle .btn-su { height: 22px; line-height: 1; font-size: 10px; padding: 2px 8px; font-weight: bold; }
    .butcestyle .input-ekle { border: 1px solid #dee2e6 !important; background: #fff !important; border-radius: 4px; padding: 4px 8px !important; }
    
    .solsutunstyle { font-size: 11px !important; }
    .solsutunstyle .card-header { font-size: 11px !important; font-weight: bold; }
    .solsutunstyle table { font-size: 11px !important; }
    .solsutunstyle table thead tr th { font-size: 11px !important; }
    .solsutunstyle input.form-control-sm { font-size: 11px !important; font-weight: 600; box-shadow: none !important; }
    .solsutunstyle .card-footer { font-size: 11px !important; }

    .monitorstyle { font-size: 11px !important; }
    .monitorstyle .card-header { font-size: 11px !important; font-weight: bold; }
    .monitorstyle .card-body { font-size: 11px !important; }
    .monitorstyle .text-uppercase { font-size: 11px !important; font-weight: bold; }
    .monitorstyle .font-monospace { font-size: 11px !important; }
    .monitorstyle #faiz-orani-input { font-size: 11px !important; }

    .excel-dashboard-wrapper { font-size: 10px !important; margin-top: 15px; }
    .excel-table { border: 1.5px solid #a0a0a0 !important; font-size: 10px !important; }
    .excel-table th, .excel-table td { 
        padding: 3px 4px !important; 
        border: 1px solid #ced4da !important; 
        vertical-align: middle !important; 
        line-height: 1.2 !important;
    }
    .excel-table thead { sticky-top: 0; background: #ffffff; z-index: 5; }
    .tepe-kutu { font-weight: 700; background-color: #f1f3f5; font-size: 10px; font-monospace: true; }
    
    .excel-head-title { background-color: #212529 !important; color: #ffffff !important; font-weight: 700; font-size: 8px; }
    .excel-group-gelir { background-color: #d1e7dd !important; color: #0f5132 !important; font-weight: 700; }
    .excel-group-gider { background-color: #f8d7da !important; color: #842029 !important; font-weight: 700; }
    .excel-group-nakit { background-color: #cff4fc !important; color: #087990 !important; font-weight: 700; }
    .excel-group-tasarruf { background-color: #e2d9f3 !important; color: #432874 !important; font-weight: 700; }
    .excel-aktif-satir { background-color: #fff3cd !important; font-weight: 600; border-top: 1.5px solid #ffc107 !important; border-bottom: 1.5px solid #ffc107 !important; }
</style>

<div class="container-fluid py-3 solsutunstyle">
    <!-- Üst Kontrol Barı -->
    <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-2 rounded shadow-sm border border-light">
        <h5 class="m-0 fw-bold text-dark d-flex align-items-center" style="font-size: 14px;">
            <i class="bi bi-eye-fill text-warning me-2"></i> 
            Gelecek Dönem Planlama Monitörü <span class="badge bg-warning text-dark ms-2 font-monospace" style="font-size: 11px;"><?= htmlspecialchars($projeksiyon_donemi) ?></span>
        </h5>
        <div>
            <span class="badge bg-light text-danger border border-danger-subtle px-3 py-2 fw-bold" style="font-size: 11px;">
                <i class="bi bi-shield-lock-fill me-1"></i> SALT OKUNUR PROJEKSİYON (ASIL DÖNEM KORUMALI)
            </span>
        </div>
    </div>

    <!-- 1. SATIR - Sütunlu Bölüm Düzeni -->
    <div class="row g-3 mb-3">
        <!-- SOL SÜTUN: KART BİLEŞENLERİ VE LİMİT İZLEME PANELİ -->
        <div class="col-md-3">
                    
            <!-- 💳 KART 1: 12 AYLIK TAKSİT MATRİSİ AYNALAMASI -->
            <form id="taksit-formu" onsubmit="return false;">
                <div class="card shadow-sm border-0 bg-white mb-2">
                    <div class="card-header bg-white border-bottom border-light py-2 px-3 d-flex justify-content-between align-items-center">
                        <span class="text-dark fw-bold">
                            <i class="bi bi-calendar-range text-secondary me-1"></i> Projeksiyon Taksitleri
                        </span>
                        <span class="badge bg-light text-secondary font-monospace" style="font-size: 9px;">Kilitli</span>
                    </div>
                    <div class="card-body p-2">
                        <div style="max-height: 340px">
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
                                                <?= sprintf("%02d", $t_satir['ay_index']) ?>.<?=$next_year_int;?>
                                                <?= $is_hedef_ay ? ' 🎯' : '' ?>
                                            </td>
                                            <td class="p-0 px-1">
                                                <input type="number" step="0.01" class="form-control form-control-sm border-0 bg-transparent p-0 text-end text-dark fw-bold isbank-taksit-input" value="<?= number_format($t_satir['isbank_tutar'], 2, '.', '') ?>" disabled style="cursor: not-allowed;">
                                            </td>
                                            <td class="p-0 px-1">
                                                <input type="number" step="0.01" class="form-control form-control-sm border-0 bg-transparent p-0 text-end text-dark fw-bold vakif-taksit-input" value="<?= number_format($t_satir['vakif_tutar'], 2, '.', '') ?>" disabled style="cursor: not-allowed;">
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
            <!-- 💳 FORM 2: CARİ AY GİRDİLERİ VE LİMİT HESAPLAMA MOTORU FORMU -->
            <form id="cari-girdiler-formu" onsubmit="return false;">
                
                <!-- ÜST KART: PROJEKSİYON KART GİRDİLERİ (ASIL DÖNEME ETKİSİZ) -->
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
                                    <td class="p-0 ps-1 text-muted fw-semibold" style="font-size:10px;">Gelecek Dönem Taksit</td>
                                    <td class="p-0 px-1"><input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-dark font-monospace" readonly value="<?= number_format($isbank_aktif_taksit, 2, ',', '.') ?> ₺" style="font-size:10px !important;"></td>
                                    <td class="p-0 px-1"><input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-dark font-monospace" readonly value="<?= number_format($vakif_aktif_taksit, 2, ',', '.') ?> ₺" style="font-size:10px !important;"></td>
                                </tr>
                                <tr class="border-bottom border-light" style="height: 24px;">
                                    <td class="p-0 ps-1 text-muted" style="font-size:10px;">Dönem İçi Harc.</td>
                                    <td class="p-0 px-1">
                                        <input type="number" step="0.01" id="isbank-donem-ici-input" class="form-control form-control-sm text-end p-0 pe-1 fw-bold text-danger font-monospace border rounded bg-white" value="<?= number_format($cc_meta_last['isbank_donem_ici'] + $isbank_ekstra_donem_ici, 2, '.', '') ?>" style="height: 19px; font-size:10px !important;" oninput="bayeCanliBilesenGuncelle('isbank', 'donem_ici', this.value)">
                                    </td>
                                    <td class="p-0 px-1">
                                        <input type="number" step="0.01" id="vakif-donem-ici-input" class="form-control form-control-sm text-end p-0 pe-1 fw-bold text-danger font-monospace border rounded bg-white" value="<?= number_format($cc_meta_last['vakif_donem_ici'] + $vakif_ekstra_donem_ici, 2, '.', '') ?>" style="height: 19px; font-size:10px !important;" oninput="bayeCanliBilesenGuncelle('vakif', 'donem_ici', this.value)">
                                    </td>
                                </tr>
                                <tr class="border-bottom border-light" style="height: 24px;">
                                    <td class="p-0 ps-1 text-muted" style="font-size:10px;">Provizyonda Bek.</td>
                                    <td class="p-0 px-1">
                                        <input type="number" step="0.01" id="isbank-provizyon-input" class="form-control form-control-sm text-end p-0 pe-1 fw-bold text-dark font-monospace border rounded bg-white" value="<?= number_format($cc_meta_last['isbank_provizyon'] + $isbank_ekstra_provizyon, 2, '.', '') ?>" style="height: 19px; font-size:10px !important;" oninput="bayeCanliBilesenGuncelle('isbank', 'provizyon', this.value)">
                                    </td>
                                    <td class="p-0 px-1">
                                        <input type="number" step="0.01" id="vakif-provizyon-input" class="form-control form-control-sm text-end p-0 pe-1 fw-bold text-warning-emphasis font-monospace border rounded bg-white" value="<?= number_format($cc_meta_last['vakif_provizyon'] + $vakif_ekstra_provizyon, 2, '.', '') ?>" style="height: 19px; font-size:10px !important;" oninput="bayeCanliBilesenGuncelle('vakif', 'provizyon', this.value)">
                                    </td>
                                </tr>
                                <tr class="border-bottom border-light" style="height: 24px;">
                                    <td class="p-0 ps-1 text-muted" style="font-size:10px;">Döviz Borcu</td>
                                    <td class="p-0 px-1">
                                        <input type="number" step="0.01" id="isbank-doviz-input" class="form-control form-control-sm text-end p-0 pe-1 fw-medium text-dark font-monospace border rounded bg-white" value="<?= number_format($cc_meta_last['isbank_doviz'] + $isbank_ekstra_doviz, 2, '.', '') ?>" style="height: 19px; font-size:10px !important;" oninput="bayeCanliBilesenGuncelle('isbank', 'doviz', this.value)">
                                    </td>
                                    <td class="p-0 px-1">
                                        <input type="number" step="0.01" id="vakif-doviz-input" class="form-control form-control-sm text-end p-0 pe-1 fw-medium text-dark font-monospace border rounded bg-white" value="<?= number_format($cc_meta_last['vakif_doviz'] + $vakif_ekstra_doviz, 2, '.', '') ?>" style="height: 19px; font-size:10px !important;" oninput="bayeCanliBilesenGuncelle('vakif', 'doviz', this.value)">
                                    </td>
                                </tr>
                                 <!-- 🎯 1. ARANAN SATIR: VERİ GİRİŞİ YAPILABİLİR HARCAMA LİMİTİ HÜCRELERİ -->
                                <tr class="border-bottom border-light" style="height: 24px; background-color: #fffaf0;">
                                    <td class="p-0 ps-1 text-dark fw-bold" style="font-size:10px;">Harcama Limiti</td>
                                    <td class="p-0 px-1">
                                        <input type="number" step="0.01" id="isbank-harcama-limiti-input" class="form-control form-control-sm text-end p-0 pe-1 fw-bold text-dark font-monospace border rounded bg-white" value="<?= number_format($isbank_ekstra_harcama_limiti, 2, '.', '') ?>" style="height: 19px; font-size:10px !important;" oninput="bayeCanliBilesenGuncelle('isbank', 'harcama_limiti', this.value)">
                                    </td>
                                    <td class="p-0 px-1">
                                        <input type="number" step="0.01" id="vakif-harcama-limiti-input" class="form-control form-control-sm text-end p-0 pe-1 fw-bold text-dark font-monospace border rounded bg-white" value="<?= number_format($vakif_ekstra_harcama_limiti, 2, '.', '') ?>" style="height: 19px; font-size:10px !important;" oninput="bayeCanliBilesenGuncelle('vakif', 'harcama_limiti', this.value)">
                                    </td>
                                </tr>

                                <!-- 🎯 2. ARANAN SATIR: HARCANABİLİR BAKİYE HÜCRELERİ (LİMİT - DÖNEM İÇİ HARCAMA) (READONLY) -->
                                <tr class="border-bottom border-light" style="height: 24px; background-color: #f4fbf7;">
                                    <td class="p-0 ps-1 text-success fw-bold" style="font-size:10px;">Harcanabilir Bakiye</td>
                                    <?php 
                                        $isbank_harcanabilir = $isbank_ekstra_harcama_limiti - ($cc_meta_last['isbank_donem_ici'] + $isbank_ekstra_donem_ici);
                                        $vakif_harcanabilir  = $vakif_ekstra_harcama_limiti - ($cc_meta_last['vakif_donem_ici'] + $vakif_ekstra_donem_ici);
                                    ?>
                                    <td class="p-0 px-1">
                                        <input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 pe-1 text-end fw-bold text-success font-monospace" readonly value="<?= number_format($isbank_harcanabilir, 2, ',', '.') ?> ₺" style="font-size:10px !important; cursor: default;">
                                    </td>
                                    <td class="p-0 px-1">
                                        <input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 pe-1 text-end fw-bold text-success font-monospace" readonly value="<?= number_format($vakif_harcanabilir, 2, ',', '.') ?> ₺" style="font-size:10px !important; cursor: default;">
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

                <!-- ALT KART: LİMİT VE TOPLAM BORÇ ARŞİV TABLOSU -->
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
                                    <!-- 🎯 DÜZELTME: Toplam yük, kalan taksitleri de içererek yansır -->
                                    <td class="p-0 px-1"><input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-danger font-monospace" readonly value="<?= number_format($isbank_nihai_toplam_borc, 2, ',', '.') ?> ₺"></td>
                                    <td class="p-0 px-1"><input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-danger font-monospace" readonly value="<?= number_format($vakif_nihai_toplam_borc, 2, ',', '.') ?> ₺"></td>
                                </tr>
                                <tr style="font-size:10px !important; height: 24px; background-color: #e3f2fd;">
                                    <td class="p-0 ps-1 text-primary-emphasis fw-bold">Kalan Limit</td>
                                    <!-- 🎯 DÜZELTME: Kalan limit, kalan taksitler düşüldükten sonra hesaplanır -->
                                    <td class="p-0 px-1"><input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-primary font-monospace" readonly value="<?= number_format(max(0, 321100.00 - $isbank_nihai_toplam_borc), 2, ',', '.') ?> ₺"></td>
                                    <td class="p-0 px-1"><input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-primary font-monospace" readonly value="<?= number_format(max(0, 263500.00 - $vakif_nihai_toplam_borc), 2, ',', '.') ?> ₺"></td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </form>

        </div> <!-- 🎯 SOL SÜTUN COL-MD-3 HÜCRESİNİN KESİN SIZINTISIZ KAPANIŞI -->
        
         <!-- ORTA SÜTUN: GELİR VE GİDER KARTLARI (TASLAK PLANLAMA) -->
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
                        ?>
                        <div class="d-flex align-items-center border-bottom border-light py-1 row-kalem" data-kalem-id="<?= $kalem['id'] ?>">
                            <input type="text" class="form-control form-control-sm text-dark fw-semibold" value="<?= htmlspecialchars($kalem['kalem_adi']) ?>" disabled style="cursor: not-allowed;">
                            
                            <div class="d-flex align-items-center ms-auto" style="width: 185px;">
                                <?php 
                                $sabit_kelimeler = ['Maaş', 'Mesai', 'Maktu', 'KK', 'Kredi', 'Faturası', 'Damacana', 'İçme Suyu', 'Aidat', 'Nergiz Cep'];
                                $is_sabit = false;
                                foreach($sabit_kelimeler as $sk) { if(strpos($kalem['kalem_adi'], $sk) !== false) { $is_sabit = true; break; } }
                                if (!$is_sabit): ?>
                                    <button type="button" class="btn btn-xs btn-link text-danger p-0 me-2" onclick="tekilTaslakKalemSil(<?= $kalem['id'] ?>)" title="Bu taslağı kaldır">
                                        <i class="bi bi-trash3-fill" style="font-size: 11px;"></i>
                                    </button>
                                <?php endif; ?>

                                <input type="number" step="0.01" class="form-control form-control-sm text-end pe-2 text-success" value="<?= number_format($gosterilecek_gelir, 2, '.', '') ?>" disabled style="cursor: not-allowed;">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-mikro" disabled><i class="bi bi-dash"></i></button>
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
                            <button type="button" class="btn btn-sm btn-success fw-bold text-white" style="height: 24px; line-height: 1; font-size: 10px; padding: 2px 8px;" onclick="hizliTaslakEkle('gelir')">
                                <i class="bi bi-plus-lg"></i>EKLE
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🔴 PROJEKSİYON GİDER VE FATURA CETVELİ KARTI -->
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-header bg-white border-bottom border-light py-2 px-3 text-danger d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-cart-dash me-1"></i> Projeksiyon Gider ve Fatura Cetveli</span>
                    <span class="badge bg-light text-danger font-monospace" style="font-size: 9px;">Planlama</span>
                </div>

                <div class="card-body p-2" style="max-height: 410px; overflow-y: auto;">
                    <form id="gider-butce-formu" onsubmit="return false;">
                        <?php foreach ($giderler as $kalem): 
                            $isim = trim($kalem['kalem_adi']);
                            $is_isbank_kk  = (strpos($isim, 'İşbank') !== false && strpos($isim, 'KK') !== false);
                            $is_vakif_kk   = (strpos($isim, 'Vakıfbank') !== false && strpos($isim, 'KK') !== false);
                            
                            $gosterilecek_tutar = $kalem['tutar'];
                            if ($is_isbank_kk)  $gosterilecek_tutar = $isbank_card_borcu;
                            if ($is_vakif_kk)   $gosterilecek_tutar = $vakif_card_borcu;
                        ?>
                        <div class="d-flex align-items-center border-bottom border-light py-1 row-kalem" data-kalem-id="<?= $kalem['id'] ?>">
                            <input type="text" class="form-control form-control-sm text-dark fw-semibold" value="<?= htmlspecialchars($kalem['kalem_adi']) ?>" disabled style="cursor: not-allowed;">
                            
                            <div class="d-flex align-items-center ms-auto" style="width: 210px;">
                                <?php 
                                $is_sabit = false;
                                foreach($sabit_kelimeler as $sk) { if(strpos($kalem['kalem_adi'], $sk) !== false) { $is_sabit = true; break; } }
                                if (!$is_sabit): ?>
                                    <button type="button" class="btn btn-xs btn-link text-danger p-0 me-2" onclick="tekilTaslakKalemSil(<?= $kalem['id'] ?>)" title="Bu taslağı kaldır">
                                        <i class="bi bi-trash3-fill" style="font-size: 11px;"></i>
                                    </button>
                                <?php endif; ?>

                                <input type="number" step="0.01" class="form-control form-control-sm text-end pe-2 text-danger fw-bold" value="<?= number_format($gosterilecek_tutar, 2, '.', '') ?>" disabled style="background: transparent; font-weight: 700; cursor: not-allowed;">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-mikro" disabled>PLAN</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </form>
                </div>

                <div class="card-footer bg-light border-top border-light p-2">
                    <div class="d-flex align-items-center row-kalem">
                        <input type="text" id="yeni-taslak-gider-adi" class="form-control form-control-sm text-dark fw-semibold input-ekle" placeholder="Ağustos Taslak Gider.." style="font-size: 11px !important;">
                        <div class="d-flex align-items-center ms-auto" style="width: 210px;">
                            <input type="number" step="0.01" id="yeni-taslak-gider-tutar" class="form-control form-control-sm text-end pe-2 text-danger input-ekle" placeholder="0.00">
                            <button type="button" class="btn btn-sm btn-danger btn-mikro px-2" onclick="hizliTaslakEkle('gider')"><i class="bi bi-plus-lg"></i>EKLE</button>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- ORTA SÜTUN KESİN KAPANIŞI -->
        <!-- SAĞ SÜTUN: PLANLAMA GÖSTERGELERİ VE PROJEKSİYON MONİTÖR EKRANI -->
        <div class="col-md-4 monitorstyle" id="monitor-ekrani">
            
            <!-- 💳 ÜST KART: AKILLI FAİZ HESAPLAMA PANELİ (SALT OKUNUR) -->
            <div style="display: none;" class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-header bg-white border-bottom border-light py-2 px-3 text-dark fw-bold">
                    <i class="bi bi-percent text-warning me-1"></i> Projeksiyon Faiz Hesaplama
                </div>
                <div class="card-body p-2">
                    <div class="row g-2 align-items-center">
                        <div class="col-2">
                            <span class="" style="font-size:10px;">Oran %</span>
                        </div>
                        <div class="col-4">
                            <input type="number" id="faiz-orani-input" class="form-control text-center p-1 fw-bold text-dark font-monospace" value="<?= $faiz_orani ?>" disabled style="cursor:not-allowed;">
                        </div>
                        <div class="col-2">
                            <span class="" style="font-size:10px;">Gün</span>
                        </div>
                        <div class="col-4">
                            <input type="number" id="faiz-gun-input" class="form-control text-center p-1 fw-bold text-dark font-monospace" value="<?= $faiz_gun_sayisi ?>" disabled style="cursor:not-allowed;">
                        </div>
                    </div>

                    <!-- Harici Faiz Başlangıç Bütçesi Girişi -->
                    <div class="row g-2 align-items-center mt-2 border-top border-light pt-2">
                        <div class="col-4">
                            <span class="text-secondary fw-semibold" style="font-size:10px;"><i class="bi bi-bank me-1 text-primary"></i>Kredi Faiz Bütçesi</span>
                        </div>
                        <div class="col-8">
                            <input type="number" step="0.01" id="faiz-kredi-butcesi-input" class="form-control text-end p-1 fw-bold text-danger font-monospace" value="<?= $kredi_input_butcesi ?>" disabled style="font-size:11px !important; cursor:not-allowed;">
                        </div>
                    </div>

                    <!-- KİLİT: Projeksiyonda faiz aktarım butonu kilitli bilgilendirme moduna geçirildi -->
                    <div class="d-grid gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary fw-bold shadow-xs" style="font-size:11px;" disabled>
                            <i class="bi bi-shield-lock-fill me-1 text-warning"></i> FAİZLER DÖNEM DEVRENDE AKTİF OLUR
                        </button>
                    </div>
                </div>
            </div>

            <!-- 💳 ORTA KART: FİNANSAL LİKİDİTE HAVUZLARI MATRİSİ -->
            <div class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-header bg-white border-bottom border-light py-2 px-3 text-dark fw-bold">
                    <i class="bi bi-bank2 text-primary me-1"></i> Öngörülen Likidite Havuzları
                </div>   
                <div class="card-body p-2">
                    <div class="row g-2 text-center mb-2">
                        <div class="col-6">
                            <!-- 🎯 İSBANK Ö HAVUZU: Doğrudan kredi_input_butcesi (53.200,00 ₺) olarak kilitlenmiştir -->
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #f8f9fa; min-height: 72px; cursor: not-allowed;">
                                <span class="d-block text-uppercase fw-bold text-secondary" style="font-size: 9px !important; line-height: 1.2;">İş Bankası (Ö)<br><span class="text-muted fw-normal" style="font-size: 8px;">Kredi Bütçesi</span></span>
                                <span class="d-block text-muted" style="font-size: 8px; line-height: 1.1;">Ana Para: <span class="fw-semibold text-dark"><?= number_format($kredi_input_butcesi, 2, ',', '.') ?> ₺</span></span>
                                <span class="fw-bold d-block mt-1 font-monospace text-secondary" style="font-size: 11px !important;"><?= number_format($isbank_o_bakiye, 2, ',', '.') ?> ₺</span>
                                <span class="text-muted d-block mt-1" style="font-size: 8px;">Brüt Nema: <span class="text-success fw-bold">+<?= number_format($isbank_o_nema, 2, ',', '.') ?> ₺</span></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <!-- 🎯 İSBANK A HAVUZU: Tam talep ettiğiniz üzere GELİRLER - GİDERLER kuralına bağlandı -->
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #e1f5fe; min-height: 72px; cursor: not-allowed;">
                                <span class="d-block text-uppercase fw-bold text-primary" style="font-size: 9px !important; line-height: 1.2;">İşbankası (A)<br><span class="text-muted fw-normal" style="font-size: 8px;">Yatırım Ana Havuz</span></span>
                                <span class="d-block text-muted" style="font-size: 8px; line-height: 1.1;">Ana Para: <span class="fw-semibold text-dark"><?= number_format($isbank_k_faizsiz_ana_para, 2, ',', '.') ?> ₺</span></span>
                                <span class="fw-bold d-block mt-1 font-monospace text-primary" style="font-size: 11px !important;"><?= number_format($isbank_a_bakiye, 2, ',', '.') ?> ₺</span>
                                <span class="text-muted d-block mt-1" style="font-size: 8px;">Brüt Nema: <span class="text-success fw-bold">+<?= number_format($isbank_a_nema, 2, ',', '.') ?> ₺</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. SATIR: VAKIFBANK (Ö) & NAKIT (EV) -->
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <!-- 🎯 VAKIFBANK HAVUZU: Sadece ve sadece Sabit Faturalar Toplamını gösterir -->
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #fff9e6; min-height: 72px;">
                                <span class="d-block text-uppercase fw-bold text-warning-emphasis" style="font-size: 9px !important; line-height: 1.2;">Vakıfbank (Ö)<br><span class="text-muted fw-normal" style="font-size: 8px;">Sabit Faturalar</span></span>
                                <span class="fw-bold d-block mt-1 font-monospace text-warning-emphasis" style="font-size: 11px !important;"><?= number_format($vakif_bloke, 2, ',', '.') ?> ₺</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <!-- 🎯 NAKİT HAVUZU: Sadece Damacana Su + Aidat toplamını gösterir -->
                            <div class="p-2 border rounded shadow-xs text-start position-relative" style="background-color: #e8f5e9; min-height: 72px; cursor: not-allowed;">                               
                                <span class="d-block text-uppercase fw-bold text-success" style="font-size: 9px !important; line-height: 1.2; padding-right: 15px;">Nakit (Ev)<br><span class="text-muted fw-normal" style="font-size: 8px;">Su + Aidat Planı</span></span>
                                <span class="fw-bold d-block mt-1 font-monospace text-success" style="font-size: 11px !important;"><?= number_format($nakit_bloke, 2, ',', '.') ?> ₺</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 💳 YENİ KART: GELECEK DÖNEM KREDİ KARTLARI DETAY ANALİZ MATRİSİ (PASTEL KURUMSAL RENK TONLU) -->
            <div class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-header bg-white border-bottom border-light py-1.5 px-3 text-dark fw-bold" style="font-size:11px;">
                    <i class="bi bi-credit-card-2-front-fill text-secondary me-1"></i> Kart Dönem Tahmini Harcamaları
                </div>
                <div class="card-body p-2">
                    <div class="row g-2 text-center">
                        
                        <!-- A. İSBANK MAXIMUM KART KUTUSU (PASTEL MAVİ) -->
                        <div class="col-6">
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #ebf3fc; border: 1px solid #b8daff !important; color: #004085; min-height: 94px; border-radius: 5px !important;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-uppercase font-monospace" style="font-size: 8.5px; color: #004085; letter-spacing:0.5px;">MAXIMUM KART</span>
                                    <i class="bi bi-alt text-primary" style="font-size:10px;"></i>
                                </div>
                                <span class="d-block text-secondary" style="font-size: 7.5px; line-height: 1.1;">Kesinleşen Dönem Yükü:</span>
                                <span class="fw-bold d-block mt-0.5 font-monospace text-dark" style="font-size: 12px !important; letter-spacing:-0.2px;">
                                    <?= number_format($isbank_card_borcu, 2, ',', '.') ?> ₺
                                </span>
                                <div class="mt-1 pt-1 border-top border-white-10" style="font-size: 7.5px; line-height: 1.25; color: #004085;">
                                    Dönem İçi: <span class="fw-bold font-monospace text-secondary"><?= number_format($cc_meta_last['isbank_donem_ici'] + $isbank_ekstra_donem_ici, 2, ',', '.') ?></span><br>
                                    Provizyon: <span class="fw-bold font-monospace text-primary"><?= number_format($cc_meta_last['isbank_provizyon'] + $isbank_ekstra_provizyon, 2, ',', '.') ?></span><br>
                                    Taksit: <span class="fw-bold font-monospace text-secondary"><?= number_format($isbank_aktif_taksit, 2, ',', '.') ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- B. VAKIFBANK WORLD KART KUTUSU (PASTEL SARI) -->
                        <div class="col-6">
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #fff9db; border: 1px solid #ffe3af !important; color: #856404; min-height: 94px; border-radius: 5px !important;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-uppercase font-monospace" style="font-size: 8.5px; color: #856404; letter-spacing:0.5px;">VAKIFBANK WORLD</span>
                                    <i class="bi bi-globe text-warning" style="font-size:10px;"></i>
                                </div>
                                <span class="d-block text-secondary" style="font-size: 7.5px; line-height: 1.1;">Kesinleşen Dönem Yükü:</span>
                                <span class="fw-bold d-block mt-0.5 font-monospace text-dark" style="font-size: 12px !important; letter-spacing:-0.2px;">
                                    <?= number_format($vakif_card_borcu, 2, ',', '.') ?> ₺
                                </span>
                                <div class="mt-1 pt-1 border-top border-white-10" style="font-size: 7.5px; line-height: 1.25; color: #856404;">
                                    Dönem İçi: <span class="fw-bold font-monospace text-secondary"><?= number_format($cc_meta_last['vakif_donem_ici'] + $vakif_ekstra_donem_ici, 2, ',', '.') ?></span><br>
                                    Provizyon: <span class="fw-bold font-monospace text-warning-emphasis" style="color: #b58105 !important;"><?= number_format($cc_meta_last['vakif_provizyon'] + $vakif_ekstra_provizyon, 2, ',', '.') ?></span><br>
                                    Taksit: <span class="fw-bold font-monospace text-secondary"><?= number_format($vakif_aktif_taksit, 2, ',', '.') ?></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- 💜 CARD 1: ÖNGÖRÜLEN YATIRIM HACMİ (TASARRUF) -->
            <div class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-body p-2" style="background-color: #fdfafd; border-left: 3.5px solid #8e24aa; border-radius: 4px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 8.5px; letter-spacing: 0.5px;">💰 ÖNGÖRÜLEN YATIRIM HACMİ</span>
                            <span class="text-muted" style="font-size: 8px;">İşbank (A) Gelecek Ay Serbest Nakit</span>
                        </div>
                        <h5 class="fw-bold mb-0 font-monospace" style="color: #6a1b9a; font-size: 13.5px;"><?= number_format($isbank_a_bakiye, 2, ',', '.') ?> ₺</h5>
                    </div>
                </div>
            </div>
            <!-- 💚 CARD 2: TOTAL GELİRLER VE AKILLI FAİZ LİKİDİTESİ -->
            <div class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-body p-2" style="background-color: #f4fbf7; border-left: 3.5px solid #2e7d32; border-radius: 4px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-1 mb-1">
                        <span class="fw-bold text-success text-uppercase" style="font-size: 8.5px; letter-spacing: 0.5px;">🚀 TAHMİNİ PROJEKSİYON BAKİYESİ</span>
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

            <!-- ❤️ CARD 4: DİĞER GİDERLER (FATURA, SU VE KREDİ BLOKE TOPLAMI) -->
            <div class="card shadow-sm border-0 bg-white mb-3">
                <div class="card-body p-2" style="background-color: #fff5f5; border-left: 3.5px solid #c92a2a; border-radius: 4px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-1 mb-1">
                        <span class="fw-bold text-danger text-uppercase" style="font-size: 8.5px; letter-spacing: 0.5px;">📊 PROJEKSİYON GİDERLER MATRİSİ</span>
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
            <!-- 🧡 CARD 3: 4 ALT HÜCRELİ DETAYLI DÖNEM RISK ANALİZİ -->
            <div class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-body p-2" style="background-color: #fff9f6; border-left: 3.5px solid #fd7e14; border-radius: 4px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-1 mb-1">
                        <span class="fw-bold text-uppercase" style="color: #d9480f; font-size: 8.5px; letter-spacing: 0.5px;">💳 PROJEKSİYON KK DÖNEM YÜKÜ</span>
                        <span class="fw-bold font-monospace" style="color: #d9480f; font-size: 13.5px;"><?= number_format($isbank_card_borcu + $vakif_card_borcu, 2, ',', '.') ?> ₺</span>
                    </div>
                    <div class="row g-1 text-center" style="font-size: 8.5px; line-height: 1.15;">
                        <div class="col-3 border-end border-light text-start ps-1">
                            <span class="text-muted d-block" style="font-size: 7px;">DÖNEM İÇİ</span>
                            <span class="fw-bold text-dark font-monospace"><?= number_format($cc_meta_last['isbank_donem_ici'] + $isbank_ekstra_donem_ici + $cc_meta_last['vakif_donem_ici'] + $vakif_ekstra_donem_ici, 2, ',', '.') ?> ₺</span>
                        </div>
                        <div class="col-3 border-end border-light px-0.5">
                            <span class="text-muted d-block" style="font-size: 7px;">PROVİZYON</span>
                            <span class="fw-bold text-primary font-monospace"><?= number_format($cc_meta_last['isbank_provizyon'] + $isbank_ekstra_provizyon + $cc_meta_last['vakif_provizyon'] + $vakif_ekstra_provizyon, 2, ',', '.') ?> ₺</span>
                        </div>
                        <div class="col-3 border-end border-light px-0.5">
                            <span class="text-muted d-block" style="font-size: 7px;">GELECEK TAKSİT</span>
                            <span class="fw-bold font-monospace" style="color: #d9480f;"><?= number_format($isbank_aktif_taksit + $vakif_aktif_taksit, 2, ',', '.') ?> ₺</span>
                        </div>
                        <div class="col-3 text-end pe-1">
                            <span class="text-muted d-block" style="font-size: 7px;">DÖVİZ BORCU</span>
                            <span class="fw-bold text-danger font-monospace"><?= number_format($cc_meta_last['isbank_doviz'] + $isbank_ekstra_doviz + $cc_meta_last['vakif_doviz'] + $vakif_ekstra_doviz, 2, ',', '.') ?> ₺</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🎯 CARD 5: TO-DO GELECEK PLAN TRANSFERLERİ -->
            <div class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-header bg-white border-bottom border-light py-1.5 px-2">
                    <span class="text-dark fw-bold text-uppercase d-block" style="font-size: 9px; letter-spacing: 0.5px;">
                        <i class="bi bi-clipboard-check-fill text-secondary me-1"></i> 📜 Öngörülen Gelecek Ay Transferleri (Rehber)
                    </span>
                </div>
                <div class="card-body p-1" style="background-color: #fafbfc;">
                    <div class="d-flex flex-column gap-1" style="opacity:0.85;">
                        <div class="todo-item p-2 border rounded bg-white" style="line-height: 1.25;">
                            <div class="d-flex align-items-start font-monospace text-muted" style="font-size: 9px;">
                                <span class="badge bg-secondary me-1.5 px-1 py-0.5" style="font-size: 8px;">PLAN</span>
                                <div>Dönem devredildiğinde taksitler ve tüm 6 bileşenli simülasyon provizyonları ana bütçeye otomatik mühürlenecektir.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="js_su_bloke_miktari" value="<?= $nakit_bloke ?>">
            <input type="hidden" id="js_fatura_bloke_miktari" value="<?= $vakif_bloke ?>">
        </div>

    </div> <!-- 1. SATIR Row Kapanışı -->
</div> <!-- Container Kapanışı -->


<script>
window.bayeProjeksiyonAbortController = null;
window.bayeBilesenZamanlayici = null;

/**
 * 🎯 MASTER SİMÜLASYON ADAPTÖRÜ (FRIENDLY URL GARANTİLİ)
 * Klasör hiyerarşisi ne olursa olsun, istekleri doğrudan tarayıcı çubuğundaki
 * aktif temiz url adresine fırlatır. POST edilmeme ve yönlendirme sızıntılarını %100 çözer.
 */
function bayeCanliBilesenGuncelle(kartTuru, alanTuru, deger) {
    // oninput düzeninde sunucuya nefes aldırmak için 450ms debounce koruması
    clearTimeout(window.bayeBilesenZamanlayici);

    window.bayeBilesenZamanlayici = setTimeout(() => {
        
        // Sunucudan yanıt bekleyen yarım kalmış bir önceki post isteği varsa tarayıcıda öldür
        if (window.bayeProjeksiyonAbortController) {
            window.bayeProjeksiyonAbortController.abort();
        }
        
        window.bayeProjeksiyonAbortController = new AbortController();
        const signal = window.bayeProjeksiyonAbortController.signal;
        const temizDeger = parseFloat(deger) || 0;

        const formData = new FormData();
        formData.append('projeksiyon_kart_bileseni', '1');
        formData.append('kart_turu', kartTuru);
        formData.append('alan_turu', alanTuru);
        formData.append('deger', temizDeger);

        const aktifOdakId = document.activeElement ? document.activeElement.id : null;
        const secimBaslangic = document.activeElement ? document.activeElement.selectionStart : null;
        const secimBitis = document.activeElement ? document.activeElement.selectionEnd : null;

        // 🎯 KESİN REFORM: Klasör aramadan, doğrudan bulunulan aktif Temiz URL adresini hedef alıyoruz!
        const hedefCanliUrl = window.location.pathname; 

        // 🚀 1. ADIM: Sadece veri yazma (POST) isteğini gönder
        fetch(hedefCanliUrl, {
            method: 'POST',
            body: formData,
            signal: signal
        })
        .then(res => {
            if (!res.ok) throw new Error("Bütçe motoru yazma başarısız.");
            return res.json();
        })
        .then(data => {
            if (data && data.status === 'success') {
                // 🚀 2. ADIM: Yazma işlemi KESİN bitti. Şimdi güvenle sayfa okuma (GET) motorunu tetikle!
                window.monitorLastGuncelle(() => {
                    // Sayfa renderı tamamen bittikten sonra odağı ve imleci eski basamağına iade et
                    setTimeout(() => {
                        if (aktifOdakId) {
                            const odaklanilacakEl = document.getElementById(aktifOdakId);
                            if (odaklanilacakEl) {
                                odaklanilacakEl.focus();
                                if (secimBaslangic !== null && secimBitis !== null && (odaklanilacakEl.tagName === 'INPUT')) {
                                    odaklanilacakEl.setSelectionRange(secimBaslangic, secimBitis);
                                }
                            }
                        }
                    }, 10);
                });
            }
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                console.error("⚠️ Projeksiyon Post İletişim Hatası:", err);
            }
        });
    }, 450); 
}

/**
 * 🎯 PROJEKSİYON CANLI RE-RENDER MOTORU (FRIENDLY URL UYUMLU)
 */
window.monitorLastGuncelle = function(callbackGeriCagir) {
    // 🎯 KESİN REFORM: Yenileme motorunu da tarayıcı url'sinin sonuna zaman damgası ekleyerek çağırıyoruz
    const guvenliUrl = window.location.pathname + '?t=' + new Date().getTime();

    fetch(guvenliUrl)
        .then(res => {
            if (!res.ok) throw new Error(`HTTP Hatası! Durum: ${res.status}`);
            return res.text();
        })
        .then(html => {
            const parser = new DOMParser(); 
            const doc = parser.parseFromString(html, 'text/html');
            
            const seciciler = ['#monitor-ekrani', '#gelir-butce-formu', '#gider-butce-formu', '#cari-girdiler-formu', '#taksit-formu'];

            seciciler.forEach(secici => {
                const yeniEl = doc.querySelector(secici); 
                const mevcutEl = document.querySelector(secici);
                if (yeniEl && mevcutEl) {
                    mevcutEl.innerHTML = '';
                    mevcutEl.innerHTML = yeniEl.innerHTML;
                }
            });

            if (typeof callbackGeriCagir === 'function') {
                callbackGeriCagir();
            }
        })
        .catch(err => console.warn("⚠️ Canlı re-render başarısız:", err.message));
}

function hizliTaslakEkle(turu) {
    const adiInput    = document.getElementById(turu === 'gelir' ? 'yeni-taslak-gelir-adi' : 'yeni-taslak-gider-adi');
    const tutarInput  = document.getElementById(turu === 'gelir' ? 'yeni-taslak-gelir-tutar' : 'yeni-taslak-gider-tutar');
    if (!adiInput || !tutarInput) return;

    const adi   = adiInput.value.trim();
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
            adiInput.value = ''; tutarInput.value = '';
            window.monitorLastGuncelle();
        }
    });
}

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
</script>
<?php require admin_view("static/footer"); ?>
