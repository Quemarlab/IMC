<?php
require 'inc/header.php';
?>

<!-- start page-title -->
<section class="page-title">
    <div class="container">
        <div class="row">
            <div class="col col-xs-12">
                <h2>Gallery</h2>
                <ol class="breadcrumb">
                    <li><a href="../Serge/">Home</a></li>
                    <li>Gallery</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div> <!-- end container -->
</section>
<!-- end page-title -->

<section class="feature-projects section-padding">
    <div class="container">
        <div class="row">
            <div class="col col-lg-10 col-lg-offset-1">
                <div class="section-title-s3">
                    <span>New Uploaded gallery</span>
                    <h2>Featured Gallery</h2>
                    <p>Wasn't a dream. His room, a proper human room although a little too small, lay peacefully between its four familiar walls collection of textile samples lay spread out on the table Samsa was a travelling salesman.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Gallery Section -->
<section id="gallery" class="gallery section">

<div class="container" data-aos="fade-up" data-aos-delay="100">

  <div class="row gy-4 justify-content-center">
  <?php
    $gallery = $clintObjects->getGallery();
    $count = 1;
    if (!empty($gallery)) {
        foreach ($gallery as $gal) {
            ?>
    <div class="col-xl-3 col-lg-4 col-md-6">
      <div class="gallery-item h-100">
        <img src="ControlPanel/upload/gallery/<?php echo $gal['file'] ?>" class="img-fluid" alt="">
        <div class="gallery-links d-flex align-items-center justify-content-center">
          <a href="ControlPanel/upload/gallery/<?php echo $gal['file'] ?>" title="SMC - Gallery <?php echo $count ?>" class="glightbox preview-link"><i class="bi bi-arrows-angle-expand"></i></a>
        </div>
      </div>
    </div>
    <?php
        $count++;
        }
    }
    else {
        echo "<div class='alert alert-warning'>No Gallery Found</div>";
    }
    ?>
                    
  </div>

</div>

</section><!-- /Gallery Section -->
<!-- end team-section -->



<?php
require 'inc/footer.php';
?>