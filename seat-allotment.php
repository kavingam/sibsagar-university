<?php 
session_start();
// Redirect to login if not logged in
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<?php
$host = 'localhost';
$dbname = 'sibsagar_university';
$username = 'root';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM seating_plan ORDER BY id ASC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>


<div class="container">


<!-- <h2 style="margin-top: 100px;">Seating Plan List</h2> -->
<h4 style="margin-top: 100px;">Seat Allotment | Print Seat Allotment |Seat Allocations Summary </h4>

<?php $jsonData = json_encode($rows); ?>

<table class="table table-bordered table-striped">
  <thead>
    <tr class="text-center">
      <th>ID</th>
      <th>Start Time</th>
      <th>Start Date</th>
      <th>Exam Name</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows): ?>
      <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['start_time']) ?></td>
          <td><?= htmlspecialchars($row['start_date']) ?></td>
          <td><?= htmlspecialchars($row['exam_name']) ?></td>

          <td class="text-center gap-2">
            <button class="btn-print-summaryx btn-lightweight btn-success-light rounded-0 redirectBtn" data-id="<?= htmlspecialchars($row['id']) ?>">
                <i class="fas fa-print"></i> Print Seat Summary
            </button>

            <button class="btn-view-summaryx btn-lightweight btn-primary-light rounded-0 viewBtn" data-id="<?= htmlspecialchars($row['id']) ?>">
                <i class="fas fa-eye"></i> View
            </button>

            <button class="btn-delete-summaryx btn-lightweight btn-danger-light rounded-0 deleteBtn" data-id="<?= htmlspecialchars($row['id']) ?>">
                <i class="fas fa-trash-alt"></i> Delete
            </button>

    
          </td>


        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="5" class="text-center">No data found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<script>
  // Redirect to print_summary.php with the given ID when Print is clicked
  document.querySelectorAll('.redirectBtn').forEach(button => {
    button.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      window.location.href = `print_summary.php?id=${id}`;
    });
  });

  // Optionally, View button can show in modal or also redirect — here we redirect
  document.querySelectorAll('.viewBtn').forEach(button => {
    button.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      window.location.href = `view_summary.php?id=${id}&view=1`;
    });
  });


  document.querySelectorAll('.deleteBtn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        if (confirm("Are you sure you want to delete this record?")) {
            window.location.href = `delete.php?id=${id}`;
        }
    });
   });

</script>



</div>
<?php include 'includes/footer.php'; ?>
