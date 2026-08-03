<?php


$meta = [
    'site_title' => settings('title'),
    'site_desc' => settings('description'),
    'site_keyw' => settings('keywords')
];

require view('index'); // app/view/TEMPLATE/index.php
?>