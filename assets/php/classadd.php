<?php
$servername = "127.0.0.1";
$username = "admin";
$password = "admin123";
$dbname = "studentmanagedatabase";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $classId = $conn->real_escape_string($_POST['addClassID']);
    $level = $conn->real_escape_string($_POST['addLevel']);
    $teacher = $conn->real_escape_string($_POST['addTeacher']);
    $openingDay = $conn->real_escape_string($_POST['addOpeningDay']);
    $endingDay = $conn->real_escape_string($_POST['addEndingDay']);

    $errors = array();

    // Validate ClassID format
    if (empty($classId)) {
        $errors['addClassID'] = "Please enter the Class ID";
    } elseif (!preg_match("/^IZ\d{4}$/", $classId)) {
        $errors['addClassID'] = "Please enter a valid Class ID (e.g., IZ1234)";
    }

    // Check if ClassID already exists in the database
    $sql_check_classid = "SELECT * FROM class WHERE ClassID = '$classId'";
    $result_check_classid = $conn->query($sql_check_classid);
    if ($result_check_classid->num_rows > 0) {
        $errors['addClassID'] = "Class ID already exists";
    }

    // Validate other input fields
    if (empty($level)) {
        $errors['addLevel'] = "Please enter class level";
    } elseif (!in_array($level, ["3.0-4.0", "4.0-5.0", "5.0-6.0", "6.0-7.0"])) {
        $errors['addLevel'] = "Please enter the correct level";
    }

    if (empty($teacher)) {
        $errors['addTeacher'] = "Please enter teacher name";
    } elseif (!preg_match("/^[^\d\W_]+\s*[^\d\W_]*$/u", $teacher)) {
        $errors['addTeacher'] = "Please enter text only";
    }

    if (empty($openingDay)) {
        $errors['addOpeningDay'] = "Please enter opening day";
    }

    if (empty($endingDay)) {
        $errors['addEndingDay'] = "Please enter ending day";
    }

    if (!empty($errors)) {
        echo json_encode(array("success" => false, "error" => $errors));
        exit;
    }

    $sql = "INSERT INTO class (ClassID, Level, Teacher, OpeningDay, EndingDay) 
            VALUES ('$classId', '$level', '$teacher', '$openingDay', '$endingDay')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("success" => true, "message" => "Class added successfully"));
    } else {
        echo json_encode(array("success" => false, "error" => "Error adding class: " . $conn->error));
    }
} else {
    echo json_encode(array("success" => false, "error" => "No data received"));
}

$conn->close();
?>
