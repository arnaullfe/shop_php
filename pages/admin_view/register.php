<?php
    session_start();
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

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</head>

<body>

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7 p-0">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Crear un compte!</h1>
                            </div>
                            <form class="user" action="../../controllers/UserController.php" method="post">
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" name="name_register" class="form-control form-control-user" placeholder="Nom" value="<?php echo $_SESSION["name_register"]?>">
                                        <?php if(isset($_SESSION["register_errors"]) && in_array("error_name_register",$_SESSION["register_errors"])):?>
                                            <label class="ml-3 text-danger" style="font-size: 14px;"><i class="fas fa-exclamation-circle mr-1"></i>Nom introduït erroni</label>
                                        <?php endif;?>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" name="lastnames_register" class="form-control form-control-user" placeholder="Cognoms" value="<?php echo $_SESSION["lastnames_register"]?>">
                                        <?php if(isset($_SESSION["register_errors"]) && in_array("error_lastnames_register",$_SESSION["register_errors"])):?>
                                            <label class="ml-3 text-danger" style="font-size: 14px;"><i class="fas fa-exclamation-circle mr-1"></i>Cognom introduït erroni</label>
                                        <?php endif;?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email" name="email_register" class="form-control form-control-user" placeholder="Email" value="<?php echo $_SESSION["email_register"]?>">
                                    <?php if(isset($_SESSION["register_errors"]) && in_array("error_email_register",$_SESSION["register_errors"])):?>
                                        <label class="ml-3 text-danger" style="font-size: 14px;"><i class="fas fa-exclamation-circle mr-1"></i>Email invàlid o ja existent</label>
                                    <?php endif;?>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" name="password_register" class="form-control form-control-user" placeholder="Contrasenya" value="<?php echo $_SESSION["password_register"]?>">
                                        <?php if(isset($_SESSION["register_errors"]) && in_array("error_password_register",$_SESSION["register_errors"])):?>
                                            <label class="ml-3 text-danger" style="font-size: 14px;"><i class="fas fa-exclamation-circle mr-1"></i>La contrasenya ha de tenir 5 caràcters com a mínim</label>
                                        <?php endif;?>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" name="password_confirm_register" class="form-control form-control-user" placeholder="Repeteix al contrasenya">
                                        <?php if(isset($_SESSION["register_errors"]) && in_array("error_password_confirm_register",$_SESSION["register_errors"])):?>
                                            <label class="ml-3 text-danger" style="font-size: 14px;"><i class="fas fa-exclamation-circle mr-1"></i>Les contrasenyes no concideixen</label>
                                        <?php endif;?>
                                    </div>
                                </div>
                                <div class="g-recaptcha" data-sitekey="6LevP-MZAAAAABZnHhp6jWTxtHJPbLwzgYha1_Lk"></div>
                                <?php if(isset($_SESSION["register_errors"]) && in_array("error_recaptcha_register",$_SESSION["register_errors"])):?>
                                    <label class="ml-3 text-danger" style="font-size: 14px;"><i class="fas fa-exclamation-circle mr-1"></i>Fés clic el reCAPTCHA per continuar</label>
                                <?php endif;?>
                                <button type="submit" href="login.html" class="btn btn-primary btn-user btn-block mt-3" style="background-color: #F6931D;border-color: #f5a342">
                                    Registrar el compte
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="forgot-password.php">Has oblidat la contrasenya?</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="login.php">Ja tens compte? Login!</a>
                            </div>
                        </div>
                        <div class=" p-0 m-0">
                            <a href="../botiga_view/index.php" class="btn btn-dark btn-user btn-block" style="color: white;background-color: #201F1E;border-radius: 0px;"><i class="fas fa-chevron-left" style="float: left;margin-top: 1%;margin-left: 10px"></i>Tornar a la botiga</a>
                        </div>
                    </div>
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

</body>

</html>

<?php
    unset($_SESSION["register_errors"]);
?>