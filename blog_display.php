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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($blog['title']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container mt-5">
        <h1><?= htmlspecialchars($blog['title']) ?></h1>
        <p><strong>Category:</strong> <?= htmlspecialchars($blog['category']) ?></p>
        <img src="<?= htmlspecialchars($blog['banner']) ?>" alt="Blog Banner" class="img-fluid mb-4">
        <p><?= nl2br(htmlspecialchars($blog['description'])) ?></p>
        <div class="related-images">
            <h3>Related Images:</h3>
            <?php foreach (explode(',', $blog['related_images']) as $image): ?>
                <img src="<?= htmlspecialchars(trim($image)) ?>" alt="Related Image" class="img-thumbnail" style="max-width: 150px; margin: 5px;">
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>