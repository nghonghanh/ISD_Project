<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "096900";
$database = "studentmanagedatabase";

// Tạo kết nối mới
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Kiểm tra xem có StudentID được gửi từ client không
if (isset($_GET['id'])) {
    // Lấy thông tin StudentID từ client
    $studentId = $_GET['id'];

    // Chuẩn bị câu truy vấn để lấy thông tin sinh viên và thông tin phụ huynh
    $sql = "SELECT s.StudentName, s.BirthDate, s.PhoneNumber, s.StudentEmail, p.ParentEmail, p.ParentPhoneNumber
            FROM student s
            LEFT JOIN parent p ON s.StudentID = p.StudentID
            WHERE s.StudentID = '$studentId'";

    // Thực thi câu truy vấn
    $result = $conn->query($sql);

    // Kiểm tra và trả về dữ liệu dưới dạng JSON
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Đặt dữ liệu vào một mảng
        $studentInfo = array(
            'StudentName' => $row['StudentName'],
            'BirthDate' => $row['BirthDate'],
            'PhoneNumber' => $row['PhoneNumber'],
            'StudentEmail' => $row['StudentEmail'],
            'ParentEmail' => $row['ParentEmail'],
            'ParentPhoneNumber' => $row['ParentPhoneNumber']
        );

        // Thiết lập header cho phản hồi là JSON
        header('Content-Type: application/json');

        // Trả về dữ liệu dưới dạng JSON
        echo json_encode($studentInfo);
    } else {
        echo json_encode(array("error" => "No student found with the provided ID"));
    }
} else {
    echo json_encode(array("error" => "StudentID not provided"));
}

// Đóng kết nối
$conn->close();
?>
