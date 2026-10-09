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
        'title' => 'Bütçe Yönetimi',
        'icon' => 'home',
        'permissions' => [
            'show' => 'Görüntüleme',
            'add' => 'Ekleme',
            'edit' => 'Düzenleme',
            'delete' => 'Silme'
        ],
        'submenus' => [
            'management' => 'Güncel Dönem',
            'management_next' => 'Gelecek Dönem',
            'harcama_raporu_arsiv' => 'Geçmiş Dönem (Arşiv)',
            'yatirim' => 'Altın - Döviz Al-Sat',
            'yatirim_arsiv_doviz' => 'Döviz Al-Sat (Arşiv)',
            'yatirim_arsiv_ev' => 'Ev Alımı',
            'yatirim_arsiv' => 'Mevduat-Altın (Arşiv)',
            'forex' => 'Yatırım Paneli',
            'currency_debt' => 'Borç Ödeme'

        ]
    ],
        'harcamalar' => [
        'title' => 'Kredi Kartı Yönetimi',
        'icon' => 'home',
        'permissions' => [
            'show' => 'Görüntüleme',
            'add' => 'Ekleme',
            'edit' => 'Düzenleme',
            'delete' => 'Silme'
        ],
        'submenus' => [
            'ekstreler' => 'Ekstre İşlemleri',
            'harcama_list' => 'Harcama Dökümü',
            'harcama_list_arsiv' => 'Harcama Dökümü 2025'
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