<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/AuthController.php';

class CategoryController {
    private $category;
    private $auth;
    
    public function __construct() {
        global $conn;
        $this->category = new Category($conn);
        $this->auth = new AuthController();
        
        // Verificar autenticação
        if (!$this->auth->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
    
    public function index() {
        return $this->category->getAll($this->auth->getCurrentUserId());
    }
    
    public function getByType($type) {
        return $this->category->getByType($type, $this->auth->getCurrentUserId());
    }
    
    public function store() {
        $data = [
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'user_id' => $this->auth->getCurrentUserId()
        ];
        
        if ($this->category->create($data)) {
            return ['success' => true, 'message' => 'Categoria criada com sucesso'];
        }
        
        return ['success' => false, 'message' => 'Erro ao criar categoria'];
    }
    
    public function update($id) {
        // Verificar se a categoria pertence ao usuário
        $category = $this->category->getById($id);
        if (!$category || $category['user_id'] !== $this->auth->getCurrentUserId()) {
            return ['success' => false, 'message' => 'Categoria não encontrada'];
        }
        
        $data = [
            'name' => $_POST['name'],
            'type' => $_POST['type']
        ];
        
        if ($this->category->update($id, $data)) {
            return ['success' => true, 'message' => 'Categoria atualizada com sucesso'];
        }
        
        return ['success' => false, 'message' => 'Erro ao atualizar categoria'];
    }
    
    public function delete($id) {
        // Verificar se a categoria pertence ao usuário
        $category = $this->category->getById($id);
        if (!$category || $category['user_id'] !== $this->auth->getCurrentUserId()) {
            return ['success' => false, 'message' => 'Categoria não encontrada'];
        }
        
        try {
            if ($this->category->delete($id)) {
                return ['success' => true, 'message' => 'Categoria excluída com sucesso'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
        
        return ['success' => false, 'message' => 'Erro ao excluir categoria'];
    }
}
