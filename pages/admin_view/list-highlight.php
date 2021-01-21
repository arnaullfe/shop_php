<?php
include_once('../../modals/Database.php');
include_once ('../../controllers/AdminTokenController.php');
session_start();
if(!isset($_SESSION["user_info"])){
    header("location: ../botiga_view/index.php");
} else{
    $database = new Database();
    $highlights = $database->executeQuery("SELECT *,shop.products.name as 'product_name' FROM shop.highlights,shop.products WHERE shop.highlights.product_id=shop.products.id;", array());
    $products = $database->executeQuery("SELECT * FROM products",array());
    $database->closeConnection();
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
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
          rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

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
                <b><i class="fas fa-clipboard-check"  style="color: white"></i>
                    <span>Productes</span></b>
            </a>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Administrar productes:</h6>
                    <a class="collapse-item" href="list-categories.php"><b>Categories</b></a>
                    <a class="collapse-item" href="list-products.php"><b>Productes i estoc</b></a>
                    <a class="collapse-item" href="list-tags.php"><b>Tags</b></a>
                    <a class="collapse-item" href="list-discounts.php"><b>Descomptes</b></a>
                    <a class="collapse-item" href="list-highlight.php"><b>Productes destacats</b></a>
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
            <a class="nav-link" href="list-users.php" >
                <b><i class="fas fa-users"></i>
                    <span>Usuaris</span></a></b>
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


                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle"  id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="img-profile rounded-circle" src='<?php echo $_SESSION["user_info"][0]["image"]?>'>
                            <span class="ml-2 d-none d-lg-inline text-gray-600 small"> <?php echo $_SESSION["user_info"][0]["name"]." ".$_SESSION["user_info"][0]["lastnames"];?></span>
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="userDropdown">
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
                <?php if(isset($_SESSION["message"])):?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION["message"]?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?endif;?>
                <?php if(isset($_SESSION["error_message"])):?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION["error_message"]?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?endif;?>
                <!-- Page Heading -->
                <h1 class="h3 mb-2 text-gray-800">Llista de productes destacats</h1>
                <p class="mb-4">Administració dels productes destacats</p>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold d-inline-block" style="color: #F7941D">Productes destacats</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Títol destacat</th>
                                    <th>Producte</th>
                                    <th>Tipus</th>
                                    <th>Estat</th>
                                    <th>Accions</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>Títol destacat</th>
                                    <th>Producte</th>
                                    <th>Tipus</th>
                                    <th>Estat</th>
                                    <th>Accions</th>
                                </tr>
                                </tfoot>
                                <tbody>
                                <?php foreach ($highlights as $highlight): ?>
                                    <tr>
                                        <td><?php echo $highlight["title"]?></td>
                                        <td><?php echo $highlight["product_name"]?></td>
                                        <td><?php echo "Producte destacat"?></td>
                                        <td><?php if($highlight["highlight_type"]==1):
                                                echo "Activat";
                                            else:
                                                echo "Desactivat";
                                            endif; ?> </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editHighlight" title="Editar"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                <? endforeach; ?>
                                </tbody>
                            </table>
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

<!--create category modal-->

<div class="modal fade" id="editHighlight" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">Editar producte destacat</h6>
            </div>
            <form action="../../controllers/HighlightsController.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-form-label mr-3">Activat: </label>
                        <input type="checkbox" data-toggle="toggle" name="activated_highlight" data-onstyle="success" value="on">
                    </div>
                    <div class="form-group">
                        <label for="product">Producte destacat</label>
                        <select name="product_id_highlight" class="form-control" id="product_id_highlight" >
                            <?php foreach ($products as $product):?>
                                <option value="<?php echo $product['id']?>"
                                <?if($highlights[0]["product_id"]==$product['id']){ echo "selected";}?>><?php echo $product['name']?></option>
                            <?php endforeach;?>
                        </select>

                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Producte destacat:</label>
                        <input type="text" name="name_highlight" class="form-control" value="<?echo $highlights[0]["title"]?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel·lar</button>
                    <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                </div>
            </form>

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
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<style>
    .page-item.active .page-link {
        background-color: #F7941D !important;
        border: 1px solid #F7941D;
    }
</style>


</body>

</html>
<?php

function formatDate($date){
    $date = new DateTime($date);
    return date_format($date,"d/m/Y H:i:s");
}

function calculateDiscount($new_price,$old_price){
    $diff = $old_price - $new_price;

}
unset($_SESSION["message"]);
unset($_SESSION["error_message"]);
?>
