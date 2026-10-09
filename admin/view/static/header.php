<!doctype html>
<html lang="en">
<head>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <meta charset="UTF-8">
    <title><?= $meta['site_title'] ? $meta['site_title'] : 'Document' ?></title>
    <meta name="description" content="<?= $meta['site_desc'] ? $meta['site_desc'] : 'Document description' ?>">


    <!-- Özel (Custom) Meta Verileri -->
    <meta name="page-name" content="ev_kredisi_takip">
    <meta name="page-id" content="101">


     <!--styles-->
        
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="<?=admin_public_url('styles/main.css')?>">
    <link rel="stylesheet" href="<?=admin_public_url('vendor/jquery.tagsinput/jquery.tagsinput-revisited.min.css')?>">
    <link rel="stylesheet" href="https://jquery.com">
    <link rel="stylesheet" href="<?=admin_public_url('styles/hfa.css')?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">


    
    <style>
        .etiket {
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 0.9em;
            margin-right: 5px;
            display: inline-block;
        }

        .arttı {
            background-color: #d4edda;
            color: #155724;
        }

        .azaldı {
            background-color: #f8d7da;
            color: #721c24;
        }

        .sabit {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .fiyat {
            background-color: #cce5ff;
            color: #004085;
        }

        .form-list{
            list-style:none;
            padding:0;
            margin:0;
            display:flex;
            flex-direction:column;
            gap:1px;
        }

        .form-list > li{
            background:#fff;
            padding:12px 14px;
            border:1px solid #e6e9ef;
            border-radius:8px;
            box-sizing:border-box;
            transition:all .2s ease;
        }

        .form-list > li:hover{
            border-color:#cfd8e3;
            box-shadow:0 2px 8px rgba(0,0,0,0.04);
        }

        .form-list label{
            display:block;
            font-weight:600;
            margin-bottom:6px;
            color:#34495e;
            font-size:13.5px;
        }

        .form-list input,
        .form-list select,
        .form-list textarea{
            width:100%;
            padding:2px 2px;
            border:1px solid #dcdfe6;
            border-radius:6px;
            box-sizing:border-box;
            outline:none;
            transition:.2s;
            font-size:14px;
        }

        .form-list input:focus,
        .form-list select:focus,
        .form-list textarea:focus{
            border-color:#007bff;
            box-shadow:0 0 0 2px rgba(0,123,255,0.12);
        }

        .form-submit{
            display:flex;
            
            /* justify-content:flex-end; */
            background:transparent !important;
            border:none !important;
            padding:0 !important;
        }

        .form-btn{
            background:linear-gradient(135deg,#007bff,#0056d2);
            color:#fff;
            border:none;
            padding:10px 18px;
            border-radius:8px;
            font-weight:600;
            cursor:pointer;
            transition:.2s;
        }

        .form-btn:hover{
            transform:translateY(-1px);
            box-shadow:0 6px 14px rgba(0,123,255,0.25);
        }

        .menu-container{
            max-width:1400px;
            margin:auto;
            padding:24px;
        }

        .report-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:24px;
        }

        .form-card{
            background:#fff;
            border:1px solid #e4e9f1;
            border-radius:12px;
            padding:24px;
        }

        .full-width-card{
            grid-column:1/-1;
            background:#fff;
            border:1px solid #e4e9f1;
            border-radius:12px;
            padding:24px;
        }

        .form-group{
            display:flex;
            flex-direction:column;
            margin-bottom:16px;
        }

        .form-group:last-child{
            margin-bottom:0;
        }

        .form-group label{
            margin-bottom:6px;
            font-weight:600;
            color:#2c3e50;
        }

        .form-group input,
        .form-group select,
        .form-group textarea{
            width:100%;
            border:1px solid #dce3eb;
            border-radius:8px;
            padding:10px 12px;
            box-sizing:border-box;
        }

        .form-group textarea{
            min-height:60px;
            resize:vertical;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus{
            outline:none;
            border-color:#3498db;
        }

        .form-actions{
            grid-column:1/-1;
            display:flex;
            justify-content:flex-end;
        }

        .save-btn{
            border:none;
            background:#2e7d32;
            color:#fff;
            border-radius:8px;
            padding:12px 32px;
            cursor:pointer;
            font-weight:600;
        }

        @media(max-width:992px){

            .report-grid{
                grid-template-columns:1fr;
            }
        }

            /* 1. Ürün Havuzu Kaydırma Çubuğu Düzenlemesi (Opsiyonel / İnce Modern Yapı) */
            #product_pool_container {
                scrollbar-width: thin;
            }
            #product_pool_container::-webkit-scrollbar {
                width: 6px;
            }
            #product_pool_container::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 4px;
            }
            #product_pool_container::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }

            /* 2. Havuz Butonlarının Tasarımı (Aykırı Ürün Etiketleriyle Birebir Aynı) */
            .pool-product-btn {
                display: inline-flex !important;
                align-items: center !important;
                background: #e9ecef !important; /* Etiket arka plan rengi */
                color: rgb(73, 80, 87) !important;      /* Etiket yazı rengi */
                padding: 4px 10px !important;    /* Etiket iç boşluğu */
                border-radius: 4px !important;   /* Etiket köşe ovali (20px yuvarlak yerine dikdörtgen) */
                font-size: 13px !important;      /* Etiket font boyutu */
                font-weight: 500 !important;     /* Etiket font kalınlığı */
                gap: 8px !important;             /* Etiket içi ikon/metin boşluğu */
                border: 1px solid #dee2e6 !important; /* Etiket sınır çizgisi */
                cursor: pointer !important;
                text-shadow: none !important;
                margin: 0 !important; */
                box-sizing: border-box !important; */
                transition: all 0.15s ease-in-out !important; */
            }

            /* 3. 🔥 Havuz Butonları İçin Hover ve Tıklama Efektleri */
            .pool-product-btn:hover {
                background: #dee2e6 !important; /* Üzerine gelindiğinde hafifçe koyulaşma */
                color: #212529 !important;      /* Yazı rengini biraz daha netleştirme */
                border-color: #ced4da !important;
                transform: translateY(-1px);    /* Çok hafif yukarı yükselme efekti */
                box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
            }

            .pool-product-btn:active {
                transform: translateY(0);
                box-shadow: none !important;
            }

        </style>














    <!--scripts-->
    
    <script src="<?=admin_public_url('scripts/jquery-1.12.2.min.js')?>"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>    <script src="<?=admin_public_url('vendor/jquery.tagsinput/jquery.tagsinput-revisited.min.js')?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
   
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.js"></script>




    <script>
        var api_url = '<?= admin_url('api') ?>';
        var app_url = '<?= site_url('app/') ?>';
        
        if (typeof tags === 'undefined') { window.tags = []; }
    </script>





    <script src="<?=admin_public_url('scripts/admin.js')?>"></script>
    <script src="<?=admin_public_url('scripts/api.js')?>"></script>
    

</head>
<body>
    
<div class="success-msg">
    <a href="#" class="success-close-btn"><i class="fa fa-times"></i></fa-times></a>
    <div></div>
</div>

<!-- Admin girişi var ise gösterilecek kısım -->
<?php if (session('user_rank') && session('user_rank') != 3): ?>


    <!--navbar-->
    <div class="navbar">
        <ul dropdown>
            <li>
                <a href="#">
                    <span class="fa fa-home"></span>
                    <span class="title">
                        <?=settings('title')?>
                    </span>
                </a>
            </li>
            <li>
                <a href="<?= admin_url('logout') ?>">
                    <span class="fa fa-home"></span>
                    <span class="title">
                        Çıkış Yap
                    </span>
                </a>
            </li>

            <!-- <li>
                <a href="#">
                    <span class="fa fa-comment"></span>
                    1
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="fa fa-plus"></span>
                    <span class="title">New</span>
                </a>
                <ul>
                    <li>
                        <a href="#">
                            New Post
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            New Page
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            New Category
                        </a>
                    </li>
                </ul>
            </li> -->

        </ul>
    </div>

    <!--sidebar-->
    <div class="sidebar">

           <ul>
           
            <?php foreach ($menus as $menu_url => $menu): if(!permission($menu_url, 'show')) continue; ?>

            <li class="<?= (route(1) == $menu_url) || isset($menu['submenus'][route(1)]) ? 'active': null?>"> 

                <a href="<?=admin_url($menu_url) ?>">
                    <span class="fa fa-<?= $menu['icon']; ?>"></span>
                    <span class="title"><?= $menu['title']; ?></span>
                </a>

                <?php if(isset($menu['submenus'])): ?>
                    
                <ul class="sub-menu">
                    <?php foreach ($menu['submenus'] as $submenu_url => $submenu_title): ?>
                    <li>
                        <a href="<?= admin_url($submenu_url); ?>"><?=$submenu_title;?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

            </li>
            <?php endforeach; ?>

            <li class="line">
                <span></span>
            </li>
        </ul>



        <a href="#" class="collapse-menu">
            <span class="fa fa-arrow-circle-left"></span>
            <span class="title">
                Collapse menu
            </span>
        </a>

    </div>

    <!--content-->
    <div class="content">

        <!-- =========================================================================
            1. KISIM: YEREL BİLDİRİM ŞERİTLERİ (SESSION & DEĞİŞKEN UYUMLU SÜRÜM)
            ========================================================================= -->
        <?php 
        // Eğer controller yukarısında session başlatılmadıysa emniyet kilidi
        if (session_status() === PHP_SESSION_NONE) { session_start(); } 

        // AJAX yenilemesinden gelen session verilerini yerel değişkenlere aktar
        if (isset($_SESSION['forex_error'])) { $error = $_SESSION['forex_error']; unset($_SESSION['forex_error']); }
        if (isset($_SESSION['forex_success'])) { $success = $_SESSION['forex_success']; unset($_SESSION['forex_success']); }
        ?>

        <?php if (isset($error) && !empty($error)): ?>
            <div class="message error box-">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success) && !empty($success)): ?>
            <!-- 🎯 DÜZELTME: Sınıf ismi kurumsal yeşil standart olan 'success' moduna çekildi -->
            <div class="message success box-">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>


<?php endif; ?>
<!-- /Admin girişi var ise gösterilecek kısım -->