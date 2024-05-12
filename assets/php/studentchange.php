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
  $studentName = $_POST['editStudentName'];
  $birthDate = $_POST['editDOB'];
  $phoneNumber = $_POST['editPhone'];
  $studentEmail = $_POST['editEmail'];
  $parentPhone = $_POST['editParentPhone'];
  $parentEmail = $_POST['editParentEmail'];
  $studentId = $_POST['editStudentID'];

  // Update student information in the database
  $sql_student = "UPDATE student SET 
                  StudentName='$studentName', 
                  BirthDate='$birthDate', 
                  PhoneNumber='$phoneNumber', 
                  StudentEmail='$studentEmail'
                  WHERE StudentID='$studentId'";
  
  // Execute the SQL statement for updating student information
  if ($conn->query($sql_student) === TRUE) {
    // Update parent information in the database
    $sql_parent = "UPDATE parent SET 
                    ParentPhoneNumber='$parentPhone', 
                    ParentEmail='$parentEmail'
                    WHERE StudentID='$studentId'";
    
    // Execute the SQL statement for updating parent information
    if ($conn->query($sql_parent) === TRUE) {
      // Update class information in the database
      // Loop through each class input field and update class information
      for ($i = 1; $i <= 4; $i++) {
        $classIdField = "editClassID$i";
        $statusField = "editStatus$i";
        $classId = $_POST[$classIdField];
        $status = $_POST[$statusField];

        // Check if class ID is empty
        if (!empty($classId)) {
          // Check if the class ID exists for the student in the database
          $sql_check_class = "SELECT * FROM student_class WHERE StudentID='$studentId' AND ClassID='$classId'";
          $result = $conn->query($sql_check_class);

          if ($result->num_rows > 0) {
            // Update status if the class ID exists
            $sql_update_status = "UPDATE student_class SET Status='$status' WHERE StudentID='$studentId' AND ClassID='$classId'";
            $conn->query($sql_update_status);
          } else {
            // Insert new record if the class ID does not exist
            $sql_insert_class = "INSERT INTO student_class (StudentID, ClassID, Status) VALUES ('$studentId', '$classId', '$status')";
            $conn->query($sql_insert_class);
          }
        }
      }

      // Return success message after updating all information
      echo json_encode(array("success" => true, "message" => "Student, parent, and class information updated successfully"));
    } else {
      // Return error message if there is an error updating parent information
      echo json_encode(array("success" => false, "error" => "Error updating parent information: " . $conn->error));
    }
  } else {
    // Return error message if there is an error updating student information
    echo json_encode(array("success" => false, "error" => "Error updating student information: " . $conn->error));
  }
}

// Close the connection
$conn->close();
?>
