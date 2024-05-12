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

    //Kiểm tra mật khẩu cũ và mật khẩu mới 
    if ($oldPassword == $newPassword) {
        echo json_encode(array("success" => false, "error" => "Old password and New password is the same"));
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
