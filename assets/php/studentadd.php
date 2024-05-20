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

// Kiểm tra nếu có dữ liệu được gửi từ form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentName = $conn->real_escape_string($_POST['addStudentName']);
    $birthDate = $conn->real_escape_string($_POST['addDOB']);
    $phoneNumber = $conn->real_escape_string($_POST['addPhone']);
    $studentEmail = $conn->real_escape_string($_POST['addEmail']);
    $parentPhone = $conn->real_escape_string($_POST['addParentPhone']);
    $parentEmail = $conn->real_escape_string($_POST['addParentEmail']);

    // Kiểm tra thông tin trống
    $errors = array();
    if (empty($studentName)) {
        $errors['addStudentName'] = "Please enter Student Name";
    }
    if (empty($birthDate)) {
        $errors['addDOB'] = "Please enter Date Of Birth";
    }
    if (empty($phoneNumber)) {
        $errors['addPhone'] = "Please enter Phone";
    }
    if (empty($studentEmail)) {
        $errors['addEmail'] = "Please enter Email";
    }
    if (empty($parentPhone)) {
        $errors['addParentPhone'] = "Please enter Parent Phone";
    }
    if (empty($parentEmail)) {
        $errors['addParentEmail'] = "Please enter Parent Email";
    }

    // Kiểm tra số điện thoại có phải là số không
    if (!empty($phoneNumber) && !is_numeric($phoneNumber)) {
        $errors['addPhone'] = "Please enter a valid phone number";
    }

    // Kiểm tra số điện thoại có nhiều hơn 10 số không
    if (!empty($phoneNumber) && is_numeric($phoneNumber) && strlen($phoneNumber) > 10) {
        $errors['addPhone'] = "Enter up to 10 numbers for phone";
    }

    // Kiểm tra email có nhiều hơn 30 kí tự không 
    if (!empty($studentEmail) && strlen($studentEmail) > 30) {
        $errors['addEmail'] = "Please enter no more than 30 characters";
    }

    if (!empty($parentEmail) && strlen($parentEmail) > 30) {
        $errors['addParentEmail'] = "Please enter no more than 30 characters";
    }

    // Kiểm tra tên sinh viên chỉ chứa ký tự chữ và không vượt quá 50 ký tự
    if (!empty($studentName) && is_numeric($studentName)) {
        $errors['addStudentName'] = "Student name only accept text";
    }
    if (!empty($studentName) && strlen($studentName) > 50) {
        $errors['addStudentName'] = "Enter up to 50 characters";
    }

    // Validate class IDs and statuses
    for ($i = 1; $i <= 4; $i++) {
        $classIdField = "addClassID$i";
        $statusField = "addStatus$i";
        $classId = $_POST[$classIdField];

        if (!empty($classId) && !preg_match("/^IZ\d{4}$/", $classId)) {
            $errors[$classIdField] = "Please enter correct characters for Class ID";
        }

        if (!empty($classId)) {
            $sql_check_class = "SELECT * FROM class WHERE ClassID = '$classId'";
            $result_check_class = $conn->query($sql_check_class);
            if ($result_check_class->num_rows == 0) {
                $errors[$classIdField] = "Class ID does not exist";
            }
        }
    }

    // Return errors if validation fails
    if (!empty($errors)) {
        echo json_encode(array("success" => false, "error" => $errors));
        exit;
    }

    // Tìm ID nhỏ nhất không tồn tại trong cơ sở dữ liệu
    $sql_find_unused_id = "SELECT MIN(t1.StudentID + 1) AS nextID
                           FROM student t1
                           LEFT JOIN student t2 ON t1.StudentID + 1 = t2.StudentID
                           WHERE t2.StudentID IS NULL";
    $result_find_unused_id = $conn->query($sql_find_unused_id);
    $row = $result_find_unused_id->fetch_assoc();
    $studentId = $row['nextID'];

    // Nếu không tìm thấy ID nào chưa được sử dụng, chọn ID tiếp theo sau ID lớn nhất
    if ($studentId === NULL) {
        $sql_max_id = "SELECT MAX(StudentID) AS MaxID FROM student";
        $result_max_id = $conn->query($sql_max_id);
        $row_max_id = $result_max_id->fetch_assoc();
        $studentId = $row_max_id["MaxID"] + 1;
    }

    // Chèn thông tin sinh viên vào cơ sở dữ liệu
    $sql_insert_student = "INSERT INTO student (StudentID, StudentName, BirthDate, PhoneNumber, StudentEmail) 
                           VALUES ('$studentId', '$studentName', '$birthDate', '$phoneNumber', '$studentEmail')";
    if ($conn->query($sql_insert_student) === TRUE) {
        // Chèn thông tin phụ huynh vào cơ sở dữ liệu
        $sql_insert_parent = "INSERT INTO parent (ParentID, StudentID, ParentPhoneNumber, ParentEmail) 
                              VALUES ('$studentId', '$studentId', '$parentPhone', '$parentEmail')";
        if ($conn->query($sql_insert_parent) === TRUE) {
            // Loop through each class field and insert
            for ($i = 1; $i <= 4; $i++) {
                $classIdField = "addClassID$i";
                $statusField = "addStatus$i";
                $classId = $_POST[$classIdField];
                $status = $_POST[$statusField];

                if (!empty($classId)) {
                    // Chèn thông tin lớp học vào cơ sở dữ liệu
                    $sql_insert_class = "INSERT INTO student_class (StudentID, ClassID, Status) 
                                         VALUES ('$studentId', '$classId', '$status')";
                    $conn->query($sql_insert_class);
                }
            }

            echo json_encode(array("success" => true, "message" => "Student, parent, and class information added successfully"));
        } else {
            echo json_encode(array("success" => false, "error" => "Error adding parent information: " . $conn->error));
        }
    } else {
        echo json_encode(array("success" => false, "error" => "Error adding student information: " . $conn->error));
    }
} else {
    echo json_encode(array("success" => false, "error" => "No data received"));
}

$conn->close();
?>

