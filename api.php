<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$ip = $_SERVER['REMOTE_ADDR'];
if (!isset($_SESSION['rate_limit'][$ip])) {
    $_SESSION['rate_limit'][$ip] = ['count' => 1, 'time' => time()];
} else {
    if (time() - $_SESSION['rate_limit'][$ip]['time'] < 60) {
        $_SESSION['rate_limit'][$ip]['count']++;
        if ($_SESSION['rate_limit'][$ip]['count'] > 100) {
            http_response_code(429);
            echo json_encode(['error' => 'Muitas requisições']);
            exit;
        }
    } else {
        $_SESSION['rate_limit'][$ip] = ['count' => 1, 'time' => time()];
    }
}

require 'config.php';
require 'routes.php';
