<?php
include_once ('../../modals/Database.php');
include_once ('../../controllers/UserTokenController.php');
include_once ('../../controllers/MainController.php');
session_start();
if(!isset($_GET["cart_id"]) || !isset($_SESSION["user_id"])){
    header("location: ../admin_view/login.php");
}
$database = new Database();
$user_id = $database->executeQuery("SELECT user_id FROM carts WHERE id = ?",array($_GET["cart_id"]));
if(count($user_id)==0 || $user_id[0]["user_id"]!=$_SESSION["user_id"]){
    $database->closeConnection();
    header("location: ../admin_view/login.php");
}

$cart_user = $database->executeQuery("SELECT id FROM carts WHERE user_id = ?",array($_SESSION["user_id"]));
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
$sending_price = ($final_price<50)? 5:0;
$money_saved = calculatSave($cartItems);
//adreçes
$addresses = $database->executeQuery("SELECT * FROM addresses WHERE user_id=? ORDER BY id DESC",array($_SESSION["user_id"]));
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
	<link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
	
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
                                            <a href="./cart.php?cart_id=<?echo $cart_user[0]['id']?>">Veure cistella</a>
                                        </div>
                                        <ul class="shopping-list">
                                            <? foreach ($cartItems as $item): ?>
                                                <li>
                                                    <a href="#" class="remove" title="Remove this item"><i
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
								<li><a href="./index.php">Home<i class="ti-arrow-right"></i></a></li>
								<li class="active"><a href="./checkout.php">Pàgina de pagament</a></li>
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
				<div class="row">
					<div class="col-lg-8 col-12">
						<div class="checkout-form">
							<h2>Fer el pagement</h2>
							<p>Selecciona una adreça o crea una de nova</p>
							<!-- Form -->
                                <div class="content mt-4">
                                    <select id="select_address" onchange="selectChange()">
                                        <?php foreach ($addresses as $address): ?>
                                            <option value="<?php echo $address['id'] ?>"><?php echo $address['alias'] ?></option>
                                        <? endforeach; ?>
                                    </select>
                                </div>
                                <div class="content mt-4">
                                    <a class="btn btn-block btn-warning" href="./profile.php" style="color: white;text-align: center">Anar a crear una nova adreça</a>
                                </div>
							<!--/ End Form -->
						</div>
					</div>
					<div class="col-lg-4 col-12">
						<div class="order-details">
							<!-- Order Widget -->
							<div class="single-widget">
								<h2>Total</h2>
								<div class="content">
									<ul>
										<li>Total<span><?echo formatPrice($final_price)?> €</span></li>
										<li>(+) Enviament<span>
                                            <?if($final_price>=50):?>
                                                <span>Gratuït</span>
                                            <?else:?>
                                                <span><?echo formatPrice(5)?> €</span>
                                            <?endif;?>
                                            </span></li>
										<li class="last">Total
                                            <?if($final_price>=50):?>
                                                <span><?echo formatPrice($final_price)?> €</span>
                                            <?else:?>
                                                <span><?echo formatPrice($final_price+5)?> €</span>
                                            <?endif;?></li>
									</ul>
								</div>
							</div>
							<!--/ End Order Widget -->
							<!-- Order Widget -->
							<div class="single-widget">
								<h2>Mètodes de pagament</h2>
								<div class="content">
									<div class="checkbox">
										<label class="checkbox-inline"><input name="payment" id="1" type="checkbox"> Pagar amb targeta</label>
										<label class="checkbox-inline"><input name="payment" id="3" type="checkbox"> PayPal</label>
									</div>
								</div>
							</div>
							<!--/ End Order Widget -->
							<!-- Payment Method Widget -->
							<div class="single-widget payement">
								<div class="content">
									<img src="images/payment-method.png" alt="#">
								</div>
							</div>
							<!--/ End Payment Method Widget -->
							<!-- Button Widget -->
							<div class="single-widget get-button">
								<div class="content">
                                    <form action="../../controllers/CommandController.php" method="post" >
                                        <input value="<?echo $_GET['cart_id']?>" name="cart_id_newCommand" style="display: none">
                                        <input value="<?echo $sending_price?>" name="sending_price_newCommand" style="display: none">

                                        <input value="<?php if(count($addresses)>0){echo $addresses[0]['id'];} ?>" name="address_id_newCommand" id="address_id_newCommand" style="display: none">
                                        <div class="button">
                                            <button type="submit" class="btn"
                                            <?if(count($addresses)==0 || count($cartItems)==0){ echo "disabled";} ?>
                                            >Realitzar pagament</button>
                                        </div>
                                    </form>

								</div>
							</div>
							<!--/ End Button Widget -->
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
							<h4>Free shiping</h4>
							<p>Orders over $100</p>
						</div>
						<!-- End Single Service -->
					</div>
					<div class="col-lg-3 col-md-6 col-12">
						<!-- Start Single Service -->
						<div class="single-service">
							<i class="ti-reload"></i>
							<h4>Free Return</h4>
							<p>Within 30 days returns</p>
						</div>
						<!-- End Single Service -->
					</div>
					<div class="col-lg-3 col-md-6 col-12">
						<!-- Start Single Service -->
						<div class="single-service">
							<i class="ti-lock"></i>
							<h4>Sucure Payment</h4>
							<p>100% secure payment</p>
						</div>
						<!-- End Single Service -->
					</div>
					<div class="col-lg-3 col-md-6 col-12">
						<!-- Start Single Service -->
						<div class="single-service">
							<i class="ti-tag"></i>
							<h4>Best Peice</h4>
							<p>Guaranteed price</p>
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
								<p class="text">Praesent dapibus, neque id cursus ucibus, tortor neque egestas augue,  magna eros eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor, facilisis luctus, metus.</p>
								<p class="call">Got Question? Call us 24/7<span><a href="tel:123456789">+0123 456 789</a></span></p>
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
									<p>Copyright © 2020 <a href="http://www.wpthemesgrid.com" target="_blank">Wpthemesgrid</a>  -  All Rights Reserved.</p>
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
</body>
<script>

    function selectChange(){
        document.getElementById("address_id_newCommand").value = document.getElementById("select_address").value;
    }
</script>
</html>