<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/AuthController.php';

class ReportController {
    private $conn;
    private $auth;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->auth = new AuthController();
        
        if (!$this->auth->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
    
    public function getTotals($year, $month = null) {
        $userId = $this->auth->getCurrentUserId();
        
        $params = [':user_id' => $userId, ':year' => $year];
        $monthCondition = '';
        
        if ($month !== null) {
            $params[':month'] = $month;
            $monthCondition = 'AND MONTH(date) = :month';
        }
        
        $query = "SELECT 
                    SUM(CASE WHEN type = 'receita' THEN amount ELSE 0 END) as total_receitas,
                    SUM(CASE WHEN type = 'despesa' THEN amount ELSE 0 END) as total_despesas,
                    COUNT(DISTINCT CASE WHEN type = 'receita' THEN date END) as dias_receitas,
                    COUNT(DISTINCT CASE WHEN type = 'despesa' THEN date END) as dias_despesas
                 FROM transactions 
                 WHERE user_id = :user_id
                 AND YEAR(date) = :year
                 {$monthCondition}";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getMonthlyData($year) {
        $userId = $this->auth->getCurrentUserId();
        
        $query = "SELECT 
                    SUM(CASE WHEN type = 'receita' THEN amount ELSE 0 END) as total_receitas,
                    SUM(CASE WHEN type = 'despesa' THEN amount ELSE 0 END) as total_despesas,
                    DATE_FORMAT(date, '%Y-%m') as month
                 FROM transactions 
                 WHERE user_id = :user_id
                 AND YEAR(date) = :year
                 GROUP BY DATE_FORMAT(date, '%Y-%m')
                 ORDER BY month ASC";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':user_id' => $userId,
            ':year' => $year
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getDailyData($year, $month) {
        $userId = $this->auth->getCurrentUserId();
        
        $query = "SELECT 
                    SUM(CASE WHEN type = 'receita' THEN amount ELSE 0 END) as total_receitas,
                    SUM(CASE WHEN type = 'despesa' THEN amount ELSE 0 END) as total_despesas,
                    DATE_FORMAT(date, '%Y-%m-%d') as day
                 FROM transactions 
                 WHERE user_id = :user_id
                    AND YEAR(date) = :year
                    AND MONTH(date) = :month
                 GROUP BY DATE_FORMAT(date, '%Y-%m-%d')
                 ORDER BY day ASC";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':user_id' => $userId,
            ':year' => $year,
            ':month' => $month
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCategoryTotals($year, $month = null) {
        $userId = $this->auth->getCurrentUserId();
        
        $params = [':user_id' => $userId, ':year' => $year];
        $monthCondition = '';
        
        if ($month !== null) {
            $params[':month'] = $month;
            $monthCondition = 'AND MONTH(t.date) = :month';
        }
        
        $query = "SELECT 
                    c.name as category_name,
                    SUM(t.amount) as total,
                    t.type
                 FROM transactions t
                 JOIN categories c ON t.category_id = c.id
                 WHERE t.user_id = :user_id
                    AND YEAR(t.date) = :year
                    {$monthCondition}
                 GROUP BY c.id, t.type
                 ORDER BY t.type, total DESC";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAvailableYears() {
        $userId = $this->auth->getCurrentUserId();
        
        $query = "SELECT DISTINCT YEAR(date) as year
                 FROM transactions 
                 WHERE user_id = :user_id
                 ORDER BY year DESC";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
