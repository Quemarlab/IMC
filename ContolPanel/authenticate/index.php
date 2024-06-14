<?php
require '../config.php';

if (!isset($_COOKIE['logintimes'])) {
    $login_access = 1;
    setcookie("logintimes", "" . $login_access . "", time() + 60 * 60 * 24 * 7);
}

if (isset($_SESSION['management'])) {
    header("location:management/");
} elseif (isset($_COOKIE['redirection'])) {
    header("location:" . $_COOKIE['redirection'] . "/");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Authentication &mdash; Serge</title>

    <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

    <link rel="stylesheet" href="../assets/modules/bootstrap-social/bootstrap-social.css">

    <!-- Template CSS -->
    <link rel="stylesheet" href="../assets/css/style.min.css">
    <link rel="stylesheet" href="../assets/css/components.min.css">
    <link rel="icon" href="../assets/logo/logo.png">
</head>

<body class="layout-4">

    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <div class="login-brand">
                            <img src="../assets/logo/logo.png" alt="logo" width="100" height="100" class="shadow-light rounded-circle">
                        </div>
                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>Authentication</h4>
                            </div>
                            <div class="alert alert-danger error-text" style="margin-top: 5px; display: none"></div>
                            <div class="card-body">
                                <form method="POST" action="#" class="needs-validation authentication" novalidate="">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input id="email" type="email" class="form-control" name="email" tabindex="1" required autocomplete="off" autofocus>
                                        <div class="invalid-feedback">
                                            Please fill in your email
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="d-block">
                                            <label for="password" class="control-label">Password</label>
                                            <div class="float-right">
                                                <a href="auth-forgot-password.html" class="text-small">
                                                    Forgot Password?
                                                </a>
                                            </div>
                                        </div>
                                        <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                                        <div class="invalid-feedback">
                                            please fill in your password
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                                            <label class="custom-control-label" for="remember-me">Remember Me</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block button" tabindex="4">
                                            <span class="spinner-grow spinner-grow-sm loader-icon" style="display: none;"></span>
                                            Login
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="simple-footer">
                            Copyright &copy; Serge <?= date('Y') ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script src="../assets/bundles/lib.vendor.bundle.js"></script>
    <script src="../js/script.js"></script>
    <script src="../js/scripts.js"></script>
    <script src="../js/custom.js"></script>
    <script src="../ajax/js/authentication.js"></script>
</body>

</html>