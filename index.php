<?php include 'includes/header.php'; 
    try {
        $sql = "SELECT COUNT(*) AS total_departments FROM departments";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalDepartments = $result['total_departments'];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>
<div class="container my-4">
    <div class="row no-gutters">
        <div class="col-md-12 col-12 mb-3">
            <!-- <h3>Department Title</h3> -->
        </div>
        <div class="col-md-6 col-12 mb-3">
            <h5>Add Department</h5>
            <form id="departmentForm" class="w-50">
                <div class="mb-3">
                    <label for="departmentId" class="form-label">Department ID</label>
                    <input type="text" class="form-control" id="departmentId" required>
                </div>
                <div class="mb-3">
                    <label for="departmentName" class="form-label">Department Name</label>
                    <input type="text" class="form-control" id="departmentName" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Department</button>
            </form>
        </div>
        <div class="col-md-6 col-12 mb-3">
            <h5>Department Table</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Department ID</th>
                        <th scope="col">Department Name</th>
                    </tr>
                </thead>
                <tbody id="departmentTableBody">
                </tbody>
            </table>
            <p>Total Departments: <?php echo $totalDepartments; ?></p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>