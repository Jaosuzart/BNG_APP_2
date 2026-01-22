<?php

define('APP_NAME',  'Basic Name Gathering');

// =======================================================
// CONFIGURAÇÕES DE BANCO DE DADOS
// =======================================================
define('MYSQL_HOST',        'localhost');
define('MYSQL_DATABASE',    'db_bng');
define('MYSQL_USERNAME',    'user_db_bng');
define('MYSQL_PASSWORD',    $env['MYSQL_PASSWORD']);

// Chaves de Segurança
define('MYSQL_AES_KEY',     $env['MYSQL_AES_KEY']);
define('OPENSSL_KEY',       $env['OPENSSL_KEY']);
define('OPENSSL_IV',        $env['OPENSSL_IV']);

// Logs
define('LOGS_PATH',         __DIR__ . '/../logs/app.log');


$envPath = __DIR__ . '/../.env';

// 2. Verifica se o arquivo existe e carrega
if (file_exists($envPath)) {
    $env = parse_ini_file($envPath);
} else {
    die('ERRO: Arquivo .env não encontrado na raiz do projeto!');
}

define('EMAIL_HOST', $env['EMAIL_HOST']);
define('EMAIL_PORT', $env['EMAIL_PORT']);
define('EMAIL_USER', $env['EMAIL_USER']);
define('EMAIL_PASS', $env['EMAIL_PASS']); 
define('EMAIL_FROM', $env['EMAIL_USER']);