<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$data = json_decode(file_get_contents("php://input"), true);
$filename = basename($data["filename"] ?? "");

if (!$filename || strpos($filename, ".jpg") === false) {
  echo json_encode(["success" => false, "error" => "Geçersiz dosya adı"]);
  exit;
}

$filepath = __DIR__ . "/api/image/" . $filename;

if (file_exists($filepath)) {
  unlink($filepath);
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false, "error" => "Dosya bulunamadı"]);
}
