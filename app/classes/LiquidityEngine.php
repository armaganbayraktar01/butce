<?php
// =========================================================================
// 🏛️ FİNANSAL ANAYASA KATMANI: SANAL MERKEZ BANKASI LİKİDİTE MOTORU (OOP)
// =========================================================================

class LiquidityEngine {
    private PDO $db;
    private string $aktifDonem;
    private array $havuzlar = [];
    private bool $isKrediOdendi = false;

    public function __construct(PDO $db, string $aktifDonem) {
        $this->db = $db;
        $this->aktifDonem = $aktifDonem;
        $this->havuzMatrahlariniYukle();
        $this->raporSatiriniGarantile();
    }

    public function havuzMatrahlariniYukle(): void {
        $rows = $this->db->query("SELECT * FROM finansal_havuzlar")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $this->havuzlar[$row['havuz_kodu']] = [
                'anapara' => floatval($row['anapara']),
                'nema'    => floatval($row['nema']),
                'bloke'   => floatval($row['bloke_tutar'] ?? 0)
            ];
        }
    }

    private function raporSatiriniGarantile(): void {
        $this->db->prepare("INSERT IGNORE INTO `aylik_harcama_raporu` (`donem`) VALUES (?)")->execute([$this->aktifDonem]);
    }

    private function havuzDurumlariniMuhurle(): void {
        try {
            $stmt = $this->db->prepare("UPDATE finansal_havuzlar SET anapara = :ana, bloke_tutar = :bloke WHERE havuz_kodu = :kod");
            foreach ($this->havuzlar as $kod => $veri) {
                $stmt->execute(['ana' => $veri['anapara'], 'bloke' => $veri['bloke'], 'kod' => $kod]);
            }
        } catch (Exception $e) {}
    }

    public function executeOzgeMaasEft(float $isbankO, float $toplam): void {
        $this->havuzlar['isbank_o']['anapara'] += $isbankO;
        $this->havuzlar['isbank_a']['anapara'] += max(0, $toplam - $isbankO);
        $this->havuzDurumlariniMuhurle();
    }

    public function executeMaktuMesaiEft(float $tutar): void {
        $this->havuzlar['isbank_a']['anapara'] += $tutar;
        $this->havuzDurumlariniMuhurle();
    }

    public function executeArmaganMaasEft(float $tutar): void {
        $this->havuzlar['isbank_a']['anapara'] += $tutar;
        $this->havuzDurumlariniMuhurle();
    }

    public function executeEvKredisiOde(float $tutar): void {
        $this->havuzlar['isbank_o']['anapara'] -= $tutar;
        $this->isKrediOdendi = true;
        $this->havuzDurumlariniMuhurle();
    }

    public function executePopUpTransfer(float $tutar, string $hedef): void {
        if (!in_array($hedef, ['vakif_o', 'nakit'])) return;
        $this->havuzlar['isbank_a']['anapara'] -= $tutar;
        $this->havuzlar[$hedef]['anapara'] += $tutar;
        $this->havuzDurumlariniMuhurle();
    }

    public function executeLiveErosion(array $kalem): void {
        $odenan = floatval($kalem['odenen_tutar'] ?? 0);
        $tutar  = floatval($kalem['tutar'] ?? 0);
        $turu   = $kalem['turu'] ?? 'gider';
        $hKod   = $kalem['havuz_kodu'] ?? 'isbank_o';
        $isim   = mb_strtolower(trim($kalem['kalem_adi'] ?? ''), 'UTF-8');

        if ($isim === 'nergiz cep') return;
        if (strpos($isim, 'devreden nakit') !== false) {
            $this->havuzlar['nakit']['anapara'] += $odenan;
            $this->havuzDurumlariniMuhurle();
            return;
        }

        $is_odendi = ($odenan > 0 || ($turu === 'gelir' && $odenan == $tutar && $tutar > 0));
        $is_fatura = false;
        foreach (['faturası', 'izsu', 'elektrik', 'su', 'doğalgaz', 'telefon', 'internet'] as $sk) {
            if (strpos($isim, $sk) !== false) { $is_fatura = true; break; }
        }

        if (!$is_odendi) {
            if ($isim === 'aidat' || $isim === 'damacana su' || strpos($isim, 'n_') === 0) { 
                $this->havuzlar['nakit']['bloke'] += $tutar; 
            } elseif ($is_fatura || strpos($isim, 'v_') === 0) { 
                $this->havuzlar['vakif_o']['bloke'] += $tutar; 
            }
            $this->havuzDurumlariniMuhurle();
            return;
        }

        if ($turu === 'gelir') {
            if (strpos($isim, 'v_') === 0) $hKod = 'vakif_o';
            elseif (strpos($isim, 'y_') === 0) $hKod = 'isbank_a';
            elseif (strpos($isim, 'n_') === 0) $hKod = 'nakit';
            $this->havuzlar[$hKod]['anapara'] += $odenan;
        } else {
            if (in_array($isim, ['vakıfbank kk', 'vakifbank kk', 'işbank kk', 'isbank kk'])) {
                $this->havuzlar['isbank_a']['anapara'] -= $odenan;
                $this->havuzDurumlariniMuhurle();
                return;
            }
            if (strpos($isim, 'v_') === 0) $hKod = 'vakif_o';
            elseif (strpos($isim, 'y_') === 0) $hKod = 'isbank_a';
            elseif (strpos($isim, 'n_') === 0) $hKod = 'nakit';
            $this->havuzlar[$hKod]['anapara'] -= $odenan;
        }
        $this->havuzDurumlariniMuhurle();
    }

    public function pasifAkislariSimuleEt(array $kalemler): void {
        foreach ($kalemler as $kalem) {
            $odenan = floatval($kalem['odenen_tutar'] ?? 0);
            $tutar  = floatval($kalem['tutar'] ?? 0);
            $turu   = $kalem['turu'] ?? 'gider';
            $hKod   = $kalem['havuz_kodu'] ?? '';
            $isim   = mb_strtolower(trim($kalem['kalem_adi'] ?? ''), 'UTF-8');

            if ($isim === 'nergiz cep' || strpos($isim, 'havuz transfer fonu') !== false) continue;
            if (strpos($isim, 'kredi') !== false && $odenan > 0) $this->isKrediOdendi = true;

            // Güvenlik Duvarı: Eğer havuz kodu sistemde tanımlı değilse emniyetli havuz ataması yap
            if (empty($hKod) || !isset($this->havuzlar[$hKod])) {
                $hKod = ($turu === 'gelir') ? 'isbank_a' : 'isbank_o';
            }

            // MÜMTAZ REFORM: Tüm harf ön takı if/else blokları kazındı!
            // Sistem artık sadece satır kilitliyse ($odenan > 0) ilgili havuz hücresine matrah mühürler.
            if ($odenan > 0 || ($turu === 'gelir' && $odenan == $tutar && $tutar > 0)) {
                if ($turu === 'gelir') {
                    // Gelir ise parayı ilgili havuza doğrudan ekle (+)
                    $this->havuzlar[$hKod]['anapara'] += $odenan;
                } else {
                    // Sabit kart kuralları emniyeti
                    if (in_array($isim, ['vakıfbank kk', 'vakifbank kk', 'işbank kk', 'isbank kk'])) {
                        $this->havuzlar['isbank_a']['anapara'] -= $odenan;
                    } else {
                        // Gider ise parayı ilgili havuzdan düş (-)
                        $this->havuzlar[$hKod]['anapara'] -= $odenan;
                    }
                }
            }
        }
    }


    public function canliFaizProjeksiyonu(float $oran, int $gun, float $stopaj): array {
        $safA = max(0.00, floatval($this->havuzlar['isbank_a']['anapara'] ?? 0));
        $safO = max(0.00, floatval($this->havuzlar['isbank_o']['anapara'] ?? 0));
        $faizA = ($safA > 0) ? ($safA * ($oran / 100) / 365) * $gun : 0.00;
        $faizO = ($safO > 0 && !$this->isKrediOdendi) ? ($safO * ($oran / 100) / 365) * $gun : 0.00;
        return [
            'isbank_a_nema' => $faizA * (1 - ($stopaj / 100)),
            'isbank_o_nema' => $faizO * (1 - ($stopaj / 100))
        ];
    }

    public function commitInterestToCapital(float $oran, int $gun, float $stopaj): float {
        $proj = $this->canliFaizProjeksiyonu($oran, $gun, $stopaj);
        $this->havuzlar['isbank_a']['anapara'] += $proj['isbank_a_nema'];
        $this->havuzlar['isbank_o']['anapara'] += $proj['isbank_o_nema'];
        $this->havuzDurumlariniMuhurle();
        return $proj['isbank_a_nema'] + $proj['isbank_o_nema'];
    }

    public function getSanalBankaKarnesi(float $oran, int $gun, float $stopaj, float $fxMaliyet): array {
        $proj = $this->canliFaizProjeksiyonu($oran, $gun, $stopaj);
        $safIsbankA = max(0.00, floatval($this->havuzlar['isbank_a']['anapara'] ?? 0));
        return [
            'isbank_a_bakiye' => max(0.00, $safIsbankA - $fxMaliyet) + $proj['isbank_a_nema'],
            'isbank_o_bakiye' => max(0.00, floatval($this->havuzlar['isbank_o']['anapara'] ?? 0)) + $proj['isbank_o_nema'],
            'vakif_o_bakiye'  => max(0.00, floatval($this->havuzlar['vakif_o']['anapara'] ?? 0)),
            'nakit_bakiye'    => max(0.00, floatval($this->havuzlar['nakit']['anapara'] ?? 0)),
            'vakif_bloke'     => max(0.00, floatval($this->havuzlar['vakif_o']['bloke'] ?? 0)),
            'nakit_bloke'     => max(0.00, floatval($this->havuzlar['nakit']['bloke'] ?? 0)),
            'kredi_odendi'    => $this->isKrediOdendi,
            'isbank_a_saf'    => max(0.00, $safIsbankA - $fxMaliyet)
        ];
    }
}
