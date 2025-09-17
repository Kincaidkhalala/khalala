<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Actions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Import Poppins font from Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: 'Poppins', sans-serif;
    background-color: #f9fafb;
    color: #374151;
    position: relative;
    min-height: 100vh;

    /* Background image */
    background-image: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1470&q=80');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    background-attachment: fixed;
}

/* Overlay to improve text readability */
body::before {
    content: "";
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(255, 255, 255, 0.85);
    pointer-events: none;
    z-index: -1;
}

/* Header styles */
.header {
    background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
    color: white;
    padding: 20px 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.header h1 {
    font-size: 1.75rem;
    margin: 0;
}

@media (min-width: 768px) {
    .header h1 {
        font-size: 2rem;
    }
}

/* Container inside header for layout */
.header > div.max-w-6xl {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Button base styles */
.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    user-select: none;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover,
.btn-primary:focus {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(41, 128, 185, 0.6);
    outline: none;
}

/* Main content styles */
main {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
    flex-grow: 1;
}

main h2 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-align: center;
    color: #1e293b;
}

@media (min-width: 768px) {
    main h2 {
        font-size: 2.25rem;
        text-align: left;
    }
}

/* Section container */
.section {
    margin-bottom: 2rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background-color: rgba(255, 255, 255, 0.9);
    color: #1e293b;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    padding: 1.5rem;
}

.section h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

/* Cards inside sections */
.bg-stone-200 {
    background-color: #f1f5f9 !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-radius: 8px;
    padding: 1rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}

.bg-stone-200:hover,
.bg-stone-200:focus-within {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    outline: none;
}

.bg-stone-200 h4 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #1e293b;
}

/* Grid container inside sections */
.grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 768px) {
    .grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Footer styles */
footer {
    background-color: #1f2937;
    color: #d1d5db;
    padding: 1rem 1rem;
    text-align: center;
    font-size: 0.9rem;
    box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
    user-select: none;
}

/* Responsive typography for buttons on small devices */
@media (max-width: 480px) {
    .btn {
        font-size: 14px;
        padding: 10px 20px;
    }
}

    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-semibold">Admin Actions</h1>
            <div>
                <a href="Admin.php" class="btn btn-primary">Back to Admin</a>
            </div>
        </div>
    </header>

    <main class="flex-1 p-6">
        <h2 class="text-3xl font-bold mb-6 align-center">Choose an Action</h2>

        <!-- Primary School Section -->
        <section class="section">
            <h3 class="text-2xl font-bold mb-4">Primary School</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-stone-200 p-4 rounded-lg shadow">
                    <h4 class="text-xl font-semibold">View Additional Classrooms</h4>
                    <a href="pview-additional-classrooms.php" class="btn btn-primary">View</a>
                </div>
                <div class="bg-stone-200 p-4 rounded-lg shadow">
                    <h4 class="text-xl font-semibold">View Additional Toilets</h4>
                    <a href="pview-additionaltoilets.php" class="btn btn-primary">View</a>
                </div>
                <div class="bg-stone-200 p-4 rounded-lg shadow">
                    <h4 class="text-xl font-semibold">View Water Infrastructure</h4>
                    <a href="pWaterdownlod.php" class="btn btn-primary">View</a>
                </div>
                <div class="bg-stone-200 p-4 rounded-lg shadow">
                    <h4 class="text-xl font-semibold">View Electricity Infrastructure</h4>
                    <a href="pview-electricity-infrastructur.php" class="btn btn-primary">View</a>
                </div>
            </div>
        </section>

        <!-- High School Section -->
        <section class="section">
            <h3 class="text-2xl font-bold mb-4">High School</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-stone-200 p-4 rounded-lg shadow">
                    <h4 class="text-xl font-semibold">View Additional Classrooms</h4>
                    <a href="view-additional-classrooms.php" class="btn btn-primary">View</a>
                </div>
                <div class="bg-stone-200 p-4 rounded-lg shadow">
                    <h4 class="text-xl font-semibold">View Additional Toilets</h4>
                    <a href="view-additionaltoilets.php" class="btn btn-primary">View</a>
                </div>
                <div class="bg-stone-200 p-4 rounded-lg shadow">
                    <h4 class="text-xl font-semibold">View Water Infrastructure</h4>
                    <a href="Waterdownlod.php" class="btn btn-primary">View</a>
                </div>
                <div class="bg-stone-200 p-4 rounded-lg shadow">
                    <h4 class="text-xl font-semibold">View Electricity Infrastructure</h4>
                    <a href="view-electricity-infrastructur.php" class="btn btn-primary">View</a>
                </div>
            </div>
        </section>

        <!-- Pre-School Section -->
        <section class="section">
            <h3 class="text-2xl font-bold mb-4">Pre-School</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-stone-200 p-4 rounded-lg shadow">
                    <h4 class="text-xl font-semibold">View Additional Classrooms</h4>
                    <a href="view-additional-classrooms.php" class="btn btn-primary">View</a>
                </div>
                <div class="bg-stone-200 p-4 rounded-lg shadow">
                    <h4 class="text-xl font-semibold">View Additional Toilets</h4>
                    <a href="view-additionaltoilets.php" class="btn btn-primary">View</a>
                </div>
                <div class="bg-stone-200 p-4 rounded-lg shadow">
                    <h4 class="text-xl font-semibold">View Water Infrastructure</h4>
                    <a href="without-Water.php" class="btn btn-primary">View</a>
                </div>
                <div class="bg-stone-200 p-4 rounded-lg shadow">
                    <h4 class="text-xl font-semibold">View Electricity Infrastructure</h4>
                    <a href="view-electricity-infrastructur.php" class="btn btn-primary">View</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4">
        <div class="max-w-6xl mx-auto text-center">
            <p>&copy; 2025 Ministry of Education and Training. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>
