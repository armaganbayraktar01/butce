<!-- C:\wamp64\www\hfa\admin\view\management.php -->
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

        /* 📊 EXCEL ARŞİV DASHBOARD TABLO STİLLERİ */
    .excel-dashboard-wrapper { font-size: 10px !important; margin-top: 15px; }
    .excel-table { border: 1.5px solid #a0a0a0 !important; font-size: 10px !important; }
    .excel-table th, .excel-table td { 
        padding: 3px 4px !important; 
        border: 1px solid #ced4da !important; 
        vertical-align: middle !important; 
        line-height: 1.2 !important;
    }
    .excel-table thead { sticky-top: 0; background: #ffffff; z-index: 5; }
    
    /* Üst Dikey Tepe Toplam Kutucukları */
    .tepe-kutu { font-weight: 700; background-color: #f1f3f5; font-size: 10px; font-monospace: true; }
    
    /* Excel Renk Grupları (Görseldeki Pastel Renkler) */
    .excel-head-title { background-color: #212529 !important; color: #ffffff !important; font-weight: 700; font-size: 8px; }
    .excel-group-gelir { background-color: #d1e7dd !important; color: #0f5132 !important; font-weight: 700; }
    .excel-group-gider { background-color: #f8d7da !important; color: #842029 !important; font-weight: 700; }
    .excel-group-nakit { background-color: #cff4fc !important; color: #087990 !important; font-weight: 700; }
    .excel-group-tasarruf { background-color: #e2d9f3 !important; color: #432874 !important; font-weight: 700; }
    .excel-group-yatirim { background-color: #f8d7da !important; color: #b769aa !important; font-weight: 700; }
    /* Aktif Dönem Satırı Vurgusu */
    .excel-aktif-satir { background-color: #fff3cd !important; font-weight: 600; border-top: 1.5px solid #ffc107 !important; border-bottom: 1.5px solid #ffc107 !important; }

</style>



<div class="container-fluid py-3 solsutunstyle">
    <!-- Üst Kontrol Barı -->
    <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-2 rounded shadow-sm border border-light">
        <h5 class="m-0 fw-bold text-dark d-flex align-items-center" style="font-size: 14px;">
            <i class="bi bi-calendar-check text-primary me-2"></i> 
            Dönem Finansal Takip <span class="badge bg-primary ms-2 font-monospace" style="font-size: 11px;"><?= htmlspecialchars($aktif_donem) ?></span>
        </h5>
        <div>
            <button type="button" id="donem-devret-btn" class="btn btn-sm btn-danger me-2 fw-bold" data-bs-toggle="modal" data-bs-target="#donemDevirOnayModal" style="font-size: 11px;">
                <i class="bi bi-calendar-plus me-1"></i> Dönemi Kapat & Devret
            </button>

            <!-- Geçici Test Butonu: Dönemi Geri Al (Kilit Aç) -->
            <button type="button" id="donem-geri-al-btn" class="btn btn-sm btn-outline-warning me-2 fw-bold" style="font-size: 11px;">
                <i class="bi bi-unlock-fill me-1"></i> [Test] Dönemi Geri Al (Kilit Aç)
            </button>

            <button type="button" id="simulasyon-sifirla-btn" class="btn btn-sm btn-outline-danger me-2 fw-bold" style="font-size: 11px;">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Adımları Sıfırla
            </button>
        </div>
    </div>

    <!-- 🎯 DÖNEM DEVİR GÜVENLİK VE ONAY MODAL PENCERESİ -->
    <div class="modal fade" id="donemDevirOnayModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" style="font-size: 12px;">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-dark text-white py-2 px-3">
                    <h6 class="modal-title fw-bold text-white m-0">
                        <i class="bi bi-exclamation-octagon-fill text-warning me-2"></i>Kritik Dönem Devir Onayı
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light p-3 text-dark">
                    <p class="mb-2 fw-bold text-danger">⚠️ DÖNEM KAPATMA İŞLEMİ GERÇEKKLEŞMEK ÜZERE!</p>
                    <p class="mb-3" style="line-height:1.4;">
                        Mevcut döneme ait (<span class="fw-bold text-primary"><?= htmlspecialchars($aktif_donem) ?></span>) tüm kümülatif harcama, fatura ve maaş datalarınız dondurularak Excel matrisine kalıcı olarak kilitlenecektir.<br><br>
                        • Bir sonraki aya ait **Kredi Kartı Taksit Matrisi satırı otomatik olarak cari ay bütçenize bağlanacaktır.**<br>
                        • Geçici harcamalar silinecek, kemik bütçe kadronuz sıfır ödemeyle yeni döneme klonlanacaktır.
                    </p>
                    <div class="alert alert-warning py-1 px-2 border-0 fw-semibold mb-0" style="font-size:11px;">
                        Bu işlem geri alınamaz. Devam etmek istediğinize emin misiniz?
                    </div>
                </div>
                <div class="modal-footer py-1 bg-white border-top border-light d-flex justify-content-between">
                    <button type="button" class="btn btn-xs btn-secondary fw-bold" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="button" id="kesin-devir-onay-btn" class="btn btn-sm btn-danger fw-bold shadow-xs">
                        <i class="bi bi-check-circle-fill me-1"></i> DÖNEMİ KAPAT VE DEVRET
                    </button>
                </div>
            </div>
        </div>
    </div>


<!-- 2 -->

    <!-- 1. SATIR - Sütunlu Bölüm Düzeni -->
    <div class="row g-3 mb-3">
        <!-- SOL SÜTUN: FORM KORUMALI ÜÇ MİKRO KARTLI SOL BORÇ PANELİ -->
        <div class="col-md-3">
                    
            <!-- 💳 CARD 1: 12 AYLIK TAKSİT MATRİSİ -->
            <form id="taksit-formu" onsubmit="return false;">
                <div class="card shadow-sm border-0 bg-white mb-2">
                    <div class="card-header bg-white border-bottom border-light py-2 px-3 d-flex justify-content-between align-items-center">
                        <span class="text-dark fw-bold">
                            <i class="bi bi-calendar-range text-secondary me-1"></i> Taksit Dağılımı
                        </span>
                        <button type="button" id="taksit-kaydet-btn" class="btn btn-xs btn-success fw-bold px-2 py-0" style="font-size: 10px;">
                            <i class="bi bi-floppy"></i> TaksitKaydet
                        </button>
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
                                    <?php foreach ($db_taksitler as $t_satir): ?>
                                        <tr class="border-bottom border-light" style="height: 22px;">
                                            <td class="p-0 text-secondary fw-semibold"><?= sprintf("%02d", $t_satir['ay_index']) ?>.<?=$aktif_yil;?></td>
                                            <td class="p-0 px-1">
                                                <input type="number" step="0.01" name="isbank_taksit[]" class="form-control form-control-sm border-0 bg-transparent p-0 text-end text-dark fw-bold isbank-taksit-input" value="<?= number_format($t_satir['isbank_tutar'], 2, '.', '') ?>" oninput="hesaplaCiftKrediKarti()">
                                            </td>
                                            <td class="p-0 px-1">
                                                <input type="number" step="0.01" name="vakif_taksit[]" class="form-control form-control-sm border-0 bg-transparent p-0 text-end text-dark fw-bold vakif-taksit-input" value="<?= number_format($t_satir['vakif_tutar'], 2, '.', '') ?>" oninput="hesaplaCiftKrediKarti()">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer py-1 px-2 border-0 d-flex align-items-center justify-content-between" style="background-color: #f1f3f5;">
                        <span class="text-secondary fw-bold">Kalan Taksit Top.</span>
                        <div class="d-flex gap-1" style="width: 68%;">
                            <input type="text" id="isbank_kalan_taksitler" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-dark font-monospace" readonly>
                            <input type="text" id="vakif_kalan_taksitler" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-dark font-monospace" readonly>
                        </div>
                    </div>
                </div>
            </form>
<!-- 3 -->

            <!-- 💳 FORM 2: CARİ AY GİRDİLERİ VE LİMİT HESAPLAMA MOTORU FORMU -->
            <form id="cari-girdiler-formu" onsubmit="return false;">
                
                <!-- ÜST KART: CARİ AY MANUEL HESAP KESİM BİLGİLERİ -->
                <div class="card shadow-sm border-0 bg-white mb-2">
                    <div class="card-header bg-white border-bottom border-light py-1 px-3 d-flex justify-content-between align-items-center">
                        <span class="text-dark fw-bold">
                            <i class="bi bi-calculator text-secondary me-1"></i> Cari Dönem Girdileri
                        </span>
                        <button type="button" id="cari-girdiler-kaydet-btn" class="btn btn-xs btn-success fw-bold px-2 py-0" style="font-size: 10px;">
                            <i class="bi bi-floppy"></i> Kaydet
                        </button>
                    </div>
                    <div class="card-body p-2">
                        <table class="table table-sm table-borderless align-middle mb-0" style="table-layout: fixed;">
                            <tbody>
                                <tr class="border-bottom border-light" style="height: 22px; background-color: #fcfcfc;">
                                    <td class="p-0 ps-1 text-muted fw-semibold">Döneme Ait Taksit</td>
                                    <td class="p-0 px-1"><input type="text" id="isbank_aktif_taksit_gosterge" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-dark font-monospace" readonly></td>
                                    <td class="p-0 px-1"><input type="text" id="vakif_aktif_taksit_gosterge" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-dark font-monospace" readonly></td>
                                </tr>
                                <tr class="border-bottom border-light" style="height: 22px;">
                                    <td class="p-0 ps-1 text-muted">Dönem İçi Harc.</td>
                                    <td class="p-0 px-1"><input type="number" step="0.01" name="isbank_donem_ici" id="isbank_donem_ici" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-danger font-monospace" value="<?= $cc_meta['isbank_donem_ici'] ?>" oninput="hesaplaCiftKrediKarti()"></td>
                                    <td class="p-0 px-1"><input type="number" step="0.01" name="vakif_donem_ici" id="vakif_donem_ici" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-danger font-monospace" value="<?= $cc_meta['vakif_donem_ici'] ?>" oninput="hesaplaCiftKrediKarti()"></td>
                                </tr>
                                <tr class="border-bottom border-light" style="height: 22px;">
                                    <td class="p-0 ps-1 text-muted">Provizyonda Bek.</td>
                                    <td class="p-0 px-1"><input type="number" step="0.01" name="isbank_provizyon" id="isbank_provizyon" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-medium text-dark font-monospace" value="<?= $cc_meta['isbank_provizyon'] ?>" oninput="hesaplaCiftKrediKarti()"></td>
                                    <td class="p-0 px-1"><input type="number" step="0.01" name="vakif_provizyon" id="vakif_provizyon" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-medium text-dark font-monospace" value="<?= $cc_meta['vakif_provizyon'] ?>" oninput="hesaplaCiftKrediKarti()"></td>
                                </tr>
                                <tr class="border-bottom border-light" style="height: 22px;">
                                    <td class="p-0 ps-1 text-muted">Döviz Borcu</td>
                                    <td class="p-0 px-1"><input type="number" step="0.01" name="isbank_doviz" id="isbank_doviz" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-medium text-dark font-monospace" value="<?= $cc_meta['isbank_doviz'] ?>" oninput="hesaplaCiftKrediKarti()"></td>
                                    <td class="p-0 px-1"><input type="number" step="0.01" name="vakif_doviz" id="vakif_doviz" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-medium text-dark font-monospace" value="<?= $cc_meta['vakif_doviz'] ?>" oninput="hesaplaCiftKrediKarti()"></td>
                                </tr>
                                <tr class="border-bottom border-light" style="height: 22px;">
                                    <td class="p-0 ps-1 text-muted">Kesinleşen Ekstre</td>
                                    <td class="p-0 px-1"><input type="number" step="0.01" name="isbank_ekstre" id="isbank_ekstre" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-danger font-monospace" value="<?= $cc_meta['isbank_ekstre'] ?>" oninput="hesaplaCiftKrediKarti()"></td>
                                    <td class="p-0 px-1"><input type="number" step="0.01" name="vakif_ekstre" id="vakif_ekstre" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-danger font-monospace" value="<?= $cc_meta['vakif_ekstre'] ?>" oninput="hesaplaCiftKrediKarti()"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer py-2 px-2 border-0 d-flex align-items-center justify-content-between" style="background-color: #e8f5e9;">
                        <span class="text-success-emphasis fw-bold">Kesinleşen Dön. Borcu</span>
                        <div class="d-flex gap-1" style="width: 68%;">
                            <input type="text" id="isbank_kesinlesen_donem" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-success font-monospace" readonly>
                            <input type="text" id="vakif_kesinlesen_donem" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-success font-monospace" readonly>
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
                                    <td class="p-0 px-1">
                                        <input type="hidden" id="isbank_sabit_limit" name="isbank_limit" value="321100.00">
                                        <input type="text" id="isbank_limit_gosterge" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-secondary font-monospace" readonly value="321.100,00 ₺">
                                    </td>
                                    <td class="p-0 px-1">
                                        <input type="hidden" id="vakif_sabit_limit"name="vakif_limit" value="263500.00">
                                        <input type="text" id="vakif_limit_gosterge" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-secondary font-monospace" readonly value="263.500,00 ₺">
                                    </td>
                                </tr>
                                <tr class="border-bottom border-light" style="height: 24px; background-color: #fdf2f2;">
                                    <td class="p-0 ps-1 text-danger-emphasis fw-bold">Toplam Borç</td>
                                    <td class="p-0 px-1"><input type="text" id="isbank_toplam_borc" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-danger font-monospace" readonly></td>
                                    <td class="p-0 px-1"><input type="text" id="vakif_toplam_borc" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-danger font-monospace" readonly></td>
                                </tr>
                                <tr style="height: 24px; background-color: #e3f2fd;">
                                    <td class="p-0 ps-1 text-primary-emphasis fw-bold">Kalan Limit</td>
                                    <td class="p-0 px-1"><input type="text" id="isbank_kalan_limit" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-primary font-monospace" readonly></td>
                                    <td class="p-0 px-1"><input type="text" id="vakif_kalan_limit" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-primary font-monospace" readonly></td>
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



        </div> <!-- SOL SÜTUN COL-MD-3 KAPANIŞI -->
<!-- 5 -->
         <!-- ORTA SÜTUN: GELİR VE GİDER KARTLARI -->
        <div class="col-md-5 butcestyle">
                
            <!-- GELİR AKIŞ PLANOR REFORMA ENTEGRASYONU -->               
            <div class="card shadow-sm border-0 bg-white mb-3">
                <div class="card-header bg-white border-bottom border-light py-2 px-3 text-success d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-wallet2 me-1"></i> Gelir Akış Planı</span>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" id="gelir-kaydet-btn" class="btn btn-xs btn-success fw-bold px-2 py-0" style="font-size: 10px;">
                            <i class="bi bi-floppy"></i> Gelirleri Kaydet
                        </button>
                    </div>
                </div>
                <form id="gelir-butce-formu" onsubmit="return false;">
                    <div class="card-body p-2">

                        <?php foreach ($gelirler as $kalem): 
                            // 🎯 DÜZELTME: Hem ödenme durumunu hem de veritabanı kilit durumunu kontrol ediyoruz
                            $is_odendi = (floatval($kalem['odenen_tutar']) > 0 || (floatval($kalem['odenen_tutar']) == floatval($kalem['tutar']) && floatval($kalem['tutar']) > 0)); 
                            $is_kilitli = (intval($kalem['is_locked'] ?? 0) === 1);
                            $input_kapat = ($is_odendi || $is_kilitli);
                            
                            $gosterilecek_gelir = floatval($kalem['tutar']); 
                            $k_isim = trim($kalem['kalem_adi']);
                            
                            if ($k_isim === 'Evdeki Nakit Girişi' || strpos(mb_strtolower($k_isim, 'UTF-8'), 'havuz transfer fonu') !== false) {
                                continue;
                            }
                        ?>
                        <div class="d-flex align-items-center border-bottom border-light py-1 row-kalem <?= $is_odendi ? 'row-odendi' : '' ?>" data-kalem-id="<?= $kalem['id'] ?>">
                            <input type="text" name="kalemler[<?= $kalem['id'] ?>][adi]" class="form-control form-control-sm text-dark fw-semibold" value="<?= htmlspecialchars($kalem['kalem_adi']) ?>" <?= $input_kapat ? 'disabled' : '' ?>>
                            
                            <div class="d-flex align-items-center ms-auto" style="width: 185px;">
                                <?php 
                                $sabit_kelimeler = ['Maaş', 'Mesai', 'Maktu', 'KK', 'Kredi', 'Faturası', 'Damacana', 'İçme Suyu', 'Aidat', 'Nergiz Cep'];
                                $is_sabit = false;
                                foreach($sabit_kelimeler as $sk) { if(strpos($kalem['kalem_adi'], $sk) !== false) { $is_sabit = true; break; } }
                                if (!$is_sabit && !$input_kapat): ?>
                                    <button type="button" class="btn btn-xs btn-link text-danger p-0 me-2" onclick="tekilKalemSil(<?= $kalem['id'] ?>)" title="Bu kalemi kaldır">
                                        <i class="bi bi-trash3-fill" style="font-size: 11px;"></i>
                                    </button>
                                <?php endif; ?>

                                <input type="number" step="0.01" name="kalemler[<?= $kalem['id'] ?>][tutar]" class="form-control form-control-sm text-end pe-2 text-success" value="<?= number_format($gosterilecek_gelir, 2, '.', '') ?>" <?= $input_kapat ? 'disabled' : '' ?>>
                                
                                <?php if (preg_match('/^[nyv](\s+|-|_|$)/i', trim($kalem['kalem_adi']))): ?>
                                    <?php if ($input_kapat): ?>
                                        <button type="button" class="btn btn-sm btn-outline-success btn-mikro prefix-gelir-eft-btn" disabled>✓</button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-success btn-mikro prefix-gelir-eft-btn" data-id="<?= $kalem['id'] ?>" onclick="hizliPrefixGelirEft(this)">EFT</button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm <?= $input_kapat ? 'btn-outline-success' : 'btn-light' ?> btn-mikro eft-btn" data-id="<?= $kalem['id'] ?>" data-type="maas_eft" <?= $input_kapat ? 'disabled' : '' ?>>
                                        <?= $input_kapat ? '✓' : 'EFT' ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>


                        <!-- 🎯 NAKİT YATIR: ÇAPRAZ POP-UP ENTEGRASYONLU GELİR GİRİŞ MOTORU -->
                        <div class="d-flex align-items-center border-bottom border-light py-1 row-kalem">                               
                            <input type="text" class="form-control form-control-sm text-dark fw-semibold" value="Önceki Aydan Devreden Nakit..." readonly style="background: transparent; cursor: default;">
                            <div class="d-flex align-items-center ms-auto" style="width: 185px;">
                                <input type="number" step="0.01" id="evdeki-nakit-tutar" class="form-control form-control-sm text-end pe-2 text-success border rounded bg-white" value="0.00" style="height: 22px;">
                                <button type="button" class="btn btn-sm btn-success btn-mikro shadow-xs" style="height: 24px; line-height: 1; font-size: 10px; padding: 2px 8px;" onclick="nakitYatirPopUpAc()">EFT</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="card-footer bg-light border-top border-light p-2">
                    <div class="d-flex align-items-center gap-1">
                        <input type="text" id="yeni-gelir-adi" class="form-control form-control-sm text-dark fw-semibold input-ekle" placeholder="Örn: n Ek Gelir.. [n, y, v]" style="font-size: 11px !important;">
                        <div class="d-flex align-items-center ms-auto" style="width: 185px;">
                            <input type="number" step="0.01" id="yeni-gelir-tutar" class="form-control form-control-sm text-end text-success input-ekle" placeholder="0.00" style="font-size: 11px !important;">
                            <button type="button" class="btn btn-sm btn-success fw-bold text-white" style="height: 24px; line-height: 1; font-size: 10px; padding: 2px 8px;" onclick="hizliKalemEkle('gelir')">
                                <i class="bi bi-plus-lg"></i>EKLE
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🔴 GIDER VE FATURA CETVELI KARTI -->
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-header bg-white border-bottom border-light py-2 px-3 text-danger d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-cart-dash me-1"></i> Gider ve Fatura Cetveli</span>
                    <button type="button" id="gider-kaydet-btn" class="btn btn-xs btn-danger fw-bold px-2 py-0" style="font-size: 10px;">
                        <i class="bi bi-floppy"></i> Giderleri Kaydet
                    </button>
                </div>

                <div class="card-body p-2" style="max-height: 410px; overflow-y: auto;">
                    
                    <form id="gider-butce-formu" onsubmit="return false;">

                        <?php foreach ($giderler as $kalem): 
                            $is_odendi = (floatval($kalem['odenen_tutar']) > 0); 
                            $is_kilitli = (intval($kalem['is_locked'] ?? 0) === 1);
                            $isim = trim($kalem['kalem_adi']);
                            $is_akilli_su = (strpos($isim, 'Damacana') !== false || strpos($isim, 'İçme Suyu') !== false || strpos($isim, 'damacana') !== false);
                            
                            if ($is_akilli_su || $isim === 'Nergiz Cep') continue; 

                            $is_isbank_kk  = (strpos($isim, 'İşbank') !== false && strpos($isim, 'KK') !== false);
                            $is_vakif_kk   = (strpos($isim, 'Vakıfbank') !== false && strpos($isim, 'KK') !== false);
                            $is_kilitli_kart = ($is_isbank_kk || $is_vakif_kk);
                            
                            $input_kapat = ($is_odendi || $is_kilitli || $is_kilitli_kart);

                            if ($is_odendi) {
                                $gosterilecek_tutar = 0.00;
                            } else {
                                $gosterilecek_tutar = $kalem['tutar'];
                                if ($is_isbank_kk)  $gosterilecek_tutar = $isbank_card_borcu;
                                if ($is_vakif_kk)   $gosterilecek_tutar = $vakif_card_borcu;
                            }
                        ?>
                        <div class="d-flex align-items-center border-bottom border-light py-1 row-kalem <?= $is_odendi ? 'row-odendi' : '' ?>" data-kalem-id="<?= $kalem['id'] ?>">
                            <input type="text" name="kalemler[<?= $kalem['id'] ?>][adi]" class="form-control form-control-sm text-dark fw-semibold" value="<?= htmlspecialchars($kalem['kalem_adi']) ?>" <?= $is_kilitli_kart ? 'readonly style="background: transparent; cursor: default;"' : ($is_kilitli ? 'disabled' : '') ?>>
                            
                            <div class="d-flex align-items-center ms-auto" style="width: 210px;">
                                <?php 
                                $is_sabit = false;
                                foreach($sabit_kelimeler as $sk) { if(strpos($kalem['kalem_adi'], $sk) !== false) { $is_sabit = true; break; } }
                                if (!$is_sabit && !$is_odendi && !$is_kilitli): ?>
                                    <button type="button" class="btn btn-xs btn-link text-danger p-0 me-2" onclick="tekilKalemSil(<?= $kalem['id'] ?>)" title="Bu kalemi kaldır">
                                        <i class="bi bi-trash3-fill" style="font-size: 11px;"></i>
                                    </button>
                                <?php endif; ?>

                                <input type="number" step="0.01" name="kalemler[<?= $kalem['id'] ?>][tutar]" class="form-control form-control-sm text-end pe-2 text-danger fw-bold" value="<?= number_format($gosterilecek_tutar, 2, '.', '') ?>" <?= $input_kapat ? 'readonly style="background: transparent; font-weight: 700;"' : '' ?>>
                                <button type="button" class="btn btn-sm <?= $is_odendi ? 'btn-outline-secondary' : 'btn-light' ?> btn-mikro ode-btn" data-id="<?= $kalem['id'] ?>" data-type="ode" <?= ($is_odendi || $is_kilitli) ? 'disabled' : '' ?>><?= $is_odendi ? '✓' : 'ÖDE' ?></button>
                            </div>
                        </div>
                        <?php endforeach; ?>

                    </form>

                    <!-- 💧 1. BAĞIMSIZ HÜCRE: AKILLI DAMACANA SU ALANI -->
                    <?php 
                        $damacana_satir = null;
                        foreach ($giderler as $g_kalem) {
                            if (strpos($g_kalem['kalem_adi'], 'Damacana') !== false || strpos($g_kalem['kalem_adi'], 'İçme Suyu') !== false || strpos($g_kalem['kalem_adi'], 'damacana') !== false) { 
                                $damacana_satir = $g_kalem; 
                                break; 
                            }
                        }
                        if ($damacana_satir):
                            $su_tutar = floatval($damacana_satir['tutar']);
                    ?>
                    <div class="d-flex align-items-center border-bottom border-light py-1 row-kalem">
                        <input type="text" class="form-control form-control-sm text-dark fw-semibold" value="<?= htmlspecialchars($damacana_satir['kalem_adi']) ?>" readonly style="background: transparent;">
                        
                        <div class="d-flex align-items-center ms-auto" style="width: 240px;">
                            <div class="d-flex align-items-center gap-1 bg-light border rounded px-1 me-1" style="height: 22px;">
                                <span class="text-muted" style="font-size: 9px;">₺</span>
                                <input type="number" id="su-birim-fiyat-<?= $damacana_satir['id'] ?>" value="140" class="p-0 text-center fw-bold border-0 bg-transparent text-dark" style="width: 42px; font-size: 10px;" title="Birim Fiyat" oninput="window.monitorGuncelle()">
                            </div>
                            <span class="text-muted fw-bold me-1" style="font-size: 9px;">Kalan:</span>
                            <input type="number" step="0.01" class="form-control form-control-sm text-end pe-1 text-danger font-monospace fw-bold" value="<?= number_format($su_tutar, 2, '.', '') ?>" readonly style="width: 80px; background: transparent; border: none;">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-xs btn-outline-success fw-bold px-2 py-0" style="font-size:10px; height:22px;" onclick="damacanaAksiyon(<?= $damacana_satir['id'] ?>, '+1')">+1</button>
                                <button type="button" class="btn btn-xs btn-warning text-dark fw-bold px-2 py-0" style="font-size:10px; height:22px;" onclick="damacanaAksiyon(<?= $damacana_satir['id'] ?>, '-1')" <?= ($su_tutar <= 0) ? 'disabled' : '' ?>>-1</button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- 🎯 2. BAĞIMSIZ HÜCRE: %100 GARANTİLİ NERGİZ CEP SATIRI -->
                    <?php 
                        $nergiz_cep_satir = null;
                        foreach ($giderler as $g_kalem) {
                            if (trim($g_kalem['kalem_adi']) === 'Nergiz Cep') { $nergiz_cep_satir = $g_kalem; break; }
                        }
                        $nergiz_odendi = $nergiz_cep_satir ? (floatval($nergiz_cep_satir['odenen_tutar']) > 0) : false;
                    ?>
                    <div class="d-flex align-items-center border-bottom border-light py-1 row-kalem <?= $nergiz_odendi ? 'row-odendi' : '' ?>">
                        <input type="text" class="form-control form-control-sm text-dark fw-semibold" value="Nergiz Cep" readonly style="background: transparent; <?= $nergiz_odendi ? 'text-decoration: line-through; opacity: 0.6;' : '' ?>">
                        <div class="d-flex align-items-center ms-auto" style="width: 210px;">
                            <input type="number" step="0.01" id="nergiz-cep-tutar" autocomplete="off" class="form-control form-control-sm text-end pe-2 text-danger border rounded bg-white px-1" value="<?= $nergiz_odendi ? '0.00' : '220.00' ?>" style="width: 145px; height: 22px; <?= $nergiz_odendi ? 'text-decoration: line-through; opacity: 0.6;' : '' ?>" <?= $nergiz_odendi ? 'disabled' : '' ?>>
                            <button type="button" class="btn btn-sm btn-light btn-mikro shadow-xs ode-btn" onclick="nergizCepOde()" <?= $nergiz_odendi ? 'disabled' : '' ?>><?= $nergiz_odendi ? '✓' : 'ÖDE' ?></button>
                        </div>
                    </div>

                </div>

                <div class="card-footer bg-light border-top border-light p-2">
                    <div class="d-flex align-items-center row-kalem">
                        <input type="text" id="yeni-gider-adi" class="form-control form-control-sm text-dark fw-semibold input-ekle" placeholder="Örn: n Gider Kalemi.. [n, y, v]" style="font-size: 11px !important;">
                        <div class="d-flex align-items-center ms-auto" style="width: 210px;">
                            <input type="number" step="0.01" id="yeni-gider-tutar" class="form-control form-control-sm text-end pe-2 text-danger input-ekle" placeholder="0.00">
                            <button type="button" class="btn btn-sm btn-danger btn-mikro px-2" onclick="hizliKalemEkle('gider')"><i class="bi bi-plus-lg"></i>EKLE</button>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- ORTA SÜTUN COL-MD-5 KAPANIŞI -->
<!-- 8 -->


        <!-- SAĞ SÜTUN: YENİ DAĞITIM MONİTÖRÜ VE ÜÇLÜ KART YAPISI -->
        <div class="col-md-4 monitorstyle" id="monitor-ekrani">
            
            <!-- 💳 ÜST KART: AKILLI FAİZ HESAPLAMA PANELİ -->
            <div class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-header bg-white border-bottom border-light py-2 px-3 text-dark fw-bold">
                    <i class="bi bi-percent text-warning me-1"></i> Faiz Hesapla
                </div>
                <div class="card-body p-2">
                    <div class="row g-2 align-items-center">
                        <div class="col-2">
                            <span class="" style="font-size:10px;">Oran %</span>
                        </div>
                        <div class="col-4">
                            <!-- 🎯 DÜZELTME: value parametresi 30 varsayılanı ile PHP'den beslenir -->
                            <input type="number" id="faiz-orani-input" class="form-control text-center p-1 fw-bold text-dark font-monospace" value="<?= $faiz_orani ?>">
                        </div>
                        <div class="col-2">
                            <span class="" style="font-size:10px;">Gün</span>
                        </div>
                        <div class="col-4">
                            <input type="number" id="faiz-gun-input" class="form-control text-center p-1 fw-bold text-dark font-monospace" value="<?= $faiz_gun_sayisi ?>">
                        </div>

                    <!-- 🎯 YENİ EKLENEN SATIR: Harici Faiz Başlangıç Bütçesi Girişi -->
                    <div class="row g-2 align-items-center mt-2 border-top border-light pt-2">
                        <div class="col-4">
                            <span class="text-secondary fw-semibold" style="font-size:10px;"><i class="bi bi-bank me-1 text-primary"></i>Kredi Faiz Bütçesi</span>
                        </div>
                        <div class="col-8">
                            <input type="number" step="0.01" id="faiz-kredi-butcesi-input" class="form-control text-end p-1 fw-bold text-danger font-monospace" value="<?= $evKredisiTutariStandart2  ?>" style="font-size:11px !important;">
                        </div>
                    </div>

                    <!-- 🚀 YENİ KUPON ÖDEMELİ FAİZ AKTAR BUTONU -->
                    <div class="d-grid gap-2 mt-2">
                        <button type="button" id="faiz-aktar-btn" class="btn btn-sm btn-success fw-bold text-white shadow-xs" style="font-size:11px;" <?= ($faiz_gun_sayisi <= 0) ? 'disabled' : '' ?>>
                            <i class="bi bi-safe2-fill me-1 text-warning"></i> ELDE EDİLEN FAİZLERİ HESAPLARA AKTAR
                        </button>
                    </div>

                    </div>
    
                </div>
            </div>
           

            <!-- 💳 ORTA KART: FİNANSAL LİKİDİTE HAVUZLARI MATRİSİ -->
            <div class="card shadow-sm border-0 bg-white mb-2">
                <div class="card-header bg-white border-bottom border-light py-2 px-3 text-dark fw-bold">
                    <i class="bi bi-bank2 text-primary me-1"></i> Finansal Likidite Havuzları
                </div>   
              
                <div class="card-body p-2">
                    <!-- 1. SATIR: İŞBANKASI (Ö) & İŞBANKASI (A) -->
                    <div class="row g-2 text-center mb-2">
                        <div class="col-6">
                            <!-- Tıklanabilir Kredi Havuzu İstasyonu -->
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #f8f9fa; min-height: 72px; cursor: pointer;" onclick="manuelHavuzTransferAksiyonu('isbank_o')">
                                <span class="d-block text-uppercase fw-bold text-secondary" style="font-size: 9px !important; line-height: 1.2;">İş Bankası (Ö)<br><span class="text-muted fw-normal" style="font-size: 8px;">Kredi Bütçesi</span></span>
                                <span id="isbank_o_kutu_anapara" class="d-block text-muted" style="font-size: 8px; line-height: 1.1;">Ana Para: <span class="fw-semibold text-dark"><?= number_format(max(0, $isbank_o_faizsiz_ana_para), 2, ',', '.') ?> ₺</span></span>
                                
                                <span id="isbank_o_kutu_bakiye" class="fw-bold d-block mt-1 font-monospace text-secondary" style="font-size: 11px !important;">
                                    <?= number_format($kredi_odendi ? $isbank_o_bakiye : max(0, $isbank_o_bakiye), 2, ',', '.') ?> ₺
                                </span>

                                <span class="text-muted d-block mt-1" style="font-size: 8px;">Brüt Nema: <span class="text-success fw-bold">+<?= number_format($net_kredi_faiz_t_php, 2, ',', '.') ?> ₺</span></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <!-- Tıklanabilir Yatırım Ana Havuz İstasyonu -->
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #e1f5fe; min-height: 72px; cursor: pointer;" onclick="manuelHavuzTransferAksiyonu('isbank_k')">
                                <span class="d-block text-uppercase fw-bold text-primary" style="font-size: 9px !important; line-height: 1.2;">İşbankası (A)<br><span class="text-muted fw-normal" style="font-size: 8px;">Yatırım Ana Havuz</span></span>
                                <span id="isbank_k_kutu_anapara" class="d-block text-muted" style="font-size: 8px; line-height: 1.1;">Ana Para: <span class="fw-semibold text-dark"><?= number_format(max(0, $isbank_k_faizsiz_ana_para), 2, ',', '.') ?> ₺</span></span>
                                
                                <span id="isbank_k_kutu_bakiye" class="fw-bold d-block mt-1 font-monospace text-primary" style="font-size: 11px !important;"><?= number_format(max(0, $isbank_k_bakiye), 2, ',', '.') ?> ₺</span>
                                <span class="text-muted d-block mt-1" style="font-size: 8px;">Brüt Nema: <span class="text-success fw-bold">+<?= number_format($net_ana_havuz_faiz_php, 2, ',', '.') ?> ₺</span></span>
                            </div>
                        </div>
                    </div>
                    <!-- 2. SATIR: VAKIFBANK (Ö) & NAKIT (EV) -->
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #fff9e6; min-height: 72px;">
                                <span class="d-block text-uppercase fw-bold text-warning-emphasis" style="font-size: 9px !important; line-height: 1.2;">Vakıfbank (Ö)<br><span class="text-muted fw-normal" style="font-size: 8px;">Bloke: <?= number_format($zorunlu_fatura_bloke_tutari, 2, ',', '.') ?> ₺</span></span>
                                <span id="vakif_kutu_bakiye" class="fw-bold d-block mt-1 font-monospace text-warning-emphasis" style="font-size: 11px !important;"><?= number_format(max(0, $vakifbank_bakiye), 2, ',', '.') ?> ₺</span>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="p-2 border rounded shadow-xs text-start position-relative" style="background-color: #e8f5e9; min-height: 72px;cursor: pointer;" onclick="maviButonPopUpAc()">                               
                                <span class="d-block text-uppercase fw-bold text-success" style="font-size: 9px !important; line-height: 1.2; padding-right: 15px;">Nakit (Ev)<br><span class="text-muted fw-normal" style="font-size: 8px;">Bloke: <?= number_format($zorunlu_su_bloke_tutari, 2, ',', '.') ?> ₺</span></span>
                                
                                <?php
                                    // 🎯 ERİME MOTORU: Orijinal yapınız kuruşu kuruşuna korundu
                                    $html_nakit_gelir = 0;
                                    $html_nakit_gider = 0;
                                    $html_harcanan_su = 0;
                                    $html_odenen_aidat = 0;

                                    foreach ($kalemler as $k_bale) {
                                        $kb_isim  = trim($k_bale['kalem_adi']);
                                        $kb_ttr   = floatval($k_bale['tutar']);
                                        $kb_odn   = floatval($k_bale['odenen_tutar']);
                                        $kb_turu  = $k_bale['turu'];
                                        $kb_kucuk = mb_strtolower($kb_isim, 'UTF-8');
                                        $kb_odendi = ($kb_odn > 0 || ($kb_turu === 'gelir' && $kb_odn == $kb_ttr && $kb_ttr > 0));

                                        if ($kb_turu === 'gelir' && $kb_odendi) {
                                            // Nakit -> Vakıf transfer kaydı gelir türünde eklense dahi parayı Nakit havuzundan çıkartır
                                            if (strpos($kb_kucuk, 'havuz transfer fonu (nakit -> vakif)') !== false) {
                                                $html_nakit_gelir -= $kb_odn;
                                                continue; 
                                            }

                                            if ($kb_isim === 'Evdeki Nakit Girişi' || str_starts_with($kb_kucuk, 'n ') || strpos($kb_kucuk, '-> nakit)') !== false) {
                                                $html_nakit_gelir += $kb_odn;
                                            }
                                        }

                                        if ($kb_turu === 'gider') {
                                            if ($kb_isim === 'Aidat' && $kb_odn > 0) {
                                                $html_odenen_aidat = $kb_odn;
                                            } 
                                            elseif (strpos($kb_kucuk, 'damacana') !== false || strpos($kb_kucuk, 'içme suyu') !== false) {
                                                $html_harcanan_su = max(0, 1120.00 - $kb_ttr);
                                            } 
                                            elseif (str_starts_with($kb_kucuk, 'n ') && $kb_odn > 0) {
                                                $html_nakit_gider += $kb_odn;
                                            }
                                        }
                                    }

                                    // Nihai saf Nakit (Ev) canlı bakiye formülü
                                    $nihai_canli_nakit_kutusu = $html_nakit_gelir - $html_nakit_gider - $html_harcanan_su - $html_odenen_aidat;
                                    if ($nihai_canli_nakit_kutusu < 0) $nihai_canli_nakit_kutusu = 0;
                                ?>

                                <span id="nakit_kutu_bakiye" class="fw-bold d-block mt-1 font-monospace text-success" style="font-size: 11px !important;">
                                    <?= number_format($nihai_canli_nakit_kutusu, 2, ',', '.') ?> ₺
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ================================================================================= -->
                <!-- 📊 SAĞ SÜTUNLA UYUMLU SAF JS TABANLI ZENGİNLEŞTİRİLMİŞ NİHAİ DASHBOARD -->
                <!-- ================================================================================= -->

                <!-- 💜 CARD 1: TASARRUF (Yatırım Yapılacak Hacim) -->
                <div class="card-body p-2" style="background-color: #fdfafd; border-left: 3.5px solid #8e24aa; border-radius: 4px; cursor: pointer;" onclick="forexAction('isbank_k')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 8.5px; letter-spacing: 0.5px;">💰 TASARRUF (YATIRIM HACMİ)</span>
                            <span class="text-muted" style="font-size: 8px;">İşbank (A) Ana Havuz Kilitli</span>
                        </div>
                        <!-- Karttaki ID ve veri aynen korunmuştur -->
                        <h5 id="canli-ana-yatirim-bakiye" class="fw-bold mb-0 font-monospace" style="color: #6a1b9a; font-size: 13.5px;">0,00 ₺</h5>
                    </div>
                </div>




                <!-- 💚 CARD 2: TOTAL GELİRLER VE AKILLI FAİZ LİKİDİTESİ -->
                <div class="card shadow-sm border-0 bg-white mb-2">
                    <div class="card-body p-2" style="background-color: #f4fbf7; border-left: 3.5px solid #2e7d32; border-radius: 4px;">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-1 mb-1">
                            <span class="fw-bold text-success text-uppercase" style="font-size: 8.5px; letter-spacing: 0.5px;">🚀 TOPLAM BAKİYE (GELİRLER)</span>
                            <span id="js_yeni_toplam_bakiye" class="fw-bold font-monospace text-success" style="font-size: 13.5px;">0,00 ₺</span>
                        </div>
                        <div class="row g-0 text-center" style="font-size: 9px; line-height: 1.2;">
                            <div class="col-6 border-end border-light text-start ps-1">
                                <span class="text-muted d-block" style="font-size: 7.5px;">MEVCUT BAKİYE (4 HAVUZ)</span>
                                <span id="js_yeni_mevcut_bakiye" class="fw-bold text-dark font-monospace">0,00 ₺</span>
                            </div>
                            <div class="col-6 text-end pe-1">
                                <span class="text-muted d-block" style="font-size: 7.5px;">CANLI FAİZ GELİRİ</span>
                                <span id="js_yeni_elde_edilecek_faiz" class="fw-bold text-warning-emphasis font-monospace">+0,00 ₺</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ❤️ CARD 4: DİĞER GİDERLER (FATURA, SU VE KREDİ BLOKE TOPLAMI) -->
                <div class="card shadow-sm border-0 bg-white mb-3">
                    <div class="card-body p-2" style="background-color: #fff5f5; border-left: 3.5px solid #c92a2a; border-radius: 4px;">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-1 mb-1">
                            <span class="fw-bold text-danger text-uppercase" style="font-size: 8.5px; letter-spacing: 0.5px;">📊 DİĞER GİDERLER TOPLAMI</span>
                            <span id="js_dashboard_mutlak_toplam_borc" class="fw-bold font-monospace text-danger" style="font-size: 13.5px;">0,00 ₺</span>
                        </div>
                        <div class="row g-1 text-center pt-1" style="font-size: 9px; line-height: 1.2;">
                            <div class="col-4 border-end border-light text-start ps-1">
                                <span class="text-muted d-block" style="font-size: 7.5px; font-weight: 500;">FATURALAR (VAKIF BLOKE)</span>
                                <span id="js_yeni_faturalar_bloke" data-taban="<?= floatval($zorunlu_fatura_bloke_tutari ?? 0) ?>" class="fw-bold text-primary font-monospace" style="font-size: 9.5px;">0,00 ₺</span>
                            </div>
                            <div class="col-4 border-end border-light px-1">
                                <span class="text-muted d-block" style="font-size: 7.5px; font-weight: 500;">AİDAT / BLOKE SU</span>
                                <span id="js_dashboard_gider_su" data-taban="<?= floatval($zorunlu_su_bloke_tutari ?? 0) ?>" class="fw-bold font-monospace text-danger-emphasis" style="font-size: 9.5px;">0,00 ₺</span>
                            </div>
                            <div class="col-4 text-end pe-1">
                                <span class="text-muted d-block" style="font-size: 7.5px; font-weight: 500;">KREDİ (İŞBANK Ö BLOKE)</span>
                                <!-- 🎯 DÜZELTME: Ev kredisi bloke taban değeri mühürlendi -->
                                <span id="js_dashboard_gider_kredi" data-taban="<?= floatval($isbank_o_faizsiz_ana_para ?? $evKredisiTutariStandart2 ?? 53315.33) ?>" class="fw-bold font-monospace text-dark" style="font-size: 9.5px;">0,00 ₺</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🧡 CARD 3: 4 ALT HÜCRELİ DETAYLI DÖNEM RISK ANALİZİ -->
                <div class="card shadow-sm border-0 bg-white mb-2">
                    <div class="card-body p-2" style="background-color: #fff9f6; border-left: 3.5px solid #fd7e14; border-radius: 4px;">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-1 mb-1">
                            <span class="fw-bold text-uppercase" style="color: #d9480f; font-size: 8.5px; letter-spacing: 0.5px;">💳 KK DÖNEM BORCU (+PROVİZYON)</span>
                            <span id="js_yeni_kk_donem_borcu" class="fw-bold font-monospace" style="color: #d9480f; font-size: 13.5px;">0,00 ₺</span>
                        </div>
                        <!-- 🎯 DÜZELTME: Tam istediğin gibi 4'lü bağımsız mikro hücreye dönüştürüldü -->
                        <div class="row g-1 text-center" style="font-size: 8.5px; line-height: 1.15;">
                            <div class="col-3 border-end border-light text-start ps-1">
                                <span class="text-muted d-block" style="font-size: 7px;">DÖNEM İÇİ</span>
                                <span id="js_dashboard_donem_ici" class="fw-bold text-dark font-monospace">0,00 ₺</span>
                            </div>
                            <div class="col-3 border-end border-light px-0.5">
                                <span class="text-muted d-block" style="font-size: 7px;">PROVİZYON</span>
                                <span id="js_dashboard_provizyon_toplami" class="fw-bold text-primary font-monospace">0,00 ₺</span>
                            </div>
                            <div class="col-3 border-end border-light px-0.5">
                                <span class="text-muted d-block" style="font-size: 7px;">DÖN. TAKSİTLERİ</span>
                                <span id="js_dashboard_donem_taksitleri" class="fw-bold font-monospace" style="color: #d9480f;">0,00 ₺</span>
                            </div>
                            <div class="col-3 text-end pe-1">
                                <span class="text-muted d-block" style="font-size: 7px;">DÖVİZ BORCU</span>
                                <span id="js_dashboard_doviz_borcu" class="fw-bold text-danger font-monospace">0,00 ₺</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 💙 YENİ CARD: KREDİ KARTI TOPLAM BORÇ MATRİSİ -->
                <div class="card shadow-sm border-0 bg-white mb-2">
                    <div class="card-body p-2" style="background-color: #e3f2fd; border-left: 3.5px solid #1565c0; border-radius: 4px;">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-1 mb-1">
                            <span class="fw-bold text-uppercase" style="color: #0d47a1; font-size: 8.5px; letter-spacing: 0.5px;">💳 KK TOPLAM BORÇ</span>
                            <span id="js_dashboard_kk_toplam_borclari" class="fw-bold font-monospace" style="color: #0d47a1; font-size: 13.5px;">0,00 ₺</span>
                        </div>
                        <div class="row g-0 text-center" style="font-size: 9px; line-height: 1.2;">
                            <div class="col-6 border-end border-light text-start ps-1">
                                <span class="text-muted d-block" style="font-size: 7.5px;">KESİNLEŞEN EKSTRELER</span>
                                <span id="js_dashboard_kesinlesen_ekstre" class="fw-bold text-dark font-monospace">0,00 ₺</span>
                            </div>
                            <div class="col-6 text-end pe-1">
                                <span class="text-muted d-block" style="font-size: 7.5px;">KALAN TAKSİTLER TOPLAMI</span>
                                <span id="js_yeni_kk_borcumuz" class="fw-bold text-dark font-monospace">0,00 ₺</span>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- 🎯 CARD 5: HARİCİ İNTERAKTİF TO-DO GÖREV İSTASYONU (YALITILMIŞ STATİK HTML HABERLEŞMESİ) -->
                <div class="card shadow-sm border-0 bg-white mb-2">
                    <div class="card-header bg-white border-bottom border-light py-1.5 px-2">
                        <span class="text-dark fw-bold text-uppercase d-block" style="font-size: 9px; letter-spacing: 0.5px;">
                            <i class="bi bi-clipboard-check-fill text-secondary me-1"></i> 📜 Yapılması Gereken Transferler (To-Do)
                        </span>
                    </div>
                    <div class="card-body p-1" style="background-color: #fafbfc;">
                        <div class="d-flex flex-column gap-1">

                                    <!-- 📌 Görev 2 (C2) -->
                            <div class="todo-item p-2 border rounded bg-white" id="todo_c2" onclick="todoGorevTikla('c2')" style="cursor: pointer; transition: all 0.2s; line-height: 1.25;">
                                <div class="d-flex align-items-start font-monospace text-dark" style="font-size: 9.5px; font-weight: 500;">
                                    <span class="badge bg-secondary me-1.5 px-1 py-0.5" style="font-size: 8px;">C1</span>
                                    <div>
                                        İŞBANKASI (A) hesabından vadesiz hesaba <span id="todo_c2_tutar" class="fw-bold text-success">0,00 ₺</span> aktar.<br>
                                        <span id="todo_c0_tutar" class="fw-bold text-success">0,00 ₺</span> dönem borcunu öde.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 📌 Görev 1 (C1) -->
                            <div class="todo-item p-2 border rounded bg-white" id="todo_c1" onclick="todoGorevTikla('c1')" style="cursor: pointer; transition: all 0.2s; line-height: 1.25;">
                                <div class="d-flex align-items-start font-monospace text-dark" style="font-size: 9.5px; font-weight: 500;">
                                    <span class="badge bg-secondary me-1.5 px-1 py-0.5" style="font-size: 8px;">C2</span>
                                    <div>İŞBANKASI (A) hesabından Vakıfbank (Ö) hesabına <span id="todo_c1_tutar" class="fw-bold text-primary">0,00 ₺</span> EFT yap. (Vkf Kesinleşen Ekstre + Faturalar + Aidat + Damacana Su) </div>
                                </div>
                            </div>
                                    
                            <!-- 📌 Görev 3 (C3) -->
                            <div class="todo-item p-2 border rounded bg-white" id="todo_c3" onclick="todoGorevTikla('c3')" style="cursor: pointer; transition: all 0.2s; line-height: 1.25;">
                                <div class="d-flex align-items-start font-monospace text-dark" style="font-size: 9.5px; font-weight: 500;">
                                    <span class="badge bg-secondary me-1.5 px-1 py-0.5" style="font-size: 8px;">C3</span>
                                    <div>Vakıfbank (Ö) hesabından <span id="todo_c5_tutar" class="fw-bold text-warning-emphasis">0,00 ₺</span> dönem borcunu öde.<br>
                                    Damacana Su + Aidat için <span id="todo_c7_tutar" class="fw-bold text-warning-emphasis">0,00 ₺</span> nakit çek.<br>
                                    Faturalar için <span id="todo_c8_tutar" class="fw-bold text-warning-emphasis">0,00 ₺</span> hesapta kalacak.</div>
                                    
                                </div>
                            </div>
                            
                            <!-- 📌 Görev 4 (C4) -->
                            <div class="todo-item p-2 border rounded bg-white" id="todo_c4" onclick="todoGorevTikla('c4')" style="cursor: pointer; transition: all 0.2s; line-height: 1.25;">
                                <div class="d-flex align-items-start font-monospace text-dark" style="font-size: 9.5px; font-weight: 500;">
                                    <span class="badge bg-secondary me-1.5 px-1 py-0.5" style="font-size: 8px;">C4</span>
                                    <div>İŞBANKASI (Ö) vadeli hesabındaki <span id="todo_c4_tutar1" class="fw-bold text-danger">0,00 ₺</span> vadesize aktar kredi borcunu öde. <span id="todo_c4_tutar2" class="fw-bold text-dark">0,00 ₺</span> Vakıfbank (Ö)'ye aktar.</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>



            </div>

            <!-- 💳 ALT KART: CANLI EFT VE HAVUZ TRANSFER LOG LİSTESİ -->
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-header bg-white border-bottom border-light py-2 px-3 text-dark fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-arrow-left-right text-info me-1"></i> Canlı EFT Transfer Emirleri</span>
                    <span class="badge bg-light text-dark font-monospace" style="font-size:8px;">Canlı Log</span>
                </div>
                <div class="card-body p-2" style="max-height: 160px; overflow-y: auto;">
                    <ul class="list-group list-group-flush" style="font-size:10px;">
                        <?php 
                        $has_transfer = false;
                        foreach ($gelirler as $t_log) {
                            if (strpos(mb_strtolower($t_log['kalem_adi'], 'UTF-8'), 'havuz transfer fonu') !== false) {
                                $has_transfer = true;
                                echo '<li class="list-group-item p-1 border-bottom border-light-subtle d-flex justify-content-between align-items-center font-monospace">';
                                echo '<span class="text-dark fw-semibold"><i class="bi bi-send text-primary me-1"></i>' . htmlspecialchars($t_log['kalem_adi']) . '</span>';
                                echo '<span class="text-success fw-bold">+' . number_format($t_log['tutar'], 2, ',', '.') . ' ₺</span>';
                                echo '</li>';
                            }
                        }
                        if (!$has_transfer) {
                            echo '<div class="text-muted text-center py-3" style="font-size:10px;"><i class="bi bi-info-circle me-1"></i>Henüz bu dönemde bir EFT işlemi gerçekleştirilmedi.</div>';
                        }
                        ?>
                    </ul>
                </div>
            </div>

            <input type="hidden" id="js_su_bloke_miktari" value="<?= $zorunlu_su_bloke_tutari ?>">
            <input type="hidden" id="js_fatura_bloke_miktari" value="<?= $zorunlu_fatura_bloke_tutari ?>">
        </div>


    </div> <!-- 1. SATIR Row Kapanışı -->
<!-- 9 -->

    <!-- 2. SATIR: EXCEL ARŞİV DASHBOARD TABLOSU (BLOK TOPLAMLARI DAHİL) -->
    <div class="row g-3 mt-2 mb-4 excel-dashboard-wrapper">
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-header bg-white border-bottom border-light py-2 px-3 d-flex justify-content-between align-items-center">
                    <span class="text-dark fw-bold" style="font-size: 12px;">
                        <i class="bi bi-grid-3x3-gap-fill text-success me-1"></i> Finansal Dönemler Arşiv Raporu (Excel Görünümü)
                    </span>
                    <span class="badge bg-success text-white font-monospace" style="font-size: 9px;">Kümülatif Otomatik Senkron</span>
                </div>
                <div class="card-body p-2">
                    <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                        <table class="table table-sm table-bordered align-middle text-center mb-0 excel-table" style="min-width: 1450px;">
                            <thead>
                                <!-- 🎯 SATIR 1: EXCEL ÜST BÜTÜNLEŞİK BLOK TOPLAMLARI (GÖRSELDEKİ BÜYÜK RENKLİ ALANLAR) -->
                                <?php 
                                    // 12 aylık dikey genel gelirler toplamına faiz gelirleri de dahil edildi
                                    $yillik_butun_gelirler_toplami = $tepe_toplamlar['maas_o'] + $tepe_toplamlar['maas_a'] + $tepe_toplamlar['maktu_mesai'] + $tepe_toplamlar['ek_gelir'] + $tepe_toplamlar['faiz_gelir'];
                                    $yillik_butun_giderler_toplami = $tepe_toplamlar['kredi_karti_o'] + $tepe_toplamlar['kredi_karti_a'] + $tepe_toplamlar['ev_kredisi'] + $tepe_toplamlar['aidat'] + $tepe_toplamlar['harcama_nakit'] + $tepe_toplamlar['fatura_nakit'];
                                    $yillik_butun_tasarruf_toplami = $yillik_butun_gelirler_toplami - $yillik_butun_giderler_toplami;
                                    $yillik_butun_yatirim_toplami = $tepe_toplamlar['yatirim_tutari'] ?? 0;
                                ?>
                                <tr style="height: 26px; font-size: 10px; font-weight: 800;">
                                    <th class="bg-dark text-white border-dark">YILLIK BLOK</th>
                                    <!-- GELİRLER BLOK TOPLAMI (4 Sütun Genişliğinde) -->
                                    <th colspan="5" class="excel-group-gelir text-center" style="font-size:10px;">
                                        GELİRLER TOPLAMI: <?= number_format($yillik_butun_gelirler_toplami, 2, ',', '.') ?> ₺
                                    </th>
                                    <!-- GİDERLER BLOK TOPLAMI (7 Sütun Genişliğinde) -->
                                    <th colspan="5" class="excel-group-gider text-center" style="font-size:10px;">
                                        GİDERLER TOPLAMI: <?= number_format($yillik_butun_giderler_toplami, 2, ',', '.') ?> ₺
                                    </th>
                                    <!-- NAKİT (EV) BLOK TOPLAMI (5 Sütun Genişliğinde) -->
                                    <th colspan="6" class="excel-group-nakit text-center" style="font-size:10px;">
                                        NAKİT (EV) TOPLAMI: <?= number_format($tepe_toplamlar['harcama_nakit'], 2, ',', '.') ?> ₺
                                    </th>
                                    <!-- TASARRUF BLOK TOPLAMI (1 Sütun Genişliğinde) -->
                                    <th class="excel-group-tasarruf text-center" style="font-size:10px;">
                                        <?= number_format($yillik_butun_tasarruf_toplami, 2, ',', '.') ?> ₺
                                    </th>
                                    <!-- YATIRIM BLOK TOPLAMI (1 Sütun Genişliğinde) -->
                                    <th class="excel-group-yatirim text-center" style="font-size:10px;">
                                        <?= number_format($yillik_butun_yatirim_toplami, 2, ',', '.') ?> ₺
                                    </th>
                                </tr>

                                <!-- SATIR 2: ANA GRUP BAŞLIKLARI -->
                                <tr class="fw-bold text-uppercase" style="font-size: 9px; height: 22px;">
                                    <th rowspan="1" class="bg-secondary text-white border-secondary">DÖNEM</th>
                                    <th colspan="5" class="excel-group-gelir">GELİRLER</th>
                                    <th colspan="5" class="excel-group-gider">GİDERLER</th>
                                    <th colspan="6" class="excel-group-nakit">SABİT FATURALAR (DETAY)</th>
                                    <th rowspan="1" class="excel-group-tasarruf" style="font-size: 10px;"></th>
                                    <th rowspan="1" class="excel-group-yatirim" style="font-size: 10px;"></th>
                                </tr>
                                
                                <!-- SATIR 3: SÜTUN ALT BAŞLIKLARI -->
                                <tr class="excel-head-title" style="height: 22px;">
                                    <th class="text-center bg-secondary text-white border-secondary" style="font-size: 9px;"></th>
                                    <th>MAAŞ (Ö)</th>
                                    <th>MAAŞ (A)</th>
                                    <th>MAKTU MESAİ</th>
                                    <th>EK GELİR</th>
                                    <th>FAİZ GELİRİ</th> <!-- Yeni Sütun Başlığı -->
                                    <th>KREDİ KARTI (Ö)</th>
                                    <th>KREDİ KARTI (A)</th>
                                    <th>KREDİ</th>
                                    <th>AİDAT</th>
                                    <th>HARCAMA (NAKİT)</th>
                                    <th class="table-primary text-dark fw-bold">FATURA (NAKİT)</th>
                                    <th>ELEKTRİK</th>
                                    <th>SU</th>
                                    <th>DOĞALGAZ</th>
                                    <th>TELEFON</th>
                                    <th>İNTERNET</th>
                                    <th class="excel-group-tasarruf" style="font-size: 9px;">TASARRUF YAPILAN TUTAR</th>
                                    <th class="excel-group-yatirim" style="font-size: 9px;">YATIRIM YAPILAN TUTAR</th>
                                </tr>
                                
                                <!-- SATIR 4: SÜTUN BAZLI MİKRO TOPLAM KUTUCUKLARI -->
                                <tr class="tepe-kutu text-end font-monospace" style="height: 24px; background-color: #f8f9fa;">
                                    <td class="text-center fw-bold bg-dark text-white border-dark" style="font-size:9px;">SÜTUN TOP.</td>
                                    <td class="table-success"><?= number_format($tepe_toplamlar['maas_o'], 2, ',', '.') ?> ₺</td>
                                    <td class="table-success"><?= number_format($tepe_toplamlar['maas_a'], 2, ',', '.') ?> ₺</td>
                                    <td class="table-success"><?= number_format($tepe_toplamlar['maktu_mesai'], 2, ',', '.') ?> ₺</td>
                                    <td class="table-success"><?= number_format($tepe_toplamlar['ek_gelir'], 2, ',', '.') ?> ₺</td>
                                    <td class="table-success"><?= number_format($tepe_toplamlar['faiz_gelir'], 2, ',', '.') ?> ₺</td> <!-- Yeni Sütun Toplam Hücresi -->
                                    <td class="table-danger"><?= number_format($tepe_toplamlar['kredi_karti_o'], 2, ',', '.') ?> ₺</td>
                                    <td class="table-danger"><?= number_format($tepe_toplamlar['kredi_karti_a'], 2, ',', '.') ?> ₺</td>
                                    <td class="table-danger"><?= number_format($tepe_toplamlar['ev_kredisi'], 2, ',', '.') ?> ₺</td>
                                    <td class="table-danger"><?= number_format($tepe_toplamlar['aidat'], 2, ',', '.') ?> ₺</td>
                                    <td class="table-danger"><?= number_format($tepe_toplamlar['harcama_nakit'], 2, ',', '.') ?> ₺</td>
                                    <td class="text-dark fw-bold bg-primary-subtle text-center"><?= number_format($tepe_toplamlar['fatura_nakit'], 2, ',', '.') ?> ₺</td>
                                    <td class="table-info"><?= number_format($tepe_toplamlar['elektrik'], 2, ',', '.') ?> ₺</td>
                                    <td class="table-info"><?= number_format($tepe_toplamlar['su'], 2, ',', '.') ?> ₺</td>
                                    <td class="table-info"><?= number_format($tepe_toplamlar['dogalgaz'], 2, ',', '.') ?> ₺</td>
                                    <td class="table-info"><?= number_format($tepe_toplamlar['telefon'], 2, ',', '.') ?> ₺</td>
                                    <td class="table-info"><?= number_format($tepe_toplamlar['internet'], 2, ',', '.') ?> ₺</td>
                                    <td class="excel-group-tasarruf text-end fw-bold" style="font-size:9px;"><?= number_format($yillik_butun_tasarruf_toplami, 2, ',', '.') ?> ₺</td>
                                    <td class="excel-group-yatirim text-end fw-bold" style="font-size:9px;"><?= number_format($yillik_butun_yatirim_toplami, 2, ',', '.') ?> ₺</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 1; $i <= 12; $i++): 
                                    $satir_var_mi = isset($yillik_matris[$i]);
                                    $s_veri = $satir_var_mi ? $yillik_matris[$i] : [];
                                  
                                    //$is_aktif_ay = ($aktif_donem === sprintf("2026-%02d", $i));
                                    $is_aktif_ay = ($aktif_donem === sprintf("{$aktif_yil}-%02d", $i));

                                    

                                    // Eğer döngü o anki aktif aya geldiyse canlı faizi satır toplamına dahil et
                                    //$s_canli_faiz = $is_aktif_ay ? $toplam_canli_faiz_kazanci : floatval($s_veri['faiz_gelir'] ?? 0);
                                    $s_canli_faiz = floatval($s_veri['faiz_gelir'] ?? 0);

                                    // 🎯 DÜZELTME: Fatura (Nakit) hücresine sadece 5 faturanın o aya ait saf bütçe toplamı kilitlenir
                                    $s_fatura_nakit_toplami = floatval($s_veri['elektrik'] ?? 0) + floatval($s_veri['su'] ?? 0) + floatval($s_veri['dogalgaz'] ?? 0) + floatval($s_veri['telefon'] ?? 0) + floatval($s_veri['internet'] ?? 0);

                                    
                                    // 🎯 SAF MATEMATİK REFORMA: Yatay sızıntıyı önlemek için faiz geliri de satır gelirine dahil edildi
                                    $s_gelir = floatval($s_veri['maas_o'] ?? 0) + floatval($s_veri['maas_a'] ?? 0) + floatval($s_veri['maktu_mesai'] ?? 0) + floatval($s_veri['ek_gelir'] ?? 0) + $s_canli_faiz;
                                    
                                    // Giderler hesaplanırken her sütun kendi değeriyle dahil edilir (Çift sayma tamamen engellendi)
                                    $s_gider = floatval($s_veri['kredi_karti_o'] ?? 0) + floatval($s_veri['kredi_karti_a'] ?? 0) + floatval($s_veri['ev_kredisi'] ?? 0) + floatval($s_veri['aidat'] ?? 0) + floatval($s_veri['harcama_nakit'] ?? 0) + $s_fatura_nakit_toplami;
                                    
                                    // Net tasarruf formülü
                                    $s_tasarruf = $s_gelir - $s_gider;
                                    $s_yatirim = floatval($s_veri['yatirim_tutari'] ?? 0);
                                ?>
                                    <tr style="height: 24px;" class="<?= $is_aktif_ay ? 'excel-aktif-satir' : 'border-bottom border-light' ?>">
                                        <!-- Dönem İsim Başlığı -->
                                        <td class="text-start ps-2 fw-bold bg-light text-secondary" style="font-size: 9px;"><?= $ay_isimleri[$i] ?> <?=$aktif_yil;?></td>
                                        
                                        <!-- GELİRLER BLOK VERİLERİ -->
                                        <td class="text-end font-monospace text-success pe-2"><?= $satir_var_mi && floatval($s_veri['maas_o'] ?? 0) > 0 ? number_format($s_veri['maas_o'], 2, ',', '.') . ' ₺' : '' ?></td>
                                        <td class="text-end font-monospace text-success pe-2"><?= $satir_var_mi && floatval($s_veri['maas_a'] ?? 0) > 0 ? number_format($s_veri['maas_a'], 2, ',', '.') . ' ₺' : '' ?></td>
                                        <td class="text-end font-monospace text-success pe-2"><?= $satir_var_mi && floatval($s_veri['maktu_mesai'] ?? 0) > 0 ? number_format($s_veri['maktu_mesai'], 2, ',', '.') . ' ₺' : '' ?></td>
                                        <td class="text-end font-monospace text-success pe-2"><?= $satir_var_mi && floatval($s_veri['ek_gelir'] ?? 0) > 0 ? number_format($s_veri['ek_gelir'], 2, ',', '.') . ' ₺' : '' ?></td>
                                        <!-- 🎯 YENİ HÜCRE: Faiz Geliri hücresi matrise basılıyor -->
                                        <td class="text-end font-monospace text-success pe-2"><?= $s_canli_faiz > 0 ? number_format($s_canli_faiz, 2, ',', '.') . ' ₺' : '' ?></td>
                                        
                                        <!-- GİDERLER BLOK VERİLERİ -->
                                        <td class="text-end font-monospace text-danger pe-2"><?= $satir_var_mi && floatval($s_veri['kredi_karti_o'] ?? 0) > 0 ? number_format($s_veri['kredi_karti_o'], 2, ',', '.') . ' ₺' : '' ?></td>
                                        <td class="text-end font-monospace text-danger pe-2"><?= $satir_var_mi && floatval($s_veri['kredi_karti_a'] ?? 0) > 0 ? number_format($s_veri['kredi_karti_a'], 2, ',', '.') . ' ₺' : '' ?></td>
                                        <td class="text-end font-monospace text-danger pe-2"><?= $satir_var_mi && floatval($s_veri['ev_kredisi'] ?? 0) > 0 ? number_format($s_veri['ev_kredisi'], 2, ',', '.') . ' ₺' : '' ?></td>
                                        <td class="text-end font-monospace text-danger pe-2"><?= $satir_var_mi && floatval($s_veri['aidat'] ?? 0) > 0 ? number_format($s_veri['aidat'], 2, ',', '.') . ' ₺' : '' ?></td>
                                        <td class="text-end font-monospace text-danger pe-2"><?= $satir_var_mi && floatval($s_veri['harcama_nakit'] ?? 0) > 0 ? number_format($s_veri['harcama_nakit'], 2, ',', '.') . ' ₺' : '' ?></td>
                                        
                                        <!-- 🎯 DÜZELTME HÜCRESİ: Sadece o aya ait faturaların toplamını basar -->
                                        <td class="text-end font-monospace text-dark fw-bold  text-center"><?= $satir_var_mi && $s_fatura_nakit_toplami > 0 ? number_format($s_fatura_nakit_toplami, 2, ',', '.') . ' ₺' : '0,00 ₺' ?></td>
                                        
                                        <!-- DETAY SABİT FATURA VERİLERİ -->
                                        <td class="text-end font-monospace text-muted pe-1" style="font-size: 9px;"><?= $satir_var_mi && floatval($s_veri['elektrik'] ?? 0) > 0 ? number_format($s_veri['elektrik'], 2, ',', '.') . ' ₺' : '' ?></td>
                                        <td class="text-end font-monospace text-muted pe-1" style="font-size: 9px;"><?= $satir_var_mi && floatval($s_veri['su'] ?? 0) > 0 ? number_format($s_veri['su'], 2, ',', '.') . ' ₺' : '' ?></td>
                                        <td class="text-end font-monospace text-muted pe-1" style="font-size: 9px;"><?= $satir_var_mi && floatval($s_veri['dogalgaz'] ?? 0) > 0 ? number_format($s_veri['dogalgaz'], 2, ',', '.') . ' ₺' : '' ?></td>
                                        <td class="text-end font-monospace text-muted pe-1" style="font-size: 9px;"><?= $satir_var_mi && floatval($s_veri['telefon'] ?? 0) > 0 ? number_format($s_veri['telefon'], 2, ',', '.') . ' ₺' : '' ?></td>
                                        <td class="text-end font-monospace text-muted pe-1" style="font-size: 9px;"><?= $satir_var_mi && floatval($s_veri['internet'] ?? 0) > 0 ? number_format($s_veri['internet'], 2, ',', '.') . ' ₺' : '' ?></td>
                                        
                                        <!-- YATAY NET TASARRUF SONUCU -->
                                        <td class="text-end font-monospace excel-group-tasarruf fw-bold pe-2 <?= $s_tasarruf >= 0 ? 'text-primary' : 'text-danger' ?>" style="font-size: 10px;">
                                            <?= $satir_var_mi && $s_gelir > 0 ? number_format($s_tasarruf, 2, ',', '.') . ' ₺' : '0,00 ₺' ?>
                                        </td>
                                        <!-- YATAY NET TASARRUF SONUCU -->
                                        <td class="text-end font-monospace excel-group-yatirim fw-bold pe-2 <?= $s_yatirim >= 0 ? 'text-primary' : 'text-danger' ?>" style="font-size: 10px;">
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



</div> <!-- Container Kapanışı -->

<!-- 🚨 YENİLENMİŞ EVRENSEL BAKİYE TRANSFER MODALI (4 HAVUZ VE MİLİMETRİK KURAL UYUMLU) -->
<div class="modal fade" id="bayeTransferModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" style="font-size: 12px;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-danger text-white py-2 px-3">
                <h6 class="modal-title fw-bold text-white m-0" id="modalTitle">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Yetersiz Bakiye Uyarısı
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-3">
                <p class="mb-3 text-dark fw-semibold" id="modalMessage"></p>
                
                <div class="card p-2 border border-secondary-subtle bg-white mb-3">
                    <span class="text-muted fw-bold d-block mb-2 text-uppercase" style="font-size: 10px;">
                        <i class="bi bi-box-arrow-in-right text-primary me-1"></i>Para Aktarılacak Kaynak Hesap Seçimi
                    </span>
                    <div id="alternatifHavuzlarAlani" class="d-flex flex-column gap-2"></div>
                </div>

                <div class="row g-2 align-items-center">
                    <div class="col-7">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light fw-bold text-secondary">₺</span>
                            <input type="number" id="transferTutarInput" class="form-control fw-bold font-monospace text-end text-dark" placeholder="0.00" step="0.01">
                        </div>
                    </div>
                    <div class="col-5">
                        <button type="button" id="modalEftBtn" class="btn btn-sm btn-primary w-100 fw-bold shadow-xs">
                            <i class="bi bi-send-fill me-1"></i> TRANSFER ET & ÖDE
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-1 bg-white border-top border-light">
                <button type="button" class="btn btn-xs btn-secondary fw-bold" data-bs-dismiss="modal">İptal Et</button>
            </div>
        </div>
    </div>
</div>

<!-- 🚀 SADECE BU SAYFAYA ÖZEL BAĞIMSIZ MANUEL FON TRANSFER MODALI -->
<div class="modal fade" id="manuelHavuzIstasyonModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" style="font-size: 12px;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white py-2 px-3">
                <h6 class="modal-title fw-bold text-white m-0">
                    <i class="bi bi-arrow-left-right me-2"></i>Aktif Likidite Transfer İstasyonu
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-3">
                <p class="mb-3 text-dark fw-semibold" id="istasyonModalMessage"></p>
                
                <div class="card p-2 border border-secondary-subtle bg-white mb-3">
                    <span class="text-muted fw-bold d-block mb-2 text-uppercase" style="font-size: 10px;">
                        <i class="bi bi-box-arrow-in-right text-primary me-1"></i>Para Aktarılacak Hedef Hesap Seçimi
                    </span>
                    <div id="istasyonHedefHavuzlarAlani" class="d-flex flex-column gap-2"></div>
                </div>

                <div class="row g-2 align-items-center">
                    <div class="col-7">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light fw-bold text-secondary">₺</span>
                            <input type="number" id="istasyonTransferTutarInput" class="form-control fw-bold font-monospace text-end text-dark" placeholder="0.00" step="0.01">
                        </div>
                    </div>
                    <div class="col-5">
                        <!-- 🎯 DÜZELTME: Karakter kodlama hatasını önlemek için fonksiyon adı evrensel karakterlere çekildi -->
                        <button type="button" id="istasyonModalEftBtn" class="btn btn-sm btn-primary w-100 fw-bold shadow-xs" onclick="kesinTransferEmriniAtesle()">
                            <i class="bi bi-check-circle-fill me-1"></i> TRANSFERİ ONAYLA
                        </button>

                    </div>
                </div>
            </div>
            <div class="modal-footer py-1 bg-white border-top border-light">
                <button type="button" class="btn btn-xs btn-secondary fw-bold" data-bs-dismiss="modal">İptal Et</button>
            </div>
        </div>
    </div>
</div>

<!-- 🚀 SAF NAKİT -> VAKIF TRANSFER POP-UP PENCERESİ -->
<div class="modal fade" id="maviButonVakifModal" tabindex="-1" aria-labelledby="maviButonVakifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white py-2 px-3">
                <h6 class="modal-title fw-bold" style="font-size:12px;">
                    <i class="bi bi-arrow-left-right text-warning me-1"></i> Nakit -> Vakıf Transferi
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="font-size:10px;"></button>
            </div>
            <div class="modal-body p-3" style="background-color: #fdfbf7;">
                <!-- Tutar Giriş Inputu -->
                <div class="mb-3">
                    <label class="form-label text-muted fw-bold mb-1" style="font-size:10px;">AKTARILACAK SAF TUTAR (₺)</label>
                    <input type="number" step="0.01" id="maviPopUpTutarInput" class="form-control form-control-sm text-end fw-bold text-dark font-monospace" placeholder="0.00" style="font-size:13px; height: 28px;">
                </div>
                
                <!-- Onay Butonu -->
                <div class="d-grid gap-2 mt-3">
                    <button type="button" id="maviPopUpOnayBtn" class="btn btn-sm btn-primary fw-bold" onclick="kesinVakifAktariminiAtesle()">
                        <i class="bi bi-check-circle-fill me-1"></i> VAKIFBANK'A AKTAR
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary fw-bold mt-1" data-bs-dismiss="modal" style="font-size:10px;">İPTAL</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- NİHAİ %100 INTERAKTİF FOREX SATIN ALMA POP-UP (MODAL) ARABİRİMİ -->
<!-- ========================================================================= -->
<div class="modal fade" id="forexYatirimModal" tabindex="-1" aria-labelledby="forexYatirimModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            
            <!-- Modal Başlık Alanı -->
            <div class="modal-header bg-dark text-white py-3" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h6 class="modal-title fw-bold text-white mb-0" id="forexYatirimModalLabel">
                    📝 Döviz & Varlık Satın Alma Terminali
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            
            <!-- Satın Alma Formu -->
            <form action="" method="POST" id="forexSatinalForm">
                <!-- Backend kabul motorunu tetikleyen gizli aksiyon tanımı -->
                <input type="hidden" name="action" value="interaktif_forex_satinal">
                
                <div class="modal-body p-4 text-dark fs-6">
                    
                    <!-- 1. Üst Şerit: İşbank (A) Yatırım Ana Havuz Dinamik Bakiyesi -->
                    <div class="p-3 bg-light rounded-3 border mb-3 d-flex justify-content-between align-items-center shadow-sm">
                        <span class="fw-bold text-secondary text-uppercase tracking-wide small mb-0">İşbank (A) Havuz Ana Para:</span>
                        <!-- Değer doğrudan Controller'daki faizsiz anaparadan beslenir -->
                        <span id="modal-tasarruf-bakiye" class="fw-bold font-monospace fs-5" style="color: #6a1b9a;">
                            <?= number_format($canli_ana_yatirim_bakiye_tl, 2, ',', '.') ?> ₺
                        </span>
                    </div>

                    <!-- 2. Varlık Seçim Alanı -->
                    <div class="mb-3 text-start">
                        <label for="modal_doviz_cinsi" class="form-label fw-bold text-secondary small text-uppercase mb-1">Döviz / Varlık Cinsi</label>
                        <select class="form-select form-select-md fw-semibold text-dark" name="modal_doviz_cinsi" id="modal_doviz_cinsi" required>
                            <option value="" disabled selected>Varlık Seçiniz...</option>
                            <option value="USD">USD (Amerikan Doları)</option>
                            <option value="EUR">EUR (Euro Bölgesi)</option>
                            <option value="XAU">XAU (Has Altın Gram)</option>
                        </select>
                    </div>

                    <!-- 3. Gösterge Kartları (Talep Ettiğiniz Temiz Beyaz Tasarım) -->
                    <div class="row g-2 mb-3">
                        <!-- Sol Beyaz Kart: İşlem Kuru -->
                        <div class="col-6">
                            <div class="p-3 bg-white rounded-3 border text-center shadow-sm">
                                <span id="modal_kur_turu_label" class="d-block text-secondary small fw-bold text-uppercase mb-1" style="font-size: 10px; letter-spacing: 0.3px;">Canlı Banka Alış</span>
                                <h5 id="modal_canli_kur_text" class="fw-bold text-dark font-monospace mb-0" style="font-size: 15px;">0,0000 ₺</h5>
                            </div>
                        </div>
                        <!-- Sağ Beyaz Kart: Maksimum Kapasite -->
                        <div class="col-6">
                            <div class="p-3 bg-white rounded-3 border text-center shadow-sm">
                                <span class="d-block text-secondary small fw-bold text-uppercase mb-1" style="font-size: 10px; letter-spacing: 0.3px;">Maks. Alınabilir</span>
                                <h5 id="modal_maks_kapasite_text" class="fw-bold text-success font-monospace mb-0" style="font-size: 15px;">0,00</h5>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Manuel Döviz Kuru Giriş Alanı ( Default Kilitli ) -->
                    <div class="mb-3 text-start">
                        <label for="modal_manuel_kur" class="form-label fw-bold text-secondary small text-uppercase mb-1">Manuel İşlem Kuru (Harici Giriş)</label>
                        <input type="number" class="form-control font-monospace fw-bold text-end" name="modal_manuel_kur" id="modal_manuel_kur" step="0.0001" min="0" placeholder="0.0000 (Canlı kuru ezmek için yazın)" disabled>
                    </div>

                    <!-- 5. Satın Alınacak Miktar Alanı ( Default Kilitli ) -->
                    <div class="mb-3 text-start">
                        <label for="modal_miktar" class="form-label fw-bold text-secondary small text-uppercase mb-1">Satın Alınacak Miktar</label>
                        <input type="number" class="form-control form-control-lg font-monospace fw-bold text-end" name="modal_miktar" id="modal_miktar" step="0.01" min="0.01" placeholder="0.00" required disabled>
                    </div>

                    <!-- 6. Hesaplanan Sonuç Çıktı Alanı ve Binde 2 Vergi Rozeti -->
                    <div class="mb-1 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="modal_toplam_tutar_tl" class="form-label fw-bold text-secondary small text-uppercase mb-0">Toplam Tahmini Maliyet</label>
                            <!-- Vergi yükünü anlık simüle eden kurumsal rozet -->
                            <span id="modal_vergi_bilgi_rozet" class="badge bg-secondary bg-opacity-10 text-secondary fw-bold font-monospace" style="font-size: 11px; padding: 4px 6px;">Vergi: 0,00 ₺</span>
                        </div>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace fw-bold bg-light text-end pe-3 fs-5 text-dark" id="modal_toplam_tutar_tl" readonly value="0,00 ₺">
                        </div>
                        <!-- Emniyet Bariyeri Uyarı Mesajı -->
                        <div id="bakiye_uyari_metni" class="text-danger small fw-bold mt-1 d-none text-start">
                            ⚠️ Havuz Ana Para Bakiyesi Yetersiz! Lütfen miktarı veya kuru düşürün.
                        </div>
                    </div>
                    
                </div>
                
                <!-- Modal Aksiyon Butonları -->
                <div class="modal-footer bg-light border-top-0 py-3" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button type="button" class="btn btn-secondary px-4 fw-bold small text-uppercase shadow-sm" data-bs-dismiss="modal" style="font-size: 12px; letter-spacing: 0.5px;">İptal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold small text-uppercase shadow-sm" id="btn_modal_satinal" disabled style="font-size: 12px; letter-spacing: 0.5px; background-color: #6a1b9a; border-color: #6a1b9a;">
                        Satın Alma İşlemini Tamamla
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>















<script>

// =================================================================================
// 🎯 JAVASCRIPT MOTORU - CONFIG & SABİT LİMİT TANIMLAMALARI
// =================================================================================
const ISBANK_KK_SABIT_LIMIT = 321100.00;
const VAKIFBANK_KK_SABIT_LIMIT = 263500.00;

// 🚀 global hafıza alanında aktif kaynak havuzu türünü tutalım
window.canliAktifKaynakHavuz = '';

// =================================================================================
// 🎯 JAVASCRIPT MOTORU - BLOK 20: LİKİDİTE MERKEZİ VE ÖN TAKI SÜZGECİ (REFORM)
// =================================================================================

function anlikHavuzDurumlariniHesapla() {
    const sayiyaCevir = (id) => {
        const el = document.getElementById(id);
        if (!el) return 0;
        let text = el.innerText || el.textContent || "";
        let temiz = text.replace(/[^0-9,.-]/g, "").replace(/\./g, "").replace(",", ".");
        return parseFloat(temiz) || 0;
    };

    // Bloke bütçelerinin input alanlarından anlık okunması
    const suBlokesiMiktari = parseFloat(document.getElementById('js_su_bloke_miktari')?.value) || 0;
    const faturaBlokesiMiktari = parseFloat(document.getElementById('js_fatura_bloke_miktari')?.value) || 0;

    // Ekrandaki kutulardan ham canlı durumların çekilmesi
    const isbankAHam = sayiyaCevir('isbank_k_kutu_bakiye');       
    const vakifHam   = sayiyaCevir('vakif_kutu_bakiye');          
    const nakitHam   = sayiyaCevir('nakit_kutu_bakiye');          
    const isbankOHam = sayiyaCevir('isbank_o_kutu_bakiye');       

    // 🎯 REFORM: Arayüzdeki net cüzdan kullanılabilirlik sınırlarının PHP ile tam senkronizasyonu
    return {
        isbank_k: { ham: isbankAHam, net: isbankAHam, ad: 'İşbankası (A) [Yatırım Ana Havuz]' },
        vakif:    { ham: vakifHam,   net: Math.max(0, vakifHam - faturaBlokesiMiktari), ad: 'Vakıfbank (Ö) [Fatura Havuzu]' },
        nakit:    { ham: nakitHam,   net: Math.max(0, nakitHam - suBlokesiMiktari), ad: 'Nakit (Ev) [Su & Aidat Havuzu]' },
        isbank_o: { ham: isbankOHam, net: isbankOHam, ad: 'İş Bankası (Ö) [Kredi Bütçesi]' }
    };
}

function giderOdeKontrolEt(btnElement, kalemAdi, tutar, islemTuru, id = 0) {
    const havuzlar = anlikHavuzDurumlariniHesapla();
    const temizIsim = kalemAdi.trim().toLowerCase();
    
    // Varsayılan ana havuz bariyeri
    let hedefHavuzTuru = 'isbank_k'; 

    // 🎯 REFORM: PHP süzgeciyle birebir örtüşen ön takı (Prefix) ve harf tarama algoritması
    if (temizIsim.startsWith('n ') || temizIsim === 'n' || temizIsim.startsWith('n-') || temizIsim.startsWith('n_')) {
        hedefHavuzTuru = 'nakit';
    } 
    else if (temizIsim.startsWith('v ') || temizIsim === 'v' || temizIsim.startsWith('v-') || temizIsim.startsWith('v_')) {
        hedefHavuzTuru = 'vakif';
    } 
    else if (temizIsim.startsWith('y ') || temizIsim === 'y' || temizIsim.startsWith('y-') || temizIsim.startsWith('y_')) {
        hedefHavuzTuru = 'isbank_k';
    } 
    // Sabit fatura isimlerinin taranarak doğrudan Vakıfbank cüzdanına yönlendirilmesi
    else if (['faturası', 'izsu', 'elektrik', 'su', 'doğalgaz', 'telefon', 'internet'].some(sk => temizIsim.includes(sk))) {
        hedefHavuzTuru = 'vakif';
    }
    else if (temizIsim.includes('kredi') || temizIsim.includes('faiz')) {
        hedefHavuzTuru = 'isbank_o';
    }

    const seciliHavuz = havuzlar[hedefHavuzTuru];

    const isFaturaGideri = ['faturası', 'izsu', 'elektrik', 'su', 'doğalgaz', 'telefon', 'internet'].some(sk => temizIsim.includes(sk));
    const isSuAidatGideri = (temizIsim === 'aidat' || temizIsim.includes('damacana') || temizIsim.includes('içme suyu'));

    // 🎯 KESİN REFORM: KULLANILABİLİR GERÇEK LİKİDİTE LİMİTİ
    // Eğer harcama zaten bloke edilmiş bir fatura veya su/aidat ise, hesabın limitini net bakiye (0) değil, 
    // hesaptaki toplam brüt para (net + bloke) seviyesine eşitliyoruz. Böylece engel tamamen kalkar!
    let harcanabilirLimit = seciliHavuz.net;
    if (isFaturaGideri && hedefHavuzTuru === 'vakif') {
        harcanabilirLimit = seciliHavuz.net + seciliHavuz.bloke; // 0 + 1337 = 1337 ₺
    } else if (isSuAidatGideri && hedefHavuzTuru === 'nakit') {
        harcanabilirLimit = seciliHavuz.net + seciliHavuz.bloke;
    }

    // 🎯 ÇAPRAZ POP-UP TRANSFER TETİKLEYİCİSİ (YENİ SARMAL)
    // Kontrol artık kilitlenen "net" değişkenine değil, akıllı "harcanabilirLimit" subabına bakıyor
    if (tutar > harcanabilirLimit) {
        if (btnElement) {
            btnElement.innerHTML = btnElement.getAttribute('data-type') === 'maas_eft' ? 'EFT' : 'ÖDE';
            btnElement.disabled = false;
        }
        // Eksik bakiyeyi tamamlamak için akıllı pop-up motoruna pasla
        transferTalebiOlustur(hedefHavuzTuru, tutar, id, islemTuru);
        return;
    }

    // 🚀 Bakiye veya bloke yeterliyse harcamayı doğrudan kilitli asıl ödeme motoruna gönder (Pop-up açılmaz!)
    asılOdemeyiBitir(id, islemTuru, btnElement, tutar);
}

// =================================================================================
// 🎯 JAVASCRIPT MOTORU - BLOK 21: GİDER YETERSİZ BAKİYE ÇAPRAZ POP-UP TRANSFER MOTORU
// =================================================================================

function transferTalebiOlustur(hedefTur, toplamTutar, asilIslemId, asilIslemTuru) {
    const havuzlar = anlikHavuzDurumlariniHesapla();
    const seciliHavuz = havuzlar[hedefTur];
    
    const suBlokesiMiktari = parseFloat(document.getElementById('js_su_bloke_miktari')?.value) || 0;
    const faturaBlokesiMiktari = parseFloat(document.getElementById('js_fatura_bloke_miktari')?.value) || 0;
    
    const mevcutBloke = hedefTur === 'nakit' ? suBlokesiMiktari : (hedefTur === 'vakif' ? faturaBlokesiMiktari : 0);
    const havuzdakiToplamPara = seciliHavuz.ham; 

    const eksikTutar = (toplamTutar + mevcutBloke) - havuzdakiToplamPara;

    const modalEl = document.getElementById('bayeTransferModal');
    const modalObj = new bootstrap.Modal(modalEl);

    const msgEl = document.getElementById('modalMessage');
    const altAlan = document.getElementById('alternatifHavuzlarAlani');
    const tutarInput = document.getElementById('transferTutarInput');
    const eftBtn = document.getElementById('modalEftBtn');



    if (tutarInput) tutarInput.value = eksikTutar.toFixed(2);
    
    if (msgEl) {
        msgEl.innerHTML = `<span class="text-danger fw-bold">${seciliHavuz.ad}</span> hesabında <span class="fw-bold font-monospace">${havuzdakiToplamPara.toFixed(2)}₺ </span> bakiyeniz bulunmaktadır. 
        Fakat <span class="fw-bold font-monospace text-warning-emphasis">${mevcutBloke.toFixed(2)}₺ </span> kısmı bloke edilmiştir.
        <span class="fw-bold font-monospace">${toplamTutar.toFixed(2)}₺'lik </span> harcamanızı ödemek için 
        <span class="text-danger fw-bold font-monospace">${eksikTutar.toFixed(2)}₺ </span> bakiyeyi hangi hesaptan aktaralım?<br><br>`;
    }

    let alternatifler = [];
    if (hedefTur === 'nakit') {
        alternatifler = [{ key: 'vakif', data: havuzlar.vakif }, { key: 'isbank_k', data: havuzlar.isbank_k }];
    } else if (hedefTur === 'vakif') {
        alternatifler = [{ key: 'nakit', data: havuzlar.nakit }, { key: 'isbank_k', data: havuzlar.isbank_k }];
    } else if (hedefTur === 'isbank_k') {
        alternatifler = [{ key: 'nakit', data: havuzlar.nakit }, { key: 'vakif', data: havuzlar.vakif }];
    }

    if (altAlan) {
        altAlan.innerHTML = '';
        alternatifler.forEach((alt, idx) => {
            const formatliBakiye = new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(alt.data.net);
            const checkedAttr = idx === 0 ? 'checked' : '';
            
            const itemHtml = `
            <label class="d-flex align-items-center justify-content-between p-2 border border-light-subtle rounded bg-light" style="cursor:pointer; margin-bottom: 0; margin-top:4px;">
                <div class="d-flex align-items-center">
                    <input type="radio" name="kaynakHavuzSecimi" value="${alt.key}" class="form-check-input me-2" ${checkedAttr}>
                    <span class="fw-semibold text-dark" style="font-size:11px;">${alt.data.ad}</span>
                </div>
                <span class="badge bg-secondary font-monospace" style="font-size:10px;">Net Bakiye: ${formatliBakiye}</span>
            </label>`;
            altAlan.insertAdjacentHTML('beforeend', itemHtml);
        });
    }

    const yeniEftBtn = eftBtn.cloneNode(true);
    eftBtn.parentNode.replaceChild(yeniEftBtn, eftBtn);

    yeniEftBtn.addEventListener('click', function () {
        const seciliKaynak = document.querySelector('input[name="kaynakHavuzSecimi"]:checked')?.value;
        const transferTutar = parseFloat(tutarInput ? tutarInput.value : 0) || 0;

        const tHavuzlar = anlikHavuzDurumlariniHesapla();
        const kaynakHavuzVerisi = tHavuzlar[seciliKaynak];
        if (transferTutar > kaynakHavuzVerisi.net) {
            alert(`⚠️ Bakiyeniz yetersiz! Seçtiğiniz kaynak hesabın kullanılabilir net bakiyesi: ${kaynakHavuzVerisi.net.toFixed(2)} ₺'dir.`);
            return;
        }

        yeniEftBtn.innerHTML = "⏳ TRANSFER EDİLİYOR...";
        yeniEftBtn.disabled = true;

        const formData = new FormData();
        formData.append('islem', 'yeni_kalem_ekle');
        formData.append('kalem_adi', `Havuz Transfer Fonu (${seciliKaynak.toUpperCase()} -> ${hedefTur.toUpperCase()})`);
        formData.append('tutar', transferTutar);
        formData.append('turu', 'gelir');

        fetch('management', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(() => {
            yeniEftBtn.innerHTML = "TRANSFER ET & ÖDE";
            yeniEftBtn.disabled = false;

            modalObj.hide();
            const backDrop = document.querySelector('.modal-backdrop');
            if(backDrop) backDrop.remove();
            document.body.style.overflow = 'auto';
            
            setTimeout(() => { asılOdemeyiBitir(asilIslemId, asilIslemTuru, null, toplamTutar); }, 300);
        })
        .catch(err => {
            console.error(err);
            yeniEftBtn.innerHTML = "TRANSFER ET & ÖDE";
            yeniEftBtn.disabled = false;
        });
    });

    modalObj.show();
}

// =================================================================================
// 🎯 JAVASCRIPT MOTORU - BLOK 22: NAKİT YATIR VE PREFİX GELİR EFT POP-UP DAĞITIM MOTORU
// =================================================================================

function nakitYatirPopUpAc() {
    const inputEl = document.getElementById('evdeki-nakit-tutar');
    if (!inputEl) return;
    const tutar = parseFloat(inputEl.value) || 0;
    if (tutar <= 0) { alert('⚠️ Lütfen geçerli bir tutar giriniz.'); return; }

    const modalEl = document.getElementById('bayeTransferModal');
    if (!modalEl) return;
    
    let modalObj = bootstrap.Modal.getInstance(modalEl);
    if (!modalObj) { modalObj = new bootstrap.Modal(modalEl); }

    // const msgEl = modalEl.querySelector('#modalMessage') || modalEl.querySelector('.modal-body p');
    // const altAlan = modalEl.querySelector('#alternatifHavuzlarAlani') || modalEl.querySelector('.modal-body .alternatif-alan');
    // const tutarInput = modalEl.querySelector('#maviPopUpTutarInput');
    // const eftBtn = modalEl.querySelector('#bayeTransferOnayBtn') || modalEl.querySelector('#maviPopUpOnayBtn');
    const msgEl = modalEl.querySelector('#modalMessage') || modalEl.querySelector('.modal-body p');
    const altAlan = modalEl.querySelector('#alternatifHavuzlarAlani') || modalEl.querySelector('.modal-body .alternatif-alan');
    const tutarInput = modalEl.querySelector('#transferTutarInput');
    const eftBtn = modalEl.querySelector('#modalEftBtn') || modalEl.querySelector('#modalEftBtn');

    if (tutarInput) tutarInput.value = tutar.toFixed(2);
    if (msgEl) {
        msgEl.innerHTML = `💵 <span class="text-success fw-bold">Önceki Aydan Devreden Nakit</span><br>
        <span class="fw-bold font-monospace text-success">${tutar.toFixed(2)} ₺</span> bakiyeyi hangi hesaba aktarmak istersiniz?`;
    }

    if (altAlan) {
        altAlan.innerHTML = `
        <label class="d-flex align-items-center justify-content-between p-2 border border-light-subtle rounded bg-light" style="cursor:pointer; margin-bottom: 0;">
            <div class="d-flex align-items-center">
                <input type="radio" name="kaynakHavuzSecimi" value="nakit" class="form-check-input me-2" checked>
                <span class="fw-semibold text-dark" style="font-size:11px;">Nakit (Ev)</span>
            </div>
        </label>
        <label class="d-flex align-items-center justify-content-between p-2 border border-light-subtle rounded bg-light" style="cursor:pointer; margin-bottom: 0; margin-top:4px;">
            <div class="d-flex align-items-center">
                <input type="radio" name="kaynakHavuzSecimi" value="isbank_k" class="form-check-input me-2">
                <span class="fw-semibold text-dark" style="font-size:11px;">İşbankası (A)</span>
            </div>
        </label>
        <label class="d-flex align-items-center justify-content-between p-2 border border-light-subtle rounded bg-light" style="cursor:pointer; margin-bottom: 0; margin-top:4px;">
            <div class="d-flex align-items-center">
                <input type="radio" name="kaynakHavuzSecimi" value="vakif" class="form-check-input me-2">
                <span class="fw-semibold text-dark" style="font-size:11px;">Vakıfbank (Ö)</span>
            </div>
        </label>`;
    }

    const yeniEftBtn = eftBtn.cloneNode(true);
    eftBtn.parentNode.replaceChild(yeniEftBtn, eftBtn);

    yeniEftBtn.addEventListener('click', function () {
        const nihaiTutar = parseFloat(tutarInput ? tutarInput.value : 0) || 0;

        yeniEftBtn.innerHTML = "⏳ Kaydediliyor...";
        yeniEftBtn.disabled = true;

        const formData = new FormData();
        formData.append('islem', 'evdeki_nakit_aktar');
        formData.append('tutar', nihaiTutar);

        fetch('management', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                inputEl.value = '0.00';
                modalObj.hide();
                const backDrop = document.querySelector('.modal-backdrop');
                if(backDrop) backDrop.remove();
                window.monitorGuncelle();
            } else {
                alert('⚠️ ' + data.message);
                yeniEftBtn.innerHTML = "TRANSFER ET & ÖDE";
                yeniEftBtn.disabled = false;
            }
        }).catch(err => {
            console.error(err);
            yeniEftBtn.innerHTML = "TRANSFER ET & ÖDE";
            yeniEftBtn.disabled = false;
        });
    });

    modalObj.show();
}

function hizliPrefixGelirEft(btnElement) {
    const rowKalem = btnElement.closest('.row-kalem');
    const id = btnElement.getAttribute('data-id');
    const textInput = rowKalem ? rowKalem.querySelector('input[type="text"]') : null;
    const kalemAdi = textInput ? textInput.value : '';
    const tutarInput = rowKalem ? rowKalem.querySelector('input[type="number"]') : null;
    const tutar = tutarInput ? parseFloat(tutarInput.value) || 0 : 0;

    if (tutar <= 0 || !id) return;
    btnElement.innerHTML = "⏳";
    btnElement.disabled = true;

    asılOdemeyiBitir(id, 'maas_eft', btnElement, tutar);
}








/* ========================================================================= 
   %100 GÜVENLİ VE ELEMENT KONTROLLÜ FİNANSAL SCRIPT MOTORU
   ========================================================================= */

const liveKurlar = {
    'USD': <?= (float)($live_kurlar['USD'] ?? 46.8215) ?>,
    'EUR': <?= (float)($live_kurlar['EUR'] ?? 53.8685) ?>,
    'XAU': <?= (float)($live_kurlar['XAU'] ?? 6086.4200) ?>
};

const netTasarrufBakiye = <?= (float)($canli_ana_yatirim_bakiye_tl ?? 28470.39) ?>;

// Tetikleyici Fonksiyon (Pop-up açıldığında elemanları sıfırlar)
function forexAction(type) {
    if (type === 'isbank_k') {
        const form = document.getElementById('forexSatinalForm');
        if (form) { form.reset(); }
        
        // Elementlerin varlığı tek tek kontrol edilerek hata fırlatması engelleniyor
        const elManuelKur = document.getElementById('modal_manuel_kur');
        const elMiktar = document.getElementById('modal_miktar');
        const elBtnSatinal = document.getElementById('btn_modal_satinal');
        const elCanliKurText = document.getElementById('modal_canli_kur_text');
        const elMaksKapasiteText = document.getElementById('modal_maks_kapasite_text');
        const elToplamTutarTl = document.getElementById('modal_toplam_tutar_tl');
        const elVergiRozet = document.getElementById('modal_vergi_bilgi_rozet');
        const elUyari = document.getElementById('bakiye_uyari_metni');

        if (elManuelKur) elManuelKur.disabled = true;
        if (elMiktar) elMiktar.disabled = true;
        if (elBtnSatinal) elBtnSatinal.disabled = true;
        
        if (elCanliKurText) elCanliKurText.textContent = "0,0000 ₺";
        if (elMaksKapasiteText) elMaksKapasiteText.textContent = "0,00";
        if (elToplamTutarTl) elToplamTutarTl.value = "0,00 ₺";
        if (elVergiRozet) elVergiRozet.textContent = "Vergi: 0,00 ₺";
        if (elUyari) elUyari.classList.add('d-none');
        
        var myModal = new bootstrap.Modal(document.getElementById('forexYatirimModal'));
        myModal.show();
    }
}

// Finansal Matematik ve Çarpan İstasyonu
function hesaplaYatirimMatematigi() {
    const elDovizCinsi = document.getElementById('modal_doviz_cinsi');
    if (!elDovizCinsi) return;
    
    const secilenVarlik = elDovizCinsi.value;
    if (!secilenVarlik) return;

    const safCanliKur = liveKurlar[secilenVarlik] || 0;
    
    const elManuelKurInput = document.getElementById('modal_manuel_kur');
    const manuelKur = elManuelKurInput ? (parseFloat(elManuelKurInput.value) || 0) : 0;
    
    const aktifIslemKuru = (manuelKur > 0) ? manuelKur : safCanliKur;
    const basamak = (secilenVarlik === 'XAU') ? 2 : 4;

    const elKurTuruLabel = document.getElementById('modal_kur_turu_label');
    const elCanliKurText = document.getElementById('modal_canli_kur_text');

    if (manuelKur > 0) {
        if (elKurTuruLabel) elKurTuruLabel.textContent = "Manuel İşlem Kuru";
        if (elCanliKurText) elCanliKurText.textContent = aktifIslemKuru.toLocaleString('tr-TR', { minimumFractionDigits: basamak }) + " ₺";
    } else {
        if (elKurTuruLabel) elKurTuruLabel.textContent = "Canlı Banka Alış";
        if (elCanliKurText) elCanliKurText.textContent = safCanliKur.toLocaleString('tr-TR', { minimumFractionDigits: basamak }) + " ₺";
    }

    if (aktifIslemKuru > 0) {
        const maksKapasite = netTasarrufBakiye / aktifIslemKuru;
        const elMaksKapasiteText = document.getElementById('modal_maks_kapasite_text');
        if (elMaksKapasiteText) elMaksKapasiteText.textContent = maksKapasite.toLocaleString('tr-TR', { minimumFractionDigits: 2 });
    }

    const elMiktarInput = document.getElementById('modal_miktar');
    const miktar = elMiktarInput ? (parseFloat(elMiktarInput.value) || 0) : 0;
    
    if (miktar > 0 && aktifIslemKuru > 0) {
        let netTutarTl = miktar * aktifIslemKuru;
        let vergiYuku = netTutarTl * 0.0020; // Tüm varlıklarda binde 2 sabit vergi maliyeti
        let toplamMaliyetTl = netTutarTl + vergiYuku;

        const elVergiRozet = document.getElementById('modal_vergi_bilgi_rozet');
        const elToplamTutarTl = document.getElementById('modal_toplam_tutar_tl');
        
        if (elVergiRozet) elVergiRozet.textContent = "Vergi: " + vergiYuku.toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + " ₺";
        if (elToplamTutarTl) elToplamTutarTl.value = TumMaliyetFormat(toplamMaliyetTl);

        const uyariMetni = document.getElementById('bakiye_uyari_metni');
        const submitButon = document.getElementById('btn_modal_satinal');
        
        if (toplamMaliyetTl > netTasarrufBakiye) {
            if (uyariMetni) uyariMetni.classList.remove('d-none');
            if (submitButon) submitButon.disabled = true;
        } else {
            if (uyariMetni) uyariMetni.classList.add('d-none');
            if (submitButon) submitButon.disabled = false;
        }
    } else {
        const elToplamTutarTl = document.getElementById('modal_toplam_tutar_tl');
        const elVergiRozet = document.getElementById('modal_vergi_bilgi_rozet');
        const submitButon = document.getElementById('btn_modal_satinal');
        
        if (elToplamTutarTl) elToplamTutarTl.value = "0,00 ₺";
        if (elVergiRozet) elVergiRozet.textContent = "Vergi: 0,00 ₺";
        if (submitButon) submitButon.disabled = true;
    }
}

function TumMaliyetFormat(val) {
    return val.toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + " ₺";
}

// Güvenli Event Listener Bağlantıları
document.addEventListener("DOMContentLoaded", function() {
    const elDovizCinsi = document.getElementById('modal_doviz_cinsi');
    const elManuelKur = document.getElementById('modal_manuel_kur');
    const elMiktar = document.getElementById('modal_miktar');

    if (elDovizCinsi) {
        elDovizCinsi.addEventListener('change', function() {
            if (this.value) {
                if (elManuelKur) elManuelKur.disabled = false;
                if (elMiktar) elMiktar.disabled = false;
                hesaplaYatirimMatematigi();
            }
        });
    }

    if (elManuelKur) elManuelKur.addEventListener('input', hesaplaYatirimMatematigi);
    if (elMiktar) elMiktar.addEventListener('input', hesaplaYatirimMatematigi);
});













// =================================================================================
// 🎯 JAVASCRIPT MOTORU - BLOK 23: ASIL ÖDEME VE YARDIMCI CRUD MOTORLARI
// =================================================================================

function asılOdemeyiBitir(id, islemTuru, btn = null, tutar = 0) {
    if (!btn && id > 0) {
        btn = document.querySelector(`.eft-btn[data-id="${id}"], .ode-btn[data-id="${id}"], .prefix-gelir-eft-btn[data-id="${id}"]`);
    }

    if (btn) {
        btn.innerHTML = "⏳";
        btn.disabled = true;
    }

    const formData = new FormData();
    formData.append('islem', islemTuru);
    if (id > 0) formData.append('id', id);
    if (tutar > 0) formData.append('tutar', tutar);

    fetch('management', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') { 
            window.monitorGuncelle(); 
        } else { 
            alert('⚠️ ' + data.message); 
            if (btn) { 
                btn.innerHTML = btn.getAttribute('data-type') === 'maas_eft' ? 'EFT' : 'ÖDE'; 
                btn.disabled = false; 
            } 
        }
    })
    .catch(err => { 
        console.error("Hata:", err); 
        if (btn) { btn.innerHTML = "ÖDE"; btn.disabled = false; } 
    });
}

function hizliKalemEkle(turu) {
    if (window.event) { window.event.preventDefault(); }
    const btn = window.event ? window.event.target : null;
    const sarmalKart = btn ? btn.closest('div, .card, .row') : null;
    
    const adiInput = sarmalKart ? (sarmalKart.querySelector(`input[id*="adi"], input[placeholder*="Ad"]`) || document.getElementById('yeni-' + turu + '-adi')) : document.getElementById('yeni-' + turu + '-adi');
    const tutarInput = sarmalKart ? (sarmalKart.querySelector(`input[id*="tutar"], input[placeholder*="0.00"]`) || document.getElementById('yeni-' + turu + '-tutar')) : document.getElementById('yeni-' + turu + '-tutar');

    if (!adiInput || !tutarInput) return;
    let kalemAdi = adiInput.value.trim();
    const tutar = parseFloat(tutarInput.value) || 0;

    if (kalemAdi === '' || tutar <= 0) { 
        alert('⚠️ Lütfen geçerli bir bütçe kalem adı ve tutar giriniz.'); 
        return; 
    }

    const formData = new FormData();
    formData.append('islem', 'yeni_kalem_ekle');
    formData.append('kalem_adi', kalemAdi);
    formData.append('tutar', tutar);
    formData.append('turu', turu);

    fetch('management', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data && data.status === 'success') {
            adiInput.value = ''; 
            tutarInput.value = '';
            window.monitorGuncelle();
        } else {
            alert('⚠️ Kalem eklenemedi.');
        }
    })
    .catch(err => console.error("Ekleme hatası:", err));
}

function tekilKalemSil(id) {
    if (!confirm('Bu bütçe kalemini silmek istediğinize emin misiniz?')) return;
    const formData = new FormData();
    formData.append('islem', 'kalem_sil');
    formData.append('id', id);

    fetch('management', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') { window.monitorGuncelle(); }
    }).catch(err => console.error("Hata:", err));
}
// =================================================================================
// 🎯 JAVASCRIPT MOTORU - BLOK 24: ÖZEL OPERASYONEL MOTORLAR VE DELEGASYONLAR
// =================================================================================

function nergizCepOde() {
    const inputEl = document.getElementById('nergiz-cep-tutar');
    if (!inputEl) return;
    const tutar = parseFloat(inputEl.value) || 0;
    if (tutar <= 0) { alert('⚠️ Lütfen geçerli bir tutar giriniz.'); return; }
    asılOdemeyiBitir(0, 'nergiz_cep_ode', window.event ? window.event.target.closest('button') : null, tutar);
}

function damacanaAksiyon(id, yon) {
    const btn = window.event ? window.event.target.closest('button') : null;
    if (btn && btn.disabled) return;
    let orijinalMetin = "";
    if (btn) { orijinalMetin = btn.innerHTML; btn.innerHTML = "⏳"; btn.disabled = true; }

    const birimFiyatInput = document.getElementById('su-birim-fiyat-' + id);
    const birimFiyat = birimFiyatInput ? parseFloat(birimFiyatInput.value) : 140;
    
    const formData = new FormData();
    formData.append('islem', 'su_damacana_aksiyon');
    formData.append('id', id);
    formData.append('birim_fiyat', birimFiyat);
    formData.append('yon', yon);
    
    fetch('management', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') { 
            window.monitorGuncelle(); 
        } else { 
            alert('⚠️ ' + data.message); 
            if (btn) { btn.innerHTML = orijinalMetin; btn.disabled = false; } 
        }
    })
    .catch(err => { 
        console.error("Hata:", err); 
        if (btn) { btn.innerHTML = orijinalMetin; btn.disabled = false; } 
    });
}


window.bayeFaizZamanlayiciBellek = null;

// 🎯 REFORM: En ufak bütçe ve faiz girdisinde Controller'ı anında besleyen ve imleç kaçırmayan motor
function bayeFaizAnaparaAdaptorAtesle(inputElement) {
    if (!inputElement) return;

    const oranInput   = document.getElementById('faiz-orani-input');
    const gunInput    = document.getElementById('faiz-gun-input');
    const butceInput  = document.getElementById('faiz-kredi-butcesi-input');
    
    if (!oranInput || !gunInput || !butceInput) return;

    // Önce arka planda beklemeksizin sadece ön yüzün matematik motorunu koştur (Anlık tepki)
    if (typeof hesaplaCiftKrediKarti === 'function') {
        hesaplaCiftKrediKarti();
    }

    // Kullanıcı hala yazmaya devam ediyorsa önceki yarım kalan isteği iptal et (Debounce)
    clearTimeout(window.bayeFaizZamanlayiciBellek);

    // Kullanıcı tuş basmayı bıraktıktan sonra 400ms bekler ve sunucuyla eşitler
    window.bayeFaizZamanlayiciBellek = setTimeout(() => {
        const formData = new FormData();
        formData.append('islem', 'faiz_guncelle');
        formData.append('faiz_orani', oranInput.value);
        formData.append('faiz_gun_sayisi', gunInput.value);
        formData.append('faiz_kredi_butcesi', butceInput.value);
        
        fetch(window.location.href, { 
            method: 'POST', 
            body: formData 
        })
        .then(res => {
            if (!res.ok) throw new Error("Controller bağlantı hatası.");
            return res.json();
        })
        .then(data => {
            if (data && data.status === 'success') {
                const aktifOdakId = document.activeElement ? document.activeElement.id : null;
                const secimBaslangic = document.activeElement ? document.activeElement.selectionStart : null;
                const secimBitis = document.activeElement ? document.activeElement.selectionEnd : null;

                // 🎯 KESİN ÇÖZÜM: Sunucudaki güncel bileşik anaparayı ekrandaki inputa zorla işle
                if (data.dinamik_anapara && butceInput && document.activeElement !== butceInput) {
                    butceInput.value = parseFloat(data.dinamik_anapara).toFixed(2);
                }

                if (typeof window.monitorGuncelle === 'function') {
                    window.monitorGuncelle();
                }

                // Re-render sonrası klavye odağını koru
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
                }, 50);
            }
        })
        .catch(err => console.error("⚠️ Adaptör İletim Hatası:", err));
    }, 400); 
}

// Global dinleyici tetikleyicisi
document.addEventListener('input', function (e) {
    if (e.target && (
        e.target.id === 'faiz-orani-input' || 
        e.target.id === 'faiz-gun-input' || 
        e.target.id === 'faiz-kredi-butcesi-input'
    )) {
        bayeFaizAnaparaAdaptorAtesle(e.target);
    }
});


// =================================================================================
// 🎯 JAVASCRIPT MOTORU - BLOK 26: TIKLAMALI MANUEL HAVUZ TRANSFER EMİR İSTASYONU
// =================================================================================

function manuelHavuzTransferAksiyonu(kaynakHavuzTuru) {
    window.canliAktifKaynakHavuz = kaynakHavuzTuru;
    const evkredisiTutar = 53315.33;

    const suBlokesiMiktari = parseFloat(document.getElementById('js_su_bloke_miktari')?.value) || 0;
    const faturaBlokesiMiktari = parseFloat(document.getElementById('js_fatura_bloke_miktari')?.value) || 0;
    
    const havuzlar = anlikHavuzDurumlariniHesapla();
    const seciliKaynakHavuz = havuzlar[kaynakHavuzTuru];

    const modalEl = document.getElementById('manuelHavuzIstasyonModal');
    let modalObj = bootstrap.Modal.getInstance(modalEl);
    if (!modalObj) { modalObj = new bootstrap.Modal(modalEl); }

    const msgEl = document.getElementById('istasyonModalMessage');
    const altAlan = document.getElementById('istasyonHedefHavuzlarAlani');
    const tutarInput = document.getElementById('istasyonTransferTutarInput');
    const eftBtn = document.getElementById('istasyonModalEftBtn');

    if (eftBtn) {
        eftBtn.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> TRANSFERİ ONAYLA`;
        eftBtn.disabled = false;
    }

    let defaultTutar = 0;
    let hedefHavuzlarHtml = '';

    const aktarilabilirKrediFazlaligi = Math.max(0, havuzlar.isbank_o.ham - evkredisiTutar);

    if (kaynakHavuzTuru === 'isbank_k') {
        defaultTutar = faturaBlokesiMiktari > 0 ? faturaBlokesiMiktari : suBlokesiMiktari;
        
        if (msgEl) {
            msgEl.innerHTML = `💼 <span class="text-primary fw-bold">${seciliKaynakHavuz.ad}</span> hesabınızdan diğer finansal havuzlarınıza manuel fon aktarımı başlatıldı.<br>
            Mevcut Canlı Bakiye: <span class="fw-bold font-monospace text-primary">${seciliKaynakHavuz.ham.toFixed(2)} ₺</span>`;
        }

        hedefHavuzlarHtml = `
        <label class="d-flex align-items-center justify-content-between p-2 border border-light-subtle rounded bg-light" style="cursor:pointer; margin-bottom: 0;">
            <div class="d-flex align-items-center">
                <input type="radio" name="hedefHavuzSecimi" value="vakif" class="form-check-input me-2" checked onchange="document.getElementById('istasyonTransferTutarInput').value = '${faturaBlokesiMiktari.toFixed(2)}'">
                <span class="fw-semibold text-dark" style="font-size:11px;">Vakıfbank (Ö) [Fatura Havuzu]</span>
            </div>
            <span class="badge bg-warning text-dark font-monospace" style="font-size:10px;">Bloke: ${faturaBlokesiMiktari.toFixed(2)} ₺</span>
        </label>
        <label class="d-flex align-items-center justify-content-between p-2 border border-light-subtle rounded bg-light" style="cursor:pointer; margin-bottom: 0; margin-top:4px;">
            <div class="d-flex align-items-center">
                <input type="radio" name="hedefHavuzSecimi" value="nakit" class="form-check-input me-2" onchange="document.getElementById('istasyonTransferTutarInput').value = '${suBlokesiMiktari.toFixed(2)}'">
                <span class="fw-semibold text-dark" style="font-size:11px;">Nakit (Ev) [Su & Aidat Havuzu]</span>
            </div>
            <span class="badge bg-success font-monospace" style="font-size:10px;">Bloke: ${suBlokesiMiktari.toFixed(2)} ₺</span>
        </label>`;
    } 
    else if (kaynakHavuzTuru === 'isbank_o') {
        defaultTutar = aktarilabilirKrediFazlaligi;
        
        if (msgEl) {
            msgEl.innerHTML = `🏦 <span class="text-secondary fw-bold">${seciliKaynakHavuz.ad}</span> içindeki nemalanmış fazla tutarlar transfer ediliyor.<br>
            Sabit Kredi Blokesi: <span class="fw-bold text-danger">${evkredisiTutar.toLocaleString('tr-TR', {minimumFractionDigits: 2})} ₺</span> | Aktarılabilir Fazlalık: <span class="fw-bold font-monospace text-success">${defaultTutar.toFixed(2)} ₺</span>`;
        }

        hedefHavuzlarHtml = `
        <label class="d-flex align-items-center justify-content-between p-2 border border-light-subtle rounded bg-light" style="cursor:pointer; margin-bottom: 0;">
            <div class="d-flex align-items-center">
                <input type="radio" name="hedefHavuzSecimi" value="isbank_k" class="form-check-input me-2" checked onchange="document.getElementById('istasyonTransferTutarInput').value = '${aktarilabilirKrediFazlaligi.toFixed(2)}'">
                <span class="fw-semibold text-dark" style="font-size:11px;">İşbankası (A) [Yatırım Ana Havuz]</span>
            </div>
        </label>
        <label class="d-flex align-items-center justify-content-between p-2 border border-light-subtle rounded bg-light" style="cursor:pointer; margin-bottom: 0; margin-top:4px;">
            <div class="d-flex align-items-center">
                <input type="radio" name="hedefHavuzSecimi" value="vakif" class="form-check-input me-2" onchange="document.getElementById('istasyonTransferTutarInput').value = '${aktarilabilirKrediFazlaligi.toFixed(2)}'">
                <span class="fw-semibold text-dark" style="font-size:11px;">Vakıfbank (Ö) [Fatura Havuzu]</span>
            </div>
            <span class="badge bg-warning text-dark font-monospace" style="font-size:10px;">Bloke: ${faturaBlokesiMiktari.toFixed(2)} ₺</span>
        </label>
        <label class="d-flex align-items-center justify-content-between p-2 border border-light-subtle rounded bg-light" style="cursor:pointer; margin-bottom: 0; margin-top:4px;">
            <div class="d-flex align-items-center">
                <input type="radio" name="hedefHavuzSecimi" value="nakit" class="form-check-input me-2" onchange="document.getElementById('istasyonTransferTutarInput').value = '${aktarilabilirKrediFazlaligi.toFixed(2)}'">
                <span class="fw-semibold text-dark" style="font-size:11px;">Nakit (Ev) [Su & Aidat Havuzu]</span>
            </div>
            <span class="badge bg-success font-monospace" style="font-size:10px;">Bloke: ${suBlokesiMiktari.toFixed(2)} ₺</span>
        </label>`;
    }

    if (tutarInput) tutarInput.value = defaultTutar.toFixed(2);
    if (altAlan) altAlan.innerHTML = hedefHavuzlarHtml;
    modalObj.show();
}

function kesinTransferEmriniAtesle() {
    const eftBtn = document.getElementById('istasyonModalEftBtn');
    const tutarInput = document.getElementById('istasyonTransferTutarInput');
    const seciliHedef = document.querySelector('input[name="hedefHavuzSecimi"]:checked')?.value;
    const nihaiTutar = parseFloat(tutarInput ? tutarInput.value : 0) || 0;

    if (nihaiTutar <= 0) { alert('⚠️ Lütfen geçerli bir transfer tutarı giriniz.'); return; }

    if (eftBtn) {
        eftBtn.innerHTML = "⏳ TRANSFER İŞLENİYOR...";
        eftBtn.disabled = true;
    }

    const formData = new FormData();
    formData.append('islem', 'yeni_kalem_ekle');
    formData.append('kalem_adi', `Havuz Transfer Fonu (${window.canliAktifKaynakHavuz.toUpperCase()} -> ${seciliHedef.toUpperCase()})`);
    formData.append('tutar', nihaiTutar);
    formData.append('turu', 'gelir');

    fetch('management', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(() => {
        const modalEl = document.getElementById('manuelHavuzIstasyonModal');
        const modalObj = bootstrap.Modal.getInstance(modalEl);
        if (modalObj) { modalObj.hide(); }

        const backDrop = document.querySelector('.modal-backdrop');
        if(backDrop) backDrop.remove();
        document.body.style.overflow = 'auto';

        window.monitorGuncelle();
    })
    .catch(err => {
        console.error(err);
        if (eftBtn) {
            eftBtn.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> TRANSFERİ ONAYLA`;
            eftBtn.disabled = false;
        }
    });
}


// =================================================================================
// 🎯 JAVASCRIPT MOTORU - BLOK 27: MAVİ OK BUTONU VAKIFBANK TRANSFER MOTORU
// =================================================================================

function maviButonPopUpAc() {
    const modalEl = document.getElementById('maviButonVakifModal');
    if (!modalEl) return;

    const inputField = modalEl.querySelector('#maviPopUpTutarInput');
    if (inputField) { inputField.value = ""; }

    let modalObj = bootstrap.Modal.getInstance(modalEl);
    if (!modalObj) { modalObj = new bootstrap.Modal(modalEl); }
    modalObj.show();
}

function kesinVakifAktariminiAtesle() {
    const modalEl = document.getElementById('maviButonVakifModal');
    if (!modalEl) return;

    const btn = modalEl.querySelector('#maviPopUpOnayBtn');
    const tutarInput = modalEl.querySelector('#maviPopUpTutarInput');
    const girilenTutar = parseFloat(tutarInput ? tutarInput.value : 0) || 0;

    if (girilenTutar <= 0) { 
        alert('⚠️ Lütfen geçerli bir transfer tutarı giriniz.'); 
        return; 
    }

    if (btn) {
        btn.innerHTML = "⏳ TRANSFER EDİLİYOR...";
        btn.disabled = true;
    }

    const formData = new FormData();
    formData.append('islem', 'vakif_nakit_aktar');
    formData.append('tutar', girilenTutar);

    fetch('management', { method: 'POST', body: formData })
    .then(res => {
        if (!res.ok) throw new Error("Yerel sunucu ağ hatası verdi. HTTP: " + res.status);
        return res.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                const modalObj = bootstrap.Modal.getInstance(modalEl);
                if (modalObj) { modalObj.hide(); }

                const backDrop = document.querySelector('.modal-backdrop');
                if (backDrop) backDrop.remove();
                document.body.style.overflow = 'auto';

                if (tutarInput) tutarInput.value = "";

                if (typeof window.monitorGuncelle === 'function') { 
                    window.monitorGuncelle(); 
                } else {
                    window.location.reload();
                }
            } else {
                alert('⚠️ ' + data.message);
                if (btn) {
                    btn.innerHTML = `VAKIFBANK'A AKTAR`;
                    btn.disabled = false;
                }
            }
        } catch (jsonErr) {
            console.error("PHP Çıktı Hatası:", text);
            throw new Error("PHP katmanı JSON yerine HTML döndü.");
        }
    })
    .catch(err => {
        console.error("Mutlak IP transfer hatası:", err);
        alert("⚠️ İletim Sağlanamadı!\nİnternetiniz kesik olduğundan, tarayıcınızın 'localhost' ismini çözebilmesi için sayfayı 'http://127.0.0...' adresi üzerinden açıp tekrar denemeniz gerekebilir.");
        if (btn) {
            btn.innerHTML = `VAKIFBANK'A AKTAR`;
            btn.disabled = false;
        }
    });
}
// =================================================================================
// 🎯 JAVASCRIPT MOTORU - BLOK 28: KREDİ KARTI GERÇEK ZAMANLI MATEMATİK MOTORU
// =================================================================================

function hesaplaCiftKrediKarti() {
    const paraFormatla = (rakam) => {
        return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY', minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(rakam);
    };

    const badgeElement = document.querySelector('.badge.bg-primary');
    let aktifAyIndex = 7;
    if (badgeElement) {
        let temizMetin = badgeElement.innerText.trim();
        if (temizMetin.indexOf('-') > -1) { 
            const parts = temizMetin.split('-');
            if (parts.length > 1) {
                aktifAyIndex = parseInt(parts[1]) || 7; 
            }
        }
    }

    let isbankAktifTaksit = 0;
    let vakifAktifTaksit  = 0;
    let isbankKalanTaksit = 0;
    let vakifKalanTaksit  = 0;

    const isbankTaksitInputs = document.querySelectorAll('.isbank-taksit-input');
    const vakifTaksitInputs  = document.querySelectorAll('.vakif-taksit-input');

    isbankTaksitInputs.forEach((input, index) => {
        const val = parseFloat(input.value) || 0;
        const ayIdx = index + 1;
        if (ayIdx === aktifAyIndex) {
            isbankAktifTaksit = val;
        } else if (ayIdx > aktifAyIndex) {
            isbankKalanTaksit += val;
        }
    });

    vakifTaksitInputs.forEach((input, index) => {
        const val = parseFloat(input.value) || 0;
        const ayIdx = index + 1;
        if (ayIdx === aktifAyIndex) {
            vakifAktifTaksit = val;
        } else if (ayIdx > aktifAyIndex) {
            vakifKalanTaksit += val;
        }
    });

    const isbankAktifGosterge = document.getElementById('isbank_aktif_taksit_gosterge');
    if (isbankAktifGosterge) isbankAktifGosterge.value = paraFormatla(isbankAktifTaksit);
    const vakifAktifGosterge = document.getElementById('vakif_aktif_taksit_gosterge');
    if (vakifAktifGosterge) vakifAktifGosterge.value = paraFormatla(vakifAktifTaksit);

    const isbankKalanGosterge = document.getElementById('isbank_kalan_taksitler');
    if (isbankKalanGosterge) isbankKalanGosterge.value = paraFormatla(isbankKalanTaksit);
    const vakifKalanGosterge = document.getElementById('vakif_kalan_taksitler');
    if (vakifKalanGosterge) vakifKalanGosterge.value = paraFormatla(vakifKalanTaksit);

    const isbankDonemIci  = parseFloat(document.getElementById('isbank_donem_ici')?.value) || 0;
    const isbankProvizyon = parseFloat(document.getElementById('isbank_provizyon')?.value) || 0;
    const isbankDoviz     = parseFloat(document.getElementById('isbank_doviz')?.value) || 0;
    const isbankEkstre    = parseFloat(document.getElementById('isbank_ekstre')?.value) || 0;

    const vakifDonemIci   = parseFloat(document.getElementById('vakif_donem_ici')?.value) || 0;
    const vakifProvizyon  = parseFloat(document.getElementById('vakif_provizyon')?.value) || 0;
    const vakifDoviz      = parseFloat(document.getElementById('vakif_doviz')?.value) || 0;
    const vakifEkstre     = parseFloat(document.getElementById('vakif_ekstre')?.value) || 0;

    const isbankLimit = ISBANK_KK_SABIT_LIMIT;
    const vakifLimit  = VAKIFBANK_KK_SABIT_LIMIT;

    const isbankKesinlesen = isbankAktifTaksit + isbankDonemIci + isbankProvizyon + isbankDoviz + isbankEkstre;
    const vakifKesinlesen  = vakifAktifTaksit + vakifDonemIci + vakifProvizyon + vakifDoviz + vakifEkstre;

    const isbankKesinlesenGosterge = document.getElementById('isbank_kesinlesen_donem');
    if (isbankKesinlesenGosterge) isbankKesinlesenGosterge.value = paraFormatla(isbankKesinlesen);
    const vakifKesinlesenGosterge = document.getElementById('vakif_kesinlesen_donem');
    if (vakifKesinlesenGosterge) vakifKesinlesenGosterge.value = paraFormatla(vakifKesinlesen);

    const isbankToplamBorc = isbankKesinlesen + isbankKalanTaksit;
    const vakifToplamBorc  = vakifKesinlesen + vakifKalanTaksit;

    const isbankToplamBorcGosterge = document.getElementById('isbank_toplam_borc');
    if (isbankToplamBorcGosterge) isbankToplamBorcGosterge.value = paraFormatla(isbankToplamBorc);
    const vakifToplamBorcGosterge = document.getElementById('vakif_toplam_borc');
    if (vakifToplamBorcGosterge) vakifToplamBorcGosterge.value = paraFormatla(vakifToplamBorc);

    const isbankKalanLimit = Math.max(0, isbankLimit - isbankToplamBorc);
    const vakifKalanLimit  = Math.max(0, vakifLimit - vakifToplamBorc);

    const isbankKalanLimitGosterge = document.getElementById('isbank_kalan_limit');
    if (isbankKalanLimitGosterge) isbankKalanLimitGosterge.value = paraFormatla(isbankKalanLimit);
    const vakifKalanLimitGosterge = document.getElementById('vakif_kalan_limit');
    if (vakifKalanLimitGosterge) vakifKalanLimitGosterge.value = paraFormatla(vakifKalanLimit);

    const mobilIsbankKullLimit = parseFloat(document.getElementById('mobil_isbank_kull_limit')?.value) || 0;
    const mobilVakifKullLimit  = parseFloat(document.getElementById('mobil_vakif_kull_limit')?.value) || 0;

    let hesaplananIsbankProvizyon = 0;
    if (mobilIsbankKullLimit > 0) {
        hesaplananIsbankProvizyon = Math.max(0, isbankLimit - isbankToplamBorc - isbankKalanTaksit - mobilIsbankKullLimit);
    }
    let hesaplananVakifProvizyon = 0;
    if (mobilVakifKullLimit > 0) {
        hesaplananVakifProvizyon = Math.max(0, vakifLimit - vakifToplamBorc - vakifKalanTaksit - mobilVakifKullLimit);
    }

    const isbankHesaplananGosterge = document.getElementById('isbank_hesaplanan_provizyon');
    if (isbankHesaplananGosterge) isbankHesaplananGosterge.value = paraFormatla(hesaplananIsbankProvizyon);
    const vakifHesaplananGosterge = document.getElementById('vakif_hesaplanan_provizyon');
    if (vakifHesaplananGosterge) vakifHesaplananGosterge.value = paraFormatla(hesaplananVakifProvizyon);

    const faiz_stopaj = 17.5;
    const js_faiz_orani = parseFloat(document.getElementById('faiz-orani-input')?.value) || 30;
    const js_faiz_gun   = parseInt(document.getElementById('faiz-gun-input')?.value) || 5;

    const dashboardSafSayiOku = (id) => {
        const el = document.getElementById(id);
        if (!el) return 0;
        let metin = (el.innerText || el.textContent || "0").trim();
        metin = metin.replace(/[\u200B-\u200D\uFEFF]/g, "").replace(/\s+/g, "");
        metin = metin.replace(/[^0-9,.-]/g, ""); 
        if (metin.indexOf(',') > -1) {
            metin = metin.replace(/\./g, "").replace(",", "."); 
        }
        return parseFloat(metin) || 0;
    };

    const safNakitBakiye   = dashboardSafSayiOku('nakit_kutu_bakiye');
    const safVakifBakiye   = dashboardSafSayiOku('vakif_kutu_bakiye');
    const safIsbankABakiye = dashboardSafSayiOku('isbank_k_kutu_bakiye');
    const safIsbankOBakiye = dashboardSafSayiOku('isbank_o_kutu_bakiye');
    const safIsbankAanapara = dashboardSafSayiOku('isbank_k_kutu_anapara');
    const safIsbankOanapara = dashboardSafSayiOku('isbank_o_kutu_anapara');

    const krediButonu = document.querySelector('.ode-btn[data-id][data-type="ode"]');
    const krediOdediMiCanli = krediButonu ? (krediButonu.innerHTML.includes('✓') || krediButonu.closest('.row-kalem')?.classList.contains('row-odendi')) : false;

    const safIsbankABrutFaiz = (safIsbankAanapara > 0) ? (safIsbankAanapara * (js_faiz_orani / 100) / 365) * js_faiz_gun : 0;
    const safIsbankANetFaiz = safIsbankABrutFaiz * (1 - (faiz_stopaj / 100));

    const safIsbankOBrutFaiz = (safIsbankOanapara > 0 && !krediOdediMiCanli) ? (safIsbankOanapara * (js_faiz_orani / 100) / 365) * js_faiz_gun : 0;
    const safIsbankONetFaiz = safIsbankOBrutFaiz * (1 - (faiz_stopaj / 100));

    const yeniEndeEdilecekFaiz = safIsbankANetFaiz + safIsbankONetFaiz;
    const yeniMevcutBakiye = safNakitBakiye + safVakifBakiye + safIsbankABakiye + safIsbankOBakiye;
    const yeniToplamBakiye = yeniMevcutBakiye + yeniEndeEdilecekFaiz;

    const yeniDnmIciHarcamalar = isbankDonemIci + vakifDonemIci;
    const yeniDonemAitTaksitleri = isbankAktifTaksit + vakifAktifTaksit;
    const yeniDovizBorclariToplam = isbankDoviz + vakifDoviz;

    const totalProvizyonlarMatrisi = isbankProvizyon + vakifProvizyon;
    const yeniKsnlesenEkstreler = isbankKesinlesen + vakifKesinlesen;
    const yeniKkDonemBorcu = yeniKsnlesenEkstreler + totalProvizyonlarMatrisi;
    const yeniKkKalanTaksitYuku = isbankKalanTaksit + vakifKalanTaksit;

    const faturaEl = document.getElementById('js_yeni_faturalar_bloke');
    const yeniFaturalarBloke = faturaEl ? (parseFloat(faturaEl.getAttribute('data-taban')) || 0) : 0;

    const suEl = document.getElementById('js_dashboard_gider_su');
    const yeniBlokeSuMiktari = suEl ? (parseFloat(suEl.getAttribute('data-taban')) || 0) : 0;

    const totalKkMutlakBorclari = isbankToplamBorc + vakifToplamBorc;
    const mutlakToplamBorcYukumlulugu = totalKkMutlakBorclari + yeniFaturalarBloke + yeniBlokeSuMiktari;
    const krediEl = document.getElementById('js_dashboard_gider_kredi');
    const yeniKrediBlokeTutari = 53315.33;
    const digerGiderlerGrubuToplamı = yeniFaturalarBloke + yeniBlokeSuMiktari + yeniKrediBlokeTutari;

    if (document.getElementById('canli-ana-yatirim-bakiye')) document.getElementById('canli-ana-yatirim-bakiye').innerHTML = paraFormatla(safIsbankABakiye);
    if (document.getElementById('js_yeni_toplam_bakiye')) document.getElementById('js_yeni_toplam_bakiye').innerHTML = paraFormatla(yeniToplamBakiye);
    if (document.getElementById('js_yeni_mevcut_bakiye')) document.getElementById('js_yeni_mevcut_bakiye').innerHTML = paraFormatla(yeniMevcutBakiye);
    if (document.getElementById('js_yeni_elde_edilecek_faiz')) document.getElementById('js_yeni_elde_edilecek_faiz').innerHTML = `+${paraFormatla(yeniEndeEdilecekFaiz)}`;
    if (document.getElementById('js_dashboard_kk_toplam_borclari')) document.getElementById('js_dashboard_kk_toplam_borclari').innerHTML = paraFormatla(totalKkMutlakBorclari);
    if (document.getElementById('js_dashboard_kesinlesen_ekstre')) document.getElementById('js_dashboard_kesinlesen_ekstre').innerHTML = paraFormatla(yeniKsnlesenEkstreler);
    if (document.getElementById('js_yeni_kk_borcumuz')) document.getElementById('js_yeni_kk_borcumuz').innerHTML = paraFormatla(yeniKkKalanTaksitYuku);
    if (document.getElementById('js_yeni_kk_donem_borcu')) document.getElementById('js_yeni_kk_donem_borcu').innerHTML = paraFormatla(yeniKkDonemBorcu);
    if (document.getElementById('js_dashboard_donem_ici')) document.getElementById('js_dashboard_donem_ici').innerHTML = paraFormatla(yeniDnmIciHarcamalar);
    if (document.getElementById('js_dashboard_provizyon_toplami')) document.getElementById('js_dashboard_provizyon_toplami').innerHTML = paraFormatla(totalProvizyonlarMatrisi);
    if (document.getElementById('js_dashboard_donem_taksitleri')) document.getElementById('js_dashboard_donem_taksitleri').innerHTML = paraFormatla(yeniDonemAitTaksitleri);
    if (document.getElementById('js_dashboard_doviz_borcu')) document.getElementById('js_dashboard_doviz_borcu').innerHTML = paraFormatla(yeniDovizBorclariToplam);
    if (document.getElementById('js_dashboard_mutlak_toplam_borc')) document.getElementById('js_dashboard_mutlak_toplam_borc').innerHTML = paraFormatla(mutlakToplamBorcYukumlulugu);

    if (faturaEl) faturaEl.innerHTML = paraFormatla(yeniFaturalarBloke);
    if (suEl) suEl.innerHTML = paraFormatla(yeniBlokeSuMiktari);
    if (krediEl) krediEl.innerHTML = paraFormatla(yeniKrediBlokeTutari);

    const nakitsorgu = (yeniBlokeSuMiktari - safNakitBakiye) < 0 ? 0 : (yeniBlokeSuMiktari - safNakitBakiye);

    const formula_c0 = isbankEkstre;
    const formula_c1 = vakifEkstre + (yeniBlokeSuMiktari > safNakitBakiye ? yeniBlokeSuMiktari - safNakitBakiye : 0) + (yeniFaturalarBloke > safVakifBakiye ? yeniFaturalarBloke - safVakifBakiye : 0);
    const formula_c2 = isbankEkstre + formula_c1;
    const formula_c3 = yeniBlokeSuMiktari;
    const formula_c4_tutar1 = safIsbankOBakiye;
    const formula_c4_fark   = Math.max(0, safIsbankOBakiye - 53313.33);
    const formula_c5 = vakifEkstre;
    const formula_c6 = yeniFaturalarBloke;
    const formula_c7 = nakitsorgu;
    const formula_c8 = yeniFaturalarBloke > safVakifBakiye ? yeniFaturalarBloke - safVakifBakiye : 0;

    if (document.getElementById('todo_c0_tutar'))  document.getElementById('todo_c0_tutar').innerHTML = paraFormatla(formula_c0);
    if (document.getElementById('todo_c1_tutar'))  document.getElementById('todo_c1_tutar').innerHTML = paraFormatla(formula_c1);
    if (document.getElementById('todo_c2_tutar'))  document.getElementById('todo_c2_tutar').innerHTML = paraFormatla(formula_c2);
    if (document.getElementById('todo_c3_tutar'))  document.getElementById('todo_c3_tutar').innerHTML = paraFormatla(formula_c3);
    if (document.getElementById('todo_c4_tutar1')) document.getElementById('todo_c4_tutar1').innerHTML = paraFormatla(formula_c4_tutar1);
    if (document.getElementById('todo_c4_tutar2')) document.getElementById('todo_c4_tutar2').innerHTML = paraFormatla(formula_c4_fark);
    if (document.getElementById('todo_c5_tutar'))  document.getElementById('todo_c5_tutar').innerHTML = paraFormatla(formula_c5);
    if (document.getElementById('todo_c6_tutar'))  document.getElementById('todo_c6_tutar').innerHTML = paraFormatla(formula_c6);
    if (document.getElementById('todo_c7_tutar'))  document.getElementById('todo_c7_tutar').innerHTML = paraFormatla(formula_c7);
    if (document.getElementById('todo_c8_tutar'))  document.getElementById('todo_c8_tutar').innerHTML = paraFormatla(formula_c8);

    if (typeof window.todoDurumlariniYukle === 'function') { 
        window.todoDurumlariniYukle(); 
    }
}

// =================================================================================
// 🎯 JAVASCRIPT MOTORU - BLOK 29: SAF RE-RENDER MOTORU VE ASENKRON YAPILAR
// =================================================================================

window.monitorGuncelle = function() {
    const urlParametreAyirici = window.location.href.indexOf('?') > -1 ? '&' : '?';
    const guvenliUrl = window.location.href + urlParametreAyirici + 't=' + new Date().getTime();

    fetch(guvenliUrl)
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP Hatası! Durum: ${res.status}`);
            }
            return res.text();
        })
        .then(html => {
            const parser = new DOMParser(); 
            const doc = parser.parseFromString(html, 'text/html');
            
            const seciciler = [
                '#monitor-ekrani', 
                '#taksit-formu', 
                '#cari-girdiler-formu', 
                '#gelir-butce-formu', 
                '#gider-butce-formu', 
                '.col-md-5.butcestyle', 
                '.row.g-3.mt-2.mb-4',
                '#js_su_bloke_miktari',
                '#js_fatura_bloke_miktari',
                '#nakit_kutu_bakiye',   
                '#vakif_kutu_bakiye',   
                '#isbank_k_kutu_bakiye', 
                '#isbank_o_kutu_bakiye',  
                '#todo_c0_tutar', 
                '#todo_c1_tutar', 
                '#todo_c2_tutar', 
                '#todo_c3_tutar', 
                '#todo_c4_tutar1', 
                '#todo_c4_tutar2',  
                '#todo_c5_tutar', 
                '#todo_c6_tutar' 
            ];

            seciciler.forEach(secici => {
                const yeniEl = doc.querySelector(secici); 
                const mevcutEl = document.querySelector(secici);
                if (yeniEl && mevcutEl) {
                    if (mevcutEl.tagName === 'INPUT') {
                        mevcutEl.value = yeniEl.value;
                    } else {
                        const b_isbank = document.getElementById('mobil_isbank_kull_limit')?.value;
                        const b_vakif  = document.getElementById('mobil_vakif_kull_limit')?.value;
                        
                        mevcutEl.innerHTML = '';
                        mevcutEl.innerHTML = yeniEl.innerHTML;
                        
                        if (b_isbank && document.getElementById('mobil_isbank_kull_limit')) {
                            document.getElementById('mobil_isbank_kull_limit').value = b_isbank;
                        }
                        if (b_vakif && document.getElementById('mobil_vakif_kull_limit')) {
                            document.getElementById('mobil_vakif_kull_limit').value = b_vakif;
                        }
                    }
                }
            });

            if (typeof hesaplaCiftKrediKarti === 'function') { 
                hesaplaCiftKrediKarti(); 
            }

            const k_Buton = document.querySelector('.ode-btn[data-id][data-type="ode"]');
            const k_OdediMi = k_Buton ? (k_Buton.innerHTML.includes('✓') || k_Buton.closest('.row-kalem')?.classList.contains('row-odendi')) : false;
            
            if (k_OdediMi) {
                const kutuBakiyeEl = document.getElementById('isbank_o_kutu_bakiye');
                if (kutuBakiyeEl) {
                    kutuBakiyeEl.innerHTML = "28,97 ₺"; 
                }
            }
        })
        .catch(err => {
            console.warn("⚠️ Canlı bütçe monitörü güncellenemedi (Bağlantı hatası):", err.message);
        });
}

// =================================================================================
// 🎯 JAVASCRIPT MOTORU - BLOK 30: EVENT DELEGATION VE INTERAKTIF TIKLAMA MOTORU
// =================================================================================

document.addEventListener("DOMContentLoaded", function () {
    
    // Tarayıcı ilk yüklendiğinde hafızadaki yapıldı işaretlerini kilitler
    if (typeof window.todoDurumlariniYukle === 'function') { 
        window.todoDurumlariniYukle(); 
    }
    
    // Tetikleyici ilk matematik döngüsünü başlatır
    if (typeof hesaplaCiftKrediKarti === 'function') {
        hesaplaCiftKrediKarti();
    }

    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'taksit-kaydet-btn') {
            const btn = e.target; if (btn.disabled) return;
            const orijinalMetin = btn.innerHTML; btn.innerHTML = "⏳ Oy..."; btn.disabled = true;
            const formEl = document.getElementById('taksit-formu');
            const formData = new FormData(formEl); formData.append('islem', 'taksit_kaydet');
            fetch(window.location.href, { method: 'POST', body: formData })
            .then(res => res.json()).then(data => { btn.innerHTML = orijinalMetin; btn.disabled = false; if (data && data.status === 'success') { window.monitorGuncelle(); } else { alert('⚠️ Taksitler kaydedilemedi.'); } })
            .catch(err => { console.error("Hata:", err); btn.innerHTML = orijinalMetin; btn.disabled = false; });
        }
        
        if (e.target && e.target.id === 'cari-girdiler-kaydet-btn') {
            const btn = e.target; if (btn.disabled) return;
            const orijinalMetin = btn.innerHTML; btn.innerHTML = "⏳ Oy..."; btn.disabled = true;
            const formEl = document.getElementById('cari-girdiler-formu');
            const formData = new FormData(formEl); formData.append('islem', 'cari_girdiler_kaydet');
            fetch(window.location.href, { method: 'POST', body: formData })
            .then(res => res.json()).then(data => { btn.innerHTML = orijinalMetin; btn.disabled = false; if (data && data.status === 'success') { window.monitorGuncelle(); } else { alert('⚠️ Cari girdiler kaydedilemedi.'); } })
            .catch(err => { console.error("Hata:", err); btn.innerHTML = orijinalMetin; btn.disabled = false; });
        }

        const gelirBtn = e.target.closest('#gelir-kaydet-btn');
        if (gelirBtn && !gelirBtn.disabled) {
            const orijinalMetin = gelirBtn.innerHTML; gelirBtn.innerHTML = "💾..."; gelirBtn.disabled = true;
            const formData = new FormData(document.getElementById('gelir-butce-formu')); formData.append('islem', 'butce_kaydet');
            fetch(window.location.href, { method: 'POST', body: formData }).then(() => { gelirBtn.innerHTML = orijinalMetin; gelirBtn.disabled = false; window.monitorGuncelle(); });
        }

        const giderBtn = e.target.closest('#gider-kaydet-btn');
        if (giderBtn && !giderBtn.disabled) {
            const orijinalMetin = giderBtn.innerHTML; giderBtn.innerHTML = "💾..."; giderBtn.disabled = true;
            const formData = new FormData(document.getElementById('gider-butce-formu')); formData.append('islem', 'butce_kaydet');
            fetch(window.location.href, { method: 'POST', body: formData }).then(() => { giderBtn.innerHTML = orijinalMetin; giderBtn.disabled = false; window.monitorGuncelle(); });
        }

        const btnAct = e.target.closest('.eft-btn, .ode-btn');
        if (btnAct && !btnAct.disabled && !btnAct.innerHTML.includes('✓') && !btnAct.getAttribute('onclick')?.includes('nergizCepOde')) {
            const id = btnAct.getAttribute('data-id');
            const type = btnAct.getAttribute('data-type') || 'ode';
            if (!id) return;

            btnAct.innerHTML = "⏳"; 
            btnAct.disabled = true;

            const rowKalem = btnAct.closest('.row-kalem');
            const textInput = rowKalem ? rowKalem.querySelector('input[type="text"]') : null;
            const kalemAdi = textInput ? textInput.value : '';
            const tutarInput = rowKalem ? rowKalem.querySelector('input[type="number"]') : null;
            const tutar = tutarInput ? parseFloat(tutarInput.value) || 0 : 0;

            if (type === 'maas_eft' || btnAct.classList.contains('eft-btn')) {
                asılOdemeyiBitir(id, 'maas_eft', btnAct, tutar);
            } else {
                giderOdeKontrolEt(btnAct, kalemAdi, tutar, type, id);
            }
        }

        if (e.target && e.target.id === 'faiz-aktar-btn') {
    const btn = e.target; 
    if (btn.disabled) return;
    
    if (!confirm('⚠️ FİNANSAL EMİR: Hesaplanan canlı faiz nema kazançlarını ana bakiyelerinize aktarmak ve vade gününü sıfırlamak istediğinize emin misiniz?')) {
        return;
    }

    btn.innerHTML = "⏳ FAİZLER NAKDE DÖNÜŞTÜRÜLÜYOR...";
    btn.disabled = true;

    const formData = new FormData(); 
    formData.append('islem', 'faiz_aktar');

    fetch(window.location.href, { 
        method: 'POST', 
        body: formData 
    })
    .then(() => {
        // 🎯 UNUTULMAZ RE-RENDER: PHP'den ne yanıt gelirse gelsin, metin çözmeye çalışıp patlamıyoruz.
        // Veritabanı arkada güncellendiği an, elindeki asenkron motoru zorla ateşleyip 
        // sağ sütundaki havuzları ve tüm göstergeleri milisaniyesinde ekranda yeniliyoruz!
        window.monitorGuncelle();
    })
    .catch(err => {
        console.error("Faiz iletim hatası:", err);
        // Ağda anlık dalgalanma olsa bile arayüzü kurtarmak için motoru yine de koşturuyoruz
        window.monitorGuncelle();
    });
}


  
        

        const sifirlaBtn = document.getElementById('simulasyon-sifirla-btn');
        if (sifirlaBtn) {
            // 🎯 KİLİT ÇÖZÜCÜ: Mükerrer tetiklenmeleri engellemek için eski listener'ı öldürüp temiz açıyoruz
            const yeniSifirlaBtn = sifirlaBtn.cloneNode(true);
            sifirlaBtn.parentNode.replaceChild(yeniSifirlaBtn, sifirlaBtn);

            yeniSifirlaBtn.addEventListener('click', function (e) {
                e.preventDefault();
                
                if (confirm('Tüm adımlar sıfırlanacak (Tablolar varsayılan ayarlara dönecek). Emin misiniz?')) {
                    yeniSifirlaBtn.innerHTML = "⏳ Sıfırlanıyor...";
                    yeniSifirlaBtn.disabled = true;

                    const formData = new FormData(); 
                    formData.append('islem', 'simulasyon_sifirla');
                    
                    fetch(window.location.href, { 
                        method: 'POST', 
                        body: formData 
                    })
                    .then(res => {
                        if (!res.ok) throw new Error("Sıfırlama sırasında sunucu hatası oluştu.");
                        return res.json();
                    })
                    .then(data => {
                        if (data && data.status === 'success') {
                            // 🎯 KESİN ÇÖZÜM: LocalStorage üzerindeki To-Do bayraklarını da temizleyip tam senkron uçuruyoruz
                            localStorage.clear();
                            window.location.reload();
                        } else {
                            alert('⚠️ Sıfırlama motoru başarısız oldu.');
                            yeniSifirlaBtn.innerHTML = "Simülasyonu Sıfırla";
                            yeniSifirlaBtn.disabled = false;
                        }
                    })
                    .catch(err => {
                        console.error("Sıfırlama Hatası:", err);
                        alert("⚠️ Bağlantı hatası nedeniyle sıfırlanamadı.");
                        yeniSifirlaBtn.innerHTML = "Simülasyonu Sıfırla";
                        yeniSifirlaBtn.disabled = false;
                    });
                }
            });
        }
// İstek durumunu tarayıcı hafızasında tutan mutlak kilit değişkeni
let isDonemDevrediliyor = false;

const kesinDevirOnayBtn = document.getElementById('kesin-devir-onay-btn');
if (kesinDevirOnayBtn) {
    kesinDevirOnayBtn.addEventListener('click', function(e) {
        // Formun veya butonun kendi kendine rastgele tetiklenmesini önle
        e.preventDefault();
        
        // 🔒 BARİYER 1: Eğer arka planda zaten bir istek yürütülüyorsa, sonraki basışları ANINDA çöpe at
        if (isDonemDevrediliyor) return false;
        
        isDonemDevrediliyor = true;
        const btn = kesinDevirOnayBtn;
        const oMetin = btn.innerHTML;
        
        // 🔒 BARİYER 2: Butonu anında fiziksel ve görsel olarak kilitle
        btn.innerHTML = "⏳ Dönem Kapatılıyor & Taksitler Senkron Ediliyor...";
        btn.disabled = true;
        
        // Kullanıcı devir esnasında vazgeçip modalı kapatamasın diye tüm iptal butonlarını gizle
        const closeButtons = document.querySelectorAll('#donemDevirOnayModal [data-bs-dismiss="modal"]');
        closeButtons.forEach(cBtn => cBtn.style.display = 'none');

        const formData = new FormData();
        formData.append('islem', 'donem_devret');

        // İstek doğrudan sunucuya tek bir hat üzerinden fırlatılır
        fetch(window.location.href, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data && data.status === 'success') {
                // Önbellek ve session takılmalarını sıfırlamak için sayfayı ana dizine zorla yönlendir
                window.location.href = window.location.pathname; 
            } else {
                alert('⚠️ Dönem devri sırasında bir hata oluştu.');
                // Hata durumunda kilitleri tamamen aç ve butonları geri getir
                isDonemDevrediliyor = false;
                btn.innerHTML = oMetin;
                btn.disabled = false;
                closeButtons.forEach(cBtn => cBtn.style.display = 'inline-block');
            }
        })
        .catch(err => {
            console.error("Devir hatası:", err);
            alert('⚠️ Sunucu ile bağlantı kesildi veya bir hata oluştu.');
            // Bağlantı koparsa kilitleri tamamen aç ve butonları geri getir
            isDonemDevrediliyor = false;
            btn.innerHTML = oMetin;
            btn.disabled = false;
            closeButtons.forEach(cBtn => cBtn.style.display = 'inline-block');
        });
    });
}


        const donemGeriAlBtn = document.getElementById('donem-geri-al-btn');
        if (donemGeriAlBtn) {
            donemGeriAlBtn.addEventListener('click', function() {
                if (!confirm('⚠️ dEneMe SÜRECİ: Aktif dönemi bir ay geriye çekmek ve kilitleri açmak istediğinize emin misiniz?')) return;
                const btn = donemGeriAlBtn;
                const oMetin = btn.innerHTML;
                btn.innerHTML = "⏳ Kilit Açılıyor...";
                btn.disabled = true;

                const formData = new FormData();
                formData.append('islem', 'donem_geri_al');

                fetch(window.location.href, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    btn.innerHTML = oMetin;
                    btn.disabled = false;
                    if (data && data.status === 'success') {
                        window.location.reload();
                    } else {
                        alert('⚠️ Dönem geri alınamadı.');
                    }
                })
                .catch(err => {
                    console.error("Kilit açma hatası:", err);
                    btn.innerHTML = oMetin;
                    btn.disabled = false;
                });
            });
        }
    });

    // 🎯 DONMA VE GECIKMEYI COZEN ANLIK INPUT RE-RENDER MOTORU
    document.addEventListener('input', function (e) {
        if (e.target && (
            e.target.id === 'faiz-orani-input' || 
            e.target.id === 'faiz-gun-input' || 
            e.target.id === 'faiz-kredi-butcesi-input' || 
            e.target.id === 'mobil_isbank_kull_limit' || 
            e.target.id === 'mobil_vakif_kull_limit'
        )) {
            hesaplaCiftKrediKarti();
        }
    });

        // 🎯 ADAPTÖR BLOK TETİKLEYİCİSİ: En ufak değişiklikte Controller'ı anında besler
    document.addEventListener('input', function (e) {
        if (e.target && (
            e.target.id === 'faiz-orani-input' || 
            e.target.id === 'faiz-gun-input' || 
            e.target.id === 'faiz-kredi-butcesi-input'
        )) {
            bayeFaizAnaparaAdaptorAtesle(e.target);
        }
    });



});

// =================================================================================
// 🎯 TO-DO LIST TARAYICI BELLEKLİ (LOCALSTORAGE) İNTERAKTİF TIKLAMA VE RENK MOTORU
// =================================================================================

function todoGorevTikla(id) {
    const el = document.getElementById('todo_' + id);
    if (!el) return;
    
    const d_donem = document.querySelector('.badge.bg-primary')?.innerText.trim() || 'genel_donem';

    if (el.classList.contains('bg-success-subtle')) {
        el.classList.remove('bg-success-subtle', 'border-success');
        el.style.opacity = "1";
        localStorage.removeItem('todo_status_' + d_donem + '_' + id);
    } else {
        el.classList.add('bg-success-subtle', 'border-success');
        el.style.opacity = "0.8";
        localStorage.setItem('todo_status_' + d_donem + '_' + id, 'done');
    }
}

window.todoDurumlariniYukle = function() {
    const d_donem = document.querySelector('.badge.bg-primary')?.innerText.trim() || 'genel_donem';
    
    ['c0', 'c1', 'c2', 'c3', 'c4', 'c5', 'c6', 'c7', 'c8'].forEach(id => {
        const status = localStorage.getItem('todo_status_' + d_donem + '_' + id);
        const el = document.getElementById('todo_' + id);
        
        if (el) {
            if (status === 'done') {
                el.classList.add('bg-success-subtle', 'border-success');
                el.style.opacity = "0.8";
            } else {
                el.classList.remove('bg-success-subtle', 'border-success');
                el.style.opacity = "1";
            }
        }
    });
}

// =================================================================================
// 🎯 TO-DO SEKMESİ VE ARKA PLAN TETİKLEYİCİ KAPANIŞ DÜĞÜMÜ
// =================================================================================

document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.todoDurumlariniYukle === 'function') { 
        window.todoDurumlariniYukle(); 
    }
});

</script>

<?php require admin_view("static/footer"); ?>
