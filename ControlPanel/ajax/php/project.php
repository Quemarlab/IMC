<?php

require '../../config.php';

class Project extends Database
{
    private $uploadDir = '../../upload/project/';
    private $error;

    public function saveProject($data, $image)
    {
        try {
            if (empty($image['file']['name'])) {
                throw new Exception("No files uploaded.");
            }

            $this->con->beginTransaction();
            $data = array_map('htmlspecialchars', $data);

            if (count(array_filter($data)) !== count($data)) {
                throw new Exception("All input fields are required");
            }

            $this->saveProjectAndFiles($data, $image);

            $this->con->commit();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->con->rollBack();
            throw $e;
        }
    }

    private function saveProjectAndFiles($data, $image)
    {
        $data['code'] = "PR" . rand(1, 9999);
        $columns = implode(",", array_keys($data));
        $placeholders = implode(",", array_fill(0, count($data), "?"));
        $values = array_values($data);

        $query = "INSERT INTO project ($columns) VALUES ($placeholders)";
        $query = $this->con->prepare($query);
        $query->execute($values);

        $code = $data['code'];

        foreach ($_FILES['file']['name'] as $key => $fileName) {
            if (!isset($_FILES['file']['tmp_name'][$key])) {
                throw new Exception("File is missing");
            }


            $fileCode = "FILE" . rand(1, 9999);
            $fileSize = $_FILES['file']['size'][$key];

            if ($fileSize > 10 * 1024 * 1024) {
                throw new Exception("File size exceeds the limit (10MB).");
            }

            $tempFile = $_FILES['file']['tmp_name'][$key];
            $targetFileName = $fileCode . "_" . $fileName;

            $targetFile = $this->uploadDir . $targetFileName;

            if (!move_uploaded_file($tempFile, $targetFile)) {
                throw new Exception("No file found to be uploaded");
            }

            $query = $this->con->prepare("INSERT INTO file(file_code, file) VALUES (?, ?)");
            $query->execute([$code, $targetFileName]);
        }
    }

    public function getError()
    {
        return $this->error;
    }

    public function getProject()
    {
        try {
            $query = "SELECT project.*,file.* FROM project INNER JOIN file ON project.code = file.file_code";
            $query = $this->con->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {
                $count = 1;
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="col-12 col-md-4 col-lg-4">
                            <article class="article article-style-c border">
                                <div class="article-header">
                                    <div class="article-image">
                                        <img src="../upload/project/' . $row["file"] . '" alt="Image" style="width: 100%; height: 100%;">
                                    </div>
                                </div>
                                <div class="article-details">
                                    <div class="article-category"><a href="#">Project ' . $count . '</a>
                                        <div class="bullet"></div> <a href="#">' . $row['date'] . '</a>
                                    </div>
                                    <div class="article-title">
                                        <h2><a href="#">' . $row['title'] . '</a></h2>
                                    </div>
                                    <div class="article-footer">
                                        <form method="POST" action="contentEdit" class="d-inline"><button type="submit" name="edit" value="proj' . $row['code'] . '" class="btn btn-warning edit">Edit</button></form>
                                        <button type="submit" name="delete" value="' . $row['code'] . '" class="btn btn-danger delete" onclick="deleteProject();">Delete</button>
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
    public function deleteProject($code)
    {
        try {
            $query = $this->con->prepare("SELECT * FROM project WHERE code = ?");
            $query->execute([$code]);
            if (!$query->rowCount()) {
                throw new Exception("Project does not exist");
            }

            $query = $this->con->prepare("SELECT file FROM file WHERE file_code = ?");
            $query->execute([$code]);
            $file = $query->fetch(PDO::FETCH_ASSOC);
            if ($file) {
                unlink($this->uploadDir . $file['file']);
            }

            $query = $this->con->prepare("DELETE FROM project WHERE code = ?");
            $query->execute([$code]);

            $query = $this->con->prepare("DELETE FROM file WHERE file_code = ?");
            $query->execute([$code]);

            echo "<div class='alert alert-success'>Project deleted successfully!</div>";
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
        }
    }
}

$project = new Project();
if (isset($_POST['publish'])) {
    try {
        unset($_POST['publish']);
        $project->saveProject($_POST, $_FILES);

        if ($project->getError()) {
            echo "<div class='alert alert-danger'>{$project->getError()}</div>";
        } else {
            echo "<div class='alert alert-success'>Project created successfully!</div>";
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
    }
}

if (isset($_POST['action']) == "getProject") {
    try {
        $project->getProject();
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
    }
}


if (isset($_POST['role']) == "delete") {
    try {
        $project->deleteProject($_POST['code']);
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
    }
}
