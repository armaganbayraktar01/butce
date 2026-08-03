<?php

// ana dizinin bulunduğu klasör
define('PATH', realpath('.'));
define('SUBFOLDER', true);
define('URL','http://localhost/butce');
define('SUBFOLDER_NAME', 'hfa');

//veritabanı bağlantısı için dizi
return [
    'db' => [
        'name' => 'hfa',
        'host' => 'localhost',
        'user' => 'root',
        'password' => ''
    ]
];


?>