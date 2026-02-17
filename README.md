#  BNG_APP_2 (Basic Name Gathering)
Sistema de gestão desenvolvido em **PHP** com arquitetura **MVC**. O projeto foca no gerenciamento de clientes, estatísticas de dados e administração de usuários, oferecendo uma interface limpa e intuitiva.
Ideal para práticas de desenvolvimento web, controle de acesso e manipulação de dados relacionais.
![Dashboard do Sistema BNG](https://github.com/Jaosuzart/BNG_APP_2/blob/main/public/assets/img/screenshot.png?raw=true) 
## 🚀 Funcionalidades
### 👥 Gestão de Clientes
- [x] Cadastro de novos clientes
- [x] Listagem detalhada
- [x] Edição e exclusão de registros
### 📊 Estatísticas
- [x] Dashboard com visão geral
- [x] Gráficos e indicadores de desempenho (KPIs)
### 🔐 Administração
- [x] Gestão de utilizadores (Usuários do sistema)
- [x] Controle de permissões e acesso
- [x] Menu administrativo protegido
### 🎨 Interface
- [x] Design responsivo e moderno
- [x] Navegação simplificada por cards
- [x] Identidade visual personalizada (Tema Roxo/Azul)
## 🛠️ Tecnologias Utilizadas
*   **Backend:** PHP 8+ (Orientação a Objetos)
*   **Arquitetura:** MVC (Model-View-Controller)
*   **Banco de Dados:** MySQL / MariaDB
*   **Frontend:** HTML5, CSS3, JavaScript
*   **Framework CSS:** Bootstrap (Provável, baseado no layout)
*   **Ícones:** FontAwesome ou similar
## 🗂️ Estrutura do Projeto
BNG_APP_2/
│── Config/
│   ├── Database.php        # Conexão com o BD
│   └── Config.php          # Configurações gerais
│── Controllers/            # Lógica das requisições
│   ├── ClienteController.php
│   ├── EstatisticaController.php
│   └── UsuarioController.php
│── Models/                 # Regras de negócio e Dados
│   ├── Cliente.php
│   ├── Estatistica.php
│   └── Usuario.php
│── Views/                  # Templates HTML/PHP
│   ├── layout/             # Cabeçalho e Rodapé
│   ├── clientes/
│   └── dashboard/
│── Public/                 # Arquivos públicos (CSS, JS, Imagens)
│── uploads/                # Arquivos enviados
└── index.php               # Ponto de entrada (Router)

⚙️ Como Executar o Projeto
1️⃣ Clonar o repositório
bash
12
git clone https://github.com/Jaosuzart/BNG_APP_2.gitcd BNG_APP_2
2️⃣ Configurar o Servidor Local
Mova a pasta para o diretório do seu servidor (XAMPP, WAMP, Laragon):
Exemplo: C:\xampp\htdocs\BNG_APP_2
3️⃣ Configurar o Banco de Dados
Crie um banco de dados chamado bng_app (ou conforme definido no seu código).
Importe o arquivo .sql (se houver na pasta database ou sql).
Ajuste as credenciais no arquivo Config/Database.php:
php
define('DB_HOST', 'localhost');define('DB_USER', 'root');define('DB_PASS', '');define('DB_NAME', 'bng_app');
4️⃣ Acessar o Sistema
No navegador, acesse:
http://localhost/BNG_APP_2/public/
(Nota: A rota pode variar dependendo da configuração do seu .htaccess ou router)
🧠 Objetivo do Projeto
Este projeto foi desenvolvido para consolidar habilidades em:
Estruturação de aplicações PHP escaláveis usando MVC.
Criação de Dashboards administrativos.
Segurança e autenticação de usuários.
Manipulação de sessões e cookies.
👨‍💻 Autor
Desenvolvido por João Marcelo Suzart Lima Castro 💻
GitHub: https://github.com/Jaosuzart
LinkedIn: João Marcelo Suzart
⭐ Se este projeto te ajudou, deixe uma estrela no repositório! ⭐
