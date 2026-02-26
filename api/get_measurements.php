<?php
require_once "includes/config.php";

$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
$measurements = [];

if ($customer_id > 0) {
    if ($conn) {
        $sql = "SELECT 
                    measurement_id, 
                    date_taken, 
                    remarks,
                    ROW_NUMBER() OVER (PARTITION BY customer_id ORDER BY date_taken DESC) as rn
                FROM measurement 
                WHERE customer_id = ? 
                ORDER BY date_taken DESC";
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $customer_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $measurements[] = $row;
                }
            }
            $stmt->close();
        }
        $conn->close();
    }
}

header('Content-Type: application/json');
echo json_encode($measurements);
?>