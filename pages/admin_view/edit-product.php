<?php
include_once('../../modals/Database.php');
include_once('../../controllers/AdminTokenController.php');
session_start();
if (!isset($_SESSION["user_info"]) || !isset($_GET["product_id"])) {
    header("location: ../botiga_view/index.php");
} else {
    $database = new Database();
    $categories = $database->executeQuery("SELECT * FROM productCategory", array());
    $images = $database->executeQuery("SELECT * FROM images_product WHERE id_product=?",array($_GET["product_id"]));
    $product = $database->executeQuery("SELECT * FROM products WHERE id=?",array($_GET["product_id"]));
    $tags = $database->executeQuery("SELECT * FROM tags",array());
    $database->closeConnection();
    if(!isset($_SESSION["images_editProduct"])){
        $_SESSION["images_editProduct"] = $images;
        $_SESSION["name_editProduct"] = $product[0]["name"];
        $_SESSION["description_editProduct"] = $product[0]["description"];
        $_SESSION["units_editProduct"] = $product[0]["units"];
        $_SESSION["price_editProduct"] = $product[0]["price_iva"];
        $_SESSION["priceIva_type_editProduct"] = 1;
        $_SESSION["category_editProduct"] = $product[0]["category_id"];
        $_SESSION["iva_editProduct"] = $product[0]["iva"];
        $_SESSION["tag_editProduct"] = $product[0]["tag_id"];
        }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Eshop</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../botiga_view/images/favicon.png">

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
            integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
            crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
          rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script src="../../dependencies/Dropzone/dist/dropzone.js"></script>
</head>

<body id="page-top">
<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #F7941D">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
            <div class="sidebar-brand-icon">
                <img src="../botiga_view/images/favicon.png">
            </div>
            <div class="sidebar-brand-text mx-3">E-shop <sup style="font-size: 10px">Online</sup></div>
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="index.php">
                <b>
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Tauler de control</span></a>
            </b>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Botiga
        </div>

        <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
               aria-expanded="true" aria-controls="collapseTwo" style="color: white">
                <b><i class="fas fa-clipboard-check" style="color: white"></i>
                    <span>Productes</span></b>
            </a>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Administrar productes:</h6>
                    <a class="collapse-item" href="list-categories.php"><b>Categories</b></a>
                    <a class="collapse-item" href="list-products.php"><b>Productes i estoc</b></a>
                    <a class="collapse-item" href="list-tags.php"><b>Tags</b></a>
                    <a class="collapse-item" href="list-discount"><b>Descomptes</b></a>
                </div>
            </div>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Opcions
        </div>

        <!-- Nav Item - Charts -->
        <li class="nav-item">
            <a class="nav-link" href="list-users.php">
                <b><i class="fas fa-users"></i>
                    <span>Usuaris</span></a></b>
        </li>

        <!-- Nav Item - Charts -->
        <li class="nav-item">
            <a class="nav-link" href="charts.php">
                <b><i class="fas fa-fw fa-chart-area"></i>
                    <span>Informació</span></a></b>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

    </ul>
    <!-- End of Sidebar -->
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <!-- Sidebar Toggle (Topbar) -->
                <form class="form-inline">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                </form>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <!-- Nav Item - Alerts -->
                    <li class="nav-item dropdown no-arrow mx-1">
                        <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bell fa-fw"></i>
                            <!-- Counter - Alerts -->
                            <span class="badge badge-danger badge-counter">3+</span>
                        </a>
                        <!-- Dropdown - Alerts -->
                        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="alertsDropdown">
                            <h6 class="dropdown-header">
                                Alerts Center
                            </h6>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="mr-3">
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-file-alt text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">December 12, 2019</div>
                                    <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                </div>
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="mr-3">
                                    <div class="icon-circle bg-success">
                                        <i class="fas fa-donate text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">December 7, 2019</div>
                                    $290.29 has been deposited into your account!
                                </div>
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="mr-3">
                                    <div class="icon-circle bg-warning">
                                        <i class="fas fa-exclamation-triangle text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">December 2, 2019</div>
                                    Spending Alert: We've noticed unusually high spending for your account.
                                </div>
                            </a>
                            <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                        </div>
                    </li>

                    <!-- Nav Item - Messages -->
                    <li class="nav-item dropdown no-arrow mx-1">
                        <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-envelope fa-fw"></i>
                            <!-- Counter - Messages -->
                            <span class="badge badge-danger badge-counter">7</span>
                        </a>
                        <!-- Dropdown - Messages -->
                        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="messagesDropdown">
                            <h6 class="dropdown-header">
                                Message Center
                            </h6>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="dropdown-list-image mr-3">
                                    <img class="rounded-circle" src="img/undraw_profile_1.svg"
                                         alt="">
                                    <div class="status-indicator bg-success"></div>
                                </div>
                                <div class="font-weight-bold">
                                    <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                        problem I've been having.
                                    </div>
                                    <div class="small text-gray-500">Emily Fowler · 58m</div>
                                </div>
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="dropdown-list-image mr-3">
                                    <img class="rounded-circle" src="img/undraw_profile_2.svg"
                                         alt="">
                                    <div class="status-indicator"></div>
                                </div>
                                <div>
                                    <div class="text-truncate">I have the photos that you ordered last month, how
                                        would you like them sent to you?
                                    </div>
                                    <div class="small text-gray-500">Jae Chun · 1d</div>
                                </div>
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="dropdown-list-image mr-3">
                                    <img class="rounded-circle" src="img/undraw_profile_3.svg"
                                         alt="">
                                    <div class="status-indicator bg-warning"></div>
                                </div>
                                <div>
                                    <div class="text-truncate">Last month's report looks great, I am very happy with
                                        the progress so far, keep up the good work!
                                    </div>
                                    <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                </div>
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="dropdown-list-image mr-3">
                                    <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60"
                                         alt="">
                                    <div class="status-indicator bg-success"></div>
                                </div>
                                <div>
                                    <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                                        told me that people say this to all dogs, even if they aren't good...
                                    </div>
                                    <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                </div>
                            </a>
                            <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                        </div>
                    </li>

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="img-profile rounded-circle"
                                 src='<?php echo $_SESSION["user_info"][0]["image"] ?>'>
                            <span class="ml-2 d-none d-lg-inline text-gray-600 small"> <?php echo $_SESSION["user_info"][0]["name"] . " " . $_SESSION["user_info"][0]["lastnames"]; ?></span>
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Perfil
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                Configuració
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logs d'activitat
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-danger"></i>
                                Logout
                            </a>
                        </div>
                    </li>

                </ul>

            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <?php if (isset($_SESSION["message"])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION["message"] ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <? endif; ?>
                <!-- Page Heading -->
                <h1 class="h3 mb-2 text-gray-800">Editar producte</h1>
                <p class="mb-4">Editar les dades del producte</p>

                <!-- DataTales Example -->

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold d-inline-block" style="color: #F7941D">Imatges</h6>
                    </div>
                    <div class="card-body">
                        <div class="container pl-5 pr-5 text-center" >
                            <div id="dropzone">
                                <form action="../../controllers/ProductController.php" class="dropzone needsclick" id="my-awesome-dropzone">
                                    <div class="row text-center justify-content-center">
                                        <div class="dz-message needsclick">
                                            <strong>Arrosega imatges</strong> o <strong>fés clic</strong> per poder pujar imatges.
                                            <span class="note needsclick">(Les imatges han de ser més petites de <strong>3MB</strong>)</span>
                                        </div>
                                        <?php foreach ($_SESSION["images_editProduct"] as $image):?>
                                        <div class="row text-center justify-content-center d-flex">
                                            <div class="col-12 col-md-3 mt-3 mr-5 image-area">
                                                <img src="<?php echo $image['url']?>" class="img-fluid border" onclick=window.open("<?php echo $image['url']?>") />
                                                <button type="button" class="remove-image" onclick=deleteImage("<?php echo $image['id']?>","<?php echo $image['url']?>","<?php echo $_GET["product_id"]?>"); style="display: inline;">&#215;</button>
                                            </div>
                                        </div>
                                        <?endforeach;?>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold d-inline-block" style="color: #F7941D">Dades</h6>
                    </div>
                    <div class="card-body">
                        <div class="container pl-5 pr-5">
                            <form action="../../controllers/ProductController.php" method="post" class="mt-5">
                                <div class="form-group mb-4 row">
                                    <div class="col-4 text-right pr-5">
                                        <label class="mr-5"><b>Producte activat:</b> </label>
                                    </div>
                                    <div class="col-8">
                                        <input type="checkbox" name="activated_editProduct" checked data-toggle="toggle"
                                               data-onstyle="success">
                                    </div>
                                </div>
                                <div class="form-group d-flex row">
                                    <div class="col-4 text-right pr-5">
                                        <label class="mr-5"><b>Categoria:</b> </label>
                                    </div>
                                    <div class="col-8">
                                        <select class="form-control w-75" name="category_editProduct">
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category["id"] ?>" <?php if(isset($_SESSION["category_editProduct"]) && $category["id"]==$_SESSION["category_editProduct"]):?>selected<?endif;?>><?php echo $category["name"] ?></option>
                                            <? endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group d-flex">
                                    <div class="col-4 text-right pr-5">
                                        <label class="mr-5"><b>Nom:</b> </label>
                                    </div>
                                    <div class="col-8">
                                        <input name="id_editProduct" value="<?php echo $_GET["product_id"]?>" style="display: none">
                                        <input class="form-control w-75" placeholder="Introdueix el nom..."
                                               name="name_editProduct" value="<?php echo $_SESSION["name_editProduct"]?>">
                                        <?php if (isset($_SESSION["errors_editProduct"]) && in_array("error_name_editProduct", $_SESSION["errors_editProduct"])): ?>
                                            <label class="text-danger" style="font-size: 15px;"><i class="fas fa-exclamation-circle mr-1"></i>Nom introduït erroni</label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group d-flex row">
                                    <div class="col-4 text-right pr-5">
                                        <label class="mr-5"><b>TAG:</b> </label>
                                    </div>
                                    <div class="col-8">
                                        <select class="form-control w-75" name="tag_editProduct">
                                            <option value="0">Sense tag</option>
                                            <?php foreach ($tags as $tag):?>
                                                <option value="<?php echo $tag["id"]?>" <?php if($tag["id"]==$_SESSION["tag_editProduct"]):?> selected <?endif;?>><?php echo $tag["name"]?></option>
                                            <?endforeach;?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group d-flex">
                                    <div class="col-4 text-right pr-5">
                                        <label class="mr-5"><b>Descripció:</b> </label>
                                    </div>
                                    <div class="col-8">
                                        <textarea class="form-control w-75" placeholder="Introdueix una descripció..."
                                                  name="description_editProduct"><?php echo $_SESSION["description_editProduct"]?></textarea>
                                        <?php if (isset($_SESSION["errors_editProduct"]) && in_array("error_description_editProduct", $_SESSION["errors_editProduct"])): ?>
                                            <label class="text-danger" style="font-size: 15px;"><i class="fas fa-exclamation-circle mr-1"></i>Escriu una descripció</label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group d-flex">
                                    <div class="col-4 text-right pr-5">
                                        <label class="mr-5"><b>Unitats:</b> </label>
                                    </div>
                                    <div class="col-8">
                                        <input class="form-control w-75" type="number" placeholder="Unitats"
                                               name="units_editProduct" value="<?php echo $_SESSION["units_editProduct"]?>">
                                        <?php if (isset($_SESSION["errors_editProduct"]) && in_array("error_units_editProduct", $_SESSION["errors_editProduct"])): ?>
                                            <label class="text-danger" style="font-size: 15px;"><i class="fas fa-exclamation-circle mr-1"></i>Escriu les unitats amb un enter</label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group d-flex">
                                    <div class="col-4 text-right pr-5">
                                        <label class="mr-5"><b>Preu introduït:</b> </label>
                                    </div>
                                    <div class="col-4 text-left m-0 pr-0">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="priceIva_type_editProduct" value="1" <?php if(!isset($_SESSION["priceIva_type_editProduct"]) || $_SESSION["priceIva_type_editProduct"]==1):?>checked<?endif;?>>
                                            <label class="form-check-label" for="exampleRadios2">
                                                Amb IVA
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4 text-left">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="priceIva_type_editProduct" value="0" <?php if(isset($_SESSION["priceIva_type_editProduct"]) && $_SESSION["priceIva_type_editProduct"]==0):?>checked<?endif;?>>
                                            <label class="form-check-label" for="exampleRadios2">
                                                Sense IVA
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group d-flex">
                                    <div class="col-4 text-right pr-5">
                                        <label class="mr-5"><b>Preu:</b> </label>
                                    </div>
                                    <div class="col-8">
                                        <div class="input-group" style="max-width: 75%">
                                            <input class="form-control" type="number" step="any" placeholder="Preu"
                                                   name="price_editProduct" value="<?php echo  $_SESSION["price_editProduct"]?>">
                                            <div class="input-group-append" style="height: 38px">
                                                <span class="input-group-text" >€</span>
                                            </div>
                                        </div>
                                        <?php if (isset($_SESSION["errors_editProduct"]) && in_array("error_price_editProduct", $_SESSION["errors_editProduct"])): ?>
                                            <label class="text-danger" style="font-size: 15px;"><i class="fas fa-exclamation-circle mr-1"></i>Introdueix un preu vàlid</label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group d-flex row">
                                    <div class="col-4 text-right pr-5">
                                        <label class="mr-5"><b>Tipus d'IVA:</b> </label>
                                    </div>
                                    <div class="col-8">
                                        <select class="form-control w-75" name="iva_editProduct">
                                            <option value="4" <?php if(isset($_SESSION["iva_editProduct"]) && $_SESSION["iva_editProduct"]==4):?>selected<?endif;?>>IVA Superreduït (4%)</option>
                                            <option value="10" <?php if(isset($_SESSION["iva_editProduct"]) && $_SESSION["iva_editProduct"]==10):?>selected<?endif;?>>IVA Reduït (10%)</option>
                                            <option value="21" <?php if(!isset($_SESSION["iva_editProduct"]) || $_SESSION["iva_editProduct"]==21):?>selected<?endif;?>>IVA General (21%)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group d-flex m-0 p-0 mt-5 ">
                                    <div class="col-0 col-md-4">
                                    </div>
                                    <div class="col-12 col-md-6 text-center m-0 p-0">
                                        <button class="btn btn-warning btn-block" type="submit"
                                                style="background-color: #F7941D;width: 100%;">Crear
                                        </button>
                                    </div>
                                    <div class="col-0 col-md-3">
                                    </div>
                                </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Eshop Online 2020</span>
        </div>
    </div>
</footer>
<!-- End of Footer -->

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">Ja vols marxar?</h6>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Clica logout si realment vols tancar la sessió.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel·lar</button>
                <a class="btn btn-danger btn-sm" href="login.php">Logout</a>
            </div>
        </div>
    </div>
</div>


<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page level custom scripts -->
<script src="js/demo/datatables-demo.js"></script>


<style>


    .dropzone {
        background: white;
        border-radius: 5px;
        border: 2px dashed rgb(0, 135, 247);
        border-image: none;
        margin-left: auto;
        margin-right: auto;
        padding:20px;
        cursor: pointer;
        min-height: 150px;
    }

    .image-area {
        position: relative;
        width: 50%;
        background: white;
        border: 3px solid #555;
    }
    .image-area img{
        max-width: 100%;
        height: auto;
    }
    .remove-image {
        display: none;
        position: absolute;
        top: -10px;
        right: -10px;
        border-radius: 10em;
        padding: 2px 6px 3px;
        text-decoration: none;
        font: 700 21px/20px sans-serif;
        background: #555;
        border: 3px solid #fff;
        color: #FFF;
        box-shadow: 0 2px 6px rgba(0,0,0,0.5), inset 0 2px 4px rgba(0,0,0,0.3);
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
        -webkit-transition: background 0.5s;
        transition: background 0.5s;
    }
    .remove-image:hover {
        background: #E54E4E;
        padding: 3px 7px;
        top: -11px;
        right: -11px;
        color: white;
        text-decoration: none;
    }
    .remove-image:active {
        background: #E54E4E;
        top: -10px;
        right: -11px;
    }
</style>

<script src="../../resouces/js/dropzoneFileEdit.js"></script>
<script>

    function deleteImage(id,url,id_product){
        $.ajax({
            url: '../../controllers/ProductController.php',
            type: "Post",
            data:{"delete_image_editProduct":id,"url_image_editProduct":url,"product_edit_id":id_product},
            contentType: 'application/x-www-form-urlencoded',
            success: function (response){
                location.reload();
            },
            error: function (response){
                location.reload();
            }
        })
    }


</script>
</body>

</html>
<?php
unset($_SESSION["message"]);
unset($_SESSION["images_editProduct"]);
unset($_SESSION["errors_editProduct"]);
unset($_SESSION["name_editProduct"]);
unset($_SESSION["description_editProduct"]);
unset($_SESSION["units_editProduct"]);
unset($_SESSION["price_editProduct"]);
unset($_SESSION["priceIva_type_editProduct"]);
unset($_SESSION["category_editProduct"]);
unset($_SESSION["iva_editProduct"]);
unset($_SESSION["image_id_temp"]);
unset($_SESSION["tag_editProduct"]);
?>

