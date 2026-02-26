<?php 
$page_title = "เพิ่มข้อมูลวัดตัว";
include 'includes/header.php';
include 'includes/config.php';
$message = "";

// ดึงรายชื่อลูกค้าทั้งหมด
$customers = [];
$customer_sql = "SELECT customer_id, customer_name FROM customer ORDER BY customer_name ASC";
$customer_result = $conn->query($customer_sql);
if ($customer_result && $customer_result->num_rows > 0) {
    while($row = $customer_result->fetch_assoc()) {
        $customers[] = $row;
    }
}

// บันทึกข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = intval($_POST['customer_id']);
    $date_taken = htmlspecialchars($_POST['date_taken']);
    $chest = !empty($_POST['chest']) ? (float)$_POST['chest'] : null;
    $waist = !empty($_POST['waist']) ? (float)$_POST['waist'] : null;
    $hip = !empty($_POST['hip']) ? (float)$_POST['hip'] : null;
    $shoulder = !empty($_POST['shoulder']) ? (float)$_POST['shoulder'] : null;
    $sleeve_length = !empty($_POST['sleeve_length']) ? (float)$_POST['sleeve_length'] : null;
    $pants_length = !empty($_POST['pants_length']) ? (float)$_POST['pants_length'] : null;
    $remarks = htmlspecialchars($_POST['remarks']);
    
    $sql = "INSERT INTO measurement (customer_id, date_taken, chest, waist, hip, shoulder, sleeve_length, pants_length, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("isdddddds", $customer_id, $date_taken, $chest, $waist, $hip, $shoulder, $sleeve_length, $pants_length, $remarks);
        if ($stmt->execute()) {
            header("Location: " . BASE_URL . "measurement.php?status=addsuccess");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>❌ เกิดข้อผิดพลาด: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<div class="container-fluid p-4">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded-3 shadow-sm mb-4">
      <div class="container-fluid">
        <a class="navbar-brand fs-4" href="<?php echo BASE_URL; ?>main.php">
        <i class="bi bi-scissors me-2"></i>
            ระบบเย็บผ้า
        </a>
        <span class="navbar-text text-white-50">
            | <?php echo $page_title; ?>
        </span>
      </div>
    </nav>
    <div class="d-flex align-items-stretch gap-4">
        <nav class="sidebar">
            <h4 class="menu-title">เมนูจัดการ</h4>
            <a class="nav-link" href="<?php echo BASE_URL; ?>main.php"><i class="bi bi-house-door-fill me-2"></i>แดชบอร์ด</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>customer.php"><i class="bi bi-people-fill me-2"></i>จัดการลูกค้า</a>
            <a class="nav-link active" href="<?php echo BASE_URL; ?>measurement.php"><i class="bi bi-rulers me-2"></i>จัดการข้อมูลวัดตัว</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>job.php"><i class="bi bi-scissors me-2"></i>จัดการงาน</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>payment.php"><i class="bi bi-credit-card-fill me-2"></i>จัดการชำระเงิน</a>
        </nav>

        <main class="w-100">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i><?php echo $page_title; ?></h5>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <form action="<?php echo BASE_URL; ?>add_measurement.php" method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_id" class="form-label">เลือกลูกค้า</label>
                                <select class="form-select" id="customer_id" name="customer_id" required>
                                    <option value="" selected disabled>-- กรุณาเลือกลูกค้า --</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['customer_id']; ?>"><?php echo htmlspecialchars($customer['customer_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_taken" class="form-label">วันที่วัดตัว</label>
                                <input type="date" class="form-control" id="date_taken" name="date_taken" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="chest" class="form-label">รอบอก (นิ้ว)</label>
                                <input type="number" step="0.01" class="form-control" id="chest" name="chest">
                            </div>
                             <div class="col-md-4 mb-3">
                                <label for="waist" class="form-label">รอบเอว (นิ้ว)</label>
                                <input type="number" step="0.01" class="form-control" id="waist" name="waist">
                            </div>
                             <div class="col-md-4 mb-3">
                                <label for="hip" class="form-label">สะโพก (นิ้ว)</label>
                                <input type="number" step="0.01" class="form-control" id="hip" name="hip">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="shoulder" class="form-label">ไหล่ (นิ้ว)</label>
                                <input type="number" step="0.01" class="form-control" id="shoulder" name="shoulder">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="sleeve_length" class="form-label">ความยาวแขน (นิ้ว)</label>
                                <input type="number" step="0.01" class="form-control" id="sleeve_length" name="sleeve_length">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="pants_length" class="form-label">ความยาวกางเกง (นิ้ว)</label>
                                <input type="number" step="0.01" class="form-control" id="pants_length" name="pants_length">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">หมายเหตุเพิ่มเติม</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle-fill me-2"></i>บันทึกข้อมูล</button>
                            <a href="<?php echo BASE_URL; ?>measurement.php" class="btn btn-secondary"><i class="bi bi-x-circle-fill me-2"></i>ยกเลิก</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>