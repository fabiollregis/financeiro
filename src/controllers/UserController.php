<?php

class UserController {
    private $conn;

    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        global $conn;
        $this->conn = $conn;
    }

    public function updateProfile($userId, $data) {
        try {
            $updates = [];
            $params = [];

            if (isset($data['name'])) {
                $updates[] = "name = :name";
                $params[':name'] = $data['name'];
            }

            if (isset($data['email'])) {
                // Verificar se o email já existe para outro usuário
                $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email AND id != :userId");
                $stmt->execute([':email' => $data['email'], ':userId' => $userId]);
                if ($stmt->fetch()) {
                    throw new Exception('Este e-mail já está em uso');
                }

                $updates[] = "email = :email";
                $params[':email'] = $data['email'];
            }

            if (isset($data['password']) && $data['password'] !== null) {
                $updates[] = "password = :password";
                $params[':password'] = $data['password'];
            }

            if (empty($updates)) {
                return true; // Nada para atualizar
            }

            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :userId";
            $params[':userId'] = $userId;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception('Erro ao atualizar perfil: ' . $e->getMessage());
        }
    }
}
