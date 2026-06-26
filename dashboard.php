<?php
session_start();
require_once 'Db.php';

// Security Check: Ensure the user is logged in as an admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: portal_access.php");
    exit;
}

// --- Handle Project Actions ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'add_project') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $url = $_POST['url'];
        $image_url = $_POST['image_url'];
        
        $stmt = $conn->prepare("INSERT INTO projects (title, description, url, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $description, $url, $image_url);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php?success=1");
        exit;
    }

    if ($_POST['action'] == 'delete_project') {
        $id = $_POST['project_id'];
        $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php?success=1");
        exit;
    }

    if ($_POST['action'] == 'toggle_visibility') {
        $id = $_POST['project_id'];
        $current_visibility = $_POST['is_visible'];
        $new_visibility = ($current_visibility == 1) ? 0 : 1;
        
        $stmt = $conn->prepare("UPDATE projects SET is_visible = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_visibility, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php?success=1");
        exit;
    }

    if ($_POST['action'] == 'edit_project') {
        $id = $_POST['project_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $url = $_POST['url'];
        $image_url = $_POST['image_url'];
        $is_visible = $_POST['is_visible'];

        $stmt = $conn->prepare("UPDATE projects SET title = ?, description = ?, url = ?, image_url = ?, is_visible = ? WHERE id = ?");
        $stmt->bind_param("ssssii", $title, $description, $url, $image_url, $is_visible, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php?success=1");
        exit;
    }
}

// Fetch all messages from the contacts table
$query = "SELECT id, name, email, subject, message, created_at FROM contacts ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$messages = $stmt->get_result();

// Fetch all projects
$project_query = "SELECT * FROM projects ORDER BY created_at DESC";
$p_stmt = $conn->prepare($project_query);
$p_stmt->execute();
$projects = $p_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        h1 { margin: 0; font-size: 1.5rem; }
        .logout-btn {
            text-decoration: none;
            color: #dc3545;
            font-weight: bold;
        }
        .section {
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #eee;
        }
        .section:last-child { border-bottom: none; }
        h2 { margin-top: 0; margin-bottom: 1.5rem; }

        /* Message Styles */
        .message-card {
            border: 1px solid #eee;
            padding: 1.5rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            background: #fafafa;
        }
        .message-header {
            margin-bottom: 1rem;
        }
        .message-header h3 {
            margin: 0 0 0.5rem 0;
            color: #333;
        }
        .message-header p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        .message-body {
            white-space: pre-wrap;
            color: #444;
            line-height: 1.6;
        }

        /* Project Styles */
        .project-form {
            background: #f9f9f9;
            padding: 1.5rem;
            border-radius: 6px;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        
        .project-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border: 1px solid #eee;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        .project-info h3 { margin: 0; }
        .project-actions {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Admin Dashboard</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <?php if (isset($_GET['success'])): ?>
            <p style="color: green; font-weight: bold;">Action successful!</p>
        <?php endif; ?>

        <div class="section">
            <h2>Manage Projects</h2>
            <div class="project-form">
                <form method="POST">
                    <input type="hidden" name="action" value="add_project">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" name="url">
                    </div>
                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="text" name="image_url">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Project</button>
                </form>
            </div>

            <?php if ($projects->num_rows > 0): ?>
                <?php while($p = $projects->fetch_assoc()): ?>
                    <div class="project-item">
                        <div class="project-info">
                            <h3><?php echo htmlspecialchars($p['title']); ?></h3>
                            <p><?php echo htmlspecialchars($p['description']); ?></p>
                        </div>
                        <div class="project-actions">
                            <a href="?edit_id=<?php echo $p['id']; ?>" class="btn btn-success">Edit</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="toggle_visibility">
                                <input type="hidden" name="project_id" value="<?php echo $p['id']; ?>">
                                <input type="hidden" name="is_visible" value="<?php echo $p['is_visible']; ?>">
                                <button type="submit" class="btn <?php echo $p['is_visible'] ? 'btn-danger' : 'btn-primary'; ?>">
                                    <?php echo $p['is_visible'] ? 'Hide' : 'Show'; ?>
                                </button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete_project">
                                <input type="hidden" name="project_id" value="<?php echo $p['id']; ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No projects found.</p>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Messages</h2>
            <?php if ($messages->num_rows > 0): ?>
                <?php while($row = $messages->fetch_assoc()): ?>
                    <div class="message-card">
                        <div class="message-header">
                            <h3>From: <?php echo htmlspecialchars($row['name']); ?></h3>
                            <p>
                                Email: <?php echo htmlspecialchars($row['email']); ?> | 
                                Subject: <?php echo htmlspecialchars($row['subject']); ?> | 
                                Date: <?php echo $row['created_at']; ?>
                            </p>
                        </div>
                        <div class="message-body">
                            <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No messages received yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
$stmt->close();
$p_stmt->close();
$conn->close();
?>

