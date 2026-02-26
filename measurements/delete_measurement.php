<?php 
$page_title = "ยืนยันการลบข้อมูลวัดตัว";
include 'includes/header.php';
include 'includes/config.php';
$m_data = null;
$message = "";
$measurement_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// จัดการการลบข้อมูลเมื่อกดยืนยัน
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_to_delete = intval($_POST['measurement_id']);
    if ($id_to_delete > 0) {
        $sql = "DELETE FROM measurement WHERE measurement_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $id_to_delete);
            if ($stmt->execute()) {
                header("Location: " . BASE_URL . "measurement.php?status=deletesuccess");
                exit();
            } else {
                $message = "<div class='alert alert-danger'>❌ เกิดข้อผิดพลาดในการลบข้อมูล: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
    }
}

// ดึงข้อมูลมาแสดงเพื่อยืนยัน
if ($measurement_id > 0) {
    $sql_fetch = "SELECT m.measurement_id, m.date_taken, c.customer_name 
                  FROM measurement m
                  JOIN customer c ON m.customer_id = c.customer_id
                  WHERE m.measurement_id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    if ($stmt_fetch) {
        $stmt_fetch->bind_param("i", $measurement_id);
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();
        if ($result->num_rows === 1) {
            $m_data = $result->fetch_assoc();
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

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-danger">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="card-title h4">คุณแน่ใจหรือไม่?</h2>
                    
                    <?php if ($m_data): ?>
                        <p class="text-muted">คุณกำลังจะลบข้อมูลการวัดตัวของลูกค้า:</p>
                        <div class="alert alert-light text-start">
                            <strong>ลูกค้า:</strong> <?php echo htmlspecialchars($m_data['customer_name']); ?><br>
                            <strong>วันที่วัดตัว:</strong> <?php echo htmlspecialchars($m_data['date_taken']); ?>
                        </div>
                        <p class="text-danger fw-bold mt-3">การกระทำนี้ ไม่สามารถย้อนกลับได้</p>
                        
                        <form method="post" action="<?php echo BASE_URL; ?>delete_measurement.php">
                            <input type="hidden" name="measurement_id" value="<?php echo $m_data['measurement_id']; ?>">
                            <button type="submit" class="btn btn-danger px-4 me-2"><i class="bi bi-trash-fill me-2"></i>ยืนยันการลบ</button>
                            <a href="<?php echo BASE_URL; ?>measurement.php" class="btn btn-secondary px-4"><i class="bi bi-x-circle-fill me-2"></i>ยกเลิก</a>
                        </form>
                    <?php else: ?>
                        <?php echo $message; ?>
                        <a href="<?php echo BASE_URL; ?>measurement.php" class="btn btn-secondary mt-3">กลับไปหน้ารายการ</a>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>