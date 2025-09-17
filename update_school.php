<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection parameters
    $host = 'localhost';
    $db = 'moet1';
    $user = 'root'; // Change this to your actual username
    $pass = ''; // Change this to your actual password
    $charset = 'utf8mb4';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get the data from the form
        $school_id = $_POST['school_id'];
        $principal_name = $_POST['principal_name'];
        $principal_surname = $_POST['principal_surname'];
        $phone_number = $_POST['phone_number'];
        $email_address = $_POST['email_address'];
        $female_teachers = $_POST['female_teachers'];
        $male_teachers = $_POST['male_teachers'];
        $gender = $_POST['gender']; // New field for gender

        // Calculate total teachers
        $total_teachers = $female_teachers + $male_teachers;

        // Update the school details in the database
        $sql = "UPDATE schools SET 
                    principal_name = :principal_name,
                    principal_surname = :principal_surname,
                    phone_number = :phone_number,
                    email_address = :email_address,
                    female_teachers = :female_teachers,
                    male_teachers = :male_teachers,
                    total_teachers = :total_teachers,
                    gender = :gender
                WHERE school_id = :school_id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':principal_name', $principal_name);
        $stmt->bindParam(':principal_surname', $principal_surname);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':email_address', $email_address);
        $stmt->bindParam(':female_teachers', $female_teachers);
        $stmt->bindParam(':male_teachers', $male_teachers);
        $stmt->bindParam(':total_teachers', $total_teachers);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':school_id', $school_id);
        $stmt->execute();

        // Redirect or show success message
        header("Location: redirectpage.php"); // Redirect back to the main page
        exit();
    } catch (PDOException $e) {
        echo 'Database error: ' . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>School Data Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f3f4f6;
    }
    .table-container {
      max-height: 70vh;
      overflow-y: auto;
    }
    .table-container::-webkit-scrollbar {
      width: 8px;
    }
    .table-container::-webkit-scrollbar-thumb {
      background-color: #3b82f6;
      border-radius: 4px;
    }
    .highlight-row {
      transition: all 0.2s ease;
    }
    .highlight-row:hover {
      background-color: #e0f2fe;
    }
    .school-header {
      font-weight: 700;
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
      color: #1d4ed8; /* Tailwind blue-700 */
      align-items: center;
      display: flex;
    }
  </style>
</head>
<body class="min-h-screen bg-gray-50">
  <div class="container mx-auto px-4 py-8 flex-grow">
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
      <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-blue-600 mb-4 md:mb-0">
          <i class="fas fa-school mr-2"></i>School Data Management
        </h1>
        <div class="flex items-center space-x-4">
          <button 
            onclick="window.location.href='redirectpage.php';" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors duration-200"
          >
            <i class="fas fa-arrow-left mr-2"></i>Back
          </button>
          <div class="flex items-center space-x-2 text-blue-600">
            <i class="fas fa-user-circle"></i>
            <span><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?></span>
          </div>
        </div>
      </div>

      <?php
      // Database connection parameters
      $host = 'localhost';
      $db = 'moet1';
      $user = 'root'; // Change this to your actual username
      $pass = ''; // Change this to your actual password
      $charset = 'utf8mb4';

      try {
          $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

          if (!isset($_SESSION['user_id'])) {
              throw new Exception("User not logged in");
          }

          $user_id = $_SESSION['user_id'];

          // Retrieve school data for the logged-in user
          $sql = "
              SELECT * 
              FROM schools 
              WHERE user_id = :user_id
          ";

          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
          $stmt->execute();

          $schools = $stmt->fetchAll();

          if (count($schools) > 0) {
              foreach ($schools as $school) {
                  echo '<div class="mb-8 p-4 border rounded-lg bg-blue-50">';
                  // Display School Name and Registration Number in bold header
                  echo '<div class="school-header">';
                  echo htmlspecialchars($school['school_name']) . ' — <span class="font-normal text-gray-600">' . htmlspecialchars($school['registration_number']) . '</span>';
                  echo '</div>';

                  echo '<div class="table-container">';
                  echo '<table class="w-full">';
                  echo '<thead class="bg-blue-100">';
                  echo '<tr>';
                  echo '<th class="px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Principal Name</th>';
                  echo '<th class="px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Surname</th>';
                  echo '<th class="px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Gender</th>';
                  echo '<th class="px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Phone Number</th>';
                  echo '<th class="px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Email</th>';
                  echo '<th class="px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Total Teachers</th>';
                  echo '<th class="px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Female Teachers</th>';
                  echo '<th class="px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Male Teachers</th>';
                  echo '<th class="px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Actions</th>';
                  echo '</tr>';
                  echo '</thead>';
                  echo '<tbody>';
                  echo '<tr class="highlight-row hover:bg-blue-50 group">';
                  echo '<td class="px-4 py-4 whitespace-nowrap">' . htmlspecialchars($school['principal_name']) . '</td>';
                  echo '<td class="px-4 py-4 whitespace-nowrap">' . htmlspecialchars($school['principal_surname']) . '</td>';
                  echo '<td class="px-4 py-4 whitespace-nowrap">' . htmlspecialchars($school['gender']) . '</td>';
                  echo '<td class="px-4 py-4 whitespace-nowrap">' . htmlspecialchars($school['phone_number']) . '</td>';
                  echo '<td class="px-4 py-4 whitespace-nowrap">' . htmlspecialchars($school['email_address']) . '</td>';
                  echo '<td class="px-4 py-4 whitespace-nowrap">' . htmlspecialchars($school['total_teachers']) . '</td>';
                  echo '<td class="px-4 py-4 whitespace-nowrap">' . htmlspecialchars($school['female_teachers']) . '</td>';
                  echo '<td class="px-4 py-4 whitespace-nowrap">' . htmlspecialchars($school['male_teachers']) . '</td>';

                  // Edit button with onclick to open modal (assuming script exists)
                  echo '<td class="px-4 py-4 whitespace-nowrap">';
                  echo '<button class="text-blue-600 hover:text-blue-800" onclick=\'showEditModal(' . json_encode($school) . ')\'>';
                  echo '<i class="fas fa-edit"></i> Edit';
                  echo '</button>';
                  echo '</td>';

                  echo '</tr>';
                  echo '</tbody>';
                  echo '</table>';
                  echo '</div>';
                  echo '</div>';
              }
          } else {
              echo '<div class="text-center py-8">';
              echo '<i class="fas fa-info-circle text-4xl text-blue-300 mb-4"></i>';
              echo '<p class="text-gray-600">No school data found for your account.</p>';
              echo '</div>';
          }
      } catch (PDOException $e) {
          echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">';
          echo '<p>Database error: ' . htmlspecialchars($e->getMessage()) . '</p>';
          echo '</div>';
      } catch (Exception $e) {
          echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">';
          echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
          echo '</div>';
      }
      ?>
    </div>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-blue-600">Edit School Details</h3>
        <button onclick="hideEditModal()" class="text-gray-500 hover:text-gray-700">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <form id="editForm" method="POST" action="update_school.php">
        <input type="hidden" id="edit_school_id" name="school_id" />

        <div class="grid grid-cols-2 gap-2 mb-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Principal Name</label>
            <input type="text" id="edit_principal_name" name="principal_name" class="w-full px-3 py-2 border rounded-md" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Principal Surname</label>
            <input type="text" id="edit_principal_surname" name="principal_surname" class="w-full px-3 py-2 border rounded-md" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
            <select id="edit_gender" name="gender" class="w-full px-3 py-2 border rounded-md" required>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
            <input type="text" id="edit_phone_number" name="phone_number" class="w-full px-3 py-2 border rounded-md" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" id="edit_email_address" name="email_address" class="w-full px-3 py-2 border rounded-md" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Female Teachers</label>
            <input type="number" id="edit_female_teachers" name="female_teachers" class="w-full px-3 py-2 border rounded-md" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Male Teachers</label>
            <input type="number" id="edit_male_teachers" name="male_teachers" class="w-full px-3 py-2 border rounded-md" required />
          </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
          <button type="button" onclick="hideEditModal()" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-100">Cancel</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Modal functions
    function showEditModal(data) {
      document.getElementById('edit_school_id').value = data.school_id;
      document.getElementById('edit_principal_name').value = data.principal_name;
      document.getElementById('edit_principal_surname').value = data.principal_surname;
      document.getElementById('edit_gender').value = data.gender;
      document.getElementById('edit_phone_number').value = data.phone_number;
      document.getElementById('edit_email_address').value = data.email_address;
      document.getElementById('edit_female_teachers').value = data.female_teachers;
      document.getElementById('edit_male_teachers').value = data.male_teachers;

      document.getElementById('editModal').classList.remove('hidden');
    }

    function hideEditModal() {
      document.getElementById('editModal').classList.add('hidden');
    }
  </script>
</body>
</html>
