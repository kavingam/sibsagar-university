<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['room_no']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$room_no = $data['room_no'];
$status = intval($data['status']);

// TODO: Replace with your actual DB connection
$conn = new mysqli('localhost', 'root', 'password', 'sibsagar_university');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Update room status
$stmt = $conn->prepare("UPDATE rooms_info SET status = ? WHERE room_no = ?");
$stmt->bind_param('is', $status, $room_no);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}
$stmt->close();
$conn->close();
