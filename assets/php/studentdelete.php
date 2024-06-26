<?php
$servername = "127.0.0.1";
$username = "admin";
$password = "admin123";
$dbname = "studentmanagedatabase";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    parse_str(file_get_contents("php://input"), $_DELETE);
    if (!isset($_DELETE['id'])) {
        echo json_encode(["success" => false, "error" => "Student ID is missing"]);
        exit;
    }
    $studentId = $_DELETE['id'];

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("DELETE FROM student_class WHERE StudentID = ?");
        $stmt->bind_param("i", $studentId);
        if (!$stmt->execute()) throw new Exception("Error deleting from student_class: " . $stmt->error);
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM parent WHERE StudentID = ?");
        $stmt->bind_param("i", $studentId);
        if (!$stmt->execute()) throw new Exception("Error deleting from parent: " . $stmt->error);
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM student WHERE StudentID = ?");
        $stmt->bind_param("i", $studentId);
        if (!$stmt->execute()) throw new Exception("Error deleting from student: " . $stmt->error);
        $stmt->close();

        $conn->commit();
        echo json_encode(["success" => true, "message" => "Student deleted successfully"]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }

    $conn->close();
}
?>
