<?php 
    ## Tags Bilgileri
    $meta = [
        'site_title' => 'Yatırım Paneli | Bütçe Yönetimi',
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
        font-size: 14px !important; /* İstediğiniz boyuta (örn: 13px, 15px) buradan çekebilirsiniz */
        font-weight: 500;
    }
    .fx-custom-font th { font-weight: 700; letter-spacing: 0.3px; }
    
    /* Net Yazı Rengi Vurguları (Arka planlar soft/light kalacak) */
    .text-fx-alis { color: #198754 !important; }  /* Finansal Canlı Yeşil Yazı */
    .text-fx-cikis { color: #dc3545 !important; } /* Finansal Canlı Kırmızı Yazı */
    
    .badge-fx { font-size: 11px !important; padding: 4px 6px; font-weight: 700; text-transform: uppercase; }


</style>

<div class="container-fluid">

    <div class="container-fluid px-4 py-4">

       <!-- ========================================================================= -->
        <!-- EN ÜST SATIR: ÇİFT HAVUZLU DÜŞÜMLÜ STRATEJİK NAKİT AKIŞ PANELİ -->
        <!-- ========================================================================= -->
        <div class="row g-4 mb-4 row-cols-1 row-cols-md-2 row-cols-xl-3">
            
            <!-- KART 1: DÖNEMSEL NET VARLIK GÜCÜ (DİNAMİK AY) -->
            <div class="col">
                <div class="card h-100 pastel-card p-2" style="background-color: #f0f4fe !important; color: #1e3a8a !important;">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-uppercase small fw-bold tracking-wider opacity-75">Dönemsel Durum (<?= $guncel_ay_donem ?>)</span>
                                <span class="fs-5">📅</span>
                            </div>
                            <h3 class="fw-bold mb-1"><?= number_format($guncel_ay_net_tasarruf_tl, 2, ',', '.') ?> ₺</h3>
                            <p class="small mb-0 text-dark opacity-75 fw-semibold">Aktif Dönem Net Tasarruf Gücü</p>
                        </div>
                        
                        <div class="card-divider"></div>
                        
                        <div class="fx-table-text text-dark">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Dönem Yapısal Geliri:</span>
                                <span class="fw-bold font-monospace"><?= number_format($guncel_ay_gelir, 2, ',', '.') ?> ₺</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Cari Giderler Toplamı:</span>
                                <span class="fw-bold font-monospace text-danger"><?= number_format($guncel_ay_gider, 2, ',', '.') ?> ₺</span>
                            </div>
                            <!-- YENİ: Kredi Kartı ve Tasfiye Harcamaları Cari Bütçeden Ayrı Düştü -->
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Sermaye / KK Döviz Gideri:</span>
                                <span class="fw-bold font-monospace text-danger"><?= number_format($guncel_ay_sermaye_gideri_tl, 2, ',', '.') ?> ₺</span>
                            </div>
                            <div class="d-flex justify-content-between pt-1 border-top border-dashed">
                                <span class="text-muted small fw-bold">Harici Faiz Kazanımı:</span>
                                <span class="fw-bold font-monospace text-primary">+ <?= number_format($guncel_ay_faiz, 2, ',', '.') ?> ₺</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KART 2: KONSOLİDE SERMAYE GÜCÜ -->
            <div class="col">
                <div class="card h-100 pastel-card p-2" style="background-color: #e0f2fe !important; color: #0369a1 !important;">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-uppercase small fw-bold tracking-wider opacity-75">Konsolide Sermaye Gücü</span>
                                <span class="fs-4">📊</span>
                            </div>
                            <h2 class="fw-bold mb-1"><?= number_format($yilsal_net_tasarruf_tl, 2, ',', '.') ?> ₺</h2>
                            <p class="small mb-0 text-dark opacity-75 fw-semibold">Yıllık Durum (2026)</p>
                        </div>
                        
                        <div class="card-divider"></div>
                        
                        <div class="fx-table-text text-dark">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Yıllık Gelir:</span>
                                <span class="fw-bold font-monospace"><?= number_format($toplam_yilsal_gelir, 2, ',', '.') ?> ₺</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Yıllık Gider:</span>
                                <span class="fw-bold font-monospace text-danger"><?= number_format($toplam_yilsal_gider, 2, ',', '.') ?> ₺</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Kümule Sermaye Gideri:</span>
                                <span class="fw-bold font-monospace text-danger"><?= number_format($toplam_sermaye_gideri_tl, 2, ',', '.') ?> ₺</span>
                            </div>
                            <div class="d-flex justify-content-between pt-1 border-top border-dashed">
                                <span class="text-muted small fw-bold">Faiz Gelir:</span>
                                <span class="fw-bold font-monospace text-success">+ <?= number_format($toplam_yilsal_faiz, 2, ',', '.') ?> ₺</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KART 3: YATIRIM BÜTÇE BAŞARISI VE SIZINTI ANALİZİ -->
            <div class="col">
                <?php 
                    // Pozitif (+) ise yatırıma dönüşmeyen atıl potansiyel (sızıntı), Negatif (-) ise bütçe optimizasyon başarısı
                    if ($gercek_finansal_sizinti_tl >= 0) {
                        $analiz_bg = '#fff9db';
                        $analiz_color = '#856404';
                        $badge_metni = 'Atıl Potansiyel';
                        $badge_sinifi = 'bg-warning text-dark';
                        $durum_aciklamasi = 'Yatırıma Dönüşmeyen Tutar Var';
                    } else {
                        $analiz_bg = '#e8f5e9';
                        $analiz_color = '#1b5e20';
                        $badge_metni = 'Maksimum Verim';
                        $badge_sinifi = 'bg-success text-white';
                        $durum_aciklamasi = 'Gelirin Üzerinde Yatırım Optimizasyonu';
                    }
                ?>
                <div class="card h-100 pastel-card p-2" style="background-color: <?= $analiz_bg ?> !important; color: <?= $analiz_color ?> !important;">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-uppercase small fw-bold tracking-wider opacity-75">Yatırım Bütçe Başarısı</span>
                                <span class="badge <?= $badge_sinifi ?> fw-bold" style="font-size: 10px; text-transform: uppercase;">
                                    <?= $badge_metni ?>
                                </span>
                            </div>
                            <h2 class="fw-bold mb-1"><?= number_format($gercek_finansal_sizinti_tl, 2, ',', '.') ?> ₺</h2>
                            <p class="small mb-0 text-dark opacity-75 fw-semibold"><?= $durum_aciklamasi ?></p>
                        </div>
                        
                        <div class="card-divider"></div>
                        
                        <div class="fx-table-text text-dark">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Enstrüman Alış Maliyet Toplamı:</span>
                                <span class="fw-bold font-monospace text-dark"><?= number_format($enstruman_ham_alis_toplami_tl, 2, ',', '.') ?> ₺</span>
                            </div>
                            <div class="d-flex justify-content-between pt-1 border-top border-dashed">
                                <span class="text-muted small fw-bold">Yatırıma Atanma Oranı:</span>
                                <span class="fw-bold font-monospace text-primary">% <?= number_format($yatirima_atenma_orani, 1, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div> <!-- Üst Kartlar Satırı Sonu -->

        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 premium-card-table">
                    <div class="card-header bg-dark py-3">
                        <h5 class="fw-bold mb-0 text-white">💳 Sermaye ve Kredi Kartı (KK) Döviz Harcamaları Raporu</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <!-- Sizin .table-sm, table-bordered ve harici yazı boyutu sınıfınız -->
                            <table class="table table-sm table-bordered table-striped align-middle text-end mb-0 fx-custom-font">
                                <thead class="table-light text-dark fw-bold">
                                    <tr>
                                        <td class="text-start ps-3 py-2">DÖNEM DETAYI</td>
                                        <td class="text-center font-monospace"><?= $guncel_ay_donem ?></td>
                                        <td colspan="2" class="text-start ps-4">AKTİF DÖNEM TOPLAM SERMAYE GİDERİ:</td>
                                        <td class="pe-3 text-danger fw-bold"><?= number_format($guncel_ay_sermaye_gideri_tl, 2, ',', '.') ?> ₺</td>
                                    </tr>
                                </thead>
                                <thead class="table-primary text-dark">
                                    <tr>
                                        <th class="text-start ps-3 py-2">Harcama Adı</th>
                                        <th class="text-center">Harcama Türü</th>
                                        <th>Miktar / Varlık</th>
                                        <th>İşlem Kuru</th>
                                        <th class="pe-3">Tutar (TL)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($sermaye_harcamalari_listesi_yillik)): ?>
                                    <?php foreach ($sermaye_harcamalari_listesi_yillik as $sh): ?>
                                        <tr class="table-light text-fx-cikis">
                                            <td class="text-start ps-3 font-weight-bold text-dark"><?= $sh['harcama_adi'] ?></td>
                                            <td class="text-center">
                                                <span class="badge border bg-white badge-fx border-danger text-danger">
                                                    <?= $sh['harcama_turu'] ?>
                                                </span>
                                            </td>
                                            <td class="fw-bold">- <?= number_format($sh['miktar'], 2, ',', '.') ?> <?= $sh['doviz_cinsi'] ?></td>
                                            <td class="text-dark"><?= number_format($sh['doviz_kuru'], 4, ',', '.') ?> ₺</td>
                                            <td class="pe-3 fw-bold">- <?= number_format($sh['tutar_tl'], 2, ',', '.') ?> ₺</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4 bg-white">Aktif döneme (<?= $guncel_ay_donem ?>) ait kayıtlı döviz veya varlık tasfiyesi harcaması bulunmuyor.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- Yeni Rapor Tablo Satırı Sonu -->

        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 premium-card-table">
                    <div class="card-header bg-dark py-3">
                        <h5 class="fw-bold mb-0 text-white">💳 2026 Yılı Sermaye ve Kredi Kartı (KK) Döviz Harcamaları Raporu</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped align-middle text-end mb-0 fx-custom-font">
                                
                                <!-- YENİLENDİ: Bu alan artık aktif ayı değil, yılbaşından beri yapılan tüm birikimli gideri gösterir -->
                                <thead class="table-light text-dark fw-bold">
                                    <tr>
                                        <td class="text-start ps-3 py-2">DÖNEM DETAYI</td>
                                        <td class="text-center font-monospace">2026 Yıllık Havuz</td>
                                        <td colspan="2" class="text-start ps-4 text-uppercase">Yılbaşından Beri Toplam Konsolide Sermaye Gideri:</td>
                                        <td class="pe-3 text-danger fw-bold fs-6"><?= number_format($toplam_sermaye_gideri_tl, 2, ',', '.') ?> ₺</td>
                                    </tr>
                                </thead>
                                
                                <thead class="table-primary text-dark">
                                    <tr>
                                        <th class="text-start ps-3 py-2">Harcama Dönemi / Adı</th>
                                        <th class="text-center">Harcama Türü</th>
                                        <th>Miktar / Varlık</th>
                                        <th>İşlem Kuru</th>
                                        <th class="pe-3">Tutar (TL)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($sermaye_harcamalari_listesi)): ?>
                                    <?php foreach ($sermaye_harcamalari_listesi as $sh): ?>
                                        <tr class="table-light text-fx-cikis">
                                            <!-- Dönem bilgisini başlığın önüne şık bir şekilde iliştiriyoruz -->
                                            <td class="text-start ps-3 font-weight-bold text-dark">
                                                <span class="badge bg-secondary me-2 font-monospace"><?= $sh['donem'] ?></span>
                                                <?= $sh['harcama_adi'] ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge border bg-white badge-fx border-danger text-danger">
                                                    <?= $sh['harcama_turu'] ?>
                                                </span>
                                            </td>
                                            <td class="fw-bold">- <?= number_format($sh['miktar'], 2, ',', '.') ?> <?= $sh['doviz_cinsi'] ?></td>
                                            <td class="text-dark"><?= number_format($sh['doviz_kuru'], 4, ',', '.') ?> ₺</td>
                                            <td class="pe-3 fw-bold">- <?= number_format($sh['tutar_tl'], 2, ',', '.') ?> ₺</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center text-muted py-5 bg-white">2026 yılına ait kayıtlı kümülatif döviz veya varlık tasfiyesi harcaması bulunmuyor.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- Yıllık Rapor Tablo Satırı Sonu -->


    </div>
</div>




    <div class="container-fluid px-4 py-4">

        <!-- ================= 4. SATIR: İLERİ DÜZEY FİNANSAL ANALİZ VE RAPORLAMA KATMANI ================= -->
        <div class="row g-4 mt-2 mb-4">
            
            <!-- Sol Sütun: Parite Arbitrajı ve Mali Kâr Ayrımı Raporu (col-md-6) -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 premium-card-table h-100">
                    <div class="card-header bg-dark py-3">
                        <h5 class="fw-bold mb-0 text-white">🔄 1 & 2: Arbitraj Paritesi ve Mali Kâr/Zarar Ayrımı</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped align-middle text-end mb-0 fx-custom-font">
                                <thead class="table-primary text-dark">
                                    <tr>
                                        <th class="text-start ps-3 py-2">Finansal Rapor Başlığı</th>
                                        <th class="text-center">Gösterge / Kur</th>
                                        <th class="pe-3">Durum / Fark (TL)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- ANALİZ 1: EUR/USD ÇAPRAZ PARİTE ARBİTRAJI -->
                                    <tr class="table-light">
                                        <td class="text-start ps-3 font-weight-bold text-dark">İç Arbitraj Paritesi (EUR/USD)</td>
                                        <td class="text-center font-monospace fw-bold"><?= number_format($ic_arbitraj_paritesi, 4, ',', '.') ?></td>
                                        <td class="pe-3 fw-bold <?= $parite_fark >= 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= ($parite_fark >= 0 ? '+' : '') . number_format($parite_fark, 4, ',', '.') ?> (Küresel: 1,1505)
                                        </td>
                                    </tr>
                                    
                                    <!-- ANALİZ 2: GERÇEKLEŞMEYEN (KAĞIT ÜSTÜNDEKİ) KÂR/ZARAR -->
                                    <?php $unrealized_class = ($unrealized_pnl >= 0) ? 'text-fx-alis' : 'text-fx-cikis'; ?>
                                    <tr class="table-light <?= $unrealized_class ?>">
                                        <td class="text-start ps-3 text-dark">Gerçekleşmeyen Kâr / Zarar (Kağıt Üstü)</td>
                                        <td class="text-center font-monospace">Açık Pozisyon</td>
                                        <td class="pe-3 fw-bold"><?= ($unrealized_pnl >= 0 ? '+' : '') . number_format($unrealized_pnl, 2, ',', '.') ?> ₺</td>
                                    </tr>

                                    <!-- ANALİZ 2: GERÇEKLEŞEN (KESİNLEŞMİŞ) KÂR/ZARAR -->
                                    <?php $realized_class = ($realized_pnl >= 0) ? 'text-fx-alis' : 'text-fx-cikis'; ?>
                                    <tr class="table-light <?= $realized_class ?>">
                                        <td class="text-start ps-3 text-dark">Gerçekleşen Kâr / Zarar (Kesinleşen Nakit)</td>
                                        <td class="text-center font-monospace">Kapalı Pozisyon</td>
                                        <td class="pe-3 fw-bold"><?= ($realized_pnl >= 0 ? '+' : '') . number_format($realized_pnl, 2, ',', '.') ?> ₺</td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light fw-bold text-dark">
                                    <tr>
                                        <td class="text-start ps-3 py-2">KONSOLİDE NET MALİ DURUM</td>
                                        <td class="text-center">-</td>
                                        <td class="pe-3 text-primary"><?= number_format($unrealized_pnl + $realized_pnl, 2, ',', '.') ?> ₺</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sağ Sütun: Ortalama Vade ve Stop-Loss / Take-Profit Alarmları (col-md-6) -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 premium-card-table h-100">
                    <div class="card-header bg-dark py-3">
                        <h5 class="fw-bold mb-0 text-white">⏱️ 3 & 4: Ortalama Vade Durumu ve Hedef / Risk Alarmları</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped align-middle text-end mb-0 fx-custom-font">
                                <thead class="table-primary text-dark">
                                    <tr>
                                        <!-- Genişlikler bu satırdan ayarlandı -->
                                        <th class="text-start ps-3 py-2" style="width: 15%;">Varlık Türü</th>
                                        <th class="text-center" style="width: 25%;">Ortalama Elde Tutma Süresi</th>
                                        <th class="text-center" style="width: 40%;">Hedef Kur / Zarar Kes</th>
                                        <th class="pe-3 text-center" style="width: 20%;">Sinyal / Alarm Durumu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- VARLIK 1: DOLAR (USD) ALARMLARI VE VADE -->
                                    <tr class="table-light">
                                        <td class="text-start ps-3 font-weight-bold text-dark">USD (Dolar)</td>
                                        <td class="text-center font-monospace fw-bold text-secondary"><?= $ortalama_vade_gun ?> Gün</td>
                                        <td class="text-center font-monospace">50,00 ₺ / 44,00 ₺</td>
                                        <td class="pe-3 text-center">
                                            <?php 
                                                $usd_badge = ($hedefler['USD']['durum'] == 'Beklemede') ? 'bg-secondary text-white' : (($live_kurlar['USD'] >= 50) ? 'bg-success text-white' : 'bg-danger text-white');
                                            ?>
                                            <span class="badge <?= $usd_badge ?> px-2.5 py-1 text-uppercase fw-bold" style="font-size: 11px;">
                                                <?= $hedefler['USD']['durum'] ?>
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- VARLIK 2: EURO (EUR) ALARMLARI VE VADE -->
                                    <tr class="table-light">
                                        <td class="text-start ps-3 font-weight-bold text-dark">EUR (Euro)</td>
                                        <td class="text-center font-monospace fw-bold text-secondary"><?= $ortalama_vade_gun ?> Gün</td>
                                        <td class="text-center font-monospace">55,00 ₺ / 50,00 ₺</td>
                                        <td class="pe-3 text-center">
                                            <?php 
                                                $eur_badge = ($hedefler['EUR']['durum'] == 'Beklemede') ? 'bg-secondary text-white' : (($live_kurlar['EUR'] >= 55) ? 'bg-success text-white' : 'bg-danger text-white');
                                            ?>
                                            <span class="badge <?= $eur_badge ?> px-2.5 py-1 text-uppercase fw-bold" style="font-size: 11px;">
                                                <?= $hedefler['EUR']['durum'] ?>
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- VARLIK 3: ALTIN (XAU) ALARMLARI VE VADE -->
                                    <tr class="table-light">
                                        <td class="text-start ps-3 font-weight-bold text-dark">XAU (Altın)</td>
                                        <td class="text-center font-monospace fw-bold text-secondary"><?= $ortalama_vade_gun ?> Gün</td>
                                        <td class="text-center font-monospace">6.500,00 ₺ / 5.800,00 ₺</td>
                                        <td class="pe-3 text-center">
                                            <?php 
                                                $xau_badge = ($hedefler['XAU']['durum'] == 'Beklemede') ? 'bg-secondary text-white' : (($live_kurlar['XAU'] >= 6500) ? 'bg-success text-white' : 'bg-danger text-white');
                                            ?>
                                            <span class="badge <?= $xau_badge ?> px-2.5 py-1 text-uppercase fw-bold" style="font-size: 11px;">
                                                <?= $hedefler['XAU']['durum'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light fw-bold text-dark">
                                    <tr>
                                        <td class="text-start ps-3 py-2">PORTFÖY ORTALAMA YAŞI</td>
                                        <td class="text-center text-primary"><?= $ortalama_vade_gun ?> Gün</td>
                                        <td class="text-center">-</td>
                                        <td class="pe-3 text-center text-muted small">Fiyat Sınırları 7/24 Aktif</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- 4. Satır (İleri Düzey Analiz Katmanı) Sonu -->

    </div>
</div> <!-- .container-fluid Sonu -->

<script>
    // 5 dakikalık (300 saniye) akıllı geri sayım sayacı
    let kalanSaniye = 300;
    const saniyeElementi = document.getElementById('kalan_saniye');
    const sayacKutusu = document.getElementById('kur_sayac_kutusu');

    const geriSayim = setInterval(function() {
        kalanSaniye--;
        if (saniyeElementi) {
            saniyeElementi.textContent = kalanSaniye;
        }

        // Son 10 saniye kala kullanıcıya görsel uyarı vermek için sayacı kırmızıya boyar
        if (kalanSaniye <= 10 && sayacKutusu) {
            sayacKutusu.classList.remove('bg-dark', 'text-info');
            sayacKutusu.classList.add('bg-danger', 'text-white');
        }

        // Süre bittiğinde sistemi kitlemeden sayfayı otomatik tazeler ve güncel kurları çeker
        if (kalanSaniye <= 0) {
            clearInterval(geriSayim);
            if (saniyeElementi) saniyeElementi.textContent = "Yenileniyor...";
            window.location.reload(); 
        }
    }, 1000);
</script>


<?php require admin_view("static/footer"); ?>
