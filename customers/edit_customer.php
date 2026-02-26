<?php 
$page_title = "แก้ไขข้อมูลลูกค้า";
include 'includes/header.php'; // ตรวจสอบ path
include 'includes/config.php'; // ตรวจสอบ path
$message = "";
$customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$customer_data = null;

// บันทึกข้อมูลอัปเดต
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $posted_id = intval($_POST['customer_id']);
    $customer_name = htmlspecialchars($_POST['customer_name']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);

    $sql_update = "UPDATE customer SET customer_name = ?, phone = ?, address = ? WHERE customer_id = ?";
    $stmt = $conn->prepare($sql_update);
    if ($stmt) {
        $stmt->bind_param("sssi", $customer_name, $phone, $address, $posted_id);
        if ($stmt->execute()) {
            header("Location: " . BASE_URL . "customer.php?status=editsuccess");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>❌ เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// ดึงข้อมูลเดิมมาแสดง 
if ($customer_id > 0) {
    $sql_fetch = "SELECT * FROM customer WHERE customer_id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    if ($stmt_fetch) {
        $stmt_fetch->bind_param("i", $customer_id);
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();
        if ($result->num_rows === 1) {
            $customer_data = $result->fetch_assoc();
        } else {
            $message = "<div class='alert alert-warning'>❌ ไม่พบข้อมูลลูกค้าที่ต้องการแก้ไข</div>";
        }
        $stmt_fetch->close();
    }
} else {
    $message = "<div class='alert alert-danger'>❌ ID ลูกค้าไม่ถูกต้อง</div>";
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
            <a class="nav-link active" href="<?php echo BASE_URL; ?>customer.php"><i class="bi bi-people-fill me-2"></i>จัดการลูกค้า</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>measurement.php"><i class="bi bi-rulers me-2"></i>จัดการข้อมูลวัดตัว</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>job.php"><i class="bi bi-scissors me-2"></i>จัดการงาน</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>payment.php"><i class="bi bi-credit-card-fill me-2"></i>จัดการชำระเงิน</a>
        </nav>

        <main class="w-100">
            <div class="card shadow-sm border-0 h-100">
                 <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-pencil-fill me-2"></i><?php echo $page_title; ?></h5>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <?php if ($customer_data): ?>
                    <form action="<?php echo BASE_URL; ?>edit_customer.php?id=<?php echo $customer_id; ?>" method="post">
                        <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($customer_data['customer_id']); ?>">
                        
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">ชื่อ-นามสกุล ลูกค้า</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($customer_data['customer_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($customer_data['phone']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">ที่อยู่</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($customer_data['address']); ?></textarea>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-warning"><i class="bi bi-check-circle-fill me-2"></i>บันทึกการแก้ไข</button>
                            <a href="<?php echo BASE_URL; ?>customer.php" class="btn btn-secondary"><i class="bi bi-x-circle-fill me-2"></i>ยกเลิก</a>
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