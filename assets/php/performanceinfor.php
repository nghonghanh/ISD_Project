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
$studentId = $_GET['studentId'];
$classId = $_GET['classId'];

    $sql_performance = "SELECT Test1, Test2, FinalTest, AttendancePercent, HomeworkPercent FROM student_class WHERE StudentID = '$studentId' AND ClassID = '$classId'";
    $result_performance = $conn->query($sql_performance);

    if ($result_performance->num_rows > 0) {
        $row_performance = $result_performance->fetch_assoc();
        $performanceInfo = array(
            "Test1" => $row_performance['Test1'],
            "Test2" => $row_performance['Test2'],
            "FinalTest" => $row_performance['FinalTest'],
            "AttendancePercent" => $row_performance['AttendancePercent'],
            "HomeworkPercent" => $row_performance['HomeworkPercent'],
          );
    } else {
        echo json_encode(["error" => "No performance data found for this student with the specified class and status"]);
        exit;
    }

echo json_encode($performanceInfo);

// Close connection
$conn->close();
?>
