<?php
// Kết nối đến cơ sở dữ liệu
$servername = "127.0.0.1";
$username = "admin";
$password = "admin123";
$dbname = "studentmanagedatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Chuẩn bị câu truy vấn mặc định (lấy toàn bộ danh sách sinh viên)
$sql = "SELECT student.StudentID, student.StudentName, student.PhoneNumber, student.StudentEmail, student_class.ClassID, student_class.Status 
        FROM student 
        LEFT JOIN student_class ON student.StudentID = student_class.StudentID";

// Kiểm tra nếu có dữ liệu tìm kiếm được gửi từ client
if (isset($_GET['search'])) {
    // Lấy thông tin tìm kiếm từ client và làm sạch dữ liệu
    $search = mysqli_real_escape_string($conn, $_GET['search']);

    // Nếu thông tin tìm kiếm không rỗng
    if (!empty($search)) {
        // Tạo điều kiện tìm kiếm dựa trên tên sinh viên
        $sql .= " WHERE LOWER(student.StudentID) LIKE '%$search%' OR LOWER(student.StudentName) LIKE '%$search%' OR LOWER(student.StudentEmail) LIKE '%$search%'";
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
