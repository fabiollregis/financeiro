<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/CategoryController.php';

class TransactionController {
    public $transaction;
    private $auth;
    
    public function __construct() {
        global $conn;
        $this->transaction = new Transaction($conn);
        $this->auth = new AuthController();
        
        // Verificar autenticação
        if (!$this->auth->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
    
    private function formatAmount($amount) {
        // Remove pontos de milhar e substitui vírgula por ponto
        $amount = str_replace('.', '', $amount);
        $amount = str_replace(',', '.', $amount);
        return (float) $amount;
    }
    
    public function index() {
        $filters = [
            'start_date' => $_GET['start_date'] ?? null,
            'end_date' => $_GET['end_date'] ?? null,
            'type' => $_GET['type'] ?? null,
            'category_id' => $_GET['category_id'] ?? null,
            'user_id' => $this->auth->getCurrentUserId()
        ];
        
        $transactions = $this->transaction->getAll($filters);
        $totals = $this->transaction->getTotals($filters['start_date'], $filters['end_date'], $filters['user_id']);
        
        // Carregar categorias do usuário
        $categoryController = new CategoryController();
        $categories = $categoryController->index();
        
        return [
            'transactions' => $transactions,
            'totals' => $totals,
            'categories' => $categories
        ];
    }
    
    public function store($data) {
        try {
            // Validação dos dados
            if (empty($data['description'])) {
                throw new Exception('A descrição é obrigatória');
            }
            if (empty($data['amount'])) {
                throw new Exception('O valor é obrigatório');
            }
            if (empty($data['date'])) {
                throw new Exception('A data é obrigatória');
            }
            if (empty($data['category_id'])) {
                throw new Exception('A categoria é obrigatória');
            }
            if (empty($data['type'])) {
                throw new Exception('O tipo é obrigatório');
            }

            // Adiciona o ID do usuário
            $data['user_id'] = $this->auth->getCurrentUserId();
            
            // Formata o valor corretamente
            $data['amount'] = $this->formatAmount($data['amount']);
            
            if ($this->transaction->create($data)) {
                return ['success' => true, 'message' => 'Transação criada com sucesso'];
            }
            
            throw new Exception('Erro ao criar transação');
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function update($id, $data) {
        try {
            // Validação dos dados
            if (empty($data['description'])) {
                throw new Exception('A descrição é obrigatória');
            }
            if (empty($data['amount'])) {
                throw new Exception('O valor é obrigatório');
            }
            if (empty($data['date'])) {
                throw new Exception('A data é obrigatória');
            }
            if (empty($data['category_id'])) {
                throw new Exception('A categoria é obrigatória');
            }
            if (empty($data['type'])) {
                throw new Exception('O tipo é obrigatório');
            }

            // Verifica se a transação pertence ao usuário
            $transaction = $this->transaction->getById($id);
            if (!$transaction || $transaction['user_id'] != $this->auth->getCurrentUserId()) {
                throw new Exception('Transação não encontrada');
            }
            
            // Formata o valor corretamente
            $data['amount'] = $this->formatAmount($data['amount']);
            
            if ($this->transaction->update($id, $data)) {
                return ['success' => true, 'message' => 'Transação atualizada com sucesso'];
            }
            
            throw new Exception('Erro ao atualizar transação');
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function delete($id) {
        try {
            // Verifica se a transação pertence ao usuário
            $transaction = $this->transaction->getById($id);
            if (!$transaction || $transaction['user_id'] != $this->auth->getCurrentUserId()) {
                throw new Exception('Transação não encontrada');
            }
            
            if ($this->transaction->delete($id)) {
                return ['success' => true, 'message' => 'Transação excluída com sucesso'];
            }
            
            throw new Exception('Erro ao excluir transação');
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function get($id) {
        // Verifica se a transação pertence ao usuário
        $transaction = $this->transaction->getById($id);
        if (!$transaction || $transaction['user_id'] != $this->auth->getCurrentUserId()) {
            return null;
        }
        return $transaction;
    }
}
