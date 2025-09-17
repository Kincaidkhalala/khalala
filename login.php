<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection configuration
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

// Handle login request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $regNo = $_POST['RegNo'];
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id, password, role, level, status FROM users WHERE RegNo = ?");
    $stmt->bind_param("s", $regNo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Bind result variables
        $stmt->bind_result($user_id, $hashedPassword, $role, $level, $status);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashedPassword)) {
            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['RegNo'] = $regNo; 
            $_SESSION['role'] = $role;
            $_SESSION['level'] = $level; // Set the level variable
            $_SESSION['user_id'] = $user_id; // Store userID in session

            if ($status == 0) {
                // Redirect based on level (redirect to different pages to register schools)
                switch ($level) {
                    case 'High':
                        header("Location: highschool.php");
                        break;
                    case 'Primary':
                        header("Location: primaryschool.php");
                        break;
                    case 'Pre':
                        header("Location: pre-school.php");
                        break;
                    case 'Admin':
                        header("Location: Admin.php");
                        break;
                    default:
                        echo "Invalid level.";
                        break;
                }
                exit;
            } else if ($status == 1) {
                // Redirect based on level if status is already 1 (Dashboard)
                switch ($level) {
                    case 'High':
                        header("Location: Highschool-Dash.php");
                        break;
                    case 'Primary':
                        header("Location: Primary-Dash.php");
                        break;
                    case 'Pre':
                        header("Location: kinder-Dash.php");
                        break;
                    default:
                        echo "Invalid level.";
                        break;
                }
                exit;
            }
        } else {
            // Invalid password
            echo "Invalid credentials. Please try again.";
        }

        $stmt->close();
    } else {
        echo "Invalid credentials. Please try again.";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Premium Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    /* Root variables for light mode */
    :root {
      --bg-color: #f0f9ff;
      --bg-container: rgba(255, 255, 255, 0.9);
      --text-color: #0369a1;
      --text-color-light: #1e293b;
      --input-bg: #e0f2fe;
      --input-border: #7dd3fc;
      --input-placeholder: #60a5fa;
      --primary: #0369a1;
      --primary-dark: #024e6a;
      --link-color: #0369a1;
      --link-hover: #024e6a;
      --box-shadow: rgba(0, 0, 0, 0.1);
    }

    /* Dark mode variables */
    body.dark {
      --bg-color: #0f172a;
      --bg-container: rgba(15, 23, 42, 0.8);
      --text-color: #60a5fa;
      --text-color-light: #cbd5e1;
      --input-bg: #1e293b;
      --input-border: #3b82f6;
      --input-placeholder: #93c5fd;
      --primary: #3b82f6;
      --primary-dark: #2563eb;
      --link-color: #60a5fa;
      --link-hover: #2563eb;
      --box-shadow: rgba(0, 0, 0, 0.5);
    }

    html {
      scroll-behavior: smooth;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
    }
    ::-webkit-scrollbar-track {
      background: transparent;
    }
    ::-webkit-scrollbar-thumb {
      background: var(--primary);
      border-radius: 10px;
    }

    body {
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: var(--bg-color);
      color: var(--text-color-light);
      padding: 1rem;
      box-sizing: border-box;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .login-container {
      position: relative;
      overflow: hidden;
      width: 100%;
      max-width: 400px;
      padding: 1.5rem;
      background: var(--bg-container);
      border-radius: 1.5rem;
      box-sizing: border-box;
      box-shadow: 0 10px 30px var(--box-shadow);
      transition: background 0.3s ease, box-shadow 0.3s ease;
    }

    .form-container {
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255 255 255 / 0.2);
      box-shadow: 0 30px 60px -20px rgba(0 0 0 / 0.15);
      background: transparent !important;
      border-radius: 1.5rem;
      padding: 2rem;
      color: var(--text-color-light);
      transition: color 0.3s ease;
    }

    .input-field {
      transition: all 0.3s ease;
      background: var(--input-bg);
      border: 2px solid var(--input-border);
      color: var(--text-color-light);
      border-radius: 0.5rem;
      width: 100%;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      box-sizing: border-box;
    }

    .input-field::placeholder {
      color: var(--input-placeholder);
    }

    .input-field:hover {
      background-color: var(--primary);
      color: white;
      border-color: var(--primary);
      cursor: text;
    }

    .input-field:focus {
      outline: none;
      border-color: var(--primary-dark);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.4);
      background: var(--primary);
      color: white;
    }

    label {
      color: var(--text-color);
      font-weight: 600;
      display: block;
      margin-bottom: 0.25rem;
      transition: color 0.3s ease;
    }

    .submit-btn {
      position: relative;
      overflow: hidden;
      transition: all 0.3s;
      background: transparent;
      border: 2px solid var(--primary);
      color: var(--primary);
      font-weight: 600;
      padding: 1rem;
      border-radius: 1rem;
      cursor: pointer;
      width: 100%;
      font-size: 1.125rem;
    }

    .submit-btn:hover {
      background: var(--primary);
      border-color: var(--primary-dark);
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(59, 130, 246, 0.6);
    }

    .submit-btn:active {
      transform: translateY(0);
    }

    .submit-btn::after {
      content: "";
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.2),
        transparent
      );
      transition: 0.5s;
    }

    .submit-btn:hover::after {
      left: 100%;
    }

    .animated-bg {
      display: none; /* Hide the previous animated background for transparency */
    }

    /* Password toggle button */
    .relative button {
      color: var(--text-color-light);
      background: transparent;
      border: none;
      cursor: pointer;
      position: absolute;
      right: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      padding: 0;
    }
    .relative button:hover {
      color: var(--primary);
    }

    a {
      color: var(--link-color);
      font-weight: 500;
      transition: color 0.3s ease;
    }
    a:hover {
      color: var(--link-hover);
      text-decoration: underline;
    }

    /* Options */
    input[type="checkbox"] {
      accent-color: var(--primary);
    }

    /* Responsive adjustments for smaller devices */
    @media (max-width: 640px) {
      body {
        padding: 0.5rem;
      }

      .login-container {
        max-width: 100%;
        padding: 1rem;
        border-radius: 1rem;
      }

      .form-container {
        padding: 1.5rem;
        border-radius: 1rem;
      }

      h1 {
        font-size: 1.75rem; /* smaller heading */
      }

      p {
        font-size: 1rem;
      }

      .input-field {
        font-size: 0.9rem;
        padding: 0.75rem 1rem;
      }

      .submit-btn {
        font-size: 1rem;
        padding: 0.75rem;
        border-radius: 0.75rem;
      }

      .login-container img {
        width: 60px;
        height: 60px;
        border-width: 3px;
      }
    }

    /* Dark mode toggle container */
    .dark-mode-toggle {
      position: fixed;
      top: 1rem;
      right: 1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 600;
      color: var(--text-color-light);
      user-select: none;
      z-index: 1000;
    }

    .dark-mode-toggle input[type="checkbox"] {
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

    .dark-mode-toggle input[type="checkbox"]:checked {
      background: var(--primary);
    }

    .dark-mode-toggle input[type="checkbox"]::before {
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

    .dark-mode-toggle input[type="checkbox"]:checked::before {
      transform: translateX(20px);
    }
  </style>
  <script>
    // Toggle password visibility
    function togglePasswordVisibility() {
      const pwInput = document.getElementById('password');
      if (pwInput.type === 'password') {
        pwInput.type = 'text';
      } else {
        pwInput.type = 'password';
      }
    }

    // Dark mode toggle logic
    document.addEventListener('DOMContentLoaded', () => {
      const toggle = document.getElementById('darkModeToggle');
      const body = document.body;

      // Load saved preference
      if (localStorage.getItem('darkMode') === 'enabled') {
        body.classList.add('dark');
        toggle.checked = true;
      }

      toggle.addEventListener('change', () => {
        if (toggle.checked) {
          body.classList.add('dark');
          localStorage.setItem('darkMode', 'enabled');
        } else {
          body.classList.remove('dark');
          localStorage.setItem('darkMode', 'disabled');
        }
      });
    });
  </script>
</head>
<body>
  <!-- Dark mode toggle -->
  <label class="dark-mode-toggle" for="darkModeToggle">
    Dark Mode
    <input type="checkbox" id="darkModeToggle" aria-label="Toggle dark mode" />
  </label>

  <form id="loginForm" action="login.php" method="POST" class="space-y-6 w-full max-w-md" novalidate>
    <div class="login-container">

      <div class="form-container">
        <div class="flex justify-center mb-8 bg-transparent p-4 rounded-full">
          <img 
            src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/add54c64-54a2-4109-8995-e376197b117e.png" 
            alt="Default user profile silhouette" 
            class="w-20 h-20 rounded-full border-4 border-white shadow-lg"
          />
        </div>

        <h1 class="text-4xl font-extrabold text-center mb-2 tracking-tight" style="color: var(--primary);">Welcome Back</h1>
        <p class="text-center mb-10 text-lg" style="color: var(--primary);">Sign in to access your exclusive content</p>

        <div>
          <label for="RegNo">School Reg-No</label>
          <input 
            type="text" 
            id="RegNo" 
            name="RegNo" 
            required 
            placeholder="Reg-Number" 
            class="input-field"
            autocomplete="username"
          />
        </div>

        <div>
          <label for="password">Password</label>
          <div class="relative">
            <input 
              type="password" 
              id="password" 
              name="password" 
              required 
              placeholder="********" 
              class="input-field pr-10"
              autocomplete="current-password"
            />
            <button 
              type="button" 
              onclick="togglePasswordVisibility()"
              aria-label="Toggle password visibility"
              title="Show/Hide password"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input 
              type="checkbox" 
              id="remember" 
              class="h-4 w-4 rounded focus:ring focus:ring-offset-0 focus:ring-primary border-gray-300"
            />
            <label for="remember" class="ml-2 block text-sm" style="color: var(--text-color);">Remember me</label>
          </div>
          <a href="register.html" class="text-sm font-medium hover:underline">Forgot password?</a>
        </div>

        <button type="submit" class="submit-btn">
          LogIn →
        </button>

        <a href="homepage.html" class="text-center block mt-4 text-sm hover:underline font-medium" style="color: var(--link-color);">
          <b>Back</b>
        </a>

        <div class="mt-6 pt-6 border-t" style="border-color: rgba(255,255,255,0.3);"></div>
      </div>
    </div>
  </form>
</body>
</html>
