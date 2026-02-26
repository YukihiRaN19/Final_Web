<?php 
$page_title = "จัดการชำระเงิน";
include 'includes/header.php';
include 'includes/config.php';
$message = "";
$where_clause = "";
$filter_query_string = "";
$search_query_string = "";
$active_method = isset($_GET['method']) ? $_GET['method'] : 'all';
$search_term = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$params = [];
$types = "";

// --- สร้างตัวแปลภาษา ---
$method_map = [
    'cash' => 'เงินสด',
    'bank_transfer' => 'โอนเงิน',
    'credit_card' => 'บัตรเครดิต',
    'mobile_banking' => 'Mobile Banking',
    'other' => 'อื่นๆ'
];

// --- การตั้งค่า Pagination ---
$records_per_page = 10;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// --- จัดการการกรองข้อมูล (Filter & Search) ---
$conditions = [];
if (isset($_GET['method']) && in_array($_GET['method'], ['cash', 'bank_transfer', 'credit_card', 'mobile_banking', 'other'])) {
    $method = $_GET['method'];
    $conditions[] = "p.method = ?";
    $types .= "s";
    $params[] = $method;
    $filter_query_string = "&method=" . $method;
}
if (!empty($search_term)) {
    $conditions[] = "(j.job_code LIKE ? OR c.customer_name LIKE ?)";
    $types .= "ss";
    $search_param = "%" . $search_term . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $search_query_string = "&search=" . urlencode($search_term);
}
if (!empty($conditions)) {
    $where_clause = " WHERE " . implode(" AND ", $conditions);
}

// --- ส่วนดึงข้อมูลสรุป (Stats) ---
$total_revenue_sql = "SELECT SUM(amount) as total FROM payments";
$total_revenue_result = $conn->query($total_revenue_sql);
$total_revenue = $total_revenue_result->fetch_assoc()['total'];

$transaction_count_sql = "SELECT COUNT(payment_id) as total FROM payments";
$transaction_count_result = $conn->query($transaction_count_sql);
$transaction_count = $transaction_count_result->fetch_assoc()['total'];

// --- ดึงจำนวนข้อมูลทั้งหมด (สำหรับคำนวณหน้า) ---
$total_records_sql = "SELECT COUNT(p.payment_id) as total 
                      FROM payments p 
                      JOIN jobs j ON p.job_id = j.job_id
                      JOIN customer c ON j.customer_id = c.customer_id" . $where_clause;
$stmt_total = $conn->prepare($total_records_sql);
if (!empty($params)) {
    $stmt_total->bind_param($types, ...$params);
}
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_records = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);
$stmt_total->close();

// --- โค้ด PHP สำหรับแสดงข้อความแจ้งเตือน ---
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'deletesuccess') $message = "<div class='alert alert-success'><i class='bi bi-check-circle-fill'></i> ลบข้อมูลสำเร็จ!</div>";
    if ($_GET['status'] == 'addsuccess') $message = "<div class='alert alert-success'><i class='bi bi-check-circle-fill'></i> บันทึกสำเร็จ!</div>";
    if ($_GET['status'] == 'editsuccess') $message = "<div class='alert alert-success'><i class='bi bi-check-circle-fill'></i> แก้ไขสำเร็จ!</div>";
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
            <h4 class="menu-title">เมนูจัดการ</h4>
            <a class="nav-link" href="<?php echo BASE_URL; ?>main.php"><i class="bi bi-house-door-fill me-2"></i>แดชบอร์ด</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>customer.php"><i class="bi bi-people-fill me-2"></i>จัดการลูกค้า</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>measurement.php"><i class="bi bi-rulers me-2"></i>จัดการข้อมูลวัดตัว</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>job.php"><i class="bi bi-scissors me-2"></i>จัดการงาน</a>
            <a class="nav-link active" href="<?php echo BASE_URL; ?>payment.php"><i class="bi bi-credit-card-fill me-2"></i>จัดการชำระเงิน</a>
        </nav>

        <main class="w-100">
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon text-primary me-3 fs-1"><i class="bi bi-cash-stack"></i></div>
                            <div>
                                <div class="text-muted">รายรับทั้งหมด</div>
                                <div class="h4 fw-bold mb-0"><?php echo number_format($total_revenue ?? 0, 2); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                     <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon text-success me-3 fs-1"><i class="bi bi-receipt"></i></div>
                            <div>
                                <div class="text-muted">จำนวนรายการ</div>
                                <div class="h4 fw-bold mb-0"><?php echo $transaction_count; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                     <a href="<?php echo BASE_URL; ?>add_payment.php" class="text-decoration-none">
                        <div class="card card-body bg-primary text-white h-100 d-flex justify-content-center align-items-center shadow-sm border-0">
                           <i class="bi bi-plus-circle-dotted fs-1"></i>
                           <span class="mt-2 fw-bold">บันทึกการชำระเงิน</span>
                        </div>
                    </a>
                </div>
            </div>

            <?php echo $message; ?>

            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0">รายการชำระเงิน</h5>
                    <div class="d-flex gap-2 mt-2 mt-md-0">
                        <div class="btn-group" role="group">
                            <a href="<?php echo BASE_URL; ?>payment.php?search=<?php echo urlencode($search_term); ?>" class="btn btn-sm <?php echo ($active_method == 'all') ? 'btn-primary' : 'btn-outline-secondary'; ?>">ทั้งหมด</a>
                            <a href="<?php echo BASE_URL; ?>payment.php?method=cash&search=<?php echo urlencode($search_term); ?>" class="btn btn-sm <?php echo ($active_method == 'cash') ? 'btn-primary' : 'btn-outline-secondary'; ?>">เงินสด</a>
                            <a href="<?php echo BASE_URL; ?>payment.php?method=bank_transfer&search=<?php echo urlencode($search_term); ?>" class="btn btn-sm <?php echo ($active_method == 'bank_transfer') ? 'btn-primary' : 'btn-outline-secondary'; ?>">โอนเงิน</a>
                        </div>
                        <form action="<?php echo BASE_URL; ?>payment.php" method="get" class="d-flex">
                             <?php if (isset($_GET['method']) && $_GET['method'] != 'all'): ?>
                                <input type="hidden" name="method" value="<?php echo htmlspecialchars($_GET['method']); ?>">
                            <?php endif; ?>
                            <input class="form-control form-control-sm me-2" type="search" name="search" placeholder="ค้นหารหัสงาน, ลูกค้า..." value="<?php echo $search_term; ?>">
                            <button class="btn btn-sm btn-outline-primary" type="submit">ค้นหา</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">วันที่ชำระ</th>
                                    <th scope="col">รหัสงาน</th>
                                    <th scope="col">ลูกค้า</th>
                                    <th scope="col">วันที่รับงาน</th>
                                    <th scope="col">จำนวนเงิน (บาท)</th>
                                    <th scope="col">วิธีชำระ</th>
                                    <th scope="col" class="text-end">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $list_sql = "SELECT p.payment_id, p.payment_date, p.amount, p.method, j.job_code, c.customer_name, j.received_at
                                             FROM payments p
                                             JOIN jobs j ON p.job_id = j.job_id 
                                             JOIN customer c ON j.customer_id = c.customer_id" . $where_clause . "
                                             ORDER BY p.payment_date DESC
                                             LIMIT ? OFFSET ?";
                                
                                $stmt = $conn->prepare($list_sql);
                                $current_types = $types . "ii";
                                $current_params = array_merge($params, [$records_per_page, $offset]);
                                $stmt->bind_param($current_types, ...$current_params);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result && $result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $method_thai = isset($method_map[$row['method']]) ? $method_map[$row['method']] : htmlspecialchars($row['method']);
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars(date('Y-m-d', strtotime($row['payment_date']))) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['job_code']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['received_at']) . "</td>";
                                        echo "<td>" . number_format($row['amount'], 2) . "</td>";
                                        echo "<td>" . $method_thai . "</td>";
                                        echo "<td class='text-end'>";
                                        echo "<a href='" . BASE_URL . "view_payment.php?id=" . $row['payment_id'] . "' class='btn btn-sm btn-info me-1'><i class='bi bi-eye-fill'></i> ดู</a> ";
                                        echo "<a href='" . BASE_URL . "edit_payment.php?id=" . $row['payment_id'] . "' class='btn btn-sm btn-warning me-1'><i class='bi bi-pencil-fill'></i> แก้ไข</a> ";
                                        echo "<a href='" . BASE_URL . "delete_payment.php?id=" . $row['payment_id'] . "' class='btn btn-sm btn-danger'><i class='bi bi-trash-fill'></i> ลบ</a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center text-muted py-3'>ไม่พบข้อมูลการชำระเงิน</td></tr>";
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
                                <a class="page-link" href="?page=<?php echo $current_page - 1; ?><?php echo $filter_query_string; ?><?php echo $search_query_string; ?>">Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php if($i == $current_page){ echo 'active'; } ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo $filter_query_string; ?><?php echo $search_query_string; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php if($current_page >= $total_pages){ echo 'disabled'; } ?>">
                                <a class="page-link" href="?page=<?php echo $current_page + 1; ?><?php echo $filter_query_string; ?><?php echo $search_query_string; ?>">Next</a>
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