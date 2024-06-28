<?php
require 'inc/header.php';
?>

<!-- start page-title -->
<section class="page-title">
    <div class="container">
        <div class="row">
            <div class="col col-xs-12">
                <h2>Projects</h2>
                <ol class="breadcrumb">
                    <li><a href="../Serge/">Home</a></li>
                    <li>Projects</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div> <!-- end container -->
</section>
<!-- end page-title -->


<!-- start projects-pg-section-s2 -->
<section class="projects-pg-section-s2 section-padding">
    <div class="container">
        <div class="row">
            <div class="col col-xs-12">
                <div class="projects-grids clearfix">
                <?php
                    $projects = $clintObjects->getProject();
                    if (!empty($projects)) {
                        foreach ($projects as $project) {
                            ?>
                            <div class="grid">
                                <div class="project-pic">
                                    <img src="ControlPanel/upload/project/<?php echo $project['file'] ?>" style="height: 200px; " alt>
                                </div>
                                <div class="details">
                                    <h3><a href="project-single?project=<?php echo $project['code'] ?>"><?php echo $project['title'] ?></a></h3>
                                    <span><?php echo $project['date'] ?></span>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    else {
                        echo "<div class='alert alert-warning'>No Projects Found</div>";
                    }
                    ?>
                    
                </div>
            </div>
        </div>
    </div> <!-- end container -->
</section>
<!-- end projects-pg-section-s2 -->



<?php
require 'inc/footer.php';
?>