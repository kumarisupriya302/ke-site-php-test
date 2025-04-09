<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['user'];
require 'db.php';

// Pagination variables
$perPage = 5; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Get total number of destinations
$totalStmt = $pdo->query("SELECT COUNT(*) FROM destinations");
$totalDestinations = $totalStmt->fetchColumn();

// Get paginated destinations
$stmt = $pdo->prepare("SELECT * FROM destinations ORDER BY destination_name LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$destinations = $stmt->fetchAll();

// Calculate total pages
$totalPages = ceil($totalDestinations / $perPage);

include 'bheader.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Destinations List</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
      margin-bottom: 50px; /* Adding space below the content */
    }
    
    .sidebar-collapsed {
      width: 64px;
    }
    
    .sidebar-collapsed .sidebar-item-text {
      display: none;
    }
    
    .sidebar-collapsed .sidebar-icon {
      text-align: center;
    }

    .page-title {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .destination-card {
      transition: all 0.3s ease;
      border-left: 4px solid transparent;
    }

    .destination-card:hover {
      transform: translateX(5px);
      border-left: 4px solid #3b82f6;
      background-color: #f8fafc;
    }

    .action-button {
      opacity: 0.7;
      transition: all 0.2s ease;
    }

    .destination-card:hover .action-button {
      opacity: 1;
    }

    .create-button {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      transition: all 0.3s ease;
      transform: translateY(0);
    }

    .create-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .pagination-button {
      transition: all 0.3s ease;
    }

    .pagination-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #sidebar {
      background: linear-gradient(180deg, #ffffff 0%, #f3f4f6 100%);
      border-right: 1px solid rgba(0, 0, 0, 0.1);
    }

    .stats-card {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }

    .stats-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .destination-post {
      position: relative;
      background: linear-gradient(135deg, #ffffff, #f9fafb);
      border-radius: 1rem;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 1rem;
    }

    .destination-post:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
    }

    .destination-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 0.5rem;
      margin-bottom: 1rem;
      position: relative;
    }

    .action-buttons-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 1rem;
      background: rgba(0, 0, 0, 0.5);
      opacity: 0;
      transition: opacity 0.3s ease;
      border-radius: 0.5rem;
    }

    .destination-post:hover .action-buttons-overlay {
      opacity: 1;
    }

    .action-button {
      padding: 0.75rem;
      font-size: 1.2rem;
      color: white;
      border-radius: 50%;
      background-color: rgba(0, 0, 0, 0.7);
      transition: background-color 0.3s ease, transform 0.2s ease;
      display: flex;
      justify-content: center;
      align-items: center;
      text-decoration: none;
    }

    .action-button:hover {
      background-color: rgba(0, 0, 0, 0.9);
      transform: scale(1.1);
    }

    .action-button i {
      font-size: 1.5rem;
    }

    .destination-title {
      font-size: 1.25rem;
      font-weight: bold;
      color: #1f2937;
      margin-bottom: 0.5rem;
    }

    .destination-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
    }
  </style>
</head>
<body class="bg-gray-50">
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-white shadow-lg transition-all duration-300">
      <div class="p-6 border-b flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 sidebar-item-text">Admin Dashboard</h1>
        <button id="toggleSidebar" class="text-gray-700 focus:outline-none">
          <i class="fas fa-bars"></i>
        </button>
      </div>
      <nav class="mt-6">
        <a href="dashboard.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors flex items-center">
          <i class="fas fa-tachometer-alt mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Dashboard</span>
        </a>
        <?php if($user['user_type'] === 'super_admin'): ?>
          <a href="manage_users.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors flex items-center">
            <i class="fas fa-users mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Manage Users</span>
          </a>
        <?php endif; ?>
        <a href="destination_list.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors flex items-center">
          <i class="fas fa-map-marker-alt mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Manage Destinations</span>
        </a>
        <a href="resort_list.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors flex items-center">
          <i class="fas fa-hotel mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Manage Resorts</span>
        </a>
        <a href="marketing_template_list.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors flex items-center">
          <i class="fas fa-envelope-open-text mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Marketing Templates</span>
        </a>
        <a href="campaign_dashboard.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors flex items-center">
          <i class="fas fa-bullhorn mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Campaign Dashboard</span>
        </a>
        <a href="logout.php" class="block py-3 px-6 text-red-500 hover:bg-red-500 hover:text-white transition-colors flex items-center">
          <i class="fas fa-sign-out-alt mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Logout</span>
        </a>
      </nav>
    </aside>
    <!-- Main Content -->
    <main class="flex-1 p-8">
      <!-- Breadcrumb -->
      <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
          <li><a href="dashboard.php" class="text-blue-500 hover:text-blue-700 transition-colors">Dashboard</a></li>
          <li><i class="fas fa-chevron-right text-gray-400 text-xs"></i></li>
          <li class="text-gray-500">Destinations</li>
        </ol>
      </nav>

      <div class="flex items-center justify-between mb-8">
        <div>
          <h2 class="text-3xl font-bold page-title">Destinations List</h2>
          <p class="text-gray-600 mt-2">Manage your travel destinations</p>
        </div>
        <a href="create_destination.php" class="create-button text-white px-6 py-3 rounded-lg flex items-center space-x-2">
          <i class="fas fa-plus"></i>
          <span>Create New Destination</span>
        </a>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="stats-card p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Total Destinations</p>
              <h3 class="text-2xl font-bold text-gray-800"><?php echo $totalDestinations; ?></h3>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
              <i class="fas fa-map-marker-alt text-blue-500"></i>
            </div>
          </div>
        </div>
        <div class="stats-card p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Current Page</p>
              <h3 class="text-2xl font-bold text-gray-800"><?php echo $page; ?></h3>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
              <i class="fas fa-file-alt text-green-500"></i>
            </div>
          </div>
        </div>
        <div class="stats-card p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Items Per Page</p>
              <h3 class="text-2xl font-bold text-gray-800"><?php echo $perPage; ?></h3>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
              <i class="fas fa-list text-purple-500"></i>
            </div>
          </div>
        </div>
      </div>

      <?php if(count($destinations) > 0): ?>
        <div class="destination-grid">
          <?php foreach ($destinations as $destination): ?>
          <div class="destination-post">
            <?php
              // Prepare image path
              $destination_slug = isset($destination['destination_name']) ? strtolower(str_replace(' ', '-', $destination['destination_name'])) : 'default';
              $destination_slug = preg_replace('/[^a-z0-9\-]/', '', $destination_slug);
              $destination_folder = 'assets/destinations/' . $destination_slug;

              $banner_image = isset($destination['banner_image']) ? $destination['banner_image'] : 'default-banner.jpg';
              $image_path = $destination_folder . '/' . $banner_image;

              if (!file_exists('c:/xampp/htdocs/ke-site-php-test/' . $image_path)) {
                  $image_path = 'assets/destinations/default-banner.jpg';
              }
            ?>
            <div class="destination-image" style="background-image: url('<?php echo htmlspecialchars($image_path); ?>'); background-size: cover; background-position: center;">
              <div class="action-buttons-overlay">
                <a href="edit_destination.php?id=<?php echo $destination['id']; ?>" class="action-button" title="Edit">
                  <i class="fas fa-edit"></i>
                </a>
                <a href="delete_destination.php?id=<?php echo $destination['id']; ?>" class="action-button" title="Delete" onclick="return confirm('Are you sure you want to delete this destination?');">
                  <i class="fas fa-trash"></i>
                </a>
              </div>
            </div>
            <div class="destination-content">
              <h3 class="destination-title">
                <?php echo htmlspecialchars($destination['destination_name'] ?? 'Unknown Destination'); ?>
              </h3>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-between items-center">
          <div class="text-sm text-gray-600 bg-white px-4 py-2 rounded-lg shadow">
            Showing <?php echo ($offset + 1) . ' - ' . min($offset + $perPage, $totalDestinations); ?> of <?php echo $totalDestinations; ?> destinations
          </div>
          <div class="flex space-x-3">
            <?php if ($page > 1): ?>
              <a href="?page=<?php echo $page - 1; ?>" 
                 class="pagination-button bg-white px-4 py-2 rounded-lg shadow text-gray-700 hover:bg-gray-50 flex items-center space-x-2">
                <i class="fas fa-chevron-left text-sm"></i>
                <span>Previous</span>
              </a>
            <?php endif; ?>
            
            <?php if ($page < $totalPages): ?>
              <a href="?page=<?php echo $page + 1; ?>" 
                 class="pagination-button bg-white px-4 py-2 rounded-lg shadow text-gray-700 hover:bg-gray-50 flex items-center space-x-2">
                <span>Next</span>
                <i class="fas fa-chevron-right text-sm"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php else: ?>
        <div class="bg-white rounded-xl shadow-lg p-8 text-center">
          <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-map-marker-alt text-3xl text-gray-400"></i>
          </div>
          <h3 class="text-xl font-medium text-gray-900 mb-2">No destinations found</h3>
          <p class="text-gray-500">Start by creating your first destination!</p>
        </div>
      <?php endif; ?>
    </main>
  </div>
  <script>
    document.getElementById('toggleSidebar').addEventListener('click', function() {
      var sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('sidebar-collapsed');
    });
  </script>
</body>
</html>
<?php include 'bfooter.php'; ?>
