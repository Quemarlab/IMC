<?php

require '../../config.php';

class Project extends Database
{
    private $uploadDir = '../../upload/gallery/';
    private $error;

    public function saveGallery($files) {
        $galleryCode = sprintf('GA%s', bin2hex(random_bytes(4)));
        $createGallery = "INSERT INTO gallery(code) VALUES (?)";
        $stmt = $this->con->prepare($createGallery);
        $stmt->execute([$galleryCode]);
    
        $fileNames = [];
        if (!empty($files['file']['name'])) {
            $filecode = sprintf('FILE%s', bin2hex(random_bytes(4)));
            $fileName = basename($_FILES['file']['name']); 
            $uploadFilePath = $this->uploadDir.$fileName; 

            if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath)){ 
                $saveImages = $this->con->prepare("INSERT INTO file (file_code, file) VALUES (?, ?)");
                $saveImages->execute([$galleryCode, $fileName]);
            } 
        }
    }

    public function getError()
    {
        return $this->error;
    }

    public function getGallery()
    {
        try {
            $query = "SELECT gallery.*,file.* FROM gallery INNER JOIN file ON gallery.code = file.file_code";
            $query = $this->con->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {
                $count = 1;
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="col-12 col-md-4 col-lg-4">
                            <article class="article article-style-c border">
                                <div class="article-header">
                                    <div class="article-image">
                                        <img src="../upload/gallery/' . $row["file"] . '" alt="Image" style="width: 100%; height: 100%;">
                                    </div>
                                </div>
                                <div class="article-details">
                                    <div class="article-category"><a href="#">Gallery ' . $count . '</a>
                                        <div class="bullet"></div> <a href="#">' . $row['date'] . '</a>
                                    </div>
                                    <div class="article-footer">
                                        <button type="submit" name="delete" value="' . $row['code'] . '" class="btn btn-danger delete" onclick="deleteGallery();">Delete</button>
                                    </div>
                                </div>
                            </article>
                        </div>';
                    $count++;
                }
            } else {
                throw new Exception("There is no records found", 1);
            }
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
        }
    }

    public function deleteGallery($code)
    {
        try {
            $query = $this->con->prepare("SELECT * FROM gallery WHERE code = ?");
            $query->execute([$code]);
            if (!$query->rowCount()) {
                throw new Exception("Gallery does not exist");
            }

            $query = $this->con->prepare("SELECT file FROM file WHERE file_code = ?");
            $query->execute([$code]);
            while ($file = $query->fetch(PDO::FETCH_ASSOC)) {
                unlink($this->uploadDir . $file['file']);
            }

            $query = $this->con->prepare("DELETE FROM gallery WHERE code = ?");
            $query->execute([$code]);

            $query = $this->con->prepare("DELETE FROM file WHERE file_code = ?");
            $query->execute([$code]);

            echo "<div class='alert alert-success'>Gallery deleted successfully!</div>";
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
        }
    }
}

$project = new Project();
if (!empty($_FILES)) {
    try {
        $project->saveGallery($_FILES);

        if ($project->getError()) {
            echo "<div class='alert alert-danger'>{$project->getError()}</div>";
        } else {
            echo "<div class='alert alert-success'>Gallery created successfully!</div>";
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
    }
}

if (isset($_POST['action']) == "getGallery") {
    try {
        $project->getGallery();
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
    }
}

if (isset($_POST['role']) == "delete") {
    try {
        $project->deleteGallery($_POST['code']);
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
    }
}
