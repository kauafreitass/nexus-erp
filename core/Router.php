<?php
class Router
{
    public static function get($uri, $action)
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Cálculo seguro do baseUri
        $baseUri = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $route = '/' . trim(str_replace($baseUri, '', $requestUri), '/');

        // Debug opcional
        // echo "URL solicitada: $route";

        if (rtrim($route, '/') === rtrim($uri, '/')) {
            [$class, $method] = $action;

            $controllerFile = __DIR__ . '/../app/Controllers/' . basename(str_replace('\\', '/', $class)) . '.php';

            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controller = new $class;
                call_user_func([$controller, $method]);
                exit;
            } else {
                http_response_code(500);
                echo "Erro: controller '$class' não encontrado.";
                exit;
            }
        }

        // Retorno final
        return;
        http_response_code(404);
        echo "Página não encontrada.";
    }
}

