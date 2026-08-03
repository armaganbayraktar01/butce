<?php

function site_url($url = false){
    return URL . '/' . $url;
}

function public_url($url = false){ 
    //return URL . '/public/' . $url; //public klasöründeki css
    return URL . '/public/' . settings('theme') . '/' . $url;
}

function error(){
    global $error;
    return isset($error) ? $error : FALSE;
}

function success(){
    global $success;
    return isset($success) ? $success : FALSE;
}

?>