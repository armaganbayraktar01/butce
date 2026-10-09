<?php 
    ## Tags Bilgileri
    $meta = [
        'site_title' => 'Ekstreler | Bütçe Yönetimi',
        'site_desc' => 'Bütçe Yönetimi',
        'site_keyw' => 'Bütçe Yönetimi'
    ];
    # Tags Bilgileri

    require admin_view("static/header"); 

?>

<div class="container-fluid mt-4">
    <!-- Başlık ve Dönem Bilgisi -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>💳 Kredi Kartı Ekstre Analiz Paneli</h2>
        <span class="badge bg-secondary p-2 fs-6">📅 Aktif Dönem: <?= $ay_isimleri[$aktif_ay_index] . ' ' . $aktif_yil ?></span>
    </div>

    <!-- İşlem Mesajları ve Bildirimler -->
    <?php if (isset($_SESSION['mesaj'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            🚀 <?= $_SESSION['mesaj']; unset($_SESSION['mesaj']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['hata'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ⚠️ <?= $_SESSION['hata']; unset($_SESSION['hata']); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- SOL PANEL: EKSTRE PDF YÜKLEME ALANI -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white font-weight-bold">
                    📁 Dijital Ekstre Yükleme İstasyonu
                </div>
                <div class="card-body">
                    <form action="?action=upload" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ekstre Sahibi Banka</label>
                            <select name="kart_turu" class="form-select" required>
                                <option value="isbank">İş Bankası (Maximum)</option>
                                <option value="vakif">VakıfBank</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">PDF Dosyası</label>
                            <input type="file" name="ekstre_pdf" class="form-control" accept=".pdf" required>
                            <div class="form-text text-muted">Bankacılık uygulamasından indirdiğiniz ham PDF çıktısını ekleyiniz.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 font-weight-bold">
                            🔍 Ekstreyi Oku ve Veritabanına İşle
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- SAĞ PANEL: DÖNEMSEL ANALİZ RAPORLARI -->
        <div class="col-xl-8 col-lg-7 mb-4">
 
        </div>
    </div>
</div>

<?php require admin_view('static/footer'); ?>
