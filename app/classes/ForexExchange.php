<?php

// =========================================================================
//  BOT SINIF TANIMLAMASI (ALIŞ VE SATIŞ KURLARININ PARSE EDİLMESİ)
// =========================================================================
class ForexExchange {

    public static function fetchIsBankKurlari() {
        $url = "https://kur.altin.in/banka/isbank";
        
        $options = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
            ]
        ];
        
        $context = stream_context_create($options);
        $html = @file_get_contents($url, false, $context);
        
        // Güvenli taban yedek kurlar (Hem Alış hem Satış için)
        $kurlar = [
            'USD' => 46.8215, 'USD_SATIS' => 47.9604,
            'EUR' => 53.8685, 'EUR_SATIS' => 55.1788,
            'XAU' => 6086.4200, 'XAU_SATIS' => 6253.8800
        ];

        if ($html !== false) {
            $html_utf8 = iconv("Windows-1254", "UTF-8//IGNORE", $html);
            $meta_utf8 = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            $html_duzeltilmis = $meta_utf8 . $html_utf8;

            $dom = new DOMDocument();
            libxml_use_internal_errors(true); 
            $dom->loadHTML($html_duzeltilmis, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);

            // --- XPATH SORGULARI (ALIŞ VE SATIŞ KÜMELERİ) ---
            $base_path = '//div[contains(@class, "kurlar") and contains(@class, "bordernone") and @title="%s"]/ul/li[contains(@class, "midrow") and contains(@class, "%s")]';

            // 1. EURO (EUR)
            $eur_a = $xpath->query(sprintf($base_path, 'Euro', 'alis'));
            if ($eur_a && $eur_a->length > 0) { $kurlar['EUR'] = (float)trim($eur_a->item(0)->nodeValue); }
            $eur_s = $xpath->query(sprintf($base_path, 'Euro', 'satis'));
            if ($eur_s && $eur_s->length > 0) { $kurlar['EUR_SATIS'] = (float)trim($eur_s->item(0)->nodeValue); }

            // 2. DOLAR (USD)
            $usd_a = $xpath->query(sprintf($base_path, 'Amerikan Doları', 'alis'));
            if ($usd_a && $usd_a->length > 0) { $kurlar['USD'] = (float)trim($usd_a->item(0)->nodeValue); }
            $usd_s = $xpath->query(sprintf($base_path, 'Amerikan Doları', 'satis'));
            if ($usd_s && $usd_s->length > 0) { $kurlar['USD_SATIS'] = (float)trim($usd_s->item(0)->nodeValue); }

            // 3. GRAM ALTIN (XAU)
            $xau_a = $xpath->query(sprintf($base_path, 'İş Bankası', 'alis'));
            if ($xau_a && $xau_a->length > 0) { $kurlar['XAU'] = (float)trim($xau_a->item(0)->nodeValue); }
            $xau_s = $xpath->query(sprintf($base_path, 'İş Bankası', 'satis'));
            if ($xau_s && $xau_s->length > 0) { $kurlar['XAU_SATIS'] = (float)trim($xau_s->item(0)->nodeValue); }
        }
        
        return $kurlar;
    }

    public static function getLiveRates() {
        return [
            'kaynak' => 'Türkiye İş Bankası',
            'kurlar' => self::fetchIsBankKurlari(),
            'zaman'  => date('H:i:s')
        ];
    }
}

?>