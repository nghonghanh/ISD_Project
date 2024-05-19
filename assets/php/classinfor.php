<?php
$servername = "127.0.0.1";
$username = "admin";
$password = "admin123";
$dbname = "studentmanagedatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$classId = $_GET['classId'];

$sql_class = "SELECT c.ClassID, c.Level, c.Teacher, c.OpeningDay, c.EndingDay, sc.Status 
              FROM class c
              LEFT JOIN student_class sc ON c.ClassID = sc.ClassID
              WHERE c.ClassID = ?";

$stmt = $conn->prepare($sql_class);
$stmt->bind_param("s", $classId);
$stmt->execute();
$result_class = $stmt->get_result();

if ($result_class->num_rows > 0) {
    $row_class = $result_class->fetch_assoc();
    $classInfo = array(
        "ClassID" => $row_class["ClassID"],
        "Level" => $row_class["Level"],
        "Teacher" => $row_class["Teacher"],
        "OpeningDay" => $row_class["OpeningDay"],
        "EndingDay" => $row_class["EndingDay"],
        "Status" => $row_class["Status"]
    );
    echo json_encode($classInfo);
} else {
    echo json_encode(["error" => "No class data found with the specified classId"]);
}

$stmt->close();
$conn->close();
?>
