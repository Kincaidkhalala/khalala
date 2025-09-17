<?php
// Database connection (adjust credentials)
$host = 'localhost';
$db = 'moet1';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

// Initialize filter variables from GET or POST
$request = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;

$constituency = $request['constituency'] ?? '';
$electricity = $request['electricity'] ?? '';
$water = $request['water'] ?? '';
$level = $request['level'] ?? ''; // 'Primary' or 'High'
$columns = $request['columns'] ?? []; // array of selected columns

// Validate level input
$allowed_levels = ['Primary', 'High'];
if (!in_array($level, $allowed_levels)) {
    $level = ''; // reset if invalid or not set
}

// Define running tap water values (case-insensitive)
$runningWaterValues = ['piped', 'tap water', 'running water', 'well', 'rainwater'];

// Define all possible optional columns and their SQL expressions and labels
$allColumns = [
    'electricity' => [
        'sql' => 'ei.has_electricity',
        'label' => 'Electricity',
        'icon' => 'bolt',    // FontAwesome icon name
        'formatter' => fn($v) => ucfirst($v ?? 'no'),
    ],
    'running_tap_water' => [
        'sql' => '', // will be set later
        'label' => 'Running Tap Water',
        'icon' => 'tint',
        'formatter' => fn($v) => ucfirst($v ?? 'no'),
    ],
    'additional_latrines' => [
        'sql' => 'MAX(COALESCE(at.additional_latrines_needed, 0))',
        'label' => 'Require Latrines',
        'icon' => 'restroom',
        'formatter' => fn($v) => ($v == 1) ? 'Yes' : 'No',
    ],
    'additional_classrooms' => [
        'sql' => "MAX(CASE WHEN ac.require_classrooms = 'yes' THEN 1 ELSE 0 END)",
        'label' => 'Require Classrooms',
        'icon' => 'chalkboard-teacher',
        'formatter' => fn($v) => ($v == 1) ? 'Yes' : 'No',
    ],
    'internet' => [
        'sql' => "MAX(CASE WHEN ii.has_internet = 'yes' THEN 1 ELSE 0 END)",
        'label' => 'Has Internet',
        'icon' => 'wifi',
        'formatter' => fn($v) => ($v == 1) ? 'Yes' : 'No',
    ],
];

// Always show these columns
$baseColumns = [
    'school_name' => ['sql' => 's.school_name', 'label' => 'School Name', 'icon' => 'school', 'formatter' => 'htmlspecialchars'],
    'reg_no' => ['sql' => 's.registration_number', 'label' => 'Reg No', 'icon' => 'id-badge', 'formatter' => 'htmlspecialchars'],
    'constituency' => ['sql' => 's.constituency', 'label' => 'Constituency', 'icon' => 'map-marker-alt', 'formatter' => 'htmlspecialchars'],
];

// Validate selected columns: keep only allowed keys
$columns = is_array($columns) ? array_intersect($columns, array_keys($allColumns)) : [];
if (empty($columns)) {
    // Default columns to show if none selected
    $columns = ['electricity', 'running_tap_water', 'additional_latrines', 'additional_classrooms', 'internet'];
}

// Prepare running water placeholders only if needed
$needRunningWaterPlaceholders = in_array('running_tap_water', $columns) || ($water === 'yes' || $water === 'no');

$runningWaterPlaceholders = [];
$runningWaterParams = [];
if ($needRunningWaterPlaceholders) {
    foreach ($runningWaterValues as $i => $val) {
        $ph = ":water_val_$i";
        $runningWaterPlaceholders[] = $ph;
        $runningWaterParams[$ph] = $val;
    }
}

// Set the SQL for running tap water column now with named placeholders if needed
if (in_array('running_tap_water', $columns)) {
    $allColumns['running_tap_water']['sql'] = "CASE WHEN LOWER(wi.water_source) IN (" . implode(',', $runningWaterPlaceholders) . ") THEN 'yes' ELSE 'no' END";
}

// Build SELECT clause
$selectParts = [];
foreach ($baseColumns as $key => $col) {
    $selectParts[] = "{$col['sql']} AS {$key}";
}
foreach ($columns as $colKey) {
    $selectParts[] = $allColumns[$colKey]['sql'] . " AS {$colKey}";
}

$sql = "SELECT " . implode(",\n", $selectParts) . "
    FROM schools s
    LEFT JOIN electricity_infrastructure ei ON s.school_id = ei.school_id
    LEFT JOIN water_infrastructure wi ON s.school_id = wi.school_id
    LEFT JOIN additionaltoilets at ON s.school_id = at.school_id
    LEFT JOIN additionalclassrooms ac ON s.school_id = ac.school_id
    LEFT JOIN internet_infrastructure ii ON s.school_id = ii.school_id
    WHERE 1=1
";

// Filter by level based on cluster/centre presence
if ($level === 'High') {
    $sql .= " AND s.cluster IS NOT NULL AND s.cluster != '' ";
} elseif ($level === 'Primary') {
    $sql .= " AND s.centre IS NOT NULL AND s.centre != '' ";
} else {
    $sql .= " AND 0=1 ";
}

// Start params array
$params = [];

// Add running water params if needed
if ($needRunningWaterPlaceholders) {
    $params = array_merge($params, $runningWaterParams);
}

// Filter by constituency if provided
if ($constituency !== '') {
    $sql .= " AND s.constituency LIKE :constituency ";
    $params[':constituency'] = "%$constituency%";
}

// Filter by electricity if provided
if ($electricity === 'yes' || $electricity === 'no') {
    $sql .= " AND ei.has_electricity = :electricity ";
    $params[':electricity'] = $electricity;
}

// Filter by running tap water if provided
if ($water === 'yes' || $water === 'no') {
    $sql .= " AND (CASE WHEN LOWER(wi.water_source) IN (" . implode(',', $runningWaterPlaceholders) . ") THEN 'yes' ELSE 'no' END) = :water ";
    $params[':water'] = $water;
}

$sql .= " GROUP BY s.school_id ORDER BY s.school_name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();

// Function to download data as Excel
function downloadExcel(array $results, array $baseColumns, array $allColumns, array $columns) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"schools_data.xls\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Prepare headers (base + selected optional columns)
    $headers = [];
    foreach ($baseColumns as $col) {
        $headers[] = $col['label'];
    }
    foreach ($columns as $colKey) {
        $headers[] = $allColumns[$colKey]['label'];
    }

    echo implode("\t", $headers) . "\n";

    // Output each row
    foreach ($results as $row) {
        $rowData = [];

        // Base columns
        foreach ($baseColumns as $key => $_) {
            $val = $row[$key] ?? '';
            // Use htmlspecialchars but decode HTML entities instead for Excel
            $rowData[] = html_entity_decode($val);
        }
        // Optional columns with formatter applied
        foreach ($columns as $colKey) {
            $val = $row[$colKey] ?? '';
            $formatter = $allColumns[$colKey]['formatter'];
            $valueFormatted = $formatter($val);
            // Decode any HTML entities for Excel
            $rowData[] = html_entity_decode($valueFormatted);
        }

        echo implode("\t", $rowData) . "\n";
    }
    exit();
}

// Handle download request (triggered by POST parameter 'download')
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download'])) {
    // We already have $results from above, use them for download
    downloadExcel($results, $baseColumns, $allColumns, $columns);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>School Filter with Selectable Columns</title>
<!-- Font Awesome CDN for Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<style>
/* Body and Container */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f7f8;
    color: #333;
    margin: 20px;
}

.container {
    max-width: 1200px;
    margin: auto;
    background-color: #ffffff;
    padding: 20px 30px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 50, 0.1);
}

/* Header */
h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #0056b3;
    font-weight: 900;
}

/* Filter Section */
.filters {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 30px;
    align-items: center;
    justify-content: center;
}

.filters label {
    font-weight: 600;
    color: #444;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 1rem;
}

/* Icons inside labels */
.filters label i.fa-solid {
    color: #007BFF;
    font-size: 1.25rem;
}

/* Select and Input Boxes */
select, input[type="text"] {
    padding: 10px 15px;
    border: 2px solid #007BFF;
    border-radius: 8px;
    background-color: #e7f0fe;
    color: #0056b3;
    font-size: 1rem;
    transition: border-color 0.3s ease, background-color 0.3s ease;
    min-width: 200px;
}

select:hover, input[type="text"]:hover,
select:focus, input[type="text"]:focus {
    border-color: #0056b3;
    outline: none;
    background-color: #d0e3ff;
}

/* Multi-select size */
select[multiple] {
    height: 140px;
}

/* Filter Button */
button.filter-btn {
    background: linear-gradient(135deg, #0069d9, #0056b3);
    border: none;
    color: white;
    padding: 12px 25px;
    font-weight: 700;
    border-radius: 10px;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0, 86, 179, 0.4);
    transition: background 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.1rem;
}

button.filter-btn:hover {
    background: linear-gradient(135deg, #0056b3, #003d80);
}

/* Icon inside button */
button.filter-btn i.fa-filter {
    font-size: 1.3rem;
}

/* Table Wrapper for horizontal scroll on small screens */
.table-wrapper {
    overflow-x: auto;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border-radius: 12px;
    overflow: hidden;
    background-color: #ffffff;
    font-size: 0.95rem;
}

/* Table head row */
thead tr {
    background: linear-gradient(90deg, #007bff, #0056b3);
    color: white;
    font-weight: 700;
}

/* Table header cells */
thead th {
    padding: 14px 18px;
    text-align: left;
    border-bottom: 3px solid #004080;
}

/* Table body rows */
tbody tr {
    transition: background-color 0.3s ease;
}

/* Zebra striping for rows */
tbody tr:nth-child(even) {
    background-color: #f9fbff;
}

/* Row hover color */
tbody tr:hover {
    background-color: #cce4ff;
}

/* Table data cells */
tbody td {
    padding: 12px 18px;
    border-bottom: 1px solid #e0e6f0;
    color: #333333;
}

/* Responsive styles */
@media (max-width: 768px) {
    /* Reset table elements to default table display */
    .table-wrapper {
        overflow-x: auto;
    }

    table, thead, tbody, th, td, tr {
        display: table;
    }

    /* Table header style on small screens */
    thead tr {
        position: relative;
        top: auto;
        left: auto;
        clip: auto;
        width: auto;
        height: auto;
        background: linear-gradient(90deg, #0056b3, #003d80);
        color: white;
    }

    tbody tr {
        margin-bottom: 0;
        border: none;
        padding: 0;
        background: transparent;
        box-shadow: none;
    }

    tbody td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
        font-weight: 500;
        color: #333;
    }
}
</style>
</head>
<body>

<div class="container">
  <h1><i class="fa-solid fa-filter"></i> Filter Schools and Select Columns to Display</h1>

  <!-- Filter form -->
  <form method="get" action="" class="filters" aria-label="School filter form">

    <label for="level">
      <i class="fa-solid fa-school"></i> School Level: <span style="color:red">*</span>
      <select id="level" name="level" required aria-required="true" aria-describedby="levelHelp">
        <option value="" <?= $level === '' ? 'selected' : '' ?>>-- Select Level --</option>
        <option value="Primary" <?= $level === 'Primary' ? 'selected' : '' ?>>Primaries</option>
        <option value="High" <?= $level === 'High' ? 'selected' : '' ?>>High schools</option>
      </select>
    </label>

    <label for="constituency">
      <i class="fa-solid fa-map-marker-alt"></i> Constituency:
      <input type="text" id="constituency" name="constituency" value="<?= htmlspecialchars($constituency) ?>" placeholder="e.g. Leribe No 12" />
    </label>

    <label for="columns">
      <i class="fa-solid fa-table-columns"></i> Select Columns to Display (Ctrl+Click to select multiple):
      <select id="columns" name="columns[]" multiple aria-multiselectable="true" size="5">
        <?php foreach ($allColumns as $key => $col): ?>
          <option value="<?= htmlspecialchars($key) ?>" <?= in_array($key, $columns) ? 'selected' : '' ?>>
            <?= htmlspecialchars($col['label']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <button type="submit" class="filter-btn" aria-label="Apply filters">
      <i class="fa-solid fa-filter"></i> Filter
    </button>

  </form>

  <?php if ($level === ''): ?>
    <p style="color:red; font-weight: 700; text-align:center;">Please select a school level to see results.</p>
  <?php elseif (count($results) === 0): ?>
    <p style="text-align:center;">No schools found matching your criteria.</p>
  <?php else: ?>
    <!-- Display a separate form for the download button to POST data with current filters/columns -->
    <form method="post" action="">
        <!-- Include all filter inputs as hidden fields to preserve current filters -->
        <input type="hidden" name="level" value="<?= htmlspecialchars($level) ?>">
        <input type="hidden" name="constituency" value="<?= htmlspecialchars($constituency) ?>">
        <input type="hidden" name="electricity" value="<?= htmlspecialchars($electricity) ?>">
        <input type="hidden" name="water" value="<?= htmlspecialchars($water) ?>">
        <?php foreach ($columns as $colKey): ?>
            <input type="hidden" name="columns[]" value="<?= htmlspecialchars($colKey) ?>">
        <?php endforeach; ?>
        <button type="submit" name="download" class="filter-btn" aria-label="Download data as Excel" style="margin-bottom: 20px;">
            <i class="fa-solid fa-file-excel"></i> Download as Excel
        </button>
    </form>

    <div class="table-wrapper">
      <table role="table" aria-label="Filtered schools results">
        <thead>
          <tr>
            <?php
            // Always show base columns headers
            foreach ($baseColumns as $key => $col) {
                echo '<th><i class="fa-solid fa-' . $col['icon'] . '" title="' . htmlspecialchars($col['label']) . '"></i> ' . htmlspecialchars($col['label']) . '</th>';
            }
            // Show selected optional columns headers
            foreach ($columns as $colKey) {
                $col = $allColumns[$colKey];
                echo '<th><i class="fa-solid fa-' . $col['icon'] . '" title="' . htmlspecialchars($col['label']) . '"></i> ' . htmlspecialchars($col['label']) . '</th>';
            }
            ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($results as $row): ?>
          <tr>
            <?php
            // Base columns values
            foreach ($baseColumns as $key => $col) {
                $val = $row[$key] ?? '';
                $formatter = $col['formatter'];
                $content = ($formatter === 'htmlspecialchars') ? htmlspecialchars($val) : $formatter($val);
                echo '<td data-label="' . htmlspecialchars($col['label']) . '">' . $content . '</td>';
            }
            // Optional columns values
            foreach ($columns as $colKey) {
                $val = $row[$colKey] ?? '';
                $formatter = $allColumns[$colKey]['formatter'];
                $label = $allColumns[$colKey]['label'];
                $content = $formatter($val);
                echo '<td data-label="' . htmlspecialchars($label) . '">' . $content . '</td>';
            }
            ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

</div>

</body>
</html>
