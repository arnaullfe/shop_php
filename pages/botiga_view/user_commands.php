<?php
include_once("../../modals/Database.php");
include_once('../../modals/Address.php');
include_once('../../controllers/MainController.php');
include_once('../../controllers/UserTokenController.php');
session_start();
if (!isset($_SESSION["user_info"])) {
    header("location: ./index.php");
}
$database = new Database();
$commands = $database->executeQuery('SELECT shop.commands.*,SUM(shop.commandItems.total_iva_price)+shop.commands.sending_price as "total",
shop.addressesCommands.name as "address_name",shop.addressesCommands.lastnames as "address_lastnames"
FROM shop.commands,shop.commandItems,shop.addressesCommands WHERE shop.commands.id = shop.commandItems.command_id 
AND shop.commands.address_command_id= shop.addressesCommands.id AND shop.commands.user_id=? GROUP BY shop.commands.id ORDER BY shop.commands.created_at DESC;',array($_SESSION["user_id"]));
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

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
                                        <a class="dropdown-item" href="./user_commands.php"><i
                                                    class="fas fa-archive mr-3"></i>Les
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
                        <li><a href="index.php">Home<i class="ti-arrow-right"></i></a></li>
                        <li class="active"><a href="./user_commands.php">Les meves comandes</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumbs -->

<!-- Start Checkout -->
<section class="shop checkout section">
    <div class="container">
        <?php if ($_SESSION["changes_email_message"]): ?>
            <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                <?php echo $_SESSION["changes_email_message"] ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <? endif; ?>

        <?php if ($_SESSION["changes_message"]): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <?php echo $_SESSION["changes_message"] ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <? endif; ?>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead style="background-color: #F7921C">
                        <tr>
                            <th>Número de comanda</th>
                            <th>Estat</th>
                            <th>Hora i data</th>
                            <th>Nom destinatari</th>
                            <th>Enviament</th>
                            <th>Total</th>
                            <th>Accions</th>
                        </tr>
                        </thead>
                        <tfoot style="background-color: #F7921C">
                        <tr>
                            <th>Número de comanda</th>
                            <th>Estat</th>
                            <th>Hora i data</th>
                            <th>Nom destinatari</th>
                            <th>Enviament</th>
                            <th>Total</th>
                            <th>Accions</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach ($commands as $command): ?>
                            <tr>
                                <td><?php echo str_pad($command['id'],6,'0',STR_PAD_LEFT)?></td>
                                <td><?php echo $command['status']?></td>
                                <td><?php echo formatDate($command['created_at'])?></td>
                                <td>
                                    <?php echo $command["address_name"]." ".$command["address_lastnames"];?>
                                </td>
                                <td>
                                    <?php echo formatPrice($command["sending_price"]);?> €
                                </td>
                                <td>
                                    <?php echo formatPrice($command["total"]) ;?> €
                                </td>
                                <td>
                                    <a href="../../controllers/PdfController.php?command_id_bill=<?echo $command['id']?>" target="_blank" class="btn" title="Factura" style="font-size: 30px;color: white;text-decoration: none">
                                        <i class="fas fa-file-pdf"></i></a>
                                </td>
                            </tr>
                        <? endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</section>
<!--/ End Checkout -->

<!-- Start Shop Services Area -->
<section class="shop-services section home mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Service -->
                <div class="single-service">
                    <i class="ti-rocket"></i>
                    <h4>Enviament gratuït</h4>
                    <p>EN Comandes superiors a 50€</p>
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
                            eros
                            eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor,
                            facilisis
                            luctus, metus.</p>
                        <p class="call">Got Question? Call us 24/7<span><a
                                        href="tel:123456789">+0123 456 789</a></span>
                        </p>
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
                                                   target="_blank">Wpthemesgrid</a> -
                                All Rights Reserved.</p>
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

<script src="../botiga_view/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../botiga_view/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page level custom scripts -->
<script src="../botiga_view/js/demo/datatables-demo.js"></script>
<script>
    function changeValuesSearchBar(){
        console.log("on change",document.getElementById("category_id_search").value)
        document.getElementById("search_button").href = './shop-grid.php?category_id='+document.getElementById("category_id_search").value+'&product_name='+document.getElementById("name_search").value;
    }
</script>

</body>
</html>
<?php
unset($_SESSION["lastnames_changes"]);
unset($_SESSION["name_changes"]);
unset($_SESSION["email_changes"]);
unset($_SESSION["changes_errors"]);
unset($_SESSION["changes_message"]);
unset($_SESSION["changes_email_message"]);
?>
