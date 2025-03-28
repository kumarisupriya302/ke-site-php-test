<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
$user = $_SESSION['user'];
require 'db.php'; // Ensure this file contains the database connection

if (!isset($conn)) {
    die("Database connection not established. Please check your db.php file.");
}

// Simulate categories (you can fetch these from a database)
$categories = ["Technology", "Health", "Travel", "Education", "Lifestyle"];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['step']) && $_POST['step'] === '1') {
        // Step 1: Save selected category in session and reload the form
        $_SESSION['selected_category'] = $_POST['category'];
        header("Location: blog.php");
        exit();
    } elseif (isset($_POST['step']) && $_POST['step'] === '2') {
        // Step 2: Handle file uploads and save blog data
        $uploadDir = 'uploads/'; // Folder to store uploaded images
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the folder if it doesn't exist
        }

        // Handle banner image upload
        $bannerFile = $_FILES['banner'];
        $bannerPath = $uploadDir . basename($bannerFile['name']);
        if (!move_uploaded_file($bannerFile['tmp_name'], $bannerPath)) {
            die("Failed to upload the banner image.");
        }

        // Handle related images upload
        $relatedImages = [];
        foreach ($_FILES['related_images']['tmp_name'] as $key => $tmpName) {
            $relatedImageName = basename($_FILES['related_images']['name'][$key]);
            $relatedImagePath = $uploadDir . $relatedImageName;
            if (move_uploaded_file($tmpName, $relatedImagePath)) {
                $relatedImages[] = $relatedImagePath;
            }
        }

        // Save blog data to the database
        $stmt = $conn->prepare("
            INSERT INTO blogs (category, banner, title, description, meta_title, meta_description, related_images)
            VALUES (:category, :banner, :title, :description, :meta_title, :meta_description, :related_images)
        ");
        $stmt->execute([
            ':category' => $_SESSION['selected_category'],
            ':banner' => $bannerPath,
            ':title' => $_POST['title'],
            ':description' => $_POST['description'],
            ':meta_title' => $_POST['meta_title'],
            ':meta_description' => $_POST['meta_description'],
            ':related_images' => implode(',', $relatedImages) // Save related images as a comma-separated string
        ]);

        // Redirect to display the blog
        header("Location: blog_display.php?success=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 600px;
            margin: auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
        }
        .form-group input, .form-group textarea, .form-group select, button {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
    </style>
</head>
<body >
    <h1>Submit a Blog</h1>
    <?php if (!isset($_SESSION['selected_category'])): ?>
        <!-- Step 1: Select Category -->
        <form method="POST" action="">
            <input type="hidden" name="step" value="1">
            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" id="category" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Next</button>
        </form>
    <?php else: ?>
        <!-- Step 2: Blog Details -->
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="step" value="2">
            <div class="form-group">
                <label for="banner">Blog Banner Image</label>
                <input type="file" name="banner" id="banner" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="title">Blog Title</label>
                <input type="text" name="title" id="title" placeholder="Enter blog title" required>
            </div>
            <div class="form-group">
                <label for="description">Blog Description</label>
                <textarea name="description" id="description" rows="5" placeholder="Enter blog description" required></textarea>
            </div>
            <div class="form-group">
                <label for="meta_title">Meta Title</label>
                <input type="text" name="meta_title" id="meta_title" placeholder="Enter meta title" required>
            </div>
            <div class="form-group">
                <label for="meta_description">Meta Description</label>
                <textarea name="meta_description" id="meta_description" rows="3" placeholder="Enter meta description" required></textarea>
            </div>
            <div class="form-group">
                <label for="related_images">Related Images</label>
                <input type="file" name="related_images[]" id="related_images" accept="image/*" multiple required>
            </div>
            <button type="submit">Submit Blog</button>
        </form>
    <?php endif; ?>
</body>
</html>