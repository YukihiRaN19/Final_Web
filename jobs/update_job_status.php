<?php
require_once "includes/config.php";

$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$new_status = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : '';
$return_page = isset($_GET['return_page']) ? htmlspecialchars($_GET['return_page']) : 'job.php';


// ตรวจสอบว่าค่าที่ส่งมาถูกต้อง
if ($job_id > 0 && in_array($new_status, ['pending', 'completed', 'cancelled'])) {
    
    $sql = "UPDATE jobs SET status = ? WHERE job_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("si", $new_status, $job_id);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();

// กลับไปยังหน้าเดิมที่กดมา
header("Location: " . BASE_URL . $return_page);
exit();
?>