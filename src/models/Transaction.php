<?php
class Transaction {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    private function formatAmount($amount) {
        // Se já for float, retorna direto
        if (is_float($amount)) {
            return $amount;
        }
        
        // Remove pontos de milhar
        $amount = str_replace('.', '', $amount);
        // Substitui vírgula por ponto
        $amount = str_replace(',', '.', $amount);
        // Converte para float
        return (float) $amount;
    }
    
    public function create($data) {
        try {
            $query = "INSERT INTO transactions (description, amount, type, category_id, date, user_id) 
                     VALUES (:description, :amount, :type, :category_id, :date, :user_id)";
            
            $stmt = $this->conn->prepare($query);
            
            // Processa o valor
            $amount = $this->formatAmount($data['amount']);
            
            $stmt->execute([
                ':description' => htmlspecialchars($data['description']),
                ':amount' => $amount,
                ':type' => $data['type'],
                ':category_id' => (int)$data['category_id'],
                ':date' => $data['date'],
                ':user_id' => (int)$data['user_id']
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log("Erro ao criar transação: " . $e->getMessage());
            return false;
        }
    }
    
    public function update($id, $data) {
        try {
            $query = "UPDATE transactions 
                     SET description = :description,
                         amount = :amount,
                         type = :type,
                         category_id = :category_id,
                         date = :date
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Processa o valor
            $amount = $this->formatAmount($data['amount']);
            
            $stmt->execute([
                ':id' => (int)$id,
                ':description' => htmlspecialchars($data['description']),
                ':amount' => $amount,
                ':type' => $data['type'],
                ':category_id' => (int)$data['category_id'],
                ':date' => $data['date']
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log("Erro ao atualizar transação: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAll($filters = []) {
        try {
            $where = [];
            $params = [];
            
            // Filtro por usuário
            if (!empty($filters['user_id'])) {
                $where[] = "t.user_id = :user_id";
                $params[':user_id'] = (int)$filters['user_id'];
            }
            
            // Filtros adicionais
            if (!empty($filters['start_date'])) {
                $where[] = "t.date >= :start_date";
                $params[':start_date'] = $filters['start_date'];
            }
            if (!empty($filters['end_date'])) {
                $where[] = "t.date <= :end_date";
                $params[':end_date'] = $filters['end_date'];
            }
            if (!empty($filters['type'])) {
                $where[] = "t.type = :type";
                $params[':type'] = $filters['type'];
            }
            if (!empty($filters['category_id'])) {
                $where[] = "t.category_id = :category_id";
                $params[':category_id'] = (int)$filters['category_id'];
            }
            
            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
            
            $query = "SELECT t.*, c.name as category_name 
                     FROM transactions t 
                     LEFT JOIN categories c ON t.category_id = c.id 
                     $whereClause 
                     ORDER BY t.date DESC, t.id DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar transações: " . $e->getMessage());
            return [];
        }
    }
    
    public function find($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM transactions WHERE id = :id");
            $stmt->execute([':id' => (int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar transação: " . $e->getMessage());
            return null;
        }
    }
    
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM transactions WHERE id = :id");
            return $stmt->execute([':id' => (int)$id]);
        } catch (Exception $e) {
            error_log("Erro ao excluir transação: " . $e->getMessage());
            return false;
        }
    }
    
    public function getById($id) {
        try {
            $query = "SELECT * FROM transactions WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => (int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar transação por ID: " . $e->getMessage());
            return null;
        }
    }
    
    public function getTotals($startDate = null, $endDate = null, $userId) {
        try {
            $where = ["user_id = :user_id"];
            $params = [':user_id' => $userId];
            
            if ($startDate) {
                $where[] = "date >= :start_date";
                $params[':start_date'] = $startDate;
            }
            
            if ($endDate) {
                $where[] = "date <= :end_date";
                $params[':end_date'] = $endDate;
            }
            
            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
            
            $query = "SELECT 
                        COALESCE(SUM(CASE WHEN type = 'receita' THEN amount ELSE 0 END), 0) as receitas,
                        COALESCE(SUM(CASE WHEN type = 'despesa' THEN amount ELSE 0 END), 0) as despesas
                     FROM transactions 
                     $whereClause";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao calcular totais: " . $e->getMessage());
            return [
                'receitas' => 0,
                'despesas' => 0
            ];
        }
    }
}
