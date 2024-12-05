<?php
header('Content-Type: application/json');
require_once __DIR__ . '/TransactionController.php';
require_once __DIR__ . '/AuthController.php';

$auth = new AuthController();

// Verificar autenticação
if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$controller = new TransactionController();
$method = $_SERVER['REQUEST_METHOD'];
$response = [];

try {
    switch ($method) {
        case 'GET':
            // Adicionar user_id aos filtros
            $_GET['user_id'] = $auth->getCurrentUserId();
            
            if (isset($_GET['action']) && $_GET['action'] === 'totals') {
                $startDate = $_GET['start_date'] ?? null;
                $endDate = $_GET['end_date'] ?? null;
                $response = $controller->getTotals($startDate, $endDate, $_GET['user_id']);
            } else {
                $response = $controller->index($_GET);
            }
            break;

        case 'POST':
            // Adicionar user_id aos dados
            $_POST['user_id'] = $auth->getCurrentUserId();
            $response = $controller->store();
            break;

        case 'PUT':
            parse_str(file_get_contents('php://input'), $_PUT);
            $_POST = $_PUT;
            if (!isset($_GET['id'])) {
                throw new Exception('ID não fornecido');
            }
            
            // Verificar se a transação pertence ao usuário
            $transaction = $controller->transaction->getById($_GET['id']);
            if (!$transaction || $transaction['user_id'] !== $auth->getCurrentUserId()) {
                throw new Exception('Transação não encontrada');
            }
            
            $response = $controller->update($_GET['id']);
            break;

        case 'DELETE':
            if (!isset($_GET['id'])) {
                throw new Exception('ID não fornecido');
            }
            
            // Verificar se a transação pertence ao usuário
            $transaction = $controller->transaction->getById($_GET['id']);
            if (!$transaction || $transaction['user_id'] !== $auth->getCurrentUserId()) {
                throw new Exception('Transação não encontrada');
            }
            
            $response = $controller->delete($_GET['id']);
            break;

        default:
            throw new Exception('Método não suportado');
    }

    if (!isset($response['success'])) {
        $response = ['success' => true, 'data' => $response];
    }

} catch (Exception $e) {
    http_response_code(400);
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
