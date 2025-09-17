<?php
session_start();

// Database connection parameters
$host = 'localhost';
$db = 'moet1';
$user = 'root'; 
$pass = ''; 
$charset = 'utf8mb4';


// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch counts and teacher sums grouped by level
    $sql = "
        SELECT u.level,
               COUNT(*) AS total_schools,
               COALESCE(SUM(s.total_teachers), 0) AS total_teachers
        FROM users u
        LEFT JOIN schools s ON u.user_id = s.user_id
        WHERE u.level IN ('High', 'Primary', 'Pre')
        GROUP BY u.level
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize totals
    $totals = [
        'High' => ['schools' => 0, 'teachers' => 0],
        'Primary' => ['schools' => 0, 'teachers' => 0],
        'Pre' => ['schools' => 0, 'teachers' => 0],
    ];

    foreach ($data as $row) {
        $level = $row['level'];
        $totals[$level]['schools'] = (int)$row['total_schools'];
        $totals[$level]['teachers'] = (int)$row['total_teachers'];
    }

} catch (PDOException $e) {
    echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">';
    echo '<p>Database error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
    exit;
} catch (Exception $e) {
    echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">';
    echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth" >
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<style>
  /* Light mode variables */
  :root {
    --bg-color: #f9fafb;
    --bg-container: rgba(255, 255, 255, 0.95);
    --text-color: #1e293b;
    --text-color-light: #475569;
    --primary: #2563eb; /* Tailwind blue-600 */
    --primary-dark: #1e40af; /* Tailwind blue-800 */
    --header-bg: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
    --sidebar-bg: #2563eb;
    --card-bg: white;
    --card-shadow: rgba(0,0,0,0.1);
    --btn-bg: #e0837dff;
    --btn-bg-hover: #770202ff;
    --btn-primary-bg: #3498db;
    --btn-primary-bg-hover: #2980b9;
    --search-bg: rgba(255,255,255,0.95);
    --search-border: #3498db;
    --search-btn-bg: #3498db;
    --search-btn-bg-hover: #2980b9;
  }

  /* Dark mode variables */
  body.dark {
    --bg-color: #0f172a;
    --bg-container: rgba(15, 23, 42, 0.9);
    --text-color: #cbd5e1;
    --text-color-light: #94a3b8;
    --primary: #3b82f6; /* Tailwind blue-500 */
    --primary-dark: #2563eb; /* Tailwind blue-600 */
    --header-bg: linear-gradient(135deg, #1e293b 0%, #2563eb 100%);
    --sidebar-bg: #1e40af;
    --card-bg: #1e293b;
    --card-shadow: rgba(0,0,0,0.7);
    --btn-bg: #b45309;
    --btn-bg-hover: #78350f;
    --btn-primary-bg: #2563eb;
    --btn-primary-bg-hover: #1e40af;
    --search-bg: rgba(30, 41, 59, 0.9);
    --search-border: #3b82f6;
    --search-btn-bg: #2563eb;
    --search-btn-bg-hover: #1e40af;
  }

  /* General styles */
  body {
    font-family: 'Poppins', sans-serif;
    background: var(--bg-color) url('https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
    background-size: cover;
    min-height: 100vh;
    margin: 0;
    color: var(--text-color);
    transition: background-color 0.3s ease, color 0.3s ease;
  }
  #content-wrapper {
    background-color: var(--bg-container);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    transition: background-color 0.3s ease;
  }
  /* Header */
  .header {
    background: var(--header-bg);
    color: white;
    padding: 20px 1rem;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    z-index: 60;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-sizing: border-box;
  }
  .header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  .header h1 {
    font-size: 1.75rem;
    margin: 0;
    white-space: nowrap;
  }
  .header img {
    width: 60px;
    height: auto;
    border-radius: 10px;
  }

  /* Dark/Light mode toggle */
  .mode-toggle {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    user-select: none;
  }
  .mode-toggle input[type="checkbox"] {
    width: 40px;
    height: 20px;
    appearance: none;
    background: #cbd5e1;
    border-radius: 9999px;
    position: relative;
    cursor: pointer;
    outline: none;
    transition: background-color 0.3s ease;
  }
  .mode-toggle input[type="checkbox"]:checked {
    background: var(--primary);
  }
  .mode-toggle input[type="checkbox"]::before {
    content: "";
    position: absolute;
    top: 2px;
    left: 2px;
    width: 16px;
    height: 16px;
    background: white;
    border-radius: 9999px;
    transition: transform 0.3s ease;
    transform: translateX(0);
  }
  .mode-toggle input[type="checkbox"]:checked::before {
    transform: translateX(20px);
  }

  /* Buttons */
  .btn {
    background: var(--btn-bg);
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: background-color 0.3s ease;
    white-space: nowrap;
  }
  .btn:hover {
    background: var(--btn-bg-hover);
  }
  .btn-primary {
    background: var(--btn-primary-bg);
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: background-color 0.3s ease;
    white-space: nowrap;
  }
  .btn-primary:hover {
    background: var(--btn-primary-bg-hover);
  }

  /* Sidebar */
  .sidebar {
    background-color: var(--sidebar-bg);
    color: white;
    width: 250px;
    min-height: 100vh;
    padding: 1.5rem;
    position: fixed;
    top: 72px;
    left: 0;
    overflow-y: auto;
    transition: transform 0.3s ease;
    z-index: 50;
  }
  .sidebar.hidden {
    transform: translateX(-100%);
  }
  .sidebar h2 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
  }
  .sidebar a {
    display: block;
    padding: 0.75rem 1rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
    border-radius: 0.375rem;
    transition: background-color 0.3s;
    color: white;
  }
  .sidebar a:hover {
    background-color: var(--primary-dark);
  }

  /* Main content */
  main {
    margin-left: 250px;
    padding: 140px 2rem 2rem 2rem; /* top padding for fixed header + search bar */
    transition: margin-left 0.3s ease;
  }
  main.shifted {
    margin-left: 0;
  }
  .dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
    gap: 1.5rem;
  }
  .card {
    background: var(--card-bg);
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 4px 6px var(--card-shadow);
    transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
  }
  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px var(--card-shadow);
  }
  .card h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: var(--primary);
  }
  .stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--primary);
  }

  /* Toggle button */
  .toggle-btn {
    background: transparent;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    transition: background-color 0.3s ease;
  }
  .toggle-btn:hover,
  .toggle-btn:focus {
    background-color: rgba(255,255,255,0.2);
    outline: none;
  }

  /* Search bar container */
  .search-container {
    position: fixed;
    top: 72px;
    left: 250px;
    right: 0;
    background: var(--search-bg);
    padding: 1rem 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    z-index: 55;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: left 0.3s ease, background-color 0.3s ease;
  }
  .search-container.shifted {
    left: 0;
  }
  .search-input {
    flex-grow: 1;
    padding: 0.5rem 1rem;
    border: 2px solid var(--search-border);
    border-radius: 0.5rem;
    font-size: 1rem;
    outline-offset: 2px;
    transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
    background-color: var(--card-bg);
    color: var(--text-color);
  }
  .search-input::placeholder {
    color: var(--text-color-light);
  }
  .search-btn {
    background: var(--search-btn-bg);
    color: white;
    padding: 0.5rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s ease;
  }
  .search-btn:hover,
  .search-btn:focus {
    background: var(--search-btn-bg-hover);
    outline: none;
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .sidebar {
      top: 72px;
      height: calc(100vh - 72px);
      position: fixed;
      transform: translateX(-100%);
      width: 220px;
      padding: 1rem;
      box-shadow: 2px 0 8px rgba(0,0,0,0.2);
    }
    .sidebar.visible {
      transform: translateX(0);
    }
    main {
      margin-left: 0;
      padding: 140px 1rem 1rem 1rem;
    }
    main.shifted {
      margin-left: 220px;
    }
    .toggle-btn {
      display: flex;
    }
    .search-container {
      left: 0;
      padding: 1rem;
      box-shadow: none;
    }
  }
  /* Overlay when sidebar open on small screens */
  body.sidebar-open::before {
    content: "";
    position: fixed;
    top: 72px;
    left: 0;
    width: 100vw;
    height: calc(100vh - 72px);
    background: rgba(0,0,0,0.5);
    z-index: 45;
    cursor: pointer;
  }
  /* Shift search bar left when sidebar visible on small screens */
  .search-container.shifted {
    left: 220px !important;
  }
</style>
</head>
<body>
  <div id="content-wrapper">
    <header class="header max-w-full px-4 md:px-8">
      <div class="header-left">
        <button id="toggleSidebarBtn" class="toggle-btn" aria-label="Toggle menu" aria-expanded="false" aria-controls="sidebar" aria-haspopup="true">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
        <img src="moet.png" alt="Ministry of Education" />
        <h1>Admin Dashboard</h1>
      </div>
      <label class="mode-toggle" for="darkModeToggle" title="Toggle dark/light mode">
        <span id="modeLabel">Light Mode</span>
        <input type="checkbox" id="darkModeToggle" aria-label="Toggle dark mode" />
      </label>
    </header>

    <aside class="sidebar hidden md:block" id="sidebar" role="navigation" aria-label="Sidebar menu" tabindex="-1">
      <h2>Admin Dashboard</h2>
      <nav>
        <ul>
          <li><a href="register.php">Register Users</a></li>
          <li><a href="Adminlinks.php">View and download additional requirements</a></li>
          <li><a href="view-high_school_enrollment.php">View high-school enrollment</a></li>
          <li><a href="view-primary_enrollment.php">View primary enrollment</a></li>
          <li><a href="view-preschool_enrollment.php">View pre-school enrollment</a></li>
          <li><a href="view-infrastructure.php">View infrastructure</a></li> 
        
          <a href="logout.php" class="btn whitespace-nowrap"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
        </ul>
      </nav>
    </aside>

    <!-- Search bar -->
    <form action="Adminsearch.php" method="POST" class="search-container" role="search" aria-label="Search schools or principals">
      <input type="text" name="search_query" class="search-input" placeholder="Search for school or principal..." required aria-required="true" />
      <button type="submit" name="search" class="search-btn" aria-label="Search">
        <i class="fas fa-search"></i> Search
      </button>
    </form>



    <main id="mainContent" tabindex="-1">
      <h2 class="text-3xl font-bold mb-6">Dashboard Overview</h2>

      <section class="dashboard-grid" aria-label="High Schools statistics">
        <div class="card" role="region" aria-labelledby="highSchoolsTitle">
          <h3 id="highSchoolsTitle">High Schools</h3>
          <p class="stat-number" aria-live="polite" aria-atomic="true"><?php echo htmlspecialchars($totals['High']['schools']); ?></p>
          <p>Total High Schools</p>
        </div>
        <div class="card" role="region" aria-labelledby="highTeachersTitle">
          <h3 id="highTeachersTitle">Teachers</h3>
          <p class="stat-number" aria-live="polite" aria-atomic="true"><?php echo htmlspecialchars($totals['High']['teachers']); ?></p>
          <p>Total Teachers (High Schools)</p>
        </div>
      </section>

      <section class="dashboard-grid mt-8" aria-label="Primary Schools statistics">
        <div class="card" role="region" aria-labelledby="primarySchoolsTitle">
          <h3 id="primarySchoolsTitle">Primary Schools</h3>
          <p class="stat-number" aria-live="polite" aria-atomic="true"><?php echo htmlspecialchars($totals['Primary']['schools']); ?></p>
          <p>Total Primary Schools</p>
        </div>
        <div class="card" role="region" aria-labelledby="primaryTeachersTitle">
          <h3 id="primaryTeachersTitle">Teachers</h3>
          <p class="stat-number" aria-live="polite" aria-atomic="true"><?php echo htmlspecialchars($totals['Primary']['teachers']); ?></p>
          <p>Total Teachers (Primary Schools)</p>
        </div>
      </section>

      <section class="dashboard-grid mt-8" aria-label="Pre-Schools statistics">
        <div class="card" role="region" aria-labelledby="preSchoolsTitle">
          <h3 id="preSchoolsTitle">Pre-Schools</h3>
          <p class="stat-number" aria-live="polite" aria-atomic="true"><?php echo htmlspecialchars($totals['Pre']['schools']); ?></p>
          <p>Total Pre-Schools</p>
        </div>
        <div class="card" role="region" aria-labelledby="preTeachersTitle">
          <h3 id="preTeachersTitle">Teachers</h3>
          <p class="stat-number" aria-live="polite" aria-atomic="true"><?php echo htmlspecialchars($totals['Pre']['teachers']); ?></p>
          <p>Total Teachers (Pre-Schools)</p>
        </div>
      </section>
    </main>

    <footer class="bg-gray-800 text-white py-4 mt-auto">
      <div class="max-w-7xl mx-auto text-center">
        <p>&copy; 2025 Ministry of Education and Training. All rights reserved.</p>
      </div>
    </footer>
  </div>

<script>
  // Sidebar toggle
  const toggleBtn = document.getElementById('toggleSidebarBtn');
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('mainContent');
  const searchContainer = document.querySelector('.search-container');

  toggleBtn.addEventListener('click', () => {
    const isHidden = sidebar.classList.toggle('hidden');
    toggleBtn.setAttribute('aria-expanded', !isHidden);
    if (!isHidden) {
      sidebar.classList.add('visible');
      sidebar.focus();
      document.body.classList.add('sidebar-open');
      if(window.innerWidth <= 1024) {
        searchContainer.classList.add('shifted');
      }
    } else {
      sidebar.classList.remove('visible');
      mainContent.focus();
      document.body.classList.remove('sidebar-open');
      searchContainer.classList.remove('shifted');
    }
  });

  // Close sidebar on outside click (mobile/tablet)
  document.addEventListener('click', (e) => {
    if (window.innerWidth <= 1024) {
      if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target) && !sidebar.classList.contains('hidden')) {
        sidebar.classList.add('hidden');
        sidebar.classList.remove('visible');
        toggleBtn.setAttribute('aria-expanded', false);
        mainContent.focus();
        document.body.classList.remove('sidebar-open');
        searchContainer.classList.remove('shifted');
      }
    }
  });

  // Close sidebar on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !sidebar.classList.contains('hidden')) {
      sidebar.classList.add('hidden');
      sidebar.classList.remove('visible');
      toggleBtn.setAttribute('aria-expanded', false);
      mainContent.focus();
      document.body.classList.remove('sidebar-open');
      searchContainer.classList.remove('shifted');
    }
  });

  // Dark/light mode toggle
  const darkModeToggle = document.getElementById('darkModeToggle');
  const modeLabel = document.getElementById('modeLabel');
  const body = document.body;

  // Load saved preference
  if (localStorage.getItem('darkMode') === 'enabled') {
    body.classList.add('dark');
    darkModeToggle.checked = true;
    modeLabel.textContent = 'Dark Mode';
  } else {
    modeLabel.textContent = 'Light Mode';
  }

  darkModeToggle.addEventListener('change', () => {
    if (darkModeToggle.checked) {
      body.classList.add('dark');
      localStorage.setItem('darkMode', 'enabled');
      modeLabel.textContent = 'Dark Mode';
    } else {
      body.classList.remove('dark');
      localStorage.setItem('darkMode', 'disabled');
      modeLabel.textContent = 'Light Mode';
    }
  });
</script>
<style>
  /* Overlay for sidebar on mobile/tablet */
  body.sidebar-open::before {
    content: "";
    position: fixed;
    top: 72px;
    left: 0;
    width: 100vw;
    height: calc(100vh - 72px);
    background: rgba(0,0,0,0.5);
    z-index: 45;
    cursor: pointer;
  }
  /* Shift search bar left when sidebar visible on small screens */
  .search-container.shifted {
    left: 220px !important;
  }
</style>
</body>
</html>
