<?php
class Category {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAll($userId) {
        $query = "SELECT * FROM categories WHERE user_id = :user_id ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByType($type, $userId) {
        if (empty($type)) {
            return $this->getAll($userId);
        }
        
        $query = "SELECT * FROM categories WHERE (type = :type OR type = 'ambos') AND user_id = :user_id ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':type' => $type,
            ':user_id' => $userId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $query = "INSERT INTO categories (name, type, user_id) VALUES (:name, :type, :user_id)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':name' => $data['name'],
            ':type' => $data['type'],
            ':user_id' => $data['user_id']
        ]);
    }
    
    public function update($id, $data) {
        $query = "UPDATE categories SET name = :name, type = :type WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':type' => $data['type']
        ]);
    }
    
    public function delete($id) {
        // Primeiro verifica se existem transações usando esta categoria
        $query = "SELECT COUNT(*) FROM transactions WHERE category_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Não é possível excluir esta categoria pois existem transações associadas a ela.');
        }
        
        $query = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
?>
