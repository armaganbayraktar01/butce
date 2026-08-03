<?php

# Site meta bilgileri 
    $meta = [
        'site_title' => settings('title'),
        'site_desc' => settings('description'),
        'site_keyw' => settings('keywords')
    ];

# // Site meta bilgileri 

# post işlemleri 
    
    if (post('submit')) {
        $user_name = post('user_name');
        $user_email = post('user_email');
        $user_password = post('user_password');
        $user_password_a = post('user_password_a');
    }


    if (!$user_name) {
        $error = 'Kullanıcı adı boş bırakılamaz.';
    } elseif (!$user_email) {
        $error = 'E-posta adresi boş bırakılamaz.';
    } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Lütfen geçerli bir e-posta adresi yazın.';
    } elseif (!$user_password || !$user_password_a) {
        $error = 'Şifre boş bırakılamaz.';
    } elseif ($user_password_a != $user_password) {
        $error = 'Girdiğiniz şifreler birbiryle uyuşmuyor.';
    } else {

        # Veritabanında üye kayıtlı mı Kontrolü 

            $row_users = User::userExist($user_name,$user_email);

        # // Veritabanında üye kayıtlı mı Kontrolü 

        if ($row_users) {
            $error = $user_name . ' kullanıcı adı ya da e-posta adresi zaten kullanılıyor. Lütfen başka bir kullanıcı adı ya da e-posta adresi yazıp tekrar deneyin.';
        }else {
            
            # Üye eklemek için classes/user.php içindeki userRegister classına diziyi iletiyoruz.
            $result_user = User::userRegister([
                'user_name' => $user_name,
                'user_url' => permalink($user_name),
                'user_email' => $user_email,
                'user_password' => password_hash($user_password, PASSWORD_DEFAULT)
            ]);

            if ($result_user) {
                $success = $user_name . ' kaydınız başarıyla gerçekleşmiştir.';

                User::Login([
                    'user_id' => $db->lastInsertId(),
                    'user_name' => $user_name
                ]); //classes/user.php içindeki User classına diziyi iletiyoruz.

                header('Refresh:2; url=' . site_url());
            }else {
                $error = 'Bir sorun oluştu, lütfen daha sonra tekrar deneyin.';
            }
        }

    }

# // post işlemleri 

require view('register'); // app/view/TEMPLATE/index.php
?>