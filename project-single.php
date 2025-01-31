<?php
require 'inc/header.php';
if (isset($_GET['project'])) {
    $code = $_GET['project'];
    $project = $clintObjects->getSingleProject($code);
}
else {
    header('Location: project');
}
?>


<!-- start page-title -->
<section class="page-title">
    <div class="container">
        <div class="row">
            <div class="col col-xs-12">
                <h2>Project <?php echo $project['title']; ?></h2>
                <ol class="breadcrumb">
                    <li><a href="../IMC/">Home</a></li>
                    <li>Project <?php echo $project['title']; ?></li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div> <!-- end container -->
</section>
<!-- end page-title -->


<!-- start project-single-section -->
<section class="project-single-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col col-md-8">
                <div class="project-pic">
                    <img src="ControlPanel/upload/project/<?php echo $project['file']; ?>" alt>
                </div>
            </div>
            <div class="col col-md-4">
                <div class="project-info">
                    <h3><?php echo $project['title']; ?></h3>
                    <ul>
                        <li><span>Owner: </span> IMC</li>
                        <li><span>Status: </span> Completed</li>
                        <li><span>Tags: </span> Geological</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row content">
            <div class="col col-xs-12">
                <div class="overview">
                    <h2>Project overview</h2>
                    <p><?php echo htmlspecialchars_decode($project['content']); ?></p>
                </div>
            </div>
        </div>
    </div> <!-- end container -->
</section>
<!-- end project-single-section -->




<?php
require 'inc/footer.php';
?>