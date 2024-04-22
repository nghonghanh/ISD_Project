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

// Check if student ID is sent via GET request
if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
  // Extract student ID from GET parameters
  $studentId = $_GET['id'];

  // Delete student from student table
  $sql_delete_student = "DELETE FROM students WHERE StudentID='$studentId'";
  if ($conn->query($sql_delete_student) === TRUE) {
    // Delete student from student_class table
    $sql_delete_student_class = "DELETE FROM student_class WHERE StudentID='$studentId'";
    $conn->query($sql_delete_student_class);

    // Delete student from parent table
    $sql_delete_parent = "DELETE FROM parents WHERE StudentID='$studentId'";
    $conn->query($sql_delete_parent);

    // Delete student from performance table
    $sql_delete_performance = "DELETE FROM performance WHERE StudentID='$studentId'";
    $conn->query($sql_delete_performance);

    // Return success message after deleting student
    echo json_encode(array("success" => true, "message" => "Student deleted successfully"));
  } else {
    // Return error message if there is an error deleting student
    echo json_encode(array("success" => false, "error" => "Error deleting student: " . $conn->error));
  }
}

// Close the connection
$conn->close();
?>
