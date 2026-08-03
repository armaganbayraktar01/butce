
<?php
/* 
path config.php dosyası içinde yer almaktadır.
path = C:\xampp\htdocs\cms
*/

/* app klasörü için fonksiyonlar */

function app_controller($controllerName){
    $controllerName = strtolower($controllerName);
    return PATH . '/app/controller/' . $controllerName . '.php';
}

function app_view($viewName){
    //return PATH . '/app/view/' .settings('theme') . '/' .  $viewName . '.php';
    return PATH . '/app/view/' . $viewName . '.php';
}

function app_url($url = false){
    return URL . '/app/' . $url;
}


/* admin klasörü için fonksiyonlar */

function admin_controller($controllerName){
    $controllerName = strtolower($controllerName);
    return PATH . '/admin/controller/' . $controllerName . '.php';
}

function admin_view($viewName){
    return PATH . '/admin/view/' . $viewName . '.php';
}

function admin_url($url = false){
    return URL . '/admin/' . $url;
}

function admin_public_url($url = false){ 
    return URL . '/admin/public/' . $url;
}

function user_ranks($rankId = null){
    $ranks = [
        '1' => 'Admin',
        '2' => 'Editor',
        '3' => 'Üye'
    ];

    return $rankId ? $ranks[$rankId] : $ranks;
}

function permission($url, $action){
    $permissions = json_decode(session('user_permissions'), true);
    if (isset($permissions[$url][$action]))
        return true;
    return false;
}

function permission_page(){
    // die('Bu bölümü görüntüleme yetkiniz yok!');
    // exit;
}

function send_email($email, $name, $subject, $message)
{

    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
            //$mail->SMTPDebug = 2;                 
            $mail->isSMTP();          
            $mail->Host       = settings('smtp_host');  
            $mail->SMTPAuth   = true;                                  
            $mail->Username   = settings('smtp_email');                     
            $mail->Password   = settings('smtp_password');                                
            $mail->SMTPSecure = settings('smtp_secure');                                  
            $mail->Port       = settings('smtp_port'); 
            $mail->CharSet    = 'UTF8';                            

        //Recipients
            $mail->setFrom(settings('smtp_send_email'), settings('smtp_send_name'));
            $mail->addAddress($email, $name);
            //$mail->addAddress('ellen@example.com');
            //$mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

        // Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');

        // Content
            $mail->isHTML(true);                                  
            $mail->Subject = $subject;
            $mail->Body    = $message;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        return true;
        //echo 'Mesajınız Gönderildi.';
    } catch (Exception $e) {
        return false;
        //echo "Mesajınız Gönderilmedi. {$mail->ErrorInfo}";
    }

}

?>