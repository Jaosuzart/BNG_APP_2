<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use bng\System\Router;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

Router::dispatch();
