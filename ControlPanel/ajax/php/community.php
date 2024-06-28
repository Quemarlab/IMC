<?php

require '../../config.php';

class Community extends Database {
    public function communityTable() {
        try {
            $query = "SELECT * FROM newslater";
            $query = $this->con->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $result[] = $row;
                }
            }
            else {
                $result = [];
                throw new Exception("No record(s) found", 1);    
            }

            return $result;
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
        }
    }

    public function contactTable() {
        try {
            $query = "SELECT * FROM contact";
            $query = $this->con->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $result[] = $row;
                }
            }
            else {
                $result = [];
                throw new Exception("No record(s) found", 1);    
            }

            return $result;
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
        }
    }
}

$data = new Community();


if(isset($_POST['action'])) {
    if($_POST['action'] == 'communityTable') {
        $new_data = $data->communityTable();
        echo json_encode($new_data);
    }
}

if(isset($_POST['action'])) {
    if($_POST['action'] == 'contactTable') {
        $new_data = $data->contactTable();
        echo json_encode($new_data);
    }
}
?>