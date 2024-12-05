<?php
// Lista de arquivos e diretórios para remover
$toRemove = [
    __DIR__ . '/financeiro.zip',
    __DIR__ . '/migrate_database_v5.sql',
    __DIR__ . '/backup.php',
    __DIR__ . '/backup_error.log'
];

// Função para remover diretório recursivamente
function removeDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        
        if (!removeDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    
    return rmdir($dir);
}

// Remove arquivos individuais
$removed = [];
$errors = [];

foreach ($toRemove as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            $removed[] = "Arquivo removido: " . basename($file);
        } else {
            $errors[] = "Erro ao remover: " . basename($file);
        }
    }
}

// Limpa diretório de backups
$backupsDir = __DIR__ . '/backups';
if (file_exists($backupsDir)) {
    if (removeDirectory($backupsDir)) {
        $removed[] = "Diretório removido: backups";
    } else {
        $errors[] = "Erro ao remover diretório: backups";
    }
}

// Exibe resultados
echo "<h2>Limpeza do Sistema</h2>";

if (!empty($removed)) {
    echo "<h3>Arquivos Removidos:</h3>";
    echo "<ul>";
    foreach ($removed as $item) {
        echo "<li>$item</li>";
    }
    echo "</ul>";
}

if (!empty($errors)) {
    echo "<h3>Erros:</h3>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
}

if (empty($removed) && empty($errors)) {
    echo "<p>Nenhum arquivo desnecessário encontrado.</p>";
}
?>
