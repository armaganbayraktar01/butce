<?php
// =========================================================================
// BÖLÜM 1: AJAX İSTEĞİ YAKALAMA (Öde / İptal Et)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) &&$_POST['action'] === 'toggle_lock') {
    header('Content-Type: application/json');
    $id = intval($_POST['id'] ?? 0);
    $isLocked = intval($_POST['is_locked'] ?? 0);

    try {
        $stmt =$db->prepare("UPDATE ev_alim_kredi SET is_locked = :is_locked WHERE id = :id");
        $success =$stmt->execute([
            ':is_locked' => $isLocked,
            ':id'        => $id
        ]);
        echo json_encode(['success' => $success]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// =========================================================================
// BÖLÜM 2: VERİ ÇEKME VE DİNAMİK ANAPARA / FAİZ HESAPLAMA
// =========================================================================
$taksitler = [];$toplamKredi = 6392728.60;
$baslangicAnapara = 1900000.00;
$toplamFaiz = $toplamKredi -$baslangicAnapara;

$odenenToplam = 0.00;
$odenenAnapara = 0.00;
$odenenFaiz = 0.00;
$odenenTaksitSayisi = 0;

try {
    $stmt =$db->prepare("SELECT * FROM ev_alim_kredi ORDER BY taksit_no ASC");
    $stmt->execute();
    $rawTaksitler =$stmt->fetchAll(PDO::FETCH_ASSOC);

    $gecerliAnapara =$baslangicAnapara;
    $aylikOran = 0.0269; // %2.69 İş Bankası Faiz Oranı

    foreach ($rawTaksitler as $index =>$item) {
        $taksitTutari = (float)($item['taksit_tutari'] ?? 0);
        $isLocked = ((int)($item['is_locked'] ?? 0) === 1);

        // Faiz hesabı
        $buAykiFaiz = $gecerliAnapara * $aylikOran;
        
        // İlk taksitte anaparanın eksiye düşmesini engelleme mantığı
        $buAykiAnapara = $taksitTutari -$buAykiFaiz;
        if ($buAykiAnapara < 0) {$buAykiAnapara = 0;
            $buAykiFaiz =$taksitTutari;
        }

        // Kalan anapara güncellemesi
        $gecerliAnapara = max(0, $gecerliAnapara -$buAykiAnapara);

        if ($isLocked) {
            $odenenToplam +=$taksitTutari;
            $odenenAnapara +=$buAykiAnapara;
            $odenenFaiz +=$buAykiFaiz;
            $odenenTaksitSayisi++;
        }

        $item['anapara_payi'] =$buAykiAnapara;
        $item['faiz_payi'] =$buAykiFaiz;
        $item['kalan_anapara'] =$gecerliAnapara;

        $taksitler[] =$item;
    }

} catch (PDOException $e) {
    error_log("Kredi Veritabanı Hatası: " . $e->getMessage());
}

$kalanBorc = $toplamKredi -$odenenToplam;
$kalanAnapara = max(0, $baslangicAnapara -$odenenAnapara);
$kalanFaiz = max(0,$kalanBorc - $kalanAnapara);$toplamTaksit = count($taksitler);$kalanTaksit = $toplamTaksit -$odenenTaksitSayisi;

// =========================================================================
// BÖLÜM 3: GAYRİMENKUL ALIM VERİLERİ (GELİR / GİDER HESAPLAMA)
// =========================================================================
$ev_islemleri = [];$ev_toplam_giris = 0.00; 
$ev_toplam_cikis = 0.00;

try {
    $sorgu =$db->prepare("SELECT * FROM `ev_alim` ORDER BY `id` ASC");
    $sorgu->execute();
    $gecici_liste =$sorgu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Ev Alım Veritabanı Hatası: " . $e->getMessage());$gecici_liste = [];
}

if (!empty($gecici_liste)) {
    foreach ($gecici_liste as$islem) {
        $tutar = (float)($islem['tutar'] ?? 0);
        if ($tutar > 0) { 
            $ev_toplam_giris +=$tutar; 
        } else { 
            $ev_toplam_cikis += abs($tutar); 
        }
    }
    $ev_islemleri =$gecici_liste;
}
$ev_kalan_bakiye = $ev_toplam_giris -$ev_toplam_cikis;

require admin_view("yatirim_arsiv_ev");