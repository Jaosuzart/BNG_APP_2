<?php

namespace bng\System;

use Exception;

class Router
{
    public static function dispatch()
    {
        // main route values
        $httpverb = $_SERVER['REQUEST_METHOD'];
        
        // default controller and method
        $controller = 'main';
        $method = 'index';

        // check uri parameters
        if (isset($_GET['ct'])) {
            $controller = $_GET['ct'];
        }

        if (isset($_GET['mt'])) {
            $method = $_GET['mt'];
        }

        // Extrai apenas os valores dos parâmetros restantes (sem 'ct' e 'mt')
        $parameters = [];
        foreach ($_GET as $key => $value) {
            if ($key !== 'ct' && $key !== 'mt') {
                $parameters[] = $value;
            }
        }

        // Instancia o controller e chama o método com os parâmetros posicionais
        try {
            $class = "bng\\Controllers\\" . ucfirst($controller); // Boa prática: capitalize controller
            if (!class_exists($class)) {
                throw new Exception("Controller '$controller' não encontrado.");
            }

            $controllerInstance = new $class();

            if (!method_exists($controllerInstance, $method)) {
                throw new Exception("Método '$method' não existe no controller '$controller'.");
            }

            // Chama o método com parâmetros posicionais (não nomeados)
            $controllerInstance->$method(...$parameters);

        } catch (Exception $err) {
            // Em produção, logar erro e mostrar página amigável
            die("Erro: " . htmlspecialchars($err->getMessage()));
        }
    }
}