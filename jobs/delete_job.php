<?php 
$page_title = "ยืนยันการลบงาน";
include 'includes/header.php';
include 'includes/config.php';
$job_data = null;
$message = "";
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// จัดการการลบข้อมูลเมื่อกดยืนยัน
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_to_delete = intval($_POST['job_id']);
    if ($id_to_delete > 0) {
        $sql = "DELETE FROM jobs WHERE job_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $id_to_delete);
            if ($stmt->execute()) {
                header("Location: " . BASE_URL . "job.php?status=deletesuccess");
                exit();
            } else {
                $message = "<div class='alert alert-danger'>❌ เกิดข้อผิดพลาดในการลบข้อมูล: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
    }
}

// ดึงข้อมูลมาแสดงเพื่อยืนยัน
if ($job_id > 0) {
    $sql_fetch = "SELECT j.job_id, j.job_code, j.title, c.customer_name 
                  FROM jobs j
                  JOIN customer c ON j.customer_id = c.customer_id
                  WHERE j.job_id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    if ($stmt_fetch) {
        $stmt_fetch->bind_param("i", $job_id);
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();
        if ($result->num_rows === 1) {
            $job_data = $result->fetch_assoc();
        } else {
            $message = "<div class='alert alert-warning'>❌ ไม่พบข้อมูลงาน</div>";
        }
        $stmt_fetch->close();
    }
} else {
    $message = "<div class='alert alert-danger'>❌ ID ไม่ถูกต้อง</div>";
}
$conn->close();
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-danger">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="card-title h4">คุณแน่ใจหรือไม่?</h2>
                    
                    <?php if ($job_data): ?>
                        <p class="text-muted">คุณกำลังจะลบข้อมูลงานอย่างถาวร:</p>
                        <div class="alert alert-light text-start">
                            <strong>รหัสงาน:</strong> <?php echo htmlspecialchars($job_data['job_code']); ?><br>
                            <strong>ชื่องาน:</strong> <?php echo htmlspecialchars($job_data['title']); ?><br>
                            <strong>ลูกค้า:</strong> <?php echo htmlspecialchars($job_data['customer_name']); ?>
                        </div>
                        <p class="text-danger fw-bold mt-3">การกระทำนี้ ไม่สามารถย้อนกลับได้</p>
                        
                        <form method="post" action="<?php echo BASE_URL; ?>delete_job.php">
                            <input type="hidden" name="job_id" value="<?php echo $job_data['job_id']; ?>">
                            <button type="submit" class="btn btn-danger px-4 me-2"><i class="bi bi-trash-fill me-2"></i>ยืนยันการลบ</button>
                            <a href="<?php echo BASE_URL; ?>job.php" class="btn btn-secondary px-4"><i class="bi bi-x-circle-fill me-2"></i>ยกเลิก</a>
                        </form>
                    <?php else: ?>
                        <?php echo $message; ?>
                        <a href="<?php echo BASE_URL; ?>job.php" class="btn btn-secondary mt-3">กลับไปหน้ารายการ</a>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>