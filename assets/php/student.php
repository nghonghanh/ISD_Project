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

// Chuẩn bị câu truy vấn mặc định (lấy toàn bộ danh sách sinh viên)
$sql = "SELECT student.StudentID, student.StudentName, student.PhoneNumber, student.StudentEmail, student_class.ClassID, student_class.Status 
        FROM student 
        LEFT JOIN student_class ON student.StudentID = student_class.StudentID";

// Kiểm tra nếu có dữ liệu tìm kiếm được gửi từ client
if (isset($_GET['search'])) {
    // Lấy thông tin tìm kiếm từ client
    $search = $_GET['search'];

    // Nếu thông tin tìm kiếm không rỗng
    if (!empty($search)) {
        // Tạo điều kiện tìm kiếm dựa trên tên sinh viên
        $sql .= " WHERE LOWER(student.StudentID) LIKE '%$search%' OR LOWER(student.StudentName) LIKE '%$search%' OR LOWER(student.StudentEmail) LIKE '%$search%'";
    }
}

// Thực hiện truy vấn
$result = $conn->query($sql);
 
// Tạo mảng chứa dữ liệu
$students = array();

// Kiểm tra và lấy dữ liệu từ kết quả truy vấn
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $students[$row['StudentID']][] = $row; // Tạo mảng lồng để nhóm các bản ghi theo StudentID
    }
}

// Chuẩn bị mảng chứa dữ liệu đã được xử lý
$processed_students = array();

// Duyệt qua từng sinh viên trong mảng đã nhóm
foreach ($students as $student_id => $student_data) {
    // Lặp qua từng bản ghi của sinh viên đó
    foreach ($student_data as $index => $data) {
        // Nếu là ClassID đầu tiên của sinh viên đó
        if ($index === 0) {
            // Thêm toàn bộ thông tin vào mảng processed_students
            $processed_students[] = $data;
        } else {
            // Nếu không phải ClassID đầu tiên, chỉ lấy ClassID và Status, các cột khác để trống
            $processed_students[] = array(
                'StudentID' => '',
                'StudentName' => '',
                'PhoneNumber' => $data['PhoneNumber'],
                'StudentEmail' => $data['StudentEmail'],
                'ClassID' => $data['ClassID'],
                'Status' => $data['Status'],
                'Function' => '',
            );
        }
    }
}

// Trả về dữ liệu dưới dạng JSON
echo json_encode($processed_students);

// Đóng kết nối
$conn->close();
?>
