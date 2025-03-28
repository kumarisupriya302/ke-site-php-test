<?php
require 'db.php';

if (!isset($conn)) {
    die("Database connection not established. Please check your db.php file.");
}

$stmt = $conn->query("SELECT * FROM blogs ORDER BY id DESC LIMIT 1");
$latestBlog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$latestBlog) {
    die("No blog found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title><?= htmlspecialchars($latestBlog['title']) ?></title>
    <style>
        /* body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        } */
        .blog-template {
            /* max-width: 800px; */
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .blog-template img {
            max-width: 100%;
            border-radius: 8px;
        }
        .blog-template h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .blog-template p {
            line-height: 1.6;
        }
        .related-images img {
            max-width: 150px;
            margin: 5px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="">
        <h1><?= htmlspecialchars($latestBlog['title']) ?></h1>
        <!-- <p><strong>Category:</strong> <?= htmlspecialchars($latestBlog['category']) ?></p> -->
        <img src="<?= htmlspecialchars($latestBlog['banner']) ?>" alt="Blog Banner">
        <div class="container">
        <p><?= nl2br(htmlspecialchars($latestBlog['description'])) ?></p>
        <!-- <p><strong>Meta Title:</strong> <?= htmlspecialchars($latestBlog['meta_title']) ?></p>
        <p><strong>Meta Description:</strong> <?= htmlspecialchars($latestBlog['meta_description']) ?></p> -->
        <div class="related-images">
            <h3>Related Images:</h3>
            <?php foreach (explode(',', $latestBlog['related_images']) as $image): ?>
                <img src="<?= htmlspecialchars(trim($image)) ?>" alt="Related Image">
            <?php endforeach; ?>
        </div>
        </div>
    </div>
</body>
</html>