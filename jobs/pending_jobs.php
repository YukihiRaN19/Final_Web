<?php 
$page_title = "งานที่ยังไม่เสร็จ";
include 'includes/header.php';
include 'includes/config.php';
$message = "";

// --- สร้างตัวแปลภาษาสำหรับสถานะงาน ---
$status_map = [
    'pending' => 'ยังไม่เสร็จ',
];

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
            <a class="nav-link" href="<?php echo BASE_URL; ?>customer.php"><i class="bi bi-people-fill me-2"></i>จัดการลูกค้า</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>measurement.php"><i class="bi bi-rulers me-2"></i>จัดการข้อมูลวัดตัว</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>job.php"><i class="bi bi-scissors me-2"></i>จัดการงาน</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>payment.php"><i class="bi bi-credit-card-fill me-2"></i>จัดการชำระเงิน</a>
        </nav>

        <main class="w-100">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 text-dark"><i class="bi bi-clock-history me-2"></i><?php echo $page_title; ?></h1>
            </div>

            <?php echo $message; ?>

            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">รหัสงาน</th>
                                    <th scope="col">ชื่องาน</th>
                                    <th scope="col">ลูกค้า</th>
                                    <th scope="col">สถานะ</th>
                                    <th scope="col" class="text-end">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $list_sql = "SELECT j.job_id, j.job_code, j.title, j.status, c.customer_name 
                                             FROM jobs j
                                             JOIN customer c ON j.customer_id = c.customer_id
                                             WHERE j.status = 'pending'
                                             ORDER BY j.received_at DESC";
                                $result = $conn->query($list_sql);

                                if ($result && $result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $status_thai = isset($status_map[$row['status']]) ? $status_map[$row['status']] : htmlspecialchars($row['status']);
                                        
                                        $status_badge = '<span class="badge bg-warning text-dark">' . $status_thai . '</span>';
                                        
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['job_code']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                        echo "<td>" . $status_badge . "</td>";
                                        echo "<td class='text-end'>";
                                        echo "<a href='" . BASE_URL . "view_job.php?id=" . $row['job_id'] . "' class='btn btn-sm btn-info me-1'><i class='bi bi-eye-fill'></i> ดู</a> ";
                                        echo "<a href='" . BASE_URL . "edit_job.php?id=" . $row['job_id'] . "' class='btn btn-sm btn-warning me-1'><i class='bi bi-pencil-fill'></i> แก้ไข</a> ";
                                        echo "<a href='" . BASE_URL . "delete_job.php?id=" . $row['job_id'] . "' class='btn btn-sm btn-danger'><i class='bi bi-trash-fill'></i> ลบ</a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center text-muted py-3'>ไม่พบข้อมูลงานที่ยังไม่เสร็จ</td></tr>";
                                }
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="text-start mt-3">
                <a href="<?php echo BASE_URL; ?>job.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle-fill me-2"></i>กลับไปหน้ารวม</a>
            </div>
            
        </main>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>