<?php
// 1. Middleware aur Database connect karein
require_once 'middleware.php';
checkAdminSession(); // Bina login ke access nahi milega
require_once '../config/db.php';

try {
    // 2. Filters ko pakadna (URL se)
    $date = $_GET['date'] ?? '';     // Example: ?date=2023-10-25
    $status = $_GET['status'] ?? ''; // Example: ?status=confirmed
    $search = $_GET['search'] ?? ''; // Example: ?search=Rahul
    
    // 3. Base Query
    $sql = "SELECT * FROM bookings WHERE 1=1";
    $params = [];

    // 4. Filters apply karna
    if (!empty($date)) {
        $sql .= " AND booking_date = ?";
        $params[] = $date;
    }

    if (!empty($status)) {
        $sql .= " AND payment_status = ?";
        $params[] = $status;
    }

    if (!empty($search)) {
        $sql .= " AND (full_name LIKE ? OR phone LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    // Nayi bookings pehle dikhein
    $sql .= " ORDER BY created_at DESC";

    // 5. Query execute karna
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "count" => count($bookings),
        "data" => $bookings
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}