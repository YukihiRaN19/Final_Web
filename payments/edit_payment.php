<?php 
$page_title = "แก้ไขการชำระเงิน";
include 'includes/header.php';
include 'includes/config.php';
$message = "";
$payment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$p_data = null; 
$p_images = []; // สำหรับเก็บรูปภาพเก่า

// ดึงรายการงานทั้งหมด
$jobs = [];
$job_sql = "SELECT j.job_id, j.job_code, j.title, c.customer_name 
            FROM jobs j 
            JOIN customer c ON j.customer_id = c.customer_id
            ORDER BY j.received_at DESC";
$job_result = $conn->query($job_sql);
if ($job_result && $job_result->num_rows > 0) {
    while($row = $job_result->fetch_assoc()) {
        $jobs[] = $row;
    }
}

// จัดการการอัปเดตข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $posted_id = intval($_POST['payment_id']);
    $job_id = intval($_POST['job_id']);
    $amount = (float)$_POST['amount'];
    $method = htmlspecialchars($_POST['method']);
    $receipt_no = htmlspecialchars($_POST['receipt_no']);
    $notes = htmlspecialchars($_POST['notes']);

    $sql = "UPDATE payments SET job_id=?, amount=?, method=?, receipt_no=?, notes=? WHERE payment_id=?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("idsssi", $job_id, $amount, $method, $receipt_no, $notes, $posted_id);
        if ($stmt->execute()) {
            
            // จัดการอัปโหลดรูปภาพใหม่
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
                            $img_stmt->bind_param("is", $posted_id, $new_image_name);
                            $img_stmt->execute();
                            $img_stmt->close();
                        }
                    }
                }
            }

            header("Location: " . BASE_URL . "payment.php?status=editsuccess");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>❌ เกิดข้อผิดพลาด: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// ดึงข้อมูลเดิมมาแสดง
if ($payment_id > 0) {
    $sql_fetch = "SELECT * FROM payments WHERE payment_id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    if ($stmt_fetch) {
        $stmt_fetch->bind_param("i", $payment_id);
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();
        if ($result->num_rows === 1) {
            $p_data = $result->fetch_assoc();
            // ดึงรูปภาพเก่ามาด้วย
            $img_sql = "SELECT image_id, image_filename FROM payment_images WHERE payment_id = ?";
            $img_stmt = $conn->prepare($img_sql);
            $img_stmt->bind_param("i", $payment_id);
            $img_stmt->execute();
            $img_result = $img_stmt->get_result();
            while($row = $img_result->fetch_assoc()){
                $p_images[] = $row;
            }
            $img_stmt->close();
        } else {
            $message = "<div class='alert alert-warning'>❌ ไม่พบข้อมูล</div>";
        }
        $stmt_fetch->close();
    }
} else {
    $message = "<div class='alert alert-danger'>❌ ID ไม่ถูกต้อง</div>";
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
            <h4 class="menu-title">เมนู</h4>
            <a class="nav-link" href="<?php echo BASE_URL; ?>main.php"><i class="bi bi-house-door-fill me-2"></i>แดชบอร์ด</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>customer.php"><i class="bi bi-people-fill me-2"></i>จัดการลูกค้า</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>measurement.php"><i class="bi bi-rulers me-2"></i>จัดการข้อมูลวัดตัว</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>job.php"><i class="bi bi-scissors me-2"></i>จัดการงาน</a>
            <a class="nav-link active" href="<?php echo BASE_URL; ?>payment.php"><i class="bi bi-credit-card-fill me-2"></i>จัดการชำระเงิน</a>
        </nav>

        <main class="w-100">
            <div class="card shadow-sm border-0 h-100">
                 <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-pencil-fill me-2"></i><?php echo $page_title; ?></h5>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <?php if ($p_data): ?>
                    <form action="<?php echo BASE_URL; ?>edit_payment.php?id=<?php echo $payment_id; ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="payment_id" value="<?php echo $p_data['payment_id']; ?>">
                        
                        <div class="mb-3">
                            <label for="job_id" class="form-label">สำหรับงาน</label>
                            <select class="form-select" id="job_id" name="job_id" required>
                                <?php foreach ($jobs as $job): ?>
                                    <option value="<?php echo $job['job_id']; ?>" <?php if($job['job_id'] == $p_data['job_id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($job['job_code'] . " - " . $job['title'] . " (" . $job['customer_name'] . ")"); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">จำนวนเงิน (บาท)</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?php echo htmlspecialchars($p_data['amount']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="method" class="form-label">วิธีชำระเงิน</label>
                                <select class="form-select" id="method" name="method" required>
                                    <option value="cash" <?php if($p_data['method'] == 'cash') echo 'selected'; ?>>เงินสด</option>
                                    <option value="bank_transfer" <?php if($p_data['method'] == 'bank_transfer') echo 'selected'; ?>>โอนเงิน</option>
                                    <option value="credit_card" <?php if($p_data['method'] == 'credit_card') echo 'selected'; ?>>บัตรเครดิต</option>
                                    <option value="mobile_banking" <?php if($p_data['method'] == 'mobile_banking') echo 'selected'; ?>>Mobile Banking</option>
                                    <option value="other" <?php if($p_data['method'] == 'other') echo 'selected'; ?>>อื่นๆ</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="receipt_no" class="form-label">เลขที่ใบเสร็จ (ถ้ามี)</label>
                            <input type="text" class="form-control" id="receipt_no" name="receipt_no" value="<?php echo htmlspecialchars($p_data['receipt_no']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">หมายเหตุ</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($p_data['notes']); ?></textarea>
                        </div>

                        <hr class="my-4">
                        
                        <div class="mb-3">
                            <label for="payment_images" class="form-label">อัปโหลดรูปภาพเพิ่มเติม</label>
                            <input class="form-control" type="file" id="payment_images" name="payment_images[]" multiple>
                            <div class="form-text">คุณสามารถเลือกได้หลายไฟล์พร้อมกัน (เช่น รูปชุด, สลิปโอนเงิน)</div>
                        </div>

                        <?php if (!empty($p_images)): ?>
                            <p class="mt-4"><strong>รูปภาพที่มีอยู่:</strong></p>
                            <div class="row g-3">
                                <?php foreach($p_images as $image): ?>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="position-relative">
                                        <a href="<?php echo BASE_URL; ?>uploads/<?php echo htmlspecialchars($image['image_filename']); ?>" target="_blank">
                                            <img src="<?php echo BASE_URL; ?>uploads/<?php echo htmlspecialchars($image['image_filename']); ?>" class="img-fluid rounded border">
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-warning"><i class="bi bi-check-circle-fill me-2"></i>บันทึกการแก้ไข</button>
                            <a href="<?php echo BASE_URL; ?>payment.php" class="btn btn-secondary"><i class="bi bi-x-circle-fill me-2"></i>ยกเลิก</a>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>