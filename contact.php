<?php
require 'ControlPanel/config.php';
require 'inc/header.php';
?>

<!-- start page-title -->
<section class="page-title">
    <div class="container">
        <div class="row">
            <div class="col col-xs-12">
                <h2>Contact</h2>
                <ol class="breadcrumb">
                    <li><a href="../Serge/">Home</a></li>
                    <li>Contact</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div> <!-- end container -->
</section>
<!-- end page-title -->


<!-- start contact-pg-section -->
<section class="contact-pg-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col col-xs-12">
                <div class="section-title-s2">
                    <span>Our contact info</span>
                    <h2>Contact with us</h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col col-lg-6">
                <div class="contact-form">
                    <form method="post" class="contact-validation-active" id="contact-form-s2">
                        <div class="half-col">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Your Name">
                        </div>
                        <div class="half-col">
                            <input type="email" name="email" id="email" class="form-control" placeholder="Your Email">
                        </div>
                        <div class="half-col">
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Your Phone">
                        </div>
                        <div class="half-col">
                            <input type="text" name="address" id="address" class="form-control" placeholder="Address">
                        </div>
                        <div>
                            <textarea class="form-control" name="note" id="note" placeholder="Description..."></textarea>
                        </div>
                        <div class="submit-btn-wrapper">
                            <button type="submit" class="theme-btn-s3">Appointment</button>
                            <div id="loader">
                                <i class="ti-reload"></i>
                            </div>
                        </div>
                        <div class="clearfix error-handling-messages">
                            <div id="success">Thank you</div>
                            <div id="error"> Error occurred while sending email. Please try again later. </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col col-lg-6">
                <div class="info-box-outer">
                    <div class="info-box">
                        <div class="grid">
                            <h3>Address</h3>
                            <p>House No, 45 street 21 Bing bang jeoor, Ukrain</p>
                        </div>
                        <div class="grid">
                            <h3>Phone</h3>
                            <p>654974-644545-344, &nbsp; 654974-644545-344</p>
                        </div>
                        <div class="grid">
                            <h3>Email</h3>
                            <p>group@info.com, &nbsp; deom@ex.com</p>
                        </div>
                        <div class="grid">
                            <ul class="social">
                                <li><a href="#"><i class="ti-facebook"></i></a></li>
                                <li><a href="#"><i class="ti-twitter-alt"></i></a></li>
                                <li><a href="#"><i class="ti-linkedin"></i></a></li>
                                <li><a href="#"><i class="ti-pinterest"></i></a></li>
                                <li><a href="#"><i class="ti-vimeo-alt"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col col-xs-12">
                <div class="contact-map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d50470.07476441127!2d-122.47261934500531!3d37.75769481910017!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80859a6d00690021%3A0x4a501367f076adff!2sSan+Francisco%2C+CA%2C+USA!5e0!3m2!1sen!2sbd!4v1532762769544" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div> <!-- end container -->
</section>
<!-- end contact-pg-section -->


<!-- stat cta-s2-section -->
<section class="cta-s2-section">
    <div class="container">
        <div class="row">
            <div class="col col-sm-9">
                <h3>If you need any industrial solution, please contact with us</h3>
            </div>
            <div class="col col-sm-3">
                <div class="contact-btn">
                    <a href="#" class="theme-btn-s4">Contact With Us</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end cta-s2-section -->


<?php
require 'inc/footer.php';
?>