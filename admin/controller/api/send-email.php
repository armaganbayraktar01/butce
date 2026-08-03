<?php

if ($data = form_control()) {

    $send = send_email($data['email'], $data['name'], $data['subject'], nl2br($data['message']));
    if ($send) {
        $json['success'] = 'Mesajınız başarıyla gönderildi!';
    } else {
        $json['error'] = 'Mesaj gönderilirken bir hata oluştu.';
    }

} else {
    $json['error'] = 'Lütfen tüm bilgileri doldurun!';
}