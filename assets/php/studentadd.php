<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "096900";
$database = "studentmanagedatabase";

// Tạo kết nối mới
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Kiểm tra xem có dữ liệu được gửi từ form không
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $studentName = $_POST["editStudentName"];
    $dob = $_POST["editDOB"];
    $phone = $_POST["editPhone"];
    $email = $_POST["editEmail"];
    $parentPhone = $_POST["editParentPhone"];
    $parentEmail = $_POST["editParentEmail"];
    $classID = $_POST["editClassID"];

    // Tìm StudentID nhỏ nhất chưa được sử dụng
    $sql = "SELECT MIN(StudentID) AS MinID FROM students WHERE StudentID NOT IN (SELECT StudentID FROM students)";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $studentID = $row["MinID"];
    } else {
        // Nếu không có StudentID nào chưa được sử dụng, sẽ lấy StudentID lớn nhất hiện có và tăng thêm 1
        $sql = "SELECT MAX(StudentID) AS MaxID FROM students";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $studentID = $row["MaxID"] + 1;
    }

    // Thêm dữ liệu vào bảng students
    $sql = "INSERT INTO students (StudentID, StudentName, BirthDate, PhoneNumber, StudentEmail, ParentPhoneNumber, ParentEmail, ClassID)
    VALUES ('$studentID', '$studentName', '$dob', '$phone', '$email', '$parentPhone', '$parentEmail', '$classID')";

    if ($conn->query($sql) === TRUE) {
        // Trả về thông báo khi thêm dữ liệu thành công
        echo json_encode(array("message" => "New record created successfully"));
    } else {
        // Trả về thông báo lỗi nếu không thể thêm dữ liệu vào bảng
        echo json_encode(array("message" => "Error: " . $sql . "<br>" . $conn->error));
    }
}

// Đóng kết nối
$conn->close();
?>
