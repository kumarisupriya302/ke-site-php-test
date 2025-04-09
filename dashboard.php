<?php
session_start();
require_once 'db.php'; // Include your db.php file to establish the database connection
require_once 'bheader.php'; // Include bheader.php to handle session management

// Get dashboard statistics
$statsQuery = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM destinations) AS total_destinations,
        (SELECT COUNT(*) FROM resorts) AS total_resorts,
        (SELECT COUNT(*) FROM campaigns WHERE status = 'active') AS active_campaigns
");
$dashboardStats = $statsQuery->fetch(PDO::FETCH_OBJ);

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['user'];

// Ensure user_type is set
if (!isset($user['user_type'])) {
    $user['user_type'] = 'user'; // Default to 'user' if not set
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard</title>
  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .sidebar-collapsed {
      width: 64px;
    }
    .sidebar-collapsed .sidebar-item-text {
      display: none;
    }
    .sidebar-collapsed .sidebar-icon {
      text-align: center;
    }
    
    /* Enhanced Styles */
    body {
      background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    }
    
    .dashboard-card {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    
    /* Card specific gradients */
    .dashboard-card.destinations {
      background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
    }
    
    .dashboard-card.resorts {
      background: linear-gradient(135deg, #34d399 0%, #059669 100%);
    }
    
    .dashboard-card.campaigns {
      background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
    }
    
    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }
    
    .dashboard-card h3,
    .dashboard-card .stats-number {
      color: white !important;
      -webkit-text-fill-color: white !important;
    }
    
    .dashboard-card h3 {
      font-weight: 600;
      opacity: 0.9;
    }
    
    .stats-number {
      font-size: 2.5rem;
      font-weight: bold;
    }
    
    .stats-icon {
      font-size: 4rem;
      opacity: 0.15;
      position: absolute;
      right: 1rem;
      bottom: -0.5rem;
      color: white;
    }
    
    #sidebar {
      background: linear-gradient(180deg, #ffffff 0%, #f3f4f6 100%);
      border-right: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .welcome-text {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .card-icon {
      color: white;
      opacity: 0.9;
    }
  </style>
</head>
<body class="bg-gray-100">
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
        <a href="dashboard.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors flex items-center sidebar-link">
          <i class="fas fa-tachometer-alt mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Dashboard</span>
        </a>
        <?php if($user['user_type'] === 'super_admin'): ?>
          <a href="manage_users.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors flex items-center sidebar-link">
            <i class="fas fa-users mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Manage Users</span>
          </a>
        <?php endif; ?>
        <a href="destination_list.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors flex items-center sidebar-link">
          <i class="fas fa-map-marker-alt mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Manage Destinations</span>
        </a>
        <a href="resort_list.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors flex items-center sidebar-link">
          <i class="fas fa-hotel mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Manage Resorts</span>
        </a>
        <a href="marketing_template_list.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors flex items-center sidebar-link">
          <i class="fas fa-envelope-open-text mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Marketing Templates</span>
        </a>
        <a href="campaign_dashboard.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors flex items-center sidebar-link">
          <i class="fas fa-bullhorn mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Campaign Dashboard</span>
        </a>
        <a href="blog.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors flex items-center sidebar-link">
          <i class="fas fa-bullhorn mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Blog</span>
        </a>
        <a href="blog-list.php" class="block py-3 px-6 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors flex items-center sidebar-link">
          <i class="fas fa-bullhorn mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Blog List</span>
        </a>

        <a href="logout.php" class="block py-3 px-6 text-red-500 hover:bg-red-500 hover:text-white transition-colors flex items-center sidebar-link">
          <i class="fas fa-sign-out-alt mr-2 sidebar-icon"></i> <span class="sidebar-item-text">Logout</span>
        </a>
      </nav>
    </aside>
    <!-- Main Content -->
    <main class="flex-1 p-8">
      <!-- Breadcrumb -->
      <nav class="mb-4 text-sm text-gray-600" aria-label="Breadcrumb">
        <ol class="list-reset flex">
          <li><a href="dashboard.php" class="text-blue-600 hover:underline">Dashboard</a></li>
          <li><span class="mx-2">/</span></li>
          <li class="text-gray-600">Welcome</li>
        </ol>
      </nav>
      <h2 class="text-3xl font-bold mb-6 welcome-text">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
      <!-- Dashboard Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="dashboard-card destinations p-6 relative overflow-hidden">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-lg font-semibold mb-2">Total Destinations</h3>
              <p class="stats-number"><?php echo $dashboardStats->total_destinations; ?></p>
            </div>
            <i class="fas fa-map-marker-alt text-xl card-icon"></i>
          </div>
          <i class="fas fa-map-marker-alt stats-icon"></i>
        </div>
        <div class="dashboard-card resorts p-6 relative overflow-hidden">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-lg font-semibold mb-2">Total Resorts</h3>
              <p class="stats-number"><?php echo $dashboardStats->total_resorts; ?></p>
            </div>
            <i class="fas fa-hotel text-xl card-icon"></i>
          </div>
          <i class="fas fa-hotel stats-icon"></i>
        </div>
        <div class="dashboard-card campaigns p-6 relative overflow-hidden">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-lg font-semibold mb-2">Active Campaigns</h3>
              <p class="stats-number"><?php echo $dashboardStats->active_campaigns; ?></p>
            </div>
            <i class="fas fa-bullhorn text-xl card-icon"></i>
          </div>
          <i class="fas fa-bullhorn stats-icon"></i>
        </div>
      </div>
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
