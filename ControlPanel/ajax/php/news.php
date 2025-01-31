<?php

require '../../config.php';

class Project extends Database
{
    private $uploadDir = '../../upload/news/';
    private $error;

    public function saveNews($data, $image)
    {
        try {
            $this->con->beginTransaction();
            if (empty($image['file']['name'])) {
                throw new Exception("No files uploaded.");
            }
            $data = array_map('htmlspecialchars', $data);

            if (count(array_filter($data)) !== count($data)) {
                throw new Exception("All input fields are required");
            }

            $this->saveNewsAndFiles($data, $image);

            $this->con->commit();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->con->rollBack();
            throw $e;
        }
    }

    private function saveNewsAndFiles($data, $image)
   {
       $data['code'] = "NW". rand(1, 9999);
       $columns = implode(",", array_keys($data));
       $placeholders = implode(",", array_fill(0, count($data), "?"));
       $values = array_values($data);

       $query = "INSERT INTO news ($columns) VALUES ($placeholders)";
       $query = $this->con->prepare($query);
       $query->execute($values);

       $code = $data['code'];

       foreach ($image as $category => $files) {
           foreach ($files['tmp_name'] as $key => $tmp_name) {
               if (!isset($tmp_name) || $files['error'][$key] != 0) {
                   throw new Exception("File is missing or upload failed");
               }

               $fileCode = "FILE". rand(1, 9999);
               $fileSize = $files['size'][$key];

               if ($fileSize > 10 * 1024 * 1024) {
                   throw new Exception("File size exceeds the limit (10MB).");
               }

               $targetFileName = $fileCode. "_". $files['name'][$key];

               $targetFile = $this->uploadDir. $targetFileName;

               if (!move_uploaded_file($tmp_name, $targetFile)) {
                   throw new Exception("No file found to be uploaded");
               }

               $query = "INSERT INTO file(file_code, file) VALUES (?,?)";
               $query = $this->con->prepare($query);
               $query->execute([$code, $targetFileName]);
           }
       }
   }

    public function getError()
    {
        return $this->error;
    }

    public function getNews()
    {
        try {
            $query = "SELECT news.*, (SELECT file.file FROM file WHERE file.file_code = news.code ORDER BY file.id DESC LIMIT 1 ) AS latest_file FROM news ORDER BY news.id DESC;";
            $query = $this->con->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {
                $count = 1;
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="col-12 col-md-4 col-lg-4">
                            <article class="article article-style-c border">
                                <div class="article-header">
                                    <div class="article-image">
                                        <img src="../upload/news/' . $row["latest_file"] . '" alt="Image" style="width: 100%; height: 100%;">
                                    </div>
                                </div>
                                <div class="article-details">
                                    <div class="article-category"><a href="#">News ' . $count . '</a>
                                        <div class="bullet"></div> <a href="#">' . $row['created_at'] . '</a>
                                    </div>
                                    <div class="article-title">
                                        <h2><a href="#">' . $row['title'] . '</a></h2>
                                    </div>
                                    <div class="article-footer">
                                        <form method="POST" action="contentEdit" class="d-inline"><button type="submit" name="edit" value="news' . $row['code'] . '" class="btn btn-warning edit">Edit</button></form>
                                        <button type="button" name="delete" value="'. $row['code']. '" class="btn btn-danger delete">Delete</button>
                                    </div>
                                </div>
                            </article>
                        </div>';
                    $count++;
                }
            } else {
                echo "<div class='alert alert-danger'>No record found</div>";
            }
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
        }
    }

    public function deleteNews($code)
    {
        try {
            $query = $this->con->prepare("SELECT * FROM news WHERE code = ?");
            $query->execute([$code]);
            if (!$query->rowCount()) {
                throw new Exception("News does not exist");
            }

            $query = $this->con->prepare("SELECT file FROM file WHERE file_code = ?");
            $query->execute([$code]);
            while ($file = $query->fetch(PDO::FETCH_ASSOC)) {
                unlink($this->uploadDir . $file['file']);
            }

            $query = $this->con->prepare("DELETE FROM news WHERE code = ?");
            $query->execute([$code]);

            $query = $this->con->prepare("DELETE FROM file WHERE file_code = ?");
            $query->execute([$code]);

            echo "<div class='alert alert-success'>News deleted successfully!</div>";
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
        }
    }
}

$project = new Project();
if (isset($_POST['publish'])) {
    try {
        unset($_POST['publish']);
        $project->saveNews($_POST, $_FILES);

        if ($project->getError()) {
            echo "<div class='alert alert-danger'>{$project->getError()}</div>";
        } else {
            echo "<div class='alert alert-success'>News created successfully!</div>";
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
    }
}

if (isset($_POST['action']) == "getNews") {
    try {
        $project->getNews();
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
    }
}

if (isset($_POST['role']) == "delete") {
    try {
        $project->deleteNews($_POST['code']);
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
    }
}
