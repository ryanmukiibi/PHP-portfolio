<?php
require_once 'Db.php';

// Fetch only visible projects
$query = "SELECT id, title FROM projects WHERE is_visible = 1 ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$navProjects = $stmt->get_result();

echo '<nav>';
while ($row = $navProjects->fetch_assoc()) {
    $id = $row['id'];
    // Create a URL fragment for the navigation
    echo '<a href="#project' . $id . '">' . htmlspecialchars($row['title']) . '</a>';
}
echo '<a href="#contact">Contact</a>';
echo '</nav>';
?>