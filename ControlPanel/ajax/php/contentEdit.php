<?php
require '../../config.php';

class contentEdit extends Database {

    private $error;
    public function defineContent($data){
        if ($data['category'] == "proj") {
            $table = "project";
        }
        else {
            $table = "news";
        }

        unset($data['category']);
        $this->editContent($data, $table);
    }

    public function editContent($data, $table){
        try {
            $sanitizedData = array_map('htmlspecialchars', $data);
    
            if (count(array_filter($sanitizedData)) !== count($sanitizedData)) {
                throw new Exception("All input fields are required");
            }
            $this->con->beginTransaction();
            $columns = implode(",", array_keys($sanitizedData));
            $placeholders = implode(",", array_fill(0, count($sanitizedData), "?"));
            $values = array_values($sanitizedData);
    
            if (!empty($columns) && !empty($values)) {
                
                $placeholders = array();
                $values = array();
                
                foreach($sanitizedData as $key => $value) {
                    if($key !== 'code') { 
                        $placeholders[] = "$key = ?";
                        $values[] = $value;
                     }
                }
                $updateQuery = "UPDATE $table SET " . implode(", ", $placeholders) . " WHERE code = ?";
                $dateValue = $sanitizedData['code'];
                
                $values[] = $dateValue;
                
                $querystatement = $this->con->prepare($updateQuery);
                $querystatement->execute($values);
                
                if($querystatement){
                    $this->con->commit();
                }
                else {
                    $this->con->rollBack();
                }
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function getError()
    {
        return $this->error;
    }
}

$editing = new contentEdit();
if (isset($_POST['edit'])) {
    try {
        unset($_POST['edit']);
        $editing->defineContent($_POST);

        if ($editing->getError()) {
            echo "<div class='alert alert-danger'>{$editing->getError()}</div>";
        } else {
            echo "<div class='alert alert-success'>Content modification successful !</div>";
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
    }
}
?>