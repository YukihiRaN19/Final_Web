<?php 
$page_title = "แดชบอร์ด";
include 'includes/header.php';
include 'includes/config.php';

// ส่วนดึงข้อมูลสรุป
$total_customers_sql = "SELECT COUNT(customer_id) as total FROM customer";
$total_customers_result = $conn->query($total_customers_sql);
$total_customers = $total_customers_result->fetch_assoc()['total'];

$pending_jobs_sql = "SELECT COUNT(job_id) as total FROM jobs WHERE status IN ('pending', 'in_progress')";
$pending_jobs_result = $conn->query($pending_jobs_sql);
$pending_jobs = $pending_jobs_result->fetch_assoc()['total'];

$completed_jobs_sql = "SELECT COUNT(job_id) as total FROM jobs WHERE status = 'completed'";
$completed_jobs_result = $conn->query($completed_jobs_sql);
$completed_jobs = $completed_jobs_result->fetch_assoc()['total'];

$total_revenue_sql = "SELECT SUM(amount) as total FROM payments";
$total_revenue_result = $conn->query($total_revenue_sql);
$total_revenue = $total_revenue_result->fetch_assoc()['total'];

// สร้างตัวแปลภาษา
$status_map = [
    'pending' => 'ยังไม่เสร็จ',

];
$method_map = [
    'cash' => 'เงินสด',
    'bank_transfer' => 'โอนเงิน',
    'credit_card' => 'บัตรเครดิต',
    'mobile_banking' => 'Mobile Banking',
    'other' => 'อื่นๆ'
];
?>

<style>
.stat-link .card { transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; }
.stat-link:hover .card { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
.stat-box .icon { font-size: 2.2em; }
.quick-actions .btn {
    width: 100%;
    text-align: left;
    padding: 1rem;
    font-size: 1.1rem;
}
</style>

<div class="container-fluid p-4">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded-3 shadow-sm mb-4">
      <div class="container-fluid">
        <a class="navbar-brand fs-4" href="main.php">
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
            <a href="<?php echo BASE_URL; ?>main.php" class="nav-link active"><i class="bi bi-house-door-fill me-2"></i>แดชบอร์ด</a>
            <a href="<?php echo BASE_URL; ?>customer.php" class="nav-link"><i class="bi bi-people-fill me-2"></i>จัดการลูกค้า</a>
            <a href="<?php echo BASE_URL; ?>measurement.php" class="nav-link"><i class="bi bi-rulers me-2"></i>จัดการข้อมูลวัดตัว</a>
            <a href="<?php echo BASE_URL; ?>job.php" class="nav-link"><i class="bi bi-scissors me-2"></i>จัดการงาน</a>
            <a href="<?php echo BASE_URL; ?>payment.php" class="nav-link"><i class="bi bi-credit-card-fill me-2"></i>จัดการชำระเงิน</a>
        </nav>

        <main class="w-100">
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <a href="<?php echo BASE_URL; ?>customer.php" class="text-decoration-none stat-link">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="display-4 text-primary me-3"><i class="bi bi-people-fill"></i></div>
                                <div>
                                    <div class="text-muted">ลูกค้าทั้งหมด</div>
                                    <div class="h4 fw-bold mb-0"><?php echo $total_customers; ?></div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                     <a href="<?php echo BASE_URL; ?>pending_jobs.php" class="text-decoration-none stat-link">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="display-4 text-warning me-3"><i class="bi bi-clock-history"></i></div>
                                <div>
                                    <div class="text-muted">งานที่ต้องทำ</div>
                                    <div class="h4 fw-bold mb-0"><?php echo $pending_jobs; ?></div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                     <a href="<?php echo BASE_URL; ?>completed_jobs.php" class="text-decoration-none stat-link">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="display-4 text-success me-3"><i class="bi bi-check-circle-fill"></i></div>
                                <div>
                                    <div class="text-muted">งานที่เสร็จแล้ว</div>
                                    <div class="h4 fw-bold mb-0"><?php echo $completed_jobs; ?></div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="<?php echo BASE_URL; ?>payment.php" class="text-decoration-none stat-link">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="display-4 text-info me-3"><i class="bi bi-cash-stack"></i></div>
                                <div>
                                    <div class="text-muted">รายรับทั้งหมด</div>
                                    <div class="h4 fw-bold mb-0"><?php echo number_format($total_revenue ?? 0, 2); ?></div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <a href="<?php echo BASE_URL; ?>pending_jobs.php" class="text-dark text-decoration-none">
                                    <i class="bi bi-card-list me-2"></i>งานที่ยังไม่เสร็จ
                                </a>
                            </h5>
                            <a href="<?php echo BASE_URL; ?>pending_jobs.php" class="btn btn-sm btn-outline-secondary">ดูทั้งหมด</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>รหัสงาน</th>
                                        <th>ลูกค้า</th>
                                        <th>สถานะ</th>
                                        <th class="text-end">การจัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $jobs_sql = "SELECT j.job_id, j.job_code, j.status, c.customer_name 
                                                 FROM jobs j 
                                                 JOIN customer c ON j.customer_id = c.customer_id 
                                                 WHERE j.status IN ('pending', 'in_progress') 
                                                 ORDER BY j.received_at DESC LIMIT 5";
                                    $jobs_result = $conn->query($jobs_sql);
                                    if ($jobs_result && $jobs_result->num_rows > 0) {
                                        while($row = $jobs_result->fetch_assoc()) {
                                            $status_thai = isset($status_map[$row['status']]) ? $status_map[$row['status']] : htmlspecialchars($row['status']);
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['job_code']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                            echo "<td>" . $status_thai . "</td>";
                                            echo "<td class='text-end'><a href='view_job.php?id=" . $row['job_id'] . "' class='btn btn-sm btn-info'><i class='bi bi-eye-fill'></i> ดู</a></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center text-muted'>ไม่พบข้อมูลงงาน</td></tr>";
                                    }

                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white fw-bold"><i class="bi bi-wallet-fill me-2"></i>รายการชำระเงินล่าสุด</div>
                        <div class="card-body">
                             <table class="table table-striped table-hover">
                                <thead><tr><th>รหัสงาน</th><th>จำนวนเงิน</th><th>วิธีชำระ</th></tr></thead>
                                <tbody>
                                <?php
                                    $payments_sql = "SELECT p.amount, p.method, j.job_code FROM payments p JOIN jobs j ON p.job_id = j.job_id ORDER BY p.payment_date DESC LIMIT 5";
                                    $payments_result = $conn->query($payments_sql);
                                    if ($payments_result && $payments_result->num_rows > 0) {
                                        while($row = $payments_result->fetch_assoc()) {
                                            $method_thai = isset($method_map[$row['method']]) ? $method_map[$row['method']] : htmlspecialchars($row['method']);
                                            echo "<tr><td>" . htmlspecialchars($row['job_code']) . "</td><td>" . number_format($row['amount'], 2) . "</td><td>" . $method_thai . "</td></tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='text-center text-muted'>ยังไม่มีรายการชำระเงิน</td></tr>";
                                    }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white fw-bold"><i class="bi bi-lightning-fill me-2"></i>ทางลัด</div>
                        <div class="card-body d-flex flex-column gap-3 justify-content-center quick-actions">
                            <a href="<?php echo BASE_URL; ?>add_customer.php" class="btn btn-outline-success fw-bold">
                                <i class="bi bi-person-plus-fill me-2"></i> เพิ่มลูกค้าใหม่
                            </a>
                            <a href="<?php echo BASE_URL; ?>add_measurement.php" class="btn btn-outline-warning fw-bold">
                                <i class="bi bi-rulers me-2"></i> เพิ่มข้อมูลวัดตัว
                            </a>
                            <a href="<?php echo BASE_URL; ?>add_job.php" class="btn btn-outline-primary fw-bold">
                                <i class="bi bi-scissors me-2"></i> เพิ่มงานใหม่
                            </a>
                            <a href="<?php echo BASE_URL; ?>add_payment.php" class="btn btn-outline-info fw-bold">
                                <i class="bi bi-cash me-2"></i> บันทึกการชำระเงิน
                            </a>
                        </div>
                    </div>
                </div>                
            </div>
        </main>
    </div>
</div>

<?php 
$conn->close();
include 'includes/footer.php'; 
?>