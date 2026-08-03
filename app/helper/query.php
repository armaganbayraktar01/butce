<?php

function query_menu($id){
    global $db;

    $query_menus = $db->prepare('SELECT * FROM menus WHERE menu_id = :menu_id');
    $result_menus = $query_menus->execute([
    'menu_id' => $id
    ]);

    $row_menus = $query_menus->fetch(PDO::FETCH_ASSOC);

    if($row_menus){
        $json_menu_content = json_decode($row_menus['menu_content'], true);
        return $json_menu_content;
    }
}








?>