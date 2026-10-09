<?php 
    ## Tags Bilgileri
    $meta = [
        'site_title' => 'Döviz ve Altın Alım-Satım 2025 Öncesi Arşivleri',
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

    <!-- ================= 1. SATIR: ANLIK VERİ ENTEGRELİ PASTEL KARTLAR (5 SÜTUNLU YAPI) ================= -->
    <div class="row g-4 mb-4 row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-5">
        <?php
            // Kartın Bootstrap Grid genişliğini ve pastel rengini el ile kilitliyoruz
            $card_class = "col-md-2 mb-4";
            $pastel_renk = "bg-warning bg-opacity-10"; // Altın için soft pastel sarı tonu

            // 🌟 FIFO ve Ağırlıklı Ortalama Analiz Verileri
            $xau_toplam_miktar = 426.96;               // Toplam İşlem Gören Altın (Gram)
            $xau_toplam_maliyet = 793972.26;           // Toplam Alış Maliyeti (TL)
            $xau_toplam_satis_geliri = 2248210.02;     // Toplam Satış Hasılatı (TL)
            $xau_kesinlesen_kar = 1454237.76;          // FIFO Yöntemine Göre Realize Net Kâr

            // 🌟 AĞIRLIKLI ORTALAMALAR
            $xau_ort_alis_kuru = 1859.59;              // Gram Başına Ağırlıklı Ortalama Alış Kuru
            $xau_ort_satis_kuru = 5265.62;             // Gram Başına Ağırlıklı Ortalama Satış Fiyatı

            // Kartın canlı/anlık bakiye gösterimi (Tüm lotlar satıldığı için 0,00 GR)
            $mevcut_stok_gr = 0.00; 

        ?>
        <!-- 1.KART -->
        <div class="<?= $card_class ?>">
            <div class="card h-100 shadow-sm border border-light-subtle rounded-3 overflow-hidden <?= $pastel_renk ?>" style="border-radius: 12px;">
                <div class="card-body d-flex flex-column justify-content-between p-4">
                    
                    <!-- 1. Üst Şerit Başlığı -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3 text-uppercase">
                            <span class="small fw-bold tracking-wider text-dark opacity-90" style="font-size: 11px; letter-spacing: 0.5px;">
                                <i class="fas fa-coins me-1 text-warning"></i> ALTIN PORTFÖYÜ (XAU)
                            </span>
                            <span class="fs-4">🟡</span>
                        </div>
                        
                        <!-- Birim Simgeli Konsolide Canlı Bakiye (Stok Durumu) -->
                        <h2 class="fw-bold mb-1 font-monospace text-dark text-uppercase" style="letter-spacing: -0.5px;">
                            <?= number_format($mevcut_stok_gr, 2, ',', '.') ?> GR
                        </h2>
                        
                        <!-- 2. Orta Maliyet Detay İstasyonu -->
                        <div class="d-flex flex-column gap-2 mb-3 bg-white bg-opacity-50 p-3 rounded-3 border border-white border-opacity-70 text-uppercase" style="font-size: 11px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted fw-semibold">ALINAN MİKTAR</span>
                                <span class="fw-bold text-dark font-monospace"><?= number_format($xau_toplam_miktar, 2, ',', '.') ?> GR</span>
                            </div>               
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted fw-semibold">ORT. ALIŞ BİRİM MALİYETİ:</span>
                                <span class="fw-bold text-dark font-monospace"><?= number_format($xau_ort_alis_kuru, 2, ',', '.') ?> ₺</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted fw-semibold">TOPLAM ALIŞ MALİYETİ:</span>
                                <span class="fw-bold text-primary font-monospace"><?= number_format($xau_toplam_maliyet, 2, ',', '.') ?> ₺</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Alt Kur Gösterge ve Değerleme Terminali -->
                    <div>
                        <div class="bg-white bg-opacity-70 rounded-3 p-3 mb-3 border border-white border-opacity-90 text-dark text-uppercase" style="font-size: 11px;">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 border-secondary border-opacity-10">
                                <span class="text-muted">SATILAN MİKTAR</span>
                                <span class="fw-bold font-monospace text-dark"><?= number_format($xau_toplam_miktar, 2, ',', '.') ?> GR</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 border-secondary border-opacity-10">
                                <span class="text-muted">ORT. SATIŞ BİRİM FİYATI:</span>
                                <span class="fw-bold font-monospace text-dark"><?= number_format($xau_ort_satis_kuru, 2, ',', '.') ?> ₺</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">TOPLAM SATIŞ HASILATI:</span>
                                <span class="fw-bold font-monospace text-dark"><?= number_format($xau_toplam_satis_geliri, 2, ',', '.') ?> ₺</span>
                            </div>
                        </div>
                        
                        <div class="card-divider my-2 border-bottom border-secondary border-opacity-10"></div>
                        

                        <div class="d-flex justify-content-between align-items-center small mb-3 text-muted text-uppercase" style="font-size: 11px;">
                            <span>NET KAZANÇ</span>
                            <span class="fw-bold px-2 py-2 rounded bg-success bg-opacity-10 text-success font-monospace border border-success border-opacity-20" style="font-size: 14px;">
                                +<?= number_format($xau_kesinlesen_kar, 2, ',', '.') ?> ₺
                            </span>
                        </div>

                    </div>
                    
                </div>
            </div>
        </div>

        <div class="<?= $card_class ?>">
            <div class="card h-100 shadow-sm border border-light-subtle rounded-3 overflow-hidden bg-warning bg-opacity-10" style="border-radius: 12px;">
                <div class="card-body d-flex flex-column justify-content-between p-4">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3 text-uppercase">
                            <span class="small fw-bold tracking-wider text-dark opacity-90" style="font-size: 11px; letter-spacing: 0.5px;">
                                <i class="fas fa-calendar-alt me-1 text-warning"></i> XAU DÖNEMSEL ALIMLAR
                            </span>
                            <span class="fs-4">📅</span>
                        </div>
                        <h2 class="fw-bold mb-1 font-monospace text-dark text-uppercase" style="letter-spacing: -0.5px;">
                            DÖNEM ÖZETİ
                        </h2>
                        
                        <div class="d-flex flex-column gap-2 mb-3 bg-white bg-opacity-50 p-3 rounded-3 border border-white border-opacity-70 text-uppercase" style="font-size: 11px; max-height: 220px; overflow-y: auto;">
                            <?php if (!empty($xau_donemsel_alimlar)): ?>
                                <?php foreach ($xau_donemsel_alimlar as $donem => $veri): ?>
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-1.5 mb-1.5 border-secondary border-opacity-5">
                                        <span class="text-dark fw-bold font-monospace"><?= $donem ?></span>
                                        <span class="fw-bold text-dark font-monospace">
                                            <?= number_format($veri['miktar'], 2, ',', '.') ?> GR / <?= number_format($veri['maliyet'], 2, ',', '.') ?> ₺
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted small py-3">Bu periyotta alım kaydı bulunmuyor.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <div class="card-divider my-2 border-bottom border-secondary border-opacity-10"></div>
                        <div class="d-flex justify-content-between align-items-center small mb-3 text-muted text-uppercase" style="font-size: 11px;">
                            <span>TOPLAM ALIM TUTARI</span>
                            <span class="fw-bold px-2 py-2 rounded bg-primary bg-opacity-10 text-primary font-monospace border border-primary border-opacity-20" style="font-size: 14px;">
                                <?= number_format(($xau_toplam_maliyet ?? 793972.26), 2, ',', '.') ?> ₺
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="<?= $card_class ?>">
            <div class="card h-100 shadow-sm border border-light-subtle rounded-3 overflow-hidden bg-info bg-opacity-10" style="border-radius: 12px;">
                <div class="card-body d-flex flex-column justify-content-between p-4">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3 text-uppercase">
                            <span class="small fw-bold tracking-wider text-dark opacity-90" style="font-size: 11px; letter-spacing: 0.5px;">
                                <i class="fas fa-university me-1 text-info"></i> MEVDUAT KÂR/ZARAR MİZANI
                            </span>
                            <span class="fs-4">🏦</span>
                        </div>
                        <h2 class="fw-bold mb-1 font-monospace text-dark text-uppercase" style="letter-spacing: -0.5px;">
                        <?= number_format(('161876.02' ?? 0), 2, ',', '.') ?> ₺
                        </h2>
                        <div class="bg-white bg-opacity-70 rounded-3 p-3 mb-3 border border-white border-opacity-90 text-dark text-uppercase" style="font-size: 11px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">KASADAKİ SERMAYE (ANAPARA):</span>
                                <span class="fw-bold font-monospace text-dark"><?= number_format(('57773.66' ?? 0), 2, ',', '.') ?> ₺</span>
                            </div>
                        </div>


                        <div class="bg-white bg-opacity-70 rounded-3 p-3 mb-3 border border-white border-opacity-90 text-dark text-uppercase" style="font-size: 11px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">EKLENEN TUTAR</span>
                                <span class="fw-bold font-monospace text-dark"><?= number_format($mev_toplam_eklenen, 2, ',', '.') ?> ₺</span>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-2 mb-3 bg-white bg-opacity-50 p-3 rounded-3 border border-white border-opacity-70 text-uppercase" style="font-size: 11px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted fw-semibold">TOPLAM BRÜT FAİZ GETİRİSİ</span>
                                <span class="fw-bold text-success font-monospace">+ <?= number_format(($mev_toplam_brut ?? 0), 2, ',', '.') ?> ₺</span>
                            </div>               
                            <div class="d-flex justify-content-between align-items-center border-top pt-1.5 border-secondary border-opacity-10">
                                <span class="text-muted fw-semibold">GELİR VERGİSİ KESİNTİSİ (STOPAJ)</span>
                                <span class="fw-bold text-danger font-monospace">- <?= number_format(($mev_toplam_vergi ?? 0), 2, ',', '.') ?> ₺</span>
                            </div>
                        </div>
                        <div class="card-divider my-2 border-bottom border-secondary border-opacity-10"></div>
                        <div class="d-flex justify-content-between align-items-center small mb-3 text-muted text-uppercase" style="font-size: 11px;">
                            <span>NET FAİZ KAZANCI</span>
                            <span class="fw-bold px-2 py-2 rounded bg-success bg-opacity-10 text-success font-monospace border border-success border-opacity-20" style="font-size: 14px;">
                                + <?= number_format(($mev_toplam_net ?? 0), 2, ',', '.') ?> ₺
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="<?= $card_class ?>">
            <div class="card h-100 shadow-sm border border-light-subtle rounded-3 overflow-hidden bg-success bg-opacity-10" style="border-radius: 12px;">
                <div class="card-body d-flex flex-column justify-content-between p-4">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3 text-uppercase">
                            <span class="small fw-bold tracking-wider text-dark opacity-90" style="font-size: 11px; letter-spacing: 0.5px;">
                                <i class="fas fa-exchange-alt me-1 text-success"></i> MEVDUAT DÖNEMSEL AKIŞLAR
                            </span>
                            <span class="fs-4">📊</span>
                        </div>
                        <h2 class="fw-bold mb-1 font-monospace text-dark text-uppercase" style="letter-spacing: -0.5px;">
                        </h2>
                        
                        <div class="d-flex justify-content-between small text-muted fw-bold px-1 mb-1 text-center" style="font-size: 9px;">
                            <div style="width: 35%; text-align: left;">DÖNEM</div>
                            <div style="width: 20%;">EKLENEN TUTAR</div>
                            <div style="width: 20%;">NET FAİZ GELİRİ</div>
                            <div style="width: 25%; text-align: right;">TOPLAM AKIŞ</div>
                        </div>

                        <div class="d-flex flex-column gap-2 mb-3 bg-white bg-opacity-50 p-2.5 rounded-3 border border-white border-opacity-70 text-uppercase" style="font-size: 10px; max-height: 300px; overflow-y: none;">
                            <?php if(!empty($mev_aylik_akislar)): ?>
                                <?php foreach($mev_aylik_akislar as $akis): ?>
                                    <div class="d-flex justify-content-between align-items-center font-monospace border-bottom pb-1 mb-1 border-secondary border-opacity-5">
                                        <span class="text-muted fw-semibold text-start" style="width: 35%;"><?= $akis['donem'] ?></span>
                                        <span class="text-primary text-center" style="width: 20%;"><?= number_format($akis['eklenen'], 0, ',', '.') ?> ₺</span>
                                        <span class="text-success text-center" style="width: 20%;"><?= number_format($akis['net_faiz'], 0, ',', '.') ?> ₺</span>
                                        <span class="fw-bold text-dark text-end" style="width: 25%;"><?= number_format($akis['toplam'], 2, ',', '.') ?> ₺</span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted small py-3">Akış verisi bulunmuyor.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>

                    </div>
                </div>
            </div>
        </div>
        <div class="<?= $card_class ?>">
            <div class="card h-100 shadow-sm border border-light-subtle rounded-3 overflow-hidden bg-secondary bg-opacity-10" style="border-radius: 12px;">
                <div class="card-body d-flex flex-column justify-content-between p-4">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3 text-uppercase">
                            <span class="small fw-bold tracking-wider text-dark opacity-90" style="font-size: 11px; letter-spacing: 0.5px;">
                                <i class="fas fa-chart-bar me-1 text-secondary"></i> YIL BAZLI PORTFÖY PERFORMANSI
                            </span>
                            <span class="fs-4">📈</span>
                        </div>
                        <h2 class="fw-bold mb-1 font-monospace text-dark text-uppercase" style="letter-spacing: -0.5px;">
                            YILLIK KAZANÇ
                        </h2>
                        
                        <div class="d-flex flex-column gap-2 mb-3 bg-white bg-opacity-50 p-3 rounded-3 border border-white border-opacity-70 text-uppercase" style="font-size: 11px;">
                            <?php if(!empty($yillik_konsolide_kazanc)): ?>
                                <?php foreach($yillik_konsolide_kazanc as $yil => $kazanc): ?>
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-1 border-secondary border-opacity-5">
                                        <span class="text-muted fw-semibold"><?= $yil ?> REEL KAZANÇ</span>
                                        <span class="fw-bold text-success font-monospace">+ <?= number_format($kazanc, 2, ',', '.') ?> ₺</span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted small py-3">Yıllık veri bulunmuyor.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <div class="bg-white bg-opacity-70 rounded-3 p-3 mb-3 border border-white border-opacity-90 text-dark text-uppercase" style="font-size: 11px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">MALİYET:</span>
                                <span class="fw-bold font-monospace text-dark"><?= number_format(($xau_toplam_maliyet + $mev_toplam_anapara), 2, ',', '.') ?> ₺</span>
                            </div>
                        </div>
                        <div class="card-divider my-2 border-bottom border-secondary border-opacity-10"></div>
                        <div class="d-flex justify-content-between align-items-center small mb-3 text-muted text-uppercase" style="font-size: 11px;">
                            <span>TOPLAM NET KÂR</span>
                            <span class="fw-bold px-2 py-2 rounded bg-success text-white font-monospace border-0" style="font-size: 14px; background: linear-gradient(135deg, #198754 0%, #157347 100%);">
                                + <?= number_format(($xau_toplam_satis_geliri - $xau_toplam_maliyet + $mev_toplam_net), 2, ',', '.') ?> ₺
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </div>



    <!-- ================= 2. SATIR: FORM VE TABLOLAR ================= -->
    <div class="container-fluid px-4 py-4">
        <div class="row g-4 mb-4">    

            <?php foreach ($yatirim_araci as $kod => $liste): ?>
            <?php
                if (empty($liste)) continue;
                $is_mevduat = (strtoupper($kod) === 'MEV');           
                
                if ($is_mevduat) {
                    $kart_basligi = "Vadeli Mevduat Hesapları (MEV)";
                    $ondalik_hane = 3;
                    
                    // MEV Sütun Metinleri
                    $vade_basi = "Vade Başlangıç";
                    $vade_sonu = "Vade Bitiş";
                    $islem_turu = "Tür";
                    $anapara = "Anapara";
                    $faiz_orani = "Faiz Oranı";
                    $brut_faiz = "Brüt Faiz";
                    $vergi = "Stopaj";
                    $net_faiz = "Net Faiz";
                    $eklenen_tutar = "Eklenen Tutar";
                    $son_bakiye = "Bakiye";
                    $kumulatif_toplam = "Kümülatif Toplam (TL)";
                } else {
                    $kart_basligi = "Altın İşlemleri (XAU)";
                    $ondalik_hane = 2;
                    
                    // XAU Sütun Metinleri
                    $vade_basi = "Tarih";
                    $islem_turu = "İşlem Türü";
                    $b_miktar = "Miktar";
                    $bakiye_gram = "Bakiye (Gr)";
                    $doviz_kuru = "Kur";
                    $tutar = "Tutar";
                    $vergi = "Vergi";
                    $maliyet = "Maliyet";
                    $kumulatif_toplam = "Kümülatif Toplam (TL)";
                }
            ?>
            
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm border-0 premium-card-table h-100">
                    <div class="card-header bg-dark py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-white">
                            <i class="<?= $is_mevduat ? 'fas fa-university' : 'fas fa-coins' ?> me-2 text-warning"></i>
                            <?= htmlspecialchars($kart_basligi) ?>
                        </h5>
                        <span class="badge bg-primary text-white p-2"><?= count($liste) ?> Kayıt</span>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped align-middle text-end mb-0 fx-custom-font">
                                <thead class="small text-uppercase">
                                <?php if ($is_mevduat): ?>
                                    <!-- MEV SÜTUN TOPLAMLARI SATIRI -->
                                    <tr class="fw-bold table-warning text-dark border-bottom">
                                        <td class="text-start ps-3 py-2"><i class="fas fa-calculator me-1"></i> TOPLAMLAR</td>
                                        <td class="text-center py-2">-</td>
                                        <td class="text-center py-2">-</td>
                                        <td class="text-center py-2">-</td>
                                        <td class="text-end py-2">-</td>
                                        <td class="text-center py-2">-</td>
                                        <td class="text-end text-success py-2">+ <?= number_format($mev_toplam_brut, 2, ',', '.') ?> ₺</td>
                                        <td class="text-end text-danger py-2">- <?= number_format($mev_toplam_vergi, 2, ',', '.') ?> ₺</td>
                                        <td class="text-end text-success py-2 fw-bold">+ <?= number_format($mev_toplam_net, 2, ',', '.') ?> ₺</td>
                                        <td class="text-end text-primary py-2">+ <?= number_format($mev_toplam_eklenen, 2, ',', '.') ?> ₺</td>
                                        <td class="text-end pe-3 py-2">-</td>
                                        <td class="text-end pe-3 py-2">-</td>
                                    </tr>
                                    <tr class="fw-bold table-dark text-white border-bottom">
                                        <td class="text-start ps-3 py-2"><?= $vade_basi ?></td>
                                        <td class="text-center py-2"><?= $vade_sonu ?></td>
                                        <td class="text-center py-2">Gün</td>
                                        <td class="text-center py-2"><?= $islem_turu ?></td>
                                        <td class="text-end py-2"><?= $anapara ?></td>
                                        <td class="text-center py-2"><?= $faiz_orani ?></td>
                                        <td class="text-end py-2"><?= $brut_faiz ?></td>
                                        <td class="text-end py-2"><?= $vergi ?></td>
                                        <td class="text-end py-2"><?= $net_faiz ?></td>
                                        <td class="text-end py-2"><?= $eklenen_tutar ?></td>
                                        <td class="text-end pe-3 fw-bold py-2"><?= $son_bakiye ?></td>
                                        <td class="text-end pe-3 fw-bold py-2"><?= $kumulatif_toplam ?></td>
                                    </tr>
                                <?php else: ?>
                                    <!-- XAU PORTFÖYÜ FIFO YÖNTEMİ ANALİZ SATIRI -->
                                    <tr class="fw-bold table-info text-dark border-bottom">
                                        <td class="text-start ps-3 py-2"><i class="fas fa-history me-1"></i> FIFO MATEMATİĞİ</td>
                                        <td class="text-end py-2" colspan="2">Toplam Alış Maliyeti: <span class="text-primary"><?= number_format($xau_toplam_maliyet, 2, ',', '.') ?> ₺</span></td>
                                        <td class="text-end py-2" colspan="2">Toplam Satış Hasılatı: <span class="text-success"><?= number_format($xau_toplam_satis_geliri, 2, ',', '.') ?> ₺</span></td>
                                        <td class="text-center py-2" colspan="2">Portföy Durumu: <span class="badge bg-secondary">TASFİYE EDİLDİ (0 Gr)</span></td>
                                        <td class="text-end py-2">Net FIFO Realize Kâr:</td>
                                        <td class="text-end pe-3 py-2 font-monospace fw-bold text-success">+ <?= number_format($xau_kesinlesen_kar, 2, ',', '.') ?> ₺</td>
                                    </tr>
                                    <tr class="fw-bold table-dark text-white border-bottom">
                                        <td class="text-start ps-3 py-2"><?= $vade_basi ?></td> <td class="text-center py-2"><?= $islem_turu ?></td> <td class="text-end py-2"><?= $b_miktar ?></td> <td class="text-end py-2"><?= $bakiye_gram ?></td> <td class="text-center py-2"><?= $doviz_kuru ?></td> <td class="text-end py-2"><?= $tutar ?></td> <td class="text-end py-2"><?= $vergi ?></td> <td class="text-end py-2"><?= $maliyet ?></td> <td class="text-end pe-3 fw-bold py-2"><?= $kumulatif_toplam ?></td>
                                    </tr>
                                <?php endif; ?>
                                </thead>
                                <tbody>
                                <?php foreach ($liste as $islem): 
                                    $islem_turu_ham = $islem['islem_turu'] ?? 2;
                                    
                                    // Güvenlik Duvarı: Dinamik Mevduat Veri Tamamlama İstasyonu 🌟
                                    if ($is_mevduat) {
                                        $faiz_gun = 0;
                                        if (!empty($islem['vade_basi']) && !empty($islem['islem_tarihi'])) {
                                            $tarih_basi = new DateTime($islem['vade_basi']);
                                            $tarih_sonu = new DateTime($islem['islem_tarihi']);
                                            $faiz_gun = $tarih_basi->diff($tarih_sonu)->days;
                                        }
                                        // Ham dizide eksik olan veya NULL gelebilecek tüm alanlar koruma altına alındı
                                        $islem['faiz_gun']      = $faiz_gun;
                                        $islem['anapara']       = (float)($islem['miktar'] ?? 0);
                                        $islem['faiz_orani']    = (float)($islem['doviz_kuru'] ?? 0);
                                        $islem['brut_faiz']     = (float)($islem['tutar'] ?? 0);
                                        $islem['vergi']         = (float)($islem['vergi'] ?? 0);
                                        $islem['net_faiz']      = $islem['brut_faiz'] - $islem['vergi'];
                                        $islem['eklenen_tutar'] = (float)($islem['eklenen_tutar'] ?? 0);
                                        $islem['son_bakiye']    = (float)($islem['maliyet'] ?? 0) + $islem['eklenen_tutar'];
                                    }
                                    
                                    if ($islem_turu_ham == 4) {
                                        $yazi_sinifi = 'text-fx-alis'; $badge_sinifi = 'border-info text-info'; $tur_metni = 'Mevduat'; $isaret = '+';
                                    } elseif ($islem_turu_ham == 2) {
                                        $yazi_sinifi = 'text-fx-alis'; $badge_sinifi = 'border-success text-success'; $tur_metni = 'Alış'; $isaret = '+';
                                    } elseif ($islem_turu_ham == 1) {
                                        $yazi_sinifi = 'text-fx-cikis'; $badge_sinifi = 'border-danger text-danger'; $tur_metni = 'Ödeme'; $isaret = '-';
                                    } elseif ($islem_turu_ham == 0) {
                                        $yazi_sinifi = 'text-fx-cikis'; $badge_sinifi = 'border-warning text-warning'; $tur_metni = 'Satış'; $isaret = '-';
                                    } else {
                                        $yazi_sinifi = 'text-fx-cikis'; $badge_sinifi = 'border-secondary text-secondary'; $tur_metni = 'N.Harcama'; $isaret = '-';
                                    }
                                    
                                    $tarih_kaynagi = $is_mevduat ? ($islem['vade_sonu'] ?? $islem['islem_tarihi'] ?? '') : ($islem['islem_tarihi'] ?? '');
                                    $is_devir = (!empty($tarih_kaynagi) && date('m-d', strtotime($tarih_kaynagi)) === '01-01');
                                    $satir_sinifi = $is_devir ? 'table-secondary text-muted' : 'table-light';
                                ?>
                                    <tr class="<?= $satir_sinifi ?> <?= $yazi_sinifi ?>">
                                    
                                    <?php if ($is_mevduat): ?>
                                        <!-- ================= MEVDUAT SATIRI (11 KOLON) ================= -->
                                        <td class="text-start ps-3 font-monospace text-dark"><?= !empty($islem['vade_basi']) ? date('d.m.Y', strtotime($islem['vade_basi'])) : '-' ?></td>
                                        <td class="text-center font-monospace text-dark"><?= !empty($islem['islem_tarihi']) ? date('d.m.Y', strtotime($islem['islem_tarihi'])) : '-' ?></td>
                                        <td class="text-center fw-bold text-dark font-monospace"><?= number_format($islem['faiz_gun'], 0, ',', '.') ?></td>
                                        <td class="text-center"><span class="badge border bg-white badge-fx <?= $badge_sinifi ?>"><?= $tur_metni ?></span></td>
                                        <td class="fw-bold text-dark"><?= number_format($islem['anapara'], 2, ',', '.') ?> TL</td>
                                        <td class="text-dark text-center">%<?= number_format($islem['faiz_orani'], $ondalik_hane, ',', '.') ?></td>
                                        <td class="text-success">+ <?= number_format($islem['brut_faiz'], 2, ',', '.') ?> ₺</td>
                                        <td class="text-danger">- <?= number_format($islem['vergi'], 2, ',', '.') ?> ₺</td>
                                        <td class="fw-bold text-success">+ <?= number_format($islem['net_faiz'], 2, ',', '.') ?> ₺</td>
                                        <td class="text-primary">+ <?= number_format($islem['eklenen_tutar'], 2, ',', '.') ?> ₺</td>
                                        <td class="fw-bold text-success"></td>
                                        <td class="pe-3 fw-bold text-dark"><?= number_format($islem['son_bakiye'], 2, ',', '.') ?> ₺</td>
                                    <?php else: ?>
                                        <!-- ================= XAU SATIRI (9 KOLON) ================= -->
                                        <td class="text-start ps-3 font-monospace text-dark">
                                            <?= !empty($islem['islem_tarihi']) ? date('d.m.Y', strtotime($islem['islem_tarihi'])) : '-' ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge border bg-white badge-fx <?= $badge_sinifi ?>"><?= $tur_metni ?></span>
                                        </td>
                                        <td class="fw-bold">
                                            <?= $isaret ?> <?= number_format(abs($islem['miktar']), 2, ',', '.') ?> Gram
                                        </td> 
                                        <td class="fw-bold text-muted font-monospace bg-white bg-opacity-25">
                                            <?= number_format($islem['anlik_bakiye'], 2, ',', '.') ?> Gram
                                        </td>
                                        <td class="text-dark text-center">
                                            <?= number_format($islem['doviz_kuru'], 2, ',', '.') ?> ₺
                                        </td>
                                        <td>
                                            <?= $isaret ?> <?= number_format(abs($islem['tutar']), 2, ',', '.') ?> ₺
                                        </td>
                                        <td class="text-secondary">
                                            <?= number_format($islem['vergi'], 2, ',', '.') ?> ₺
                                        </td>
                                        <td class="fw-bold text-dark">
                                            <?= number_format($islem['maliyet'], 2, ',', '.') ?> ₺
                                        </td>
                                        <td class="pe-3 fw-bold text-primary font-monospace">
                                            <?= $islem['kumulatif_tl'] <= 0 ? '0,00' : number_format($islem['kumulatif_tl'], 2, ',', '.') ?> ₺
                                        </td>
                                    <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>


        </div> <!-- /.container-fluid -->


    </div>


</div>





<script>


    // 1. PARA FONKSİYON VE FORMATLAR

    const TR_PARA_FORMATI = new Intl.NumberFormat('tr-TR', { 
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2 
    });

    // 🎯 SIZDIRMAZ PARA TEMİZLEME FONKSİYONU
    // function paraTemizle(deger) {
    //     if (deger === null || deger === undefined) return 0;
        
    //     // Girdiyi stringe çevir ve sağındaki/solundaki boşlukları temizle
    //     let temiz = deger.toString().trim();
    //     if (!temiz) return 0;

    //     // Sadece rakamlar, virgül, nokta ve eksi işaretini tut
    //     temiz = temiz.replace(/[^0-9,.-]/g, '');

    //     // Eğer sayı standart Türkçe formatındaysa (Binlik ayraç nokta, kuruş virgül):
    //     // Örn: "1.250,50" -> Noktaları sil, virgülü noktaya çevir
    //     if (temiz.includes(',') && temiz.includes('.')) {
    //         temiz = temiz.replace(/\./g, '').replace(',', '.');
    //     } 
    //     // Eğer sadece tek bir bilyon/kuruş ayracı olarak virgül kullanıldıysa:
    //     // Örn: "1250,50" -> Virgülü noktaya çevir
    //     else if (temiz.includes(',')) {
    //         temiz = temiz.replace(',', '.');
    //     }

    //     const sonuc = parseFloat(temiz);
    //     return isNaN(sonuc) ? 0 : sonuc; // NaN çökme koruması
    // }

    function paraTemizle(deger) {
        if (deger === null || deger === undefined) return 0;
        let temiz = deger.toString().trim();
        if (!temiz) return 0;
        
        // Eğer girdi "1.250,50" gibi klasik TR formatındaysa noktayı sil, virgülü noktaya çevir
        if (temiz.includes(',') && temiz.includes('.')) {
            temiz = temiz.replace(/\./g, '').replace(',', '.');
        } 
        // Eğer sadece "1250,50" girdiyse virgülü noktaya çevir
        else if (temiz.includes(',')) {
            temiz = temiz.replace(',', '.');
        }
        // Eğer girdi doğrudan saf float ise ("1500.50") dokunmadan geçecek.

        temiz = temiz.replace(/[^0-9.-]/g, '');
        const sonuc = parseFloat(temiz);
        return isNaN(sonuc) ? 0 : sonuc;
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

</script>

        
<?php require admin_view('static/footer'); ?>