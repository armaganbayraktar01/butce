<?php

if ($data = form_control()){

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
        $json['error'] = 'Lütfen geçerli bir e-posta adresi girin.';
    } else {

        $query = $db->insert('contact')
            ->set([
                'contact_name' => $data['name'],
                'contact_email' => $data['email'],
                'contact_phone' => $data['phone'],
                'contact_subject' => $data['subject'],
                'contact_message' => $data['message']
            ]);

        if ($query) {
            $json['success'] = 'Mesajınız bize ulaştı, teşekkür ederiz.';
        } else {
            $json['error'] = 'Bir sorun oluştu ve mesajınız gönderilemedi!';
        }

    }

} else {
    $json['error'] = 'Lütfen tüm alanları doldurup tekrar deneyin.';
}