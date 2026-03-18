<?php
session_start();
session_unset(); // Saare variables hatao
session_destroy(); // Session khatam karo (Task ki line 2)

echo json_encode(["success" => true, "message" => "Logged out successfully"]);
?>