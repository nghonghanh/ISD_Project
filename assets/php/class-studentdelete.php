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

// Kiểm tra nếu có StudentID được gửi từ client
if (isset($_GET['studentid'])) {
    // Lấy thông tin StudentID từ client và làm sạch dữ liệu
    $studentid = mysqli_real_escape_string($conn, $_GET['studentid']);

    // Chuẩn bị câu truy vấn xóa
    $sql = "DELETE FROM student WHERE StudentID = '$studentid'";

    // Thực hiện truy vấn
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
}

// Đóng kết nối
$conn->close();
?>
