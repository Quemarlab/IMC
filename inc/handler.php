<?php
require 'ControlPanel/config.php';

class ClientObject extends Database {
    public function getProjectLimit() {
        try {
            $query = "SELECT project.*,file.* FROM project INNER JOIN file ON project.code = file.file_code ORDER BY project.id DESC LIMIT 3";
            $query = $this->con->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }
            }
            else {
                $data = array();
            }
            return $data;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
    }

    public function getProject() {
        try {
            $query = "SELECT project.*,file.* FROM project INNER JOIN file ON project.code = file.file_code ORDER BY project.id DESC";
            $query = $this->con->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }
            }
            else {
                $data = array();
            }
            return $data;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
    }

    public function getSingleProject($code) {
        try {
            $query = "SELECT project.*,file.* FROM project INNER JOIN file ON project.code = file.file_code WHERE project.code = ? ";
            $query = $this->con->prepare($query);
            $query->execute([$code]);

            if ($query->rowCount() > 0) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
            else {
                $data = array();
            }
            return $data;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
    }

    public function getSingleNews($code) {
        try {
            $query = "SELECT news.*,file.* FROM news INNER JOIN file ON news.code = file.file_code WHERE news.code = ? ";
            $query = $this->con->prepare($query);
            $query->execute([$code]);

            if ($query->rowCount() > 0) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
            else {
                $data = array();
            }
            return $data;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
    }


    public function getSingleNewsImages($code) {
        try {
            $query = "SELECT * FROM file WHERE file_code = ?";
            $query = $this->con->prepare($query);
            $query->execute([$code]);

            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }
            }
            else {
                $data = array();
            }
            return $data; 
            } catch (Exception $e) {
                echo $e->getMessage();
            }
    }

    public function getNewsLimit() {
        try {
            $query = "SELECT news.*, (SELECT file.file FROM file WHERE file.file_code = news.code ORDER BY file.id DESC LIMIT 1 ) AS latest_file FROM news ORDER BY news.id DESC LIMIT 3;";
            $query = $this->con->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }
            }
            else {
                $data = array();
            }
            return $data;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
    }

    public function getGallery() {
        try {
            $query = "SELECT gallery.*,file.* FROM gallery INNER JOIN file ON gallery.code = file.file_code";
            $query = $this->con->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }
            }
            else {
                $data = array();
            }
            return $data;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
    }

    public function getNews($datacode) {
        try {
            if (empty($datacode) || $datacode == 'nostr') {
                $query = "SELECT news.*, (SELECT file.file FROM file WHERE file.file_code = news.code ORDER BY file.id DESC LIMIT 1 ) AS latest_file FROM news ORDER BY news.id DESC;";
                $query = $this->con->prepare($query);
                $query->execute();

                if ($query->rowCount() > 0) {
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        $data[] = $row;
                    }
                }
                else {
                    $data = array();
                }
                return $data;
            }
            elseif (!empty($datacode)) {
                $query = "SELECT news.*, (SELECT file.file FROM file WHERE file.file_code = news.code ORDER BY file.id DESC LIMIT 1 ) AS latest_file FROM news WHERE title LIKE '%$datacode%' ORDER BY news.id DESC;";
                $query = $this->con->prepare($query);
                $query->execute();

                if ($query->rowCount() > 0) {
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        $data[] = $row;
                    }
                }
                else {
                    $data = array();
                }
                return $data;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    
}

$clintObjects = new ClientObject();
?>