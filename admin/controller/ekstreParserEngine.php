<?php


// 🎯 COMPOSER GLOBAL AUTOLOAD KÖPRÜSÜ
// dirname(__DIR__, 2) ifadesi 2 basamak yukarı çıkarak doğrudan butce/vendor/autoload.php dosyasını bağlar.
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// PDF Analiz Motorunu Tanımlama
$parser = new \Smalot\PdfParser\Parser();


class EkstreParserEngine {
    private $db;
    private $aktif_donem;

    public function __construct($db, $aktif_donem) {
        $this->db = $db;
        $this->aktif_donem = $aktif_donem;
    }

    public function yukleVeAyristir($filePath, $kartTuru) {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf    = $parser->parseFile($filePath);
            $text   = $pdf->getText();

            $satirlar = explode("\n", $text);
            
            if ($kartTuru === 'isbank') {
                $harcamalar = $this->parseIsbank($satirlar);
            } elseif ($kartTuru === 'vakif') {
                $harcamalar = $this->parseVakif($satirlar);
            } else {
                throw new Exception("Geçersiz banka türü.");
            }

            return $this->veritabaninaKaydet($harcamalar, $kartTuru);

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    private function parseIsbank($satirlar) {
        $sonuc = [];
        $regex = '/^(\d{2}\.\d{2}\.\d{4})\s+(.+)\s+([\d\.,]+)$/';

        foreach ($satirlar as $satir) {
            $satir = trim($satir);
            if (preg_match($regex, $satir, $matches)) {
                $rawTarih    = $matches[1];
                $isletmeAdi  = trim($matches[2]);
                $rawTutar    = $matches[3];

                $tutar = str_replace(['.', ','], ['', '.'], $rawTutar);
                $tarih = date('Y-m-d', strtotime(str_replace('.', '-', $rawTarih)));

                if ($this->harcamaMi($isletmeAdi, $tutar)) {
                    $sonuc[] = [
                        'tarih'       => $tarih,
                        'isletme_adi' => $isletmeAdi,
                        'tutar'       => floatval($tutar),
                        'kategori'    => $this->otomatikKategoriAta($isletmeAdi)
                    ];
                }
            }
        }
        return $sonuc;
    }

    private function parseVakif($satirlar) {
        $sonuc = [];
        $regex = '/^(\d{2}\/\d{2}\/\d{4})\s+(.+?)\s+([\d\.,]+)\s*(?:TL)?$/i';

        foreach ($satirlar as $satir) {
            $satir = trim($satir);
            if (preg_match($regex, $satir, $matches)) {
                $rawTarih    = $matches[1];
                $isletmeAdi  = trim($matches[2]);
                $rawTutar    = $matches[3];

                $tutar = str_replace(['.', ','], ['', '.'], $rawTutar);
                $tarih = date('Y-m-d', strtotime(str_replace('/', '-', $rawTarih)));

                if ($this->harcamaMi($isletmeAdi, $tutar)) {
                    $sonuc[] = [
                        'tarih'       => $tarih,
                        'isletme_adi' => $isletmeAdi,
                        'tutar'       => floatval($tutar),
                        'kategori'    => $this->otomatikKategoriAta($isletmeAdi)
                    ];
                }
            }
        }
        return $sonuc;
    }

    private function harcamaMi($isletmeAdi, $tutar) {
        $yasakliKelimeler = ['YAZ FAİZ', 'GECİKME FAİZİ', 'KKDF', 'BSMV', 'TOPLAM BORÇ', 'ASGARİ ÖDEME', 'DEVREDEN BORÇ'];
        foreach ($yasakliKelimeler as $kelime) {
            if (stripos($isletmeAdi, $kelime) !== false) {
                return false;
            }
        }
        return $tutar > 0;
    }

    private function otomatikKategoriAta($isletmeAdi) {
        $isletmeAdi = mb_strtoupper($isletmeAdi, 'UTF-8');

        $harita = [
            'MARKET'    => ['MIGROS', 'A101', 'BIM', 'SOK', 'CARREFOUR', 'SANAL MARKET', 'HEMEN'],
            'E-TICARET' => ['TRENDYOL', 'HEPSIBURADA', 'AMAZON', 'N11'],
            'SPOR'      => ['MACFIT', 'DECATHLON', 'SPORTIVE', 'ADIDAS', 'NIKE', 'PUMA'],
            'PET'       => ['VETQOM', 'TEMIZMAMA', 'VETERINER', 'PETSHOP'],
            'GIYIM'     => ['MAVI', 'H&M', 'BERSHKA', 'LCW', 'ZARA', 'COLLINS'],
            'FATURA'    => ['IZMIRGAZ', 'GEDIZ', 'TELEKOM', 'TURKCELL', 'VODAFONE', 'TTNET']
        ];

        foreach ($harita as $kategori => $anahtarKelimeler) {
            foreach ($anahtarKelimeler as $kelime) {
                if (strpos($isletmeAdi, $kelime) !== false) {
                    return $kategori;
                }
            }
        }
        return 'Diğer';
    }

    private function veritabaninaKaydet($harcamalar, $kartTuru) {
        if (empty($harcamalar)) {
            return ['status' => 'warning', 'message' => 'Eşleşen harcama kalemi bulunamadı.'];
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                INSERT INTO harcama_gecmisi (donem, kart_turu, tarih, isletme_adi, tutar, kategori) 
                VALUES (:donem, :kart_turu, :tarih, :isletme_adi, :tutar, :kategori)
            ");

            $kaydedilenHarcamaSayisi = 0;
            foreach ($harcamalar as $h) {
                $stmt->execute([
                    'donem'        => $this->aktif_donem,
                    'kart_turu'    => $kartTuru,
                    'tarih'        => $h['tarih'],
                    'isletme_adi'  => $h['isletme_adi'],
                    'tutar'        => $h['tutar'],
                    'kategori'     => $h['kategori']
                ]);
                $kaydedilenHarcamaSayisi++;
            }

            $this->db->commit();
            return [
                'status' => 'success',
                'message' => "{$kaydedilenHarcamaSayisi} adet harcama kalemi başarıyla sisteme aktarıldı."
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'Veritabanı yazma hatası: ' . $e->getMessage()];
        }
    }
}
