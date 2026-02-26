<?php 
$page_title = "บันทึกการชำระเงิน";
include 'includes/header.php';
include 'includes/config.php';
$message = "";

// ดึงรายการงานที่เสร็จแล้ว
$jobs = [];
$job_sql = "SELECT j.job_id, j.job_code, j.title, c.customer_name 
            FROM jobs j 
            JOIN customer c ON j.customer_id = c.customer_id
            WHERE j.status = 'completed'
            ORDER BY j.received_at DESC";
$job_result = $conn->query($job_sql);
if ($job_result && $job_result->num_rows > 0) {
    while($row = $job_result->fetch_assoc()) {
        $jobs[] = $row;
    }
}

// บันทึกข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_id = intval($_POST['job_id']);
    $amount = (float)$_POST['amount'];
    $method = htmlspecialchars($_POST['method']);
    $receipt_no = htmlspecialchars($_POST['receipt_no']);
    $notes = htmlspecialchars($_POST['notes']);
    $payment_date = date('Y-m-d H:i:s');

    $sql = "INSERT INTO payments (job_id, amount, method, receipt_no, notes, payment_date) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("idssss", $job_id, $amount, $method, $receipt_no, $notes, $payment_date);
        if ($stmt->execute()) {
            $last_payment_id = $conn->insert_id;

            if (isset($_FILES['payment_images'])) {
                $target_dir = "uploads/";
                foreach ($_FILES['payment_images']['name'] as $key => $name) {
                    if ($_FILES['payment_images']['error'][$key] == 0) {
                        $image_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        $new_image_name = uniqid('payment_') . '.' . $image_extension;
                        $target_file = $target_dir . $new_image_name;
                        if (move_uploaded_file($_FILES['payment_images']['tmp_name'][$key], $target_file)) {
                            $img_sql = "INSERT INTO payment_images (payment_id, image_filename) VALUES (?, ?)";
                            $img_stmt = $conn->prepare($img_sql);
                            $img_stmt->bind_param("is", $last_payment_id, $new_image_name);
                            $img_stmt->execute();
                            $img_stmt->close();
                        }
                    }
                }
            }
            
            header("Location: " . BASE_URL . "payment.php?status=addsuccess");
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
            <a class="nav-link" href="<?php echo BASE_URL; ?>measurement.php"><i class="bi bi-rulers me-2"></i>จัดการข้อมูลวัดตัว</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>job.php"><i class="bi bi-scissors me-2"></i>จัดการงาน</a>
            <a class="nav-link active" href="<?php echo BASE_URL; ?>payment.php"><i class="bi bi-credit-card-fill me-2"></i>จัดการชำระเงิน</a>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            <main class="w-100">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i><?php echo $page_title; ?></h5>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <form action="<?php echo BASE_URL; ?>add_payment.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="job_id" class="form-label">สำหรับงาน (แสดงเฉพาะงานที่เสร็จแล้ว)</label>
                            <select class="form-select" id="job_id" name="job_id" required>
                                <option value="" selected disabled>-- กรุณาเลือกงาน --</option>
                                <?php foreach ($jobs as $job): ?>
                                    <option value="<?php echo $job['job_id']; ?>">
                                        <?php echo htmlspecialchars($job['job_code'] . " - " . $job['title'] . " (" . $job['customer_name'] . ")"); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">จำนวนเงิน (บาท)</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="method" class="form-label">วิธีชำระเงิน</label>
                                <select class="form-select" id="method" name="method" required>
                                    <option value="cash">เงินสด</option>
                                    <option value="bank_transfer">โอนเงิน</option>
                                    <option value="credit_card">บัตรเครดิต</option>
                                    <option value="mobile_banking">Mobile Banking</option>
                                    <option value="other">อื่นๆ</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="receipt_no" class="form-label">เลขที่ใบเสร็จ (ถ้ามี)</label>
                            <input type="text" class="form-control" id="receipt_no" name="receipt_no">
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">หมายเหตุ</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>

                        <hr class="my-4">
                        <div class="mb-3">
                            <label for="payment_images" class="form-label">อัปโหลดรูปภาพ (ชุดที่เสร็จ, สลิป ฯลฯ)</label>
                            <input class="form-control" type="file" id="payment_images" name="payment_images[]" multiple>
                            <div class="form-text">คุณสามารถเลือกได้หลายไฟล์พร้อมกัน</div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle-fill me-2"></i>บันทึก</button>
                            <a href="<?php echo BASE_URL; ?>payment.php" class="btn btn-secondary"><i class="bi bi-x-circle-fill me-2"></i>ยกเลิก</a>
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