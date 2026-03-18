<?php
// Session start karna zaruri hai middleware ke liye
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Preflight request handle karne ke liye
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Zaruri files load karein
require_once 'middleware.php';

$route = $_GET['route'] ?? '';

switch ($route) {
    // ===================================================
    // 1. PUBLIC ROUTES 
    // ===================================================
    case 'astrologer-info':
        require 'get_info.php';
        break;
        
    case 'slots':
        require 'get_slots.php';
        break;
        
    case 'booked-dates':
        require 'get_booked_dates.php';
        break;

    case 'reviews':
        require 'get_reviews.php'; // Sirf approved wale public ko dikhenge
        break;
        
    case 'submit-review':
        require 'submit_review.php'; // Customer review dalne ke liye
        break;

    case 'admin-login':
        require 'admin_login.php'; 
        break;

    // ===================================================
    // 2. ADMIN PROTECTED ROUTES (Middleware zaroori hai)
    // ===================================================
    
    // Auth
    case 'admin-logout':
        require 'admin_logout.php';
        break;

    // Appointments/Bookings
    case 'get-admin-bookings':
        checkAdminSession();
        require 'get_admin_bookings.php'; 
        break;

    case 'get-single-booking':
        checkAdminSession();
        require 'get_single_booking.php';
        break;

    case 'update-status':
        checkAdminSession();
        require 'update_booking_status.php';
        break;

    // Analytics & Stats
    case 'dashboard-stats':
        checkAdminSession();
        require 'get_dashboard_stats.php';
        break;

    case 'get-analytics':
        checkAdminSession();
        require 'get_analytics.php';
        break;

    // Reviews Management
    case 'get-admin-reviews':
        checkAdminSession();
        require 'get_admin_reviews.php'; 
        break;

    case 'approve-review':
        checkAdminSession();
        require 'approve_review.php'; 
        break;

    case 'delete-review':
        checkAdminSession();
        require 'delete_review.php';
        break;

    // Export Data
    case 'export-bookings':
        checkAdminSession();
        require 'export_bookings.php';
        break;

        case 'meta-test':
        require_once 'meta_capi_helper.php';
        // Testing ke liye dummy data
        $response = sendMetaPurchase('test@example.com', '919876543210', 501, 'TEST_REF_' . time());
        echo json_encode([
            "success" => true, 
            "meta_response" => $response,
            "message" => "Meta CAPI Triggered"
        ]);
        break;

    // ===================================================
    // 404 - DEFAULT
    // ===================================================
    default:
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "API Route not found",
            "error" => "404_NOT_FOUND"
        ]);
        break;
}
?>