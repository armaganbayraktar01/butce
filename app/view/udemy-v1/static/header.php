<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title><?=$meta['site_title']?></title>

    <?php if (isset($meta['site_desc'])): ?>
    <meta name='description' content="<?=$meta['site_desc']?>">
    <?php endif; ?>

    <?php if (isset($meta['site_keyw'])): ?>
    <meta name='keyword' content="<?=$meta['site_keyw']?>">
    <?php endif; ?>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>


    <script>
        var api_url = '<?=site_url('api')?>';
    </script>

    <script src="<?=public_url('scripts/api.js')?>"></script>


    <link rel="stylesheet" href="<?=public_url('styles/main.css')?>">

</head>


<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?=site_url()?>"><?=settings('site_logo')?></a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Header Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <!-- NavMenu-->
            <ul class="navbar-nav mr-auto">
                <?php foreach(query_menu(1) as $key => $headermenu): ?>
                    <!-- NavMenu Submenu -->
                    <li class="nav-item <?= isset($headermenu['submenu']) ? ' dropdown' : null ?>">
                        <?php if(isset($headermenu['submenu'])): ?>
                            <a class="nav-link dropdown-toggle" href="<?= $headermenu['url'] ?>" id="navbarDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?= $headermenu['title'] ?>
                            </a>

                            
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <?php foreach($headermenu['submenu'] as $submenu_key => $header_submenu): ?>
                                    <a class="dropdown-item" href="<?= $header_submenu['url'] ?>"><?= $header_submenu['title'] ?></a>
                                <?php endforeach; ?>
                            </div>
                            <!-- /NavMenu Submenu -->

                            <?php else: ?>
                            <a class="nav-link" href="<?= $headermenu['url'] ?>"><?= $headermenu['title'] ?></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <!-- /NavMenu-->

            <!-- Search-->
            <form class="form-inline my-2 my-lg-0 mr-3">
                <input class="form-control mr-sm-2" type="search" placeholder="<?=settings('search_placeholder')?>" aria-label="Search">
                <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Ara</button>
            </form>
            <!-- Search-->

            <!-- LoginButton -->
            <?php if(session('user_id')): ?>

                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= session('user_name');?>
                    </button>
                
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="<?=site_url('profile')?>">Profil</a>
                        <a class="dropdown-item" href="<?=site_url('exit')?>">Çıkış Yap</a>
                    </div>
                </div>

                <?php else: ?>

                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Giriş Yap
                    </button>
                
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="<?=site_url('login')?>">Giriş Yap</a>
                        <a class="dropdown-item" href="<?=site_url('register')?>">Kayıt Ol</a>
                    </div>
                </div>
            
            <?php endif; ?>
            <!-- /LoginButton -->

        </div>
        <!-- /Header Navigation Menu -->
    </div>
</nav>