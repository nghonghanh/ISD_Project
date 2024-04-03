<?php
// Kết nối đến cơ sở dữ liệu
$servername = "127.0.0.1";
$username = "root";
$password = "096900";
$dbname = "studentmanagedatabase";

// Tạo kết nối mới
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Chuẩn bị câu truy vấn mặc định (lấy toàn bộ danh sách sinh viên)
$sql = "SELECT * FROM student";

// Kiểm tra nếu có dữ liệu tìm kiếm được gửi từ client
if (isset($_GET['search'])) {
    // Lấy thông tin tìm kiếm từ client
    $search = $_GET['search'];

    // Nếu thông tin tìm kiếm không rỗng
    if (!empty($search)) {
        // Tạo điều kiện tìm kiếm dựa trên tên sinh viên
        $sql = "SELECT * FROM student WHERE LOWER(StudentName) LIKE '%$search%' OR LOWER(StudentEmail) LIKE '%$search%'"; 
    }
}

// Thực hiện truy vấn
$result = $conn->query($sql);

// Tạo mảng chứa dữ liệu
$students = array();

// Kiểm tra và lấy dữ liệu từ kết quả truy vấn
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Trả về dữ liệu dưới dạng JSON
echo json_encode($students);

// Đóng kết nối
$conn->close();
?>
