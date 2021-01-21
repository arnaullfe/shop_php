<?php
include_once('../../modals/Database.php');
include_once ('../../controllers/AdminTokenController.php');
session_start();
if(!isset($_SESSION["user_info"])){
    header("location: ../botiga_view/index.php");
} else{
    $database = new Database();
    $users = $database->executeQuery("SELECT * FROM users", array());
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
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


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
               aria-expanded="true" aria-controls="collapseTwo">
                <b><i class="fas fa-clipboard-check"></i>
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
               <b><i class="fas fa-users" style="color: white"></i>
                   <span style="color: white">Usuaris</span></a></b>
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

                <!-- Page Heading -->
                <h1 class="h3 mb-2 text-gray-800">Llista d'usuaris</h1>
                <p class="mb-4">Administració de tot el llistat d'usuaris del sistema.</p>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold" style="color: #F7941D">Usuaris</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Nom i cognoms</th>
                                    <th>Email</th>
                                    <th>Tipus</th>
                                    <th>Estat</th>
                                    <th>Últim login</th>
                                    <th>Accions</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>Nom i cognoms</th>
                                    <th>Email</th>
                                    <th>Tipus</th>
                                    <th>Estat</th>
                                    <th>Últim login</th>
                                    <th>Accions</th>
                                </tr>
                                </tfoot>
                                <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user["name"] . " " . $user["lastnames"] ?></td>
                                        <td><?php echo $user["email"] ?></td>
                                        <td style="text-align: center;">
                                            <?php if ($user["role"] == 0 || $user["role"] == 1): ?>
                                                <select class="form-control" id="<?php echo "check-".$user["id"]?>" onchange="adminAction(<?php echo $user["id"]?>)">
                                                    <option value="0">Client</option>
                                                    <option  value="1" <?php  if($user["role"] == 1){echo "selected";}?>>Administrador</option>
                                                    <?php if($_SESSION["user_info"][0]["role"]==2):?>
                                                        <option  value="2">Superadministrador</option>
                                                    <?endif;?>
                                                </select>
                                            <?php else: ?>
                                                <select class="form-control" id="<?php echo "check-".$user["id"]?>" disabled>
                                                        <option  value="2">Superadministrador</option>
                                                </select>
                                            <? endif; ?>
                                        </td>
                                        <td><?php if ($user["banned"] == 1): ?>
                                                Banejat
                                            <?php elseif ($user["activated"] == 1): ?>
                                                Activat
                                            <?php else: ?>
                                                No activat
                                            <? endif; ?>
                                        </td>
                                        <td><?php
                                            if ($user["last_session"] == null) {
                                                echo "No ha fet login";
                                            } else {
                                                echo formatDate($user["last_session"]);
                                            }
                                            ?>

                                        </td>
                                        <td>
                                            <?php if ($user["role"] < 2 || $user["banned"] == 1): ?>
                                                <?php if ($user["banned"] == 0): ?>
                                                    <a href='<?php echo "http://".$_SERVER["SERVER_NAME"]."/controllers/UserController.php?id_ban=".$user["id"]."&status_ban=1"?>' class="btn btn-warning btn-sm" title="Banejar"><i class="fas fa-ban"></i></a>
                                                <?php else: ?>
                                                    <a href='<?php echo "http://".$_SERVER["SERVER_NAME"]."/controllers/UserController.php?id_ban=".$user["id"]."&status_ban=0"?>' class="btn btn-success btn-sm" title="Desbanejar"><i
                                                                class="fas fa-undo-alt"></i></a>
                                                <?php endif; ?>
                                                <a href='<?php echo "http://".$_SERVER["SERVER_NAME"]."/controllers/UserController.php?id_delete=".$user["id"]?>' class="btn btn-danger btn-sm" title="Eliminar usuari"><i class="fas fa-trash-alt"></i></a>
                                            <? else: ?>
                                                <button class="btn btn-warning btn-sm" title="Banejar" disabled><i
                                                            class="fas fa-ban"></i></button>
                                                <button class="btn btn-danger btn-sm" title="Eliminar usuari" disabled>
                                                    <i class="fas fa-trash-alt"></i></button>
                                            <? endif; ?>
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

<script>
    function adminAction(id){
        var action = document.getElementById("check-"+id).value;
        $.ajax({
            type: "GET",
            url: '../../controllers/UserController.php',
            data: {"id_admin": id,"status_admin":action},
            dataType: 'JSON',
            success: function (response) {
                console.log("END")
                location.reload();
            }
        })
    }

</script>

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
?>