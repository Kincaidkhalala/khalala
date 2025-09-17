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

// Retrieve the most recent additionaltoilets record per school_id
// where centre IS NOT NULL and not empty, and cluster IS NULL or empty
$sql = "
    SELECT ac_latest.*, s.registration_number
    FROM (
        SELECT school_id, MAX(id) AS max_id
        FROM additionaltoilets
        WHERE centre IS NOT NULL AND centre <> ''
          AND (cluster IS NULL OR cluster = '')
        GROUP BY school_id
    ) latest
    INNER JOIN additionaltoilets ac_latest ON ac_latest.school_id = latest.school_id AND ac_latest.id = latest.max_id
    INNER JOIN schools s ON ac_latest.school_id = s.school_id
    ORDER BY ac_latest.school_name
";

$result = $conn->query($sql);

// Check if there are results
if ($result && $result->num_rows > 0) {
    $toiletsData = [];
    while ($row = $result->fetch_assoc()) {
        $toiletsData[] = $row;
    }
} else {
    $toiletsData = [];
}

// Close the connection
$conn->close();

// Function to download data as Excel
function downloadExcel($data) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"additional_toilets_data_filtered_latest.xls\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Output the column headings
    echo "ID\tRegistration Number\tSchool Name\tCentre\tCurrent Enrolment\tAdditional Latrines Needed\tLatrine Groups\tNumber of Latrines\tRequests Made\tSummary\tCreated At\n";

    // Output the data
    foreach ($data as $row) {
        echo implode("\t", [
            $row['id'],
            $row['registration_number'],
            $row['school_name'],
            $row['centre'],
            $row['current_enrolment'],
            $row['additional_latrines_needed'],
            $row['latrine_groups'],
            $row['number_of_latrines'],
            $row['requests_made'],
            $row['summary'],
            $row['created_at']
        ]) . "\n";
    }
    exit();
}

// Check if download is requested
if (isset($_POST['download'])) {
    downloadExcel($toiletsData);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Additional Toilets Data</title>
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
            padding: 10px 0;
            display: flex;
            justify-content: center;
            gap: 20px;
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

        .btn-secondary {
            background: #e74c3c;
            color: white;
        }

        .btn-secondary:hover {
            background: #c0392b;
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
            <h1>Additional Toilets Data</h1>
            <p>Information on Additional Toilets in Schools</p>
            <div class="controls">
                <form method="POST" style="display: inline;">
                    <button type="submit" name="download" class="btn btn-primary">Download as Excel</button>
                </form>
                <a href="Adminlinks.php" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Registration Number</th>
                        <th>School Name</th>
                        <th>Centre</th>
                        <th>Current Enrolment</th>
                        <th>Additional Latrines Needed</th>
                        <th>Latrine Groups</th>
                        <th>Number of Latrines</th>
                        <th>Requests Made</th>
                        <th>Summary</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($toiletsData)): ?>
                        <?php foreach ($toiletsData as $toilet): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($toilet['id']); ?></td>
                                <td><?php echo htmlspecialchars($toilet['registration_number']); ?></td>
                                <td><?php echo htmlspecialchars($toilet['school_name']); ?></td>
                                <td><?php echo htmlspecialchars($toilet['centre']); ?></td>
                                <td><?php echo htmlspecialchars($toilet['current_enrolment']); ?></td>
                                <td><?php echo htmlspecialchars($toilet['additional_latrines_needed']); ?></td>
                                <td><?php echo htmlspecialchars($toilet['latrine_groups']); ?></td>
                                <td><?php echo htmlspecialchars($toilet['number_of_latrines']); ?></td>
                                <td><?php echo htmlspecialchars($toilet['requests_made']); ?></td>
                                <td><?php echo htmlspecialchars($toilet['summary']); ?></td>
                                <td><?php echo htmlspecialchars($toilet['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="no-data">No data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
