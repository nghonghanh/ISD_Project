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

// Kiểm tra nếu có ClassID được gửi từ client
if (isset($_GET['classid'])) {
    // Lấy thông tin ClassID từ client và làm sạch dữ liệu
    $classid = mysqli_real_escape_string($conn, $_GET['classid']);

    // Chuẩn bị câu truy vấn lấy thông tin học sinh
    $sql = "SELECT StudentID, StudentName, PhoneNumber, StudentEmail FROM student WHERE StudentID IN (SELECT StudentID FROM student_class WHERE ClassID = '$classid')";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $students = array();
        while($row_student = $result->fetch_assoc()) {
            $students[] = array(
                "StudentID" => $row_student['StudentID'],
                "StudentName" => $row_student['StudentName'],
                "PhoneNumber" => $row_student['PhoneNumber'],
                "StudentEmail" => $row_student['StudentEmail'],
            );
        }
    } else {
        echo "No students found for this class";
        exit;
    }
    echo json_encode($students);
} else {
    echo "No ClassID provided";
}

// Đóng kết nối
$conn->close();
?>
