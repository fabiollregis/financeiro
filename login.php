<?php
session_start();

// Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3c72;
            --secondary-color: #2a5298;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-logo i {
            font-size: 3rem;
            color: var(--primary-color);
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .form-control {
            border-radius: 50px;
            padding: 12px 20px;
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(30,60,114,0.25);
            border-color: var(--primary-color);
        }
        
        .alert {
            border-radius: 10px;
        }
        
        .back-to-home {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        .back-to-home:hover {
            opacity: 1;
            color: white;
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-to-home">
        <i class="fas fa-arrow-left"></i>
        Voltar para a página inicial
    </a>

    <div class="login-container">
        <div class="login-logo">
            <i class="fas fa-wallet"></i>
            <h2 class="mt-3">Login</h2>
            <p class="text-muted">Acesse sua conta para gerenciar suas finanças</p>
        </div>

        <form id="loginForm" action="src/controllers/AuthController.php" method="POST">
            <div class="mb-4">
                <label class="form-label">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-envelope text-muted"></i>
                    </span>
                    <input type="email" class="form-control border-start-0" name="email" required placeholder="seu@email.com">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Senha</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-lock text-muted"></i>
                    </span>
                    <input type="password" class="form-control border-start-0" name="password" required placeholder="Sua senha">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="fas fa-sign-in-alt me-2"></i>Entrar
            </button>

            <div class="text-center">
                <p class="mb-0">Não tem uma conta? <a href="register.php">Criar conta</a></p>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            window.location.href = 'dashboard.php';
                        } else {
                            alert(response.message || 'Erro ao fazer login. Tente novamente.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro:', xhr.responseText);
                        alert('Erro ao fazer login. Por favor, tente novamente.');
                    }
                });
            });
        });
    </script>
</body>
</html>
