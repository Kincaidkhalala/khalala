<?php
session_start();

// Database connection parameters
$host = 'localhost';
$db = 'moet1';
$user = 'root'; 
$pass = ''; 
$charset = 'utf8mb4';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Initialize search results
    $searchResults = [];
    $searchQuery = '';

    // Check if a search has been made
    if (isset($_POST['search'])) {
        $searchQuery = $_POST['search_query'];
        $sqlSearch = "
            SELECT * FROM schools 
            WHERE school_name LIKE :search OR 
                  principal_name LIKE :search OR 
                  principal_surname LIKE :search
        ";
        $stmtSearch = $pdo->prepare($sqlSearch);
        $stmtSearch->execute(['search' => '%' . $searchQuery . '%']);
        $searchResults = $stmtSearch->fetchAll(PDO::FETCH_ASSOC);
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Search Schools</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
    font-family: 'Poppins', sans-serif;
    background-color: #f9fafb;
    color: #374151;

    background-image: url('https://images.unsplash.com/photo-1497493292307-31c376b6e479?auto=format&fit=crop&w=1470&q=80');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    background-attachment: fixed;
}


        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 2rem;
            margin: 0;
        }

        .header img {
            width: 100px; /* Logo size */
            height: auto;
            border-radius: 10px;
        }

        .search-container {
            display: flex;
            align-items: center;
            margin-top: 20px;
        }

        .search-container input {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            width: 300px; /* Increased width */
            margin-right: 10px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px; /* Increased font size */
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
            background: #e3f2fd;
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
            background-color: #b2b3ecff;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        i {
  font-size: 24px;
  color: #333;
}

    </style>
</head>

<body>
    <div class="header">
        <img src="moet.png" alt="Ministry of Education">
        <h1>Admin Dashboard</h1>
        <div>
            <a href="Admin.php" class="btn btn-primary">BACK</a>
        </div>
    </div>

    <div class="container mx-auto p-6">
        <div class="search-container">
            <form method="POST" class="flex">
                <input type="text" name="search_query" placeholder="Enter school or principal name" required  > <i class="fas fa-search"></i>
                <button type="submit" name="search" class="btn btn-primary">Search</button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Principal Name</th>
                        <th>Principal Surname</th>
                        <th>School Name</th>
                        <th>Cluster</th>
                        <th>Phone Number</th>
                        <th>Email Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($searchResults)): ?>
                        <?php foreach ($searchResults as $school): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($school['registration_number']); ?></td>
                                <td><?php echo htmlspecialchars($school['principal_name']); ?></td>
                                <td><?php echo htmlspecialchars($school['principal_surname']); ?></td>
                                <td><?php echo htmlspecialchars($school['school_name']); ?></td>
                                <td><?php echo htmlspecialchars($school['cluster']); ?></td>
                                <td><?php echo htmlspecialchars($school['phone_number']); ?></td>
                                <td><?php echo htmlspecialchars($school['email_address']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="no-data">No data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
