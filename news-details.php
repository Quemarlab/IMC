<?php
require 'inc/header.php';
if (isset($_GET['article'])) {
    $code = $_GET['article'];
    $news = $clintObjects->getSingleNews($code);
}
else {
    header('Location: news');
}
?>



<!-- start page-title -->
<section class="page-title">
    <div class="container">
        <div class="row">
            <div class="col col-xs-12">
                <h2><?php echo $news['title']; ?></h2>
                <ol class="breadcrumb">
                    <li><a href="../Serge/">Home</a></li>
                    <li><?php echo $news['title']; ?></li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div> <!-- end container -->
</section>
<!-- end page-title -->





        <!-- start blog-single-section -->
        <section class="blog-single-section blog-fullwidth-single-section section-padding">
            <div class="container">
                <div class="row">
                    <div class="col col-xs-12">
                        <div class="blog-content">
                            <div class="post">
                                <div class="entry-media">
                                    <img src="ControlPanel/upload/news/<?php echo $news['file']; ?>" style="width: 100%; height: 400px" alt>
                                </div>
                                <h2><?php echo $news['title']; ?></h2>
                                <ul class="meta">
                                    <li>By: <a href="#">SMC - Media</a></li>
                                    <li><p><?php echo $news['created_at']; ?></p></li>
                                </ul>
                                <div class="entry-details">
                                <?php echo htmlspecialchars_decode($news['content']); ?>
                                </div>
                            </div>
                            <section class="team-section section-padding">
    <div class="container">
        <div class="row">
            <?php
            $gallery = $clintObjects->getSingleNewsImages($news['code']);
            $count = count($gallery);
            ?>
            <div class="col col-xs-12">
                <div class="team-grids clearfix slider" reverse="true" style="
            --width: 350px;
            --height: 300px;
            --quantity: <?php echo $count; ?>; ">
                <div class="grid list">
                <?php
                    $gallery = $clintObjects->getSingleNewsImages($news['code']);
                    $i = 1; 
                    if (!empty($gallery)) {
                        foreach ($gallery as $gal) {
                            ?>
                    
                        <div class="img-holder item" style="--position: <?php echo $i; ?>">
                            <img src="ControlPanel/upload/news/<?php echo $gal['file'] ?>" alt>
                        </div>
                    
                    <?php
                        $i++;
                        }
                    }
                    else {
                        echo "<div class='alert alert-warning'>No Gallery Found</div>";
                    }
                    ?>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- end container -->
</section>

                            <div class="tag-share">
                                <div class="tag">
                                    <i class="ti-folder"></i>
                                    <ul>
                                        <li><a href="#">SMC</a></li>
                                        <li><a href="#">Geological news</a></li>
                                    </ul>
                                </div>
                                <div class="share">
                                    <i class="ti-sharethis"></i>
                                    <ul>
                                        <li><a href="#"><i class="ti-facebook"></i></a></li>
                                        <li><a href="#"><i class="ti-twitter-alt"></i></a></li>
                                        <li><a href="#"><i class="ti-linkedin"></i></a></li>
                                        <li><a href="#"><i class="ti-pinterest"></i></a></li>
                                    </ul>
                                </div>
                            </div><!-- end tag-share -->

        <!-- start team-section -->

                        </div>
                    </div>
                </div>
            </div> <!-- end container -->
        </section>
        <!-- end blogblog-single-section-section -->




<?php
require 'inc/footer.php';
?>