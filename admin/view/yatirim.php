<?php 
    ## Tags Bilgileri
    $meta = [
        'site_title' => 'Döviz ve Altın Al-Sat Paneli | Bütçe Yönetimi',
        'site_desc' => 'Bütçe Yönetimi',
        'site_keyw' => 'Bütçe Yönetimi'
    ];
    # Tags Bilgileri

    require admin_view("static/header"); 

?>

<style>
    body { background-color: #f8fafc; font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    
    /* Geliştirilmiş Yeni Pastel Ton Kart Tasarımları */
    .pastel-card {
        border: 1px solid rgba(0,0,0,0.03) !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .pastel-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04) !important; }
    
    /* İsteğiniz Doğrultusunda Belirlenen Pastel Arka Plan ve Metin Renk Kombinasyonları */
    .pastel-usd { background-color: #e8f5e9 !important; color: #1b5e20 !important; } /* Mint Yeşili */
    .pastel-eur { background-color: #e3f2fd !important; color: #0d47a1 !important; } /* Açık Safir Mavi */
    .pastel-xau { background-color: #fff8e1 !important; color: #78350f !important; } /* Kehribar Sarı */
    .pastel-report { background-color: #f1f5f9 !important; color: #1e293b !important; } /* Nötr Koyu Gri */

    .card-divider { border-top: 1px solid rgba(0, 0, 0, 0.06); margin: 12px 0; }
    .badge-kur-info { font-size: 11px; font-weight: 700; padding: 5px 8px; border-radius: 6px; background: rgba(0,0,0,0.04); }
    .text-muted-pastel { opacity: 0.75; font-size: 12px; font-weight: 500; }


    /* TABLO KISMI İSTEĞİNİZ: Font boyutunu tek elden doğrudan değiştirebileceğiniz harici özel sınıfınız */
    .fx-custom-font { 
        font-size: 12px !important; /* İstediğiniz boyuta (örn: 13px, 15px) buradan çekebilirsiniz */
        font-weight: 500;
    }
    .fx-custom-font th { font-weight: 700; letter-spacing: 0.3px; }
    
    /* Net Yazı Rengi Vurguları (Arka planlar soft/light kalacak) */
    .text-fx-alis { color: #198754 !important; }  /* Finansal Canlı Yeşil Yazı */
    .text-fx-cikis { color: #dc3545 !important; } /* Finansal Canlı Kırmızı Yazı */
    
    .badge-fx { font-size: 11px !important; padding: 4px 6px; font-weight: 700; text-transform: uppercase; }

    /* --- ZÜMRÜT YEŞİLİ TONLARI --- */

    /* --- ZÜMRÜT YEŞİLİ TONLARI --- */

    /* --- ORANTILI ZÜMRÜT YEŞİLİ TONLARI --- */

    /* 1. Ton: Az Koyu / Açık Zümrüt (Yumuşak, göz yormayan zemin) */
    .table_zumrut1, 
    .table_zumrut1 th, 
    .table_zumrut1 td {
    background-color: #e6f4ea !important; /* Çok hafif, dengeli yeşil zemin */
    color: #0f5132 !important;            /* Okunabilir koyu yeşil metin */
    box-shadow: inset 0 0 0 9999px #e6f4ea !important;
    }

    /* 2. Ton: Orta Zümrüt (Belirgin, canlı ve dengeli) */
    .table_zumrut2, 
    .table_zumrut2 th, 
    .table_zumrut2 td {
    background-color: #c6ddcd !important; /* Net, temiz orta zümrüt yeşili */
    color: #0f5132 !important;            /* Beyaz metin */
    box-shadow: inset 0 0 0 9999px #c6ddcd !important;
    }

    /* 3. Ton: Koyu Zümrüt (Derin, baskın ve net) */
    .table_zumrut3, 
    .table_zumrut3 th, 
    .table_zumrut3 td {
    background-color: #1f744e !important; /* Gerçek derin zümrüt tonu */
    color: #ffffff !important;            /* Beyaz metin */
    box-shadow: inset 0 0 0 9999px #1f744e !important;
    }



    /* --- HATA (ERROR) TONLARI --- */

    /* 1. Ton: Açık Hata Rengi (Soft/Uyarı) */
    .table_error1, 
    .table_error1 th, 
    .table_error1 td {
    background-color: #f8d7da !important;
    color: #842029 !important;
    box-shadow: inset 0 0 0 9999px #f8d7da !important;
    }

    /* 2. Ton: Orta Hata Rengi (Standart Error) */
    .table_error2, 
    .table_error2 th, 
    .table_error2 td {
    background-color: #dc3545 !important;
    color: #ffffff !important;
    box-shadow: inset 0 0 0 9999px #dc3545 !important;
    }

    /* 3. Ton: Koyu Hata Rengi (Kritik Error) */
    .table_error3, 
    .table_error3 th, 
    .table_error3 td {
    background-color: #721c24 !important;
    color: #ffffff !important;
    box-shadow: inset 0 0 0 9999px #721c24 !important;
    }

 

</style>

<div class="container-fluid px-4 py-4">
    <!-- ================= 1. SATIR: ANLIK VERİ ENTEGRELİ PASTEL KARTLAR ================= -->

    <div class="container-fluid px-4 py-4">
        <!-- ================= 1. SATIR: ANLIK VERİ ENTEGRELİ PASTEL KARTLAR (5 SÜTUNLU YAPI) ================= -->
        <div class="row g-4 mb-4 row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-5">

            <!-- SÜTUN 1: BANKA HESAPLARI VE HÜCRESEL BAKİYE KARTI -->
            <div class="col">
                <div class="card h-100 border border-light-subtle shadow-sm p-2 rounded-3" style="background-color: #f3e5f5;">
                    <div class="card-body d-flex flex-column justify-content-between p-3">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-uppercase small fw-bold tracking-wider opacity-90" style="color: #4a148c; font-size: 11px;">Banka Hesapları</span>
                                <span class="fs-4">🇹🇷</span>
                            </div>
                            <h2 class="fw-bold mb-1 font-monospace" style="color: #4a148c; letter-spacing: -0.5px;"><?= number_format($isbank_a_anapara ?? 0, 2, ',', '.') ?> ₺</h2>
                            <div class="d-flex justify-content-between align-items-center mb-3 text-muted" style="font-size: 11px;">
                                <span>İşbankası(A) TL Mevcudu</span>
                                <span class="fw-bold text-uppercase" style="font-size: 9px; color: #4a148c;">Hesap Durumu:</span>
                            </div>
                        </div>

                        <div>
                            <div class="bg-white bg-opacity-60 rounded-3 p-3 mb-3 border border-white border-opacity-70 shadow-none" style="font-size: 11px;">
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 border-secondary border-opacity-10">
                                    <span class="text-muted">İşbank (A) EUR:</span>
                                    <span class="fw-bold font-monospace">€ <?= number_format($isbank_a_eu_bakiye ?? 0, 2, ',', '.') ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 border-secondary border-opacity-10">
                                    <span class="text-muted">İşbank (Ö) EUR:</span>
                                    <span class="fw-bold font-monospace" style="color: #6a1b9a;">€ <?= number_format($isbank_o_eu_bakiye ?? 0, 2, ',', '.') ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 border-secondary border-opacity-10">
                                    <span class="text-muted">Nakit EUR Bakiye:</span>
                                    <span class="fw-bold font-monospace" style="color: #6a1b9a;">€ <?= number_format($nakit_eu_bakiye ?? 0, 2, ',', '.') ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 border-secondary border-opacity-10">
                                    <span class="text-muted">İşbank (A) USD:</span>
                                    <span class="fw-bold font-monospace">$ <?= number_format($isbank_a_usd_bakiye ?? 0, 2, ',', '.') ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">İşbank (A) XAU:</span>
                                    <span class="fw-bold font-monospace"><?= number_format($isbank_a_xau_bakiye ?? 0, 2, ',', '.') ?> GR</span>
                                </div>
                            </div>
                            
                            <div class="card-divider my-2" style="border-top-color: rgba(74, 20, 140, 0.1) !important;"></div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3 text-muted" style="font-size: 11px;">
                                <span class="opacity-75">Nakit Akış Kulvarı:</span>
                                <span class="fw-bold text-dark">Forex & Bütçe</span>
                            </div>
                            
                            <button type="button" class="btn btn-dark w-100 btn-lg py-2.5 fw-bold text-uppercase d-flex align-items-center justify-content-center border-0 shadow-none" onclick="forexAction('isbank_a', 'EUR')" style="font-size: 11px; letter-spacing: 0.5px; background-color: #4a148c; color: #ffffff; border-radius: 6px;">
                                <i class="fas fa-wallet me-2 text-warning"></i> İşlem Terminali
                            </button>

                        </div>
                    </div>
                </div>
            </div>

<!-- SÜTUN 2: DÖVİZ KARTLARI (AKILLI BORÇ SENSÖRLÜ & POP-UP DESTEKLİ) -->
<?php foreach (['EUR', 'USD', 'XAU'] as $kod): ?>
    <?php 
    $p = $pariteler[$kod]; 
    
    // 1. 'kredi_kartlari_last' tablosundaki kümülatif toplam döviz borçlarını hesapla
    $guncel_kart_doviz_borcu = 0.00;
    if ($kod === 'USD') {
        $guncel_kart_doviz_borcu = floatval($kkart['isbank']['usd_borcu'] ?? 0) + floatval($kkart['vakifbank']['usd_borcu'] ?? 0);
    } elseif ($kod === 'EUR') {
        $guncel_kart_doviz_borcu = floatval($kkart['isbank']['eu_borcu'] ?? 0) + floatval($kkart['vakifbank']['eu_borcu'] ?? 0);
    }
    ?>
    <div class="col">
        <div class="card h-100 pastel-card <?= $p['renk'] ?> p-2 shadow-sm border border-light-subtle rounded-3">
            <div class="card-body d-flex flex-column justify-content-between p-3">
                
                <!-- 1. Üst Şerit Başlığı -->
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3 text-uppercase">
                        <span class="small fw-bold tracking-wider opacity-90" style="font-size: 11px;"><?= mb_strtoupper($p['baslik'], 'UTF-8') ?></span>
                        <span class="fs-4"><?= $p['bayrak'] ?></span>
                    </div>
                    <!-- Birim Simgeli Konsolide Canlı Bakiye -->
                    <h2 class="fw-bold mb-1 font-monospace text-uppercase" style="letter-spacing: -0.5px;">
                        <?= $kod !== 'XAU' ? $p['simge'] . ' ' : '' ?><?= number_format($p['is_bakiye'], 2, ',', '.') ?><?= $kod === 'XAU' ? ' GR' : '' ?>
                    </h2>
                    
                    <!-- 2. Orta Maliyet Detay İstasyonu -->
                    <div class="d-flex flex-column gap-2 mb-3 bg-white bg-opacity-25 p-2.5 rounded-3 border border-white border-opacity-40 text-uppercase" style="font-size: 11px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary opacity-75">ORT. ALIŞ MALİYETİ:</span>
                            <span class="fw-bold text-dark font-monospace"><?= number_format($p['ort_maliyet'], 2, ',', '.') ?> ₺</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-1.5 border-secondary border-opacity-10">
                            <span class="text-secondary opacity-75"><?= $aktif_yil ?> YILI ALIMLAR:</span>
                            <span class="fw-bold text-dark font-monospace"><?= number_format($p['donem_alis'], 2, ',', '.') ?> ₺</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary opacity-75">GÜNCEL (ALIŞ-ÖDEME+DEVİR):</span>
                            <span class="fw-bold text-primary font-monospace"><?= number_format($p['guncel_veri'], 2, ',', '.') ?> ₺</span>
                        </div>
                        
                        <!-- 🎯 REFORM: Dinamik isimlendirmeli, kurumsal ve soft-kırmızı şerit badge tasarımı -->
                        <?php if ($guncel_kart_doviz_borcu > 0): ?>
                            <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-1 border-danger border-opacity-10">
                                <span class="badge bg-danger bg-opacity-10 text-danger fw-bold border border-danger border-opacity-20 px-2 py-1" style="font-size: 9.5px; letter-spacing: 0.3px;">
                                    <?= $kod === 'USD' ? 'KART DOLAR BORCU' : 'KART EURO BORCU' ?>
                                </span>
                                <span class="fw-bold text-danger font-monospace" style="font-size: 12px;"><?= number_format($guncel_kart_doviz_borcu, 2, ',', '.') ?> <?= $p['simge'] ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 3. Alt Kur Gösterge ve Değerleme Terminali -->
                <div>
                    <div class="bg-white bg-opacity-60 rounded-3 p-3 mb-3 border border-white border-opacity-70 fx-table-text text-dark text-uppercase" style="font-size: 11px;">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 border-secondary border-opacity-10">
                            <span class="text-muted">BANKA ALIŞ (BOZMA):</span>
                            <span class="fw-bold font-monospace"><?= number_format($live_kurlar[$p['kur_alis']], $p['kur_basamak'], ',', '.') ?> ₺</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">BANKA SATIŞ (ALIM):</span>
                            <span class="fw-bold font-monospace"><?= number_format($live_kurlar[$p['kur_satis']], $p['kur_basamak'], ',', '.') ?> ₺</span>
                        </div>
                    </div>
                    
                    <div class="card-divider my-2"></div>
                    
                    <div class="d-flex justify-content-between align-items-center small mb-1.5 text-muted text-uppercase" style="font-size: 11px;">
                        <span>ANLIK BANKA EDERİ:</span>
                        <span class="fw-bold text-dark font-monospace"><?= number_format($p['anlik_eder'], 2, ',', '.') ?> ₺</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small mb-3 text-muted text-uppercase" style="font-size: 11px;">
                        <span>ANLIK KÂR / ZARAR:</span>
                        <span class="fw-bold px-2 py-0.5 rounded bg-white font-monospace" style="color: <?= $p['fark_tl'] >= 0 ? $p['fark_renk'] : '#b71c1c' ?>; border: 1px solid rgba(0,0,0,0.05);">
                            <?= $p['fark_isaret'] . number_format($p['fark_tl'], 2, ',', '.') ?> ₺
                        </span>
                    </div>

                    <!-- Akıllı Buton Terminali (Pop-up Pencere Tetiği) -->
                    <?php if ($guncel_kart_doviz_borcu > 0): ?>
                        <button type="button" class="btn btn-danger w-100 btn-lg py-2.5 fw-bold text-uppercase d-flex align-items-center justify-content-center shadow-none border-0 text-white" 
                                onclick="window.open('currency_debt', 'Döviz Borç Ödeme', 'width=1100,height=650,scrollbars=yes,resizable=yes')" 
                                style="font-size: 11px; letter-spacing: 0.5px; border-radius: 6px; background-color: #c92a2a !important;">
                            <i class="fas fa-exclamation-triangle me-2 text-warning"></i> <?= number_format($guncel_kart_doviz_borcu, 2, ',', '.') ?> <?= $p['simge'] ?> BORCU ÖDE
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-dark w-100 btn-lg py-2.5 fw-bold text-uppercase d-flex align-items-center justify-content-center shadow-none border-0" onclick="forexAction('isbank_k', '<?= $p['btn_key'] ?>')" style="font-size: 11px; letter-spacing: 0.5px; border-radius: 6px;">
                            <i class="fas fa-shopping-cart me-2 text-warning"></i> <?= mb_strtoupper($p['baslik'], 'UTF-8') ?> SATIN AL
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- =========================================================================
     SÜTUN 3: KONSOLİDE PORTFÖY ÖZETİ (TABLO FORMATLI PREMIUM SÜRÜM)
     ========================================================================= -->
<?php 
// 1. PHP Katmanında Finansal Formatların Hazırlanması
$fmt_toplam_cikis = number_format($konsolide_toplam_cikis_tl, 2, ',', '.');
$fmt_toplam_giris = number_format($konsolide_toplam_giris_tl, 2, ',', '.');
$fmt_cari_donem   = number_format($konsolide_cari_donem_tl, 2, ',', '.');
$fmt_devreden     = number_format($konsolide_devreden_bakiye_tl, 2, ',', '.');
$fmt_genel_durum  = number_format($konsolide_genel_durum_tl, 2, ',', '.');
$fmt_genel_tasfiye= number_format($konsolide_genel_durum_tasfiye_tl, 2, ',', '.'); // 🎯 Yeni eklenen mevcudun tasfiye ederi

$fmt_tasfiye_eder = number_format($anlik_banka_degeri_tl, 2, ',', '.');
$fmt_alis_maliyet = number_format($toplam_alis_maliyeti_tl, 2, ',', '.');
$fmt_arbitraj     = $fark_isaret . number_format($arbitraj_fark_tl, 2, ',', '.');

// 2. Portföy Durum Rozet Ayarları
$rozet_css_sinifi = ($arbitraj_fark_tl >= 0) ? 'bg-success text-success bg-opacity-10' : 'bg-danger text-danger bg-opacity-10';
$rozet_durum_text = ($arbitraj_fark_tl >= 0) ? 'Kârda' : 'Zararda';
?>

<!-- =========================================================================
     NİHAİ REFORM: 3 SÜTUN KİLİTLİ, PY-2 KOMPAKT KONSOLİDE TABLO
     ========================================================================= -->
<div class="col">
    <div class="card h-100 shadow-lg border-0 rounded-3 overflow-hidden">
        
        <!-- Üst Başlık Şeridi -->
        <div class="card-header bg-dark py-3 d-flex justify-content-between align-items-center border-0">
            <h6 class="fw-bold mb-0 text-white d-inline-flex align-items-center m-0" style="font-size: 12px; letter-spacing: 0.5px;">
                <i class="fas fa-chart-pie me-2 text-warning fs-6"></i> KONSOLİDE PORTFÖY ÖZETİ
            </h6>
            <span class="badge bg-secondary fw-bold font-monospace px-2.5 py-1 text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Kümülatif Mizan</span>
        </div>
        
        <div class="card-body p-0 bg-white">
            <div class="table-responsive m-0 border-0">
                <!-- 🎯 CSS GÜVENCESİ: table-layout: fixed ve white-space: nowrap ile dikey yığılmalar tamamen engellendi -->
                <table class="table table-sm table-bordered align-middle text-end mb-0 font-monospace" style="font-size: 11px; table-layout: fixed; width: 100%;">
                    
                    <!-- SÜTUN GENİŞLİKLERİNİ MİLİMETRİK KİLİTLEYEN ŞABLON -->
                    <colgroup>
                        <col style="width: 45%;"> <!-- Başlık Sütunu -->
                        <col style="width: 25%;"> <!-- Orta Bilgi / Tasfiye Sütunu -->
                        <col style="width: 30%;"> <!-- Sağ Nihai Tutar Sütunu -->
                    </colgroup>

                    <tbody class="text-dark small text-uppercase">

                        <!-- 5. SATIR: MEVCUT PORTFÖY (ELDEKİ MEVCUT) -->
                        <tr class="fw-bold table-warning text-warning-emphasis border-bottom">
                            <td class="text-start ps-3 py-2" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">MEVCUT PORTFÖY</td>
                            <td class="text-center font-monospace py-2 fw-bold">-</td>
                            <td class="text-end pe-3 font-monospace fw-bold py-2" style="white-space: nowrap;">+ <?= $fmt_genel_durum ?> ₺</td>
                        </tr>
                        
                        <!-- 1. SATIR: TOPLAM PORTFÖY (ÇIKIŞ) -->
                        <tr class="fw-bold table-danger text-danger-emphasis border-bottom">
                            <td class="text-start ps-3 py-2" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">TOPLAM PORTFÖY (ÇIKIŞ)</td>
                            <td class="text-center text-muted py-2">-</td>
                            <td class="text-end pe-3 fw-bold py-2" style="white-space: nowrap;">- <?= $fmt_toplam_cikis ?> ₺</td>
                        </tr>

                        <!-- 2. SATIR: TOPLAM PORTFÖY (GİRİŞ) -->
                        <tr class="fw-bold table-success text-success-emphasis border-bottom">
                            <td class="text-start ps-3 py-2" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">TOPLAM PORTFÖY (GİRİŞ)</td>
                            <td class="text-center text-muted py-2">-</td>
                            <td class="text-end pe-3 fw-bold py-2" style="white-space: nowrap;">+ <?= $fmt_toplam_giris ?> ₺</td>
                        </tr>

                        <!-- 6. SATIR: DEĞERLEME GRUBU BAŞLIĞI -->
                        <tr class="table_zumrut3 text-white border-bottom text-center">
                            <td colspan="3" class="py-2 text-uppercase tracking-wider small fw-bold" style="letter-spacing: 0.5px; font-size: 10px;">
                                PORTFÖY GİRİŞ KALEMLERİ
                            </td>
                        </tr>

                        <!-- 3. SATIR: CARİ DÖNEM (GİRİŞ) -->
                        <tr class="fw-bold table_zumrut1 text-primary-emphasis border-bottom">
                            <td class="text-start ps-3 py-2" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">CARİ DÖNEM (GİRİŞ)</td>
                            <td class="text-center text-muted py-2">-</td>
                            <td class="text-end pe-3 fw-bold py-2" style="white-space: nowrap;">+ <?= $fmt_cari_donem ?> ₺</td>
                        </tr>

                        <!-- 4. SATIR: DEVREDEN BAKİYE -->
                        <tr class="fw-bold table_zumrut2 text-secondary-emphasis border-bottom">
                            <td class="text-start ps-3 py-2" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">DEVREDEN BAKİYE</td>
                            <td class="text-center text-muted py-2">-</td>
                            <td class="text-end pe-3 fw-bold py-2" style="white-space: nowrap;">+ <?= $fmt_devreden ?> ₺</td>
                        </tr>


                        <!-- 6. SATIR: DEĞERLEME GRUBU BAŞLIĞI -->
                        <tr class="table-dark text-white border-bottom text-center">
                            <td colspan="3" class="py-2 text-uppercase tracking-wider small fw-bold" style="letter-spacing: 0.5px; font-size: 10px;">
                                AKTİF DEĞERLEME VE ARBİTRAJ DENGESİ
                            </td>
                        </tr>

                        <!-- 7. SATIR: TOPLAM TASFİYE EDERİ -->
                        <tr class="fw-semibold bg-white border-bottom" style="color: #444;">
                            <td class="text-start ps-3 py-2" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">TOPLAM TASFİYE EDERİ</td>
                            <td class="text-center text-muted py-2">-</td>
                            <td class="text-end pe-3 fw-bold text-dark py-2" style="white-space: nowrap;"><?= $fmt_tasfiye_eder ?> ₺</td>
                        </tr>

                        <!-- 8. SATIR: TOPLAM ALIŞ MALİYETİ -->
                        <tr class="fw-semibold bg-white border-bottom" style="color: #444;">
                            <td class="text-start ps-3 py-2" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">TOPLAM ALIŞ MALİYETİ</td>
                            <td class="text-center text-muted py-2" style="font-size: 9px; opacity: 0.85;">(VARLIK x ORT. MAL)</td>
                            <td class="text-end pe-3 fw-bold text-dark py-2" style="white-space: nowrap;"><?= $fmt_alis_maliyet ?> ₺</td>
                        </tr>

                        <!-- 9. SATIR: NET ARBİTRAJ DENGESİ -->
                        <tr class="fw-bold bg-white border-bottom-0">
                            <td class="text-start ps-3 py-2 text-dark" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">NET ARBİTRAJ DENGESİ (KÂR/ZARAR)</td>
                            <td class="text-center py-2">
                                <span class="badge rounded-pill px-2.5 py-1 text-uppercase fw-bold <?= $rozet_css_sinifi ?>" style="font-size: 9px; letter-spacing: 0.3px;">
                                    <?= $rozet_durum_text ?>
                                </span>
                            </td>
                            <td class="text-end pe-3 fw-bold <?= $fark_sinifi ?> py-2" style="white-space: nowrap; font-size: 12px;"><?= $fmt_arbitraj ?> ₺</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>






        </div> <!-- 1. Satır Sonu (row sonu) -->
    </div> <!-- .container-fluid sonu -->



    <!-- ================= 2. SATIR: FORM VE TABLOLAR ================= -->
    <div class="container-fluid px-4 py-4">

        <div class="row g-4 mb-4">
            

            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-lg h-100 rounded-3">
                    
                    <!-- Üst Başlık (Mevduat Transfer Ekranı ile Uyumlu) -->
                    <div class="card-header bg-dark text-white p-3 border-0 d-flex align-items-center justify-content-between">
                        <h6 class="card-title m-0 fw-bold d-inline-flex align-items-center">
                            <i class="fas fa-exchange-alt me-2 text-warning"></i> Döviz & Varlık İşlem Terminali
                        </h6>
                        <span class="badge bg-primary fw-bold font-monospace px-2 py-1">Hücresel Mimari</span>
                    </div>
                    
                    <form action="" method="POST" id="forexIslemForm" class="m-0">
                        <!-- <input type="hidden" name="action" value="interaktif_forex_panel_islem"> -->
                        <input type="hidden" name="islem" value="interaktif_forex_panel_islem">
                        
                        <div class="card-body p-4 text-dark bg-light">
                            
                            <?php if (isset($hata_mesaji)): ?>
                                <div class="alert alert-danger fw-semibold py-2 px-3 border-0 mb-3 rounded-2">
                                    ⚠️ <?= htmlspecialchars($hata_mesaji) ?>
                                </div>
                            <?php endif; ?>

                            <!-- 1. İŞLEM YÖNÜ SEÇİMİ -->
                            <div class="mb-4 text-start">
                                <label class="form-label fw-bold text-secondary small text-uppercase mb-2 tracking-wider">İşlem Yönü</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="panel_islem_yonu" id="panel_islem_alis" value="ALIS" checked autocomplete="off">
                                    <label class="btn btn-outline-success btn-lg fw-bold text-uppercase py-3 d-flex align-items-center justify-content-center shadow-none" for="panel_islem_alis">VARLIK AL</label>
                                    
                                    <input type="radio" class="btn-check" name="panel_islem_yonu" id="panel_islem_satis" value="SATIS" autocomplete="off">
                                    <label class="btn btn-outline-danger btn-lg fw-bold text-uppercase py-3 d-flex align-items-center justify-content-center shadow-none" for="panel_islem_satis">VARLIK SAT</label>
                                </div>
                            </div>
                            
                            <!-- 2. ÇEKİLECEK VE AKTARILACAK HESAP SELECT BOX'LARI -->
                            <div class="row g-3 mb-4">
                                <!-- Sol Seçici: Çekilecek Hücre -->
                                <div class="col-6 text-start">
                                    <label id="lbl_cekilecek_hesap" for="panel_cekilecek_hesap" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">Paranın Çekileceği Hesap</label>
                                    <select class="form-select form-select-lg fw-semibold text-dark p-3" id="panel_cekilecek_hesap" name="panel_cekilecek_hesap" required>
                                        <option value="" disabled selected>Hesap Seçiniz...</option>
                                        <?php if (isset($finansal_havuzlar) && is_array($finansal_havuzlar)): ?>
                                            <?php foreach($finansal_havuzlar as $havuz): ?>
                                                <?php if (in_array($havuz['havuz_kodu'], ['isbank_a', 'isbank_o'])): ?>
                                                    <option value="<?= (int)$havuz['id'] ?>_anapara_TL" 
                                                            data-tl="<?= (float)$havuz['anapara'] ?>"
                                                            data-usd="<?= (float)$havuz['usd_bakiye'] ?>"
                                                            data-eur="<?= (float)$havuz['eu_bakiye'] ?>"
                                                            data-xau="<?= (float)$havuz['xau_bakiye'] ?>"
                                                            data-bakiye="<?= (float)$havuz['anapara'] ?>">
                                                        <?= htmlspecialchars($havuz['havuz_adi']) ?> - Anapara (TL)
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <small id="txt_cekilecek_bakiye" class="text-primary font-monospace fw-bold d-block mt-1">Bakiye: --</small>
                                </div>

                                <!-- Sağ Seçici: Aktarılacak Hücre (JS Dinamik Yönetir) -->
                                <div class="col-6 text-start">
                                    <label id="lbl_aktarilacak_hesap" for="panel_aktarilacak_hesap" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">Paranın Aktarılacağı Hesap</label>
                                    <select class="form-select form-select-lg fw-semibold text-dark p-3" id="panel_aktarilacak_hesap" name="panel_aktarilacak_hesap" required>
                                        <option value="" disabled selected>Hesap Seçiniz...</option>
                                    </select>
                                    <small id="txt_aktarilacak_bakiye" class="text-success font-monospace fw-bold d-block mt-1">Bakiye: --</small>
                                </div>
                            </div>

                            <!-- 3. GÖSTERGE KARTLARI -->
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="card bg-white border border-light-subtle rounded-2 p-3 text-center shadow-sm">
                                        <span id="panel_kur_turu_label" class="d-block text-secondary small fw-bold text-uppercase mb-1 tracking-wider">Canlı Banka Alış</span>
                                        <h5 id="panel_canli_kur_text" class="fw-bold text-dark font-monospace m-0">0,0000 ₺</h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-white border border-light-subtle rounded-2 p-3 text-center shadow-sm">
                                        <span id="panel_maks_kapasite_label" class="d-block text-secondary small fw-bold text-uppercase mb-1 tracking-wider">Maks. Alınabilir</span>
                                        <h5 id="panel_maks_kapasite_text" class="fw-bold text-success font-monospace m-0">0,00</h5>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. MANUEL KURU GİRİŞİ -->
                            <div class="mb-4 text-start">
                                <label for="panel_manuel_kur" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">
                                    <i class="fas fa-edit me-1 text-muted"></i> Manuel İşlem Kuru (Harici Giriş)
                                </label>
                                <input type="number" class="form-control form-control-lg font-monospace fw-bold text-end border p-3" name="panel_manuel_kur" id="panel_manuel_kur" step="0.0001" min="0" placeholder="0.0000 (Canlı kuru ezmek için yazın)">
                            </div>

                            <!-- 5. YAN YANA GİRİŞ ALANLARI -->
                            <div class="row g-3 mb-4">
                                <!-- Giriş: TL Tutar -->
                                <div class="col-6 text-start">
                                    <label for="panel_tutar_tl" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">
                                        <i class="fas fa-lira-sign me-1 text-muted"></i> İşlem Tutarı (TL)
                                    </label>
                                    <!-- Input alanı standart genişliğinde -->
                                    <input type="number" class="form-control form-control-lg font-monospace fw-bold text-end border p-3 text-primary shadow-none rounded-2" name="panel_tutar_tl" id="panel_tutar_tl" step="0.01" min="0" placeholder="0.00">
                                    <!-- Sağa tam yaslanmış akıllı metin şeridi -->
                                    <div class="d-flex justify-content-end mt-1">
                                        <span class="text-decoration-underline fw-bold text-dark small font-monospace user-select-none" id="btn_max_tl" style="cursor: pointer; font-size: 11px;">Bakiyenin Tümü</span>
                                    </div>
                                </div>
                                
                                <!-- Giriş: Varlık Miktar -->
                                <div class="col-6 text-start">
                                    <label id="lbl_islem_miktari" for="panel_miktar" class="form-label fw-bold text-secondary small text-uppercase mb-1 tracking-wider">
                                        <i class="fas fa-coins me-1 text-muted"></i> İşlem Miktarı
                                    </label>
                                    <!-- Input alanı standart genişliğinde -->
                                    <input type="number" class="form-control form-control-lg font-monospace fw-bold text-end border p-3 text-primary shadow-none rounded-2" name="panel_miktar" id="panel_miktar" step="0.0001" min="0" placeholder="0.0000">
                                    <!-- Sağa tam yaslanmış akıllı metin şeridi -->
                                    <div class="d-flex justify-content-end mt-1">
                                        <span class="text-decoration-underline fw-bold text-dark small font-monospace user-select-none" id="btn_max_miktar" style="cursor: pointer; font-size: 11px;">Bakiyenin Tümü</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 6. NİHAİ MATRAH ÇIKTISI VE VERGİ ŞERİDİ -->
                            <div class="mb-2 text-start">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold text-secondary small text-uppercase mb-0 tracking-wider">Tutar (Vergi Dahil)</label>
                                    <div class="d-flex gap-1 align-items-center">
                                        <!-- 🎯 YENİ ROZET: Vergisiz Net Alım/Satım Matrahını Gösteren Şerit -->
                                        <span id="panel_net_tutar_bilgi_rozet" class="badge text-bg-secondary fw-bold font-monospace px-2 py-1">Net Tutar: 0,00 ₺</span>
                                        <span id="panel_vergi_bilgi_rozet" class="badge text-bg-warning fw-bold font-monospace px-2 py-1">Vergi: 0,00 ₺</span>
                                    </div>
                                </div>
                                <div class="input-group input-group-lg">
                                    <input type="text" class="form-control font-monospace fw-bold bg-white text-end border p-3 text-dark shadow-none" id="panel_nihai_sonuc" readonly value="0,00 ₺">
                                </div>
                                <!-- Bootstrap Standart Tehlike Şeridi -->
                                <div id="panel_bakiye_uyari_metni" class="alert alert-danger d-flex align-items-center p-3 mt-3 mb-0 border-0 fw-semibold rounded-2 d-none">
                                    <i class="fas fa-times-circle me-2 fs-5"></i> Havuz Ana Para Bakiyesi Yetersiz! Lütfen girdiğiniz tutarı veya miktarı düşürün.
                                </div>
                            </div>

                                            
                        </div> <!-- .card-body sonu -->
                        
                        <!-- Kart Footer Aksiyonları (Tamamen Bootstrap 5 Standardında Büyük Boy Butonlar) -->
                        <div class="card-footer bg-light border-top p-3 d-flex align-items-center justify-content-end">
                            <button type="button" id="btn_panel_temizle" class="btn btn-secondary btn-lg px-4 fw-bold text-uppercase d-inline-flex align-items-center justify-content-center me-2">
                                <i class="fas fa-undo me-2"></i> Temizle
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg px-4 fw-bold text-uppercase d-inline-flex align-items-center justify-content-center text-white" id="btn_panel_onayla" disabled>
                                <i class="fas fa-check-circle me-2"></i> DÖVİZ ALIŞ İŞLEMİNİ TAMAMLA
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <?php foreach ($pariteler as $kod => $p): ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-0 premium-card-table h-100">
                        
                        <!-- Kart Başlığı ve Yıl İçi Alış Maliyeti -->
                        <div class="card-header bg-dark py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-white"><?= $p['baslik'] ?></h5>
                            <span class="badge bg-primary text-white p-2">
                            </span>
                        </div>
                        
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped align-middle text-end mb-0 fx-custom-font">
            
  
                                    <thead class="small text-uppercase">

                                        <!-- 1. SATIR: MEVCUT PORTFÖY (Sarı Satır - Kahverengi/Sarı Yazı) -->
                                        <tr class="fw-bold table-warning border-bottom">
                                            <td class="text-start ps-3 font-monospace text-warning-emphasis py-2">
                                                <i class="fas fa-wallet me-1"></i> MEVCUT PORTFÖY
                                            </td>
                                            <td class="text-center text-warning-emphasis py-2">-</td>
                                            <td class="text-end font-monospace text-warning-emphasis py-2">
                                                <?= number_format($p['genel_net_pozisyon']['miktar'] ?? 0, (strtolower($kod)==='xau'?4:2), ',', '.') ?> <?= $p['simge'] ?>
                                            </td>
                                            <td class="text-center text-warning-emphasis py-2">-</td>
                                            <td class="text-center text-warning-emphasis py-2">-</td>
                                            <td class="text-end font-monospace text-warning-emphasis py-2"><?= number_format($p['genel_net_pozisyon']['tutar'] ?? 0, 2, ',', '.') ?> ₺</td>
                                            <td class="text-end font-monospace text-warning-emphasis py-2"><?= number_format($p['genel_net_pozisyon']['vergi'] ?? 0, 2, ',', '.') ?> ₺</td>
                                            <td class="text-end pe-3 font-monospace fw-bold text-warning-emphasis py-2"><?= number_format($p['genel_net_pozisyon']['maliyet'] ?? 0, 2, ',', '.') ?> ₺</td>
                                        </tr>
                                        
                                        <!-- 2. SATIR: TOPLAM PORTFÖY ÇIKIŞLARI (Kırmızı Satır - Kırmızı Yazı) -->
                                        <tr class="fw-bold table-danger border-bottom">
                                            <td class="text-start ps-3 font-monospace text-danger-emphasis py-2">
                                                <i class="fas fa-arrow-circle-up me-1"></i> TOPLAM PORTFÖY (ÇIKIŞ)
                                            </td>
                                            <td class="text-center text-danger-emphasis py-2">-</td>
                                            <td class="text-end font-monospace text-danger-emphasis py-2">
                                                - <?= number_format($p['toplam_portfoy_cikis']['miktar'] ?? 0, (strtolower($kod)==='xau'?4:2), ',', '.') ?> <?= $p['simge'] ?>
                                            </td>
                                            <td class="text-center text-danger-emphasis py-2">-</td>
                                            <td class="text-center text-danger-emphasis py-2">-</td>
                                            <td class="text-end font-monospace text-danger-emphasis py-2">- <?= number_format($p['toplam_portfoy_cikis']['tutar'] ?? 0, 2, ',', '.') ?> ₺</td>
                                            <td class="text-end font-monospace text-danger-emphasis py-2">0,00 ₺</td>
                                            <td class="text-end pe-3 font-monospace fw-bold text-danger-emphasis py-2">- <?= number_format($p['toplam_portfoy_cikis']['maliyet'] ?? 0, 2, ',', '.') ?> ₺</td>
                                        </tr>

                                        <!-- 3. SATIR: TOPLAM PORTFÖY GİRİŞLERİ (Yeşil Satır - Yeşil Yazı) -->
                                        <tr class="fw-bold table-success border-bottom">
                                            <td class="text-start ps-3 font-monospace text-success-emphasis py-2">
                                                <i class="fas fa-arrow-circle-down me-1"></i> TOPLAM PORTFÖY (GİRİŞ)
                                            </td>
                                            <td class="text-center text-success-emphasis py-2">-</td>
                                            <td class="text-end font-monospace text-success-emphasis py-2">
                                                + <?= number_format($p['toplam_portfoy_giris']['miktar'] ?? 0, (strtolower($kod)==='xau'?4:2), ',', '.') ?> <?= $p['simge'] ?>
                                            </td>
                                            <td class="text-center text-success-emphasis py-2">-</td>
                                            <td class="text-center text-success-emphasis py-2">-</td>
                                            <td class="text-end font-monospace text-success-emphasis py-2">+ <?= number_format($p['toplam_portfoy_giris']['tutar'] ?? 0, 2, ',', '.') ?> ₺</td>
                                            <td class="text-end font-monospace text-success-emphasis py-2"><?= number_format($p['toplam_portfoy_giris']['vergi'] ?? 0, 2, ',', '.') ?> ₺</td>
                                            <td class="text-end pe-3 font-monospace fw-bold text-success-emphasis py-2">+ <?= number_format($p['toplam_portfoy_giris']['maliyet'] ?? 0, 2, ',', '.') ?> ₺</td>
                                        </tr>
                                        <tr class="fw-bold table_zumrut3 border-bottom">
                                        <td class="text-center text-success-emphasis py-2">PORTFÖY GİRİŞ KALEMLERİ</td>
                                        </tr>
                                        <!-- 4. SATIR: CARİ DÖNEM GİRİŞLERİ (Mavi Satır - Mavi Yazı) -->
                                        <tr class="fw-bold table_zumrut1 border-bottom">
                                            <td class="text-start ps-3 font-monospace text-primary-emphasis py-2">
                                                <i class="fas fa-calendar-check me-1"></i> CARİ DÖNEM (GİRİŞ)
                                            </td>
                                            <td class="text-center text-primary-emphasis py-2">-</td>
                                            <td class="text-end font-monospace text-primary-emphasis py-2">
                                                + <?= number_format($p['cari_donem_giris']['miktar'] ?? 0, (strtolower($kod)==='xau'?4:2), ',', '.') ?> <?= $p['simge'] ?>
                                            </td>
                                            <td class="text-center text-primary-emphasis py-2">-</td>
                                            <td class="text-center text-primary-emphasis py-2">-</td>
                                            <td class="text-end font-monospace text-primary-emphasis py-2">+ <?= number_format($p['cari_donem_giris']['tutar'] ?? 0, 2, ',', '.') ?> ₺</td>
                                            <td class="text-end font-monospace text-primary-emphasis py-2"><?= number_format($p['cari_donem_giris']['vergi'] ?? 0, 2, ',', '.') ?> ₺</td>
                                            <td class="text-end pe-3 font-monospace fw-bold text-primary-emphasis py-2">+ <?= number_format($p['cari_donem_giris']['maliyet'] ?? 0, 2, ',', '.') ?> ₺</td>
                                        </tr>

                                        <!-- 5. SATIR: DEVREDEN BAKİYE (Gri Satır - Gri Yazı) -->
                                        <tr class="fw-bold table_zumrut2 border-bottom">
                                            <td class="text-start ps-3 font-monospace text-secondary-emphasis py-2">
                                                <i class="fas fa-history me-1"></i> DEVREDEN BAKİYE
                                            </td>
                                            <td class="text-center text-secondary-emphasis py-2">-</td>
                                            <td class="text-end font-monospace text-secondary-emphasis py-2">
                                                + <?= number_format($p['tarihsel_devir_pozisyon']['miktar'] ?? 0, (strtolower($kod)==='xau'?4:2), ',', '.') ?> <?= $p['simge'] ?>
                                            </td>
                                            <td class="text-center text-secondary-emphasis py-2">-</td>
                                            <td class="text-center text-secondary-emphasis py-2">-</td>
                                            <td class="text-end font-monospace text-secondary-emphasis py-2">+ <?= number_format($p['tarihsel_devir_pozisyon']['tutar'] ?? 0, 2, ',', '.') ?> ₺</td>
                                            <td class="text-end font-monospace text-secondary-emphasis py-2">0,00 ₺</td>
                                            <td class="text-end pe-3 font-monospace fw-bold text-secondary-emphasis py-2">+ <?= number_format($p['tarihsel_devir_pozisyon']['maliyet'] ?? 0, 2, ',', '.') ?> ₺</td>
                                        </tr>


                                        <!-- 6. SATIR: KRONOLOJİK HAREKET BAŞLIKLARI (Koyu Tema) -->
                                        <tr class="fw-bold table-dark text-white border-bottom">
                                            <td class="text-start ps-3 font-monospace text-white py-2">Tarih</td>
                                            <td class="text-center text-white py-2">İşlem Türü</td>
                                            <td class="text-end font-monospace text-white py-2">Miktar</td>
                                            <td class="text-center text-white py-2">Bakiye</td>
                                            <td class="text-center text-white py-2">Kur</td>
                                            <td class="text-end font-monospace text-white py-2">Tutar</td>
                                            <td class="text-end font-monospace text-white py-2">Vergi</td>
                                            <td class="text-end pe-3 font-monospace fw-bold text-white py-2">Maliyet</td>
                                        </tr>

                                    </thead>
                                    
                                    <tbody>
                                    <?php if (!empty($p['islemler'])): ?>
                                        <?php foreach ($p['islemler'] as $islem): 
                                            $islem_turu_ham = $islem['islem_turu'] ?? 2;

                                            // KURAL: Alışlar pozitif (+), Ödemeler ise negatif (-) işaret alır ve yazı sınıfları belirlenir
                                            if ($islem_turu_ham == 2) {
                                                $yazi_sinifi   = 'text-fx-alis';
                                                $badge_sinifi  = 'border-success text-success';
                                                $tur_metni     = 'Alış';
                                                $isaret        = '+';
                                            } elseif ($islem_turu_ham == 1) {
                                                $yazi_sinifi   = 'text-fx-cikis';
                                                $badge_sinifi  = 'border-danger text-danger';
                                                $tur_metni     = 'Ödeme';
                                                $isaret        = '-';
                                            } elseif ($islem_turu_ham == 0) {
                                                $yazi_sinifi   = 'text-fx-cikis';
                                                $badge_sinifi  = 'border-warning text-warning';
                                                $tur_metni     = 'Satış';
                                                $isaret        = '-';
                                            } else {
                                                $yazi_sinifi   = 'text-fx-cikis';
                                                $badge_sinifi  = 'border-info text-info';
                                                $tur_metni     = 'N.Harcama';
                                                $isaret        = '-';
                                            }

                                            // Controller'da kurallı biçimde hesaplanan yürüyen bakiye
                                            $guncel_bakiye = (float)($islem['anlik_bakiye'] ?? 0);
                                            
                                            // 1 Ocak Devir Satırı Kontrolü (Bootstrap Standartlarında .table-secondary)
                                            $is_devir      = (date('m-d', strtotime($islem['islem_tarihi'])) === '01-01');
                                            $satir_sinifi  = $is_devir ? 'table-secondary text-muted' : 'table-light';
                                            
                                            // Kur ondalık hassasiyeti yönetimi
                                            $ondalik_hane  = ($kod === 'XAU') ? 2 : 4; 
                                        ?>
                                            <tr class="<?= $satir_sinifi ?> <?= $yazi_sinifi ?>">
                                                <!-- İşlem Tarihi -->
                                                <td class="text-start ps-3 font-monospace text-dark">
                                                    <?= date('d.m.Y', strtotime($islem['islem_tarihi'])) ?>
                                                    <?= $is_devir ? ' <small class="fw-bold text-secondary">(Devir)</small>' : '' ?>
                                                </td>
                                                
                                                <!-- Tür Rozeti -->
                                                <td class="text-center">
                                                    <span class="badge border bg-white badge-fx <?= $badge_sinifi ?>"><?= $tur_metni ?></span>
                                                </td>
                                                
                                                <!-- Miktar Hücresi -->
                                                <td class="fw-bold"><?= $isaret ?> <?= number_format(abs($islem['miktar']), 2, ',', '.') ?> <?= $p['simge'] ?></td>
                                                
                                                <!-- Akış Kuralına Göre İlerleyen Yürüyen Bakiye Hücresi -->
                                                <td class="fw-bold text-dark font-monospace text-center bg-white bg-opacity-25">
                                                    <?= number_format($guncel_bakiye, 2, ',', '.') ?> <?= $p['simge'] ?>
                                                </td>
                                                
                                                <!-- Döviz / Emtia Kuru -->
                                                <td class="text-dark"><?= number_format($islem['doviz_kuru'], $ondalik_hane, ',', '.') ?> ₺</td>
                                                
                                                <!-- Tutar, Vergi ve Gerçek Maliyet Hücreleri -->
                                                <td><?= $isaret ?> <?= number_format(abs($islem['tutar']), 2, ',', '.') ?> ₺</td>
                                                <td class="text-secondary"><?= number_format($islem['vergi'], 2, ',', '.') ?> ₺</td>
                                                <td class="pe-3 fw-bold"><?= $isaret ?> <?= number_format(abs($islem['maliyet']), 2, ',', '.') ?> ₺</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-5 bg-white">Kayıtlı işlem bulunmuyor.</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                    
                                    <tfoot class="table-light fw-bold text-dark border-top-2">
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>


        </div>
    </div>

</div>

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

                <!-- Adım 1: Havuz, Hedef ve Tutar Girişi -->
                <div id="transferAdim1">
                    
                    <!-- ADIM A: Döviz Cinsi Seçimi -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary mb-1 d-inline-flex align-items-center" style="line-height: 1;">
                            <i class="fas fa-coins me-1 text-warning" style="font-size: 11px;"></i> 1. Transfer Edilecek Varlık Cinsi
                        </label>
                        <select class="form-select form-select-sm shadow-none p-2 border-0" id="transferVarlikCinsi" style="font-size: 11px; background-color: #eaeded; border-radius: 4px; color: #495057; font-weight: 600;">
                            <option value="" disabled selected>-- Varlık Seçiniz --</option>
                            <option value="TL">Türk Lirası (TL)</option>
                            <option value="USD">Amerikan Doları (USD)</option>
                            <option value="EUR">Euro (EUR)</option>
                            <option value="XAU">Gram Altın (XAU)</option>
                        </select>
                    </div>

                    <!-- ADIM B: Kaynak Havuz Seçimi -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary mb-1 d-inline-flex align-items-center" style="line-height: 1;">
                            <i class="fas fa-sign-out-alt me-1 text-danger" style="font-size: 11px;"></i> 2. Paranın Çekileceği Kaynak Hesap
                        </label>
                        <select class="form-select form-select-sm shadow-none p-2 border-0" id="transferKaynakHavuz" disabled style="font-size: 11px; background-color: #eaeded; border-radius: 4px; color: #495057; font-weight: 500;">
                            <option value="">-- Önce Varlık Seçiniz --</option>
                        </select>
                    </div>
                    
                    <!-- ADIM C: Hedef Havuz Seçimi -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary mb-1 d-inline-flex align-items-center" style="line-height: 1;">
                            <i class="fas fa-sign-in-alt me-1 text-success" style="font-size: 11px;"></i> 3. Paranın Aktarılacağı Hedef Hesap
                        </label>
                        <select class="form-select form-select-sm shadow-none p-2 border-0" id="transferHedefHavuz" disabled style="font-size: 11px; background-color: #eaeded; border-radius: 4px; color: #495057; font-weight: 500;">
                            <option value="">-- Önce Kaynak Seçiniz --</option>
                        </select>
                    </div>

                    <!-- Tutar Girişi -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary mb-1 d-inline-flex align-items-center" style="line-height: 1;" id="lblTransferTutar">
                            <i class="fas fa-money-bill-wave me-1 text-primary" style="font-size: 11px;"></i> 4. Transfer Edilecek Miktar
                        </label>
                        <input type="text" class="form-control form-control-sm shadow-none p-2 border-0 text-end fw-bold font-monospace" id="transferTutar" placeholder="0,00" style="background-color: #eaeded; font-size: 12px; border-radius: 4px; color: #2196f3;">
                    </div>

                    <!-- AKILLI VERİTABANI BÜTÇE TAG ALANI -->
                    <div id="akilliBütçeTagAlani" class="mb-3 p-3 rounded border bg-white d-none" style="border-color: #e9ecef !important;">
                        <div class="text-secondary fw-bold mb-2 d-inline-flex align-items-center" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px; line-height: 1;">
                            <i class="fas fa-brain text-info me-1" style="font-size: 10px;"></i> Tanımlı Transferler:
                        </div>
                        <div id="tagKonteyner" class="d-flex flex-wrap gap-2 align-items-center"></div>
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
                        <i class="fas fa-check-circle me-2" style="font-size: 12px;"></i> Seçilen hesap bakiyesi bu transfer için tamamen yeterlidir.
                    </div>

                    <!-- Yetersiz Bakiye Durumu -->
                    <div id="senaryoYetersiz" class="d-none">
                        <div class="alert alert-danger p-2 mb-3 border-0 d-inline-flex align-items-center w-100" style="background-color: #ffebee; color: #c62828; border-radius: 4px; font-weight: 500; line-height: 1.2;">
                            <i class="fas fa-exclamation-triangle me-2" style="font-size: 12px;"></i> <span><strong>Bakiye Yetersiz!</strong> Eksik Tutar: <span class="fw-bold font-monospace" id="eksikTutarMetni">0,00</span></span>
                        </div>
                        
                        <label class="form-label fw-bold text-secondary mb-2 d-inline-flex align-items-center" style="line-height: 1;">
                            <i class="fas fa-route me-1 text-warning" style="font-size: 11px;"></i> Öneriler:
                        </label>
                        
                        <!-- Seçenek A -->
                        <div class="form-check border p-2 rounded mb-2 bg-white d-flex align-items-center style-cursor" style="border-color: #e9ecef !important; min-height: 32px;">
                            <input class="form-check-input my-0 ms-1 me-2 shadow-none" type="radio" name="yetersizCozumTuru" id="cozumSadeceMevcut" value="sadece_mevcut" checked>
                            <label class="form-check-label fw-bold d-block style-cursor text-muted" for="cozumSadeceMevcut" style="margin-left: 5px; line-height: 1.2;">
                                Sadece <span id="mevcutAktarilabilirMetin" class="font-monospace text-secondary">0,00</span> aktar.
                            </label>
                        </div>

                        <!-- Seçenek B -->
                        <div class="form-check border p-2 rounded bg-white style-cursor" style="border-color: #e9ecef !important;">
                            <div class="d-flex align-items-center" style="min-height: 20px;">
                                <input class="form-check-input my-0 ms-1 me-2 shadow-none" type="radio" name="yetersizCozumTuru" id="cozumDestekli" value="destekli">
                                <label class="form-check-label fw-bold d-block style-cursor text-muted" for="cozumDestekli" style="margin-left: 5px; line-height: 1.2;">
                                    <span id="mevcutAktarilabilirMetin1" class="font-monospace text-secondary">0,00</span> ana hesaptan, kalan <span class="fw-bold font-monospace" id="eksikTutarMetni1">0,00</span> diğer hesaptan aktar.
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



<script>


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

    // 1. Canlı Kur Tanımları (Controller'dan beslenir)
    const liveKurlar = {
        'USD': <?= (float)($live_kurlar['USD'] ?? 46.8215) ?>,
        'EUR': <?= (float)($live_kurlar['EUR'] ?? 53.8685) ?>,
        'XAU': <?= (float)($live_kurlar['XAU'] ?? 6086.4200) ?>
    };

    let sonAktifGiris = 'TL'; 


    function hesaplaPanelYatirimMatematigi(tetikleyici = null) {
        if (tetikleyici) sonAktifGiris = tetikleyici;

        const elIslemYonu = document.querySelector('input[name="panel_islem_yonu"]:checked');
        if (!elIslemYonu) return;
        
        const islemYonu = elIslemYonu.value;
        const elCekilecek = document.getElementById('panel_cekilecek_hesap'); 
        const elAktarilacak = document.getElementById('panel_aktarilacak_hesap'); 
        const btnSubmit = document.getElementById('btn_panel_onayla');

        // Koruma Bariyeri 1: Hesaplar seçilmediyse butonu kilitle ve çık
        if (!elCekilecek || !elAktarilacak || !elCekilecek.value || !elAktarilacak.value) {
            if (btnSubmit) btnSubmit.disabled = true;
            return;
        }

        const optCekilecek = elCekilecek.options[elCekilecek.selectedIndex];
        const optAktarilacak = elAktarilacak.options[elAktarilacak.selectedIndex];

        if (!optCekilecek || !optAktarilacak) return;

        const cekilecekParcalar = optCekilecek.value.split('_');
        const aktarilacakParcalar = optAktarilacak.value.split('_');

        const varlikCinsi = (islemYonu === 'ALIS') ? aktarilacakParcalar[aktarilacakParcalar.length - 1] : cekilecekParcalar[cekilecekParcalar.length - 1];

        const bakiyeTL = parseFloat(optCekilecek.dataset.tl) || parseFloat(optAktarilacak.dataset.tl) || 0;
        const bakiyeVarlik = parseFloat(optCekilecek.dataset[varlikCinsi.toLowerCase()]) || parseFloat(optAktarilacak.dataset[varlikCinsi.toLowerCase()]) || 0;

        const safCanliKur = liveKurlar[varlikCinsi] || 0;
        const elManuelKur = document.getElementById('panel_manuel_kur');
        const manuelKur = elManuelKur ? (parseFloat(elManuelKur.value) || 0) : 0;
        const aktifIslemKuru = (manuelKur > 0) ? manuelKur : safCanliKur;
        const basamak = (varlikCinsi === 'XAU') ? 2 : 4;

        // Göstergeleri Eşle
        if (document.getElementById('panel_canli_kur_text')) {
            document.getElementById('panel_canli_kur_text').textContent = aktifIslemKuru.toLocaleString('tr-TR', { minimumFractionDigits: basamak }) + " ₺";
        }
        if (document.getElementById('txt_cekilecek_bakiye')) {
            document.getElementById('txt_cekilecek_bakiye').textContent = (islemYonu === 'ALIS') ? "Bakiye: " + paraFormatlaTL(bakiyeTL) : `Bakiye: ${bakiyeVarlik.toFixed(basamak)} ${varlikCinsi}`;
        }
        if (document.getElementById('txt_aktarilacak_bakiye')) {
            document.getElementById('txt_aktarilacak_bakiye').textContent = (islemYonu === 'ALIS') ? `Portföy: ${bakiyeVarlik.toFixed(basamak)} ${varlikCinsi}` : "Bakiye: " + paraFormatlaTL(bakiyeTL);
        }
        if (document.getElementById('lbl_islem_miktari')) {
            document.getElementById('lbl_islem_miktari').textContent = `İşlem Miktarı (${varlikCinsi})`;
        }

        // Limit Göstergelerini Tazele
        if (document.getElementById('panel_maks_kapasite_text')) {
            if (islemYonu === 'ALIS') {
                const netKapasiteTL = bakiyeTL / 1.0020; 
                const maksAlinabilir = netKapasiteTL / aktifIslemKuru;
                document.getElementById('panel_maks_kapasite_text').textContent = maksAlinabilir.toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + " " + varlikCinsi;
            } else {
                document.getElementById('panel_maks_kapasite_text').textContent = paraFormatlaTL(bakiyeVarlik * aktifIslemKuru);
            }
        }

        if (aktifIslemKuru <= 0) return;

        // Çapraz Giriş Eşleme Alanı
        const elTutarInput = document.getElementById('panel_tutar_tl');
        const elMiktarInput = document.getElementById('panel_miktar');
        if (!elTutarInput || !elMiktarInput) return;

        let finalTutarTL = 0;
        let finalMiktarAdet = 0;

        if (sonAktifGiris === 'TL') {
            finalTutarTL = parseFloat(elTutarInput.value) || 0;
            if (finalTutarTL > 0) {
                finalMiktarAdet = (islemYonu === 'ALIS') ? (finalTutarTL / 1.0020) / aktifIslemKuru : finalTutarTL / aktifIslemKuru;
                elMiktarInput.value = finalMiktarAdet.toFixed(basamak);
            } else { elMiktarInput.value = ''; }
        } else {
            finalMiktarAdet = parseFloat(elMiktarInput.value) || 0;
            if (finalMiktarAdet > 0) {
                finalTutarTL = (islemYonu === 'ALIS') ? (finalMiktarAdet * aktifIslemKuru) * 1.0020 : finalMiktarAdet * aktifIslemKuru;
                elTutarInput.value = finalTutarTL.toFixed(2);
            } else { elTutarInput.value = ''; }
        }

        // Likidite ve Onay Bariyerleri Yönetimi
        const elVergiRozet = document.getElementById('panel_vergi_bilgi_rozet');
        const elNetTutarRozet = document.getElementById('panel_net_tutar_bilgi_rozet');
        const elNihaiSonuc = document.getElementById('panel_nihai_sonuc');
        const elUyari = document.getElementById('panel_bakiye_uyari_metni');

        if (finalTutarTL > 0 && finalMiktarAdet > 0) {
            if (islemYonu === 'ALIS') {
                let vergi = finalTutarTL - (finalTutarTL / 1.0020);
                let netMaliyet = finalTutarTL - vergi;

                if (elVergiRozet) elVergiRozet.textContent = "Vergi: " + paraFormatlaTL(vergi);
                if (elNetTutarRozet) elNetTutarRozet.textContent = "Net Tutar: " + paraFormatlaTL(netMaliyet);
                if (elNihaiSonuc) elNihaiSonuc.value = paraFormatlaTL(finalTutarTL);

                // Bakiye kontrolü ve buton kilidinin çözülmesi
                if (finalTutarTL > bakiyeTL) {
                    if (elUyari) elUyari.classList.remove('d-none');
                    if (btnSubmit) btnSubmit.disabled = true;
                } else {
                    if (elUyari) elUyari.classList.add('d-none');
                    if (btnSubmit) btnSubmit.disabled = false; // 🔓 KİLİT ÇÖZÜLDÜ
                }
            } else {
                if (elVergiRozet) elVergiRozet.textContent = "Komisyon: 0,00 ₺";
                if (elNetTutarRozet) elNetTutarRozet.textContent = "Net Kazanç: " + paraFormatlaTL(finalTutarTL);
                if (elNihaiSonuc) elNihaiSonuc.value = paraFormatlaTL(finalTutarTL);

                // Stok kontrolü ve buton kilidinin çözülmesi
                if (finalMiktarAdet > bakiyeVarlik) {
                    if (elUyari) elUyari.classList.remove('d-none');
                    if (btnSubmit) btnSubmit.disabled = true;
                } else {
                    if (elUyari) elUyari.classList.add('d-none');
                    if (btnSubmit) btnSubmit.disabled = false; // 🔓 KİLİT ÇÖZÜLDÜ
                }
            }
        } else {
            // Girdiler sıfırsa veya silindiyse sistemi kilitle
            if (elNihaiSonuc) elNihaiSonuc.value = "0,00 ₺";
            if (elVergiRozet) elVergiRozet.textContent = islemYonu === 'ALIS' ? "Vergi: 0,00 ₺" : "Komisyon: 0,00 ₺";
            if (elNetTutarRozet) elNetTutarRozet.textContent = islemYonu === 'ALIS' ? "Net Tutar: 0,00 ₺" : "Net Kazanç: 0,00 ₺";
            if (btnSubmit) btnSubmit.disabled = true;
        }
    }

    function dinamikArayuzYonetimi() {
        const islemYonu = document.querySelector('input[name="panel_islem_yonu"]:checked').value;
        const elCekilecek = document.getElementById('panel_cekilecek_hesap');
        const elAktarilacak = document.getElementById('panel_aktarilacak_hesap');
        const btnOnayla = document.getElementById('btn_panel_onayla');
        
        if (!window.globalRawHavuzlar || window.globalRawHavuzlar.length === 0) {
            window.globalRawHavuzlar = Array.from(elCekilecek.querySelectorAll('option')).filter(opt => opt.value !== "");
        }

        elCekilecek.innerHTML = '<option value="" disabled selected>Hesap Seçiniz...</option>';
        elAktarilacak.innerHTML = '<option value="" disabled selected>Hesap Seçiniz...</option>';

        if (islemYonu === 'ALIS') {
            if (btnOnayla) {
                btnOnayla.textContent = "DÖVİZ ALIŞ İŞLEMİNİ TAMAMLA";
                btnOnayla.className = "btn btn-success btn-lg px-4 fw-bold text-uppercase d-inline-flex align-items-center justify-content-center text-white";
            }
            document.getElementById('lbl_cekilecek_hesap').textContent = "Paranın Çekileceği Hesap (TL)";
            document.getElementById('lbl_aktarilacak_hesap').textContent = "Paranın Aktarılacağı Hesap (Varlık)";
            document.getElementById('panel_kur_turu_label').textContent = "Canlı Banka Alış";
            document.getElementById('panel_maks_kapasite_label').textContent = "Maks. Alınabilir";

            window.globalRawHavuzlar.forEach(opt => {
                elCekilecek.appendChild(opt.cloneNode(true));
            });

            elCekilecek.onchange = function() {
                const seciliHavuzSatiri = elCekilecek.options[elCekilecek.selectedIndex];
                if (!seciliHavuzSatiri || !seciliHavuzSatiri.value) {
                    elAktarilacak.innerHTML = '<option value="" disabled selected>Önce Hesap Seçiniz...</option>';
                    return;
                }
                
                const havuzId = seciliHavuzSatiri.value.split('_')[0];
                const havuzText = seciliHavuzSatiri.text.split(' - ')[0];

                elAktarilacak.innerHTML = '<option value="" disabled selected>Varlık Hücresi Seçiniz...</option>';
                elAktarilacak.innerHTML += `<option value="${havuzId}_usd_bakiye_USD" data-bakiye="${seciliHavuzSatiri.dataset.usd}">${havuzText} - USD Kasası</option>`;
                elAktarilacak.innerHTML += `<option value="${havuzId}_eu_bakiye_EUR" data-bakiye="${seciliHavuzSatiri.dataset.eur}">${havuzText} - EUR Kasası</option>`;
                elAktarilacak.innerHTML += `<option value="${havuzId}_xau_bakiye_XAU" data-bakiye="${seciliHavuzSatiri.dataset.xau}">${havuzText} - XAU Kasası</option>`;
                
                hesaplaPanelYatirimMatematigi();
            };

        } else {
            if (btnOnayla) {
                btnOnayla.textContent = "DÖVİZ SATIŞ İŞLEMİNİ TAMAMLA";
                btnOnayla.className = "btn btn-danger btn-lg px-4 fw-bold text-uppercase d-inline-flex align-items-center justify-content-center text-white";
            }
            document.getElementById('lbl_cekilecek_hesap').textContent = "Varlığın Çekileceği Hesap (Varlık)";
            document.getElementById('lbl_aktarilacak_hesap').textContent = "Paranın Aktarılacağı Hesap (TL)";
            document.getElementById('panel_kur_turu_label').textContent = "Canlı Banka Satış";
            document.getElementById('panel_maks_kapasite_label').textContent = "Maks. Satılabilir";

            window.globalRawHavuzlar.forEach(opt => {
                const havuzId = opt.value.split('_')[0];
                const havuzText = opt.text.split(' - ')[0];

                elCekilecek.innerHTML += `<option value="${havuzId}_usd_bakiye_USD" data-bakiye="${opt.dataset.usd}">${havuzText} - USD Kasası</option>`;
                elCekilecek.innerHTML += `<option value="${havuzId}_eu_bakiye_EUR" data-bakiye="${opt.dataset.eur}">${havuzText} - EUR Kasası</option>`;
                elCekilecek.innerHTML += `<option value="${havuzId}_xau_bakiye_XAU" data-bakiye="${opt.dataset.xau}">${havuzText} - XAU Kasası</option>`;
            });

            elCekilecek.onchange = function() {
                const seciliVarlıkSatiri = elCekilecek.options[elCekilecek.selectedIndex];
                if (!seciliVarlıkSatiri || !seciliVarlıkSatiri.value) {
                    elAktarilacak.innerHTML = '<option value="" disabled selected>Önce Varlık Seçiniz...</option>';
                    return;
                }

                const havuzId = seciliVarlıkSatiri.value.split('_')[0];
                const eslesenAnaOpt = window.globalRawHavuzlar.find(opt => opt.value.split('_')[0] === havuzId);

                if (eslesenAnaOpt) {
                    elAktarilacak.innerHTML = '';
                    elAktarilacak.appendChild(eslesenAnaOpt.cloneNode(true));
                    elAktarilacak.selectedIndex = 0;
                }
                
                hesaplaPanelYatirimMatematigi();
            };
        }

        elCekilecek.dispatchEvent(new Event('change'));
        elAktarilacak.onchange = function() { hesaplaPanelYatirimMatematigi(); };
        hesaplaPanelYatirimMatematigi();
    }

    // =========================================================================
// 🔄 DİNAMİK KAYNAK SEÇİMLİ AKILLI TRANSFER MOTORU
// =========================================================================

// Controller katmanından gelen finansal mizan dizisini JS katmanına bağlıyoruz
const tumFinansalHavuzlar = <?= json_encode($finansal_havuzlar, JSON_UNESCAPED_UNICODE) ?>;

// Global Durum ve Likidite Takipçileri
let aktifVirlikCinsi = 'TL';
let aktifKaynakHavuz = '';
let aktifKaynakBakiye = 0.00;

// Tanımlı Akıllı Transfer Şablon Matrisi (İsteğe göre genişletilebilir)
const akilliTransferSablonlari = {
    'EUR': [
        { etiket: 'Banka Borç Kapatma', tutar: 500, aciklama: 'Kredi kartı Euro borç transferi' },
        { etiket: 'Euro Kasa Dengeleme', tutar: 1000, aciklama: 'Hesaplar arası likidite optimizasyonu' },
        { etiket: 'Nakit Tamamlama', tutar: 250, aciklama: 'Nakit kasaya fiziki takviye fon' }
    ],
    'USD': [
        { etiket: 'Dolar Borç Ödeme', tutar: 400, aciklama: 'İşbank kart dolar borcu transferi' },
        { etiket: 'Mevduat Dengeleme', tutar: 1500, aciklama: 'Ana hesaplar arası denge' }
    ],
    'XAU': [
        { etiket: 'Gram Altın Transfer', tutar: 10, aciklama: 'Hesaplar arası altın kaydırma' }
    ],
    'TL': [
        { etiket: 'Kira/Bütçe Ayırımı', tutar: 20000, aciklama: 'Dönem içi standart bütçe transferi' }
    ]
};


/**
 * Ana "İşlem Terminali" butonuna basıldığında modalı tertemiz başlatan tetikleyici
 */
function forexAction(havuzKodu = null) {
    // Form elemanlarını ve emniyet kilitlerini ilk haline sıfırla
    $('#transferVarlikCinsi').val('');
    $('#transferKaynakHavuz').empty().append('<option value="">-- Önce Varlık Seçiniz --</option>').prop('disabled', true);
    $('#transferHedefHavuz').empty().append('<option value="">-- Önce Kaynak Seçiniz --</option>').prop('disabled', true);
    $('#transferTutar').val('');
    $('#transferSenaryoPaneli').addClass('d-none');
    $('#akilliBütçeTagAlani').addClass('d-none');
    
    // Modalı şık bir şekilde ekrana fırlat
    $('#akilliTransferModal').modal('show');
}

//////////////////////////

// -------------------------------------------------------------------------
// 1. ZİNCİR BARİYERİ: Varlık Cinsi Seçildiğinde Kaynak Havuzları Doldur (DÜZELTİLDİ)
// -------------------------------------------------------------------------
$(document).on('change', '#transferVarlikCinsi', function() {
    aktifVirlikCinsi = $(this).val();
    
    let sutunHaritasi = { 'TL': 'anapara', 'USD': 'usd_bakiye', 'EUR': 'eu_bakiye', 'XAU': 'xau_bakiye' };
    let bakiyeSutunAdi = sutunHaritasi[aktifVirlikCinsi];
    let simge = aktifVirlikCinsi === 'TL' ? '₺' : (aktifVirlikCinsi === 'EUR' ? '€' : (aktifVirlikCinsi === 'USD' ? '$' : 'GR'));

    let $kaynakSelect = $('#transferKaynakHavuz');
    $kaynakSelect.empty().append('<option value="" disabled selected>-- Kaynak Hesap Seçiniz --</option>');
    
    // Kaynak listesinde sadece gerçek hesaplar listelenir
    $.each(tumFinansalHavuzlar, function(index, havuz) {
        let bakiye = parseFloat(havuz[bakiyeSutunAdi] ?? 0);
        $kaynakSelect.append(`<option value="${havuz.havuz_kodu}" data-bakiye="${bakiye}">${havuz.havuz_adi} (Mevcut: ${paraFormatla(bakiye)} ${simge})</option>`);
    });

    $kaynakSelect.prop('disabled', false);
    
    // Alt alanları temizle ve kilitle
    $('#transferHedefHavuz').empty().append('<option value="">-- Önce Kaynak Seçiniz --</option>').prop('disabled', true);
    $('#transferTutar').val('');
    $('#transferSenaryoPaneli').addClass('d-none');
    
    $('#lblTransferTutar').html(`<i class="fas fa-money-bill-wave me-1 text-primary"></i> 4. Transfer Edilecek Miktar (${aktifVirlikCinsi})`);
    if (typeof akilliTagSisteminiBesle === 'function') akilliTagSisteminiBesle(aktifVirlikCinsi);
});

// -------------------------------------------------------------------------
// 2. ZİNCİR BARİYERİ: Kaynak Hesap Seçildiğinde Hedef Havuzları Filtrele (DÜZELTİLDİ)
// -------------------------------------------------------------------------
$(document).on('change', '#transferKaynakHavuz', function() {
    aktifKaynakHavuz = $(this).val();
    let seciliOption = $(this).find('option:selected');
    aktifKaynakBakiye = parseFloat(seciliOption.data('bakiye') ?? 0);

    let sutunHaritasi = { 'TL': 'anapara', 'USD': 'usd_bakiye', 'EUR': 'eu_bakiye', 'XAU': 'xau_bakiye' };
    let bakiyeSutunAdi = sutunHaritasi[aktifVirlikCinsi];
    let simge = aktifVirlikCinsi === 'TL' ? '₺' : (aktifVirlikCinsi === 'EUR' ? '€' : (aktifVirlikCinsi === 'USD' ? '$' : 'GR'));

    let $hedefSelect = $('#transferHedefHavuz');
    $hedefSelect.empty().append('<option value="" disabled selected>-- Hedef Hesap Seçiniz --</option>');

    // 🔥 DOĞRU ENTEGRASYON: "Nakit Harcama" seçeneği aktarılacak HEDEF listesinin en başına eklenir
    $hedefSelect.append(`<option value="NAKIT_HARCAMA" style="color: #c62828; font-weight: bold;">⚠️ NAKİT HARCAMA (Sistem Dışına Çıkış)</option>`);

    // Kaynak hesap hariç diğer hesapları hedefe ekle (Çapraz kur engeli korunur)
    $.each(tumFinansalHavuzlar, function(index, havuz) {
        if (havuz.havuz_kodu !== aktifKaynakHavuz) {
            let hedefBakiye = parseFloat(havuz[bakiyeSutunAdi] ?? 0);
            $hedefSelect.append(`<option value="${havuz.havuz_kodu}">${havuz.havuz_adi} (Mevcut: ${paraFormatla(hedefBakiye)} ${simge})</option>`);
        }
    });

    $hedefSelect.prop('disabled', false);
    $('#transferTutar').val('');
    $('#transferSenaryoPaneli').addClass('d-none');
});

// -------------------------------------------------------------------------
// 3. MATEMATİKSEL MOTOR: Canlı Senaryo Hesaplama Girişi (DÜZELTİLDİ)
// -------------------------------------------------------------------------
$(document).on('input', '#transferTutar', function() {
    let girilenTutar = paraTemizle($(this).val());
    let seciliHedef = $('#transferHedefHavuz').val();
    
    if (girilenTutar <= 0 || !seciliHedef) {
        $('#transferSenaryoPaneli').addClass('d-none');
        return;
    }

    let $panel = $('#transferSenaryoPaneli');
    $panel.removeClass('d-none');

    // 🔥 KURAL: Eğer HEDEF "Nakit Harcama" ise kontrolü KAYNAK hesap bakiyesine göre yap
    if (seciliHedef === 'NAKIT_HARCAMA') {
        $('#cozumSadeceMevcut').closest('.form-check').addClass('d-none');
        $('#cozumDestekli').closest('.form-check').addClass('d-none');
        
        if (aktifKaynakBakiye >= girilenTutar) {
            $('#senaryoYeterli').removeClass('d-none').html(`<i class="fas fa-check-circle me-2"></i> Kaynak hesap bakiyesi bu nakit harcama kaydı için tamamen yeterlidir.`);
            $('#senaryoYetersiz').addClass('d-none');
            $('#btnTransferiOnayla').prop('disabled', false).removeClass('btn-secondary btn-success').addClass('btn-danger').html('<i class="fas fa-shopping-cart me-2"></i> Harcamayı Kaydet ve Kaynaktan Düş');
        } else {
            $('#senaryoYeterli').addClass('d-none');
            $('#senaryoYetersiz').removeClass('d-none');
            let eksik = girilenTutar - aktifKaynakBakiye;
            $('#eksikTutarMetni').text(paraFormatla(eksik) + ' ' + aktifVirlikCinsi + ' (Kaynak hesapta harcayacak yeterli fon yok!)');
            $('#btnTransferiOnayla').prop('disabled', true);
        }
    } else {
        // Eğer normal bir hesap transferiyse eski kuralları çalıştır
        $('#cozumSadeceMevcut').closest('.form-check').removeClass('d-none');
        $('#cozumDestekli').closest('.form-check').removeClass('d-none');
        $('#btnTransferiOnayla').removeClass('btn-danger').addClass('btn-success').html('<i class="fas fa-check-double me-2"></i> Transferi Onayla');
        transferSenaryosunuHesapla(girilenTutar);
    }
});

// Hedef selectbox değiştiğinde de hesaplamayı tetikle
$(document).on('change', '#transferHedefHavuz', function() {
    $('#transferTutar').trigger('input');
});




// // -------------------------------------------------------------------------
// // 1. ZİNCİR BARİYERİ: Varlık Cinsi Seçildiğinde Kaynak Havuzları Doldur
// // -------------------------------------------------------------------------
// $(document).on('change', '#transferVarlikCinsi', function() {
//     aktifVirlikCinsi = $(this).val();
    
//     let sutunHaritasi = { 'TL': 'anapara', 'USD': 'usd_bakiye', 'EUR': 'eu_bakiye', 'XAU': 'xau_bakiye' };
//     let bakiyeSutunAdi = sutunHaritasi[aktifVirlikCinsi];
//     let simge = aktifVirlikCinsi === 'TL' ? '₺' : (aktifVirlikCinsi === 'EUR' ? '€' : (aktifVirlikCinsi === 'USD' ? '$' : 'GR'));

//     let $kaynakSelect = $('#transferKaynakHavuz');
//     $kaynakSelect.empty().append('<option value="" disabled selected>-- Kaynak Hesap Seçiniz --</option>');
    
//     // İlgili döviz kırılımına sahip tüm havuzları listeye basıyoruz
//     $.each(tumFinansalHavuzlar, function(index, havuz) {
//         let bakiye = parseFloat(havuz[bakiyeSutunAdi] ?? 0);
//         $kaynakSelect.append(`<option value="${havuz.havuz_kodu}" data-bakiye="${bakiye}">${havuz.havuz_adi} (Mevcut: ${paraFormatla(bakiye)} ${simge})</option>`);
//     });

//     $kaynakSelect.prop('disabled', false);
    
//     // Alt basamakları sıfırla ve kilitle
//     $('#transferHedefHavuz').empty().append('<option value="">-- Önce Kaynak Seçiniz --</option>').prop('disabled', true);
//     $('#transferTutar').val('');
//     $('#transferSenaryoPaneli').addClass('d-none');
    
//     // Başlık etiketini seçilen birime göre dinamikleştir
//     $('#lblTransferTutar').html(`<i class="fas fa-money-bill-wave me-1 text-primary"></i> 4. Transfer Edilecek Miktar (${aktifVirlikCinsi})`);
    
//     // Hızlı bütçe etiketlerini yükle
//     akilliTagSisteminiBesle(aktifVirlikCinsi);
// });

// // -------------------------------------------------------------------------
// // 2. ZİNCİR BARİYERİ: Kaynak Hesap Seçildiğinde Hedef Havuzları Filtrele
// // -------------------------------------------------------------------------
// $(document).on('change', '#transferKaynakHavuz', function() {
//     aktifKaynakHavuz = $(this).val();
//     let seciliOption = $(this).find('option:selected');
//     aktifKaynakBakiye = parseFloat(seciliOption.data('bakiye') ?? 0);

//     let sutunHaritasi = { 'TL': 'anapara', 'USD': 'usd_bakiye', 'EUR': 'eu_bakiye', 'XAU': 'xau_bakiye' };
//     let bakiyeSutunAdi = sutunHaritasi[aktifVirlikCinsi];
//     let simge = aktifVirlikCinsi === 'TL' ? '₺' : (aktifVirlikCinsi === 'EUR' ? '€' : (aktifVirlikCinsi === 'USD' ? '$' : 'GR'));

//     let $hedefSelect = $('#transferHedefHavuz');
//     $hedefSelect.empty().append('<option value="" disabled selected>-- Hedef Hesap Seçiniz --</option>');

//     // 🔒 ÇAPRAZ KUR ENGELLİ FİLTRELEME: Sadece kaynak olmayan ve aynı döviz türündeki havuzları getir
//     $.each(tumFinansalHavuzlar, function(index, havuz) {
//         if (havuz.havuz_kodu !== aktifKaynakHavuz) {
//             let hedefBakiye = parseFloat(havuz[bakiyeSutunAdi] ?? 0);
//             $hedefSelect.append(`<option value="${havuz.havuz_kodu}">${havuz.havuz_adi} (Mevcut: ${paraFormatla(hedefBakiye)} ${simge})</option>`);
//         }
//     });

//     $hedefSelect.prop('disabled', false);
//     $('#transferTutar').val('');
//     $('#transferSenaryoPaneli').addClass('d-none');
// });

// // -------------------------------------------------------------------------
// // 3. MATEMATİKSEL MOTOR: Canlı Senaryo ve Bakiye Doğrulama Analizi
// // -------------------------------------------------------------------------
// function transferSenaryosunuHesapla(girilenTutar) {
//     let $panel = $('#transferSenaryoPaneli');
//     let $yeterli = $('#senaryoYeterli');
//     let $yetersiz = $('#senaryoYetersiz');
    
//     $panel.removeClass('d-none');
    
//     if (aktifKaynakBakiye >= girilenTutar) {
//         // Durum A: Likidite Tamamen Yeterli
//         $yeterli.removeClass('d-none');
//         $yetersiz.addClass('d-none');
//         $('#btnTransferiOnayla').prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
//     } else {
//         // Durum B: Bakiye Yetersiz (Ortak Paylaşımlı Destek Çözüm Paneli)
//         $yeterli.addClass('d-none');
//         $yetersiz.removeClass('d-none');
//         $('#btnTransferiOnayla').prop('disabled', false);
        
//         let eksikTutar = girilenTutar - aktifKaynakBakiye;
//         let simge = aktifVirlikCinsi === 'TL' ? ' ₺' : ' ' + aktifVirlikCinsi;
        
//         // Metinsel alanları kuruşu kuruşuna eşleme
//         $('#eksikTutarMetni, #eksikTutarMetni1').text(paraFormatla(eksikTutar) + simge);
//         $('#mevcutAktarilabilirMetin, #mevcutAktarilabilirMetin1').text(paraFormatla(aktifKaynakBakiye) + simge);
        
//         // Eğer kullanıcı destekli çözümü seçtiyse havuzları doldur
//         let cozumTuru = $('input[name="yetersizCozumTuru"]:checked').val();
//         if (cozumTuru === 'destekli') {
//             $('#destekHavuzSecimAlani').removeClass('d-none');
//             destekHavuzlariniDoldur();
//         } else {
//             $('#destekHavuzSecimAlani').addClass('d-none');
//         }
//     }
// }

function destekHavuzlariniDoldur() {
    let $destekSelect = $('#transferDestekHavuz');
    let hedefHavuz = $('#transferHedefHavuz').val();
    
    $destekSelect.empty().append('<option value="">-- Eksiği Kapatacak Destek Hesap Seçin --</option>');
    
    let sutunHaritasi = { 'TL': 'anapara', 'USD': 'usd_bakiye', 'EUR': 'eu_bakiye', 'XAU': 'xau_bakiye' }[aktifVirlikCinsi];
    let simge = aktifVirlikCinsi === 'TL' ? '₺' : (aktifVirlikCinsi === 'EUR' ? '€' : (aktifVirlikCinsi === 'USD' ? '$' : 'GR'));

    // Kaynak ve hedef dışındaki 3. havuzları (destek) süzme bariyeri
    $.each(tumFinansalHavuzlar, function(index, havuz) {
        if (havuz.havuz_kodu !== aktifKaynakHavuz && havuz.havuz_kodu !== hedefHavuz) {
            let destekBakiye = parseFloat(havuz[sutunHaritasi] ?? 0);
            $destekSelect.append(`<option value="${havuz.havuz_kodu}">${havuz.havuz_adi} (Mevcut: ${paraFormatla(destekBakiye)} ${simge})</option>`);
        }
    });
}

// Tutar yazılırken veya buton tıklandığında senaryoyu çalıştır
$(document).on('input', '#transferTutar', function() {
    let girilenTutar = paraTemizle($(this).val());
    if (girilenTutar > 0) {
        transferSenaryosunuHesapla(girilenTutar);
    } else {
        $('#transferSenaryoPaneli').addClass('d-none');
    }
});

$(document).on('click', '#btnTransferKontrolEt', function() {
    let girilenTutar = paraTemizle($('#transferTutar').val());
    if (girilenTutar <= 0) {
        alert('Lütfen önce geçerli bir transfer tutarı veya miktarı giriniz.');
        return;
    }
    transferSenaryosunuHesapla(girilenTutar);
});

$(document).on('change', 'input[name="yetersizCozumTuru"]', function() {
    let girilenTutar = paraTemizle($('#transferTutar').val());
    if (girilenTutar > 0) transferSenaryosunuHesapla(girilenTutar);
});

// -------------------------------------------------------------------------
// 4. ETİKET MOTORU: Akıllı Bütçe Tag Alanı Yönetimi
// -------------------------------------------------------------------------

/**
 * Seçilen varlık cinsine göre tanımlı hızlı transfer etiketlerini basar
 * @param {string} varlikCinsi - Örn: 'TL', 'USD', 'EUR', 'XAU'
 */
function akilliTagSisteminiBesle(varlikCinsi) {
    let $tagKonteyner = $('#tagKonteyner');
    let $bütçeTagAlani = $('#akilliBütçeTagAlani');
    
    // Konteyneri temizle
    $tagKonteyner.empty();
    
    // Varlık cinsine ait şablon dizisini çek
    let sablonlar = akilliTransferSablonlari[varlikCinsi] || [];
    
    if (sablonlar.length > 0) {
        $bütçeTagAlani.removeClass('d-none'); // Alanı görünür kıl
        
        $.each(sablonlar, function(index, sablon) {
            // Hizalaması ve pastel tonları milimetrik ayarlanmış hızlı buton etiketleri
            $tagKonteyner.append(`
                <span class="badge bg-secondary bg-opacity-10 text-dark border border-secondary border-opacity-20 px-2 py-1 user-select-none style-cursor akilli-transfer-tag" 
                      data-tutar="${sablon.tutar}" 
                      title="${sablon.aciklama}"
                      style="font-size: 10px; font-weight: 600; cursor: pointer; border-radius: 4px; transition: all 0.2s;">
                    <i class="fas fa-bolt text-warning me-1"></i> ${sablon.etiket} (${sablon.tutar})
                </span>`);
        });
    } else {
        $bütçeTagAlani.addClass('d-none'); // Şablon yoksa alanı gizle
    }
}

// Akıllı hızlı transfer etiketlerine tıklandığında tetiklenen veri basma motoru
$(document).on('click', '.akilli-transfer-tag', function() {
    let secilenTutar = $(this).data('tutar');
    
    // Girdi alanına yerel para formatında bas ve girdi analiz motorunu (input) tetikle
    $('#transferTutar').val(paraFormatla(secilenTutar)).trigger('input');
    
    // Seçilen etiketi görsel olarak premium mavi renge boya, diğerlerini pastel tonuna çek
    $('.akilli-transfer-tag').removeClass('bg-primary text-white bg-opacity-100').addClass('bg-secondary text-dark bg-opacity-10');
    $(this).removeClass('bg-secondary text-dark bg-opacity-10').addClass('bg-primary text-white bg-opacity-100');
});
// -------------------------------------------------------------------------
// 5. BACKEND İLETİŞİM MOTORU: AJAX Post Kabul Entegrasyonu
// -------------------------------------------------------------------------

$(document).on('click', '#btnTransferiOnayla', function() {
    let btn = $(this);
    
    // Form girdilerini ve dinamik seçilen verileri yakala
    let hedefHavuz = $('#transferHedefHavuz').val();
    let girilenTutar = paraTemizle($('#transferTutar').val());
    let cozumTuru = $('input[name="yetersizCozumTuru"]:checked').val();
    let destekHavuz = $('#transferDestekHavuz').val();

    // Emniyet Bariyerleri ve Önyüz Doğrulamaları
    if (!hedefHavuz) {
        alert('Lütfen paranın aktarılacağı hedef hesabı seçiniz.');
        return;
    }
    if (girilenTutar <= 0) {
        alert('Lütfen geçerli bir transfer tutarı veya miktarı giriniz.');
        return;
    }
    if (aktifKaynakBakiye < girilenTutar && cozumTuru === 'destekli' && !destekHavuz) {
        alert('Lütfen eksiği tamamlayacak destek havuzunu seçiniz.');
        return;
    }

    // Mükerrer (Double-Click) tıklamaları engellemek için butonu kilitle
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Havuzlar İşleniyor...');

    // Asenkron veri transferi operasyonu
    $.ajax({
        url: '', // Mevcut PHP controller dosyanızın POST kabul adresi
        type: 'POST',
        data: {
            islem: 'mevduat_transfer',
            kaynak_havuz: aktifKaynakHavuz,
            hedef_havuz: hedefHavuz,
            toplam_talep_tutari: girilenTutar,
            // Gerçek zamanlı bakiye yeterliyse doğrudan yeterli moduna kilitle
            cozum_turu: (aktifKaynakBakiye >= girilenTutar) ? 'yeterli' : cozumTuru,
            destek_havuz: destekHavuz,
            varlik_cinsi: aktifVirlikCinsi // PHP sızdırmazlık kalkanı için kritik dinamik döviz cinsi
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Sayfayı güvenle tazeleyip mizan kartlarındaki yeni bakiyeleri işleme alıyoruz
                location.reload();
            } else {
                alert('⚠️ İşlem Başarısız: ' + response.message);
                btn.prop('disabled', false).html('<i class="fas fa-check-double me-2"></i> Transferi Onayla');
            }
        },
        error: function(xhr) {
            console.error("Hata Detayı:", xhr.responseText);
            alert('❌ Sistemsel bir bağlantı hatası oluştu. Likidite havuz bütünlüğü korundu.');
            btn.prop('disabled', false).html('<i class="fas fa-check-double me-2"></i> Transferi Onayla');
        }
    });
});


    // // GLOBAL HAVUZ MATRİSİ (Controller'dan gelen $finansal_havuzlar verisini JS tarafına bağlama)
    // // Not: Bu değişkeni sayfa yüklenirken bir kez tanımlamanız yeterlidir.
    // const tumFinansalHavuzlar = <?= json_encode($finansal_havuzlar, JSON_UNESCAPED_UNICODE) ?>;

    // function forexAction(havuzKodu, opsiyonelDoviz = 'TL') {
    //     // İşlem Terminali butonuna basıldığında tetiklenen ana fonksiyon
    //     akilliTransferModaliAc(havuzKodu, opsiyonelDoviz);
    // }

    // function akilliTransferModaliAc(havuzKodu, varlikCinsi = 'TL') {
    //     // Kaynak havuzu matristen buluyoruz
    //     let kaynakHavuz = tumFinansalHavuzlar.find(h => h.havuz_kodu === havuzKodu);
    //     if (!kaynakHavuz) {
    //         alert("Emniyet Bariyeri: Kaynak havuz sistemde saptanamadı!");
    //         return;
    //     }

    //     aktifKaynakHavuz = havuzKodu;
    //     aktifVirlikCinsi = varlikCinsi.toUpperCase(); // Örn: 'USD', 'EUR', 'XAU', 'TL'

    //     // Dinamik bakiye sütun haritası
    //     let sutunHaritasi = { 'TL': 'anapara', 'USD': 'usd_bakiye', 'EUR': 'eu_bakiye', 'XAU': 'xau_bakiye' };
    //     let bakiyeSutunAdi = sutunHaritasi[aktifVirlikCinsi] || 'anapara';
        
    //     aktifKaynakBakiye = parseFloat(kaynakHavuz[bakiyeSutunAdi] ?? 0);
    //     let simge = aktifVirlikCinsi === 'TL' ? '₺' : (aktifVirlikCinsi === 'EUR' ? '€' : (aktifVirlikCinsi === 'USD' ? '$' : 'GR'));

    //     // Arayüz metinlerini milimetrik güncelleme
    //     $('#transferKaynakMetni').text(`KAYNAK HESAP: ${kaynakHavuz.havuz_adi} (${aktifVirlikCinsi} KASASI)`);
    //     $('#transferKaynakBakiyeMetni').text(`Mevcut Bakiye: ${paraFormatla(aktifKaynakBakiye)} ${simge}`);
        
    //     // Tutar girdi etiketini dinamikleştirme
    //     $('label[for="transferTutar"]').html(`<i class="fas fa-money-bill-wave me-1 text-primary"></i> Transfer Edilecek Miktar (${aktifVirlikCinsi})`);

    //     // HEDEF SEÇİCİ FİLTRELEME (Aynı döviz cinsine sahip diğer havuzları listeleme bariyeri)
    //     let $hedefSelect = $('#transferHedefHavuz');
    //     $hedefSelect.empty().append('<option value="" disabled selected>-- Hedef Havuz/Kasa Seçiniz --</option>');
        
    //     let eslesmeSayisi = 0;
    //     $.each(tumFinansalHavuzlar, function(index, havuz) {
    //         // Kendisi hariç ve hedef havuzda ilgili para birimine ait bakiye alanı mevcutsa listele
    //         if (havuz.havuz_kodu !== aktifKaynakHavuz) {
    //             let hedefBakiye = parseFloat(havuz[bakiyeSutunAdi] ?? 0);
    //             $hedefSelect.append(`<option value="${havuz.havuz_kodu}">${havuz.havuz_adi} (Mevcut: ${paraFormatla(hedefBakiye)} ${simge})</option>`);
    //             eslesmeSayisi++;
    //         }
    //     });

    //     if(eslesmeSayisi === 0) {
    //         $hedefSelect.append('<option value="" disabled>Transfer edilebilecek uygun hedef kasa bulunamadı.</option>');
    //     }

    //     // Modal sıfırlama ve gösterim
    //     $('#transferTutar').val('');
    //     $('#transferSenaryoPaneli').addClass('d-none');
    //     $('#akilliTransferModal').modal('show');
    // }

    // $(document).on('click', '#btnTransferiOnayla', function() {
    //     let btn = $(this);
    //     let hedefHavuz = $('#transferHedefHavuz').val();
    //     let girilenTutar = paraTemizle($('#transferTutar').val());

    //     if (!hedefHavuz) {
    //         alert('Lütfen hedef hesap seçimini tamamlayın.');
    //         return;
    //     }
    //     if (girilenTutar <= 0) {
    //         alert('Lütfen sıfırdan büyük geçerli bir transfer miktarı giriniz.');
    //         return;
    //     }
    //     if (girilenTutar > aktifKaynakBakiye) {
    //         alert('Emniyet Bariyeri: Kaynak hesapta talep ettiğiniz miktarda fon bulunmuyor!');
    //         return;
    //     }

    //     btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Likidite Havuzları İşleniyor...');

    //     $.ajax({
    //         url: '', // Mevcut controller sayfanız
    //         type: 'POST',
    //         data: {
    //             islem: 'mevduat_transfer',
    //             kaynak_havuz: aktifKaynakHavuz,
    //             hedef_havuz: hedefHavuz,
    //             transfer_tutari: girilenTutar,
    //             varlik_cinsi: aktifVirlikCinsi // Güvenlik doğrulaması ve dinamik sütun yönetimi için kritik
    //         },
    //         dataType: 'json',
    //         success: function(response) {
    //             if (response.success) {
    //                 // Sayfayı tazeleyip yeni bakiyeleri mizan tablosuna kuruşu kuruşuna işleme
    //                 location.reload();
    //             } else {
    //                 alert('⚠️ İşlem Başarısız: ' + response.message);
    //                 btn.prop('disabled', false).html('<i class="fas fa-check-double me-1"></i> Transferi Onayla');
    //             }
    //         },
    //         error: function(xhr) {
    //             console.error("Hata Detayı:", xhr.responseText);
    //             alert('❌ Sistemsel bir bağlantı hatası oluştu. Transfer iptal edildi, havuz bütünlüğü korundu.');
    //             btn.prop('disabled', false).html('<i class="fas fa-check-double me-1"></i> Transferi Onayla');
    //         }
    //     });
    // });



    function initPanelInputDinleyicileri() {
        const elCekilecek = document.getElementById('panel_cekilecek_hesap');
        const elAktarilacak = document.getElementById('panel_aktarilacak_hesap');
        const elManuelKur = document.getElementById('panel_manuel_kur');
        const elTutarTl = document.getElementById('panel_tutar_tl');
        const elMiktar = document.getElementById('panel_miktar');
        const radYon = document.querySelectorAll('input[name="panel_islem_yonu"]');

        if (elManuelKur) elManuelKur.addEventListener('input', () => hesaplaPanelYatirimMatematigi());
        if (elTutarTl) elTutarTl.addEventListener('input', () => hesaplaPanelYatirimMatematigi('TL'));
        if (elMiktar) elMiktar.addEventListener('input', () => hesaplaPanelYatirimMatematigi('MIKTAR'));
        
        radYon.forEach(radio => radio.addEventListener('change', dinamikArayuzYonetimi));

        document.getElementById('btn_max_tl').addEventListener('click', function() {
            const islemYonu = document.querySelector('input[name="panel_islem_yonu"]:checked').value;
            if (islemYonu === 'ALIS') {
                const opt = elCekilecek.options[elCekilecek.selectedIndex];
                if (opt && opt.value) {
                    elTutarTl.value = (parseFloat(opt.dataset.tl) || 0).toFixed(2);
                    hesaplaPanelYatirimMatematigi('TL');
                }
            }
        });

        document.getElementById('btn_max_miktar').addEventListener('click', function() {
            const islemYonu = document.querySelector('input[name="panel_islem_yonu"]:checked').value;
            if (islemYonu === 'SATIS') {
                const opt = elCekilecek.options[elCekilecek.selectedIndex];
                if (opt && opt.value) {
                    elMiktar.value = parseFloat(opt.dataset.bakiye) || 0;
                    hesaplaPanelYatirimMatematigi('MIKTAR');
                }
            }
        });

        document.getElementById('btn_panel_temizle').addEventListener('click', function() {
            document.getElementById('forexIslemForm').reset();
            document.getElementById('panel_canli_kur_text').textContent = "0,0000 ₺";
            document.getElementById('panel_maks_kapasite_text').textContent = "0,00";
            document.getElementById('panel_nihai_sonuc').value = "0,00 ₺";
            dinamikArayuzYonetimi();
        });

        // 🚀 BİRLEŞTİRİLMİŞ ASENKRON ASLA YENİLEMEYEN AJAX FORM SÜRÜCÜ MOTORU
        const form = document.getElementById('forexIslemForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Sayfa yenilenmesini kökten engelle

                const btnSubmit = document.getElementById('btn_panel_onayla');
                const eskiButonYazisi = btnSubmit ? btnSubmit.innerHTML : 'İŞLEMİ TAMAMLA';
                
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> İşlem Kaydediliyor...';
                }

                const formData = new FormData(form);
                const requestUrl = form.getAttribute('action') || window.location.href;

                fetch(requestUrl, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => { throw new Error(`Sunucu Arızası: ${response.status}\nDetay: ${text}`); });
                    }
                    return response.json();
                })

                .then(res => {
                    window.location.reload(); 
                })
                .catch(error => {
                    console.error('AJAX Error:', error);
                    // Sadece sunucu bağlantısının tamamen koptuğu (Network hatası) ekstrem durumlarda uyarır
                    alert("İletişim Hatası: Sunucu veri paketine yanıt vermedi.");
                    if (btnSubmit) {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = eskiButonYazisi;
                    }
                });
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        dinamikArayuzYonetimi(); 
        initPanelInputDinleyicileri();
    });

</script>

        
<?php require admin_view('static/footer'); ?>