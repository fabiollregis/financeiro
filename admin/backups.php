<?php
require_once '../config/database.php';
require_once '../src/controllers/AuthController.php';

// Verifica autenticação
$auth = new AuthController();
if (!$auth->isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

// Lista os backups disponíveis
$backupDir = __DIR__ . '/../backups';
$backups = [];

if (file_exists($backupDir)) {
    foreach (glob($backupDir . '/backup_*.zip') as $file) {
        $backups[] = [
            'name' => basename($file),
            'size' => filesize($file),
            'date' => date('d/m/Y H:i:s', filemtime($file))
        ];
    }
}

// Ordena por data, mais recente primeiro
usort($backups, function($a, $b) {
    return strcmp($b['date'], $a['date']);
});
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Backups - Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Backups Disponíveis</h5>
                        <a href="backup.php" class="btn btn-light btn-sm">
                            <i class="fas fa-plus me-2"></i>Novo Backup
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($backups)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Nenhum backup encontrado.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Nome</th>
                                            <th>Tamanho</th>
                                            <th class="text-end">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($backups as $backup): ?>
                                            <tr>
                                                <td><?php echo $backup['date']; ?></td>
                                                <td><?php echo $backup['name']; ?></td>
                                                <td><?php echo number_format($backup['size'] / 1024 / 1024, 2) . ' MB'; ?></td>
                                                <td class="text-end">
                                                    <a href="../backups/<?php echo $backup['name']; ?>" class="btn btn-sm btn-success" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-danger delete-backup" 
                                                            data-file="<?php echo $backup['name']; ?>" 
                                                            title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja excluir este backup?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Excluir</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let fileToDelete = '';
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            
            // Ao clicar no botão de excluir
            $('.delete-backup').on('click', function() {
                fileToDelete = $(this).data('file');
                deleteModal.show();
            });
            
            // Confirma a exclusão
            $('#confirmDelete').on('click', function() {
                $.post('delete_backup.php', { file: fileToDelete }, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Erro ao excluir backup: ' + response.message);
                    }
                })
                .fail(function() {
                    alert('Erro ao excluir backup. Tente novamente.');
                });
            });
        });
    </script>
</body>
</html>
