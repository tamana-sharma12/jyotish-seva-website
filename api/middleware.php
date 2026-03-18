<?php
function checkAdminSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
        // 401 Unauthorized error (Task ki line 3)
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Unauthorized access. Please login."]);
        exit;
    }
}