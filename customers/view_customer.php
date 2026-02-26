<?php 
$page_title = "ดูข้อมูลลูกค้า";
include 'includes/header.php';
include 'includes/config.php';
$customer_data = null;
$message = "";

// ดึงข้อมูลลูกค้า
if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $customer_id = intval($_GET['id']);
    $sql_fetch = "SELECT * FROM customer WHERE customer_id = ?";
    $stmt = $conn->prepare($sql_fetch);
    if ($stmt) {
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $customer_data = $result->fetch_assoc();
        } else {
            $message = "<div class='alert alert-warning'>❌ ไม่พบข้อมูลลูกค้า</div>";
        }
        $stmt->close();
    }
} else {
    $message = "<div class='alert alert-danger'>❌ ID ลูกค้าไม่ถูกต้องหรือไม่ถูกส่งมา</div>";
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
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i><?php echo $page_title; ?></h5>
                </div>
                <div class="card-body p-4">
                    <?php if ($customer_data): ?>
                        <dl class="row">
                            <dt class="col-sm-3 text-muted">รหัสลูกค้า:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($customer_data['customer_id']); ?></dd>

                            <dt class="col-sm-3 text-muted">ชื่อ-นามสกุล:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($customer_data['customer_name']); ?></dd>

                            <dt class="col-sm-3 text-muted">เบอร์โทรศัพท์:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($customer_data['phone'] ? $customer_data['phone'] : '-'); ?></dd>

                            <dt class="col-sm-3 text-muted">ที่อยู่:</dt>
                            <dd class="col-sm-9"><?php echo nl2br(htmlspecialchars($customer_data['address'] ? $customer_data['address'] : '-')); ?></dd>
                        </dl>
                        <hr>
                        <div class="mt-4">
                            <a href="<?php echo BASE_URL; ?>edit_customer.php?id=<?php echo $customer_data['customer_id']; ?>" class="btn btn-warning"><i class="bi bi-pencil-fill me-2"></i>แก้ไขข้อมูล</a>
                            <a href="<?php echo BASE_URL; ?>customer.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle-fill me-2"></i>กลับไปหน้ารายชื่อ</a>
                        </div>
                        <?php else: ?>
                        <?php echo $message; ?>
                        <a href="<?php echo BASE_URL; ?>customer.php" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left-circle-fill me-2"></i>กลับไปหน้ารายชื่อ</a>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>