<?php

function post($name)
{
    if (isset($_POST[$name])){

        if (is_array($_POST[$name]))
        {
            return array_map(function($item){      
                return htmlspecialchars(trim($item));
            }, $_POST[$name]);
        }
            return htmlspecialchars(trim($_POST[$name]));
    }

}

function post_arr($name = null) {
    if ($name) {
        if (!isset($_POST[$name])) {
            return null;
        }

        $value = $_POST[$name];

        // Eğer değer array ise, trim yapmadan direkt dön
        if (is_array($value)) {
            return $value;
        }

        // Değilse trim uygula ve dön
        return trim($value);
    }

    return $_POST;
}


function get($name)
{
    if (isset($_GET[$name])){

        if (is_array($_GET[$name]))
        {
            return array_map(function($item){      
                return htmlspecialchars(trim($item));
            }, $_GET[$name]);
        }
            return htmlspecialchars(trim($_GET[$name]));
    }

}

function form_control(...$except_these) // buraya yazılanlar control edilmeyecek
{
    unset($_POST['submit']);
    $form_control = [];
    $error = false;
    foreach($_POST as $key => $value){
        if( !in_array($key, $except_these) && !post($key)){
            $error = true;
        }else{
            $form_control[$key] = post($key);
        }
    }

    if ( $error ){
        return false;
    }

    return $form_control;
}

function form_control_arr(...$except_these) // buraya yazılanlar control edilmeyecek
{
    unset($_POST['submit']);
    $form_control = [];
    $error = false;
    foreach($_POST as $key => $value){
        if( !in_array($key, $except_these) && !post_arr($key)){
            $error = true;
        }else{
            $form_control[$key] = post_arr($key);
        }
    }

    if ( $error ){
        return false;
    }

    return $form_control;
}