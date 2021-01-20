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
$addresses = $database->executeQuery("SELECT * FROM shop.addresses WHERE user_id =? ", array($_SESSION["user_id"]));
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
                                <select>
                                    <option selected="selected">All Category</option>
                                    <option>watch</option>
                                    <option>mobile</option>
                                    <option>kid’s item</option>
                                </select>
                                <form>
                                    <input name="search" placeholder="Search Products Here....." type="search">
                                    <button class="btnn"><i class="ti-search"></i></button>
                                </form>
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
                                            <a class="dropdown-item" href="#"><i class="fas fa-archive mr-3"></i>Les
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
                                                    <a href="#" class="remove" title="Remove this item"><i
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
                                                <li class="active"><a href="./shop-grid.php">Productes</a></li>
                                                <li><a href="#">Informació<i class="ti-angle-down"></i></a>
                                                    <ul class="dropdown">
                                                        <li><a href="blog-single-sidebar.php">Blog</a></li>
                                                        <li><a href="blog-single-sidebar.php">Reviews</a></li>
                                                    </ul>
                                                </li>
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
                            <li class="active"><a href="blog-single.html">El teu perfil</a></li>
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
                    <div class="order-details ">
                        <div class="single-widget">
                            <h2>El teu perfil</h2>
                            <!-- Form -->
                            <form class="form mt-3 pr-4 pl-4" method="post"
                                  action="../../controllers/UserController.php">
                                <div class="row">
                                    <div class="col-12" style="text-align: center">
                                        <img class="" src='<?php echo $_SESSION["user_info"][0]["image"] ?>'
                                             style="vertical-align: middle;width: 7vw;height: 7vw;min-width: 90px;min-height: 90px;border-radius: 50%;"/>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Nom<span>*</span></label>
                                            <?php if (isset($_SESSION["name_changes"])): ?>
                                                <input type="text" name="name_changes" placeholder=""
                                                       required="required"
                                                       value='<?php echo $_SESSION["name_changes"] ?>'>
                                            <? else: ?>
                                                <input type="text" name="name_changes" placeholder=""
                                                       required="required"
                                                       value='<?php echo $_SESSION["user_info"][0]["name"] ?>'>
                                            <?php endif; ?>
                                            <?php if (isset($_SESSION["changes_errors"]) && in_array("error_name_changes", $_SESSION["changes_errors"])): ?>
                                                <label class="ml-3 text-danger" style="font-size: 14px;"><i
                                                            class="fas fa-exclamation-circle mr-1"></i>Nom introduït
                                                    erroni</label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Cognoms<span>*</span></label>
                                            <?php if (isset($_SESSION["lastnames_changes"])): ?>
                                                <input type="text" name="lastnames_changes" placeholder=""
                                                       required="required"
                                                       value='<?php echo $_SESSION["lastnames_changes"] ?>'>
                                            <? else: ?>
                                                <input type="text" name="lastnames_changes" placeholder=""
                                                       required="required"
                                                       value='<?php echo $_SESSION["user_info"][0]["lastnames"] ?>'>
                                            <?php endif; ?>
                                            <?php if (isset($_SESSION["changes_errors"]) && in_array("error_lastnames_changes", $_SESSION["changes_errors"])): ?>
                                                <label class="ml-3 text-danger" style="font-size: 14px;"><i
                                                            class="fas fa-exclamation-circle mr-1"></i>Cognom introduït
                                                    erroni</label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Email<span>*</span></label>
                                            <?php if (isset($_SESSION["email_changes"])): ?>
                                                <input type="text" name="email_changes" placeholder=""
                                                       required="required"
                                                       value='<?php echo $_SESSION["email_changes"] ?>'>
                                            <? else: ?>
                                                <input type="text" name="email_changes" placeholder=""
                                                       required="required"
                                                       value='<?php echo $_SESSION["user_info"][0]["email"] ?>'>
                                            <?php endif; ?>
                                            <?php if (isset($_SESSION["changes_errors"]) && in_array("error_email_changes", $_SESSION["changes_errors"])): ?>
                                                <label class="ml-3 text-danger" style="font-size: 14px;"><i
                                                            class="fas fa-exclamation-circle mr-1"></i>Email no vàlid o
                                                    ja existent</label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Estat del compte<span></span></label>
                                            <?php if ($_SESSION["user_info"][0]["activated"] == true): ?>
                                                <p class="bg-success pl-5 pr-5" readonly
                                                   style="color: white;height: 45px;display: table-cell;vertical-align: middle;font-size: 17px ">
                                                    <i class="fas fa-check-double mr-3"></i>Activat</p>
                                            <?php else: ?>
                                                <p class="bg-danger pl-5 pr-5" readonly
                                                   style="color: white;height: 45px;display: table-cell;vertical-align: middle;font-size: 17px ">
                                                    <i class="fas fa-times mr-3"></i>No Activat</p>
                                            <? endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-warning btn-block" style="background-color: #F7941D">Guardar
                                </button>
                            </form>
                        </div><!--/ End Form -->
                    </div>
                </div>
                <div class=" col-12 mt-4">
                    <div class="order-details">
                        <!-- Order Widget -->
                        <div class="single-widget">
                            <h2>Les teves adreçes</h2>
                            <div class="content pr-4 pl-4 mt-4">
                                <select id="select_address" onchange="selectChange()">
                                    <?php foreach ($addresses as $address): ?>
                                        <option value="<?php echo $address['id'] ?>"><?php echo $address['alias'] ?></option>
                                    <? endforeach; ?>
                                </select>
                            </div>
                            <div class="row pr-4 pl-4 mt-5">
                                <div class="col-md-6 col-12 mt-2">
                                    <button class="btn btn-success bg-success btn-block"
                                            onclick="newAddress(<?php echo $_SESSION['user_id'] ?>)">Nova Adreça
                                    </button>
                                </div>
                                <div class="col-md-6 col-12 mt-2">
                                    <form action="../../controllers/AddressController.php" method="post">
                                        <button class="btn btn-danger bg-danger btn-block" id="delete_address" <?if (count($addresses) == 0):?>disabled<?endif;?>>Eliminar adreça
                                        </button>
                                        <input type="text" id="id_deleteAddress" name="id_deleteAddress" value=" <? if (count($addresses)> 0) {
                                            echo $addresses[0]['id'];
                                        } ?>" style="display: none">
                                    </form>

                                </div>
                            </div>
                        </div>

                        <!--/ End Order Widget -->
                        <!-- Order Widget -->
                        <div class="single-widget mt-4">
                            <h2>Dades Adreça Seleccionada</h2>
                            <form class="form" method="post" action="../../controllers/AddressController.php">
                                <div class="row p-4 pr-4">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Àlies<span>*</span></label>
                                            <input type="text" name="alias_editAddress" id="alias_editAddress"
                                                   required="required"
                                                <? if (count($addresses) == 0): ?>
                                                    <? echo "readonly"; ?>
                                                <? else: ?>
                                                    value="<? echo $addresses[0]['alias'] ?>"
                                                <? endif; ?>>

                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Nom<span>*</span></label>
                                            <input type="text" name="name_editAddress" id="name_editAddress"
                                                   placeholder="" required="required"
                                                <? if (count($addresses) == 0): ?>
                                                    <? echo "readonly"; ?>
                                                <? else: ?>
                                                    value="<? echo $addresses[0]['name'] ?>"
                                                <? endif; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Cognoms<span>*</span></label>
                                            <input type="text" name="lastnames_editAddress" id="lastnames_editAddress"
                                                   required="required"
                                                <? if (count($addresses) == 0): ?>
                                                    <? echo "readonly"; ?>
                                                <? else: ?>
                                                    value="<? echo $addresses[0]['lastnames'] ?>"
                                                <? endif; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Número de Telèfon<span>*</span></label>
                                            <input type="name" name="phone_editAddress" id="phone_editAddress"
                                                   required="required"
                                                <? if (count($addresses) == 0): ?>
                                                    <? echo "readonly"; ?>
                                                <? else: ?>
                                                    value="<? echo $addresses[0]['phone'] ?>"
                                                <? endif; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Email<span>*</span></label>
                                            <input type="email" name="email_editAddress" id="email_editAddress"
                                                   required="required"
                                                <? if (count($addresses) == 0): ?>
                                                    <? echo "readonly"; ?>
                                                <? else: ?>
                                                    value="<? echo $addresses[0]['email'] ?>"
                                                <? endif; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>CIF/NIF<span>*</span></label>
                                            <input type="text" name="nif_editAddress" id="nif_editAddress"
                                                   required="required"
                                                <? if (count($addresses) == 0): ?>
                                                    <? echo "readonly"; ?>
                                                <? else: ?>
                                                    value="<? echo $addresses[0]['nif'] ?>"
                                                <? endif; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Païs<span>*</span></label>
                                            <input type="text" name="country_editAddress" id="country_editAddress"
                                                   required="required"
                                                <? if (count($addresses) == 0): ?>
                                                    <? echo "readonly"; ?>
                                                <? else: ?>
                                                    value="<? echo $addresses[0]['country'] ?>"
                                                <? endif; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Provincia<span>*</span></label>
                                            <input type="text" name="province_editAddress" id="province_editAddress"
                                                   required="required"
                                                <? if (count($addresses) == 0): ?>
                                                    <? echo "readonly"; ?>
                                                <? else: ?>
                                                    value="<? echo $addresses[0]['province'] ?>"
                                                <? endif; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Població<span>*</span></label>
                                            <input type="text" name="city_editAddress" id="city_editAddress"
                                                   required="required"
                                                <? if (count($addresses) == 0): ?>
                                                    <? echo "readonly"; ?>
                                                <? else: ?>
                                                    value="<? echo $addresses[0]['city'] ?>"
                                                <? endif; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Adreça<span>*</span></label>
                                            <input type="text" name="address_editAddress" id="address_editAddress"
                                                   required="required" <? if (count($addresses) == 0): ?>
                                                <? echo "readonly"; ?>
                                            <? else: ?>
                                                value="<? echo $addresses[0]['address'] ?>"
                                            <? endif; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Codi postal<span>*</span></label>
                                            <input type="text" name="postal_code_editAddress"
                                                   id="postal_code_editAddress"
                                                   required="required"
                                                <? if (count($addresses) == 0): ?>
                                                    <? echo "readonly"; ?>
                                                <? else: ?>
                                                    value="<? echo $addresses[0]['postal_code'] ?>"
                                                <? endif; ?>>
                                        </div>
                                    </div>
                                </div>
                                <input type="text" name="id_editAddress" id="id_editAddress" required="required"
                                       style="display: none;"
                                    <? if (count($addresses) > 0): ?>
                                        value="<? echo $addresses[0]['id'] ?>"
                                    <? endif; ?>>
                                <input type="text" name="created_at_editAddress" id="created_at_editAddress" required="required"
                                       style="display: none;"
                                    <? if (count($addresses) > 0): ?>
                                        value="<? echo $addresses[0]['created_at'] ?>"
                                    <? endif; ?>>
                                <div class="col-12 pl-4 pr-4">
                                    <button type="submit" class="btn btn-warning btn-block" id="save_editAddress"
                                            style="background-color: #F7941D" <?if (count($addresses) == 0):?>disabled<?endif;?>>Guardar</button>
                                </div>
                            </form>

                        </div>
                        <!--/ End Order Widget -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ End Checkout -->

    <!-- Start Shop Services Area  -->
    <section class="shop-services section home">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-rocket"></i>
                        <h4>Enviament gratuït</h4>
                        <p>En comandes de més de 50€</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-reload"></i>
                        <h4>Devolucions gratuïtes</h4>
                        <p>Amb compres de menys de 30 dies</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-lock"></i>
                        <h4>Pagament segur</h4>
                        <p>Seguretat 100% a l'hora de pagar</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-tag"></i>
                        <h4>El millor preu</h4>
                        <p>Garantim el millor preu del mercat</p>
                    </div>
                    <!-- End Single Service -->
                </div>
            </div>
        </div>
    </section>
    <!-- End Shop Services -->

    <!-- Start Shop Newsletter  -->
    <section class="shop-newsletter section">
        <div class="container">
            <div class="inner-top">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 col-12">
                        <!-- Start Newsletter Inner -->
                        <div class="inner">
                            <h4>Newsletter</h4>
                            <p> Subscriu-te al nostre butlletí de notícies i obtindràs un <span>10%</span> de descompte
                                en la primera compra</p>
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

    <script type="text/javascript">
        var loading = false;

        function newAddress(user_id) {
           if(loading==false){
               loading = true;
               $.ajax({
                   type: "POST",
                   url: '../../controllers/AddressController.php',
                   data: {"user_id_newAddress": user_id},
                   headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               }).then((data) => {
                   data = JSON.parse(data);
                   console.log(data);
                   var select = document.getElementById("select_address");
                   var option = document.createElement('option');
                   option.value = data.id;
                   option.innerText = data.alias;
                   select.appendChild(option);
                   select.value = option.value;
                   changeInputValues(data);
                   loading = false;
               });
           }
        }

        function selectChange() {
            if(loading==false){
                loading = true;
                var id = document.getElementById("select_address").value;
                $.ajax({
                    type: "GET",
                    url: '../../controllers/AddressController.php',
                    data: {"address_id_getAddress": id},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                }).then((data) => {
                    data = JSON.parse(data)[0];
                    console.log(data);
                    changeInputValues(data);
                    loading = false;
                });
            }
        }
       function changeInputValues(data){

           document.getElementById("id_editAddress").value = data.id;
           changeInput("id_editAddress",data.id);
           changeInput("id_deleteAddress",data.id);
           changeInput("alias_editAddress",data.alias);
           changeInput("name_editAddress",data.name);
           changeInput("lastnames_editAddress",data.lastnames);
           changeInput("email_editAddress",data.email);
           changeInput("phone_editAddress",data.phone);
           changeInput("nif_editAddress",data.nif);
           changeInput("address_editAddress",data.address);
           changeInput("country_editAddress",data.country);
           changeInput("city_editAddress",data.city);
           changeInput("postal_code_editAddress",data.postal_code);
           changeInput("province_editAddress",data.province)
           changeInput("created_at_editAddress",data.created_at)
           document.getElementById("delete_address").removeAttribute("disabled");
           document.getElementById("save_editAddress").removeAttribute("disabled");
       }

       function changeInput(id,value){
           document.getElementById(id).removeAttribute("readonly");
           document.getElementById(id).value = value;
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