<?php

$aktif_donem = $_SESSION['aktif_donem'] ?? date('Y-m');
$ay_isimleri = [1=>'OCAK', 2=>'ŞUBAT', 3=>'MART', 4=>'NİSAN', 5=>'MAYIS', 6=>'HAZİRAN', 7=>'TEMMUZ', 8=>'AĞUSTOS', 9=>'EYLÜL', 10=>'EKİM', 11=>'KASIM', 12=>'ARALIK'];
list($aktif_yil, $aktif_ay_str) = explode('-', $aktif_donem);
$aktif_yil = trim($aktif_yil);
$aktif_ay_index = intval($aktif_ay_str);

// DÖVİZ İŞLEMLERİ (CANLI KURLARIN ALINMASI)
$kur_servis_management = ForexExchange::getLiveRates();
$live_kurlar = $kur_servis_management['kurlar']; 

// Veritabanındaki tüm finansal havuzların listelenmesi (Yatırım terminali select box'larını besler)
$tum_havuzlar_sorgu = $db->query("SELECT `id`, `havuz_kodu`, `havuz_adi`, `anapara`, `usd_bakiye`, `eu_bakiye`, `xau_bakiye` FROM `finansal_havuzlar` ORDER BY `id` ASC");
$finansal_havuzlar = $tum_havuzlar_sorgu->fetchAll(PDO::FETCH_ASSOC);

// Dönemlik saf alım maliyet havuzları (1 Ocak Devirleri ve Dönem İçi Ayırıcı Matrisleri)
$devir_maliyetleri = ['USD' => 0, 'EUR' => 0, 'XAU' => 0];
$donem_ici_maliyetler = ['USD' => 0, 'EUR' => 0, 'XAU' => 0];

// =========================================================================
// 📊 REFORM 1: MEVCUT HAVUZ DİZİSİ ÜZERİNDEN İŞBANK HÜCRELERİNİN AYIKLANMASI
// =========================================================================

// 1. İŞBANK (A) VERİLERİNİN AYIKLANMASI
$isbank_a_anahtari = array_search(2, array_column($finansal_havuzlar, 'id'));

if ($isbank_a_anahtari !== false) {
    $isbank_a_data       = $finansal_havuzlar[$isbank_a_anahtari];
    $isbank_a_anapara    = (float)($isbank_a_data['anapara'] ?? 0); // 0 gösterme hatası bu satırla tamamen çözüldü
    $isbank_a_usd_bakiye = (float)($isbank_a_data['usd_bakiye'] ?? 0);
    $isbank_a_eu_bakiye  = (float)($isbank_a_data['eu_bakiye'] ?? 0);
    $isbank_a_xau_bakiye = (float)($isbank_a_data['xau_bakiye'] ?? 0);
} else {
    $isbank_a_anapara = 0.00; $isbank_a_usd_bakiye = 0.00; $isbank_a_eu_bakiye = 0.00; $isbank_a_xau_bakiye = 0.0000;
}

// 2. İŞBANK (Ö) VERİLERİNİN AYIKLANMASI (YENİ EKLEME TALİMATI)
$isbank_o_anahtari = array_search(3, array_column($finansal_havuzlar, 'id'));

if ($isbank_o_anahtari !== false) {
    $isbank_o_data       = $finansal_havuzlar[$isbank_o_anahtari];
    $isbank_o_eu_bakiye  = (float)($isbank_o_data['eu_bakiye'] ?? 0); // İşbank Ö Euro kasası cımbızlandı
} else {
    $isbank_o_eu_bakiye  = 0.00;
}

// 2. Nakit VERİLERİNİN AYIKLANMASI (YENİ EKLEME TALİMATI)
$nakit_anahtari = array_search(4, array_column($finansal_havuzlar, 'id'));

if ($nakit_anahtari !== false) {
    $nakit_data       = $finansal_havuzlar[$nakit_anahtari];
    $nakit_eu_bakiye  = (float)($nakit_data['eu_bakiye'] ?? 0); // nakit Euro kasası cımbızlandı
} else {
    $nakit_eu_bakiye  = 0.00;
}

// =========================================================================
// B. POST DURUMU: HÜCRESEL PANEL ÇİFT YÖNLÜ (ALIS/SATIS) AJAX YÖNETİMİ
// =========================================================================
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'interaktif_forex_panel_islem') {
    
//     // 🔒 ÇIKTI TEMİZLİK KALKANI: Olası PHP hata/uyarı sızıntılarının JSON yapısını bozmasını engeller
//     if (ob_get_length()) { ob_clean(); }
//     error_reporting(0);
//     ini_set('display_errors', 0);

//     // Standart AJAX Yanıt Şablonu
//     $response = [
//         'success' => false,
//         'message' => 'Bilinmeyen bir iç hata oluştu.',
//         'data'    => []
//     ];

//     // --- 1. GİRDİLERİN ARITILMASI VE GÜVENLİK FİLTRELERİ ---
//     $islem_yonu         = filter_input(INPUT_POST, 'panel_islem_yonu', FILTER_SANITIZE_SPECIAL_CHARS); // ALIS veya SATIS
//     $cekilecek_ham      = filter_input(INPUT_POST, 'panel_cekilecek_hesap', FILTER_SANITIZE_SPECIAL_CHARS); // Örn: "2_anapara_TL" veya "2_usd_bakiye_USD"
//     $aktarilacak_ham    = filter_input(INPUT_POST, 'panel_aktarilacak_hesap', FILTER_SANITIZE_SPECIAL_CHARS); // Örn: "2_eu_bakiye_EUR" veya "2_anapara_TL"
//     $tutar_tl           = filter_input(INPUT_POST, 'panel_tutar_tl', FILTER_VALIDATE_FLOAT); // Toplam TL Karşılığı (Vergi dahil/Brüt)
//     $miktar             = filter_input(INPUT_POST, 'panel_miktar', FILTER_VALIDATE_FLOAT); // İşlem yapılan döviz adedi
//     $manuel_kur         = filter_input(INPUT_POST, 'panel_manuel_kur', FILTER_VALIDATE_FLOAT);
    
//     $islem_tarihi       = date('Y-m-d');
//     $aktif_donem        = (isset($aktif_donem) && !empty($aktif_donem)) ? $aktif_donem : date('Y-m'); 

//     // Hücresel value mimarisini parçalayarak ID ve varlık birimini dinamik yakalıyoruz
//     $cekilecek_parcalar   = explode('_', $cekilecek_ham);
//     $aktarilacak_parcalar = explode('_', $aktarilacak_ham);

//     // 🎯 REVİZYON: Yeni standart kodlama mantığı entegre edildi
//     if ($islem_yonu === 'ALIS') {
//         $tl_havuz_id = (int)$cekilecek_parcalar[0];
//         $doviz_cinsi = end($aktarilacak_parcalar); // USD, EUR, XAU
//         $islem_turu  = 2; // 🔄 ALIM DAİMA = 2
//     } else {
//         $tl_havuz_id = (int)$aktarilacak_parcalar[0];
//         $doviz_cinsi = end($cekilecek_parcalar); // USD, EUR, XAU
//         $islem_turu  = 1; // 🔄 SATIŞ DAİMA = 1
//     }

//     // Canlı kurların varlık kontrolü ve emniyet şeridi
//     if (!isset($live_kurlar) || !is_array($live_kurlar)) {
//         $live_kurlar = ['USD' => 46.8215, 'EUR' => 53.8685, 'XAU' => 6086.4200];
//     }
//     $saf_canli_kur = (float)($live_kurlar[$doviz_cinsi] ?? 0);
//     $doviz_kuru    = ($manuel_kur > 0) ? $manuel_kur : $saf_canli_kur;

//     // Sütun manipülasyonunu engelleyen Beyaz Liste (Whitelist)
//     $doviz_sutun_haritasi = [
//         'USD' => 'usd_bakiye',
//         'EUR' => 'eu_bakiye',
//         'XAU' => 'xau_bakiye'
//     ];

//     if (!array_key_exists($doviz_cinsi, $doviz_sutun_haritasi)) {
//         $response['message'] = "Emniyet Bariyeri: Geçersiz döviz/varlık cinsi saptandı!";
//         header('Content-Type: application/json; charset=utf-8');
//         echo json_encode($response);
//         exit;
//     }

//     $stok_sutun_adi = $doviz_sutun_haritasi[$doviz_cinsi];

//     // --- 2. VERİTABANI İŞLEMİ (TRANSACTION) VE EMRE İTAAT MOTORU ---
//     if ($tl_havuz_id > 0 && $doviz_kuru > 0 && ($tutar_tl > 0 || $miktar > 0)) {
//         try {
//             $db->beginTransaction();

//             // 🔄 Çapraz Giriş Matematik Formül Entegrasyonu (%0.2 Kambiyo Vergisi Dahil)
//             if ($tutar_tl > 0 && (!$miktar || $miktar <= 0)) {
//                 $miktar = ($islem_yonu === 'ALIS') ? ($tutar_tl / 1.0020) / $doviz_kuru : $tutar_tl / $doviz_kuru;
//             } elseif ($miktar > 0 && (!$tutar_tl || $tutar_tl <= 0)) {
//                 $tutar_tl = ($islem_yonu === 'ALIS') ? ($miktar * $doviz_kuru) * 1.0020 : $miktar * $doviz_kuru;
//             }

//             // Vergi ve Net Matrah Ayrıştırması
//             $vergi_tutari = ($islem_yonu === 'ALIS') ? ($tutar_tl - ($tutar_tl / 1.0020)) : 0;
//             $net_tutar    = $tutar_tl - $vergi_tutari;

//             // Sızdırmazlık kalkanı: İlgili havuzun güncel durumunu satır kilitleme (FOR UPDATE) ile oku
//             $havuz_sorgu = $db->prepare("SELECT `anapara`, `usd_bakiye`, `eu_bakiye`, `xau_bakiye` FROM `finansal_havuzlar` WHERE `id` = ? LIMIT 1 FOR UPDATE");
//             $havuz_sorgu->execute([$tl_havuz_id]);
//             $aktif_havuz = $havuz_sorgu->fetch(PDO::FETCH_ASSOC);

//             if (!$aktif_havuz) {
//                 throw new Exception("Seçilen finansal havuz sistemde bulunamadı!");
//             }

//             // 🔀 ÇİFT YÖNLÜ DOĞRULAMA VE HESAP REFORMA HAREKETİ
//             if ($islem_yonu === 'ALIS') {
//                 if ($tutar_tl > (float)$aktif_havuz['anapara']) {
//                     throw new Exception("Seçilen havuzda yeterli TL anapara bakiyesi yok!");
//                 }
//                 // Havuz Bakiyelerini Güncelle: TL Azalt, Döviz Stokunu Artır
//                 $havuz_guncelle = $db->prepare("UPDATE `finansal_havuzlar` SET `anapara` = `anapara` - ?, `{$stok_sutun_adi}` = `{$stok_sutun_adi}` + ? WHERE `id` = ?");
//                 $havuz_guncelle->execute([$tutar_tl, $miktar, $tl_havuz_id]);
                
//             } else {
//                 if ($miktar > (float)($aktif_havuz[$stok_sutun_adi] ?? 0)) {
//                     throw new Exception("Seçilen havuz kasasında satmak istediğiniz miktarda stok bulunmuyor!");
//                 }
//                 // Havuz Bakiyelerini Güncelle: Döviz Stokunu Düş, Kazanılan TL'yi Ekle
//                 $havuz_guncelle = $db->prepare("UPDATE `finansal_havuzlar` SET `{$stok_sutun_adi}` = `{$stok_sutun_adi}` - ?, `anapara` = `anapara` + ? WHERE `id` = ?");
//                 $havuz_guncelle->execute([$miktar, $tutar_tl, $tl_havuz_id]);
//             }

//             // 📝 ADIM 2: Forex İşlemler Tablosuna Detaylı Emir Kaydı ($islem_turu güncellenmiş yeni koda göre basılır)
//             $forex_kayit = $db->prepare("INSERT INTO `forex_islemler` (`islem_tarihi`, `islem_turu`, `doviz_cinsi`, `miktar`, `doviz_kuru`, `tutar`, `vergi`, `maliyet`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
//             $forex_kayit->execute([$islem_tarihi, $islem_turu, $doviz_cinsi, $miktar, $doviz_kuru, $net_tutar, $vergi_tutari, $tutar_tl]);

//             // 📊 ADIM 3: Sadece Management/Yönetim sayfasından (id=2 olan hesaptan) ALIM yapıldıysa Aylık Harcama Raporuna kümülatif ekle
//             if ($tl_havuz_id === 2 && $islem_yonu === 'ALIS') {
//                 $rapor_guncelle = $db->prepare("INSERT INTO `aylik_harcama_raporu` (`donem`, `yatirim_tutari`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `yatirim_tutari` = `yatirim_tutari` + VALUES(`yatirim_tutari`)");
//                 $rapor_guncelle->execute([$aktif_donem, $tutar_tl]);
//             }


//             $db->commit();

//             // 🎯 BAŞARI ENTEGRASYONU: Sayfa yenilenince yerel $success alanına düşecek veri
//             if (session_status() === PHP_SESSION_NONE) { session_start(); }
//             $_SESSION['forex_success'] = $islem_yonu === 'ALIS' ? "Döviz ALIM İŞLEMİ BAŞARILI" : "Döviz SATIM İŞLEMİ BAŞARILI";

//             $response['success'] = true;
//             $response['message'] = "İşlem tamamlandı.";

//             // Güncel bakiye verilerini çekme alanı
//             $bakiye_sorgu = $db->prepare("SELECT `id`, `anapara`, `usd_bakiye`, `eu_bakiye`, `xau_bakiye` FROM `finansal_havuzlar` WHERE `id` = ?");
//             $bakiye_sorgu->execute([$tl_havuz_id]);
//             $guncel_havuz = $bakiye_sorgu->fetch(PDO::FETCH_ASSOC);
//             $response['data'] = [
//                 'havuz_id'     => (int)$guncel_havuz['id'],
//                 'yeni_anapara' => (float)$guncel_havuz['anapara']
//             ];

//         } catch (Exception $e) {
//             if ($db->inTransaction()) { $db->rollBack(); }
            
//             // 🎯 HATA ENTEGRASYONU: Sayfa yenilenince yerel $error alanına düşecek arıza verisi
//             if (session_status() === PHP_SESSION_NONE) { session_start(); }
//             $_SESSION['forex_error'] = "Emniyet Bariyeri: " . $e->getMessage();

//             $response['message'] = "Emniyet Bariyeri: " . $e->getMessage();
//         }
//     } else {
//         $response['message'] = "Emniyet Bariyeri: Parametreler sıfırdan büyük olmalıdır!";
//     }

//     // Saf PHP Çıktı Kilidi
//     if (ob_get_length()) { ob_end_clean(); }
//     if (!headers_sent()) {
//         header_remove();
//         http_response_code(200);
//         header('Content-Type: application/json; charset=utf-8');
//     }
//     echo json_encode($response, JSON_UNESCAPED_UNICODE);
//     exit;

// }

$islem = $_POST['islem'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($islem)) {
    while (ob_get_level()) { ob_end_clean(); }
    header('Content-Type: application/json; charset=utf-8');
    ini_set('display_errors', 0);

    if($islem === 'interaktif_forex_panel_islem') {

        // Standart AJAX Yanıt Şablonu
        $response = [
            'success' => false,
            'message' => 'Bilinmeyen bir iç hata oluştu.',
            'data'    => []
        ];

        // --- 1. GİRDİLERİN ARITILMASI VE GÜVENLİK FİLTRELERİ ---
        $islem_yonu         = filter_input(INPUT_POST, 'panel_islem_yonu', FILTER_SANITIZE_SPECIAL_CHARS); // ALIS veya SATIS
        $cekilecek_ham      = filter_input(INPUT_POST, 'panel_cekilecek_hesap', FILTER_SANITIZE_SPECIAL_CHARS); // Örn: "2_anapara_TL" veya "2_usd_bakiye_USD"
        $aktarilacak_ham    = filter_input(INPUT_POST, 'panel_aktarilacak_hesap', FILTER_SANITIZE_SPECIAL_CHARS); // Örn: "2_eu_bakiye_EUR" veya "2_anapara_TL"
        $tutar_tl           = filter_input(INPUT_POST, 'panel_tutar_tl', FILTER_VALIDATE_FLOAT); // Toplam TL Karşılığı (Vergi dahil/Brüt)
        $miktar             = filter_input(INPUT_POST, 'panel_miktar', FILTER_VALIDATE_FLOAT); // İşlem yapılan döviz adedi
        $manuel_kur         = filter_input(INPUT_POST, 'panel_manuel_kur', FILTER_VALIDATE_FLOAT);
        
        $islem_tarihi       = date('Y-m-d');
        $aktif_donem        = (isset($aktif_donem) && !empty($aktif_donem)) ? $aktif_donem : date('Y-m'); 

        // Hücresel value mimarisini parçalayarak ID ve varlık birimini dinamik yakalıyoruz
        $cekilecek_parcalar   = explode('_', $cekilecek_ham);
        $aktarilacak_parcalar = explode('_', $aktarilacak_ham);

        // 🎯 REVİZYON: Yeni standart kodlama mantığı entegre edildi
        if ($islem_yonu === 'ALIS') {
            $tl_havuz_id = (int)$cekilecek_parcalar[0];
            $doviz_cinsi = end($aktarilacak_parcalar); // USD, EUR, XAU
            $islem_turu  = 2; // 🔄 ALIM DAİMA = 2
        } else {
            $tl_havuz_id = (int)$aktarilacak_parcalar[0];
            $doviz_cinsi = end($cekilecek_parcalar); // USD, EUR, XAU
            $islem_turu  = 1; // 🔄 SATIŞ DAİMA = 1
        }

        // Canlı kurların varlık kontrolü ve emniyet şeridi
        if (!isset($live_kurlar) || !is_array($live_kurlar)) {
            $live_kurlar = ['USD' => 46.8215, 'EUR' => 53.8685, 'XAU' => 6086.4200];
        }
        $saf_canli_kur = (float)($live_kurlar[$doviz_cinsi] ?? 0);
        $doviz_kuru    = ($manuel_kur > 0) ? $manuel_kur : $saf_canli_kur;

        // Sütun manipülasyonunu engelleyen Beyaz Liste (Whitelist)
        $doviz_sutun_haritasi = [
            'USD' => 'usd_bakiye',
            'EUR' => 'eu_bakiye',
            'XAU' => 'xau_bakiye'
        ];

        if (!array_key_exists($doviz_cinsi, $doviz_sutun_haritasi)) {
            $response['message'] = "Emniyet Bariyeri: Geçersiz döviz/varlık cinsi saptandı!";
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit;
        }

        $stok_sutun_adi = $doviz_sutun_haritasi[$doviz_cinsi];

        // --- 2. VERİTABANI İŞLEMİ (TRANSACTION) VE EMRE İTAAT MOTORU ---
        if ($tl_havuz_id > 0 && $doviz_kuru > 0 && ($tutar_tl > 0 || $miktar > 0)) {
            try {
                $db->beginTransaction();

                // 🔄 Çapraz Giriş Matematik Formül Entegrasyonu (%0.2 Kambiyo Vergisi Dahil)
                if ($tutar_tl > 0 && (!$miktar || $miktar <= 0)) {
                    $miktar = ($islem_yonu === 'ALIS') ? ($tutar_tl / 1.0020) / $doviz_kuru : $tutar_tl / $doviz_kuru;
                } elseif ($miktar > 0 && (!$tutar_tl || $tutar_tl <= 0)) {
                    $tutar_tl = ($islem_yonu === 'ALIS') ? ($miktar * $doviz_kuru) * 1.0020 : $miktar * $doviz_kuru;
                }

                // Vergi ve Net Matrah Ayrıştırması
                $vergi_tutari = ($islem_yonu === 'ALIS') ? ($tutar_tl - ($tutar_tl / 1.0020)) : 0;
                $net_tutar    = $tutar_tl - $vergi_tutari;

                // Sızdırmazlık kalkanı: İlgili havuzun güncel durumunu satır kilitleme (FOR UPDATE) ile oku
                $havuz_sorgu = $db->prepare("SELECT `anapara`, `usd_bakiye`, `eu_bakiye`, `xau_bakiye` FROM `finansal_havuzlar` WHERE `id` = ? LIMIT 1 FOR UPDATE");
                $havuz_sorgu->execute([$tl_havuz_id]);
                $aktif_havuz = $havuz_sorgu->fetch(PDO::FETCH_ASSOC);

                if (!$aktif_havuz) {
                    throw new Exception("Seçilen finansal havuz sistemde bulunamadı!");
                }

                // 🔀 ÇİFT YÖNLÜ DOĞRULAMA VE HESAP REFORMA HAREKETİ
                if ($islem_yonu === 'ALIS') {
                    if ($tutar_tl > (float)$aktif_havuz['anapara']) {
                        throw new Exception("Seçilen havuzda yeterli TL anapara bakiyesi yok!");
                    }
                    // Havuz Bakiyelerini Güncelle: TL Azalt, Döviz Stokunu Artır
                    $havuz_guncelle = $db->prepare("UPDATE `finansal_havuzlar` SET `anapara` = `anapara` - ?, `{$stok_sutun_adi}` = `{$stok_sutun_adi}` + ? WHERE `id` = ?");
                    $havuz_guncelle->execute([$tutar_tl, $miktar, $tl_havuz_id]);
                    
                } else {
                    if ($miktar > (float)($aktif_havuz[$stok_sutun_adi] ?? 0)) {
                        throw new Exception("Seçilen havuz kasasında satmak istediğiniz miktarda stok bulunmuyor!");
                    }
                    // Havuz Bakiyelerini Güncelle: Döviz Stokunu Düş, Kazanılan TL'yi Ekle
                    $havuz_guncelle = $db->prepare("UPDATE `finansal_havuzlar` SET `{$stok_sutun_adi}` = `{$stok_sutun_adi}` - ?, `anapara` = `anapara` + ? WHERE `id` = ?");
                    $havuz_guncelle->execute([$miktar, $tutar_tl, $tl_havuz_id]);
                }

                // 📝 ADIM 2: Forex İşlemler Tablosuna Detaylı Emir Kaydı ($islem_turu güncellenmiş yeni koda göre basılır)
                $forex_kayit = $db->prepare("INSERT INTO `forex_islemler` (`islem_tarihi`, `islem_turu`, `doviz_cinsi`, `miktar`, `doviz_kuru`, `tutar`, `vergi`, `maliyet`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $forex_kayit->execute([$islem_tarihi, $islem_turu, $doviz_cinsi, $miktar, $doviz_kuru, $net_tutar, $vergi_tutari, $tutar_tl]);

                // 📊 ADIM 3: Sadece Management/Yönetim sayfasından (id=2 olan hesaptan) ALIM yapıldıysa Aylık Harcama Raporuna kümülatif ekle
                if ($tl_havuz_id === 2 && $islem_yonu === 'ALIS') {
                    $rapor_guncelle = $db->prepare("INSERT INTO `aylik_harcama_raporu` (`donem`, `yatirim_tutari`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `yatirim_tutari` = `yatirim_tutari` + VALUES(`yatirim_tutari`)");
                    $rapor_guncelle->execute([$aktif_donem, $tutar_tl]);
                }


                $db->commit();

                // 🎯 BAŞARI ENTEGRASYONU: Sayfa yenilenince yerel $success alanına düşecek veri
                if (session_status() === PHP_SESSION_NONE) { session_start(); }
                $_SESSION['forex_success'] = $islem_yonu === 'ALIS' ? "Döviz ALIM İŞLEMİ BAŞARILI" : "Döviz SATIM İŞLEMİ BAŞARILI";

                $response['success'] = true;
                $response['message'] = "İşlem tamamlandı.";

                // Güncel bakiye verilerini çekme alanı
                $bakiye_sorgu = $db->prepare("SELECT `id`, `anapara`, `usd_bakiye`, `eu_bakiye`, `xau_bakiye` FROM `finansal_havuzlar` WHERE `id` = ?");
                $bakiye_sorgu->execute([$tl_havuz_id]);
                $guncel_havuz = $bakiye_sorgu->fetch(PDO::FETCH_ASSOC);
                $response['data'] = [
                    'havuz_id'     => (int)$guncel_havuz['id'],
                    'yeni_anapara' => (float)$guncel_havuz['anapara']
                ];

            } catch (Exception $e) {
                if ($db->inTransaction()) { $db->rollBack(); }
                
                // 🎯 HATA ENTEGRASYONU: Sayfa yenilenince yerel $error alanına düşecek arıza verisi
                if (session_status() === PHP_SESSION_NONE) { session_start(); }
                $_SESSION['forex_error'] = "Emniyet Bariyeri: " . $e->getMessage();

                $response['message'] = "Emniyet Bariyeri: " . $e->getMessage();
            }
        } else {
            $response['message'] = "Emniyet Bariyeri: Parametreler sıfırdan büyük olmalıdır!";
        }

        // Saf PHP Çıktı Kilidi
        if (ob_get_length()) { ob_end_clean(); }
        if (!headers_sent()) {
            header_remove();
            http_response_code(200);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;


    }
       
    // if (isset($_POST['islem']) && $_POST['islem'] === 'mevduat_transfer') {
        
    //     // TEMİZ ÇIKTI KALKANI: Olası PHP sızıntılarının veya uyarılarının JSON yapısını bozmasını engeller
    //     if (ob_get_level() > 0) { ob_clean(); }
    //     header('Content-Type: application/json; charset=utf-8');

    //     // Girdilerin Arıtılması ve Güvenlik Filtreleri (JS AJAX parametreleriyle eşleştirildi)
    //     $kaynak_havuz   = filter_input(INPUT_POST, 'kaynak_havuz', FILTER_SANITIZE_SPECIAL_CHARS);
    //     $hedef_havuz    = filter_input(INPUT_POST, 'hedef_havuz', FILTER_SANITIZE_SPECIAL_CHARS);
    //     $varlik_cinsi   = filter_input(INPUT_POST, 'varlik_cinsi', FILTER_SANITIZE_SPECIAL_CHARS); // USD, EUR, XAU, TL
    //     $cozum_turu     = filter_input(INPUT_POST, 'cozum_turu', FILTER_SANITIZE_SPECIAL_CHARS);   // yeterli, sadece_mevcut, destekli
    //     $destek_havuz   = filter_input(INPUT_POST, 'destek_havuz', FILTER_SANITIZE_SPECIAL_CHARS);
        
    //     // Önyüzden gelen ham para formatını float türüne dönüştürme (AJAX'taki isimle eşitlendi)
    //     $toplam_talep_tutari = filter_input(INPUT_POST, 'toplam_talep_tutari', FILTER_VALIDATE_FLOAT);

    //     // 🔒 EMNİYET BARİYERİ 1: Temel Parametre Doğrulaması
    //     if (empty($kaynak_havuz) || empty($hedef_havuz) || $toplam_talep_tutari === false || $toplam_talep_tutari <= 0 || $kaynak_havuz === $hedef_havuz) {
    //         echo json_encode(['success' => false, 'message' => 'Geçersiz parametre hatası veya hatalı transfer miktarı!']);
    //         exit;
    //     }

    //     // 🔒 EMNİYET BARİYERİ 2: Sütun Manipülasyonunu Engelleyen Beyaz Liste (Whitelist)
    //     $varlik_sutun_haritasi = [
    //         'TL'  => 'anapara',
    //         'USD' => 'usd_bakiye',
    //         'EUR' => 'eu_bakiye',
    //         'XAU' => 'xau_bakiye'
    //     ];

    //     if (!array_key_exists($varlik_cinsi, $varlik_sutun_haritasi)) {
    //         echo json_encode(['success' => false, 'message' => 'Emniyet Bariyeri: Geçersiz veya desteklenmeyen varlık cinsi!']);
    //         exit;
    //     }

    //     // İşlem yapılacak dinamik veritabanı sütun adı
    //     $hedef_sutun = $varlik_sutun_haritasi[$varlik_cinsi];

    //     try {
    //         // Çift yönlü veritabanı bütünlüğü için Transaction Başlatma
    //         $db->beginTransaction();

    //         // 1. Kaynak Havuzun Güncel Durumunu Satır Kilitleme (FOR UPDATE) ile Oku
    //         $stmt = $db->prepare("SELECT `{$hedef_sutun}` FROM `finansal_havuzlar` WHERE `havuz_kodu` = :kod LIMIT 1 FOR UPDATE");
    //         $stmt->execute([':kod' => $kaynak_havuz]);
    //         $kaynak_data = $stmt->fetch(PDO::FETCH_ASSOC);

    //         if (!$kaynak_data) {
    //             throw new Exception("Kaynak hesap veritabanında bulunamadı!");
    //         }

    //         $kaynak_mevcut_bakiye = floatval($kaynak_data[$hedef_sutun] ?? 0);

    //         // Matematiksel Dağılım Değişkenleri
    //         $ana_transfer_tutari    = 0.00;
    //         $destek_transfer_tutari = 0.00;
    //         $toplam_aktarilan       = 0.00;

    //         // 2. Senaryo Yönetim Matrisi (Varlık Sütununa Göre Dinamikleştirildi)
    //         if ($cozum_turu === 'yeterli') {
    //             if ($kaynak_mevcut_bakiye < $toplam_talep_tutari) {
    //                 throw new Exception("Kaynak hesap bakiyesi yetersiz! Lütfen sayfayı yenileyip tekrar deneyin.");
    //             }
    //             $ana_transfer_tutari = $toplam_talep_tutari;
    //             $toplam_aktarilan    = $toplam_talep_tutari;

    //         } elseif ($cozum_turu === 'sadece_mevcut') {
    //             if ($kaynak_mevcut_bakiye <= 0) {
    //                 throw new Exception("Kaynak hesapta aktarılacak bakiye bulunmuyor!");
    //             }
    //             $ana_transfer_tutari = $kaynak_mevcut_bakiye;
    //             $toplam_aktarilan    = $kaynak_mevcut_bakiye;

    //         } elseif ($cozum_turu === 'destekli') {
    //             if (empty($destek_havuz) || $destek_havuz === $kaynak_havuz || $destek_havuz === $hedef_havuz) {
    //                 throw new Exception("Geçersiz veya hatalı destek hesap seçimi!");
    //             }

    //             // Destek havuzun güncel bakiyesini sorgulayalım (FOR UPDATE)
    //             $stmtDestek = $db->prepare("SELECT `{$hedef_sutun}` FROM `finansal_havuzlar` WHERE `havuz_kodu` = :kod LIMIT 1 FOR UPDATE");
    //             $stmtDestek->execute([':kod' => $destek_havuz]);
    //             $destek_data = $stmtDestek->fetch(PDO::FETCH_ASSOC);

    //             if (!$destek_data) {
    //                 throw new Exception("Destek hesap veritabanında bulunamadı!");
    //             }

    //             $destek_mevcut_bakiye = floatval($destek_data[$hedef_sutun] ?? 0);
    //             $eksik_tutar = $toplam_talep_tutari - $kaynak_mevcut_bakiye;

    //             if ($destek_mevcut_bakiye < $eksik_tutar) {
    //                 throw new Exception("Destek hesabın bakiyesi de eksik tutarı kapatmaya yetmiyor!");
    //             }

    //             // Paylaşımlı transfer dağılımı
    //             $ana_transfer_tutari    = $kaynak_mevcut_bakiye; 
    //             $destek_transfer_tutari = $eksik_tutar;
    //             $toplam_aktarilan       = $toplam_talep_tutari;

    //         } else {
    //             throw new Exception("Sistem tarafından anlaşılamayan transfer çözüm türü!");
    //         }

    //         // 3. Veritabanı Güncelleme Operasyonları (UPDATE)
            
    //         // A) Kaynak havuzun ilgili döviz kasasından parayı düş
    //         if ($ana_transfer_tutari > 0) {
    //             $upKaynak = $db->prepare("UPDATE `finansal_havuzlar` SET `{$hedef_sutun}` = `{$hedef_sutun}` - :tutar WHERE `havuz_kodu` = :kod");
    //             $upKaynak->execute([':tutar' => $ana_transfer_tutari, ':kod' => $kaynak_havuz]);
    //         }

    //         // B) Varsa Destek havuzun aynı döviz kasasından eksiği düş
    //         if ($destek_transfer_tutari > 0) {
    //             $upDestek = $db->prepare("UPDATE `finansal_havuzlar` SET `{$hedef_sutun}` = `{$hedef_sutun}` - :tutar WHERE `havuz_kodu` = :kod");
    //             $upDestek->execute([':tutar' => $destek_transfer_tutari, ':kod' => $destek_havuz]);
    //         }

    //         // C) Hedef havuzun aynı döviz kasasına toplam parayı ekle
    //         if ($toplam_aktarilan > 0) {
    //             $upHedef = $db->prepare("UPDATE `finansal_havuzlar` SET `{$hedef_sutun}` = `{$hedef_sutun}` + :tutar WHERE `havuz_kodu` = :kod");
    //             $upHedef->execute([':tutar' => $toplam_aktarilan, ':kod' => $hedef_havuz]);
    //         }

    //         // 4. Akıllı Log Sistemini Besleme (INSERT)
    //         $logSql = "INSERT INTO `transfer_logs` 
    //                     (`donem`, `kaynak_havuz`, `destek_havuz`, `hedef_havuz`, `ana_transfer_tutari`, `destek_transfer_tutari`, `toplam_aktarilan`, `cozum_turu`, `tarih`) 
    //                 VALUES 
    //                     (:donem, :kaynak, :destek, :hedef, :ana_tutar, :destek_tutar, :toplam, :cozum, NOW())";
            
    //         $logStmt = $db->prepare($logSql);
    //         $logStmt->execute([
    //             ':donem'        => $aktif_donem ?? date('Y-m'),
    //             ':kaynak'       => $kaynak_havuz . "_{$varlik_cinsi}",
    //             ':destek'       => !empty($destek_havuz) && $destek_transfer_tutari > 0 ? $destek_havuz . "_{$varlik_cinsi}" : null,
    //             ':hedef'        => $hedef_havuz . "_{$varlik_cinsi}",
    //             ':ana_tutar'    => $ana_transfer_tutari,
    //             ':destek_tutar' => $destek_transfer_tutari,
    //             ':toplam'       => $toplam_aktarilan,
    //             ':cozum'        => $cozum_turu
    //         ]);

    //         // Her şey yolunda, işlemleri taahhüt et ve kalıcı yap!
    //         $db->commit();

    //         echo json_encode([
    //             'success' => true, 
    //             'message' => 'Likidite transferi başarıyla işlendi! Kasa bakiyeleri güncellendi.'
    //         ]);
    //         exit;

    //     } catch (Exception $e) {
    //         // Hata durumunda rollback yaparak veritabanı mizan bütünlüğünü koru
    //         if ($db->inTransaction()) { $db->rollBack(); }
            
    //         echo json_encode([
    //             'success' => false, 
    //             'message' => 'Finansal Transfer İptal Edildi: ' . $e->getMessage()
    //         ]);
    //         exit;
    //     }
    // }
    if (isset($_POST['islem']) && $_POST['islem'] === 'mevduat_transfer') {
    
    if (ob_get_level() > 0) { ob_clean(); }
    header('Content-Type: application/json; charset=utf-8');

    $kaynak_havuz   = filter_input(INPUT_POST, 'kaynak_havuz', FILTER_SANITIZE_SPECIAL_CHARS);
    $hedef_havuz    = filter_input(INPUT_POST, 'hedef_havuz', FILTER_SANITIZE_SPECIAL_CHARS);
    $varlik_cinsi   = filter_input(INPUT_POST, 'varlik_cinsi', FILTER_SANITIZE_SPECIAL_CHARS); 
    $cozum_turu     = filter_input(INPUT_POST, 'cozum_turu', FILTER_SANITIZE_SPECIAL_CHARS);   
    $destek_havuz   = filter_input(INPUT_POST, 'destek_havuz', FILTER_SANITIZE_SPECIAL_CHARS);
    $toplam_talep_tutari = filter_input(INPUT_POST, 'toplam_talep_tutari', FILTER_VALIDATE_FLOAT);

    if (empty($kaynak_havuz) || empty($hedef_havuz) || $toplam_talep_tutari === false || $toplam_talep_tutari <= 0) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz parametre veya hatalı miktar girişi!']);
        exit;
    }
 
    $varlik_sutun_haritasi = [
        'TL'  => 'anapara',
        'USD' => 'usd_bakiye',
        'EUR' => 'eu_bakiye',
        'XAU' => 'xau_bakiye'
    ];

    if (!array_key_exists($varlik_cinsi, $varlik_sutun_haritasi)) {
        echo json_encode(['success' => false, 'message' => 'Desteklenmeyen varlık cinsi!']);
        exit;
    }

    $hedef_sutun = $varlik_sutun_haritasi[$varlik_cinsi];

    try {
        $db->beginTransaction();

        // =========================================================================
        // 🔥 DOĞRU SENARYO: HEDEF "NAKIT_HARCAMA" İSE (SİSTEMDEN PARA ÇIKIŞI)
        // =========================================================================
        if ($hedef_havuz === 'NAKIT_HARCAMA') {
            
            // Paranın eksileceği KAYNAK havuzun güncel bakiyesini satır bazlı kilitle (FOR UPDATE)
            $stmt = $db->prepare("SELECT `{$hedef_sutun}` FROM `finansal_havuzlar` WHERE `havuz_kodu` = :kod LIMIT 1 FOR UPDATE");
            $stmt->execute([':kod' => $kaynak_havuz]);
            $kaynak_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$kaynak_data) {
                throw new Exception("Harcamanın yapılacağı kaynak hesap veritabanında bulunamadı!");
            }

            $mevcut_kaynak_bakiyesi = floatval($kaynak_data[$hedef_sutun] ?? 0);

            // Emniyet Bariyeri: Gerçek zamanlı likidite kontrolü
            if ($toplam_talep_tutari > $mevcut_kaynak_bakiyesi) {
                throw new Exception("Kaynak hesapta bu nakit harcamayı karşılayacak yeterli bakiye yok!");
            }

            // 🎯 DİNAMİK CANLI KUR ENTEGRASYON MOTORU ($live_kurlar)
            $o_anki_alis_kuru = 1.00; // TL harcamaları için varsayılan çarpan jilet gibi 1'dir
            
            if ($varlik_cinsi === 'EUR') {
                $o_anki_alis_kuru = isset($live_kurlar['EUR']) ? floatval($live_kurlar['EUR']) : (isset($canli_eur_kur) ? floatval($canli_eur_kur) : 1.00);
            } elseif ($varlik_cinsi === 'USD') {
                $o_anki_alis_kuru = isset($live_kurlar['USD']) ? floatval($live_kurlar['USD']) : (isset($canli_usd_kur) ? floatval($canli_usd_kur) : 1.00);
            } elseif ($varlik_cinsi === 'XAU') {
                // Gram altın için eğer $live_kurlar dizisinde tanımlıysa al, yoksa sistemdeki altın değişkenine bak
                $o_anki_alis_kuru = isset($live_kurlar['XAU']) ? floatval($live_kurlar['XAU']) : (isset($canli_xau_kur) ? floatval($canli_xau_kur) : 1.00);
            }

            // Kur değerinin sistemsel bir hatadan ötürü 0 veya negatif gelmesini engelleyen koruma kalkanı
            if ($o_anki_alis_kuru <= 0) {
                throw new Exception("Sistemsel Hata: Canlı döviz veri akışı sağlanamadığı için harcama hesaplanamadı.");
            }

            // Kur x Miktar çarpımı ile net TL tutarını bulma
            $hesaplanan_tutar = $toplam_talep_tutari * $o_anki_alis_kuru;

            // 1. Finansal havuzlar tablosunda seçilen KAYNAK hesaptan harcanan miktarı düş (UPDATE)
            $upKasa = $db->prepare("UPDATE `finansal_havuzlar` SET `{$hedef_sutun}` = `{$hedef_sutun}` - :miktar WHERE `havuz_kodu` = :kod");
            $upKasa->execute([':miktar' => $toplam_talep_tutari, ':kod' => $kaynak_havuz]);

            // 2. Forex İşlemler tablosuna harcama kaydını kuruşu kuruşuna yaz (INSERT - islem_turu = 3)
            $forexSql = "INSERT INTO `forex_islemler` 
                            (`islem_tarihi`, `islem_turu`, `doviz_cinsi`, `miktar`, `doviz_kuru`, `tutar`, `vergi`, `maliyet`) 
                         VALUES 
                            (NOW(), 3, :doviz, :miktar, :kur, :tutar, 0, :maliyet)";
            
            $forexStmt = $db->prepare($forexSql);
            $forexStmt->execute([
                ':doviz'   => $varlik_cinsi,
                ':miktar'  => $toplam_talep_tutari,
                ':kur'     => $o_anki_alis_kuru,
                ':tutar'   => $hesaplanan_tutar,
                ':maliyet' => $hesaplanan_tutar // vergi 0 olduğu için direkt net maliyet tutara eşittir
            ]);

            $db->commit();
            echo json_encode([
                'success' => true, 
                'message' => 'Nakit harcama başarıyla kaydedildi, canlı kur matrisi üzerinden hesaplanarak kaynak hesaptan düşüldü!'
            ]);
            exit;
        }


        // =========================================================================
        // HESAPLAR ARASI NORMAL TRANSFER SENARYOSU (Aynen korunuyor)
        // =========================================================================
        if ($kaynak_havuz === $hedef_havuz) {
            throw new Exception("Kaynak ve hedef havuz aynı olamaz!");
        }

        $stmt = $db->prepare("SELECT `{$hedef_sutun}` FROM `finansal_havuzlar` WHERE `havuz_kodu` = :kod LIMIT 1 FOR UPDATE");
        $stmt->execute([':kod' => $kaynak_havuz]);
        $kaynak_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$kaynak_data) { throw new Exception("Kaynak hesap veritabanında bulunamadı!"); }
        $kaynak_mevcut_bakiye = floatval($kaynak_data[$hedef_sutun] ?? 0);

        $ana_transfer_tutari = 0.00; $destek_transfer_tutari = 0.00; $toplam_aktarilan = 0.00;

        if ($cozum_turu === 'yeterli') {
            if ($kaynak_mevcut_bakiye < $toplam_talep_tutari) { throw new Exception("Bakiye yetersiz!"); }
            $ana_transfer_tutari = $toplam_talep_tutari; $toplam_aktarilan = $toplam_talep_tutari;
        } elseif ($cozum_turu === 'sadece_mevcut') {
            if ($kaynak_mevcut_bakiye <= 0) { throw new Exception("Aktarılacak bakiye yok!"); }
            $ana_transfer_tutari = $kaynak_mevcut_bakiye; $toplam_aktarilan = $kaynak_mevcut_bakiye;
        } elseif ($cozum_turu === 'destekli') {
            $stmtDestek = $db->prepare("SELECT `{$hedef_sutun}` FROM `finansal_havuzlar` WHERE `havuz_kodu` = :kod LIMIT 1 FOR UPDATE");
            $stmtDestek->execute([':kod' => $destek_havuz]);
            if (!$destek_data = $stmtDestek->fetch(PDO::FETCH_ASSOC)) { throw new Exception("Destek hesap bulunamadı!"); }
            $destek_mevcut_bakiye = floatval($destek_data[$hedef_sutun] ?? 0);
            $eksik_tutar = $toplam_talep_tutari - $kaynak_mevcut_bakiye;
            if ($destek_mevcut_bakiye < $eksik_tutar) { throw new Exception("Destek yetersiz!"); }
            $ana_transfer_tutari = $kaynak_mevcut_bakiye; $destek_transfer_tutari = $eksik_tutar; $toplam_aktarilan = $toplam_talep_tutari;
        }

        if ($ana_transfer_tutari > 0) {
            $db->prepare("UPDATE `finansal_havuzlar` SET `{$hedef_sutun}` = `{$hedef_sutun}` - :tutar WHERE `havuz_kodu` = :kod")->execute([':tutar' => $ana_transfer_tutari, ':kod' => $kaynak_havuz]);
        }
        if ($destek_transfer_tutari > 0) {
            $db->prepare("UPDATE `finansal_havuzlar` SET `{$hedef_sutun}` = `{$hedef_sutun}` - :tutar WHERE `havuz_kodu` = :kod")->execute([':tutar' => $destek_transfer_tutari, ':kod' => $destek_havuz]);
        }
        if ($toplam_aktarilan > 0) {
            $db->prepare("UPDATE `finansal_havuzlar` SET `{$hedef_sutun}` = `{$hedef_sutun}` + :tutar WHERE `havuz_kodu` = :kod")->execute([':tutar' => $toplam_aktarilan, ':kod' => $hedef_havuz]);
        }

        $logSql = "INSERT INTO `transfer_logs` (`donem`, `kaynak_havuz`, `destek_havuz`, `hedef_havuz`, `ana_transfer_tutari`, `destek_transfer_tutari`, `toplam_aktarilan`, `cozum_turu`, `tarih`) VALUES (:donem, :kaynak, :destek, :hedef, :ana_tutar, :destek_tutar, :toplam, :cozum, NOW())";
        $db->prepare($logSql)->execute([':donem' => $aktif_donem ?? date('Y-m'), ':kaynak' => $kaynak_havuz . "_{$varlik_cinsi}", ':destek' => !empty($destek_havuz) ? $destek_havuz . "_{$varlik_cinsi}" : null, ':hedef' => $hedef_havuz . "_{$varlik_cinsi}", ':ana_tutar' => $ana_transfer_tutari, ':destek_tutar' => $destek_transfer_tutari, ':toplam' => $toplam_aktarilan, ':cozum' => $cozum_turu]);

        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Likidite transferi başarıyla işlendi!']);
        exit;

    } catch (Exception $e) {
        if ($db->inTransaction()) { $db->rollBack(); }
        echo json_encode(['success' => false, 'message' => 'İşlem İptal Edildi: ' . $e->getMessage()]);
        exit;
    }
}


}
// ---------------------------------------------------------------------------------------------------------------

$emtialar = ['USD', 'EUR', 'XAU'];
$islemler_data = [];

// =========================================================================
// 🔄 KRONOLOJİK YÜRÜYEN BAKİYE VE SAF ALIM MALİYETLERİ HESAP MOTORU
// =========================================================================
foreach ($emtialar as $kod) {
    $gecici_liste = [];
    try {
        $sorgu = $db->prepare("SELECT * FROM `forex_islemler` WHERE `doviz_cinsi` = :kod ORDER BY `islem_tarihi` DESC, `id` DESC");
        $sorgu->execute([':kod' => $kod]);
        $gecici_liste = $sorgu->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) { 
        $gecici_liste = []; 
    }

    if (!empty($gecici_liste)) {
        // Geçmişten bugüne doğru yürüyen bakiyeyi kilitliyoruz
        $kronolojik = array_reverse($gecici_liste);
        $yuruyen_bakiye = 0;

        foreach ($kronolojik as $key => $islem) {
            $miktar = (float)($islem['miktar'] ?? 0);
            $islem_turu_ham = $islem['islem_turu'] ?? 2;

            // Sizin Tablonuzun Orijinal Kuralı: Girişler/Alışlar (2) Pozitif (+), Çıkışlar/Ödemeler (1) Negatif (-)
            if ($islem_turu_ham == 2) {
                $yuruyen_bakiye += $miktar;
            } elseif ($islem_turu_ham == 1) {
                $yuruyen_bakiye -= $miktar;
            } else {
                $yuruyen_bakiye -= $miktar;
            }
            $kronolojik[$key]['anlik_bakiye'] = $yuruyen_bakiye;
        }

        // Tablo görünümü için yeniden orijinal eskiye doğru sıraya döndürme
        $gecici_liste = array_reverse($kronolojik);

        foreach ($gecici_liste as $key => $islem) {
            $tarih = $islem['islem_tarihi'] ?? ''; 
            $miktar = (float)($islem['miktar'] ?? 0);
            $kur = (float)($islem['doviz_kuru'] ?? $islem['islem_kuru'] ?? $islem['kur'] ?? 1);
            
            $maliyet = (float)($islem['maliyet'] ?? ($miktar * $kur));
            $tutar = (float)($islem['tutar'] ?? ($miktar * $kur));
            $vergi = (float)($islem['vergi'] ?? 0);
            $islem_turu_ham = $islem['islem_turu'] ?? 2;

            // HTML/View tarafında uyuşmazlık olmaması için kur bilgisini kesinleştiriyoruz
            $gecici_liste[$key]['doviz_kuru'] = $kur;
            $gecici_liste[$key]['maliyet'] = $maliyet;
            $gecici_liste[$key]['tutar'] = $tutar;
            $gecici_liste[$key]['vergi'] = $vergi;

            if (!empty($tarih) && $islem_turu_ham == 2) {
                $zaman_damgasi = strtotime($tarih);
                $islem_yili = date('Y', $zaman_damgasi); 
                $islem_ay_gun = date('m-d', $zaman_damgasi); 
                $islem_gun = (int)date('d', $zaman_damgasi);
                $islem_ay = (int)date('m', $zaman_damgasi);

                if ($islem_ay_gun === '01-01') {
                    $devir_maliyetleri[$kod] += $maliyet;
                } else {
                    // Sizin Dönem Kuralınız: 14 Aralık - 14 Ocak işlemleri 2026-01 (Ocak) bütçesidir.
                    // 15 Ocak vadeli büyük USD alımı (Satır 23), bütçenizde Ocak dönemine fon sağladığı için buraya kilitlenir.
                    $butce_yili = $islem_yili;
                    
                    // Aralık 2025'in sonundaki taze alımları 2026 bütçe yılına dahil etme kuralı
                    if ($islem_ay == 12 && $islem_gun >= 14) {
                        $butce_yili = "2026";
                    }

                    if ($butce_yili === $aktif_yil) {
                        // 2026 yılı taze, saf alımların gerçek bütçe maliyeti
                        $donem_ici_maliyetler[$kod] += $maliyet;
                    }
                }
            }


        }
        $islemler_data[$kod] = $gecici_liste;
    } else {
        $islemler_data[$kod] = [];
    }
}

$usd_islemler = $islemler_data['USD'] ?? [];
$eur_islemler = $islemler_data['EUR'] ?? [];
$xau_islemler = $islemler_data['XAU'] ?? [];

// 🔒 REFORM: View katmanındaki tüm büyük/küçük harf çağrımlarına tam uyum için çift indeks kilitlendi
$pariteler = [
    'EUR' => [
        'baslik' => '🇪🇺 EUR (Euro) Pozisyon Geçmişi', 'simge' => '€', 'islemler' => $eur_islemler, 'kur_key' => 'EUR',
        'yil_ici_alis_maliyet' => $donem_ici_maliyetler['EUR'] ?? 0,
        'genel_net_pozisyon'      => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'toplam_portfoy_cikis'    => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'toplam_portfoy_giris'    => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'cari_donem_giris'        => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'tarihsel_devir_pozisyon'  => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        // Eski Görünüm Geriye Dönük Uyum Kalkanları (Hataları Önler)
        'genel_toplam'     => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'sadece_alis'      => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'alis_devir_haric' => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00]
    ],
    'USD' => [
        'baslik' => '🇺🇸 USD (Amerikan Doları) Pozisyon Geçmişi', 'simge' => '$', 'islemler' => $usd_islemler, 'kur_key' => 'USD',
        'yil_ici_alis_maliyet' => $donem_ici_maliyetler['USD'] ?? 0,
        'genel_net_pozisyon'      => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'toplam_portfoy_cikis'    => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'toplam_portfoy_giris'    => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'cari_donem_giris'        => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'tarihsel_devir_pozisyon'  => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        // Eski Görünüm Geriye Dönük Uyum Kalkanları (Hataları Önler)
        'genel_toplam'     => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'sadece_alis'      => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'alis_devir_haric' => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00]
    ],
    'XAU' => [
        'baslik' => '👑 XAU (Altın / Gr) Pozisyon Geçmişi', 'simge' => 'GR', 'islemler' => $xau_islemler, 'kur_key' => 'XAU',
        'yil_ici_alis_maliyet' => $donem_ici_maliyetler['XAU'] ?? 0,
        'genel_net_pozisyon'      => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'toplam_portfoy_cikis'    => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'toplam_portfoy_giris'    => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'cari_donem_giris'        => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'tarihsel_devir_pozisyon'  => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        // Eski Görünüm Geriye Dönük Uyum Kalkanları (Hataları Önler)
        'genel_toplam'     => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'sadece_alis'      => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'alis_devir_haric' => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00]
    ]
];

// Emniyet Kemeri: Canlı kurlar dizisi dışarıdan eksik gelirse sistem çökmesin
if (!isset($live_kurlar) || !is_array($live_kurlar)) {
    $live_kurlar = ['USD' => 1.00, 'USD_SATIS' => 1.00, 'EUR' => 1.00, 'EUR_SATIS' => 1.00, 'XAU' => 1.00, 'XAU_SATIS' => 1.00];
}

$anlik_banka_degeri_tl = 0;
$toplam_alis_maliyeti_tl = 0;



// =========================================================================
// --- BÖLÜM 2: DİNAMİK PARİTE YAPILANDIRMASI VE AKILLI KART MATRİSİ ---
// =========================================================================

$usd_islemler = $islemler_data['USD'] ?? [];
$eur_islemler = $islemler_data['EUR'] ?? [];
$xau_islemler = $islemler_data['XAU'] ?? [];

// 🎯 REFORM: Görünüm şablonunun tek döngüde dönmesi için kart parametreleri matrise enjekte edildi.
$pariteler = [
    'EUR' => [
        'baslik'  => 'Euro Bölgesi (EUR)', 
        'simge'   => '€', 
        'bayrak'  => '🇪🇺', 
        'renk'    => 'pastel-eur', 
        // 🎯 EURO KONSOLİDASYONU: Üç kasanın kümülatif toplamı doğrudan kart üstüne basılır
        'is_bakiye' => (float)(($isbank_a_eu_bakiye ?? 0) + ($isbank_o_eu_bakiye ?? 0) + ($nakit_eu_bakiye ?? 0)),
        'btn_key' => 'EUR',
        'fark_renk' => '#0d47a1', // Pozitif kâr mavisi
        'ondalik' => 2,
        'kur_alis' => 'EUR', 'kur_satis' => 'EUR_SATIS', 'kur_basamak' => 4,
        'islemler' => $eur_islemler, 'kur_key' => 'EUR',
        'yil_ici_alis_maliyet' => $donem_ici_maliyetler['EUR'] ?? 0,
        'genel_net_pozisyon'      => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'toplam_portfoy_cikis'    => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'toplam_portfoy_giris'    => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'cari_donem_giris'        => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'tarihsel_devir_pozisyon'  => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'sadece_alis'      => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'alis_devir_haric' => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00]
    ],
    'USD' => [
        'baslik'  => 'Amerikan Doları (USD)', 
        'simge'   => '$', 
        'bayrak'  => '🇺🇸', 
        'renk'    => 'pastel-usd', 
        'is_bakiye' => (float)($isbank_a_usd_bakiye ?? 0),
        'btn_key' => 'USD',
        'fark_renk' => '#1b5e20', // Pozitif kâr yeşili
        'ondalik' => 2,
        'kur_alis' => 'USD', 'kur_satis' => 'USD_SATIS', 'kur_basamak' => 4,
        'islemler' => $usd_islemler, 'kur_key' => 'USD',
        'yil_ici_alis_maliyet' => $donem_ici_maliyetler['USD'] ?? 0,
        'genel_net_pozisyon'      => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'toplam_portfoy_cikis'    => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'toplam_portfoy_giris'    => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'cari_donem_giris'        => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'tarihsel_devir_pozisyon'  => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'sadece_alis'      => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'alis_devir_haric' => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00]
    ],
    'XAU' => [
        'baslik'  => 'Gram Altın (XAU)', 
        'simge'   => 'GR', 
        'bayrak'  => '👑', 
        'renk'    => 'pastel-xau', 
        'is_bakiye' => (float)($isbank_a_xau_bakiye ?? 0),
        'btn_key' => 'XAU',
        'fark_renk' => '#78350f', // Pozitif altın kahvesi
        'ondalik' => 2,
        'kur_alis' => 'XAU', 'kur_satis' => 'XAU_SATIS', 'kur_basamak' => 2,
        'islemler' => $xau_islemler, 'kur_key' => 'XAU',
        'yil_ici_alis_maliyet' => $donem_ici_maliyetler['XAU'] ?? 0,
        'genel_net_pozisyon'      => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'toplam_portfoy_cikis'    => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'toplam_portfoy_giris'    => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'cari_donem_giris'        => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'tarihsel_devir_pozisyon'  => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'sadece_alis'      => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00],
        'alis_devir_haric' => ['miktar' => 0.00, 'tutar' => 0.00, 'vergi' => 0.00, 'maliyet' => 0.00]
    ]
];

// Emniyet Kemeri: Canlı kurlar dizisi dışarıdan eksik gelirse sistem çökmesin
if (!isset($live_kurlar) || !is_array($live_kurlar)) {
    $live_kurlar = ['USD' => 1.00, 'USD_SATIS' => 1.00, 'EUR' => 1.00, 'EUR_SATIS' => 1.00, 'XAU' => 1.00, 'XAU_SATIS' => 1.00];
}

$anlik_banka_degeri_tl = 0;
$toplam_alis_maliyeti_tl = 0;

// =========================================================================
// --- BÖLÜM 3: ÜST TOPLAM TABLOLARINDAKİ 5'Lİ FINANSAL AKIŞ MOTORU ---
// =========================================================================
foreach ($pariteler as $anahtar => $veri) {
    if (empty($veri['islemler'])) { continue; }

    foreach ($veri['islemler'] as $i) {
        $islem_turu_ham = isset($i['islem_turu']) ? (int)$i['islem_turu'] : 2;
        $tarih_metni = $i['islem_tarihi'] ?? '';
        $is_devir_tarihi = (date('m-d', strtotime($tarih_metni)) === '01-01');

        $miktar   = (float)($i['miktar'] ?? 0);
        $tutar    = (float)($i['tutar'] ?? 0);
        $vergi    = (float)($i['vergi'] ?? 0);
        $maliyet  = (float)($i['maliyet'] ?? 0);

        $yon_carpani = ($islem_turu_ham === 2) ? 1.0 : -1.0;
        $pariteler[$anahtar]['genel_net_pozisyon']['miktar']   += $miktar * $yon_carpani;
        $pariteler[$anahtar]['genel_net_pozisyon']['tutar']    += $tutar * $yon_carpani;
        $pariteler[$anahtar]['genel_net_pozisyon']['vergi']    += $vergi * $yon_carpani;
        $pariteler[$anahtar]['genel_net_pozisyon']['maliyet']  += $maliyet * $yon_carpani;

        if ($islem_turu_ham === 1) {
            $pariteler[$anahtar]['toplam_portfoy_cikis']['miktar']  += $miktar;
            $pariteler[$anahtar]['toplam_portfoy_cikis']['tutar']   += $tutar;
            $pariteler[$anahtar]['toplam_portfoy_cikis']['vergi']   += $vergi;
            $pariteler[$anahtar]['toplam_portfoy_cikis']['maliyet'] += $maliyet;
        } else {
            $pariteler[$anahtar]['toplam_portfoy_giris']['miktar']  += $miktar;
            $pariteler[$anahtar]['toplam_portfoy_giris']['tutar']   += $tutar;
            $pariteler[$anahtar]['toplam_portfoy_giris']['vergi']   += $vergi;
            $pariteler[$anahtar]['toplam_portfoy_giris']['maliyet'] += $maliyet;

            if (!$is_devir_tarihi) {
                $pariteler[$anahtar]['cari_donem_giris']['miktar']  += $miktar;
                $pariteler[$anahtar]['cari_donem_giris']['tutar']   += $tutar;
                $pariteler[$anahtar]['cari_donem_giris']['vergi']   += $vergi;
                $pariteler[$anahtar]['cari_donem_giris']['maliyet'] += $maliyet;
            } else {
                $pariteler[$anahtar]['tarihsel_devir_pozisyon']['miktar']  += $miktar;
                $pariteler[$anahtar]['tarihsel_devir_pozisyon']['tutar']   += $tutar;
                $pariteler[$anahtar]['tarihsel_devir_pozisyon']['vergi']   += $vergi;
                $pariteler[$anahtar]['tarihsel_devir_pozisyon']['maliyet'] += $maliyet;
            }
        }
    }

    $pariteler[$anahtar]['sadece_alis']      = $pariteler[$anahtar]['toplam_portfoy_giris'];
    $pariteler[$anahtar]['alis_devir_haric'] = $pariteler[$anahtar]['cari_donem_giris'];

    $net_miktar = $pariteler[$anahtar]['genel_net_pozisyon']['miktar'];
    $pariteler[$anahtar]['kart_bakiye'] = $net_miktar;
    
    $brut_giris_miktar = $pariteler[$anahtar]['toplam_portfoy_giris']['miktar'];
    $brut_giris_maliyet = $pariteler[$anahtar]['toplam_portfoy_giris']['maliyet'];
    $pariteler[$anahtar]['ort_maliyet'] = ($brut_giris_miktar > 0) ? ($brut_giris_maliyet / $brut_giris_miktar) : 0;
    
    $bozma_kuru = (float)($live_kurlar[$veri['kur_key']] ?? 1);
    $anlik_eder = $net_miktar * $bozma_kuru;
    $pariteler[$anahtar]['anlik_eder'] = $anlik_eder;
    
    $net_bakiye_maliyeti = $net_miktar * $pariteler[$anahtar]['ort_maliyet'];
    $fark_tl = $anlik_eder - $net_bakiye_maliyeti;
    
    $pariteler[$anahtar]['fark_tl'] = $fark_tl;
    $pariteler[$anahtar]['fark_isaret'] = ($fark_tl >= 0) ? '+' : '';
    
    $anlik_banka_degeri_tl += $anlik_eder;
    $toplam_alis_maliyeti_tl += $net_bakiye_maliyeti;
}

// // 🔒 KÜÇÜK HARF ENTEGRASYON KÖPRÜSÜ (Geriye Dönük Uyumluluk)
// $pariteler['eur'] = &$pariteler['EUR']; $pariteler['usd'] = &$pariteler['USD']; $pariteler['xau'] = &$pariteler['XAU'];

// Üst kart bağlayıcıları
foreach (['EUR', 'USD', 'XAU'] as $ukod) {
    $pariteler[$ukod]['donem_alis']  = (float)$pariteler[$ukod]['cari_donem_giris']['maliyet'];
    $pariteler[$ukod]['guncel_veri'] = (float)$pariteler[$ukod]['genel_net_pozisyon']['maliyet'];
}

// =========================================================================
// --- BÖLÜM 4: TÜM EMTİALAR İÇİN KÜMÜLATİF KONSOLİDE MATRİSLER ---
// =========================================================================

// Her bir kırılımın kümülatif TL maliyet matrahlarını kuruşu kuruşuna topluyoruz (EUR + USD + XAU)
$konsolide_toplam_cikis_tl    = (float)($pariteler['EUR']['toplam_portfoy_cikis']['maliyet'] + $pariteler['USD']['toplam_portfoy_cikis']['maliyet'] + $pariteler['XAU']['toplam_portfoy_cikis']['maliyet']);
$konsolide_toplam_giris_tl    = (float)($pariteler['EUR']['toplam_portfoy_giris']['maliyet'] + $pariteler['USD']['toplam_portfoy_giris']['maliyet'] + $pariteler['XAU']['toplam_portfoy_giris']['maliyet']);
$konsolide_cari_donem_tl      = (float)($pariteler['EUR']['cari_donem_giris']['maliyet'] + $pariteler['USD']['cari_donem_giris']['maliyet'] + $pariteler['XAU']['cari_donem_giris']['maliyet']);
$konsolide_devreden_bakiye_tl = (float)($pariteler['EUR']['tarihsel_devir_pozisyon']['maliyet'] + $pariteler['USD']['tarihsel_devir_pozisyon']['maliyet'] + $pariteler['XAU']['tarihsel_devir_pozisyon']['maliyet']);
$konsolide_genel_durum_tl     = (float)($pariteler['EUR']['genel_net_pozisyon']['maliyet'] + $pariteler['USD']['genel_net_pozisyon']['maliyet'] + $pariteler['XAU']['genel_net_pozisyon']['maliyet']);

// 2026 yılı Toplam Yatırım Tutarı değişken eşitlemesi
$toplam_2026_donem_alis_maliyeti_tl = $konsolide_cari_donem_tl;

// Net Arbitraj ve Kar/Zarar Kararlılık Şeritleri
$arbitraj_fark_tl = $anlik_banka_degeri_tl - $toplam_alis_maliyeti_tl;
$fark_isaret      = ($arbitraj_fark_tl >= 0) ? '+' : '';
$fark_sinifi      = ($arbitraj_fark_tl >= 0) ? 'text-success' : 'text-danger';

// =========================================================================
// 🎯 YENİ ENTEGRASYON: ELDEKİ MEVCUDUN (GENEL DURUM) CANLI TASFİYE EDERİ
// =========================================================================
// Her paritenin eldeki net miktarını anlık banka bozma kuruyla çarpıp konsolide ediyoruz
$konsolide_genel_durum_tasfiye_tl = ($pariteler['EUR']['genel_net_pozisyon']['miktar'] * (float)($live_kurlar['EUR'] ?? 1)) + 
                                     ($pariteler['USD']['genel_net_pozisyon']['miktar'] * (float)($live_kurlar['USD'] ?? 1)) + 
                                     ($pariteler['XAU']['genel_net_pozisyon']['miktar'] * (float)($live_kurlar['XAU'] ?? 1));


$cc_last = $db->query("SELECT * FROM kredi_kartlari_last")->fetchAll(PDO::FETCH_ASSOC);
$kkart = [];
foreach ($cc_last as $kartname) {
    $k_key = ($kartname['kart_adi'] === 'vakifbank') ? 'vakifbank' : 'isbank';
    $kkart[$k_key] = $kartname;
}


// =========================================================================
// VIEW KATMANININ GÜVENLİ ÇAĞRILMASI
// =========================================================================
require admin_view("yatirim");
