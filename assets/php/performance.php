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
$sql = "SELECT student.StudentID, student.StudentName, student_class.ClassID, student_class.Test1, student_class.Test2, student_class.FinalTest, student_class.AttendancePercent, student_class.HomeworkPercent
        FROM student
        INNER JOIN student_class ON student.StudentID = student_class.StudentID";

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
                // Thêm điều kiện tìm kiếm dựa trên StudentID hoặc StudentName
                $sql .= " AND (LOWER(student.StudentID) LIKE '%$search%' OR LOWER(student.StudentName) LIKE '%$search%')";
            }
        }
    }
}

// Thực hiện truy vấn
$result = $conn->query($sql);

// Tạo mảng chứa dữ liệu
$performanceData = array();

// Kiểm tra và lấy dữ liệu từ kết quả truy vấn
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Thêm dữ liệu vào mảng theo cấu trúc mong muốn
        $performanceData[] = array(
            'StudentID' => $row['StudentID'],
            'StudentName' => $row['StudentName'],
            'ClassID' => $row['ClassID'],
            'Test1' => $row['Test1'],
            'Test2' => $row['Test2'],
            'FinalTest' => $row['FinalTest'],
            'AttendancePercent' => $row['AttendancePercent'],
            'HomeworkPercent' => $row['HomeworkPercent']
        );
    }
}

// Trả về dữ liệu dưới dạng JSON
echo json_encode($performanceData);

// Đóng kết nối
$conn->close();
?>
