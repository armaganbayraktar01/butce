<?php

if (!permission('index', 'show')){
    permission_page();
}

require admin_view('index'); //cms/admin/view/index.php
?>


