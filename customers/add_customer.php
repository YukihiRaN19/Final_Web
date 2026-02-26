<?php 
$page_title = "เพิ่มลูกค้าใหม่";
include 'includes/header.php';
include 'includes/config.php';
$message = "";

// บันทึกข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = htmlspecialchars($_POST['customer_name']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);

    $sql = "INSERT INTO customer (customer_name, phone, address) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sss", $customer_name, $phone, $address);
        if ($stmt->execute()) {
            // ใช้ BASE_URL ในการ Redirect
            header("Location: " . BASE_URL . "customer.php?status=addsuccess");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>❌ เกิดข้อผิดพลาด: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='alert alert-danger'>❌ เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . htmlspecialchars($conn->error) . "</div>";
    }
    $conn->close();
}
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
            <a class="nav-link active" href="<?php echo BASE_URL; ?>customer.php"><i class="bi bi-people-fill me-2"></i>จัดการลูกค้า</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>measurement.php"><i class="bi bi-rulers me-2"></i>จัดการข้อมูลวัดตัว</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>job.php"><i class="bi bi-scissors me-2"></i>จัดการงาน</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>payment.php"><i class="bi bi-credit-card-fill me-2"></i>จัดการชำระเงิน</a>
        </nav>

        <main class="w-100">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i><?php echo $page_title; ?></h5>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <form action="<?php echo BASE_URL; ?>add_customer.php" method="post">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">ชื่อ-นามสกุล ลูกค้า</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">ที่อยู่</label>
                            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle-fill me-2"></i>บันทึกข้อมูล</button>
                            <a href="<?php echo BASE_URL; ?>customer.php" class="btn btn-secondary"><i class="bi bi-x-circle-fill me-2"></i>ยกเลิก</a>
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