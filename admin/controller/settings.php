<?php

if (!permission('settings', 'show')){
    permission_page();
}

//view klasöründeki dosyaları listeleme
$themes = [];
foreach (glob(PATH . '/app/view/*/') as $folder){
    $folder = explode('/', rtrim($folder, '/'));
    $themes[] = end($folder);
}

//settings dosyasına ayarları kaydetme fonksiyonu
if (post('submit')){

   
if (!permission('settings', 'show')){
    permission_page();

} else {

    $html = '<?php' . PHP_EOL . PHP_EOL;
    foreach (post('settings') as $key => $value){
        $html .= '$settings["' . $key . '"] = "' . $value . '";' . PHP_EOL;
    }

    //ayarları PATH . '/app/settings.php' dosyasında yazdıralım
    file_put_contents(PATH . '/app/settings.php', $html);
    header('location' . admin_url('settings'));

}
}

require admin_view('settings'); //cms/admin/view/settings.php

?>