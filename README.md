BNG_APP_2 👤✨
Sistema de cadastro e gestão de usuários desenvolvido em PHP, com estilização via CSS (app.css).
📌 Visão geral
Ele é um projeto web para registrar usuários e gerenciar dados de forma organizada, com separação de pastas típica de aplicação (camada app/ e ponto de entrada em public/).
🧩 Tecnologias
- PHP (back-end)
- CSS (app.css) (estilo da aplicação)
- Composer (dependências PHP)
📁 Estrutura do projeto
Uma visão rápida das principais pastas/arquivos:
- public/  
  Ponto de entrada (onde você abre o projeto no navegador)
- app/  
  Código principal da aplicação (controllers, models, views, etc.)
- vendor/  
  Dependências instaladas pelo Composer
- uploads/  
  Arquivos enviados (se o sistema permitir upload)
- logs/  
  Registros/relatórios da aplicação
- composer.json / composer.lock  
  Dependências PHP do projeto
- .gitignore  
  Arquivos ignorados no Git
✅ Funcionalidades (principais)
- Cadastro de usuários
- Listagem/consulta de usuários
- Atualização e remoção
- Interface usando app.css
🚀 Como rodar o projeto (local)
Pré-requisitos
- PHP instalado (recomendado PHP 8+)
- Composer instalado
- Servidor local (Laragon, XAMPP, WAMP, ou php -S)
- Se usar banco: MySQL/MariaDB
Passo a passo
1) Clone o repositório
bash
git clone https://github.com/Jaosuzart/BNG_APP_2.git

cd BNG_APP_2

