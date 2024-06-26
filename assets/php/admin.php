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

// Truy vấn dữ liệu từ bảng Admin
$sql = "SELECT * FROM Admin";
$result = $conn->query($sql);

// Nếu có dữ liệu, trả về dưới dạng JSON
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $data = array(
        'name' => $row['Name'],
        'username' => $row['Username'],
        'password' => $row['Password']
    );
    echo json_encode($data);
} else {
    echo "0 results";
}
$conn->close();
?>
