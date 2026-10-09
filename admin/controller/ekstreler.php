<?php
// =========================================================================
// 💳 KART ANALİZ EKSTRE PARSER & HARCAMA YÖNETİM CONTROLLER KATMANI
// =========================================================================

if (!defined('BASEPATH') && !isset($db)) {
    die("Emniyet Bariyeri: Bu alana doğrudan erişim yetkiniz bulunmuyor.");
}

$autoload_path = dirname(__DIR__, 2) . '/vendor/autoload.php';
if (file_exists($autoload_path)) {
    require_once $autoload_path;
} else {
    die("❌ Kritik Hata: 'vendor/autoload.php' bulunamadı!");
}

if (session_status() === PHP_SESSION_NONE) { session_start(); }


// 1. DÖNEM VE ZAMAN YÖNETİMİ
if (!isset($_SESSION['aktif_donem']) || empty($_SESSION['aktif_donem'])) {
    $en_guncel_sorgu = $db->query("SELECT aktif_donem FROM butce_akis ORDER BY aktif_donem DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $_SESSION['aktif_donem'] = ($en_guncel_sorgu && !empty($en_guncel_sorgu['aktif_donem'])) ? $en_guncel_sorgu['aktif_donem'] : date('Y-m');
}

$aktif_donem = $_SESSION['aktif_donem'];

$ay_isimleri = [1=>'OCAK', 2=>'ŞUBAT', 3=>'MART', 4=>'NİSAN', 5=>'MAYIS', 6=>'HAZİRAN', 7=>'TEMMUZ', 8=>'AĞUSTOS', 9=>'EYLÜL', 10=>'EKİM', 11=>'KASIM', 12=>'ARALIK'];
list($aktif_yil, $aktif_ay_str) = explode('-', $aktif_donem);
$aktif_yil = trim($aktif_yil);
$aktif_ay_index = intval($aktif_ay_str);

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['ekstre_pdf'])) {
    
    $ham_kart_turu = filter_input(INPUT_POST, 'kart_turu', FILTER_SANITIZE_SPECIAL_CHARS);
    $kart_turu = strtolower(trim($ham_kart_turu ?? ''));
    
    if (preg_match('/isbank|işbank|maximum/i', $kart_turu)) {
        $kart_turu = 'isbank';
    } elseif (preg_match('/vakif|vakıf|world/i', $kart_turu)) {
        $kart_turu = 'vakifbank';
    }
    
    if (!in_array($kart_turu, ['isbank', 'vakifbank'])) {
        $error = "Emniyet Bariyeri: Geçersiz kredi kartı türü!";
    } elseif ($_FILES['ekstre_pdf']['error'] !== UPLOAD_ERR_OK) {
        $error = "Dosya yükleme hatası oluştu.";
    } else {
        
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf    = $parser->parseFile($_FILES['ekstre_pdf']['tmp_name']);
            $text   = $pdf->getText();

            // 🛡️ VAKIFBANK WINDOWS-1254 VE ISO-8859-9 DEŞİFRE MOTORU
            // Bozuk gelen Ý, Þ, Ð, Ý gibi karakterleri jilet gibi Türkçeye çevirir
            $bozuk_karakterler = ['Ý', 'Þ', 'Ð', 'Ý', 'Þ', 'Ý', 'ð', 'þ', 'ý'];
            $temiz_karakterler = ['İ', 'Ş', 'Ğ', 'İ', 'Ş', 'İ', 'ğ', 'ş', 'i'];
            $text = str_replace($bozuk_karakterler, $temiz_karakterler, $text);

            // Eğer kütüphane metni UTF-8 haricinde algılatıyorsa zorla
            if (!mb_check_encoding($text, 'UTF-8')) {
                $text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-9');
            }

            
            // Satırları ham olarak ayrıştırıyoruz
            $satirlar = explode("\n", $text);



                
                // 🎯 TÜM METİN ÜZERİNDEN ESNEK HESAP KESİM TARİHİ YAKALAMA
                $hesaplanan_donem = $aktif_donem;
                //$hesaplanan_donem = 2025-01;

                // \s* kullanarak satır başı, alt satıra geçme (\n) ve boşlukları tamamen esnetiyoruz
                if (preg_match('/Hesap\s*Kesim\s*Tarihi\s*[:\s]*(\d{2})[.\/-](\d{2})[.\/-](\d{4})/i', $text, $kesim_matches)) {
                    $hesaplanan_donem = "{$kesim_matches[3]}-{$kesim_matches[2]}"; // YYYY-MM formatı
                } 
                // Fallback: Eğer Hesap Kesim Tarihi bulunamazsa alt satırlara bölünmüş Son Ödeme Tarihi'ni de tüm metinden aratabilirsiniz
                else if (preg_match('/Son\s*Ödeme\s*Tarihi\s*[:\s]*(\d{2})[.\/-](\d{2})[.\/-](\d{4})/i', $text, $son_matches)) {
                    $hesaplanan_donem = "{$son_matches[3]}-{$son_matches[2]}";
                }


             

           

            // 🏢 ESKİ TABLOLAR SİLİNDİ. SADECE YENİ FİRMALAR TABLOSUNU HAFIZAYA ALIYORUZ
            // Kelime uzunluğuna göre sıralıyoruz ki "MİGROS SANAL MARKET", "MİGROS"tan önce eşleşsin.
            $kural_sorgu = $db->query("
                SELECT `aranacak_kelime`, `isletme_adi`, `kategori_id` 
                FROM `firmalar` 
                ORDER BY LENGTH(`aranacak_kelime`) DESC
            ");
            //$db_firmalari = $firmalar_sorgu->fetchAll(PDO::FETCH_ASSOC);
            $db_kurallari = $kural_sorgu->fetchAll(PDO::FETCH_ASSOC);

            $db->beginTransaction();
            $eklenen_kayit_sayisi = 0;
            $toplam_okunan_satir = 0;

           


           
  
            // =========================================================================
            // 🔍 YOL HARİTASI A: İŞ BANKASI MOTORU (ÜRETİM MODU - CANLI DB KAYDI)
            // =========================================================================

            // insert
            if ($kart_turu === 'isbank') {
    
                // 🏢 FİRMALAR TABLOSUNU HAFIZAYA (MEMORY) ALMA (KELİME UZUNLUĞUNA GÖRE SIRALI)
                $firmalar_sorgu = $db->query("
                    SELECT `aranacak_kelime`, `isletme_adi`, `kategori_id` 
                    FROM `firmalar` 
                    ORDER BY LENGTH(`aranacak_kelime`) DESC
                ");
                $db_firmalari = $firmalar_sorgu->fetchAll(PDO::FETCH_ASSOC);

                foreach ($satirlar as $satir) {
                    $satir = trim($satir);
                    if (empty($satir)) continue;
                    $toplam_okunan_satir++;

                    // 🛡️ 1. ADIM: Kesinlikle Kaydedilmeyecek Gürültü Satırları Bariyeri
                    $engellenen_kaliplar = [
                        'BİR\s*ÖNCEKİ\s*HESAP\s*ÖZETİ\s*BAKİYENİZ',
                        'HESAPTAN\s*AKTARIM',
                        'İNTERAKTİF',
                        'HEDİYE\s*BANKAM\s*BENİMLE\s*MAXIPUAN',
                        'MAXIPUAN\s*PUAN\s*YÜKLEME',
                        'MAXİPUAN\s*PUAN\s*YÜKLEME',
                        'ÖDEMELER\s*IÇIN\s*TESEKKÜR\s*EDERIZ',
                        'ÖDEMELERİNİZ\s*İÇİN\s*TEŞEKKÜR\s*EDERİZ',
                        'AYLIK\s*TAKSİTLİ\s*BORÇ\s*TOPLAMI',
                        'KALAN\s*TAKSİTLİ\s*BORÇ\s*TOPLAMI',
                        'MAXIPUAN',
                        'MAXİPUAN'
                    ];
                    
                    $engelle = false;
                    foreach ($engellenen_kaliplar as $kalip) {
                        if (preg_match('/' . $kalip . '/i', $satir)) {
                            $engelle = true;
                            break;
                        }
                    }
                    if ($engelle) continue;

                    // 🛡️ 2. ADIM: Satır İçi Puan ve İlave Gürültülerini Budama
                    $satir = preg_replace('/-?MAXİPUAN\s*İLAVE\s*:\s*[0-9.,]+/i', '', $satir);
                    $satir = preg_replace('/-?MAXIPUAN\s*İLAVE\s*:\s*[0-9.,]+/i', '', $satir);
                    $satir = trim($satir);

                    // 🎯 3. ADIM: Ham Veriye Özel Milimetrik Regex Dünyası
                    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})[\s\t]*(.+?)[\s\t]+([0-9]{1,3}(?:\.[0-9]{3})*,[0-9]{2}|-[0-9]{1,3}(?:\.[0-9]{3})*,[0-9]{2}|[0-9]+,[0-9]{2}|-[0-9]+,[0-9]{2})(.*)$/i', $satir, $matches)) {
                        
                        $gun  = $matches[1];
                        $ay   = $matches[2];
                        $yil  = $matches[3];
                        $islem_tarihi = $yil . '-' . $ay . '-' . $gun; 

                        $aciklama     = trim($matches[4]);
                        $ham_tutar    = trim($matches[5]);
                        $kalan_kuyruk = isset($matches[6]) ? trim($matches[6]) : '';
                        
                        if (strpos($ham_tutar, '-') !== false) continue;

                        // 💰 TUTAR TAMİR LABORATUVARI
                        $temiz_tutar = str_replace('.', '', $ham_tutar); 
                        $temiz_tutar = str_replace(',', '.', $temiz_tutar); 
                        $tutar = floatval($temiz_tutar);
                        
                        if ($tutar <= 0) continue;

                        // 📊 TAKSİT BİLGİSİ AYIKLAMA MOTORU
                        $mevcut_taksit = 1; 
                        $toplam_taksit = 1;
                        if (!empty($kalan_kuyruk) && preg_match('/(\d+)\/(\d+)\s*taksidi/i', $kalan_kuyruk, $taksit_matches)) {
                            $mevcut_taksit = intval($taksit_matches[1]);
                            $toplam_taksit = intval($taksit_matches[2]);
                        }

                        // 🎯 4. ADIM: Akıllı İşletme Adı & Yeni Firmalar Kategori Motoru
                        $belirlenen_kategori_id = 0;
                        $isletme_adi = $aciklama; 

                        foreach ($db_firmalari as $firma) {
                            $aranacak = trim($firma['aranacak_kelime']);
                            $aranacak_onay = str_replace(['I', 'İ', 'Ğ', 'Ş', 'Ç'], ['[Iİıi]', '[Iİıi]', '[ĞĞğg]', '[ŞŞsş]', '[ÇÇcc]'], $aranacak);
                            $temiz_aciklama_vb = str_replace([' ', '/', '.', '-'], '', $aciklama);
                            
                            if (stripos($aciklama, $aranacak) !== false || 
                                preg_match('/' . preg_quote($aranacak_onay, '/') . '/i', $aciklama) ||
                                stripos($temiz_aciklama_vb, $aranacak) !== false) {
                                
                                // 🎯 Yeni tekil modele tam uyum
                                $belirlenen_kategori_id = (int)$firma['kategori_id'];
                                $isletme_adi = $firma['isletme_adi']; 
                                break;
                            }
                        }

                        // 💾 5. ADIM: Veritabanına Canlı Kayıt İstasyonu (İş Bankası)
                        try {
                            $ins = $db->prepare("INSERT IGNORE INTO harcama_gecmisi_arsiv 
                                (donem, kart_turu, islem_tarihi, isletme_adi, aciklama, kategori_id, tutar, toplam_taksit, mevcut_taksit, eklenme_tarihi) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                            
                            $ins->execute([
                                $hesaplanan_donem, 
                                'isbank', 
                                $islem_tarihi, 
                                $isletme_adi, 
                                $aciklama, 
                                $belirlenen_kategori_id, 
                                $tutar, 
                                $toplam_taksit, 
                                $mevcut_taksit
                            ]);

                            if ($ins->rowCount() > 0) { 
                                $eklenen_kayit_sayisi++; 
                            }
                        } catch (PDOException $e) {
                            continue;
                        }
                    }
                }
            }

            //json
            // if ($kart_turu === 'isbank') {
            //     $json_cikti_listesi = [];
                
            //     // 🏢 FİRMALAR TABLOSUNU HAFIZAYA (MEMORY) ALMA (KELİME UZUNLUĞUNA GÖRE SIRALI)
            //     $firmalar_sorgu = $db->query("
            //         SELECT `aranacak_kelime`, `isletme_adi`, `kategori_id` 
            //         FROM `firmalar` 
            //         ORDER BY LENGTH(`aranacak_kelime`) DESC
            //     ");
            //     $db_firmalari = $firmalar_sorgu->fetchAll(PDO::FETCH_ASSOC);

            //     foreach ($satirlar as $satir) {
            //         $satir = trim($satir);
            //         //if (empty($satir)) continue;
            //         $toplam_okunan_satir++;

                    

            //         // 🛡️ 1. ADIM: Kesinlikle Kaydedilmeyecek Gürültü Satırları Bariyeri
            //         $engellenen_kaliplar = [
            //             'BİR\s*ÖNCEKİ\s*HESAP\s*ÖZETİ\s*BAKİYENİZ',
            //             'HESAPTAN\s*AKTARIM',
            //             'İNTERAKTİF',
            //             'HEDİYE\s*BANKAM\s*BENİMLE\s*MAXIPUAN',
            //             'MAXIPUAN\s*PUAN\s*YÜKLEME',
            //             'MAXİPUAN\s*PUAN\s*YÜKLEME',
            //             'ÖDEMELER\s*IÇIN\s*TESEKKÜR\s*EDERIZ',
            //             'ÖDEMELERİNİZ\s*İÇİN\s*TEŞEKKÜR\s*EDERİZ',
            //             'AYLIK\s*TAKSİTLİ\s*BORÇ\s*TOPLAMI',
            //             'KALAN\s*TAKSİTLİ\s*BORÇ\s*TOPLAMI',
            //             'MAXIPUAN',
            //             'MAXİPUAN'
            //         ];
                    
            //         $engelle = false;
            //         foreach ($engellenen_kaliplar as $kalip) {
            //             if (preg_match('/' . $kalip . '/i', $satir)) {
            //                 $engelle = true;
            //                 break;
            //             }
            //         }
            //         if ($engelle) continue;

            //         // 🛡️ 2. ADIM: Satır İçi Puan ve İlave Gürültülerini Budama
            //         $satir = preg_replace('/-?MAXİPUAN\s*İLAVE\s*:\s*[0-9.,]+/i', '', $satir);
            //         $satir = preg_replace('/-?MAXIPUAN\s*İLAVE\s*:\s*[0-9.,]+/i', '', $satir);
            //         $satir = trim($satir);

            //         // 🎯 3. ADIM: Ham Veriye Özel Milimetrik Regex Dünyası
            //         if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})[\s\t]*(.+?)[\s\t]+([0-9]{1,3}(?:\.[0-9]{3})*,[0-9]{2}|-[0-9]{1,3}(?:\.[0-9]{3})*,[0-9]{2}|[0-9]+,[0-9]{2}|-[0-9]+,[0-9]{2})(.*)$/i', $satir, $matches)) {
                        
            //             $gun  = $matches[1];
            //             $ay   = $matches[2];
            //             $yil  = $matches[3];
            //             $islem_tarihi = $yil . '-' . $ay . '-' . $gun; 

            //             $aciklama     = trim($matches[4]);
            //             $ham_tutar    = trim($matches[5]);
            //             $kalan_kuyruk = isset($matches[6]) ? trim($matches[6]) : '';
                        
            //             if (strpos($ham_tutar, '-') !== false) continue;

            //             // 💰 TUTAR TAMİR LABORATUVARI
            //             $temiz_tutar = str_replace('.', '', $ham_tutar); 
            //             $temiz_tutar = str_replace(',', '.', $temiz_tutar); 
            //             $tutar = floatval($temiz_tutar);
                        
            //             if ($tutar <= 0) continue;

            //             // 📊 TAKSİT BİLGİSİ AYIKLAMA MOTORU
            //             $mevcut_taksit = 1; 
            //             $toplam_taksit = 1;
            //             if (!empty($kalan_kuyruk) && preg_match('/(\d+)\/(\d+)\s*taksidi/i', $kalan_kuyruk, $taksit_matches)) {
            //                 $mevcut_taksit = intval($taksit_matches[1]);
            //                 $toplam_taksit = intval($taksit_matches[2]);
            //             }

            //             // 🎯 4. ADIM: Akıllı İşletme Adı & Yeni Firmalar Kategori Motoru
            //             $belirlenen_kategori_id = 0;
            //             $isletme_adi = $aciklama; 

            //             foreach ($db_firmalari as $firma) {
            //                 $aranacak = trim($firma['aranacak_kelime']);
            //                 $aranacak_onay = str_replace(['I', 'İ', 'Ğ', 'Ş', 'Ç'], ['[Iİıi]', '[Iİıi]', '[ĞĞğg]', '[ŞŞsş]', '[ÇÇcc]'], $aranacak);
            //                 $temiz_aciklama_vb = str_replace([' ', '/', '.', '-'], '', $aciklama);
                            
            //                 if (stripos($aciklama, $aranacak) !== false || 
            //                     preg_match('/' . preg_quote($aranacak_onay, '/') . '/i', $aciklama) ||
            //                     stripos($temiz_aciklama_vb, $aranacak) !== false) {
                                
            //                     $belirlenen_kategori_id = (int)$firma['kategori_id'];
            //                     $isletme_adi = $firma['isletme_adi']; 
            //                     break;
            //                 }
            //             }

            //             // 💾 5. ADIM: JSON Çıktısı İçin Diziyi Dolduruyoruz (DB Kaydı Yok)
            //             $json_cikti_listesi[] = [
            //                 'donem'          => $hesaplanan_donem,
            //                 'kart_turu'      => 'isbank',
            //                 'islem_tarihi'   => $islem_tarihi,
            //                 'isletme_adi'    => $isletme_adi,
            //                 'aciklama'       => $aciklama,
            //                 'kategori_id'    => $belirlenen_kategori_id,
            //                 'tutar'          => $tutar,
            //                 'toplam_taksit'  => $toplam_taksit,
            //                 'mevcut_taksit'  => $mevcut_taksit,
            //                 'eklenme_tarihi' => date('Y-m-d H:i:s')
            //             ];
            //         }
            //     }

            //     // 💣 SAF JSON ÇIKTIYI BAS VE SÜRECİ SONLANDIR
            //     header('Content-Type: application/json; charset=utf-8');
            //     echo json_encode([
            //         'toplam_bulunan_isbank_harcama' => count($json_cikti_listesi),
            //         'harcamalar' => $json_cikti_listesi
            //     ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            //     exit;
            // }

            // if ($kart_turu === 'isbank') {
            //     $json_cikti_listesi = [];

            //     // 🏢 FİRMALAR TABLOSUNU HAFIZAYA (MEMORY) ALMA
            //     // Kelime uzunluğuna göre sıralı
            //     $firmalar_sorgu = $db->query("
            //         SELECT `aranacak_kelime`, `isletme_adi`, `kategori_id` 
            //         FROM `firmalar` 
            //         ORDER BY LENGTH(`aranacak_kelime`) DESC
            //     ");

            //     $db_firmalari = $firmalar_sorgu->fetchAll(PDO::FETCH_ASSOC);

            //     // 🛠️ UTF-8 KARAKTER ONARIM LABORATUVARI
            //     // İş Bankası bozuk çıktıları için özel
            //     $bozuk_isbank = [
            //         'Ä°',
            //         'Å ',
            //         'Ä±',
            //         'Ã‡',
            //         'Ã§',
            //         'Ã–',
            //         'Ã¶',
            //         'Ãœ',
            //         'Ã¼',
            //         'ÅŸ',
            //         'ÄŸ',
            //         'Ã–N',
            //         'Ã–nem',
            //         'MÃ¼',
            //         'ÅŸt',
            //         'AsgariÃ¶',
            //         'Ã¶den'
            //     ];

            //     $temiz_isbank = [
            //         'İ',
            //         'Ş',
            //         'ı',
            //         'Ç',
            //         'ç',
            //         'Ö',
            //         'ö',
            //         'Ü',
            //         'ü',
            //         'ş',
            //         'ğ',
            //         'ÖN',
            //         'Önem',
            //         'Mü',
            //         'şt',
            //         'Asgariö',
            //         'öden'
            //     ];

            //     $text = str_replace(
            //         $bozuk_isbank,
            //         $temiz_isbank,
            //         $text
            //     );

            //     // 🎯 ESNEK HESAP KESİM TARİHİ MOTORU
            //     // Tüm TEXT üzerinden - satır bağımsız
            //     $hesaplanan_donem = $aktif_donem;

            //     if (
            //         preg_match(
            //             '/Hesap\s*Kesim\s*Tarihi\s*[:\s]*(\d{2})[.\/-](\d{2})[.\/-](\d{4})/iu',
            //             $text,
            //             $kesim_matches
            //         )
            //     ) {
            //         $hesaplanan_donem =
            //             "{$kesim_matches[3]}-{$kesim_matches[2]}";

            //     } elseif (
            //         preg_match(
            //             '/Son\s*Ödeme\s*Tarihi\s*[:\s]*(\d{2})[.\/-](\d{2})[.\/-](\d{4})/iu',
            //             $text,
            //             $son_matches
            //         )
            //     ) {
            //         $hesaplanan_donem =
            //             "{$son_matches[3]}-{$son_matches[2]}";
            //     }

            //     // Satırları onarılmış metinden tekrar oluşturuyoruz
            //     $satirlar = explode("\n", $text);

            //     // Tarih üst satırda kaldığında alt satıra aktarmak
            //     // için hafıza hücresi
            //     $gecici_tarih = null;

            //     foreach ($satirlar as $satir) {

            //         $satir = trim($satir);

            //         if (empty($satir)) {
            //             continue;
            //         }

            //         // 🛡️ 1. ADIM:
            //         // Kesinlikle kaydedilmeyecek gürültü satırları bariyeri
            //         $engellenen_kaliplar = [
            //             'BİR\s*ÖNCEKİ\s*HESAP\s*ÖZETİ\s*BAKİYENİZ',
            //             'HESAPTAN\s*AKTARIM',
            //             'İNTERAKTİF',
            //             'HEDİYE\s*BANKAM\s*BENİMLE\s*MAXIPUAN',
            //             'BANKAMBENIMLEHARCAMAMAXIPUAN',
            //             'MAXIPUAN\s*PUAN\s*YÜKLEME',
            //             'MAXI\s*PUAN',
            //             'MAXİ\s*PUAN',
            //             'ÖDEMELER\s*IÇIN\s*TESEKKÜR\s*EDERIZ',
            //             'ÖDEMELERİNİZ\s*İÇİN\s*TEŞEKKÜR\s*EDERİZ',
            //             'AYLIK\s*TAKSİTLİ',
            //             'KALAN\s*TAKSİTLİ',
            //             'Hesap\s*Özeti\s*Borcu',
            //             'Baskı\s*tarihi'
            //         ];

            //         $engelle = false;

            //         foreach ($engellenen_kaliplar as $kalip) {

            //             if (preg_match('/' . $kalip . '/iu', $satir)) {
            //                 $engelle = true;
            //                 break;
            //             }
            //         }

            //         if ($engelle) {
            //             continue;
            //         }

            //         // 🛡️ 2. ADIM:
            //         // Sadece tarih içeren satırı yakalama
            //         // Örn: 11/12/2024
            //         if (
            //             preg_match(
            //                 '/^(\d{2})\/(\d{2})\/(\d{4})$/',
            //                 $satir,
            //                 $tarih_matches
            //             )
            //         ) {
            //             $gecici_tarih =
            //                 "{$tarih_matches[3]}-" .
            //                 "{$tarih_matches[2]}-" .
            //                 "{$tarih_matches[1]}";

            //             // Bir sonraki satırdaki harcama detayını bekle
            //             continue;
            //         }

            //         // 🎯 3. ADIM:
            //         // Harcama detayı ve tutar satırını çözme motoru
            //         if (
            //             $gecici_tarih &&
            //             preg_match(
            //                 '/^(.+?)\s*([0-9]{1,3}(?:\.[0-9]{3})*,[0-9]{2}|-[0-9]{1,3}(?:\.[0-9]{3})*,[0-9]{2}|[0-9]+,[0-9]{2}|-[0-9]+,[0-9]{2})(.*)$/ui',
            //                 $satir,
            //                 $matches
            //             )
            //         ) {

            //             $islem_tarihi = $gecici_tarih;

            //             $aciklama =
            //                 trim($matches[1]);

            //             $ham_tutar =
            //                 trim($matches[2]);

            //             $kalan_kuyruk =
            //                 isset($matches[3])
            //                     ? trim($matches[3])
            //                     : '';

            //             // İptal veya Alacak hareketlerini
            //             // (eksi bakiyeleri) atla
            //             if (
            //                 strpos($ham_tutar, '-') !== false ||
            //                 strpos($aciklama, 'İPTAL') !== false
            //             ) {
            //                 $gecici_tarih = null;
            //                 continue;
            //             }

            //             // 💰 TUTAR TAMİR LABORATUVARI
            //             $temiz_tutar =
            //                 str_replace('.', '', $ham_tutar);

            //             $temiz_tutar =
            //                 str_replace(',', '.', $temiz_tutar);

            //             $tutar =
            //                 floatval($temiz_tutar);

            //             if ($tutar <= 0) {
            //                 $gecici_tarih = null;
            //                 continue;
            //             }

            //             // 📊 TAKSİT BİLGİSİ AYIKLAMA MOTORU
            //             // Kuyrukta veya açıklamada arar
            //             $mevcut_taksit = 1;
            //             $toplam_taksit = 1;

            //             if (
            //                 preg_match(
            //                     '/(\d+)\/(\d+)\s*taksidi/ui',
            //                     $kalan_kuyruk . ' ' . $aciklama,
            //                     $taksit_matches
            //                 )
            //             ) {
            //                 $mevcut_taksit =
            //                     intval($taksit_matches[1]);

            //                 $toplam_taksit =
            //                     intval($taksit_matches[2]);
            //             }

            //             // 🎯 4. ADIM:
            //             // Akıllı işletme adı & yeni firmalar kategori motoru
            //             $belirlenen_kategori_id = 0;

            //             $isletme_adi =
            //                 $aciklama;

            //             foreach ($db_firmalari as $firma) {

            //                 $aranacak =
            //                     trim($firma['aranacak_kelime']);

            //                 $aranacak_onay =
            //                     str_replace(
            //                         [
            //                             'I',
            //                             'İ',
            //                             'Ğ',
            //                             'Ş',
            //                             'Ç'
            //                         ],
            //                         [
            //                             '[Iİıi]',
            //                             '[Iİıi]',
            //                             '[ĞğGg]',
            //                             '[ŞşSs]',
            //                             '[ÇçCc]'
            //                         ],
            //                         $aranacak
            //                     );

            //                 $temiz_aciklama_vb =
            //                     str_replace(
            //                         [
            //                             ' ',
            //                             '/',
            //                             '.',
            //                             '-'
            //                         ],
            //                         '',
            //                         $aciklama
            //                     );

            //                 if (
            //                     stripos(
            //                         $aciklama,
            //                         $aranacak
            //                     ) !== false ||

            //                     preg_match(
            //                         '/' .
            //                         preg_quote(
            //                             $aranacak_onay,
            //                             '/'
            //                         ) .
            //                         '/iu',
            //                         $aciklama
            //                     ) ||

            //                     stripos(
            //                         $temiz_aciklama_vb,
            //                         $aranacak
            //                     ) !== false
            //                 ) {

            //                     $belirlenen_kategori_id =
            //                         (int)$firma['kategori_id'];

            //                     $isletme_adi =
            //                         $firma['isletme_adi'];

            //                     break;
            //                 }
            //             }

            //             // 💾 5. ADIM:
            //             // JSON çıktısı için diziyi dolduruyoruz
            //             $json_cikti_listesi[] = [
            //                 'donem' =>
            //                     $hesaplanan_donem,

            //                 'kart_turu' =>
            //                     'isbank',

            //                 'islem_tarihi' =>
            //                     $islem_tarihi,

            //                 'isletme_adi' =>
            //                     $isletme_adi,

            //                 'aciklama' =>
            //                     $aciklama,

            //                 'kategori_id' =>
            //                     $belirlenen_kategori_id,

            //                 'tutar' =>
            //                     $tutar,

            //                 'toplam_taksit' =>
            //                     $toplam_taksit,

            //                 'mevcut_taksit' =>
            //                     $mevcut_taksit,

            //                 'eklenme_tarihi' =>
            //                     date('Y-m-d H:i:s')
            //             ];

            //             // İşlem bitti, sonraki satırlar için
            //             // tarih hafızasını temizle
            //             $gecici_tarih = null;
            //         }
            //     }

            //     // 💣 SAF JSON ÇIKTIYI BAS VE SÜRECİ SONLANDIR
            //     header(
            //         'Content-Type: application/json; charset=utf-8'
            //     );

            //     echo json_encode(
            //         [
            //             'toplam_bulunan_isbank_harcama' =>
            //                 count($json_cikti_listesi),

            //             'harcamalar' =>
            //                 $json_cikti_listesi
            //         ],
            //         JSON_PRETTY_PRINT |
            //         JSON_UNESCAPED_UNICODE
            //     );

            //     exit;
            // }



           
            // // =========================================================================
            // // 🔍 YOL HARİTASI B: VAKIFBANK GELİŞMİŞ HÜCRESEL REGEX MOTORU (TUTAR TAMİRLİ)
            // // =========================================================================

            //insert
            if ($kart_turu === 'vakifbank') {
                $temiz_vakif_satirlari = [];
                $gecici_satir = "";
                
                // 🏢 FİRMALAR TABLOSUNU HAFIZAYA (MEMORY) ALMA (KELİME UZUNLUĞUNA GÖRE SIRALI)
                $firmalar_sorgu = $db->query("
                    SELECT `aranacak_kelime`, `isletme_adi`, `kategori_id` 
                    FROM `firmalar` 
                    ORDER BY LENGTH(`aranacak_kelime`) DESC
                ");
                $db_firmalari = $firmalar_sorgu->fetchAll(PDO::FETCH_ASSOC);
                
                // 🔄 1. ADIM: VakıfBank'ın parçalı dikey satırlarını doğrusal tek satıra diziyoruz
                foreach ($satirlar as $s) {
                    $s = trim($s);
                    if (empty($s)) continue;
                    if (preg_match('/^\d{2}\.\d{2}\.\d{4}/', $s)) {
                        if (!empty($gecici_satir)) { $temiz_vakif_satirlari[] = $gecici_satir; }
                        $gecici_satir = $s;
                    } else {
                        $gecici_satir .= " " . $s;
                    }
                }
                if (!empty($gecici_satir)) { $temiz_vakif_satirlari[] = $gecici_satir; }

                // 🔄 2. ADIM: Birleştirilmiş satırları tek tek işlem laboratuvarına alıyoruz
                foreach ($temiz_vakif_satirlari as $satir) {
                    $toplam_okunan_satir++;
                    
                    // 🛡️ Mutlak Engellenecek Gürültü Satırları Bariyeri
                    $engellenen_kaliplar = [
                        'ÖNCEKİ\s*DÖNEM\s*HESAP\s*ÖZETİ\s*BAKİYENİZ',
                        'ÖDEMENİZ\s*İÇİN\s*TEŞEKKÜR\s*EDERİZ',
                        'ÖDEMENIZ\s*IÇIN\s*TESEKKÜRLER',
                        'Puan\s+Kullanımı',
                        'Puan\s+Kullanımı',
                        'TEŞEKKÜRLER',
                        'AKTARIM',
                        'İADE',
                        'IADE'
                    ];
                    
                    $engelle = false;
                    foreach ($engellenen_kaliplar as $kalip) {
                        if (preg_match('/' . $kalip . '/i', $satir)) {
                            $engelle = true;
                            break;
                        }
                    }
                    if ($engelle) continue;

                    // Tarih kalıbını doğrula ve içeriği süz
                    if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})\s*(.+)$/i', $satir, $tarih_matches)) {
                        $islem_gun    = $tarih_matches[1];
                        $islem_ay     = $tarih_matches[2];
                        $islem_yil    = $tarih_matches[3];
                        $kalan_metin  = trim($tarih_matches[4]);
                        $islem_tarihi = $islem_yil . '-' . $islem_ay . '-' . $islem_gun; 

                        // Taksit Bilgisi Ayıklama Motoru
                        $mevcut_taksit = 1; $toplam_taksit = 1;
                        if (preg_match('/(\d+)\s*[\.\/]\s*Taksit/i', $kalan_metin, $taksit_yazi_matches)) {
                            $mevcut_taksit = intval($taksit_yazi_matches[1]);
                            $toplam_taksit = $mevcut_taksit;
                            $kalan_metin = trim(str_replace($taksit_yazi_matches[0], '', $kalan_metin));
                        }

                        // Satır sonundaki çarpımlı taksit/worldpuan gürültülerini temizle
                        $kalan_metin = preg_replace('/\s*\d+x[0-9.,]+/i', '', $kalan_metin);

                        // 💰 TUTAR TAMİR REFORMA
                        if (preg_match('/([0-9]{1,3}(?:,[0-9]{3})*\.[0-9]{2})|([0-9]{1,3}(?:\.[0-9]{3})*,[0-9]{2})|([0-9]{1,6}\.[0-9]{2})/', $kalan_metin, $tutar_matches)) {
                            
                            $ham_tutar = $tutar_matches[0];
                            
                            if (strpos($ham_tutar, ',') !== false && strpos($ham_tutar, '.') !== false) {
                                $tutar = floatval(str_replace(',', '', $ham_tutar));
                            } else {
                                if (strpos($ham_tutar, ',') !== false && strpos($ham_tutar, '.') === false) {
                                    $tutar = floatval(str_replace(',', '.', str_replace('.', '', $ham_tutar)));
                                } else {
                                    $tutar = floatval($ham_tutar);
                                }
                            }
                            
                            if ($tutar <= 0) continue;

                            // Tutarın sağında kalan ek tekil taksit sayısını süz
                            $artık_metin = trim(str_replace($ham_tutar, '', $kalan_metin));
                            if (preg_match('/(?:\s+|\t)(\d+)\s*$/', $artık_metin, $taksit_num_matches)) {
                                $toplam_taksit = intval($taksit_num_matches[1]);
                                $artık_metin = trim(str_replace($taksit_num_matches[0], '', $artık_metin));
                            }

                            // 🛡️ REFORM: Sayfa gürültülerini jilet gibi temizleme
                            $aciklama = $artık_metin;
                            $aciklama = preg_replace('/HESAP\s+ÖZETİ.*/i', '', $aciklama); 
                            $aciklama = preg_replace('/Toplam\s+Harcama\s+Tutari.*/i', '', $aciklama); 
                            $aciklama = preg_replace('/\d{4}\s*\**\s*\d{4}.*/i', '', $aciklama); 
                            $aciklama = preg_replace('/Sayfa\s+\d+.*/i', '', $aciklama); 
                            $aciklama = preg_replace('/Kredi\s+Karti\s+Hesap.*/i', '', $aciklama);
                            $aciklama = trim($aciklama);
                            
                            if (empty($aciklama) || preg_match('/ÖNCEKİ\s+DÖNEM/i', $aciklama)) continue;

                            // 🎯 3. ADIM: Yeni Firmalar Tablosu Odaklı İşletme & Kategori Motoru
                            $belirlenen_kategori_id = 0; 
                            $isletme_adi = $aciklama; 

                            foreach ($db_firmalari as $firma) {
                                $aranacak = trim($firma['aranacak_kelime']);
                                $aranacak_onay = str_replace(['I', 'İ', 'Ğ', 'Ş', 'Ç'], ['[Iİıi]', '[Iİıi]', '[ĞĞğg]', '[ŞŞsş]', '[ÇÇcc]'], $aranacak);
                                $temiz_aciklama_vb = str_replace([' ', '/', '.', '-'], '', $aciklama);
                                
                                if (stripos($aciklama, $aranacak) !== false || 
                                    preg_match('/' . preg_quote($aranacak_onay, '/') . '/i', $aciklama) ||
                                    stripos($temiz_aciklama_vb, $aranacak) !== false) {
                                    
                                    $belirlenen_kategori_id = (int)$firma['kategori_id'];
                                    $isletme_adi = $firma['isletme_adi']; 
                                    break;
                                }
                            }

                            // 💾 4. ADIM: Veritabanına Canlı Kayıt İstasyonu (VakıfBank)
                            try {
                                $ins = $db->prepare("INSERT IGNORE INTO harcama_gecmisi 
                                    (donem, kart_turu, islem_tarihi, isletme_adi, aciklama, kategori_id, tutar, toplam_taksit, mevcut_taksit, eklenme_tarihi) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                                
                                $ins->execute([
                                    $hesaplanan_donem, 
                                    'vakifbank', 
                                    $islem_tarihi, 
                                    $isletme_adi, 
                                    $aciklama, 
                                    $belirlenen_kategori_id, 
                                    $tutar, 
                                    $toplam_taksit, 
                                    $mevcut_taksit
                                ]);

                                if ($ins->rowCount() > 0) { 
                                    $eklenen_kayit_sayisi++; 
                                }
                            } catch (PDOException $e) {
                                continue;
                            }
                        }
                    }
                }
            }

            // // json
            // if ($kart_turu === 'vakifbank') {
            //     $temiz_vakif_satirlari = [];
            //     $gecici_satir = "";
            //     $json_cikti_listesi = [];
                
            //     // 🏢 1. ADIM (ÖNCESİ): Yeni firmalar tablosunu hafızaya alıyoruz (Kelime uzunluğuna göre sıralı)
            //     $firmalar_sorgu = $db->query("
            //         SELECT `aranacak_kelime`, `isletme_adi`, `kategori_id` 
            //         FROM `firmalar` 
            //         ORDER BY LENGTH(`aranacak_kelime`) DESC
            //     ");
            //     $db_firmalari = $firmalar_sorgu->fetchAll(PDO::FETCH_ASSOC);
                
            //     // 🔄 1. ADIM: VakıfBank'ın parçalı dikey satırlarını doğrusal tek satıra diziyoruz
            //     foreach ($satirlar as $s) {
            //         $s = trim($s);
            //         if (empty($s)) continue;
            //         if (preg_match('/^\d{2}\.\d{2}\.\d{4}/', $s)) {
            //             if (!empty($gecici_satir)) { $temiz_vakif_satirlari[] = $gecici_satir; }
            //             $gecici_satir = $s;
            //         } else {
            //             $gecici_satir .= " " . $s;
            //         }
            //     }
            //     if (!empty($gecici_satir)) { $temiz_vakif_satirlari[] = $gecici_satir; }

            //     // 🔄 2. ADIM: Birleştirilmiş satırları tek tek işlem laboratuvarına alıyoruz
            //     foreach ($temiz_vakif_satirlari as $satir) {
                    
            //         // 🛡️ Mutlak Engellenecek Gürültü Satırları Bariyeri (Boşluk ve TR Karakter Esnek)
            //         $engellenen_kaliplar = [
            //             'ÖNCEKİ\s*DÖNEM\s*HESAP\s*ÖZETİ\s*BAKİYENİZ',
            //             'ÖDEMENİZ\s*İÇİN\s*TEŞEKKÜR\s*EDERİZ',
            //             'ÖDEMENIZ\s*IÇIN\s*TESEKKÜRLER',
            //             'Puan\s+Kullanımı',
            //             'Puan\s+Kullanimi',
            //             'TEŞEKKÜRLER',
            //             'AKTARIM',
            //             'İADE',
            //             'IADE'
            //         ];
                    
            //         $engelle = false;
            //         foreach ($engellenen_kaliplar as $kalip) {
            //             if (preg_match('/' . $kalip . '/i', $satir)) {
            //                 $engelle = true;
            //                 break;
            //             }
            //         }
            //         if ($engelle) continue;

           
            //         // Tarih kalıbını doğrula ve içeriği süz
            //         if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})\s*(.+)$/i', $satir, $tarih_matches)) {
            //             $islem_gun    = $tarih_matches[1];
            //             $islem_ay     = $tarih_matches[2];
            //             $islem_yil    = $tarih_matches[3];
            //             $kalan_metin  = trim($tarih_matches[4]);
            //             $islem_tarihi = $islem_yil . '-' . $islem_ay . '-' . $islem_gun; 

            //             // Taksit Bilgisi Ayıklama Motoru
            //             $mevcut_taksit = 1; $toplam_taksit = 1;
            //             if (preg_match('/(\d+)\s*[\.\/]\s*Taksit/i', $kalan_metin, $taksit_yazi_matches)) {
            //                 $mevcut_taksit = intval($taksit_yazi_matches[1]);
            //                 $toplam_taksit = $mevcut_taksit;
            //                 $kalan_metin = trim(str_replace($taksit_yazi_matches[0], '', $kalan_metin));
            //             }

            //             // Satır sonundaki çarpımlı taksit/worldpuan gürültülerini (Örn: 1x1,283.00) temizle
            //             $kalan_metin = preg_replace('/\s*\d+x[0-9.,]+/i', '', $kalan_metin);

            //             // 💰 TUTAR TAMİR REFORMA: VakıfBank kuruş-binlik ayrım kaçış düzenleyici
            //             if (preg_match('/([0-9]{1,3}(?:,[0-9]{3})*\.[0-9]{2})|([0-9]{1,3}(?:\.[0-9]{3})*,[0-9]{2})|([0-9]{1,6}\.[0-9]{2})/', $kalan_metin, $tutar_matches)) {
                            
            //                 $ham_tutar = $tutar_matches[0];
                            
            //                 if (strpos($ham_tutar, ',') !== false && strpos($ham_tutar, '.') !== false) {
            //                     $tutar = floatval(str_replace(',', '', $ham_tutar));
            //                 } else {
            //                     if (strpos($ham_tutar, ',') !== false && strpos($ham_tutar, '.') === false) {
            //                         $tutar = floatval(str_replace(',', '.', str_replace('.', '', $ham_tutar)));
            //                     } else {
            //                         $tutar = floatval($ham_tutar);
            //                     }
            //                 }
                            
            //                 if ($tutar <= 0) continue;

            //                 // Tutarın sağında kalan ek tekil taksit sayısını süz (Örn: 747.25 2)
            //                 $artık_metin = trim(str_replace($ham_tutar, '', $kalan_metin));
            //                 if (preg_match('/(?:\s+|\t)(\d+)\s*$/', $artık_metin, $taksit_num_matches)) {
            //                     $toplam_taksit = intval($taksit_num_matches[1]);
            //                     $artık_metin = trim(str_replace($taksit_num_matches[0], '', $artık_metin));
            //                 }

            //                 // 🛡️ REFORM: Sayfa gürültülerini, Özet Tablolarını ve İsim Alanlarını Jilet Gibi Ayıklama
            //                 $aciklama = $artık_metin;
            //                 $aciklama = preg_replace('/HESAP\s+ÖZETİ.*/i', '', $aciklama); 
            //                 $aciklama = preg_replace('/Toplam\s+Harcama\s+Tutari.*/i', '', $aciklama); 
            //                 $aciklama = preg_replace('/\d{4}\s*\**\s*\d{4}.*/i', '', $aciklama); 
            //                 $aciklama = preg_replace('/Sayfa\s+\d+.*/i', '', $aciklama); 
            //                 $aciklama = preg_replace('/Kredi\s+Karti\s+Hesap.*/i', '', $aciklama);
            //                 $aciklama = trim($aciklama);
                            
            //                 if (empty($aciklama) || preg_match('/ÖNCEKİ\s+DÖNEM/i', $aciklama)) continue;

            //                 // 🎯 3. ADIM: Akıllı İşletme Adı & Kategori Motoru (Yeni firmalar tablosuna göre tekil ID)
            //                 $belirlenen_kategori_id = 0; // Herhangi bir eşleşme bulunamazsa default kategori ID
            //                 $isletme_adi = $aciklama;    // Eşleşme olmazsa ham temiz açıklamayı korur

            //                 foreach ($db_firmalari as $firma) {
            //                     $aranacak = trim($firma['aranacak_kelime']);
            //                     $aranacak_onay = str_replace(['I', 'İ', 'Ğ', 'Ş', 'Ç'], ['[Iİıi]', '[Iİıi]', '[ĞĞğg]', '[ŞŞsş]', '[ÇÇcc]'], $aranacak);
            //                     $temiz_aciklama_vb = str_replace([' ', '/', '.', '-'], '', $aciklama);
                                
            //                     if (stripos($aciklama, $aranacak) !== false || 
            //                         preg_match('/' . preg_quote($aranacak_onay, '/') . '/i', $aciklama) ||
            //                         stripos($temiz_aciklama_vb, $aranacak) !== false) {
                                    
            //                         // 🎯 ARTIK TEKİL ID BASILIYOR: Firmalar tablosundaki net integer değeri atıyoruz
            //                         $belirlenen_kategori_id = (int)$firma['kategori_id'];
            //                         // Ekstredeki kirli isim yerine yeni tablodaki temiz kurumsal ismi basıyoruz (Örn: MİGROS)
            //                         $isletme_adi = $firma['isletme_adi']; 
            //                         break;
            //                     }
            //                 }

            //                 // JSON listesine paketleme yapıyoruz
            //                 $json_cikti_listesi[] = [
            //                     'donem'          => $hesaplanan_donem,
            //                     'kart_turu'      => 'vakifbank',
            //                     'islem_tarihi'   => $islem_tarihi,
            //                     'isletme_adi'    => $isletme_adi,
            //                     'aciklama'       => $aciklama,
            //                     'kategori_id'    => $belirlenen_kategori_id, // String liste bitti, temiz integer ID geldi!
            //                     'tutar'          => $tutar,
            //                     'toplam_taksit'  => $toplam_taksit,
            //                     'mevcut_taksit'  => $mevcut_taksit,
            //                     'eklenme_tarihi' => date('Y-m-d H:i:s')
            //                 ];
            //             }
            //         }
            //     }

            //     // 💣 SAF JSON ÇIKTIYI BAS VE SÜRECİ DURDUR
            //     header('Content-Type: application/json; charset=utf-8');
            //     echo json_encode([
            //         'toplam_bulunan_vakif_harcama' => count($json_cikti_listesi),
            //         'harcamalar' => $json_cikti_listesi
            //     ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            //     exit;
            // }


            $db->commit();
            $success = "Ekstre başarıyla işlendi! Hesap kesim takvimine göre <strong>{$eklenen_kayit_sayisi} adet benzersiz harcama</strong> kuruşu kuruşuna '{$hesaplanan_donem}' dönem bütçenize aktarıldı.";
            
        } catch (Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            $error = "Kritik Ekstre Hatası: " . $e->getMessage();
        }
    }
}




// =========================================================================
// 📊 VIEW VERİ BAĞLAMA: Aktif Döneme Ait Harcamaların Listelenmesi
// =========================================================================
$harcamalar = [];
try {
    // Harcamaları mizan tablosuna basılmak üzere tam döküm halinde çekiyoruz
    $stmt = $db->prepare("
        SELECT * FROM harcama_gecmisi 
        WHERE donem = ? 
        ORDER BY islem_tarihi DESC, id DESC
    ");
    $stmt->execute([$aktif_donem]);
    $hargamalar = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Mizan tablosu listelenirken hata oluştu: " . $e->getMessage();
}


// Görünüm (View) katmanı güvenli bir şekilde çağrılır
require_once 'admin/view/ekstreler.php';