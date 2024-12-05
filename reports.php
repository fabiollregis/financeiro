<?php
require_once __DIR__ . '/src/controllers/ReportController.php';
require_once __DIR__ . '/src/controllers/AuthController.php';

$auth = new AuthController();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$controller = new ReportController();

// Obter ano e mês atual
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$currentMonth = isset($_GET['month']) ? ($_GET['month'] === 'all' ? null : (int)$_GET['month']) : (int)date('m');

// Obter anos disponíveis
$availableYears = $controller->getAvailableYears();
if (empty($availableYears)) {
    $availableYears = [$currentYear];
}

// Obter totais
$totals = $controller->getTotals($currentYear, $currentMonth);
$monthlyData = $controller->getMonthlyData($currentYear);
$dailyData = $currentMonth ? $controller->getDailyData($currentYear, $currentMonth) : [];
$categoryTotals = $controller->getCategoryTotals($currentYear, $currentMonth);

// Preparar dados para os gráficos
$monthLabels = [];
$monthReceitas = [];
$monthDespesas = [];

foreach ($monthlyData as $data) {
    $monthLabels[] = date('M/Y', strtotime($data['month'] . '-01'));
    $monthReceitas[] = round($data['total_receitas'], 2);
    $monthDespesas[] = round($data['total_despesas'], 2);
}

$dayLabels = [];
$dayReceitas = [];
$dayDespesas = [];

if ($currentMonth) {
    foreach ($dailyData as $data) {
        $dayLabels[] = date('d/m', strtotime($data['day']));
        $dayReceitas[] = round($data['total_receitas'], 2);
        $dayDespesas[] = round($data['total_despesas'], 2);
    }
}

$months = [
    'all' => 'Todos os Meses',
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
    4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
    7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
    10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

$periodo = $currentMonth ? $months[$currentMonth] . '/' . $currentYear : 'Ano de ' . $currentYear;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Controle Financeiro</title>
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

    <div class="container my-4">
        <!-- Painéis de Totais -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <h6 class="card-title">Total Receitas</h6>
                        <h3 class="mb-0">R$ <?php echo number_format($totals['total_receitas'] ?? 0, 2, ',', '.'); ?></h3>
                        <small><?php echo $totals['dias_receitas'] ?? 0; ?> dias com receitas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">
                        <h6 class="card-title">Total Despesas</h6>
                        <h3 class="mb-0">R$ <?php echo number_format($totals['total_despesas'] ?? 0, 2, ',', '.'); ?></h3>
                        <small><?php echo $totals['dias_despesas'] ?? 0; ?> dias com despesas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card <?php echo ($totals['total_receitas'] - $totals['total_despesas'] >= 0) ? 'bg-primary' : 'bg-warning text-dark'; ?> text-white h-100">
                    <div class="card-body">
                        <h6 class="card-title">Saldo</h6>
                        <h3 class="mb-0">R$ <?php echo number_format(($totals['total_receitas'] ?? 0) - ($totals['total_despesas'] ?? 0), 2, ',', '.'); ?></h3>
                        <small>Período: <?php echo $periodo; ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <h6 class="card-title">Média Diária Despesas</h6>
                        <h3 class="mb-0">R$ <?php 
                            $diasDespesas = $totals['dias_despesas'] ?? 0;
                            echo number_format($diasDespesas > 0 ? ($totals['total_despesas'] ?? 0) / $diasDespesas : 0, 2, ',', '.');
                        ?></h3>
                        <small>Baseado nos dias com despesas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="get" class="row g-3">
                            <div class="col-md-4">
                                <label for="year" class="form-label">Ano</label>
                                <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                                    <?php foreach ($availableYears as $year): ?>
                                        <option value="<?php echo $year; ?>" <?php echo $year == $currentYear ? 'selected' : ''; ?>>
                                            <?php echo $year; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="month" class="form-label">Mês</label>
                                <select name="month" id="month" class="form-select" onchange="this.form.submit()">
                                    <?php foreach ($months as $num => $name): ?>
                                        <option value="<?php echo $num; ?>" <?php echo $num === ($currentMonth ?? 'all') ? 'selected' : ''; ?>>
                                            <?php echo $name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Gráficos e Categorias -->
            <div class="col-md-8">
                <!-- Gráfico Anual -->
                <div class="card mb-4">
                    <div class="card-header py-2">
                        <h6 class="mb-0">Movimentações por Mês - <?php echo $currentYear; ?></h6>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyChart" height="200"></canvas>
                    </div>
                </div>
                
                <?php if ($currentMonth): ?>
                <!-- Gráfico Mensal -->
                <div class="card mb-4">
                    <div class="card-header py-2">
                        <h6 class="mb-0">Movimentações Diárias - <?php echo $months[$currentMonth] . '/' . $currentYear; ?></h6>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyChart" height="200"></canvas>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Totais por Categoria -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header py-2">
                        <h6 class="mb-0">Totais por Categoria - <?php echo $periodo; ?></h6>
                    </div>
                    <div class="card-body">
                        <h6 class="text-success mb-2">Receitas</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Categoria</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categoryTotals as $category): ?>
                                    <?php if ($category['type'] === 'receita'): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                        <td class="text-end">R$ <?php echo number_format($category['total'], 2, ',', '.'); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <h6 class="text-danger mb-2 mt-4">Despesas</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Categoria</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categoryTotals as $category): ?>
                                    <?php if ($category['type'] === 'despesa'): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                        <td class="text-end">R$ <?php echo number_format($category['total'], 2, ',', '.'); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Configuração dos gráficos
        const monthlyData = {
            labels: <?php echo json_encode($monthLabels); ?>,
            receitas: <?php echo json_encode($monthReceitas); ?>,
            despesas: <?php echo json_encode($monthDespesas); ?>
        };

        const dailyData = {
            labels: <?php echo json_encode($dayLabels); ?>,
            receitas: <?php echo json_encode($dayReceitas); ?>,
            despesas: <?php echo json_encode($dayDespesas); ?>
        };

        // Gráfico Anual
        new Chart(document.getElementById('monthlyChart'), {
            type: 'bar',
            data: {
                labels: monthlyData.labels,
                datasets: [{
                    label: 'Receitas',
                    data: monthlyData.receitas,
                    backgroundColor: 'rgba(25, 135, 84, 0.5)',
                    borderColor: '#198754',
                    borderWidth: 1
                }, {
                    label: 'Despesas',
                    data: monthlyData.despesas,
                    backgroundColor: 'rgba(220, 53, 69, 0.5)',
                    borderColor: '#dc3545',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        <?php if ($currentMonth): ?>
        // Gráfico Mensal
        new Chart(document.getElementById('dailyChart'), {
            type: 'bar',
            data: {
                labels: dailyData.labels,
                datasets: [{
                    label: 'Receitas',
                    data: dailyData.receitas,
                    backgroundColor: 'rgba(25, 135, 84, 0.5)',
                    borderColor: '#198754',
                    borderWidth: 1
                }, {
                    label: 'Despesas',
                    data: dailyData.despesas,
                    backgroundColor: 'rgba(220, 53, 69, 0.5)',
                    borderColor: '#dc3545',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        <?php endif; ?>

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
    </script>
</body>
</html>
