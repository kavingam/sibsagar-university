<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}

// DB credentials
$host = 'localhost';
$dbname = 'sibsagar_university';
$username = 'root';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    die("Invalid ID.");
}

// Fetch record
$stmt = $pdo->prepare("SELECT * FROM seating_plan WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    die("Record not found.");
}

// Decode JSON data
$tableData = json_decode($record['data_json'], true);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Seat Summary</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
  <style>
    @media print {
      .no-print {
        display: none;
      }
    }
    body {
      padding: 20px;
    }
  </style>
</head>
<body>

<!-- <div class="no-print mb-3">
  <button onclick="window.print()" class="btn btn-primary">
    <i class="fas fa-print"></i> Print
  </button>
  <button class="btn btn-secondary" onclick="closeOrRedirect()">
    <i class="fas fa-arrow-left"></i> Back
  </button>
</div> -->

<h3>Exam Seating Details</h3>
<p><strong>Exam Name:</strong> <?= htmlspecialchars($record['exam_name']) ?> | <strong>Date:</strong> <?= htmlspecialchars($record['start_date']) ?> | <strong>Start Time:</strong> <?= htmlspecialchars($record['start_time']) ?> | <strong>Bench Seat:</strong> <?= htmlspecialchars($record['bench_seat']) ?></p>
<table class="table table-bordered">
  <thead class="thead-dark">
    <tr>
      <th>Department</th>
      <th>Course</th>
      <th>Semester</th>
      <th>Subject</th>
      <th>Total Students</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($tableData)): ?>
      <?php foreach ($tableData as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['department']) ?></td>
          <td>
              <?= 
                  $item['course'] == 1 ? 'UG' :
                  ($item['course'] == 2 ? 'PG' :
                  ($item['course'] == 3 ? 'TDC' :
                  ($item['course'] == 4 ? 'FYUG' : 'Unknown')))
              ?>
          </td>

          <td><?= htmlspecialchars($item['semester']) ?></td>
          <td><?= htmlspecialchars($item['subject']) ?></td>
          <td><?= htmlspecialchars($item['totalStudent']) ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="5" class="text-center">No seat data available.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<!-- Bottom Action Bar -->
<div class="fixed-bottom bg-light py-3x border-top text-center no-print shadow-sm">
  <div class="container">
    <div class="btn-group d-flex justify-content-center" role="group" aria-label="Action Buttons">

      <button onclick="window.print()" class="btn btn-secondary mx-2 rounded-0">
        <i class="fas fa-print"></i> <span class="d-none d-sm-inline">Print</span>
      </button>

      <button class="btn btn-success mx-2 rounded-0" onclick="closeOrRedirect()">
        <i class="fas fa-arrow-left"></i> <span class="d-none d-sm-inline">Back</span>
      </button>

    </div>
  </div>
</div>

<script>
function closeOrRedirect() {
  // window.close();
  // fallback if close fails
  // setTimeout(() => {
    // if (!window.closed) {
      window.location.href = 'seat-summary.php';
    // }
  // }, 500);
}
</script>
</body>
</html>
