<?php require admin_view('static/header'); ?>

    <div class="box-">
        <h1>
            Ayarlar
        </h1>
    </div>

    <div class="clear" style="height: 10px;"></div>

    <div class="box-" tab>

        <div class="admin-tab">
            <ul tab-list>
                <li>
                    <a href="#">Genel</a>
                </li>
                <li>
                    <a href="#">Bakım Modu</a>
                </li>
                <li>
                    <a href="#">Tema</a>
                </li> 
                <li>
                    <a href="#">SMTP Mail</a>
                </li>
            </ul>
        </div>

        <form action="" method="post" class="form label">
            <div class="tab-container">
                <div tab-content>
                    <ul>
                        <li>
                            <label>Site Title</label>
                            <div class="form-content">
                                <input type="text" name="settings[title]" value='<?=settings('title')?>'>
                            </div>
                        </li>

                        <li>
                            <label>Site Description</label>
                            <div class="form-content">
                                <input type="text" name="settings[description]" value='<?=settings('description')?>'>
                            </div>
                        </li>

                        <li>
                            <label>Site Keywords</label>
                            <div class="form-content">
                                <input type="text" name="settings[keywords]" value='<?=settings('keywords')?>'>
                            </div>
                        </li>

                        <li>
                            <label>Site Teması</label>
                            <div class="form-content">
                                <select name="settings[theme]">
                                    <option value="">- Tema seçin -</option>
                                    <?php foreach($themes as $theme): ?>
                                    <option <?=settings('theme') == $theme ? 'selected' : null?> value="<?= $theme ?>"><?= $theme ?></option>
                                    <?php endforeach; ?>
                                </select>                     
                            </div>
                        </li>
                    </ul>
                </div>
                <div tab-content>     
                    <ul>
                        <li>
                            <label>Bakım Modu</label>
                            <div class="form-content">
                                <select name="settings[maintenance_mode]">
                                    <option value="">- Seçin -</option>
                                    <option <?=settings('maintenance_mode') == 1 ? 'selected' : null?> value="1">Açık</option>
                                    <option <?=settings('maintenance_mode') == 2 ? 'selected' : null?> value="2">Kapalı</option>
                                </select>                     
                            </div>
                        </li>

                        <li>
                            <label>Bakım Modu Title</label>
                            <div class="form-content">
                                <input type="text" name="settings[maintenance_mode_title]" value='<?=settings('maintenance_mode_title')?>'>
                            </div>
                        </li>

                        <li>
                            <label>Bakım Modu Description</label>
                            <div class="form-content">
                                <input type="text" name="settings[maintenance_mode_description]" value='<?=settings('maintenance_mode_description')?>'>
                            </div>
                        </li>

                    </ul>
                </div>
                <div tab-content>              
                    <ul>
                        <li>
                            <label>Logo</label>
                            <div class="form-content">
                                <input type="text" name="settings[site_logo]" value='<?=settings('site_logo')?>'>
                            </div>
                        </li>

                        <li>
                            <label>Search Box Placeholder</label>
                            <div class="form-content">
                                <input type="text" name="settings[search_placeholder]" value='<?=settings('search_placeholder')?>'>
                            </div>
                        </li>

                        <li>
                            <label>Footer about</label>
                            <div class="form-content">
                                <textarea class="editor" rows="4" cols="50" name="settings[footer_about]"><?=settings('footer_about')?></textarea>
                            </div>
                        </li>
                        
                        <li>
                            <label>Facebook</label>
                            <div class="form-content">
                                <input type="text" name="settings[footer_facebook]" value='<?=settings('footer_facebook')?>'>
                            </div>
                        </li>

                        <li>
                            <label>Instagram</label>
                            <div class="form-content">
                                <input type="text" name="settings[footer_instagram]" value='<?=settings('footer_instagram')?>'>
                            </div>
                        </li>

                        <li>
                            <label>Twitter</label>
                            <div class="form-content">
                                <input type="text" name="settings[footer_twitter]" value='<?=settings('footer_twitter')?>'>
                            </div>
                        </li>

                        <li>
                            <label>Linkedin</label>
                            <div class="form-content">
                                <input type="text" name="settings[footer_linkedin]" value='<?=settings('footer_linkedin')?>'>
                            </div>
                        </li>
                    </ul>
                </div>
                <div tab-content>
                    <ul>
                        <li>
                            <label>SMTP Host</label>
                            <div class="form-content">
                                <input type="text" name="settings[smtp_host]" value='<?=settings('smtp_host')?>'>
                            </div>
                        </li>

                        <li>
                            <label>SMTP E-Posta Adresi</label>
                            <div class="form-content">
                                <input type="text" name="settings[smtp_email]" value='<?=settings('smtp_email')?>'>
                            </div>
                        </li>

                        <li>
                            <label>SMTP Şifre</label>
                            <div class="form-content">
                                <input type="text" name="settings[smtp_password]" value='<?=settings('smtp_password')?>'>
                            </div>
                        </li>
                        
                        <li>
                            <label>SMTP Post</label>
                            <div class="form-content">
                                <input type="text" name="settings[smtp_port]" value="<?= settings('smtp_port') ?>">
                            </div>
                        </li>

                        <li>
                            <label>SMTP Secure</label>
                            <div class="form-content">
                                <select name="settings[smtp_secure]">
                                    <option <?= settings('smtp_secure') == 'ssl' ? ' selected' : null ?> value="ssl">SSL</option>
                                    <option <?= settings('smtp_secure') == 'tls' ? ' selected' : null ?> value="tls">TLS</option>
                                </select>
                            </div>
                        </li>

                        <li>
                            <label>SMTP Gönderen <br> E-posta Adresi</label>
                            <div class="form-content">
                                <input type="text" name="settings[smtp_send_email]" value="<?= settings('smtp_send_email') ?>">
                            </div>
                        </li>

                        <li>
                            <label>SMTP Gönderen <br> Ad-Soyad</label>
                            <div class="form-content">
                                <input type="text" name="settings[smtp_send_name]" value="<?= settings('smtp_send_name') ?>">
                            </div>
                        </li>
                    </ul>
                </div>
                <ul>
                    <li class="submit">
                        <input type="hidden" name="submit" value="1">
                        <button type="submit">Güncelle</button>
                    </li>
                </ul>
            </div>
        </form>
    </div>
<?php require admin_view('static/footer'); ?>