<?php

if (post('submit')) {

    $user_name = post('user_name');
    $user_password = post('user_password');

    if (!$user_name) {
        $error = 'Kullanıcı adı boş bırakılamaz.';
    } elseif (!$user_password) {
        $error = 'Şifre boş bırakılamaz.';
    } else {

        // PDO ile kullanıcıyı veritabanından çekiyoruz
        $stmt = $db->prepare("SELECT * FROM users WHERE user_name = :user_name LIMIT 1");
        $stmt->execute(['user_name' => $user_name]);
        //$row_users = $stmt->fetch(PDO::FETCH_ASSOC);

         # Veritabanında üye kayıtlı mı Kontrolü 
        $row_users = User::userExist($user_name); //classes/user.php içindeki User classı

        if ( !$row_users )
        {
            $error = $user_name . " adında bir kullanıcı veritabanımızda kayıtlı değildir. Lütfen tekrar deneyin."; 

        } else {

            $user_password_hash = $row_users['user_password'];
            $password_verify = password_verify($user_password, $user_password_hash);

            if ( $password_verify )
            {
                if ( $row_users['user_rank'] ==3 ){
                    $error = "Bu bölüme giriş yapmak için yetkiniz bulunmamaktadır.";
                    header('Refresh:2; url=' . site_url());
                } else {
                    $success = "Hoşgeldiniz " . $row_users['user_name'];
                    User::userLogin($row_users); //user.php içindeki user classı
                    header('Refresh:2; url=' . admin_url());
                }

            } else {
                
                $error = "Şifrenizi hatalı girdiniz. Lütfen tekrar deneyin.";
            }

        } 
    }
}


require admin_view('login');
