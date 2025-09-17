<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "moet1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/**
 * Retrieve the most recent additionalclassrooms record per school_id
 * where the school's cluster is NOT NULL and NOT empty.
 * "Most recent" is determined by the highest id per school_id.
 */
$sql = "
    SELECT ac.*
         , s.registration_number
         , s.school_name
         , s.cluster
    FROM additionalclassrooms ac
    INNER JOIN schools s ON ac.school_id = s.school_id
    INNER JOIN (
        SELECT school_id, MAX(id) AS max_id
        FROM additionalclassrooms
        GROUP BY school_id
    ) latest ON ac.school_id = latest.school_id AND ac.id = latest.max_id
    WHERE s.cluster IS NOT NULL AND s.cluster <> ''
    ORDER BY s.school_name ASC
";

$result = $conn->query($sql);

$classroomsData = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classroomsData[] = $row;
    }
}

// Close the connection
$conn->close();

// Function to download data as Excel
function downloadExcel($data) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"additional_classrooms_data.xls\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Output the column headings
    echo "ID\tRegistration Number\tSchool Name\tCluster\tCurrent Enrolment\tRequire Classrooms\tInfrastructure Summary\tRequests Made\tGrades\tClassroom Counts\n";

    // Output the data
    foreach ($data as $row) {
        echo implode("\t", [
            $row['id'],
            $row['registration_number'],
            $row['school_name'],
            $row['cluster'],
            $row['current_enrolment'],
            $row['require_classrooms'],
            $row['infrastructure_summary'],
            $row['requests_made'],
            $row['grades'],
            $row['classroom_counts']
        ]) . "\n";
    }
    exit();
}

// Check if download is requested
if (isset($_POST['download'])) {
    downloadExcel($classroomsData);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Classroom Infrastructure Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <style>
        /* CSS styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .controls {
            padding: 20px 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .table-container {
            overflow-x: auto;
            max-height: 70vh;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        thead {
            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        th {
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        tbody tr {
            transition: all 0.3s ease;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        tbody tr:hover {
            background: #e3f2fd;
            transform: scale(1.01);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            border-right: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Classroom Infrastructure Management System</h1>
            <p>Most Recent Additional Classrooms Data per School (Cluster not empty)</p>
        </div>

        <div class="controls">
            <form method="POST" class="mb-4 text-center">
                <button type="submit" name="download" class="btn btn-primary">Download as Excel</button>
            </form>
            <a href="Adminlinks.php" class="btn btn-primary">Back to Admin Dashboard</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Registration Number</th>
                        <th>School Name</th>
                        <th>Cluster</th>
                        <th>Current Enrolment</th>
                        <th>Require Classrooms</th>
                        <th>Infrastructure Summary</th>
                        <th>Requests Made</th>
                        <th>Grades</th>
                        <th>Classroom Counts</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($classroomsData)): ?>
                        <?php foreach ($classroomsData as $classroom): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($classroom['id']); ?></td>
                                <td><?php echo htmlspecialchars($classroom['registration_number']); ?></td>
                                <td><?php echo htmlspecialchars($classroom['school_name']); ?></td>
                                <td><?php echo htmlspecialchars($classroom['cluster']); ?></td>
                                <td><?php echo htmlspecialchars($classroom['current_enrolment']); ?></td>
                                <td><?php echo htmlspecialchars($classroom['require_classrooms']); ?></td>
                                <td><?php echo htmlspecialchars($classroom['infrastructure_summary']); ?></td>
                                <td><?php echo htmlspecialchars($classroom['requests_made']); ?></td>
                                <td><?php echo htmlspecialchars($classroom['grades']); ?></td>
                                <td><?php echo htmlspecialchars($classroom['classroom_counts']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="no-data">No data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
