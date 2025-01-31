<?php 
require 'inc/header.php'; 
if (isset($_GET['token']) && isset($_GET['time'])) {
    $token = $_GET['token'];
    $dateGenerated = $_GET['time'];
    $current_time = date('h:i:s d-m-Y');

    $date1 = DateTime::createFromFormat('H:i:s d-m-Y', $dateGenerated);
    $date2 = DateTime::createFromFormat('H:i:s d-m-Y', $current_time);

    $interval = $date2->diff($date1);
    
    if ($interval->format('%s seconds') > 600) {
        echo "<script>window.alert('Link is expired'); self.location='../authenticate/'</script>";
    }
}
else {
    header('location: forget-password');
}
?>
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4>Reset Password</h4>
                        </div>
                        <div class="card-body">
                            <div class="error-text" style="margin-top: 5px; display: none"></div>
                            <p class="text-muted">Please fill out the form to reset password</p>
                            <form action="#" class="resetPassword">
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="hidden" name="token" value="<?php echo $token; ?>">

                                    <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator" name="password" tabindex="2" autofocus>
                                    <div id="pwindicator" class="pwindicator">
                                        <div class="bar"></div>
                                        <div class="label"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password-confirm">Confirm Password</label>
                                    <input id="password-confirm" type="password" class="form-control" name="confirm-password" tabindex="2">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="clickBtn btn btn-primary btn-lg btn-block" tabindex="4">Reset Password</button>
                                </div>
                            </form>
                            <a href="../authenticate/" class="text-center">Already reset? Login</a>
                        </div>
                        <div class="card-footer text-center">
                            <span class="spinner-grow spinner-grow-lg loader-icon" style="display: none"></span>
                        </div>
                    </div>
<?php require 'inc/footer.php'; ?>
<script src="../ajax/js/reset-password.js"></script>
