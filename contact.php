<?php
require_once 'Db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Explicitly load the .env file
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect the data from the form fields
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // --- Database Insertion ---
    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
    
    // Bind parameters (s = string)
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    // Execute the statement
    if ($stmt->execute()) {
        // --- PHPMailer Configuration ---
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.example.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USER'] ?? '';
            $mail->Password   = $_ENV['SMTP_PASS'] ?? '';
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'] ?? 'tls';
            $mail->Port       = $_ENV['SMTP_PORT'] ?? 587;

            // Recipients
            $mail->setFrom('your-email@example.com', 'Portfolio Contact');
            $mail->addAddress('baconhairrulesfornow@gmail.com'); // Your real inbox
            $mail->addReplyTo($email, $name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = "<h3>New Message from $name</h3>
                            <p><b>Email:</b> $email</p>
                            <p><b>Message:</b><br>$message</p>";

            $mail->send();
            
            // Redirect
            header("refresh:3;url=index.html");
            echo "Thank you! Your message has been saved and sent. You will be redirected back to the portfolio in 3 seconds...";
        } catch (Exception $e) {
            echo "Your message has been saved to the database, but there was an issue sending the email. <br>Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Sorry, something went wrong while saving your message. <a href='index.html'>Click here to go back.</a>";
    }

    $stmt->close();
    $conn->close();
}
?>