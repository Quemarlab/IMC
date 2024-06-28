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
 
<!-- start newsletter-section -->
<section class="newsletter-section">
    <h2 class="hidden">Newsletter</h2>
    <div class="container">
        <div class="row">
            <div class="col col-xs-12">
                <div class="newsletter">
                    <div class="newsletter-form">
                        <form action="ControlPanel/mailerService/newsletter" method="POST" class="php-email-form">
                            <input type="text" class="form-control" name="email" placeholder="Subscribe Newsletter (Enter Email Address)">
                            <button type="submit"><i class="ti-email"></i></button>
                            <div class="loading">Loading</div>
                            <div class="error-message"></div>
                            <div class="sent-message">Your subscription request has been sent. Thank you!</div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end container -->
</section>
<!-- end newsletter-section -->
<!-- start site-footer -->
<footer class="site-footer">
    <div class="upper-footer">
        <div class="container">
            <div class="row">
                <div class="col col-lg-4 col-md-3 col-sm-6">
                    <div class="widget about-widget">
                        <div class="logo widget-title">
                            <img src="ControlPanel/assets/logo/logo.png" alt>
                        </div>
                        <p>It showed a lady fitted out with a fur hat and fur boa who sat upright, raising a
                            heavy fur muff that covered the whole of her lower arm towards the viewer.</p>
                        <div class="social-icons">
                            <ul>
                                <li><a href="#"><i class="ti-facebook"></i></a></li>
                                <li><a href="#"><i class="ti-twitter-alt"></i></a></li>
                                <li><a href="#"><i class="ti-linkedin"></i></a></li>
                                <li><a href="#"><i class="ti-pinterest"></i></a></li>
                                <li><a href="#"><i class="ti-vimeo-alt"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col col-lg-2 col-md-3 col-sm-6">
                    <div class="widget link-widget">
                        <div class="widget-title">
                            <h3>Navigation</h3>
                        </div>
                        <ul>
                            <li><a href="about">About us</a></li>
                            <li><a href="contact">Contact us</a></li>
                            <li><a href="#">Our Services</a></li>
                            <li><a href="#">Privacy Policy </a></li>
                            <li><a href="#">Recent Projects</a></li>
                            <li><a href="#">Washing history</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col col-lg-3 col-md-3 col-sm-6">
                    <div class="widget link-widget service-link-widget">
                        <div class="widget-title">
                            <h3>Our Services</h3>
                        </div>
                        <ul>
                            <li><a href="#">Petroleum and Gas</a></li>
                            <li><a href="#">Pharmaceutical Research</a></li>
                            <li><a href="#">Agricultural Processing</a></li>
                            <li><a href="#">Oil And Lubricant</a></li>
                            <li><a href="#">Material Engineering</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col col-lg-3 col-md-3 col-sm-6">
                    <div class="widget contact-widget">
                        <div class="widget-title">
                            <h3>Contact Info</h3>
                        </div>
                        <ul>
                            <li>House no, 2005 dreem city insprine urrine London.</li>
                            <li><span>Email: </span> regulation@info.com</li>
                            <li><span>Phone: </span> 325456.1245</li>
                            <li><span>Fax: </span> 6548651231</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> <!-- end container -->
    </div>
    <div class="lower-footer">
        <div class="container">
            <div class="row">
                <div class="separator"></div>
                <div class="col col-xs-12">
                    <p class="copyright">SMC &copy; <?= date('Y') ?></p>
                    <ul>
                        <li><a href="about">About Us</a></li>
                        <li><a href="news">News</a></li>
                        <li><a href="contact">Contact</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- end site-footer -->
</div>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

<script src="assets/js/jquery-plugin-collection.js"></script>
<script src="assets/js/aos/aos.js"></script>
<script src="assets/js/glightbox/js/glightbox.min.js"></script>
<script src="assets/js/script.js"></script>
<script src="ControlPanel/ajax/js/php-mail-form.js"></script>
</body>

</html>