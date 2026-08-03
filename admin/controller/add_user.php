<?php

    # Permission
        if (!permission('users', 'add')) {

            permission_page();

        }
    ## Permission ->

    # Submit Kısmı
        if (post('submit')) {

            $user_name = post('user_name');
            $user_password = post('user_password');
            $user_email = post('user_email');

        }
    ## Submit Kısmı ->

    # Submit Kısmı ->

        if (post('submit')) {

            if (!$user_name) {
                $error = 'Kullanıcı adı boş bırakılamaz.';
            } elseif (!$user_email) {
                $error = 'E-posta adresi boş bırakılamaz.';
            } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Lütfen geçerli bir e-posta adresi yazın.';
            } elseif (!$user_password) {
                $error = 'Şifre boş bırakılamaz.';

            } else {

                # DB'de üyenin var olup olmadığını kontrol et. Yoksa Kayıt et

                    $row_users = User::userExist($user_name, $user_email);

                    if ($row_users) {

                        $error = $user_name . ' kullanıcı adı ya da e-posta adresi zaten kullanılıyor. Lütfen başka bir kullanıcı adı ya da e-posta adresi yazıp tekrar deneyin.';
                        header('Refresh:2; url=' . $_SERVER['HTTP_REFERER']);

                    } else {

                        $query_adduser = $db->insert('users')
                                            ->set(array(
                                                'user_name' => $user_name,
                                                'user_password' => password_hash($user_password, PASSWORD_DEFAULT),
                                                'user_email' => $user_email,
                                                'user_url' => permalink($user_name)
                                                ));
                            
                    }
                ## DB'de üyenin var olup olmadığını kontrol et. Yoksa Kayıt et ->
            }

        }

    ## Submit Kısmı ->

require admin_view('add_user');
