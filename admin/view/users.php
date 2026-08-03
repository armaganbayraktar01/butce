<?php require admin_view('static/header'); ?>

<div class="box-">
    <h1>
        Üyeler
        <?php if (permission('users', 'add')): ?>
        <a href="<?= admin_url('add_user') ?>">Üye Ekle</a>
        <?php endif; ?>

    </h1>
</div>

<div class="clear" style="height: 10px;"></div>

<div class="table">
    <table>
        <thead>
            <tr>
                <th>Üye Kullanıcı adı</th>
                <th>Üye E-mail Adresi</th>
                <th>Üye Statü</th>
                <th>Üye Ad Soyad</th>
                <th class="hide">Eklenme Tarihi</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>

            <?php foreach($query_users as $row_users): ?>
                <tr>
                    <td class="hide">
                        <a href="#"><?=$row_users['user_name']?></a>
                    </td>
                    <td class="hide">
                        <a href="#"><?=$row_users['user_email']?></a>
                    </td>
                    <td class="hide">
                        <a href="#"><?= user_ranks($row_users['user_rank']) ?></a>
                    </td>
                    <td class="hide">
                        <a href="#"><?=$row_users['user_namesurname']?></a>
                    </td>                     
                    <td>
                        <span class="date"><?=timeConvert($row_users['user_date'])?></span>
                    </td>
                    <td>
                        <?php if (permission('users', 'edit')): ?>
                        <a href="<?= admin_url('edit_user?id=' . $row_users['user_id']) ?>" class="btn">Düzenle</a>
                        <?php endif; ?>
                        <?php if (permission('users', 'delete')): ?>
                        <a href="<?= admin_url('delete?table=users&column=user_id&id=' . $row_users['user_id']) ?>" class="btn">Sil</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>

    <div class="pagination">
        <ul><?php echo $db->showPagination(admin_url(route(1)) . '?'.$pageParam.'=[page]'); ?></ul>
    </div>
</div>
        
<?php require admin_view('static/footer'); ?>