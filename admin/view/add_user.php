    <?php require admin_view('static/header'); ?>
   
     
    <div style="height:50px;">
    <div class="box-">
        <h1>Üye Ekle</h1>
    </div>

    <div class="clear" style="height: 10px;"></div>

    <div class="box-">
        <form action="" method="post" class="form label">
            <ul>
                <li>
                    <label>Kullanıcı Adı</label>
                    <div class="form-content">
                        <input type="text" name="user_name" value='<?= post('submit') ? post('user_name') : FALSE  ?>' placeholder="Kullanıcı adınızı yazın..">
                    </div>
                </li>

                <li>
                    <label>E-Posta</label>
                    <div class="form-content">
                        <input type="text" name="user_email" value='<?= post('submit') ? post('user_name') : FALSE  ?>' placeholder="E-Posta adresinizi yazın..">
                    </div>
                </li>

                <li>
                    <label>Şifre</label>
                    <div class="form-content">
                        <input type="text" name="user_password" placeholder="*******">
                    </div>
                </li>

                <li class="submit">
                    <input type="hidden" name="submit" value="1">
                    <button type="submit">Gönder</button>
                </li>
            </ul>
        </form>
    </div>

    </div>
    <?php require admin_view('static/footer'); ?>