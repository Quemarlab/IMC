<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../mailerService/phpmailer/src/Exception.php';
require '../../mailerService/phpmailer/src/PHPMailer.php';
require '../../mailerService/phpmailer/src/SMTP.php';
require '../../config.php';

class Profile extends Database {

    private $error;
    public function saveUser($data) {
        try {
            $sanitizedData = array_map('htmlspecialchars', $data);
            if (count(array_filter($data)) !== count($data)) {
                throw new Exception("All Input field are required", 1);
            }
            else {
                $this->con->beginTransaction();
                $columns = implode(",", array_keys($sanitizedData));
                $placeholders = implode(",", array_fill(0, count($sanitizedData), "?"));
                $values = array_values($sanitizedData);

                if (!empty($columns) && !empty($values)) {
               
                    $placeholders = array();
                    $values = array();
                    
                    foreach($sanitizedData as $key => $value) {
                        if($key !== 'account_id') { 
                            $placeholders[] = "$key = ?";
                            $values[] = $value;
                         }
                     }
                     
                     $updateQuery = "UPDATE account_holders SET " . implode(", ", $placeholders) . " WHERE account_id = ?";
                     $account_id = $sanitizedData['account_id'];
                     
                     $values[] = $account_id;
                     
                     $querystatement = $this->con->prepare($updateQuery);
                     $querystatement->execute($values);
                     
                     if($querystatement){
                        $this->sendEmail($sanitizedData['account_id']);
                        $this->con->commit();
                     } else {
                        $this->con->rollback();
                        throw new Exception("Something's wrong, Try again later", 1);
                         
                     }
                }

            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function savePass($data) {
        try {
            $sanitizedData = array_map('htmlspecialchars', $data);
            if (count(array_filter($data)) !== count($data)) {
                throw new Exception("All Input field are required", 1);
            }
            else {
                $this->con->beginTransaction();
                $query = "SELECT * FROM account_holders WHERE account_id = ?";
                $account_id = $sanitizedData['account_id'];
                $querystatement = $this->con->prepare($query);
                $querystatement->execute(array($account_id));

                if($querystatement->rowCount() > 0){
                    $row = $querystatement->fetch(PDO::FETCH_ASSOC);
                    if (password_verify($sanitizedData['current'], $row['password'])) {
                        if ($sanitizedData['password'] !== $sanitizedData['confirm']) {
                            throw new Exception("Password and Confirm Password must be the same", 1);
                        }
                        else {
                            $updateQuery = "UPDATE account_holders SET password = ? WHERE account_id = ?";
                            $password = password_hash($sanitizedData['password'], PASSWORD_DEFAULT);
                            $values[] = $password;
                            $values[] = $sanitizedData['account_id'];
                            $query = $this->con->prepare($updateQuery);
                            $query->execute($values);

                            if ($query) {
                                $this->sendEmail($sanitizedData['account_id']);
                                $this->con->commit();
                            }
                            else {
                                $this->con->rollback();
                            }
                        }
                    }
                    else {
                        throw new Exception("Password is incorrect", 1);
                    }
                }
                else {
                    throw new Exception("Account ID does not exist", 1);
                }
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function userInformation($account_id) {
        try {
            $query = "SELECT * FROM account_holders WHERE account_id = ?";
            $querystatement = $this->con->prepare($query);
            $querystatement->execute(array($account_id));
            if($querystatement->rowCount() > 0) {
                $row = $querystatement->fetch(PDO::FETCH_ASSOC);
            }
            else {
                $row = [];
                throw new Exception("Account ID does not exist", 1);
            }

            return $row;
        }
        catch(Exception $e){
            $this->error = $e->getMessage();
        }

    }

    public function sendEmail($account_id){
        $user = $this->userInformation($account_id);
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $this->userEmail;
            $mail->Password = $this->userPassword;
            $mail->SMTPSecure = 'ssl';
	        $mail->Port = '465';


            $mail->setFrom($this->userEmail, 'IMC - Security Support');
            $mail->addAddress($user['email'], ''.$user['firstname'].' '.$user['lastname'].'');
            $mail->isHTML(true);
            $mail->Subject = "Successful changed";
	        $mail->Body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Email Template</title>
            <style>
                /* Reset styles */
                body, h1, p {
                    margin: 0;
                    padding: 0;
                }
            
                /* Body styles */
                body {
                    font-family: Verdana, sans-serif;
                    background-color: #f4f4f4;
                    padding: 20px;
                }
            
                /* Container styles */
                .container {
                    width: 600px;
                    margin: 0 auto; /* Center the container */
                    background-color: #dcf7da;
                    border-radius: 10px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Add box-shadow */
                }
            
                /* Header styles */
                .header {
                    background-color: #35b051;
                    color: #fff;
                    text-align: center;
                    padding: 10px;
                    border-top-left-radius: 10px;
                    border-top-right-radius: 10px;
                }
            
                /* Content styles */
                .content {
                    padding: 20px;
                }
                p {
                    margin-top: 20px;
                    color: black;
                }
                .link {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin: 3rem 5rem;
                }
                .link a{
                    padding: 10px 5rem;
                    border-radius: 5px;
                    background-color: #35b051;
                    color: #fff;
                    box-shadow: 0 0 3pt 1pt rgba(0,0,0,.2);
                    text-decoration: none;
                    font-size: 20px;
                }
            
                /* Footer styles */
                .footer {
                    background-color: #35b051;
                    color: #fff;
                    text-align: center;
                    padding: 10px;
                    border-bottom-left-radius: 10px;
                    border-bottom-right-radius: 10px;
                }
            </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>IMC</h1>
                    </div>
                    <div class="content">
                        <h1>Changed the credential</h1>
                        <p>Subject: Your User information are successful modified<br><br>

                        Dear '.$user['firstname'].' '.$user['lastname'].',<br>

                        You have successfully modified your user profile. Your credential are well updated. You can now log in to your account using this new password.<br><br>

                        If you have any trouble logging in, please contact our support team at support@imc.com <br><br>

                        Best regards,<br>
                        [IMC - Security support] </p>

                        </div>
                    <div class="footer">
                        <p>&copy; '.date('Y').' IMC. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
            ';
            $mail->send();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function getError(){
        return $this->error;
    }
}

$profileObject = new Profile();

if (isset($_GET['role']) && isset($_GET['role']) == "saveUser") {
    $profileObject->saveUser($_POST);

    if ($profileObject->getError()) {
        echo "<div class='alert alert-danger'><i class='fa-solid fa-circle-exclamation'></i> {$profileObject->getError()}</div>";
    }
    else {
        echo "<div class='alert alert-success'><i class='fa-solid fa-envelope-circle-check'></i> Cofnrimation email is already sent !</div>";
    }
}

if (isset($_GET['action']) && isset($_GET['action']) == "savePass") {
    $profileObject->savePass($_POST);

    if ($profileObject->getError()) {
        echo "<div class='alert alert-danger'><i class='fa-solid fa-circle-exclamation'></i> {$profileObject->getError()}</div>";
    }
    else {
        echo "<div class='alert alert-success'><i class='fa-solid fa-envelope-circle-check'></i> Confirmation email is already sent !</div>";
    }
}