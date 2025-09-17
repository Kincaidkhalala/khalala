<?php
session_start();

// Show success message if set
if (isset($_SESSION['success_message'])) {
    echo '<script>alert("' . htmlspecialchars($_SESSION['success_message']) . '");</script>';
    unset($_SESSION['success_message']);
}

// Database connection parameters
$host = 'localhost';
$db = 'moet1';
$user = 'root'; // Update as needed
$pass = '';     // Update as needed
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User  not logged in");
    }

    $user_id = $_SESSION['user_id'];

    // Total teachers sum from schools for this user
    $sqlTeachers = "
        SELECT SUM(total_teachers) AS total_teachers 
        FROM schools 
        WHERE user_id = :user_id
    ";
    $stmtTeachers = $pdo->prepare($sqlTeachers);
    $stmtTeachers->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtTeachers->execute();
    $totalTeachers = $stmtTeachers->fetchColumn();

    // Total students from most recent enrollment per school
    $sqlStudents = "
        SELECT SUM(latest.total_students) AS total_students FROM (
            SELECT hse.total_students
            FROM high_school_enrollment hse
            INNER JOIN (
                SELECT school_id, MAX(entry_date) AS max_date
                FROM high_school_enrollment
                WHERE school_id IN (SELECT school_id FROM schools WHERE user_id = :user_id)
                GROUP BY school_id
            ) latest_enrollment ON hse.school_id = latest_enrollment.school_id AND hse.entry_date = latest_enrollment.max_date
        ) AS latest
    ";
    $stmtStudents = $pdo->prepare($sqlStudents);
    $stmtStudents->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtStudents->execute();
    $totalStudents = $stmtStudents->fetchColumn();

    // Principal surname (limit 1)
    $sqlSurname = "
        SELECT principal_surname 
        FROM schools 
        WHERE user_id = :user_id
        LIMIT 1
    ";
    $stmtSurname = $pdo->prepare($sqlSurname);
    $stmtSurname->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtSurname->execute();
    $_SESSION['principal_surname'] = $stmtSurname->fetchColumn();

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
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Principal's Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
<style>
    body {
        font-family: 'Inter', sans-serif;
        background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1920&q=80');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        color: #374151;
        transition: margin-left 0.3s ease;
        overflow-x: hidden;
    }
    .header {
        position: fixed;
        top: 0; left: 0; width: 100%;
        background-color: white;
        color: rgb(102, 93, 230);
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 1000;
    }
    .header:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 10px rgba(0,0,0,0.15);
    }
    .header-title {
        font-size: 1.5rem;
        font-weight: 700;
    }
    .user-profile {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .avatar {
        width: 40px; height: 40px;
        border-radius: 50%;
        background-color: #4a5568;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }
    .sidebar {
        width: 250px;
        background-color: #4CAF50;
        height: 100vh;
        position: fixed;
        top: 72px;
        left: 0;
        padding-top: 20px;
        color: white;
        overflow-y: auto;
        box-shadow: 2px 0 8px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        transform: translateX(-100%);
        z-index: 1100;
    }
    .sidebar.visible {
        transform: translateX(0);
    }
    .sidebar a {
        padding: 10px 15px;
        text-decoration: none;
        font-weight: 600;
        color: white;
        display: block;
        transition: background-color 0.3s;
    }
    .sidebar a:hover {
        background-color: #45a049;
    }
    .sidebar h2 {
        text-align: center;
        margin-bottom: 20px;
        font-weight: 700;
        font-size: 1.25rem;
    }
    .content {
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.3s ease;
    }
    .content.shifted {
        margin-left: 250px;
    }
    .toggle-btn {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
        position: fixed;
        left: 12px;
        top: 12px;
        z-index: 1201;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        transition: background-color 0.3s ease;
    }
    .toggle-btn:hover {
        background-color: #3a9d3a;
    }
    .toggle-btn i {
        font-size: 1.2rem;
    }
    .quick-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-top: 5rem;
        padding: 0 1rem;
        justify-items: center;
        color: #1a365d;
        background-color: #e0f2fe;
        border-radius: 0.5rem;
    }
    .stat-card {
        background-color: white;
        border-radius: 0.5rem;
        padding: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        text-align: center;
        transition: all 0.3s ease;
        height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        background-color: #f1f8e9;
    }
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        padding: 2rem;
        justify-items: center;
    }
    .dashboard-section {
        background-color: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        transform: scale(1);
        position: relative;
    }
    .dashboard-section:hover {
        transform: scale(1.02) rotate(0.5deg);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        background-color: #f0f8ff;
        animation: bounce 0.5s ease;
    }
    .dashboard-section h2 {
        color: #1a365d;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0.5rem;
    }
    .btn-primary {
        background-color: #4CAF50;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        width: 100%;
        text-align: center;
        margin-bottom: 0.75rem;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
    }
    .btn-primary:after {
        content: "";
        position: absolute;
        top: 0; left: 0;
        width: 0; height: 100%;
        background-color: rgba(255,255,255,0.3);
        transition: width 0.3s ease;
    }
    .btn-primary:hover:after {
        width: 100%;
    }
    .btn-primary:hover {
        background-color: #3182ce;
        transform: translateY(-1px);
    }
    .btn-secondary {
        background-color: #d1ababff;
        color: #4a5568;
        font-weight: 600;
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        width: 100%;
        text-align: center;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    .btn-secondary:hover {
        background-color: #b98989ff;
        transform: translateY(-1px);
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    @media (max-width: 768px) {
        .sidebar {
            top: 56px;
            height: calc(100vh - 56px);
            width: 100%;
            transform: translateY(-100%);
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }
        .sidebar.visible {
            transform: translateY(0);
        }
        .content {
            margin-left: 0 !important;
            padding: 100px 20px 20px 20px;
        }
        .toggle-btn {
            top: 12px;
            left: 12px;
            z-index: 1201;
        }
        body.sidebar-open::before {
            content: "";
            position: fixed;
            top: 56px;
            left: 0;
            width: 100%;
            height: calc(100% - 56px);
            background: rgba(0, 0, 0, 0.5);
            z-index: 1099;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        body.sidebar-open {
            overflow: hidden;
        }
    }
</style>
</head>
<body>
<header class="header">
    <div class="header-title">Principal's Dashboard</div>
    <div class="user-profile">
        <div class="avatar" aria-label="User  avatar">
            <?php
            if (isset($_SESSION['username'])) {
                echo strtoupper(substr($_SESSION['username'], 0, 1));
            } else {
                echo '?';
            }
            ?>
        </div>
        <span><?php echo isset($_SESSION['principal_surname']) ? htmlspecialchars($_SESSION['principal_surname']) : 'Unknown'; ?></span>
    </div>
</header>

<button class="toggle-btn" aria-label="Toggle menu" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<nav class="sidebar" id="sidebar" aria-label="Sidebar navigation">
    <h2>Menu</h2>
    <a href="update-ifrastructure.php">View Infrastructure</a>
    <a href="edit-AdditionalClassRooms.php">Additional Classrooms</a>
    <a href="edit-AdditionalToilets.php">Additional Toilets</a>
    <a href="edit-electricity_infrastructure.php">Electricity Infrastructure</a>
    <a href="edit-internet_infrastructure.php">Internet Infrastructure</a>
    <a href="update_school.php">Edit Profile</a>
    <a href="logout.php" class="btn-secondary"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
</nav>

<main class="content" id="content" tabindex="-1">
    <div class="quick-stats" role="region" aria-label="Quick statistics">
        <div class="stat-card" tabindex="0" aria-label="Total students">
            <div class="stat-value" aria-live="polite" aria-atomic="true"><?php echo (int)($totalStudents ?? 0); ?></div>
            <div class="stat-label">Students</div>
        </div>
        <div class="stat-card" tabindex="0" aria-label="Total teachers">
            <div class="stat-value" aria-live="polite" aria-atomic="true"><?php echo (int)($totalTeachers ?? 0); ?></div>
            <div class="stat-label">Teachers</div>
        </div>
    </div>

    <div class="dashboard-grid">
        <section class="dashboard-section" tabindex="0" aria-label="Profile section">
            <h2>Profile</h2>
            <button class="btn-primary" onclick="redirectTo('update_school.php')" aria-label="View Profile">View Profile</button>
            <button class="btn-primary" onclick="redirectTo('update_school.php')" aria-label="Edit Profile">Edit Profile</button>
        </section>

        <section class="dashboard-section" tabindex="0" aria-label="Infrastructure section">
            <h2>Infrastructure</h2>
            <h3>Does your school require?</h3>
            <button class="btn-primary" onclick="redirectTo('infrastructure.php')" aria-label="Infrastructure">Infrastructure</button>
            <button class="btn-primary" onclick="redirectTo('AdditionalClassRooms.php')" aria-label="Additional Classrooms">Additional-Classrooms</button>
            <button class="btn-primary" onclick="redirectTo('AdditionalToilets.php')" aria-label="Additional Toilets">Additional Toilets</button>
        </section>

        <section class="dashboard-section" tabindex="0" aria-label="Utilities section">
            <h2>Utilities</h2>
            <h3>Does your school have?</h3>
            <button class="btn-primary" onclick="redirectTo('withoutElecticity.php')" aria-label="Electricity">Electricity</button>
            <button class="btn-primary" onclick="redirectTo('without-Water.php')" aria-label="Water">Water</button>
            <button class="btn-primary" onclick="redirectTo('without-internet.php')" aria-label="Internet">Internet</button>
        </section>

        <section class="dashboard-section" tabindex="0" aria-label="Enrollment section">
            <h2>Enrollment</h2>
            <button class="btn-primary" onclick="redirectTo('Highenrolment.php')" aria-label="New Enrollment">New Enrollment</button>
            <button class="btn-primary" onclick="redirectTo('retrivehigh-school-enrollment.php')" aria-label="View Enrollments">View Enrollments</button>
        </section>
    </div>
</main>

<footer class="bg-gray-800 text-white py-8" role="contentinfo">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p>&copy; 2025 Ministry of Education and Training. All rights reserved.</p>
        <p>Contact: <a href="mailto:info@education.gov" class="text-blue-400">info@education.gov</a></p>
    </div>
</footer>

<script>
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const body = document.body;

    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            if (sidebar.classList.contains('visible')) {
                sidebar.classList.remove('visible');
                body.classList.remove('sidebar-open');
            } else {
                sidebar.classList.add('visible');
                body.classList.add('sidebar-open');
            }
        } else {
            sidebar.classList.toggle('visible');
            content.classList.toggle('shifted');
        }
    }

        // Close sidebar on mobile if clicking outside sidebar or toggle button
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                if (sidebar.classList.contains('visible') &&
                    !sidebar.contains(event.target) &&
                    !event.target.closest('.toggle-btn')) {
                    sidebar.classList.remove('visible');
                    body.classList.remove('sidebar-open');
                }
            }
        });

        function redirectTo(page) {
            window.location.href = page;
        }
    </script>

</body>

</html>
