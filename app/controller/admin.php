<?php
if (!route(1)){
    $route[1] = 'index';
}

if (!file_exists(admin_controller(route(1)))){
    $route[1] = 'index';

}

if (!session('user_rank') || session('user_rank')== 3 ){
    $route[1] = 'login';
}

// <!--header.php => sidebar-->
$menus = [

    'index' => [
        'title' => 'Anasayfa',
        'icon' => 'tachometer-alt',
        'permissions' => [
            'show' => 'Görüntüleme',
            'edit' => 'Düzenleme',
            'delete' => 'Silme'
        ]
    ],       
    'management' => [
        'title' => 'Ev Yönetimi',
        'icon' => 'home',
        'permissions' => [
            'show' => 'Görüntüleme',
            'add' => 'Ekleme',
            'edit' => 'Düzenleme',
            'delete' => 'Silme'
        ],
        'submenus' => [
            'management' => 'Güncel Dönem',
            'management_last' => 'Gelecek Dönem',
            'forex' => 'Yatırım Paneli',
            //'add_menu' => 'Menü Ekle',
            //'edit_product' => 'Ürün Düzenle',
            'budget' => 'Maaş Hesapla'
        ]
    ],
    'users' => [
        'title' => 'Üyeler',
        'icon' => 'users',
        'permissions' => [
            'show' => 'Görüntüleme',
            'add' => 'Ekleme',
            'edit' => 'Düzenleme',
            'delete' => 'Silme'
        ],
        'submenus' => [
            'add_user' => 'Üye Ekle'            
        ]
     ],
    'settings' => [
        'title' => 'Ayarlar',
        'icon' => 'cog',
        'permissions' => [
            'show' => 'Görüntüleme',
            'edit' => 'Düzenleme'
        ]         
    ]

];

require admin_controller(route(1));

?>