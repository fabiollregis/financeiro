# Sistema de Controle Financeiro

Um sistema web completo para gerenciamento de finanças pessoais, desenvolvido em PHP com MySQL.

## 📋 Funcionalidades

- **Autenticação Completa**
  - Registro de usuários
  - Login/Logout
  - Proteção de rotas

- **Gestão de Transações**
  - Cadastro de receitas e despesas
  - Categorização de transações
  - Edição e exclusão de registros
  - Filtros por data e tipo

- **Categorias**
  - Categorias padrão pré-cadastradas
  - Possibilidade de criar novas categorias
  - Separação entre receitas e despesas

- **Dashboard**
  - Resumo financeiro
  - Saldo atual
  - Total de receitas e despesas
  - Listagem das últimas transações

## 🚀 Tecnologias Utilizadas

- PHP 7.4+
- MySQL 5.7+
- HTML5
- CSS3
- JavaScript/jQuery
- Bootstrap 5
- Font Awesome

## ⚙️ Requisitos

- Servidor Web (Apache/Nginx)
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Composer (opcional)

## 📦 Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/financeiro.git
```

2. Importe o banco de dados:
```bash
mysql -u seu_usuario -p sua_senha < migrate_database_v5.sql
```

3. Configure o banco de dados:
   - Abra o arquivo `config/database.php`
   - Atualize as credenciais do banco de dados

4. Configure o servidor web:
   - Aponte o document root para a pasta do projeto
   - Certifique-se que o mod_rewrite está habilitado (se estiver usando Apache)

## 🔧 Configuração

1. Configurações do Banco de Dados (`config/database.php`):
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'financeiro');
```

## 🏃‍♂️ Iniciando

1. Acesse o sistema através do navegador
2. Crie uma nova conta ou faça login
3. Comece a registrar suas transações!

## 🔒 Segurança

- Senhas criptografadas com bcrypt
- Proteção contra SQL Injection
- Validação de dados
- Sanitização de inputs
- Proteção contra XSS
- Sessões seguras

## 📝 Recursos Adicionais

- Interface responsiva
- Feedback visual das ações
- Confirmações antes de ações importantes
- Mensagens de erro amigáveis
- Filtro dinâmico de categorias

## 🤝 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👤 Autor

Seu Nome
- GitHub: [@seu-usuario](https://github.com/seu-usuario)
- LinkedIn: [@seu-linkedin](https://linkedin.com/in/seu-linkedin)

## 🙏 Agradecimentos

- Bootstrap Team
- Font Awesome
- jQuery Team
- Todos os contribuidores

---
⌨️ com ❤️ por [Seu Nome](https://github.com/seu-usuario)
