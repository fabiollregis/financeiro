<?php
header('Content-Type: application/json');
require_once 'AuthController.php';

$auth = new AuthController();
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'login':
            echo json_encode($auth->login($_POST));
            break;
            
        case 'register':
            echo json_encode($auth->register($_POST));
            break;
            
        case 'logout':
            $auth->logout();
            header('Location: ../../index.php');
            exit;
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
