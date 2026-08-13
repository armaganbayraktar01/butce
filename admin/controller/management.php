<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }


// 1. DÖNEM VE ZAMAN YÖNETİMİ
if (!isset($_SESSION['aktif_donem']) || empty($_SESSION['aktif_donem'])) {
    $en_guncel_sorgu = $db->query("SELECT aktif_donem FROM butce_akis ORDER BY aktif_donem DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $_SESSION['aktif_donem'] = ($en_guncel_sorgu && !empty($en_guncel_sorgu['aktif_donem'])) ? $en_guncel_sorgu['aktif_donem'] : date('Y-m');
}

$aktif_donem = $_SESSION['aktif_donem'];

$ay_isimleri = [1=>'OCAK', 2=>'ŞUBAT', 3=>'MART', 4=>'NİSAN', 5=>'MAYIS', 6=>'HAZİRAN', 7=>'TEMMUZ', 8=>'AĞUSTOS', 9=>'EYLÜL', 10=>'EKİM', 11=>'KASIM', 12=>'ARALIK'];
list($aktif_yil, $aktif_ay_str) = explode('-', $aktif_donem);
$aktif_ay_index = intval($aktif_ay_str);



// FİNANSAL METRİKLER VE PARAMETRELER

if (isset($_POST['telefon_birim_fiyat'])) {
    $_SESSION['sys_telefon_birim_fiyat'] = floatval(str_replace(',', '.', $_POST['telefon_birim_fiyat']));
}
if (isset($_POST['su_birim_fiyat'])) {
    $_SESSION['sys_su_birim_fiyat'] = floatval(str_replace(',', '.', $_POST['su_birim_fiyat']));
}


$telefonFaturasiBirimTutari = $_SESSION['sys_telefon_birim_fiyat'] ?? 340.00;
$damacanaSuBirimTutari      = $_SESSION['sys_su_birim_fiyat']      ?? 140.00;

$damacanaMiktari = intval(damacanaMiktari($aktif_donem)) * 2;
$aylikInternetFaturasiTutari = 407.00;

$aylikDamacanaTutari = $damacanaMiktari * $damacanaSuBirimTutari;
$aylikTelefonFaturasi = $telefonFaturasiBirimTutari * 2;
  

// 1. Orijinal Sabitler (Fabrika Ayarları)
$faiz_stopaj = 17.5;
$faiz_oran = 30.00;
$faiz_gun = 0;

$evKredisiTutariStandart = 53315.33;
$evKredisiConst = 53200.00; 

if (isset($_POST['evKredisiHam'])) {
    $_SESSION['evKredisiHam'] = floatval($_POST['evKredisiHam']);
}
if (isset($_POST['faiz_gun_sayisi'])) {
    $_SESSION['dinamik_faiz_gun_sayisi'] = intval($_POST['faiz_gun_sayisi']);
}
if (isset($_POST['faiz_orani'])) {
    $_SESSION['dinamik_faiz_orani'] = floatval($_POST['faiz_orani']);
}

$evKredisiHam = $_SESSION['evKredisiHam'] ?? $evKredisiConst;
$faiz_orani = $_SESSION['dinamik_faiz_orani'] ?? $faiz_oran;
$faiz_gun_sayisi = $_SESSION['dinamik_faiz_gun_sayisi'] ?? $faiz_gun;





// MOTORU ÇALIŞTIR
$engine = new LiquidityEngine($db, $aktif_donem);

  
$islem = $_POST['islem'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($islem)) {
    while (ob_get_level()) { ob_end_clean(); }
    header('Content-Type: application/json; charset=utf-8');

    // Sol Sütun
    if ($islem === 'taksit_kaydet') {
        $isbank_taksitler = $_POST['isbank_taksit'] ?? [];
        $vakif_taksitler  = $_POST['vakif_taksit'] ?? [];

        for ($i = 0; $i < 12; $i++) {
            $db->prepare("UPDATE cc_taksitler SET isbank_tutar = :is_tutar, vakif_tutar = :vk_tutar WHERE ay_index = :ay_index")
            ->execute([
                'is_tutar' => floatval($isbank_taksitler[$i] ?? 0), 
                'vk_tutar' => floatval($vakif_taksitler[$i] ?? 0), 
                'ay_index' => $i + 1
            ]);
        }
        echo json_encode(['status' => 'success']); 
        exit;
    }

    if ($islem === 'cari_girdiler_kaydet') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');

        // 🎯 REFORM: Mükerrer str_replace sızıntılarını önleyen merkezi temizlik fonksiyonu
        if (!function_exists('finansalParaParse')) {
        // 🎯 REFORM: Hem HTML5 Number inputlarını hem de formatlı metinleri kuruşu kuruşuna çözen akıllı parse motoru
        if (!function_exists('finansalParaParse')) {
            function finansalParaParse($deger) {
                if ($deger === null || trim($deger) === '') return 0.00;
                
                $deger = trim(str_replace(['₺', ' '], '', $deger));
                
                // SENARYO A: Eğer değer '47437.26' gibi standart HTML5 number formatındaysa doğrudan float'a çevir
                if (preg_match('/^\d+\.\d+$/', $deger)) {
                    return floatval($deger);
                }
                
                // SENARYO B: Eğer değer '47.437,26' gibi binlik noktalı ve virgüllü bütçe formatındaysa
                if (strpos($deger, '.') !== false && strpos($deger, ',') !== false) {
                    $temiz = str_replace('.', '', $deger); // Binlik noktalarını kaldır
                    $temiz = str_replace(',', '.', $temiz); // Ondalık virgülü noktaya çevir
                    return floatval($temiz);
                }
                
                // SENARYO C: Eğer değer sadece tek bir virgül içeriyorsa '47437,26' ➡️ '47437.26'
                if (strpos($deger, ',') !== false && strpos($deger, '.') === false) {
                    $temiz = str_replace(',', '.', $deger);
                    return floatval($temiz);
                }
                
                // SENARYO D: Eğer düz tam sayı ise '47437'
                return floatval($deger);
            }
        }

        }

        // Üst kesinleşen dönem ham girdileri
        $raw_isbank = $_POST['isbank_kesinlesen_donem'] ?? '';
        $raw_vakif  = $_POST['vakif_kesinlesen_donem'] ?? '';

        // 1. ADIM: İki input birden tamamen boşsa engelle (En az biri dolu olmalı)
        if (trim($raw_isbank) === '' && trim($raw_vakif) === '') {
            ob_clean(); 
            echo json_encode(['status' => 'error', 'message' => '⚠️ Hata: Kesinleşen dönem tutarlarının en az biri doldurulmalıdır!']); 
            exit;
        }

        // 2. ADIM: Merkezi motor üzerinden pürüzsüz dönüştürme
        $isbank_kesinlesen_donem_tutar = finansalParaParse($raw_isbank);
        $vakif_kesinlesen_donem_tutar  = finansalParaParse($raw_vakif);

        // 3. ADIM: Kredi kartları detay tablosu alt girdilerinin temizlenmesi
        $kartlar = [
            'isbank' => [
                'donem_ici'         => finansalParaParse($_POST['isbank_donem_ici'] ?? 0), 
                'provizyon'         => finansalParaParse($_POST['isbank_provizyon'] ?? 0), 
                'doviz_borcu'       => finansalParaParse($_POST['isbank_doviz'] ?? 0),
                'kesinlesen_ekstre' => finansalParaParse($_POST['isbank_ekstre'] ?? 0)
            ],
            'vakifbank' => [
                'donem_ici'         => finansalParaParse($_POST['vakif_donem_ici'] ?? 0), 
                'provizyon'         => finansalParaParse($_POST['vakif_provizyon'] ?? 0), 
                'doviz_borcu'       => finansalParaParse($_POST['vakif_doviz'] ?? 0),
                'kesinlesen_ekstre' => finansalParaParse($_POST['vakif_ekstre'] ?? 0)
            ]
        ];

        try {
            $db->beginTransaction();

            // 4. HAMLE: Kredi kartları detay tablosunu güncelliyoruz
            foreach ($kartlar as $kart_adi => $m) {
                $db->prepare("
                    UPDATE kredi_kartlari 
                    SET donem_ici = :donem_ici, 
                        provizyon = :provizyon, 
                        doviz_borcu = :doviz, 
                        kesinlesen_ekstre = :ekstre 
                    WHERE kart_adi = :kart_adi
                ")->execute([
                    'donem_ici' => $m['donem_ici'], 
                    'provizyon' => $m['provizyon'], 
                    'doviz'     => $m['doviz_borcu'], 
                    'ekstre'    => $m['kesinlesen_extre'] ?? $m['kesinlesen_ekstre'], 
                    'kart_adi'  => $kart_adi
                ]);
            }

            // 5. HAMLE: Kilit kontrolü ve esnek senkronizasyon matrisi (|| kilidi kırıldı!)
            
            // --- İŞBANKASI KREDİ KARTI SEYRİ ---
            if ($isbank_kesinlesen_donem_tutar > 0) {
                $checkIsbank = $db->prepare("SELECT is_locked FROM butce_akis WHERE aktif_donem = ? AND turu = 'gider' AND name = 'kredi_karti_a' LIMIT 1");
                $checkIsbank->execute([$aktif_donem]);
                $isbankSatir = $checkIsbank->fetch(PDO::FETCH_ASSOC);

                if ($isbankSatir && intval($isbankSatir['is_locked']) === 1) {
                    throw new Exception("⚠️ İşbankası Kredi Kartı (A) borcu bu dönem ödenip kilitlendiği için yeni ekstre tutarı kaydedilemez!");
                }
                
                $db->prepare("
                    UPDATE butce_akis 
                    SET tutar = :tutar 
                    WHERE name = :kart_adi 
                    AND aktif_donem = :donem
                ")->execute([
                    'tutar'    => $isbank_kesinlesen_donem_tutar, 
                    'kart_adi' => 'kredi_karti_a',
                    'donem'    => $aktif_donem
                ]);
            }

            // --- VAKIFBANK KREDİ KARTI SEYRİ ---
            if ($vakif_kesinlesen_donem_tutar > 0) {
                $checkVakif = $db->prepare("SELECT is_locked FROM butce_akis WHERE aktif_donem = ? AND turu = 'gider' AND name = 'kredi_karti_o' LIMIT 1");
                $checkVakif->execute([$aktif_donem]);
                $vakifSatir = $checkVakif->fetch(PDO::FETCH_ASSOC);

                if ($vakifSatir && intval($vakifSatir['is_locked']) === 1) {
                    throw new Exception("⚠️ Vakıfbank Kredi Kartı (Ö) borcu bu dönem ödenip kilitlendiği için yeni ekstre tutarı kaydedilemez!");
                }
                
                $db->prepare("
                    UPDATE butce_akis 
                    SET tutar = :tutar 
                    WHERE name = :kart_adi 
                    AND aktif_donem = :donem
                ")->execute([
                    'tutar'    => $vakif_kesinlesen_donem_tutar, 
                    'kart_adi' => 'kredi_karti_o',
                    'donem'    => $aktif_donem
                ]);
            }

            $db->commit();
            ob_clean(); 
            echo json_encode(['status' => 'success']); 
            exit;

        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            ob_clean(); 
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); 
            exit;
        }
    }

    // Orta Sütun

    if ($islem === 'gelir_kaydet') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');

        $kalemler_data = $_POST['kalemler'] ?? [];


        try {
            $db->beginTransaction();

            foreach ($kalemler_data as $k_id => $data) {
                $k_id  = intval($k_id); 
                $tutar = floatval(str_replace(',', '.', $data['tutar'] ?? 0));

                if ($k_id <= 0) continue;

                // Satırın mevcut durumunu ve name anahtarını çekiyoruz
                $kontrol = $db->prepare("SELECT name, is_locked, turu FROM butce_akis WHERE id = ? AND aktif_donem = ? LIMIT 1");
                $kontrol->execute([$k_id, $aktif_donem]);
                $satir = $kontrol->fetch(PDO::FETCH_ASSOC);

                // Emniyet Bariyeri: Satır yoksa, gelir değilse veya ödenip kilitlendiyse kesinlikle dokunma!
                if (!$satir || $satir['turu'] !== 'gelir' || intval($satir['is_locked']) === 1) {
                    continue;
                }

                $sysName = trim($satir['name']);

                // 🎯 KURAL: Sadece maas_o, maas_a ve maktu_mesai name anahtarına sahip satırların TUTARI güncellenir!
                // Kalem adına (isim) ve alt satırdan eklenen ek gelirlere kesinlikle dokunulmaz!
                if (in_array($sysName, ['maas_o', 'maas_a', 'maktu_mesai'])) {
                    $db->prepare("UPDATE butce_akis SET tutar = :tutar WHERE id = :id AND aktif_donem = :donem")
                       ->execute([
                           'tutar' => $tutar, 
                           'id'    => $k_id, 
                           'donem' => $aktif_donem
                       ]);
                }
            }

            $db->commit();
            ob_clean(); echo json_encode(['status' => 'success']); exit;

        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            ob_clean(); echo json_encode(['status' => 'error', 'message' => "Gelir Toplu Kayıt Hatası: " . $e->getMessage()]); exit;
        }
    }

    if ($islem === 'gider_kaydet') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');

        $kalemler_data = $_POST['kalemler'] ?? [];


        try {
            $db->beginTransaction();

            // 🎯 Sadece bütçe tutarları güncellenecek mühürlü şablon sabitleri listesi
            $guncellenecek_sabitler = ['kredi_karti_a', 'kredi_karti_o','elektrik', 'su', 'dogalgaz', 'internet', 'aidat'];

            foreach ($kalemler_data as $k_id => $data) {
                $k_id  = intval($k_id); 
                $tutar = floatval(str_replace(',', '.', $data['tutar'] ?? 0));

                if ($k_id <= 0) continue;

                // Satırın mevcut durumunu ve name anahtarını çekiyoruz
                $kontrol = $db->prepare("SELECT name, is_locked, turu FROM butce_akis WHERE id = ? AND aktif_donem = ? LIMIT 1");
                $kontrol->execute([$k_id, $aktif_donem]);
                $satir = $kontrol->fetch(PDO::FETCH_ASSOC);

                // Emniyet Bariyeri: Satır yoksa, gider değilse veya ödenip kilitlendiyse kesinlikle dokunma!
                if (!$satir || $satir['turu'] !== 'gider' || intval($satir['is_locked']) === 1) {
                    continue;
                }

                $sysName = trim($satir['name']);

                // 🎯 KURAL 1: Eğer hedef listesindeyse (elektrik, su, dogalgaz, internet, aidat) SADECE TUTAR güncellenir!
                // 🎯 KURAL 2: Eğer $sysName === 'harcama_nakit' ise (alt satırdan eklenen ek giderler) buraya asla giremez, tutarları sıfır sızıntıyla korunur!
                if (in_array($sysName, $guncellenecek_sabitler)) {
                    $db->prepare("UPDATE butce_akis SET tutar = :tutar WHERE id = :id AND aktif_donem = :donem")
                       ->execute([
                           'tutar' => $tutar, 
                           'id'    => $k_id, 
                           'donem' => $aktif_donem
                       ]);
                }
            }

            $db->commit();
            ob_clean(); echo json_encode(['status' => 'success']); exit;

        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            ob_clean(); echo json_encode(['status' => 'error', 'message' => "Gider Toplu Kayıt Hatası: " . $e->getMessage()]); exit;
        }
    }

    if ($islem === 'birimFiyat_kaydet') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');

        // Gelen verileri güvenli float süzgecinden geçirerek oturuma mühürlüyoruz
        $_SESSION['sys_telefon_birim_fiyat'] = floatval(str_replace(',', '.', $_POST['telefon_birim_fiyat'] ?? 340.00));
        $_SESSION['sys_su_birim_fiyat']      = floatval(str_replace(',', '.', $_POST['su_birim_fiyat'] ?? 140.00));

        ob_clean(); 
        echo json_encode(['status' => 'success']); 
        exit;
    }

    if ($islem === 'gelir_kalem_ekle') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');

        $kalemAdi   = trim($_POST['kalem_adi'] ?? '');
        $havuzKodu  = trim($_POST['havuz_kodu'] ?? '');
        $aktifDonem = $_SESSION['aktif_donem'] ?? date('Y-m');
        $tutar      = floatval(str_replace(',', '.', $_POST['tutar'] ?? 0));
        // JS'den gelen öz hakiki tür (Varsayılan 'gelir')
        $turu       = trim($_POST['turu'] ?? 'gelir'); 

        if (empty($kalemAdi) || $tutar <= 0 || empty($havuzKodu)) {
            ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Eksik veya hatalı parametre girdisi!']); exit;
        }

        try {
            // 🎯 MÜHÜR: turu parametresi açıkça 'gelir' olarak SQL'e yazılır, gidere karışamaz!
            $sorgu = $db->prepare("INSERT INTO butce_akis (kalem_adi, turu, tutar, odenen_tutar, islem_gunu, aktif_donem, odenme_tarihi, is_locked, havuz_kodu) VALUES (?, ?, ?, 0.00, ?, ?, NULL, 0, ?)");
            
            $islemGunu = intval(date('d'));
            $durum = $sorgu->execute([$kalemAdi, $turu, $tutar, $islemGunu, $aktifDonem, $havuzKodu]);

            if ($durum) {
                ob_clean(); echo json_encode(['status' => 'success']); exit;
            } else {
                ob_clean(); echo json_encode(['status' => 'error', 'message' => 'SQL bütçe yazma hatası gerçekleşti!']); exit;
            }
        } catch (Exception $e) {
            ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Veritabanı Hatası: ' . $e->getMessage()]); exit;
        }
    }

    if ($islem === 'gider_kalem_ekle') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');

        $kalemAdi   = trim($_POST['kalem_adi'] ?? '');
        $havuzKodu  = trim($_POST['havuz_kodu'] ?? '');
        $aktifDonem = $_SESSION['aktif_donem'] ?? date('Y-m');
        $tutar      = floatval(str_replace(',', '.', $_POST['tutar'] ?? 0));
        $turu       = trim($_POST['turu'] ?? 'gider'); // JS'den gelen saf gider kimliği

        if (empty($kalemAdi) || $tutar <= 0 || empty($havuzKodu)) {
            ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Eksik veya hatalı parametre girdisi!']); exit;
        }

        try {
            // 🎯 MÜHÜR 1: turu alanı kesinlikle 'gider' olarak SQL'e mühürlenir
            $sorgu = $db->prepare("INSERT INTO butce_akis (kalem_adi, turu, tutar, odenen_tutar, islem_gunu, aktif_donem, odenme_tarihi, is_locked, havuz_kodu) VALUES (?, ?, ?, 0.00, ?, ?, NULL, 0, ?)");
            $islemGunu = intval(date('d'));
            $sorgu->execute([$kalemAdi, $turu, $tutar, $islemGunu, $aktifDonem, $havuzKodu]);

            ob_clean(); echo json_encode(['status' => 'success']); exit;
        } catch (Exception $e) {
            ob_clean(); echo json_encode(['status' => 'error', 'message' => 'SQL Gider Ekleme Hatası: ' . $e->getMessage()]); exit;
        }
    }

    if ($islem === 'gelir_eft') {
        if (ob_get_length()) { ob_clean(); } else { while (ob_get_level()) { ob_end_clean(); } ob_start(); }
        header('Content-Type: application/json; charset=utf-8');

        $gelen_isim  = trim($_POST['kalem_adi'] ?? '');
        $gelirTuru   = trim($_POST['gelir_turu'] ?? 'dinamik_ek_gelir');
        $havuzKodu   = trim($_POST['havuz_kodu'] ?? '');
        $id          = intval($_POST['id'] ?? 0);
        $aktifDonem  = $_SESSION['aktif_donem'] ?? date('Y-m');
        $kAdiKucuk   = mb_strtolower($gelen_isim, 'UTF-8');

        if (strpos($gelen_isim, '(') !== false) {
            $parcalar = explode('(', $gelen_isim);
            $gelen_isim = trim($parcalar[0]);
            $kAdiKucuk  = mb_strtolower($gelen_isim, 'UTF-8');
        }

        $hamTutar = $_POST['odenen_tutar'] ?? 0;
        $odenenTutar = floatval(str_replace(',', '.', $hamTutar));

        if ($gelirTuru === 'ozge_maas')     $odenenTutar = floatval(str_replace(',', '.', $_POST['toplam_maas_tutar'] ?? 0));
        if ($gelirTuru === 'maktu_mesai')   $odenenTutar = floatval(str_replace(',', '.', $_POST['maktu_tutar'] ?? 0));
        if ($gelirTuru === 'armagan_maas')  $odenenTutar = floatval(str_replace(',', '.', $_POST['armagan_maas_tutar'] ?? 0));

        if (empty($gelen_isim) || $odenenTutar <= 0) {
            ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Parametre Hatası: Kalem adı boş veya tutar bütçe sınırları dışında!']); exit;
        }

        try {
            $db->beginTransaction();

            // 🎯 REFORM 1: butce_akis tablosunda sanal satır aranırken 'turu = "gelir"' şartı zorunlu kılındı!
            // Böylece isminde "su" yazan gerçek bir gider faturası satırı asla yanlışlıkla mühürlenemez.
            if ($id <= 0 && !empty($gelen_isim)) {
                $arama_kelimesi = '%' . $gelen_isim . '%';
                $kontrolSorgu = $db->prepare("SELECT id FROM butce_akis WHERE aktif_donem = ? AND turu = 'gelir' AND (kalem_adi LIKE ? OR ? LIKE CONCAT('%', kalem_adi, '%')) LIMIT 1");
                $kontrolSorgu->execute([$aktifDonem, $arama_kelimesi, $gelen_isim]);
                $id = intval($kontrolSorgu->fetchColumn());
            }

            // 🎯 REFORM 2: Eğer id varsa, bu satırın gerçekten bir GELİR satırı olduğunu ve kilitli olmadığını DB düzeyinde bir kez daha doğrula!
            if ($id > 0) {
                $kalemSorgu = $db->prepare("SELECT id, havuz_kodu, is_locked, turu FROM butce_akis WHERE id = ? LIMIT 1");
                $kalemSorgu->execute([$id]);
                $kalemRow = $kalemSorgu->fetch(PDO::FETCH_ASSOC);

                if (!$kalemRow || $kalemRow['turu'] !== 'gelir') {
                    throw new Exception("Bariyer Blokesi: Doğrulamaya çalışılan bütçe kalemi bir GELİR satırı değil!");
                }
                if (intval($kalemRow['is_locked']) === 1) {
                    throw new Exception("Bu bütçe kalemi daha önce zaten ödenmiş ve kilitlenmiş!");
                }

                if (!empty($kalemRow['havuz_kodu'])) { $havuzKodu = trim($kalemRow['havuz_kodu']); }
            }

            if (empty($havuzKodu)) { $havuzKodu = 'isbank_a'; }

            // 🎯 REFORM 3: UPSERT - Yoksa otomatik oluştur (turu='gelir'), varsa id ile kilitle
            if ($id <= 0) {
                $eklemeSorgu = $db->prepare("INSERT INTO butce_akis (kalem_adi, turu, tutar, odenen_tutar, islem_gunu, aktif_donem, odenme_tarihi, is_locked, havuz_kodu) VALUES (?, 'gelir', ?, ?, ?, ?, NOW(), 1, ?)");
                $islemGunu = intval(date('d'));
                $eklemeSorgu->execute([$gelen_isim, $odenenTutar, $odenenTutar, $islemGunu, $aktifDonem, $havuzKodu]);
                $id = intval($db->lastInsertId());
            } else {
                $mühürSorgu = $db->prepare("UPDATE butce_akis SET odenen_tutar = ?, is_locked = 1, odenme_tarihi = NOW() WHERE id = ?");
                $mühürSorgu->execute([$odenenTutar, $id]);
            }

            // 3. ADIM: SAF SQL MATRAH BÜYÜTME MOTORU (+)
            $is_ozge_sorgu = ($gelirTuru === 'ozge_maas' || preg_match('/zge.*maa/i', $kAdiKucuk) || strpos($kAdiKucuk, 'özge') !== false);

            if ($is_ozge_sorgu) {
                $hamIsbankO = $evKredisiHam ?? $evKredisiConst;
                $isbankOTutari = floatval(str_replace(',', '.', $hamIsbankO));
                if ($isbankOTutari <= 0) $isbankOTutari = $evKredisiConst;

                $isbankATutari = max(0, $odenenTutar - $isbankOTutari);

                $db->prepare("UPDATE finansal_havuzlar SET anapara = anapara + ? WHERE havuz_kodu = 'isbank_o'")->execute([$isbankOTutari]);
                $db->prepare("UPDATE finansal_havuzlar SET anapara = anapara + ? WHERE havuz_kodu = 'isbank_a'")->execute([$isbankATutari]);
                
                if ($id > 0) {
                    $db->prepare("UPDATE butce_akis SET havuz_kodu = 'isbank_a' WHERE id = ?")->execute([$id]);
                }
            } else {
                $havuzMühür = $db->prepare("UPDATE finansal_havuzlar SET anapara = anapara + ? WHERE havuz_kodu = ?");
                $havuzMühür->execute([$odenenTutar, $havuzKodu]);
            }

            // 4. ADIM: aylik_harcama_raporu Tablosunun Kümülatif Güncellenmesi
            $raporKontrol = $db->prepare("SELECT COUNT(*) FROM aylik_harcama_raporu WHERE donem = ?");
            $raporKontrol->execute([$aktifDonem]);
            $raporVarMi = (intval($raporKontrol->fetchColumn()) > 0);

            $raporSutun = 'ek_gelir'; 
            if ($is_ozge_sorgu) {
                $raporSutun = 'maas_o';
            } elseif ($gelirTuru === 'maktu_mesai' || strpos($kAdiKucuk, 'maktu') !== false || strpos($kAdiKucuk, 'mesai') !== false) {
                $raporSutun = 'maktu_mesai';
            } elseif ($gelirTuru === 'armagan_maas' || (strpos($kAdiKucuk, 'armağan') !== false && strpos($kAdiKucuk, 'maaş') !== false)) {
                $raporSutun = 'maas_a';
            }

            if ($raporVarMi) {
                if ($raporSutun === 'maas_o' || $raporSutun === 'maas_a') {
                    $db->prepare("UPDATE `aylik_harcama_raporu` SET `{$raporSutun}` = ? WHERE `donem` = ?")->execute([$odenenTutar, $aktifDonem]);
                } else {
                    $db->prepare("UPDATE `aylik_harcama_raporu` SET `{$raporSutun}` = `{$raporSutun}` + ? WHERE `donem` = ?")->execute([$odenenTutar, $aktifDonem]);
                }
            } else {
                $db->prepare("INSERT INTO `aylik_harcama_raporu` (donem, {$raporSutun}) VALUES (?, ?)")->execute([$aktifDonem, $odenenTutar]);
            }

            $db->commit();
            ob_clean(); echo json_encode(['status' => 'success']); exit;

        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            ob_clean(); echo json_encode(['status' => 'error', 'message' => 'SQL Mühürleme Hatası: ' . $e->getMessage()]); exit;
        }
    }

    if ($islem === 'gider_ode') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');

        $id          = intval($_POST['id'] ?? 0);
        $odenenTutar = floatval(str_replace(',', '.', $_POST['odenen_tutar'] ?? 0));
        $havuzKodu   = trim($_POST['havuz_kodu'] ?? ''); // Ön yüzden (hizadaki selectboxtan) gelen anlık seçim
        $aktifDonem  = $_SESSION['aktif_donem'] ?? date('Y-m');

        if ($id <= 0 || $odenenTutar <= 0) {
            ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Geçersiz bütçe ID veya tutar girdisi!']); exit;
        }

        try {
            $db->beginTransaction();

            // 1. ADIM: Satır durumunu ve name anahtarını tek hamlede çekiyoruz
            $kalemSorgu = $db->prepare("SELECT id, kalem_adi, name, havuz_kodu, is_locked, turu FROM butce_akis WHERE id = ? LIMIT 1");
            $kalemSorgu->execute([$id]);
            $kalem = $kalemSorgu->fetch(PDO::FETCH_ASSOC);

            if (!$kalem || $kalem['turu'] !== 'gider') { throw new Exception("Gider cetvelinde bu kayda ulaşılamadı!"); }
            if (intval($kalem['is_locked']) === 1) { throw new Exception("Bu gider kalemi daha önce zaten ödenmiş!"); }

            $sysName = trim($kalem['name']); // maas_o, maas_a, internet, harcama_nakit vb.

            // 🎯 REFORM BARİYERİ: Telefon faturası kalemi ana ödeme motorundan kesinlikle muaftır!
            if ($sysName === 'telefon') {
                throw new Exception("🔒 Telefon ödemeleri sadece özel (Ö) ve (A) sayaç butonları üzerinden yapılabilir.");
            }

            // 🎯 ROTASYON MATRİSİ 1: HARCAMA YAPILACAK KAYNAK HAVUZUN MUTLAK DOĞRU SEÇİMİ
            // Öncelik: Eğer veritabanındaki satırda bir havuz mühürlüyse onu al, yoksa ön yüzden gelen anlık parametreyi seç!
            $hedefHavuz = !empty($kalem['havuz_kodu']) ? trim($kalem['havuz_kodu']) : $havuzKodu;

            // Sadece ŞABLON SABİTLERİ ( name alanları ) için havuz zorlaması yapılır!
            if ($sysName === 'ev_kredisi') {
                $hedefHavuz = 'isbank_o'; // Ev Kredisi her zaman İşbank Ö
            } elseif ($sysName === 'kredi_karti_a' || $sysName === 'kredi_karti_o') {
                $hedefHavuz = 'isbank_a'; // Kredi Kartları her zaman İşbank A
            } elseif (in_array($sysName, ['elektrik', 'su', 'dogalgaz', 'internet'])) {
                $hedefHavuz = 'vakif_o';   // Orijinal sabit faturalar her zaman Vakıfbank Ö
            } elseif ($sysName === 'aidat') {
                $hedefHavuz = 'nakit';     // Sabit konut aidatı her zaman Nakit
            }
            // 🎯 KESİN REFORM: Eğer $sysName === 'harcama_nakit' ise (yani alt satırdan eklenen dinamik harcamalarda)
            // yukarıdaki if bloklarına ASLA girmez; veritabanında selectboxtan ne kaydedildiyse (`$hedefHavuz`) kuruşu kuruşuna o kalır!

            if (empty($hedefHavuz)) { $hedefHavuz = 'isbank_a'; } // Emniyet barajı
            $is_nergiz = ($sysName === 'nergiz_cep');

            if (!$is_nergiz) {
                // 2. ADIM: KASADAKİ LİKİDİTE KONTROLÜ VE BLOKAJ ( Tam Hedeflenen Havuz Taranır )
                $havuzKontrol = $db->prepare("SELECT havuz_adi, anapara FROM finansal_havuzlar WHERE havuz_kodu = ? LIMIT 1");
                $havuzKontrol->execute([$hedefHavuz]);
                $havuzRow = $havuzKontrol->fetch(PDO::FETCH_ASSOC);

                if (!$havuzRow) { throw new Exception("Sistemde '{$hedefHavuz}' koduna ait finansal havuz bulunamadı!"); }

                $mevcutBakiye = floatval($havuzRow['anapara']);
                if ($mevcutBakiye < $odenenTutar) {
                    throw new Exception("❌ Ödeme Reddedildi! {$havuzRow['havuz_adi']} hesabınızda yeterli bakiye yok.\nMevcut Bakiye: " . number_format($mevcutBakiye, 2, ',', '.') . " ₺");
                }

                // 3. ADIM: SAF SQL MATRAH AŞINDIRMA MOTORU (-)
                $db->prepare("UPDATE finansal_havuzlar SET anapara = anapara - ? WHERE havuz_kodu = ?")
                   ->execute([$odenenTutar, $hedefHavuz]);
            }

            // 4. ADIM: butce_akis tablosunda satırı mühürle, kilitle ve o öz hakiki hedef havuz kodunu yaz!
            $db->prepare("UPDATE butce_akis SET odenen_tutar = ?, is_locked = 1, odenme_tarihi = NOW(), havuz_kodu = ? WHERE id = ?")
               ->execute([$odenenTutar, $hedefHavuz, $id]);

            // 5. ADIM: aylik_harcama_raporu TABLOSU SAF SENKRONİZASYONU
            if (!$is_nergiz) {
                $raporKontrol = $db->prepare("SELECT COUNT(*) FROM aylik_harcama_raporu WHERE donem = ?");
                $raporKontrol->execute([$aktifDonem]);
                $raporVarMi = (intval($raporKontrol->fetchColumn()) > 0);

                $raporSutun = $sysName; // Dinamik harcamalarda tam karşılığı olan 'harcama_nakit' sütununa işlenir

                if ($raporVarMi) {
                    $db->prepare("UPDATE `aylik_harcama_raporu` SET `{$raporSutun}` = `{$raporSutun}` + ? WHERE `donem` = ?")
                       ->execute([$odenenTutar, $aktifDonem]);
                } else {
                    $db->prepare("INSERT INTO `aylik_harcama_raporu` (donem, `{$raporSutun}`) VALUES (?, ?)")
                       ->execute([$aktifDonem, $odenenTutar]);
                }
            }

            $db->commit();
            ob_clean(); echo json_encode(['status' => 'success']); exit;

        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            ob_clean(); echo json_encode(['status' => 'error', 'message' => "Gider Ödeme Hatası: " . $e->getMessage()]); exit;
        }
    }

    if ($islem === 'damacana_ode') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');

        $su_id       = intval($_POST['id'] ?? ($_POST['damacana_id'] ?? 0));
        $birim_fiyat = floatval($_POST['birim_fiyat'] ?? 140);
        $yon         = trim($_POST['yon'] ?? '');


        // 🎯 REFORM 1: Parametre ismi kayıp veya sıfır gelse dahi,
        // name anahtarı ve Kalem Adı üzerinden gerçek satırı bulma zırhı!
        if ($su_id <= 0) {
            $idKurtar = $db->prepare("SELECT id FROM butce_akis WHERE aktif_donem = ? AND turu = 'gider' AND name = 'harcama_nakit' AND (kalem_adi LIKE '%Damacana%' OR kalem_adi LIKE '%damacana%') LIMIT 1");
            $idKurtar->execute([$aktif_donem]);
            $su_id = intval($idKurtar->fetchColumn());
        }

        if ($su_id <= 0) {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Parametre Hatası: Veritabanında Damacana Su satırı bulunamadı!']);
            exit;
        }

        try {
            // Veritabanından mühürlü gerçek su bütçesini anlık çekiyoruz
            $stmt = $db->prepare("SELECT tutar FROM butce_akis WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $su_id]);
            $su_row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$su_row) { 
                ob_clean(); 
                echo json_encode(['status' => 'error', 'message' => 'Bütçe akış cetvelinde bu su kalemi bulunamadı!']); 
                exit; 
            }

            // --- YÖN +1 SÜRECİ (İPTAL / İADE AKSİYONU) ---
            if ($yon === '+1') {
                $db->beginTransaction();

                // 1. Matrah Mühürü: butce_akis tablosundaki bütçeyi geri arttır
                $db->prepare("UPDATE butce_akis SET tutar = tutar + :fiyat WHERE id = :id")
                   ->execute(['fiyat' => $birim_fiyat, 'id' => $su_id]);

                // 2. Matrah Mühürü: Nakit (Ev) anaparasını fiziksel olarak geri iade et (+)
                $db->prepare("UPDATE finansal_havuzlar SET anapara = anapara + ? WHERE havuz_kodu = 'nakit'")
                   ->execute([$birim_fiyat]);

                $db->commit();
                ob_clean(); echo json_encode(['status' => 'success']); exit;
            }

            // --- YÖN -1 SÜRECİ (SU SİPARİŞİ / KASADAN NET ERİME) ---
            if ($yon === '-1') {
                $mevcut_su_butcesi = floatval($su_row['tutar']);
                if ($mevcut_su_butcesi < $birim_fiyat) { 
                    throw new Exception("⚠️ Kalan damacana su bütçeniz tükenmiştir! Sipariş verilemez."); 
                }

                $db->beginTransaction();

                // NAKİT KASASINDA FİZİKSEL PARA VAR MI KONTROLÜ
                $nakitKontrol = $db->prepare("SELECT anapara FROM finansal_havuzlar WHERE havuz_kodu = 'nakit' LIMIT 1");
                $nakitKontrol->execute();
                $nakitRow = $nakitKontrol->fetch(PDO::FETCH_ASSOC);
                $mevcutNakit = floatval($nakitRow['anapara'] ?? 0);

                if ($mevcutNakit < $birim_fiyat) {
                    throw new Exception("❌ Ödeme Reddedildi! Nakit (Ev) havuzunda yeterli para kalmadı.\nMevcut Nakit: " . number_format($mevcutNakit, 2, ',', '.') . " ₺");
                }

                // 1. Matrah Mühürü: butce_akis tablosundaki su bütçesini azalt (Kalan Su 140 TL eksilir)
                $yeni_tutar = max(0, $mevcut_su_butcesi - $birim_fiyat);
                $db->prepare("UPDATE butce_akis SET tutar = :yeni_tutar, odenme_tarihi = NOW() WHERE id = :id")
                   ->execute(['yeni_tutar' => $yeni_tutar, 'id' => $su_id]);

                // 2. Matrah Mühürü: Nakit (Ev) anaparasını fiziksel olarak aşındır (-)
                $db->prepare("UPDATE finansal_havuzlar SET anapara = anapara - ? WHERE havuz_kodu = 'nakit'")
                   ->execute([$birim_fiyat]);

                // 3. Matrah Mühürü: aylik_harcama_raporu tablosundaki harcama_nakit sütununu kümülatif büyüt (+)
                $db->prepare("INSERT IGNORE INTO `aylik_harcama_raporu` (`donem`) VALUES (:donem)")
                   ->execute(['donem' => $aktif_donem]);
                $db->prepare("UPDATE `aylik_harcama_raporu` SET `harcama_nakit` = `harcama_nakit` + :fiyat WHERE `donem` = :donem")
                   ->execute(['fiyat' => $birim_fiyat, 'donem' => $aktif_donem]);

                $db->commit();
                ob_clean(); echo json_encode(['status' => 'success']); exit;
            }
            
        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            ob_clean(); echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); exit;
        }
    }

    if ($islem === 'telefon_ode') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');

        $tel_id      = intval($_POST['id'] ?? 0);
        $birim_fiyat = floatval($_POST['birim_fiyat'] ?? 340);


        if ($tel_id <= 0 || $birim_fiyat <= 0) {
            ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Geçersiz parametre!']); exit;
        }

        $stmt = $db->prepare("SELECT tutar, odenen_tutar FROM butce_akis WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $tel_id]);
        $tel_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tel_row) { ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Kayıt bulunamadı!']); exit; }

        try {
            $orijinal_plan_tutari = floatval($tel_row['tutar']); // 680 TL Sabit Kalır
            $mevcut_odenen_matrah = floatval($tel_row['odenen_tutar']);

            // EMNİYET BARİYERİ: Eğer zaten iki adım da ödendiyse (680 TL) yeni basışı bloke et
            if ($mevcut_odenen_matrah >= $orijinal_plan_tutari) {
                throw new Exception("🔒 Bu döneme ait tüm telefon ödeme adımları tamamlanmıştır.");
            }

            $db->beginTransaction();

            // Vakıfbank Ö kasasında para var mı kontrolü
            $vakifKontrol = $db->query("SELECT anapara FROM finansal_havuzlar WHERE havuz_kodu = 'vakif_o' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $mevcutVakif = floatval($vakifKontrol['anapara'] ?? 0);

            if ($mevcutVakif < $birim_fiyat) {
                throw new Exception("❌ Ödeme Reddedildi! Vakıfbank (Ö) hesabınızda yeterli bakiye yok.");
            }

            // 🎯 MÜHÜR 1: tutar sütunu asla eksilmez! odenen_tutar her basışta +340 TL eklenerek büyür.
            $yeni_odenen = $mevcut_odenen_matrah + $birim_fiyat;

            // Eğer bu harcamayla beraber toplam ödenen plan tutarına ulaştıysa satır is_locked=1 ile mühürlenir
            $is_locked = ($yeni_odenen >= $orijinal_plan_tutari) ? 1 : 0;

            $db->prepare("UPDATE butce_akis SET odenen_tutar = ?, is_locked = ?, odenme_tarihi = NOW(), havuz_kodu = 'vakif_o' WHERE id = ?")
               ->execute([$yeni_odenen, $is_locked, $tel_id]);

            // 🎯 MÜHÜR 2: Parayı doğrudan veritabanındaki Vakıfbank (Ö) kasasından düşüyoruz (-)
            $db->prepare("UPDATE finansal_havuzlar SET anapara = anapara - ? WHERE havuz_kodu = 'vakif_o'")->execute([$birim_fiyat]);

            // 🎯 MÜHÜR 3: Harcama raporundaki 'telefon' sütununu kümülatif büyüt (+)
            $db->prepare("INSERT IGNORE INTO `aylik_harcama_raporu` (`donem`) VALUES (:donem)")->execute(['donem' => $aktif_donem]);
            $db->prepare("UPDATE `aylik_harcama_raporu` SET `telefon` = `telefon` + :fiyat WHERE `donem` = :donem")->execute(['fiyat' => $birim_fiyat, 'donem' => $aktif_donem]);

            $db->commit();
            ob_clean(); echo json_encode(['status' => 'success']); exit;

        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            ob_clean(); echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); exit;
        }
    }

    if ($islem === 'bakiye_devret') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');

        $havuzKodu = trim($_POST['havuz_kodu'] ?? '');
        $tutar     = floatval(str_replace(',', '.', $_POST['tutar'] ?? 0));

        if (empty($havuzKodu) || $tutar <= 0) {
            ob_clean(); 
            echo json_encode(['status' => 'error', 'message' => 'Parametre Hatası: Eksik havuz kodu veya sıfırdan küçük tutar!']); 
            exit;
        }

        try {
            $db->beginTransaction();

            // 1. ADIM: Hedef finansal havuzun sistemde var olduğunu kesin doğrula
            $havuzKontrol = $db->prepare("SELECT id, havuz_adi FROM finansal_havuzlar WHERE havuz_kodu = ? LIMIT 1");
            $havuzKontrol->execute([$havuzKodu]);
            $havuzRow = $havuzKontrol->fetch(PDO::FETCH_ASSOC);

            if (!$havuzRow) {
                throw new Exception("Sistemde '{$havuzKodu}' koduna ait aktif bir finansal havuz cüzdanı bulunamadı!");
            }

            // 2. ADIM: 🎯 MÜHÜR: butce_akis tablosuna ve aylik_harcama_raporu tablosuna KESİNLİKLE müdahale edilmiyor!
            // Para doğrudan veritabanındaki finansal_havuzlar tablosunda ilgili kasanın üzerine kümülatif eklenir (+).
            $enjeksiyonSorgu = $db->prepare("UPDATE finansal_havuzlar SET anapara = anapara + ? WHERE havuz_kodu = ?");
            $enjeksiyonSorgu->execute([$tutar, $havuzKodu]);

            $db->commit();
            ob_clean(); 
            echo json_encode(['status' => 'success']); 
            exit;

        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            ob_clean(); 
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); 
            exit;
        }
    }

    if ($islem === 'simulasyon_sifirla') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');
        


        try {
            $db->beginTransaction();

            // 🎯 EMNİYET KALKANI: Sayısal verilerin sıfır veya dize hatası üretme riskine karşı mutlak floatval koruması
            $saf_tel_tutar   = floatval(str_replace(',', '.', $aylikTelefonFaturasi));
            $saf_damacana_tutar = floatval(str_replace(',', '.', $aylikDamacanaTutari));
            $saf_ev_kredisi  = floatval(str_replace(',', '.', $evKredisiConst));
            $saf_internet_tutar = floatval(str_replace(',', '.', $aylikInternetFaturasiTutari));

            // 🎯 MÜHÜR 1: Tüm bütçe akış planının ödeme matrahlarını temizle (odenen_tutar = 0, is_locked = 0)
            // Özel bütçeli sayaç alanlarının tutarlarını ezmemek için name kalkanları vurulmuştur.
            $db->prepare("UPDATE butce_akis SET odenen_tutar = 0.00, odenme_tarihi = NULL, is_locked = 0 
                          WHERE aktif_donem = :donem 
                          AND name NOT IN ('telefon', 'harcama_nakit', 'internet')")
               ->execute(['donem' => $aktif_donem]);

            // 🎯 MÜHÜR 2: TELEFON satırının bütçesini ve kilitlerini öz hakiki name alanı üzerinden mühürle
            $db->prepare("UPDATE butce_akis SET tutar = :tel_butce, odenen_tutar = 0.00, is_locked = 0 
                          WHERE aktif_donem = :donem AND name = 'telefon'")
               ->execute(['tel_butce' => $saf_tel_tutar, 'donem' => $aktif_donem]);

            // 🎯 MÜHÜR 3: DAMACANA SU satırının bütçesini ve kilitlerini öz hakiki name alanı üzerinden mühürle
            $db->prepare("UPDATE butce_akis SET tutar = :su_butce, odenen_tutar = 0.00, is_locked = 0 
                          WHERE aktif_donem = :donem AND name = 'harcama_nakit' AND kalem_adi LIKE '%Damacana%'")
               ->execute(['su_butce' => $saf_damacana_tutar, 'donem' => $aktif_donem]);

            // 🎯 MÜHÜR 3.5: İNTERNET satırının bütçesini ve kilitlerini öz hakiki name alanı üzerinden mühürle
            $db->prepare("UPDATE butce_akis SET tutar = :net_butce, odenen_tutar = 0.00, is_locked = 0 
                          WHERE aktif_donem = :donem AND name = 'internet'")
               ->execute(['net_butce' => $saf_internet_tutar, 'donem' => $aktif_donem]);

            // 🎯 MÜHÜR 4: SONRADAN EKLENEN TÜM DİNAMİK EK GİDER VE GELİRLERİ VERİTABANINDAN TEMİZLE
            $db->prepare("
                DELETE FROM butce_akis 
                WHERE aktif_donem = :donem 
                AND (
                    (turu = 'gider' AND (
                        name NOT IN ('ev_kredisi', 'kredi_karti_a', 'kredi_karti_o', 'elektrik', 'su', 'dogalgaz', 'telefon', 'internet', 'aidat')
                        AND kalem_adi NOT LIKE '%Damacana%'
                        AND kalem_adi NOT LIKE '%damacana%'
                        AND kalem_adi NOT LIKE '%Nergiz%'
                    )) 
                    OR 
                    (turu = 'gelir' AND name NOT IN ('maas_o', 'maas_a', 'maktu_mesai'))
                )
            ")->execute(['donem' => $aktif_donem]);
            // 🎯 MÜHÜR 5: Aylık harcama raporu tablosundaki tüm dikey sütunları kuruşu kuruşuna sıfırla
            $db->prepare("UPDATE `aylik_harcama_raporu` SET `maas_o`=0, `maas_a`=0, `maktu_mesai`=0, `ek_gelir`=0, `faiz_gelir`=0, `kredi_karti_o`=0, `kredi_karti_a`=0, `ev_kredisi`=0, `aidat`=0, `harcama_nakit`=0, `fatura_nakit`=0, `elektrik`=0, `su`=0, `dogalgaz`=0, `telefon`=0, `internet`=0 WHERE `donem` = :donem")
               ->execute(['donem' => $aktif_donem]);

            // 🎯 MÜHÜR 6: Faiz tablosundaki o aya ait tüm çoklu basım geçmişini temizle
            $db->prepare("DELETE FROM faiz_gelirleri WHERE donem = :donem")
               ->execute(['donem' => $aktif_donem]);

            // 🎯 MÜHÜR 7: Finansal havuzların nemasını ve bloke tutarlarını sıfırla (Kasa anaparaları korunur) ANAPARA DÜZENLENEBİLİR
            $db->query("UPDATE finansal_havuzlar SET anapara = 0.000, nema = 0.0000, bloke_tutar = 0.0000");
            //$db->query("UPDATE finansal_havuzlar SET nema = 0.0000, bloke_tutar = 0.0000");

            // Kredi faiz input bütçelerini fabrika ayarlarına çek
            $_SESSION['evKredisiHam'] = $evKredisiConst;
            $_SESSION['evKredisiFaizliNetTutar'] = $saf_ev_kredisi;
            $_SESSION['dinamik_faiz_gun_sayisi'] = 0;

            $db->commit();
            ob_clean(); echo json_encode(['status' => 'success']); exit;

        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            ob_clean(); echo json_encode(['status' => 'error', 'message' => "SQL Motor Hatası: " . $e->getMessage()]); exit;
        }
    }

    // sağ sütun

    if ($islem === 'faiz_hesapla') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');

        // 1. ADIM: AJAX post paketinden süzülen en taze anlık sayıları yakalıyoruz
        $anlikHam  = isset($_POST['evKredisiHam']) ? floatval($_POST['evKredisiHam']) : $evKredisiHam;
        $anlikOran = isset($_POST['faiz_orani']) ? floatval($_POST['faiz_orani']) : $faiz_orani;
        $anlikGun  = isset($_POST['faiz_gun_sayisi']) ? intval($_POST['faiz_gun_sayisi']) : $faiz_gun_sayisi;

        try {
            $db->beginTransaction();

            // 2. ADIM: 🎯 Veritabanından İşbank Ö ve İşbank A cüzdanlarının canlı anapara tutarlarını çekiyoruz
            $havuzSorgu = $db->query("SELECT havuz_kodu, anapara FROM finansal_havuzlar WHERE havuz_kodu IN ('isbank_o', 'isbank_a')");
            $havuzlar = $havuzSorgu->fetchAll(PDO::FETCH_ASSOC);

            $isbank_o_anapara = 0.00;
            $isbank_a_anapara = 0.00;

            foreach ($havuzlar as $havuz) {
                if ($havuz['havuz_kodu'] === 'isbank_o') $isbank_o_anapara = floatval($havuz['anapara']);
                if ($havuz['havuz_kodu'] === 'isbank_a') $isbank_a_anapara = floatval($havuz['anapara']);
            }

 
            $isbank_o_nema = netFaizHesapla($isbank_o_anapara, $anlikOran, $anlikGun, $faiz_stopaj);
            $isbank_a_nema = netFaizHesapla($isbank_a_anapara, $anlikOran, $anlikGun, $faiz_stopaj);
            $evKredisiFaizliNetTutar = 0;


            // 4. ADIM: 🎯 SAF SQL MÜHÜRLEME MOTORU (Nemalar cüzdanlara işleniyor)
            $nemaMühürÖ = $db->prepare("UPDATE finansal_havuzlar SET nema = ? WHERE havuz_kodu = 'isbank_o'");
            $nemaMühürÖ->execute([$isbank_o_nema]);

            $nemaMühürA = $db->prepare("UPDATE finansal_havuzlar SET nema = ? WHERE havuz_kodu = 'isbank_a'");
            $nemaMühürA->execute([$isbank_a_nema]);

            // 5. ADIM: Gelecek sayfa yüklemeleri ve View katmanı için tüm session hafızasını tazeleyin
            $_SESSION['evKredisiHam']              = $anlikHam;
            $_SESSION['dinamik_faiz_orani']        = $anlikOran;
            $_SESSION['dinamik_faiz_gun_sayisi']   = $anlikGun;
            
            $_SESSION['isbank_o_nema']             = $isbank_o_nema;
            $_SESSION['isbank_a_nema']             = $isbank_a_nema;
            $_SESSION['evKredisiFaizliNetTutar']   = $evKredisiFaizliNetTutar; // Korumaya alındı

            $db->commit();
            ob_clean(); 
            echo json_encode(['status' => 'success', 'ev_kredisi_net' => $evKredisiFaizliNetTutar]); 
            exit;

        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            ob_clean(); 
            echo json_encode(['status' => 'error', 'message' => 'Nema Hesaplama SQL Hatası: ' . $e->getMessage()]); 
            exit;
        }
    }

    if ($islem === 'faiz_aktar') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');



        try {
            $db->beginTransaction();

            // 1. ADIM: Veritabanından İşbank Ö ve İşbank A cüzdanlarının güncel canlı NEMA değerlerini çekiyoruz
            $havuzSorgu = $db->query("SELECT havuz_kodu, anapara, nema FROM finansal_havuzlar WHERE havuz_kodu IN ('isbank_o', 'isbank_a')");
            $havuzlar = $havuzSorgu->fetchAll(PDO::FETCH_ASSOC);

            $nema_o = 0.00;
            $nema_a = 0.00;

            foreach ($havuzlar as $havuz) {
                if ($havuz['havuz_kodu'] === 'isbank_o') $nema_o = floatval($havuz['nema'] ?? 0);
                if ($havuz['havuz_kodu'] === 'isbank_a') $nema_a = floatval($havuz['nema'] ?? 0);
            }

            $toplam_aktarilacak_nema = $nema_o + $nema_a;

            // Emniyet Bariyeri: Eğer iki kasada da birikmiş canlı bir nema yoksa aktarımı bloke et
            if ($toplam_aktarilacak_nema <= 0) {
                throw new Exception("⚠️ Cüzdanlarınızda aktarılacak aktif bir canlı faiz (nema) geliri birikmemiştir!");
            }

            // 2. ADIM: 🎯 SAF SQL MATRAH BİLEŞTİRME MOTORU
            // Nemaları anaparanın üzerine ekliyoruz ve nema hücresini kuruşu kuruşuna 0.0000 yapıyoruz!
            $db->query("UPDATE finansal_havuzlar SET anapara = anapara + nema, nema = 0.0000 WHERE havuz_kodu IN ('isbank_o', 'isbank_a')");

            // 3. ADIM: 🎯 Arşiv kaydı için faiz_gelirleri tablosuna işlem mühürünü vuruyoruz
            $faizKayit = $db->prepare("INSERT INTO faiz_gelirleri (donem, faizgeliri_a, faizgeliri_o, tarih) VALUES (?, ?, ?, NOW())");
            $faizKayit->execute([$aktif_donem, $nema_a, $nema_o]);

            // 4. ADIM: 🎯 Bütçe akış planına (butce_akis) kilitli gelir belgelerini mühürlüyoruz
            // Not: Her faiz aktarımında yeni belge satırı eklenir (Mükerrerlik oluşturmaz, ek gelirdir)
            if ($nema_a > 0) {
                $insA = $db->prepare("INSERT INTO butce_akis (aktif_donem, kalem_adi, turu, name, tutar, odenen_tutar, islem_gunu, odenme_tarihi, is_locked, havuz_kodu) VALUES (?, 'İşbank (A) Faiz Geliri', 'gelir', 'ek_gelir', ?, ?, ?, NOW(), 1, 'isbank_a')");
                $insA->execute([$aktif_donem, $nema_a, $nema_a, intval(date('d'))]);
            }

            if ($nema_o > 0) {
                $insO = $db->prepare("INSERT INTO butce_akis (aktif_donem, kalem_adi, turu, name, tutar, odenen_tutar, islem_gunu, odenme_tarihi, is_locked, havuz_kodu) VALUES (?, 'İşbank (Ö) Faiz Geliri', 'gelir', 'ek_gelir', ?, ?, ?, NOW(), 1, 'isbank_o')");
                $insO->execute([$aktif_donem, $nema_o, $nema_o, intval(date('d'))]);
                
                // Faiz bütçesini bileşik büyüterek orijinal session hattına da mühür vuruyoruz
                if (isset($_SESSION['evKredisiFaizliNetTutar'])) {
                    $_SESSION['evKredisiFaizliNetTutar'] += $nema_o;
                }
            }

            // 5. ADIM: 🎯 aylik_harcama_raporu tablosundaki faiz_gelir sütununu KÜMÜLATİF büyütüyoruz (+=)
            $raporKontrol = $db->prepare("SELECT COUNT(*) FROM aylik_harcama_raporu WHERE donem = ?");
            $raporKontrol->execute([$aktif_donem]);
            $raporVarMi = (intval($raporKontrol->fetchColumn()) > 0);

            if ($raporVarMi) {
                // Birden fazla faiz geliri alabilmeniz için kuruşu kuruşuna kümülatif artı (+=) yönlü büyütülür
                $db->prepare("UPDATE `aylik_harcama_raporu` SET `faiz_gelir` = `faiz_gelir` + ? WHERE `donem` = ?")
                ->execute([$toplam_aktarilacak_nema, $aktif_donem]);
            } else {
                $db->prepare("INSERT INTO `aylik_harcama_raporu` (donem, `faiz_gelir`) VALUES (?, ?)")
                ->execute([$aktif_donem, $toplam_aktarilacak_nema]);
            }

            // Sayfa yenilendiğinde eski nemaların ekranda hayalet gibi kalmaması için session'ları sıfırlıyoruz
            $_SESSION['isbank_o_nema'] = 0.00;
            $_SESSION['isbank_a_nema'] = 0.00;
            $_SESSION['dinamik_faiz_gun_sayisi'] = 0;

            $faiz_gun_sayisi = $_SESSION['dinamik_faiz_gun_sayisi'];

            $db->commit();
            ob_clean(); echo json_encode(['status' => 'success']); exit;

        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            ob_clean(); echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); exit;
        }
    }

    if ($islem === 'mevduat_transfer') {
        
        // Temiz çıktı için tamponu temizleyelim (Mevcut controller mimarinizdeki standart koruma)
        if (ob_get_level() > 0) { ob_clean(); }
        header('Content-Type: application/json; charset=utf-8');

        // Gelen Parametrelerin Alınması ve Filtrelenmesi
        $kaynak_havuz = filter_var($_POST['kaynak_havuz'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $hedef_havuz  = filter_var($_POST['hedef_havuz'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $cozum_turu   = filter_var($_POST['cozum_turu'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $destek_havuz = filter_var($_POST['destek_havuz'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        
        // Önyüzden gelen ham para formatını float türüne dönüştürme
        $toplam_talep_tutari = floatval($_POST['toplam_talep_tutari'] ?? 0);

        // Temel Güvenlik Bariyeri
        if (empty($kaynak_havuz) || empty($hedef_havuz) || $toplam_talep_tutari <= 0 || $kaynak_havuz === $hedef_havuz) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz parametreler veya hatalı transfer tutarı!']);
            exit;
        }

        try {
            // Matematiksel bir hata durumunda havuzları korumak için Transaction Başlatıyoruz
            $db->beginTransaction();

            // 1. Kaynak Havuzun Güncel Veritabanı Anaparasını Çekelim
            // Krtitik Düzeltme: havuz_anahtari -> havuz_kodu yapıldı
            $stmt = $db->prepare("SELECT anapara FROM finansal_havuzlar WHERE havuz_kodu = :kod LIMIT 1");
            $stmt->execute([':kod' => $kaynak_havuz]);
            $kaynak_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$kaynak_data) {
                throw new Exception("Kaynak havuz veritabanında bulunamadı!");
            }

            $kaynak_mevcut_anapara = floatval($kaynak_data['anapara']);

            // Matematiksel Hesaplama Değişkenleri
            $ana_transfer_tutari = 0.00;
            $destek_transfer_tutari = 0.00;
            $toplam_aktarilan = 0.00;

            // 2. Senaryo Yönetim Matrisi
            if ($cozum_turu === 'yeterli') {
                if ($kaynak_mevcut_anapara < $toplam_talep_tutari) {
                    throw new Exception("Kaynak havuz bakiyesi yetersiz! Lütfen sayfayı yenileyip tekrar deneyin.");
                }
                $ana_transfer_tutari = $toplam_talep_tutari;
                $toplam_aktarilan    = $toplam_talep_tutari;

            } elseif ($cozum_turu === 'sadece_mevcut') {
                if ($kaynak_mevcut_anapara <= 0) {
                    throw new Exception("Kaynak havuzda aktarılacak bakiye yok!");
                }
                $ana_transfer_tutari = $kaynak_mevcut_anapara;
                $toplam_aktarilan    = $kaynak_mevcut_anapara;

            } elseif ($cozum_turu === 'destekli') {
                if (empty($destek_havuz) || $destek_havuz === $kaynak_havuz || $destek_havuz === $hedef_havuz) {
                    throw new Exception("Geçersiz destek havuz seçimi!");
                }

                // Destek havuzun anaparasını sorgulayalım
                // Krtitik Düzeltme: havuz_anahtari -> havuz_kodu yapıldı
                $stmtDestek = $db->prepare("SELECT anapara FROM finansal_havuzlar WHERE havuz_kodu = :kod LIMIT 1");
                $stmtDestek->execute([':kod' => $destek_havuz]);
                $destek_data = $stmtDestek->fetch(PDO::FETCH_ASSOC);

                if (!$destek_data) {
                    throw new Exception("Destek havuz veritabanında bulunamadı!");
                }

                $destek_mevcut_anapara = floatval($destek_data['anapara']);
                $eksik_tutar = $toplam_talep_tutari - $kaynak_mevcut_anapara;

                if ($destek_mevcut_anapara < $eksik_tutar) {
                    throw new Exception("Destek havuzunun bakiyesi de eksik tutarı kapatmaya yetmiyor!");
                }

                // Paylaşımlı transfer dağılımı
                $ana_transfer_tutari    = $kaynak_mevcut_anapara; 
                $destek_transfer_tutari = $eksik_tutar;
                $toplam_aktarilan       = $toplam_talep_tutari;

            } else {
                throw new Exception("Bilinmeyen transfer çözüm türü!");
            }

            // 3. Veritabanı Güncelleme Operasyonları (UPDATE)
            // Krtitik Düzeltmeler: havuz_anahtari -> havuz_kodu yapıldı
            
            // A) Kaynak havuzdan parayı düş
            if ($ana_transfer_tutari > 0) {
                $upKaynak = $db->prepare("UPDATE finansal_havuzlar SET anapara = anapara - :tutar WHERE havuz_kodu = :kod");
                $upKaynak->execute([':tutar' => $ana_transfer_tutari, ':kod' => $kaynak_havuz]);
            }

            // B) Varsa Destek havuzdan eksiği düş
            if ($destek_transfer_tutari > 0) {
                $upDestek = $db->prepare("UPDATE finansal_havuzlar SET anapara = anapara - :tutar WHERE havuz_kodu = :kod");
                $upDestek->execute([':tutar' => $destek_transfer_tutari, ':kod' => $destek_havuz]);
            }

            // C) Hedef havuza toplam parayı ekle
            if ($toplam_aktarilan > 0) {
                $upHedef = $db->prepare("UPDATE finansal_havuzlar SET anapara = anapara + :tutar WHERE havuz_kodu = :kod");
                $upHedef->execute([':tutar' => $toplam_aktarilan, ':kod' => $hedef_havuz]);
            }

            // 4. Akıllı Log Sistemini Besleme (INSERT)
            $logSql = "INSERT INTO transfer_logs 
                        (donem, kaynak_havuz, destek_havuz, hedef_havuz, ana_transfer_tutari, destek_transfer_tutari, toplam_aktarilan, cozum_turu, tarih) 
                    VALUES 
                        (:donem, :kaynak, :destek, :hedef, :ana_tutar, :destek_tutar, :toplam, :cozum, NOW())";
            
            $logStmt = $db->prepare($logSql);
            $logStmt->execute([
                ':donem'        => $aktif_donem,
                ':kaynak'       => $kaynak_havuz,
                ':destek'       => !empty($destek_havuz) && $destek_transfer_tutari > 0 ? $destek_havuz : null,
                ':hedef'        => $hedef_havuz,
                ':ana_tutar'    => $ana_transfer_tutari,
                ':destek_tutar' => $destek_transfer_tutari,
                ':toplam'       => $toplam_aktarilan,
                ':cozum'        => $cozum_turu
            ]);

            // İşlemleri taahhüt et ve kalıcı yap
            $db->commit();

            echo json_encode([
                'success' => true, 
                'message' => 'Likidite transferi başarıyla işlendi! Havuz anaparaları güncellendi.'
            ]);
            exit;

        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            
            echo json_encode([
                'success' => false, 
                'message' => 'Finansal Transfer İptal Edildi: ' . $e->getMessage()
            ]);
            exit;
        }
    }
    

}


$stmt_kalemler = $db->prepare("SELECT * FROM butce_akis WHERE aktif_donem = :donem ORDER BY id ASC");
$stmt_kalemler->execute(['donem' => $aktif_donem]); 
$kalemler = $stmt_kalemler->fetchAll(PDO::FETCH_ASSOC);


$db_taksitler = $db->query("SELECT * FROM cc_taksitler ORDER BY ay_index ASC")->fetchAll(PDO::FETCH_ASSOC);
$cc_meta = []; 
foreach ($db->query("SELECT * FROM kredi_kartlari")->fetchAll(PDO::FETCH_ASSOC) as $kart) { 
    $k = ($kart['kart_adi'] === 'vakifbank') ? 'vakif' : 'isbank'; 
    $cc_meta[$k . '_donem_ici'] = floatval($kart['donem_ici']); 
    $cc_meta[$k . '_provizyon'] = floatval($kart['provizyon']); 
    $cc_meta[$k . '_doviz'] = floatval($kart['doviz_borcu']); 
    $cc_meta[$k . '_ekstre'] = floatval($kart['kesinlesen_ekstre']); 
}
$isbank_aktif_taksit = 0; $vakif_aktif_taksit = 0; 
foreach ($db_taksitler as $t) { 
    if (intval($t['ay_index']) === $aktif_ay_index) { 
        $isbank_aktif_taksit = floatval($t['isbank_tutar']); 
        $vakif_aktif_taksit = floatval($t['vakif_tutar']); 
    } 
}
$isbank_card_borcu = $cc_meta['isbank_donem_ici'] + $cc_meta['isbank_provizyon'] + $cc_meta['isbank_doviz'] + $cc_meta['isbank_ekstre'] + $isbank_aktif_taksit;
$vakif_card_borcu = $cc_meta['vakif_donem_ici'] + $cc_meta['vakif_provizyon'] + $cc_meta['vakif_doviz'] + $cc_meta['vakif_ekstre'] + $vakif_aktif_taksit;


$gelirler = []; $giderler = []; 
foreach ($kalemler as $k) { if ($k['turu'] === 'gelir') $gelirler[] = $k; else $giderler[] = $k; }
// $live_kurlar = ForexExchange::getLiveRates()['kurlar']; 
// $canli_ana_yatirim_bakiye_tl = max(0, $isbank_a_bakiye - $net_ana_havuz_faiz_php);   


 







// 📊 HARCAMA MATRİSİNİ VE SÜTUN TOPLAMLARINI KÜMÜLATİF SENKRONİZE ETME
$db_raporlar = $db->query("SELECT * FROM `aylik_harcama_raporu` WHERE `donem` LIKE '{$aktif_yil}-%'")->fetchAll(PDO::FETCH_ASSOC);
$yillik_matris = [];
foreach ($db_raporlar as $r_satir) { $yillik_matris[intval(substr($r_satir['donem'], 5, 2))] = $r_satir; }

$tepe_toplamlar = ['maas_o'=>0,'maas_a'=>0,'maktu_mesai'=>0,'ek_gelir'=>0,'faiz_gelir'=>0,'kredi_karti_o'=>0,'kredi_karti_a'=>0,'ev_kredisi'=>0,'aidat'=>0,'harcama_nakit'=>0,'fatura_nakit'=>0,'elektrik'=>0,'su'=>0,'dogalgaz'=>0,'telefon'=>0,'internet'=>0,'yatirim_tutari'=>0];

for ($m = 1; $m <= 12; $m++) {
    if (isset($yillik_matris[$m])) {
        
        // 🎯 KESİN SENKRONİZASYON REFORMU: Mükerrer faiz şişmesini kökten engelle!
        if ($aktif_donem === sprintf("{$aktif_yil}-%02d", $m)) { 
            // Eğer veritabanındaki raporda halihazırda mühürlü bir faiz birikmişse (0'dan büyükse), sanal formüllerin onu şişirmesine izin verme!
            if (floatval($yillik_matris[$m]['faiz_gelir']) > 0) {
                // Veritabanındaki gerçek mühürlü cüzdan faizini olduğu gibi koru
                $yillik_matris[$m]['faiz_gelir'] = floatval($yillik_matris[$m]['faiz_gelir']);
            } else {
                // Eğer veritabanında henüz hiç faiz yoksa (Simülasyon başlangıcıysa) anlık canlı nema simülasyonunu göster
                //$yillik_matris[$m]['faiz_gelir'] = $harcama_tablosu_nihai_faizi; 
            }
        }
        
        foreach ($tepe_toplamlar as $s_key => $v) { 
            $tepe_toplamlar[$s_key] += floatval($yillik_matris[$m][$s_key] ?? 0); 
        }
    }
}


// =========================================================================
// 🏛️ REFORM: MUTLAKname VE SAF SQL TABANLI DASHBOARD VERİ MOTORU 🚀
// =========================================================================
try {
    // 1. Kasa Havuz ve Canlı Nema Verilerinin Çekilmesi
    $havuzSorgu = $db->query("SELECT havuz_kodu, anapara, nema FROM finansal_havuzlar");
    $havuzListesi = $havuzSorgu->fetchAll(PDO::FETCH_ASSOC);
    
    $havuzAnapara = [];
    $havuzNema     = [];
    foreach ($havuzListesi as $h) {
        $kod = trim($h['havuz_kodu']);
        $havuzAnapara[$kod] = floatval($h['anapara']);
        $havuzNema[$kod]     = floatval($h['nema'] ?? 0);
    }

    // 2. Aktif Dönem Sabitlerinin 'tutar - odenen_tutar' Farklarının Saf SQL ile Hesaplanması
    // Kural: name alanları üzerinden doğrudan bütçe yükleri (Kalan borçlar) hesaplanır
    $butceSorgu = $db->prepare("SELECT name, (tutar - odenen_tutar) AS kalan_yuk FROM butce_akis WHERE aktif_donem = ? AND turu = 'gider'");
    $butceSorgu->execute([$aktif_donem]);
    $butceListesi = $butceSorgu->fetchAll(PDO::FETCH_ASSOC);

    $butceKalanlar = [];
    foreach ($butceListesi as $b) {
        $bName = trim($b['name']);
        if (!isset($butceKalanlar[$bName])) {
            $butceKalanlar[$bName] = 0.00;
        }
        $butceKalanlar[$bName] += floatval($b['kalan_yuk']);
    }

    // Damacana Su satırı name='harcama_nakit' altında olduğu için onu ayrıca temiz yakalıyoruz
    $suSorgu = $db->prepare("SELECT (tutar - odenen_tutar) AS kalan_su FROM butce_akis WHERE aktif_donem = ? AND turu = 'gider' AND name = 'harcama_nakit' AND (kalem_adi LIKE '%Damacana%' OR kalem_adi LIKE '%damacana%') LIMIT 1");
    $suSorgu->execute([$aktif_donem]);
    $kalan_damacana_su_tutari = floatval($suSorgu->fetchColumn());

} catch (Exception $e) {
    // Çökme Emniyet Barajı
    $havuzAnapara = ['isbank_o' => 0, 'isbank_a' => 0, 'vakif_o' => 0, 'nakit' => 0];
    $havuzNema     = ['isbank_o' => 0, 'isbank_a' => 0];
    $butceKalanlar = ['elektrik' => 0, 'su' => 0, 'dogalgaz' => 0, 'telefon' => 0, 'internet' => 0, 'aidat' => 0, 'ev_kredisi' => 0];
    $kalan_damacana_su_tutari = 0.00;
}

    // Canlı Değişkenlerin Atanması (JS Hesaplayıcısına data-taban olarak enjekte edilecek)
    $ana_isbank_o_anapara = max(0, $havuzAnapara['isbank_o'] ?? 0);
    $ana_isbank_a_anapara = max(0, $havuzAnapara['isbank_a'] ?? 0);
    $ana_vakif_o_anapara  = max(0, $havuzAnapara['vakif_o'] ?? 0);
    $ana_nakit_anapara    = max(0, $havuzAnapara['nakit'] ?? 0);

    $canli_isbank_o_nema   = max(0, $havuzNema['isbank_o'] ?? 0);
    $canli_isbank_a_nema   = max(0, $havuzNema['isbank_a'] ?? 0);

    // Söylediğiniz kurallara göre kümülatif bütçe bloke tabanları hesaplanıyor (tutar - odenen)
    $bloke_faturalar_taban = floatval(($butceKalanlar['elektrik'] ?? 0) + ($butceKalanlar['su'] ?? 0) + ($butceKalanlar['dogalgaz'] ?? 0) + ($butceKalanlar['telefon'] ?? 0) + ($butceKalanlar['internet'] ?? 0));
    $bloke_su_aidat_taban  = floatval($kalan_damacana_su_tutari + ($butceKalanlar['aidat'] ?? 0));
    $bloke_kredi_taban     = floatval($ana_isbank_o_anapara); // Kredi bloke tabanı her zaman İşbank Ö anapara


    // DÖVİZ İŞLEMLERİ
    $kur_servis_management = ForexExchange::getLiveRates();
    $live_kurlar = $kur_servis_management['kurlar']; 
    $canli_ana_yatirim_bakiye_tl = $ana_isbank_a_anapara ?? 0.00;


require admin_view('management');
