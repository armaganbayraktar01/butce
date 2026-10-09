<?php
// =========================================================================
// 📑 HARCAMA LİSTESİ CONTROLLER KATMANI (MÜKEMMELLEŞTİRİLMİŞ NİHAİ SÜRÜM - PART 1/4)
// =========================================================================

if (!defined('BASEPATH') && !isset($db)) {
    die("Emniyet Bariyeri: Bu alana doğrudan erişim yetkiniz bulunmuyor.");
}

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// DÖNEM VE ZAMAN YÖNETİMİ
if (!isset($_SESSION['aktif_donem']) || empty($_SESSION['aktif_donem'])) {
    $en_guncel_sorgu = $db->query("SELECT aktif_donem FROM butce_akis ORDER BY aktif_donem DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $_SESSION['aktif_donem'] = ($en_guncel_sorgu && !empty($en_guncel_sorgu['aktif_donem'])) ? $en_guncel_sorgu['aktif_donem'] : date('Y-m');
}
$aktif_donem = $_SESSION['aktif_donem'];

// AJAX VE POST İSTEKLERİ YÖNETİM MERKEZİ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['islem']) || isset($_REQUEST['islem']))) {
    
    while (ob_get_level() > 0) { ob_end_clean(); }
    ob_start();
    header('Content-Type: application/json; charset=utf-8');

    $islem = $_POST['islem'] ?? $_REQUEST['islem'] ?? '';

    // 🔴 1. KAPI: TEKİL KATEGORİ GÜNCELLEME
    if ($islem === 'etiket_guncelle_ajax') {
        $harcama_id  = isset($_POST['harcama_id']) ? intval($_POST['harcama_id']) : 0;
        $yeni_kategori_id = isset($_POST['yeni_tagler']) ? intval($_POST['yeni_tagler']) : 0;

        if (!$harcama_id) {
            echo json_encode(['success' => false, 'message' => 'Eksik veya geçersiz parametre!']);
            die();
        }
        try {
            $stmt = $db->prepare("UPDATE `harcama_gecmisi_arsiv` SET `kategori_id` = ? WHERE `id` = ?");
            $status = $stmt->execute([$yeni_kategori_id, $harcama_id]);
            echo json_encode(['success' => (bool)$status, 'message' => $status ? 'Kategori güncellendi.' : 'Yazma hatası!']);
            die();
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'SQL Hatası: ' . $e->getMessage()]);
            die();
        }
    }

    // 🔵 2. KAPI: TOPLU KATEGORİ GÜNCELLEME
    if ($islem === 'toplu_etiket_guncelle_ajax') {
        $harcama_ids     = isset($_POST['harcama_ids']) ? array_filter(array_map('intval', $_POST['harcama_ids'])) : [];
        $yeni_kategori_id = isset($_POST['yeni_tagler']) ? intval($_POST['yeni_tagler']) : 0;
        $guncelleme_turu = filter_input(INPUT_POST, 'guncelleme_turu', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($harcama_ids) || !$guncelleme_turu) {
            echo json_encode(['success' => false, 'message' => 'Toplu parametre hatası!']);
            die();
        }
        try {
            $db->beginTransaction();
            foreach ($harcama_ids as $id) {
                if ($guncelleme_turu === 'uzerine_yaz' || $guncelleme_turu === 'uzerine_ekle') {
                    $db->prepare("UPDATE `harcama_gecmisi_arsiv` SET `kategori_id` = ? WHERE `id` = ?")->execute([$yeni_kategori_id, $id]);
                } elseif ($guncelleme_turu === 'tamamen_sil') {
                    $db->prepare("UPDATE `harcama_gecmisi_arsiv` SET `kategori_id` = 0 WHERE `id` = ?")->execute([$id]);
                }
            }
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Toplu kategori güncellemesi başarılı!']);
            die();
        } catch (PDOException $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            echo json_encode(['success' => false, 'message' => 'Toplu SQL Hatası: ' . $e->getMessage()]);
            die();
        }
    }

        // 💳 2-B KAPI: TOPLU ÖDEME TÜRÜ GÜNCELLEME (BULK PAYMENT TYPE)
    if ($islem === 'toplu_odeme_turu_guncelle_ajax') {
        $harcama_ids     = isset($_POST['harcama_ids']) ? array_filter(array_map('intval', $_POST['harcama_ids'])) : [];
        $odeme_turu      = filter_input(INPUT_POST, 'odeme_turu', FILTER_SANITIZE_SPECIAL_CHARS); // PESIN veya TAKSITLI
        $toplam_taksit   = isset($_POST['toplam_taksit']) ? intval($_POST['toplam_taksit']) : 1;

        if (empty($harcama_ids) || !$odeme_turu) {
            echo json_encode(['success' => false, 'message' => 'Toplu ödeme parametre hatası!']);
            die();
        }

        try {
            $db->beginTransaction();
            
            // Eğer peşin seçildiyse taksit değerleri 1 olmalı
            $hedef_toplam_taksit = ($odeme_turu === 'PESIN') ? 1 : $toplam_taksit;
            // Taksitli işlemlerde toplu atamada mevcut taksiti başlangıç olarak 1 yapıyoruz
            $hedef_mevcut_taksit = 1; 

            $stmt = $db->prepare("UPDATE `harcama_gecmisi_arsiv` SET `toplam_taksit` = ?, `mevcut_taksit` = ? WHERE `id` = ?");
            
            foreach ($harcama_ids as $id) {
                $stmt->execute([$hedef_toplam_taksit, $hedef_mevcut_taksit, $id]);
            }
            
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Seçili harcamaların ödeme türleri başarıyla toplu güncellendi!']);
            die();
        } catch (PDOException $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            echo json_encode(['success' => false, 'message' => 'Toplu Ödeme SQL Hatası: ' . $e->getMessage()]);
            die();
        }
    }


    // 🟢 3. KAPI: SATIR İÇİ ÇARPI (X) İLE KATEGORİ SIFIRLAMA
    if ($islem === 'tekil_etiket_sil_ajax') {
        $harcama_id = isset($_POST['harcama_id']) ? intval($_POST['harcama_id']) : 0;
        if (!$harcama_id) {
            echo json_encode(['success' => false, 'message' => 'Parametre doğrulaması başarısız!']);
            die();
        }
        try {
            $stmt = $db->prepare("UPDATE `harcama_gecmisi_arsiv` SET `kategori_id` = 0 WHERE `id` = ?");
            $status = $stmt->execute([$harcama_id]);
            echo json_encode(['success' => $status, 'yeni_tagler' => 0]);
            die();
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            die();
        }
    }

    // 🏢 4. KAPI: TOPLU FİRMA GÜNCELLEME VE OTOMATİK ÖĞRENME
    if ($islem === 'toplu_firma_guncelle_ajax') {
        $harcama_ids = isset($_POST['harcama_ids']) ? array_filter(array_map('intval', $_POST['harcama_ids'])) : [];
        $yeni_firma  = filter_input(INPUT_POST, 'yeni_firma', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($harcama_ids) || empty($yeni_firma)) {
            echo json_encode(['success' => false, 'message' => 'Eksik toplu firma verisi!']); die();
        }
        $yeni_firma_temiz = mb_strtoupper(trim($yeni_firma), 'UTF-8');
        try {
            $db->beginTransaction();
            $kural_chk = $db->prepare("SELECT COUNT(id) FROM `firmalar` WHERE UPPER(TRIM(`aranacak_kelime`)) = ?");
            $kural_chk->execute([$yeni_firma_temiz]);
            
            if (intval($kural_chk->fetch(PDO::FETCH_COLUMN) ?? 0) === 0 && !empty($yeni_firma_temiz)) {
                $kural_ins = $db->prepare("INSERT INTO `firmalar` (`aranacak_kelime`, `isletme_adi`, `kategori_id`, `eklenme_tarihi`) VALUES (?, ?, 0, NOW())");
                $kural_ins->execute([$yeni_firma_temiz, $yeni_firma_temiz]);
            }
            $stmt = $db->prepare("UPDATE `harcama_gecmisi_arsiv` SET `isletme_adi` = ? WHERE `id` = ?");
            foreach ($harcama_ids as $id) {
                $stmt->execute([$yeni_firma_temiz, $id]);
            }
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Firmalar toplu güncellendi ve kurallara işlendi!']); die();
        } catch (PDOException $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            echo json_encode(['success' => false, 'message' => 'Toplu SQL Hatası: ' . $e->getMessage()]); die();
        }
    }

    // 🏢 5. KAPI: TEKİL FİRMA GÜNCELLEME VE KATEGORİ ENTEGRELİ OTOMATİK ÖĞRENME
    if ($islem === 'tekil_firma_guncelle_ajax') {
        $harcama_id = isset($_POST['harcama_id']) ? intval($_POST['harcama_id']) : 0;
        $yeni_firma = filter_input(INPUT_POST, 'yeni_firma', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$harcama_id || empty($yeni_firma)) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz parametre dizilimi!']); die();
        }
        $yeni_firma_temiz = mb_strtoupper(trim($yeni_firma), 'UTF-8');
        try {
            $db->beginTransaction();
            $satir_sorgu = $db->prepare("SELECT `aciklama`, `kategori_id` FROM `harcama_gecmisi_arsiv` WHERE `id` = ?"); 
            $satir_sorgu->execute([$harcama_id]);
            $harcama_verisi = $satir_sorgu->fetch(PDO::FETCH_ASSOC);

            $ham_harcama_aciklamasi = isset($harcama_verisi['aciklama']) ? trim($harcama_verisi['aciklama']) : '';
            $mevcut_kategori_id     = isset($harcama_verisi['kategori_id']) ? intval($harcama_verisi['kategori_id']) : 0;

            if (!empty($ham_harcama_aciklamasi)) {
                $kural_chk = $db->prepare("SELECT COUNT(id) FROM `firmalar` WHERE UPPER(TRIM(`aranacak_kelime`)) = ?");
                $kural_chk->execute([mb_strtoupper($ham_harcama_aciklamasi, 'UTF-8')]); 
                if (intval($kural_chk->fetch(PDO::FETCH_COLUMN) ?? 0) === 0) {
                    $kural_ins = $db->prepare("INSERT INTO `firmalar` (`aranacak_kelime`, `isletme_adi`, `kategori_id`, `eklenme_tarihi`) VALUES (?, ?, ?, NOW())");
                    $kural_ins->execute([mb_strtoupper($ham_harcama_aciklamasi, 'UTF-8'), $yeni_firma_temiz, $mevcut_kategori_id]);
                }
            }
            $stmt = $db->prepare("UPDATE `harcama_gecmisi_arsiv` SET `isletme_adi` = ? WHERE `id` = ?");
            $status = $stmt->execute([$yeni_firma_temiz, $harcama_id]);
            $db->commit();
            echo json_encode(['success' => (bool)$status, 'message' => $status ? 'Firma mühürlendi ve sisteme öğretildi.' : 'Hata!']);
            die();
        } catch (PDOException $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            echo json_encode(['success' => false, 'message' => 'SQL Hatası: ' . $e->getMessage()]); die();
        }
    }

    // 🎛️ 6. KAPI: SATIR TAM DÜZENLEME (EDIT MODAL KAPISI)
    if ($islem === 'harcama_tam_guncelle_ajax') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $donem = filter_input(INPUT_POST, 'donem', FILTER_SANITIZE_SPECIAL_CHARS);
        $kart_turu = filter_input(INPUT_POST, 'kart_turu', FILTER_SANITIZE_SPECIAL_CHARS);
        $islem_tarihi = filter_input(INPUT_POST, 'islem_tarihi', FILTER_SANITIZE_SPECIAL_CHARS);
        $isletme_adi = filter_input(INPUT_POST, 'isletme_adi', FILTER_SANITIZE_SPECIAL_CHARS);
        $aciklama = filter_input(INPUT_POST, 'aciklama', FILTER_SANITIZE_SPECIAL_CHARS);
        $kategori_id = isset($_POST['kategori_id']) ? intval($_POST['kategori_id']) : 0;
        $tutar = isset($_POST['tutar']) ? floatval(str_replace(['.', ','], ['', '.'], $_POST['tutar'])) : 0.00;
        $toplam_taksit = isset($_POST['toplam_taksit']) ? intval($_POST['toplam_taksit']) : 1;
        $mevcut_taksit = isset($_POST['mevcut_taksit']) ? intval($_POST['mevcut_taksit']) : 1;

        if (!$id) { 
            echo json_encode(['success' => false, 'message' => 'ID parametresi eksik!']); 
            die(); 
        }

        try {
            $stmt = $db->prepare("UPDATE `harcama_gecmisi_arsiv` SET `donem` = ?, `kart_turu` = ?, `islem_tarihi` = ?, `isletme_adi` = ?, `aciklama` = ?, `kategori_id` = ?, `tutar` = ?, `toplam_taksit` = ?, `mevcut_taksit` = ? WHERE `id` = ?");
            $status = $stmt->execute([$donem, $kart_turu, $islem_tarihi, mb_strtoupper($isletme_adi, 'UTF-8'), $aciklama, $kategori_id, $tutar, $toplam_taksit, $mevcut_taksit, $id]);
            echo json_encode(['success' => $status, 'message' => $status ? 'Kayıt başarıyla güncellendi.' : 'Güncelleme sırasında yazma hatası oluştu.']);
            die();
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'SQL Hatası: ' . $e->getMessage()]); 
            die();
        }
    }

    // 🗑️ 7. KAPI: TEKİL KAYIT KALICI SİLME MOTORU
    if ($islem === 'harcama_sil_ajax') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if (!$id) { 
            echo json_encode(['success' => false, 'message' => 'Geçersiz veya eksik ID!']); 
            die(); 
        }

        try {
            $stmt = $db->prepare("DELETE FROM `harcama_gecmisi_arsiv` WHERE `id` = ?");
            $status = $stmt->execute([$id]);
            echo json_encode(['success' => $status, 'message' => $status ? 'Harcama kaydı mizan havuzundan tamamen silindi.' : 'Silme işlemi veritabanı tarafından reddedildi.']);
            die();
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'SQL Hatası: ' . $e->getMessage()]); 
            die();
        }
    }
}

// =========================================================================
// 📂 MİZAN VERİ ÇEKME VE HAZIRLIK MOTORU (VIEW KATMANI İÇİN)
// =========================================================================

$secili_donem = $_GET['donem'] ?? $_SESSION['secili_donem'] ?? 'TUMU';
$_SESSION['secili_donem'] = $secili_donem;

// Menü ve filtre kutuları için yüklenmiş benzersiz dönemler
$tum_yuklu_donemler = [];
try {
    $tum_yuklu_donemler = $db->query("SELECT DISTINCT donem FROM harcama_gecmisi_arsiv WHERE donem != '' AND donem IS NOT NULL ORDER BY donem DESC")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) { }

// Kategori matrisi hafızaya alınıyor (Tekil Kategori İlişkisi)
$kategori_haritasi = [];
$tum_kategoriler = [];
try {
    $tum_kategoriler = $db->query("SELECT id, kategori_adi FROM kategoriler ORDER BY kategori_adi ASC")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($tum_kategoriler as $row) { 
        $kategori_haritasi[$row['id']] = $row['kategori_adi']; 
    }
} catch (PDOException $e) { }

// Mizan listeleme sorgusu
$harcamalar = [];
try {
    if ($secili_donem === 'TUMU') {
        $harcamalar = $db->query("SELECT * FROM harcama_gecmisi_arsiv ORDER BY islem_tarihi DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $db->prepare("SELECT * FROM harcama_gecmisi_arsiv WHERE donem = ? ORDER BY islem_tarihi DESC, id DESC");
        $stmt->execute([$secili_donem]);
        $harcamalar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) { }

// Datalist otomatik tamamlama havuzu için kayıtlı firma listesi
$tum_firma_kurallari = [];
try {
    $tum_firma_kurallari = $db->query("SELECT DISTINCT `isletme_adi` FROM `firmalar` WHERE `isletme_adi` != '' AND `isletme_adi` IS NOT NULL ORDER BY `isletme_adi` ASC")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) { }

// =========================================================================
// 🏢 DİNAMİK VE KATEGORİYE DUYARLI POPÜLER FİRMA SKORLAMA MOTORU (PART 1/2)
// =========================================================================
$en_cok_islem_goren_firmalar = [];
try {
    // 🎯 AKILLI SENSÖR: Eğer kullanıcı arayüzde bir kategori rozetine basmışsa,
    // o kategorinin ID'si localStorage üzerinden veya AJAX ile saklanır. 
    // Sayfa yenilendiğinde GET/SESSION durumuna göre kategoriye kilitleniyoruz.
    $aktif_filtre_kategori = $_GET['kat_id'] ?? 0; 

    if (intval($aktif_filtre_kategori) > 0) {
        // A) Eğer kategori seçilmişse: Sadece o kategoriye ait harcamaların en yoğun 30 firmasını getir
        $firma_skor_stmt = $db->prepare("
            SELECT `isletme_adi`, COUNT(`id`) as toplam_islem_adedi 
            FROM `harcama_gecmisi_arsiv` 
            WHERE `isletme_adi` != '' AND `isletme_adi` IS NOT NULL AND `kategori_id` = ?
            GROUP BY `isletme_adi` 
            ORDER BY toplam_islem_adedi DESC 
            LIMIT 30
        ");
        $firma_skor_stmt->execute([intval($aktif_filtre_kategori)]);
        $en_cok_islem_goren_firmalar = $firma_skor_stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // B) Eğer kategori seçilmemişse (TÜMÜ aktifse): Standart genel en yoğun 30 firmayı getir
        $en_cok_islem_goren_firmalar = $db->query("
            SELECT `isletme_adi`, COUNT(`id`) as toplam_islem_adedi 
            FROM `harcama_gecmisi_arsiv` 
            WHERE `isletme_adi` != '' AND `isletme_adi` IS NOT NULL 
            GROUP BY `isletme_adi` 
            ORDER BY toplam_islem_adedi DESC 
            LIMIT 30
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Sessiz kal emniyet bariyeri
}

// Görünüm (View) katmanı güvenli bir şekilde çağrılır
require_once 'admin/view/harcama_list_arsiv.php';

