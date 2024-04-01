<?php
// Kết nối đến cơ sở dữ liệu
$servername = "127.0.0.1";
$username = "root";
$password = "096900";
$dbname = "studentmanagedatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

// Xử lý dữ liệu gửi từ form login.html
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra thông tin đăng nhập từ cơ sở dữ liệu
    $sql = "SELECT * FROM admin WHERE Username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row["Password"] === $password) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, "error" => "incorrect_password"));
        }
    } else {
        echo json_encode(array("success" => false, "error" => "incorrect_username"));
    }
}

$conn->close();
?>
