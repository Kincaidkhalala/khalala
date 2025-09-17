<?php
session_start();

// Database connection setup
$dbConfig = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'moet1'
];

// Establish database connection with error handling
try {
    $conn = new mysqli($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database']);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die($e->getMessage());
}

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/**
 * Retrieve the most recent additionalclassrooms record per school_id
 * where centre IS NOT NULL and NOT empty AND cluster IS NULL or empty.
 * "Most recent" is determined by the highest id per school_id.
 */
$query = "
    SELECT ac.*
         , s.registration_number
    FROM additionalclassrooms ac
    INNER JOIN schools s ON ac.school_id = s.school_id
    INNER JOIN (
        SELECT school_id, MAX(id) AS max_id
        FROM additionalclassrooms
        WHERE centre IS NOT NULL AND centre <> '' AND (cluster IS NULL OR cluster = '')
        GROUP BY school_id
    ) latest ON ac.school_id = latest.school_id AND ac.id = latest.max_id
    WHERE ac.centre IS NOT NULL AND ac.centre <> '' AND (ac.cluster IS NULL OR ac.cluster = '')
    ORDER BY ac.school_name ASC
";

$result = $conn->query($query);
$classroomRecords = $result && $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Generate Excel file
function exportToExcel($data) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="primary_classroom_data_filtered.xls"');
    
    $columns = [
        'Registration Number',
        'Region',
        'School Name',
        'Centre',
        'Current Enrolment',
        'Require Classrooms',
        'Infrastructure Summary',
        'Requests Made',
        'Grades',
        'Classroom Counts'
    ];

    echo implode("\t", $columns) . "\n";

    foreach ($data as $record) {
        $row = [
            $record['registration_number'],
            $record['region'],
            $record['school_name'],
            $record['centre'],
            $record['current_enrolment'],
            $record['require_classrooms'],
            $record['infrastructure_summary'],
            $record['requests_made'],
            $record['grades'],
            $record['classroom_counts']
        ];
        echo implode("\t", $row) . "\n";
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download'])) {
    exportToExcel($classroomRecords);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Primary Classroom Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .dashboard-card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .table-container {
            max-height: 70vh;
            overflow-y: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background-color: #2c3e50;
            color: white;
            position: sticky;
            top: 0;
            text-align: left;
            padding: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        td {
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        tr:hover {
            background-color: #e5e7eb;
        }
        .action-btn {
            transition: all 0.2s ease;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
        }
        .action-btn:hover {
            transform: translateY(-1px);
        }
        .btn-download {
            background-color: #3b82f6;
            color: white;
        }
        .btn-download:hover {
            background-color: #2563eb;
        }
        .btn-back {
            background-color: #6b7280;
            color: white;
        }
        .btn-back:hover {
            background-color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="max-w-7xl mx-auto dashboard-card">
        <div class="bg-gradient-to-r from-blue-800 to-blue-600 text-white p-8 text-center">
            <h1 class="text-3xl font-bold mb-2">Primary Classroom Management</h1>
            <p class="opacity-90">Most Recent Additional Classrooms Data per School (Centre not empty, Cluster null or empty)</p>
        </div>

        <div class="flex justify-between items-center p-6 bg-gray-50 border-b">
            <form method="post">
                <button type="submit" name="download" class="action-btn btn-download">
                    Export to Excel
                </button>
            </form>
            <a href="Adminlinks.php" class="action-btn btn-back">
                Back
            </a>
        </div>

        <div class="table-container p-4">
            <table>
                <thead>
                    <tr>
                        <th>Registration</th>
                        <th>Region</th>
                        <th>School</th>
                        <th>Centre</th>
                        <th>Enrollment</th>
                        <th>Needs Classrooms</th>
                        <th>Infrastructure</th>
                        <th>Requests</th>
                        <th>Grades</th>
                        <th>Classrooms' count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($classroomRecords)): ?>
                        <?php foreach ($classroomRecords as $record): ?>
                        <tr>
                            <td><?= htmlspecialchars($record['registration_number']) ?></td>
                            <td><?= htmlspecialchars($record['region']) ?></td>
                            <td><?= htmlspecialchars($record['school_name']) ?></td>
                            <td><?= htmlspecialchars($record['centre']) ?></td>
                            <td><?= htmlspecialchars($record['current_enrolment']) ?></td>
                            <td><?= htmlspecialchars($record['require_classrooms']) ?></td>
                            <td><?= htmlspecialchars($record['infrastructure_summary']) ?></td>
                            <td><?= htmlspecialchars($record['requests_made']) ?></td>
                            <td><?= htmlspecialchars($record['grades']) ?></td>
                            <td><?= htmlspecialchars($record['classroom_counts']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="py-4 text-center text-gray-500">
                                No classroom records found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
