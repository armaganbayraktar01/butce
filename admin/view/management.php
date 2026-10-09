<!-- C:\wamp64\www\hfa\admin\view\management.php -->
<?php 
    ## Tags Bilgileri
    $meta = [
        'site_title' => 'Güncel Dönem Bütçe Yönetimi | Bütçe Yönetimi',
        'site_desc' => 'Bütçe Yönetimi',
        'site_keyw' => 'Bütçe Yönetimi'
    ];
    # Tags Bilgileri

    require admin_view("static/header"); 

?>

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

    /* JavaScript tarafından basılacak butonların düz, ortalı ve gölgesiz pastel hali */
    .akilli-butce-tag {
        border: 1px solid #dee2e6 !important;
        background-color: #8e3586 !important;
        color: #f8f9fa;
        font-size: 10px !important;
        font-weight: 600 !important;
        padding: 5px 9px !important;
        border-radius: 4px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-shadow : none !important;
        line-height: 1 !important;
        box-shadow: none !important;
        transition: all 0.15s ease-in-out;
    }
    .akilli-butce-tag:hover {
        background-color: #b85eaf !important;
        border-color: #ced4da !important;
        color: #f8f9fa !important;
    }
    /* Kümbi Toplam Butonunun Düz Koyu Pastel Hali */
    .akilli-butce-tag-toplam {
        background-color: #b85eaf !important;
        border-color: #ced4da !important;
        color: #f8f9fa !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        padding: 5px 9px !important;
        border-radius: 4px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
        box-shadow: none !important;
        text-shadow : none !important;
        transition: background-color 0.15s ease-in-out;}
    .akilli-butce-tag-toplam:hover {
        border: 1px solid #dee2e6 !important;
        background-color: #72256b !important;
        color: #f8f9fa;
    }

    /* 🎯 MERKEZİ TASARIM STANDARTLARI CSS ALANI (GÖLGESİZ, SCROLLBARSIZ VE NET BORDERLI) */

    /* Kaydırma çubuğunu tamamen yok etme ve sığdırma */
    .excel-dashboard-wrapper .excel-temiz-responsive {
        max-height: none !important;
        overflow-y: visible !important;
        overflow-x: visible !important;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .excel-dashboard-wrapper .excel-temiz-responsive::-webkit-scrollbar {
        display: none;
    }

    /* Akıllı Faiz Paneli Premium Görsel Kimliği */
    .faiz-card-title {
        font-size: 13px !important;
        letter-spacing: -0.2px;
    }
    .faiz-title-icon {
        animation: pulseGlow 2s infinite ease-in-out;
    }
    .badge-fx { 
        font-size: 10px !important; 
        padding: 3px 6px; 
        font-weight: 700; 
        text-transform: uppercase; 
        border-radius: 6px;
        background: #f8f9fa;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.03);
        display: inline-flex;
        align-items: center;
        gap: 3px;
        transition: all 0.2s ease-in-out;
    }
    .badge-fx:hover {
        background: #f1f3f5;
        transform: translateY(-1px);
    }
    .fx-prefix {
        font-weight: 800;
        opacity: 0.75;
    }
    /* Banka renk kimliklerine bağlı sızdırmaz renk mühürleri */
    #pre-kredi-input { color: #dc3545 !important; } /* Kredi bütçesi: Dikkat çeken soft kırmızı */
    #pre-isbank-a    { color: #004797 !important; } /* İşbank A: Kurumsal İş Bankası Mavisi */
    #pre-isbank-o    { color: #007a33 !important; } /* İşbank Ö: Güven veren cüzdan yeşili */

    /* İnput Alanlarının Finansal Odak Tasarımları */
    .faiz-input-label {
        font-size: 11px !important;
        font-weight: 600;
        color: #495057;
    }
    .faiz-input-style {
        border: 1px solid #dee2e6 !important;
        border-radius: 6px !important;
        background-color: #fafbfc !important;
        transition: all 0.15s ease-in-out !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02) !important;
    }
    .faiz-input-style:focus {
        background-color: #ffffff !important;
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15) !important;
    }

    @keyframes pulseGlow {
        0% { transform: scale(1); opacity: 0.9; }
        50% { transform: scale(1.08); opacity: 1; filter: drop-shadow(0 0 4px rgba(255, 193, 7, 0.6)); }
        100% { transform: scale(1); opacity: 0.9; }
    }


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
                                                <input type="number" step="0.01" name="isbank_taksit[]" class="form-control form-control-sm border-0 bg-transparent p-0 text-end text-dark fw-bold isbank-taksit-input" value="<?= number_format($t_satir['isbank_tutar'], 2, '.', '') ?>">
                                            </td>
                                            <td class="p-0 px-1">
                                                <input type="number" step="0.01" name="vakif_taksit[]" class="form-control form-control-sm border-0 bg-transparent p-0 text-end text-dark fw-bold vakif-taksit-input" value="<?= number_format($t_satir['vakif_tutar'], 2, '.', '') ?>">
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
                        <!-- 🎯 REFORM: Buton onclick tetiği doğrudan bizim sızdırmaz yeni fonksiyonumuza kenetlendi! -->
                        <button type="button" 
                                id="cari-girdiler-kaydet-btn" 
                                class="btn btn-xs btn-success fw-bold px-2 py-0" style="font-size: 10px;" 
                                onclick="cariGirdileriKaydetAksiyonu()">
                            Kaydet
                        </button>

                    </div>



                    <div class="card-body p-2">
                        <table class="table table-sm table-borderless align-middle mb-0" style="table-layout: fixed;">
                            <thead>
                                <tr class="border-bottom border-light text-center fw-bold text-muted">
                                    <th style="width: 32%; text-align: left;" class="p-0 pb-1">AÇIKLAMA</th>
                                    <th style="width: 34%; color: #004b87;" class="p-0 pb-1">İŞBANK (A)</th>
                                    <th style="width: 34%; color: #b38f00;" class="p-0 pb-1">VAKIF (Ö)</th>
                                </tr>
                            </thead>


                            <tbody>
                                <!-- SATIR 1: GELECEK DÖNEM TAKSİT GÖSTERGELERİ -->
                                <tr class="border-bottom border-light" style="height: 22px; background-color: #fcfcfc;">
                                    <td class="p-0 ps-1 text-muted fw-semibold" style="font-size:10px; vertical-align: middle;">Gelecek Dönem Taksit</td>
                                    <td class="p-0 px-1">
                                        <input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-dark font-monospace" 
                                            readonly value="<?= number_format($isbank_aktif_taksit ?? 0, 2, ',', '.') ?> ₺" 
                                            style="background-color: #e9ecef !important; font-size:10px !important;">
                                    </td>
                                    <td class="p-0 px-1">
                                        <input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-dark font-monospace" 
                                            readonly value="<?= number_format($vakif_aktif_taksit ?? 0, 2, ',', '.') ?> ₺" 
                                            style="background-color: #e9ecef !important; font-size:10px !important;">
                                    </td>
                                </tr>

                                <!-- SATIR 2: DÖNEM İÇİ HARCAMA ALANLARI -->
                                <tr class="border-bottom border-light" style="height: 24px;">
                                    <td class="p-0 ps-1 text-muted" style="font-size:10px; vertical-align: middle;">Dönem İçi Harc.</td>
                                    
                                    <!-- İŞ BANKASI -->
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <?php 
                                            $is_isbank_active = (isset($cc_meta['isbank']['donem_ici']) && $cc_meta['isbank']['donem_ici'] > 0); 
                                            $isbank_val = $is_isbank_active ? $cc_meta['isbank']['donem_ici'] : ($kkart['isbank']['donem_ici'] ?? 0);
                                        ?>
                                        <input type="number" step="0.01" id="isbank-donem-ici-input" 
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-bold font-monospace border rounded <?= $is_isbank_active ? 'text-danger bg-white' : 'text-muted' ?>" 
                                            value="<?= number_format($isbank_val, 2, '.', '') ?>" 
                                            style="height: 19px; font-size:10px !important; <?= !$is_isbank_active ? 'background-color: #e9ecef !important; cursor: not-allowed;' : '' ?>" 
                                            <?= !$is_isbank_active ? 'readonly' : '' ?>
                                            oninput="cariGirdileriAnlikGuncelle('isbank', 'donem_ici', this.value)">
                                    </td>
                                    
                                    <!-- VAKIFBANK -->
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <?php 
                                            $is_vakif_active = (isset($cc_meta['vakifbank']['donem_ici']) && $cc_meta['vakifbank']['donem_ici'] > 0); 
                                            $vakif_val = $is_vakif_active ? $cc_meta['vakifbank']['donem_ici'] : ($kkart['vakifbank']['donem_ici'] ?? 0);
                                        ?>
                                        <input type="number" step="0.01" id="vakif-donem-ici-input" 
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-bold font-monospace border rounded <?= $is_vakif_active ? 'text-danger bg-white' : 'text-muted' ?>" 
                                            value="<?= number_format($vakif_val, 2, '.', '') ?>" 
                                            style="height: 19px; font-size:10px !important; <?= !$is_vakif_active ? 'background-color: #e9ecef !important; cursor: not-allowed;' : '' ?>" 
                                            <?= !$is_vakif_active ? 'readonly' : '' ?>
                                            oninput="cariGirdileriAnlikGuncelle('vakifbank', 'donem_ici', this.value)">
                                    </td>
                                </tr>

                                <!-- SATIR 3: PROVİZYONDA BEKLEYEN HARCAMA ALANLARI -->
                                <tr class="border-bottom border-light" style="height: 24px;">
                                    <td class="p-0 ps-1 text-muted" style="font-size:10px; vertical-align: middle;">Provizyonda Bek.</td>
                                    
                                    <!-- İŞ BANKASI -->
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <?php 
                                            $is_isbank_prov = (isset($cc_meta['isbank']['provizyon']) && $cc_meta['isbank']['provizyon'] > 0); 
                                            $isbank_prov_val = $is_isbank_prov ? $cc_meta['isbank']['provizyon'] : ($kkart['isbank']['provizyon'] ?? 0);
                                        ?>
                                        <input type="number" step="0.01" id="isbank-provizyon-input" 
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-bold font-monospace border rounded <?= $is_isbank_prov ? 'text-dark bg-white' : 'text-muted' ?>" 
                                            value="<?= number_format($isbank_prov_val, 2, '.', '') ?>" 
                                            style="height: 19px; font-size:10px !important; <?= !$is_isbank_prov ? 'background-color: #e9ecef !important; cursor: not-allowed;' : '' ?>" 
                                            <?= !$is_isbank_prov ? 'readonly' : '' ?>
                                            oninput="cariGirdileriAnlikGuncelle('isbank', 'provizyon', this.value)">
                                    </td>
                                    
                                    <!-- VAKIFBANK -->
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <?php 
                                            $is_vakif_prov = (isset($cc_meta['vakifbank']['provizyon']) && $cc_meta['vakifbank']['provizyon'] > 0); 
                                            $vakif_prov_val = $is_vakif_prov ? $cc_meta['vakifbank']['provizyon'] : ($kkart['vakifbank']['provizyon'] ?? 0);
                                        ?>
                                        <input type="number" step="0.01" id="vakif-provizyon-input" 
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-bold font-monospace border rounded <?= $is_vakif_prov ? 'text-warning-emphasis bg-white' : 'text-muted' ?>" 
                                            value="<?= number_format($vakif_prov_val, 2, '.', '') ?>" 
                                            style="height: 19px; font-size:10px !important; <?= !$is_vakif_prov ? 'background-color: #e9ecef !important; cursor: not-allowed;' : '' ?>" 
                                            <?= !$is_vakif_prov ? 'readonly' : '' ?>
                                            oninput="cariGirdileriAnlikGuncelle('vakifbank', 'provizyon', this.value)">
                                    </td>
                                </tr>

                                <!-- SATIR 4: KONSOLİDE DÖVİZ BORCU -->
                                <tr class="border-bottom border-light" style="height: 24px;">
                                    <td class="p-0 ps-1 text-muted" style="font-size:10px; vertical-align: middle;">Döviz Borcu</td>
                                    
                                    <!-- İŞ BANKASI -->
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <?php 
                                            $is_isbank_dov = (isset($cc_meta['isbank']['doviz_borcu']) && $cc_meta['isbank']['doviz_borcu'] > 0); 
                                            $isbank_dov_val = $is_isbank_dov ? $cc_meta['isbank']['doviz_borcu'] : ($kkart['isbank']['doviz_borcu'] ?? 0);
                                        ?>
                                        <input type="number" step="0.01" id="isbank-doviz-input" 
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-medium font-monospace border rounded <?= $is_isbank_dov ? 'text-dark bg-white' : 'text-muted' ?>" 
                                            value="<?= number_format($isbank_dov_val, 2, '.', '') ?>" 
                                            style="height: 19px; font-size:10px !important; <?= !$is_isbank_dov ? 'background-color: #e9ecef !important; cursor: not-allowed;' : '' ?>" 
                                            <?= !$is_isbank_dov ? 'readonly' : '' ?>
                                            oninput="cariGirdileriAnlikGuncelle('isbank', 'doviz_borcu', this.value)">
                                    </td>
                                    
                                    <!-- VAKIFBANK -->
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <?php 
                                            $is_vakif_dov = (isset($cc_meta['vakifbank']['doviz_borcu']) && $cc_meta['vakifbank']['doviz_borcu'] > 0); 
                                            $vakif_dov_val = $is_vakif_dov ? $cc_meta['vakifbank']['doviz_borcu'] : ($kkart['vakifbank']['doviz_borcu'] ?? 0);
                                        ?>
                                        <input type="number" step="0.01" id="vakif-doviz-input" 
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-medium font-monospace border rounded <?= $is_vakif_dov ? 'text-dark bg-white' : 'text-muted' ?>" 
                                            value="<?= number_format($vakif_dov_val, 2, '.', '') ?>" 
                                            style="height: 19px; font-size:10px !important; <?= !$is_vakif_dov ? 'background-color: #e9ecef !important; cursor: not-allowed;' : '' ?>" 
                                            <?= !$is_vakif_dov ? 'readonly' : '' ?>
                                            oninput="cariGirdileriAnlikGuncelle('vakifbank', 'doviz_borcu', this.value)">
                                    </td>
                                </tr>
                                <!-- SATIR 5: EURO BORCU GİRİŞ HÜCRELERİ (SALT OKUNUR + BALON GÖSTERGELİ) -->
                                <tr class="border-bottom border-light" style="height: 24px;">
                                    <td class="p-0 ps-1 text-muted" style="font-size:10px; vertical-align: middle;">Euro Borcu</td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="isbank-eu-input" readonly
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-medium text-dark font-monospace border rounded" 
                                            value="<?= (isset($kkart['isbank']['eu_borcu']) && $kkart['isbank']['eu_borcu'] > 0) ? number_format($kkart['isbank']['eu_borcu'], 2, '.', '') : (isset($cc_meta['isbank']['eu_borcu']) ? number_format($cc_meta['isbank']['eu_borcu'], 2, '.', '') : '0.00') ?>" 
                                            style="background-color: #e9ecef !important; cursor: not-allowed; height: 19px; font-size:10px !important;" 
                                            data-bs-toggle="tooltip" data-bs-placement="top" 
                                            data-bs-title="<?= (isset($kkart['isbank']['eu_borc_tl']) && $kkart['isbank']['eu_borc_tl'] > 0) ? number_format($kkart['isbank']['eu_borc_tl'], 2, ',', '.') : (isset($cc_meta['isbank']['eu_borc_tl']) ? number_format($cc_meta['isbank']['eu_borc_tl'], 2, ',', '.') : '0,00') ?> ₺"
                                            oninput="cariGirdileriAnlikGuncelle('isbank', 'eu_borcu', this.value)">
                                    </td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="vakif-eu-input" readonly
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-medium text-dark font-monospace border rounded" 
                                            value="<?= (isset($kkart['vakifbank']['eu_borcu']) && $kkart['vakifbank']['eu_borcu'] > 0) ? number_format($kkart['vakifbank']['eu_borcu'], 2, '.', '') : (isset($cc_meta['vakifbank']['eu_borcu']) ? number_format($cc_meta['vakifbank']['eu_borcu'], 2, '.', '') : '0.00') ?>" 
                                            style="background-color: #e9ecef !important; cursor: not-allowed; height: 19px; font-size:10px !important;" 
                                            data-bs-toggle="tooltip" data-bs-placement="top" 
                                            data-bs-title="<?= (isset($kkart['vakifbank']['eu_borc_tl']) && $kkart['vakifbank']['eu_borc_tl'] > 0) ? number_format($kkart['vakifbank']['eu_borc_tl'], 2, ',', '.') : (isset($cc_meta['vakifbank']['eu_borc_tl']) ? number_format($cc_meta['vakifbank']['eu_borc_tl'], 2, ',', '.') : '0,00') ?> ₺"
                                            oninput="cariGirdileriAnlikGuncelle('vakifbank', 'eu_borcu', this.value)">
                                    </td>
                                </tr>

                                <!-- SATIR 6: DOLAR BORCU GİRİŞ HÜCRELERİ (SALT OKUNUR + BALON GÖSTERGELİ) -->
                                <tr class="border-bottom border-light" style="height: 24px;">
                                    <td class="p-0 ps-1 text-muted" style="font-size:10px; vertical-align: middle;">Dolar Borcu</td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="isbank-usd-input" readonly
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-medium text-dark font-monospace border rounded" 
                                            value="<?= (isset($kkart['isbank']['usd_borcu']) && $kkart['isbank']['usd_borcu'] > 0) ? number_format($kkart['isbank']['usd_borcu'], 2, '.', '') : (isset($cc_meta['isbank']['usd_borcu']) ? number_format($cc_meta['isbank']['usd_borcu'], 2, '.', '') : '0.00') ?>" 
                                            style="background-color: #e9ecef !important; cursor: not-allowed; height: 19px; font-size:10px !important;" 
                                            data-bs-toggle="tooltip" data-bs-placement="top" 
                                            data-bs-title="<?= (isset($kkart['isbank']['usd_borc_tl']) && $kkart['isbank']['usd_borc_tl'] > 0) ? number_format($kkart['isbank']['usd_borc_tl'], 2, ',', '.') : (isset($cc_meta['isbank']['usd_borc_tl']) ? number_format($cc_meta['isbank']['usd_borc_tl'], 2, ',', '.') : '0,00') ?> ₺"
                                            oninput="cariGirdileriAnlikGuncelle('isbank', 'usd_borcu', this.value)">
                                    </td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="number" step="0.01" id="vakif-usd-input" readonly
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-medium text-dark font-monospace border rounded" 
                                            value="<?= (isset($kkart['vakifbank']['usd_borcu']) && $kkart['vakifbank']['usd_borcu'] > 0) ? number_format($kkart['vakifbank']['usd_borcu'], 2, '.', '') : (isset($cc_meta['vakifbank']['usd_borcu']) ? number_format($cc_meta['vakifbank']['usd_borcu'], 2, '.', '') : '0.00') ?>" 
                                            style="background-color: #e9ecef !important; cursor: not-allowed; height: 19px; font-size:10px !important;" 
                                            data-bs-toggle="tooltip" data-bs-placement="top" 
                                            data-bs-title="<?= (isset($kkart['vakifbank']['usd_borc_tl']) && $kkart['vakifbank']['usd_borc_tl'] > 0) ? number_format($kkart['vakifbank']['usd_borc_tl'], 2, ',', '.') : (isset($cc_meta['vakifbank']['usd_borc_tl']) ? number_format($cc_meta['vakifbank']['usd_borc_tl'], 2, ',', '.') : '0,00') ?> ₺"
                                            oninput="cariGirdileriAnlikGuncelle('vakifbank', 'usd_borcu', this.value)">
                                    </td>
                                </tr>
                                <!-- SATIR 7: KESİNLEŞEN EKSTRE -->
                                <tr class="border-bottom border-light" style="height: 24px;">
                                    <td class="p-0 ps-1 text-muted" style="font-size:10px; vertical-align: middle;">Kesinleşen Ekstre</td>
                                    
                                    <!-- İŞ BANKASI -->
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <?php 
                                            $is_isbank_kesin = (isset($cc_meta['isbank']['kesinlesen_ekstre']) && $cc_meta['isbank']['kesinlesen_ekstre'] > 0); 
                                            $isbank_kesin_val = $is_isbank_kesin ? $cc_meta['isbank']['kesinlesen_ekstre'] : ($kkart['isbank']['kesinlesen_ekstre'] ?? 0);
                                        ?>
                                        <div class="input-group" style="height: 19px;">
                                            <input type="number" step="0.01" id="isbank-ekstre-input" 
                                                class="form-control form-control-sm text-end p-0 pe-1 fw-bold font-monospace <?= $is_isbank_kesin ? 'text-dark bg-white' : 'text-muted' ?>" 
                                                value="<?= number_format($isbank_kesin_val, 2, '.', '') ?>" 
                                                style="font-size:10px !important; height: 100%; max-width: 80% !important; border-top-right-radius: 0; border-bottom-right-radius: 0; <?= !$is_isbank_kesin ? 'background-color: #e9ecef !important; cursor: not-allowed;' : '' ?>" 
                                                <?= !$is_isbank_kesin ? 'readonly' : '' ?>
                                                oninput="cariGirdileriAnlikGuncelle('isbank', 'kesinlesen_ekstre', this.value)">
                                            <button class="btn btn-xs btn-outline-primary d-flex align-items-center justify-content-center p-0 px-1" 
                                                type="button" 
                                                title="İşbank Hesap Kesimi Yap"
                                                onclick="hesapKesimiYapAksiyonu('isbank')"
                                                style="font-size: 9px; height: 100%; border-top-left-radius: 0; border-bottom-left-radius: 0; min-height: 19px;">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </button>
                                        </div>
                                    </td>
                                    
                                    <!-- VAKIFBANK -->
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <?php 
                                            $is_vakif_kesin = (isset($cc_meta['vakifbank']['kesinlesen_ekstre']) && $cc_meta['vakifbank']['kesinlesen_ekstre'] > 0); 
                                            $vakif_kesin_val = $is_vakif_kesin ? $cc_meta['vakifbank']['kesinlesen_ekstre'] : ($kkart['vakifbank']['kesinlesen_ekstre'] ?? 0);
                                        ?>
                                        <div class="input-group" style="height: 19px;">
                                            <input type="number" step="0.01" id="vakif-ekstre-input" 
                                                class="form-control form-control-sm text-end p-0 pe-1 fw-bold font-monospace <?= $is_vakif_kesin ? 'text-warning-emphasis bg-white' : 'text-muted' ?>" 
                                                value="<?= number_format($vakif_kesin_val, 2, '.', '') ?>" 
                                                style="font-size:10px !important; max-width: 80% !important; height: 100%; border-top-right-radius: 0; border-bottom-right-radius: 0; <?= !$is_vakif_kesin ? 'background-color: #e9ecef !important; cursor: not-allowed;' : '' ?>" 
                                                <?= !$is_vakif_kesin ? 'readonly' : '' ?>
                                                oninput="cariGirdileriAnlikGuncelle('vakifbank', 'kesinlesen_ekstre', this.value)">
                                            <button class="btn btn-xs btn-outline-warning d-flex align-items-center justify-content-center p-0 px-1" 
                                                type="button" 
                                                title="Vakıfbank Hesap Kesimi Yap"
                                                onclick="hesapKesimiYapAksiyonu('vakifbank')"
                                                style="font-size: 9px; height: 100%; border-top-left-radius: 0; border-bottom-left-radius: 0; min-height: 19px;">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- SATIR 7: HARCAMA LİMİTİ AYARLAMA HÜCRELERİ -->
                                <tr class="border-bottom border-light" style="height: 24px; background-color: #fffaf0;">
                                    <td class="p-0 ps-1 text-dark fw-bold" style="font-size:10px; vertical-align: middle;">Harcama Limiti</td>
                                    
                                    <!-- İŞ BANKASI -->
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <?php 
                                            $is_isbank_lmt = (isset($cc_meta['isbank']['harcanabilir_limit']) && $cc_meta['isbank']['harcanabilir_limit'] > 0); 
                                            $isbank_lmt_val = $is_isbank_lmt ? $cc_meta['isbank']['harcanabilir_limit'] : ($kkart['isbank']['harcanabilir_limit'] ?? 0);
                                        ?>
                                        <input type="number" step="0.01" id="isbank-harcama-limiti-input" 
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-bold font-monospace border rounded <?= $is_isbank_lmt ? 'text-dark bg-white' : 'text-muted' ?>"
                                            value="<?= number_format($isbank_lmt_val, 2, '.', '') ?>"
                                            style="height: 19px; font-size:10px !important; <?= !$is_isbank_lmt ? 'background-color: #e9ecef !important; cursor: not-allowed;' : '' ?>" 
                                            <?= !$is_isbank_lmt ? 'readonly' : '' ?>
                                            oninput="cariGirdileriAnlikGuncelle('isbank', 'harcanabilir_limit', this.value)">
                                    </td>
                                    
                                    <!-- VAKIFBANK -->
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <?php 
                                            $is_vakif_lmt = (isset($cc_meta['vakifbank']['harcanabilir_limit']) && $cc_meta['vakifbank']['harcanabilir_limit'] > 0); 
                                            $vakif_lmt_val = $is_vakif_lmt ? $cc_meta['vakifbank']['harcanabilir_limit'] : ($kkart['vakifbank']['harcanabilir_limit'] ?? 0);
                                        ?>
                                        <input type="number" step="0.01" id="vakif-harcama-limiti-input" 
                                            class="form-control form-control-sm text-end p-0 pe-1 fw-bold font-monospace border rounded <?= $is_vakif_lmt ? 'text-dark bg-white' : 'text-muted' ?>"
                                            value="<?= number_format($vakif_lmt_val, 2, '.', '') ?>"
                                            style="height: 19px; font-size:10px !important; <?= !$is_vakif_lmt ? 'background-color: #e9ecef !important; cursor: not-allowed;' : '' ?>" 
                                            <?= !$is_vakif_lmt ? 'readonly' : '' ?>
                                            oninput="cariGirdileriAnlikGuncelle('vakifbank', 'harcanabilir_limit', this.value)">
                                    </td>
                                </tr>

                                <!-- SATIR 8: HARCANABİLİR BAKİYE SONUÇ ALANLARI -->
                                <tr class="border-bottom border-light" style="height: 24px; background-color: #f4fbf7;">
                                    <td class="p-0 ps-1 text-success fw-bold" style="font-size:10px; vertical-align: middle;">Harcanabilir Bakiye</td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 pe-1 text-end fw-bold text-success font-monospace" 
                                            readonly value="<?= number_format($isbank_harcanabilir ?? 0, 2, ',', '.') ?> ₺" 
                                            style="background-color: #e9ecef !important; font-size:10px !important; cursor: default;">
                                    </td>
                                    <td class="p-0 px-1" style="vertical-align: top; padding-top: 3px !important;">
                                        <input type="text" class="form-control form-control-sm border-0 bg-transparent p-0 pe-1 text-end fw-bold text-success font-monospace" 
                                            readonly value="<?= number_format($vakif_harcanabilir ?? 0, 2, ',', '.') ?> ₺" 
                                            style="background-color: #e9ecef !important; font-size:10px !important; cursor: default;">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer py-2 px-2 border-0 d-flex align-items-center justify-content-between" style="background-color: #e8f5e9;">
                        <span class="text-success-emphasis fw-bold" style="font-size: 11px;">Kesinleşen Dön. Borcu</span>
                        <div class="d-flex gap-1" style="width: 68%;">
                            <!-- REFORM: Sayfa ilk açıldığında taze PHP değerleri value olarak mühürleniyor, JS anlık artırıp azaltabiliyor -->
                            <input type="text" id="isbank_kesinlesen_donem" name="isbank_kesinlesen_donem" 
                                class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-success font-monospace" 
                                value="<?= isset($cc_meta['isbank_ekstre']) ? number_format($cc_meta['isbank_ekstre'], 2, ',', '.') . ' ₺' : '0,00 ₺' ?>" readonly>
                                
                            <input type="text" id="vakif_kesinlesen_donem" name="vakif_kesinlesen_donem" 
                                class="form-control form-control-sm border-0 bg-transparent p-0 text-end fw-bold text-success font-monospace" 
                                value="<?= isset($cc_meta['vakif_ekstre']) ? number_format($cc_meta['vakif_ekstre'], 2, ',', '.') . ' ₺' : '0,00 ₺' ?>" readonly>
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

                <form id="gelir-butce-formu" onsubmit="return false;" style="overflow: hidden; margin: 0; padding: 0; width: 100%;">


                        <?php foreach ($gelirler as $kalem): 
                            $is_odendi = (floatval($kalem['odenen_tutar']) > 0 || (floatval($kalem['odenen_tutar']) == floatval($kalem['tutar']) && floatval($kalem['tutar']) > 0)); 
                            $is_kilitli = (intval($kalem['is_locked'] ?? 0) === 1);
                            $input_kapat = ($is_odendi || $is_kilitli);
                            
                            $gosterilecek_gelir = floatval($kalem['tutar']); 
                            $k_isim = trim($kalem['kalem_adi']);
                            
                            if ($k_isim === 'Evdeki Nakit Girişi' || strpos(mb_strtolower($k_isim, 'UTF-8'), 'havuz transfer fonu') !== false) {
                                continue;
                            }
                        ?>
                        <!-- 🎯 SATIR 1: GELİR DÖNGÜSÜ (Orijinal %65 İsim - %35 Konsol Nizamı) -->
                        <div class="d-flex align-items-center border-bottom border-light px-2 py-0 row-kalem <?= ($kalem['is_locked'] ?? 0) == 1 ? 'row-odendi' : '' ?>" 
                            data-kalem-id="<?= intval($kalem['id'] ?? 0) ?>"
                            data-havuz-kodu="<?= htmlspecialchars($kalem['havuz_kodu'] ?? '') ?>"
                            data-kalem-adi="<?= htmlspecialchars($kalem['kalem_adi'] ?? '') ?>"
                            style="height: 30px; min-height: 30px; width: 100%; padding-left: 4px; padding-right: 4px;">
                            
                            <!-- 1. BÖLÜM: Sol Alan (%65) - Sadece Saf İsim Input Kutusu Var -->
                            <div style="width: 60%; min-width: 60%;">
                                <input type="text" name="kalemler[<?= $kalem['id'] ?? 0 ?>][adi]" 
                                    class="form-control form-control-sm text-dark fw-semibold border border-secondary-subtle px-2" 
                                    value="<?= htmlspecialchars($kalem['kalem_adi']) ?>" 
                                    <?= $input_kapat ? 'disabled' : '' ?> 
                                    style="font-size: 11px !important; height: 24px; min-height: 24px; background-color: #fff; width: 100%;">
                            </div>
                            
                            <!-- 2. BÖLÜM: Sağ Konsol Alanı (%35) - Tutar + Orijinal Yeşil EFT Butonu -->
                            <div class="d-flex align-items-center justify-content-end" style="width: 40%; min-width: 40%; gap: 4px;">
                            
                                <!-- Çöp Kutusu Hizalama Boşluğu (14px) -->
                                <?php 
                                $sabit_kelimeler = ['Maaş', 'Mesai', 'Maktu', 'KK', 'Kredi', 'Faturası', 'Damacana', 'İçme Suyu', 'Aidat', 'Nergiz Cep'];
                                $is_sabit = false;
                                foreach($sabit_kelimeler as $sk) { if(strpos($kalem['kalem_adi'], $sk) !== false) { $is_sabit = true; break; } }
                                
                                if (!$is_sabit && !($kalem['is_locked'] ?? 0)): ?>
                                    <button type="button" class="btn btn-xs btn-link text-danger p-0 d-inline-flex align-items-center justify-content-center me-1" 
                                            onclick="tekilKalemSil(<?= intval($kalem['id'] ?? 0) ?>)" 
                                            title="Bu kalemi kaldır"
                                            style="width: 14px; height: 14px; min-width: 14px; border: none; background: transparent;">
                                        <i class="bi bi-trash3-fill" style="font-size: 11px; line-height: 1;"></i>
                                    </button>
                                <?php else: ?>
                                    <div style="width: 14px; min-width: 14px; height: 14px;" class="me-1"></div>
                                <?php endif; ?>

                                <!-- Tutar Giriş Kutusu (Genişlik 110px, Öz Hakiki Yeşil Yazı Rengi) -->
                                <input type="number" step="0.01" name="kalemler[<?= $kalem['id'] ?? 0 ?>][tutar]" 
                                    class="form-control form-control-sm text-end text-success fw-bold font-monospace <?= $input_kapat ? '' : 'border border-secondary-subtle px-2' ?>" 
                                    value="<?= number_format($gosterilecek_getir = $gosterilecek_gelir, 2, '.', '') ?>" 
                                    <?= $input_kapat ? 'disabled style="background: transparent; font-weight: 700; border: none !important; padding-right: 8px;"' : 'style="background-color: #fff; font-size: 11px !important; width: 110px; min-width: 110px; height: 24px; min-height: 24px;"' ?>>
                                
                                <!-- Orijinal Yeşil Renkli Saf EFT Butonu (Genişlik tam 65px) -->
                                <?php if (($kalem['is_locked'] ?? 0) == 1): ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center fw-bold" disabled style="width: 65px; min-width: 65px; height: 24px; font-size: 10px; padding: 0;">✓</button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-success text-white fw-bold shadow-xs d-flex align-items-center justify-content-center eft-btn" 
                                            onclick="gelirEftBitir(<?= intval($kalem['id'] ?? 0) ?>, 'gelir_eft', this, <?= number_format($gosterilecek_gelir, 2, '.', '') ?>)"
                                            style="width: 65px; min-width: 65px; height: 24px; font-size: 10px; padding: 0; gap: 3px; border: none;">
                                        <i class="bi bi-credit-card-fill text-white" style="font-size: 9px; line-height: 1;"></i>EFT
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>


                        <!-- 🎯 SATIR 2: BAKİYE DEVRET ALANI (Üst Listeyle Milimetrik Hizalanmış Yapı) -->
                        
                        <div class="card-footer bg-light border-top border-light py-0 px-2 d-flex align-items-center" style="height: 34px; min-height: 34px;">
                            <div class="d-flex align-items-center w-100" style="height: 30px; min-height: 30px;">
                                

                                <div class="d-flex align-items-center" style="width: 65%; min-width: 65%; gap: 6px;">
                                    <!-- Sabit Başlık Plakası (Genişlik esnektir, yanına select sığar) -->
                                    <input type="text" class="form-control form-control-sm text-dark fw-bold border border-secondary-subtle px-2 flex-grow-1" 
                                        value="Bakiye Devret" readonly 
                                        style="font-size: 10px !important; height: 24px; min-height: 24px; background-color: #f8f9fa; cursor: default; letter-spacing: 0.5px;">
                                    
                                    <!-- 🎯 REFORM: Finansal Havuz Seçimi artık tam bu sol alandadır! (Genişlik tam 110px) -->
                                    <select id="devir-havuz-kodu" 
                                            class="form-select form-select-sm fw-semibold text-secondary border border-secondary-subtle" 
                                            style="font-size: 11px !important; width: 110px; min-width: 110px; height: 24px; padding: 0 4px; background-color: #fff;">
                                        <?php 
                                        try {
                                            $havuzSorgu = $db->query("SELECT havuz_kodu, havuz_adi FROM finansal_havuzlar ORDER BY id ASC");
                                            $havuzlar = $havuzSorgu->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($havuzlar as $havuz) {
                                                echo '<option value="' . htmlspecialchars($havuz['havuz_kodu']) . '">' . htmlspecialchars($havuz['havuz_adi']) . '</option>';
                                            }
                                        } catch (Exception $e) {
                                            echo '<option value="isbank_a">İşbank (A)</option>';
                                            echo '<option value="isbank_o">İşbank (Ö)</option>';
                                            echo '<option value="vakif_o">Vakıf (Ö)</option>';
                                            echo '<option value="nakit">Nakit</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                

                                <div class="d-flex align-items-center justify-content-end" style="width: 35%; min-width: 35%; gap: 4px;">
                                
                                    <!-- Hayalet Çöp Kutusu Boşluk Tamponu (Üstteki listedeki 14px boşlukla milimetrik eşitlendi) -->
                                    <div style="width: 14px; min-width: 14px; height: 14px;" class="me-1"></div>

                                    <!-- Tutar Giriş Kutusu (Üstteki tutarlarla alt alta jilet gibi hizalanması için tam 110px kilitli) -->
                                    <input type="number" step="0.01" id="devir-havuz-tutar" 
                                        class="form-control form-control-sm text-end text-success fw-bold font-monospace border border-secondary-subtle px-2" 
                                        placeholder="0.00" 
                                        style="font-size: 11px !important; width: 110px; min-width: 110px; height: 24px; min-height: 24px; background-color: #fff;">
                                    
                                    <!-- Orijinal Değişmeyen Mavi Renkli Konsept EFT Butonu (Üstteki yeşil butonla alt alta jilet hizadadır: Tam 65px) -->
                                    <button type="button" class="btn btn-sm text-white fw-bold shadow-xs d-flex align-items-center justify-content-center text-uppercase" 
                                            style="width: 65px; min-width: 65px; height: 24px; font-size: 10px; padding: 0; gap: 2px; border: none; " 
                                            onclick="bakiyeDevretEftAksiyon(this)">
                                        <i class="bi bi-credit-card-fill text-white" style="font-size: 9px; line-height: 1;"></i>EFT
                                    </button>
                                </div>
                            </div>
                        </div>

                </form>


                <!-- GELİR FOOTER ALANI (%60-%40 KONSEPT SÜRÜMÜ) -->
                <div class="card-footer bg-light border-top border-light py-0 px-2 d-flex align-items-center" style="height: 34px; min-height: 34px;">
                    <div class="d-flex align-items-center w-100" style="height: 30px; min-height: 30px;">
                        
                        <div style="width: 60%; min-width: 60%;" class="pe-2">
                            <input type="text" id="yeni-gelir-adi" class="form-control form-control-sm text-dark fw-semibold border border-secondary-subtle px-2 input-ekle" placeholder="Gelir kalemi adı giriniz.." style="font-size: 11px !important; height: 24px; min-height: 24px; background-color: #fff; width: 100%;">
                        </div>
                        

                        <div class="d-flex align-items-center justify-content-end" style="width: 40%; min-width: 40%; gap: 6px;">
                            
  
                            <input type="number" step="0.01" id="yeni-gelir-tutar" class="form-control form-control-sm text-end text-success fw-bold font-monospace border border-secondary-subtle px-2 input-ekle" placeholder="0.00" style="font-size: 11px !important; width: 110px; min-width: 110px; height: 24px; min-height: 24px; background-color: #fff;">
                            
                            <!-- Finansal Havuz Seçimi (Genişlik 110px sabit, yükseklik konsept gereği 24px yapıldı) -->
                            <select id="yeni-gelir-havuz" class="form-select form-select-sm fw-semibold text-secondary border border-secondary-subtle" style="font-size: 11px !important; width: 110px; min-width: 110px; height: 24px; padding: 0 4px; background-color: #fff;">
                                <?php 
                                try {
                                    $havuzSorgu = $db->query("SELECT havuz_kodu, havuz_adi FROM finansal_havuzlar ORDER BY id ASC");
                                    $havuzlar = $havuzSorgu->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($havuzlar as $havuz) {
                                        echo '<option value="' . htmlspecialchars($havuz['havuz_kodu']) . '">' . htmlspecialchars($havuz['havuz_adi']) . '</option>';
                                    }
                                } catch (Exception $e) {
                                    echo '<option value="isbank_a">İşbank (A)</option>';
                                    echo '<option value="isbank_o">İşbank (Ö)</option>';
                                    echo '<option value="vakif_o">Vakıf (Ö)</option>';
                                    echo '<option value="nakit">Nakit</option>';
                                }
                                ?>
                            </select>

                            <!-- 🎯 REFORM: Beyaz Yazılı, Mavi Renkli Konsept EKLE Butonu (Genişlik tam 65px sabitlenerek jilet hizaya alındı) -->
                            <button type="button" class="btn btn-sm text-white fw-bold shadow-xs d-flex align-items-center justify-content-center text-uppercase" 
                                    style="width: 65px; min-width: 65px; height: 24px; font-size: 10px; padding: 0; gap: 2px; border: none; " 
                                    onclick="ekGelirKalemiEkle(this)">
                                <i class="bi bi-plus-lg text-white" style="font-size: 9px; line-height: 1;"></i>EKLE
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

                <div class="card-body p-2" style="max-height: auto; overflow-y: hidden;">
                    
                    <form id="gider-butce-formu" onsubmit="return false;" style="overflow: hidden; margin: 0; padding: 0; width: 100%;">

                        <?php foreach ($giderler as $kalem): 
                            $is_odendi = (floatval($kalem['odenen_tutar']) > 0); 
                            $is_kilitli = (intval($kalem['is_locked'] ?? 0) === 1);
                            $isim = trim($kalem['kalem_adi']);
                            
                            $is_akilli_su = (strpos($isim, 'Damacana') !== false || strpos($isim, 'İçme Suyu') !== false || strpos($isim, 'damacana') !== false);
                            $is_telefon_faturasi = (strpos(mb_strtolower($isim, 'UTF-8'), 'telefon') !== false);
                            
                            if ($is_akilli_su || $isim === 'Nergiz Cep' || $is_telefon_faturasi) continue; 

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
                        <!-- 🎯 REFORM 1: Satır genişliği %100 yapıldı. Padding değerleri sıfırlanarak jilet nizam sağlandı -->
                        <div class="d-flex align-items-center border-bottom border-light py-0 row-kalem <?= $is_odendi ? 'row-row-odendi' : '' ?>" 
                            data-kalem-id="<?= intval($kalem['id']) ?>"
                            data-havuz-kodu="<?= htmlspecialchars($kalem['havuz_kodu'] ?? '') ?>"
                            data-kalem-adi="<?= htmlspecialchars($kalem['kalem_adi'] ?? '') ?>"
                            style="height: 30px; min-height: 30px; width: 100%; padding-left: 4px; padding-right: 4px;">
                            
                            <!-- 1. BÖLÜM: SAF İSİM INPUT ALANI-->
                            <div style="width: 60%; min-width: 60%;">
                                <?php if ($is_kilitli_kart): ?>
                                    <input type="text" class="form-control form-control-sm text-dark fw-bold border border-secondary-subtle px-2" 
                                        value="<?= htmlspecialchars($kalem['kalem_adi']) ?>" readonly 
                                        style="background-color: #fff; font-size: 11px !important; height: 24px; min-height: 24px; padding-left: 8px; cursor: default; width: 100%;">
                                <?php else: ?>
                                    <input type="text" name="kalemler[<?= $kalem['id'] ?>][adi]" 
                                        class="form-control form-control-sm text-dark fw-semibold border border-secondary-subtle px-2" 
                                        value="<?= htmlspecialchars($kalem['kalem_adi']) ?>" 
                                        <?= $is_kilitli ? 'disabled' : '' ?> 
                                        style="font-size: 11px !important; height: 24px; min-height: 24px; background-color: #fff; width: 100%;">
                                <?php endif; ?>
                            </div>
                            
                            <!-- 2. BÖLÜM: SAĞ KONSOL ALANI (ÇÖP KUTUSU + TUTAR + ÖDE BUTONU)-->
                            <div class="d-flex align-items-center justify-content-end" style="width: 40%; min-width: 40%; gap: 6px;">
                                
                                <?php 
                                $sabit_kelimeler = ['Maaş', 'Mesai', 'Maktu', 'KK', 'Kredi', 'Damacana', 'İçme Suyu', 'Aidat', 'Nergiz Cep'];
                                $is_sabit = false;

                                foreach($sabit_kelimeler as $sk) { 
                                    if(strpos($kalem['kalem_adi'], $sk) !== false) { $is_sabit = true; break; } 
                                }

                                if (strpos(mb_strtolower($kalem['kalem_adi'], 'UTF-8'), 'faturası') !== false && !empty($kalem['havuz_kodu']) && $kalem['havuz_kodu'] === 'vakif_o') {
                                    $is_sabit = true;
                                }

                                // Çöp Kutusu Hizalama Boşluğu (Genişlik: 16px Sabit)
                                if (!$is_sabit && !$is_odendi && !$is_kilitli): ?>
                                    <button type="button" class="btn btn-xs btn-link text-danger p-0 d-inline-flex align-items-center justify-content-center" 
                                            onclick="tekilKalemSil(<?= intval($kalem['id']) ?>)" 
                                            title="Bu kalemi kaldır"
                                            style="width: 16px; height: 16px; min-width: 16px; border: none; background: transparent;">
                                        <i class="bi bi-trash3-fill" style="font-size: 11px; line-height: 1;"></i>
                                    </button>
                                <?php else: ?>
                                    <div style="width: 16px; height: 16px; min-width: 16px;"></div>
                                <?php endif; ?>

                                <!-- 
                                🚀 3. BÖLÜM: TUTAR GÖSTERGE INPUTU  Genişlik: Sağ konsolun içinden tam 110px alan kaplar.
                                -->
                                <input type="number" step="0.01" name="kalemler[<?= $kalem['id'] ?>][tutar]" 
                                    class="form-control form-control-sm text-end text-danger fw-bold font-monospace <?= $input_kapat ? '' : 'border border-secondary-subtle px-2' ?>" 
                                    value="<?= number_format($gosterilecek_tutar, 2, '.', '') ?>" 
                                    <?= $input_kapat ? 'readonly style="background: transparent; font-weight: 700; padding-right: 8px;"' : '' ?> 
                                    style="font-size: 11px !important; width: 110px; min-width: 110px; height: 24px; min-height: 24px;">
                                
                                <!-- 
                                🚀 4. BÖLÜM: ÖDE / ONAY BUTONU
                                Genişlik: Sağ konsolun içinden tam 65px alan kaplar.
                                -->
                                <?php if ($is_odendi || $is_kilitli): ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center fw-bold" disabled style="width: 65px; min-width: 65px; height: 24px; font-size: 10px; padding: 0;">✓</button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm text-white fw-bold shadow-xs d-flex align-items-center justify-content-center ode-btn" 
                                            onclick="giderOdeBitir(<?= intval($kalem['id']) ?>, 'gider_ode', this, <?= number_format($gosterilecek_tutar, 2, '.', '') ?>)"
                                            style="width: 65px; min-width: 65px; height: 24px; font-size: 10px; padding: 0; gap: 3px; border: none;">
                                        <i class="bi bi-credit-card-fill text-white" style="font-size: 9px; line-height: 1;"></i>ÖDE
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>

                    </form>

                    <form id="telefon_damacana_birim_miktar_formu" onsubmit="return false;" style="overflow: hidden; margin: 0; padding: 0; width: 100%;">

                        <!-- 🎯 1. BAĞIMSIZ HÜCRE: TELEFON SAYAÇ VE MÜHÜR İSTASYONU (ORİJİNAL YAPINIZ) -->
                        <?php 
                            $telefon_satir = null;
                            foreach ($giderler as $g_kalem) {
                                if (strpos(mb_strtolower($g_kalem['kalem_adi'], 'UTF-8'), 'telefon') !== false) { 
                                    $telefon_satir = $g_kalem; 
                                    break; 
                                }
                            }
                            if ($telefon_satir):
                                $tel_tutar  = floatval($telefon_satir['tutar']);
                                $tel_odenen = floatval($telefon_satir['odenen_tutar']); 
                                $tel_id     = intval($telefon_satir['id']);
                                
                                $is_birinci_kilitli = ($tel_odenen >= $telefonFaturasiBirimTutari); 
                                $is_ikinci_kilitli  = ($tel_odenen >= $aylikTelefonFaturasi); 

                                // Tutar sütununa dokunulmadığı için ekrandaki kalan fark canlı hesaplanır
                                $telefonFaturasiToplamTutar = max(0, $tel_tutar - $tel_odenen);
                        ?>
                        <div class="d-flex align-items-center border-bottom border-light py-0 row-kalem" data-kalem-id="<?= $tel_id ?>" data-kalem-adi="Telefon" style="height: 30px; min-height: 30px; width: 100%; padding-left: 4px; padding-right: 4px;">
                            
                            <!-- İsim Alanı: Satırın Tam %60'ını Kaplar -->
                            <div style="width: 60%; min-width: 60%;">
                                <input type="text" class="form-control form-control-sm text-dark fw-semibold border border-secondary-subtle px-2" value="<?= htmlspecialchars($telefon_satir['kalem_adi']) ?>" readonly style="background-color: #fff; font-size: 11px !important; height: 24px; min-height: 24px; cursor: default; width: 100%;">
                            </div>
                            
                            <!-- Sağ Konsol Alanı: Satırın Kalan Tam %40'ını Kaplar -->
                            <div class="d-flex align-items-center justify-content-end" style="width: 40%; min-width: 40%; gap: 6px;">
                                <!-- Birim Fiyat Kutusu -->
                                <div class="d-flex align-items-center gap-1 bg-white border border-secondary-subtle rounded px-1" style="height: 24px; width: 48px;">
                                    <span class="text-muted" style="font-size: 8px; padding-left: 2px;">₺</span>
                                    <input type="number" id="telefon-birim-fiyat-<?= $tel_id ?>" value="<?= $telefonFaturasiBirimTutari ?>" class="p-0 text-center fw-bold border-0 bg-transparent text-dark" style="width: 100%; font-size: 9px;" title="Birim Fiyat">
                                </div>
                                
                                <!-- Kalan Bakiye Göstergesi -->
                                <input type="number" step="0.01" class="form-control form-control-sm text-end text-danger fw-bold font-monospace border border-secondary-subtle" value="<?= number_format($telefonFaturasiToplamTutar, 2, '.', '') ?>" readonly style="font-size: 10px !important; width: 60px; min-width: 60px; height: 24px; min-height: 24px; background: transparent; padding-right: 4px;">
                                
                                <!-- Ö ve A Buton İstasyonu (Toplam 65px alan kaplar) -->
                                <div class="btn-group" role="group" style="height: 24px;">
                                    <button type="button" 
                                            class="btn btn-xs fw-bold px-0 border border-secondary-subtle shadow-xs d-flex align-items-center justify-content-center <?= $is_birinci_kilitli ? 'btn-secondary disabled' : 'btn-light' ?>" 
                                            style="font-size:10px; width: 32.5px; height: 24px;" 
                                            onclick="telefonAksiyon(event, <?= $tel_id ?>)" 
                                            <?= $is_birinci_kilitli ? 'disabled' : '' ?>>
                                        <?= $is_birinci_kilitli ? '✓' : 'Ö' ?>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-xs fw-bold px-0 border border-secondary-subtle shadow-xs d-flex align-items-center justify-content-center <?= $is_ikinci_kilitli ? 'btn-secondary disabled' : 'btn-light' ?>" 
                                            style="font-size:10px; width: 32.5px; height: 24px; margin-left: -1px;" 
                                            onclick="telefonAksiyon(event, <?= $tel_id ?>)" 
                                            <?= $is_ikinci_kilitli ? 'disabled' : '' ?>>
                                        <?= $is_ikinci_kilitli ? '✓' : 'A' ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>


                        <!-- 🎯 2. BAĞIMSIZ HÜCRE: TELEFONDAN KOPYALANAN BİREBİR YENİ DAMACANA SU İSTASYONU -->
                        <?php 
                            $damacana_satir = null;
                            foreach ($giderler as $g_kalem) {
                                if (strpos($g_kalem['kalem_adi'], 'Damacana') !== false || strpos($g_kalem['kalem_adi'], 'İçme Suyu') !== false || strpos($g_kalem['kalem_adi'], 'damacana') !== false) { 
                                    $damacana_satir = $g_kalem; 
                                    break; 
                                }
                            }
                            if ($damacana_satir):
                                $damacanaToplamTutar = floatval($damacana_satir['tutar']);
                                $su_id = intval($damacana_satir['id']);
                        ?>
                        <div class="d-flex align-items-center border-bottom border-light py-0 row-kalem" style="height: 30px; min-height: 30px; width: 100%; padding-left: 4px; padding-right: 4px;">
                            
                            <!-- İsim Alanı: Satırın Tam %60'ını Kaplar -->
                            <div style="width: 60%; min-width: 60%;">
                                <input type="text" class="form-control form-control-sm text-dark fw-semibold border border-secondary-subtle px-2" value="<?= htmlspecialchars($damacana_satir['kalem_adi']) ?>" readonly style="background-color: #fff; font-size: 11px !important; height: 24px; min-height: 24px; cursor: default; width: 100%;">
                            </div>
                            
                            <!-- Sağ Konsol Alanı: Satırın Kalan Tam %40'ını Kaplar (Milimetrik Eşitlendi) -->
                            <div class="d-flex align-items-center justify-content-end" style="width: 40%; min-width: 40%; gap: 6px;">
                                <!-- Birim Fiyat Kutusu (Birebir Telefon Detayları: Genişlik 48px, Font 9px) -->
                                <div class="d-flex align-items-center gap-1 bg-white border border-secondary-subtle rounded px-1" style="height: 24px; width: 48px;">
                                    <span class="text-muted" style="font-size: 8px; padding-left: 2px;">₺</span>
                                    <input type="number" id="su-birim-fiyat-<?= $su_id ?>" value="<?= $damacanaSuBirimTutari ?>" class="p-0 text-center fw-bold border-0 bg-transparent text-dark" style="width: 100%; font-size: 9px;" title="Birim Fiyat" oninput="window.monitorGuncelle()">
                                </div>
                                
                                <!-- Kalan Bakiye Göstergesi (Birebir Telefon Detayları: Genişlik 60px, Font 10px, Kenarlıklı border) -->
                                <input type="number" step="0.01" class="form-control form-control-sm text-end text-danger fw-bold font-monospace border border-secondary-subtle" value="<?= number_format($damacanaToplamTutar, 2, '.', '') ?>" readonly style="font-size: 10px !important; width: 60px; min-width: 60px; height: 24px; min-height: 24px; background: transparent; padding-right: 4px;">
                                
                                <!-- +1 ve -1 Sayaç Buton İstasyonu (Birebir Telefon Detayları: Buton Genişlikleri 32.5px, Toplam 65px) -->
                                <div class="btn-group" role="group" style="height: 24px;">
                                    <button type="button" class="btn btn-xs btn-outline-success fw-bold px-0 d-flex align-items-center justify-content-center border border-secondary-subtle shadow-xs" style="font-size:10px; width: 32.5px; height: 24px;" onclick="damacanaAksiyon(event, <?= $su_id ?>, '+1')">+1</button>
                                    <button type="button" class="btn btn-xs btn-warning text-dark fw-bold px-0 d-flex align-items-center justify-content-center border border-secondary-subtle shadow-xs" style="font-size:10px; width: 32.5px; height: 24px; margin-left: -1px;" onclick="damacanaAksiyon(event, <?= $su_id ?>, '-1')" <?= ($damacanaToplamTutar <= 0) ? 'disabled' : '' ?>>-1</button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- 🎯 3. BAĞIMSIZ HÜCRE: %60 - %40 ORANLI YENİ NERGİZ CEP İSTASYONU -->
                        <?php 
                            $nergiz_cep_satir = null;
                            foreach ($giderler as $g_kalem) {
                                if (trim($g_kalem['kalem_adi']) === 'Nergiz Cep') { $nergiz_cep_satir = $g_kalem; break; }
                            }
                            $nergiz_id = $nergiz_cep_satir ? intval($nergiz_cep_satir['id']) : 0;
                            $nergiz_odendi = $nergiz_cep_satir ? (floatval($nergiz_cep_satir['odenen_tutar']) > 0 || intval($nergiz_cep_satir['is_locked'] ?? 0) === 1) : false;
                            $nergiz_tutar = $nergiz_odendi ? 0.00 : 220.00;
                        ?>                   
                        <div class="d-flex align-items-center border-bottom border-light py-0 row-kalem <?= $nergiz_odendi ? 'row-row-odendi' : '' ?>"
                            data-kalem-id="<?= $nergiz_id ?>"
                            data-havuz-kodu="<?= $nergiz_cep_satir ? htmlspecialchars($nergiz_cep_satir['havuz_kodu'] ?? '') : 'nakit' ?>"
                            data-kalem-adi="Nergiz Cep"
                            style="height: 30px; min-height: 30px; width: 100%; padding-left: 4px; padding-right: 4px;">
                            
                            <!-- İsim Alanı: Konsepte Uyumlu, Satırın Tam %60'ını Kaplar -->
                            <div style="width: 60%; min-width: 60%;">
                                <input type="text" class="form-control form-control-sm text-dark fw-semibold border border-secondary-subtle px-2" 
                                    value="Nergiz Cep" readonly 
                                    style="background-color: #fff; font-size: 11px !important; height: 24px; min-height: 24px; cursor: default; width: 100%; <?= $nergiz_odendi ? 'text-decoration: line-through; opacity: 0.6;' : '' ?>">
                            </div>
                            
                            <!-- Sağ Konsol Alanı: Konsepte Uyumlu, Satırın Kalan Tam %40'ını Kaplar -->
                            <div class="d-flex align-items-center justify-content-end" style="width: 40%; min-width: 40%; gap: 6px;">
                                
                                <!-- Hayalet Çöp Kutusu Hizalama Tamponu (Hizanın milimetrik korunması için 16px) -->
                                <div style="width: 16px; min-width: 16px; height: 16px;"></div>

                                <!-- Tutar Gösterge Kutusu (Telefon ve Damacana düzeniyle tam uyumlu: Genişlik 90px, Font 11px) -->
                                <input type="number" step="0.01" id="nergiz-cep-tutar" autocomplete="off" 
                                    class="form-control form-control-sm text-end text-danger fw-bold font-monospace <?= $nergiz_odendi ? '' : 'border border-secondary-subtle px-2' ?>" 
                                    value="<?= number_format($nergiz_tutar, 2, '.', '') ?>" 
                                    <?= $nergiz_odendi ? 'disabled style="background: transparent; font-weight: 700; border: none !important; padding-right: 8px; text-decoration: line-through; opacity: 0.6;"' : 'style="background-color: #fff; font-size: 11px !important; width: 110px; min-width: 110px; height: 24px; min-height: 24px;"' ?>>
                                
                                <!-- 🎯 REFORM: İkonlu, Mavi Renkli ve Tam Beyaz ÖDE/✓ Buton İstasyonu (Genişlik 58px) -->
                                <?php if ($nergiz_odendi): ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center fw-bold" disabled style="width: 65px; min-width: 65px; height: 24px; font-size: 10px; padding: 0;">✓</button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm text-white fw-bold shadow-xs d-flex align-items-center justify-content-center ode-btn" 
                                            onclick="giderOdeBitir(<?= $nergiz_id ?>, 'gider_ode', this, <?= number_format($nergiz_tutar, 2, '.', '') ?>)"
                                            style="width: 65px; min-width: 65px; height: 24px; font-size: 10px; padding: 0; gap: 3px; border: none;">
                                        <i class="bi bi-credit-card-fill text-white" style="font-size: 9px; line-height: 1;"></i>ÖDE
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>

                </div>

                <!-- GİDER KARTI ALT EKLEME İSTASYONU (JS ASENKRON REFORMU - %60-%40 KONSEPT SÜRÜMÜ) -->
                <div class="card-footer bg-light border-top border-light py-0 px-2 d-flex align-items-center" style="height: 34px; min-height: 34px;">
                    <div class="d-flex align-items-center w-100" style="height: 30px; min-height: 30px; padding-left: 4px; padding-right: 4px;">
                        
                        <!-- 
                        🚀 1. BÖLÜM: KALEM ADI GİRİŞ KUTUSU
                        Genişlik: Satırın tam %60'ını kaplar. 
                        🎯 DÜZELTME: Sabit 240px kaldırıldı, width: 100% verilerek bu %60'lık alanı tamamen doldurması sağlandı!
                        -->
                        <div style="width: 60%; min-width: 60%;" class="pe-2">
                            <input type="text" id="yeni-gider-adi" class="form-control form-control-sm text-dark fw-semibold border border-secondary-subtle px-2 input-ekle" placeholder="Gider kalemi adı giriniz.." style="font-size: 11px !important; height: 24px; min-height: 24px; background-color: #fff; width: 100%;">
                        </div>
                        
                        <!-- 
                        🚀 2. BÖLÜM: SAĞ KONSOL ALANI (TUTAR + HAVUZ SEÇİMİ + EKLE BUTONU)
                        Genişlik: Üst listeyle tam paralel olması için kalan %40 alanı kaplar.
                        -->
                        <div class="d-flex align-items-center justify-content-end" style="width: 40%; min-width: 40%; gap: 6px;">
                            
                            <!-- 🎯 DÜZELTME: Tutar giriş kutusunun genişliği, üst listeyle tam hizalanması için 110px yapıldı -->
                            <input type="number" step="0.01" id="yeni-gider-tutar" class="form-control form-control-sm text-end text-danger fw-bold font-monospace border border-secondary-subtle px-2 input-ekle" placeholder="0.00" style="font-size: 11px !important; width: 110px; min-width: 110px; height: 24px; min-height: 24px; background-color: #fff;">
                            
                            <!-- Finansal Havuz Seçimi (Genişlik 110px sabit, yükseklik 24px) -->
                            <select id="yeni-gider-havuz" class="form-select form-select-sm fw-semibold text-secondary border border-secondary-subtle" style="font-size: 11px !important; width: 110px; min-width: 110px; height: 24px; padding: 0 4px; background-color: #fff;">
                                <?php 
                                try {
                                    $havuzSorgu = $db->query("SELECT havuz_kodu, havuz_adi FROM finansal_havuzlar ORDER BY id ASC");
                                    $havuzlar = $havuzSorgu->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($havuzlar as $havuz) {
                                        echo '<option value="' . htmlspecialchars($havuz['havuz_kodu']) . '">' . htmlspecialchars($havuz['havuz_adi']) . '</option>';
                                    }
                                } catch (Exception $e) {
                                    echo '<option value="isbank_a">İşbank (A)</option>';
                                    echo '<option value="vakif_o">Vakıf (Ö)</option>';
                                    echo '<option value="nakit">Nakit</option>';
                                    echo '<option value="isbank_o">İşbank (Ö)</option>';
                                }
                                ?>
                            </select>

                            <!-- 🎯 DÜZELTME: EKLE Butonu üst listedeki ÖDE butonlarıyla tam hizalı olması için 65px genişliğe kilitlendi -->
                            <button type="button" class="btn btn-sm text-white fw-bold shadow-xs d-flex align-items-center justify-content-center text-uppercase" 
                                    style="width: 65px; min-width: 65px; height: 24px; font-size: 10px; padding: 0; gap: 2px; border: none;" 
                                    onclick="ekGiderKalemiEkle(this)">
                                <i class="bi bi-plus-lg text-white" style="font-size: 9px; line-height: 1;"></i>EKLE
                            </button>
                        </div>
                    </div>
                </div>



            </div>
        </div> <!-- ORTA SÜTUN COL-MD-5 KAPANIŞI -->



        <!-- SAĞ SÜTUN: YENİ DAĞITIM MONİTÖRÜ VE ÜÇLÜ KART YAPISI -->
        <div class="col-md-4 monitorstyle" id="monitor-ekrani">


            <!-- 💳 ÜST KART: AKILLI FAİZ HESAPLAMA PANELİ -->
            <div class="card shadow-sm border-0 bg-white mb-2" style="border-radius: 10px;">
                <!-- Canlı Önizleme Göstergeli Kart Başlığı -->
                <div class="card-header bg-white border-bottom border-light py-2 px-3 text-dark d-flex justify-content-between align-items-center" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <span class="fw-bold text-secondary faiz-card-title">
                        <i class="bi bi-percent text-warning me-1 faiz-title-icon"></i>Faiz Hesapla
                    </span>
                    
                    <!-- Dinamik ve Renk Kodlu Canlı Önizleme Rozetleri -->
                    <div class="font-monospace d-flex align-items-center gap-1">
                        <span class="badge-fx border text-secondary">
                            <span class="fx-prefix">#</span> <span id="pre-kredi-input">0,00 ₺</span>
                        </span>
                        <span class="badge-fx border text-secondary">
                            <span class="fx-prefix">A</span> <span id="pre-isbank-a">0,00 ₺</span>
                        </span>
                        <span class="badge-fx border text-secondary">
                            <span class="fx-prefix">Ö</span> <span id="pre-isbank-o">0,00 ₺</span>
                        </span>
                    </div>
                </div>
                
                <div class="card-body p-3">
                    <!-- İlk Satır Grubu: Oran ve Gün Girişleri -->
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-2">
                            <span class="faiz-input-label">Oran %</span>
                        </div>
                        <div class="col-4">
                            <input type="number" id="faiz-orani-input" class="form-control text-center p-1 fw-bold text-dark font-monospace faiz-input-style" style="font-size: 12px;" value="<?= htmlspecialchars($faiz_orani ?? 30) ?>">
                        </div>
                        <div class="col-2 text-end pe-2">
                            <span class="faiz-input-label">Gün</span>
                        </div>
                        <div class="col-4">
                            <input type="number" id="faiz-gun-input" class="form-control text-center p-1 fw-bold text-dark font-monospace faiz-input-style" style="font-size: 12px;" value="<?= htmlspecialchars($faiz_gun_sayisi ?? 0) ?>">
                        </div>
                    </div>

                    <!-- İkinci Satır Grubu: Harici Kredi Faiz Bütçesi Girişi -->
                    <div class="row g-2 align-items-center border-top border-light pt-2 mt-2">
                        <div class="col-4">
                            <span class="faiz-input-label d-flex align-items-center"><i class="bi bi-bank me-1 text-primary"></i>Kredi Bütçesi</span>
                        </div>
                        <div class="col-8">
                            <input type="number" step="0.01" id="faiz-kredi-butcesi-input" class="form-control text-end p-1 fw-bold text-danger font-monospace faiz-input-style" value="<?= htmlspecialchars($evKredisiHam ?? 0) ?>" style="font-size:12px !important;">
                        </div>
                    </div>

                    <!-- Üçüncü Satır Grubu: Aktarım Modalı Tetikleyici Buton -->
                    <div class="d-grid gap-2 mt-3">
                        <button type="button" class="btn btn-sm btn-success fw-bold text-white shadow-sm" style="font-size:11px; border-radius: 6px; letter-spacing: 0.3px;" data-bs-toggle="modal" data-bs-target="#faizAktarSecimModal" id="faiz-aktar-modal-ac-btn">
                            <i class="bi bi-safe2-fill me-1 text-warning"></i> ELDE EDİLEN FAİZLERİ HESAPLARA AKTAR
                        </button>
                    </div>
                </div>
            </div>



            <!-- ==================================================== -->
            <!-- 🎯 FAİZ AKTARIM MODALI -->
            <!-- ==================================================== -->
            <div class="modal fade" id="faizAktarSecimModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header py-2 px-3 border-bottom border-light">
                            <h6 class="modal-title fw-bold text-dark" style="font-size:13px;">
                                <i class="bi bi-sliders text-success me-1"></i> Faiz Aktarım Detayları
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 10px;"></button>
                        </div>
                        <div class="modal-body p-3" style="font-size: 12px;">
                            <form id="faiz-aktar-secimli-form" onsubmit="return false;">
                                
                                <!-- Hesap Seçimi -->
                                <div class="mb-3">
                                    <label class="form-label text-secondary fw-semibold mb-1">Kaynak / Hedef Hesap Seçimi</label>
                                    <select class="form-select form-select-sm fw-bold text-dark" id="modal-aktar-havuz-secim" name="havuz_kodu">
                                        <option value="" selected disabled>-- Hesap Seçiniz --</option>
                                        <option value="isbank_a">İşbank (A)</option>
                                        <option value="isbank_o">İşbank (Ö)</option>
                                        <option value="all">Tümünü Aktar (A + Ö)</option>
                                    </select>
                                </div>

                                <!-- Esnek Tutar Alanı -->
                                <div class="mb-3">
                                    <label class="form-label text-secondary fw-semibold mb-1">Aktarılacak Faiz Tutarı (₺)</label>
                                    <input type="number" step="0.01" min="0.01" class="form-control form-control-sm text-end fw-bold text-success font-monospace" id="modal-aktar-tutar-input" name="aktarilacak_tutar" placeholder="0.00" disabled>
                                    <div class="form-text text-muted text-end" style="font-size: 9px;" id="modal-aktar-uyari-metni">Lütfen önce bir hesap seçin.</div>
                                </div>

                                <!-- Kesin Onay Butonu -->
                                <div class="d-grid mt-2">
                                    <button type="button" id="kesin-faiz-aktar-onay-btn" class="btn btn-sm btn-success fw-bold text-white" disabled>
                                        <i class="bi bi-check-circle me-1"></i> AKTARIMI ONAYLA VE MÜHÜRLE
                                    </button>
                                </div>

                            </form>
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
                    <?php
                        try {
                            // Canlı anapara ve veritabanına kaydettiğimiz güncel nemaları tek sorguda çekiyoruz
                            $canliHavuzlarSorgu = $db->query("SELECT havuz_kodu, anapara, nema FROM finansal_havuzlar");
                            $canliHavuzlar = $canliHavuzlarSorgu->fetchAll(PDO::FETCH_ASSOC);
                            
                            $dbHavuz = [];
                            $dbNema  = [];
                            foreach ($canliHavuzlar as $ch) {
                                $hk = trim($ch['havuz_kodu']);
                                $dbHavuz[$hk] = floatval($ch['anapara']);
                                $dbNema[$hk]  = floatval($ch['nema'] ?? 0.00); // 🎯 REFORM: Veritabanındaki canlı nemalar çekiliyor!
                            }
                        } catch (Exception $e) {
                            $dbHavuz = ['isbank_o' => 0, 'isbank_a' => 0, 'vakif_o' => 0, 'nakit' => 0];
                            $dbNema  = ['isbank_o' => 0, 'isbank_a' => 0, 'vakif_o' => 0, 'nakit' => 0];
                        }

                        // Veritabanındaki nema sütunundan gelen kesin canlı kazançlar
                        $nema_isbank_o = $dbNema['isbank_o'] ?? 0.00;
                        $nema_isbank_a = $dbNema['isbank_a'] ?? 0.00;

                        $zorunlu_fatura_bloke_tutari = $zorunlu_fatura_bloke_tutari ?? 0;
                        $zorunlu_su_bloke_tutari     = $zorunlu_su_bloke_tutari ?? 0;

                        // Veritabanından gelen hilesiz, kesin canlı anapara değerleri
                        $ana_isbank_o = max(0, $dbHavuz['isbank_o'] ?? 0);
                        $ana_isbank_a = max(0, $dbHavuz['isbank_a'] ?? 0);
                        $ana_vakif_o  = max(0, $dbHavuz['vakif_o'] ?? 0);
                        $ana_nakit_ev = max(0, $dbHavuz['nakit'] ?? 0);
                        
                        $isbank_o_bakiye = $ana_isbank_o + $nema_isbank_o;
                        $isbank_a_bakiye = $ana_isbank_a + $nema_isbank_a;
                    ?>

                    <!-- 1. SATIR: İŞBANKASI (Ö) & İŞBANKASI (A) -->
                    <div class="row g-2 text-center mb-2">
                        <!-- KART 1: İŞ BANKASI (Ö) -->
                        <div class="col-6">
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #f8f9fa; min-height: 90px; cursor: pointer;" onclick="akilliTransferModaliAc('isbank_o')">
                                <span class="d-block text-uppercase fw-bold text-secondary" style="font-size: 9px !important; line-height: 1.2;">İş Bankası (Ö)<br><span class="text-muted fw-normal" style="font-size: 8px;">Kredi Bütçesi</span></span>
                                <span class="d-block text-muted mt-1" style="font-size: 8px; line-height: 1.1;">anapara: <span id="isbank_o_anapara" class="fw-semibold text-dark"><?= number_format($ana_isbank_o, 2, ',', '.') ?> ₺</span></span>
                                <span id="isbank_o_bakiye" class="fw-bold d-block font-monospace text-secondary" style="font-size: 11px !important; line-height: 1.3;">
                                    <?= number_format($isbank_o_bakiye, 2, ',', '.') ?> ₺
                                </span>
                                <span class="d-block text-muted mt-1" style="font-size: 8px; line-height: 1.1;">Nema: <span id="isbank_o_nema" class="text-success fw-bold">+<?= number_format($nema_isbank_o, 2, ',', '.') ?> ₺</span></span>
                            </div>
                        </div>

                        <!-- KART 2: İŞBANKASI (A) -->
                        <div class="col-6">
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #e1f5fe; min-height: 90px; cursor: pointer;" onclick="akilliTransferModaliAc('isbank_a')">
                                <span class="d-block text-uppercase fw-bold text-primary" style="font-size: 9px !important; line-height: 1.2;">İşbankası (A)<br><span class="text-muted fw-normal" style="font-size: 8px;">Yatırım Ana Havuz</span></span>
                                <span class="d-block text-muted mt-1" style="font-size: 8px; line-height: 1.1;">anapara: <span id="isbank_a_anapara" class="fw-semibold text-dark"><?= number_format($ana_isbank_a, 2, ',', '.') ?> ₺</span></span>
                                <span id="isbank_a_bakiye" class="fw-bold d-block font-monospace text-primary" style="font-size: 11px !important; line-height: 1.3;">
                                    <?= number_format($isbank_a_bakiye, 2, ',', '.') ?> ₺
                                </span>
                                <span class="d-block text-muted mt-1" style="font-size: 8px; line-height: 1.1;">Nema: <span id="isbank_a_nema" class="text-success fw-bold">+<?= number_format($nema_isbank_a, 2, ',', '.') ?> ₺</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. SATIR: VAKIFBANK (Ö) & NAKIT (EV) -->
                    <div class="row g-2 text-center mb-2">
                        <!-- KART 3: VAKIFBANK (Ö) -->
                        <div class="col-6">
                            <!-- Vakıfbank Ö kartına da tıklama ekledik -->
                            <div class="p-2 border rounded shadow-xs text-start" style="background-color: #fff9e6; min-height: 90px; cursor: pointer;" onclick="akilliTransferModaliAc('vakif_o')">
                                <span class="d-block text-uppercase fw-bold text-warning-emphasis" style="font-size: 9px !important; line-height: 1.2;">Vakıfbank (Ö)<br><span class="text-muted fw-normal" style="font-size: 8px;">Bloke: <?= number_format($zorunlu_fatura_bloke_tutari, 2, ',', '.') ?> ₺</span></span>
                                <span class="d-block text-muted mt-1" style="font-size: 8px; line-height: 1.1;">anapara: <span id="vakif_o_anapara" class="fw-semibold text-dark"><?= number_format($ana_vakif_o, 2, ',', '.') ?> ₺</span></span>
                                <span id="vakif_o_bakiye" class="fw-bold d-block font-monospace text-warning-emphasis" style="font-size: 11px !important; line-height: 1.3;">
                                    <?= number_format($ana_vakif_o, 2, ',', '.') ?> ₺
                                </span>
                                <span class="d-block text-muted mt-1" style="font-size: 8px; line-height: 1.1;">Nema: <span id="vakif_o_nema" class="text-muted fw-semibold">0,00 ₺</span></span>
                            </div>
                        </div>

                        <!-- KART 4: NAKİT (EV) -->
                        <div class="col-6">
                            <div class="p-2 border rounded shadow-xs text-start position-relative" style="background-color: #e8f5e9; min-height: 90px; cursor: pointer;" onclick="akilliTransferModaliAc('nakit')">                               
                                <span class="d-block text-uppercase fw-bold text-success" style="font-size: 9px !important; line-height: 1.2; padding-right: 15px;">Nakit (Ev)<br><span class="text-muted fw-normal" style="font-size: 8px;">Bloke: <?= number_format($zorunlu_su_bloke_tutari, 2, ',', '.') ?> ₺</span></span>
                                <span class="d-block text-muted mt-1" style="font-size: 8px; line-height: 1.1;">anapara: <span id="nakit_anapara" class="fw-semibold text-dark"><?= number_format($ana_nakit_ev, 2, ',', '.') ?> ₺</span></span>
                                <span id="nakit_bakiye" class="fw-bold d-block mt-1 font-monospace text-success" style="font-size: 11px !important; line-height: 1.3;">
                                    <?= number_format($ana_nakit_ev, 2, ',', '.') ?> ₺
                                </span>
                                <span class="d-block text-muted mt-1" style="font-size: 8px; line-height: 1.1;">Nema: <span id="nakit_nema" class="text-muted fw-semibold">0,00 ₺</span></span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ================================================================================= -->
                <!-- 📊 SAĞ SÜTUNLA UYUMLU SAF JS TABANLI ZENGİNLEŞTİRİLMİŞ NİHAİ DASHBOARD -->
                <!-- ================================================================================= -->

                <!-- 💜 CARD 1: TASARRUF (Yatırım Yapılacak Hacim) -->
                <div class="card shadow-sm border-0 bg-white mb-2">
                    <div class="card-body p-2" style="background-color: #fdfafd; border-left: 3.5px solid #8e24aa; border-radius: 4px; cursor: pointer;" onclick="forexAction('isbank_k')">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 8.5px; letter-spacing: 0.5px;">💰 TASARRUF (YATIRIM HACMİ)</span>
                                <span class="text-muted" style="font-size: 8px;">İşbank (A) Ana Havuz Kilitli</span>
                            </div>
                            <!-- 🎯 REFORM: data-anapara özniteliğiyle veritabanı canlı verisi mühürlendi -->
                            <h5 id="canli-ana-yatirim-bakiye" data-anapara="<?= $tasarruf_tutari ?>" class="fw-bold mb-0 font-monospace" style="color: #6a1b9a; font-size: 13.5px;">0,00 ₺</h5>
                        </div>
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
                            <!-- 🎯 REFORM: 4 Havuzun toplam anapara ve canlı nemaları JS'e görünmez data ile paslanıyor -->
                            <div class="col-6 border-end border-light text-start ps-1" 
                                id="data_havuz_matrahlari"
                                data-isbank-a="<?= $ana_isbank_a_anapara ?>"
                                data-isbank-o="<?= $ana_isbank_o_anapara ?>"
                                data-vakif-o="<?= $ana_vakif_o_anapara ?>"
                                data-nakit="<?= $ana_nakit_anapara ?>"
                                data-nema-a="<?= $canli_isbank_a_nema ?>"
                                data-nema-o="<?= $canli_isbank_o_nema ?>">
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
                                <span id="js_yeni_faturalar_bloke" data-taban="<?= $bloke_faturalar_taban ?>" class="fw-bold text-primary font-monospace" style="font-size: 9.5px;">0,00 ₺</span>
                            </div>
                            <div class="col-4 border-end border-light px-1">
                                <span class="text-muted d-block" style="font-size: 7.5px; font-weight: 500;">AİDAT / BLOKE SU</span>
                                <span id="js_dashboard_gider_su" data-taban="<?= $bloke_su_aidat_taban ?>" class="fw-bold font-monospace text-danger-emphasis" style="font-size: 9.5px;">0,00 ₺</span>
                            </div>
                            <div class="col-4 text-end pe-1">
                                <span class="text-muted d-block" style="font-size: 7.5px; font-weight: 500;">KREDİ (İŞBANK Ö BLOKE)</span>
                                <span id="js_dashboard_gider_kredi" data-taban="<?= $bloke_kredi_taban ?>" class="fw-bold font-monospace text-dark" style="font-size: 9.5px;">0,00 ₺</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🧡 CARD 3: KREDİ KARTI RISK ANALİZİ -->
                <div class="card shadow-sm border-0 bg-white mb-2">
                    <div class="card-body p-2" style="background-color: #fff9f6; border-left: 3.5px solid #fd7e14; border-radius: 4px;">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-1 mb-1">
                            <span class="fw-bold text-uppercase" style="color: #d9480f; font-size: 8.5px; letter-spacing: 0.5px;">💳 KK DÖNEM BORCU (+PROVİZYON)</span>
                            <span id="js_yeni_kk_donem_borcu" class="fw-bold font-monospace" style="color: #d9480f; font-size: 13.5px;">0,00 ₺</span>
                        </div>
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

                <!-- 💙 CARD: KREDİ KARTI TOPLAM BORÇ MATRİSİ -->
                <div class="card shadow-sm border-0 bg-white mb-2">
                    <div class="card-body p-2" style="background-color: #e3f2fd; border-left: 3.5px solid #1565c0; border-radius: 4px;">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-1 mb-1">
                            <span class="fw-bold text-uppercase" style="color: #0d47a1; font-size: 8.5px; letter-spacing: 0.5px;">💳 KK TOPLAM BORÇ</span>
                            <span id="js_dashboard_kk_toplam_borclari" class="fw-bold font-monospace" style="color: #0d47a1; font-size: 13.5px;">0,00 ₺</span>
                        </div>
                        <div class="row g-0 text-center" style="font-size: 9px; line-height: 1.2;">
                            <div class="col-4 border-end border-light text-start ps-1">
                                <span class="text-muted d-block" style="font-size: 7.5px;">KESINLEŞEN EKSTRELER</span>
                                <span id="js_dashboard_kesinlesen_ekstre" class="fw-bold text-danger font-monospace">0,00 ₺</span>
                            </div>
                            <div class="col-2 border-end border-light px-0.5">
                                <span class="text-muted d-block" style="font-size: 7px;">İŞBANKASI K.EKSTRE</span>
                                <span id="js_dashboard_isbank_kekstre" class="fw-bold font-monospace">0,00 ₺</span>
                            </div>
                            <div class="col-2 border-end border-light px-0.5">
                                <span class="text-muted d-block" style="font-size: 7px;">VAKIFBANK K.EKSTRE</span>
                                <span id="js_dashboard_vakif_kekstre" class="fw-bold font-monospace">0,00 ₺</span>
                            </div>
                            <div class="col-4 text-end pe-1">
                                <span class="text-muted d-block" style="font-size: 7.5px;">KALAN TAKSİTLER TOPLAMI</span>
                                <span id="js_yeni_kk_borcumuz" class="fw-bold text-danger font-monospace">0,00 ₺</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🎯 CARD 5: HARİCİ İNTERAKTİF TO-DO GÖREV GEREKSİNİMLERİ -->
                <div class="card shadow-sm border-0 bg-white mb-2">
                    <div class="card-header bg-white border-bottom border-light py-1.5 px-2">
                        <span class="text-dark fw-bold text-uppercase d-block" style="font-size: 9px; letter-spacing: 0.5px;">
                            <i class="bi bi-clipboard-check-fill text-secondary me-1"></i> 📜 Yapılması Gereken Transferler (To-Do)
                        </span>
                    </div>
                    <div class="card-body p-1" style="background-color: #fafbfc;">
                        <div class="d-flex flex-column gap-1">

                            <!-- 📌 Görev 2 (C2) -->
                            <div class="todo-item p-2 border rounded bg-white" id="todo_c2" style="line-height: 1.25;">
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
                                    <div>İŞBANKASI (Ö) vadeli hesabındaki <span id="todo_c4_tutar1" class="fw-bold text-danger">0,00 ₺</span> vadesize aktar kredi borcunu öde. Kalan <span id="todo_c4_tutar2" class="fw-bold text-dark">0,00 ₺</span> nema tutarını Vakıfbank (Ö)'ye aktar.</div>
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

  

<!--  
<div class="row g-3 mt-2 mb-4 excel-dashboard-wrapper">
    <div class="col-12">
        <div class="card excel-ana-kart bg-white">
            

            <div class="card-header excel-baslik text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold d-inline-flex align-items-center excel-baslik-metin">
                    <i class="fas fa-th text-success me-2 excel-baslik-ikon"></i> Finansal Dönemler Arşiv Raporu (Excel Görünümü)
                </span>
                <span class="badge excel-baslik-rozet font-monospace">Kümülatif Otomatik Senkron</span>
            </div>

            <div class="card-body p-2 bg-temiz-taban">
                <div class="table-responsive excel-temiz-responsive">
                    <table class="table table-sm table-bordered align-middle text-center mb-0 excel-table">
                        <thead>
                            

                            <?php 
                                $yillik_butun_gelirler_toplami = $tepe_toplamlar['maas_o'] + $tepe_toplamlar['maas_a'] + $tepe_toplamlar['maktu_mesai'] + $tepe_toplamlar['ek_gelir'] + $tepe_toplamlar['faiz_gelir'];
                                $yillik_butun_giderler_toplami = $tepe_toplamlar['kredi_karti_o'] + $tepe_toplamlar['kredi_karti_a'] + $tepe_toplamlar['ev_kredisi'] + $tepe_toplamlar['aidat'] + $tepe_toplamlar['harcama_nakit'] + $tepe_toplamlar['fatura_nakit'];
                                $yillik_butun_tasarruf_toplami = $yillik_butun_gelirler_toplami - $yillik_butun_giderler_toplami;
                                $genel_tasarruf_toplami = $yillik_butun_gelirler_toplami - $yillik_butun_giderler_toplami + $tepe_toplamlar['devir_bakiye'];
                                $yillik_butun_yatirim_toplami = $tepe_toplamlar['yatirim_tutari'] ?? 0;
                                $genel_yatirim_toplami = $yillik_butun_yatirim_toplami + $tepe_toplamlar['devir_yatirim'];
                            ?>
                            <tr class="excel-blok-toplam-satir">
                                <th class="excel-group-tasarruf-th text-center"></th>
                                <th colspan="5" class="excel-group-gelir-th text-center"><?= number_format($yillik_butun_gelirler_toplami, 2, ',', '.') ?> ₺</th>
                                <th colspan="5" class="excel-group-gider-th text-center"><?= number_format($yillik_butun_giderler_toplami, 2, ',', '.') ?> ₺</th>
                                <th colspan="6" class="excel-group-nakit-th text-center"><?= number_format($tepe_toplamlar['harcama_nakit'], 2, ',', '.') ?> ₺</th>
                                <th class="excel-group-tasarruf-th text-center"><?= number_format($genel_tasarruf_toplami, 2, ',', '.') ?> ₺</th>
                                <th class="excel-group-yatirim-th text-center"><?= number_format($genel_yatirim_toplami, 2, ',', '.') ?> ₺</th>
                            </tr>


                            <tr class="fw-bold text-uppercase excel-ana-grup-satir">
                                <th rowspan="1" class="excel-group-tasarruf-th"><?= $aktif_yil - 1 . " - DEVİR"?></th>
                                <th colspan="5" class="excel-group-gelir-th">GELİRLER TOPLAMI</th>
                                <th colspan="5" class="excel-group-gider-th">GİDERLER TOPLAMI</th>
                                <th colspan="6" class="excel-group-nakit-th">FATURALAR TOPLAMI</th>
                                <th rowspan="1" class="excel-group-tasarruf-th">Devir: <?= number_format( $tepe_toplamlar['devir_bakiye'], 2, ',', '.') ?> ₺</th>
                                <th rowspan="1" class="excel-group-yatirim-th">Devir: <?= number_format( $tepe_toplamlar['devir_yatirim'], 2, ',', '.') ?> ₺</th>
                            </tr>
                            

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
                                <th class="excel-group-tasarruf-th">TASARRUF</th>
                                <th class="excel-group-yatirim-th">YATIRIM</th>
                            </tr>



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
                                <td class="excel-group-tasarruf-th"><?= number_format($yillik_butun_tasarruf_toplami, 2, ',', '.') ?> ₺</td>
                                <td class="excel-group-yatirim-th"><?= number_format($yillik_butun_yatirim_toplami, 2, ',', '.') ?> ₺</td>
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
                                    <td class="text-end font-monospace text-success pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['maas_o'] ?? 0) > 0 ? number_format($s_veri['maas_o'], 2, ',', '.') . ' ₺' : '' ?></td>
                                    <td class="text-end font-monospace text-success pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['maas_a'] ?? 0) > 0 ? number_format($s_veri['maas_a'], 2, ',', '.') . ' ₺' : '' ?></td>
                                    <td class="text-end font-monospace text-success pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['maktu_mesai'] ?? 0) > 0 ? number_format($s_veri['maktu_mesai'], 2, ',', '.') . ' ₺' : '' ?></td>
                                    <td class="text-end font-monospace text-success pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['ek_gelir'] ?? 0) > 0 ? number_format($s_veri['ek_gelir'], 2, ',', '.') . ' ₺' : '' ?></td>
                                    <td class="text-end font-monospace text-success pe-2 excel-v-ortali excel-govde-faiz-baskisi"><?= $s_canli_faiz > 0 ? number_format($s_canli_faiz, 2, ',', '.') . ' ₺' : '' ?></td>
                                    
                                    <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['kredi_karti_o'] ?? 0) > 0 ? number_format($s_veri['kredi_karti_o'], 2, ',', '.') . ' ₺' : '' ?></td>
                                    <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['kredi_karti_a'] ?? 0) > 0 ? number_format($s_veri['kredi_karti_a'], 2, ',', '.') . ' ₺' : '' ?></td>
                                    <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['ev_kredisi'] ?? 0) > 0 ? number_format($s_veri['ev_kredisi'], 2, ',', '.') . ' ₺' : '' ?></td>
                                    <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['aidat'] ?? 0) > 0 ? number_format($s_veri['aidat'], 2, ',', '.') . ' ₺' : '' ?></td>
                                    <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['harcama_nakit'] ?? 0) > 0 ? number_format($s_veri['harcama_nakit'], 2, ',', '.') . ' ₺' : '' ?></td>

                                    <td class="text-end font-monospace text-dark fw-bold text-center excel-fatura-nakit-govde-hucre"><?= $satir_var_mi && $s_fatura_nakit_toplami > 0 ? number_format($s_fatura_nakit_toplami, 2, ',', '.') . ' ₺' : '0,00 ₺' ?></td>
                                    
                                    <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['elektrik'] ?? 0) > 0 ? number_format($s_veri['elektrik'], 2, ',', '.') . ' ₺' : '' ?></td>
                                    <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['su'] ?? 0) > 0 ? number_format($s_veri['su'], 2, ',', '.') . ' ₺' : '' ?></td>
                                    <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['dogalgaz'] ?? 0) > 0 ? number_format($s_veri['dogalgaz'], 2, ',', '.') . ' ₺' : '' ?></td>
                                    <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['telefon'] ?? 0) > 0 ? number_format($s_veri['telefon'], 2, ',', '.') . ' ₺' : '' ?></td>
                                    <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['internet'] ?? 0) > 0 ? number_format($s_veri['internet'], 2, ',', '.') . ' ₺' : '' ?></td>
                                    
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
-->

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
            <!-- <div class="input-group input-group-sm">
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
            </div> -->
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
                    <span class="badge excel-baslik-rozet font-monospace"><?= number_format(($yillik_toplam['genel_tasarruf']-$yillik_toplam['genel_yatirim'])+($kumulatif_net_tasarruf-$kumulatif_net_yatirim), 2, ',', '.') ?> ₺</span>
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
                                    <th colspan="6" class="excel-group-ktasarruf text-center"><?= number_format($kumulatif_net_tasarruf + $yillik_toplam['butun_tasarruf'] - $kumulatif_net_yatirim - $yillik_toplam['butun_yatirim'], 2, ',', '.') ?> ₺</th>

                                </tr>
                                
                                <!-- SATIR 1: EXCEL ÜST BÜTÜNLEŞİK BLOK TOPLAMLARI (PHP Hesaplamaları) -->
                                <tr class="excel-blok-toplam-satir">
                                    <th class="excel-group-ktasarruf-th text-center"></th>
                                    <th colspan="5" class="excel-group-gelir-th text-center"><?= number_format($yillik_toplam['butun_gelir'], 2, ',', '.') ?> ₺</th>
                                    <th colspan="5" class="excel-group-gider-th text-center"><?= number_format($yillik_toplam['butun_gider'], 2, ',', '.') ?> ₺</th>
                                    <th colspan="6" class="excel-group-ktasarruf text-end"><?= $aktif_yil . " Dönem Sonu İtibariyle Tasarruf/Yatırım Kümülatif Toplamı" ?></th>
                                    <th class="excel-group-ktasarruf-th text-center"><?= number_format($kumulatif_net_tasarruf + $yillik_toplam['butun_tasarruf'], 2, ',', '.') ?> ₺</th>
                                    <th class="excel-group-kyatirim-th text-center"><?= number_format($kumulatif_net_yatirim + $yillik_toplam['butun_yatirim'], 2, ',', '.') ?> ₺</th>
                                </tr>

                                <!-- SATIR 2: ANA GRUP BAŞLIKLARI -->
                                <tr class="fw-bold text-uppercase excel-ana-grup-satir">
                                    <th rowspan="1" class="excel-group-ktasarruf-th"></th>
                                    <th colspan="5" class="excel-group-gelir-th">GELİRLER TOPLAMI</th>
                                    <th colspan="5" class="excel-group-gider-th">GİDERLER TOPLAMI</th>
                                    <th colspan="6" class="excel-group-ktasarruf-th text-end"><?=$aktif_yil - 1 . " ve Öncesi Dönem Tasarruf/Yatırım Verileri (Kümülatif)" ?></th>
                                    <th class="excel-group-tasarruf-th text-center"><?= number_format($kumulatif_net_tasarruf, 2, ',', '.') ?> ₺</th>
                                    <th class="excel-group-yatirim-th text-center"><?= number_format($kumulatif_net_yatirim, 2, ',', '.') ?> ₺</th>
                                </tr>
                                <tr class="tepe-kutu text-end font-monospace fw-bold excel-mikro-toplam-satir">
                                    <td class="text-center"></td>
                                    <td colspan="5" class="text-center"></td>
                                    <td colspan="5" class="text-center"></td>
                                    <td colspan="6" class="excel-group-tasarruf-th text-end"><?= $aktif_yil - 1 . " Yılı Devir"?></td>
                                    <td class="excel-group-tasarruf-th"><?= number_format($kumulatif_net_tasarruf-$kumulatif_net_yatirim, 2, ',', '.') ?> ₺</td>
                                    <td class="excel-group-yatirim-th">-</td>
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
                                    $is_aktif_ay = ($aktif_donem === sprintf("%d-%02d", $aktif_yil, $i));

                                    $s_canli_faiz = $satir_var_mi ? floatval($s_veri['faiz_gelir'] ?? 0) : 0;

                                    $s_fatura_nakit_toplami = $satir_var_mi
                                        ? floatval($s_veri['elektrik'] ?? 0)
                                        + floatval($s_veri['su'] ?? 0)
                                        + floatval($s_veri['dogalgaz'] ?? 0)
                                        + floatval($s_veri['telefon'] ?? 0)
                                        + floatval($s_veri['internet'] ?? 0)
                                        : 0;

                                    $s_gelir = $satir_var_mi
                                        ? floatval($s_veri['maas_o'] ?? 0)
                                        + floatval($s_veri['maas_a'] ?? 0)
                                        + floatval($s_veri['maktu_mesai'] ?? 0)
                                        + floatval($s_veri['ek_gelir'] ?? 0)
                                        + $s_canli_faiz
                                        : 0;

                                    $s_gider = $satir_var_mi
                                        ? floatval($s_veri['kredi_karti_o'] ?? 0)
                                        + floatval($s_veri['kredi_karti_a'] ?? 0)
                                        + floatval($s_veri['ev_kredisi'] ?? 0)
                                        + floatval($s_veri['aidat'] ?? 0)
                                        + floatval($s_veri['harcama_nakit'] ?? 0)
                                        + $s_fatura_nakit_toplami
                                        : 0;

                                    $s_tasarruf = $s_gelir - $s_gider;
                                    $s_yatirim = $satir_var_mi ? floatval($s_veri['yatirim_tutari'] ?? 0) : 0;

                                    $t_color_class = $s_tasarruf >= 0 ? 'excel-pozitif-tutar' : 'excel-negatif-tutar';
                                    $y_color_class = $s_yatirim >= 0 ? 'excel-mor-tutar' : 'excel-negatif-tutar';
                                    $satir_sinifi = $is_aktif_ay ? 'excel-aktif-satir' : 'excel-standart-satir';
                                ?>
                                <tr class="border-bottom border-light excel-govde-satir <?= $satir_sinifi ?>"></td>
                                    <td class="text-start ps-2 fw-bold text-secondary excel-donem-baslik-hucre">
                                        <?= $ay_isimleri[$i] ?> <?= $aktif_yil ?>
                                    </td>

                                    <td class="text-end font-monospace text-success pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['maas_o'] ?? 0) > 0 ? number_format($s_veri['maas_o'],2,',','.').' ₺' : '0,00 ₺' ?></td>
                                    <td class="text-end font-monospace text-success pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['maas_a'] ?? 0) > 0 ? number_format($s_veri['maas_a'],2,',','.').' ₺' : '0,00 ₺' ?></td>
                                    <td class="text-end font-monospace text-success pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['maktu_mesai'] ?? 0) > 0 ? number_format($s_veri['maktu_mesai'],2,',','.').' ₺' : '0,00 ₺' ?></td>
                                    <td class="text-end font-monospace text-success pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['ek_gelir'] ?? 0) > 0 ? number_format($s_veri['ek_gelir'],2,',','.').' ₺' : '0,00 ₺' ?></td>
                                    <td class="text-end font-monospace text-success pe-2 excel-v-ortali excel-govde-faiz-baskisi"><?= $s_canli_faiz > 0 ? number_format($s_canli_faiz,2,',','.').' ₺' : '0,00 ₺' ?></td>

                                    <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['kredi_karti_o'] ?? 0) > 0 ? number_format($s_veri['kredi_karti_o'],2,',','.').' ₺' : '0,00 ₺' ?></td>
                                    <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['kredi_karti_a'] ?? 0) > 0 ? number_format($s_veri['kredi_karti_a'],2,',','.').' ₺' : '0,00 ₺' ?></td>
                                    <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['ev_kredisi'] ?? 0) > 0 ? number_format($s_veri['ev_kredisi'],2,',','.').' ₺' : '0,00 ₺' ?></td>
                                    <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['aidat'] ?? 0) > 0 ? number_format($s_veri['aidat'],2,',','.').' ₺' : '0,00 ₺' ?></td>
                                    <td class="text-end font-monospace text-danger pe-2 excel-v-ortali"><?= $satir_var_mi && floatval($s_veri['harcama_nakit'] ?? 0) > 0 ? number_format($s_veri['harcama_nakit'],2,',','.').' ₺' : '0,00 ₺' ?></td>

                                    <td class="text-end font-monospace text-dark fw-bold text-center excel-fatura-nakit-govde-hucre"><?= $s_fatura_nakit_toplami > 0 ? number_format($s_fatura_nakit_toplami,2,',','.').' ₺' : '0,00 ₺' ?></td>

                                    <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['elektrik'] ?? 0) > 0 ? number_format($s_veri['elektrik'],2,',','.').' ₺' : '0,00 ₺' ?></td>
                                    <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['su'] ?? 0) > 0 ? number_format($s_veri['su'],2,',','.').' ₺' : '0,00 ₺' ?></td>
                                    <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['dogalgaz'] ?? 0) > 0 ? number_format($s_veri['dogalgaz'],2,',','.').' ₺' : '0,00 ₺' ?></td>
                                    <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['telefon'] ?? 0) > 0 ? number_format($s_veri['telefon'],2,',','.').' ₺' : '0,00 ₺' ?></td>
                                    <td class="text-end font-monospace text-muted pe-1 excel-fatura-detay-hucre"><?= $satir_var_mi && floatval($s_veri['internet'] ?? 0) > 0 ? number_format($s_veri['internet'],2,',','.').' ₺' : '0,00 ₺' ?></td>

                                    <td class="text-end font-monospace fw-bold pe-2 excel-karsilastirma-hucre <?= $t_color_class ?>">
                                        <?= $satir_var_mi && $s_gelir > 0 ? number_format($s_tasarruf,2,',','.').' ₺' : '0,00 ₺' ?>
                                    </td>

                                    <td class="text-end font-monospace fw-bold pe-2 excel-yatirim-hucre <?= $y_color_class ?>">
                                        <?= $satir_var_mi && $s_gelir > 0 ? number_format($s_yatirim,2,',','.').' ₺' : '0,00 ₺' ?>
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












</div> <!-- Container Kapanışı -->




<!-- AKILLI FİNANSAL TRANSFER MODALIDIR (DÜZ PASTEL & MİLİMETRİK HİZALAMA) -->
<div class="modal fade" id="akilliTransferModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="akilliTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="font-size: 11px; border-radius: 6px; overflow: hidden; background-color: #ffffff;">
            
            <!-- Düz Pastel Koyu Başlık -->
            <div class="modal-header text-white p-3 border-0 d-flex align-items-center justify-content-between" style="background-color: #2b303a;">
                <h6 class="modal-title m-0 fw-bold d-inline-flex align-items-center" id="akilliTransferModalLabel" style="font-size: 12px; line-height: 1;">
                    <i class="fas fa-exchange-alt me-2 text-warning" style="font-size: 13px;"></i> Mevduat Transfer Ekranı
                </h6>
                <button type="button" class="btn-close btn-close-white shadow-none m-0 p-1" data-bs-dismiss="modal" aria-label="Close" style="font-size: 8px; line-height: 1;"></button>
            </div>
            
            <div class="modal-body p-3 text-start" style="background-color: #f8f9fa;">
                
                <!-- Kaynak Kartı - Hafif Pastel Mavi (Tasarımı Korundu) -->
                <div class="p-3 mb-3 rounded-2 border-0" style="background-color: #e3f2fd; border-left: 4px solid #2196f3 !important;">
                    <span class="fw-bold text-secondary text-uppercase d-block mb-1" id="transferKaynakMetni" style="letter-spacing: 0.5px; font-size: 10px;">KAYNAK HESAP: -</span>
                    <span class="fs-6 fw-bold font-monospace" id="transferKaynakBakiyeMetni" style="color: #1e88e5;">Bakiye: 0,00 ₺</span>
                </div>

                <!-- Adım 1: Hedef ve Tutar Girişi -->
                <div id="transferAdim1">
                    
                    <!-- Hedef Havuz Seçimi -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary mb-1 d-inline-flex align-items-center" style="line-height: 1;">
                            <i class="fas fa-sign-in-alt me-1 text-success" style="font-size: 11px;"></i> Hesap Seçimi
                        </label>
                        <select class="form-select form-select-sm shadow-none p-2 border-0" id="transferHedefHavuz" style="font-size: 11px; background-color: #eaeded; border-radius: 4px; color: #495057; font-weight: 500;">
                            <!-- Dinamik olarak JS ile doldurulacak -->
                        </select>
                    </div>

                    <!-- Tutar Girişi -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary mb-1 d-inline-flex align-items-center" style="line-height: 1;">
                            <i class="fas fa-money-bill-wave me-1 text-primary" style="font-size: 11px;"></i> Transfer Edilecek Tutar (₺)
                        </label>
                        <input type="text" class="form-control form-control-sm shadow-none p-2 border-0 text-end fw-bold font-monospace" id="transferTutar" placeholder="0,00" style="background-color: #eaeded; font-size: 12px; border-radius: 4px; color: #2196f3;">
                    </div>

                    <!-- AKILLI VERİTABANI BÜTÇE TAG ALANI -->
                    <div id="akilliBütçeTagAlani" class="mb-3 p-3 rounded border bg-white d-none" style="border-color: #e9ecef !important;">
                        <div class="text-secondary fw-bold mb-2 d-inline-flex align-items-center" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px; line-height: 1;">
                            <i class="fas fa-brain text-info me-1" style="font-size: 10px;"></i> Tanımlı Transferler:
                        </div>
                        <!-- gap-2 ile dikey ve yatayda etiketler arası jilet gibi hizalandı -->
                        <div id="tagKonteyner" class="d-flex flex-wrap gap-2 align-items-center">
                            <!-- Veritabanındaki havuz koduna eşleşen dinamik etiketler -->
                        </div>
                    </div>

                    <!-- Kontrol Et Butonu -->
                    <button type="button" class="btn btn-sm btn-dark w-100 p-2 fw-bold mt-2 border-0 d-inline-flex align-items-center justify-content-center" id="btnTransferKontrolEt" style="background-color: #4a545e; border-radius: 4px; font-size: 11px;">
                        <i class="fas fa-calculator me-2 text-warning" style="font-size: 12px;"></i> Bakiye Kontrolü Yap
                    </button>
                </div>

                <!-- Adım 2: Akıllı Karar ve Senaryo Paneli (Gizli) -->
                <div id="transferSenaryoPaneli" class="mt-3 pt-3 border-top d-none" style="border-top-style: dashed !important; border-top-color: #dee2e6 !important;">
                    
                    <!-- Başarı Durumu (Bakiye Yeterli) -->
                    <div id="senaryoYeterli" class="alert alert-success p-2 m-0 border-0 d-none d-inline-flex align-items-center w-100" style="background-color: #e8f5e9; color: #2e7d32; border-radius: 4px; font-weight: 500; line-height: 1.2;">
                        <i class="fas fa-check-circle me-2" style="font-size: 12px;"></i> Kaynak hesap bakiyesi bu transfer için tamamen yeterlidir.
                    </div>

                    <!-- Yetersiz Bakiye Durumu -->
                    <div id="senaryoYetersiz" class="d-none">
                        <div class="alert alert-danger p-2 mb-3 border-0 d-inline-flex align-items-center w-100" style="background-color: #ffebee; color: #c62828; border-radius: 4px; font-weight: 500; line-height: 1.2;">
                            <i class="fas fa-exclamation-triangle me-2" style="font-size: 12px;"></i> <span><strong>Bakiye Yetersiz!</strong> Eksik Tutar: <span class="fw-bold font-monospace" id="eksikTutarMetni">0,00</span> ₺</span>
                        </div>
                        
                        <label class="form-label fw-bold text-secondary mb-2 d-inline-flex align-items-center" style="line-height: 1;"><i class="fas fa-route me-1 text-warning" style="font-size: 11px;"></i> Öneriler:</label>
                        
                        <!-- Seçenek A -->
                        <div class="form-check border p-2 rounded mb-2 bg-white d-flex align-items-center style-cursor" style="border-color: #e9ecef !important; min-height: 32px;">
                            <input class="form-check-input my-0 ms-1 me-2 shadow-none" type="radio" name="yetersizCozumTuru" id="cozumSadeceMevcut" value="sadece_mevcut" checked>
                            <label class="form-check-label fw-bold d-block style-cursor text-muted" for="cozumSadeceMevcut" style="margin-left: 5px; line-height: 1.2;">
                                Sadece <span id="mevcutAktarilabilirMetin" class="font-monospace text-secondary">0,00</span> ₺'yi aktar.
                            </label>
                        </div>
                        
                        <!-- Seçenek B -->
                        <div class="form-check border p-2 rounded bg-white style-cursor" style="border-color: #e9ecef !important;">
                            <div class="d-flex align-items-center" style="min-height: 20px;">
                                <input class="form-check-input my-0 ms-1 me-2 shadow-none" type="radio" name="yetersizCozumTuru" id="cozumDestekli" value="destekli">
                                <label class="form-check-label fw-bold d-block style-cursor text-muted" for="cozumDestekli" style="margin-left: 5px; line-height: 1.2;">
                                    <span id="mevcutAktarilabilirMetin1" class="font-monospace text-secondary">0,00</span> ₺'yi ana hesaptan, 
                                    kalan <span class="fw-bold font-monospace" id="eksikTutarMetni1">0,00</span> ₺'yi diğer hesaptan aktar.
                                </label>
                            </div>
                            <div class="mt-2 ms-4 d-none" id="destekHavuzSecimAlani">
                                <select class="form-select form-select-sm border-0 p-2 font-monospace shadow-none" id="transferDestekHavuz" style="font-size: 11px; background-color: #fff3e0; border-radius: 4px; color: #e65100; font-weight: 500;">
                                    <!-- Dinamik destek havuzları -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Nihai Onay Butonu -->
                    <button type="button" class="btn btn-sm btn-success w-100 p-2 fw-bold mt-3 border-0 d-inline-flex align-items-center justify-content-center" id="btnTransferiOnayla" style="background-color: #2e7d32; border-radius: 4px; font-size: 11px;">
                        <i class="fas fa-check-double me-2" style="font-size: 12px;"></i> Transferi Onayla
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     HTML 1. KISIM: MODAL BAŞLIK, GİZLİ VERİ KALKANLARI VE HESAP SEÇİCİLER
     ========================================================================= -->
<div class="modal fade" id="forexYatirimModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="forexYatirimModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            
            <!-- Modal Başlık Alanı (Koyu Kurumsal Pastel) -->
            <div class="modal-header bg-dark text-white p-3 border-0 d-flex align-items-center justify-content-between" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h6 class="modal-title m-0 fw-bold d-inline-flex align-items-center" id="forexYatirimModalLabel">
                    <i class="fas fa-exchange-alt me-2 text-warning"></i> Döviz & Varlık Satın Alma Terminali
                </h6>
                <button type="button" class="btn-close btn-close-white shadow-none m-0 p-1" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            
            <!-- Satın Alma Formu -->
            <form action="" method="POST" id="forexSatinalForm" class="m-0">
                <!-- Backend kabul motorunu tetikleyen gizli aksiyon tanımları -->
                <input type="hidden" name="action" value="interaktif_forex_panel_islem">
                <input type="hidden" name="panel_islem_yonu" value="ALIS">
                
                <!-- 🛡️ Sızdırmazlık Kalkanı: İşbank (A) ID'si backend'e kilitli post edilir -->
                <input type="hidden" name="panel_cekilecek_hesap" value="2_anapara_TL">

                <div class="modal-body p-4 text-dark bg-light">
                    
                    <!-- 1. Üst Şerit: İşbank (A) Yatırım Ana Havuz Dinamik Bakiyesi -->
                    <div class="p-3 bg-white rounded-3 border border-light-subtle d-flex justify-content-between align-items-center shadow-sm mb-4">
                        <span class="fw-bold text-secondary text-uppercase tracking-wider small mb-0">İşbank (A) Havuz Ana Para:</span>
                        <span id="modal-tasarruf-bakiye" class="fw-bold font-monospace fs-5 text-primary" data-tl="<?= (float)($canli_ana_yatirim_bakiye_tl ?? 0) ?>">
                            <?= number_format($canli_ana_yatirim_bakiye_tl ?? 0, 2, ',', '.') ?> ₺
                        </span>
                    </div>

                    <!-- 2. ÇEKİLECEK VE AKTARILACAK HESAP SELECT BOX'LARI -->
                    <div class="row g-3 mb-4">
                        <!-- Sol Seçici: İşbank (A) Olarak Kilitli -->
                        <div class="col-6 text-start">
                            <label for="modal_cekilecek_hesap" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">Paranın Çekileceği Hesap</label>
                            <select class="form-select form-select-lg fw-semibold text-dark p-3" id="modal_cekilecek_hesap" disabled>
                                <option value="2_anapara_TL" selected>İşbankası (A) (TL)</option>
                            </select>
                        </div>

                        <!-- Sağ Seçici: Dinamik Varlık Hücresi (onchange tetikleyicili) -->
                        <div class="col-6 text-start">
                            <label for="modal_aktarilacak_hesap" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">Paranın Aktarılacağı Hesap</label>
                            <select class="form-select form-select-lg fw-semibold text-dark p-3" id="modal_aktarilacak_hesap" name="panel_aktarilacak_hesap" onchange="hesaplaModalYatirimMatematigi()" required>
                                <option value="" disabled selected>Varlık Seçiniz...</option>
                                <option value="USD">USD (Amerikan Doları)</option>
                                <option value="EUR">EUR (Euro Bölgesi)</option>
                                <option value="XAU">XAU (Has Altın Gram)</option>
                            </select>
                        </div>
                    </div>
                    <!-- =========================================================================
                         HTML 2. KISIM: CANLI GÖSTERGELER, TUTAR/MİKTAR GİRİŞLERİ VE NİHAİ MATRAH
                         ========================================================================= -->
                    <!-- 3. GÖSTERGE KARTLARI -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="card bg-white border border-light-subtle rounded-2 p-3 text-center shadow-sm">
                                <span id="modal_kur_turu_label" class="d-block text-secondary small fw-bold text-uppercase mb-1 tracking-wider">Banka Alış</span>
                                <h5 id="modal_canli_kur_text" class="fw-bold text-dark font-monospace m-0">0,0000 ₺</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-white border border-light-subtle rounded-2 p-3 text-center shadow-sm">
                                <span id="modal_maks_kapasite_label" class="d-block text-secondary small fw-bold text-uppercase mb-1 tracking-wider">Maks. Alınabilir</span>
                                <h5 id="modal_maks_kapasite_text" class="fw-bold text-success font-monospace m-0">0,00</h5>
                            </div>
                        </div>
                    </div>

                    <!-- 4. MANUEL KURU GİRİŞİ -->
                    <div class="mb-4 text-start">
                        <label for="modal_manuel_kur" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">
                            <i class="fas fa-edit me-1 text-muted"></i> Kur Belirle
                        </label>
                        <input type="number" class="form-control form-control-lg font-monospace fw-bold text-end border p-3" name="panel_manuel_kur" id="modal_manuel_kur" step="0.0001" min="0" placeholder="0.0000 (Canlı kuru ezmek için yazın)">
                    </div>

                    <!-- 5. YAN YANA GİRİŞ ALANLARI (SAĞA TAM YASLI LINK STANDARDI) -->
                    <div class="row g-3 mb-4">
                        <!-- Giriş: TL Tutar -->
                        <div class="col-6 text-start">
                            <label for="modal_tutar_tl" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">
                                <i class="fas fa-lira-sign me-1 text-muted"></i> Tutar Belirle (TL)
                            </label>
                            <input type="number" class="form-control form-control-lg font-monospace fw-bold text-end border p-3 text-primary shadow-none rounded-2" name="panel_tutar_tl" id="modal_tutar_tl" step="0.01" min="0" placeholder="0.00">
                            <div class="d-flex justify-content-end mt-1">
                                <span class="text-decoration-underline fw-bold text-dark small font-monospace user-select-none" id="modal_btn_max_tl" style="cursor: pointer; font-size: 11px;">Bakiyenin Tümü</span>
                            </div>
                        </div>
                        
                        <!-- Giriş: Varlık Miktar -->
                        <div class="col-6 text-start">
                            <label id="modal_lbl_islem_miktari" for="modal_miktar" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">
                                <i class="fas fa-coins me-1 text-muted"></i> Miktar Belirle
                            </label>
                            <input type="number" class="form-control form-control-lg font-monospace fw-bold text-end border p-3 text-primary shadow-none rounded-2" name="panel_miktar" id="modal_miktar" step="0.0001" min="0" placeholder="0.0000">
                            <div class="d-flex justify-content-end mt-1">
                                <span class="text-decoration-underline fw-bold text-dark small font-monospace user-select-none" id="modal_btn_max_miktar" style="cursor: pointer; font-size: 11px;">Bakiyenin Tümü</span>
                            </div>
                        </div>
                    </div>
                  

                    <!-- 6. NİHAİ MATRAH ÇIKTISI VE VERGİ ŞERİDİ -->
                    <div class="mb-2 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-secondary small text-uppercase mb-0 tracking-wider">İşlem Tutarı (Vergi Dahil)</label>
                            <div class="d-flex gap-1">
                                <span id="modal_panel_tutar_bilgi_rozet" class="badge text-bg-secondary fw-bold font-monospace px-2 py-1">Ana Tutar: 0,00 ₺</span>
                                <span id="modal_panel_vergi_bilgi_rozet" class="badge text-bg-warning fw-bold font-monospace px-2 py-1">Vergi: 0,00 ₺</span>
                            </div>
                            <!-- 🎯 KİLİT VERİ ENTEGRASYONU: JavaScript burayı anlık besler ve post verisinde taşır -->
                            <input id="modal_panel_tutar_bilgi_input" type="hidden" name="panel_doviz_alış_tutari" value="0,00 ₺">
                        </div>
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control font-monospace fw-bold bg-white text-end border p-3 text-dark shadow-none" id="modal_panel_nihai_sonuc" readonly value="0,00 ₺">
                        </div>
                        <!-- Bootstrap Standart Tehlike Şeridi -->
                        <div id="modal_panel_bakiye_uyari_metni" class="alert alert-danger d-flex align-items-center p-3 mt-3 mb-0 border-0 fw-semibold rounded-2 d-none">
                            <i class="fas fa-times-circle me-2 fs-5"></i> İşbank (A) TL Bakiyesi Yetersiz! Lütfen girdiğiniz tutarı veya kuru kontrol edin.
                        </div>
                    </div>
                    
                </div> <!-- .modal-body sonu -->
                
                <!-- Modal Footer Aksiyonları (Büyük Boy Butonlar) -->
                <div class="modal-footer bg-light border-top p-3 d-flex align-items-center justify-content-end" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button type="button" class="btn btn-secondary btn-lg px-4 fw-bold text-uppercase d-inline-flex align-items-center justify-content-center m-0 me-2" data-bs-dismiss="modal">
                        <i class="fas fa-undo me-2"></i> İptal
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg px-4 fw-bold text-uppercase d-inline-flex align-items-center justify-content-center text-white m-0" id="modal_btn_panel_onayla" disabled style="background-color: #6a1b9a; border-color: #6a1b9a;">
                        <i class="fas fa-check-circle me-2"></i> Satın Alma İşlemini Tamamla
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> <!-- En dıştaki #forexYatirimModal sonu -->


<!-- =========================================================================
     BLOK 2: MANAGEMENT VIEW - DÖVİZ SATIN ALMA MODAL HTML YAPISI
     ========================================================================= -->
<div class="modal fade" id="forexYatirimModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="forexYatirimModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            
            <!-- Modal Başlık Alanı -->
            <div class="modal-header bg-dark text-white p-3 border-0 d-flex align-items-center justify-content-between" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h6 class="modal-title m-0 fw-bold d-inline-flex align-items-center" id="forexYatirimModalLabel">
                    <i class="fas fa-exchange-alt me-2 text-warning"></i> Döviz & Varlık Satın Alma Terminali
                </h6>
                <button type="button" class="btn-close btn-close-white shadow-none m-0 p-1" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            
            <!-- Satın Alma Formu -->
            <form action="" method="POST" id="forexSatinalForm" class="m-0">
                <!-- Backend kabul motorunu tetikleyen gizli parametre tanımları -->
                <input type="hidden" name="action" value="interaktif_forex_panel_islem">
                <input type="hidden" name="panel_islem_yonu" value="ALIS">
                
                <!-- 🚨 CRITICAL REFORM: Kaynak hesap kilitli olduğu için veritabanı ID'sini kalkan girdide taşıyoruz -->
                <input type="hidden" name="panel_cekilecek_hesap" value="2_anapara_TL">

                <div class="modal-body p-4 text-dark bg-light">
                    
                    <!-- 1. Üst Şerit: İşbank (A) Yatırım Ana Havuz Dinamik Bakiyesi (JS data-tl Değerini Okur) -->
                    <div class="p-3 bg-white rounded-3 border border-light-subtle d-flex justify-content-between align-items-center shadow-sm mb-4">
                        <span class="fw-bold text-secondary text-uppercase tracking-wider small mb-0">İşbank (A) Havuz Ana Para:</span>
                        <span id="modal-tasarruf-bakiye" class="fw-bold font-monospace fs-5 text-primary" data-tl="<?= (float)($canli_ana_yatirim_bakiye_tl ?? 0) ?>">
                            <?= number_format($canli_ana_yatirim_bakiye_tl ?? 0, 2, ',', '.') ?> ₺
                        </span>
                    </div>

                    <!-- 2. ÇEKİLECEK VE AKTARILACAK HESAP SELECT BOX'LARI -->
                    <div class="row g-3 mb-4">
                        <!-- Sol Seçici: İşbank (A) Olarak Kilitli -->
                        <div class="col-6 text-start">
                            <label for="modal_cekilecek_hesap" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">Paranın Çekileceği Hesap</label>
                            <select class="form-select form-select-lg fw-semibold text-dark p-3" id="modal_cekilecek_hesap" disabled>
                                <option value="2_anapara_TL" selected>İşbankası (A) (TL)</option>
                            </select>
                        </div>

                        <!-- Sağ Seçici: Paranın Aktarılacağı Döviz/Varlık Hücresi -->
                        <div class="col-6 text-start">
                            <label for="modal_aktarilacak_hesap" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">Paranın Aktarılacağı Hesap</label>
                            <select class="form-select form-select-lg fw-semibold text-dark p-3" id="modal_aktarilacak_hesap" name="panel_aktarilacak_hesap" onchange="hesaplaModalYatirimMatematigi()" required>
                                <option value="" disabled selected>Varlık Seçiniz...</option>
                                <option value="USD">USD (Amerikan Doları)</option>
                                <option value="EUR">EUR (Euro Bölgesi)</option>
                                <option value="XAU">XAU (Has Altın Gram)</option>
                            </select>
                        </div>
                    </div>

                    <!-- 3. GÖSTERGE KARTLARI -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="card bg-white border border-light-subtle rounded-2 p-3 text-center shadow-sm">
                                <span id="modal_kur_turu_label" class="d-block text-secondary small fw-bold text-uppercase mb-1 tracking-wider">Banka Alış</span>
                                <h5 id="modal_canli_kur_text" class="fw-bold text-dark font-monospace m-0">0,0000 ₺</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-white border border-light-subtle rounded-2 p-3 text-center shadow-sm">
                                <span id="modal_maks_kapasite_label" class="d-block text-secondary small fw-bold text-uppercase mb-1 tracking-wider">Maks. Alınabilir</span>
                                <h5 id="modal_maks_kapasite_text" class="fw-bold text-success font-monospace m-0">0,00</h5>
                            </div>
                        </div>
                    </div>

                    <!-- 4. MANUEL KURU GİRİŞİ -->
                    <div class="mb-4 text-start">
                        <label for="modal_manuel_kur" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">
                            <i class="fas fa-edit me-1 text-muted"></i> Kur Belirle
                        </label>
                        <input type="number" class="form-control form-control-lg font-monospace fw-bold text-end border p-3" name="panel_manuel_kur" id="modal_manuel_kur" step="0.0001" min="0" placeholder="0.0000 (Canlı kuru ezmek için yazın)">
                    </div>

                    <!-- 5. YAN YANA GİRİŞ ALANLARI -->
                    <div class="row g-3 mb-4">
                        <!-- Giriş: TL Tutar -->
                        <div class="col-6 text-start">
                            <label for="modal_tutar_tl" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">
                                <i class="fas fa-lira-sign me-1 text-muted"></i> Tutar Belirle (TL)
                            </label>
                            <input type="number" class="form-control form-control-lg font-monospace fw-bold text-end border p-3 text-primary shadow-none rounded-2" name="panel_tutar_tl" id="modal_tutar_tl" step="0.01" min="0" placeholder="0.00">
                            <div class="d-flex justify-content-end mt-1">
                                <span class="text-decoration-underline fw-bold text-dark small font-monospace user-select-none" id="modal_btn_max_tl" style="cursor: pointer; font-size: 11px;">Bakiyenin Tümü</span>
                            </div>
                        </div>
                        
                        <!-- Giriş: Varlık Miktar -->
                        <div class="col-6 text-start">
                            <label id="modal_lbl_islem_miktari" for="modal_miktar" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">
                                <i class="fas fa-coins me-1 text-muted"></i> Miktar Belirle
                            </label>
                            <input type="number" class="form-control form-control-lg font-monospace fw-bold text-end border p-3 text-primary shadow-none rounded-2" name="panel_miktar" id="modal_miktar" step="0.0001" min="0" placeholder="0.0000">
                            <div class="d-flex justify-content-end mt-1">
                                <span class="text-decoration-underline fw-bold text-dark small font-monospace user-select-none" id="modal_btn_max_miktar" style="cursor: pointer; font-size: 11px;">Bakiyenin Tümü</span>
                            </div>
                        </div>
                    </div>

                    <!-- 6. NİHAİ MATRAH ÇIKTISI VE VERGİ ŞERİDİ -->
                    <div class="mb-2 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-secondary small text-uppercase mb-0 tracking-wider">İşlem Tutarı (Vergi Dahil)</label>
                            <div class="d-flex gap-1 align-items-center">
                                <span id="modal_panel_tutar_bilgi_rozet" class="badge text-bg-warning fw-bold font-monospace px-2 py-1">Ana Tutar: 0,00 ₺</span>
                                <span id="modal_panel_vergi_bilgi_rozet" class="badge text-bg-warning fw-bold font-monospace px-2 py-1">Vergi: 0,00 ₺</span>
                                <!-- 🎯 KİLİT VERİ ALANI: JS burayı anlık doldurur ve post paketinde taşır -->
                                <input id="modal_panel_tutar_bilgi_input" type="hidden" name="panel_doviz_alış_tutari" value="0,00 ₺">
                            </div>
                        </div>
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control font-monospace fw-bold bg-white text-end border p-3 text-dark shadow-none" id="modal_panel_nihai_sonuc" readonly value="0,00 ₺">
                        </div>
                        <!-- Bootstrap Standart Tehlike Şeridi -->
                        <div id="modal_panel_bakiye_uyari_metni" class="alert alert-danger d-flex align-items-center p-3 mt-3 mb-0 border-0 fw-semibold rounded-2 d-none">
                            <i class="fas fa-times-circle me-2 fs-5"></i> İşbank (A) TL Bakiyesi Yetersiz! Lütfen girdiğiniz tutarı veya kuru kontrol edin.
                        </div>



<script>

// =================================================================================
// 🎯 JAVASCRIPT MOTORU - CONFIG & SABİT LİMİT TANIMLAMALARI
// =================================================================================
const ISBANK_KK_SABIT_LIMIT = 321100.00;
const VAKIFBANK_KK_SABIT_LIMIT = 263500.00;

// 🚀 global hafıza alanında aktif kaynak havuzu türünü tutalım
window.canliAktifKaynakHavuz = '';


let isRequestPending = false;
let aktifKaynakHavuz = '';
let aktifKaynakAnapara = 0;


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


// =================================================================================
// 🎯 GENEL FONKSİYONLAR
// =================================================================================

// ====================== sol blok =================================================


window.cariGirdilerAbortController = null;
window.cariGirdilerZamanlayici = null;

function cariGirdileriAnlikGuncelle(kartTuru, alanTuru, deger) {
    // Klavye yazım durma tamponu (Debounce)
    clearTimeout(window.cariGirdilerZamanlayici);
    
    // 🎯 SIFIR GECİKME: Klavyeden harf girdiğin salisede sunucuyu beklemeden
    // sizin orijinal hesaplama motorlarınızı önyüzde anlık olarak peş peşe tetikliyoruz!
    if (typeof hariciKrediKartiMatrisiniHesapla === 'function') hariciKrediKartiMatrisiniHesapla();
    if (typeof hariciProvizyonHesaplaNihai === 'function') {
        hariciProvizyonHesaplaNihai('isbank');
        hariciProvizyonHesaplaNihai('vakif');
    }
    if (typeof renderNihaiDashboardCore === 'function') renderNihaiDashboardCore();

    window.cariGirdilerZamanlayici = setTimeout(() => {
        if (navigator.onLine === false && !window.location.hostname.includes('localhost') && !window.location.hostname.includes('127.0.0.1')) return;
        
        if (window.cariGirdilerAbortController) window.cariGirdilerAbortController.abort();
        window.cariGirdilerAbortController = new AbortController();
        const signal = window.cariGirdilerAbortController.signal;
        
        // Sadece değişen tek bir hücrenin verisini post paketine ekle (Sıfır ezme güvencesi)
        const formData = new FormData();
        formData.append('islem', 'cari_girdiler_kaydet');
        formData.append('kart_turu', kartTuru); 
        formData.append('alan_turu', alanTuru); 
        formData.append('deger', parseFloat(deger) || 0);
        
        // Üst kesinleşen dönem tutarları temizlenerek paketleniyor
        formData.append('isbank_kesinlesen_donem', hariciParaOkuVeTemizle('isbank_kesinlesen_donem'));
        formData.append('vakif_kesinlesen_donem',  hariciParaOkuVeTemizle('vakif_kesinlesen_donem'));
        
        const aktifElemanId = document.activeElement ? document.activeElement.id : null;
        const secimBaslangic = document.activeElement ? document.activeElement.selectionStart : null;
        const secimBitis = document.activeElement ? document.activeElement.selectionEnd : null;

        const istekUrl = window.location.origin + '/butce/admin/management';

        fetch(istekUrl, { method: 'POST', body: formData, signal: signal })
        .then(res => {
            if (!res.ok) throw new Error(`HTTP Hata: ${res.status}`);
            return res.json();
        })
        .then(data => {
            if (data && data.status === 'success') {
                // Sayfayı yenilemeden birleşik sanal DOM motorunu tetikle
                window.monitorGuncelle(() => {
                    setTimeout(() => {
                        if (aktifElemanId) {
                            const odakEl = document.getElementById(aktifElemanId);
                            if (odakEl) {
                                odakEl.focus();
                                if (secimBaslangic !== null && secimBitis !== null && odakEl.tagName === 'INPUT' && odakEl.type !== 'number') {
                                    odakEl.setSelectionRange(secimBaslangic, secimBitis);
                                }
                            }
                        }
                    }, 10);
                    
                    // Veritabanı mühürlemesinden sonra sizin orijinal matematiksel zinciri son kez mühürle
                    if (typeof hariciKrediKartiMatrisiniHesapla === 'function') hariciKrediKartiMatrisiniHesapla();
                    if (typeof hariciProvizyonHesaplaNihai === 'function') {
                        hariciProvizyonHesaplaNihai('isbank');
                        hariciProvizyonHesaplaNihai('vakif');
                    }
                    if (typeof renderNihaiDashboardCore === 'function') renderNihaiDashboardCore();
                });
            }
        })
        .catch(err => {
            if (err.name === 'AbortError') return; 
            console.log("%c[OFFLINE SAFE LAYER ACTIVE]", "color: #198754; font-weight: bold;");
        });
    }, 1500); 
}

function hesapKesimiYapAksiyonu(kartAdi) {
    const kartEtiket = kartAdi === 'isbank' ? 'İş Bankası' : 'Vakıfbank';
    
    if (!confirm(`${kartEtiket} için hesap kesim işlemi uygulamak istediğinize emin misiniz?\nDönem içi harcamalar kesinleşen ekstreye aktarılacaktır.`)) {
        return false;
    }

    // İstek parametrelerini hazırla
    const veriFormu = new FormData();
    veriFormu.append('islem', 'kart_hesap_kesimi');
    veriFormu.append('kart_turu', kartAdi);

    fetch(window.location.href, {
        method: 'POST',
        body: veriFormu
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            // Ön yüzdeki inputları sayfa yenilemeden canlı güncelle
            if (kartAdi === 'isbank') {
                const donemIciInput = document.getElementById('isbank-donem-ici-input');
                const ekstreInput = document.getElementById('isbank-ekstre-input');
                
                if (donemIciInput && ekstreInput) {
                    ekstreInput.value = parseFloat(donemIciInput.value || 0).toFixed(2);
                    donemIciInput.value = "0.00";
                }
            } else {
                const donemIciInput = document.getElementById('vakif-donem-ici-input');
                const ekstreInput = document.getElementById('vakif-ekstre-input');
                
                if (donemIciInput && ekstreInput) {
                    ekstreInput.value = parseFloat(donemIciInput.value || 0).toFixed(2);
                    donemIciInput.value = "0.00";
                }
            }
            
            // Eğer projenizde ana monitor ekranını güncelleyen global bir fonksiyon varsa tetikleyin
            if (typeof window.monitorGuncelle === "function") {
                window.monitorGuncelle();
            }
            
            alert(`${kartEtiket} hesap kesimi kuruşu kuruşuna işlendi!`);
        } else {
            alert("Hata: " + res.message);
        }
    })
    .catch(err => {
        console.error("Hesap kesim hatası:", err);
        alert("Sistem bağlantı hatası oluştu!");
    });
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




// ====================== orta blok =================================================

function gelirEftBitir(id, islemTuru, btn = null, tutar = 0) {
    if (!btn && id > 0) {
        btn = document.querySelector(`.eft-btn[data-id="${id}"], .prefix-gelir-eft-btn[data-id="${id}"]`);
    }

    if (btn) {
        btn.innerHTML = "⏳";
        btn.disabled = true;
    }

    const rowKalem = btn ? btn.closest('.row-kalem') : null;
    
    // 🎯 YEDEK EMNİYET KİLİDİ: Önce div etiketine bak, bulamazsan input kutusunun içini zorla oku!
    let kalemAdi = rowKalem ? (rowKalem.getAttribute('data-kalem-adi') || '').trim() : '';
    
    if (kalemAdi === '' && rowKalem) {
        const gizliInput = rowKalem.querySelector('input[type="text"]');
        if (gizliInput) {
            kalemAdi = gizliInput.value.trim(); // Disabled inputun içindeki metni zorla koparır
        }
    }

    const kAdiKucuk = kalemAdi.toLowerCase();
    let havuzKodu = rowKalem ? (rowKalem.getAttribute('data-havuz-kodu') || '').trim() : '';

    let nihaiIslemParametresi = 'gelir_eft';
    const formData = new FormData();

    // Akıllı karakter koruma bariyeri
    const isOzgeMaas   = /zge.*maa/i.test(kAdiKucuk) || kAdiKucuk.includes('özge');
    const isMaktuMesai = /maktu|mesai/i.test(kAdiKucuk);
    const isArmagan    = /arma/i.test(kAdiKucuk) && kAdiKucuk.includes('maaş');
    const isDevir      = /devir|nceki/i.test(kAdiKucuk);

    if (isDevir) {
        formData.append('gelir_turu', 'prev_month_devir');
        formData.append('odenen_tutar', tutar);
    } 
    else if (isOzgeMaas) {
        formData.append('gelir_turu', 'ozge_maas');
        const inputO = document.querySelector('input[name="faiz_kredi_butcesi"]') || document.getElementById('faiz_kredi_butcesi');
        formData.append('isbank_o_tutar', inputO ? parseFloat(inputO.value) || 53200 : 53200);
        formData.append('toplam_maas_tutar', tutar);
    } 
    else if (isMaktuMesai) {
        formData.append('gelir_turu', 'maktu_mesai');
        formData.append('maktu_tutar', tutar);
    } 
    else if (isOzgeMaas) {
        formData.append('gelir_turu', 'armagan_maas');
        formData.append('armagan_maas_tutar', tutar);
    } 
    else {
        formData.append('gelir_turu', 'dinamik_ek_gelir');
        formData.append('odenen_tutar', tutar);
    }

    formData.append('islem', nihaiIslemParametresi);
    formData.append('id', id); 
    formData.append('kalem_adi', kalemAdi); 
    if (havuzKodu) formData.append('havuz_kodu', havuzKodu);

    fetch('/butce/admin/management', { method: 'POST', body: formData })
    .then(res => {
        if (!res.ok) throw new Error("HTTP Hata Kodu: " + res.status);
        return res.json();
    })
    .then(data => {
        if (data && data.status === 'success') { 
            if (btn) {
                btn.innerHTML = "✓";
                btn.disabled = true; 
                btn.classList.add('disabled', 'btn-secondary'); 
                btn.classList.remove('btn-success');
            }
            window.location.reload(); 
        } else { 
            alert(`⚠️ Gelir İşlemi Reddildi!\nSunucu Mesajı: ${data.message || 'İşlem tamamlanamadı.'}`); 
            if (btn) { btn.innerHTML = 'EFT'; btn.disabled = false; } 
        }
    })
    .catch(err => { 
        console.error("Ağ Hatası:", err); 
        alert(`⚠️ Ağ Bağlantı Hatası!\nDetay: ${err.message}`);
        if (btn) { btn.innerHTML = 'EFT'; btn.disabled = false; } 
    });
}

function giderOdeBitir(id, islemTuru, btn = null, tutar = 0) {
    if (!btn && id > 0) {
        btn = document.querySelector(`.ode-btn[data-id="${id}"]`);
    }

    if (btn) { btn.innerHTML = "⏳"; btn.disabled = true; }

    const rowKalem = btn ? btn.closest('.row-kalem') : null;
    const kalemAdi = rowKalem ? (rowKalem.getAttribute('data-kalem-adi') || '').trim() : '';
    const havuzKodu = rowKalem ? (rowKalem.getAttribute('data-havuz-kodu') || '').trim() : '';

    const formData = new FormData();
    formData.append('islem', 'gider_ode');
    formData.append('id', id);
    formData.append('kalem_adi', kalemAdi);
    formData.append('odenen_tutar', tutar);
    if (havuzKodu) formData.append('havuz_kodu', havuzKodu);

    fetch('/butce/admin/management', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data && data.status === 'success') {
            window.location.reload();
        } else {
            alert(`⚠️ Gider İşlemi Reddildi!\nSunucu Mesajı: ${data.message || 'İşlem tamamlanamadı.'}`);
            if (btn) { btn.innerHTML = 'ÖDE'; btn.disabled = false; }
        }
    })
    .catch(err => {
        alert("⚠️ Sunucu bağlantısı koptu veya JSON yapısı bozuldu.");
        if (btn) { btn.innerHTML = 'ÖDE'; btn.disabled = false; }
    });
}

function damacanaAksiyon(event, id, yon) {
    if (event) event.preventDefault();
    
    const birimFiyatInput = document.getElementById('su-birim-fiyat-' + id);
    const birimFiyat = birimFiyatInput ? parseFloat(birimFiyatInput.value) || <?=$damacanaSuBirimTutari?> : <?=$damacanaSuBirimTutari?>;

    const formData = new FormData();
    formData.append('islem', 'damacana_ode');
    formData.append('id', id);
    formData.append('birim_fiyat', birimFiyat);
    formData.append('yon', yon);

    fetch('/butce/admin/management', { method: 'POST', body: formData })
    .then(res => res.text()) // 🎯 REFORM: Sunucu çökme metinlerini yakalamak için önce ham text alıyoruz
    .then(hamMetin => {
        console.log("[Damacana Sunucu Saf Çıktı]:", hamMetin);
        
        const startIdx = hamMetin.indexOf('{');
        const endIdx = hamMetin.lastIndexOf('}');
        
        if (startIdx !== -1 && endIdx !== -1) {
            const data = JSON.parse(hamMetin.substring(startIdx, endIdx + 1));
            if (data && data.status === 'success') {
                window.location.reload();
                return;
            } else {
                alert(`⚠️ İşlem Reddedildi!\nSunucu Mesajı: ${data.message || 'SQL Hatası.'}`);
                return;
            }
        }
        
        // Eğer JSON bulunamazsa ham PHP Fatal Error çıktısını ayıklayıp ekrana basar
        alert(`⚠️ Sunucu Sözdizimi (Syntax) Hatası!\n\n${hamMetin.replace(/<[^>]*>/g, '')}`);
    })
    .catch(err => {
        console.error("Damacana asenkron hatası:", err);
        alert("⚠️ Sunucu bağlantısı koptu! Lütfen WampServer/Apache servislerinin aktif olduğunu kontrol edin.");
    });
}

function telefonAksiyon(e, id) {
    if (e) { e.preventDefault(); e.stopPropagation(); }

    const btn = e ? e.target.closest('button') : null;
    if (btn && btn.disabled) return;
    
    let orijinalMetin = "";
    if (btn) { orijinalMetin = btn.innerHTML; btn.innerHTML = "⏳"; btn.disabled = true; }

    const birimFiyatInput = document.getElementById('telefon-birim-fiyat-' + id);
    const birimFiyat = birimFiyatInput ? parseFloat(birimFiyatInput.value) : <?=$telefonFaturasiBirimTutari?>;
    
    const formData = new FormData();
    formData.append('islem', 'telefon_ode'); 
    formData.append('id', id);
    formData.append('birim_fiyat', birimFiyat);
    
    fetch('/butce/admin/management', { method: 'POST', body: formData })
    .then(res => res.text())
    .then(hamMetin => {
        const startIdx = hamMetin.indexOf('{');
        const endIdx = hamMetin.lastIndexOf('}');
        if (startIdx !== -1 && endIdx !== -1) {
            const data = JSON.parse(hamMetin.substring(startIdx, endIdx + 1));
            if (data && data.status === 'success') { 
                window.location.reload(); // Değişen havuz matrahlarını ve yeni kilitleri DB'den çek
                return;
            } else {
                alert(`⚠️ Reddedildi: ${data.message}`);
                if (btn) { btn.innerHTML = orijinalMetin; btn.disabled = false; }
                return;
            }
        }
        alert("⚠️ Sunucu JSON yapısı bozuldu!");
        if (btn) { btn.innerHTML = orijinalMetin; btn.disabled = false; }
    })
    .catch(err => { alert("⚠️ Sunucu bağlantı hatası!"); if (btn) { btn.innerHTML = orijinalMetin; btn.disabled = false; } });
}

function bakiyeDevretEftAksiyon(btnElement) {
    const tutarInput = document.getElementById('devir-havuz-tutar');
    const havuzSelect = document.getElementById('devir-havuz-kodu');

    const tutar = tutarInput ? parseFloat(tutarInput.value) || 0 : 0;
    const havuzKodu = havuzSelect ? havuzSelect.value : '';

    // Emniyet Bariyeri: Geçersiz tutar veya havuz girişini bloke et
    if (tutar <= 0 || havuzKodu === '') {
        alert("⚠️ Lütfen doğrudan yükleme yapmak için geçerli bir tutar ve hedef finansal havuz seçin!");
        return;
    }

    if (btnElement) {
        btnElement.innerHTML = "⏳";
        btnElement.disabled = true;
    }

    const formData = new FormData();
    formData.append('islem', 'bakiye_devret'); // Controller endpoint ismi
    formData.append('tutar', tutar);
    formData.append('havuz_kodu', havuzKodu);

    // Rota uyuşmazlığını önlemek için mutlak relative endpoint hattı
    fetch('/butce/admin/management', { method: 'POST', body: formData })
    .then(res => res.text())
    .then(hamMetin => {
        const startIdx = hamMetin.indexOf('{');
        const endIdx = hamMetin.lastIndexOf('}');
        
        if (startIdx !== -1 && endIdx !== -1) {
            const data = JSON.parse(hamMetin.substring(startIdx, endIdx + 1));
            if (data && data.status === 'success') {
                // Başarılı yüklemeden sonra havuz kutularındaki büyümeyi anlık görmek için yenile
                window.location.reload();
                return;
            } else {
                alert(`⚠️ Yükleme Reddedildi!\nSunucu Mesajı: ${data.message || 'SQL Hatası.'}`);
                if (btnElement) { btnElement.innerHTML = "<i class='bi bi-credit-card-fill text-white'></i>EFT"; btnElement.disabled = false; }
                return;
            }
        }
        alert("⚠️ Sunucu odası JSON bütünlüğünü kırdı!");
        if (btnElement) { btnElement.innerHTML = "<i class='bi bi-credit-card-fill text-white'></i>EFT"; btnElement.disabled = false; }
    })
    .catch(err => {
        console.error("Havuz enjeksiyon hatası:", err);
        alert("⚠️ Sunucu asenkron bağlantısı koptu!");
        if (btnElement) { btnElement.innerHTML = "<i class='bi bi-credit-card-fill text-white'></i>EFT"; btnElement.disabled = false; }
    });
}

function ekGelirKalemiEkle(btnElement) {
    const adiInput = document.getElementById('yeni-gelir-adi');
    const tutarInput = document.getElementById('yeni-gelir-tutar');
    const havuzSelect = document.getElementById('yeni-gelir-havuz');

    let kalemAdi = adiInput ? adiInput.value.trim() : '';
    const tutar = tutarInput ? parseFloat(tutarInput.value) || 0 : 0;
    const havuzKodu = havuzSelect ? havuzSelect.value : '';

    // Emniyet Bariyeri: Boş veri girişini durdur
    if (kalemAdi === '' || tutar <= 0 || havuzKodu === '') {
        alert("⚠️ Lütfen bütçe kalemi adı, geçerli bir tutar ve kaynak havuz seçtiğinizden emin olun!");
        return;
    }

    // Eğer kullanıcı zaten parantez içinde bir şey yazmadıysa otomatik ekler
    if (!kalemAdi.includes('(')) {
        // Selectbox metnindeki parantez içi veya ilk kelimeyi temiz şekilde yakalıyoruz
        let kısaHavuzAdi = "Nakit";
        if (havuzKodu === 'isbank_a') kısaHavuzAdi = "İşbankası (A)";
        if (havuzKodu === 'isbank_o') kısaHavuzAdi = "İşbankası (Ö)";
        if (havuzKodu === 'vakif_o') kısaHavuzAdi = "Vakıfbank (Ö)";
        if (havuzKodu === 'halkbank_o') kısaHavuzAdi = "Halkbank (Ö)";
        if (havuzKodu === 'vakifbank_a') kısaHavuzAdi = "Vakıfbank (A)";
        
        kalemAdi = `${kalemAdi} (${kısaHavuzAdi})`;
    }

    // Butonu istek havaydayken kilitle
    if (btnElement) {
        btnElement.innerHTML = "⏳";
        btnElement.disabled = true;
    }

    const formData = new FormData();
    formData.append('islem', 'gelir_kalem_ekle'); // Controller'daki endpoint adımız
    formData.append('kalem_adi', kalemAdi);
    formData.append('tutar', tutar);
    formData.append('havuz_kodu', havuzKodu);
    formData.append('turu', 'gelir');

    // Rota sızıntısını önlemek için tam sayfa URL'sine asenkron istek atıyoruz
    fetch(window.location.href, { method: 'POST', body: formData })
        .then(res => {
            if (!res.ok) throw new Error("Sunucu yanıt vermiyor.");
            return res.json();
        })
        .then(data => {
            if (data && data.status === 'success') {
                if (btnElement) btnElement.innerHTML = "✓";
                
                // Başarılı kayıttan sonra sayfayı tazeleyerek bütçeyi render et
                window.location.reload();
            } else {
                alert("⚠️ Hata: Bütçe katmanı kaydı reddetti.");
                if (btnElement) { btnElement.innerHTML = "<i class='bi bi-plus-lg me-1'></i>EKLE"; btnElement.disabled = false; }
            }
        })
        .catch(err => {
            console.error("Gelir ekleme post hatası:", err);
            alert("⚠️ Sunucu bağlantı hatası oluştu!");
            if (btnElement) { btnElement.innerHTML = "<i class='bi bi-plus-lg me-1'></i>EKLE"; btnElement.disabled = false; }
        });
}

function ekGiderKalemiEkle(btnElement) {
    const adiInput = document.getElementById('yeni-gider-adi');
    const tutarInput = document.getElementById('yeni-gider-tutar');
    const havuzSelect = document.getElementById('yeni-gider-havuz');

    let kalemAdi = adiInput ? adiInput.value.trim() : '';
    const tutar = tutarInput ? parseFloat(tutarInput.value) || 0 : 0;
    const havuzKodu = havuzSelect ? havuzSelect.value : '';

    if (kalemAdi === '' || tutar <= 0 || havuzKodu === '') {
        alert("⚠️ Lütfen bütçe kalemi adı, geçerli bir tutar ve kaynak havuz seçtiğinizden emin olun!");
        return;
    }

    if (!kalemAdi.includes('(')) {
        let kısaHavuzAdi = "Nakit";
        if (havuzKodu === 'isbank_a') kısaHavuzAdi = "İşbankası (A)";
        if (havuzKodu === 'isbank_o') kısaHavuzAdi = "İşbankası (Ö)";
        if (havuzKodu === 'vakif_o') kısaHavuzAdi = "Vakıfbank (Ö)";
        if (havuzKodu === 'halkbank_o') kısaHavuzAdi = "Halkbank (Ö)";
        if (havuzKodu === 'vakifbank_a') kısaHavuzAdi = "Vakıfbank (A)";
        
        kalemAdi = `${kalemAdi} (${kısaHavuzAdi})`;
    }

    if (btnElement) { btnElement.innerHTML = "⏳"; btnElement.disabled = true; }

    const formData = new FormData();
    formData.append('islem', 'gider_kalem_ekle');
    formData.append('kalem_adi', kalemAdi);
    formData.append('tutar', tutar);
    formData.append('havuz_kodu', havuzKodu);
    formData.append('turu', 'gider');

    fetch('/butce/admin/management', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data && data.status === 'success') {
                window.location.reload();
            } else {
                alert(`⚠️ Hata: ${data.message || 'Gider kaydı reddedildi.'}`);
                if (btnElement) { btnElement.innerHTML = "EKLE"; btnElement.disabled = false; }
            }
        }).catch(err => {
            alert("⚠️ Sunucu bağlantı hatası oluştu!");
            if (btnElement) { btnElement.innerHTML = "EKLE"; btnElement.disabled = false; }
        });
}

function tekilKalemSil(id) {
    if (!confirm('Bu bütçe kalemini silmek istediğinize emin misiniz?')) return;
    const formData = new FormData();
    formData.append('islem', 'kalem_sil');
    formData.append('id', id);

    fetch(window.location.href, { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') { window.monitorGuncelle(); }
    }).catch(err => console.error("Hata:", err));
}

function nergizCepOde() {
    const inputEl = document.getElementById('nergiz-cep-tutar');
    if (!inputEl) return;
    const tutar = parseFloat(inputEl.value) || 0;
    if (tutar <= 0) { alert('⚠️ Lütfen geçerli bir tutar giriniz.'); return; }
    asılOdemeyiBitir(0, 'nergiz_cep_ode', window.event ? window.event.target.closest('button') : null, tutar);
}

function anlikBirimFiyatlariniGuncelle() {
    // Sizin belirttiğiniz öz hakiki arayüz input ID'leri
    const inpTel = document.getElementById('telefon-birim-fiyat-10');
    const inpSu  = document.getElementById('su-birim-fiyat-12');

    const telFiyat = inpTel ? parseFloat(inpTel.value) || <?=$telefonFaturasiBirimTutari?> : <?=$telefonFaturasiBirimTutari?>;
    const suFiyat  = inpSu  ? parseFloat(inpSu.value)  || <?=$damacanaSuBirimTutari?> : <?=$damacanaSuBirimTutari?>;

    const formData = new FormData();
    formData.append('islem', 'birimFiyat_kaydet'); // Endpoint mühürü
    formData.append('telefon_birim_fiyat', telFiyat);
    formData.append('su_birim_fiyat', suFiyat);

    fetch('/butce/admin/management', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data && data.status === 'success') {
            // Yeni birim fiyatlara göre kalan bakiyelerin canlı hesaplanması için sayfayı yenile
            window.location.reload();
        }
    })
    .catch(err => console.error("Birim fiyat senkronizasyon hatası:", err));
}

function canlıFaizOnizlemeHesapla() {
    // 1. Kutulardaki Girdileri Yakalıyoruz
    const inpKredi = document.getElementById('faiz-kredi-butcesi-input');
    const inpOran  = document.getElementById('faiz-orani-input');
    const inpGun   = document.getElementById('faiz-gun-input');
    const stopaj   = 17.5;
    
    // Girdileri güvenli bir şekilde sayı formatına döküyoruz
    const sanalButce = inpKredi && inpKredi.value ? parseFloat(inpKredi.value) : 0;
    const oran       = inpOran && inpOran.value   ? parseFloat(inpOran.value)  : 0;
    const gun        = inpGun && inpGun.value     ? parseInt(inpGun.value)     : 0;

    // 2. 🎯 DÜZELTİLDİ: Orijinal HTML kimliğiniz olan "data_havuz_matrahlari" alanına bağlanıyoruz
    const dataHavuzu = document.getElementById('data_havuz_matrahlari');
    
    // 3. 🎯 DÜZELTİLDİ: HTML etiketlerinizle birebir uyan orijinal "data-isbank-a" ve "data-isbank-o" okunuyor
    const anaA = dataHavuzu ? parseFloat(dataHavuzu.getAttribute('data-isbank-a')) || 0 : 0;
    const anaO = dataHavuzu ? parseFloat(dataHavuzu.getAttribute('data-isbank-o')) || 0 : 0;

    // 4. Her Bir Matrah İçin Müstakil Net Faiz Hesaplama Formülü
    const faizHesapla = (anapara) => {
        if (anapara <= 0 || oran <= 0 || gun <= 0) return 0;
        const brut = (anapara * oran * gun) / 36500;
        return brut * (1 - (stopaj / 100));
    };

    // Her havuz kendi anaparasına göre bağımsız faiz getirisi üretir
    const faizGetirisiA = faizHesapla(anaA);
    const faizGetirisiO = faizHesapla(anaO);
    const faizGetirisiK = faizHesapla(sanalButce); // Kredi bütçesinin kendi sanal faizi

    // 5. Gösterim Alanları Yakalanır
    const elPreA = document.getElementById('pre-isbank-a');
    const elPreO = document.getElementById('pre-isbank-o');
    const elPreK = document.getElementById('pre-kredi-input');
    
    // 6. Güncel paraFormatlaTL Motoruyla Ekrana Basım Yapılır
    if (elPreA) elPreA.innerHTML = paraFormatlaTL(anaA + faizGetirisiA);
    if (elPreO) elPreO.innerHTML = paraFormatlaTL(anaO + faizGetirisiO);
    if (elPreK) elPreK.innerHTML = paraFormatlaTL(sanalButce + faizGetirisiK);

    // 7. Emniyet Bariyeri
    const modalAcBtn = document.getElementById('faiz-aktar-modal-ac-btn');
    if (modalAcBtn) {
        modalAcBtn.disabled = false;
    }
}


function anlikKrediFaizGuncelle() {
    const inpKredi = document.getElementById('faiz-kredi-butcesi-input');
    const inpOran  = document.getElementById('faiz-orani-input');
    const inpGun   = document.getElementById('faiz-gun-input');

    const formData = new FormData();
    formData.append('islem', 'faiz_hesapla'); 
    formData.append('evKredisiHam', inpKredi ? parseFloat(inpKredi.value) || 0 : 0);
    formData.append('faiz_orani', inpOran ? parseFloat(inpOran.value) || 0 : 0);
    formData.append('faiz_gun_sayisi', inpGun ? parseInt(inpGun.value) || 0 : 0);

    fetch('/butce/admin/management', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data && data.status === 'success') {
            // TAVSİYE: Sayfayı yenilemek yerine, gelen net tutarı direkt ekrandaki ilgili elemente basın
            // Örnek: document.getElementById('net-tutar-alani').innerText = data.netTutar;
            
            window.location.reload(); 
        } else {
            console.error("Sunucu işlemi başarısız:", data.message || "Bilinmeyen hata");
        }
    })
    .catch(err => console.error("Faiz senkronizasyon hatası:", err));
}


// TRANSFER BLOĞU
const bekleyenButceGiderleri = [
    <?php 
    if (isset($giderler) && is_array($giderler)) {
        foreach ($giderler as $gider) {
            // locked == 1 ise bu tutarı kesinlikle saymıyor ve listelemiyoruz
            if (isset($gider['locked']) && (int)$gider['locked'] === 1) { continue; }
            
            // Eğer gider tablonuzda havuz cüzdan eşleşmesi 'havuz_kodu' veya 'cuzdan' ise o veriyi çekiyoruz
            // Örn veritabanı içeriği: 'vakif_o', 'nakit', 'isbank_o' vs.
            $gider_havuz_kodu = $gider['havuz_kodu'] ?? ''; 
            
            echo "{" . 
                    "havuz_kodu: '" . addslashes($gider_havuz_kodu) . "', " .
                    "ad: '" . addslashes($gider['kalem_adi'] ?? $gider['aciklama'] ?? '') . "', " .
                    "tutar: " . floatval($gider['tutar'] ?? 0) . 
                 "},";
        }
    }
    ?>
];

// Küresel Havuz Tanımları
const finansalHavuzlar = {
    'isbank_o': { ad: 'İş Bankası (Ö)', idKok: 'isbank_o' },
    'isbank_a': { ad: 'İşbankası (A)', idKok: 'isbank_a' },
    'vakif_o':  { ad: 'Vakıfbank (Ö)',  idKok: 'vakif_o' },
    'nakit':    { ad: 'Nakit (Ev)',     idKok: 'nakit' },
    'vakif_a':  { ad: 'Vakıfbank (A)',  idKok: 'vakif_a' },
    'halkbank_o': { ad: 'Halkbank (Ö)', idKok: 'halkbank_o' }
};

// MODAL AÇILIŞ TETİKLEYİCİSİ
function akilliTransferModaliAc(havuzKey) {
    if (!finansalHavuzlar[havuzKey]) return;

    aktifKaynakHavuz = havuzKey;
    let anaparaMetni = $('#' + finansalHavuzlar[havuzKey].idKok + '_anapara').text() || '0,00';
    aktifKaynakAnapara = paraTemizle(anaparaMetni);

    $('#transferKaynakMetni').text('KAYNAK HESAP: ' + finansalHavuzlar[havuzKey].ad);
    $('#transferKaynakBakiyeMetni').text('Mevcut Anapara: ' + paraFormatla(aktifKaynakAnapara) + ' ₺');

    let $hedefSelect = $('#transferHedefHavuz');
    $hedefSelect.empty().append('<option value="">-- Hedef Havuz Seçiniz --</option>');
    
    $.each(finansalHavuzlar, function(key, havuz) {
        if (key !== aktifKaynakHavuz) {
            $hedefSelect.append(`<option value="${key}">${havuz.ad}</option>`);
        }
    });

    $('#transferTutar').val('');
    $('#transferSenaryoPaneli').addClass('d-none');
    $('#akilliBütçeTagAlani').addClass('d-none');
    $('#akilliTransferModal').modal('show');
}

// SEÇİLEN HEDEF HAVUZUN KODUNA GÖRE TAM OTOMATİK VERİTABANI ETİKET FİLTRELEMESİ
$('#transferHedefHavuz').on('change', function() {
    let hedefHavuzKodu = $(this).val();
    let $tagKonteyner = $('#tagKonteyner');
    $tagKonteyner.empty();

    if (!hedefHavuzKodu) {
        $('#akilliBütçeTagAlani').addClass('d-none');
        return;
    }

    // Tam Otomatik Filtreleme: Gider kalemlerindeki havuz_kodu ile seçilen hedef havuz kodunu birebir eşleştiriyoruz
    // Böylece vakif_o seçilirse sadece bütçe akışında havuz_kodu 'vakif_o' olan faturalar gelir, nakit olan aidat asla karışmaz.
    let havuzFiltreliGiderler = bekleyenButceGiderleri.filter(g => g.havuz_kodu === hedefHavuzKodu);
    let toplamGiderTutari = 0;

    if (havuzFiltreliGiderler.length > 0) {
        $('#akilliBütçeTagAlani').removeClass('d-none');

                havuzFiltreliGiderler.forEach(g => {
            toplamGiderTutari += g.tutar;
            $tagKonteyner.append(`
                <button type="button" class="btn akilli-butce-tag" data-tutar="${g.tutar}">
                    ${g.ad}: ${paraFormatla(g.tutar)} ₺
                </button>
            `);
        });

        if (havuzFiltreliGiderler.length > 1) {
            $tagKonteyner.prepend(`
                <button type="button" class="btn akilli-butce-tag-toplam me-1" data-tutar="${toplamGiderTutari}">
                    <i class="fas fa-layer-group me-1"></i> TOPLAM G GİDER: ${paraFormatla(toplamGiderTutari)} ₺
                </button>
            `);
        }

    } else {
        $('#akilliBütçeTagAlani').addClass('d-none');
    }
});
 

// BAKIYE KONTROLÜ
$(document).on('click', '#btnTransferKontcelEt', function() { // İsim yazım hatası düzeltildi
    // Buton tetikleyicisi kontrol mantığı
});
$(document).on('click', '#btnTransferKontrolEt', function() {
    let hedefHavuz = $('#transferHedefHavuz').val();
    let girilenTutar = paraTemizle($('#transferTutar').val());

    if (!hedefHavuz) { alert('Lütfen bir hedef havuz seçiniz.'); return; }
    if (girilenTutar <= 0) { alert('Lütfen geçerli bir transfer tutarı giriniz.'); return; }

    $('#transferSenaryoPaneli').removeClass('d-none');
    $('#senaryoYeterli, #senaryoYetersiz').addClass('d-none');
    $('#cozumSadeceMevcut').prop('checked', true);
    $('#destekHavuzSecimAlani').addClass('d-none');

    if (aktifKaynakAnapara >= girilenTutar) {
        $('#senaryoYeterli').removeClass('d-none');
    } else {
        let eksikTutar = girilenTutar - aktifKaynakAnapara;
        $('#eksikTutarMetni').text(paraFormatla(eksikTutar));
        $('#eksikTutarMetni1').text(paraFormatla(eksikTutar));
        $('#mevcutAktarilabilirMetin').text(paraFormatla(aktifKaynakAnapara));
        $('#mevcutAktarilabilirMetin1').text(paraFormatla(aktifKaynakAnapara));
        $('#senaryoYetersiz').removeClass('d-none');

        let $destekSelect = $('#transferDestekHavuz');
        $destekSelect.empty().append('<option value="">-- Destek Havuz Seçiniz --</option>');
        
        let destekBulundu = false;
        $.each(finansalHavuzlar, function(key, havuz) {
            if (key !== aktifKaynakHavuz && key !== hedefHavuz) {
                let havuzAnapara = paraTemizle($('#' + havuz.idKok + '_anapara').text() || '0');
                if (havuzAnapara >= eksikTutar) {
                    $destekSelect.append(`<option value="${key}">${havuz.ad} (Anapara: ${paraFormatla(havuzAnapara)} ₺)</option>`);
                    destekBulundu = true;
                }
            }
        });

        if (!destekBulundu) {
            $destekSelect.append('<option value="" disabled>Eksiği tek kalemde kapatabilecek havuz yok!</option>');
        }
    }
});

$(document).on('change', 'input[name="yetersizCozumTuru"]', function() {
    if ($(this).val() === 'destekli') {
        $('#destekHavuzSecimAlani').removeClass('d-none');
    } else {
        $('#destekHavuzSecimAlani').addClass('d-none');
    }
});

// NİHAİ TRANSFER ONAYI VE BACKEND FIRLATMA
$(document).on('click', '#btnTransferiOnayla', function() {
    let btn = $(this);
    let hedefHavuz = $('#transferHedefHavuz').val();
    let girilenTutar = paraTemizle($('#transferTutar').val());
    let cozumTuru = $('input[name="yetersizCozumTuru"]:checked').val();
    let destekHavuz = $('#transferDestekHavuz').val();

    if (aktifKaynakAnapara < girilenTutar && cozumTuru === 'destekli' && !destekHavuz) {
        alert('Lütfen eksiği tamamlayacak destek havuzunu seçiniz.');
        return;
    }

    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Havuzlar İşleniyor...');

    $.ajax({
        url: '', 
        type: 'POST',
        data: {
            islem: 'mevduat_transfer',
            kaynak_havuz: aktifKaynakHavuz,
            hedef_havuz: hedefHavuz,
            toplam_talep_tutari: girilenTutar,
            cozum_turu: (aktifKaynakAnapara >= girilenTutar) ? 'yeterli' : cozumTuru,
            destek_havuz: destekHavuz
        },
        dataType: 'json',
            success: function(response) {
            if (response.success) {
                // Başarılı durumda alert kaldırıldı
                // Eğer uygulamanızda monitorGuncelle fonksiyonu tanımlıysa tetikler, yoksa yedek olarak sayfayı yeniler
                if (typeof window.monitorGuncelle === 'function') {
                    window.monitorGuncelle();
                    // Modalı şık bir şekilde kapatıyoruz
                    $('#akilliTransferModal').modal('hide');
                    btn.prop('disabled', false).html('<i class="fas fa-check-double me-1"></i> Likiditeyi Havuzlara İşle');
                } else {
                    // Eğer fonksiyon o an bulunamazsa veri kaybını önlemek için güvenli liman olarak sayfayı yeniler
                    location.reload();
                }
            } else {
                alert('⚠️ İşlem Başarısız: ' + response.message);
                btn.prop('disabled', false).html('<i class="fas fa-check-double me-1"></i> Likiditeyi Havuzlara İşle');
            }
        },
        error: function(xhr) {
            console.error("Hata Detayı:", xhr.responseText);
            alert('❌ Sistemsel bir bağlantı hatası oluştu. Likidite havuz bütünlüğü korundu.');
            btn.prop('disabled', false).html('<i class="fas fa-check-double me-1"></i> Likiditeyi Havuzlara İşle');
        }

    });
});

// =========================================================================
// 💳 MODÜLER KREDİ KARTI VE TAKSİT HESAPLAMA MOTORU
// =========================================================================
function hariciKrediKartiMatrisiniHesapla() {
    // 1. Yardımcı Para Formatlama Rozeti
    const localParaFormatla = (rakam) => {
        return new Intl.NumberFormat('tr-TR', { 
            style: 'currency', 
            currency: 'TRY', 
            minimumFractionDigits: 2, 
            maximumFractionDigits: 2 
        }).format(rakam);
    };

    // 2. Aktif Dönem / Ay Endeksinin Üst Rozetten Canlı Cımbızlanması (TAMAMEN DİNAMİK)
    const badgeElement = document.querySelector('.badge.bg-primary');
    
    // Rozet okunamazsa, tarayıcının o anki canlı tarih nesnesinden cari ayı (1-12) otomatik çeker.
    let aktifAyIndex = new Date().getMonth() + 1; 
    
    if (badgeElement) {
        let temizMetin = badgeElement.innerText.trim(); // Örn: "2026-08" veya "2026-09"
        if (temizMetin.indexOf('-') > -1) { 
            const parts = temizMetin.split('-');
            if (parts.length > 1) {
                // Tire işaretinin sağındaki dinamik ay parçasını (örn: "08") alır ve tam sayıya (8) çevirir
                aktifAyIndex = parseInt(parts[1], 10) || (new Date().getMonth() + 1); 
            }
        }
    }

    // Değişken Matrisleri
    let isbankAktifTaksit = 0; let isbankKalanTaksit = 0;
    let vakifAktifTaksit  = 0; let vakifKalanTaksit  = 0;

    // 3. `cc_taksitler` Tablo Yansıması Olan Input Döngüleri
    const isbankTaksitInputs = document.querySelectorAll('.isbank-taksit-input');
    const vakifTaksitInputs  = document.querySelectorAll('.vakif-taksit-input');

    // İşbank Taksit Ayrıştırma (ay_index kolonu ile birebir senkron)
    isbankTaksitInputs.forEach((input, index) => {
        const val = parseFloat(input.value) || 0;
        const ayIdx = index + 1; // 1-12 arası aylık matris
        if (ayIdx === aktifAyIndex) {
            isbankAktifTaksit = val; // İçinde bulunulan ay ödenecek olan
        } else if (ayIdx > aktifAyIndex) {
            isbankKalanTaksit += val; // Gelecek aylara sarkan toplam yük
        }
    });

    // Vakıfbank Taksit Ayrıştırma (ay_index kolonu ile birebir senkron)
    vakifTaksitInputs.forEach((input, index) => {
        const val = parseFloat(input.value) || 0;
        const ayIdx = index + 1;
        if (ayIdx === aktifAyIndex) {
            vakifAktifTaksit = val; // İçinde bulunulan ay ödenecek olan
        } else if (ayIdx > aktifAyIndex) {
            vakifKalanTaksit += val; // Gelecek aylara sarkan toplam yük
        }
    });

    // 4. Cari Göstergelerin Arayüze Jilet Gibi Basılması
    const isbankAktifGosterge = document.getElementById('isbank_aktif_taksit_gosterge');
    if (isbankAktifGosterge) isbankAktifGosterge.value = localParaFormatla(isbankAktifTaksit);
    
    const vakifAktifGosterge = document.getElementById('vakif_aktif_taksit_gosterge');
    if (vakifAktifGosterge) vakifAktifGosterge.value = localParaFormatla(vakifAktifTaksit);

    const isbankKalanGosterge = document.getElementById('isbank_kalan_taksitler');
    if (isbankKalanGosterge) isbankKalanGosterge.value = localParaFormatla(isbankKalanTaksit);
    
    const vakifKalanGosterge = document.getElementById('vakif_kalan_taksitler');
    if (vakifKalanGosterge) vakifKalanGosterge.value = localParaFormatla(vakifKalanTaksit);

    // 5. `kredi_kartlari` Tablo Karşılıkları (DÜZELTİLDİ: PDF'teki tire çizgili DOM ID yapısıyla tam senkronize edildi) 🎯
    const isbankDonemIci  = parseFloat(document.getElementById('isbank-donem-ici-input')?.value) || 0;
    const isbankProvizyon = parseFloat(document.getElementById('isbank-provizyon-input')?.value) || 0;
    const isbankDoviz     = parseFloat(document.getElementById('isbank-doviz-input')?.value) || 0;
    const isbankEkstre    = parseFloat(document.getElementById('isbank-ekstre-input')?.value) || 0;

    const vakifDonemIci   = parseFloat(document.getElementById('vakif-donem-ici-input')?.value) || 0;
    const vakifProvizyon  = parseFloat(document.getElementById('vakif-provizyon-input')?.value) || 0;
    const vakifDoviz      = parseFloat(document.getElementById('vakif-doviz-input')?.value) || 0;
    const vakifEkstre     = parseFloat(document.getElementById('vakif-ekstre-input')?.value) || 0;

    // Veritabanı ekran görüntünüzde doğrulanan mutlak limit matrahları
    const isbankLimit = 321100.00;
    const vakifLimit  = 263500.00;

    // 6. BANKACILIK FORMÜLLERİ ZIRHLAMASI
    // Kesinleşen Dönem Borcu = Aktif Taksit + Dönem İçi + Provizyon + Döviz + Ekstre
    const isbankKesinlesen = isbankAktifTaksit + isbankDonemIci + isbankProvizyon + isbankDoviz + isbankEkstre;
    const vakifKesinlesen  = vakifAktifTaksit + vakifDonemIci + vakifProvizyon + vakifDoviz + vakifEkstre;

    const isbankKesinlesenGosterge = document.getElementById('isbank_kesinlesen_donem');
    if (isbankKesinlesenGosterge) isbankKesinlesenGosterge.value = localParaFormatla(isbankKesinlesen);
    
    const vakifKesinlesenGosterge = document.getElementById('vakif_kesinlesen_donem');
    if (vakifKesinlesenGosterge) vakifKesinlesenGosterge.value = localParaFormatla(vakifKesinlesen);

    // Toplam Borç = Kesinleşen Dönem Borcu + Gelecek Kalan Taksit Yükü
    const isbankToplamBorc = isbankKesinlesen + isbankKalanTaksit;
    const vakifToplamBorc  = vakifKesinlesen + vakifKalanTaksit;

    const isbankToplamBorcGosterge = document.getElementById('isbank_toplam_borc');
    if (isbankToplamBorcGosterge) isbankToplamBorcGosterge.value = localParaFormatla(isbankToplamBorc);
    
    const vakifToplamBorcGosterge = document.getElementById('vakif_toplam_borc');
    if (vakifToplamBorcGosterge) vakifToplamBorcGosterge.value = localParaFormatla(vakifToplamBorc);

    // Kalan Boş Limit = Sabit Kart Limiti - Toplam Borç
    const isbankKalanLimit = Math.max(0, isbankLimit - isbankToplamBorc);
    const vakifKalanLimit  = Math.max(0, vakifLimit - vakifToplamBorc);

    const isbankKalanLimitGosterge = document.getElementById('isbank_kalan_limit');
    if (isbankKalanLimitGosterge) isbankKalanLimitGosterge.value = localParaFormatla(isbankKalanLimit);
    
    const vakifKalanLimitGosterge = document.getElementById('vakif_kalan_limit');
    if (vakifKalanLimitGosterge) vakifKalanLimitGosterge.value = localParaFormatla(vakifKalanLimit);

    // 7. Hesaplanan Tüm Değerleri Ana Fonksiyona Güvenle Paketleyip Gönder
    return {
        isbank: {
            aktifTaksit: isbankAktifTaksit,
            kalanTaksit: isbankKalanTaksit,
            donemIci: isbankDonemIci,
            provizyon: isbankProvizyon,
            doviz: isbankDoviz,
            ekstre: isbankEkstre,
            kesinlesen: isbankKesinlesen,
            toplamBorc: isbankToplamBorc,
            kalanLimit: isbankKalanLimit
        },
        vakif: {
            aktifTaksit: vakifAktifTaksit,
            kalanTaksit: vakifKalanTaksit,
            donemIci: vakifDonemIci,
            provizyon: vakifProvizyon,
            doviz: vakifDoviz,
            ekstre: vakifEkstre,
            kesinlesen: vakifKesinlesen,
            toplamBorc: vakifToplamBorc,
            kalanLimit: vakifKalanLimit
        }
    };
}


// =========================================================================
// TO DO LİST KISMI VE AKIŞ.
// =========================================================================

function renderNihaiDashboardCore() {
    //KREDİ KARTI VERİLERİ PARAMETRELER.

    const ccMatrisi = hariciKrediKartiMatrisiniHesapla();
    const isbankAktifTaksit = ccMatrisi.isbank.aktifTaksit;
    const isbankKalanTaksit = ccMatrisi.isbank.kalanTaksit;
    const isbankDonemIci    = ccMatrisi.isbank.donemIci;
    const isbankProvizyon   = ccMatrisi.isbank.provizyon;
    const isbankDoviz       = ccMatrisi.isbank.doviz;
    const isbankEkstre      = ccMatrisi.isbank.ekstre;
    const isbankKesinlesen  = ccMatrisi.isbank.kesinlesen;
    const isbankToplamBorc  = ccMatrisi.isbank.toplamBorc;
    const isbankKalanLimit  = ccMatrisi.isbank.kalanLimit;
    const vakifAktifTaksit  = ccMatrisi.vakif.aktifTaksit;
    const vakifKalanTaksit  = ccMatrisi.vakif.kalanTaksit;
    const vakifDonemIci     = ccMatrisi.vakif.donemIci;
    const vakifProvizyon    = ccMatrisi.vakif.provizyon;
    const vakifDoviz        = ccMatrisi.vakif.doviz;
    const vakifEkstre       = ccMatrisi.vakif.ekstre;
    const vakifKesinlesen   = ccMatrisi.vakif.kesinlesen;
    const vakifToplamBorc   = ccMatrisi.vakif.toplamBorc;
    const vakifKalanLimit   = ccMatrisi.vakif.kalanLimit;
    const yeniDnmIciHarcamalar   = isbankDonemIci + vakifDonemIci;
    const yeniDonemAitTaksitleri = isbankAktifTaksit + vakifAktifTaksit;
    const yeniDovizBorclariToplam = isbankDoviz + vakifDoviz;

    // B. PHP Katmanından Gelen Canlı Veritabanı Değerlerinin Yakalanması
    const divMatrah = document.getElementById('data_havuz_matrahlari');
    const anaIsbankA = divMatrah ? parseFloat(divMatrah.dataset.isbankA) || 0 : 0;
    const anaIsbankO = divMatrah ? parseFloat(divMatrah.dataset.isbankO) || 0 : 0;
    const anaVakifO  = divMatrah ? parseFloat(divMatrah.dataset.vakifO)  || 0 : 0;
    const anaNakit   = divMatrah ? parseFloat(divMatrah.dataset.nakit)   || 0 : 0;
    const nemaA      = divMatrah ? parseFloat(divMatrah.dataset.nemaA)   || 0 : 0;
    const nemaO      = divMatrah ? parseFloat(divMatrah.dataset.nemaO)   || 0 : 0;
    const evKredisi  = 53315.33;
    

    // C. CARD 1 & CARD 2: CÜZDAN VE TOTAL BAKİYE MATRİSLERİ HESABI
    const js_yeni_mevcut_bakiye = anaIsbankA + anaIsbankO + anaVakifO + anaNakit;
    const js_yeni_elde_edilen_faiz = nemaA + nemaO;
    const js_yeni_toplam_bakiye = js_yeni_mevcut_bakiye + js_yeni_elde_edilen_faiz;
    const isbank_a_son_bakiye = anaIsbankA + nemaA;


  

    document.getElementById('js_yeni_mevcut_bakiye').innerHTML = paraFormatlaTL(js_yeni_mevcut_bakiye);
    document.getElementById('js_yeni_elde_edilecek_faiz').innerHTML = "+" + paraFormatlaTL(js_yeni_elde_edilen_faiz);
    document.getElementById('js_yeni_toplam_bakiye').innerHTML = paraFormatlaTL(js_yeni_toplam_bakiye);

    // D. CARD 4: DİĞER GİDERLER (BLOKE) MATRİSLERİ HESABI (tutar - odenen)
    const elFatBloke = document.getElementById('js_yeni_faturalar_bloke');
    const elSuBloke  = document.getElementById('js_dashboard_gider_su');
    const elKreBloke = document.getElementById('js_dashboard_gider_kredi');

    const js_yeni_faturalar_bloke = elFatBloke ? parseFloat(elFatBloke.dataset.taban) || 0 : 0;
    const js_dashboard_gider_su   = elSuBloke  ? parseFloat(elSuBloke.dataset.taban)  || 0 : 0;
    const js_dashboard_gider_kredi = elKreBloke ? parseFloat(elKreBloke.dataset.taban) || 0 : 0;

    const tasarruf_tutari = isbank_a_son_bakiye - isbankEkstre - vakifEkstre - js_yeni_faturalar_bloke - js_dashboard_gider_su;
      // Ekran basımları
    const elTasarruf = document.getElementById('canli-ana-yatirim-bakiye');
    if (elTasarruf) elTasarruf.innerHTML = paraFormatlaTL(tasarruf_tutari);
    const js_dashboard_mutlak_toplam_borc = js_yeni_faturalar_bloke + js_dashboard_gider_su + js_dashboard_gider_kredi;

    if (elFatBloke) elFatBloke.innerHTML = paraFormatlaTL(js_yeni_faturalar_bloke);
    if (elSuBloke)  elSuBloke.innerHTML  = paraFormatlaTL(js_dashboard_gider_su);
    if (elKreBloke) elKreBloke.innerHTML = paraFormatlaTL(js_dashboard_gider_kredi);
    document.getElementById('js_dashboard_mutlak_toplam_borc').innerHTML = paraFormatlaTL(js_dashboard_mutlak_toplam_borc);

    // E. CARD 3 & CARD 5: KREDİ KARTI RISK VE TOPLAM BORÇ MATRİSLERİ HESABI
    const js_dashboard_donem_ici        = isbankDonemIci + vakifDonemIci;
    const js_dashboard_provizyon_toplami = isbankProvizyon + vakifProvizyon;
    const js_dashboard_donem_taksitleri  = isbankAktifTaksit + vakifAktifTaksit;
    const js_dashboard_doviz_borcu       = isbankDoviz + vakifDoviz;

  
    const js_dashboard_isbank_kekstre = isbankEkstre;
    const js_dashboard_vakif_kekstre = vakifEkstre;
    const js_dashboard_kesinlesen_ekstre = isbankEkstre + vakifEkstre;
    const js_yeni_kk_borcumuz            = isbankKalanTaksit + vakifKalanTaksit;
    const js_dashboard_kk_toplam_borclari = js_dashboard_kesinlesen_ekstre + js_yeni_kk_borcumuz;

    // Değerlerin kart hücrelerine enjekte edilmesi
    document.getElementById('js_dashboard_donem_ici').innerHTML = paraFormatlaTL(isbankDonemIci + vakifDonemIci);
    document.getElementById('js_dashboard_provizyon_toplami').innerHTML = paraFormatlaTL(js_dashboard_provizyon_toplami);
    document.getElementById('js_dashboard_donem_taksitleri').innerHTML = paraFormatlaTL(js_dashboard_donem_taksitleri);
    document.getElementById('js_dashboard_doviz_borcu').innerHTML = paraFormatlaTL(js_dashboard_doviz_borcu);
    
    document.getElementById('js_yeni_kk_donem_borcu').innerHTML = paraFormatlaTL(js_dashboard_donem_ici + js_dashboard_provizyon_toplami);
    document.getElementById('js_dashboard_isbank_kekstre').innerHTML = paraFormatlaTL(isbankEkstre);
    document.getElementById('js_dashboard_vakif_kekstre').innerHTML = paraFormatlaTL(vakifEkstre);
    document.getElementById('js_dashboard_kesinlesen_ekstre').innerHTML = paraFormatlaTL(js_dashboard_kesinlesen_ekstre);
    document.getElementById('js_yeni_kk_borcumuz').innerHTML = paraFormatlaTL(js_yeni_kk_borcumuz);
    document.getElementById('js_dashboard_kk_toplam_borclari').innerHTML = paraFormatlaTL(js_dashboard_kk_toplam_borclari);

    // F. 📜 TO-DO TRANSFER EMİRLERİ LOJİSTİK HESAPLAMA AYRIŞTIRMASI
    // Söylediğiniz kurallara göre net atamalar yapılıyor
    const todo_c2_tutar = isbankEkstre > 0 ? isbankEkstre : isbankKesinlesen;
    const todo_c1_tutar = vakifEkstre > 0  ? vakifEkstre  : vakifKesinlesen;
    
    const todo_c5_tutar  = todo_c1_tutar;
    const todo_c8_tutar  = js_yeni_faturalar_bloke;
    const todo_c7_tutar  = js_dashboard_gider_su;
    const todo_c4_tutar1 = js_dashboard_gider_kredi;
    const todo_c4_tutar2 = evKredisi - js_dashboard_gider_kredi;

    // To-Do hücre bütçelerinin mühürlenmesi
    document.getElementById('todo_c2_tutar').innerHTML = paraFormatlaTL(todo_c2_tutar);
    document.getElementById('todo_c0_tutar').innerHTML = paraFormatlaTL(todo_c2_tutar);
    
    // C1 Toplam Transfer Formülü = Vakıf Ekstre/Kesinlesen + Faturalar Bloke + Aidat/Su Bloke
    const c1_toplam_transfer = todo_c1_tutar + js_yeni_faturalar_bloke + js_dashboard_gider_su;
    document.getElementById('todo_c1_tutar').innerHTML = paraFormatlaTL(c1_toplam_transfer);
    
    document.getElementById('todo_c5_tutar').innerHTML = paraFormatlaTL(todo_c5_tutar);
    document.getElementById('todo_c8_tutar').innerHTML = paraFormatlaTL(todo_c8_tutar);
    document.getElementById('todo_c7_tutar').innerHTML = paraFormatlaTL(todo_c7_tutar);
    document.getElementById('todo_c4_tutar1').innerHTML = paraFormatlaTL(todo_c4_tutar1);
    
    // İşbank Ö kalan nema tutarı aktarımı emniyet hesabı
    document.getElementById('todo_c4_tutar2').innerHTML = paraFormatlaTL(todo_c4_tutar2);
}


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

// ======================================================================
//   DÖVİZ ALIM SATIM MODÜLÜ
// ======================================================================

// --- ÜST KAPILAR VE HAFIZA ---
const modalLiveKurlar = {
    'USD': parseFloat('<?= (float)($live_kurlar['USD'] ?? 46.8215) ?>'),
    'EUR': parseFloat('<?= (float)($live_kurlar['EUR'] ?? 53.8685) ?>'),
    'XAU': parseFloat('<?= (float)($live_kurlar['XAU'] ?? 6086.4200) ?>')
};

let modalSonAktifGiris = 'TL'; 

function forexAction(type) {
    if (type === 'isbank_k') {
        const form = document.getElementById('forexSatinalForm');
        if (form) { form.reset(); }
        
        if (document.getElementById('modal_btn_panel_onayla')) document.getElementById('modal_btn_panel_onayla').disabled = true;
        if (document.getElementById('modal_canli_kur_text')) document.getElementById('modal_canli_kur_text').textContent = "0,0000 ₺";
        if (document.getElementById('modal_maks_kapasite_text')) document.getElementById('modal_maks_kapasite_text').textContent = "0,00";
        if (document.getElementById('modal_panel_nihai_sonuc')) document.getElementById('modal_panel_nihai_sonuc').value = "0,00 ₺";
        if (document.getElementById('modal_panel_vergi_bilgi_rozet')) document.getElementById('modal_panel_vergi_bilgi_rozet').textContent = "Vergi: 0,00 ₺";
        if (document.getElementById('modal_panel_tutar_bilgi_rozet')) document.getElementById('modal_panel_tutar_bilgi_rozet').textContent = "Ana Tutar: 0,00 ₺";
        if (document.getElementById('modal_panel_tutar_bilgi_input')) document.getElementById('modal_panel_tutar_bilgi_input').value = "0,00 ₺";
        
        const elUyari = document.getElementById('modal_panel_bakiye_uyari_metni');
        if (elUyari) elUyari.classList.add('d-none');
        
        var myModal = new bootstrap.Modal(document.getElementById('forexYatirimModal'));
        myModal.show();
    }
}


function hesaplaModalYatirimMatematigi(tetikleyici = null) {
    if (tetikleyici) modalSonAktifGiris = tetikleyici;

    const elAktarilacak = document.getElementById('modal_aktarilacak_hesap');
    const btnSubmit = document.getElementById('modal_btn_panel_onayla');

    // Koruma bariyeri: Varlık cinsi seçilmediyse kilitli kal
    if (!elAktarilacak || !elAktarilacak.value) {
        if (btnSubmit) btnSubmit.disabled = true;
        return;
    }

    const varlikCinsi = elAktarilacak.value; 
    const elBakiyeElementi = document.getElementById('modal-tasarruf-bakiye');
    const bakiyeTL = elBakiyeElementi ? (parseFloat(elBakiyeElementi.dataset.tl) || 0) : 0;

    const safCanliKur = modalLiveKurlar[varlikCinsi] || 0;
    const elManuelKur = document.getElementById('modal_manuel_kur');
    const manuelKur = elManuelKur ? (parseFloat(elManuelKur.value) || 0) : 0;
    const aktifIslemKuru = (manuelKur > 0) ? manuelKur : safCanliKur;
    const basamak = (varlikCinsi === 'XAU') ? 2 : 4;

    document.getElementById('modal_canli_kur_text').textContent = aktifIslemKuru.toLocaleString('tr-TR', { minimumFractionDigits: basamak }) + " ₺";
    document.getElementById('modal_lbl_islem_miktari').innerHTML = `<i class="fas fa-coins me-1 text-muted"></i> Miktar Belirle (${varlikCinsi})`;

    // Maksimum kapasite limit hesabı (%0.2 kambiyo vergisi arındırılarak)
    const netKapasiteTL = bakiyeTL / 1.0020; 
    const maksAlinabilir = netKapasiteTL / aktifIslemKuru;
    document.getElementById('modal_maks_kapasite_text').textContent = maksAlinabilir.toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + " " + varlikCinsi;

    if (aktifIslemKuru <= 0) return;

    const elTutarInput = document.getElementById('modal_tutar_tl');
    const elMiktarInput = document.getElementById('modal_miktar');
    let finalTutarTL = 0;
    let finalMiktarAdet = 0;

    // Giriş yönüne göre akıllı çapraz hesaplama
    if (modalSonAktifGiris === 'TL') {
        finalTutarTL = elTutarInput ? (parseFloat(elTutarInput.value) || 0) : 0;
        if (finalTutarTL > 0) {
            let netYatirim = finalTutarTL / 1.0020;
            finalMiktarAdet = netYatirim / aktifIslemKuru;
            if (elMiktarInput) elMiktarInput.value = finalMiktarAdet.toFixed(basamak);
        } else { if (elMiktarInput) elMiktarInput.value = ''; }
    } else {
        finalMiktarAdet = elMiktarInput ? (parseFloat(elMiktarInput.value) || 0) : 0;
        if (finalMiktarAdet > 0) {
            finalTutarTL = (finalMiktarAdet * aktifIslemKuru) * 1.0020;
            if (elTutarInput) elTutarInput.value = finalTutarTL.toFixed(2);
        } else { if (elTutarInput) elTutarInput.value = ''; }
    }

    const elVergiRozet = document.getElementById('modal_panel_vergi_bilgi_rozet');
    const elTutarRozet = document.getElementById('modal_panel_tutar_bilgi_rozet');
    const elNihaiSonuc = document.getElementById('modal_panel_nihai_sonuc');
    const elUyari = document.getElementById('modal_panel_bakiye_uyari_metni');
    const elGizliTutarInput = document.getElementById('modal_panel_tutar_bilgi_input');

    if (finalTutarTL > 0 || finalMiktarAdet > 0) {
        let vergi = finalTutarTL - (finalTutarTL / 1.0020);
        let anaTutarNet = finalTutarTL - vergi;

        if (elVergiRozet) elVergiRozet.textContent = "Vergi: " + paraFormatlaTL(vergi);
        if (elNihaiSonuc) elNihaiSonuc.value = paraFormatlaTL(finalTutarTL);
        if (elTutarRozet) elTutarRozet.textContent = "Ana Tutar: " + paraFormatlaTL(anaTutarNet);
        
        // İstediğiniz kilit gizli input eşleşmesi anlık besleniyor
        if (elGizliTutarInput) elGizliTutarInput.value = paraFormatlaTL(anaTutarNet);

        // Bakiye yetersizlik kalkanı kontrolü
        if (finalTutarTL > bakiyeTL) {
            if (elUyari) elUyari.classList.remove('d-none');
            if (btnSubmit) btnSubmit.disabled = true;
        } else {
            if (elUyari) elUyari.classList.add('d-none');
            if (btnSubmit) btnSubmit.disabled = false;
        }
    } else {
        if (elNihaiSonuc) elNihaiSonuc.value = "0,00 ₺";
        if (elVergiRozet) elVergiRozet.textContent = "Vergi: 0,00 ₺";
        if (elTutarRozet) elTutarRozet.textContent = "Ana Tutar: 0,00 ₺";
        if (elGizliTutarInput) elGizliTutarInput.value = "0,00 ₺";
        if (btnSubmit) btnSubmit.disabled = true;
    }
}

// ===============================================================================
// MERKEZİ ASENKRON RE-RENDER VE SANAL DOM RESTORE MOTORU (DÖVİZ VE LİMİT REFORMLU) 🚀
// ===============================================================================
window.monitorGuncelle = function(callbackGeriCagir) {
    const urlParametreAyirici = window.location.href.indexOf('?') > -1 ? '&' : '?';
    const guvenliUrl = window.location.href + urlParametreAyirici + 't=' + new Date().getTime();

    fetch(guvenliUrl)
        .then(res => {
            if (!res.ok) throw new Error(`HTTP Hatası! Durum: ${res.status}`);
            return res.text();
        })
        .then(html => {
            const parser = new DOMParser(); 
            const doc = parser.parseFromString(html, 'text/html');
            
            const aktifElemanId = document.activeElement ? document.activeElement.id : null;

            // Güncellenecek tüm kritik DOM seçicilerinin listesi 🎯
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
                '#data_havuz_matrahlari',     
                '.badge.bg-primary',         
                '#nakit_kutu_bakiye',   
                '#vakif_kutu_bakiye',   
                '#isbank_kk_kutu_bakiye',    
                '#isbank_o_kutu_bakiye',  
                '#todo_c0_tutar', 
                '#todo_c1_tutar', 
                '#todo_c2_tutar', 
                '#todo_c3_tutar', 
                '#todo_c4_tutar1', 
                '#todo_c4_tutar2',  
                '#todo_c5_tutar', 
                '#todo_c6_tutar',
                // Donup kalan alt/üst gösterge girdilerinin tam listesi eklendi
                '#isbank_kesinlesen_donem',
                '#vakif_kesinlesen_donem',
                '#isbank_toplam_borc',
                '#vakif_toplam_borc',
                '#isbank_kalan_limit',
                '#vakif_kalan_limit'
            ];

            seciciler.forEach(secici => {
                const yeniEl = doc.querySelector(secici); 
                const mevcutEl = document.querySelector(secici);
                
                if (yeniEl && mevcutEl) {
                    // REFORM: Eğer eleman bir INPUT ise içeriğini .value ile eşitle! (Kutuların donmasını çözen yer) 🎯
                    if (mevcutEl.tagName === 'INPUT') {
                        if (mevcutEl.id !== aktifElemanId) {
                            mevcutEl.value = yeniEl.value;
                        }
                    } else {
                        // Diğer standart div/span alanları için innerHTML akışını koru
                        const b_isbank = document.getElementById('mobil_isbank_kull_limit')?.value;
                        const b_vakif  = document.getElementById('mobil_vakif_kull_limit')?.value;
                        
                        mevcutEl.innerHTML = yeniEl.innerHTML;
                        
                        if (b_isbank && document.getElementById('mobil_isbank_kull_limit') && 'mobil_isbank_kull_limit' !== aktifElemanId) {
                            document.getElementById('mobil_isbank_kull_limit').value = b_isbank;
                        }
                        if (b_vakif && document.getElementById('mobil_vakif_kull_limit') && 'mobil_vakif_kull_limit' !== aktifElemanId) {
                            document.getElementById('mobil_vakif_kull_limit').value = b_vakif;
                        }
                    }
                }
            });

            // Gelişmiş yerel hesaplama motorunu tetikle
            if (typeof hariciKrediKartiMatrisiniHesapla === 'function') { 
                hariciKrediKartiMatrisiniHesapla(); 
            }

            // Bootstrap tooltip balonlarını yeniden canlandır
            if (typeof window.bootstrapTooltipYenile === 'function') {
                window.bootstrapTooltipYenile();
            }

            // Odaklanma (Focus) ve imleç koruma kalkanı
            if (aktifElemanId) {
                const yenidenOdaklan = document.getElementById(aktifElemanId);
                if (yenidenOdaklan) {
                    const uzunluk = yenidenOdaklan.value ? yenidenOdaklan.value.length : 0;
                    yenidenOdaklan.focus();
                    if (typeof yenidenOdaklan.setSelectionRange === 'function' && yenidenOdaklan.type === 'text') {
                        yenidenOdaklan.setSelectionRange(uzunluk, uzunluk);
                    }
                }
            }

            if (typeof callbackGeriCagir === 'function') {
                callbackGeriCagir();
            }
        })
        .catch(err => {
            console.warn("⚠️ Sanal DOM render motoru uyarısı:", err.message);
            if (typeof callbackGeriCagir === 'function') callbackGeriCagir();
        });
}


function initMerkeziInputDinleyicileri() {


    document.addEventListener('input', function(e) {
        if (!e.target) return;

        // A. Akıllı Faiz Hesaplama Kutuları Tetikleyicisi
        if (e.target.id === 'faiz-orani-input' || 
            e.target.id === 'faiz-gun-input' || 
            e.target.id === 'faiz-kredi-butcesi-input') {
            canlıFaizOnizlemeHesapla();
        }

        // B. Modal İçi Faiz Aktarım Over-Draft (Limit Aşım) Kalkanı
        if (e.target.id === 'modal-aktar-tutar-input') {
            const tutarInput = e.target;
            const maxLimit = parseFloat(tutarInput.getAttribute('data-max')) || 0;
            const yazilan = parseFloat(tutarInput.value) || 0;
            const onayBtn = document.getElementById('kesin-faiz-aktar-onay-btn');

            if (onayBtn) {
                if (yazilan > maxLimit || yazilan <= 0) {
                    tutarInput.classList.add('is-invalid');
                    onayBtn.disabled = true;
                } else {
                    tutarInput.classList.remove('is-invalid');
                    onayBtn.disabled = false;
                }
            }
        }
    });

    document.addEventListener('change', function(e) {
        if (!e.target) return;

        // A. Faiz Kutularından Çıkıldığında Veri Tabanı Senkronu
        if (e.target.id === 'faiz-orani-input' || 
            e.target.id === 'faiz-gun-input' || 
            e.target.id === 'faiz-kredi-butcesi-input') {
            anlikKrediFaizGuncelle();
        }

        // B. Modalda Hesap Seçildiğinde Gerçek Birikmiş Nemayı Kutulara Doldurma Motoru
        if (e.target.id === 'modal-aktar-havuz-secim') {
            const select = e.target;
            const dataHavuzu = document.getElementById('data_havuz_matrahlari');
            
            const nemaA = dataHavuzu ? parseFloat(dataHavuzu.getAttribute('data-nema-a')) || 0 : 0;
            const nemaO = dataHavuzu ? parseFloat(dataHavuzu.getAttribute('data-nema-o')) || 0 : 0;

            let kullanılabilirNema = 0;
            if (select.value === 'isbank_a') kullanılabilirNema = nemaA;
            else if (select.value === 'isbank_o') kullanılabilirNema = nemaO;
            else if (select.value === 'all') kullanılabilirNema = nemaA + nemaO;

            const tutarInput = document.getElementById('modal-aktar-tutar-input');
            const onayBtn    = document.getElementById('kesin-faiz-aktar-onay-btn');
            const uyariMetni = document.getElementById('modal-aktar-uyari-metni');

            if (tutarInput && onayBtn && uyariMetni) {
                tutarInput.disabled = false;
                tutarInput.value = kullanılabilirNema.toFixed(2);
                tutarInput.setAttribute('data-max', kullanılabilirNema);
                tutarInput.classList.remove('is-invalid');
                
                uyariMetni.innerHTML = `Aktarılabilir birikmiş faiz: <span class="text-success fw-bold">${paraFormatlaTL(kullanılabilirNema)}</span>`;
                onayBtn.disabled = (kullanılabilirNema <= 0);
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target) return;

        // Butonun kendisine veya içindeki ikon/yazıya tıklanma ihtimaline karşı en yakın butonu buluyoruz
        const onayBtn = e.target.closest('#kesin-faiz-aktar-onay-btn');
        if (!onayBtn) return;

        const form = document.getElementById('faiz-aktar-secimli-form');
        if (!form) return;

        // Mükerrer (çift) tıklamayı önlemek için butonu hemen donduruyoruz
        onayBtn.disabled = true;
        const orjinalIcerik = onayBtn.innerHTML;
        onayBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mühürleniyor...`;

        // Form verilerini tarayıcı hafızasına paketliyoruz
        const formData = new FormData(form);
        
        // PHP kodunuzdaki if ($islem === 'faiz_aktar') şartını yakalaması için parametreyi ekliyoruz
        formData.append('islem', 'faiz_aktar');
        fetch(window.location.href, { method: 'POST', body: formData })
        .then(response => {
            if (!response.ok) {
                throw new Error('Sunucu hatası: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // PHP'den gelen json_encode yanıtını kontrol ediyoruz
            if (data.status === 'success') {
                alert('🎉 ' + data.message); 
                location.reload(); // Sayfa yenilenerek veritabanı ve session'lar ekrana tam oturur
            } else {
                // PHP tarafında bir hata oluştuysa (Örn: bakiye yetersiz uyarısı)
                alert(data.message);
                onayBtn.disabled = false;
                onayBtn.innerHTML = orjinalIcerik;
            }
        })
        .catch(error => {
            console.error('Mühürleme Hatası:', error);
            alert('⚠️ Sunucuyla iletişim kurulurken teknik bir hata oluştu!');
            onayBtn.disabled = false;
            onayBtn.innerHTML = orjinalIcerik;
        });
    });


    // A. CARİ AY GİRDİLERİ FORMU DİNLEYİCİSİ (Tek Bir Merkezden Yönetim)
    const formCari = document.getElementById('cari-girdiler-formu');
    if (formCari) {
        formCari.addEventListener('input', function() {
            // Önce ana matris hesaplanır
            hariciKrediKartiMatrisiniHesapla();
            
            // Ardından provizyonlar güncellenir
            if (typeof hariciProvizyonHesaplaNihai === 'function') {
                hariciProvizyonHesaplaNihai('isbank');
                hariciProvizyonHesaplaNihai('vakif');
            }
        });
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

    // C. TELEFON VE SU BİRİM FİYAT GÜNCELLEMELERİ (Change)
    const birimFiyatIds = ['telefon-birim-fiyat-10', 'su-birim-fiyat-12'];
    birimFiyatIds.forEach(id => {
        const element = document.getElementById(id);
        if (element && typeof anlikBirimFiyatlariniGuncelle === 'function') {
            element.addEventListener('change', anlikBirimFiyatlariniGuncelle);
        }
    });

    // Modalda hesap seçildiğinde gerçek birikmiş nemayı (faizi) bulma ve emniyet kilidi
    const havuzSelect = document.getElementById('modal-aktar-havuz-secim');
    if (havuzSelect) {
        havuzSelect.addEventListener('change', function() {
            const dataHavuzu = document.getElementById('data_faiz_matrahlari');
            const nemaA = dataHavuzu ? parseFloat(dataHavuzu.getAttribute('data-nema-a')) || 0 : 0;
            const nemaO = dataHavuzu ? parseFloat(dataHavuzu.getAttribute('data-nema-o')) || 0 : 0;

            let kullanılabilirNema = 0;
            if (this.value === 'isbank_a') kullanılabilirNema = nemaA;
            else if (this.value === 'isbank_o') kullanılabilirNema = nemaO;
            else if (this.value === 'all') kullanılabilirNema = nemaA + nemaO;

            const tutarInput = document.getElementById('modal-aktar-tutar-input');
            const onayBtn    = document.getElementById('kesin-faiz-aktar-onay-btn');
            const uyariMetni = document.getElementById('modal-aktar-uyari-metni');

            if (tutarInput && onayBtn && uyariMetni) {
                tutarInput.disabled = false;
                tutarInput.value = kullanılabilirNema.toFixed(2);
                tutarInput.setAttribute('data-max', kullanılabilirNema);
                tutarInput.classList.remove('is-invalid');
                
                uyariMetni.innerHTML = `Aktarılabilir birikmiş faiz: <span class="text-success fw-bold">${paraFormatlaTL(kullanılabilirNema)}</span>`;
                onayBtn.disabled = (kullanılabilirNema <= 0);
            }
        });
    }

    // Kullanıcının elindeki faizden büyük sayı yazmasını engelleyen over-draft kalkanı
    const modalTutarInput = document.getElementById('modal-aktar-tutar-input');
    if (modalTutarInput) {
        modalTutarInput.addEventListener('input', function() {
            const maxLimit = parseFloat(this.getAttribute('data-max')) || 0;
            const yazilan = parseFloat(this.value) || 0;
            const onayBtn = document.getElementById('kesin-faiz-aktar-onay-btn');

            if (onayBtn) {
                if (yazilan > maxLimit || yazilan <= 0) {
                    this.classList.add('is-invalid');
                    onayBtn.disabled = true;
                } else {
                    this.classList.remove('is-invalid');
                    onayBtn.disabled = false;
                }
            }
        });
    }

    const elAktarilacak = document.getElementById('modal_aktarilacak_hesap');
    const elManuelKur = document.getElementById('modal_manuel_kur');
    const elTutarTl = document.getElementById('modal_tutar_tl');
    const elMiktar = document.getElementById('modal_miktar');

    // Canlı input aksiyon dinleyicileri bağlama
    if (elAktarilacak) elAktarilacak.addEventListener('change', () => hesaplaModalYatirimMatematigi());
    if (elManuelKur) elManuelKur.addEventListener('input', () => hesaplaModalYatirimMatematigi());
    if (elTutarTl) elTutarTl.addEventListener('input', () => hesaplaModalYatirimMatematigi('TL'));
    if (elMiktar) elMiktar.addEventListener('input', () => hesaplaModalYatirimMatematigi('MIKTAR'));

    // TL "Bakiyenin Tümü" Dinleyicisi
    const btnMaxTl = document.getElementById('modal_btn_max_tl');
    if (btnMaxTl) {
        btnMaxTl.addEventListener('click', function() {
            const elBakiyeElementi = document.getElementById('modal-tasarruf-bakiye');
            if (elBakiyeElementi && elAktarilacak && elAktarilacak.value) {
                const safAnaparaBakiye = parseFloat(elBakiyeElementi.dataset.tl) || 0;
                if (safAnaparaBakiye > 0) {
                    if (elTutarTl) elTutarTl.value = safAnaparaBakiye.toFixed(2);
                    hesaplaModalYatirimMatematigi('TL');
                }
            } else { alert("Lütfen önce paranın aktarılacağı bütçe varlık hücresini seçiniz!"); }
        });
    }

    // Varlık Cinsi "Bakiyenin Tümü" Dinleyicisi
    const btnMaxMiktar = document.getElementById('modal_btn_max_miktar');
    if (btnMaxMiktar) {
        btnMaxMiktar.addEventListener('click', function() {
            const elBakiyeElementi = document.getElementById('modal-tasarruf-bakiye');
            if (elBakiyeElementi && elAktarilacak && elAktarilacak.value) {
                const varlikCinsi = elAktarilacak.value;
                const safAnaparaBakiye = parseFloat(elBakiyeElementi.dataset.tl) || 0;
                const safCanliKur = modalLiveKurlar[varlikCinsi] || 0;
                const manuelKur = elManuelKur ? (parseFloat(elManuelKur.value) || 0) : 0;
                const aktifIslemKuru = (manuelKur > 0) ? manuelKur : safCanliKur;
                const basamak = (varlikCinsi === 'XAU') ? 2 : 4;

                if (safAnaparaBakiye > 0 && aktifIslemKuru > 0) {
                    const netYatirimMatrahi = safAnaparaBakiye / 1.0020;
                    const alinabilirMiktar = netYatirimMatrahi / aktifIslemKuru;
                    if (elMiktar) elMiktar.value = alinabilirMiktar.toFixed(basamak);
                    hesaplaModalYatirimMatematigi('MIKTAR');
                }
            } else { alert("Lütfen önce paranın aktarılacağı bütçe varlık hücresini seçiniz!"); }
        });
    }

    // 1. KESİNLENEN DÖNEM DEVİR (KAPATMA & KLONLAMA) EMİR TETİKLEYİCİSİ
    const kesinDevirOnayBtn = document.getElementById('kesin-devir-onay-btn');
    if (kesinDevirOnayBtn) {
        kesinDevirOnayBtn.addEventListener('click', function() {
            const btn = kesinDevirOnayBtn;
            const oMetin = btn.innerHTML;
            
            // Önyüz kalkanı: Çift tıklamayı engelle ve yükleniyor animasyonunu bas
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> ⏳ Dönem Kapatılıyor & Taksitler Senkron Ediliyor...';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('islem', 'donem_devret');

            fetch('/butce/admin/management', { method: 'POST', body: formData })
            .then(res => {
                if (!res.ok) throw new Error(`Ağ Hatası: ${res.status}`);
                return res.json();
            })
            .then(data => {
                btn.innerHTML = oMetin;
                btn.disabled = false;
                
                if (data && data.status === 'success') {
                    // Açık olan Bootstrap onay modal penceresini güvenle kapat ve temizle
                    const mEl = document.getElementById('donemDevirOnayModal');
                    const modalObj = bootstrap.Modal.getInstance(mEl);
                    if (modalObj) modalObj.hide();
                    
                    // Zamanı ileri sarıp yeni dönemi render etmek üzere sayfayı yenile
                    window.location.reload();
                } else {
                    // Backend'den (Exception) gelen kurumsal bütçe hata bariyeri mesajını kullanıcıya göster
                    alert(data.message ? data.message : '⚠️ Dönem devri sırasında veritabanı veya mantık hatası oluştu.');
                }
            })
            .catch(err => {
                console.error("Dönem devir motor hatası:", err);
                alert('❌ Sunucuyla iletişim kurulamadı. Lütfen servislerinizi kontrol edin.');
                btn.innerHTML = oMetin;
                btn.disabled = false;
            });
        });
    }

    // 2. [TEST] DÖNEMİ GERİ AL (ROLLBACK / HAFİF SIFIRLAMA) EMİR TETİKLEYİCİSİ
    const donemGeriAlBtn = document.getElementById('donem-geri-al-btn');
    if (donemGeriAlBtn) {
        donemGeriAlBtn.addEventListener('click', function() {
            if (!confirm('⚠️ GERİ YÜKLEME SÜRECİ: Arşiv loglarındaki en son bütçe durumuna geri dönmek, mevcut yeni ayı silmek ve kilitleri açmak istediğinize emin misiniz?')) return;
            
            const btn = donemGeriAlBtn;
            const oMetin = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> ⏳ Zaman Geri Sarılıyor & Kilitler Çözülüyor...';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('islem', 'donem_geri_al');

            fetch('/butce/admin/management', { method: 'POST', body: formData })
            .then(res => {
                if (!res.ok) throw new Error(`Ağ Hatası: ${res.status}`);
                return res.json();
            })
            .then(data => {
                btn.innerHTML = oMetin;
                btn.disabled = false;
                
                if (data && data.status === 'success') {
                    // Başarı bildirimini bas ve eski bütçe sayfasına render ol
                    if (data.message) alert(data.message);
                    window.location.reload();
                } else {
                    // Log bulunamadı uyarısını veya fırlatılan SQL hatasını bas
                    alert(data.message ? data.message : '⚠️ Dönem geri alınamadı. Arşiv kaydı eksik olabilir.');
                }
            })
            .catch(err => {
                console.error("Rollback motor hatası:", err);
                alert('❌ Sunucuyla iletişim kurulamadı. Zaman geri sarma işlemi başarısız.');
                btn.innerHTML = oMetin;
                btn.disabled = false;
            });
        });
    }

}

function initMerkeziTiklamaDinleyicileri() {
    document.addEventListener('click', function(e) {
        if (!e.target) return;

        // 1. TAKSİT KAYDET BUTONU
        if (e.target.id === 'taksit-kaydet-btn') {
            if (isRequestPending) return;
            const btn = e.target;
            const orijinalMetin = btn.innerHTML;

            isRequestPending = true;
            btn.innerHTML = "⏳ Oy...";
            btn.disabled = true;

            const formEl = document.getElementById('taksit-formu');
            const formData = new FormData(formEl);
            formData.append('islem', 'taksit_kaydet');

            fetch(window.location.href, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data && data.status === 'success') {
                    if (typeof window.monitorGuncelle === 'function') window.monitorGuncelle();
                } else {
                    alert('⚠️ Taksitler kaydedilemedi.');
                }
            })
            .catch(err => console.error("Hata:", err))
            .finally(() => {
                btn.innerHTML = orijinalMetin;
                btn.disabled = false;
                isRequestPending = false; // Kilit açıldı
            });
        }
        
        // 2. GELİR KAYDET BUTONU
        const gelirBtn = e.target.closest('#gelir-kaydet-btn');
        if (gelirBtn) {
            if (isRequestPending || gelirBtn.disabled) return;
            const orijinalMetin = gelirBtn.innerHTML;

            isRequestPending = true;
            gelirBtn.innerHTML = "💾...";
            gelirBtn.disabled = true;

            const formData = new FormData(document.getElementById('gelir-butce-formu'));
            formData.append('islem', 'gelir_kaydet');

            fetch(window.location.href, { method: 'POST', body: formData })
            .then(() => {
                if (typeof window.monitorGuncelle === 'function') window.monitorGuncelle();
            })
            .catch(err => console.error(err))
            .finally(() => {
                gelirBtn.innerHTML = orijinalMetin;
                gelirBtn.disabled = false;
                isRequestPending = false;
            });
        }

        // 3. GİDER KAYDET BUTONU
        const giderBtn = e.target.closest('#gider-kaydet-btn');
        if (giderBtn) {
            if (isRequestPending || giderBtn.disabled) return;
            const orijinalMetin = giderBtn.innerHTML;

            isRequestPending = true;
            giderBtn.innerHTML = "💾...";
            giderBtn.disabled = true;

            const formData = new FormData(document.getElementById('gider-butce-formu'));
            formData.append('islem', 'gider_kaydet');

            fetch(window.location.href, { method: 'POST', body: formData })
            .then(() => {
                if (typeof window.monitorGuncelle === 'function') window.monitorGuncelle();
            })
            .catch(err => console.error(err))
            .finally(() => {
                giderBtn.innerHTML = orijinalMetin;
                giderBtn.disabled = false;
                isRequestPending = false;
            });
        }

        // 4. EFT VE ÖDEME AKSİYON BUTONLARI (.eft-btn, .ode-btn)
        const btnAct = e.target.closest('.eft-btn, .ode-btn');
        if (btnAct) {
            if (isRequestPending || btnAct.disabled || btnAct.innerHTML.includes('✓')) return;
            if (btnAct.getAttribute('onclick')?.includes('nergizCepOde')) return;

            const id = btnAct.getAttribute('data-id');
            const type = btnAct.getAttribute('data-type') || 'ode';
            if (!id) return;

            isRequestPending = true;
            btnAct.innerHTML = "⏳"; 
            btnAct.disabled = true;

            const rowKalem = btnAct.closest('.row-kalem');
            const textInput = rowKalem ? rowKalem.querySelector('input[type="text"]') : null;
            const kalemAdi = textInput ? textInput.value : '';
            const tutarInput = rowKalem ? rowKalem.querySelector('input[type="number"]') : null;
            const tutar = tutarInput ? parseFloat(tutarInput.value) || 0 : 0;

            if (type === 'maas_eft' || btnAct.classList.contains('eft-btn')) {
                if (typeof asılOdemeyiBitir === 'function') asılOdemeyiBitir(id, 'maas_eft', btnAct, tutar);
            } else {
                if (typeof giderOdeKontrolEt === 'function') giderOdeKontrolEt(btnAct, kalemAdi, tutar, type, id);
            }
            
            isRequestPending = false; 
        }

        // 🎯 AKİLLİ BÜTÇE TAG TIKLAMA ENTEGRASYONU (Merkezi Klik Yöneticisi İçine Eklenecek)
        const akilliTag = e.target.closest('.akilli-butce-tag');
        if (akilliTag) {
            // jQuery bağımlılığı bitti, saf JS dataset ile tutar güvenle okunuyor
            let ttr = parseFloat(akilliTag.dataset.tutar) || 0;
            
            if (ttr > 0) {
                const transferInput = document.getElementById('transferTutar');
                if (transferInput) {
                    // Bir önceki aşamada toparladığımız sızdırmaz paraFormatla motoru kullanılıyor
                    transferInput.value = paraFormatla(ttr); 
                }
            }
        }


        // 5. FAİZ AKTAR BUTONU
        if (e.target.id === 'faiz-aktar-btn') {
            e.preventDefault();
            if (isRequestPending) return;

            if (confirm('Cüzdanlardaki birikmiş nemalar anapara üzerine eklenecek ve raporlara işlenecek. Emin misiniz?')) {
                const faizAktarBtn = e.target;
                isRequestPending = true;
                faizAktarBtn.innerHTML = "⏳ Aktarılıyor...";
                faizAktarBtn.disabled = true;

                const formData = new FormData();
                formData.append('islem', 'faiz_aktar');

                fetch('/butce/admin/management', { method: 'POST', body: formData })
                .then(res => res.text())
                .then(hamMetin => {
                    const startIdx = hamMetin.indexOf('{');
                    const endIdx = hamMetin.lastIndexOf('}');
                    
                    if (startIdx !== -1 && endIdx !== -1) {
                        const data = JSON.parse(hamMetin.substring(startIdx, endIdx + 1));
                        if (data && data.status === 'success') {
                            window.location.reload();
                            return;
                        } else {
                            alert(`⚠️ Aktarım Reddedildi!\nSunucu Mesajı: ${data.message || 'Bütçe katmanı reddetti.'}`);
                        }
                    } else {
                        alert(`⚠️ Sunucu Odasından Geçersiz Çıktı Sızdı!\n\n${hamMetin.replace(/<[^>]*>/g, '')}`);
                    }
                })
                .catch(err => {
                    console.error("Faiz aktarım hatası:", err);
                    alert("⚠️ Sunucu asenkron bağlantısı koptu!");
                })
                .finally(() => {
                    faizAktarBtn.innerHTML = "Faiz Aktar";
                    faizAktarBtn.disabled = false;
                    isRequestPending = false;
                });
            }
        }

        // 6. SİMÜLASYON SIFIRLA BUTONU
        if (e.target.id === 'simulasyon-sifirla-btn') {
            e.preventDefault();
            if (isRequestPending) return;

            if (confirm('Tüm adımlar sıfırlanacak (Tablolar varsayılan ayarlara dönecek). Emin misiniz?')) {
                const sifirlaBtn = e.target;
                isRequestPending = true;
                sifirlaBtn.innerHTML = "⏳ Sıfırlanıyor...";
                sifirlaBtn.disabled = true;

                const formData = new FormData(); 
                formData.append('islem', 'simulasyon_sifirla');
                
                fetch('/butce/admin/management', { method: 'POST', body: formData })
                .then(res => res.text())
                .then(hamMetin => {
                    const startIdx = hamMetin.indexOf('{');
                    const endIdx = hamMetin.lastIndexOf('}');
                    
                    if (startIdx !== -1 && endIdx !== -1) {
                        const data = JSON.parse(hamMetin.substring(startIdx, endIdx + 1));
                        if (data && data.status === 'success') {
                            Object.keys(localStorage).forEach(key => {
                                if (key.startsWith('tel_kilit_')) {
                                    localStorage.removeItem(key);
                                }
                            });
                            window.location.reload();
                            return;
                        }
                    }
                    alert("⚠️ Sıfırlama işlemi sunucu tarafında tamamlanamadı.");
                })
                .catch(err => console.error("Sıfırlama hatası:", err))
                .finally(() => {
                    sifirlaBtn.innerHTML = "Simülasyonu Sıfırla";
                    sifirlaBtn.disabled = false;
                    isRequestPending = false;
                });
            }
        }
    });
}


document.addEventListener('DOMContentLoaded', function() {
    console.log("🚀 Yapılandırılmış Yeni Finansal Motor Aktif.");

    // 1. Önceki Durumları / State Verilerini Yükle
    if (typeof window.todoDurumlariniYukle === 'function') { 
        window.todoDurumlariniYukle(); 
    }
    // 2. Sayfa İlk Açılış Tetiklemeleri (Ana Matris Aktif Olacak Şekilde Sıralı Render)
    if (typeof hariciKrediKartiMatrisiniHesapla === 'function') {
        hariciKrediKartiMatrisiniHesapla();
    }
    if (typeof renderNihaiDashboardCore === 'function') {
        renderNihaiDashboardCore();
    }
    // 3. Olay Dinleyicilerini Güvenli Çatılar Altında Başlat
    initMerkeziInputDinleyicileri();
    initMerkeziTiklamaDinleyicileri();
    canlıFaizOnizlemeHesapla();

    
    // Bootstrap modal kapatıldığında veya gizlendiğinde form alanlarını temizleme güvenliği
    const myModalEl = document.getElementById('forexYatirimModal');
    if (myModalEl) {
        myModalEl.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('forexSatinalForm');
            if (form) form.reset();
            document.getElementById('modal_canli_kur_text').textContent = "0,0000 ₺";
            document.getElementById('modal_maks_kapasite_text').textContent = "0,00";
            document.getElementById('modal_panel_nihai_sonuc').value = "0,00 ₺";
            document.getElementById('modal_panel_vergi_bilgi_rozet').textContent = "Vergi: 0,00 ₺";
            document.getElementById('modal_panel_tutar_bilgi_rozet').textContent = "Tutar: 0,00 ₺";
            document.getElementById('modal_panel_bakiye_uyari_metni').classList.add('d-none');
            document.getElementById('modal_btn_panel_onayla').disabled = true;
        });
    }

    // =========================================================================
    // 🚀 ASENKRON AJAX SÜRÜCÜ MOTORU (NİHAİ BAŞARI ENTEGRASYONLU SÜRÜM)
    // =========================================================================
    const form = document.getElementById('forexSatinalForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Sayfa yenilenmesini kökten engelle

            const btnSubmit = document.getElementById('modal_btn_panel_onayla');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Kaydediliyor...';
            }

            const formData = new FormData(form);
            const requestUrl = form.getAttribute('action') || window.location.href;

            fetch(requestUrl, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                // 🔔 HATA RADARI: Sunucudan JSON dışı bir arıza veya HTML sızarsa doğrudan yakalar
                if (!response.ok) {
                    return response.text().then(text => { 
                        throw new Error(`Sunucu Hatası Kodu: ${response.status}\nDetay: ${text}`); 
                    });
                }
                return response.json();
            })
            .then(res => {
                // 🎯 KİLİT ÇÖZÜM: Sunucudan dönen JSON yanıtını işleyen asıl motor burasıdır
                if (res.success) {
                    // 1. Başarı Mesajını Ekrana Bas
                    alert(res.message);

                    // 2. Ana Ekrandaki ve Modal İçindeki Bakiyeyi Sayfa Yenilenmeden Anlık Güncelle
                    const elBakiyeElementi = document.getElementById('modal-tasarruf-bakiye');
                    if (elBakiyeElementi) {
                        elBakiyeElementi.dataset.tl = res.data.yeni_anapara;
                        elBakiyeElementi.textContent = res.data.yeni_anapara.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + " ₺";
                    }

                    // 3. Modalı Kapat, Formu Temizle ve Arkada Kalan Siyah Perdeyi Temizle
                    setTimeout(() => {
                        const modalElement = document.getElementById('forexYatirimModal');
                        let modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (!modalInstance) {
                            modalInstance = new bootstrap.Modal(modalElement);
                        }
                        modalInstance.hide(); // Modalı pürüzsüzce kapatır
                        form.reset(); // Giriş alanlarını sıfırlar

                        // 🛡️ Çift Karartma (Backdrop) Koruma Kalkanı
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) backdrop.remove();
                        document.body.classList.remove('modal-open');
                        document.body.style.removeProperty('padding-right');
                    }, 100);

                } else {
                    // Sunucu veritabanı kalkanından (Örn: Bakiye Yetersiz) dönen hatayı gösterir
                    alert(res.message);
                    if (btnSubmit) {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = '<i class="fas fa-check-circle me-2"></i> Satın Alma İşlemini Tamamla';
                    }
                }
            })
            .catch(error => {
                console.error('AJAX Detaylı Hata:', error);
                alert("İletişim/Sistem Arızası:\n" + error.message); // Ekrana sunucunun fırlattığı gerçek hatayı basar
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-check-circle me-2"></i> Satın Alma İşlemini Tamamla';
                }
            });
        });
    }

});


</script>






<?php require admin_view("static/footer"); ?>
