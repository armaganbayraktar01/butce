<?php

if (!permission('users', 'edit')){
    permission_page();
}

$id = get('id');

if(!$id){
    header ('Location:', admin_url('users'));
    exit;
}

$row_users = $db->from('users')
                ->where('user_id', $id)
                ->first();

if ( !$row_users ){
    header ('Location:', admin_url('users'));
    exit;
}

if ( post('submit') ){

   //printr($_POST);
    if ( $form_control = form_control_arr('user_permissions')){
// printr($form_control);
        $form_control['user_url'] = permalink( $form_control['user_name']);
        $form_control['user_permissions'] = json_encode($_POST['user_permissions']);

        $query_update_user = $db->update('users')
                                ->where('user_id', $id)
                                ->set($form_control);

        if ( $query_update_user ) {
            $success = $row_users['user_name'] . " başarıyla güncellendi.";
            header ('Location:', admin_url('users'));
        } else {
            $error = "Güncelleme başarılı olmadı. Lütfen daha sonra tekrar deneyin";
        }

    } else {
        $error = 'Eksik alanlar var lütfen doldurun.';
    }
}


$permissions = json_decode($row_users['user_permissions'], true);
require admin_view('edit_user');
?>