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

    // Retrieve total students from the most recent primary_enrollment per school
    $sqlStudents = "
        SELECT SUM(pe.overall_total) AS total_students FROM primary_enrollment pe
        INNER JOIN (
            SELECT school_id, MAX(created_at) AS max_created_at
            FROM primary_enrollment
            WHERE school_id IN (SELECT school_id FROM schools WHERE user_id = :user_id)
            GROUP BY school_id
        ) latest ON pe.school_id = latest.school_id AND pe.created_at = latest.max_created_at
    ";
    $stmtStudents = $pdo->prepare($sqlStudents);
    $stmtStudents->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtStudents->execute();
    $totalStudents = $stmtStudents->fetchColumn();

    // Retrieve principal surname (limit 1)
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal's Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
 /* Base body and font */
body {
    font-family: 'Inter', sans-serif;
    background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1920&q=80');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    color: #374151;
    transition: margin-left 0.3s;
    overflow-x: hidden;
}

/* Fixed header */
.header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: white;
    color: rgb(102, 93, 230);
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    z-index: 1000;
}

.header:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
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
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #4a5568;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
}

/* Toggle button */
.toggle-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
    position: fixed;
    left: 12px;
    top: 12px;
    z-index: 1201; /* Above sidebar overlay */
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: background-color 0.3s ease;
}

.toggle-btn:hover {
    background-color: #3a9d3a;
}

.toggle-btn i {
    font-size: 1.2rem;
}

/* Sidebar - Desktop */
.sidebar {
    width: 250px;
    background-color: #4CAF50;
    height: 100vh;
    position: fixed;
    top: 72px; /* height of header */
    left: 0;
    padding-top: 20px;
    color: white;
    transition: transform 0.3s ease;
    transform: translateX(0);
    overflow-y: auto;
    z-index: 1100;
    box-shadow: 2px 0 8px rgba(0,0,0,0.1);
}

/* Sidebar hidden on desktop */
.sidebar.hidden {
    transform: translateX(-100%);
}

/* Sidebar links */
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

/* Content area */
.content {
    margin-left: 260px; /* Space for the sidebar */
    padding: 20px;
    transition: margin-left 0.3s;
}

.content.shifted {
    margin-left: 10px; /* Adjust margin when sidebar is hidden */
}

/* Quick stats */
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
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: all 0.3s ease;
    height: 100px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    background-color: #f1f8e9;
}

/* Dashboard grid */
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
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    transform: scale(1);
    position: relative;
}

.dashboard-section:hover {
    transform: scale(1.02) rotate(0.5deg);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
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

/* Buttons */
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
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
    font-size: 1.1rem;
    letter-spacing: 0.5px;
}

.btn-primary:after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.3);
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
    background-color: #0ec099ff;
    color: #ffffffff;
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
    background-color: #f35252ff;
    transform: translateY(-1px);
}

/* Bounce animation */
@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

/* Footer */
footer {
    background-color: #2d3748;
    color: white;
    padding: 2rem 1rem;
    text-align: center;
}

/* Responsive styles for mobile */
@media (max-width: 768px) {
    /* Sidebar covers full width and overlays content */
    .sidebar {
        width: 100%;
        height: calc(100vh - 56px); /* full height minus header */
        max-height: none;
        position: fixed;
        top: 56px; /* below header */
        left: 0;
        padding: 20px 0;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        background-color: #4CAF50;
        transform: translateY(-100%);
        transition: transform 0.3s ease;
        overflow-y: auto;
        z-index: 1100;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Show sidebar when active */
    .sidebar.active {
        transform: translateY(0);
    }

    /* Hide sidebar when not active */
    .sidebar.hidden {
        transform: translateY(-100%);
    }

    /* Sidebar links styling */
    .sidebar a {
        padding: 15px 30px;
        font-size: 1.2rem;
        border-bottom: 1px solid rgba(255,255,255,0.3);
        width: 100%;
        text-align: center;
        font-weight: 700;
        transition: background-color 0.3s;
    }

    .sidebar a:last-child {
        border-bottom: none;
    }

    .sidebar a:hover {
        background-color: #45a049;
    }

    .sidebar h2 {
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
        font-weight: 800;
        width: 100%;
        text-align: center;
        padding: 0 20px;
    }

    /* Content area full width with padding top for fixed header */
    .content {
        margin-left: 0;
        padding: 100px 20px 20px 20px;
        transition: none;
    }

    .content.shifted {
        margin-left: 0;
    }

    /* Toggle button fixed top-left */
    .toggle-btn {
        position: fixed;
        top: 12px;
        left: 12px;
        z-index: 1201;
    }

    /* Overlay background when sidebar active */
    body.sidebar-open::before {
        content: "";
        position: fixed;
        top: 56px; /* below header */
        left: 0;
        width: 100%;
        height: calc(100% - 56px);
        background: rgba(0, 0, 0, 0.5);
        z-index: 1099;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    /* Hide scrollbar on overlay */
    body.sidebar-open {
        overflow: hidden;
    }
}

    </style>
</head>

<body class="bg-gray-100" style="opacity: 0; transition: opacity 0.5s ease">
    <script>
        window.addEventListener('load', function () {
            document.body.style.opacity = "1";
        });

        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const content = document.querySelector('.content');
            sidebar.classList.toggle('hidden');
            content.classList.toggle('shifted');
        }
    </script>

    <header class="header">
        <div class="header-title">Principal's Dashboard</div>
        <div class="user-profile">
            <div class="avatar">
                <?php
                // Display the first letter of the user's name as an avatar
                if (isset($_SESSION['username'])) {
                    echo strtoupper(substr($_SESSION['username'], 0, 1));
                } else {
                    echo '?';
                }
                ?>
            </div>
            <span><?php echo isset($_SESSION['principal_surname']) ? htmlspecialchars($_SESSION['principal_surname']) : 'Unknown'; ?></span>    
        </div>
        <div>
            
        </div>
    </header>

    <button class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar">
        <h2>Menu</h2>
        <a href="update-ifrastructure.php">View Infrastructure</a>
        <a href="edit-AdditionalClassRooms.php">view Additional Classrooms</a>
        <a href="edit-AdditionalToilets.php">view Additional Toilets</a>
        <a href="edit-electricity_infrastructure.php">view Electricity Infrastructure</a>
        <a href="edit-internet_infrastructure.php">Internet Infrastructure</a>
        <a href="update_school.php">Edit Profile</a>
        <a href="logout.php" class="btn-secondary"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
    </div>

    <div class="content">
        <div class="quick-stats">
            <div class="stat-card">
                <div class="stat-value"><?php echo isset($totalStudents) ? $totalStudents : 0; ?></div>
                <div class="stat-label">Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo isset($totalTeachers) ? $totalTeachers : 0; ?></div>
                <div class="stat-label">Teachers</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-section">
                <h2>Profile</h2>
                <button class="btn-primary" onclick="redirectTo('update_school.php')">View Profile</button>
                <button class="btn-primary" onclick="redirectTo('update_school.php')">Edit Profile</button>
            </div>

            <div class="dashboard-section">
                <h2>Infrastructure</h2>
                <h2>Does your school require?</h2>
                <button class="btn-primary" onclick="redirectTo('infrastructure.php')">Infrastructure</button>            
                <button class="btn-primary" onclick="redirectTo('pAdditionalClassRooms.php')">Additional-Classrooms</button>          
                <button class="btn-primary" onclick="redirectTo('pAdditionalToilets.php')">Additional Toilets</button>

            </div>

            <div class="dashboard-section">
                <h2>Utilities</h2>
                <h2>Does your school have?</h2>
                <button class="btn-primary" onclick="redirectTo('pwithoutElecticity.php')">Electricity</button>
                <button class="btn-primary" onclick="redirectTo('pwithout-Water.php')">Water</button>
                <button class="btn-primary" onclick="redirectTo('pwithout-internet.php')">Internet</button>
            </div>

            <div class="dashboard-section">
                <h2>Enrollment</h2>
                <button class="btn-primary" onclick="redirectTo('primaryenrolment.php')">New Enrollment</button>
                <button class="btn-primary" onclick="redirectTo('retrive-primary-enroll.php')">View Enrollments</button>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; 2025 Ministry of Education and Training. All rights reserved.</p>
            <p>Contact: <a href="mailto:info@education.gov" class="text-blue-400">info@education.gov</a></p>
        </div>
    </footer>

    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
// Fade in body on load
window.addEventListener('load', function () {
    document.body.style.opacity = "1";
});

function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const content = document.querySelector('.content');
    const body = document.body;

    if (window.innerWidth <= 768) {
        // Mobile: toggle active class and overlay
        if (sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            body.classList.remove('sidebar-open');
        } else {
            sidebar.classList.add('active');
            body.classList.add('sidebar-open');
        }
    } else {
        // Desktop: toggle hidden class and content margin
        sidebar.classList.toggle('hidden');
        content.classList.toggle('shifted');
    }
}

// Close sidebar if clicking outside on mobile
document.addEventListener('click', function (event) {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.toggle-btn');
    const body = document.body;

    if (window.innerWidth <= 768) {
        if (sidebar.classList.contains('active') &&
            !sidebar.contains(event.target) &&
            !toggleBtn.contains(event.target)) {
            sidebar.classList.remove('active');
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
