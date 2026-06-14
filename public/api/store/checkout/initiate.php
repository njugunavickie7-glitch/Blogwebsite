<?php
// public/api/store/checkout/initiate.php
ini_set('display_errors', '0');
error_reporting(E_ALL);
ob_start();
header('Content-Type: application/json; charset=utf-8');

function out($d, $code = 200) { while (ob_get_level() > 0) ob_end_clean(); http_response_code($code); echo json_encode($d); exit; }

require_once __DIR__ . '/../../../../app/config/db_connect.php';
require_once __DIR__ . '/../../../../app/controllers/CheckoutController.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    out(['success' => false, 'message' => 'Method not allowed'], 405);
}
if (empty($_SESSION['user_id'])) {
    out(['success' => false, 'message' => 'Please login to checkout', 'require_login' => true], 401);
}

$raw = file_get_contents('php://input');
$body = json_decode($raw, true);
if (!is_array($body)) $body = $_POST;

try {
    $mpesaCfg = require __DIR__ . '/../../../../app/config/mpesa.php';
    $checkout = new CheckoutController($pdo, new MpesaService($mpesaCfg));
    $result = $checkout->initiate((int) $_SESSION['user_id'], $body);
} catch (Throwable $e) {
    error_log('[checkout/initiate] ' . $e->getMessage());
    out(['success' => false, 'message' => 'Could not start checkout. Please try again.'], 500);
}

out($result, $result['success'] ? 200 : 422);