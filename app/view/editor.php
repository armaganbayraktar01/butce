<?php
if(!route(1)){
    $route[1] = 'index'; // cms/admin/index.php
}

//admin klasörü altında route(1) sayfası yoksa index e yönlendirdi
if (!file_exists(editor_controller(route(1)))){
    $route[1] = 'index'; //  cms/admin/index.php
}

// <!--header.php => sidebar-->
$menus = [

    'index' => [
        'title' => 'Anasayfa',
        'icon' => 'tachometer'
    ],

    'editor' => [
        'title' => 'Editör',
        'icon' => 'cog',
        'submenus' => [
           //$submenu_url => $submenu_title
            'add-page' => 'Sayfa Ekle',
            'add-func' => 'Fonksiyon Ekle'
        ]
    ],

    'settings' => [
        'title' => 'Ayarlar',
        'icon' => 'cog'         
    ]

];

require editor_controller(route(1));

?>