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

// Check if data is sent from the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Extract data from the form
  $studentName = $_POST['addStudentName'];
  $birthDate = $_POST['addDOB'];
  $phoneNumber = $_POST['addPhone'];
  $studentEmail = $_POST['addEmail'];
  $parentPhone = $_POST['addParentPhone'];
  $parentEmail = $_POST['addParentEmail'];

  // Find the minimum unused StudentID
  $sql_student_id = "SELECT MIN(StudentID) AS MinID FROM students WHERE StudentID NOT IN (SELECT StudentID FROM students)";
  $result = $conn->query($sql_student_id);
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $studentId = $row["MinID"];
  } else {
    // If no unused StudentID exists, get the maximum StudentID and increment by 1
    $sql_max_student_id = "SELECT MAX(StudentID) AS MaxID FROM students";
    $result = $conn->query($sql_max_student_id);
    $row = $result->fetch_assoc();
    $studentId = $row["MaxID"] + 1;
  }

  // Insert student information into the database
  $sql_insert_student = "INSERT INTO students (StudentID, StudentName, BirthDate, PhoneNumber, StudentEmail) 
                         VALUES ('$studentId', '$studentName', '$birthDate', '$phoneNumber', '$studentEmail')";

  if ($conn->query($sql_insert_student) === TRUE) {
    // Insert parent information into the database
    $sql_insert_parent = "INSERT INTO parents (StudentID, ParentPhoneNumber, ParentEmail) 
                          VALUES ('$studentId', '$parentPhone', '$parentEmail')";

    if ($conn->query($sql_insert_parent) === TRUE) {
      // Loop through each class input field and insert class information
      for ($i = 1; $i <= 4; $i++) {
        $classIdField = "addClassID$i";
        $statusField = "addStatus$i";
        $classId = $_POST[$classIdField];
        $status = $_POST[$statusField];

        // Check if class ID is empty
        if (!empty($classId)) {
          // Insert class information into the database
          $sql_insert_class = "INSERT INTO student_class (StudentID, ClassID, Status) VALUES ('$studentId', '$classId', '$status')";
          $conn->query($sql_insert_class);
        }
      }

      // Return success message after inserting all information
      echo json_encode(array("success" => true, "message" => "Student information added successfully"));
    } else {
      // Return error message if there is an error inserting parent information
      echo json_encode(array("success" => false, "error" => "Error adding parent information: " . $conn->error));
    }
  } else {
    // Return error message if there is an error inserting student information
    echo json_encode(array("success" => false, "error" => "Error adding student information: " . $conn->error));
  }
}

// Close the connection
$conn->close();
?>
