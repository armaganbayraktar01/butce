<?php require admin_view('static/header'); ?>

<!--content-->
<div class="content">

    <div class="box-">
        <h1><?= post('user_namesurname') ?  post('user_namesurname') : $row_users['user_namesurname'] ?></h1>
    </div>

    <div class="clear" style="height: 10px;"></div>

    <div class="box-">
        <form action="" method="post" class="form label">
            <ul>
                <li>
                    <label for="title">Kullanıcı Adı</label>
                    <div class="form-content">
                        <input type="text" id="title" name="user_name" value="<?= post('user_name') ?  post('user_name') : $row_users['user_name'] ?>">
                    </div>
                </li>
                <li>
                    <label for="title">Email Adresi</label>
                    <div class="form-content">
                        <input type="text" id="title" name="user_email" value="<?=  post('user_email') ?  post('user_email') : $row_users['user_email'] ?>">
                    </div>
                </li>

                <li>
                    <label for="title">Ad Soyad</label>
                    <div class="form-content">
                        <input type="text" id="title" name="user_namesurname" value="<?=  post('user_namesurname') ?  post('user_namesurname') : $row_users['user_namesurname'] ?>">
                    </div>
                </li>

                <li>
                <label for="select">Üye Statü</label>
                <div class="form-content">
                    <select name="user_rank" id="select">
                    <option> Üye Statüsünü seçin </option>
                        <?php foreach(user_ranks() as $user_rank_id => $user_rank):?>
                            <option <?= $row_users['user_rank'] == $user_rank_id ? 'selected' : null ?> value="<?=$user_rank_id ?>"><?=$user_rank ?></option>
                        <?php endforeach; ?>
                        </select>
                </div>
                </li>

                <li>
                    <label>İzinler</label>
                    <div class="form-content">
                        <div class="permissions">
                            <?php foreach ($menus as $url => $menu): ?>
                                <div>
                                    <h3><?= $menu['title'] ?></h3>
                                    <div class="list">
                                        <?php foreach ($menu['permissions'] as $key => $val): ?>
                                            <label>
                                                <input <?=(isset($permissions[$url][$key]) && $permissions[$url][$key] == 1 ? ' checked' : null)?> name="user_permissions[<?=$url?>][<?=$key?>]" value="1" type="checkbox">
                                                <?=$val?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <style>
                    .permissions {
                        border: 1px solid #ccc;
                        background: #fff;
                        max-width: 400px;
                        padding: 10px;
                    }

                    .permissions h3 {
                        font-weight: bold;
                    }
                    .permissions .list{
                        padding-bottom: 15px;
                    }
                    .permissions div:last-child .list {
                        padding-bottom: 0;
                    }
                    .permissions .list label {
                        float: none !important;
                        display: inline-block;
                        width: auto !important;
                        font-weight: normal !important;
                        margin-right: 10px;
                    }
                </style>
                </li>

                <li class="submit">
                    <button name="submit" value="1" type="submit">Güncelle</button>
                </li>
            </ul>
        </form>
    </div>

</div>

<?php require admin_view('static/footer'); ?>