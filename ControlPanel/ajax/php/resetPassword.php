<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../mailerService/phpmailer/src/Exception.php';
require '../../mailerService/phpmailer/src/PHPMailer.php';
require '../../mailerService/phpmailer/src/SMTP.php';
require '../../config.php';

class resetPassword extends Database{
    private $error;
    public function sendEmail($email, $userName){
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
            $mail->addAddress($email, 'Dear User');
            $mail->isHTML(true);
            $mail->Subject = "Forgot Password link";
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
                        <h1>Successful Resetted</h1>
                        <p>Subject: Your Password Has Been Reset<br><br>

                        Dear '.$userName.',<br>

                        You have successfully reset your password. Your new password is now [new_password]. You can now log in to your account updated credentials.<br><br>

                        If you have any trouble logging in or displaying, please contact our support team at support@icm.com <br><br>

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
    public function gettingUser($data) {
        try {
            $token = $_POST['token'];
            $query = "SELECT * FROM account_holders WHERE token = ? ";
            $query = $this->con->prepare($query);
            $query->execute([$token]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $userName = $result['firstname']. $result['lastname'];

            if ($query->rowCount() > 0) {
                if ($_POST['password'] !== $_POST['confirm-password']) {
                    throw new Exception("Password doesn't match", 1);    
                }
                else {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $query = "UPDATE account_holders SET password = ? WHERE token = ? ";
                    $query = $this->con->prepare($query);
                    $query->execute([$password, $token]);
                    if ($query) {
                        $this->sendEmail($result['email'], $userName);
                    }
                    else {
                        throw new Exception("Something went wrong", 1);
                    }
                }
            }
            else {
                throw new Exception("Invalid token !", 1);
                
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

$functionObject = new resetPassword();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $token = $_POST['token'];
    $functionObject->gettingUser($_POST);

    if ($functionObject->getError()) {
        echo "<div class='alert alert-danger'><i class='fa-solid fa-circle-exclamation'></i> {$functionObject->getError()}</div>";
    }
    else {
        echo "<div class='alert alert-success'><i class='fa-solid fa-envelope-circle-check'></i> Confirmation Email is already sent !</div>";
    }
}
?>