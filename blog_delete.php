<?php
require 'db.php';

if (!isset($conn)) {
    die("Database connection not established. Please check your db.php file.");
}

// Get the blog ID from the URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Delete the blog
$stmt = $conn->prepare("DELETE FROM blogs WHERE id = :id");
$stmt->execute([':id' => $id]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Deleted</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-success">
            <h4 class="alert-heading">Blog Deleted Successfully!</h4>
            <p>The blog has been deleted from the database.</p>
        </div>
        <a href="blog-list.php" class="btn btn-primary">Back to Blog List</a>
    </div>
</body>
</html>