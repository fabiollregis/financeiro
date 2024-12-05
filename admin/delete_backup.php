<?php
require_once '../config/database.php';
require_once '../src/controllers/AuthController.php';

// Verifica autenticação
$auth = new AuthController();
if (!$auth->isLoggedIn()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

// Verifica se recebeu o nome do arquivo
if (!isset($_POST['file'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Nome do arquivo não fornecido']);
    exit;
}

$filename = $_POST['file'];

// Valida o nome do arquivo (deve ser um backup válido)
if (!preg_match('/^backup_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.zip$/', $filename)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Nome de arquivo inválido']);
    exit;
}

$filepath = __DIR__ . '/../backups/' . $filename;

// Verifica se o arquivo existe
if (!file_exists($filepath)) {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['success' => false, 'message' => 'Arquivo não encontrado']);
    exit;
}

// Tenta excluir o arquivo
if (unlink($filepath)) {
    echo json_encode(['success' => true, 'message' => 'Backup excluído com sucesso']);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir arquivo']);
}
?>
