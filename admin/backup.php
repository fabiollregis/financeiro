<?php
require_once '../config/database.php';
require_once '../src/controllers/AuthController.php';

// Verifica autenticação
$auth = new AuthController();
if (!$auth->isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup do Sistema - Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Backup do Sistema</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-database fa-4x text-primary mb-3"></i>
                            <h4>Gerenciamento de Backup</h4>
                            <p class="text-muted">Faça backup dos seus dados e arquivos do sistema</p>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            O backup incluirá:
                            <ul class="mb-0">
                                <li>Banco de dados completo</li>
                                <li>Todos os arquivos do sistema</li>
                                <li>Configurações</li>
                            </ul>
                        </div>

                        <div id="backupStatus" class="alert d-none"></div>

                        <div class="d-grid gap-2">
                            <button id="createBackup" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>Criar Backup
                            </button>
                            <a href="backups.php" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-2"></i>Listar Backups
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#createBackup').on('click', function() {
                const button = $(this);
                const status = $('#backupStatus');
                
                // Desabilita o botão e mostra loading
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Criando backup...');
                status.removeClass('d-none alert-success alert-danger').addClass('alert-info').html('Criando backup, por favor aguarde...');
                
                // Faz a requisição para criar o backup
                $.ajax({
                    url: '../backup.php?execute=1',
                    method: 'GET',
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            status.removeClass('alert-info alert-danger').addClass('alert-success')
                                .html(`<i class="fas fa-check-circle me-2"></i>${result.message}`);
                            
                            // Adiciona link para download se disponível
                            if (result.path) {
                                const downloadPath = result.path.replace(/^.*[\\\/]/, '');
                                status.append(`<br><a href="../backups/${downloadPath}" class="btn btn-sm btn-success mt-2">
                                    <i class="fas fa-download me-2"></i>Baixar Backup
                                </a>`);
                            }
                        } else {
                            throw new Error(result.message || 'Erro desconhecido ao criar backup');
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMsg = 'Erro ao criar backup';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response && response.message) {
                                errorMsg = response.message;
                            }
                        } catch (e) {
                            errorMsg += ': ' + (error || 'Erro desconhecido');
                        }
                        
                        status.removeClass('alert-info alert-success').addClass('alert-danger')
                            .html(`<i class="fas fa-exclamation-circle me-2"></i>${errorMsg}`);
                            
                        // Log do erro para debug
                        console.error('Erro detalhado:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error
                        });
                    },
                    complete: function() {
                        // Reativa o botão
                        button.prop('disabled', false).html('<i class="fas fa-download me-2"></i>Criar Backup');
                    }
                });
            });
        });
    </script>
</body>
</html>
