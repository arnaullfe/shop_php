<?php
include_once("../../modals/Database.php");
include_once('../../controllers/UserTokenController.php');
include_once ('../../controllers/MainController.php');
session_start();
if(!isset($_GET["product_id"])){
    header("location: ./index.php");
}
$database = new Database();
$product = $database->executeQuery('SELECT shop.products.*,shop.tags.name as "tag_name",shop.tags.color as "tag_color" FROM shop.products LEFT JOIN shop.tags ON shop.products.tag_id=shop.tags.id WHERE shop.products.id=?', array($_GET["product_id"]));
if(count($product)==0){
    header("location: ./index.php");
}
$product = $product[0];
$images_product = $database->executeQuery("SELECT * FROM images_product WHERE id_product=?",array($_GET["product_id"]));
$discount = $database->executeQuery("SELECT * FROM shop.discounts WHERE id_product=? ORDER BY discount DESC,end_date DESC LIMIT 1",array($_GET["product_id"]));

//cart
$cart_user = $database->executeQuery("SELECT id FROM carts WHERE user_id = ?", array($_SESSION["user_id"]));
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
$categories = $database->executeQuery("SELECT * FROM shop.productCategory WHERE id IN(SELECT shop.products.category_id FROM shop.products WHERE activated=1) AND activated=1", array(1));
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
                                            <a class="dropdown-item" href="../admin_view/login.php"><i
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
                            <li class="active"><a href="">Detall producte</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->
    <section class="product-area shop-sidebar shop section" style="margin-top: 2% !important;padding-top: 0!important;">
        <div class="container">
            <?php if (isset($_SESSION["message"])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION["message"] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <? endif; ?>
            <?php if (isset($_SESSION["error_message"])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION["error_message"] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <? endif; ?>
            <!-- Modal -->
            <div class="row no-gutters card-html">
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <!-- Product Slider -->
                    <div class="product-gallery">
                        <div class="quickview-slider-active">
                            <?php if(count($images_product)>0):?>
                                <?php foreach ($images_product as $image):?>
                                    <div class="single-slider">
                                        <img src="<?php echo $image['url']?>" alt="<?php echo $image['name']?>" style="min-height:400px;max-height: 900px;">
                                    </div>
                                <?php endforeach;?>
                            <?php else:?>
                                <div class="single-slider">
                                    <img src="https://via.placeholder.com/569x528" alt="#" style="min-height:400px;max-height: 900px;">
                                </div>
                                <div class="single-slider">
                                    <img src="https://via.placeholder.com/569x528" alt="#" style="min-height:400px;max-height: 900px;">
                                </div>
                                <div class="single-slider">
                                    <img src="https://via.placeholder.com/569x528" alt="#" style="min-height:400px;max-height: 900px;">
                                </div>
                                <div class="single-slider">
                                    <img src="https://via.placeholder.com/569x528" alt="#" style="min-height:400px;max-height: 900px;">
                                </div>
                            <?php endif;?>
                        </div>
                    </div>
                    <!-- End Product slider -->
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="quickview-content">
                        <h2><?php echo $product["name"]?></h2>
                        <div class="quickview-ratting-review">
                            <div class="quickview-stock">
                                <?php if($product["units"]>0):?>
                                    <span><i class="fa fa-check-circle-o"></i> En estoc</span>
                                <?else:?>
                                    <span><i class="fa fa-times-circle-o text-danger"></i> Sense estoc</span>
                                <?endif;?>
                            </div>
                        </div>
                        <?php if(count($discount)>0):?>
                            <div class="">
                                <h3 style="color: gray;text-decoration: line-through;display: inline-block;margin-right: 2%"><?php echo number_format($product["price_iva"], 2, ",", ".") . " €" ?></h3>
                                <h3 style="display: inline-block"><?php echo number_format(calculateNewPrice($product["price_iva"],$discount[0]["discount"]), 2, ",", ".") . " €" ?></h3>
                            </div>

                        <?php else:?>
                            <h3><?php echo number_format($product["price_iva"], 2, ",", ".") . " €" ?></h3>
                        <?php endif;?>
                        <div class="quickview-peragraph">
                            <p><?php echo $product["description"]?></p>
                        </div>
                        <form action="../../controllers/CartItemController.php" method="post">
                        <div class="quantity mt-5">
                            <!-- Input Order -->
                            <div class="input-group">
                                <div class="button minus">
                                    <button type="button" class="btn btn-primary btn-number" disabled="disabled"
                                            data-type="minus" data-field="units_editCart">
                                        <i class="ti-minus"></i>
                                    </button>
                                </div>
                                <input name="product_id_editCart" value="<?echo $_GET['product_id']?>" style="display: none">
                                <input type="text" name="units_editCart" class="input-number" data-min="1"
                                       data-max="1000" value="1">
                                <div class="button plus">
                                    <button type="button" class="btn btn-primary btn-number" data-type="plus"
                                            data-field="units_editCart">
                                        <i class="ti-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <!--/ End Input Order -->
                        </div>
                        <div class="add-to-cart">
                            <button type="submit" href="#" class="btn">Afegir al carro</button>
                        </div>
                        </form>
                        <div class="default-social">
                            <h4 class="share-now">Comparteix</h4>
                            <ul>
                                <li><a class="facebook" target="_blank" href="https://www.facebook.com/"><i class="fa fa-facebook"></i></a></li>
                                <li><a class="twitter" target="_blank" href="https://twitter.com/home?lang=en"><i class="fa fa-twitter"></i></a></li>
                                <li><a class="youtube" target="_blank" href="https://www.youtube.com/"><i class="fa fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        <!-- Modal end -->
    </section>




    <!-- Start Shop Newsletter  -->
    <section class="shop-newsletter section">
        <div class="container">
            <div class="inner-top">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 col-12">
                        <!-- Start Newsletter Inner -->
                        <div class="inner">
                            <h4>Newsletter</h4>
                            <p> Subscribe to our newsletter and get <span>10%</span> off your first purchase</p>
                            <form action="mail/mail.php" method="get" target="_blank" class="newsletter-inner">
                                <input name="EMAIL" placeholder="Your email address" required="" type="email">
                                <button class="btn">Subscribe</button>
                            </form>
                        </div>
                        <!-- End Newsletter Inner -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Shop Newsletter -->
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
<style>
    .card-html {
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
        width: 100%;
        border-radius: 5px 5px;
    }

    .card-html:hover {
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }
</style>

    <script>
        function changeValuesSearchBar(){
            console.log("on change",document.getElementById("category_id_search").value)
            document.getElementById("search_button").href = './shop-grid.php?category_id='+document.getElementById("category_id_search").value+'&product_name='+document.getElementById("name_search").value;
        }
    </script>
    </body>
    </html>
<?php
unset($_SESSION["message"]);
unset($_SESSION["error_message"]);
?>