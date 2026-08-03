<?php
/* 
path config.php dosyası içinde yer almaktadır.
path = C:\xampp\htdocs\cms
*/

/* app klasörü için fonksiyonlar */

//  app/controller içindeki dosyalar için fonksiyon
function controller($controllerName){
    $controllerName = strtolower($controllerName);
    return PATH . '/app/controller/' . $controllerName . '.php';
}

//  app/view içindeki tema dosyasındaki dosyalar için fonksiyon
function view($viewName){
    return PATH . '/app/view/' .settings('theme') . '/' .  $viewName . '.php';
}

//ANA DİZİNDEKİ İNDEX PHP DE YER ALIR
function route($index){
    global $route;
    return isset($route[$index]) ? $route[$index] : FALSE;
}

// PATH . '/app/settings.php' içindeki ayarlar dizisinin elemanlarını çekmek için kullanılacak
function settings($name){
    global $settings;
    return isset($settings[$name]) ? $settings[$name] : false;
}

function session($name){
    return isset($_SESSION[$name]) ? $_SESSION[$name] : FALSE;
}
?>