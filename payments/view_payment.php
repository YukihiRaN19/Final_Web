<?php 
$page_title = "ดูรายละเอียดการชำระเงิน";
include 'includes/header.php';
include 'includes/config.php';
$p_data = null;
$p_images = [];
$message = "";

// สร้างตัวแปลภาษา
$method_map = [
    'cash' => 'เงินสด',
    'bank_transfer' => 'โอนเงิน',
    'credit_card' => 'บัตรเครดิต',
    'mobile_banking' => 'Mobile Banking',
    'other' => 'อื่นๆ'
];

if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $payment_id = intval($_GET['id']);
    
    // ดึงข้อมูลหลักของการชำระเงิน
    $sql = "SELECT p.*, j.job_code, c.customer_name 
            FROM payments p
            JOIN jobs j ON p.job_id = j.job_id
            JOIN customer c ON j.customer_id = c.customer_id
            WHERE p.payment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $p_data = $result->fetch_assoc();
        
        // ดึงรูปภาพทั้งหมด
        $img_sql = "SELECT image_filename FROM payment_images WHERE payment_id = ?";
        $img_stmt = $conn->prepare($img_sql);
        $img_stmt->bind_param("i", $payment_id);
        $img_stmt->execute();
        $img_result = $img_stmt->get_result();
        while($row = $img_result->fetch_assoc()){
            $p_images[] = $row;
        }
        $img_stmt->close();
    } else {
        $message = "<div class='alert alert-warning'>❌ ไม่พบข้อมูลการชำระเงิน</div>";
    }
    $stmt->close();
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
    <div class="d-flex align-items-stretch gap-4">>
    
        <nav class="sidebar">
            <h4 class="menu-title">เมนูจัดการ</h4>
            <a class="nav-link" href="<?php echo BASE_URL; ?>main.php"><i class="bi bi-house-door-fill me-2"></i>แดชบอร์ด</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>customer.php"><i class="bi bi-people-fill me-2"></i>จัดการลูกค้า</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>measurement.php"><i class="bi bi-rulers me-2"></i>จัดการข้อมูลวัดตัว</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>job.php"><i class="bi bi-scissors me-2"></i>จัดการงาน</a>
            <a class="nav-link active" href="<?php echo BASE_URL; ?>payment.php"><i class="bi bi-credit-card-fill me-2"></i>จัดการชำระเงิน</a>
        </nav>

        <main class="w-100">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i><?php echo $page_title; ?></h5>
                </div>
                <div class="card-body p-4">
                    <?php if ($p_data): ?>
                        <dl class="row">
                            <dt class="col-sm-3 text-muted">วันที่ชำระ:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars(date('Y-m-d', strtotime($p_data['payment_date']))); ?></dd>

                            <dt class="col-sm-3 text-muted">สำหรับงาน:</dt>
                            <dd class="col-sm-9 fw-bold"><?php echo htmlspecialchars($p_data['job_code']); ?></dd>

                            <dt class="col-sm-3 text-muted">ลูกค้า:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($p_data['customer_name']); ?></dd>

                            <dt class="col-sm-3 text-muted">จำนวนเงิน:</dt>
                            <dd class="col-sm-9"><?php echo number_format($p_data['amount'], 2); ?> บาท</dd>

                            <dt class="col-sm-3 text-muted">วิธีชำระ:</dt>
                            <dd class="col-sm-9"><?php echo isset($method_map[$p_data['method']]) ? $method_map[$p_data['method']] : htmlspecialchars($p_data['method']); ?></dd>
                            
                            <dt class="col-sm-3 text-muted">เลขที่ใบเสร็จ:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($p_data['receipt_no'] ?? '-'); ?></dd>

                            <dt class="col-sm-3 text-muted">หมายเหตุ:</dt>
                            <dd class="col-sm-9"><?php echo nl2br(htmlspecialchars($p_data['notes'] ?? '-')); ?></dd>
                        </dl>
                        
                        <?php if (!empty($p_images)): ?>
                            <hr>
                            <h5 class="mt-4 mb-3">รูปภาพประกอบ</h5>
                            <div class="row g-3">
                                <?php foreach($p_images as $image): ?>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <a href="uploads/<?php echo htmlspecialchars($image['image_filename']); ?>" target="_blank">
                                        <img src="uploads/<?php echo htmlspecialchars($image['image_filename']); ?>" class="img-fluid rounded shadow-sm border" alt="รูปประกอบการชำระเงิน">
                                    </a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <hr>
                        <div class="mt-4">
                            <a href="<?php echo BASE_URL; ?>edit_payment.php?id=<?php echo $p_data['payment_id']; ?>" class="btn btn-warning"><i class="bi bi-pencil-fill me-2"></i>แก้ไข</a>
                            <a href="<?php echo BASE_URL; ?>payment.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle-fill me-2"></i>กลับไปหน้ารายการ</a>
                        </div>
                    <?php else: ?>
                        <?php echo $message; ?>
                        <a href="<?php echo BASE_URL; ?>payment.php" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left-circle-fill me-2"></i>กลับไปหน้ารายการ</a>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>