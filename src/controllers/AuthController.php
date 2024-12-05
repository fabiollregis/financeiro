<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Category.php';

class AuthController {
    private $user;
    private $category;
    
    public function __construct() {
        global $conn;
        $this->user = new User($conn);
        $this->category = new Category($conn);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    private function createDefaultCategories($userId) {
        $defaultCategories = [
            ['name' => 'Salário', 'type' => 'receita'],
            ['name' => 'Freelance', 'type' => 'receita'],
            ['name' => 'Investimentos', 'type' => 'receita'],
            ['name' => 'Alimentação', 'type' => 'despesa'],
            ['name' => 'Transporte', 'type' => 'despesa'],
            ['name' => 'Moradia', 'type' => 'despesa'],
            ['name' => 'Saúde', 'type' => 'despesa'],
            ['name' => 'Educação', 'type' => 'despesa'],
            ['name' => 'Lazer', 'type' => 'despesa'],
            ['name' => 'Outros', 'type' => 'ambos']
        ];
        
        foreach ($defaultCategories as $category) {
            $category['user_id'] = $userId;
            $this->category->create($category);
        }
    }
    
    public function register($data) {
        try {
            // Validações básicas
            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                throw new Exception('Todos os campos são obrigatórios');
            }
            
            if (strlen($data['password']) < 6) {
                throw new Exception('A senha deve ter pelo menos 6 caracteres');
            }
            
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('E-mail inválido');
            }
            
            // Verificar se e-mail já existe
            if ($this->user->findByEmail($data['email'])) {
                throw new Exception('Este e-mail já está cadastrado');
            }
            
            // Criar usuário
            if ($this->user->create($data)) {
                $newUser = $this->user->findByEmail($data['email']);
                $_SESSION['user_id'] = $newUser['id'];
                $_SESSION['user_name'] = $newUser['name'];
                $_SESSION['user_email'] = $newUser['email'];
                
                // Criar categorias padrão
                $this->createDefaultCategories($newUser['id']);
                
                return ['success' => true, 'message' => 'Cadastro realizado com sucesso'];
            }
            
            throw new Exception('Erro ao criar usuário');
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function login($data) {
        try {
            if (empty($data['email']) || empty($data['password'])) {
                throw new Exception('E-mail e senha são obrigatórios');
            }
            
            $user = $this->user->findByEmail($data['email']);
            
            if (!$user || !$this->user->verifyPassword($data['password'], $user['password'])) {
                throw new Exception('E-mail ou senha incorretos');
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            return ['success' => true, 'message' => 'Login realizado com sucesso'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function logout() {
        session_destroy();
        return ['success' => true];
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function getCurrentUserName() {
        return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
    }

    public function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email']
        ];
    }

    public function verifyPassword($password) {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        $user = $this->user->findById($_SESSION['user_id']);
        if (!$user) {
            return false;
        }
        
        return password_verify($password, $user['password']);
    }
}

// Tratar requisições diretas ao controller
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController();
    header('Content-Type: application/json');
    
    // Verifica se é uma requisição de login (deve conter email e password)
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $response = $auth->login($_POST);
        echo json_encode($response);
        exit;
    }
}
?>
