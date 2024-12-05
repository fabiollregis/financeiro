<?php
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/UserController.php';

$auth = new AuthController();
$userController = new UserController();

// Verificar se está logado
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = $auth->getCurrentUser();
$message = '';
$error = '';

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    try {
        if (!empty($newPassword)) {
            // Validar senha atual
            if (!$auth->verifyPassword($currentPassword)) {
                throw new Exception('Senha atual incorreta');
            }
            
            // Validar nova senha
            if ($newPassword !== $confirmPassword) {
                throw new Exception('Nova senha e confirmação não correspondem');
            }
            
            if (strlen($newPassword) < 6) {
                throw new Exception('Nova senha deve ter pelo menos 6 caracteres');
            }
        }

        // Atualizar perfil
        $userController->updateProfile($user['id'], [
            'name' => $name,
            'email' => $email,
            'password' => !empty($newPassword) ? password_hash($newPassword, PASSWORD_DEFAULT) : null
        ]);

        $message = 'Perfil atualizado com sucesso!';
        $user = $auth->getCurrentUser(); // Recarregar dados do usuário
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Perfil - Controle Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container">
            <a href="index.php" class="navbar-brand">Controle Financeiro</a>
            <div class="navbar-nav flex-row">
                <span class="nav-link me-3">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($auth->getCurrentUserName()); ?>
                </span>
                <a href="index.php" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Gerenciar Perfil</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>

                            <hr class="my-4">
                            <h5>Alterar Senha</h5>
                            <p class="text-muted small">Preencha apenas se desejar alterar sua senha</p>

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Senha Atual</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">Nova Senha</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" 
                                       minlength="6">
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validação do formulário
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Validação das senhas
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (newPassword !== confirmPassword) {
                this.setCustomValidity('As senhas não correspondem');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
