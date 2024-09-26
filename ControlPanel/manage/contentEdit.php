<?php
include 'inc/header.php';
include 'inc/sidebar.php';

class contentEdit extends Database{
    private $error;
    private $data;
    public function detContent($content){
        try {
            $type = substr($content, 0, 4);
            $code = substr($content, 4);
            if ($type == 'news') {
                $this->data = $this->getContent('news', $code);
            }
            elseif ($type == 'proj'){
                $this->data = $this->getContent('project', $code);
            }
            else {
                throw new Exception("The project is not defined", 1);
                
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function getContent ($table, $code){
        try {
            $this->con->beginTransaction();
            $query = "SELECT * FROM $table WHERE code = '$code'";
            $stmt = $this->con->prepare($query);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else {
                $row = [];
            }

            $this->con->commit();
            return $row;

        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->con->rollBack();
        }
    }
    public function getError()
    {
        return $this->error;
    }

    public function getData()
    {
        return $this->data;
    }
}

function backLink ($url) {
    if ($url == "news") {
        $hyperlink = "news";
    }
    elseif($url == "proj"){
        $hyperlink = "project";
    }

    return $hyperlink;
}

$editObject = new contentEdit();

if (isset($_POST['edit'])) {
   $request = substr($_POST['edit'], 0, 4);
   $code = substr($_POST['edit'], 4);
   $hyperlink = backLink($request);
   $editObject->detContent($_POST['edit']);
   if ($editObject->getError()) {
    echo "<div class='alert alert-danger'>{$editObject->getError()}</div>";
   }
}
else {
    header("location: dashboard");
}
?>
<div class="main-content">
    <section class="section"> 
        <div class="section-header">
            <h1>Content Editing</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                    <div class="card-header">
                    <h4>Content [<?php echo $editObject->getData()['title']; ?>]</h4>
                    <div class="card-header-action">
                        <a href="<?php echo $hyperlink ?>"><button class="btn btn-danger"><i class="fas fa-arrow-left"></i> Back</button></a>
                    </div>
                    </div>
                        <div class="card-body">
                        <div class="error-text" style="margin-top: 5px; display: none"></div>
                            <form action="" enctype="multipart/form-data" class="contentEdit">
                                <div class="row">
                                    <div class="form-group col-md-8">
                                        <label class="col-form-label text-md-right col-12 col-md-6 col-lg-6">Title</label>
                                        <div class="col-sm-12 col-md-12">
                                            <input type="text" name="title" class="form-control" value="<?php echo $editObject->getData()['title']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="col-form-label text-md-right col-12 col-md-6 col-lg-6">Category</label>
                                        <div class="col-sm-12 col-md-12">
                                            <input type="text" name="category" class="form-control" value="<?php echo $request; ?>">
                                            <input type="hidden" name="code" value="<?php echo $code; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-6 col-lg-6">Content</label>
                                    <div class="col-sm-12 col-md-12">
                                        <textarea class="summernote" name="content"><?php echo $editObject->getData()['content']; ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <div class="col-sm-12 col-md-7">
                                        <input type="hidden" name="edit">
                                        <button class="btn btn-primary button"><span class="spinner-grow spinner-grow-sm loader-icon" style="display: none;"></span>Edit</button>
                                    </div>
                                </div>
                            </form>
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
<script src="../ajax/js/contentEdit.js"></script>