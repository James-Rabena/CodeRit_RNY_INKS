<?php
// === DATABASE CONFIGURATION FOR INFINITYFREE ===
// Replace the values below with your actual InfinityFree MySQL database credentials

$db_host = 'sql308.infinityfree.com'; // Example: change this to your real MySQL Hostname
$db_user = 'if0_38911492';            // Your InfinityFree MySQL username
$db_pass = '1aT0aibPfUn';    // Your InfinityFree MySQL password
$db_name = 'if0_38911492_fragrancefusion'; // Full database name from control panel

// === CREATE CONNECTION ===
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// === CHECK CONNECTION ===
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// === SET UTF-8 CHARACTER SET ===
if (!$conn->set_charset("utf8mb4")) {
    die("Error setting character set: " . $conn->error);
}
?>
