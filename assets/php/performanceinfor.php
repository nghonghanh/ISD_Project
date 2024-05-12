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

// Retrieve parameters securely
$studentId = isset($_GET['studentId']) ? intval($_GET['studentId']) : 0;
$classId = isset($_GET['classId']) ? intval($_GET['classId']) : 0;
$status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

if ($studentId > 0 && $classId > 0 && !empty($status)) {
    $stmt = $conn->prepare("SELECT ClassID, Test1, Test2, FinalTest, AttendancePercent, HomeworkPercent FROM student_class WHERE StudentID = ? AND ClassID = ? AND Status = ?");
    $stmt->bind_param("iis", $studentId, $classId, $status);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row_performance = $result->fetch_assoc();
        echo json_encode($row_performance);
    } else {
        echo json_encode(["error" => "No performance data found for this student with the specified class and status"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid parameters"]);
}

// Close connection
$conn->close();
?>
