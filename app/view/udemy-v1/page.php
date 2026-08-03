<?php require view('static/header'); ?>

<section class="jumbotron text-center">
    <div class="container">
        <h1><?=$row_pages['page_title']?></h1>
        <div class="breadcrumb-custom">
            <a href="<?=site_url()?>">Anasayfa</a> /
            <a href="<?=site_url('page/' . permalink($row_pages['page_title']))?>" class="active"><?=$row_pages['page_title']?></a>
        </div>
    </div>
</section>

<div class="container">
    <div class="row">

        <div class="col-md-12">

             <?=htmlspecialchars_decode($row_pages['page_content'])?>

        </div>

    </div>
</div>


<?php require view('static/footer'); ?>