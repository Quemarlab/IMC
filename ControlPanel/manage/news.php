<?php
include 'inc/header.php';
include 'inc/sidebar.php';
?>
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>News</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>News Tab</h4>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-pills" id="myTab3" role="tablist">
                                <li class="nav-item"><a class="nav-link active" id="News-tab3" data-toggle="tab" href="#News" role="tab" aria-controls="News" aria-selected="true">News</a></li>
                                <li class="nav-item"><a class="nav-link" id="create-tab3" data-toggle="tab" href="#create" role="tab" aria-controls="Create" aria-selected="false">Create +</a></li>
                            </ul>
                            <div class="tab-content" id="myTabContent2">
                                <div class="tab-pane fade show active" id="News" role="tabpanel" aria-labelledby="home-tab3">
                                    <div class="row databox"></div>
                                </div>
                                <div class="tab-pane fade" id="create" role="tabpanel" aria-labelledby="profile-tab3">
                                    <div class="error-text" style="margin-top: 5px; display: none"></div>
                                    <form action="" method="POST" enctype="multipart/form-data" class="news border">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Title</label>
                                                    <div class="col-sm-12 col-md-12">
                                                        <input type="text" name="title" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Background</label>
                                                    <div class="col-sm-12 col-md-12">
                                                        <div class="custom-file">
                                                            <input type="file" name="file[]" class="custom-file-input" id="site-logo">
                                                            <label class="custom-file-label">Choose File</label>
                                                        </div>
                                                        <div class="form-text text-muted">The image must have a maximum size of 10MB</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Content</label>
                                                <div class="col-sm-12 col-md-12">
                                                    <textarea class="summernote" name="content"></textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Images</label>
                                                    <div class="col-sm-12 col-md-12">
                                                        <div class="custom-file">
                                                            <input type="file" name="images[]" class="custom-file-input" id="site-logo" multiple>
                                                            <label class="custom-file-label">Choose File</label>
                                                        </div>
                                                        <div class="form-text text-muted">Each image must not execeed 10MBs</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <div class="col-sm-12 col-md-7">
                                                    <input type="hidden" name="publish">
                                                    <button class="btn btn-primary button"><span class="spinner-grow spinner-grow-sm loader-icon" style="display: none;"></span>Publish</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'inc/footer.php'; ?>
<script src="../ajax/js/news.js"></script>