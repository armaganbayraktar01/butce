<?php

if ( post('submit') ){

    if ( $form_control = form_control()){
        
        $row_users = $db->from('users')
                        ->where('user_url', permalink($form_control['user_name']))
                        ->first();

        if ( !$row_users ) {
            $error = "Veritabanında böyle bir üye bulunmamaktadır. Girdiğiniz bilgiler eksik ya da hatalı olabilir. Lütfen tekrar deneyin.";            
        } else {
            
            if ( $row_users['user_rank'] ==3 ){
                $error = "Bu bölüme giriş yapmak için yetkiniz bulunmamaktadır.";
            } else {

                $password_verify = password_verify( $form_control['user_password'], $row_users['user_password'] );

                if ( $password_verify ){

                    $success = "Hoşgeldiniz " . $form_control['user_name'];
                    User::userLogin($row_users); //user.php içindeki user classı
                    header('Refresh:2; url=' . admin_url());
                } else {
                    
                    $error = "Şifrenizi hatalı girdiniz. Lütfen tekrar deneyin.";
                }
            }
        }
    }
}


require admin_view('login');

?>