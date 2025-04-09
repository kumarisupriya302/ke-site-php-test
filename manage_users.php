<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] != 'super_admin') {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['user'];
require 'db.php';
require_once 'bheader.php';
$stmt = $pdo->query("SELECT * FROM users ORDER BY username");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Users</title>
  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
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

    .card {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .table-header {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: white;
    }

    th, td {
      padding: 12px 16px;
      text-align: left;
    }

    th {
      font-size: 0.875rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    tbody tr {
      border-bottom: 1px solid #e5e7eb;
      transition: background-color 0.3s ease;
    }

    tbody tr:hover {
      background-color: #f3f4f6;
    }

    td {
      font-size: 0.875rem;
      color: #4b5563;
    }

    .action-button {
      display: inline-block;
      padding: 6px 12px;
      font-size: 0.875rem;
      font-weight: 500;
      color: white;
      border-radius: 4px;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .action-button:hover {
      transform: translateY(-2px);
    }

    .bg-yellow-500:hover {
      background-color: #d97706;
    }

    .bg-red-500:hover {
      background-color: #b91c1c;
    }

    .add-user-button {
      background: linear-gradient(135deg, #34d399 0%, #059669 100%);
      transition: all 0.3s ease;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: bold;
      text-decoration: none;
    }

    .add-user-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
      <nav class="mb-4 text-sm text-gray-600" aria-label="Breadcrumb">
        <ol class="list-reset flex">
          <li><a href="dashboard.php" class="text-blue-600 hover:underline">Dashboard</a></li>
          <li><span class="mx-2">/</span></li>
          <li class="text-gray-600">Manage Users</li>
        </ol>
      </nav>
      <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Users</h1>
        <a href="create_user.php" class="add-user-button inline-block mb-6">Add New User</a>
        <div class="card p-6">
          <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
            <thead class="table-header">
              <tr>
                <th class="py-3 px-6">Username</th>
                <th class="py-3 px-6">User Type</th>
                <th class="py-3 px-6">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($users as $user): ?>
              <tr class="hover:bg-gray-50">
                <td class="py-3 px-6 text-gray-700 font-medium">
                  <?php echo htmlspecialchars($user['username']); ?>
                </td>
                <td class="py-3 px-6 text-gray-500">
                  <?php echo htmlspecialchars($user['user_type']); ?>
                </td>
                <td class="py-3 px-6">
                  <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="action-button bg-yellow-500 mr-2">Edit</a>
                  <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="action-button bg-red-500" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
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
