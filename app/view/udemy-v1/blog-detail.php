<?php require view('static/header') ?>

<section class="jumbotron text-center">
    <div class="container">
        <h1><?= $row_findpost['post_title'] ?></h1>
        <div class="breadcrumb-custom">
            <a href="<?= site_url() ?>">Anasayfa</a> /
            <a href="<?= site_url('blog') ?>">Blog</a> /
            <a href="<?= site_url('blog/kategori/' . $row_findpost['category_url']) ?>" class="active"><?=$row_findpost['category_name'] ?></a> /
            <a href="<?= site_url('blog/' .  $row_findpost['post_url']) ?>" class="active"><?=$row_findpost['post_title'] ?></a>
        </div>
    </div>
</section>
<div class="container">
    <div class="row">
        <div class="col-md-12">

            <div class="card mb-3">
                <div class="card-header">
                <?=$row_findpost['category_name'] ?>
                    <div class="date">
                    <?=timeConvert($row_findpost['post_date']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <p>
                    <?= htmlspecialchars_decode($row_findpost['post_short_content']) ?>
                    </p>

                    <p>Kod</p>
                    <pre>
                        <?= htmlspecialchars_decode($row_findpost['post_content']) ?>
                    </pre>

                </div>
            </div>

            <div class="card text-center mb-3">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#comments" role="tab" aria-controls="comments" aria-selected="true">Yorumlar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="share-tab" data-toggle="tab" href="#share" role="tab" aria-controls="share" aria-selected="false">Paylaş</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="comments" role="tabpanel" aria-labelledby="home-tab">

                            <div class="media comment-box">
                                <div class="media-left">
                                    <a href="#">
                                        <img class="img-responsive user-photo" src="https://ssl.gstatic.com/accounts/ui/avatar_2x.png">
                                    </a>
                                </div>
                                <div class="media-body">
                                    <h6 class="media-heading">Tayfun Erbilen</h6>
                                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>

                                </div>
                            </div>

                            <div class="media comment-box">
                                <div class="media-left">
                                    <a href="#">
                                        <img class="img-responsive user-photo" src="https://pbs.twimg.com/profile_images/931133407291150336/l8IeLCoc_400x400.jpg">
                                    </a>
                                </div>
                                <div class="media-body">
                                    <h6 class="media-heading">Tayfun Erbilen</h6>
                                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>

                                </div>
                            </div>

                            <h5 class="card-title">İlk yorumu siz yazın!</h5>
                            <p class="card-text">Bu konu için hiç yorum yazılmamış, ilk yorumu siz yazarak destek verin!</p>
                            <a href="#" class="btn btn-primary">Yorum Yaz</a>

                        </div>
                        <div class="tab-pane fade" id="share" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="share mb-4">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= site_url('blog/' .  $row_findpost['post_url']) ?>" class="facebook" data-toggle="tooltip" data-placement="top" title="Facebook'da Paylaş">
                                    <span class="fab fa-facebook-f"></span>
                                </a>
                                <a href="https://twitter.com/home?status=<?= site_url('blog/' .  $row_findpost['post_url']) ?> " class="twitter" data-toggle="tooltip" data-placement="top" title="Tweet at">
                                    <span class="fab fa-twitter"></span>
                                </a>
                                <a href="https://pinterest.com/pin/create/button/?url=<?= site_url('blog/' .  $row_findpost['post_url']) ?>&media=&description=" class="gplus" data-toggle="tooltip" data-placement="top" title="Pinterest'te Paylaş">
                                    <span class="fab fa-pinterest"></span>
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= site_url('blog/' .  $row_findpost['post_url']) ?>&title=&summary=&source=" class="linkedin" data-toggle="tooltip" data-placement="top" title="Linkedin'de Paylaş">
                                    <span class="fab fa-linkedin-in"></span>
                                </a>
                                <a href="whatsapp://send?text=<?= site_url('blog/' .  $row_findpost['post_url']) ?>" class="whatsapp" data-toggle="tooltip" data-placement="top" title="Whatsapp'dan Gönder">
                                    <span class="fab fa-whatsapp"></span>
                                </a>
                                <a href="mailto:info@example.com?&subject=&body=<?= site_url('blog/' .  $row_findpost['post_url']) ?> " class="mail" data-toggle="tooltip" data-placement="top" title="E-posta olarak Gönder">
                                    <span class="fa fa-envelope"></span>
                                </a>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Bağlantı Linki</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" onclick="this.select()" value="<?= site_url('blog/' .  $row_findpost['post_url']) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    Yorum Yaz
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="email">E-posta Adresiniz</label>
                            <input type="email" class="form-control" id="email">
                            <small id="emailHelp" class="form-text text-muted">E-posta adresiniz yorumlar listelenirken gizli kalacaktır.</small>
                        </div>
                        <div class="form-group">
                            <label for="name">Adınız-soyadınız</label>
                            <input type="text" class="form-control" id="name">
                        </div>
                        <div class="form-group">
                            <label for="yorum">Yorumunuz</label>
                            <textarea name="yorum" id="yorum" cols="30" rows="5" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Gönder</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require view('static/footer') ?>