<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../mailerService/phpmailer/src/Exception.php';
require '../../mailerService/phpmailer/src/PHPMailer.php';
require '../../mailerService/phpmailer/src/SMTP.php';
require '../../config.php';

class forgotPassword extends Database{

    private $error;
    public function creatingToken(){
        $token = bin2hex(random_bytes(16));
        return $token;
    }

    public function emailValidate ($email){
        try {
            if(!empty($email)){
                if (filter_var($email, FILTER_VALIDATE_EMAIL)){
                    return $email;
                }
                else {
                    throw new Exception("Email Address in not valid", 1);
                }
            }
            else {
                throw new Exception("Email Address is required", 1);
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function updateUserToken($email){
        try {
            $token = $this->creatingToken();
            $generated_time = date('h:i:s d-m-Y');
            $sql = "UPDATE account_holders SET token = ?, generated_time = ? WHERE email = ?";
            $sql = $this->con->prepare($sql);
            $sql->execute([$token, $generated_time, $email]);

            if ($sql) {
                $this->creatingLink($email, $token, $generated_time);
            }
            else {
                throw new Exception("Something's wrong with updating", 1);
            }
        } catch (Exception $e) {
            $this->error =  $e->getMessage();
        }
    }

    public function creatingLink($email, $token, $generated_time){
        $link = "http://localhost/IMC/ControlPanel/authenticate/reset-password?token=$token&time=$generated_time";

        $this->sendEmail($email, $link);
    }
    public function sendEmail($email, $link){
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
                        <h1>Forgot Password</h1>
                        <div class="link">
                            <a href="'.$link.'">Click here</a>
                        </div>

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
    public function gettingUser($email){
        try {
            $email = $this->emailValidate($email);
            $query = "SELECT * FROM account_holders WHERE email = '$email'";
            $query = $this->con->prepare($query);
            $query->execute();

           if ($query->rowCount() > 0) {
               $this->updateUserToken($email);
           }
           else {
               throw new Exception("The email is not found", 1);
           }
        } catch (Exception $e) {
            $this->error =  $e->getMessage();
        }
    }

    public function getError()
    {
        return $this->error;
    }
}

$functionObject = new forgotPassword();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $functionObject->gettingUser($email);

    if ($functionObject->getError()) {
        echo "<div class='alert alert-danger'><i class='fa-solid fa-circle-exclamation'></i> {$functionObject->getError()}</div>";
    }
    else {
        echo "<div class='alert alert-success'><i class='fa-solid fa-envelope-circle-check'></i> Confirmation email is already sent !</div>";
    }
}

?>
