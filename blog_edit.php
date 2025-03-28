<?php
require 'db.php';

if (!isset($conn)) {
    die("Database connection not established. Please check your db.php file.");
}

// Get the blog ID from the URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT * FROM blogs WHERE id = :id");
$stmt->execute([':id' => $id]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    die("Blog not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];

    $stmt = $conn->prepare("
        UPDATE blogs
        SET title = :title, description = :description, meta_title = :meta_title, meta_description = :meta_description
        WHERE id = :id
    ");
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':meta_title' => $meta_title,
        ':meta_description' => $meta_description,
        ':id' => $id
    ]);

    header("Location: blog_list.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Blog</h1>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="title" class="form-label">Blog Title</label>
                <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($blog['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Blog Description</label>
                <textarea name="description" id="description" class="form-control" rows="5" required><?= htmlspecialchars($blog['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="meta_title" class="form-label">Meta Title</label>
                <input type="text" name="meta_title" id="meta_title" class="form-control" value="<?= htmlspecialchars($blog['meta_title']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="meta_description" class="form-label">Meta Description</label>
                <textarea name="meta_description" id="meta_description" class="form-control" rows="3" required><?= htmlspecialchars($blog['meta_description']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-success">Save Changes</button>
        </form>
    </div>
</body>
</html>