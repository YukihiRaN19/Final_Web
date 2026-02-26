<?php 
$page_title = "แก้ไขงาน";
include 'includes/header.php';
include 'includes/config.php';
$message = "";
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$job_data = null;

// ดึงรายชื่อลูกค้า
$customers = [];
$customer_sql = "SELECT customer_id, customer_name FROM customer ORDER BY customer_name ASC";
$customer_result = $conn->query($customer_sql);
if ($customer_result && $customer_result->num_rows > 0) {
    while($row = $customer_result->fetch_assoc()) {
        $customers[] = $row;
    }
}

// จัดการการอัปเดตข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $new_image_name = "";
    if (isset($_FILES['job_image']) && $_FILES['job_image']['error'] == 0) {
        $target_dir = "uploads/";
        $image_extension = strtolower(pathinfo($_FILES["job_image"]["name"], PATHINFO_EXTENSION));
        $new_image_name = uniqid('job_') . '.' . $image_extension;
        $target_file = $target_dir . $new_image_name;
        if (!move_uploaded_file($_FILES["job_image"]["tmp_name"], $target_file)) {
            $message = "<div class='alert alert-danger'>❌ ขออภัย, เกิดข้อผิดพลาดในการอัปโหลดไฟล์</div>";
            $new_image_name = ""; 
        }
    }
    
    $posted_id = intval($_POST['job_id']);
    $customer_id = intval($_POST['customer_id']);
    $measurement_id = !empty($_POST['measurement_id']) ? intval($_POST['measurement_id']) : null;
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $status = htmlspecialchars($_POST['status']);
    $due_date = !empty($_POST['due_date']) ? htmlspecialchars($_POST['due_date']) : null;
    $image_to_save = $new_image_name ? $new_image_name : $_POST['existing_image'];


    $sql = "UPDATE jobs SET customer_id=?, measurement_id=?, title=?, description=?, status=?, due_date=?, completed_image=? WHERE job_id=?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iisssssi", $customer_id, $measurement_id, $title, $description, $status, $due_date, $image_to_save, $posted_id);
        if ($stmt->execute()) {
            header("Location: " . BASE_URL . "job.php?status=editsuccess");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>❌ เกิดข้อผิดพลาด: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// ดึงข้อมูลเดิมมาแสดง
if ($job_id > 0) {
    $sql_fetch = "SELECT * FROM jobs WHERE job_id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    if ($stmt_fetch) {
        $stmt_fetch->bind_param("i", $job_id);
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();
        if ($result->num_rows === 1) {
            $job_data = $result->fetch_assoc();
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
                    <h5 class="mb-0"><i class="bi bi-pencil-fill me-2"></i><?php echo $page_title; ?> (รหัส: <?php echo htmlspecialchars($job_data['job_code'] ?? ''); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <?php if ($job_data): ?>
                    <form action="<?php echo BASE_URL; ?>edit_job.php?id=<?php echo $job_id; ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="job_id" value="<?php echo $job_data['job_id']; ?>">
                        <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($job_data['completed_image']); ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_id" class="form-label">ลูกค้า</label>
                                <select class="form-select" id="customer_id" name="customer_id" required>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['customer_id']; ?>" <?php if($customer['customer_id'] == $job_data['customer_id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($customer['customer_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="measurement_id" class="form-label">อ้างอิงข้อมูลวัดตัว (ถ้ามี)</label>
                                <select class="form-select" id="measurement_id" name="measurement_id">
                                    <option value="">-- ไม่ต้องอ้างอิง --</option>
                                </select>
                            </div>
                        </div>
                        <div id="measurement_details" class="alert alert-secondary" style="display: none;"></div>

                        <div class="mb-3">
                            <label for="title" class="form-label">ชื่องาน</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($job_data['title']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">รายละเอียดเพิ่มเติม</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($job_data['description']); ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">สถานะงาน</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" <?php if(in_array($job_data['status'], ['pending', 'in_progress', 'ready'])) echo 'selected'; ?>>ยังไม่เสร็จ</option>
                                    <option value="completed" <?php if(in_array($job_data['status'], ['delivered', 'completed'])) echo 'selected'; ?>>เสร็จแล้ว</option>
                                    <option value="cancelled" <?php if($job_data['status'] == 'cancelled') echo 'selected'; ?>>ยกเลิก</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label">วันที่ส่ง</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo htmlspecialchars($job_data['due_date']); ?>">
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="mb-3">
                            <label for="job_image" class="form-label">รูปภาพงาน (อัปโหลดใหม่ถ้าต้องการเปลี่ยน)</label>
                            <input class="form-control" type="file" id="job_image" name="job_image">
                            <?php if (!empty($job_data['completed_image'])): ?>
                                <div class="mt-2">
                                    <small class="text-muted d-block mb-1">รูปภาพปัจจุบัน:</small>
                                    <a href="uploads/<?php echo htmlspecialchars($job_data['completed_image']); ?>" target="_blank">
                                        <img src="uploads/<?php echo htmlspecialchars($job_data['completed_image']); ?>" height="100" class="img-thumbnail">
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-warning"><i class="bi bi-check-circle-fill me-2"></i>บันทึกการแก้ไข</button>
                            <a href="<?php echo BASE_URL; ?> job.php" class="btn btn-secondary"><i class="bi bi-x-circle-fill me-2"></i>ยกเลิก</a>
                        </div>
                    </form>
                    <?php endif; ?>
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
    const initialCustomerId = '<?php echo $job_data["customer_id"] ?? 0; ?>';
    const initialMeasurementId = '<?php echo $job_data["measurement_id"] ?? 0; ?>';

    function fetchMeasurements(customerId, selectedMeasurementId) {
        measurementSelect.innerHTML = '<option value="">กำลังโหลด...</option>';
        if (customerId) {
            fetch(`get_measurements.php?customer_id=${customerId}`)
                .then(response => response.json())
                .then(data => {
                    measurementSelect.innerHTML = '<option value="">-- ไม่ต้องอ้างอิง --</option>';
                    if (data.length > 0) {
                        data.forEach(m => {
                            const option = document.createElement('option');
                            option.value = m.measurement_id;
                            option.textContent = `ครั้งที่ ${m.rn} (วันที่: ${m.date_taken})`;
                            if (m.measurement_id == selectedMeasurementId) {
                                option.selected = true;
                            }
                            measurementSelect.appendChild(option);
                        });
                        if(selectedMeasurementId > 0) {
                           measurementSelect.dispatchEvent(new Event('change'));
                        }
                    } else {
                        measurementSelect.innerHTML = '<option value="" disabled>-- ไม่พบข้อมูลวัดตัว --</option>';
                    }
                });
        } else {
            measurementSelect.innerHTML = '<option value="">-- เลือกลูกค้าก่อน --</option>';
        }
    }

    function fetchMeasurementDetails() {
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
    }

    if (initialCustomerId > 0) {
        fetchMeasurements(initialCustomerId, initialMeasurementId);
    }
    
    customerSelect.addEventListener('change', () => fetchMeasurements(customerSelect.value, 0));
    measurementSelect.addEventListener('change', fetchMeasurementDetails);
});
</script>


<?php 
include 'includes/footer.php'; 
?>