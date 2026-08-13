<?php

// ana dizinin bulunduğu klasör
define('PATH', realpath('.'));
define('SUBFOLDER', true);
define('URL','http://localhost/butce');
define('SUBFOLDER_NAME', 'butce');

//veritabanı bağlantısı için dizi
return [
    'db' => [
        'name' => 'butce',
        'host' => 'localhost',
        'user' => 'root',
        'password' => ''
    ]
];


?>