<?php

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "096900";
$database = "studentmanagedatabase";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Retrieve student ID from the URL parameter
$studentId = $_GET['id']; // Change 'studentId' to 'id'

// Fetch student information from the 'student' table
$sql_student = "SELECT s.StudentName, s.BirthDate, s.PhoneNumber, s.StudentEmail, p.ParentEmail, p.ParentPhoneNumber
                FROM student s
                LEFT JOIN parent p ON s.StudentID = p.StudentID
                WHERE s.StudentID = '$studentId'";
$result_student = $conn->query($sql_student);

if ($result_student->num_rows > 0) {
  $row_student = $result_student->fetch_assoc();

  // Convert date value to JSON-compatible format
  $row_student['BirthDate'] = $row_student['BirthDate'];

  // Prepare student information array
  $studentInfo = array(
    "StudentName" => $row_student['StudentName'],
    "BirthDate" => $row_student['BirthDate'],
    "PhoneNumber" => $row_student['PhoneNumber'],
    "StudentEmail" => $row_student['StudentEmail'],
    "ParentEmail" => $row_student['ParentEmail'],
    "ParentPhoneNumber" => $row_student['ParentPhoneNumber'],
  );
} else {
  echo "Error fetching student data";
  exit;
}

// Fetch class information for each level
$levels = array(3, 5, 7, 9);
$classInfo = array();
foreach ($levels as $level) {
  $sql_class = "SELECT c.ClassID, sc.Status FROM class c
                LEFT JOIN student_class sc ON c.ClassID = sc.ClassID
                WHERE sc.StudentID = '$studentId' AND c.Level = '$level'";
  $result_class = $conn->query($sql_class);

  if ($result_class->num_rows > 0) {
    $row_class = $result_class->fetch_assoc();
    $classInfo["ClassID$level"] = $row_class['ClassID'];
    $classInfo["Status$level"] = $row_class['Status'];
  } else {
    // Handle empty class record for this level, but also check for query errors
    if ($conn->error) {
      echo "Error fetching student class data: " . $conn->error;
      exit;
    } else {
      $classInfo["ClassID$level"] = null;
      $classInfo["Status$level"] = null;
    }
  }
}

// Combine student and class information into a single array
$output = array_merge($studentInfo, $classInfo);

// Convert output array to JSON format and return
echo json_encode($output);

// Close connection
$conn->close();
?>
