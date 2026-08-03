<?php
require_once 'db_connection.php';

// JSON dosyasını oku
$jsonFile = __DIR__ . '/json/veri.json';

if (!file_exists($jsonFile)) {
    die("❌ Hata: '$jsonFile' dosyası bulunamadı.");
}

date_default_timezone_set('Europe/Istanbul');
$now = date("Y-m-d H:i:s");

$json = file_get_contents($jsonFile);
$productData = json_decode($json, true);

$eklenen = 0;
$atlanan = 0;

foreach ($productData as $item) {
    $site_id = $item['id'];

    // 🔍 Aynı ürün daha önce eklenmiş mi?
    $stmt = $db->prepare("SELECT product_id FROM products WHERE site_id = ?");
    $stmt->execute([$site_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        // 🆕 Yeni ürün ekle
        $stmt = $db->prepare("INSERT INTO products
        (site_id, title, brand, image_url, refined_quantity_unit, refined_volume_or_weight, categories, productDepotInfoList, created_date, uploaded_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $site_id,
            $item['title'],
            $item['brand'],
            $item['imageUrl'],
            $item['refinedQuantityUnit'] ?? null,
            $item['refinedVolumeOrWeight'] ?? null,
            json_encode($item['categories'], JSON_UNESCAPED_UNICODE),
            json_encode($item['productDepotInfoList'], JSON_UNESCAPED_UNICODE),
            $now,
            $now
        ]);

        $product_id = $db->lastInsertId();
    } else {
        $product_id = $product['product_id'];
    }

    // 🔁 Şimdi fiyatları ekle
    foreach ($item['productDepotInfoList'] as $fiyat) {

        /*
        // ❌ Aynı ürün + aynı şube + aynı fiyat varsa atla
        $stmt = $db->prepare("SELECT COUNT(*) FROM prices 
            WHERE product_id = ? AND market_depo_code = ? AND price = ?");
        $stmt->execute([$product_id, $fiyat['depotId'], $fiyat['price']]);
        if ($stmt->fetchColumn() > 0) {
            $atlanan++;
            continue;
        }
*/
        $stmt = $db->prepare("SELECT COUNT(*) FROM prices 
            WHERE product_id = ? AND market_depo_code = ? AND price = ? AND index_time = ?");
        $stmt->execute([$product_id, $fiyat['depotId'], $fiyat['price'], date('Y-m-d H:i:s', strtotime($fiyat['indexTime']))]);


        // 💡 ₺ işaretini temizle, sayısal değer ve birim ayır
        preg_match('/([\d,\.]+)\s*₺\/?(\w+)?/', $fiyat['unitPrice'], $matches);
        $unit_price = isset($matches[1]) ? str_replace(',', '.', $matches[1]) : null;
        $unit_type = $matches[2] ?? null;

        $stmt = $db->prepare("INSERT INTO prices 
        (product_id, title, market, market_depo, market_depo_code, price, percentage, unit_price, unit_type, index_time, created_date, uploaded_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $product_id,
            $item['title'],
            $fiyat['marketAdi'],
            $fiyat['depotName'],
            $fiyat['depotId'],
            $fiyat['price'],
            $fiyat['percentage'],
            $unit_price,
            $unit_type,
            date('Y-m-d H:i:s', strtotime($fiyat['indexTime'])),
            $now,
            $now
        ]);

        $eklenen++;
    }
}

echo "✅ $eklenen fiyat eklendi. ⛔ $atlanan fiyat atlandı.";
