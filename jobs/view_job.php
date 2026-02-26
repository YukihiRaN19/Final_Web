<?php 
$page_title = "ดูรายละเอียดงาน";
include 'includes/header.php';
include 'includes/config.php';
$job_data = null;
$message = "";

// สร้างตัวแปลภาษา
$status_map = [
    'pending' => 'ยังไม่เสร็จ',
    'completed' => 'เสร็จแล้ว',
    'cancelled' => 'ยกเลิก'
];

// ดึงข้อมูล
if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $job_id = intval($_GET['id']);
    $sql = "SELECT j.*, c.customer_name, m.date_taken as measurement_date
            FROM jobs j 
            JOIN customer c ON j.customer_id = c.customer_id
            LEFT JOIN measurement m ON j.measurement_id = m.measurement_id
            WHERE j.job_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $job_data = $result->fetch_assoc();
        } else {
            $message = "<div class='alert alert-warning'>❌ ไม่พบข้อมูลงาน</div>";
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
            <a class="nav-link" href="<?php echo BASE_URL; ?>measurement.php"><i class="bi bi-rulers me-2"></i>จัดการข้อมูลวัดตัว</a>
            <a class="nav-link active" href="<?php echo BASE_URL; ?>job.php"><i class="bi bi-scissors me-2"></i>จัดการงาน</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>payment.php"><i class="bi bi-credit-card-fill me-2"></i>จัดการชำระเงิน</a>
        </nav>

        <main class="w-100">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i><?php echo $page_title; ?></h5>
                </div>
                <div class="card-body p-4">
                    <?php if ($job_data): ?>
                        <dl class="row">
                            <dt class="col-sm-3 text-muted">รหัสงาน:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($job_data['job_code']); ?></dd>
                            
                            <dt class="col-sm-3 text-muted">ลูกค้า:</dt>
                            <dd class="col-sm-9 fw-bold"><?php echo htmlspecialchars($job_data['customer_name']); ?></dd>
                            
                            <dt class="col-sm-3 text-muted">อ้างอิงการวัดตัว:</dt>
                            <dd class="col-sm-9">
                                <?php if (!empty($job_data['measurement_id'])): ?>
                                    <a href="view_measurement.php?id=<?php echo $job_data['measurement_id']; ?>">
                                        ดูข้อมูลที่วัดเมื่อวันที่ <?php echo htmlspecialchars($job_data['measurement_date']); ?>
                                    </a>
                                <?php else: ?>
                                    - (ไม่มีการอ้างอิง)
                                <?php endif; ?>
                            </dd>
                            
                            <dt class="col-sm-3 text-muted">ชื่องาน:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($job_data['title']); ?></dd>
                            
                            <dt class="col-sm-3 text-muted">สถานะ:</dt>
                            <dd class="col-sm-9">
                                <?php 
                                $status_thai = isset($status_map[$job_data['status']]) ? $status_map[$job_data['status']] : htmlspecialchars($job_data['status']);
                                echo $status_thai; 
                                ?>
                            </dd>

                            <dt class="col-sm-3 text-muted">วันที่รับงาน:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars(date('Y-m-d', strtotime($job_data['received_at']))); ?></dd>
                            
                            <dt class="col-sm-3 text-muted">วันที่กำหนดส่ง:</dt>
                            <dd class="col-sm-9"><?php echo htmlspecialchars($job_data['due_date'] ?? '-'); ?></dd>
                            
                            <dt class="col-sm-3 text-muted">รายละเอียด:</dt>
                            <dd class="col-sm-9"><?php echo nl2br(htmlspecialchars($job_data['description'] ?? '-')); ?></dd>
                        </dl>
                        
                        <?php if (!empty($job_data['completed_image'])): ?>
                            <hr>
                            <h5 class="mt-4 mb-3">รูปภาพงาน</h5>
                            <div>
                                <a href="uploads/<?php echo htmlspecialchars($job_data['completed_image']); ?>" target="_blank">
                                    <img src="uploads/<?php echo htmlspecialchars($job_data['completed_image']); ?>" class="img-fluid rounded shadow-sm" style="max-height: 400px; border: 1px solid #dee2e6;" alt="ภาพงาน">
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <hr>
                        <div class="mt-4">
                            <a href="<?php echo BASE_URL; ?>edit_job.php?id=<?php echo $job_data['job_id']; ?>" class="btn btn-warning"><i class="bi bi-pencil-fill me-2"></i>แก้ไขข้อมูล</a>
                            <a href="<?php echo BASE_URL; ?>job.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle-fill me-2"></i>กลับไปหน้ารายการ</a>
                        </div>
                    <?php else: ?>
                        <?php echo $message; ?>
                        <a href="<?php echo BASE_URL; ?>job.php" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left-circle-fill me-2"></i>กลับไปหน้ารายการ</a>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>