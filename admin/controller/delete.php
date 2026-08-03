<?php

$table = get('table');
$column = get('column');
$id = get('id');

if (!permission($table, 'delete')){
    permission_page();
}


$query_delete = $db->prepare('DELETE FROM ' . $table . ' WHERE ' . $column . ' = :id');
$result_delete = $query_delete->execute([
    'id' => $id
]);

header('Location:' . $_SERVER['HTTP_REFERER']);
exit;

?>