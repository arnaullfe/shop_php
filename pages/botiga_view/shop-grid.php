<?php
include_once("../../modals/Database.php");
include_once('../../controllers/UserTokenController.php');
include_once ('../../controllers/MainController.php');
session_start();
$database = new Database();
$products = [];
$text = "Tots els productes";

if(isset($_GET["category_id"]) && isset($_GET["product_name"])){
    if($_GET["category_id"]=="*"){
        $products = $database->executeQuery("SELECT shop.products.*,shop.images_product.url,shop.productCategory.name as 'category',shop.tags.name as 'tag_name',shop.tags.color as 'tag_color',shop.discounts.discount FROM shop.products LEFT JOIN shop.productCategory ON shop.products.category_id = shop.productCategory.id LEFT JOIN shop.tags ON shop.products.tag_id = shop.tags.id LEFT JOIN shop.images_product ON shop.images_product.id_product = shop.products.id LEFT JOIN shop.discounts ON shop.products.id = shop.discounts.id_product AND shop.discounts.start_date <= now() and shop.discounts.end_date>= now() WHERE shop.products.name LIKE ? ORDER BY products.id DESC;", array('%'.$_GET["product_name"].'%'));
        $text = "Tots els productes ";
    }else{
        $products = $database->executeQuery("SELECT shop.products.*,shop.images_product.url,shop.productCategory.name as 'category',shop.tags.name as 'tag_name',shop.tags.color as 'tag_color',shop.discounts.discount FROM shop.products LEFT JOIN shop.productCategory ON shop.products.category_id = shop.productCategory.id LEFT JOIN shop.tags ON shop.products.tag_id = shop.tags.id LEFT JOIN shop.images_product ON shop.images_product.id_product = shop.products.id LEFT JOIN shop.discounts ON shop.products.id = shop.discounts.id_product AND shop.discounts.start_date <= now() and shop.discounts.end_date>= now() WHERE shop.products.name LIKE ? AND shop.products.category_id = ? ORDER BY products.id DESC;", array("%".$_GET["product_name"]."%",$_GET["category_id"]));
        $categ = $database->executeQuery("SELECT * FROM productCategory WHERE id=?", array($_GET["category_id"]));
        $text = "Categoria " . $categ[0]["name"];
    }
} else if (isset($_GET["category_id"])) {
    $products = $database->executeQuery("SELECT shop.products.*,shop.images_product.url,shop.productCategory.name as 'category',shop.tags.name as 'tag_name',shop.tags.color as 'tag_color',shop.discounts.discount FROM shop.products LEFT JOIN shop.productCategory ON shop.products.category_id = shop.productCategory.id LEFT JOIN shop.tags ON shop.products.tag_id = shop.tags.id LEFT JOIN shop.images_product ON shop.images_product.id_product = shop.products.id LEFT JOIN shop.discounts ON shop.products.id = shop.discounts.id_product AND shop.discounts.start_date <= now() and shop.discounts.end_date>= now() WHERE shop.products.category_id = ? ORDER BY products.id DESC;", array($_GET["category_id"]));
    $categ = $database->executeQuery("SELECT * FROM productCategory WHERE id=?", array($_GET["category_id"]));
    $text = "Categoria " . $categ[0]["name"];
} else {
    $products = $database->executeQuery("SELECT shop.products.*,shop.images_product.url,shop.productCategory.name as 'category',shop.tags.name as 'tag_name',shop.tags.color as 'tag_color',shop.discounts.discount FROM shop.products LEFT JOIN shop.productCategory ON shop.products.category_id = shop.productCategory.id LEFT JOIN shop.tags ON shop.products.tag_id = shop.tags.id LEFT JOIN shop.images_product ON shop.images_product.id_product = shop.products.id LEFT JOIN shop.discounts ON shop.products.id = shop.discounts.id_product AND shop.discounts.start_date <= now() and shop.discounts.end_date>= now() ORDER BY products.id DESC;", array());
}
$categories = $database->executeQuery("SELECT * FROM productCategory WHERE id IN (SELECT category_id FROM products) GROUP BY id ORDER BY id", array());
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
$num = 0;
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
                    <div class="col-lg-7 col-md-4 col-12">
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
                                            <?php echo $_SESSION["user_info"][0]["name"]; ?>
                                        </a>

                                        <div class="dropdown-menu mr-5" aria-labelledby="dropdownMenuLink">
                                            <a class="dropdown-item" href="./profile.php"><i
                                                        class="fas fa-user mr-3"></i>El meu perfil</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="./user_commands.php"><i class="fas fa-archive mr-3"></i>Les
                                                meves comandes</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#"><i
                                                        class="fas fa-sign-out-alt mr-3 text-danger"></i>Tancar
                                                sessió</a>
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
                                            <a href="./cart.php?cart_id=<? echo $cart_user[0]['id'] ?>">Veure
                                                cistella</a>
                                        </div>
                                        <ul class="shopping-list">
                                            <? foreach ($cartItems as $item): ?>
                                                <li>
                                                    <a href="../../controllers/CartItemController.php?product_id_deleteCart=<?echo $item['product_id']?>" class="remove" title="Remove this item"><i
                                                                class="fa fa-remove"></i></a>
                                                    <a class="cart-img"
                                                       href="./product.php?product_id=<? echo $item['id'] ?>">
                                                        <? if (isset($item['url']) && $item['url'] != null): ?>
                                                            <img src="<? echo $item['url'] ?>"
                                                                 alt="#">
                                                        <? else: ?>
                                                            <img src="https://via.placeholder.com/70x70"
                                                                 alt="#">
                                                        <? endif; ?>

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
                                            <a href="checkout.php?cart_id=<?php echo $cart_user[0]['id'] ?>"
                                               class="btn animate">Anar a pagar</a>
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
        </div>
        <!-- Header Inner -->
        <div class="header-inner">
            <div class="container">
                <div class="cat-nav-head">
                    <div class="row">
                        <div class="col-12">
                            <div class="menu-area">
                                <!-- Main Menu -->
                                <nav class="navbar navbar-expand-lg">
                                    <div class="navbar-collapse">
                                        <div class="nav-inner">
                                            <ul class="nav main-menu menu navbar-nav">
                                                <li><a href="./index.php">Home</a></li>
                                                <li><a href="./shop-grid.php">Productes</a></li>
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

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="./index.php">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="blog-single.html">Shop Grid</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Product Style -->
    <section class="product-area shop-sidebar shop section" style="margin-top: 2% !important;padding-top: 0!important;">
        <div style="display: block;width: 100%;text-align: center;padding: 0;">
            <h1 style="margin: 0;padding: 0;"><?php echo $text ?></h1>
        </div>
        <div class="container" style="margin-top: 2%;">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="shop-sidebar">
                        <!-- Single Widget -->
                        <div class="single-widget category">
                            <h3 class="title">Categories</h3>
                            <ul class="categor-list">
                                <?php if (!isset($_GET["category_id"])): ?>
                                    <li><a href="./shop-grid.php"
                                           style="color: #F7931C">Tots els productes</a></li>
                                <? else: ?>
                                    <li>
                                        <a href="./shop-grid.php">Tots els productes</a>
                                    </li>
                                <? endif; ?>
                                <?php foreach ($categories as $category): ?>
                                    <?php if (isset($_GET["category_id"]) && $_GET["category_id"] == $category["id"]): ?>
                                        <li><a href="?category_id=<?php echo $category['id'] ?>"
                                               style="color: #F7931C"><?php echo $category["name"] ?></a></li>
                                    <? else: ?>
                                        <li>
                                            <a href="?category_id=<?php echo $category['id'] ?>"><?php echo $category["name"] ?></a>
                                        </li>
                                    <? endif; ?>
                                <? endforeach; ?>
                            </ul>
                        </div>
                        <!--/ End Single Widget -->
                        <!-- Shop By Price -->
                        <!--	<div class="single-widget range">
                                <h3 class="title">Filtrar preu</h3>
                                <div class="price-filter">
                                    <div class="price-filter-inner">
                                        <div id="slider-range"></div>
                                            <div class="price_slider_amount">
                                            <div class="label-input">
                                                <span>Rang:</span><input type="text" id="amount" name="price" placeholder="Afageix el preu" readonly/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <ul class="check-box-list">
                                    <li>
                                        <label class="checkbox-inline" for="1"><input name="news" id="1" type="checkbox">20€ - 50€<span class="count">(3)</span></label>
                                    </li>
                                    <li>
                                        <label class="checkbox-inline" for="2"><input name="news" id="2" type="checkbox">50€ - 100€<span class="count">(5)</span></label>
                                    </li>
                                    <li>
                                        <label class="checkbox-inline" for="3"><input name="news" id="3" type="checkbox">100€ - 250€<span class="count">(8)</span></label>
                                    </li>
                                    <li>
                                        <label class="checkbox-inline" for="3"><input name="news" id="3" type="checkbox">>250€<span class="count">(8)</span></label>
                                    </li>
                                </ul>
                            </div>-->
                        <!--/ End Shop By Price -->
                        <!-- Single Widget -->
                        <div class="single-widget recent-post">
                            <h3 class="title">Posts recents</h3>
                            <!-- Single Post -->
                            <?php foreach ($products as $product): ?>
                                <?php if ($num == 3) {
                                    break;
                                }
                                $num++; ?>
                                <div class="single-post first">
                                    <div class="image">
                                        <?php if ($product["url"] != null): ?>
                                            <img src="<?php echo $product['url'] ?>" alt="#">
                                        <? else: ?>
                                            <img class="default-img" src="https://via.placeholder.com/550x750" alt="#">
                                        <? endif; ?>
                                    </div>
                                    <div class="content">
                                        <h5><a href="./product.php?product_id=<?php echo $product['id']?>"><?php echo $product["name"] ?></a></h5>
                                        <?php if ($product["discount"] != null): ?>
                                            <span style="color: gray;text-decoration: line-through"><?php echo number_format($product["price_iva"], 2, ",", ".") . " €" ?></span>
                                            <span><?php echo number_format(calculateNewPrice($product["price_iva"], $product["discount"]), 2, ",", ".") . " €" ?></span>
                                        <? else: ?>
                                            <p class="price"><?php echo number_format($product["price_iva"], 2, ",", ".") . " €" ?></p>
                                        <? endif; ?>
                                    </div>
                                </div>
                            <? endforeach; ?>
                            <!-- End Single Post -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                    <div class="row">
                        <?php foreach ($products as $product): ?>
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="single-product" style="box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);">
                                    <div class="product-img">
                                        <a href="./product.php?product_id=<?php echo $product['id']?>">
                                        <?php if (isset($product["url"]) && $product["url"] != null): ?>
                                            <img class="default-img" src="<?php echo $product["url"] ?>" alt="#"
                                                 style="height: 365px;">
                                            <img class="hover-img" src="<?php echo $product["url"] ?>" alt="#"
                                                 style="height: 365px;">
                                            <?php if (isset($product["tag_name"]) && $product["tag_name"] != null): ?>
                                                <span class="new"
                                                      style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;max-width: 70%;background-color: <?php echo $product['tag_color'] ?>"><?php echo $product["tag_name"] ?></span>
                                            <? endif; ?>
                                        <?php else: ?>
                                            <img class="default-img" src="https://via.placeholder.com/550x750" alt="#"
                                                 style="height: 365px;">
                                            <img class="hover-img" src="https://via.placeholder.com/550x750" alt="#"
                                                 style="height: 365px;">
                                        <?php endif; ?>
                                        </a>
                                        <div class="button-head">
                                            <div class="product-action" style="padding-right: 3%">
                                                <a title="Detall del producte" href="./product.php?product_id=<?php echo $product['id']?>"><i class=" ti-eye"></i><span>Més detalls</span></a>
                                                <!--<a title="Desitjats" href="#"><i class=" ti-heart "></i><span>Afegir a desitjats</span></a>
                                                <a title="Compare" href="#"><i class="ti-bar-chart-alt"></i><span>Add to Compare</span></a>-->
                                                <a title="Cistella" href="#"><i class="fas fa-shopping-bag"></i><span>Afegir a la cistella</span></a>
                                            </div>
                                            <div class="product-action-2" style="padding-left: 3%">
                                                <a title="Afegir" href="#">Cistella</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="product-content" style="padding:4%">
                                        <h3><a href="product-details.html"
                                               style="word-break: break-all"><?php echo $product["name"] ?></a></h3>
                                        <div class="product-price">
                                            <?php if ($product["discount"] != null): ?>
                                                <span class="old"><?php echo number_format($product["price_iva"], 2, ",", ".") . " €" ?></span>
                                                <span><?php echo number_format(calculateNewPrice($product["price_iva"], $product["discount"]), 2, ",", ".") . " €" ?></span>

                                            <? else: ?>
                                                <span><?php echo number_format($product["price_iva"], 2, ",", ".") . " €" ?></span>
                                            <? endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ End Product Style 1  -->


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="ti-close"
                                                                                                      aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row no-gutters">
                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            <!-- Product Slider -->
                            <div class="product-gallery">
                                <div class="quickview-slider-active">
                                    <div class="single-slider">
                                        <img src="https://via.placeholder.com/569x528" alt="#">
                                    </div>
                                    <div class="single-slider">
                                        <img src="https://via.placeholder.com/569x528" alt="#">
                                    </div>
                                    <div class="single-slider">
                                        <img src="https://via.placeholder.com/569x528" alt="#">
                                    </div>
                                    <div class="single-slider">
                                        <img src="https://via.placeholder.com/569x528" alt="#">
                                    </div>
                                </div>
                            </div>
                            <!-- End Product slider -->
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            <div class="quickview-content">
                                <h2>Flared Shift Dress</h2>
                                <div class="quickview-ratting-review">
                                    <div class="quickview-ratting-wrap">
                                        <div class="quickview-ratting">
                                            <i class="yellow fa fa-star"></i>
                                            <i class="yellow fa fa-star"></i>
                                            <i class="yellow fa fa-star"></i>
                                            <i class="yellow fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a href="#"> (1 customer review)</a>
                                    </div>
                                    <div class="quickview-stock">
                                        <span><i class="fa fa-check-circle-o"></i> in stock</span>
                                    </div>
                                </div>
                                <h3>$29.00</h3>
                                <div class="quickview-peragraph">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Mollitia iste laborum
                                        ad impedit pariatur esse optio tempora sint ullam autem deleniti nam in quos qui
                                        nemo ipsum numquam.</p>
                                </div>
                                <div class="size">
                                    <div class="row">
                                        <div class="col-lg-6 col-12">
                                            <h5 class="title">Size</h5>
                                            <select>
                                                <option selected="selected">s</option>
                                                <option>m</option>
                                                <option>l</option>
                                                <option>xl</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-12">
                                            <h5 class="title">Color</h5>
                                            <select>
                                                <option selected="selected">orange</option>
                                                <option>purple</option>
                                                <option>black</option>
                                                <option>pink</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="quantity">
                                    <!-- Input Order -->
                                    <div class="input-group">
                                        <div class="button minus">
                                            <button type="button" class="btn btn-primary btn-number" disabled="disabled"
                                                    data-type="minus" data-field="quant[1]">
                                                <i class="ti-minus"></i>
                                            </button>
                                        </div>
                                        <input type="text" name="quant[1]" class="input-number" data-min="1"
                                               data-max="1000" value="1">
                                        <div class="button plus">
                                            <button type="button" class="btn btn-primary btn-number" data-type="plus"
                                                    data-field="quant[1]">
                                                <i class="ti-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!--/ End Input Order -->
                                </div>
                                <div class="add-to-cart">
                                    <a href="#" class="btn">Add to cart</a>
                                    <a href="#" class="btn min"><i class="ti-heart"></i></a>
                                    <a href="#" class="btn min"><i class="fa fa-compress"></i></a>
                                </div>
                                <div class="default-social">
                                    <h4 class="share-now">Share:</h4>
                                    <ul>
                                        <li><a class="facebook" href="#"><i class="fa fa-facebook"></i></a></li>
                                        <li><a class="twitter" href="#"><i class="fa fa-twitter"></i></a></li>
                                        <li><a class="youtube" href="#"><i class="fa fa-pinterest-p"></i></a></li>
                                        <li><a class="dribbble" href="#"><i class="fa fa-google-plus"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal end -->

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
    <!-- Fancybox JS -->
    <script src="js/facnybox.min.js"></script>
    <!-- Waypoints JS -->
    <script src="js/waypoints.min.js"></script>
    <!-- Countdown JS -->
    <script src="js/finalcountdown.min.js"></script>
    <!-- Nice Select JS -->
    <script src="js/nicesellect.js"></script>
    <!-- Ytplayer JS -->
    <script src="js/ytplayer.min.js"></script>
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


?>