<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require '../config.php';

class Email extends Database{
    public function sendEmail($email) {
        try {
            $mail = new PHPMailer(true);

	        $mail->isSMTP();
	        $mail->Host = 'smtp.gmail.com';
	        $mail->SMTPAuth = true;
	        $mail->Username = $this->userEmail;
	        $mail->Password = $this->userPassword;
	        $mail->SMTPSecure = 'ssl';
	        $mail->Port = '465';

	        $mail->setFrom($this->userEmail);
	        $mail->addAddress($email);
	        $mail->isHTML(true);

	        $mail->Subject = "Confirmation Subcription";
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
                        <h1>Interafrican Mining Corporation (IMC)</h1>
                    </div>
                    <div class="content">
                        <p>
                        Subject: Confirmation Subscription
                        
                        <p>Thank you for subscribing to our newsletter at IMC - Support ! We are thrilled to welcome you to our community of mining enthusiasts and industry followers. By subscribing to our newsletter, you have taken the first step towards staying informed about the latest developments, insights, and updates in the mining sector. Your interest in our company motivates us to continue providing valuable content and resources to keep you informed and engaged.</p><br><br>
                        
                        <p>At IMC - Support , we are dedicated to delivering top-quality information and resources to our subscribers. Your decision to subscribe to our newsletter showcases your commitment to staying informed and connected within the mining industry. We look forward to providing you with regular updates, exclusive content, and exciting news to enhance your knowledge and deepen your passion for mining. Thank you once again for choosing to be part of IMC - Support  newsletter community.</p><br><br>
                        
                        Warm regards,<br><br>
                        
                        <b>Mr, In-charge of IMC</b><br>
                        IMC - Support </p>

                    </div>
                    <div class="footer">
                        <p>&copy; '.date('Y').' IMC. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
            ';
            
	        if ($mail->send()) {
                $code = "NL". rand(1, 9999);
                $query = "INSERT INTO newslater(code, email) VALUES (?, ?)";
                $query = $this->con->prepare($query);
                $query->execute([$code, $email]);
                if ($query) {
                    echo "OK";
                }
                else {
                    throw new Exception("Something's wrong while saving to database", 1);
                    
                }
                
            }

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}




if ($_SERVER['REQUEST_METHOD'] == "POST") {
    try {
        if (!empty($_POST['email'])) {
            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $email = $_POST['email'];
                $sendMail = new Email();
                $sendMail->sendEmail($email);
            }
            else {
                throw new Exception("Invalid email", 1);
                
            }
        }
        else {
            throw new Exception("Email is required", 1);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    

    
}
?>