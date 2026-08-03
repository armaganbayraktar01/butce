<?php

$page_url = route(1);

if ( !$page_url ){
    header ('Location:', site_url('404'));
    exit;
}

$row_pages = $db->from('pages')
                ->where('page_url', $page_url)
                ->first();

if ( !$row_pages ){
    header ('Location:', site_url('404'));
    exit;
}

$row_seo = json_decode($row_pages['page_seo'], true);

$meta = [
    'site_title' => $row_pages['page_title'],
    'site_desc'  => $row_seo['description'] ? $row_seo['description'] : cut_text($row_pages['page_content'])
];

require view('page');