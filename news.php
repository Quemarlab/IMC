<?php
require 'inc/header.php';
?>



<!-- start page-title -->
<section class="page-title">
    <div class="container">
        <div class="row">
            <div class="col col-xs-12">
                <h2>News</h2>
                <ol class="breadcrumb">
                    <li><a href="../Serge/">Home</a></li>
                    <li>News</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div> <!-- end container -->
</section>
<!-- end page-title -->


<!-- start blog-pg-section -->
<section class="blog-pg-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col col-md-9">
                <div class="blog-content">
                <?php
                    if (isset($_POST['search'])) {
                        $datacode = $_POST['searchstring'];
                        $datacode = filter_var($datacode, FILTER_SANITIZE_STRING);
                        $news = $clintObjects->getNews($datacode);
                    }
                    else{
                        $datacode = 'nostr';
                        $news = $clintObjects->getNews($datacode);
                    }
                    
                    if (!empty($news)) {
                        foreach ($news as $new) {
                            ?>
                    <div class="post quote-post">
                        <div class="entry-media">
                            <img src="ControlPanel/upload/news/<?php echo $new['latest_file'] ?>" alt>
                        </div>
                        <h3><a href="news-details?article=<?php echo $new['code']; ?>"><?php echo $new['title'] ?></a></h3>
                        <ul class="meta">
                            <li>By: <a href="#">SMC - Media</a></li>
                            <li><p><?php echo $new['created_at'] ?></p></li>
                        </ul>
                        <div class="entry-details">
                            <p class="blogContent"><?php echo $new['content']; ?></p>
                            <a href="news-details?article=<?php echo $new['code']; ?>" class="read-more">Read More -</a>
                        </div>
                        <div class="quote-icon">
                            <i class="fi flaticon-quote-sign"></i>
                        </div>
                    </div>
                    <?php
                        }
                    }
                    else {
                        echo "<div class='alert alert-warning'>No News Found</div>";
                    }
                    ?>
                </div>
            </div>
            <div class="col col-md-3">
                <div class="blog-sidebar">
                    <div class="widget search-widget">
                        <h3>Search Here</h3>
                        <form method="POST">
                            <div>
                                <input type="text" name="searchstring" class="form-control" placeholder="Enter Search Keyword">
                                <button type="submit" name="search"><i class="ti-search"></i></button>
                            </div>   
                        </form>
                    </div>
                    <div class="widget popular-post-widget">
                        <h3>Recent posts</h3>
                        <ul>
                        <?php
                    $newsLimit = $clintObjects->getNewsLimit();
                    if (!empty($newsLimit)) {
                        foreach ($newsLimit as $newLimit) {
                            ?>
                            <li>
                                <div class="post-image">
                                    <a href="news-details?article=<?php echo $newLimit['code']; ?>"><img src="ControlPanel/upload/news/<?php echo $newLimit['latest_file'] ?>" alt></a>
                                </div>
                                <div class="post-info">
                                    <a href="news-details?article=<?php echo $newLimit['code']; ?>"><span class="post-title"><?php echo $newLimit['title']; ?></span></a>
                                </div>
                            </li>
                            <?php
                        }
                    }
                    else {
                        echo "<div class='alert alert-warning'>No recent news Found</div>";
                    }
                    ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end container -->
</section>
<!-- end blog-pg-section -->



<?php
require 'inc/footer.php';
?>