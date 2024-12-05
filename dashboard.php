<?php
session_start();

// Se não estiver logado, redireciona para o login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/src/controllers/TransactionController.php';
require_once __DIR__ . '/src/controllers/CategoryController.php';
require_once __DIR__ . '/src/controllers/AuthController.php';

$auth = new AuthController();
$transactionController = new TransactionController();
$categoryController = new CategoryController();

$data = $transactionController->index();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle Financeiro - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-sm i {
            font-size: 0.875rem;
        }
        
        .gap-2 {
            gap: 0.75rem !important;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categorias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">Relatórios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Perfil</a>
                    </li>
                    <li class="nav-item">
                        <button id="logoutBtn" class="btn btn-outline-light ms-2">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Resumo Financeiro -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Receitas</h5>
                        <p class="card-text h3">R$ <?php echo number_format($data['totals']['receitas'] ?? 0, 2, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Despesas</h5>
                        <p class="card-text h3">R$ <?php echo number_format($data['totals']['despesas'] ?? 0, 2, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <?php
                $saldo = ($data['totals']['receitas'] ?? 0) - ($data['totals']['despesas'] ?? 0);
                ?>
                <div class="card <?php echo $saldo >= 0 ? 'bg-primary' : 'bg-danger'; ?> text-white">
                    <div class="card-body">
                        <h5 class="card-title">Saldo</h5>
                        <p class="card-text h3">R$ <?php echo number_format($saldo, 2, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botão Adicionar Transação -->
        <div class="mb-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transactionModal">
                <i class="fas fa-plus"></i> Nova Transação
            </button>
        </div>

        <!-- Lista de Transações -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Últimas Transações</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Categoria</th>
                                <th>Valor</th>
                                <th>Tipo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['transactions'] as $transaction): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($transaction['date'])); ?></td>
                                <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['category_name']); ?></td>
                                <td class="text-end">R$ <?php echo number_format($transaction['amount'], 2, ',', '.'); ?></td>
                                <td>
                                    <span class="badge <?php echo $transaction['type'] === 'receita' ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo ucfirst($transaction['type']); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button class="btn btn-outline-primary btn-sm edit-transaction" data-id="<?php echo $transaction['id']; ?>" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm delete-transaction" data-id="<?php echo $transaction['id']; ?>" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nova Transação -->
    <div class="modal fade" id="transactionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Transação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="transactionForm">
                        <input type="hidden" name="id" id="transactionId">
                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Valor</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control" id="amount" name="amount" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-control" name="type" id="transactionType" required>
                                <option value="receita">Receita</option>
                                <option value="despesa">Despesa</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Categoria</label>
                            <select class="form-control" name="category_id" id="categorySelect" required>
                                <option value="">Selecione uma categoria</option>
                                <?php foreach ($data['categories'] as $category): ?>
                                <option value="<?php echo $category['id']; ?>" data-type="<?php echo $category['type']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveTransaction">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {
            // Filtrar categorias baseado no tipo selecionado
            function filterCategories() {
                const selectedType = $('#transactionType').val();
                const categorySelect = $('#categorySelect');
                
                // Esconde todas as opções exceto a primeira (placeholder)
                categorySelect.find('option:not(:first)').hide();
                
                // Mostra apenas as categorias do tipo selecionado
                categorySelect.find('option[data-type="' + selectedType + '"]').show();
                
                // Reset a seleção para o placeholder
                categorySelect.val('');
            }

            // Filtrar categorias quando o tipo é alterado
            $('#transactionType').on('change', filterCategories);

            // Filtrar categorias quando o modal é aberto
            $('#transactionModal').on('shown.bs.modal', filterCategories);

            // Logout
            $('#logoutBtn').on('click', function() {
                if (confirm('Tem certeza que deseja sair?')) {
                    window.location.href = 'src/controllers/auth_api.php?action=logout';
                }
            });

            // Máscara para o campo de valor
            $('#amount').mask('#.##0,00', {
                reverse: true,
                placeholder: '0,00'
            });
            
            // Ao abrir o modal para nova transação
            $('#transactionModal').on('show.bs.modal', function() {
                $('#transactionForm')[0].reset();
                $('#transactionId').val('');
                $('#amount').val('0,00');
            });
            
            // Ao editar uma transação
            $('.edit-transaction').on('click', function() {
                const id = $(this).data('id');
                $.get('src/controllers/transaction_api.php', { action: 'get', id: id }, function(response) {
                    if (response.success) {
                        const transaction = response.data;
                        $('#transactionId').val(transaction.id);
                        $('#description').val(transaction.description);
                        
                        // Formata o valor para exibição
                        const amount = parseFloat(transaction.amount);
                        $('#amount').val(amount.toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).replace('.', ','));
                        
                        $('#date').val(transaction.date);
                        $('#category_id').val(transaction.category_id);
                        $('#type').val(transaction.type);
                        $('#transactionModal').modal('show');
                    }
                });
            });
            
            // Ao salvar a transação
            $('#saveTransaction').on('click', function() {
                const form = $('#transactionForm');
                const formData = new FormData();
                
                // Adiciona os campos ao FormData
                formData.append('description', form.find('[name="description"]').val());
                formData.append('date', form.find('[name="date"]').val());
                formData.append('category_id', form.find('[name="category_id"]').val());
                formData.append('type', form.find('[name="type"]').val());
                
                // Trata o valor antes de enviar
                let amount = form.find('[name="amount"]').val();
                formData.append('amount', amount);
                
                // Adiciona o ID se estiver editando
                const transactionId = form.find('#transactionId').val();
                if (transactionId) {
                    formData.append('id', transactionId);
                    formData.append('action', 'update');
                } else {
                    formData.append('action', 'create');
                }
                
                $.ajax({
                    url: 'src/controllers/transaction_api.php',
                    method: 'POST',
                    data: Object.fromEntries(formData),
                    success: function(response) {
                        if (response.success) {
                            $('#transactionModal').modal('hide');
                            window.location.reload();
                        } else {
                            alert(response.message || 'Erro ao salvar transação');
                        }
                    },
                    error: function() {
                        alert('Erro ao salvar transação');
                    }
                });
            });

            // Delete Transaction
            $('.delete-transaction').on('click', function() {
                const id = $(this).data('id');
                
                if (confirm('Tem certeza que deseja excluir esta transação?')) {
                    $.get('src/controllers/transaction_api.php?action=delete&id=' + id, function(response) {
                        if (response.success) {
                            window.location.reload();
                        } else {
                            alert('Erro ao excluir transação: ' + response.message);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
