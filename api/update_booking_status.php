<?php
// 1. Security & Header Setup
require_once 'middleware.php';
checkAdminSession(); // Validate admin access
require_once '../config/db.php';

header('Content-Type: application/json');

// 2. Read JSON Input Data
$data = json_decode(file_get_contents("php://input"), true);

$id         = $data['id'] ?? null;
$new_status = $data['status'] ?? null; // Options: confirmed, cancelled, completed

// 3. Validation: Ensure ID and Status are provided
if (!$id || !$new_status) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Booking ID and Status are required."]);
    exit;
}

// 4. Validation: Allow only specific status values (Task Requirement)
$allowed_statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
if (!in_array($new_status, $allowed_statuses)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid status value provided."]);
    exit;
}

try {
    // 5. Update Status in Database
    $stmt = $conn->prepare("UPDATE bookings SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$new_status, $id]);

    // Check if any row was actually updated
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "success" => true,
            "message" => "Booking status updated to '$new_status' successfully."
        ]);
    } else {
        echo json_encode([
            "success" => false, 
            "message" => "No changes made. Booking not found or status already matches."
        ]);
    }

} catch (PDOException $e) {
    // Error Handling
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database Error: " . $e->getMessage()]);
}
?>