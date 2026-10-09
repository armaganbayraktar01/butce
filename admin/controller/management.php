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


$telefonFaturasiBirimTutari = $_SESSION['sys_telefon_birim_fiyat'] ?? 469.00;//469,05
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


// DÖVİZ İŞLEMLERİ
$kur_servis_management = ForexExchange::getLiveRates();
$live_kurlar = $kur_servis_management['kurlar']; 
$usd_kur = floatval($live_kurlar['USD_SATIS'] ?? 49.20);
$eur_kur = floatval($live_kurlar['EUR_SATIS'] ?? 56.20);


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
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');

        // 1. Canlı döviz kurlarını sabitle
        $usd_kur = floatval($live_kurlar['USD_SATIS'] ?? 34.50);
        $eur_kur = floatval($live_kurlar['EUR_SATIS'] ?? 37.80);

        // 2. Ön yüzden post edilen parametreleri yakala
        $kart_turu  = trim($_POST['kart_turu'] ?? ''); 
        $alan_turu  = trim($_POST['alan_turu'] ?? ''); 
        $deger_ham  = trim($_POST['deger'] ?? '0');
        $val        = floatval($deger_ham);

        // Whitelist Güvenlik Filtresi
        $izin_verilen_alanlar = ['donem_ici', 'provizyon', 'doviz_borcu', 'eu_borcu', 'usd_borcu', 'harcanabilir_limit', 'kesinlesen_ekstre'];
        $izin_verilen_kartlar = ['isbank', 'vakifbank'];

        if (!in_array($alan_turu, $izin_verilen_alanlar) || !in_array($kart_turu, $izin_verilen_kartlar)) {
            echo json_encode(['status' => 'error', 'message' => 'Geçersiz parametre girdisi ulaştı!']);
            exit;
        }

        try {
            $db->beginTransaction();

            // 3. Hedeflenen tekil kart hücresini doğrudan veritabanında UPDATE et (Diğer alanları asla ezmez)
            $stmt_update_cell = $db->prepare("UPDATE kredi_kartlari SET {$alan_turu} = :val WHERE kart_adi = :kart");
            $stmt_update_cell->execute(['val' => $val, 'kart' => ($kart_turu === 'vakifbank' ? 'vakifbank' : 'isbank')]);

            // 4. Eğer girilen veri Euro veya Dolar ise döviz_borcu alanını arka planda otomatik hesapla ve mühürle
            if ($alan_turu === 'eu_borcu' || $alan_turu === 'usd_borcu') {
                $stmt_read = $db->prepare("SELECT eu_borcu, usd_borcu FROM kredi_kartlari WHERE kart_adi = :kart");
                $stmt_read->execute(['kart' => ($kart_turu === 'vakifbank' ? 'vakifbank' : 'isbank')]);
                $k_data = $stmt_read->fetch(PDO::FETCH_ASSOC);

                $yeni_doviz_borcu_tl = (floatval($k_data['usd_borcu']) * $usd_kur) + (floatval($k_data['eu_borcu']) * $eur_kur);

                $stmt_calc = $db->prepare("UPDATE kredi_kartlari SET doviz_borcu = :yeni_tl WHERE kart_adi = :kart");
                $stmt_calc->execute(['yeni_tl' => $yeni_doviz_borcu_tl, 'kart' => ($kart_turu === 'vakifbank' ? 'vakifbank' : 'isbank')]);
            }

            $db->commit();
            echo json_encode(['status' => 'success']);
            exit;

        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    if ($islem === 'kart_hesap_kesimi') {
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        
        $kart_turu = trim($_POST['kart_turu'] ?? '');
        
        // Güvenlik Duvarı / Beyaz Liste kalkanı
        if (!in_array($kart_turu, ['isbank', 'vakifbank'])) {
            echo json_encode(['status' => 'error', 'message' => 'Emniyet Bariyeri: Geçersiz kart seçimi!']);
            exit;
        }
        
        $target_kart_name = ($kart_turu === 'vakifbank') ? 'vakifbank' : 'isbank';
        
        try {
            $db->beginTransaction();
            
            // 1. Kartın güncel dönem içi harcamasını tam isabet FOR UPDATE ile kilitleyip oku
            $stmt_read = $db->prepare("SELECT donem_ici FROM kredi_kartlari WHERE kart_adi = :kart LIMIT 1 FOR UPDATE");
            $stmt_read->execute(['kart' => $target_kart_name]);
            $kart_data = $stmt_read->fetch(PDO::FETCH_ASSOC);
            
            if (!$kart_data) {
                throw new Exception("Hedef kredi kartı kaydına veritabanında ulaşılamadı!");
            }
            
            $mevcut_donem_ici = floatval($kart_data['donem_ici']);
            
            // 2. Dönem içindeki bakiyeyi Kesinleşen Ekstreye aktar ve Dönem İçini sıfırla
            $stmt_update = $db->prepare("UPDATE kredi_kartlari SET kesinlesen_ekstre = :ekstre, donem_ici = 0.00 WHERE kart_adi = :kart");
            $stmt_update->execute([
                'ekstre' => $mevcut_donem_ici,
                'kart'  => $target_kart_name
            ]);
            
            $db->commit();
            echo json_encode(['status' => 'success']);
            exit;
            
        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            echo json_encode(['status' => 'error', 'message' => 'Veritabanı Motor Hatası: ' . $e->getMessage()]);
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

            // 🎯 6. ADIM: EV KREDİSİ İÇİN VADE TARİHİ BAZLI EV_ALIM_KREDI KİLİTLEME İŞLEMİ
            if ($sysName === 'ev_kredisi') {
                // Aktif dönemden '2026-08' -> Vade Tarihi olan '2026-08-20' oluşturuluyor
                $hedefVadeTarihi = $aktifDonem . '-20'; 

                // ev_alim_kredi tablosunda vade_tarihi = '2026-08-20' olan satırı bularak is_locked = 1 yapıyoruz
                $stmtEvKredi =$db->prepare("UPDATE ev_alim_kredi SET is_locked = 1 WHERE vade_tarihi = ?");
                $stmtEvKredi->execute([$hedefVadeTarihi]);
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

    // 1. ADIM: Faiz Hesaplama İstasyonu (Session ve anlık veritabanı nemalarını günceller)
    if ($islem === 'faiz_hesapla') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');

        $anlikHam  = isset($_POST['evKredisiHam']) ? floatval($_POST['evKredisiHam']) : $evKredisiHam;
        $anlikOran = isset($_POST['faiz_orani']) ? floatval($_POST['faiz_orani']) : $faiz_orani;
        $anlikGun  = isset($_POST['faiz_gun_sayisi']) ? intval($_POST['faiz_gun_sayisi']) : $faiz_gun_sayisi;

        try {
            $db->beginTransaction();

            // Veritabanından cüzdanların canlı anaparalarını çekiyoruz
            $havuzSorgu = $db->query("SELECT havuz_kodu, anapara FROM finansal_havuzlar WHERE havuz_kodu IN ('isbank_o', 'isbank_a')");
            $havuzlar = $havuzSorgu->fetchAll(PDO::FETCH_ASSOC);

            $isbank_o_anapara = 0.00;
            $isbank_a_anapara = 0.00;

            foreach ($havuzlar as $havuz) {
                if ($havuz['havuz_kodu'] === 'isbank_o') $isbank_o_anapara = floatval($havuz['anapara']);
                if ($havuz['havuz_kodu'] === 'isbank_a') $isbank_a_anapara = floatval($havuz['anapara']);
            }

            // Sunucu taraflı kesin net faiz hesabı ateşlenir
            $isbank_o_nema = netFaizHesapla($isbank_o_anapara, $anlikOran, $anlikGun, $faiz_stopaj);
            $isbank_a_nema = netFaizHesapla($isbank_a_anapara, $anlikOran, $anlikGun, $faiz_stopaj);
            $evKredisiFaizliNetTutar = 0;

            // Hesaplanan faizleri (nema) veritabanına geçici olarak mühürlüyoruz
            $nemaMühürÖ = $db->prepare("UPDATE finansal_havuzlar SET nema = ? WHERE havuz_kodu = 'isbank_o'");
            $nemaMühürÖ->execute([$isbank_o_nema]);

            $nemaMühürA = $db->prepare("UPDATE finansal_havuzlar SET nema = ? WHERE havuz_kodu = 'isbank_a'");
            $nemaMühürA->execute([$isbank_a_nema]);

            // Gelecek sayfa yüklemeleri için View oturum hafızasını güncelliyoruz
            $_SESSION['evKredisiHam']              = $anlikHam;
            $_SESSION['dinamik_faiz_orani']        = $anlikOran;
            $_SESSION['dinamik_faiz_gun_sayisi']   = $anlikGun;
            $_SESSION['isbank_o_nema']             = $isbank_o_nema;
            $_SESSION['isbank_a_nema']             = $isbank_a_nema;

            $db->commit();
            ob_clean(); 
            echo json_encode(['status' => 'success', 'ev_kredisi_net' => $evKredisiFaizliNetTutar]); 
            exit;

        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            ob_clean(); 
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); 
            exit;
        }
    }

    if ($islem === 'faiz_aktar') {
        while (ob_get_level()) { ob_end_clean(); } ob_start();
        header('Content-Type: application/json; charset=utf-8');

        // 1. ADIM: POST verilerini süzgeçten geçirip tiplerini kesinleştiriyoruz
        $secilenHavuz = isset($_POST['havuz_kodu']) ? trim($_POST['havuz_kodu']) : '';
        $talepTutar   = isset($_POST['aktarilacak_tutar']) ? floatval($_POST['aktarilacak_tutar']) : 0.00;

        // Girdi Emniyet Kontrolü
        if (!in_array($secilenHavuz, ['isbank_a', 'isbank_o', 'all']) || $talepTutar <= 0) {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => '⚠️ Geçersiz hesap seçimi veya aktarım tutarı girdiniz!']);
            exit;
        }

        try {
            $db->beginTransaction();

            // 2. ADIM: Veritabanından her iki havuzun güncel verilerini çekiyoruz
            $havuzSorgu = $db->query("SELECT havuz_kodu, anapara, nema FROM finansal_havuzlar WHERE havuz_kodu IN ('isbank_o', 'isbank_a')");
            $havuzlar = $havuzSorgu->fetchAll(PDO::FETCH_ASSOC);

            $db_nema_o = 0.00;
            $db_nema_a = 0.00;

            foreach ($havuzlar as $havuz) {
                if ($havuz['havuz_kodu'] === 'isbank_o') $db_nema_o = floatval($havuz['nema'] ?? 0);
                if ($havuz['havuz_kodu'] === 'isbank_a') $db_nema_a = floatval($havuz['nema'] ?? 0);
            }

            // 3. ADIM: Gelen veriye göre işlem listesini dinamik olarak haritalandırıyoruz
            $islemListesi = [];
            $toplamAktarilan = 0.00;

            if ($secilenHavuz === 'isbank_a') {
                if ($talepTutar > $db_nema_a + 0.01) {
                    throw new Exception("⚠️ Talep edilen tutar İşbank (A) hesabındaki mevcut faiz bakiyesinden büyüktür!");
                }
                $islemListesi[] = ['havuz_kodu' => 'isbank_a', 'tutar' => $talepTutar];
                $toplamAktarilan = $talepTutar;
            } 
            elseif ($secilenHavuz === 'isbank_o') {
                if ($talepTutar > $db_nema_o + 0.01) {
                    throw new Exception("⚠️ Talep edilen tutar İşbank (Ö) hesabındaki mevcut faiz bakiyesinden büyüktür!");
                }
                $islemListesi[] = ['havuz_kodu' => 'isbank_o', 'tutar' => $talepTutar];
                $toplamAktarilan = $talepTutar;
            } 
            elseif ($secilenHavuz === 'all') {
                $toplamMevcutNema = $db_nema_a + $db_nema_o;
                if ($talepTutar > $toplamMevcutNema + 0.01) {
                    throw new Exception("⚠️ Talep edilen tutar toplam birikmiş faiz gelirinizden büyüktür!");
                }

                // Orantılı dağıtım motoru (Kuruş kaybını önler)
                if (abs($talepTutar - $toplamMevcutNema) > 0.01) {
                    $oran = $talepTutar / $toplamMevcutNema;
                    $aktarilacak_a = round($db_nema_a * $oran, 2);
                    $aktarilacak_o = round($talepTutar - $aktarilacak_a, 2);
                } else {
                    $aktarilacak_a = $db_nema_a;
                    $aktarilacak_o = $db_nema_o;
                }

                if ($aktarilacak_a > 0) $islemListesi[] = ['havuz_kodu' => 'isbank_a', 'tutar' => $aktarilacak_a];
                if ($aktarilacak_o > 0) $islemListesi[] = ['havuz_kodu' => 'isbank_o', 'tutar' => $aktarilacak_o];
                $toplamAktarilan = $talepTutar;
            }

                    // 4. ADIM: 🎯 TAM EŞLEŞMELİ ANAPARA BÜYÜTME VE NEMA DÜŞÜM MOTORU
            // Parametre çakışmasını önlemek için isimlendirmeleri benzersiz yaptık (:eklenecek ve :dusulecek)
            $updateHavuz = $db->prepare("
                UPDATE finansal_havuzlar 
                SET anapara = anapara + :eklenecek_tutar, 
                    nema = nema - :dusulecek_tutar 
                WHERE havuz_kodu = :havuz_kodu
            ");
            
            $arşiv_a = 0.00;
            $arşiv_o = 0.00;

            foreach ($islemListesi as $islem) {
                // 🎯 DÜZELTİLDİ: Tüm benzersiz parametreler execute dizisine eksiksiz dağıtıldı
                $updateHavuz->execute([
                    'eklenecek_tutar' => $islem['tutar'], 
                    'dusulecek_tutar' => $islem['tutar'], 
                    'havuz_kodu'      => $islem['havuz_kodu']
                ]);

                if ($islem['havuz_kodu'] === 'isbank_a') $arşiv_a = $islem['tutar'];
                if ($islem['havuz_kodu'] === 'isbank_o') $arşiv_o = $islem['tutar'];

                // Ev kredisi session kontrolü
                if ($islem['havuz_kodu'] === 'isbank_o' && isset($_SESSION['evKredisiFaizliNetTutar'])) {
                    $_SESSION['evKredisiFaizliNetTutar'] += $islem['tutar'];
                }
            }




            // 5. ADIM: 🎯 aylik_harcama_raporu Tablosuna Kümülatif (+=) Olarak İşleme
            $raporKontrol = $db->prepare("SELECT COUNT(*) FROM aylik_harcama_raporu WHERE donem = :donem");
            $raporKontrol->execute(['donem' => $aktif_donem]);
            $raporVarMi = (intval($raporKontrol->fetchColumn()) > 0);

            if ($raporVarMi) {
                // Mevcut dönem kaydı varsa üzerine ekleme yapılır
                $raporUpdate = $db->prepare("UPDATE `aylik_harcama_raporu` SET `faiz_gelir` = `faiz_gelir` + :tutar WHERE `donem` = :donem");
                $raporUpdate->execute([
                    'tutar' => $toplamAktarilan, 
                    'donem' => $aktif_donem
                ]);
            } else {
                // Dönem kaydı yoksa yeni satır açılır
                $raporInsert = $db->prepare("INSERT INTO `aylik_harcama_raporu` (donem, `faiz_gelir`) VALUES (:donem, :tutar)");
                $raporInsert->execute([
                    'donem' => $aktif_donem, 
                    'tutar' => $toplamAktarilan
                ]);
            }

            // 6. ADIM: Session Güncellemeleri
            if (isset($_SESSION['isbank_a_nema'])) {
                $_SESSION['isbank_a_nema'] = max(0.00, floatval($_SESSION['isbank_a_nema']) - $arşiv_a);
            }
            if (isset($_SESSION['isbank_o_nema'])) {
                $_SESSION['isbank_o_nema'] = max(0.00, floatval($_SESSION['isbank_o_nema']) - $arşiv_o);
            }

            if ($secilenHavuz === 'all' || (floatval($_SESSION['isbank_a_nema'] ?? 0) <= 0 && floatval($_SESSION['isbank_o_nema'] ?? 0) <= 0)) {
                $_SESSION['dinamik_faiz_gun_sayisi'] = 0;
            }

            $db->commit();
            ob_clean(); 
            echo json_encode(['status' => 'success', 'message' => 'Faiz aktarımı kuruşu kuruşuna havuzunuza mühürlendi.']); 
            exit;

        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            ob_clean(); 
            echo json_encode([
                'status' => 'error', 
                'message' => $e->getMessage() . " | Satır: " . $e->getLine()
            ]); 
            exit;
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

    // ===============================================================================
    // 14. DÖNEM DEVRET MOTORU (MIGRATION & FULL CLEAN ARCHIVE LAYER)
    // ===============================================================================
    if ($islem === 'donem_devret') {
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');

        // 1. Dönem string hesaplamaları (Örn: 2026-08 -> 2026-09)
        list($current_y, $current_m) = explode('-', $aktif_donem);
        $next_m = intval($current_m) + 1; 
        $next_y = intval($current_y);
        if ($next_m > 12) { 
            $next_m = 1; 
            $next_y++; 
        }
        $yeni_aktif_donem = $next_y . '-' . sprintf("%02d", $next_m);

        // 2. Bir sonraki planlama dönemini hesapla (Örn: 2026-09 -> 2026-10)
        $last_m = $next_m + 1; 
        $last_y = $next_y;
        if ($last_m > 12) { 
            $last_m = 1; 
            $last_y++; 
        }
        $planlama_donemi_last = $last_y . '-' . sprintf("%02d", $last_m);

        try {
            // 🔒 VERİ GÜVENLİK ZIRHI
            $db->beginTransaction();

            // -----------------------------------------------------------------------
            // ADIM A: ARŞİV LOG PAKETLEME (CARI VE GELECEK DÖNEM DAHİL FULL JSON)
            // -----------------------------------------------------------------------
            
            // 1. Finansal havuzların tüm anapara, nema ve döviz/altın stok durumlarını mühürle
            $havuzlar_raw = $db->query("SELECT * FROM finansal_havuzlar")->fetchAll(PDO::FETCH_ASSOC);
            $havuz_log = [];
            foreach ($havuzlar_raw as $h) {
                $havuz_log[trim($h['havuz_kodu'])] = [
                    'anapara'      => floatval($h['anapara']),
                    'nema'         => floatval($h['nema'] ?? 0),
                    'bloke_tutar'  => floatval($h['bloke_tutar'] ?? 0),
                    'eu_bakiye'    => floatval($h['eu_bakiye'] ?? 0),
                    'usd_bakiye'   => floatval($h['usd_bakiye'] ?? 0),
                    'xau_bakiye'   => floatval($h['xau_bakiye'] ?? 0)
                ];
            }

            // 2. Kapatılan cari döneme ait tüm bütçe akış satırlarını tam liste olarak çek
            $kalemler_stmt = $db->prepare("SELECT * FROM butce_akis WHERE aktif_donem = :donem");
            $kalemler_stmt->execute(['donem' => $aktif_donem]);
            $kalemler_log = $kalemler_stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Devir öncesindeki butce_akis_last tablosunun durumunu tam liste yakala
            $last_stmt = $db->prepare("SELECT * FROM butce_akis_last WHERE aktif_donem = :y_donem");
            $last_stmt->execute(['y_donem' => $yeni_aktif_donem]);
            $kalemler_last_log = $last_stmt->fetchAll(PDO::FETCH_ASSOC);

            // 4. Canlı kredi kartları tablosundaki verileri çek
            $kartlar_raw = $db->query("SELECT * FROM kredi_kartlari")->fetchAll(PDO::FETCH_ASSOC);
            $kart_log = [];
            foreach ($kartlar_raw as $k) {
                $kart_log[($k['kart_adi'] === 'vakifbank' ? 'vakifbank' : 'isbank')] = [
                    'donem_ici'         => floatval($k['donem_ici']),
                    'provizyon'         => floatval($k['provizyon']),
                    'doviz_borcu'       => floatval($k['doviz_borcu']),
                    'eu_borcu'          => floatval($k['eu_borcu'] ?? 0),
                    'usd_borcu'         => floatval($k['usd_borcu'] ?? 0),
                    'kesinlesen_ekstre' => floatval($k['kesinlesen_ekstre']),
                    'kart_limiti'       => floatval($k['kart_limiti'])
                ];
            }

            // 5. Devir öncesindeki kredi_kartlari_last tablosunun durumunu yakala
            $kart_last_raw = $db->query("SELECT * FROM kredi_kartlari_last")->fetchAll(PDO::FETCH_ASSOC);
            $kart_last_log = [];
            foreach ($kart_last_raw as $kl) {
                $kart_last_log[($kl['kart_adi'] === 'vakifbank' ? 'vakifbank' : 'isbank')] = [
                    'donem_ici'         => floatval($kl['donem_ici']),
                    'provizyon'         => floatval($kl['provizyon']),
                    'doviz_borcu'       => floatval($kl['doviz_borcu']),
                    'eu_borcu'          => floatval($kl['eu_borcu'] ?? 0),
                    'usd_borcu'         => floatval($kl['usd_borcu'] ?? 0),
                    'kesinlesen_ekstre' => floatval($kl['kesinlesen_ekstre']),
                    'kart_limiti'       => floatval($kl['kart_limiti'])
                ];
            }

            // 6. Genişletilmiş bütce_donem_loglari tablosuna tek satır mühürleme
            $stmt_insert_log = $db->prepare("
                INSERT INTO bütce_donem_loglari (islem_donemi, islem_tipi, havuz_durumlari, butce_kalemleri, butce_kalemleri_last, kart_durumlari, kart_durumlari_last, tarih)
                VALUES (:donem, 'Aylık Bütçe Kapatma', :havuz, :butce, :butce_last, :kart, :kart_last, NOW())
            ");
            $stmt_insert_log->execute([
                'donem'      => $aktif_donem,
                'havuz'      => json_encode($havuz_log, JSON_UNESCAPED_UNICODE),
                'butce'      => json_encode($kalemler_log, JSON_UNESCAPED_UNICODE),
                'butce_last' => json_encode($kalemler_last_log, JSON_UNESCAPED_UNICODE),
                'kart'       => json_encode($kart_log, JSON_UNESCAPED_UNICODE),
                'kart_last'  => json_encode($kart_last_log, JSON_UNESCAPED_UNICODE)
            ]);

            // -----------------------------------------------------------------------
            // ADIM B: TEMİZLİK VE TAŞIMA OPERASYONU (MÜKERRER ŞİŞME VE PARALEL KORUMA)
            // -----------------------------------------------------------------------
            // 1. Canlı bütçe tablosunu tamamen boşaltıyoruz (Eski dönem kalıntı bariyeri)
            $db->query("DELETE FROM butce_akis");

            // 2. Hazırladığınız yeni ay planını asıl bütçe tablosuna jilet nizam aktar
            if (count($kalemler_last_log) > 0) {
                $stmt_migrate = $db->prepare("
                    INSERT INTO butce_akis (aktif_donem, kalem_adi, tutar, odenen_tutar, turu, is_locked, name, havuz_kodu)
                    SELECT :yeni_donem_insert, kalem_adi, tutar, 0.00, turu, 0, name, havuz_kodu FROM butce_akis_last WHERE aktif_donem = :yeni_donem_search
                ");
                $stmt_migrate->execute([
                    'yeni_donem_insert' => $yeni_aktif_donem,
                    'yeni_donem_search' => $yeni_aktif_donem
                ]);
            }

            // 3. butce_akis_last tablosunu boşaltıp bir sonraki geleceğe (2026-10) orijinal nizamla klonla
            $db->query("DELETE FROM butce_akis_last");
            $stmt_next_prep = $db->prepare("
                INSERT INTO butce_akis_last (aktif_donem, kalem_adi, tutar, odenen_tutar, turu, is_locked, name, havuz_kodu)
                SELECT :p_donem, kalem_adi, tutar, 0.00, turu, 0, name, havuz_kodu FROM butce_akis WHERE aktif_donem = :y_donem
            ");
            $stmt_next_prep->execute([
                'p_donem' => $planlama_donemi_last,
                'y_donem' => $yeni_aktif_donem
            ]);

            // -----------------------------------------------------------------------
            // ADIM C: KREDI KARTLARI AYNALAMA VE SIFIRLAMA MOTORU
            // -----------------------------------------------------------------------
            foreach ($kart_last_raw as $l_kart) {
                $db->prepare("
                    UPDATE kredi_kartlari 
                    SET donem_ici = :dnm, provizyon = :prv, doviz_borcu = :dvz, eu_borcu = :eu, usd_borcu = :usd, kesinlesen_ekstre = :ekstre 
                    WHERE kart_adi = :k_adi
                ")
                ->execute([
                    'dnm'    => $l_kart['donem_ici'], 
                    'prv'    => $l_kart['provizyon'], 
                    'dvz'    => $l_kart['doviz_borcu'], 
                    'eu'     => $l_kart['eu_borcu'] ?? 0.00, 
                    'usd'    => $l_kart['usd_borcu'] ?? 0.00, 
                    'ekstre' => $l_kart['kesinlesen_ekstre'] ?? 0.00, 
                    'k_adi'  => $l_kart['kart_adi']
                ]);
            }
            $db->query("UPDATE kredi_kartlari_last SET donem_ici = 0.00, provizyon = 0.00, doviz_borcu = 0.00, eu_borcu = 0.00, usd_borcu = 0.00, kesinlesen_ekstre = 0.00");

            // -----------------------------------------------------------------------
            // ADIM D: GELECEK TAKSİT ENJEKSİYON MOTORU
            // -----------------------------------------------------------------------
            list($y_yil, $y_ay_str) = explode('-', $yeni_aktif_donem);
            $yeni_taksitler = $db->prepare("SELECT isbank_tutar, vakif_tutar FROM cc_taksitler WHERE ay_index = ? LIMIT 1");
            $yeni_taksitler->execute([intval($y_ay_str)]);
            $yt = $yeni_taksitler->fetch(PDO::FETCH_ASSOC);
            if ($yt) {
                $db->prepare("UPDATE butce_akis SET tutar = ? WHERE aktif_donem = ? AND name = 'kredi_karti_a'")->execute([floatval($yt['isbank_tutar']), $yeni_aktif_donem]);
                $db->prepare("UPDATE butce_akis SET tutar = ? WHERE aktif_donem = ? AND name = 'kredi_karti_o'")->execute([floatval($yt['vakif_tutar']), $yeni_aktif_donem]);
            }

            $db->commit();
            $_SESSION['aktif_donem'] = $yeni_aktif_donem;
            
            echo json_encode(['status' => 'success']);
            exit;
        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            echo json_encode(['status' => 'error', 'message' => 'Dönem Devir Hatası: ' . $e->getMessage()]);
            exit;
        }
    }

    // ===============================================================================
    // 15. DÖNEM GERİ AL / ROLLBACK MOTORU (FULL RESTORE REFORM - FINAL)
    // ===============================================================================
    if ($islem === 'donem_geri_al') {
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');

        try {
            // 1. Veritabanındaki en son bütçe kapatma logunu bul
            $en_son_log = $db->query("SELECT * FROM bütce_donem_loglari ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if (!$en_son_log) {
                echo json_encode(['status' => 'error', 'message' => 'Hata: Geri yüklenecek dönem arşiv log kaydı bulunamadı!']);
                exit;
            }

            $log_donemi = $en_son_log['islem_donemi']; // Geri dönülecek asıl ay (Örn: 2026-08)
            list($c_y, $c_m) = explode('-', $log_donemi);
            
            // Log donemine göre devir esnasında açılmış olan o hatalı gelecek ayı hesapla (Örn: 2026-09)
            $n_m = intval($c_m) + 1; 
            $n_y = intval($c_y);
            if ($n_m > 12) { 
                $n_m = 1; 
                $n_y++; 
            }
            $log_gelecek_donem = $n_y . '-' . sprintf("%02d", $n_m); 

            $havuz_data      = json_decode($en_son_log['havuz_durumlari'], true);
            $butce_data      = json_decode($en_son_log['butce_kalemleri'], true);
            $butce_last_data = json_decode($en_son_log['butce_kalemleri_last'], true); 
            $kart_data       = json_decode($en_son_log['kart_durumlari'], true);
            $kart_last_data  = json_decode($en_son_log['kart_durumlari_last'], true); 

            // 🔒 VERİ GÜVENLİK ZIRHI: Geri yükleme operasyonunu tek bir transaction ile kilitliyoruz
            $db->beginTransaction();

            // -----------------------------------------------------------------------
            // ADIM 1: YANLIŞLIKLA AÇILAN YENİ DÖNEMİN VE LAST KALINTILARININ KAZINMASI
            // -----------------------------------------------------------------------
            $db->query("DELETE FROM butce_akis");
            $db->query("DELETE FROM butce_akis_last");

            // -----------------------------------------------------------------------
            // ADIM 2: CANLI BÜTÇE SATIRLARININ ORİJİNAL YAPISIYLA DİRİLTİLMESİ
            // -----------------------------------------------------------------------
            $stmt_r_b = $db->prepare("
                INSERT INTO butce_akis (id, aktif_donem, kalem_adi, tutar, odenen_tutar, turu, is_locked, name, havuz_kodu, islem_gunu, odenme_tarihi) 
                VALUES (:id, :donem, :adi, :tutar, :odenen, :turu, :locked, :name, :havuz, :gun, :tarih)
            ");
            foreach ($butce_data as $b) {
                $stmt_r_b->execute([
                    'id' => $b['id'], 'donem' => $b['aktif_donem'], 'adi' => $b['kalem_adi'], 'tutar' => $b['tutar'], 
                    'odenen' => $b['odenen_tutar'], 'turu' => $b['turu'], 'locked' => $b['is_locked'], 
                    'name' => $b['name'] ?? null, 'havuz' => $b['havuz_kodu'] ?? null, 'gun' => $b['islem_gunu'] ?? 1, 
                    'tarih' => $b['odenme_tarihi'] ?? null
                ]);
            }

            // -----------------------------------------------------------------------
            // ADIM 3: PLANLAMA (LAST) SATIRLARININ JİLET GİBİ ESKİ AYINA DÖNDÜRÜLMESİ
            // -----------------------------------------------------------------------
            $stmt_r_bl = $db->prepare("
                INSERT INTO butce_akis_last (id, aktif_donem, kalem_adi, tutar, odenen_tutar, turu, is_locked, name, havuz_kodu) 
                VALUES (:id, :donem, :adi, :tutar, :odenen, :turu, :locked, :name, :havuz)
            ");
            foreach ($butce_last_data as $bl) {
                $stmt_r_bl->execute([
                    'id' => $bl['id'], 'donem' => $bl['aktif_donem'], 'adi' => $bl['kalem_adi'], 'tutar' => $bl['tutar'], 
                    'odenen' => $bl['odenen_tutar'] ?? 0.00, 'turu' => $bl['turu'], 'locked' => $bl['is_locked'] ?? 0, 
                    'name' => $bl['name'] ?? null, 'havuz' => $bl['havuz_kodu'] ?? null
                ]);
            }

            // -----------------------------------------------------------------------
            // ADIM 4: LİKİDİTE HAVUZLARI VE DÖVİZ STOKLARININ KURTARILMASI
            // -----------------------------------------------------------------------
            $stmt_r_h = $db->prepare("
                UPDATE finansal_havuzlar 
                SET anapara = :ana, nema = :nem, bloke_tutar = :blk, eu_bakiye = :eu, usd_bakiye = :usd, xau_bakiye = :xau 
                WHERE havuz_kodu = :kod
            ");
            foreach ($havuz_data as $kod => $h) {
                $stmt_r_h->execute([
                    'ana' => $h['anapara'], 'nem' => $h['nema'], 'blk' => $h['bloke_tutar'] ?? 0.00, 
                    'eu' => $h['eu_bakiye'] ?? 0.00, 'usd' => $h['usd_bakiye'] ?? 0.00, 'xau' => $h['xau_bakiye'] ?? 0.00, 
                    'kod' => $kod
                ]);
            }

            // -----------------------------------------------------------------------
            // ADIM 5: CANLI KREDİ KARTLARI DURUMUNUN RESTORE EDİLMESİ
            // -----------------------------------------------------------------------
            $stmt_r_k = $db->prepare("
                UPDATE kredi_kartlari 
                SET donem_ici = :dnm, provizyon = :prv, doviz_borcu = :dvz, eu_borcu = :eu, usd_borcu = :usd, kesinlesen_ekstre = :ekstre 
                WHERE kart_adi = :k_adi
            ");
            foreach ($kart_data as $k_adi => $k) {
                $stmt_r_k->execute([
                    'dnm' => $k['donem_ici'], 'prv' => $k['provizyon'], 'dvz' => $k['doviz_borcu'], 
                    'eu' => $k['eu_borcu'] ?? 0.00, 'usd' => $k['usd_borcu'] ?? 0.00, 'ekstre' => $k['kesinlesen_ekstre'], 
                    'k_adi' => $k_adi
                ]);
            }

            // -----------------------------------------------------------------------
            // ADIM 6: GELECEK DÖNEM (LAST) KREDİ KARTLARININ RESTORE EDİLMESİ
            // -----------------------------------------------------------------------
            $stmt_r_kl = $db->prepare("
                UPDATE kredi_kartlari_last 
                SET donem_ici = :dnm, provizyon = :prv, doviz_borcu = :dvz, eu_borcu = :eu, usd_borcu = :usd, kesinlesen_ekstre = :ekstre 
                WHERE kart_adi = :k_adi
            ");
            foreach ($kart_last_data as $kl_adi => $kl) {
                $stmt_r_kl->execute([
                    'dnm' => $kl['donem_ici'], 'prv' => $kl['provizyon'], 'dvz' => $kl['doviz_borcu'], 
                    'eu' => $kl['eu_borcu'] ?? 0.00, 'usd' => $kl['usd_borcu'] ?? 0.00, 'ekstre' => $kl['kesinlesen_ekstre'], 
                    'k_adi' => $kl_adi
                ]);
            }

            // -----------------------------------------------------------------------
            // ADIM 7: KULLANILAN LOG KAYDININ SİLİNMESİ VE SEANS MÜHÜRLENMESİ
            // -----------------------------------------------------------------------
            $db->prepare("DELETE FROM bütce_donem_loglari WHERE id = ?")->execute([$en_son_log['id']]);
            
            $db->commit();
            $_SESSION['aktif_donem'] = $log_donemi;
            
            echo json_encode(['status' => 'success', 'message' => "Mükemmel Geri Yükleme! Tüm canlı ve planlama (last) tabloları geçmişe mühürlendi."]);
            exit;
        } catch (Exception $e) {
            if ($db->inTransaction()) { 
                $db->rollBack(); 
            }
            echo json_encode(['status' => 'error', 'message' => 'Dönem Geri Yükleme Hatası: ' . $e->getMessage()]);
            exit;
        }
    }
       
}

// =========================================================================
// B. POST DURUMU: MANAGEMENT MODAL YATIRIM İŞLEMİ (SADECE AJAX KARŞILAYICI)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'interaktif_forex_panel_islem') {
    
    // 🔒 ÇIKTI TEMİZLİK KALKANI: Olası PHP hata/uyarı sızıntılarının JSON yapısını bozmasını engeller
    if (ob_get_length()) { ob_clean(); }
    error_reporting(0);
    ini_set('display_errors', 0);

    // Standart AJAX Yanıt Şablonu
    $response = [
        'success' => false,
        'message' => 'Bilinmeyen bir iç hata oluştu.',
        'data'    => []
    ];

    // --- 1. GİRDİLERİN ARITILMASI VE GÜVENLİK FİLTRELERİ ---
    $alinacak_doviz_cinsi  = filter_input(INPUT_POST, 'panel_aktarilacak_hesap', FILTER_SANITIZE_SPECIAL_CHARS); // USD, EUR, XAU
    $cekilecek_tutar       = filter_input(INPUT_POST, 'panel_tutar_tl', FILTER_VALIDATE_FLOAT); // Vergi dahil toplam maliyet
    $alinacak_miktar       = filter_input(INPUT_POST, 'panel_miktar', FILTER_VALIDATE_FLOAT); // Alınan döviz/varlık adedi
    $manuel_kur            = filter_input(INPUT_POST, 'panel_manuel_kur', FILTER_VALIDATE_FLOAT);
    
    $islem_tarihi          = date('Y-m-d');
    $aktif_donem           = (isset($aktif_donem) && !empty($aktif_donem)) ? $aktif_donem : date('Y-m'); 
    
    $cekilecek_hesap_id    = 2; // İşbankası (A) Havuz ID (Sabit)
    $islem_turu            = 2; // Alış kodu = 2 (Sabit)

    // Gizli input alanından gelen metinsel net tutarı (Örn: "10.904,86 ₺") arıtıp float sayıya çevirme
    $ham_doviz_alis_tutar  = $_POST['panel_doviz_alış_tutari'] ?? ($_POST['panel_doviz_alis_tutari'] ?? '0');
    $temiz_doviz_alis_tutar = preg_replace('/[^0-9,.-]/', '', $ham_doviz_alis_tutar);
    
    if (strpos($temiz_doviz_alis_tutar, '.') !== false && strpos($temiz_doviz_alis_tutar, ',') !== false) {
        $temiz_doviz_alis_tutar = str_replace('.', '', $temiz_doviz_alis_tutar);
        $temiz_doviz_alis_tutar = str_replace(',', '.', $temiz_doviz_alis_tutar);
    } elseif (strpos($temiz_doviz_alis_tutar, ',') !== false) {
        $temiz_doviz_alis_tutar = str_replace(',', '.', $temiz_doviz_alis_tutar);
    }
    $doviz_alis_tutar = (float)$temiz_doviz_alis_tutar;

    // Sütun manipülasyonunu engelleyen whitelist (Beyaz Liste) kalkanı
    $doviz_sutun_haritasi = [
        'USD' => 'usd_bakiye',
        'EUR' => 'eu_bakiye',
        'XAU' => 'xau_bakiye'
    ];

    if (!array_key_exists($alinacak_doviz_cinsi, $doviz_sutun_haritasi)) {
        $response['message'] = "Emniyet Bariyeri: Geçersiz döviz/varlık seçimi ulaştı!";
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        exit;
    }

    $hedef_stok_sutunu = $doviz_sutun_haritasi[$alinacak_doviz_cinsi];

    // Canlı kurların varlık kontrolü ve emniyet şeridi
    if (!isset($live_kurlar) || !is_array($live_kurlar)) {
        $live_kurlar = ['USD' => 46.8215, 'EUR' => 53.8685, 'XAU' => 6086.4200];
    }
    $saf_canli_kur = (float)($live_kurlar[$alinacak_doviz_cinsi] ?? 0);
    $doviz_kuru    = ($manuel_kur > 0) ? $manuel_kur : $saf_canli_kur;
    
    // Matematiksel fark üzerinden vergi kırılım hesaplaması
    $hesaplanan_vergi = $cekilecek_tutar - $doviz_alis_tutar;

    // --- 2. VERİTABANI İŞLEMİ (TRANSACTION) VE DOĞRULAMA MOTORU ---
    if ($cekilecek_tutar > 0 && $alinacak_miktar > 0 && $doviz_kuru > 0) {
        try {
            $db->beginTransaction();

            // Sızdırmazlık kalkanı: İşbank (A) (id=2) güncel anapara bakiyesini satır kilitleme (FOR UPDATE) ile oku
            $havuz_sorgu = $db->prepare("SELECT `anapara` FROM `finansal_havuzlar` WHERE `id` = ? LIMIT 1 FOR UPDATE");
            $havuz_sorgu->execute([$cekilecek_hesap_id]);
            $aktif_havuz = $havuz_sorgu->fetch(PDO::FETCH_ASSOC);

            if (!$aktif_havuz) {
                throw new Exception("İşbank (A) finansal havuz hesabı sistemde bulunamadı!");
            }

            if ($cekilecek_tutar > (float)$aktif_havuz['anapara']) {
                throw new Exception("İşbank (A) havuzunda yeterli TL anapara bakiyesi mevcut değil!");
            }

            // 1️⃣ ADIM: Finansal Havuz Güncellemesi (TL Azalt, İlgili Döviz Stokunu Artır)
            $havuz_guncelle = $db->prepare("
                UPDATE `finansal_havuzlar` 
                SET `anapara` = `anapara` - ?, 
                    `{$hedef_stok_sutunu}` = `{$hedef_stok_sutunu}` + ? 
                WHERE `id` = ?
            ");
            $havuz_guncelle->execute([$cekilecek_tutar, $alinacak_miktar, $cekilecek_hesap_id]);

            // 2️⃣ ADIM: Forex İşlemler Tablosuna Alım Emir Kaydı
            $forex_kayit = $db->prepare("
                INSERT INTO `forex_islemler` 
                (`islem_tarihi`, `islem_turu`, `doviz_cinsi`, `miktar`, `doviz_kuru`, `tutar`, `vergi`, `maliyet`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $forex_kayit->execute([
                $islem_tarihi, 
                $islem_turu, 
                $alinacak_doviz_cinsi, 
                $alinacak_miktar, 
                $doviz_kuru, 
                $doviz_alis_tutar, 
                $hesaplanan_vergi, 
                $cekilecek_tutar
            ]);

            // 3️⃣ ADIM: Aylık Harcama Raporu Kümülans Güncellemesi (Yatırım sütununa kümülatif ekleme)
            $rapor_guncelle = $db->prepare("
                INSERT INTO `aylik_harcama_raporu` (`donem`, `yatirim_tutari`) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE `yatirim_tutari` = `yatirim_tutari` + VALUES(`yatirim_tutari`)
            ");
            $rapor_guncelle->execute([$aktif_donem, $cekilecek_tutar]);
            $db->commit();

            // En taze bakiyeyi çekme alanı
            $bakiye_sorgu = $db->prepare("SELECT `anapara` FROM `finansal_havuzlar` WHERE `id` = ?");
            $bakiye_sorgu->execute([$cekilecek_hesap_id]);
            $yeni_bakiye = $bakiye_sorgu->fetch(PDO::FETCH_ASSOC);

            $response['success'] = true;
            $response['message'] = "Döviz satın alma işlemi başarıyla veritabanına işlendi.";
            $response['data'] = [
                'yeni_anapara' => (float)$yeni_bakiye['anapara']
            ];

        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $response['message'] = "Emniyet Bariyeri (Modal): " . $e->getMessage();
        }
    } else {
        $response['message'] = "Emniyet Bariyeri: Girdi değerleri sıfırdan büyük olmalıdır!";
    }

    // 🔒 SAF PHP ÇIKTI TEMİZLİK KİLİDİ: Hayalet boşlukları tamamen kazır
    if (ob_get_length()) {
        ob_end_clean();
    }
    
    // 🔔 500 HATASINI ÇÖZEN ALAN: Tanımsız fonksiyonlar söküldü, düz PHP standardı getirildi
    if (!headers_sent()) {
        header_remove();
        http_response_code(200); // Durumu temiz "OK" moduna çeker
        header('Content-Type: application/json; charset=utf-8');
    }
    
    // Unicode karakter kaybını önleyerek saf JSON çıktısını bas
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}


$stmt_kalemler = $db->prepare("SELECT * FROM butce_akis WHERE aktif_donem = :donem ORDER BY id ASC");
$stmt_kalemler->execute(['donem' => $aktif_donem]); 
$kalemler = $stmt_kalemler->fetchAll(PDO::FETCH_ASSOC);


$db_taksitler = $db->query("SELECT * FROM cc_taksitler ORDER BY ay_index ASC")->fetchAll(PDO::FETCH_ASSOC);

$kredikarti = $db->query("SELECT * FROM kredi_kartlari")->fetchAll(PDO::FETCH_ASSOC);
$cc_meta = [];

foreach ($kredikarti as $kart) {
    $k_anahtar = ($kart['kart_adi'] === 'vakifbank') ? 'vakifbank' : 'isbank';
    
    $cc_meta[$k_anahtar] = [
        'id'                   => (int)$kart['id'],
        'donem_ici'            => floatval($kart['donem_ici']),
        'provizyon'            => floatval($kart['provizyon']),
        'usd_borcu'            => floatval($kart['usd_borcu']),
        'eu_borcu'             => floatval($kart['eu_borcu']),
        'doviz_borcu'          => floatval($kart['doviz_borcu']), 
        'kesinlesen_ekstre'    => floatval($kart['kesinlesen_ekstre']),
        'kart_limiti'          => floatval($kart['kart_limiti']),
        'kullanilabilir_limit' => floatval($kart['kullanilabilir_limit']),
        'harcanabilir_limit'   => floatval($kart['harcanabilir_limit']),
        'usd_borc_tl'          => floatval($kart['usd_borcu']) * $usd_kur,
        'eu_borc_tl'           => floatval($kart['eu_borcu']) * $eur_kur
    ];
}

// ===============================================================================
// KREDİ KART LAST MATRİS OKUMASI VE FİNANSAL ANAYASA KURAL MOTORU
// ===============================================================================
$kredi_kartlari_last = $db->query("SELECT * FROM kredi_kartlari_last")->fetchAll(PDO::FETCH_ASSOC);

$kkart = [];
foreach ($kredi_kartlari_last as $kart) {
    $k_anahtar = ($kart['kart_adi'] === 'vakifbank') ? 'vakifbank' : 'isbank';
    
    $kkart[$k_anahtar] = [
        'id'                   => (int)$kart['id'],
        'donem_ici'            => floatval($kart['donem_ici']),
        'provizyon'            => floatval($kart['provizyon']),
        'usd_borcu'            => floatval($kart['usd_borcu']),
        'eu_borcu'             => floatval($kart['eu_borcu']),
        'doviz_borcu'          => floatval($kart['doviz_borcu']), 
        'kesinlesen_ekstre'    => floatval($kart['kesinlesen_ekstre']),
        'kart_limiti'          => floatval($kart['kart_limiti']),
        'kullanilabilir_limit' => floatval($kart['kullanilabilir_limit']),
        'harcanabilir_limit'   => floatval($kart['harcanabilir_limit']),
        'usd_borc_tl'          => floatval($kart['usd_borcu']) * $usd_kur,
        'eu_borc_tl'           => floatval($kart['eu_borcu']) * $eur_kur
    ];
}

//REFORM: Sayfa açılışında döviz borçlarını canlı kurlarla kontrol et ve veritabanını güncelle
foreach ($kredi_kartlari_last as $index => $kart) {
    $k_usd = floatval($kart['usd_borcu']);
    $k_eur = floatval($kart['eu_borcu']);
    
    // Otomatik hesaplanan güncel kur karşılığı
    $otomatik_tl_karsiligi = ($k_usd * $usd_kur) + ($k_eur * $eur_kur);
    
    // HATA ÖNLEME BARİYERİ: Sayfa açılışında (GET sürecinde) döviz borcu eşitsizliğini 
    // sadece mevcut istek bir POST güncellemesi DEĞİLSE denetle.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        if (floatval($kart['doviz_borcu']) != $otomatik_tl_karsiligi) {
            $stmt_sync = $db->prepare("UPDATE kredi_kartlari_last SET doviz_borcu = :otomatik_tl WHERE id = :id");
            $stmt_sync->execute(['otomatik_tl' => $otomatik_tl_karsiligi, 'id' => $kart['id']]);
            
            // Çalışma zamanındaki mevcut dizi hücresini de senkronize et
            $kredi_kartlari_last[$index]['doviz_borcu'] = $otomatik_tl_karsiligi;
        }
    }
}
//////////////////////////////////////////////////////

$isbank_aktif_taksit = 0; $vakif_aktif_taksit = 0; 
foreach ($db_taksitler as $t) { 
    if (intval($t['ay_index']) === $aktif_ay_index) { 
        $isbank_aktif_taksit = floatval($t['isbank_tutar']); 
        $vakif_aktif_taksit = floatval($t['vakif_tutar']); 
    } 
}
// $isbank_card_borcu = $cc_meta['isbank_donem_ici'] + $cc_meta['isbank_provizyon'] + $cc_meta['isbank_doviz'] + $cc_meta['isbank_ekstre'] + $isbank_aktif_taksit;
// $vakif_card_borcu = $cc_meta['vakif_donem_ici'] + $cc_meta['vakif_provizyon'] + $cc_meta['vakif_doviz'] + $cc_meta['vakif_ekstre'] + $vakif_aktif_taksit;
$isbank_card_borcu = $cc_meta['isbank']['donem_ici'] + $cc_meta['isbank']['provizyon'] + $cc_meta['isbank']['doviz_borcu'] + $cc_meta['isbank']['kesinlesen_ekstre'] + $isbank_aktif_taksit;
$vakif_card_borcu = $cc_meta['vakifbank']['donem_ici'] + $cc_meta['vakifbank']['provizyon'] + $cc_meta['vakifbank']['doviz_borcu'] + $cc_meta['vakifbank']['kesinlesen_ekstre'] + $vakif_aktif_taksit;


$gelirler = []; $giderler = []; 
foreach ($kalemler as $k) { if ($k['turu'] === 'gelir') $gelirler[] = $k; else $giderler[] = $k; }
// $live_kurlar = ForexExchange::getLiveRates()['kurlar']; 
// $canli_ana_yatirim_bakiye_tl = max(0, $isbank_a_bakiye - $net_ana_havuz_faiz_php);   


// AYLIK HARCAMA RAPORU TABLOSU CONTROLLER


// 🔄 AKTİF YILDAN ÖNCEKİ TÜM YILLARIN DEVİR HESABI
$secilen_yil = $aktif_yil;

$onceki_yil = $aktif_yil - 1;
$db_onceki_raporlar = $db->query("SELECT * FROM `aylik_harcama_raporu_arsiv` WHERE `donem` LIKE '{$onceki_yil}-%'")->fetchAll(PDO::FETCH_ASSOC);

$last_net_tasarruf = 0;
$last_net_yatirim = 0;

foreach ($db_onceki_raporlar as $o_satir) {
    $o_fatura = floatval($o_satir['elektrik'] ?? 0) + floatval($o_satir['su'] ?? 0) + floatval($o_satir['dogalgaz'] ?? 0) + floatval($o_satir['telefon'] ?? 0) + floatval($o_satir['internet'] ?? 0);
    $o_gelir = floatval($o_satir['maas_o'] ?? 0) + floatval($o_satir['maas_a'] ?? 0) + floatval($o_satir['maktu_mesai'] ?? 0) + floatval($o_satir['ek_gelir'] ?? 0) + floatval($o_satir['faiz_gelir'] ?? 0);
    $o_gider = floatval($o_satir['kredi_karti_o'] ?? 0) + floatval($o_satir['kredi_karti_a'] ?? 0) + floatval($o_satir['ev_kredisi'] ?? 0) + floatval($o_satir['aidat'] ?? 0) + floatval($o_satir['harcama_nakit'] ?? 0) + $o_fatura;
    $last_net_tasarruf += $o_gelir - $o_gider;
    $last_net_yatirim += floatval($o_satir['yatirim_tutari'] ?? 0);
}

$last_year_aktarim = $last_net_tasarruf - $last_net_yatirim;


// 🔄 AKTİF YILDAN ÖNCEKİ TÜM YILLARIN DEVİR HESABI kümülatif
$secilen_yil = $aktif_yil;

$devir_sorgu = $db->prepare("
    SELECT
        CAST(SUBSTRING(`donem`,1,4) AS UNSIGNED) AS yil,
        SUM(
            IFNULL(maas_o,0) +
            IFNULL(maas_a,0) +
            IFNULL(maktu_mesai,0) +
            IFNULL(ek_gelir,0) +
            IFNULL(faiz_gelir,0) -
            IFNULL(kredi_karti_o,0) -
            IFNULL(kredi_karti_a,0) -
            IFNULL(ev_kredisi,0) -
            IFNULL(aidat,0) -
            IFNULL(harcama_nakit,0) -
            IFNULL(elektrik,0) -
            IFNULL(su,0) -
            IFNULL(dogalgaz,0) -
            IFNULL(telefon,0) -
            IFNULL(internet,0)
        ) AS net_tasarruf,
        SUM(IFNULL(yatirim_tutari,0)) AS net_yatirim
    FROM `aylik_harcama_raporu_arsiv`
    WHERE CAST(SUBSTRING(`donem`,1,4) AS UNSIGNED) < :secilen_yil
    GROUP BY CAST(SUBSTRING(`donem`,1,4) AS UNSIGNED)
    ORDER BY yil ASC
");

$devir_sorgu->execute(['secilen_yil' => $secilen_yil]);
$devir_yillari = $devir_sorgu->fetchAll(PDO::FETCH_ASSOC);

$kumulatif_net_tasarruf = 0;
$kumulatif_net_yatirim = 0;

foreach ($devir_yillari as $yil) {
    $kumulatif_net_tasarruf += (float)$yil['net_tasarruf'];
    $kumulatif_net_yatirim += (float)$yil['net_yatirim'];
}


// 📊 TABLO VERİ ÇEKME VE TEPE TOPLAMI HESAPLAMA
$db_raporlar = $db->query("SELECT * FROM `aylik_harcama_raporu` WHERE `donem` LIKE '{$aktif_yil}-%'")->fetchAll(PDO::FETCH_ASSOC);

$yillik_matris = [];
foreach ($db_raporlar as $r_satir) { $yillik_matris[intval(substr($r_satir['donem'], 5, 2))] = $r_satir; }

$tepe_toplamlar = ['devir_yatirim'=>0,'devir_bakiye'=>0,'maas_o'=>0,'maas_a'=>0,'maktu_mesai'=>0,'ek_gelir'=>0,'faiz_gelir'=>0,'kredi_karti_o'=>0,'kredi_karti_a'=>0,'ev_kredisi'=>0,'aidat'=>0,'harcama_nakit'=>0,'fatura_nakit'=>0,'elektrik'=>0,'su'=>0,'dogalgaz'=>0,'telefon'=>0,'internet'=>0,'yatirim_tutari'=>0];

for ($m = 1; $m <= 12; $m++) {
    if (isset($yillik_matris[$m])) {
                
        foreach ($tepe_toplamlar as $s_key => $v) { 
            $tepe_toplamlar[$s_key] += floatval($yillik_matris[$m][$s_key] ?? 0); 
        }
    }
}


// Önce temel hesaplamaları değişkenlere atayalım
$butun_gelir = $tepe_toplamlar['maas_o'] + $tepe_toplamlar['maas_a'] + $tepe_toplamlar['maktu_mesai'] + $tepe_toplamlar['ek_gelir'] + $tepe_toplamlar['faiz_gelir'];
$butun_gider = $tepe_toplamlar['kredi_karti_o'] + $tepe_toplamlar['kredi_karti_a'] + $tepe_toplamlar['ev_kredisi'] + $tepe_toplamlar['aidat'] + $tepe_toplamlar['harcama_nakit'] + $tepe_toplamlar['elektrik'] + $tepe_toplamlar['su'] + $tepe_toplamlar['dogalgaz'] + $tepe_toplamlar['telefon'] + $tepe_toplamlar['internet'];
$yillik_butun_yatirim_toplami = $tepe_toplamlar['yatirim_tutari'] ?? 0;

// Şimdi temiz bir şekilde array yapısına aktaralım
$yillik_toplam = [
    "butun_gelir"    => $butun_gelir,
    "butun_gider"    => $butun_gider,
    "butun_tasarruf" => $butun_gelir - $butun_gider,
    "genel_tasarruf" => ($butun_gelir - $butun_gider) + $tepe_toplamlar['devir_bakiye'],
    "butun_yatirim"  => $yillik_butun_yatirim_toplami,
    "genel_yatirim"  => $yillik_butun_yatirim_toplami + $tepe_toplamlar['devir_yatirim']
];



// SELECTBOX TARİH: Güncel dönemden selectbox yıl listesini dinamik üretme
// $yeardb = $db->query("SELECT donem FROM aylik_harcama_raporu_arsiv ORDER BY donem DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// if ($yeardb && !empty($yeardb['donem'])) {
//     list($en_guncel_arsiv_yili) = explode('-', $yeardb['donem']);
//     $bitis_yili = intval($en_guncel_arsiv_yili);
// } else {
//     $bitis_yili = intval(date('Y'));
// }
// $selectbox_years = range(2021, $bitis_yili);



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

$canli_ana_yatirim_bakiye_tl = $ana_isbank_a_anapara ?? 0.00;

$tasarruf_tutari = $ana_isbank_a_anapara - $bloke_faturalar_taban - $bloke_su_aidat_taban - $isbank_card_borcu - $vakif_card_borcu;

require admin_view('management');
