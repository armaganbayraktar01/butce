<?php require view('static/header') ?>

<section class="jumbotron text-center">
    <div class="container">
        <h1>BLOG</h1>
        <div class="breadcrumb-custom">
            <a href="<?=site_url()?>">Anasayfa</a> /
            <a href="<?=site_url('blog')?>" class="active">Blog</a>
        </div>
    </div>
</section>
<div class="container">
    <div class="row">
        <div class="col-md-8">

            <h4 class="pb-3">Son Konular</h4>

            <?php if ($query_posts): ?>

                <?php foreach ($query_posts as $row_post): ?>

                    <div class="card mb-3">

                        <div class="card-header">

                            <?= $row_post['category_name'] ?>

                            <div class="date">
                                <?= timeConvert($row_post['post_date']) ?>
                            </div>

                        </div>

                        <div class="card-body">
                            <h5 class="card-title"><?= $row_post['post_title'] ?></h5>
                            <p class="card-text">
                                <?= htmlspecialchars_decode($row_post['post_short_content']) ?>
                            </p>
                            <a href="<?= site_url('blog/' . $row_post['post_url'] ) ?>" class="btn btn-dark">Görüntüle</a>
                        </div>
                        
                    </div>

                <?php endforeach; ?>

                <?php if ($totalRecord > $pageLimit): ?>

                    <div class="pagination-container text-center mb-4">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination">
                                <li class="page-item">
                                    <a class="page-link" href="<?= site_url('blog?' . $pageParam . '=' . $db->prevPage()) ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                </li>
                                
                                <?= $db->showPagination(site_url('blog?' . $pageParam . '=[page]'), 'active', true) ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= site_url('blog?' . $pageParam . '=' . $db->nextPage()) ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>

                <?php endif; ?>

            <?php else: ?>

                <div class="alert alert-warning">
                    Blog için henüz hiç konu eklenmedi.                
                </div>

            <?php endif; ?>

        </div>

        <div class="col-md-4">
            <h4 class="pb-3">
                <i class="fa fa-folder"></i>
                Kategoriler
            </h4>

            <ul class="list-group mb-4">      
                <?php foreach (Blog::Categories() as $category): ?>
                    <li class="list-group-item">
                        <a href="<?=site_url('blog/kategori/' . $category['category_url'])?>" style="color: #333;" class="d-flex justify-content-between align-items-center">
                        <?=$category['category_name']?>
                            <span class="badge badge-dark badge-pill"><?= $category['category_total'] ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h4 class="pb-3">
                <i class="fa fa-hashtag"></i>
                Etiketler
            </h4>

            <a href="#" class="badge badge-light badge-pill">html5 video</a>
            <a href="#" class="badge badge-light badge-pill">html5 audio</a>
            <a href="#" class="badge badge-light badge-pill">css ie7</a>
            <a href="#" class="badge badge-light badge-pill">jquery dersleri</a>
            <a href="#" class="badge badge-light badge-pill">css3 calc()</a>
            <a href="#" class="badge badge-light badge-pill">php array_shift()</a>
            <a href="#" class="badge badge-light badge-pill">gökhan toy</a>
            <a href="#" class="badge badge-light badge-pill">aile</a>
            <a href="#" class="badge badge-light badge-pill">hayat</a>
        </div>
    </div>
</div>
    

<?php require view('static/footer') ?>