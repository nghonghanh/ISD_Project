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

    // Kiểm tra thông tin trống
    $errors = array();
    if (empty($studentName)) {
        $errors['editStudentName'] = "Please enter Student Name";
    }
    if (empty($birthDate)) {
        $errors['editDOB'] = "Please enter Date Of Birth";
    }
    if (empty($phoneNumber)) {
        $errors['editPhone'] = "Please enter Phone";
    }
    if (empty($studentEmail)) {
        $errors['editEmail'] = "Please enter Email";
    }
    if (empty($parentPhone)) {
        $errors['editParentPhone'] = "Please enter Parent Phone";
    }
    if (empty($parentEmail)) {
        $errors['editParentEmail'] = "Please enter Parent Email";
    }

    // Kiểm tra số điện thoại có phải là số không
    if (!empty($phoneNumber) && !is_numeric($phoneNumber)) {
        $errors['editPhone'] = "Please enter a valid phone number";
    }

    // Kiểm tra số điện thoại có nhiều hơn 10 số không
    if (!empty($phoneNumber) && is_numeric($phoneNumber) && strlen($phoneNumber) > 10) {
        $errors['editPhone'] = "Enter up to 10 numbers for phone";
    }

    // Kiểm tra email có nhiều hơn 30 kí tự không
    if (!empty($studentEmail) && strlen($studentEmail) > 30) {
        $errors['editEmail'] = "Please enter no more than 30 characters";
    }

    if (!empty($parentEmail) && strlen($parentEmail) > 30) {
        $errors['editParentEmail'] = "Please enter no more than 30 characters";
    }

    // Kiểm tra tên sinh viên chỉ chứa ký tự chữ và không vượt quá 50 ký tự
    if (!empty($studentName) && is_numeric($studentName)) {
        $errors['addStudentName'] = "Student name only accept text";
    }
    if (!empty($studentName) && strlen($studentName) > 50) {
        $errors['addStudentName'] = "Enter up to 50 characters";
    }

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

                // Kiểm tra định dạng ClassID và Status
                if (!empty($classId) && !preg_match("/^IZ\d{4}$/", $classId)) {
                    $errors[$classIdField] = "Please enter correct characters for Class ID";
                }

                if (!empty($classId) && !isset($errors[$classIdField])) {
                  $sql_check_class = "SELECT * FROM class WHERE ClassID = '$classId'";
                  $result_check_class = $conn->query($sql_check_class);
                  if ($result_check_class->num_rows == 0) {
                      $errors[$classIdField] = "ClassID does not exist";
                  } else {
                      // Kiểm tra sự tồn tại của ClassID trong bảng student_class
                      $sql_check_studentclass = "SELECT * FROM student_class WHERE StudentID='$studentId' AND ClassID='$classId'";
                      $result_check_studentclass = $conn->query($sql_check_studentclass);

                      if ($result_check_studentclass->num_rows > 0 && !empty($status)) {
                          // Update status if the class ID exists
                          $sql_update_status = "UPDATE student_class SET Status='$status' WHERE StudentID='$studentId' AND ClassID='$classId'";
                          $conn->query($sql_update_status);
                      } elseif ($result_check_studentclass->num_rows == 0) {
                          // Insert new record if the class ID does not exist
                          $sql_insert_class = "INSERT INTO student_class (StudentID, ClassID, Status) VALUES ('$studentId', '$classId', '$status')";
                          $conn->query($sql_insert_class);
                      }
                  }
              }
          }

          if (!empty($errors)) {
              echo json_encode(array("success" => false, "error" => $errors));
              exit;
          } else {
              // Return success message after updating all information
              echo json_encode(array("success" => true, "message" => "Student, parent, and class information updated successfully"));
              exit;
          }
      } else {
          // Return error message if there is an error updating parent information
          echo json_encode(array("success" => false, "error" => "Error updating parent information: " . $conn->error));
          exit;
      }
  } else {
      // Return error message if there is an error updating student information
      echo json_encode(array("success" => false, "error" => "Error updating student information: " . $conn->error));
      exit;
  }
}

// Close the connection
$conn->close();
?>
