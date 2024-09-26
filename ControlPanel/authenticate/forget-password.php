<?php require 'inc/header.php'; ?>
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4>Forgot Password</h4>
                        </div>
                        <div class="card-body">
                        <div class="error-text" style="margin-top: 5px; display: none"></div>
                            <p class="text-muted">We will send a link to reset your password</p>
                            <form action="#" class="forgotPassword">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input id="email" type="email" class="form-control" name="email" tabindex="1" autofocus>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="clickBtn btn btn-primary btn-lg btn-block" tabindex="4">
                                    Forgot Password
                                    </button>
                                </div>
                            </form>
                            <a href="../authenticate/" class="text-center">Remember? Login</a>
                        </div>
                        <div class="card-footer text-center">
                            <span class="spinner-grow spinner-grow-lg loader-icon" style="display: none"></span>
                        </div>
                    </div>
<?php require 'inc/footer.php'; ?>
<script src="../ajax/js/forgot-password.js"></script>