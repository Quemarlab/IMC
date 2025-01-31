<?php 
include_once 'inc/header.php'; 
include_once 'inc/sidebar.php';

$dashboard = $userAccess->dashboardCount();
$lastcontact = $userAccess->getlastcontact();
?>




<!-- Start app main Content -->
<div class="main-content">
    <section class="section">
    <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="far fa-user"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Project</h4>
                                </div>
                                <div class="card-body">
                                    <?php echo $dashboard['project'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                                <i class="far fa-newspaper"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>News</h4>
                                </div>
                                <div class="card-body">
                                    <?php echo $dashboard['news'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning">
                                <i class="far fa-file"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Gallery</h4>
                                </div>
                                <div class="card-body">
                                <?php echo $dashboard['gallery'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-success">
                                <i class="fas fa-circle"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Online Users</h4>
                                </div>
                                <div class="card-body">
                                <?php echo $dashboard['users'] ?>
                                </div>
                            </div>
                        </div>
                    </div>                  
                </div>
        <div class="row row-deck">
        </div>
        <div class="row row-deck">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Last 5 News</h4>
                        <div class="card-header-action">
                            <a href="news" class="btn btn-danger">View More <i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive table-invoice">
                            <table class="table table-striped">
                                <tr>
                                    <th>News Code</th>
                                    <th>Title</th>
                                    <th>Due Date</th>
                                </tr>
                                <?php
                                $news = $userAccess->getdashnews();
                                if (!empty($news)) {
                                    foreach($news as $new){
                                        ?>
                                <tr>
                                    <td><?php echo $new['code'] ?></td>
                                    <td class="font-weight-600"><?php echo $new['title'] ?></td>
                                    <td><?php echo $new['created_at'] ?></td>
                                </tr>
                                        <?php
                                    }
                                }
                                else {
                                    echo "<tr><td colspan='3' align='center'>No News Found</td></tr>";
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-hero">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="far fa-question-circle"></i>
                        </div>
                        <h4>1 Customer</h4>
                        <div class="card-description">Last customer contact made</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="tickets-list">
                            <a href="#" class="ticket-item">
                                <div class="ticket-title">
                                    <h4><?php echo $lastcontact['name']?></h4>
                                </div>
                                <div class="ticket-info">
                                    <p><?php echo $lastcontact['note'] ?></p>
                                </div>
                            </a>
                            <a href="community" class="ticket-item ticket-more">
                                View All <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include_once 'inc/footer.php'; ?>