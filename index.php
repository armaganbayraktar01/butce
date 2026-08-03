<?php
require __DIR__ . '/app/init.php';

$routeExplode = explode('?', $_SERVER['REQUEST_URI']);
$route = array_filter(explode('/', $routeExplode[0]));


//subfolder klasörü silme
if (SUBFOLDER === true){
    // $route dizisindeki key'i 0 olan cms'yi sildik
    array_shift($route); 
}


if (!route(0)){
    $route[0] = 'index'; //  cms/app/controller/index.php
}

//controller altında sayfa yoksa 404 sayfası gösterilecek
if (!file_exists(controller(route(0)))){
    $route[0] = '404'; //  cms/app/controller/app.php
}

// Bakım modu sayfası ayarlama
if (settings('maintenance_mode') == 1 && (route(0) !== 'admin')){
    $route[0] = 'maintenance-mode'; //  cms/app/controller/maintenance-mode.php
}

require controller(route(0));
?>