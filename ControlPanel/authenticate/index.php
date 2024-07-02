                    <?php include 'inc/header.php'; ?>
                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>Authentication</h4>
                            </div>
                            <div class="alert alert-danger error-text" style="margin-top: 5px; display: none"></div>
                            <div class="card-body">
                                <form method="POST" action="#" class="needs-validation authentication" novalidate="">
                                    <div class="form-group">
                                        <label for="username">Email</label>
                                        <input id="username" type="text" class="form-control" name="email" tabindex="1" required autocomplete="off" autofocus>
                                        <div class="invalid-feedback">
                                            Please fill in your email
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="d-block">
                                            <label for="password" class="control-label">Password</label>
                                            <div class="float-right">
                                                <a href="forget-password" class="text-small">
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
                                            Login
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer text-center">
                                <span class="spinner-grow spinner-grow-lg loader-icon" style="display: none"></span>
                            </div>
                        </div>
                        <?php include 'inc/footer.php'; ?>
                        <script src="../ajax/js/authentication.js"></script>