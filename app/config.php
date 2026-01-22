<?php

$envPath = __DIR__ . '/../.env';
$env = [];

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            
            $valueParts = explode('#', $value);
            $value = $valueParts[0]; 
            $env[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
        }
    }
} else {
    // Se não achar o arquivo, para tudo para evitar vazamento de dados
    die('ERRO CRÍTICO: O arquivo .env não foi encontrado na raiz do projeto.');
}

// =======================================================
// 2. DEFINIÇÃO DAS CONSTANTES (Com valores padrão de segurança)
// =======================================================

define('APP_NAME', 'Basic Name Gathering');

// Configurações de Banco de Dados
// Se não encontrar no .env, usa 'root' e senha vazia (padrão Wamp/XAMPP)
define('MYSQL_HOST',      $env['MYSQL_HOST'] ?? 'localhost');
define('MYSQL_DATABASE',  $env['MYSQL_DATABASE'] ?? 'db_bng');
define('MYSQL_USERNAME',  $env['MYSQL_USERNAME'] ?? 'root');
define('MYSQL_PASSWORD',  $env['MYSQL_PASSWORD'] ?? '');

// Chaves de Segurança
define('MYSQL_AES_KEY',   $env['MYSQL_AES_KEY'] ?? '');
define('OPENSSL_KEY',     $env['OPENSSL_KEY'] ?? '');
define('OPENSSL_IV',      $env['OPENSSL_IV'] ?? '');

// Logs
define('LOGS_PATH',       __DIR__ . '/../logs/app.log');

// =======================================================
// 3. CONFIGURAÇÕES DE E-MAIL
// =======================================================
define('EMAIL_HOST', $env['EMAIL_HOST'] ?? '');
define('EMAIL_PORT', (int)($env['EMAIL_PORT'] ?? 587));

define('EMAIL_USER', $env['EMAIL_USERNAME'] ?? '');      // <-- pega do .env
define('EMAIL_PASS', $env['EMAIL_PASSWORD'] ?? '');      // <-- pega do .env

define('EMAIL_FROM', $env['EMAIL_USERNAME'] ?? '');      // <-- mesmo email