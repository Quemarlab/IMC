<?php
include 'inc/header.php';
include 'inc/sidebar.php';
?>
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Gallery</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Gallery Tab</h4>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-pills" id="myTab3" role="tablist">
                                <li class="nav-item"><a class="nav-link active" id="Gallery-tab3" data-toggle="tab" href="#Gallery" role="tab" aria-controls="Gallery" aria-selected="true">Gallery</a></li>
                                <li class="nav-item"><a class="nav-link" id="create-tab3" data-toggle="tab" href="#create" role="tab" aria-controls="Create" aria-selected="false">Create +</a></li>
                            </ul>
                            <div class="tab-content" id="myTabContent2">
                                <div class="tab-pane fade show active" id="Gallery" role="tabpanel" aria-labelledby="home-tab3">
                                    <div class="row databox"></div>
                                </div>
                                <div class="tab-pane fade" id="create" role="tabpanel" aria-labelledby="profile-tab3">
                                    <div class="error-text" style="margin-top: 5px; display: none"></div>
                                    <div class="card-body">
                                        <form action="" class="dropzone" id="mydropzone" enctype="multipart/form-data"></form>
                                    </div>
                                </div>
                            </div>
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
<script src="../ajax/js/gallery.js"></script>