<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect the data from the form fields
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Set up the email details
    $to = "baconhairrulesfornow@gmail.com"; // Replace with your actual email
    $headers = "From: " . $email;

    // The actual content of the email
    $body = "Name: $name\n";
    $body .= "Email: $email\n";
    $body .= "Subject: $subject\n\n";
    $body .= "Message:\n$message";

    // Send the email
    if (mail($to, $subject, $body, $headers)) {
        // Redirect back to index.html after 3 seconds
        header("refresh:3;url=index.html");
        echo "Thank you! Your message has been sent. You will be redirected back to the portfolio in 3 seconds...";
    } else {
        echo "Sorry, something went wrong. <a href='index.html'>Click here to go back.</a>";
    }
}
?>