<?php

session_start();
ob_start();
date_default_timezone_set('Europe/Istanbul');

error_reporting(E_ALL);
ini_set('display_errors', 1);


//class ları otomatik yükleyen fonksiyon
function loadClasses($classname)
{
    require __DIR__ . '/classes/' . strtolower($classname) . '.php';
}

spl_autoload_register('loadClasses');


//config dosyamız
$config = require __DIR__ . '/config.php';


//Veritabanı bağlantısı veriler config.php den alınıyor
$host = $config['db']['host'];
$dbname = $config['db']['name'];
$dbuser = $config['db']['user'];
$dbpassword = $config['db']['password'];

try {
    $db = new basicDb($host, $dbname, $dbuser, $dbpassword);
     //$db = new PDO('mysql:host=' . $host . ';dbname=' . $dbname, $dbuser, $dbpassword);
    //$db->query("SET CHARACTER SET utf8");
} catch (PDOException $e) {
    die($e->getMessage());
}
$pdo = $db;


//PATH . '/app/settings.php' içindeki ayarlar dizisini çemkiyoruz
require __DIR__ . '/settings.php';

//glob ile helper içindeki tüm php dosyalarını bulduk ve dahil ettik
foreach (glob(__DIR__ . '/helper/*.php') as $helperFile)
{
    require $helperFile;
}

?>
