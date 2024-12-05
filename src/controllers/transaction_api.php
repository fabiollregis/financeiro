<?php
header('Content-Type: application/json');
require_once 'TransactionController.php';

$controller = new TransactionController();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'create':
            $result = $controller->store($_POST);
            break;
            
        case 'update':
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception('ID não fornecido');
            }
            $result = $controller->update($id, $_POST);
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? $_GET['id'] ?? null;
            if (!$id) {
                throw new Exception('ID não fornecido');
            }
            $result = $controller->delete($id);
            break;
            
        case 'get':
            $id = $_POST['id'] ?? $_GET['id'] ?? null;
            if (!$id) {
                throw new Exception('ID não fornecido');
            }
            $transaction = $controller->get($id);
            if ($transaction) {
                $result = ['success' => true, 'data' => $transaction];
            } else {
                throw new Exception('Transação não encontrada');
            }
            break;
            
        default:
            throw new Exception('Ação inválida');
    }
} catch (Exception $e) {
    $result = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($result);
