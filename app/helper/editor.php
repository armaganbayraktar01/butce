
<?php

function editor_controller($controllerName){
    $controllerName = strtolower($controllerName);
    return PATH . '/editor/controller/' . $controllerName . '.php';
}

function editor_view($viewName){
    return PATH . '/editor/view/' . $viewName . '.php';
}

function editor_url($url = false){
    return URL . '/editor/' . $url;
}

function addPage($pageName, $workingDir){
		
    $file = PATH . '/' . $workingDir . '/controller/' . $pageName . '.php';
        
    if (file_exists($file)) {
         echo $pageName . '.php isminde dosya bulunuyor.';
         header('Location: ' . editor_url('add-page'));
         exit;
    
    } else {
        
        touch($file);
        $fopen = fopen($file, 'a+');
        $icerik = '<?php' . PHP_EOL . PHP_EOL . "require " . $workingDir . "_view('" . $pageName . "');" . PHP_EOL . PHP_EOL . '?>';
        fwrite($fopen, $icerik);
        $fclose = fclose($fopen);

        if($fclose == true) {

            $file1 = PATH . '/' . $workingDir . '/view/' . $pageName . '.php';
                        
            if (file_exists($file1)) {
                exit();

            } else {

                touch($file1);
                $fopen = fopen($file1, 'a+');
               
                $icerik = "<?php require " . $workingDir . "_view('static/header'); ?>" . PHP_EOL . PHP_EOL . "<?php require " . $workingDir . "_view('static/footer'); ?>";

                fwrite($fopen, $icerik);
                fclose($fopen);
                exit();

            }
        }
    }
}

?>