# AgendaFácil

Sistema web de agendamento para barbearia/salão, com CRUD completo e banco de dados MySQL.
Projeto acadêmico individual.

- **Autor:** [Seu nome] (projeto individual)
- **Tutor:** [Nome do tutor]

## Stack

- PHP 8 (orientado a objetos, organização MVC simples)
- MySQL 8 / MariaDB, acessado com PDO e prepared statements
- Bootstrap 5 (via CDN)
- Ambiente local: XAMPP

## Funcionalidades

| Caso de uso | Arquivo | Operações |
| --- | --- | --- |
| UC01 Autenticar no sistema | `login.php`, `logout.php` | login e logout |
| UC02 Gerenciar clientes | `clientes.php` | criar, listar, editar, excluir |
| UC03 Gerenciar serviços | `servicos.php` | criar, listar, editar, excluir |
| UC04 Gerenciar profissionais | `profissionais.php` | criar, listar, editar, excluir |
| UC05 Gerenciar agendamentos | `agendamentos.php`, `index.php` | agendar, listar com filtros, remarcar, concluir, cancelar, excluir |

Regras de negócio: não permite dois agendamentos sobrepostos para o mesmo profissional (RF09) e não permite excluir cliente, serviço ou profissional que tenha agendamentos (RF10).

## Como rodar (Windows + XAMPP + VS Code)

1. Instale o [XAMPP](https://www.apachefriends.org) (PHP 8.x).
2. Copie esta pasta para `C:\xampp\htdocs\agendafacil`.
3. No XAMPP Control Panel, clique em **Start** em **Apache** e **MySQL**.
4. Abra `http://localhost/phpmyadmin`, vá na aba **Importar**, escolha `database/schema.sql` e clique em **Importar**. Isso cria o banco `agendafacil`, as tabelas e dados de exemplo.
5. Acesse `http://localhost/agendafacil`.
6. Entre com **admin@agendafacil.com** e senha **admin123**.

Se o seu MySQL tiver senha ou porta diferente do padrão do XAMPP (usuário `root`, senha vazia, porta 3306), ajuste `config/config.php`.

Sem Apache, também dá para rodar com o servidor embutido do PHP (com o MySQL ligado):

```
php -S localhost:8000
```

e acessar `http://localhost:8000`.

## Estrutura de pastas

```
agendafacil/
├── index.php              # agenda do dia (RF11)
├── login.php / logout.php # UC01
├── clientes.php           # controller UC02
├── servicos.php           # controller UC03
├── profissionais.php      # controller UC04
├── agendamentos.php       # controller UC05
├── app/
│   ├── bootstrap.php      # carrega tudo e define renderizar()
│   ├── Database.php       # conexão PDO
│   ├── helpers.php        # sessão, login, CSRF, formatação
│   └── models/            # acesso ao banco (um Model por entidade)
├── views/                 # telas (HTML + PHP) com layout Bootstrap
├── config/config.php      # dados de conexão com o banco
└── database/schema.sql    # criação do banco, tabelas e dados iniciais
```

Fluxo de uma requisição: o **controller** (arquivo na raiz) recebe a requisição, valida os dados e chama o **model** (`app/models`), que fala com o MySQL via PDO; depois o controller escolhe a **view** (`views/`) para mostrar o resultado.

## Segurança

- Senhas guardadas com `password_hash` (bcrypt) e conferidas com `password_verify`.
- Todas as consultas usam prepared statements (PDO).
- Saídas escapadas com `htmlspecialchars` (função `e()`).
- Token CSRF em todos os formulários POST.
- Exclusões e mudanças de status só por POST.

## Publicando no GitHub

```
git init
git add .
git commit -m "AgendaFácil: versão inicial"
git branch -M main
git remote add origin https://github.com/SEU-USUARIO/agendafacil.git
git push -u origin main
```

Depois, no GitHub: **Settings > Collaborators > Add people** e adicione o tutor e `fernandonakagawa-unifil`. Se o repositório for privado, sem isso eles não conseguem abrir.

Faça commits pequenos e frequentes (um por funcionalidade). Isso mostra o andamento do projeto.
