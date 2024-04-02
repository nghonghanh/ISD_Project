<?php
// Kiểm tra kết nối
$conn = new mysqli("localhost", "root", "096900", "studentmanagedatabase");
if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

// Lấy mật khẩu cũ từ cơ sở dữ liệu
$sql = "SELECT Password FROM admin WHERE AdminID = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $oldPasswordFromDB = $row["Password"];
} else {
    echo json_encode(array("success" => false, "error" => "Unable to retrieve old password"));
    $conn->close();
    exit();
}

// Xử lý dữ liệu gửi từ form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Kiểm tra mật khẩu cũ
    if ($oldPassword !== $oldPasswordFromDB) {
        echo json_encode(array("success" => false, "error" => "Old password is incorrect"));
        $conn->close();
        exit();
    }

    // Kiểm tra mật khẩu mới và xác nhận mật khẩu mới
    if ($newPassword !== $confirmPassword) {
        echo json_encode(array("success" => false, "error" => "New password and confirm password do not match"));
        $conn->close();
        exit();
    }

    // Cập nhật mật khẩu mới vào cơ sở dữ liệu
    $sql = "UPDATE admin SET Password='$newPassword' WHERE AdminID=1";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("success" => true, "message" => "Password updated successfully"));
    } else {
        echo json_encode(array("success" => false, "error" => "Error updating password: " . $conn->error));
    }
}

$conn->close();
?>