<?php
include 'includes/config.php';
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "sewing"; 

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Server Connection Failed: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS `$dbname`";
if ($conn->query($sql) === TRUE) {
    echo "Database '$dbname' created successfully or already exists.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

$conn->select_db($dbname);
echo "Successfully selected database '$dbname'.<br><hr>";

$conn->query("DROP TABLE IF EXISTS payment_images");
$conn->query("DROP TABLE IF EXISTS payments");
$conn->query("DROP TABLE IF EXISTS jobs");
$conn->query("DROP TABLE IF EXISTS measurement");
$conn->query("DROP TABLE IF EXISTS customer");

$cus = "CREATE TABLE customer (
    customer_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address VARCHAR(255)
)";

$mea = "CREATE TABLE measurement (
    measurement_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED,
    date_taken DATE NOT NULL,
    chest DECIMAL(5,2),
    waist DECIMAL(5,2),
    hip DECIMAL(5,2),
    shoulder DECIMAL(5,2),
    sleeve_length DECIMAL(5,2),
    pants_length DECIMAL(5,2),
    remarks VARCHAR(255),
    FOREIGN KEY (customer_id) REFERENCES customer(customer_id) ON DELETE SET NULL
)";

$job = "CREATE TABLE jobs (
  job_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id INT UNSIGNED NOT NULL,
  measurement_id INT UNSIGNED NULL DEFAULT NULL,
  job_code VARCHAR(50) UNIQUE,
  title VARCHAR(200),
  description TEXT,
  status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
  received_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  due_date DATE,
  completed_image VARCHAR(255) NULL DEFAULT NULL,
  FOREIGN KEY (customer_id) REFERENCES customer(customer_id) ON DELETE CASCADE,
  FOREIGN KEY (measurement_id) REFERENCES measurement(measurement_id) ON DELETE SET NULL 
)";

$pay = "CREATE TABLE payments (
  payment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  job_id INT UNSIGNED NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  method ENUM('cash','bank_transfer','credit_card','mobile_banking','other') DEFAULT 'cash',
  receipt_no VARCHAR(100),
  notes TEXT,
  payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE
)";

$p_ima = "CREATE TABLE `payment_images` (
  `image_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_id` INT UNSIGNED NOT NULL,
  `image_filename` VARCHAR(255) NOT NULL,
  `uploaded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`image_id`),
  FOREIGN KEY (`payment_id`) REFERENCES `payments`(`payment_id`) ON DELETE CASCADE
)";

if ($conn->query($cus) === TRUE) { echo "Table 'customer' created successfully.<br>"; } else { echo "Error creating customer table: " . $conn->error . "<br>"; }
if ($conn->query($mea) === TRUE) { echo "Table 'measurement' created successfully.<br>"; } else { echo "Error creating measurement table: " . $conn->error . "<br>"; }
if ($conn->query($job) === TRUE) { echo "Table 'jobs' created successfully.<br>"; } else { echo "Error creating jobs table: " . $conn->error . "<br>"; }
if ($conn->query($pay) === TRUE) { echo "Table 'payments' created successfully.<br>"; } else { echo "Error creating payments table: " . $conn->error . "<br>"; }
if ($conn->query($p_ima) === TRUE) { echo "Table 'payments_images' created successfully.<br>"; } else { echo "Error creating payments_images table: " . $conn->error . "<br>"; }

$conn->close();
?>
<p>
Go to fill the main form <a href="<?php echo BASE_URL; ?>main.php">here</a>
</p>