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

// Retrieve electricity infrastructure data for high schools only
$sql = "
    SELECT ei.*, s.registration_number 
    FROM electricity_infrastructure ei
    JOIN schools s ON ei.school_id = s.school_id
    WHERE s.typeofschool = 'Public' 
      AND s.cluster IS NOT NULL 
      AND s.centre IS NULL
      AND ei.id IN (
          SELECT MAX(id) 
          FROM electricity_infrastructure 
          WHERE school_id IN (
              SELECT school_id 
              FROM schools 
              WHERE cluster IS NOT NULL AND centre IS NULL
          )
          GROUP BY school_id
      )
    ORDER BY s.school_name
";

$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    $infrastructureData = [];
    while ($row = $result->fetch_assoc()) {
        $infrastructureData[] = $row;
    }
} else {
    $infrastructureData = [];
}

// Close the connection
$conn->close();

// Function to download data as Excel
function downloadExcel($data) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"electricity_infrastructure_data.xls\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Output the column headings
    echo "ID\tRegistration Number\tSchool Name\tCluster\tHas Electricity\tSource\tChallenges\tMitigations\tAdditional Info\n";

    // Output the data
    foreach ($data as $row) {
        echo implode("\t", [
            $row['id'],
            $row['registration_number'],
            $row['school_name'],
            $row['cluster'],
            $row['has_electricity'],
            $row['source'],
            $row['challenges'],
            $row['mitigations'],
            $row['additional_info']
        ]) . "\n";
    }
    exit();
}

// Check if download is requested
if (isset($_POST['download'])) {
    downloadExcel($infrastructureData);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity Infrastructure Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
            <h1>Electricity Infrastructure Management System</h1>
            <p>High Schools with Electricity Infrastructure Data</p>
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
                        <th>Has Electricity</th>
                        <th>Source</th>
                        <th>Challenges</th>
                        <th>Mitigations</th>
                        <th>Additional Info</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($infrastructureData)): ?>
                        <?php foreach ($infrastructureData as $infrastructure): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($infrastructure['id']); ?></td>
                                <td><?php echo htmlspecialchars($infrastructure['registration_number']); ?></td>
                                <td><?php echo htmlspecialchars($infrastructure['school_name']); ?></td>
                                <td><?php echo htmlspecialchars($infrastructure['cluster']); ?></td>
                                <td><?php echo htmlspecialchars($infrastructure['has_electricity']); ?></td>
                                <td><?php echo htmlspecialchars($infrastructure['source']); ?></td>
                                <td><?php echo htmlspecialchars($infrastructure['challenges']); ?></td>
                                <td><?php echo htmlspecialchars($infrastructure['mitigations']); ?></td>
                                <td><?php echo htmlspecialchars($infrastructure['additional_info']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="no-data">No data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
