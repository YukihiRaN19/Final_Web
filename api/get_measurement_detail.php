<?php
require_once 'includes/config.php';
$measurement_id = isset($_GET['measurement_id']) ? intval($_GET['measurement_id']) : 0;
$details = null;

if ($measurement_id > 0) {
    $sql = "SELECT * FROM measurement WHERE measurement_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $measurement_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $details = $result->fetch_assoc();
    }
    $stmt->close();
}

$conn->close();

// ส่งข้อมูลกลับไปในรูปแบบ JSON
header('Content-Type: application/json');
echo json_encode($details);
?>