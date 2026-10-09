<?php 
    ## Tags Bilgileri
    $meta = [
        'site_title' => 'Harcama Listesi | Bütçe Yönetimi',
        'site_desc' => 'Bütçe Yönetimi',
        'site_keyw' => 'Bütçe Yönetimi'
    ];
    # Tags Bilgileri

    require admin_view("static/header"); 

?>

<!-- ARAYÜZ MESAJ SENSÖRLERİ -->
<div id="canliAlertAlani"></div>



<!-- PREMIUM KURSAL MİZAN FİLTRE PANELİ -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 8px; border: 1px solid #eef2f5 !important; background-color: #ffffff;">
    <div class="card-header bg-white p-3 border-0 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #f4f7f9 !important;">
        <span class="fw-bold text-dark d-inline-flex align-items-center" style="font-size: 12px; letter-spacing: 0.3px; color: #1e293b !important;">
            <i class="fas fa-filter text-primary me-2" style="font-size: 13px;"></i> EKSTRE DÖKÜMÜ
        </span>
        <!-- TOPLU İŞLEM REFORM BUTONLARI -->
        <div class="d-flex gap-2">
            <button type="button" id="btnTopluFirmaModalAc" class="btn btn-sm btn-success fw-bold px-3 border-0 d-inline-flex align-items-center d-none" style="font-size: 11px; border-radius: 6px; background-color: #10b981; box-shadow: 0 2px 4px rgba(16,185,129,0.12);">
                <i class="fas fa-store me-1.5"></i> (<span id="topluFirmaSatirSayisi">0</span>) Toplu Firma Ata
            </button>
            <button type="button" id="btnTopluEtiketleModalAc" class="btn btn-sm btn-primary fw-bold px-3 border-0 d-inline-flex align-items-center d-none" style="font-size: 11px; border-radius: 6px; background-color: #3b82f6; box-shadow: 0 2px 4px rgba(59,130,246,0.12);">
                <i class="fas fa-layer-group me-1.5"></i> (<span id="topluEtiketSatirSayisi">0</span>) Toplu Kategori Ata
            </button>
            <button type="button" id="btnTopluOdemeModalAc" class="btn btn-sm btn-secondary fw-bold px-3 border-0 d-inline-flex align-items-center d-none" style="font-size: 11px; border-radius: 6px; background-color: #64748b; box-shadow: 0 2px 4px rgba(100,116,139,0.12);">
                <i class="fas fa-money-check-dollar me-1.5"></i> (<span id="topluOdemeSatirSayisi">0</span>) Toplu Ödeme Türü Değiştir
            </button>

        </div>
    </div>
    
    <div class="card-body p-3 text-secondary" style="font-size: 11.5px;">
        <!-- 1. BLOK: ANA PARAMETRE GİRDİ SATIRI -->
        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-3">
                <label class="form-label fw-bold mb-1 text-muted"><i class="fas fa-keyboard me-1"></i> Açıklama Metni ile Filtrele</label>
                <input type="text" id="filtreFirma" class="form-control border-0 bg-light shadow-none fw-medium text-uppercase" placeholder="Kelime yazın..." style="font-size: 11.5px; height: 34px; border-radius: 6px; background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important;">
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-bold mb-1 text-muted"><i class="fas fa-store me-1"></i>Firma Adına Göre Filtrele</label>
                <select id="filtreFirmaSelectbox" class="form-select border-0 bg-light shadow-none fw-bold" style="font-size: 11.5px; height: 34px; border-radius: 6px; background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important; cursor: pointer;">
                    <option value="HEPSI">Tüm Firmaları Seç</option>
                    <option value="FIRMASIZLAR">Firma Adı Boş Olanlar</option>
                    <?php if (!empty($tum_firma_kurallari)): ?>
                        <?php foreach ($tum_firma_kurallari as $f_secenek): ?>
                            <option value="<?= htmlspecialchars(trim($f_secenek)) ?>"><?= htmlspecialchars(trim($f_secenek)) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label fw-bold mb-1 text-muted"><i class="fas fa-calendar-alt me-1"></i> İşlem Tarihine Göre Filtrele</label>
                <div class="input-group shadow-none" style="border: 1px solid #e2e8f0 !important; border-radius: 6px; overflow: hidden; height: 34px;">
                    <input type="date" id="filtreTarihBaslangic" class="form-control border-0 bg-light shadow-none text-center font-monospace" style="font-size: 11px; background-color: #f8fafc !important;">
                    <span class="input-group-text bg-white border-0 fw-bold text-muted px-2" style="font-size: 10px;">-</span>
                    <input type="date" id="filtreTarihBitis" class="form-control border-0 bg-light shadow-none text-center font-monospace" style="font-size: 11px; background-color: #f8fafc !important;">
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold mb-1 text-muted"><i class="fas fa-folder-open me-1"></i>Ekstre Dönemine Göre Filtrele</label>
                <select id="donemSeciciSüzgec" class="form-select border-0 bg-light shadow-none fw-bold" style="font-size: 11.5px; height: 34px; border-radius: 6px; background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important;" onchange="location = this.value;">
                    <option value="?page=harcama_listesi&donem=TUMU" <?= $secili_donem === 'TUMU' ? 'selected' : '' ?>><?= date('Y') . " Yılı Harcamalarının Tümü"?></option>
                    <?php foreach ($tum_yuklu_donemler as $d): ?>
                        <option value="?page=harcama_listesi&donem=<?= $d ?>" <?= $d === $secili_donem ? 'selected' : '' ?>> <?= date('m / Y', strtotime($d . "-01")) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- 2. BLOK: ROZET FİLTRE GRUPLARI (KUSURSUZ YATAY SÜRÜM) -->
        <div class="d-flex flex-column gap-2 border-top pt-3 mt-2" style="border-color: #f1f5f9 !important;">
            
            <!-- Satır A: Kart Türü & Taksit Ortak Hizalama Şeridi -->
            <div class="row g-2 align-items-center">
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <div class="fw-bold text-muted text-uppercase d-flex align-items-center justify-content-center bg-light rounded" style="font-size: 9.5px; min-width: 90px; height: 26px; background-color: #f1f5f9 !important; letter-spacing: 0.2px;"><i class="fas fa-credit-card me-1 text-primary"></i>Kredi Kartı</div>
                    <div id="filtreKartRozetKonteyner" class="d-flex gap-1">
                        <button type="button" class="btn btn-xs btn-dark btn-kart-filtre aktif fw-bold px-2.5 py-1" data-kart="HEPSI" style="font-size: 9.5px; border-radius: 4px;">TÜM KARTLAR</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-kart-filtre fw-medium px-2.5 py-1" data-kart="isbank" style="font-size: 9.5px; border-radius: 4px;">İŞ BANKASI</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-kart-filtre fw-medium px-2.5 py-1" data-kart="vakifbank" style="font-size: 9.5px; border-radius: 4px;">VAKIFBANK</button>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center gap-2 justify-content-md-end justify-content-start mt-md-0 mt-2">
                    <div class="fw-bold text-muted text-uppercase d-flex align-items-center justify-content-center bg-light rounded" style="font-size: 9.5px; min-width: 90px; height: 26px; background-color: #f1f5f9 !important; letter-spacing: 0.2px;"><i class="fas fa-history me-1 text-info"></i> Ödeme Türü</div>
                    <div id="filtreTaksitRozetKonteyner" class="d-flex gap-1">
                        <button type="button" class="btn btn-xs btn-dark btn-taksit-filtre aktif fw-bold px-2.5 py-1" data-taksit="HEPSI" style="font-size: 9.5px; border-radius: 4px;">Tüm Harcamalar</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-taksit-filtre fw-medium px-2.5 py-1" data-taksit="PESIN" style="font-size: 9.5px; border-radius: 4px;">Peşin Harcamalar</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-taksit-filtre fw-medium px-2.5 py-1" data-taksit="TAKSITLI" style="font-size: 9.5px; border-radius: 4px;">Taksitli Harcamalar</button>
                    </div>
                </div>
            </div>
            <!-- Satır B: Kategoriler Şeridi -->
            <div class="d-flex align-items-start gap-2 mt-1.5">
                <div class="fw-bold text-muted text-uppercase d-flex align-items-center justify-content-center bg-light rounded" style="font-size: 10px; min-width: 100px; height: 32px; background-color: #f1f5f9 !important; letter-spacing: 0.3px;"><i class="fas fa-tags me-1 text-success"></i> Kategoriler</div>
                <div id="filtreTagKonteyner" class="d-flex flex-wrap gap-1.5 align-items-center">
                    <span class="badge bg-dark px-3 py-2 style-cursor tag-filtre-rozet aktif" data-id="HEPSI" style="cursor:pointer; font-size:11px; border-radius:6px; font-weight: 600; line-height: 1.2;">Tüm Kategoriler</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 px-3 py-2 style-cursor tag-filtre-rozet" data-id="0" style="cursor:pointer; font: size 10px; border-radius:6px; font-weight: 500; line-height: 1.2;"><i class="fas fa-question-circle me-1"></i> KATEGORİ (BOŞ)</span>
                    <?php foreach ($tum_kategoriler as $kat): ?>
                        <span class="badge bg-secondary bg-opacity-10 text-dark border border-secondary border-opacity-10 px-3 py-2 style-cursor tag-filtre-rozet" data-id="<?= $kat['id'] ?>" style="cursor:pointer; font-size:10px; border-radius:6px; font-weight: 500; line-height: 1.2;"><?= htmlspecialchars($kat['kategori_adi']) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Satır C: Popüler Firmalar Şeridi -->
            <div class="d-flex align-items-start gap-2 mt-2">
                <div class="fw-bold text-muted text-uppercase d-flex align-items-center justify-content-center bg-light rounded" style="font-size: 10px; min-width: 100px; height: 32px; background-color: #f1f5f9 !important; letter-spacing: 0.3px;"><i class="fas fa-store me-1 text-primary"></i> Firmalar</div>
                <div id="filtreFirmaRozetKonteyner" class="d-flex flex-wrap gap-1.5 align-items-center">
                    <span class="badge bg-dark px-3 py-2 style-cursor firma-filtre-rozet aktif" data-firma="HEPSI" style="cursor:pointer; font-size:10px; border-radius:6px; font-weight: 600; line-height: 1.2;">Tüm Firmalar</span>
                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-10 px-3 py-2 style-cursor firma-filtre-rozet" data-firma="FIRMASIZLAR" style="cursor:pointer; font-size:11px; border-radius:6px; font-weight: 500; line-height: 1.2;"><i class="fas fa-ghost me-1 text-warning"></i> FİRMA ADI (BOŞ)</span>
                    
                    <span id="rozetFirmaDiger" class="badge bg-info bg-opacity-10 text-dark border border-info border-opacity-10 px-3 py-2 d-none style-cursor firma-filtre-rozet" data-firma="DIGER" style="cursor:pointer; font-size:11px; border-radius:6px; font-weight: 600; line-height: 1.2;">
                        <i class="fas fa-random text-info me-1"></i> DİĞER (<span id="digerFirmaEtiketIsmi">Seçili</span>)
                    </span>

                    <?php if (!empty($en_cok_islem_goren_firmalar)): ?>
                        <?php foreach ($en_cok_islem_goren_firmalar as $f_rozet): $f_adi = trim($f_rozet['isletme_adi']); ?>
                            <span class="badge bg-secondary bg-opacity-10 text-dark border border-secondary border-opacity-10 px-3 py-2 style-cursor firma-filtre-rozet" data-firma="<?= htmlspecialchars($f_adi) ?>" style="cursor:pointer; font-size:11px; border-radius:6px; font-weight: 500; line-height: 1.2; transition: all 0.1s;">
                                <?= htmlspecialchars($f_adi) ?> <span class="text-muted font-monospace ms-0.5" style="font-size: 9px;">(<?= $f_rozet['toplam_islem_adedi'] ?>)</span>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>


        </div><!-- /.d-flex (2. BLOK) -->
    </div><!-- /.card-body -->
</div><!-- /.card -->


 <!-- HARCAMA LİSTESİ MİZAN TABLOSU -->
<div class="card border-0 shadow-sm" style="border-radius: 6px; overflow: hidden;">
    <div class="card-header bg-dark text-white p-3 border-0 d-flex align-items-center justify-content-between">
        <h6 class="card-title m-0 fw-bold d-inline-flex align-items-center" style="font-size: 12px; letter-spacing: 0.5px;">
            <i class="fas fa-list me-2 text-warning"></i>KREDİ KARTLARI <?= htmlspecialchars($secili_donem) === "TUMU" ? date('Y') : htmlspecialchars($secili_donem) ?> DÖNEMİ HARCAMA DÖKÜMÜ
        </h6>
        <div class="mizan-sag-istatistik" style="float: right;">
            <span id="mizan_ust_kayit_adedi" class="fw-bold">Toplam: 0 Kayıt</span>
            <span style="margin: 0 10px; color: #555;">|</span>
            <span>Süzülen Toplam Tutar: <strong id="mizan_ust_toplam_tutari" style="color: #2ecc71; font-size:13px;">0,00 ₺</strong></span>
        </div>
    </div>
    
    <div class="table-responsive bg-white">
        <table class="table table-hover table-striped-columns align-middle m-0" style="font-size: 11px;">
            <thead class="table-light text-uppercase text-secondary" style="font-size: 10px; letter-spacing: 0.5px; font-weight: 700;">
                <tr>
                    <th style="width: 4%;">ID</th>
                    <th style="width: 9%;">İşlem Tarihi</th>
                    <th style="width: 9%;">Kredi Karıt</th>
                    <th style="width: 15%;">Firma Adı</th>
                    <th>Harcama Açıklaması</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 8%;">Ödeme Türü</th>
                    <th style="width: 10%;" class="text-end">Tutar</th>
                    <th style="width: 8%;" class="text-center">İşlemler</th>
                </tr>
            </thead>
            <tbody class="text-dark fw-medium">
                <?php if (!empty($harcamalar)): ?>
                    <?php foreach ($harcamalar as $harcama): 
                        $is_isbank = ($harcama['kart_turu'] === 'isbank');
                        $kart_badge = $is_isbank ? 'bg-primary bg-opacity-10 text-primary' : 'bg-warning bg-opacity-10 text-warning';
                        $kart_adi = $is_isbank ? 'İş Bankası' : 'VakıfBank';
                        $kat_id = intval($harcama['kategori_id'] ?? 0);
                        $kategori_ismi = $kategori_haritasi[$kat_id] ?? null;
                        $rozet_renkleri = ['primary', 'success', 'danger', 'warning', 'info', 'dark'];
                        $renk = $rozet_renkleri[$kat_id % count($rozet_renkleri)];
                    ?>
                    <tr style="transition: all 0.15s;" data-kart="<?= $harcama['kart_turu'] ?>" data-taksit="<?= ($harcama['toplam_taksit'] > 1) ? 'TAKSITLI' : 'PESIN' ?>">
                        <td class="font-monospace text-muted"><?= $harcama['id'] ?></td>
                        <td class="font-monospace"><?= date('d.m.Y', strtotime($harcama['islem_tarihi'])) ?></td>
                        <td>
                            <span class="badge <?= $kart_badge ?> px-2 py-1" style="border-radius: 4px; font-weight: 600; font-size: 10px;">
                                <i class="fas <?= $is_isbank ? 'fa-credit-card' : 'fa-credit-card-alt' ?> me-1"></i> <?= $kart_adi ?>
                            </span>
                        </td>
                        <td class="align-middle text-start firma-duzenle-tetikleyici style-cursor" data-id="<?= $harcama['id'] ?>" data-mevcut-firma="<?= htmlspecialchars($harcama['isletme_adi'] ?? '') ?>" style="cursor: pointer;">
                            <?php if (!empty($harcama['isletme_adi'])): ?>
                                <span class="badge bg-dark bg-opacity-10 text-dark border border-dark border-opacity-15 px-2 py-1 font-monospace text-uppercase" style="font-size: 9.5px; font-weight: 700; border-radius: 4px; letter-spacing: 0.3px;">
                                    <i class="fas fa-building me-1" style="font-size: 8px; opacity: 0.7;"></i> <?= htmlspecialchars($harcama['isletme_adi']) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-light text-secondary border border-secondary border-opacity-20 px-2 py-1 font-monospace" style="font-size: 9px; font-weight: 500; border-radius: 4px;"><i class="fas fa-store-slash me-1 opacity-50"></i> FİRMA ADI (BOŞ)</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-wrap font-monospace text-uppercase" style="max-width: 250px; font-size: 10.5px; letter-spacing: -0.2px;">
                            <?= htmlspecialchars($harcama['aciklama']) ?>
                        </td>
                        <td class="align-middle text-start etiket-duzenle-tetikleyici style-cursor" data-id="<?= $harcama['id'] ?>" data-aciklama="<?= htmlspecialchars($harcama['aciklama']) ?>" data-mevcut-tagler="<?= $kat_id ?>" style="cursor: pointer;">
                            <div class="d-flex flex-wrap gap-1 align-items-center etiket-rozet-alani">
                                <?php if ($kat_id > 0 && $kategori_ismi): ?>
                                    <span class="badge bg-<?= $renk ?> bg-opacity-10 text-<?= $renk ?> border border-<?= $renk ?> border-opacity-20 px-2 py-1 user-select-none d-inline-flex align-items-center" style="font-size: 9.5px; font-weight: 600; border-radius: 4px;" data-tag-id="<?= $kat_id ?>">
                                        <i class="fas fa-tag me-1" style="font-size: 7.5px; opacity: 0.7;"></i> <?= htmlspecialchars($kategori_ismi) ?>
                                        <span class="tekil-etiket-sil-btn ms-1.5 p-0 d-inline-flex align-items-center justify-content-center" data-harcama-id="<?= $harcama['id'] ?>" style="cursor: pointer; font-size: 11px; width: 12px; height: 12px; border-radius: 50%; opacity: 0.6;" onmouseover="this.style.opacity='1'; this.style.backgroundColor='rgba(0,0,0,0.1)';" onmouseout="this.style.opacity='0.6'; this.style.backgroundColor='transparent';" onclick="event.stopPropagation();"> &times; </span>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-1.5 py-1" style="font-size: 9px; font-weight: 500; border-radius: 4px;"><i class="fas fa-tag me-1 opacity-50"></i> KATEGORİ (BOŞ)</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="font-monospace text-secondary">
                            <?php if ($harcama['toplam_taksit'] > 1): ?>
                                <span class="text-danger fw-bold"><i class="fas fa-history me-1" style="font-size: 9px;"></i> <?= $harcama['mevcut_taksit'] ?>/<?= $harcama['toplam_taksit'] ?></span>
                            <?php else: ?>
                                <span class="text-muted">Peşin</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end font-monospace fw-bold text-primary" style="font-size: 11.5px;">
                            <?= number_format($harcama['tutar'], 2, ',', '.') ?> ₺
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn btn-xs btn-outline-secondary p-1 btnSatirDuzenle" data-json='<?= json_encode($harcama, JSON_HEX_APOS | JSON_HEX_QUOT) ?>' style="font-size: 9px; padding: 2px 5px !important;" title="Gelişmiş Düzenle"><i class="fas fa-edit"></i></button>
                                <button type="button" class="btn btn-xs btn-outline-danger p-1 btnSatirSil" data-id="<?= $harcama['id'] ?>" style="font-size: 9px; padding: 2px 5px !important;" title="Kalıcı Sil"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center p-4 text-muted"><i class="fas fa-folder-open d-block mb-2 fs-4 text-secondary opacity-50"></i> Bu döneme ait harcama mizanı bulunamadı.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- =========================================================================
// MODALLAR VE SEÇİM PENCERELERİ 🏢
// ========================================================================= -->

<!-- SATIR İÇİ TEKLİ KATEGORİ GÜNCELLEME MODALI -->
<div class="modal fade" id="etiketGuncelleModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0" style="font-size: 11px; border-radius: 6px;">
            <div class="modal-header bg-dark text-white p-2.5 border-0">
                <h6 class="modal-title m-0 fw-bold" style="font-size: 11px;"><i class="fas fa-tags text-warning me-1"></i> Kategoriyi Düzenle</h6>
                <button type="button" class="btn-close btn-close-white shadow-none m-0 p-1" data-bs-dismiss="modal" style="font-size: 8px;"></button>
            </div>
            <div class="modal-body p-3 bg-light text-start">
                <input type="hidden" id="modalHarcamaId">
                <div class="text-secondary fw-bold mb-2 text-uppercase font-monospace text-truncate" id="modalHarcamaMetni" style="font-size: 9px; letter-spacing: 0.3px;"></div>
                
                <label class="form-label fw-bold text-secondary mb-1">Hedef Kategori Seçin</label>
                <select id="modalKategoriSelect" class="form-select form-select-sm border-0 bg-white shadow-sm fw-bold" style="font-size: 11px;">
                    <option value="0">-- KATEGORİ (BOŞ) --</option>
                    <?php foreach ($tum_kategoriler as $kat): ?>
                        <option value="<?= $kat['id'] ?>"><?= htmlspecialchars($kat['kategori_adi']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <button type="button" id="btnEtiketleriKaydet" class="btn btn-sm btn-success w-100 p-2 fw-bold mt-3 border-0 d-inline-flex align-items-center justify-content-center" style="background-color: #2e7d32; border-radius: 4px; font-size: 11px;">
                    <i class="fas fa-check-double me-1"></i> Değişikliği Uygula
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TOPLU KATEGORİ GÜNCELLEME MODALI (BULK TAGGING) -->
<div class="modal fade" id="topluEtiketleModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0" style="font-size: 11px; border-radius: 6px;">
            <div class="modal-header bg-primary text-white p-2.5 border-0">
                <h6 class="modal-title m-0 fw-bold" style="font-size: 11px;"><i class="fas fa-layer-group me-1"></i> Toplu Kategori Yönetimi</h6>
                <button type="button" class="btn-close btn-close-white shadow-none m-0 p-1" data-bs-dismiss="modal" style="font-size: 8px;"></button>
            </div>
            <div class="modal-body p-3 bg-light text-start">
                <div class="alert alert-info py-1.5 px-2 mb-3 border-0 font-monospace text-center" style="font-size: 10px; border-radius: 4px;">
                    <i class="fas fa-info-circle me-1"></i> Toplam <strong id="modalTopluSatirAdet">0</strong> harcama güncellenecektir.
                </div>
                <label class="form-label fw-bold text-secondary mb-1">Eylem Türü</label>
                <select id="topluGuncellemeTuru" class="form-select form-select-sm mb-3 border-0 bg-white shadow-none" style="font-size: 11px;">
                    <option value="uzerine_yaz">Seçilen Kategoriyle Üzerine Yaz</option>
                    <option value="tamamen_sil">Seçilen Harcamaların Kategorisini Sıfırla (0 Yap)</option>
                </select>
                
                <div id="topluKategoriSeçimAlani">
                    <label class="form-label fw-bold text-secondary mb-1">Uygulanacak Kategori</label>
                    <select id="modalTopluKategoriSelect" class="form-select form-select-sm border-0 bg-white shadow-sm fw-bold" style="font-size: 11px;">
                        <option value="0">-- KATEGORİ (BOŞ) --</option>
                        <?php foreach ($tum_kategoriler as $kat): ?>
                            <option value="<?= $kat['id'] ?>"><?= htmlspecialchars($kat['kategori_adi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="button" id="btnTopluKaydetCalistir" class="btn btn-sm btn-success w-100 p-2 fw-bold mt-3 border-0 d-inline-flex align-items-center justify-content-center" style="background-color: #198754; border-radius: 4px; font-size: 11px;">
                    <i class="fas fa-save me-1"></i> Toplu Değişikliği Uygula
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SATIR İÇİ TEKİL FİRMA ADI DÜZENLEME MODALI -->
<div class="modal fade" id="tekilFirmaDuzenleModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-sm" style="font-size: 11px; border-radius: 6px; overflow: hidden;">
            <div class="modal-header bg-dark text-white p-2.5 border-0">
                <h6 class="modal-title m-0 fw-bold d-inline-flex align-items-center" style="font-size: 11px;">
                    <i class="fas fa-pen-to-square text-warning me-1.5"></i> Firma Adını Düzenle
                </h6>
                <button type="button" class="btn-close btn-close-white shadow-none m-0 p-1" data-bs-dismiss="modal" style="font-size: 9px;"></button>
            </div>
            <div class="modal-body p-3 bg-light text-start">
                <input type="hidden" id="modalTekilHarcamaId">
                <div class="mb-3">
                    <label for="modalTekilFirmaInput" class="form-label fw-bold text-secondary mb-1" style="font-size: 10.5px;">
                        <i class="fas fa-store me-1 text-muted"></i> İşletme / Marka Adı
                    </label>
                    <input type="text" id="modalTekilFirmaInput" class="form-control form-control-sm border-0 bg-white shadow-sm fw-bold text-dark font-monospace text-uppercase" placeholder="Firma adı seçin veya yazın..." list="firmaOnerileri" autocomplete="off" style="font-size: 11px; height: 31px; border-radius: 4px;">
                </div>
                <button type="button" id="btnTekilFirmaKaydet" class="btn btn-sm btn-success w-100 p-2 fw-bold border-0 d-inline-flex align-items-center justify-content-center shadow-sm" style="background-color: #198754; border-radius: 4px; font-size: 11px;">
                    <i class="fas fa-save me-1.5"></i> Firma Adını Güncelle
                </button>
            </div>
        </div>
    </div>
</div>

<!-- AKILLI TOPLU FİRMA ADI ATA MODALI -->
<div class="modal fade" id="topluFirmaModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-sm" style="font-size: 11px; border-radius: 6px; overflow: hidden;">
            <div class="modal-header bg-success text-white p-2.5 border-0 d-flex align-items-center justify-content-between" style="background-color: #198754 !important;">
                <h6 class="modal-title m-0 fw-bold d-inline-flex align-items-center" style="font-size: 11px;">
                    <i class="fas fa-store me-1.5"></i> Toplu Firma Adı Ata
                </h6>
                <button type="button" class="btn-close btn-close-white shadow-none m-0 p-1" data-bs-dismiss="modal" style="font-size: 9px;"></button>
            </div>
            <div class="modal-body p-3 bg-light text-start">
                <div class="alert alert-success py-1.5 px-2 mb-3 border-0 font-monospace text-center text-success bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="font-size: 10px; border-radius: 4px;">
                    <i class="fas fa-info-circle me-1.5"></i> Süzülen <strong class="modalSatirAdetFirma mx-1">0</strong> harcama mühürlenecektir.
                </div>
                <div class="mb-3">
                    <label for="topluFirmaInput" class="form-label fw-bold text-secondary mb-1" style="font-size: 10.5px;">
                        <i class="fas fa-building-user me-1 text-muted"></i> Toplu Atanacak Firma Adı
                    </label>
                    <input type="text" id="topluFirmaInput" class="form-control form-control-sm border-0 bg-white shadow-sm fw-bold text-dark font-monospace text-uppercase" placeholder="Örn: MIGROS, BİM, KOTON..." list="firmaOnerileri" autocomplete="off" style="font-size: 11px; height: 31px; border-radius: 4px;">
                </div>
                <button type="button" id="btnTopluFirmaKaydetCalistir" class="btn btn-sm btn-success w-100 p-2 fw-bold border-0 d-inline-flex align-items-center justify-content-center shadow-sm" style="background-color: #198754; border-radius: 4px; font-size: 11px;">
                    <i class="fas fa-check-double me-1.5"></i> Firmayı Toplu Ata
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TOPLU ÖDEME TÜRÜ DEĞİŞTİRME MODALI -->
<div class="modal fade" id="topluOdemeModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-sm" style="font-size: 11px; border-radius: 6px; overflow: hidden;">
            <div class="modal-header bg-dark text-white p-2.5 border-0">
                <h6 class="modal-title m-0 fw-bold d-inline-flex align-items-center" style="font-size: 11px;">
                    <i class="fas fa-credit-card text-warning me-1.5"></i> Toplu Ödeme Biçimi Yönetimi
                </h6>
                <button type="button" class="btn-close btn-close-white shadow-none m-0 p-1" data-bs-dismiss="modal" style="font-size: 9px;"></button>
            </div>
            <div class="modal-body p-3 bg-light text-start">
                <div class="alert alert-secondary py-1.5 px-2 mb-3 border-0 font-monospace text-center text-dark bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="font-size: 10px; border-radius: 4px;">
                    <i class="fas fa-info-circle me-1.5"></i> Süzülen <strong class="modalSatirAdetOdeme mx-1">0</strong> harcama güncellenecektir.
                </div>
                
                <!-- ÖDEME TÜRÜ RADYO SEÇİM GRUBU -->
                <label class="form-label fw-bold text-secondary mb-1">Yeni Hesaplaşma Biçimi</label>
                <div class="d-flex gap-3 bg-white p-2 rounded border border-light mb-3">
                    <div class="form-check m-0">
                        <input class="form-check-input" type="radio" name="toplu_satis_turu" id="toplu_odeme_pesin" value="PESIN" checked>
                        <label class="form-check-label fw-bold text-muted" for="toplu_odeme_pesin" style="cursor:pointer; user-select:none;">Peşin İşlem</label>
                    </div>
                    <div class="form-check m-0">
                        <input class="form-check-input" type="radio" name="toplu_satis_turu" id="toplu_odeme_taksit" value="TAKSITLI">
                        <label class="form-check-label fw-bold text-muted" for="toplu_odeme_taksit" style="cursor:pointer; user-select:none;">Taksitli İşlem</label>
                    </div>
                </div>

                <!-- GİZLENEBİLİR TOPLAM TAKSİT GİRDİSİ -->
                <div id="topluTaksitGirdiGrubu" class="col-md-12 d-none mb-3">
                    <label class="form-label fw-bold text-secondary mb-1">Atanacak Toplam Taksit Sayısı</label>
                    <input type="number" class="form-control form-control-sm border-0 bg-white text-center font-monospace fw-bold" id="toplu_toplam_taksit" min="2" value="2" style="height: 32px; border-radius:4px;">
                </div>
                
                <button type="button" id="btnTopluOdemeKaydetCalistir" class="btn btn-sm btn-success w-100 p-2 fw-bold border-0 d-inline-flex align-items-center justify-content-center shadow-sm" style="background-color: #198754; border-radius: 4px; font-size: 11px;">
                    <i class="fas fa-check-double me-1.5"></i> Ödeme Türünü Toplu Uygula
                </button>
            </div>
        </div>
    </div>
</div>


<!-- KANALLI SATIR TAM DÜZENLEME MODALI (GİZLENEBİLİR TAKSİT MOTORLU) -->
<div class="modal fade" id="harcamaTamDuzenleModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="font-size: 11px; border-radius: 6px;">
            <div class="modal-header bg-dark text-white p-3 border-0">
                <h6 class="modal-title m-0 fw-bold"><i class="fas fa-sliders text-warning me-1.5"></i> Harcama Kayıdı Düzenle</h6>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 bg-light">
                <form id="formHarcamaTamDuzenle">
                    <input type="hidden" id="edit_id" name="id">
                    <input type="hidden" name="islem" value="harcama_tam_guncelle_ajax">
                    
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary mb-1">İşlem Tarihi</label>
                            <input type="date" class="form-control form-control-sm border-0 bg-white fw-bold" id="edit_islem_tarihi" name="islem_tarihi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary mb-1">Ekstre Dönemi</label>
                            <input type="text" class="form-control form-control-sm border-0 bg-white fw-bold font-monospace" id="edit_donem" name="donem" placeholder="YYYY-MM" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary mb-1">Kredi Kartı</label>
                            <select class="form-select form-select-sm border-0 bg-white fw-bold" id="edit_kart_turu" name="kart_turu">
                                <option value="isbank">İş Bankası</option>
                                <option value="vakifbank">Vakıfbank</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary mb-1">Harcama Tutarı (₺)</label>
                            <input type="text" class="form-control form-control-sm border-0 bg-white fw-bold text-primary font-monospace" id="edit_tutar" name="tutar" placeholder="0,00" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-secondary mb-1">İşletme Adı</label>
                            <input type="text" class="form-control form-control-sm border-0 bg-white fw-bold font-monospace text-uppercase" id="edit_isletme_adi" name="isletme_adi" placeholder="Firma adı seçin veya belirtin..." list="firmaOnerileri" autocomplete="off">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-secondary mb-1">Harcama Açıklaması</label>
                            <textarea class="form-control form-control-sm border-0 bg-white text-uppercase font-monospace" id="edit_aciklama" name="aciklama" rows="2" style="font-size:10.5px;" placeholder="Detaylı açıklama belirtin..." required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-secondary mb-1">Kategori</label>
                            <select class="form-select form-select-sm border-0 bg-white fw-bold" id="edit_kategori_id" name="kategori_id">
                                <option value="0">-- KATEGORİ (BOŞ) --</option>
                                <?php foreach ($tum_kategoriler as $kat): ?>
                                    <option value="<?= $kat['id'] ?>"><?= htmlspecialchars($kat['kategori_adi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- PREMIUM REFORM: PEŞİN/TAKSİT INTERAKTIF SEÇİM ALANI -->
                        <div class="col-md-12 py-1">
                            <label class="form-label fw-bold text-secondary mb-1 d-block">Ödeme Türü</label>
                            <div class="d-flex gap-3 bg-white p-2 rounded border border-light">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="satis_turu" id="edit_satis_pesin" value="PESIN" checked>
                                    <label class="form-check-label fw-bold text-muted" for="edit_satis_pesin" style="cursor:pointer; user-select:none;">Peşin Satış</label>
                                </div>
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="satis_turu" id="edit_satis_taksit" value="TAKSITLI">
                                    <label class="form-check-label fw-bold text-muted" for="edit_satis_taksit" style="cursor:pointer; user-select:none;">Taksitli Satış</label>
                                </div>
                            </div>
                        </div>

                        <!-- GİZLENEBİLİR TAKSİT PANELİ -->
                        <div id="editTaksitGirdiGrubu" class="col-md-12 d-none">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label fw-bold text-secondary mb-1">Mevcut Taksit No</label>
                                    <input type="number" class="form-control form-control-sm border-0 bg-white text-center font-monospace" id="edit_mevcut_taksit" name="mevcut_taksit" min="1" value="1">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-secondary mb-1">Toplam Taksit Sayısı</label>
                                    <input type="number" class="form-control form-control-sm border-0 bg-white text-center font-monospace" id="edit_toplam_taksit" name="toplam_taksit" min="1" value="1">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" id="btnHarcamaTamKaydet" class="btn btn-sm btn-primary w-100 p-2 fw-bold mt-3 border-0 d-inline-flex align-items-center justify-content-center shadow-sm" style="border-radius:4px;">
                        <i class="fas fa-save me-1.5"></i> Düzenlemeleri Kaydet
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- OTOMATİK TAMAMLAMA DATA LISTESİ -->
<datalist id="firmaOnerileri">
    <?php if (!empty($tum_firma_kurallari) && is_array($tum_firma_kurallari)): ?>
        <?php foreach ($tum_firma_kurallari as $firma_kurali): ?>
            <option value="<?= htmlspecialchars(trim($firma_kurali)) ?>"></option>
        <?php endforeach; ?>
    <?php endif; ?>
</datalist>



<script>
// Sayfa açıldığında yerel depolamadaki filtre kilitlerini çağırıyoruz
let seciliFiltreTagId = localStorage.getItem('mizan_secili_tag') || "HEPSI";
let seciliFiltreFirmaAdi = localStorage.getItem('mizan_secili_firma_rozet') || "HEPSI";
let seciliFiltreKartTuru = localStorage.getItem('mizan_secili_kart') || "HEPSI";
let seciliFiltreTaksitDurumu = localStorage.getItem('mizan_secili_taksit') || "HEPSI";

/**
 * Türkçe karakter uyuşmazlık kalkanı
 */
function turkceKarakterEvrenselYap(metin) {
    if (!metin) return "";
    return metin.toString()
        .replace(/İ/g, "i").replace(/I/g, "i").replace(/ı/g, "i")
        .replace(/Ş/g, "s").replace(/ş/g, "s").replace(/Ç/g, "c")
        .replace(/ç/g, "c").replace(/Ü/g, "u").replace(/ü/g, "u")
        .replace(/Ö/g, "o").replace(/ö/g, "o").replace(/Ğ/g, "g")
        .replace(/ğ/g, "g").replace(/[\/.,\-]/g, " ").replace(/\s+/g, " ")
        .toLowerCase().trim();
}

/**
 * 6 Katmanlı Gelişmiş Mizan Süzme Motoru ve Canlı Toplam Muhasebe Çekirdeği ⚡
 */
function dinamikMizanTablosunuSuz() {
    let arananKelime = turkceKarakterEvrenselYap($('#filtreFirma').val());
    let baslangicTar  = $('#filtreTarihBaslangic').val();
    let bitisTar      = $('#filtreTarihBitis').val();
    let selectboxFirma = $('#filtreFirmaSelectbox').val();

    // Filtre durumlarını tarayıcı hafızasına kilitliyoruz
    localStorage.setItem('mizan_filtre_firma_input', $('#filtreFirma').val());
    localStorage.setItem('mizan_filtre_firma_select', selectboxFirma);
    localStorage.setItem('mizan_filtre_baslangic', baslangicTar);
    localStorage.setItem('mizan_filtre_bitis', bitisTar);

    let visibleSatirSayisi = 0;
    let filtreliToplamTutar = 0.00;

    $('table tbody tr').each(function() {
        let $satir = $(this);
        if ($satir.find('td').length <= 1) return;

        let temizSatirMetni = turkceKarakterEvrenselYap($satir.text());
        let rowKart = $satir.attr('data-kart') || "";
        let rowTaksit = $satir.attr('data-taksit') || "";
        let satirKategoriId = $satir.find('.etiket-duzenle-tetikleyici').attr('data-mevcut-tagler') || "0";
        let satirFirmaAdiKoku = $satir.find('.firma-duzenle-tetikleyici').attr('data-mevcut-firma') || "";
        let isFirmaIsmiBosMu = (satirFirmaAdiKoku.trim() === "");

        // Tarih normalleştirme
        let hamTabloTarihi = $satir.find('td:nth-child(2)').text().trim();
        let parca = hamTabloTarihi.split('.');
        let formatliTabloTarihi = (parca.length === 3) ? `${parca[2]}-${parca[1]}-${parca[0]}` : "";

        // 1) Serbest Kelime Arama Kontrolü
        let isKelimeUyumlu = (arananKelime === "" || temizSatirMetni.indexOf(arananKelime) !== -1);
        
        // 2) Kart Türü Rozet Kontrolü
        let isKartUyumlu = (seciliFiltreKartTuru === "HEPSI" || rowKart === seciliFiltreKartTuru);
        
        // 3) Hesaplaşma/Taksit Rozet Kontrolü
        let isTaksitUyumlu = (seciliFiltreTaksitDurumu === "HEPSI" || rowTaksit === seciliFiltreTaksitDurumu);

        // 4) Kategori Rozet Kontrolü
        let isTagUyumlu = false;
        if (seciliFiltreTagId === "HEPSI") {
            isTagUyumlu = true;
        } else if (seciliFiltreTagId === "0") {
            isTagUyumlu = (satirKategoriId === "0" || satirKategoriId === "");
        } else {
            isTagUyumlu = (satirKategoriId.toString() === seciliFiltreTagId.toString());
        }

        // 5) Selectbox ve Popüler Rozet Ortak Firma Süzme Çekirdeği 🎯
        let isFirmaUyumlu = false;
        if (selectboxFirma !== "HEPSI") {
            if (selectboxFirma === "FIRMASIZLAR") {
                isFirmaUyumlu = isFirmaIsmiBosMu;
            } else {
                isFirmaUyumlu = (satirFirmaAdiKoku.trim().toUpperCase() === selectboxFirma.trim().toUpperCase());
            }
        } else {
            if (seciliFiltreFirmaAdi === "HEPSI") {
                isFirmaUyumlu = true;
            } else if (seciliFiltreFirmaAdi === "FIRMASIZLAR") {
                isFirmaUyumlu = isFirmaIsmiBosMu;
            } else if (seciliFiltreFirmaAdi === "DIGER") {
                isFirmaUyumlu = true;
            } else {
                isFirmaUyumlu = (satirFirmaAdiKoku.trim().toUpperCase() === seciliFiltreFirmaAdi.trim().toUpperCase());
            }
        }

        // 6) Tarih Skalası Kontrolü
        let isTarihUyumlu = true;
        if (formatliTabloTarihi !== "") {
            if (baslangicTar && formatliTabloTarihi < baslangicTar) isTarihUyumlu = false;
            if (bitisTar && formatliTabloTarihi > bitisTar) isTarihUyumlu = false;
        }

        // 🎯 TÜM SÜZGEC KANALLARINDAN BAŞARIYLA GEÇEN SATIRI GÖSTER VE TUTARI HESAPLA
        if (isKelimeUyumlu && isKartUyumlu && isTaksitUyumlu && isTagUyumlu && isFirmaUyumlu && isTarihUyumlu) {
            $satir.removeClass('d-none').addClass('süzülen-aktif-satir');
            visibleSatirSayisi++;

            let hamTutarMetni = $satir.find('td:nth-last-child(2)').text() || "0";
            let temizTutar = hamTutarMetni.replace(/[^\d.,-]/g, '').trim();
            if (temizTutar !== "") {
                if (temizTutar.indexOf(',') > -1) {
                    temizTutar = temizTutar.replace(/\./g, '').replace(',', '.');
                }
                let satirTutari = parseFloat(temizTutar);
                if (!isNaN(satirTutari)) { filtreliToplamTutar += satirTutari; }
            }
        } else {
            $satir.addClass('d-none').removeClass('süzülen-aktif-satir');
        }
    });

    // İSTATİSTİK ŞERİDİ BAĞLANTILARI
    let formatliCikti = filtreliToplamTutar.toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + ' ₺';
    $('#mizan_ust_toplam_tutari').text(formatliCikti);
    $('#mizan_ust_kayit_adedi').text(`Toplam: ${visibleSatirSayisi} Kayıt`);

    // Toplu buton kilit yönetim sensörü
    if (visibleSatirSayisi > 0 && ($('#filtreFirma').val() !== "" || selectboxFirma !== "HEPSI" || baslangicTar !== "" || bitisTar !== "" || seciliFiltreKartTuru !== "HEPSI" || seciliFiltreTaksitDurumu !== "HEPSI" || seciliFiltreTagId !== "HEPSI" || seciliFiltreFirmaAdi !== "HEPSI")) {
        $('#btnTopluEtiketleModalAc, #btnTopluFirmaModalAc, #btnTopluOdemeModalAc').removeClass('d-none');
        $('#topluFirmaSatirSayisi, #topluEtiketSatirSayisi, #topluOdemeSatirSayisi').text(visibleSatirSayisi);
    } else {
        $('#btnTopluEtiketleModalAc, #btnTopluFirmaModalAc, #btnTopluOdemeModalAc').addClass('d-none');
    }

}

// KATEGORİ SÜZGEÇ ROZET DİNLEYİCİSİ (URL PARAMETRE DESTEKLİ REFORM)
$(document).on('click', '.tag-filtre-rozet', function() {
    $('.tag-filtre-rozet').removeClass('bg-dark text-white aktif').addClass('bg-secondary text-dark bg-opacity-10');
    $(this).removeClass('bg-secondary text-dark bg-opacity-10').addClass('bg-dark text-white aktif');
    
    seciliFiltreTagId = ($(this).attr('data-id') || "HEPSI").toString();
    localStorage.setItem('mizan_secili_tag', seciliFiltreTagId);
    
    // 🎯 AKILLI YÖNLENDİRME: Kategori değiştiğinde firmaları yeniden skorlamak için sayfayı küçük bir parametreyle tazeliyoruz
    let mevcutUrl = new URL(window.location.href);
    if (seciliFiltreTagId !== "HEPSI" && seciliFiltreTagId !== "0") {
        mevcutUrl.searchParams.set('kat_id', seciliFiltreTagId);
    } else {
        mevcutUrl.searchParams.delete('kat_id');
    }
    
    // Sayfayı yeniler, localStorage hafızası sayesinde tüm süzgeçleriniz tam kaldığı yerden devam eder!
    window.location.href = mevcutUrl.toString();
});


$(document).on('click', '.btn-kart-filtre', function() {
    $('.btn-kart-filtre').removeClass('btn-dark text-white aktif').addClass('btn-outline-secondary');
    $(this).removeClass('btn-outline-secondary').addClass('btn-dark text-white aktif');
    seciliFiltreKartTuru = $(this).attr('data-kart') || "HEPSI";
    localStorage.setItem('mizan_secili_kart', seciliFiltreKartTuru);
    dinamikMizanTablosunuSuz();
});

$(document).on('click', '.btn-taksit-filtre', function() {
    $('.btn-taksit-filtre').removeClass('btn-dark text-white aktif').addClass('btn-outline-secondary');
    $(this).removeClass('btn-outline-secondary').addClass('btn-dark text-white aktif');
    seciliFiltreTaksitDurumu = $(this).attr('data-taksit') || "HEPSI";
    localStorage.setItem('mizan_secili_taksit', seciliFiltreTaksitDurumu);
    dinamikMizanTablosunuSuz();
});

$(document).on('input change', '#filtreFirma, #filtreTarihBaslangic, #filtreTarihBitis', function() {
    dinamikMizanTablosunuSuz();
});

// SELECTBOX DEĞİŞTİĞİNDE POPÜLER ROZETLERİ SENKRONİZE ET
$(document).on('change', '#filtreFirmaSelectbox', function() {
    let secilenVal = $(this).val();
    $('.firma-filtre-rozet').removeClass('bg-dark text-white aktif').addClass('bg-secondary text-dark bg-opacity-10');
    
    let $rozetBul = $(`.firma-filtre-rozet[data-firma="${secilenVal}"]`);
    if ($rozetBul.length > 0) {
        $('#rozetFirmaDiger').addClass('d-none');
        $rozetBul.removeClass('bg-secondary text-dark bg-opacity-10').addClass('bg-dark text-white aktif');
        seciliFiltreFirmaAdi = secilenVal;
    } else if (secilenVal !== "HEPSI" && secilenVal !== "FIRMASIZLAR") {
        $('#digerFirmaEtiketIsmi').text(secilenVal);
        $('#rozetFirmaDiger').removeClass('d-none bg-secondary text-dark bg-opacity-10').addClass('bg-dark text-white aktif');
        seciliFiltreFirmaAdi = "DIGER";
    } else {
        $('#rozetFirmaDiger').addClass('d-none');
        $(`.firma-filtre-rozet[data-firma="HEPSI"]`).addClass('bg-dark text-white aktif');
        seciliFiltreFirmaAdi = "HEPSI";
    }
    localStorage.setItem('mizan_secili_firma_rozet', seciliFiltreFirmaAdi);
    dinamikMizanTablosunuSuz();
});

// POPÜLER ROZETE TIKLANDIĞINDA SELECTBOX'I EŞZAMANLI DEĞİŞTİR 🎯
$(document).on('click', '.firma-filtre-rozet', function() {
    let fAdi = $(this).attr('data-firma') || "HEPSI";
    if (fAdi === "DIGER") return;
    
    $('.firma-filtre-rozet').removeClass('bg-dark text-white aktif').addClass('bg-secondary text-dark bg-opacity-10');
    $(this).removeClass('bg-secondary text-dark bg-opacity-10').addClass('bg-dark text-white aktif');
    $('#rozetFirmaDiger').addClass('d-none');

    seciliFiltreFirmaAdi = fAdi;
    localStorage.setItem('mizan_secili_firma_rozet', seciliFiltreFirmaAdi);
    $('#filtreFirmaSelectbox').val(fAdi);
    dinamikMizanTablosunuSuz();
});

// MODAL İÇİ PEŞİN/TAKSİT INTERAKTIF SEÇİM DİNLEYİCİSİ 🎛️
$(document).on('change', 'input[name="satis_turu"]', function() {
    if ($(this).val() === 'TAKSITLI') {
        $('#editTaksitGirdiGrubu').removeClass('d-none').hide().fadeIn(150);
    } else {
        $('#editTaksitGirdiGrubu').fadeOut(100, function() {
            $(this).addClass('d-none');
            $('#edit_mevcut_taksit, #edit_toplam_taksit').val(1);
        });
    }
});

// VERİ BESLEME VE MODAL TETİKLEYİCİLERİ
$(document).on('click', '.btnSatirDuzenle', function() {
    let veri = $(this).data('json');
    if (!veri) return;

    $('#edit_id').val(veri.id);
    $('#edit_islem_tarihi').val(veri.islem_tarihi);
    $('#edit_donem').val(veri.donem);
    $('#edit_kart_turu').val(veri.kart_turu);
    $('#edit_isletme_adi').val(veri.isletme_adi);
    $('#edit_aciklama').val(veri.aciklama);
    $('#edit_kategori_id').val(veri.kategori_id);
    
    if (parseInt(veri.toplam_taksit) > 1) {
        $('#edit_satis_taksit').prop('checked', true);
        $('#editTaksitGirdiGrubu').removeClass('d-none');
        $('#edit_mevcut_taksit').val(veri.mevcut_taksit);
        $('#edit_toplam_taksit').val(veri.toplam_taksit);
    } else {
        $('#edit_satis_pesin').prop('checked', true);
        $('#editTaksitGirdiGrubu').addClass('d-none');
        $('#edit_mevcut_taksit').val(1);
        $('#edit_toplam_taksit').val(1);
    }

    let formatliTutar = parseFloat(veri.tutar).toFixed(2).replace('.', ',');
    $('#edit_tutar').val(formatliTutar);
    $('#harcamaTamDuzenleModal').modal('show');
});

// ASENKRON AJAX FORM VE İŞLEM YÖNETİMİ
$(document).on('submit', '#formHarcamaTamDuzenle', function(e) {
    e.preventDefault();
    let btn = $('#btnHarcamaTamKaydet');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Kaydediliyor...');
    $.ajax({
        url: window.location.href, type: 'POST', data: $(this).serialize(), dataType: 'json',
        success: function(res) { if (res && res.success) { location.reload(); } else { alert('Hata: ' + res.message); btn.prop('disabled', false).html('Kaydet'); } }
    });
});

$(document).on('click', '.etiket-duzenle-tetikleyici', function(e) {
    if ($(e.target).hasClass('tekil-etiket-sil-btn')) return false;
    $('#modalHarcamaId').val($(this).data('id'));
    $('#modalHarcamaMetni').text(`İŞLEM: ${$(this).data('aciklama')}`);
    $('#modalKategoriSelect').val($(this).attr('data-mevcut-tagler') || "0");
    $('#etiketGuncelleModal').modal('show');
});

$(document).on('click', '#btnEtiketleriKaydet', function() {
    $.ajax({
        url: window.location.href, type: 'POST',
        data: { islem: 'etiket_guncelle_ajax', harcama_id: parseInt($('#modalHarcamaId').val()), yeni_tagler: parseInt($('#modalKategoriSelect').val()) },
        dataType: 'json', success: function(res) { if (res && res.success) { location.reload(); } }
    });
});

$(document).on('click', '.tekil-etiket-sil-btn', function() {
    if (!confirm('Kategoriyi kaldırmak istediğinize emin misiniz?')) return;
    $.ajax({
        url: window.location.href, type: 'POST', data: { islem: 'tekil_etiket_sil_ajax', harcama_id: parseInt($(this).data('harcama-id')) },
        dataType: 'json', success: function(res) { if (res && res.success) { location.reload(); } }
    });
});

$(document).on('click', '.firma-duzenle-tetikleyici', function() {
    $('#modalTekilHarcamaId').val($(this).attr('data-id'));
    $('#modalTekilFirmaInput').val($(this).attr('data-mevcut-firma') || "");
    $('#tekilFirmaDuzenleModal').modal('show');
});

$(document).on('click', '#btnTekilFirmaKaydet', function() {
    let yeniFirma = $('#modalTekilFirmaInput').val().trim(); if (yeniFirma === "") return;
    $.ajax({
        url: window.location.href, type: 'POST', data: { islem: 'tekil_firma_guncelle_ajax', harcama_id: parseInt($('#modalTekilHarcamaId').val()), yeni_firma: yeniFirma },
        dataType: 'json', success: function(res) { if (res && res.success) { location.reload(); } }
    });
});

$(document).on('click', '#btnTopluFirmaModalAc', function() {
    $('.modalSatirAdetFirma').text($('#topluFirmaSatirSayisi').text());
    $('#topluFirmaInput').val('');
    $('#topluFirmaModal').modal('show');
});

$(document).on('click', '#btnTopluFirmaKaydetCalistir', function() {
    let yeniFirma = $('#topluFirmaInput').val().trim(); if (yeniFirma === "") return;
    let süzülenIds = [];
    $('table tbody tr.süzülen-aktif-satir').each(function() {
        let id = $(this).find('.firma-duzenle-tetikleyici').attr('data-id'); if(id) süzülenIds.push(parseInt(id));
    });
    if (süzülenIds.length === 0) return;
    $.ajax({
        url: window.location.href, type: 'POST', data: { islem: 'toplu_firma_guncelle_ajax', harcama_ids: süzülenIds, yeni_firma: yeniFirma },
        dataType: 'json', success: function(res) { if (res && res.success) { location.reload(); } }
    });
});

$(document).on('click', '#btnTopluEtiketleModalAc', function() {
    $('#modalTopluSatirAdet').text($('#topluEtiketSatirSayisi').text());
    $('#topluEtiketleModal').modal('show');
});

$(document).on('click', '#btnTopluKaydetCalistir', function() {
    let süzülenIds = [];
    $('table tbody tr.süzülen-aktif-satir').each(function() {
        let id = $(this).find('.etiket-duzenle-tetikleyici').attr('data-id'); if(id) süzülenIds.push(parseInt(id));
    });
    if(süzülenIds.length === 0) return;
    $.ajax({
        url: window.location.href, type: 'POST',
        data: { islem: 'toplu_etiket_guncelle_ajax', harcama_ids: süzülenIds, yeni_tagler: parseInt($('#modalTopluKategoriSelect').val()), guncelleme_turu: $('#topluGuncellemeTuru').val() },
        dataType: 'json', success: function(res) { if (res && res.success) { location.reload(); } }
    });
});

// TOPLU ÖDEME TÜRÜ MODALINI TETİKLEME
$(document).on('click', '#btnTopluOdemeModalAc', function() {
    let adet = $('#topluOdemeSatirSayisi').text();
    $('.modalSatirAdetOdeme').text(adet);
    $('#toplu_odeme_pesin').prop('checked', true);
    $('#topluTaksitGirdiGrubu').addClass('d-none');
    $('#toplu_toplam_taksit').val(2);
    $('#topluOdemeModal').modal('show');
});

// TOPLU MODAL İÇİ RADYO SEÇİM TAKİPÇİSİ
$(document).on('change', 'input[name="toplu_satis_turu"]', function() {
    if ($(this).val() === 'TAKSITLI') {
        $('#topluTaksitGirdiGrubu').removeClass('d-none').hide().fadeIn(150);
    } else {
        $('#topluTaksitGirdiGrubu').fadeOut(100, function() { $(this).addClass('d-none'); });
    }
});

// TOPLU ÖDEME TÜRÜ AJAX YÜRÜTÜCÜ MOTORU
$(document).on('click', '#btnTopluOdemeKaydetCalistir', function() {
    let btn = $(this);
    let odemeTuru = $('input[name="toplu_satis_turu"]:checked').val();
    let taksitSayisi = $('#toplu_toplam_taksit').val();

    let süzülenIds = [];
    $('table tbody tr.süzülen-aktif-satir').each(function() {
        let id = $(this).find('.firma-duzenle-tetikleyici').attr('data-id'); 
        if(id) süzülenIds.push(parseInt(id));
    });

    if (süzülenIds.length === 0) return;
    btn.prop('disabled', true).html('Yürütülüyor...');

    $.ajax({
        url: window.location.href,
        type: 'POST',
        data: { 
            islem: 'toplu_odeme_turu_guncelle_ajax', 
            harcama_ids: süzülenIds, 
            odeme_turu: odemeTuru, 
            toplam_taksit: parseInt(taksitSayisi) 
        },
        dataType: 'json',
        success: function(res) { 
            if (res && res.success) { 
                location.reload(); 
            } else {
                alert('Hata: ' + res.message);
                btn.prop('disabled', false).html('<i class="fas fa-check-double me-1.5"></i> Uygula');
            }
        },
        error: function() {
            alert('Toplu ödeme türü güncellenemedi!');
            btn.prop('disabled', false).html('<i class="fas fa-check-double me-1.5"></i> Uygula');
        }
    });
});


$(document).on('click', '.btnSatirSil', function() {
    if (!confirm('Bu harcama kaydını mizan havuzundan tamamen silmek istediğinize emin misiniz?')) return;
    $.ajax({
        url: window.location.href, type: 'POST', data: { islem: 'harcama_sil_ajax', id: parseInt($(this).data('id')) },
        dataType: 'json', success: function(res) { if (res && res.success) { location.reload(); } }
    });
});

// HAFIZA DURUMUNU GERI YÜKLEME ÇEKİRDEĞİ
$(document).ready(function() {
    // Tarayıcı hafızasında kayıtlı olan girdi verilerini form elemanlarına geri dolduruyoruz
    $('#filtreFirma').val(localStorage.getItem('mizan_filtre_firma_input') || "");
    $('#filtreTarihBaslangic').val(localStorage.getItem('mizan_filtre_baslangic') || "");
    $('#filtreTarihBitis').val(localStorage.getItem('mizan_filtre_bitis') || "");
    
    let eskiSelectFirma = localStorage.getItem('mizan_filtre_firma_select') || "HEPSI";
    $('#filtreFirmaSelectbox').val(eskiSelectFirma);

    // Kart filtre rozetlerini hafızaya göre tarayıp premium koyu tona boyama
    $('.btn-kart-filtre').removeClass('btn-dark text-white aktif').addClass('btn-outline-secondary');
    $(`.btn-kart-filtre[data-kart="${seciliFiltreKartTuru}"]`).removeClass('btn-outline-secondary').addClass('btn-dark text-white aktif');

    // Hesaplaşma/Taksit filtre rozetlerini hafızaya göre tarayıp premium koyu tona boyama
    $('.btn-taksit-filtre').removeClass('btn-dark text-white aktif').addClass('btn-outline-secondary');
    $(`.btn-taksit-filtre[data-taksit="${seciliFiltreTaksitDurumu}"]`).removeClass('btn-outline-secondary').addClass('btn-dark text-white aktif');

    // Kategori filtre rozetlerini hafızaya göre tarayıp aktif duruma getirme
    $('.tag-filtre-rozet').removeClass('bg-dark text-white aktif').addClass('bg-secondary text-dark bg-opacity-10');
    $(`.tag-filtre-rozet[data-id="${seciliFiltreTagId}"]`).removeClass('bg-secondary text-dark bg-opacity-10').addClass('bg-dark text-white aktif');

    // Akıllı firma selectbox ve popüler yatay rozet butonlarının eşzamanlı hafıza kontrolü
    function firmaRozetAyarla(hedefFirma) {
        $('.firma-filtre-rozet').removeClass('bg-dark text-white aktif').addClass('bg-secondary text-dark bg-opacity-10');
        let $rozetBul = $(`.firma-filtre-rozet[data-firma="${hedefFirma}"]`);
        
        if ($rozetBul.length > 0) {
            $('#rozetFirmaDiger').addClass('d-none');
            $rozetBul.removeClass('bg-secondary text-dark bg-opacity-10').addClass('bg-dark text-white aktif');
        } else if (hedefFirma !== "HEPSI" && hedefFirma !== "FIRMASIZLAR") {
            // Eğer seçilen firma popüler rozet limit sınırında yoksa, "DİĞER" rozet kalkanını uyandırıp parlatıyoruz
            $('#digerFirmaEtiketIsmi').text(hedefFirma);
            $('#rozetFirmaDiger').removeClass('d-none bg-secondary text-dark bg-opacity-10').addClass('bg-dark text-white aktif');
            seciliFiltreFirmaAdi = "DIGER";
            localStorage.setItem('mizan_secili_firma_rozet', "DIGER");
        }
    }
    
    if(eskiSelectFirma !== "HEPSI") {
        firmaRozetAyarla(eskiSelectFirma);
    } else {
        firmaRozetAyarla(seciliFiltreFirmaAdi);
    }

    // Tabloyu hafızadan gelen tüm bu kombinasyonlara göre canlı olarak süzüyoruz
    dinamikMizanTablosunuSuz();
});
</script>
<?php require admin_view('static/footer'); ?>






