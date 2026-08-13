<?php

function cut_text($text, $text_start = 0, $text_end = 180){
    
    $text = strip_tags(htmlspecialchars_decode($text));
    $text_lenght = strlen($text);

    if ($text_lenght > $text_end){

        $text = mb_substr($text, $text_start, $text_end, 'UTF8') . '...';
        $text_cut = strrchr($text, " ");
        $text = str_replace($text_cut, " ...", $text);
    }

    return $text;
}

function safe($value) {
    return htmlspecialchars($value ?? '');
}

function printr($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit;
}

/**
 * 🎯 REFORM: HAFTALIK BÜTÇE DİLİMİ PAZARTESİ SAYAÇ MOTORU
 * Aktif dönemin 15. gününün haftasından, sonraki ayın 15. gününün haftasına kadar olan Pazartesi sayısını hesaplar.
 * 
 * @param string|null $aktif_donem 'YYYY-MM' formatında dönem metni (Boşsa cari ay alınır)
 * @return int Toplam Pazartesi sayısı (Hafta dilimi sayısı)
 */
function damacanaMiktari($aktif_donem = null) {
    // 1. ADIM: Aktif dönemi süz ve yıl/ay parçalarına ayır
    if (empty($aktif_donem)) {
        $aktif_donem = $_SESSION['aktif_donem'] ?? date('Y-m');
    }
    
    $parcalar = explode('-', $aktif_donem);
    $yil = intval($parcalar[0]);
    $ay  = intval($parcalar[1]);

    // 2. ADIM: Bu ayın 15'i ve bir sonraki ayın 15'i için DateTime nesnelerini mühürle
    $buAy15 = new DateTime("$yil-$ay-15 00:00:00");
    
    // Ay +1 lojistiği (Aralık ayında yılın otomatik devretmesi için DateTime modify kullanılır)
    $sonrakiAy15 = clone $buAy15;
    $sonrakiAy15->modify('+1 month');

    // 3. ADIM: 15'lerin ait olduğu haftaların başlangıç günlerini (Pazartesi) bulma zırhı
    // ISO-8601 standardına göre 'N' parametresi Pazartesi için 1, Pazar için 7 döner.
    $buAyHaftaBaslangic = clone $buAy15;
    $w1 = intval($buAyHaftaBaslangic->format('N'));
    if ($w1 > 1) {
        $buAyHaftaBaslangic->modify('-' . ($w1 - 1) . ' days'); // O haftanın Pazartesi gününe geri sar
    }

    $sonrakiAyHaftaBaslangic = clone $sonrakiAy15;
    $w2 = intval($sonrakiAyHaftaBaslangic->format('N'));
    if ($w2 > 1) {
        $sonrakiAyHaftaBaslangic->modify('-' . ($w2 - 1) . ' days'); // Sonraki haftanın Pazartesi gününe geri sar
    }

    // 4. ADIM: İki Pazartesi tarihi arasında döngü çalıştırarak kaç tane Pazartesi olduğunu kuruşu kuruşuna say
    $pazartesiSayaci = 0;
    $taramaGunu = clone $buAyHaftaBaslangic;

    // Hedef haftanın Pazartesi gününe kadar (o gün hariç olacak şekilde) gün gün değil, hafta hafta (7'şer gün) atlayarak sayar
    while ($taramaGunu < $sonrakiAyHaftaBaslangic) {
        $pazartesiSayaci++;
        $taramaGunu->modify('+7 days'); // Sonraki haftanın Pazartesi gününe zıpla
    }

    return $pazartesiSayaci;
}

function netFaizHesapla ($anapara, $faiz_orani, $faiz_gun_sayisi, $faiz_stopaj) {
    
    $faiz_net_carpan = 0;

    // Faiz oranı ve gün sayısı sıfırdan büyükse hesaplama yap
    if ($faiz_gun_sayisi > 0 && $faiz_orani > 0) {
        $brut_faiz = ($faiz_orani / 100) * ($faiz_gun_sayisi / 365);
        $faiz_net_carpan = $brut_faiz * (1 - ($faiz_stopaj / 100));
    }

    // Nihai tutarı hesapla ve döndür
    return $anapara * $faiz_net_carpan;
}


function faizHesapla ($anapara, $faiz_orani, $faiz_gun_sayisi, $faiz_stopaj) {
    
    $faiz_net_carpan = 0;

    // Faiz oranı ve gün sayısı sıfırdan büyükse hesaplama yap
    if ($faiz_gun_sayisi > 0 && $faiz_orani > 0) {
        $brut_faiz = ($faiz_orani / 100) * ($faiz_gun_sayisi / 365);
        $faiz_net_carpan = $brut_faiz * (1 - ($faiz_stopaj / 100));
    }

    // Nihai tutarı hesapla ve döndür
    return $anapara * (1 + $faiz_net_carpan);
}
