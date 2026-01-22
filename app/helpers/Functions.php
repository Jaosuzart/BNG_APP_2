<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// =======================================================
function check_session()
{
    // check if there is an active session
    return isset($_SESSION['user']);
}

// =======================================================
function logger($message = '', $level = 'info')
{
    // create log channel
    $log = new Logger('app_logs');
    $log->pushHandler(new StreamHandler(LOGS_PATH));

    // add log message
    switch ($level) {
        case 'info':
            $log->info($message);
            break;
        case 'notice':
            $log->notice($message);
            break;
        case 'warning':
            $log->warning($message);
            break;
        case 'error':
            $log->error($message);
            break;
        case 'critical':
            $log->critical($message);
            break;
        case 'alert':
            $log->alert($message);
            break;
        case 'emergency':
            $log->emergency($message);
            break;
        
        default:
            $log->info($message);
            break;
    }
}

// =======================================================
// =======================================================
function aes_encrypt($value)
{
    // Tratar null ou valores inválidos para evitar depreciação no PHP 8.1+
    if ($value === null || $value === false || $value === '') {
        return '';  // Ou retorne false se preferir invalidar a operação
    }

    // Converter para string se não for
    $value = (string) $value;

    // Encrypt $value
    $encrypted = openssl_encrypt($value, 'aes-256-cbc', OPENSSL_KEY, OPENSSL_RAW_DATA, OPENSSL_IV);
    if ($encrypted === false) {
        // Log de erro se a criptografia falhar (opcional, mas bom para debug)
        logger("Erro ao criptografar valor: " . $value, 'error');
        return false;
    }

    return bin2hex($encrypted);
}
function aes_decrypt($value)
{
    if (empty($value) || strlen($value) % 2 != 0) {
        return false;
    }

    $decrypted = openssl_decrypt(hex2bin($value), 'aes-256-cbc', OPENSSL_KEY, OPENSSL_RAW_DATA, OPENSSL_IV);
    if ($decrypted === false) {
        logger("Erro ao descriptografar valor: " . $value, 'error');
        return false;
    }

    return $decrypted;
}
// =======================================================
function get_active_user_name()
{
    return $_SESSION['user']->name;
}

// =======================================================
function printData($data, $die = true)
{
    // debug
    echo '<pre>';
    if(is_object($data) || is_array($data)){
        print_r($data);
    } else {
        echo $data;
    }

    if($die){
        die('<br>FIM</br>');
    }
}