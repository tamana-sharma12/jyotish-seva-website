<?php
// 1. Security & Header Setup
require_once 'middleware.php';
checkAdminSession(); // Validate admin session
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    // 2. Handle Pagination Inputs (Task Requirement)
    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    // 3. Capture Filters from URL
    $date   = $_GET['date'] ?? ''; 
    $status = $_GET['status'] ?? ''; 
    $search = $_GET['search'] ?? ''; 
    
    // Base SQL Query
    $sql = "SELECT * FROM bookings WHERE 1=1";
    $params = [];

    // Apply Date Filter
    if (!empty($date)) {
        $sql .= " AND booking_date = ?";
        $params[] = $date;
    }

    // Apply Status Filter
    if (!empty($status)) {
        $sql .= " AND status = ?"; 
        $params[] = $status;
    }

    // Apply Search Filter (Name or Phone)
    if (!empty($search)) {
        $sql .= " AND (full_name LIKE ? OR phone LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    // 4. Apply Sorting and Pagination
    $sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Fetch Total Record Count (Required for Frontend Pagination)
    $total_count = $conn->query("SELECT COUNT(*) FROM bookings")->fetchColumn();

    // Success Response
    echo json_encode([
        "success" => true,
        "page" => $page,
        "limit" => $limit,
        "total_count" => (int)$total_count,
        "count" => count($bookings),
        "data" => $bookings
    ]);

} catch (PDOException $e) {
    // Error Response
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Database Error: " . $e->getMessage()
    ]);
} 
?>