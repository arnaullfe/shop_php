<?php
include_once("../../modals/Database.php");
include_once('../../controllers/UserTokenController.php');
include_once ('../../controllers/MainController.php');
session_start();
$database = new Database();
//pagina
$categories = $database->executeQuery("SELECT * FROM shop.productCategory WHERE id IN(SELECT shop.products.category_id FROM shop.products WHERE activated=1) AND activated=1", array(1));
$discounts = $database->executeQuery('SELECT shop.discounts.*,shop.products.id as "product_id",shop.products.name as "product_name",shop.products.description as "product_desc",shop.products.price_iva,shop.images_product.url  FROM shop.discounts inner join shop.products ON shop.products.id = shop.discounts.id_product LEFT JOIN shop.images_product ON shop.discounts.id_product=shop.images_product.id_product  where start_date<=now() AND end_date>=now() AND highlight=?', array(1));
$products = $database->executeQuery('SELECT shop.products.*,shop.images_product.url,shop.tags.name "tag_name",shop.tags.color as "tag_color",shop.discounts.discount,shop.productCategory.name as "category_name" FROM shop.products 
	LEFT JOIN shop.images_product ON shop.products.id = shop.images_product.id_product 
    LEFT JOIN shop.discounts ON shop.products.id  = shop.discounts.id_product
		AND shop.discounts.start_date<=now() 
        AND shop.discounts.end_date>=now()
        AND shop.discounts.discount = (SELECT max(shop.discounts.discount) FROM shop.discounts WHERE shop.discounts.id_product = shop.products.id)
    LEFT JOIN shop.tags ON shop.products.tag_id = shop.tags.id
    LEFT JOIN shop.productCategory ON shop.products.category_id = shop.productCategory.id 
		WHERE shop.products.activated = 1 
		AND shop.productCategory.activated = 1 
    GROUP BY shop.products.id;',array());
$highlights = $database->executeQuery('SELECT shop.highlights.highlight_type,shop.highlights.url,shop.highlights.title,shop.productCategory.name as "category_name",shop.productCategory.id as "category_id",
		shop.products.name as "product_name",shop.products.id as "product_id",shop.products.description as "product_desc",
        shop.discounts.discount 
        FROM shop.highlights
        LEFT JOIN shop.productCategory ON shop.highlights.category_id = shop.productCategory.id
		LEFT JOIN shop.products ON shop.highlights.product_id = shop.products.id
        LEFT JOIN shop.discounts ON shop.highlights.product_id = shop.discounts.id_product
        AND shop.discounts.discount = (SELECT max(shop.discounts.discount) FROM shop.discounts WHERE shop.discounts.id_product= shop.highlights.product_id
                                    AND shop.discounts.start_date<=now() AND shop.discounts.end_date>=now())
        WHERE shop.products.activated = 1 OR shop.productCategory.activated = 1
        ORDER BY highlight_type ASC',array());
//cart
$cart_user = $database->executeQuery("SELECT id FROM carts WHERE user_id = ?",array($_SESSION["user_id"]));
$cartItems = $database->executeQuery('SELECT shop.cartItems.*,
shop.products.name as "product_name",shop.products.description as "product_desc",shop.products.price_iva as "product_price",shop.products.id as "product_id",
shop.discounts.discount,shop.images_product.url
FROM shop.cartItems 
LEFT JOIN shop.products ON shop.cartItems.product_id = shop.products.id
LEFT JOIN shop.discounts ON shop.cartItems.product_id = shop.discounts.id_product AND
shop.discounts.discount = (SELECT max(discount) FROM shop.discounts WHERE shop.discounts.id_product = shop.cartItems.product_id)
LEFT JOIN shop.images_product ON shop.cartItems.product_id = shop.images_product.id_product
WHERE shop.products.activated = 1', array($_SESSION["user_id"]));
$items_number = $database->executeQuery('SELECT count(*) as "items" FROM shop.cartItems WHERE cart_id = (SELECT id FROM shop.carts WHERE user_id=?)', array($_SESSION["user_id"]))[0]["items"];
$final_price = calculateItemsPrices($cartItems);
$money_saved = calculatSave($cartItems);
$database->closeConnection();
?>
    <!DOCTYPE html>
    <html lang="zxx">
    <head>
        <!-- Meta Tag -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name='copyright' content=''>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- Title Tag  -->
        <title>Eshop</title>
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="images/favicon.png">
        <!-- Web Font -->
        <link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap"
              rel="stylesheet">

        <!-- StyleSheet -->
        <script src="https://kit.fontawesome.com/e7269a261c.js" crossorigin="anonymous"></script>

        <!-- Bootstrap -->
        <link rel="stylesheet" href="css/bootstrap.css">
        <!-- Magnific Popup -->
        <link rel="stylesheet" href="css/magnific-popup.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="css/font-awesome.css">
        <!-- Fancybox -->
        <link rel="stylesheet" href="css/jquery.fancybox.min.css">
        <!-- Themify Icons -->
        <link rel="stylesheet" href="css/themify-icons.css">
        <!-- Nice Select CSS -->
        <link rel="stylesheet" href="css/niceselect.css">
        <!-- Animate CSS -->
        <link rel="stylesheet" href="css/animate.css">
        <!-- Flex Slider CSS -->
        <link rel="stylesheet" href="css/flex-slider.min.css">
        <!-- Owl Carousel -->
        <link rel="stylesheet" href="css/owl-carousel.css">
        <!-- Slicknav -->
        <link rel="stylesheet" href="css/slicknav.min.css">

        <!-- Eshop StyleSheet -->
        <link rel="stylesheet" href="css/reset.css">
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="css/responsive.css">


    </head>
    <body class="js">

    <!-- Preloader -->
    <div class="preloader">
        <div class="preloader-inner">
            <div class="preloader-icon">
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <!-- End Preloader -->


    <!-- Header -->
    <header class="header shop">
        <div class="middle-inner">
            <div class="container">
                <?php if (isset($_SESSION["email_message"])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION["email_message"] ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <? endif; ?>
                <div class="row">
                    <div class="col-lg-2 col-md-2 col-12">
                        <!-- Logo -->
                        <div class="logo">
                            <a href="index.php"><img src="images/logo.png" alt="logo"></a>
                        </div>
                        <!--/ End Logo -->
                        <!-- Search Form -->
                        <div class="search-top">
                            <div class="top-search"><a href="#0"><i class="ti-search"></i></a></div>
                            <!-- Search Form -->
                            <div class="search-top">
                                <form class="search-form">
                                    <input type="text" placeholder="Search here..." name="search">
                                    <button value="search" type="submit"><i class="ti-search"></i></button>
                                </form>
                            </div>
                            <!--/ End Search Form -->
                        </div>
                        <!--/ End Search Form -->
                        <div class="mobile-nav"></div>
                    </div>
                    <div class="col-lg-7 col-md-5 col-12">
                        <div class="search-bar-top">
                            <div class="search-bar">
                                <select onchange="changeValuesSearchBar()" id="category_id_search">
                                    <option selected="selected" value="*">Tots</option>
                                    <?foreach ($categories as $cat):?>
                                    <option value="<?echo $cat['id']?>"><?echo $cat["name"]?></option>
                                    <?endforeach;?>
                                </select>
                                    <input name="search" placeholder="Cerca els teus productes....." type="search" id="name_search" oninput="changeValuesSearchBar()">
                                    <a class="btnn" href="./shop-grid.php" id="search_button"><i class="ti-search"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-5 col-12">
                        <div class="right-bar">
                            <!-- Search Form -->
                            <?php if (isset($_SESSION["token_login"]) && isset($_SESSION["user_id"]) && isset($_SESSION["user_info"])): ?>
                                <div class="sinlge-bar ">
                                    <div class="dropdown">
                                        <a class="single-icon dropdown-toggle" id="dropdownMenuLink"
                                           data-toggle="dropdown" aria-expanded="false"
                                           style="font-size: 18px;background-color: transparent;cursor: pointer">
                                            <img class="" src='<?php echo $_SESSION["user_info"][0]["image"] ?>'
                                                 style="vertical-align: middle;width: 2vw;height: 2vw;min-width: 30px;min-height: 30px;border-radius: 50%;margin-top: -5px"/>
                                            <?php echo $_SESSION["user_info"][0]["name"] ?>
                                        </a>

                                        <div class="dropdown-menu mr-5" aria-labelledby="dropdownMenuLink">
                                            <a class="dropdown-item" href="./profile.php"><i
                                                        class="fas fa-user mr-3"></i>El meu perfil</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="./user_commands.php"><i class="fas fa-archive mr-3"></i>Les
                                                meves comandes</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="../admin_view/login.php"><i
                                                        class="fas fa-sign-out-alt mr-3 text-danger"></i>Logout</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="sinlge-bar shopping">
                                    <a href="#" class="single-icon"><i class="ti-bag"></i> <span
                                                class="total-count"><? echo $items_number ?></span></a>
                                    <!-- Shopping Item -->
                                    <div class="shopping-item">
                                        <div class="dropdown-cart-header">
                                    <span><? echo $items_number ?> Producte<?php if ($items_number > 1 || $items_number == 0) {
                                            echo "s";
                                        } ?></span>
                                            <a href="./cart.php?cart_id=<?echo $cart_user[0]['id']?>">Veure cistella</a>
                                        </div>
                                        <ul class="shopping-list">
                                            <? foreach ($cartItems as $item): ?>
                                                <li>
                                                    <a href="../../controllers/CartItemController.php?product_id_deleteCart=<?echo $item['product_id']?>" class="remove" title="Remove this item"><i
                                                                class="fa fa-remove"></i></a>
                                                    <a class="cart-img" href="./product.php?product_id=<?echo $item['id']?>">
                                                        <?if(isset($item['url']) && $item['url']!=null):?>
                                                            <img src="<?echo $item['url']?>"
                                                                 alt="#">
                                                        <?else:?>
                                                            <img src="https://via.placeholder.com/70x70"
                                                                 alt="#">
                                                        <?endif;?>

                                                    </a>
                                                    <h4>
                                                        <a href="./product.php?product_id=<? echo $item['product_id'] ?>"><? echo $item["product_name"] ?></a>
                                                    </h4>
                                                    <p class="quantity"><? echo $item["units"] ?>x
                                                        - <? if (isset($item["discount"]) && $item["discount"] != null): ?>
                                                            <span class="amount"><? echo formatPrice((calculateNewPrice($item["product_price"], $item["discount"]) * $item["units"])) ?> €</span></td>
                                                        <? else: ?>
                                                            <span class="amount"><? echo formatPrice(($item["product_price"] * $item["units"])) ?> €</span></td>
                                                        <? endif; ?></p>
                                                </li>
                                            <? endforeach; ?>
                                        </ul>
                                        <div class="bottom">
                                            <div class="total">
                                                <span>Total</span>
                                                <? if (isset($item["discount"]) && $item["discount"] != null): ?>
                                                    <span class="total-amount"><? echo formatPrice((calculateNewPrice($item["product_price"], $item["discount"]) * $item["units"])) ?> €</span>
                                                    </td>
                                                <? else: ?>
                                                    <span class="total-amount"><? echo formatPrice(($item["product_price"] * $item["units"])) ?> €</span></td>
                                                <? endif; ?>
                                            </div>
                                            <a href="checkout.php?cart_id=<?php echo $cart_user[0]['id']?>" class="btn animate">Anar a pagar</a>
                                        </div>
                                    </div>
                                    <!--/ End Shopping Item -->
                                </div>
                            <? else: ?>
                                <div class="sinlge-bar ">
                                    <a href="../admin_view/login.php" class="single-icon"><i class="fa fa-user-circle-o"
                                                                                             aria-hidden="true"></i></a>
                                </div>
                            <? endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header Inner -->
        <div class="header-inner">
            <div class="container">
                <div class="cat-nav-head">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="all-category">
                                <h3 class="cat-heading"><i class="fa fa-bars" aria-hidden="true"></i>CATEGORIES</h3>
                                <ul class="main-category">
                                    <?php foreach ($categories as $category): ?>
                                        <li>
                                            <a href="./shop-grid.php?category_id=<?php echo $category['id'] ?>"><?php echo $category["name"] ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-9 col-12">
                            <div class="menu-area">
                                <!-- Main Menu -->
                                <nav class="navbar navbar-expand-lg">
                                    <div class="navbar-collapse">
                                        <div class="nav-inner">
                                            <ul class="nav main-menu menu navbar-nav">
                                                <li class="active"><a href="#">Home</a></li>
                                                <li><a href="shop-grid.php">Productes</a></li>
                                                <li><a href="contact.php">Contacte</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </nav>
                                <!--/ End Main Menu -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ End Header Inner -->
    </header>
    <!--/ End Header -->
    <? $principal = searchValueHighlightType(1,$highlights);?>
    <?if(isset($principal) && count($principal)>0):?>
    <!-- Slider Area -->
    <section class="hero-slider">
        <!-- Single Slider -->
        <div class="single-slider" style="background-image: url(<?echo $principal[0]['url']?>) !important;" >
            <div class="container">
                <div class="row no-gutters">
                    <div class="col-lg-9 offset-lg-3 col-12">
                        <div class="text-inner">
                            <div class="row">
                                <div class="col-lg-7 col-12">
                                    <div class="hero-text">
                                        <h1><span><?echo $principal[0]["title"]?> </span><?echo $principal[0]["product_name"]?></h1>
                                        <p><?echo $principal[0]["product_desc"]?></p>
                                        <div class="button">
                                            <a href="./product.php?product_id=<?echo $principal[0]["product_id"]?>" class="btn">Detalls</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ End Single Slider -->
    </section>
    <?endif;?>
    <? $secondaries = searchValueHighlightType(2,$highlights);?>

    <?if(isset($secondaries) && count($secondaries)>0):?>
    <!--/ End Slider Area -->
    <!-- Start Small Banner  -->
    <section class="small-banner section">
        <div class="container-fluid">
            <div class="row">
        <?foreach ($secondaries as $secondary):?>
            <!-- Single Banner  -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="single-banner">
                        <img src="<?echo $secondary['url']?>" alt="#">
                        <div class="content">
                            <p><?echo $secondary["category_name"]?></p>
                            <h3><?echo $secondary["title"]?></h3>
                            <a href="./shop-grid.php?category_id=<?echo $secondary['category_id']?>">DESCOBRIR</a>
                        </div>
                    </div>
                </div>
                <!-- /End Single Banner  -->
        <?endforeach;?>
            </div>
        </div>
    </section>
    <!-- End Small Banner -->
    <?endif;?>
    <!-- Start Product Area -->
    <div class="product-area section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <h2>Els nostres productes</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="product-info">
                        <div class="nav-main">
                            <!-- Tab Nav -->
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <?php $inside = false;?>
                                <?php foreach ($categories as $category):?>
                                    <li class="nav-item"><a class="nav-link <?php if($inside==false)echo 'active';?> "data-toggle="tab" href="<?php echo '#'.$category['id']?>" role="tab"><?php echo $category['name']?></a></li>
                                    <?php $inside = true;?>
                                <?php endforeach;?>
                            </ul>
                            <!--/ End Tab Nav -->
                        </div>
                        <div class="tab-content" id="myTabContent">
                            <?php $inside = false;?>
                            <?php foreach ($categories as $category):?>
                            <!-- Start Single Tab -->
                            <div class="tab-pane fade <?php if($inside==false)echo 'show';?> <?php if($inside==false)echo 'active';?>" id="<?echo $category['id']?>" role="tabpanel">
                                <?php $inside = true;?>
                                <div class="tab-single">
                                    <div class="row">
                                        <?foreach ($products as $product):?>
                                            <?if($product["category_id"]==$category['id']):?>
                                        <div class="col-xl-3 col-lg-4 col-md-4 col-12">
                                                    <div class="single-product " style="box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);">
                                                        <div class="product-img">
                                                            <a href="./product.php?product_id=<?echo $product['id']?>">
                                                                <?if(isset($product['url']) && $product['url']!=null):?>
                                                                <img class="default-img"
                                                                     src="<?echo $product['url']?>" alt="#" style="height: 365px;">
                                                                <img class="hover-img" src="<?echo $product['url']?>"
                                                                     alt="#" style="height: 365px;">
                                                                <?else:?>
                                                                    <img class="default-img"
                                                                         src="https://via.placeholder.com/550x750" alt="#">
                                                                    <img class="hover-img" src="https://via.placeholder.com/550x750"
                                                                         alt="#">
                                                                <?endif;?>
                                                                <?if(isset($product["tag_name"]) && $product["tag_name"]!=null):?>
                                                                <span class="new" style="background-color: <?echo $product['tag_color']?>"><?echo $product["tag_name"]?></span>
                                                                <?endif;?>
                                                            </a>
                                                            <div class="button-head">
                                                                <div class="product-action" style="padding-right: 3%">
                                                                    <a title="Detall del producte" href="./product.php?product_id=<?echo $product['id']?>"><i class=" ti-eye"></i><span>Més detalls</span></a>
                                                                    <!--<a title="Desitjats" href="#"><i class=" ti-heart "></i><span>Afegir a desitjats</span></a>
                                                                    <a title="Compare" href="#"><i class="ti-bar-chart-alt"></i><span>Add to Compare</span></a>-->
                                                                    <a title="Cistella" href="./product.php?product_id=<?echo $product['id']?>"><i class="fas fa-shopping-bag" aria-hidden="true"></i><span>Afegir a la cistella</span></a>
                                                                </div>
                                                                <div class="product-action-2" style="padding-left: 3%">
                                                                    <a title="Afegir" href="#">Cistella</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="product-content" style="padding: 4%;">
                                                            <h3><a href="./product.php?product_id=<?echo $product['id']?>"><?echo $product['name']?></a></h3>
                                                            <div class="product-price">
                                                                <?if(isset($product['discount']) && $product['discount']!=null):?>
                                                                    <span class="old"><?php echo number_format($product["price_iva"], 2, ",", ".") . " €" ?></span>
                                                                    <span><?php echo number_format(calculateNewPrice($product["price_iva"],$product['discount']), 2, ",", ".") . " €" ?></span>
                                                                <?else:?>
                                                                    <span><?php echo number_format($product["price_iva"], 2, ",", ".") . " €" ?></span>
                                                                <?endif;?>
                                                            </div>
                                                        </div>
                                                    </div>

                                        </div>
                                        <?endif;?>
                                        <?endforeach;?>
                                    </div>
                                </div>
                            </div>
                            <!-- End Single Tab -->
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- End Product Area -->
    
    <!-- Start Cowndown Area -->
    <section class="cown-down mb-5" style="height: auto!important;">
        <div class="section-inner">
            <div class="container-fluid px-4">
                <div class="quickview-slider-active">
                    <?php foreach ($discounts as $discount):?>
                        <div class="single-slider" >
                            <div class="row" >
                                <div class="col-lg-6 col-12 padding-right">
                                    <div class="image">
                                        <?php if(isset($discount["url"]) && $discount["url"]!=null):?>
                                            <img src="<?php echo $discount["url"]?>" alt="#" style="height: 500px;">
                                        <?php else:?>
                                            <img src="https://via.placeholder.com/750x590" alt="#" style="height: 500px;">
                                        <?endif;?>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12 padding-left">
                                    <div class="content">
                                        <div class="heading-block">
                                            <p class="small-title">Descompte del dia</p>
                                            <h3 class="title"><?php echo $discount["product_name"]?></h3>
                                            <p class="text"><?php echo $discount["product_desc"]?></p>
                                            <h1 class="price"><?php echo number_format(calculateNewPrice($discount["price_iva"],$discount["discount"]), 2, ",", ".") . " €" ?> <s><?php echo number_format($discount["price_iva"], 2, ",", ".") . " €" ?></s></h1>
                                            <div class="coming-time">
                                                <div class="clearfix" data-countdown="<?php echo $discount["end_date"]?>"></div>
                                            </div>
                                            <a class="btn btn-dark btn-lg mt-5" href="./product.php?product_id=<?echo $discount['product_id']?>" style="color: white">Veure Detalls</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?endforeach;?>
                </div>
            </div>
        </div>
    </section>
    <!-- /End Cowndown Area -->

    <!-- Start Shop Services Area -->
    <section class="shop-services section home mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-rocket"></i>
                        <h4>Enviament gratuït</h4>
                        <p>EN Comandes superiors a 100€</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-reload"></i>
                        <h4>Devolució gratuïta</h4>
                        <p>30 dies de devolució</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-lock"></i>
                        <h4>Pagament segur</h4>
                        <p>Pagament 100% segur</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-tag"></i>
                        <h4>Els millors preus</h4>
                        <p>Garantim els millors preus</p>
                    </div>
                    <!-- End Single Service -->
                </div>
            </div>
        </div>
    </section>
    <!-- End Shop Services Area -->
S
    <!-- Start Footer Area -->
    <footer class="footer">
        <!-- Footer Top -->
        <div class="footer-top section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-6 col-12">
                        <!-- Single Widget -->
                        <div class="single-footer about">
                            <div class="logo">
                                <a href="index.php"><img src="images/logo2.png" alt="#"></a>
                            </div>
                            <p class="text">Praesent dapibus, neque id cursus ucibus, tortor neque egestas augue, magna
                                eros eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor,
                                facilisis luctus, metus.</p>
                            <p class="call">Got Question? Call us 24/7<span><a
                                            href="tel:123456789">+0123 456 789</a></span></p>
                        </div>
                        <!-- End Single Widget -->
                    </div>
                    <div class="col-lg-2 col-md-6 col-12">
                        <!-- Single Widget -->
                        <div class="single-footer links">
                            <h4>Information</h4>
                            <ul>
                                <li><a href="#">About Us</a></li>
                                <li><a href="#">Faq</a></li>
                                <li><a href="#">Terms & Conditions</a></li>
                                <li><a href="#">Contact Us</a></li>
                                <li><a href="#">Help</a></li>
                            </ul>
                        </div>
                        <!-- End Single Widget -->
                    </div>
                    <div class="col-lg-2 col-md-6 col-12">
                        <!-- Single Widget -->
                        <div class="single-footer links">
                            <h4>Customer Service</h4>
                            <ul>
                                <li><a href="#">Payment Methods</a></li>
                                <li><a href="#">Money-back</a></li>
                                <li><a href="#">Returns</a></li>
                                <li><a href="#">Shipping</a></li>
                                <li><a href="#">Privacy Policy</a></li>
                            </ul>
                        </div>
                        <!-- End Single Widget -->
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <!-- Single Widget -->
                        <div class="single-footer social">
                            <h4>Get In Tuch</h4>
                            <!-- Single Widget -->
                            <div class="contact">
                                <ul>
                                    <li>NO. 342 - London Oxford Street.</li>
                                    <li>012 United Kingdom.</li>
                                    <li>info@eshop.com</li>
                                    <li>+032 3456 7890</li>
                                </ul>
                            </div>
                            <!-- End Single Widget -->
                            <ul>
                                <li><a href="#"><i class="ti-facebook"></i></a></li>
                                <li><a href="#"><i class="ti-twitter"></i></a></li>
                                <li><a href="#"><i class="ti-flickr"></i></a></li>
                                <li><a href="#"><i class="ti-instagram"></i></a></li>
                            </ul>
                        </div>
                        <!-- End Single Widget -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Footer Top -->
        <div class="copyright">
            <div class="container">
                <div class="inner">
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <div class="left">
                                <p>Copyright © 2020 <a href="http://www.wpthemesgrid.com"
                                                       target="_blank">Wpthemesgrid</a> - All Rights Reserved.</p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="right">
                                <img src="images/payments.png" alt="#">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- /End Footer Area -->
    <!-- Jquery -->
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-migrate-3.0.0.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <!-- Popper JS -->
    <script src="js/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Color JS -->
    <script src="js/colors.js"></script>
    <!-- Slicknav JS -->
    <script src="js/slicknav.min.js"></script>
    <!-- Owl Carousel JS -->
    <script src="js/owl-carousel.js"></script>
    <!-- Magnific Popup JS -->
    <script src="js/magnific-popup.js"></script>
    <!-- Waypoints JS -->
    <script src="js/waypoints.min.js"></script>
    <!-- Countdown JS -->
    <script src="js/finalcountdown.min.js"></script>
    <!-- Nice Select JS -->
    <script src="js/nicesellect.js"></script>
    <!-- Flex Slider JS -->
    <script src="js/flex-slider.js"></script>
    <!-- ScrollUp JS -->
    <script src="js/scrollup.js"></script>
    <!-- Onepage Nav JS -->
    <script src="js/onepage-nav.min.js"></script>
    <!-- Easing JS -->
    <script src="js/easing.js"></script>
    <!-- Active JS -->
    <script src="js/active.js"></script>


    <script>
        function changeValuesSearchBar(){
            console.log("on change",document.getElementById("category_id_search").value)
            document.getElementById("search_button").href = './shop-grid.php?category_id='+document.getElementById("category_id_search").value+'&product_name='+document.getElementById("name_search").value;
        }
    </script>
    </body>
    </html>
<?php


function searchValueHighlightType($value,$array) {
    $high = [];
    foreach ($array as $highlight){
        if($highlight["highlight_type"]==$value){
            array_push($high,$highlight);
        }
    }
    return $high;
}
unset($_SESSION["email_message"]);
?>