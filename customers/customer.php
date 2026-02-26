<?php 
$page_title = "จัดการลูกค้า";
include 'includes/header.php';
include 'includes/config.php';
$message = "";
$search_term = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$where_clause = '';
$search_query_string = '';

// --- สร้างเงื่อนไข SQL สำหรับการค้นหา ---
if (!empty($search_term)) {
    $where_clause = " WHERE customer_name LIKE ?";
    $search_query_string = "&search=" . urlencode($search_term);
}

// --- การตั้งค่า Pagination ---
$records_per_page = 10;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// --- ส่วนดึงข้อมูลสรุป (Stats) ---
$total_customers_sql = "SELECT COUNT(customer_id) as total FROM customer";
$total_customers_result = $conn->query($total_customers_sql);
$total_customers = $total_customers_result->fetch_assoc()['total'];

$total_jobs_sql = "SELECT COUNT(job_id) as total FROM jobs";
$total_jobs_result = $conn->query($total_jobs_sql);
$total_jobs = $total_jobs_result->fetch_assoc()['total'];

// --- ดึงจำนวนข้อมูลทั้งหมด (สำหรับคำนวณหน้า) ---
$total_records_sql = "SELECT COUNT(customer_id) as total FROM customer" . $where_clause;
$stmt_total = $conn->prepare($total_records_sql);
if (!empty($where_clause)) {
    $search_param = "%" . $search_term . "%";
    $stmt_total->bind_param("s", $search_param);
}
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_records = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);
$stmt_total->close();

// --- โค้ด PHP สำหรับแสดงข้อความแจ้งเตือน ---
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'deletesuccess') $message = "<div class='alert alert-success'><i class='bi bi-check-circle-fill'></i> ลบข้อมูลลูกค้าสำเร็จ!</div>";
    if ($_GET['status'] == 'addsuccess') $message = "<div class='alert alert-success'><i class='bi bi-check-circle-fill'></i> เพิ่มข้อมูลลูกค้าใหม่สำเร็จ!</div>";
    if ($_GET['status'] == 'editsuccess') $message = "<div class='alert alert-success'><i class='bi bi-check-circle-fill'></i> แก้ไขข้อมูลลูกค้าสำเร็จ!</div>";
}
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $customer_id_to_delete = intval($_GET['id']);
    $sql = "DELETE FROM customer WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $customer_id_to_delete);
        if ($stmt->execute()) {
            header("Location: customer.php?status=deletesuccess");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>❌ เกิดข้อผิดพลาดในการลบข้อมูล: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}
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
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon text-primary me-3 fs-1"><i class="bi bi-people-fill"></i></div>
                            <div>
                                <div class="text-muted">ลูกค้าทั้งหมด</div>
                                <div class="h4 fw-bold mb-0"><?php echo $total_customers; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon text-secondary me-3 fs-1"><i class="bi bi-scissors"></i></div>
                            <div>
                                <div class="text-muted">งานทั้งหมด</div>
                                <div class="h4 fw-bold mb-0"><?php echo $total_jobs; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                     <a href="<?php echo BASE_URL; ?>add_customer.php" class="text-decoration-none">
                        <div class="card card-body bg-primary text-white h-100 d-flex justify-content-center align-items-center shadow-sm border-0">
                           <i class="bi bi-plus-circle-dotted fs-1"></i>
                           <span class="mt-2 fw-bold">เพิ่มลูกค้าใหม่</span>
                        </div>
                    </a>
                </div>
            </div>

            <?php echo $message; ?>

            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0">รายชื่อลูกค้า</h5>
                    <form action="<?php echo BASE_URL; ?>customer.php" method="get" class="d-flex mt-2 mt-md-0">
                        <input class="form-control me-2" type="search" name="search" placeholder="ค้นหาชื่อลูกค้า..." value="<?php echo $search_term; ?>">
                        <button class="btn btn-outline-primary" type="submit">ค้นหา</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">ชื่อ-นามสกุล ลูกค้า</th>
                                    <th scope="col" class="text-end">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $list_sql = "SELECT customer_id, customer_name FROM customer" . $where_clause . " ORDER BY customer_id DESC LIMIT ? OFFSET ?";
                                $stmt = $conn->prepare($list_sql);
                                
                                if (!empty($where_clause)) {
                                    $stmt->bind_param("sii", $search_param, $records_per_page, $offset);
                                } else {
                                    $stmt->bind_param("ii", $records_per_page, $offset);
                                }
                                
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result && $result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                        echo "<td class='text-end'>";
                                        echo "<a href='" . BASE_URL . "view_customer.php?id=" . $row['customer_id'] . "' class='btn btn-sm btn-info me-1'><i class='bi bi-eye-fill'></i> ดู</a> ";
                                        echo "<a href='" . BASE_URL . "edit_customer.php?id=" . $row['customer_id'] . "' class='btn btn-sm btn-warning me-1'><i class='bi bi-pencil-fill'></i> แก้ไข</a> ";
                                        echo "<a href='" . BASE_URL . "delete_customer.php?id=" . $row['customer_id'] . "' class='btn btn-sm btn-danger'><i class='bi bi-trash-fill'></i> ลบ</a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='2' class='text-center text-muted py-3'>ไม่พบข้อมูลลูกค้า</td></tr>";
                                }
                                $stmt->close();
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="card-footer bg-white">
                    <nav>
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item <?php if($current_page <= 1){ echo 'disabled'; } ?>">
                                <a class="page-link" href="?page=<?php echo $current_page - 1; ?><?php echo $search_query_string; ?>">Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php if($i == $current_page){ echo 'active'; } ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search_query_string; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php if($current_page >= $total_pages){ echo 'disabled'; } ?>">
                                <a class="page-link" href="?page=<?php echo $current_page + 1; ?><?php echo $search_query_string; ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>