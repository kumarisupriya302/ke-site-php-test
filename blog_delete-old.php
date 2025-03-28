<?php
require 'db.php';

if (!isset($conn)) {
    die("Database connection not established. Please check your db.php file.");
}

// Get the blog ID from the URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("DELETE FROM blogs WHERE id = :id");
$stmt->execute([':id' => $id]);

//header("Location: blog_list.php?success=1");
header("Location: blog-list.php");
exit();
?>