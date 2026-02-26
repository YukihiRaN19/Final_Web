<?php 
$page_title = "ดูข้อมูลวัดตัว";
include 'includes/header.php';
include 'includes/config.php';
$m_data = null;
$message = "";

// ดึงข้อมูล
if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $measurement_id = intval($_GET['id']);
    $sql_fetch = "SELECT m.*, c.customer_name 
                  FROM measurement m
                  JOIN customer c ON m.customer_id = c.customer_id
                  WHERE m.measurement_id = ?";
    $stmt = $conn->prepare($sql_fetch);
    if ($stmt) {
        $stmt->bind_param("i", $measurement_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $m_data = $result->fetch_assoc();
        } else {
            $message = "<div class='alert alert-warning'>❌ ไม่พบข้อมูลการวัดตัว</div>";
        }
        $stmt->close();
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
            <h4 class="menu-title">เมนูจัดการ</h4>
            <a class="nav-link" href="<?php echo BASE_URL; ?>main.php"><i class="bi bi-house-door-fill me-2"></i>แดชบอร์ด</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>customer.php"><i class="bi bi-people-fill me-2"></i>จัดการลูกค้า</a>
            <a class="nav-link active" href="<?php echo BASE_URL; ?>measurement.php"><i class="bi bi-rulers me-2"></i>จัดการข้อมูลวัดตัว</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>job.php"><i class="bi bi-scissors me-2"></i>จัดการงาน</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>payment.php"><i class="bi bi-credit-card-fill me-2"></i>จัดการชำระเงิน</a>
        </nav>

        <main class="w-100">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i><?php echo $page_title; ?></h5>
                </div>
                <div class="card-body p-4">
                    <?php if ($m_data): ?>
                        <dl class="row">
                            <dt class="col-sm-3 text-muted">ลูกค้า:</dt>
                            <dd class="col-sm-9 fw-bold"><?php echo htmlspecialchars($m_data['customer_name']); ?></dd>
                            
                            <dt class="col-sm-3 text-muted">วันที่วัดตัว:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($m_data['date_taken']); ?></dd>

                            <dt class="col-sm-3 text-muted">รอบอก:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($m_data['chest'] ?? '-'); ?> นิ้ว</dd>

                            <dt class="col-sm-3 text-muted">รอบเอว:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($m_data['waist'] ?? '-'); ?> นิ้ว</dd>

                            <dt class="col-sm-3 text-muted">สะโพก:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($m_data['hip'] ?? '-'); ?> นิ้ว</dd>
                            
                            <dt class="col-sm-3 text-muted">ไหล่:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($m_data['shoulder'] ?? '-'); ?> นิ้ว</dd>

                            <dt class="col-sm-3 text-muted">ความยาวแขน:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($m_data['sleeve_length'] ?? '-'); ?> นิ้ว</dd>

                            <dt class="col-sm-3 text-muted">ความยาวกางเกง:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($m_data['pants_length'] ?? '-'); ?> นิ้ว</dd>

                            <dt class="col-sm-3 text-muted">หมายเหตุ:</dt>
                            <dd class="col-sm-9"><?php echo nl2br(htmlspecialchars($m_data['remarks'] ?? '-')); ?></dd>
                        </dl>
                        <hr>
                        <div class="mt-4">
                            <a href="<?php echo BASE_URL; ?>edit_measurement.php?id=<?php echo $m_data['measurement_id']; ?>" class="btn btn-warning"><i class="bi bi-pencil-fill me-2"></i>แก้ไขข้อมูล</a>
                            <a href="<?php echo BASE_URL; ?>measurement.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle-fill me-2"></i>กลับไปหน้ารายการ</a>
                        </div>
                    <?php else: ?>
                        <?php echo $message; ?>
                        <a href="<?php echo BASE_URL; ?>measurement.php" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left-circle-fill me-2"></i>กลับไปหน้ารายการ</a>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>