# ✂️ Tailoring Shop Management System (ระบบบริหารจัดการร้านเย็บผ้า)

A comprehensive Web Application designed to digitalize and streamline the workflow of a tailoring business. This system replaces traditional manual record-keeping with an efficient relational database, enabling easy tracking of customers, measurements, job statuses, and payments.

## 🚀 Features (คุณสมบัติหลัก)
* **Customer & Measurement Management:** จัดเก็บข้อมูลลูกค้าและประวัติการวัดตัว (รอบอก, เอว, สะโพก ฯลฯ) โดยสามารถอ้างอิงข้อมูลวัดตัวแต่ละครั้งกับงานสั่งตัดได้อย่างแม่นยำ
* **Smart Job Tracking:** ระบบติดตามสถานะงาน (Jobs) ตั้งแต่เริ่มสั่งตัดจนถึงส่งมอบ พร้อมฟังก์ชันเปลี่ยนสถานะด่วน (Quick Actions)
* **Dynamic Data Fetching:** ใช้ AJAX ในการดึงข้อมูลประวัติการวัดตัวของลูกค้ามาแสดงผลแบบ Real-time บนฟอร์มรับงานใหม่ เพื่อความรวดเร็วและลดข้อผิดพลาด
* **Payment & File Management:** ระบบบันทึกการชำระเงินที่สามารถแยกอัปโหลดและจัดเก็บไฟล์ "รูปภาพผลงาน" และ "สลิปโอนเงิน" ได้อย่างเป็นระบบ
* **Interactive Dashboard:** สรุปสถิติภาพรวมของร้านค้า เช่น จำนวนงานทั้งหมด, งานที่ยังไม่เสร็จ และรายรับรวม เพื่อช่วยในการบริหารจัดการ

## 🛠️ Tech Stack (เทคโนโลยีที่ใช้)
* **Backend:** PHP (Procedural & Prepared Statements for Security)
* **Database:** MySQL (Relational Database with ER-Diagram Design)
* **Frontend:** HTML5, Bootstrap 5, CSS
* **Scripting:** JavaScript (AJAX)

## 🗄️ Database Design (โครงสร้างฐานข้อมูล)
The system is built on a normalized relational database design, focusing on data integrity across 5 main entities:
* `customer` (ข้อมูลลูกค้า)
* `measurement` (ข้อมูลการวัดตัว)
* `jobs` (รายการสั่งตัด และสถานะงาน)
* `payments` (ข้อมูลการชำระเงิน)
* `payment_images` (คลังรูปภาพหลักฐานและผลงาน)

## 💻 How to Run (วิธีติดตั้งและใช้งาน)
1. Install [XAMPP](https://www.apachefriends.org/) or any local server environment.
2. Clone this repository into the `htdocs` folder:
   `git clone https://github.com/yourusername/Tailor-Management-System.git`
3. Start Apache and MySQL modules in XAMPP.
4. Open phpMyAdmin and create a new database named `sewing`.
5. Import the `database.sql` file (if provided) into the `sewing` database.
6. Open your browser and navigate to `http://localhost/Tailor-Management-System/main.php`
