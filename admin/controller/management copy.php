<?php

if (!isset($_SESSION)) {
    session_start();
}


//$error = "";

// 📌 1. TALEP VE OTURUM METRİKLERİNİN YAKALANMASI
$islem       = $_POST['islem'] ?? '';
$faiz_stopaj = 17.5;


if (!isset($_SESSION['aktif_donem']) || empty($_SESSION['aktif_donem'])) {
    // Session boşalmışsa (PC açılıp kapandıysa), DB'deki en büyük/en son dönemi çek
    $en_guncel_donem_sorgu = $db->query("SELECT aktif_donem FROM butce_akis ORDER BY aktif_donem DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    
    if ($en_guncel_donem_sorgu && !empty($en_guncel_donem_sorgu['aktif_donem'])) {
        // Veritabanında kayıtlı en güncel ayı oturuma kilitliyoruz
        $_SESSION['aktif_donem'] = $en_guncel_donem_sorgu['aktif_donem'];
    } else {
        // Eğer veritabanı tamamen boşsa, sistemin patlamaması için bulunulan cari ayı (Y-m formatında) baz al
        $_SESSION['aktif_donem'] = date('Y-m');
    }
}

// Artık aktif dönem her zaman veritabanındaki reel canlı ayı temsil eder
$aktif_donem = $_SESSION['aktif_donem'];

// Finansal Anayasa: Sabit kredi ödeme tutarı her zaman korunur
$evKredisiTutariStandart = 53315.33; 

// 🎯 REFORM: İnputtan gelen ham veriyi yakala ve session'da mühürle
if (isset($_POST['faiz_kredi_butcesi'])) {
    $_SESSION['saf_kredi_input_butcesi'] = floatval($_POST['faiz_kredi_butcesi']);
}

// Eğer session boşsa varsayılan olarak 53200.00 ata
$kredi_input_butcesi = $_SESSION['saf_kredi_input_butcesi'] ?? 53200.00;

// 2. Oturum Tanımlamaları ve Faiz Parametreleri
$faiz_orani      = isset($_POST['faiz_orani']) ? floatval($_POST['faiz_orani']) : ($_SESSION['dinamik_faiz_orani'] ?? 30.00);
$faiz_gun_sayisi = isset($_POST['faiz_gun_sayisi']) ? intval($_POST['faiz_gun_sayisi']) : ($_SESSION['dinamik_faiz_gun_sayisi'] ?? 1);

// Canlı tetiklenen faiz parametreleri oturuma kilitlenir
$_SESSION['dinamik_faiz_orani']      = $faiz_orani;
$_SESSION['dinamik_faiz_gun_sayisi'] = $faiz_gun_sayisi;

// 🎯 BİLEŞİK FAİZ HAVUZU: Faiz aktarıldıkça büyüyen bütçe takibi
if (!isset($_SESSION['dinamik_faiz_kredi_butcesi'])) {
    $_SESSION['dinamik_faiz_kredi_butcesi'] = $kredi_input_butcesi;
}
$dinamik_faiz_kredi_butcesi = $_SESSION['dinamik_faiz_kredi_butcesi'];

$isbank_o_faizsiz_ana_para = $dinamik_faiz_kredi_butcesi;

$ay_isimleri = [
    1=>'OCAK', 2=>'ŞUBAT', 3=>'MART', 4=>'NİSAN', 5=>'MAYIS', 6=>'HAZİRAN', 
    7=>'TEMMUZ', 8=>'AĞUSTOS', 9=>'EYLÜL', 10=>'EKİM', 11=>'KASIM', 12=>'ARALIK'
];

// 📌 2. TÜM FİNANSAL HAVUZ BİLEŞENLERİNİN İLK DEĞER BARİYERİ
$isbank_a_anapara  = 0.00; $isbank_a_nema  = 0.00; $isbank_a_bakiye  = 0.00;
$isbank_o_anapara  = 0.00; $isbank_o_nema  = 0.00; $isbank_o_bakiye  = 0.00;
$vakif_o_anapara   = 0.00; $vakif_o_nema   = 0.00; $vakif_o_bakiye   = 0.00;
$nakit_anapara     = 0.00; $nakit_nema     = 0.00; $nakit_bakiye     = 0.00;

$vakif_bloke       = 0.00; 
$nakit_bloke       = 0.00; 

$ozge_maas_alindi    = false;
$armagan_maas_alindi = false;
$maktu_mesai_alindi  = false;
$kredi_odendi        = false;

$db_ozge_maas        = 0.00;
$db_armagan_maas     = 0.00;
$db_maktu_mesai      = 0.00;
$db_ev_kredisi       = 0.00;

$damacana_kalan_butce = 1120.00;
$aidat_aylik_butce    = 0.00;
$aidat_odenen_tutar   = 0.00;

// 📌 3. VERİ TABANINDAN CARİ AY LİSTESİNİN ÇEKİLMESİ
$stmt_Sorgu = $db->prepare("SELECT * FROM butce_akis WHERE aktif_donem = :donem ORDER BY id ASC");
$stmt_Sorgu->execute(['donem' => $aktif_donem]);
$kalemler = $stmt_Sorgu->fetchAll(PDO::FETCH_ASSOC);

list($aktif_yil, $aktif_ay_str) = explode('-', $aktif_donem);
$aktif_ay_index = intval($aktif_ay_str);


// =========================================================================
// 📌 2. TÜM FİNANSAL HAVUZ BİLEŞENLERİNİN İLK DEĞER BARİYERİ (DB ENTEGRELİ)
// =========================================================================
$querry_havuz = $db->query("SELECT * FROM finansal_havuzlar")->fetchAll(PDO::FETCH_ASSOC);
$havuzlar = [];
foreach ($querry_havuz as $h) {
    $havuzlar[$h['havuz_kodu']] = [
        'anapara' => floatval($h['anapara']),
        'nema' => floatval($h['nema']),
        'bloke' => floatval($h['bloke_tutar'])
    ];
}

// Veritabanındaki gerçek ve kilitli anaparaları havuz değişkenlerine mühürle
$isbank_a_anapara = $havuzlar['isbank_a']['anapara'] ?? 0.00;
$isbank_o_anapara = $havuzlar['isbank_o']['anapara'] ?? 0.00;
$vakif_o_anapara  = $havuzlar['vakif_o']['anapara'] ?? 0.00;
$nakit_anapara    = $havuzlar['nakit']['anapara'] ?? 0.00;

// Nemaları ve bloke bütçelerini başlangıçta eşitle
$isbank_a_nema    = $havuzlar['isbank_a']['nema'] ?? 0.00;
$isbank_o_nema    = $havuzlar['isbank_o']['nema'] ?? 0.00;
$vakif_o_nema     = $havuzlar['vakif_o']['nema'] ?? 0.00;
$nakit_nema       = $havuzlar['nakit']['nema'] ?? 0.00;

$vakif_bloke      = $havuzlar['vakif_o']['bloke'] ?? 0.00;
$nakit_bloke      = $havuzlar['nakit']['bloke'] ?? 0.00;



 // 🚀 4. MERKEZİ VERİ İŞLEME DÖNGÜSÜ (CENTRAL PROCESSING LOOP - %100 SIZINTISIZ SÜZGEC)
foreach ($kalemler as $kalem) {
    $isim      = trim($kalem['kalem_adi']);
    $tutar     = floatval($kalem['tutar']);
    $odenan    = floatval($kalem['odenen_tutar']);
    $turu      = $kalem['turu'];
    $k_kucuk   = mb_strtolower($isim, 'UTF-8');
    
    $is_odendi = ($odenan > 0 || ($turu === 'gelir' && $odenan == $tutar && $tutar > 0));

    // 🟢 A. GELİR (ENJEKSİYON) AKIŞLARININ ANALİZİ (%100 SIZINTISIZ KİLİTLİ MATRİS)
    if ($turu === 'gelir') {
        
        if ($is_odendi) {
            
            // 🎯 REFORM 1: TRANSFER SÜZGECİ EN ÜSTE ALINDI (HİÇBİR KURAL TEĞET GEÇEMEZ!)
            if (strpos($k_kucuk, 'transfer') !== false) {
                // Para VAKIFBANK'A gidiyorsa
                if (strpos($k_kucuk, '-> vakif') !== false || strpos($k_kucuk, '-> vakıf') !== false) {
                    $vakif_o_anapara += $odenan; // Hedef havuz doldu
                    
                    // 🏛️ MUTLAK DÜŞÜM: Kaynak neresi olursa olsun içinden 'isbank' geçiyorsa İşbank A'dan düş!
                    if (strpos($k_kucuk, 'isbank') !== false) {
                        $isbank_a_anapara -= $odenan; 
                    } 
                    elseif (strpos($k_kucuk, 'nakit') !== false || strpos($k_kucuk, 'nakıt') !== false) {
                        $nakit_anapara -= $odenan;
                    }
                } 
                // Para NAKİT havuzuna gidiyorsa
                elseif (strpos($k_kucuk, '-> nakit') !== false || strpos($k_kucuk, '-> nakıt') !== false) {
                    $nakit_anapara += $odenan;
                    
                    if (strpos($k_kucuk, 'isbank') !== false) {
                        $isbank_a_anapara -= $odenan;
                    } elseif (strpos($k_kucuk, 'vakif') !== false || strpos($k_kucuk, 'vakıf') !== false) {
                        $vakif_o_anapara -= $odenan;
                    }
                } 
                // Para İŞBANKASI A havuzuna geliyorsa
                elseif (strpos($k_kucuk, '-> isbank') !== false) {
                    $isbank_a_anapara += $odenan;
                    
                    if (strpos($k_kucuk, 'nakit') !== false || strpos($k_kucuk, 'nakıt') !== false) {
                        $nakit_anapara -= $odenan;
                    } elseif (strpos($k_kucuk, 'vakif') !== false || strpos($k_kucuk, 'vakıf') !== false) {
                        $vakif_o_anapara -= $odenan;
                    }
                }
                continue; // Çift taraflı enjeksiyon bitti, alt kurallara sızmaması için döngüyü kır!
            }

            // 🎯 REFORM 2: FAİZ GELİRLERİ SÜZGECİ (2. SIRAYA ALINDI)
            if (strpos($k_kucuk, 'faizgeliri') !== false) {
                if (strpos($k_kucuk, '_a') !== false) {
                    $isbank_a_anapara += $odenan;
                } elseif (strpos($k_kucuk, '_o') !== false) {
                    $isbank_o_anapara += $odenan;
                }
                continue; 
            }
            
            // 🎯 KURAL 3: Evdeki Nakit Girişi
            if ($isim === 'Evdeki Nakit Girişi') {
                $nakit_anapara += $odenan;
                continue;
            }

            // 🎯 KURAL 4: Ana Maaş Enjeksiyon Kontrolleri
            if (strpos($isim, 'Özge') !== false && strpos($isim, 'Maaş') !== false) {
                $db_ozge_maas = $tutar;
                $ozge_maas_alindi = true;
                
                $isbank_o_anapara += $kredi_input_butcesi; 
                $isbank_a_anapara += max(0, $odenan - $kredi_input_butcesi); 
            }
            elseif (strpos($isim, 'Armağan') !== false && strpos($isim, 'Maaş') !== false) {
                $db_armagan_maas = $tutar;
                $armagan_maas_alindi = true;
                $isbank_a_anapara += $odenan;
            }
            elseif (strpos($isim, 'Maktu') !== false || strpos($isim, 'Mesai') !== false) {
                $db_maktu_mesai = $tutar;
                $maktu_mesai_alindi = true;
                $isbank_a_anapara += $odenan;
            }
            elseif (strpos($isim, 'Fazla Ödeme') !== false || strpos($k_kucuk, 'fazla ödemesi') !== false) {
                $isbank_a_anapara += $odenan;
            }
            // 🎯 KURAL 5: Ön Takılı İstisna Diğer Harici Gelirler
            else {
                if (preg_match('/^v(\s+|-|_|$)/i', $k_kucuk))      { $vakif_o_anapara += $odenan; }
                elseif (preg_match('/^n(\s+|-|_|$)/i', $k_kucuk))   { $nakit_anapara += $odenan; }
                elseif (preg_match('/^y(\s+|-|_|$)/i', $k_kucuk))   { $isbank_a_anapara += $odenan; }
            }
        }
    }
    // 🔴 B. GİDER AKIŞLARININ ANALİZİ (%100 GÜVENLİ VE NET HAVUZ DÜŞÜM MOTORU)
    else {
        // 🎯 KURAL 1: Havuz Transfer Fonları Gelir (A) tarafında çift taraflı (Borç/Alacak) olarak işlendi.
        // Gider tarafında mükerrer (üst üste iki kez) düşüm yaparak cüzdanları eksiye uçurmaması için burayı direkt atlıyoruz.
        if (strpos($k_kucuk, 'havuz transfer fonu') !== false) {
            continue;
        }

        // 🎯 KURAL 2: Aidat harcaması Nakit anaparasından düşer, ödenmediyse Nakit Bloke bütçesine rezerve olur.
        if ($isim === 'Aidat') {
            $aidat_aylik_butce = $tutar; 
            $aidat_odenen_tutar = $odenan;
            if ($odenan > 0) { 
                $nakit_anapara -= $odenan; 
            } else { 
                $nakit_bloke += $tutar; 
            }
        }
        // 🎯 KURAL 3: Damacana / İçme Suyu bütçe kalemi Nakit cüzdanından düşer, kalan bütçe rezerve olur.
        elseif (strpos($k_kucuk, 'damacana') !== false || strpos($k_kucuk, 'içme suyu') !== false) {
            $damacana_kalan_butce = $tutar; 
            $saf_harcanan_su = max(0, 1120.00 - $tutar);
            if ($saf_harcanan_su > 0) { 
                $nakit_anapara -= $saf_harcanan_su; 
            } 
            $nakit_bloke += $tutar;
        }
        // 🎯 KURAL 4: Ev Kredisi ödendiği an İşbank (Ö) [Kredi Cüzdanı] anaparasından düşer!
        elseif (strpos($k_kucuk, 'kredi') !== false) {
            $db_ev_kredisi = $tutar; 
            if ($odenan > 0) { 
                $kredi_odendi = true; 
                $isbank_o_anapara -= $odenan; 
            }
        }
        // 🎯 KURAL 5: Nergiz Cep hiçbir harcama matrisine yazılmaz ve pas geçilir
        elseif ($isim === 'Nergiz Cep') { 
            continue; 
        }

        // 6. Kredi Kartları ve Diğer Faturaların/Ön Takıların Dağıtım İstasyonu
        else {
            $is_sabit_fatura = false;
            foreach (['faturası', 'izsu', 'elektrik', 'su', 'doğalgaz', 'telefon', 'internet'] as $sk) {
                if (strpos($k_kucuk, $sk) !== false) { $is_sabit_fatura = true; break; }
            }
            
            // 🎯 BLOKE MANTIĞI: Eğer sabit bir faturasıysa ve HENÜZ ÖDENMEDİYSE Vakıfbank Bloke rezervine eklenir
            if ($is_sabit_fatura && !$is_odendi) {
                $vakif_bloke += $tutar;
            }
            
            // Sadece ödenmiş olan gider kalemleri ilgili havuzların anaparasını eritir (Reel Erime)
            if ($is_odendi) {
                if (strpos($isim, 'İşbank') !== false && strpos($isim, 'KK') !== false) {
                    $isbank_a_anapara -= $odenan;
                }
                elseif (strpos($isim, 'Vakıfbank') !== false && strpos($isim, 'KK') !== false) {
                    $isbank_a_anapara -= $odenan; 
                }
                // 🎯 REEL ERİME REFORMU: Sabit faturalar ödendiği an İşbank A'dan değil, VAKIFBANK Ö havuzundan erir!
                elseif ($is_sabit_fatura || preg_match('/^v(\s+|-|_|$)/i', $k_kucuk)) {
                    $vakif_o_anapara -= $odenan;
                }
                elseif (preg_match('/^n(\s+|-|_|$)/i', $k_kucuk)) {
                    $nakit_anapara -= $odenan;
                }
                elseif (preg_match('/^y(\s+|-|_|$)/i', $k_kucuk)) {
                    $isbank_a_anapara -= $odenan;
                }
                else {
                    // Ön takısız düz harici giderler peşinen değil, sadece ÖDENİNCE İşbank A'dan düşer
                    $isbank_a_anapara -= $odenan;
                }
            }
        }
    }
} 

// <--- MERKEZİ VERİ İŞLEME DÖNGÜSÜNÜN (FOREACH) ASIL KAPANIŞ PARANTEZİ


/**
 * 🏛️ PART 2: REQUEST ROUTER & ACTION ENGINES (POST)
 * Tüm buton aksiyonları, bileşik faiz aktarımları ve erken çıkışlı ödeme motorları.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    // 💳 1. FORM YAKALAYICI: 12 AYLIK TAKSİT MATRİSİ GÜNCELLEME SÜRECİ
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

    // 💳 2. FORM YAKALAYICI: CARİ AY MANUEL HESAP KESİM VERİLERİNİ GÜNCELLEME
    if ($islem === 'cari_girdiler_kaydet') {
        $kartlar = [
            'isbank' => [
                'donem_ici'         => floatval($_POST['isbank_donem_ici'] ?? 0), 
                'provizyon'         => floatval($_POST['isbank_provizyon'] ?? 0), 
                'doviz_borcu'       => floatval($_POST['isbank_doviz'] ?? 0),
                'kesinlesen_ekstre' => floatval($_POST['isbank_ekstre'] ?? 0)
            ],
            'vakifbank' => [
                'donem_ici'         => floatval($_POST['vakif_donem_ici'] ?? 0), 
                'provizyon'         => floatval($_POST['vakif_provizyon'] ?? 0), 
                'doviz_borcu'       => floatval($_POST['vakif_doviz'] ?? 0),
                'kesinlesen_ekstre' => floatval($_POST['vakif_ekstre'] ?? 0)
            ]
        ];

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
                'ekstre'    => $m['kesinlesen_ekstre'], 
                'kart_adi'  => $kart_adi
            ]);
        }
        echo json_encode(['status' => 'success']); 
        exit;
    }

    // 🟢🔴 3. FORM YAKALAYICI: GELİR VEYA GİDER KALEMLERİNİ TOPLU KAYDETME
    if ($islem === 'butce_kaydet') {
        $kalemler_data = $_POST['kalemler'] ?? [];
        foreach ($kalemler_data as $k_id => $data) {
            $k_id  = intval($k_id); 
            $adi   = trim($data['adi'] ?? ''); 
            $tutar = floatval($data['tutar'] ?? 0);
            
            if ($k_id > 0 && $adi !== '') {
                $db->prepare("UPDATE butce_akis SET kalem_adi = :adi, tutar = :tutar WHERE id = :id AND aktif_donem = :donem")
                ->execute([
                    'adi'   => $adi, 
                    'tutar' => $tutar, 
                    'id'    => $k_id, 
                    'donem' => $aktif_donem
                ]);
            }
        }
        echo json_encode(['status' => 'success']); 
        exit;
    }

    // ⚙️ 4. İŞLEM: YENİ GELİR / GİDER KALEMİ EKLEME MOTORU (TRANSFER DESTEKLİ)
    if ($islem === 'yeni_kalem_ekle') {
        $adi   = trim($_POST['kalem_adi'] ?? ''); 
        $tutar = floatval($_POST['tutar'] ?? 0); 
        $turu  = $_POST['turu'] === 'gelir' ? 'gelir' : 'gider';
        
        if ($adi !== '' && $tutar > 0) {
            $temiz_adi = mb_strtolower($adi, 'UTF-8');
            if (strpos($temiz_adi, 'havuz transfer') !== false) {
                $db->prepare("INSERT INTO butce_akis (aktif_donem, kalem_adi, tutar, odenen_tutar, turu, odenme_tarihi) VALUES (:donem, :adi, :tutar, :odenen, :turu, NOW())")
                ->execute(['donem' => $aktif_donem, 'adi' => $adi, 'tutar' => $tutar, 'odenen' => $tutar, 'turu' => $turu]);
            } else {
                $db->prepare("INSERT INTO butce_akis (aktif_donem, kalem_adi, tutar, odenen_tutar, turu) VALUES (:donem, :adi, :tutar, 0.00, :turu)")
                ->execute(['donem' => $aktif_donem, 'adi' => $adi, 'tutar' => $tutar, 'turu' => $turu]);
            }
            echo json_encode(['status' => 'success']);
        } else { 
            echo json_encode(['status' => 'error']); 
        }
        exit;
    }

    // ⚙️ 5. İŞLEM: TEKİL GEÇİCİ KALEM SİLME MOTORU
    if ($islem === 'kalem_sil') {
        if ($id > 0) { 
            $db->prepare("DELETE FROM butce_akis WHERE id = :id")->execute(['id' => $id]); 
            echo json_encode(['status' => 'success']); 
        }
        exit;
    }

    // ⚙️ 6. İŞLEM: EXCEL MATRİS SÜTUNLARINA KÜMÜLATİF DAĞITIM VE ÖDEME MOTORU
    if ($id > 0 && ($islem === 'maas_eft' || $islem === 'ode')) {
        $stmt = $db->prepare("SELECT id, kalem_adi, tutar, turu FROM butce_akis WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $k_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($k_row) {
            $isim = trim($k_row['kalem_adi']);
            $kilitlenecek_tutar = floatval($k_row['tutar']);
            $turu = $k_row['turu'];
            $k_kucuk = mb_strtolower($isim, 'UTF-8');

            // 🏦 VAKIFBANK KREDİ KARTI ÖDEME ANINDA CANLI BORÇ KİLİTLEME VE ANINDA ERKEN ÇIKIŞ
            if (strpos($isim, 'Vakıfbank') !== false && strpos($isim, 'KK') !== false) {
                $cc = $db->query("SELECT donem_ici, provizyon, doviz_borcu, kesinlesen_ekstre FROM kredi_kartlari WHERE kart_adi='vakifbank'")->fetch(PDO::FETCH_ASSOC);
                $t_tutar = $db->query("SELECT vakif_tutar FROM cc_taksitler WHERE ay_index = $aktif_ay_index")->fetchColumn();
                $kilitlenecek_tutar = floatval($cc['donem_ici'] ?? 0) + floatval($cc['provizyon'] ?? 0) + floatval($cc['doviz_borcu'] ?? 0) + floatval($cc['kesinlesen_ekstre'] ?? 0) + floatval($t_tutar);
                if ($kilitlenecek_tutar <= 0) { $kilitlenecek_tutar = 30000.00; }
                
                $db->prepare("UPDATE butce_akis SET odenen_tutar = :odenen, odenme_tarihi = NOW() WHERE id = :id")->execute(['odenen' => $kilitlenecek_tutar, 'id' => $id]);
                $db->query("UPDATE kredi_kartlari SET donem_ici = 0.00, provizyon = 0.00, doviz_borcu = 0.00, kesinlesen_ekstre = 0.00 WHERE kart_adi = 'vakifbank'");
                $db->query("UPDATE cc_taksitler SET vakif_tutar = 0.00 WHERE ay_index = $aktif_ay_index");
                
                $db->prepare("INSERT IGNORE INTO `aylik_harcama_raporu` (`donem`) VALUES (:donem)")->execute(['donem' => $aktif_donem]);
                $db->prepare("UPDATE `aylik_harcama_raporu` SET `kredi_karti_o` = `kredi_karti_o` + :t_tutar WHERE `donem` = :donem")->execute(['t_tutar' => $kilitlenecek_tutar, 'donem' => $aktif_donem]);
                echo json_encode(['status' => 'success']); 
                exit;
            }
            
            // 🏦 İŞ BANKASI KREDİ KARTI ÖDEME ANINDA CANLI BORÇ KİLİTLEME VE ANINDA ERKEN ÇIKIŞ
            if (strpos($isim, 'İşbank') !== false && strpos($isim, 'KK') !== false) {
                $cc = $db->query("SELECT donem_ici, provizyon, doviz_borcu, kesinlesen_ekstre FROM kredi_kartlari WHERE kart_adi='isbank'")->fetch(PDO::FETCH_ASSOC);
                $t_tutar = $db->query("SELECT isbank_tutar FROM cc_taksitler WHERE ay_index = $aktif_ay_index")->fetchColumn();
                $kilitlenecek_tutar = floatval($cc['donem_ici'] ?? 0) + floatval($cc['provizyon'] ?? 0) + floatval($cc['doviz_borcu'] ?? 0) + floatval($cc['kesinlesen_ekstre'] ?? 0) + floatval($t_tutar);
                if ($kilitlenecek_tutar <= 0) { $kilitlenecek_tutar = 68000.00; }
                
                $db->prepare("UPDATE butce_akis SET odenen_tutar = :odenen, odenme_tarihi = NOW() WHERE id = :id")->execute(['odenen' => $kilitlenecek_tutar, 'id' => $id]);
                $db->query("UPDATE kredi_kartlari SET donem_ici = 0.00, provizyon = 0.00, doviz_borcu = 0.00, kesinlesen_ekstre = 0.00 WHERE kart_adi = 'isbank'");
                $db->query("UPDATE cc_taksitler SET isbank_tutar = 0.00 WHERE ay_index = $aktif_ay_index");
                
                $db->prepare("INSERT IGNORE INTO `aylik_harcama_raporu` (`donem`) VALUES (:donem)")->execute(['donem' => $aktif_donem]);
                $db->prepare("UPDATE `aylik_harcama_raporu` SET `kredi_karti_a` = `kredi_karti_a` + :t_tutar WHERE `donem` = :donem")->execute(['t_tutar' => $kilitlenecek_tutar, 'donem' => $aktif_donem]);
                echo json_encode(['status' => 'success']); 
                exit;
            }

            // Standart Maaş, Fatura veya Harici Giderlerin kilitlenme lojiği
            $db->prepare("UPDATE butce_akis SET odenen_tutar = :odenen, odenme_tarihi = NOW() WHERE id = :id")->execute(['odenen' => $kilitlenecek_tutar, 'id' => $id]);
            $db->prepare("INSERT IGNORE INTO `aylik_harcama_raporu` (`donem`) VALUES (:donem)")->execute(['donem' => $aktif_donem]);

            $h_sutun = ''; 
            $is_fatura = false;

            if (strpos($isim, 'Nergiz Cep') !== false) {
                $h_sutun = ''; // Nergiz Öde hiçbir harcama matrisine yazılmaz
            }
            elseif (strpos($isim, 'Özge') !== false && strpos($isim, 'Maaş') !== false)     $h_sutun = 'maas_o';
            elseif (strpos($isim, 'Armağan') !== false && strpos($isim, 'Maaş') !== false)   $h_sutun = 'maas_a';
            elseif (strpos($isim, 'Maktu') !== false || strpos($isim, 'Mesai') !== false)    $h_sutun = 'maktu_mesai';
            elseif (strpos($isim, 'Kredi') !== false)                                         $h_sutun = 'ev_kredisi';
            elseif (strpos($isim, 'Aidat') !== false)                                         $h_sutun = 'aidat';
            elseif (strpos($isim, 'Elektrik') !== false)                                      { $h_sutun = 'elektrik'; $is_fatura = true; }
            elseif (strpos($isim, 'Su Faturası') !== false || strpos($isim, 'İzsu') !== false) { $h_sutun = 'su'; $is_fatura = true; }
            elseif (strpos($isim, 'Doğalgaz') !== false)                                      { $h_sutun = 'dogalgaz'; $is_fatura = true; }
            elseif (strpos($isim, 'Telefon') !== false)                                       { $h_sutun = 'telefon'; $is_fatura = true; }
            elseif (strpos($isim, 'İnternet') !== false)                                      { $h_sutun = 'internet'; $is_fatura = true; }
            else {
                if ($turu === 'gider') {
                    $h_sutun = 'harcama_nakit'; 
                } elseif ($turu === 'gelir') {
                    if (strpos($isim, 'Fazla Ödeme') !== false || preg_match('/^[vny]/i', $k_kucuk)) {
                        $h_sutun = 'ek_gelir';
                    }
                }
            }

            if ($h_sutun !== '') {
                $db->prepare("UPDATE `aylik_harcama_raporu` SET `{$h_sutun}` = `{$h_sutun}` + :t_tutar WHERE `donem` = :donem")
                   ->execute(['t_tutar' => $kilitlenecek_tutar, 'donem' => $aktif_donem]);

                if ($is_fatura) {
                    $db->prepare("UPDATE `aylik_harcama_raporu` SET `fatura_nakit` = `fatura_nakit` + :t_tutar WHERE `donem` = :donem")
                       ->execute(['t_tutar' => $kilitlenecek_tutar, 'donem' => $aktif_donem]);
                }
            }
            echo json_encode(['status' => 'success']); 
            exit;
        }
    }

    // ⚙️ 7. İŞLEM: AKILLI DAMACANA SU SİPARİŞİ VE HARCAMA NAKİT MATRİS BAĞLANTISI
    if ($islem === 'su_damacana_aksiyon') {
        $birim_fiyat = floatval($_POST['birim_fiyat'] ?? 140);
        $yon         = $_POST['yon'] ?? '';

        $stmt = $db->prepare("SELECT tutar FROM butce_akis WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $su_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$su_row) { echo json_encode(['status' => 'error']); exit; }

        if ($yon === '+1') {
            $db->prepare("UPDATE butce_akis SET tutar = tutar + :fiyat WHERE id = :id")->execute(['fiyat' => $birim_fiyat, 'id' => $id]);
            echo json_encode(['status' => 'success']); exit;
        }

        if ($yon === '-1') {
            if (floatval($su_row['tutar']) < $birim_fiyat) { echo json_encode(['status' => 'error']); exit; }
            $yeni_tutar = max(0, floatval($su_row['tutar']) - $birim_fiyat);
            $db->prepare("UPDATE butce_akis SET tutar = :yeni_tutar, odenme_tarihi = NOW() WHERE id = :id")->execute(['yeni_tutar' => $yeni_tutar, 'id' => $id]);

            $db->prepare("INSERT IGNORE INTO `aylik_harcama_raporu` (`donem`) VALUES (:donem)")->execute(['donem' => $aktif_donem]);
            $db->prepare("UPDATE `aylik_harcama_raporu` SET `harcama_nakit` = `harcama_nakit` + :fiyat WHERE `donem` = :donem")->execute(['fiyat' => $birim_fiyat, 'donem' => $aktif_donem]);
            echo json_encode(['status' => 'success']); exit;
        }
    }

    // ⚙️ 8. İŞLEM: SİMÜLASYON ADIMLARINI VARSAYILAN DEĞERLERE SIFIRLAMA (TAM TEMİZLİK)
    if ($islem === 'simulasyon_sifirla') {
        $db->prepare("UPDATE butce_akis SET odenen_tutar = 0.00, odenme_tarihi = NULL WHERE aktif_donem = :donem")->execute(['donem' => $aktif_donem]);
        $db->prepare("UPDATE butce_akis SET tutar = 1120.00 WHERE aktif_donem = :donem AND (kalem_adi LIKE '%Damacana%' OR kalem_adi LIKE '%İçme Suyu%' OR kalem_adi LIKE '%damacana%')")->execute(['donem' => $aktif_donem]);
        
        $db->prepare("
            DELETE FROM butce_akis WHERE aktif_donem = :donem AND (
                (turu = 'gider' AND kalem_adi NOT LIKE '%Maaş%' AND kalem_adi NOT LIKE '%Mesai%' AND kalem_adi NOT LIKE '%Maktu%' AND kalem_adi NOT LIKE '%KK%' AND kalem_adi NOT LIKE '%Kredi%' AND kalem_adi NOT LIKE '%Faturası%' AND kalem_adi NOT LIKE '%Damacana%' AND kalem_adi NOT LIKE '%İçme Suyu%' AND kalem_adi NOT LIKE '%Aidat%' AND kalem_adi NOT LIKE '%Nergiz Cep%' AND kalem_adi NOT LIKE '%İzsu%' AND kalem_adi NOT LIKE '%Elektrik%' AND kalem_adi NOT LIKE '%Su%' AND kalem_adi NOT LIKE '%Doğalgaz%' AND kalem_adi NOT LIKE '%Telefon%' AND kalem_adi NOT LIKE '%İnternet%') 
                OR 
                (turu = 'gelir' AND kalem_adi NOT LIKE '%Maaş%' AND kalem_adi NOT LIKE '%Maktu%')
            )
        ")->execute(['donem' => $aktif_donem]);
        
        $db->prepare("UPDATE `aylik_harcama_raporu` SET `maas_o`=0, `maas_a`=0, `maktu_mesai`=0, `ek_gelir`=0, `faiz_gelir`=0, `kredi_karti_o`=0, `kredi_karti_a`=0, `ev_kredisi`=0, `aidat`=0, `harcama_nakit`=0, `fatura_nakit`=0, `elektrik`=0, `su`=0, `dogalgaz`=0, `telefon`=0, `internet`=0 WHERE `donem` = :donem")->execute(['donem' => $aktif_donem]);
        
        // Faiz tablosundaki o aya ait tüm çoklu basım geçmişini siler
        $db->prepare("DELETE FROM faiz_gelirleri WHERE donem = :donem")->execute(['donem' => $aktif_donem]);

        // 🎯 REFORM: Sıfırlama anında hem bileşik bütçeyi hem de ham input bütçesini fabrika ayarlarına çek
        $_SESSION['saf_kredi_input_butcesi'] = 53200.00;
        $_SESSION['dinamik_faiz_kredi_butcesi'] = 53200.00;
        
        echo json_encode(['status' => 'success']); 
        exit;
    }

    // ⚙️ 9. İŞLEM: EVDEKİ NAKİT AKTARMA MOTORU
    if ($islem === 'evdeki_nakit_aktar') {
        $tutar = floatval($_POST['tutar'] ?? 0);
        if ($tutar > 0) {
            $db->prepare("INSERT INTO butce_akis (aktif_donem, kalem_adi, tutar, odenen_tutar, turu) VALUES (:donem, 'Evdeki Nakit Girişi', :tutar, :odenen, 'gelir')")
               ->execute(['donem' => $aktif_donem, 'tutar' => $tutar, 'odenen' => $tutar]);
            echo json_encode(['status' => 'success']);
        } else { echo json_encode(['status' => 'error']); }
        exit;
    }

    // ⚙️ 10. İŞLEM: NAKİT -> VAKIF SAF LİKİDİTE AKTARMA MOTORU
    if ($islem === 'vakif_nakit_aktar' || $islem === 'mavi_butondan_vakifa_aktar') {
        $tutar = floatval($_POST['tutar'] ?? 0);
        if ($tutar > 0) {
            $db->prepare("INSERT INTO butce_akis (aktif_donem, kalem_adi, tutar, odenen_tutar, turu, is_locked) VALUES (:donem, 'Havuz Transfer Fonu (NAKIT -> VAKIF)', :tutar, :odenen, 'gelir', 0)")
               ->execute(['donem' => $aktif_donem, 'tutar' => $tutar, 'odenen' => $tutar]);
            $db->prepare("INSERT IGNORE INTO `aylik_harcama_raporu` (`donem`) VALUES (:donem)")->execute(['donem' => $aktif_donem]);
            echo json_encode(['status' => 'success']);
        } else { echo json_encode(['status' => 'error']); }
        exit;
    }

    // ⚙️ 11. İŞLEM: NERGİZ CEP ÖDEME MOTORU
    if ($islem === 'nergiz_cep_ode') {
        $db->prepare("UPDATE butce_akis SET odenen_tutar = 220.00, odenme_tarihi = NOW() WHERE aktif_donem = :donem AND kalem_adi = 'Nergiz Cep'")->execute(['donem' => $aktif_donem]);
        echo json_encode(['status' => 'success']); 
        exit;
    }

    // 🏛️ 12. İŞLEM: ÇOKLU BASIM DESTEKLİ BİLEŞİK FAİZ AKTARMA MOTORU
    if ($islem === 'faiz_aktar') {
        // 1. Gelişmiş Net Faiz Hesaplama Parametreleri
        $oran   = $faiz_orani;
        $gun    = $faiz_gun_sayisi;
        $stopaj = $faiz_stopaj;

        // 🎯 REFORM 1: Hayali veritabanı sorguları ve harcamaları görmeyen eski formüller TAMAMEN SİLİNDİ!
        // Döngünün (Blok 2) harcamaları, kartları ve transferleri düşerek bulduğu EN GÜNCEL REEL ANAPARALARI doğrudan matrah yap!
        $safIsbankAanapara = max(0, $isbank_a_anapara);
        $safIsbankOanapara = max(0, $isbank_o_anapara);

        // Kredinin ödenme durumunu kontrol ediyoruz
        $stmtKredi = $db->prepare("SELECT odenen_tutar FROM butce_akis WHERE aktif_donem = :donem AND kalem_adi LIKE '%Kredi%' AND turu='gider' LIMIT 1");
        $stmtKredi->execute(['donem' => $aktif_donem]);
        $rowKredi = $stmtKredi->fetch(PDO::FETCH_ASSOC);
        $krediOdediMiCanli = $rowKredi ? (floatval($rowKredi['odenen_tutar']) > 0) : false;

        // 2. Canlı Faiz Üretim Denklemleri (Gerçekleşen Canlı Anapara Üzerinden)
        $safIsbankABrutFaiz = ($safIsbankAanapara > 0) ? ($safIsbankAanapara * ($oran / 100) / 365) * $gun : 0;
        $anlik_net_faiz_a   = $safIsbankABrutFaiz * (1 - ($stopaj / 100));

        $safIsbankOBrutFaiz = ($safIsbankOanapara > 0 && !$krediOdediMiCanli) ? ($safIsbankOanapara * ($oran / 100) / 365) * $gun : 0;
        $anlik_net_faiz_o   = $safIsbankOBrutFaiz * (1 - ($stopaj / 100));

        $toplam_aktarilacak_faiz = $anlik_net_faiz_a + $anlik_net_faiz_o;

        if ($toplam_aktarilacak_faiz > 0) {
            
            $db->prepare("INSERT INTO faiz_gelirleri (donem, faizgeliri_a, faizgeliri_o, tarih) VALUES (:donem, :faiz_a, :faiz_o, NOW())")->execute([
                'donem'  => $aktif_donem, 
                'faiz_a' => $anlik_net_faiz_a, 
                'faiz_o' => $anlik_net_faiz_o
            ]);

            if ($anlik_net_faiz_a > 0) {
                $db->prepare("INSERT INTO butce_akis (aktif_donem, kalem_adi, tutar, odenen_tutar, turu, is_locked, odenme_tarihi) VALUES (:donem, 'y faizgeliri_a', :tutar, :odenen, 'gelir', 1, NOW())")->execute([
                    'donem'  => $aktif_donem, 'tutar'  => $anlik_net_faiz_a, 'odenen' => $anlik_net_faiz_a
                ]);
            }

            if ($anlik_net_faiz_o > 0) {
                $db->prepare("INSERT INTO butce_akis (aktif_donem, kalem_adi, tutar, odenen_tutar, turu, is_locked, odenme_tarihi) VALUES (:donem, 'k faizgeliri_o', :tutar, :odenen, 'gelir', 1, NOW())")->execute([
                    'donem'  => $aktif_donem, 'tutar'  => $anlik_net_faiz_o, 'odenen' => $anlik_net_faiz_o
                ]);
                
                // Faiz cüzdanını bileşik büyüterek session'a mühürle
                $_SESSION['dinamik_faiz_kredi_butcesi'] = $dinamik_faiz_kredi_butcesi + $anlik_net_faiz_o;
            }

            try {
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $db->prepare("INSERT IGNORE INTO aylik_harcama_raporu (donem) VALUES (:donem)")->execute(['donem' => $aktif_donem]);
                $db->prepare("UPDATE aylik_harcama_raporu SET faiz_gelir = faiz_gelir + :faiz WHERE donem = :donem")->execute([
                    'faiz'  => $toplam_aktarilacak_faiz, 'donem' => $aktif_donem
                ]);
            } catch (PDOException $e) {
                exit("Veritabanı Hatası: " . $e->getMessage());
            }

            while (ob_get_level() > 0) { ob_end_clean(); }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            while (ob_get_level() > 0) { ob_end_clean(); }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status'  => 'error', 'message' => 'Aktarılacak canlı faiz geliri bulunamadı.']);
            exit;
        }
    }

    // 13. Faiz Güncelle
    if ($islem === 'faiz_guncelle') {
        echo json_encode([
            'status' => 'success',
            'message' => 'Finansal komuta merkezi parametreleri senkronize edildi.',
            'dinamik_anapara' => $isbank_o_faizsiz_ana_para
        ]);
        exit;
    }

 // =========================================================================
// 📌 14. DÖNEM DEVRET MOTORU (2 AY VADELİ TASLAK FORMÜLLÜ NİHAİ SÜRÜM)
// =========================================================================
if ($islem === 'donem_devret') {
    // Tarayıcı çıktısını temizle ve saf JSON modunu garanti et
    while (ob_get_level()) { ob_end_clean(); }
    header('Content-Type: application/json');

    // 1. 🎯 MATEMATİKSEL REFORM: Canlı bütçe (+1 ay) ve Taslak şablon (+2 ay) dönemlerini hesapla
    list($current_y, $current_m) = explode('-', $aktif_donem);
    $c_year = intval($current_y); $c_month = intval($current_m);

    // Canlı Bütçe Hedefi: +1 Ay Sonrası (Örn: 2026-07 -> 2026-08)
    $m_canli = $c_month + 1; $y_canli = $c_year;
    if ($m_canli > 12) { $m_canli = 1; $y_canli++; }
    $yeni_canli_donem = $y_canli . '-' . sprintf("%02d", $m_canli);

    // Taslak Şablon Hedefi: +2 Ay Sonrası (Örn: 2026-07 -> 2026-09 Eylül)
    $m_taslak = $c_month + 2; $y_taslak = $c_year;
    if ($m_taslak > 12) { 
        $m_taslak = $m_taslak - 12; 
        $y_taslak++; 
    }
    $yeni_taslak_donem = $y_taslak . '-' . sprintf("%02d", $m_taslak);

    // 2. Havuzların mevcut durum fotoğraflarını çek
    $q_havuz = $db->query("SELECT * FROM finansal_havuzlar")->fetchAll(PDO::FETCH_ASSOC);
    $h_anapara = []; $h_bloke = [];
    foreach ($q_havuz as $h) {
        $h_anapara[$h['havuz_kodu']] = floatval($h['anapara'] ?? 0);
        $h_bloke[$h['havuz_kodu']] = floatval($h['bloke_tutar'] ?? 0);
    }
    $isbank_a = $h_anapara['isbank_a'] ?? 0.00;
    $isbank_o = $h_anapara['isbank_o'] ?? 0.00;
    $vakif_o  = $h_anapara['vakif_o'] ?? 0.00;
    $nakit    = $h_anapara['nakit'] ?? 0.00;
    $v_bloke  = $h_bloke['vakif_o'] ?? 0.00;
    $n_bloke  = $h_bloke['nakit'] ?? 0.00;

    // 3. Mevcut ay bitmeden hemen önce tüm hareketleri oku ve cüzdan eritmelerini hesapla
    $mevcut_akis_sorgu = $db->prepare("SELECT * FROM butce_akis WHERE aktif_donem = ?");
    $mevcut_akis_sorgu->execute([$aktif_donem]);
    $mevcut_akis_verileri = $mevcut_akis_sorgu->fetchAll(PDO::FETCH_ASSOC);

    foreach ($mevcut_akis_verileri as $satir) {
        $odenan = floatval($satir['odenen_tutar'] ?? 0);
        $isim = strval($satir['kalem_adi'] ?? '');
        $turu = strval($satir['turu'] ?? 'gider');
        if ($odenan <= 0) continue;

        if ($turu === 'gider') {
            if (strpos($isim, 'İşbank Yatırım') !== false || strpos($isim, 'İşbank A') !== false) { $isbank_a -= $odenan; }
            elseif (strpos($isim, 'Vakıfbank') !== false) { $vakif_o -= $odenan; }
            elseif (strpos($isim, 'Nakit') !== false || strpos($isim, 'Elden') !== false) { $nakit -= $odenan; }
            else { $isbank_o -= $odenan; }
        } elseif ($turu === 'gelir') {
            if (strpos($isim, 'Maaş') !== false || strpos($isim, 'EFT') !== false) { $isbank_a += $odenan; }
            else { $nakit += $odenan; }
        }
    }

    // 4. Acil durumlar için ham MySQL INSERT yedek stringini sıfır hata ile üret
    $mysql_backup_query = "";
    if (!empty($mevcut_akis_verileri)) {
        $sql_values = [];
        foreach ($mevcut_akis_verileri as $satir) {
            $k_adi  = addslashes($satir['kalem_adi'] ?? '');
            $tutar  = floatval($satir['tutar'] ?? 0);
            $o_tut  = floatval($satir['odenen_tutar'] ?? 0);
            $t_turu = addslashes($satir['turu'] ?? 'gider');
            $locked = intval($satir['is_locked'] ?? 0);
            $i_gunu = intval($satir['islem_gunu'] ?? 1);
            $sql_values[] = "('{$aktif_donem}', '{$k_adi}', {$tutar}, {$o_tut}, '{$t_turu}', {$locked}, {$i_gunu}, NULL)";
        }
        $mysql_backup_query = "INSERT INTO `butce_akis` (`aktif_donem`, `kalem_adi`, `tutar`, `odenen_tutar`, `turu`, `is_locked`, `islem_gunu`, `odenme_tarihi`) VALUES \n" . implode(",\n", $sql_values) . ";";
    }

    // 5. Kara Kutu Log Mühürlemesi
    $log_detay = json_encode([
        'havuz_durumlari' => ['isbank_a' => $isbank_a, 'isbank_o' => $isbank_o, 'vakif_o' => $vakif_o, 'nakit' => $nakit],
        'butce_kalemleri' => $mevcut_akis_verileri,
        'mysql' => $mysql_backup_query
    ], JSON_UNESCAPED_UNICODE);
    
    $log_stmt = $db->prepare("INSERT INTO logs (islem_donemi, islem_tipi, detay) VALUES (?, 'DONEM_DEVIR_YEDEGI', ?)");
    $log_stmt->execute([$aktif_donem, $log_detay]);

    // 6. Dinamik Başlangıç Reformu: Hesaplanan paraları veritabanında yeni ayın temiz anaparası yap
    $havuz_devir_stmt = $db->prepare("UPDATE finansal_havuzlar SET anapara = :yeni_anapara, nema = 0.00, bloke_tutar = :yeni_bloke WHERE havuz_kodu = :kod");
    $havuz_devir_stmt->execute(['yeni_anapara' => $isbank_a, 'yeni_bloke' => 0.00, 'kod' => 'isbank_a']);
    $havuz_devir_stmt->execute(['yeni_anapara' => $isbank_o, 'yeni_bloke' => 0.00, 'kod' => 'isbank_o']);
    $havuz_devir_stmt->execute(['yeni_anapara' => $vakif_o,  'yeni_bloke' => $v_bloke, 'kod' => 'vakif_o']);
    $havuz_devir_stmt->execute(['yeni_anapara' => $nakit,    'yeni_bloke' => $n_bloke, 'kod' => 'nakit']);

    // 7. Kapanan döneme ait eski canlı harcamaları tamamen sil
    $db->prepare("DELETE FROM butce_akis WHERE aktif_donem = :donem")->execute(['donem' => $aktif_donem]);

    // 8. REFORM: butce_akis_last tablosunda önceden hazır bekleyen canlı hedef ayın (2026-08) şablonlarını, asıl bütçeye naklet!
    $db->prepare("
        INSERT INTO butce_akis (aktif_donem, kalem_adi, tutar, odenen_tutar, turu, is_locked, islem_gunu, odenme_tarihi)
        SELECT :yeni_canli_1, kalem_adi, tutar, 0.00, turu, 0, islem_gunu, NULL 
        FROM butce_akis_last 
        WHERE aktif_donem = :yeni_canli_2
    ")->execute([
        'yeni_canli_1' => $yeni_canli_donem,
        'yeni_canli_2' => $yeni_canli_donem
    ]);

    // 🔥 OTOMATİK KLONLAMA DİŞLİSİ: Canlıya nakledilen bu şablonu (2026-08), bir sonraki devirde (Ağustos biterken) 
    // sistemin boş kalmaması için +2 ay sonrasının (2026-09 Eylül) taslak bütçesi olarak otomatik kopyala!
    $db->prepare("
        INSERT INTO butce_akis_last (aktif_donem, kalem_adi, tutar, turu, islem_gunu)
        SELECT :yeni_taslak, kalem_adi, tutar, turu, islem_gunu 
        FROM butce_akis_last 
        WHERE aktif_donem = :donem_canli
    ")->execute([
        'yeni_taslak' => $yeni_taslak_donem,
        'donem_canli' => $yeni_canli_donem
    ]);

    // 9. REFORM: Canlıya nakledilen ve klonlanan eski şablonu (2026-08) last tablosundan temizle
    $db->prepare("DELETE FROM butce_akis_last WHERE aktif_donem = :donem_canli")->execute(['donem_canli' => $yeni_canli_donem]);

    // 10. Kredi kartları asıl tablosunu senkronize et ve last alanları sıfırla
    $cc_last_data = $db->query("SELECT * FROM kredi_kartlari_last")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cc_last_data as $l_kart) {
        $db->prepare("UPDATE kredi_kartlari SET donem_ici = :dnm, provizyon = :prv, doviz_borcu = :dvz WHERE kart_adi = :k_adi")
           ->execute(['dnm' => $l_kart['donem_ici'], 'prv' => $l_kart['provizyon'], 'dvz' => $l_kart['doviz_borcu'], 'k_adi' => $l_kart['kart_adi']]);
    }
    $db->query("UPDATE kredi_kartlari_last SET donem_ici = 0.00, provizyon = 0.00, doviz_borcu = 0.00, kesinlesen_ekstre = 0.00");

    // 11. Tarayıcı oturumunu resmen yeni döneme (+1 ay sonrasın olan Ağustos'a) mühürle
    $_SESSION['aktif_donem'] = $yeni_canli_donem;
    
    // JS'e tertemiz başarı yanıtını döndür
    echo json_encode(['status' => 'success']);
    exit;
}










} 





// <--- POST İSTEKLERİNİ YÖNETEN ANA IF SARMALININ KAPANIŞI (POST KİLİTLENDİ)


// 📌 KLONLAMA MOTORU SÜRECİ (POST SARMALI DIŞI)
$stmt_kalemler = $db->prepare("SELECT * FROM butce_akis WHERE aktif_donem = :donem ORDER BY id ASC");
$stmt_kalemler->execute(['donem' => $aktif_donem]);
$kalemler = $stmt_kalemler->fetchAll(PDO::FETCH_ASSOC);

// Eğer yeni dönem (Örn: 2026-08) veritabanında tamamen boşsa klonlama motorunu çalıştır
if (empty($kalemler)) {
    list($g_yil, $g_ay) = explode('-', $aktif_donem);
    $gecmis_ay = intval($g_ay) - 1; $gecmis_yil = intval($g_yil);
    if ($gecmis_ay < 1) { $gecmis_ay = 12; $gecmis_yil--; }
    $gecmis_donem_string = $gecmis_yil . '-' . sprintf("%02d", $gecmis_ay);

    // Geçmiş aydan nakit girişleri ve transfer fonları dışındaki kalıcı şablon bütçe kalemleri çekilir
    $stmt_gecmis = $db->prepare("SELECT * FROM butce_akis WHERE aktif_donem = :gecis AND kalem_adi != 'Evdeki Nakit Girişi' AND kalem_adi NOT LIKE '%Havuz Transfer Fonu%'");
    $stmt_gecmis->execute(['gecis' => $gecmis_donem_string]);
    $gecmis_kalemler = $stmt_gecmis->fetchAll(PDO::FETCH_ASSOC);

    list($aktif_yil_hesap, $aktif_ay_hesap) = explode('-', $aktif_donem);
    $a_yil_int = intval($aktif_yil_hesap); $a_ay_int = intval($aktif_ay_hesap);

    $su_birim_fiyat = 140.00; $yeni_pazartesi_sayisi = 0;
    
    // Herhangi bir takvim extension bağımlılığı olmadan DateTime sınıfı ile Pazartesi gün tarama motoru
    $tarih_nesnesi = new DateTime("$a_yil_int-$a_ay_int-01");
    $yeni_ay_gun_sayisi = intval($tarih_nesnesi->format('t'));

    for ($d = 1; $d <= $yeni_ay_gun_sayisi; $d++) {
        $kontrol_tarihi = new DateTime("$a_yil_int-$a_ay_int-$d");
        if ($kontrol_tarihi->format('N') == 1) { $yeni_pazartesi_sayisi++; }
    }
    
    // Pazartesi tarama sonucuna göre dinamik su bütçesi enjeksiyonu
    $yeni_ay_dinamik_su_tutari = $yeni_pazartesi_sayisi * $su_birim_fiyat * 2;
    if ($yeni_ay_dinamik_su_tutari <= 0) { $yeni_ay_dinamik_su_tutari = 1120.00; }

    $stmt_kontrol = $db->prepare("SELECT COUNT(*) FROM butce_akis WHERE aktif_donem = :donem AND kalem_adi = :adi");

    foreach ($gecmis_kalemler as $g_kalem) {
        $stmt_kontrol->execute(['donem' => $aktif_donem, 'adi' => $g_kalem['kalem_adi']]);
        if ($stmt_kontrol->fetchColumn() == 0) {
            $isim = trim($g_kalem['kalem_adi']); $l_isim_kucuk = mb_strtolower($isim, 'UTF-8');
            
            // Eğer kalem Damacana veya Su ise geçmiş aydaki değeri ezip yeni ayın formül bütçesini atıyoruz
            if (strpos($l_isim_kucuk, 'damacana') !== false || strpos($l_isim_kucuk, 'içme suyu') !== false || strpos($l_isim_kucuk, 'su') !== false) {
                $enjekte_tutar = $yeni_ay_dinamik_su_tutari;
            } else { 
                $enjekte_tutar = floatval($g_kalem['tutar']); 
            }

            $db->prepare("INSERT INTO butce_akis (aktif_donem, kalem_adi, tutar, odenen_tutar, turu, is_locked) VALUES (:donem, :adi, :tutar, 0.00, :turu, 0)")
               ->execute(['donem' => $aktif_donem, 'adi' => $g_kalem['kalem_adi'], 'tutar' => $enjekte_tutar, 'turu' => $g_kalem['turu']]);
        }
    }
    // Listeyi yeni döneme ait satırlarla tam senkron güncelle
    $stmt_kalemler->execute(['donem' => $aktif_donem]);
    $kalemler = $stmt_kalemler->fetchAll(PDO::FETCH_ASSOC);
}



// %100 GARANTİLİ NERGİZ CEP VE KALICI AİDAT VERİ GÜVENCESİ REFORMLARI
$nergiz_kontrol = false;
$aidat_kontrol = false;
foreach ($kalemler as $kalem) {
    $k_k_adi = trim($kalem['kalem_adi']);
    if ($k_k_adi === 'Nergiz Cep') { $nergiz_kontrol = true; }
    if ($k_k_adi === 'Aidat')      { $aidat_kontrol = true; }
}

if (!$nergiz_kontrol) {
    $db->prepare("INSERT INTO butce_akis (aktif_donem, kalem_adi, tutar, odenen_tutar, turu) VALUES (:donem, 'Nergiz Cep', 220.00, 0.00, 'gider')")->execute(['donem' => $aktif_donem]);
}
if (!$aidat_kontrol) {
    $db->prepare("INSERT INTO butce_akis (aktif_donem, kalem_adi, tutar, odenen_tutar, turu) VALUES (:donem, 'Aidat', 1000.00, 0.00, 'gider')")->execute(['donem' => $aktif_donem]);
}

if (!$nergiz_kontrol || !$aidat_kontrol) {
    $stmt_kalemler->execute(['donem' => $aktif_donem]);
    $kalemler = $stmt_kalemler->fetchAll(PDO::FETCH_ASSOC);
}

// Görünüme (View) gidecek olan gelir ve gider dizilerini sızıntısız ayrıştırma
$gelirler = []; $giderler = [];
foreach ($kalemler as $kalem) {
    if (trim($kalem['aktif_donem']) === trim($aktif_donem)) {
        if ($kalem['turu'] === 'gelir') { $gelirler[] = $kalem; } else { $giderler[] = $kalem; }
    }
}

// 💳 SAĞ SÜTUN: KREDİ KARTI TAKSİT VE METRİK VERİLERİNİN ÇEKİLMESİ
$db_taksitler = $db->query("SELECT * FROM cc_taksitler ORDER BY ay_index ASC")->fetchAll(PDO::FETCH_ASSOC);
$db_kart_metrikleri = $db->query("SELECT * FROM kredi_kartlari")->fetchAll(PDO::FETCH_ASSOC);
$cc_meta = [];
foreach ($db_kart_metrikleri as $kart) {
    $k_adi = ($kart['kart_adi'] === 'vakifbank') ? 'vakif' : 'isbank'; 
    $cc_meta[$k_adi . '_donem_ici']   = floatval($kart['donem_ici']);
    $cc_meta[$k_adi . '_provizyon']   = floatval($kart['provizyon']);
    $cc_meta[$k_adi . '_doviz']       = floatval($kart['doviz_borcu']);
    $cc_meta[$k_adi . '_ekstre']      = floatval($kart['kesinlesen_ekstre']);
}


foreach(['isbank', 'vakif'] as $p) {
    $cc_meta[$p.'_donem_ici']  = $cc_meta[$p.'_donem_ici'] ?? 0.00;
    $cc_meta[$p.'_provizyon']  = $cc_meta[$p.'_provizyon'] ?? 0.00;
    $cc_meta[$p.'_doviz']      = $cc_meta[$p.'_doviz'] ?? 0.00;
    $cc_meta[$p.'_ekstre']     = $cc_meta[$p.'_ekstre'] ?? 0.00;
}

$isbank_aktif_taksit = 0; $vakif_aktif_taksit = 0;
$isbank_kalan_taksit = 0; $vakif_kalan_taksit = 0;

foreach ($db_taksitler as $t_satir) {
    $idx = intval($t_satir['ay_index']);
    if ($idx === $aktif_ay_index) {
        $isbank_aktif_taksit = floatval($t_satir['isbank_tutar']);
        $vakif_aktif_taksit  = floatval($t_satir['vakif_tutar']);
    } elseif ($idx > $aktif_ay_index) {
        $isbank_kalan_taksit += floatval($t_satir['isbank_tutar']);
        $vakif_kalan_taksit  += floatval($t_satir['vakif_tutar']);
    }
}


// =================================================================================
// 🏛️ PART 3: REEL LİKİDİTE FAİZ MATRİSİ VE NİHAİ ADAPTÖR MÜHÜRLEME KATMANI
// =================================================================================

$temiz_faiz_donem = trim($aktif_donem);

// 1. Veritabanına geçmişte aktarılmış (tahsili yapılmış) faizlerin toplamını tam eşleşmeyle oku
$db_gecmis_faiz_a = $db->prepare("SELECT SUM(faizgeliri_a) FROM faiz_gelirleri WHERE TRIM(donem) = :donem");
$db_gecmis_faiz_a->execute(['donem' => $temiz_faiz_donem]);
$toplam_eski_faiz_a = floatval($db_gecmis_faiz_a->fetchColumn());

$db_gecmis_faiz_o = $db->prepare("SELECT SUM(faizgeliri_o) FROM faiz_gelirleri WHERE TRIM(donem) = :donem");
$db_gecmis_faiz_o->execute(['donem' => $temiz_faiz_donem]);
$toplam_eski_faiz_o = floatval($db_gecmis_faiz_o->fetchColumn());

$toplam_aktarilmis_eski_faiz = $toplam_eski_faiz_a + $toplam_eski_faiz_o;


// 🎯 FİNANSAL MATEMATİK: Döngü içerisinde faizler eklenip krediler/harcamalar düşüldüğü için,
// elde kalan ham anaparalar bizim nihai faizsiz anapara bariyerimizdir.
$isbank_k_faizsiz_ana_para = max(0, $isbank_a_anapara);
$isbank_o_faizsiz_ana_para = $isbank_o_anapara;

// Eğer simülasyon başlangıç anındaysa, hiçbir şey ödenmediyse ve faiz birikmediyse input bütçesini göster
if ($isbank_o_anapara == 0 && !$kredi_odendi && $toplam_eski_faiz_o == 0) {
    $isbank_o_faizsiz_ana_para = floatval($dinamik_faiz_kredi_butcesi);
}


// 2. Sadece içinde bulunulan saniyedeki anlık canlı nemaları (geleceğe yönelik) hesapla
$ana_havuz_faiz    = ($isbank_k_faizsiz_ana_para > 0) ? ($isbank_k_faizsiz_ana_para * ($faiz_orani / 100) / 365) * $faiz_gun_sayisi : 0;
$kredi_havuzu_faiz = ($isbank_o_faizsiz_ana_para > 0 && !$kredi_odendi) ? ($isbank_o_faizsiz_ana_para * ($faiz_orani / 100) / 365) * $faiz_gun_sayisi : 0;

// Net nemaların mühürlenmesi (Stopaj Kesintili)
$isbank_a_nema = $ana_havuz_faiz * (1 - ($faiz_stopaj / 100));
$isbank_o_nema = $kredi_havuzu_faiz * (1 - ($faiz_stopaj / 100));

$toplam_canli_faiz_kazanci = $isbank_a_nema + $isbank_o_nema;


// 🎯 MÜHÜR: Yıllık Excel matrisinin (Blok 11) hata vermeden çalışması için nihai mühürlü faizi bağla
$harcama_tablosu_nihai_faizi = $toplam_aktarilmis_eski_faiz + $toplam_canli_faiz_kazanci;



// =========================================================================
// GÜVENLİK YAMASI: INTERAKTİF FOREX SATIN ALMA DÜŞÜM ADAPTÖRÜ
// =========================================================================
// İlgili döneme ait toplam yapılan forex satın alma maliyetini DB'den çekip 
// ana paradan kalıcı olarak düşüyoruz. Böylece sayfa yenilense de bakiye hatasız azalır.
try {
    $aktif_donem_kontrol = isset($aktif_donem) ? $aktif_donem : date('Y-m');
    $fx_bakiye_sorgu = $db->prepare("SELECT IFNULL(`yatirim_tutari`, 0.0000) as `toplam_harcanan` FROM `aylik_harcama_raporu` WHERE `donem` = ? LIMIT 1");
    $fx_bakiye_sorgu->execute([$aktif_donem_kontrol]);
    $fx_bakiye_row = $fx_bakiye_sorgu->fetch(PDO::FETCH_ASSOC);
    if ($fx_bakiye_row && floatval($fx_bakiye_row['toplam_harcanan']) > 0) {
        // Harcanan para, sayfa yenilendiğinde yukarıdaki eski kodların hesapladığı ana paradan zorla düşülüyor!
        $isbank_k_faizsiz_ana_para = $isbank_k_faizsiz_ana_para - floatval($fx_bakiye_row['toplam_harcanan']);
    }
} catch (PDOException $e) { }


// 🎯 NİHAİ MÜHÜRLÜ BAKİYELER (Sizin mevcut kod alanınız bu satırdan itibaren aynen devam edecek)
$isbank_a_bakiye  = $isbank_k_faizsiz_ana_para + $isbank_a_nema;
$isbank_o_bakiye  = $isbank_o_faizsiz_ana_para + $isbank_o_nema;
$vakif_o_bakiye   = $vakif_o_anapara; 
$nakit_bakiye     = $nakit_anapara;

$isbank_k_bakiye  = $isbank_a_bakiye;


// =================================================================================
// 🛡️ REFACTOR: VIEW KATMANI ADAPTÖRÜ VE NULL DEĞER KORUMA BARİYERİ (PHP 8+ UYUMLU)
// =================================================================================
$net_kredi_faiz_t_php          = floatval($isbank_o_nema);
$net_ana_havuz_faiz_php         = floatval($isbank_a_nema);
$zorunlu_fatura_bloke_tutari   = floatval($vakif_bloke);
$vakifbank_bakiye              = floatval($vakif_o_bakiye);
$zorunlu_su_bloke_tutari       = floatval($nakit_bloke);



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
                $yillik_matris[$m]['faiz_gelir'] = $harcama_tablosu_nihai_faizi; 
            }
        }
        
        foreach ($tepe_toplamlar as $s_key => $v) { 
            $tepe_toplamlar[$s_key] += floatval($yillik_matris[$m][$s_key] ?? 0); 
        }
    }
}





// =========================================================================
// %100 SAF DİNAMİK HER VARLIKTA BİNDE 2 VERGİ ENTEGRELİ CONTROLLER
// =========================================================================

$kur_servis_management = ForexExchange::getLiveRates();
$live_kurlar = $kur_servis_management['kurlar']; 

$canli_ana_yatirim_bakiye_tl = floatval($isbank_k_faizsiz_ana_para);



// =========================================================================
// %100 INTERAKTİF SATIN ALMA VE DEĞİŞKEN TABANLI ANLIK HAVUZ DÜŞÜMÜ (POST)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'interaktif_forex_satinal') {
    
    $modal_doviz_cinsi = filter_input(INPUT_POST, 'modal_doviz_cinsi', FILTER_SANITIZE_SPECIAL_CHARS);
    $modal_miktar      = filter_input(INPUT_POST, 'modal_miktar', FILTER_VALIDATE_FLOAT);
    $modal_manuel_kur  = filter_input(INPUT_POST, 'modal_manuel_kur', FILTER_VALIDATE_FLOAT);
    
    // Panelinizde seçili olan cari dönemi (Örn: 2026-07) baz alıyoruz
    $hedef_butce_donemi = isset($aktif_donem) ? $aktif_donem : date('Y-m');


    $canli_secilen_kur = isset($live_kurlar[$modal_doviz_cinsi]) ? (float)$live_kurlar[$modal_doviz_cinsi] : 0;
    $nihai_islem_kuru  = ($modal_manuel_kur > 0) ? $modal_manuel_kur : $canli_secilen_kur;

    if ($modal_doviz_cinsi && $modal_miktar > 0 && $nihai_islem_kuru > 0) {
        try {
            $db->beginTransaction();

            // 1. İşlem: Toplam maliyet hesaplama (%0.2 kambiyo vergisi dahil)
            $net_tutar = $modal_miktar * $nihai_islem_kuru;
            $vergi_tutari = $net_tutar * 0.0020;
            $toplam_maliyet = $net_tutar + $vergi_tutari;

            // Güvenlik Kilidi: Alınacak tutar, kilitli faizsiz ana paradan küçük veya eşit mi?
            if ($toplam_maliyet <= $canli_ana_yatirim_bakiye_tl) {

                // ADIM 1: forex_islemler tablosuna yeni alımı canlı kur ve vergisiyle mühürle
                $ins_sorgu = $db->prepare("INSERT INTO `forex_islemler` 
                    (`islem_tarihi`, `islem_turu`, `doviz_cinsi`, `miktar`, `doviz_kuru`, `tutar`, `vergi`, `maliyet`) 
                    VALUES (CURDATE(), 2, ?, ?, ?, ?, ?, ?)");
                $ins_sorgu->execute([
                    $modal_doviz_cinsi, 
                    $modal_miktar, 
                    $nihai_islem_kuru, 
                    $net_tutar, 
                    $vergi_tutari, 
                    $toplam_maliyet
                ]);

                // ADIM 2: Harcanan TL tutarı, o döneme ait aylik_harcama_raporu -> yatirim_tutari kolonuna kümülatif eklenir
                $upsert_harcama = $db->prepare("INSERT INTO `aylik_harcama_raporu` (`donem`, `yatirim_tutari`) 
                    VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE `yatirim_tutari` = `yatirim_tutari` + ?");
                $upsert_harcama->execute([
                    $hedef_butce_donemi, 
                    $toplam_maliyet, 
                    $toplam_maliyet
                ]);

                $db->commit();

                // =========================================================================
                // 🎯 KESİN ÇÖZÜM: TÜM ÜST/ALT DEĞİŞKENLERİ MANUEL OLARAK ANINDA GÜNCELLE
                // =========================================================================
                // Harcanan toplam maliyet tutarı, sizin faizsiz ana paranızdan anında düşülür
                $isbank_k_faizsiz_ana_para = $isbank_k_faizsiz_ana_para - $toplam_maliyet;
                
                // Bağlı olan nihai mühürlü bakiyeler zincirleme olarak anında güncelleniyor
                $isbank_a_bakiye  = $isbank_k_faizsiz_ana_para + $isbank_a_nema;
                
                // HATA DÜZELTİLDİ: Sizin arayüze basılan ana mühür değişkeniniz el ile güncelleniyor!
                $isbank_k_bakiye  = $isbank_a_bakiye;
                
                // JavaScript ve View katmanına giden emniyet sınırları anında revize ediliyor
                $canli_ana_yatirim_bakiye_tl = floatval($isbank_k_faizsiz_ana_para);
                $yilsal_net_tasarruf_tl      = $canli_ana_yatirim_bakiye_tl;

                // Formun çift gönderilmesini önlemek amacıyla sayfayı yenileyerek işlemi mühürle
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            } else {
                $db->rollBack();
            }
        } catch (Exception $e) {
            $db->rollBack();
        }
    }
}





// =================================================================================
// 🛡️ VIEW KATMANI ENTEGRASYON KÖPRÜLERİ (GÜNCELLENEN KART MATRİSİ)
// =================================================================================

// 🎯 ÇÖZÜM 1: HTML satır 532'deki Kredi Faiz Bütçesi inputunun eski ismini yeni güvenli tabana bağla
$evKredisiTutariStandart2 = $kredi_input_butcesi;

// 🎯 ÇÖZÜM 2: HTML satır 415 ve kart alanları için kümülatif canlı kart borç matrisleri
$isbank_card_borcu = $cc_meta['isbank_donem_ici'] + $cc_meta['isbank_provizyon'] + $cc_meta['isbank_doviz'] + $cc_meta['isbank_ekstre'] + $isbank_aktif_taksit;
$vakif_card_borcu  = $cc_meta['vakif_donem_ici'] + $cc_meta['vakif_provizyon'] + $cc_meta['vakif_doviz'] + $cc_meta['vakif_ekstre'] + $vakif_aktif_taksit;


// 🏛️ GÖRÜNÜM KATMANININ YÜKLENMESİ
require admin_view('management');

