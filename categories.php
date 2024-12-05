<?php
require_once __DIR__ . '/src/controllers/CategoryController.php';
require_once __DIR__ . '/src/controllers/AuthController.php';

$auth = new AuthController();

// Verificar se está logado
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$categoryController = new CategoryController();
$categories = $categoryController->index();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Categorias - Controle Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container">
            <span class="navbar-brand">Controle Financeiro</span>
            <div class="navbar-nav flex-row">
                <span class="nav-link me-3">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($auth->getCurrentUserName()); ?>
                </span>
                <a href="index.php" class="btn btn-outline-light me-2">
                    <i class="fas fa-home"></i> Início
                </a>
                <button id="logoutBtn" class="btn btn-outline-light">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </button>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <!-- Formulário de Nova Categoria -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Nova Categoria</div>
                    <div class="card-body">
                        <form id="categoryForm">
                            <div class="mb-3">
                                <label class="form-label">Nome</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo</label>
                                <select class="form-select" name="type" required>
                                    <option value="receita">Receita</option>
                                    <option value="despesa">Despesa</option>
                                    <option value="ambos">Ambos</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Categorias -->
        <div class="card">
            <div class="card-header">Categorias</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td>
                                    <span class="badge <?php 
                                        echo $category['type'] == 'receita' ? 'bg-success' : 
                                            ($category['type'] == 'despesa' ? 'bg-danger' : 'bg-primary'); 
                                    ?>">
                                        <?php echo ucfirst($category['type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-category" 
                                            data-id="<?php echo $category['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                            data-type="<?php echo $category['type']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-category" 
                                            data-id="<?php echo $category['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Formulário de Nova Categoria
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: 'src/controllers/category_api.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            alert('Categoria salva com sucesso!');
                            location.reload();
                        } else {
                            alert('Erro ao salvar categoria: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Erro ao processar requisição');
                    }
                });
            });

            // Editar Categoria
            $('.edit-category').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const type = $(this).data('type');
                
                $('[name="name"]').val(name);
                $('[name="type"]').val(type);
                
                $('#categoryForm').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    
                    $.ajax({
                        url: 'src/controllers/category_api.php?id=' + id,
                        method: 'PUT',
                        data: $(this).serialize(),
                        success: function(response) {
                            if (response.success) {
                                alert('Categoria atualizada com sucesso!');
                                location.reload();
                            } else {
                                alert('Erro ao atualizar categoria: ' + response.message);
                            }
                        },
                        error: function() {
                            alert('Erro ao processar requisição');
                        }
                    });
                });
            });

            // Excluir Categoria
            $('.delete-category').on('click', function() {
                if (confirm('Tem certeza que deseja excluir esta categoria?')) {
                    const id = $(this).data('id');
                    
                    $.ajax({
                        url: 'src/controllers/category_api.php?id=' + id,
                        method: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                alert('Categoria excluída com sucesso!');
                                location.reload();
                            } else {
                                alert('Erro ao excluir categoria: ' + response.message);
                            }
                        },
                        error: function() {
                            alert('Erro ao processar requisição');
                        }
                    });
                }
            });

            // Logout
            $('#logoutBtn').on('click', function() {
                if (confirm('Tem certeza que deseja sair?')) {
                    $.get('src/controllers/auth_api.php?action=logout', function(response) {
                        if (response.success) {
                            window.location.href = 'login.php';
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
