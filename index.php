<?php

use function PHPSTORM_META\type;

require_once 'Db.php';
$query = "SELECT id, title, description, url, image_url, is_visible, created_at FROM projects WHERE is_visible = 1 ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$projects = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Portfolio</title>
<style>
    .projectDescription {
         font-family: Arial, Helvetica, sans-serif;
         padding: 10px;
         margin: 10px;
         border: 1px hidden;
         border-radius: 5px;
         
    }
    .highlight {background-color: rgb(213, 242, 250);}
    h1 {font-size:280%;
        font-family: Arial, Helvetica, sans-serif;
    }
    .h2 {font-size:150%; font-family: Verdana, Geneva, Tahoma, sans-serif;}
    .h3 {font-size: 150%;
        font-family: Arial, Helvetica, sans-serif;
    }
    html {
        scroll-behavior: smooth;
    }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
        margin: 0;
        padding: 0;
        background-color: #c0ddf8;
    }
    .header { 
        padding: 30px;
        background: rgba(255, 255, 255, 0.7);
        text-align: center;
        border-radius: 15px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        position: sticky;
        top: 20px;
        z-index: 1000;
        margin: 0 20px;
        max-width: 90%;
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .h1 {
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        font-size: 3.5rem;
        margin: 0;
        color: #1a3a5a; /* A deep navy blue */
        letter-spacing: -1px;
    }
    nav {
        margin-top: 25px;
        padding: 10px;
    }
    nav a {
        margin: 0 15px;
        text-decoration: none;
        color: #007bff;
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        font-weight: 600;
        font-size: 1.1rem;
        transition: color 0.2s ease;
    }
    nav a:hover {
        color: #0056b3;
        text-decoration: none;
        background-color: rgba(0, 123, 255, 0.1);
        padding: 5px 10px;
        border-radius: 5px;
    }
    section {
        max-width: 800px;
        margin: 40px auto;
        padding: 20px;
        scroll-margin-top: 250px; /* This adds space at the top when jumping to a section */
    }
    #about {
        border-radius: 20px;
        background: white;
        padding: 40px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .project-card {
        background: rgb(255, 255, 255);
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        margin-bottom: 30px;
    }
    .project-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .form-group input, .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-sizing: border-box;
    }
    .submit-btn {
        padding: 12px 24px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: bold;
    }
    .submit-btn:hover {
        background-color: #0056b3;
    }
    footer {
        text-align: center;
        padding: 40px;
        background: #333;
        color: white;
        margin-top: 60px;
    }
</style>
</head>
<body style="background-color: rgb(228, 245, 252);">
<div class="header"> 
    <h1 ><strong style="color: #007bff;">Portfolio</strong></h1> 
    <?php include 'nav.php'; ?>
</div>

<hr>

<section id="about" class="highlight">
    <h3 class="h3">About Me:</h3>
    <p>My name is Ryan. I am fifteen and I have spent two years in school learning Python. I'm now currently learning the programming languages: PHP, HTML, and CSS.</p>
    <h3 class="h3">Credentials:</h3>
    <p>Placeholder for credentials.</p>
</section>

<?php if ($projects->num_rows > 0): ?>
    <?php while($p = $projects->fetch_assoc()): ?>
        <section id="project<?php echo $p['id']; ?>">
            <h2 class="h2"><?php echo htmlspecialchars($p['title']); ?>:</h2>
            <div class="project-card">
                <p><?php echo htmlspecialchars($p['description']); ?></p>
            </div>
        </section>
    <?php endwhile; ?>
<?php else: ?>
    <p style="text-align:center;">No projects found.</p>
<?php endif; ?>

<section id="contact">
    <h2 class="h2">Contact:</h2>
    <div class="project-card">
        <form action="contact.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="example@email.com" required>
            </div>
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject" placeholder="What is this about?" required>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="5" placeholder="Write your message here..." required></textarea>
            </div>
            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </div>
</section>

<footer>
    <p>&copy; 2026 Ryan's Portfolio. All rights reserved.</p>
</footer>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>