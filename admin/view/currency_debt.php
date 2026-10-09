<?php 
    ## Tags Bilgileri
    $meta = [
        'site_title' => 'Borç Ödeme | Bütçe Yönetimi',
        'site_desc' => 'Bütçe Yönetimi',
        'site_keyw' => 'Bütçe Yönetimi'
    ];
    # Tags Bilgileri

    require admin_view("static/header"); 

?>






  <!-- SYSTEM: GELECEK DÖNEM DÖVİZ BORÇ YÖNETİM TERMINALİ -->
<div class="container-fluid py-4">
    <div class="row g-4">
        

        
        <!-- SOL BLOK: OPERASYON VE ÖDEME EMİR PANELİ -->
        <div class="col-md-6">
            <div class="card shadow-sm border border-secondary border-opacity-10 rounded-3 p-4 bg-white">
                <h5 class="fw-bold text-dark text-uppercase mb-4 d-flex align-items-center" style="font-size: 13px; letter-spacing: 0.5px;">
                    <i class="fas fa-credit-card me-2 text-primary"></i> Kredi Kartı Döviz Borç Mahsup Paneli
                </h5>

                <form id="currencyDebtForm" autocomplete="off">
                    <input type="hidden" name="action" value="pay_currency_debt">

                    <!-- 1. Seçim Alanı: Döviz Birimi Seçimi (Alanın Tamamı Tıklanabilir Yapı) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.3px;">Ödenecek Döviz Birimi</label>
                        <div class="d-flex gap-3 text-uppercase">
                            <!-- USD Kutusu -->
                            <label class="d-flex align-items-center justify-content-center p-3 rounded-3 flex-fill text-center border cursor-pointer bg-light border-light-subtle transition-all" style="user-select: none;" for="radio_usd">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input m-0 cursor-pointer" type="radio" name="doviz_turu" id="radio_usd" value="USD" checked onchange="dovizBirimDegisti()">
                                    <span class="fw-bold text-dark font-monospace ms-2" style="font-size: 12px;">USD ($)</span>
                                </div>
                            </label>
                            <!-- EUR Kutusu -->
                            <label class="d-flex align-items-center justify-content-center p-3 rounded-3 flex-fill text-center border cursor-pointer bg-light border-light-subtle transition-all" style="user-select: none;" for="radio_eur">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input m-0 cursor-pointer" type="radio" name="doviz_turu" id="radio_eur" value="EUR" onchange="dovizBirimDegisti()">
                                    <span class="fw-bold text-dark font-monospace ms-2" style="font-size: 12px;">EUR (€)</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 2. Seçim Alanı: Kredi Kartı Seçimi + Yan Yana Canlı Borç Gösterge Kutusu -->
                    <div class="mb-4">
                        <div class="row g-2">
                            <!-- Sol Taraf: Kredi Kartı Seçim Kutusu (%65 Genişlik) -->
                            <div class="col-8">
                                <label for="kart_id" class="form-label fw-bold text-secondary text-uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.3px;">Ödemenin Yapılacağı Kart</label>
                                <select class="form-select form-select-lg fw-semibold text-dark text-uppercase p-3" id="kart_id" name="kart_id" required onchange="dovizBirimDegisti()" style="font-size: 12px; height: 50px;">
                                    <?php if(isset($kkart) && is_array($kkart)): ?>
                                        <?php foreach($kkart as $k_code => $k_detay): ?>
                                            <option value="<?= (int)$k_detay['id'] ?>" 
                                                    data-orj-text="<?= $k_code === 'isbank' ? 'İŞBANKASI MAXIMUM KART' : 'VAKIFBANK WORLDCARD' ?>"
                                                    data-usd-borc="<?= (float)$k_detay['usd_borcu'] ?>" 
                                                    data-eur-borc="<?= (float)$k_detay['eu_borcu'] ?>">
                                                <?= $k_code === 'isbank' ? 'İşbankası Maximum Kart' : 'Vakıfbank Worldcard' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <!-- Sağ Taraf: Canlı ve Readonly Borç Gösterge Kutusu (%35 Genişlik) -->
                            <div class="col-4">
                                <label id="lbl_baye_canli_kart_borc_input" for="baye_canli_kart_borc_input" class="form-label fw-bold text-secondary text-uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.3px;">Güncel Borç</label>
                                <input type="text" id="baye_canli_kart_borc_input" class="form-control form-control-lg p-3 fw-bold font-monospace text-end bg-light border-light-subtle text-danger" readonly value="0.00 $" style="font-size: 12px; height: 50px; cursor: default;">
                            </div>
                        </div>
                    </div>


                     <!-- 3. Dinamik Seçici: Paranın Çekileceği Hesap + Yan Yana Canlı Bakiye Kutusu -->
                    <div class="mb-4">
                        <div class="row g-2">
                            <!-- Sol Taraf: Hesap Seçim Kutusu (%65 Genişlik) -->
                            <div class="col-8">
                                <label id="lbl_cekilecek_hesap" for="panel_cekilecek_hesap" class="form-label fw-bold text-secondary text-uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.3px;">Paranın Çekileceği Hesap</label>
                                <select class="form-select form-select-lg fw-semibold text-dark text-uppercase p-3" id="panel_cekilecek_hesap" name="havuz_id" required onchange="hesapDegisti()" style="font-size: 12px; height: 50px;">
                                    <option value="" disabled>Hesap Seçiniz...</option>
                                    <?php if (isset($havuz_sorgu) && is_array($havuz_sorgu)): ?>
                                        <?php foreach($havuz_sorgu as $havuz): ?>
                                            <option value="<?= (int)$havuz['id'] ?>" 
                                                    data-orj-adi="<?= htmlspecialchars($havuz['havuz_adi']) ?>"
                                                    data-usd-bakiye="<?= (float)$havuz['usd_bakiye'] ?>"
                                                    data-eur-bakiye="<?= (float)$havuz['eu_bakiye'] ?>"
                                                    <?= $havuz['havuz_kodu'] === 'isbank_a' ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($havuz['havuz_adi']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <!-- Sağ Taraf: Canlı ve Readonly Gösterge Kutusu (%35 Genişlik) -->
                            <div class="col-4">
                                <label for="baye_canli_havuz_bakiye_input" class="form-label fw-bold text-secondary text-uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.3px;">Kasa Bakiyesi</label>
                                <input type="text" id="baye_canli_havuz_bakiye_input" class="form-control form-control-lg p-3 fw-bold font-monospace text-end bg-light border-light-subtle text-primary" readonly value="0.00 $" style="font-size: 12px; height: 50px; cursor: default;">
                            </div>
                        </div>
                        <!-- Alt Bilgi Şeridi: Sadece Kart Borcu Gösterimi İçin Korundu -->
                        <div class="d-flex justify-content-end mt-1 px-1 text-uppercase text-muted text-opacity-70 font-monospace" style="font-size: 10.5px; letter-spacing: 0.2px;">
                            <small id="txt_cekilecek_bakiye" style="display:none;">Havuz Bakiyesi: --</small>
                            <small id="txt_kart_guncel_borc" style="display:none;">GÜNCEL KART BORCU: --</small>
                        </div>
                    </div>


                    <!-- 4. Girdi Alanı: Ödeme Miktarı + Alttan Bağlantılı Şık Link Yapısı -->
                    <div class="mb-4">
                        <label for="miktar" class="form-label fw-bold text-secondary text-uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.3px;">Ödenecek Döviz Miktarı</label>
                        
                        <!-- Kaymayı önleyen esneklik kalkanı -->
                        <div class="input-group d-flex flex-nowrap align-items-stretch" style="width: 100% !important;">
                            <input type="number" step="0.01" min="0.01" class="form-control form-control-lg p-3 fw-bold font-monospace text-end text-dark flex-grow-1" id="miktar" name="miktar" placeholder="0.00" required oninput="miktarHesaplaKontrol()" style="font-size: 14px; height: 50px; width: 10px !important; min-width: 0 !important;">
                            <span class="input-group-text fw-bold font-monospace bg-light px-3 text-secondary d-flex align-items-center justify-content-center flex-shrink-0" id="birim_simge" style="min-width: 45px; font-size: 13px; height: 50px; border-left: 0 !important;">$</span>
                        </div>

                        <!-- 🎯 REFORM: Kesin olarak tıklanabilir, tarayıcı korumalı gerçek link yapısı -->
                        <div class="d-flex justify-content-end mt-1 px-1" style="position: relative; z-index: 10;">
                            <a href="javascript:void(0);" 
                               onclick="borcunTamaminiDoldur();" 
                               class="text-muted text-opacity-70 font-monospace fw-bold text-decoration-underline text-uppercase d-inline-block" 
                               style="font-size: 10.5px; letter-spacing: 0.2px; cursor: pointer !important;">
                                BORCUN TAMAMINI SEÇ
                            </a>
                        </div>
                    </div>



                    <!-- Onaylama Butonu Terminali -->
                    <button type="button" id="btn_onayla" class="btn btn-dark w-100 btn-lg py-3 fw-bold text-uppercase d-flex align-items-center justify-content-center px-3 tracking-wider" onclick="fireCurrencyPayment()" style="font-size: 12px; border-radius: 6px; height: 50px;">
                        <i class="fas fa-money-bill-wave me-2 text-warning"></i> KART BORCUNU ÖDE
                    </button>

                </form>
            </div>
        </div>

        <!-- SAĞ BLOK: ANLIK DÖVİZ BORÇ VE DURUM MATRİSİ -->
        <div class="col-md-6">
            <div class="card shadow-sm border border-light-subtle rounded-3 p-4 bg-white h-100 d-flex flex-column justify-content-between">
                <div>
                    <h5 class="fw-bold text-dark mb-4 d-flex align-items-center">
                        <i class="fas fa-chart-pie me-2 text-success"></i> Gelecek Dönem Döviz Risk Durumu
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle font-monospace mb-0" style="font-size: 12px;">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th class="py-2 ps-2">Kredi Kartı</th>
                                    <th class="py-2 text-end">Dolar Borcu ($)</th>
                                    <th class="py-2 text-end">Euro Borcu (€)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($kkart) && is_array($kkart)): ?>
                                    <?php foreach($kkart as $k_code => $k_detay): ?>
                                        <tr class="border-bottom">
                                            <td class="py-3 ps-2 fw-bold text-dark">
                                                <?= $k_code === 'isbank' ? '💼 Maximum Kart' : '🛡️ Worldcard' ?>
                                            </td>
                                            <td class="py-3 text-end fw-bold <?= $k_detay['usd_borcu'] > 0 ? 'text-danger' : 'text-muted' ?>">
                                                $ <?= number_format($k_detay['usd_borcu'], 2, ',', '.') ?>
                                            </td>
                                            <td class="py-3 text-end fw-bold <?= $k_detay['eu_borcu'] > 0 ? 'text-danger' : 'text-muted' ?>">
                                                € <?= number_format($k_detay['eu_borcu'], 2, ',', '.') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Alt Bilgilendirme Şeridi -->
                <div class="bg-light p-3 rounded-3 border mt-4 text-muted" style="font-size: 11px; line-height: 1.6;">
                    <span class="d-block fw-bold text-dark mb-1"><i class="fas fa-info-circle text-primary me-1"></i> Finansal Kurallar Sözleşmesi:</span>
                    Yapılan mahsup ödemeleri doğrudan döviz kasalarınızdan düşürülür. İşlem başarılı sonuçlandığında <code>forex_islemler</code> tablosuna ödeme kaydı mühürlenir ve kart limitleriniz anlık genişler.
                </div>
            </div>
        </div>

    </div> <!-- .row sonu -->
</div> <!-- .container-fluid sonu -->



<!-- JAVASCRIPT JALUZİ KORUMA VE ASENKRON PLAN MOTORU -->
<script>

window.gelecekDonemAbortController = null;
window.gelecekDonemZamanlayici = null;

document.addEventListener("DOMContentLoaded", function() {
    dovizBirimDegisti();
});

function dovizBirimDegisti() {
    const seciliDoviz = document.querySelector('input[name="doviz_turu"]:checked').value;
    const simgeEl = document.getElementById('birim_simge');
    const simge = seciliDoviz === 'USD' ? '$' : '€';
    
    simgeEl.textContent = simge;
    
    // Kredi Kartı Selectbox Seçeneklerini Sade Tut (Sadece Kart Öz İsmi)
    const kartEl = document.getElementById('kart_id');
    Array.from(kartEl.options).forEach(option => {
        option.text = option.dataset.orjText;
    });
    
    // 6 Yönlü Havuz Hesap Seçeneklerini Sade Tut
    const hesapEl = document.getElementById('panel_cekilecek_hesap');
    Array.from(hesapEl.options).forEach(option => {
        if (option.value === "") return;
        option.text = option.dataset.orjAdi;
    });
    
    // Aktif kart limit ve borç tespitleri
    const seciliKart = kartEl.options[kartEl.selectedIndex];
    let kartBorcu = 0;
    if (seciliKart) {
        kartBorcu = seciliDoviz === 'USD' ? parseFloat(seciliKart.dataset.usdBorc) || 0 : parseFloat(seciliKart.dataset.eurBorc) || 0;
    }
    
    // 🎯 REFORM: Yeni eklediğimiz readonly kart borç girdi kutusunun değerini senkronize eder
    const borcInput = document.getElementById('baye_canli_kart_borc_input');
    if (borcInput) {
        borcInput.value = `${kartBorcu.toFixed(2)} ${simge}`;
    }
    
    // Eski alt şerit ve miktar sınır korumalarının devamı
    document.getElementById('txt_kart_guncel_borc').textContent = `GÜNCEL KART BORCU: ${kartBorcu.toFixed(2)} ${simge}`;
    document.getElementById('miktar').max = kartBorcu;

    hesapDegisti();
}


function hesapDegisti() {
    const seciliDoviz = document.querySelector('input[name="doviz_turu"]:checked').value;
    const hesapEl = document.getElementById('panel_cekilecek_hesap');
    const seciliHesap = hesapEl.options[hesapEl.selectedIndex];
    const simge = seciliDoviz === 'USD' ? '$' : '€';
    
    let havuzBakiye = 0;
    if (seciliHesap) {
        havuzBakiye = seciliDoviz === 'USD' ? 
            parseFloat(seciliHesap.dataset.usdBakiye) || 0 : 
            parseFloat(seciliHesap.dataset.eurBakiye) || 0;
    }
    
    // 🎯 REFORM: Yeni eklediğimiz readonly girdi kutusunun değerini kuruşu kuruşuna günceller
    const bakiyeInput = document.getElementById('baye_canli_havuz_bakiye_input');
    if (bakiyeInput) {
        bakiyeInput.value = `${havuzBakiye.toFixed(2)} ${simge}`;
    }
    
    // Eski alt şerit uyumluluğu ve kontrollerin devamı
    document.getElementById('txt_cekilecek_bakiye').textContent = `HAVUZ BAKİYESİ: ${havuzBakiye.toFixed(2)} ${simge}`;
    
    const kartEl = document.getElementById('kart_id');
    const seciliKart = kartEl.options[kartEl.selectedIndex];
    let kartBorcu = 0;
    if (seciliKart) {
        kartBorcu = seciliDoviz === 'USD' ? parseFloat(seciliKart.dataset.usdBorc) || 0 : parseFloat(seciliKart.dataset.eurBorc) || 0;
    }
    const borcKutusu = document.getElementById('txt_kart_guncel_borc');
    borcKutusu.style.display = "block";
    borcKutusu.textContent = `GÜNCEL KART BORCU: ${kartBorcu.toFixed(2)} ${simge}`;
    
    miktarHesaplaKontrol();
}


// 🎯 REFORM: Butona tıklandığında borcun tamamını kuruşu kuruşuna girdi alanına doldurur
function borcunTamaminiDoldur() {
    const seçiliDöviz = document.querySelector('input[name="doviz_turu"]:checked').value;
    const kartEl = document.getElementById('kart_id');
    const seçiliKart = kartEl.options[kartEl.selectedIndex];
    
    let kartBorcu = 0;
    if (seçiliKart) {
        kartBorcu = seçiliDöviz === 'USD' ? 
            parseFloat(seçiliKart.dataset.usdBorc) || 0 : 
            parseFloat(seçiliKart.dataset.eurBorc) || 0;
    }
    
    const miktarInput = document.getElementById('miktar');
    miktarInput.value = kartBorcu > 0 ? kartBorcu.toFixed(2) : "";
    
    // İnput değeri değiştiği için kural sensörünü yeniden tetikle
    miktarHesaplaKontrol();
}



function miktarHesaplaKontrol() {
    const seçiliDöviz = document.querySelector('input[name="doviz_turu"]:checked').value;
    const miktarEl = document.getElementById('miktar');
    const btnEl = document.getElementById('btn_onayla');
    const girdiMiktar = parseFloat(miktarEl.value) || 0;
    
    const hesapEl = document.getElementById('panel_cekilecek_hesap');
    const seçiliHesap = hesapEl.options[hesapEl.selectedIndex];
    const kartEl = document.getElementById('kart_id');
    const seçiliKart = kartEl.options[kartEl.selectedIndex];
    
    let havuzBakiye = 0;
    let kartBorcu = 0;
    
    if (seçiliHesap) {
        havuzBakiye = seçiliDöviz === 'USD' ? parseFloat(seçiliHesap.dataset.usdBakiye) || 0 : parseFloat(seçiliHesap.dataset.eurBakiye) || 0;
    }
    if (seçiliKart) {
        kartBorcu = seçiliDöviz === 'USD' ? parseFloat(seçiliKart.dataset.usdBorc) || 0 : parseFloat(seçiliKart.dataset.eurBorc) || 0;
    }
    
    if (girdiMiktar > havuzBakiye) {
        btnEl.disabled = true;
        btnEl.textContent = "Bakiyeniz yetersiz!";
        btnEl.className = "btn btn-danger w-100 btn-lg fw-bold text-uppercase";
    } else if (girdiMiktar > kartBorcu) {
        btnEl.disabled = true;
        btnEl.textContent = "Borç Tutarından Fazla Tutar Ödenemez!";
        btnEl.className = "btn btn-warning text-dark w-100 btn-lg fw-bold text-uppercase";
    } else {
        btnEl.disabled = false;
        btnEl.textContent = "Döviz Borç Ödeme İşlemini Tamamla";
        btnEl.className = "btn btn-dark w-100 btn-lg fw-bold text-uppercase";
    }
}

function fireCurrencyPayment() {
    const form = document.getElementById('currencyDebtForm');
    const formData = new FormData(form);
    
    const seçiliDöviz = document.querySelector('input[name="doviz_turu"]:checked').value;
    const miktar = parseFloat(document.getElementById('miktar').value) || 0;
    const hesapText = document.getElementById('panel_cekilecek_hesap').options[document.getElementById('panel_cekilecek_hesap').selectedIndex].text;

    if(miktar <= 0) {
        alert("Lütfen geçerli bir ödeme miktarı giriniz.");
        return;
    }

    if (!confirm(`${hesapText} hesabınızdan ${miktar.toFixed(2)} ${seçiliDöviz} çekilerek kredi kartı borcunuza mahsup edilecektir. Onaylıyor musunuz?`)) {
        return;
    }

    fetch('/butce/admin/currency_debt', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data && data.status === 'success') {
            alert(data.message);
            window.location.reload();
        } else {
            alert("Hata: " + data.message);
        }
    })
    .catch(err => {
        console.error("Kritik Döviz Ödeme Hatası:", err);
        alert("Ağ bağlantısı veya yerel sunucu hatası meydana geldi.");
    });
}
</script>









<?php require admin_view("static/footer"); ?>
