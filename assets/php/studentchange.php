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
    $studentName = $_POST['editStudentName'];
    $birthDate = $_POST['editDOB'];
    $phoneNumber = $_POST['editPhone'];
    $studentEmail = $_POST['editEmail'];
    $parentPhone = $_POST['editParentPhone'];
    $parentEmail = $_POST['editParentEmail'];
    $studentId = $_POST['editStudentID']; // Lấy StudentID từ form

    // Lưu thông tin sinh viên vào cơ sở dữ liệu
    $sql = "UPDATE student SET 
                StudentName='$studentName', 
                BirthDate='$birthDate', 
                PhoneNumber='$phoneNumber', 
                StudentEmail='$studentEmail'
                WHERE StudentID='$studentId'";
    
    // Thực thi câu lệnh SQL và kiểm tra kết quả
    if ($conn->query($sql) === TRUE) {
        // Tiếp tục cập nhật thông tin của phụ huynh
        $sql_parent = "UPDATE parent SET 
                        ParentPhoneNumber='$parentPhone', 
                        ParentEmail='$parentEmail'
                        WHERE StudentID='$studentId'";

        if ($conn->query($sql_parent) === TRUE) {
            // Cập nhật thông tin thành công cho cả sinh viên và phụ huynh
            echo json_encode(array("success" => true, "message" => "Student and parent information updated successfully"));
        } else {
            // Trả về thông báo lỗi nếu có lỗi xảy ra khi cập nhật thông tin phụ huynh
            echo json_encode(array("success" => false, "error" => "Error updating parent information: " . $conn->error));
        }
    } else {
        // Trả về thông báo lỗi nếu có lỗi xảy ra khi cập nhật thông tin sinh viên
        echo json_encode(array("success" => false, "error" => "Error updating student information: " . $conn->error));
    }
}

// Đóng kết nối
$conn->close();
?>
