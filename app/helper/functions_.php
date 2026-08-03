<?php

function cut_text($text, $text_start = 0, $text_end = 180){
    
    $text = strip_tags(htmlspecialchars_decode($text));
    $text_lenght = strlen($text);

    if ($text_lenght > $text_end){

        $text = mb_substr($text, $text_start, $text_end, 'UTF8') . '...';
        $text_cut = strrchr($text, " ");
        $text = str_replace($text_cut, " ...", $text);
    }

    return $text;
}

function safe($value) {
    return htmlspecialchars($value ?? '');
}

function printr($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit;
}

