<?php 
$page_title = "เพิ่มงานใหม่";
include 'includes/header.php';
include 'includes/config.php';
$message = "";

// ดึงรายชื่อลูกค้า
$customers = [];
$customer_sql = "SELECT c.customer_id, c.customer_name FROM customer c ORDER BY c.customer_name ASC";
$customer_result = $conn->query($customer_sql);
if ($customer_result && $customer_result->num_rows > 0) {
    while($row = $customer_result->fetch_assoc()) {
        $customers[] = $row;
    }
}

// บันทึกข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $new_image_name = null;
    if (isset($_FILES['job_image']) && $_FILES['job_image']['error'] == 0) {
        $target_dir = "uploads/";
        $image_extension = strtolower(pathinfo($_FILES["job_image"]["name"], PATHINFO_EXTENSION));
        $new_image_name = uniqid('job_') . '.' . $image_extension;
        $target_file = $target_dir . $new_image_name;
        if (!move_uploaded_file($_FILES["job_image"]["tmp_name"], $target_file)) {
            $message = "<div class='alert alert-danger'>❌ ขออภัย, เกิดข้อผิดพลาดในการอัปโหลดไฟล์</div>";
            $new_image_name = null;
        }
    }
    
    $customer_id = intval($_POST['customer_id']);
    $measurement_id = !empty($_POST['measurement_id']) ? intval($_POST['measurement_id']) : null;
    $job_code = "JOB-" . time();
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $due_date = !empty($_POST['due_date']) ? htmlspecialchars($_POST['due_date']) : null;
    $received_at = date('Y-m-d H:i:s');

    $sql = "INSERT INTO jobs (customer_id, measurement_id, job_code, title, description, due_date, received_at, completed_image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iisssdss", $customer_id, $measurement_id, $job_code, $title, $description, $due_date, $received_at, $new_image_name);
        if ($stmt->execute()) {
            header("Location: " . BASE_URL . "job.php?status=addsuccess");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>❌ เกิดข้อผิดพลาด: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
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
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i><?php echo $page_title; ?></h5>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <form action="<?php echo BASE_URL; ?>add_job.php" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_id" class="form-label">เลือกลูกค้า</label>
                                <select class="form-select" id="customer_id" name="customer_id" required>
                                    <option value="" selected disabled>-- กรุณาเลือกลูกค้า --</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['customer_id']; ?>"><?php echo htmlspecialchars($customer['customer_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="measurement_id" class="form-label">อ้างอิงข้อมูลวัดตัว (ถ้ามี)</label>
                                <select class="form-select" id="measurement_id" name="measurement_id">
                                    <option value="">-- เลือกลูกค้าก่อน --</option>
                                </select>
                            </div>
                        </div>

                        <div id="measurement_details" class="alert alert-secondary" style="display: none;"></div>

                        <div class="mb-3">
                            <label for="title" class="form-label">ชื่องาน (เช่น ตัดชุดเดรส, แก้ทรงกางเกง)</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">รายละเอียดเพิ่มเติม</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="due_date" class="form-label">วันที่รับงาน</label>
                            <input type="date" class="form-control" id="due_date" name="due_date">
                        </div>
                        
                        <hr class="my-4">
                        <div class="mb-3">
                            <label for="job_image" class="form-label">รูปภาพงาน (ถ้ามี)</label>
                            <input class="form-control" type="file" id="job_image" name="job_image">
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle-fill me-2"></i>บันทึกงาน</button>
                            <a href="<?php echo BASE_URL; ?>job.php" class="btn btn-secondary"><i class="bi bi-x-circle-fill me-2"></i>ยกเลิก</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customer_id');
    const measurementSelect = document.getElementById('measurement_id');
    const detailsDiv = document.getElementById('measurement_details');

    customerSelect.addEventListener('change', function() {
        const customerId = this.value;
        measurementSelect.innerHTML = '<option value="">กำลังโหลด...</option>';
        detailsDiv.style.display = 'none';
        
        if (customerId) {
            fetch('get_measurements.php?customer_id=' + customerId)
                .then(response => response.json())
                .then(data => {
                    measurementSelect.innerHTML = '<option value="">-- ไม่ต้องอ้างอิง --</option>';
                    if (data.length > 0) {
                        data.forEach(m => {
                            const option = document.createElement('option');
                            option.value = m.measurement_id;
                            option.textContent = `ครั้งที่ ${m.rn} (วันที่: ${m.date_taken})`;
                            measurementSelect.appendChild(option);
                        });
                    } else {
                        measurementSelect.innerHTML = '<option value="" disabled>-- ไม่พบข้อมูลวัดตัว --</option>';
                    }
                });
        } else {
            measurementSelect.innerHTML = '<option value="">-- เลือกลูกค้าก่อน --</option>';
        }
    });

    measurementSelect.addEventListener('change', function() {
        const measurementId = this.value;
        
        if (measurementId) {
            fetch('get_measurement_details.php?measurement_id=' + measurementId)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        let detailsHtml = '<strong><i class="bi bi-rulers"></i> รายละเอียดการวัดตัว:</strong><div class="row mt-2 small">';
                        detailsHtml += `<div class="col-sm-4"><strong>รอบอก:</strong> ${data.chest || '-'} นิ้ว</div>`;
                        detailsHtml += `<div class="col-sm-4"><strong>รอบเอว:</strong> ${data.waist || '-'} นิ้ว</div>`;
                        detailsHtml += `<div class="col-sm-4"><strong>สะโพก:</strong> ${data.hip || '-'} นิ้ว</div>`;
                        detailsHtml += `<div class="col-sm-4"><strong>ไหล่:</strong> ${data.shoulder || '-'} นิ้ว</div>`;
                        detailsHtml += `<div class="col-sm-4"><strong>ยาวแขน:</strong> ${data.sleeve_length || '-'} นิ้ว</div>`;
                        detailsHtml += `<div class="col-sm-4"><strong>ยาวกางเกง:</strong> ${data.pants_length || '-'} นิ้ว</div>`;
                        detailsHtml += `</div>`;
                        detailsDiv.innerHTML = detailsHtml;
                        detailsDiv.style.display = 'block';
                    } else {
                        detailsDiv.style.display = 'none';
                    }
                });
        } else {
            detailsDiv.style.display = 'none';
        }
    });
});
</script>

<?php 
include 'includes/footer.php'; 
?>