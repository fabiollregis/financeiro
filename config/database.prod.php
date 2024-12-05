<?php
// Configurações do banco de dados para produção
$host = 'localhost'; // Geralmente é localhost no cPanel
$dbname = ''; // Nome do banco de dados que você criará no cPanel
$username = ''; // Usuário do banco de dados no cPanel
$password = ''; // Senha do banco de dados no cPanel

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
