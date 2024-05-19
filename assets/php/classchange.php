<?php
$servername = "127.0.0.1";
$username = "admin";
$password = "admin123";
$dbname = "studentmanagedatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $classId = $_POST['classId'];
  $level = $_POST['editLevel'];
  $teacher = $_POST['editTeacher'];
  $openingDay = $_POST['editOpeningDay'];
  $endingDay = $_POST['editEndingDay'];
  $status = $_POST['editStatus'];

  $errors = array();

  if (empty($level)) {
    $errors['editLevel'] = "Please enter the information";
  } elseif (!in_array($level, ["3.0-4.0", "4.0-5.0", "5.0-6.0", "6.0-7.0"])) {
    $errors['editLevel'] = "Please enter the correct level";
  }

  if (empty($teacher)) {
    $errors['addTeacher'] = "Please enter teacher name";
} elseif (!preg_match("/^[^\d\W_]+\s*[^\d\W_]*$/u", $teacher)) {
    $errors['addTeacher'] = "Please enter text only";
}

  if (empty($status)) {
    $errors['editStatus'] = "Please enter the information";
  } elseif (!in_array($status, ["Completed", "Studying"])) {
    $errors['editStatus'] = "Please enter the correct class status";
  }

  if (!empty($errors)) {
    echo json_encode(array("success" => false, "error" => $errors));
    exit;
  }

  $sql = "UPDATE class 
          SET Level = ?, Teacher = ?, OpeningDay = ?, EndingDay = ?
          WHERE ClassID = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssss", $level, $teacher, $openingDay, $endingDay, $classId);

  if ($stmt->execute() === TRUE) {
    $sql_status = "UPDATE student_class 
                   SET Status = ?
                   WHERE ClassID = ?";
    $stmt_status = $conn->prepare($sql_status);
    $stmt_status->bind_param("ss", $status, $classId);
    if ($stmt_status->execute() === TRUE) {
      echo json_encode(array("success" => true, "message" => "Class information updated successfully"));
    } else {
      echo json_encode(array("success" => false, "error" => "Error updating class status: " . $conn->error));
    }
    $stmt_status->close();
  } else {
    echo json_encode(array("success" => false, "error" => "Error updating class information: " . $conn->error));
  }
  $stmt->close();
}
$conn->close();
?>
