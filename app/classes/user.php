<?php

    class User {

        public static function userRegister($data){
            global $db;

            $query_user = $db->prepare('INSERT INTO users SET user_name = :user_name, user_email = :user_email, user_password = :user_password, user_url = :user_url');
            return $query_user->execute($data);
        }

        public static function userLogin($data){
            $_SESSION['user_id'] = $data['user_id'];
            $_SESSION['user_name'] = $data['user_name'];
            $_SESSION['user_rank'] = $data['user_rank'];
            $_SESSION['user_permissions'] = $data['user_permissions'];
        }

        # Veritabanında üye kayıtlı mı Kontrolü 
        public static function userExist($user_name, $user_email = '@@'){

            global $db;

            $query_users = $db->prepare('SELECT * FROM users WHERE user_name = :user_name || user_email = :user_email');
            $query_users->execute([
                'user_name' => $user_name,
                'user_email' => $user_email
            ]);

            return $query_users->fetch(PDO::FETCH_ASSOC);

        }

    
    }

?>