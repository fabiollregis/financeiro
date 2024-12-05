<?php
header('Content-Type: application/json');
require_once __DIR__ . '/CategoryController.php';
require_once __DIR__ . '/AuthController.php';

$auth = new AuthController();

// Verificar autenticação
if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$controller = new CategoryController();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['type'])) {
                $categories = $controller->getByType($_GET['type']);
            } else {
                $categories = $controller->index();
            }
            echo json_encode($categories);
            break;

        case 'POST':
            $response = $controller->store();
            echo json_encode($response);
            break;

        case 'PUT':
            parse_str(file_get_contents('php://input'), $_PUT);
            $_POST = $_PUT;
            if (!isset($_GET['id'])) {
                throw new Exception('ID não fornecido');
            }
            $response = $controller->update($_GET['id']);
            echo json_encode($response);
            break;

        case 'DELETE':
            if (!isset($_GET['id'])) {
                throw new Exception('ID não fornecido');
            }
            $response = $controller->delete($_GET['id']);
            echo json_encode($response);
            break;

        default:
            throw new Exception('Método não suportado');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
