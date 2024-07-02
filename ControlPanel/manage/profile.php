<?php
include 'inc/header.php';
include 'inc/sidebar.php';
$location = $userAccess->userLocation($userProfile['account_id']);
?>

 <!-- Start app main Content -->
 <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>Profile</h1>
                    <div class="section-header-breadcrumb">
                    </div>
                </div>
                <div class="section-body">
                    <h2 class="section-title">Hi, <?php echo $userProfile['lastname'] ?></h2>
                    <p class="section-lead">Change information about yourself on this page.</p>

                    <div class="row mt-sm-4">
                        <div class="col-12 col-md-12 col-lg-5">
                            <div class="card profile-widget">
                                <div class="profile-widget-header">                     
                                    <img alt="Profile" src="../upload/users/<?php echo $userProfile['profile'] ?>" class="rounded-circle profile-widget-picture">
                                    <div class="profile-widget-items">
                                        <div class="profile-widget-item">
                                            <div class="profile-widget-item-label">Status</div>
                                            <div class="profile-widget-item-value"><?php echo $userProfile['last_access'] ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="profile-widget-description">
                                    <div class="profile-widget-name"><?php echo $userProfile['firstname']." ".$userProfile['lastname'] ?> <div class="text-muted d-inline font-weight-normal"><div class="slash"></div><?php echo $userProfile['username']?></div></div>
                                    <b>Email Address:</b> <?php echo $userProfile['email'] ?><br><br>
                                    <p class="text-muted font-weight-normal">Last access location</p>
                                    <b>Country: </b><?php echo $location['country'] ?><br>
                                    <b>Region: </b><?php echo $location['region'] ?><br>
                                    <b>City: </b><?php echo $location['city'] ?><br>
                                    <b>Coordinates: </b><?php echo $location['latitude'].' | '.$location['longitude'] ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-7">
                            <div class="card">
                                
                                    <div class="card-header">
                                        <h4>Edit Profile</h4>
                                    </div>
                                    <div class="card-body">
                                    <div class="error-text" style="margin-top: 5px; display: none"></div>
                                    <form method="post" class="needs-validation userInfo" novalidate="">
                                        <div class="row">
                                            <div class="form-group col-md-6 col-12">
                                                <label>First Name</label>
                                                <input type="hidden" name="account_id" value="<?php echo $userProfile['account_id'] ?>">
                                                <input type="text" class="form-control" name="firstname" value="<?php echo $userProfile['firstname'] ?>" required="">
                                                <div class="invalid-feedback">Please fill in the first name</div>
                                            </div>
                                            <div class="form-group col-md-6 col-12">
                                                <label>Last Name</label>
                                                <input type="text" class="form-control" name="lastname" value="<?php echo $userProfile['lastname'] ?>" required="">
                                                <div class="invalid-feedback">Please fill in the last name</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-7 col-12">
                                                <label>Email</label>
                                                <input type="email" class="form-control" name="email" value="<?php echo $userProfile['email'] ?>" required="">
                                                <div class="invalid-feedback">Please fill in the email</div>
                                            </div>
                                            <div class="form-group col-md-5 col-12">
                                                <label>Username</label>
                                                <input type="text" class="form-control" name="username" value="<?php echo $userProfile['username'] ?>" required="">
                                                <div class="invalid-feedback">Please fill in the username</div>
                                            </div>
                                            <div class="form-group col-md-5 col-12">
                                                <button class="btn btn-primary saveUser">Save Changes</button>
                                            </div>   
                                        </div>
                                    </form>
                                    <div class="divider"></div>
                                    <form method="post" class="needs-validation userPass" novalidate="">
                                        <div class="row">
                                            <div class="form-group col-md-4 col-12">
                                                <label>Current Password</label>
                                                <input type="hidden" name="account_id" value="<?php echo $userProfile['account_id'] ?>">
                                                <input type="password" class="form-control" name="current" value="" required="">
                                                <div class="invalid-feedback">Enter Current Password</div>
                                            </div>
                                            <div class="form-group col-md-4 col-12">
                                                <label>New Password</label>
                                                <input id="password" type="password" name="password" class="form-control pwstrength" data-indicator="pwindicator" required="">
                                                <div id="pwindicator" class="pwindicator">
                                                    <div class="bar"></div>
                                                    <div class="label"></div>
                                                </div>
                                                <div class="invalid-feedback">Enter new Password</div>
                                            </div>
                                            <div class="form-group col-md-4 col-12">
                                                <label>Confirm Password</label>
                                                <input type="password" class="form-control" name="confirm" value="" required="">
                                                <div class="invalid-feedback">Confirm Password</div>
                                            </div>
                                            <div class="form-group col-md-5 col-12">
                                                <button class="btn btn-primary savePass">Change Password</button>
                                            </div>
                                            <div class="loading text-center">
                                            <span class="spinner-grow spinner-grow-lg loader-icon" style="display: none"></span>
                                            </div> 
                                        </div>
                                    </form>

                                    </div>
                                    <div class="card-footer text-right">
                                       Powered by Q-Security 
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

<?php
include 'inc/footer.php'; 
?>
<script src="../ajax/js/profile.js"></script>