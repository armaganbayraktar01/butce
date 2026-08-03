<?php
if (!permission('users', 'show')){
    permission_page();
}

$totalRecord = $db->from('users')
                  ->select('count(user_id) as total')
                  ->total();

$pageLimit = 1;
$pageParam = 'page';
$pagination = $db->pagination($totalRecord, $pageLimit, $pageParam);

$query_users = $db->from('users')
                  ->orderby('user_id', 'DESC')
                  ->limit($pagination['start'], $pagination['limit'])
                  ->all();

require admin_view('users'); //cms/admin/view/users.php

?>