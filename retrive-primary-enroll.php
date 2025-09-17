<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view this page.");
}

$user_id = $_SESSION['user_id'];

// Database connection
$servername = "localhost";
$username = "root"; // replace with your database username
$password = ""; // replace with your database password
$dbname = "moet1"; // replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the school_id for the logged-in user from the schools table via user_id
$stmt = $conn->prepare("SELECT school_id FROM schools WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($school_id);
if (!$stmt->fetch()) {
    die("User 's school not found.");
}
$stmt->close();

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Delete only if the record belongs to the user's school
    $stmt = $conn->prepare("DELETE FROM primary_enrollment WHERE P_id = ? AND school_id = ?");
    $stmt->bind_param("ii", $id, $school_id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle edit request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['P_id'])) {
    $id = intval($_POST['P_id']);
    // Sanitize and default to 0 if empty
    $female_reception = !empty($_POST['female_reception']) ? intval($_POST['female_reception']) : 0;
    $male_reception = !empty($_POST['male_reception']) ? intval($_POST['male_reception']) : 0;
    $grade1_girls = !empty($_POST['grade1_girls']) ? intval($_POST['grade1_girls']) : 0;
    $grade1_boys = !empty($_POST['grade1_boys']) ? intval($_POST['grade1_boys']) : 0;
    $grade2_girls = !empty($_POST['grade2_girls']) ? intval($_POST['grade2_girls']) : 0;
    $grade2_boys = !empty($_POST['grade2_boys']) ? intval($_POST['grade2_boys']) : 0;
    $grade3_girls = !empty($_POST['grade3_girls']) ? intval($_POST['grade3_girls']) : 0;
    $grade3_boys = !empty($_POST['grade3_boys']) ? intval($_POST['grade3_boys']) : 0;
    $grade4_girls = !empty($_POST['grade4_girls']) ? intval($_POST['grade4_girls']) : 0;
    $grade4_boys = !empty($_POST['grade4_boys']) ? intval($_POST['grade4_boys']) : 0;
    $grade5_girls = !empty($_POST['grade5_girls']) ? intval($_POST['grade5_girls']) : 0;
    $grade5_boys = !empty($_POST['grade5_boys']) ? intval($_POST['grade5_boys']) : 0;
    $grade6_girls = !empty($_POST['grade6_girls']) ? intval($_POST['grade6_girls']) : 0;
    $grade6_boys = !empty($_POST['grade6_boys']) ? intval($_POST['grade6_boys']) : 0;
    $grade7_girls = !empty($_POST['grade7_girls']) ? intval($_POST['grade7_girls']) : 0;
    $grade7_boys = !empty($_POST['grade7_boys']) ? intval($_POST['grade7_boys']) : 0;
    $repeaters_girls = !empty($_POST['repeaters_girls']) ? intval($_POST['repeaters_girls']) : 0;
    $repeaters_boys = !empty($_POST['repeaters_boys']) ? intval($_POST['repeaters_boys']) : 0;

    // Update only if the record belongs to the user's school
    $stmt = $conn->prepare("UPDATE primary_enrollment SET 
        female_reception = ?,
        male_reception = ?,
        grade1_girls = ?,
        grade1_boys = ?,
        grade2_girls = ?,
        grade2_boys = ?,
        grade3_girls = ?,
        grade3_boys = ?,
        grade4_girls = ?,
        grade4_boys = ?,
        grade5_girls = ?,
        grade5_boys = ?,
        grade6_girls = ?,
        grade6_boys = ?,
        grade7_girls = ?,
        grade7_boys = ?,
        repeaters_girls = ?,
        repeaters_boys = ?
        WHERE P_id = ? AND school_id = ?");
    $stmt->bind_param("iiiiiiiiiiiiiiiiiiii", 
        $female_reception,
        $male_reception,
        $grade1_girls,
        $grade1_boys,
        $grade2_girls,
        $grade2_boys,
        $grade3_girls,
        $grade3_boys,
        $grade4_girls,
        $grade4_boys,
        $grade5_girls,
        $grade5_boys,
        $grade6_girls,
        $grade6_boys,
        $grade7_girls,
        $grade7_boys,
        $repeaters_girls,
        $repeaters_boys,
        $id,
        $school_id
    );
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all data for the user's school only
$stmt = $conn->prepare("SELECT * FROM primary_enrollment WHERE school_id = ?");
$stmt->bind_param("i", $school_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Primary Enrollment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* General body styles */
body {
    font-family: Arial, sans-serif;
    background-color: #eaf2f8;
    margin: 0;
    padding: 20px;
}

/* Header styles */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #3b82f6;
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.header img {
    width: 80px;
    height: auto;
    border-radius: 50px;
    margin-right: 15px;
}

.header h1 {
    font-size: 24px;
    margin: 0;
}

.header a {
    background-color: #bd0d07ff;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s;
}

.header a:hover {
    background-color: #1d4ed8;
}

/* Page heading */
h2 {
    text-align: center;
    color: #1e3a8a;
    margin-bottom: 30px;
}

/* Container for table with horizontal scroll on small screens */
.table-container {
    width: 100%;
    overflow-x: auto;
    margin: 20px 0;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border-radius: 8px;
    background-color: white;
}

/* Table styles */
table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px; /* Ensures horizontal scroll on narrow screens */
}

th, td {
    padding: 15px;
    text-align: center;
    border: 1px solid #e2e8f0;
    font-size: 16px;
    white-space: nowrap; /* Prevent text wrapping */
}

th {
    background-color: #3b82f6;
    color: white;
    position: sticky;
    top: 0;
    z-index: 1;
}

tr:nth-child(even) {
    background-color: #f8fafc;
}

tr:hover {
    background-color: #bfdbfe;
}

/* Button container */
.button-container {
    text-align: center;
    margin: 30px 0;
}

/* Buttons */
.button {
    padding: 12px 24px;
    margin: 0 10px;
    border: none;
    border-radius: 6px;
    background-color: #3b82f6;
    color: white;
    cursor: pointer;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s;
    display: inline-block;
}

.button:hover {
    background-color: #2563eb;
}

.button.delete {
    background-color: #ef4444;
}

.button.delete:hover {
    background-color: #dc2626;
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
    padding: 20px;
}

.modal-content {
    background-color: #ffffff;
    margin: 5% auto;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    width: 80%;
    max-width: 800px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 15px;
}

.modal-title {
    font-size: 1.5rem;
    color: #1e3a8a;
    margin: 0;
}

.close {
    color: #64748b;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s;
}

.close:hover {
    color: #1e293b;
}

/* Form grid for modal inputs */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #334155;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 1rem;
}

/* Form actions */
.form-actions {
    text-align: right;
    margin-top: 20px;
}

/* Responsive adjustments */
@media (max-width: 600px) {
    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    .header img {
        width: 60px;
        margin-right: 0;
    }
    .header h1 {
        font-size: 20px;
    }
    .header a {
        padding: 8px 12px;
        font-size: 14px;
    }
    h2 {
        font-size: 20px;
        margin-bottom: 20px;
    }
    .button {
        padding: 10px 18px;
        font-size: 14px;
        margin: 5px 5px;
    }
    /* Table container already scrolls horizontally */
    table {
        font-size: 14px;
    }
    th, td {
        padding: 10px 8px;
    }
    .form-grid {
        grid-template-columns: 1fr;
    }
}

        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #334155;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 1rem;
        }
        .form-actions {
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="moet.png" alt="Ministry of Education" />
        <div>
            <h1>Primary Enrollment</h1>
            <h2>Ministry of Education & Training</h2>
        </div>
        <a href="primary-Dash.php">Back to Dashboard</a>
    </div>

    <h2>Primary Enrollment Data</h2>

    <div class="button-container">
        <a href="primaryenrolment.php" class="button">
            <i class="fas fa-plus"></i> Add New Enrollment
        </a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Female Reception</th>
                    <th>Male Reception</th>
                    <th>Grade 1 (F/M)</th>
                    <th>Grade 2 (F/M)</th>
                    <th>Grade 3 (F/M)</th>
                    <th>Grade 4 (F/M)</th>
                    <th>Grade 5 (F/M)</th>
                    <th>Grade 6 (F/M)</th>
                    <th>Grade 7 (F/M)</th>
                    <th>Repeaters (F/M)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['female_reception']) ?></td>
                    <td><?= htmlspecialchars($row['male_reception']) ?></td>
                    <td><?= htmlspecialchars($row['grade1_girls']) ?> / <?= htmlspecialchars($row['grade1_boys']) ?></td>
                    <td><?= htmlspecialchars($row['grade2_girls']) ?> / <?= htmlspecialchars($row['grade2_boys']) ?></td>
                    <td><?= htmlspecialchars($row['grade3_girls']) ?> / <?= htmlspecialchars($row['grade3_boys']) ?></td>
                    <td><?= htmlspecialchars($row['grade4_girls']) ?> / <?= htmlspecialchars($row['grade4_boys']) ?></td>
                    <td><?= htmlspecialchars($row['grade5_girls']) ?> / <?= htmlspecialchars($row['grade5_boys']) ?></td>
                    <td><?= htmlspecialchars($row['grade6_girls']) ?> / <?= htmlspecialchars($row['grade6_boys']) ?></td>
                    <td><?= htmlspecialchars($row['grade7_girls']) ?> / <?= htmlspecialchars($row['grade7_boys']) ?></td>
                    <td><?= htmlspecialchars($row['repeaters_girls']) ?> / <?= htmlspecialchars($row['repeaters_boys']) ?></td>
                    <td>
                        <button class="button" onclick='openEditModal(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <a href="?delete=<?= urlencode($row['P_id']) ?>" class="button delete" onclick="return confirm('Are you sure you want to delete this record?');">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Edit Enrollment Record</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <form id="editForm" method="POST">
                <input type="hidden" id="edit_P_id" name="P_id" />
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_female_reception">Female Reception</label>
                        <input type="number" id="edit_female_reception" name="female_reception" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_male_reception">Male Reception</label>
                        <input type="number" id="edit_male_reception" name="male_reception" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_grade1_girls">Grade 1 Girls</label>
                        <input type="number" id="edit_grade1_girls" name="grade1_girls" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_grade1_boys">Grade 1 Boys</label>
                        <input type="number" id="edit_grade1_boys" name="grade1_boys" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_grade2_girls">Grade 2 Girls</label>
                        <input type="number" id="edit_grade2_girls" name="grade2_girls" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_grade2_boys">Grade 2 Boys</label>
                        <input type="number" id="edit_grade2_boys" name="grade2_boys" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_grade3_girls">Grade 3 Girls</label>
                        <input type="number" id="edit_grade3_girls" name="grade3_girls" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_grade3_boys">Grade 3 Boys</label>
                        <input type="number" id="edit_grade3_boys" name="grade3_boys" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_grade4_girls">Grade 4 Girls</label>
                        <input type="number" id="edit_grade4_girls" name="grade4_girls" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_grade4_boys">Grade 4 Boys</label>
                        <input type="number" id="edit_grade4_boys" name="grade4_boys" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_grade5_girls">Grade 5 Girls</label>
                        <input type="number" id="edit_grade5_girls" name="grade5_girls" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_grade5_boys">Grade 5 Boys</label>
                        <input type="number" id="edit_grade5_boys" name="grade5_boys" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_grade6_girls">Grade 6 Girls</label>
                        <input type="number" id="edit_grade6_girls" name="grade6_girls" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_grade6_boys">Grade 6 Boys</label>
                        <input type="number" id="edit_grade6_boys" name="grade6_boys" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_grade7_girls">Grade 7 Girls</label>
                        <input type="number" id="edit_grade7_girls" name="grade7_girls" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_grade7_boys">Grade 7 Boys</label>
                        <input type="number" id="edit_grade7_boys" name="grade7_boys" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_repeaters_girls">Repeaters Girls</label>
                        <input type="number" id="edit_repeaters_girls" name="repeaters_girls" min="0" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_repeaters_boys">Repeaters Boys</label>
                        <input type="number" id="edit_repeaters_boys" name="repeaters_boys" min="0" required />
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="button delete" onclick="closeEditModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="button">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(data) {
            document.getElementById('edit_P_id').value = data.P_id;
            document.getElementById('edit_female_reception').value = data.female_reception;
            document.getElementById('edit_male_reception').value = data.male_reception;
            document.getElementById('edit_grade1_girls').value = data.grade1_girls;
            document.getElementById('edit_grade1_boys').value = data.grade1_boys;
            document.getElementById('edit_grade2_girls').value = data.grade2_girls;
            document.getElementById('edit_grade2_boys').value = data.grade2_boys;
            document.getElementById('edit_grade3_girls').value = data.grade3_girls;
            document.getElementById('edit_grade3_boys').value = data.grade3_boys;
            document.getElementById('edit_grade4_girls').value = data.grade4_girls;
            document.getElementById('edit_grade4_boys').value = data.grade4_boys;
            document.getElementById('edit_grade5_girls').value = data.grade5_girls;
            document.getElementById('edit_grade5_boys').value = data.grade5_boys;
            document.getElementById('edit_grade6_girls').value = data.grade6_girls;
            document.getElementById('edit_grade6_boys').value = data.grade6_boys;
            document.getElementById('edit_grade7_girls').value = data.grade7_girls;
            document.getElementById('edit_grade7_boys').value = data.grade7_boys;
            document.getElementById('edit_repeaters_girls').value = data.repeaters_girls;
            document.getElementById('edit_repeaters_boys').value = data.repeaters_boys;

            document.getElementById('editModal').style.display = "block";
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
