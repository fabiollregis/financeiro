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
    <title>Sistema de Controle Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3c72;
            --secondary-color: #2a5298;
            --accent-color: #4CAF50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('assets/img/pattern.png');
            opacity: 0.1;
        }
        
        .feature-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        
        .cta-section {
            background-color: #f8f9fa;
            padding: 80px 0;
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
        
        .navbar {
            background: var(--primary-color);
            padding: 15px 0;
        }
        
        .footer {
            background: var(--primary-color);
            color: white;
            padding: 40px 0;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .auth-buttons .btn {
            margin: 0 10px;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-wallet me-2"></i>
                FinanceControl
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Recursos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#benefits">Benefícios</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light ms-3" href="register.php">Criar Conta</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-2" href="login.php">Entrar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Controle suas finanças com simplicidade e eficiência</h1>
                    <p class="lead mb-4">Organize suas receitas e despesas, acompanhe seus gastos e tome decisões financeiras mais inteligentes com nossa plataforma completa.</p>
                    <div class="auth-buttons">
                        <a href="register.php" class="btn btn-light btn-lg me-3">
                            <i class="fas fa-user-plus me-2"></i>Começar Agora
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-info-circle me-2"></i>Saiba Mais
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="assets/img/finance-hero.svg" alt="Finanças" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" id="features">
        <div class="container">
            <h2 class="text-center mb-5">Recursos Principais</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card card h-100 p-4">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line feature-icon"></i>
                            <h4>Dashboard Intuitivo</h4>
                            <p>Visualize suas finanças em um painel completo e fácil de entender.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card card h-100 p-4">
                        <div class="card-body text-center">
                            <i class="fas fa-tags feature-icon"></i>
                            <h4>Categorização</h4>
                            <p>Organize suas transações em categorias personalizadas.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card card h-100 p-4">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt feature-icon"></i>
                            <h4>Relatórios Detalhados</h4>
                            <p>Gere relatórios completos para análise financeira.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="bg-light py-5" id="benefits">
        <div class="container">
            <h2 class="text-center mb-5">Por que escolher nossa plataforma?</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="stats-card">
                        <i class="fas fa-lock mb-3 feature-icon"></i>
                        <div class="stats-number">100%</div>
                        <p>Seguro e Confiável</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <i class="fas fa-users mb-3 feature-icon"></i>
                        <div class="stats-number">10k+</div>
                        <p>Usuários Ativos</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <i class="fas fa-star mb-3 feature-icon"></i>
                        <div class="stats-number">4.8</div>
                        <p>Avaliação Média</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <h2 class="mb-4">Pronto para começar?</h2>
            <p class="lead mb-4">Junte-se a milhares de usuários e comece a controlar suas finanças hoje mesmo!</p>
            <a href="register.php" class="btn btn-primary btn-lg">
                <i class="fas fa-rocket me-2"></i>Criar Minha Conta
            </a>
            <p class="mt-3">
                Já tem uma conta? <a href="login.php">Fazer Login</a>
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Sobre o FinanceControl</h5>
                    <p>Sistema completo para gerenciamento de finanças pessoais, desenvolvido para ajudar você a ter mais controle sobre seu dinheiro.</p>
                </div>
                <div class="col-md-3">
                    <h5>Links Úteis</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Termos de Uso</a></li>
                        <li><a href="#" class="text-white">Política de Privacidade</a></li>
                        <li><a href="#" class="text-white">Suporte</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contato</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i> contato@financecontrol.com</li>
                        <li><i class="fas fa-phone me-2"></i> (11) 1234-5678</li>
                    </ul>
                </div>
            </div>
            <hr class="mt-4 mb-4 bg-light">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 FinanceControl. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm" action="src/controllers/AuthController.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Entrar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Smooth scroll para links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                var target = $(this.hash);
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 800);
                }
            });

            // Login form submission
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
