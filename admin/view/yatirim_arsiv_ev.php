<?php 
    ## Tags Bilgileri
    $meta = [
        'site_title' => 'Ev Kredisi Ödeme & Yapılandırma Arşivi | Bütçe Yönetimi',
        'site_desc' => 'Bütçe Yönetimi',
        'site_keyw' => 'Bütçe Yönetimi'
    ];
    # Tags Bilgileri

    require admin_view("static/header"); 

?>

<style>
    body { background-color: #f4f6f9; font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    .fx-custom-font { font-size: 11.5px !important; }

    /* Kredi Özet Kartı */
    .summary-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #fff;
        border-radius: 10px;
    }
    .stat-box { padding: 15px; border-right: 1px solid rgba(255,255,255,0.1); }
    .stat-box:last-child { border-right: none; }
    .stat-title { font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-size: 1.2rem; font-weight: 700; margin-top: 4px; }
    .stat-sub { font-size: 0.72rem; color: #cbd5e1; margin-top: 4px; line-height: 1.4; }

    /* Yapılandırma Kartı */
    .refinance-card { background: #ffffff; border-left: 4px solid #2563eb; border-radius: 8px; }

    /* Tablo Temizliği */
    .table-custom { font-size: 11.5px; }
    .table-custom th { background-color: #1e293b !important; color: #f8fafc !important; font-weight: 600; text-transform: uppercase; font-size: 10.5px; padding: 8px 4px !important; }
    .table-custom td { padding: 6px 4px !important; vertical-align: middle; }

    /* Ödendi Satır Stili (Tüm Satırı Yeşil Yapmak Yerine Temiz Görünüm) */
    .tr-paid { background-color: #f0fdf4 !important; }
    .tr-paid td { color: #166534 !important; }
    .badge-paid { background-color: #22c55e; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: 600; }

    /* Özet Kartları */
    .card-counter { padding: 12px 16px; border-radius: 8px; background-color: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }



 /* ================= SATIR ARKA PLAN DOLGULARI (FULL ROW BACKGROUND)================= */
 /* HTML td ve th elemanlarının Bootstrap varsayılan arka planını ezer */
 .table-ev-altin,
 .table-ev-altin > td,
 .table-ev-altin > th {
    background-color: #fef08a !important; /* Dolgun Sarı / Amber Pastel */
    color: #713f12 !important;
 }
 .table-ev-kredi,
 .table-ev-kredi > td,
 .table-ev-kredi > th {
    background-color: #bfdbfe !important; /* Dolgun Mavi Pastel */
    color: #1e3a8a !important;
 }
 .table-ev-diger-gelir,
 .table-ev-diger-gelir > td,
 .table-ev-diger-gelir > th {
    background-color: #bbf7d0 !important; /* Dolgun Yeşil Pastel */
    color: #14532d !important;
 }
 .table-ev-gider,
 .table-ev-gider > td,
 .table-ev-gider > th {
    background-color: #fecaca !important; /* Dolgun Pembe / Kırmızı Pastel */
    color: #7f1d1d !important;
 }
 .badge-fx {
    font-size: 11px !important;
    padding: 4px 8px;
    font-weight: 700;
    text-transform: uppercase;
 }
 
 /* Özet Bilgi Kartı */
 .summary-card {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
 }
 .stat-box {
    padding: 15px;
    border-right: 1px solid rgba(255,255,255,0.15);
 }
 .stat-box:last-child { border-right: none; }
 .stat-title { font-size: 0.8rem; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.5px; }
 .stat-value { font-size: 1.35rem; font-weight: 700; margin-top: 5px; }

 /* Tablo Dolgusu ve Rozetler */
 .fx-custom-font { font-size: 10px !important; font-weight: 500; }
 .table-paid,
 .table-paid > td {
    background-color: #d1e7dd !important;
    color: #0f5132 !important;
    font-weight: 600;
 }
 .badge-validated {
    background-color: #198754;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
    gap: 5px;
 }


</style>

<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-dark fw-bold">
            <i class="fa-solid fa-house-chimney text-primary me-2"></i>Ev Kredisi Ödeme & Yapılandırma Paneli
        </h5>
    </div>

    
    <!-- 2. YAPILANDIRMA SİMÜLATÖR KARTI -->
    <div class="card refinance-card shadow-sm mb-4 border-0">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h6 class="fw-bold text-primary mb-1"><i class="fa-solid fa-calculator me-1"></i> Kredi Yapılandırma Simülatörü</h6>
                    <p class="text-muted small mb-2" style="font-size: 11px;">Kalan anapara (<b>₺<?= number_format($kalanAnapara, 2, ',', '.') ?></b>) ve <b><?= $kalanTaksit ?> ay</b> vade üzerinden hesaplanır.</p>

                    <!-- Satır 1: %, Input ve Buton (Tek Parça Bitişik Yan Yana) -->
                    <div class="input-group input-group-sm mb-2" style="flex-wrap: nowrap !important; max-width: 300px;">
                        <span class="input-group-text bg-light fw-bold">%</span>
                        <input type="number" step="0.01" id="yeniFaizInput" class="form-control fw-bold text-primary" value="1.89">
                        <button class="btn btn-primary px-3" type="button" onclick="hesaplaYatirim()">Hesapla</button>
                    </div>

                    <!-- Satır 2: Checkbox Switch (Alt Satırda) -->
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="cezaEkleCheck" checked onchange="hesaplaYatirim()">
                        <label class="form-check-label text-muted" style="font-size: 11px;" for="cezaEkleCheck">%2 Erken Kapama / Yapılandırma Ücreti</label>
                    </div>
                </div>

                <div class="col-md-8 border-start ps-md-4 mt-2 mt-md-0">
                    <div class="row text-center g-2">
                        <div class="col-md-4">
                            <div class="p-2 bg-light rounded">
                                <span class="d-block text-muted small text-uppercase" style="font-size: 10px;">Yeni Taksit</span>
                                <span class="fs-6 fw-bold text-dark font-monospace" id="resYeniTaksit">₺0,00</span>
                                <span class="d-block text-muted mt-1" style="font-size: 10px;">Eski: ₺53.272,74</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 bg-light rounded">
                                <span class="d-block text-muted small text-uppercase" style="font-size: 10px;">Yeni Toplam Borç</span>
                                <span class="fs-6 fw-bold text-danger font-monospace" id="resYeniToplamBorc">₺0,00</span>
                                <span class="d-block text-muted mt-1" style="font-size: 10px;">Mevcut: ₺<?= number_format($kalanBorc, 2, ',', '.') ?></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 bg-success bg-opacity-10 rounded border border-success border-opacity-25">
                                <span class="d-block text-success fw-bold small text-uppercase" style="font-size: 10px;">Yapılandırma Karı</span>
                                <span class="fs-5 fw-bold text-success font-monospace" id="resToplamKar">₺0,00</span>
                                <span class="d-block text-success mt-1" style="font-size: 10px;">Net Kazanç</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- 1. KREDİ ÖZET KARTI -->
    <div class="card summary-card mb-3 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="row text-center align-items-center">
                <div class="col-md-3 stat-box">
                    <div class="stat-title"><i class="fa-solid fa-vault me-1"></i> Toplam Kredi Tutarı</div>
                    <div class="stat-value font-monospace">₺<?= number_format($toplamKredi, 2, ',', '.') ?></div>
                    <div class="stat-sub">
                        Anapara: <b>₺<?= number_format($baslangicAnapara, 2, ',', '.') ?></b><br>
                        Faiz: <b>₺<?= number_format($toplamFaiz, 2, ',', '.') ?></b>
                    </div>
                </div>
                <div class="col-md-3 stat-box">
                    <div class="stat-title"><i class="fa-solid fa-hand-holding-dollar me-1"></i> Kalan Borç</div>
                    <div class="stat-value text-warning font-monospace">₺<?= number_format($kalanBorc, 2, ',', '.') ?></div>
                    <div class="stat-sub text-warning">
                        Kalan Anapara: <b>₺<?= number_format($kalanAnapara, 2, ',', '.') ?></b><br>
                        Kalan Faiz: <b>₺<?= number_format($kalanFaiz, 2, ',', '.') ?></b>
                    </div>
                </div>
                <div class="col-md-3 stat-box">
                    <div class="stat-title"><i class="fa-solid fa-circle-check me-1"></i> Toplam Ödenen</div>
                    <div class="stat-value font-monospace text-info">₺<?= number_format($odenenToplam, 2, ',', '.') ?></div>
                    <div class="stat-sub text-info">
                        Eritilen Anapara: <b>₺<?= number_format($odenenAnapara, 2, ',', '.') ?></b><br>
                        Ödenen Faiz: <b>₺<?= number_format($odenenFaiz, 2, ',', '.') ?></b>
                    </div>
                </div>
                <div class="col-md-3 stat-box">
                    <div class="stat-title"><i class="fa-solid fa-calendar-check me-1"></i> Ödenen / Toplam Vade</div>
                    <div class="stat-value font-monospace"><?= $odenenTaksitSayisi ?> / <?= $toplamTaksit ?></div>
                    <div class="stat-sub text-light">Kalan Vade: <b><?= $kalanTaksit ?> Ay</b></div>
                </div>
            </div>
        </div>
    </div>

        <!-- 4. GAYRİMENKUL GELİR & GİDER ANALİZİ -->
    <hr class="my-4">
    <h6 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-wallet me-2 text-primary"></i>Gayrimenkul Gelir & Gider Analizi</h6>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card-counter border-start border-4 bg-success">
                <span class="text-light small text-uppercase" style="font-size: 10px;">Toplam Giriş (Gelir)</span>
                <h5 class="fw-bold text-light font-monospace mb-0">₺<?= number_format($ev_toplam_giris, 2, ',', '.') ?></h5>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-counter border-start border-4 bg-danger">
                <span class="text-light small text-uppercase" style="font-size: 10px;">Toplam Çıkış (Gider)</span>
                <h5 class="fw-bold text-light font-monospace mb-0">₺<?= number_format($ev_toplam_cikis, 2, ',', '.') ?></h5>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-counter border-start border-4 bg-primary">
                <span class="text-light small text-uppercase" style="font-size: 10px;">Net Kalan Bakiye</span>
                <h5 class="fw-bold text-light font-monospace mb-0">₺<?= number_format($ev_kalan_bakiye, 2, ',', '.') ?></h5>
            </div>
        </div>
    </div>

    <!-- ================= GELİR GRUPLARI ÖZET KARTLARI ================= -->
    <?php
        // Altın, Kredi ve Diğer Gelir Gruplama Hesaplamaları
        $toplam_altin_geliri = 0;
        $toplam_kredi_geliri = 0;
        $toplam_diger_gelir  = 0;

        if (!empty($ev_islemleri)) {
            foreach ($ev_islemleri as $item) {
                $t = (float)($item['tutar'] ?? 0);
                if ($t > 0) {
                    $gt = $item['gelir_turu'] ?? '';
                    $ht = mb_strtolower($item['hesap_turu'] ?? '');
                    $ac = mb_strtolower($item['aciklama'] ?? '');

                    if ($gt === 'altin' || strpos($ht, 'altin') !== false || strpos($ht, 'altın') !== false) {
                        $toplam_altin_geliri += $t;
                    } elseif ($gt === 'kredi' || strpos($ht, 'kredi') !== false || strpos($ac, 'kredi') !== false) {
                        $toplam_kredi_geliri += $t;
                    } else {
                        $toplam_diger_gelir += $t;
                    }
                }
            }
        }
    ?>

    <div class="row g-3 mb-4">
        <!-- Altın Satışı Toplamı -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card-counter border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold small text-uppercase opacity-75 d-block">Altın Satışı Geliri</span>
                        <h4 class="fw-bold text-success font-monospace mb-0">₺<?= number_format($toplam_altin_geliri, 2, ',', '.') ?></h4>
                    </div>
                    <div class="fs-1 opacity-75">🪙</div>
                </div>
            </div>
        </div>

        <!-- Banka Kredisi Toplamı -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card-counter border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold small text-uppercase opacity-75 d-block">Konut Kredisi Girişi</span>
                        <h4 class="fw-bold text-success font-monospace mb-0">₺<?= number_format($toplam_kredi_geliri, 2, ',', '.') ?></h4>
                    </div>
                    <div class="fs-1 opacity-75">🏦</div>
                </div>
            </div>
        </div>

        <!-- Diğer Gelirler Toplamı -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card-counter border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold small text-uppercase opacity-75 d-block">Diğer Özkaynak & Gelir</span>
                        <h4 class="fw-bold text-success font-monospace mb-0">₺<?= number_format($toplam_diger_gelir, 2, ',', '.') ?></h4>
                    </div>
                    <div class="fs-1 opacity-75">💵</div>
                </div>
            </div>
        </div>

    </div>

    
    <?php 
        $yariMiktar = !empty($taksitler) ? ceil(count($taksitler) / 2) : 0;
        $sutunlar   = !empty($taksitler) ? array_chunk($taksitler, $yariMiktar) : [[], []];
    ?>
    <div class="row g-3 mb-4">

        <div class="col-4">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden h-100">
                <!-- Kart Başlığı -->
                <div class="card-header bg-dark py-3 px-4 d-flex justify-content-between align-items-center border-0">
                    <h5 class="fw-bold mb-0 text-white fs-6 d-flex align-items-center">
                        <i class="fas fa-home me-2 text-warning fs-5"></i>
                        <span>Gayrimenkul Alım & Masraf Dökümü</span>
                    </h5>
                    <span class="badge bg-primary bg-gradient rounded-pill px-3 py-2 fw-semibold">
                        <?= count($ev_islemleri ?? []) ?> Kayıt
                    </span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 fx-custom-font">
                            <thead>
                                <!-- KOLON BAŞLIKLARI -->
                                <tr class="table-dark text-white text-uppercase small" style="letter-spacing: 0.5px;">
                                    <th class="text-center py-2-5 ps-3" style="width: 20%;">İşlem Tarihi</th>
                                    <th class="text-start py-2-5" style="width: 40%;">Açıklama / İşlem Grubu</th>
                                    <th class="text-end py-2-5" style="width: 20%;">Tutar</th>
                                    <th class="text-center py-2-5 pe-3" style="width: 20%;">Hesap Türü</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($ev_islemleri)): ?>
                                    <?php foreach ($ev_islemleri as $islem): 
                                        $tutar = (float)($islem['tutar'] ?? 0);
                                        $is_giris = $tutar >= 0;

                                        $gelir_turu = $islem['gelir_turu'] ?? '';
                                        $aciklama   = mb_strtolower($islem['aciklama'] ?? '');
                                        $hesap_turu = mb_strtolower($islem['hesap_turu'] ?? '');

                                        if (!$is_giris) {
                                            $satir_sinifi = 'table-ev-gider';
                                            $tutar_sinifi = 'text-danger';
                                        } elseif ($gelir_turu === 'altin' || strpos($hesap_turu, 'altin') !== false || strpos($hesap_turu, 'altın') !== false) {
                                            $satir_sinifi = 'table-ev-altin';
                                            $tutar_sinifi = 'text-dark';
                                        } elseif ($gelir_turu === 'kredi' || strpos($hesap_turu, 'kredi') !== false || strpos($aciklama, 'kredi') !== false) {
                                            $satir_sinifi = 'table-ev-kredi';
                                            $tutar_sinifi = 'text-dark';
                                        } else {
                                            $satir_sinifi = 'table-ev-diger-gelir';
                                            $tutar_sinifi = 'text-dark';
                                        }
                                    ?>
                                        <tr class="<?= $satir_sinifi ?>">
                                            <!-- Tarih -->
                                            <td class="text-center font-monospace fw-semibold py-2-5 ps-3 text-secondary">
                                                <?= !empty($islem['islem_tarihi']) ? date('d.m.Y', strtotime($islem['islem_tarihi'])) : '-' ?>
                                            </td>
                                            <!-- Açıklama -->
                                            <td class="text-start fw-medium py-2-5">
                                                <?= htmlspecialchars($islem['aciklama'] ?? '') ?>
                                            </td>
                                            <!-- Tutar -->
                                            <td class="text-end font-monospace fw-bold py-2-5 <?= $tutar_sinifi ?>">
                                                <?= $is_giris ? '₺' : '-₺' ?><?= number_format(abs($tutar), 2, ',', '.') ?>
                                            </td>
                                            <!-- Hesap Türü / Tip -->
                                            <td class="text-center py-2-5 pe-3">
                                                <span class="badge bg-body-tertiary border text-dark fw-normal rounded-pill px-2-5 py-1">
                                                    <?= htmlspecialchars($islem['hesap_turu'] ?? 'Genel') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fs-3 d-block mb-2 text-secondary opacity-50"></i>
                                            <code class="text-muted">ev_alim</code> tablosunda henüz kayıtlı bir işlem bulunmuyor.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <!-- TAKSİT TABLOLARI (2 SÜTUNLU) -->
        <?php foreach ($sutunlar as $index => $sutunTaksitleri): ?>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-2">
                    <div class="card-header bg-dark py-2 px-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-white" style="font-size: 12px;">
                            <i class="fas fa-list me-1 text-warning"></i> Taksit Listesi (<?= $index === 0 ? '1. Bölüm' : '2. Bölüm' ?>)
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0 table-custom">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 6%;">T.No</th>
                                        <th style="width: 15%;">Vade</th>
                                        <th class="text-end" style="width: 17%;">Anapara</th>
                                        <th class="text-end" style="width: 17%;">Faiz</th>
                                        <th class="text-end" style="width: 18%;">Taksit</th>
                                        <th class="text-end" style="width: 17%;">K. Borç</th>
                                        <th class="text-center" style="width: 10%;">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($sutunTaksitleri)): ?>
                                        <?php foreach ($sutunTaksitleri as $row): 
                                            $isPaid = ((int)($row['is_locked'] ?? 0) === 1);
                                        ?>
                                            <tr class="<?= $isPaid ? 'tr-paid' : '' ?>">
                                                <td class="text-center fw-bold font-monospace"><?= $row['taksit_no'] ?></td>
                                                <td class="font-monospace"><?= date('d.m.Y', strtotime($row['vade_tarihi'])) ?></td>
                                                <td class="text-end font-monospace text-success fw-bold">₺<?= number_format($row['anapara_payi'], 2, ',', '.') ?></td>
                                                <td class="text-end font-monospace text-danger">₺<?= number_format($row['faiz_payi'], 2, ',', '.') ?></td>
                                                <td class="text-end fw-bold font-monospace">₺<?= number_format($row['taksit_tutari'], 2, ',', '.') ?></td>
                                                <td class="text-end font-monospace">₺<?= number_format($row['kalan_borc'], 2, ',', '.') ?></td>
                                                <td class="text-center">
                                                    <?php if ($isPaid): ?>
                                                        <span class="badge-paid d-inline-block p-2" title="Ödendi">Ödendi</span>
                                                        <!-- <button class="btn btn-sm btn-link text-danger p-0 ms-1 align-middle" onclick="odemeYap(<?= $row['id'] ?>, 0)" title="Ödemeyi İptal Et">
                                                            <i class="fa-solid fa-rotate-left" style="font-size: 10px;"></i>
                                                        </button> -->
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 10px;" onclick="odemeYap(<?= $row['id'] ?>, 1)">Öde</button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

</div>

<script>
    const kalanAnapara = <?= (float)$kalanAnapara ?>;
    const kalanBorc = <?= (float)$kalanBorc ?>;
    const kalanVade = <?= (int)$kalanTaksit ?>;

    function hesaplaYatirim() {
        const oranInput = parseFloat(document.getElementById('yeniFaizInput').value);
        const cezaEkle = document.getElementById('cezaEkleCheck').checked;

        if (isNaN(oranInput) || oranInput <= 0 || kalanVade <= 0) return;

        let eklenecekAnapara = kalanAnapara;
        if (cezaEkle) {
            eklenecekAnapara += kalanAnapara * 0.02;
        }

        const r = oranInput / 100;
        const ust = r * Math.pow(1 + r, kalanVade);
        const alt = Math.pow(1 + r, kalanVade) - 1;
        const yeniAylikTaksit = eklenecekAnapara * (ust / alt);
        
        const yeniToplamGeriOdeme = yeniAylikTaksit * kalanVade;
        const toplamKar = kalanBorc - yeniToplamGeriOdeme;

        document.getElementById('resYeniTaksit').innerText = '₺' + yeniAylikTaksit.toLocaleString('tr-TR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('resYeniToplamBorc').innerText = '₺' + yeniToplamGeriOdeme.toLocaleString('tr-TR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        const karElement = document.getElementById('resToplamKar');
        if (toplamKar > 0) {
            karElement.innerText = '₺' + toplamKar.toLocaleString('tr-TR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            karElement.className = 'fs-5 fw-bold text-success font-monospace';
        } else {
            karElement.innerText = '₺' + Math.abs(toplamKar).toLocaleString('tr-TR', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (Zarar)';
            karElement.className = 'fs-5 fw-bold text-danger font-monospace';
        }
    }

    document.addEventListener('DOMContentLoaded', hesaplaYatirim);

    function odemeYap(id, isLocked) {
        const mesaj = isLocked === 1 ? 'Bu taksiti ödendi olarak işaretlemek istediğinize emin misiniz?' : 'Bu ödemeyi iptal etmek istediğinize emin misiniz?';
        if (!confirm(mesaj)) return;

        const formData = new FormData();
        formData.append('action', 'toggle_lock');
        formData.append('id', id);
        formData.append('is_locked', isLocked);

        fetch(window.location.href, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => { if (data.success) location.reload(); else alert('Güncelleme hatası.'); });
    }
</script>

<?php require admin_view('static/footer'); ?>