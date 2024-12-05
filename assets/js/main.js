$(document).ready(function() {
    // Formulário de Nova Transação
    $('#transactionForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'src/controllers/api.php',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    alert('Transação salva com sucesso!');
                    location.reload();
                } else {
                    alert('Erro ao salvar transação: ' + response.message);
                }
            },
            error: function() {
                alert('Erro ao processar requisição');
            }
        });
    });

    // Formulário de Filtros
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        
        const queryString = $(this).serialize();
        window.location.href = '?' + queryString;
    });

    // Excluir Transação
    $('.delete-transaction').on('click', function() {
        if (confirm('Tem certeza que deseja excluir esta transação?')) {
            const id = $(this).data('id');
            
            $.ajax({
                url: 'src/controllers/api.php?action=delete&id=' + id,
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        alert('Transação excluída com sucesso!');
                        location.reload();
                    } else {
                        alert('Erro ao excluir transação: ' + response.message);
                    }
                },
                error: function() {
                    alert('Erro ao processar requisição');
                }
            });
        }
    });

    // Editar Transação
    $('.edit-transaction').on('click', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        
        // Preenche o formulário com os dados da transação
        $('[name="description"]').val(row.find('td:eq(1)').text());
        $('[name="amount"]').val(parseFloat(row.find('td:eq(4)').text().replace('R$ ', '').replace('.', '').replace(',', '.')));
        $('[name="type"]').val(row.find('td:eq(3)').text().trim().toLowerCase());
        $('[name="category"]').val(row.find('td:eq(2)').text().trim().toLowerCase());
        $('[name="date"]').val(row.find('td:eq(0)').text().split('/').reverse().join('-'));
        
        // Altera o comportamento do formulário para atualização
        $('#transactionForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: 'src/controllers/api.php?action=update&id=' + id,
                method: 'PUT',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        alert('Transação atualizada com sucesso!');
                        location.reload();
                    } else {
                        alert('Erro ao atualizar transação: ' + response.message);
                    }
                },
                error: function() {
                    alert('Erro ao processar requisição');
                }
            });
        });
        
        // Scroll até o formulário
        $('html, body').animate({
            scrollTop: $('#transactionForm').offset().top - 100
        }, 500);
    });
});
