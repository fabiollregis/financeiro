<?php
require_once 'AuthController.php';

header('Content-Type: application/json');

$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = $auth->login($_POST);
    echo json_encode($response);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Método não permitido']);
?>
