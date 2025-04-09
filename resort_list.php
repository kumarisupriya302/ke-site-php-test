<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['user'];
require 'db.php';
$stmt = $pdo->query("SELECT r.*, d.destination_name FROM resorts r JOIN destinations d ON r.destination_id = d.id ORDER BY d.destination_name, r.resort_name");
$resorts = $stmt->fetchAll();

// Filter resorts to show only active ones in the frontend
$frontendResorts = array_filter($resorts, function($resort) {
    return $resort['is_active'] == 1;
});
?>
<?php include 'bheader.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resorts List</title>
  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
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

    .add-resort-button {
      background: linear-gradient(135deg, #34d399 0%, #059669 100%);
      transition: all 0.3s ease;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: bold;
      text-decoration: none;
    }

    .add-resort-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Toggle switch styles */
    .switch {
      position: relative;
      display: inline-block;
      width: 40px;
      height: 20px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: 0.4s;
      border-radius: 20px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 14px;
      width: 14px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: 0.4s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background-color: #34d399;
    }

    input:checked + .slider:before {
      transform: translateX(20px);
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
      <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Resorts List</h1>
        <a href="create_or_edit_resort.php" class="add-resort-button inline-block mb-6">Add New Resort</a>
        <div class="card p-6">
          <table id="resorts-table" class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
            <thead class="table-header">
              <tr>
                <th class="py-3 px-6">Resort Name</th>
                <th class="py-3 px-6">Destination</th>
                <th class="py-3 px-6">Active</th>
                <th class="py-3 px-6">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($resorts as $resort): ?>
              <tr class="hover:bg-gray-50" id="resort-row-<?php echo $resort['id']; ?>">
                <td class="py-3 px-6 text-gray-700 font-medium">
                  <?php echo htmlspecialchars($resort['resort_name']); ?>
                </td>
                <td class="py-3 px-6 text-gray-500">
                  <?php echo htmlspecialchars($resort['destination_name']); ?>
                </td>
                <td class="py-3 px-6 text-center">
                  <label class="switch">
                    <input type="checkbox" class="toggle-active" data-resort-id="<?php echo $resort['id']; ?>" <?php echo ($resort['is_active'] == 1) ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                  </label>
                </td>
                <td class="py-3 px-6">
                  <a id="view-button-<?php echo $resort['id']; ?>" 
                     class="action-button <?php echo ($resort['is_active'] == 1) ? 'bg-blue-500' : 'bg-gray-400 cursor-not-allowed'; ?>" 
                     <?php if ($resort['is_active'] == 1): ?>
                       href="<?php echo htmlspecialchars($resort['resort_slug']); ?>"
                     <?php else: ?>
                       disabled
                     <?php endif; ?>
                     data-href="<?php echo htmlspecialchars($resort['resort_slug']); ?>">
                    View
                  </a>
                  <a href="create_or_edit_resort.php?destination_id=<?php echo $resort['destination_id']; ?>&resort_id=<?php echo $resort['id']; ?>" class="action-button bg-yellow-500 mr-2">Edit</a>
                  <a href="delete_resort.php?id=<?php echo $resort['id']; ?>" class="action-button bg-red-500" onclick="return confirm('Are you sure you want to delete this resort?');">Delete</a>
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
    // Toggle sidebar collapse (if needed)
    document.getElementById('toggleSidebar').addEventListener('click', function() {
      var sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('sidebar-collapsed');
    });

    async function updateResortStatus(checkbox) {
      const resortId = checkbox.getAttribute('data-resort-id');
      const newStatus = checkbox.checked ? 1 : 0;

      try {
        const response = await fetch('update_resort_status.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ resort_id: resortId, is_active: newStatus })
        });

        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();

        if (!data.success) throw new Error(data.message || 'Failed to update status');

        // Dynamically enable or disable the 'View' button
        const viewButton = document.querySelector(`#view-button-${resortId}`);
        if (newStatus === 1) {
          viewButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
          viewButton.classList.add('bg-blue-500');
          viewButton.href = viewButton.getAttribute('data-href');
          viewButton.removeAttribute('disabled');
        } else {
          viewButton.classList.remove('bg-blue-500');
          viewButton.classList.add('bg-gray-400', 'cursor-not-allowed');
          viewButton.removeAttribute('href');
          viewButton.setAttribute('disabled', 'true');
        }

        // Show success notification
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Resort status updated successfully',
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        });
      } catch (error) {
        console.error('Error:', error);
        checkbox.checked = !checkbox.checked;
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.message,
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        });
      }
    }

    document.querySelectorAll('.toggle-active').forEach(function(checkbox) {
      checkbox.addEventListener('change', function() {
        updateResortStatus(this);
      });
    });

    document.getElementById('apply-bulk-action').addEventListener('click', async function() {
      const selectedResorts = Array.from(document.querySelectorAll('.resort-checkbox:checked'))
        .map(checkbox => checkbox.value);
      const action = document.getElementById('bulk-action').value;

      if (!action || selectedResorts.length === 0) {
        Swal.fire('Error', 'Please select an action and at least one resort', 'error');
        return;
      }

      try {
        const response = await fetch('bulk_action.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ resorts: selectedResorts, action })
        });

        const data = await response.json();
        if (data.success) {
          Swal.fire('Success', 'Bulk action completed successfully', 'success');
          location.reload(); // Refresh to show changes
        } else {
          throw new Error(data.message);
        }
      } catch (error) {
        Swal.fire('Error', error.message, 'error');
      }
    });

    function filterTable() {
      const search = document.getElementById('search').value.toLowerCase();
      const status = document.getElementById('filter-status').value;

      document.querySelectorAll('#resorts-table tbody tr').forEach(row => {
        const name = row.querySelector('td:first-child').textContent.toLowerCase();
        const rowStatus = row.querySelector('.toggle-active').checked ? '1' : '0';
        const matchesSearch = name.includes(search);
        const matchesStatus = status === '' || rowStatus === status;
        row.style.display = matchesSearch && matchesStatus ? '' : 'none';
      });
    }

    document.getElementById('search').addEventListener('input', filterTable);
    document.getElementById('filter-status').addEventListener('change', filterTable);

    function sortTable(columnIndex, isAsc) {
      const table = document.getElementById('resorts-table');
      const tbody = table.querySelector('tbody');
      const rows = Array.from(tbody.querySelectorAll('tr'));

      rows.sort((a, b) => {
        const aText = a.querySelectorAll('td')[columnIndex].textContent.trim();
        const bText = b.querySelectorAll('td')[columnIndex].textContent.trim();
        return isAsc ? aText.localeCompare(bText) : bText.localeCompare(aText);
      });

      while (tbody.firstChild) tbody.removeChild(tbody.firstChild);
      rows.forEach(row => tbody.appendChild(row));
    }

    document.querySelectorAll('#resorts-table th').forEach((th, index) => {
      th.style.cursor = 'pointer';
      th.addEventListener('click', () => {
        const isAsc = th.classList.toggle('asc');
        sortTable(index, isAsc);
      });
    });
  </script>
</body>
</html>
<?php include 'bfooter.php'; ?>
