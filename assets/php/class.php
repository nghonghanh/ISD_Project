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

// Chuẩn bị câu truy vấn mặc định
$sql = "SELECT DISTINCT class.ClassID, class.Level, class.Teacher, class.OpeningDay, class.EndingDay
        FROM class
        INNER JOIN student_class ON class.ClassID = student_class.ClassID";

// Kiểm tra nếu có dữ liệu trạng thái được gửi từ client
if (isset($_GET['status'])) {
    // Lấy thông tin trạng thái từ client
    $status = $_GET['status'];

    // Nếu thông tin trạng thái không rỗng và hợp lệ
    if (!empty($status) && ($status == 'Completed' || $status == 'Studying')) {
        // Sử dụng trạng thái từ client để tạo điều kiện truy vấn
        $sql .= " WHERE student_class.Status = '$status'";
        
        // Kiểm tra nếu có dữ liệu tìm kiếm được gửi từ client
        if (isset($_GET['search'])) {
            // Lấy thông tin tìm kiếm từ client
            $search = $_GET['search'];

            // Nếu thông tin tìm kiếm không rỗng
            if (!empty($search)) {
                // Thêm điều kiện tìm kiếm dựa trên ClassID hoặc Teacher
                $sql .= " AND (LOWER(class.ClassID) LIKE '%$search%' OR LOWER(class.Teacher) LIKE '%$search%')";
            }
        }
    }
}

// Thực hiện truy vấn
$result = $conn->query($sql);

// Tạo mảng chứa dữ liệu
$classData = array();

// Kiểm tra và lấy dữ liệu từ kết quả truy vấn
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Thêm dữ liệu vào mảng theo cấu trúc mong muốn
        $classData[] = array(
            'ClassID' => $row['ClassID'],
            'Level' => $row['Level'],
            'Teacher' => $row['Teacher'],
            'OpeningDay' => $row['OpeningDay'],
            'EndingDay' => $row['EndingDay']
        );
    }
}

// Trả về dữ liệu dưới dạng JSON
echo json_encode($classData);

// Đóng kết nối
$conn->close();
?>
