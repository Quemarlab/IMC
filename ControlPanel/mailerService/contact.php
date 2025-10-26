<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require '../config.php';

class Email extends Database{
    public function sendEmail($data) {
        try {
            $mail = new PHPMailer(true);

	        $mail->isSMTP();
	        $mail->Host = 'smtp.gmail.com';
	        $mail->SMTPAuth = true;
	        $mail->Username = $this->userEmail;
	        $mail->Password = $this->userPassword;
	        $mail->SMTPSecure = 'ssl';
	        $mail->Port = '465';

	        $mail->setFrom($this->userEmail,'IMC - Contact');
	        $mail->addAddress('usabuweraquemar@gmail.com', ''.$data['name'].'');
	        $mail->isHTML(true);

	        $mail->Subject = "Contact Form Submitted";
	        $mail->Body = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Submission</title>
    <style>
        /* Reset styles */
        body, h1, h2, p {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        /* Container styles */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Header styles */
        .email-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: #ffffff;
            text-align: center;
            padding: 30px 20px;
        }

        .email-header img {
            max-width: 150px; /* Adjust the size of the logo */
            height: auto;
            margin-bottom: 15px;
        }

        .email-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .email-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        /* Content styles */
        .email-content {
            padding: 30px 20px;
            color: #333333;
        }

        .email-content h2 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #007bff;
        }

        .email-content p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .email-content .highlight {
            background-color: #f4f4f4;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .email-content .highlight p {
            margin: 0;
        }

        /* Footer styles */
        .email-footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666666;
            border-top: 1px solid #e9ecef;
        }

        .email-footer a {
            color: #007bff;
            text-decoration: none;
        }

        .email-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <img src="http://localhost/imc/controlpanel/assets/logo/logo.png" alt="IMC Logo">
            <h1>Interafrican Mining Corporation (IMC)</h1>
            <p>Contact Form Submission</p>
        </div>

        <!-- Content -->
        <div class="email-content">
            <h2>New Contact Form Submission</h2>
            <div class="highlight">
                <p><strong>From:</strong> ' . $data['name'] . '</p>
                <p><strong>Email:</strong> ' . $data['email'] . '</p>
                <p><strong>Phone:</strong> ' . $data['phone'] . '</p>
                <p><strong>Address:</strong> ' . $data['address'] . '</p>
            </div>
            <p><strong>Message:</strong></p>
            <p>' . $data['note'] . '</p>
            <p>Warm regards,</p>
            <p><strong>Mr/Mrs ' . $data['name'] . '</strong></p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>&copy; ' . date('Y') . ' Interafrican Mining Corporation (IMC). All rights reserved.</p>
            <p><a href="https://www.imc.com" target="_blank">Visit our website</a></p>
        </div>
    </div>
</body>
</html>
';
            
	        if ($mail->send()) {
                $data = array_map('htmlspecialchars', $data);

                if (count(array_filter($data)) !== count($data)) {
                    throw new Exception("All input fields are required");
                }
                else {

                $data['code'] = "CF" . rand(1, 9999);
                $columns = implode(",", array_keys($data));
                $placeholders = implode(",", array_fill(0, count($data), "?"));
                $values = array_values($data);

                $query = "INSERT INTO contact ($columns) VALUES ($placeholders)";
                $query = $this->con->prepare($query);
                $query->execute($values);

                if ($query) {
                    echo "OK";
                }
                else {
                    throw new PDOException("Something's wrong while saving to database", 1);
                    
                }
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
                $sendMail->sendEmail($_POST);
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