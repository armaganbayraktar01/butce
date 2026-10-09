<?php 
    ## Tags Bilgileri
    $meta = [
        'site_title' => 'Döviz Alım Satım Arşivi | Bütçe Yönetimi',
        'site_desc' => 'Bütçe Yönetimi',
        'site_keyw' => 'Bütçe Yönetimi'
    ];
    # Tags Bilgileri

    require admin_view("static/header"); 

?>

<style>
 body { background-color: #f8fafc; font-family: 'Inter', system-ui, -apple-system, sans-serif; }
 .pastel-card { border: 1px solid rgba(0,0,0,0.03) !important; border-radius: 16px !important; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02) !important; transition: transform 0.2s ease, box-shadow 0.2s ease; }
 .pastel-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04) !important; }
 .pastel-usd { background-color: #e8f5e9 !important; color: #1b5e20 !important; } /* Mint Yeşili Dolar */
 .pastel-eur { background-color: #e3f2fd !important; color: #0d47a1 !important; } /* Safir Mavi Euro */
 .card-divider { border-top: 1px solid rgba(0, 0, 0, 0.06); margin: 12px 0; }
 .fx-custom-font { font-size: 12px !important; font-weight: 500; }
 .fx-custom-font th { font-weight: 700; letter-spacing: 0.3px; }
 .text-fx-alis { color: #198754 !important; }
 .text-fx-cikis { color: #dc3545 !important; }
 .badge-fx { font-size: 11px !important; padding: 4px 6px; font-weight: 700; text-transform: uppercase; }
</style>

<div class="container-fluid px-4 py-4">
    <div class="row g-4 mb-4">
        
    <?php foreach ($yatirim_araci as $kod => $liste): ?>
        <?php 
        if (empty($liste)) continue;
        
        // Para Birimine Göre Tasarımsal ve Simgesel Ayarlar
        $simge = ($kod === 'USD') ? '$' : '€';
        $tablo_stil_sinifi = ($kod === 'USD') ? 'table-success' : 'table-primary';
        $kart_basligi = ($kod === 'USD') ? "Amerikan Doları İşlemleri (USD)" : "Euro İşlemleri (EUR)";
        
        // FIFO motoru sonuçlarını bağlıyoruz
        $analiz = $doviz_analiz_sonuclari[$kod] ?? [];
        $toplam_maliyet      = $analiz['alis_maliyeti'] ?? 0;
        $toplam_satis_geliri = $analiz['satis_geliri'] ?? 0;
        $kesinlesen_kar      = $analiz['realize_kar'] ?? 0;
        
        // Listenin en sonundaki satırın yürüyen bakiyesi mevcut stok durumudur
        $guncel_stok_miktari = !empty($liste) ? $liste[0]['anlik_bakiye'] : 0;
        ?>

        <div class="col-md-12 mb-4">
            <div class="card shadow-sm border-0 premium-card-table h-100">
                <div class="card-header bg-dark py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-white">
                        <i class="fas fa-exchange-alt me-2 text-warning"></i>
                        <?= htmlspecialchars($kart_basligi) ?>
                    </h5>
                    <span class="badge bg-primary text-white p-2"><?= count($liste) ?> Kayıt</span>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped align-middle text-end mb-0 fx-custom-font">
                            <thead class="small text-uppercase">
                                <!-- DÖVİZ PORTFÖYÜ FIFO YÖNTEMİ ANALİZ SATIRI -->
                                <tr class="fw-bold <?= $tablo_stil_sinifi ?> text-dark border-bottom">
                                    <td class="text-start ps-3 py-2"><i class="fas fa-history me-1"></i> FIFO MATEMATİĞİ</td>
                                    <td class="text-end py-2" colspan="2">Toplam Alış Maliyeti: <span class="text-primary"><?= number_format($toplam_maliyet, 2, ',', '.') ?> ₺</span></td>
                                    <td class="text-end py-2" colspan="2">Toplam Satış Hasılatı: <span class="text-success"><?= number_format($toplam_satis_geliri, 2, ',', '.') ?> ₺</span></td>
                                    <td class="text-center py-2" colspan="2">Mevcut Stok Durumu: 
                                        <span class="badge <?= $guncel_stok_miktari <= 0 ? 'bg-secondary' : 'bg-success' ?>">
                                            <?= $guncel_stok_miktari <= 0 ? 'TASFİYE EDİLDİ (0)' : number_format($guncel_stok_miktari, 2, ',', '.') . ' ' . $kod ?>
                                        </span>
                                    </td>
                                    <td class="text-end py-2">Net FIFO Realize Kâr:</td>
                                    <td class="text-end pe-3 py-2 font-monospace fw-bold text-success">+ <?= number_format($kesinlesen_kar, 2, ',', '.') ?> ₺</td>
                                </tr>
                                <tr class="fw-bold table-dark text-white border-bottom">
                                    <td class="text-start ps-3 py-2">Tarih</td> 
                                    <td class="text-center py-2">İşlem Türü</td> 
                                    <td class="text-end py-2">Miktar</td> 
                                    <td class="text-end py-2">Bakiye (<?= $kod ?>)</td> 
                                    <td class="text-center py-2">Kur</td> 
                                    <td class="text-end py-2">Tutar</td> 
                                    <td class="text-end py-2">Vergi / Komisyon</td> 
                                    <td class="text-end py-2">Maliyet (TL)</td> 
                                    <td class="text-end pe-3 fw-bold py-2">Kümülatif Toplam (TL)</td>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($liste as $islem): 
                                $islem_turu_ham = $islem['islem_turu'] ?? 2;
                                
                                if ($islem_turu_ham == 4) {
                                    $yazi_sinifi = 'text-fx-alis'; $badge_sinifi = 'border-info text-info'; $tur_metni = 'Mevduat'; $isaret = '+';
                                } elseif ($islem_turu_ham == 2) {
                                    $yazi_sinifi = 'text-fx-alis'; $badge_sinifi = 'border-success text-success'; $tur_metni = 'Alış'; $isaret = '+';
                                } elseif ($islem_turu_ham == 1) {
                                    $yazi_sinifi = 'text-fx-cikis'; $badge_sinifi = 'border-danger text-danger'; $tur_metni = 'Ödeme'; $isaret = '-';
                                } elseif ($islem_turu_ham == 0) {
                                    $yazi_sinifi = 'text-fx-cikis'; $badge_sinifi = 'border-warning text-warning'; $tur_metni = 'Satış'; $isaret = '-';
                                } else {
                                    $yazi_sinifi = 'text-fx-cikis'; $badge_sinifi = 'border-secondary text-secondary'; $tur_metni = 'Harcama'; $isaret = '-';
                                }
                                
                                $tarih_kaynagi = $islem['islem_tarihi'] ?? '';
                                $is_devir = (!empty($tarih_kaynagi) && date('m-d', strtotime($tarih_kaynagi)) === '01-01');
                                $satir_sinifi = $is_devir ? 'table-secondary text-muted' : 'table-light';
                            ?>
                                <tr class="<?= $satir_sinifi ?> <?= $yazi_sinifi ?>">
                                    <td class="text-start ps-3 font-monospace text-dark">
                                        <?= !empty($islem['islem_tarihi']) ? date('d.m.Y', strtotime($islem['islem_tarihi'])) : '-' ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge border bg-white badge-fx <?= $badge_sinifi ?>"><?= $tur_metni ?></span>
                                    </td>
                                    <td class="fw-bold">
                                        <?= $isaret ?> <?= number_format(abs($islem['miktar']), 2, ',', '.') ?> <?= $simge ?>
                                    </td> 
                                    <td class="fw-bold text-muted font-monospace bg-white bg-opacity-25">
                                        <?= number_format($islem['anlik_bakiye'], 2, ',', '.') ?> <?= $simge ?>
                                    </td>
                                    <td class="text-dark text-center">
                                        <?= number_format($islem['doviz_kuru'], 4, ',', '.') ?> ₺
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
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</div>

<?php require admin_view('static/footer'); ?>
