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
  $test1 = $_POST['test1Input'];
  $test2 = $_POST['test2Input'];
  $final = $_POST['finalInput'];
  $attendance = $_POST['attendanceInput'];
  $homework = $_POST['homeworkInput'];
  $studentId = $_POST['studentId'];
  $classId = $_POST['classId'];

  // Kiểm tra
  $errors = array();
  if (!empty($test1) && !is_numeric($test1)) {
        $errors['test1Input'] = "Please enter the number";
  }
  if (!empty($test1) && is_numeric($test1) && ($test1 < 0 || $test1 > 10)) {
        $errors['test1Input'] = "The value is only in the range 0 -> 10";
  }
  if (!empty($test2) && !is_numeric($test2)) {
        $errors['test2Input'] = "Please enter the number";
  }
  if (!empty($test2) && is_numeric($test2) && ($test2 < 0 || $test2 > 10)) {
        $errors['test2Input'] = "The value is only in the range 0 -> 10";
  }
  if (!empty($final) && !is_numeric($final)) {
        $errors['finalInput'] = "Please enter the number";
  }
  if (!empty($final) && is_numeric($final) && ($final < 0 || $final > 10)) {
        $errors['finalInput'] = "The value is only in the range 0 -> 10";
  }
  if (!empty($attendance) && !is_numeric($attendance)) {
        $errors['attendanceInput'] = "Please enter the number";
  }
  if (!empty($attendance) && is_numeric($attendance) && ($attendance < 0 || $attendance > 30)) {
        $errors['attendanceInput'] = "The value is only in the range 0 -> 30";
  }
  if (!empty($homework) && !is_numeric($homework)) {
        $errors['homeworkInput'] = "Please enter the number";
  }
  if (!empty($homework) && is_numeric($homework) && ($homework < 0 || $homework > 30)) {
        $errors['homeworkInput'] = "The value is only in the range 0 -> 30";
  }

  // Nếu có lỗi, trả về các thông báo lỗi
  if (!empty($errors)) {
      echo json_encode(array("success" => false, "error" => $errors));
      exit;
  }

  // Tính tỉ lệ phần trăm và lưu vào cơ sở dữ liệu
  $attendancePercentage = ($attendance / 30) * 100;
  $homeworkPercentage = ($homework / 30) * 100;

  // Update performance information in the database
  $sql = "UPDATE student_class 
          SET Test1 = '$test1', Test2 = '$test2', FinalTest = '$final', AttendancePercent = '$attendancePercentage', HomeworkPercent = '$homeworkPercentage' 
          WHERE StudentID = '$studentId' AND ClassID = '$classId'";

  if ($conn->query($sql) === TRUE) {
    echo json_encode(array("success" => true, "message" => "Performance information updated successfully"));
  } else {
    echo json_encode(array("success" => false, "error" => "Error updating performance information: " . $conn->error));
  }
}
// Close connection
$conn->close();
?>
