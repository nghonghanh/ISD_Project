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

// Check if data is sent from the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Extract data from the form
$test1 = $_POST['test1'];
$test2 = $_POST['test2'];
$final = $_POST['final'];
$attendance = $_POST['attendance'];
$homework = $_POST['homework'];

// Update performance information in the database
$sql = "UPDATE student_class 
        SET Test1 = '$test1', Test2 = '$test2', FinalTest = '$final', AttendancePercent = '$attendance', HomeworkPercent = '$homework' 
        WHERE StudentID = '$studentId'";

if ($conn->query($sql) === TRUE) {
  echo json_encode(array("success" => "Performance information updated successfully"));
} else {
  echo json_encode(array("error" => "Error updating performance information: " . $conn->error));
}
}
// Close connection
$conn->close();
?>
