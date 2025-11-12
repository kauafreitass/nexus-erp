<?php
class Router
{
    /**
     * Método principal para adicionar uma rota.
     * Agora ele verifica o método da requisição (GET, POST, etc.)
     */
    private static function add($method, $uri, $action)
    {
        // 1. NOVO: Verifica se o método da requisição é o esperado
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            return; // O método não bate (ex: é um GET, mas a rota é POST), então ignora.
        }

        // 2. Sua lógica original de verificação de URI
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $baseUri = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $route = '/' . trim(str_replace($baseUri, '', $requestUri), '/');

        // 3. Sua lógica original de carregamento de Controller
        if (rtrim($route, '/') === rtrim($uri, '/')) {
            [$class, $method] = $action;

            $controllerFile = __DIR__ . '/../app/Controllers/' . basename(str_replace('\\', '/', $class)) . '.php';

            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controller = new $class;
                call_user_func([$controller, $method]);
                exit; // Sai após encontrar a rota correta
            } else {
                http_response_code(500);
                echo "Error: controller '$class' not found.";
                exit;
            }
        }
    }

    /**
     * Registra uma rota que SÓ funciona com o método GET.
     */
    public static function get($uri, $action)
    {
        self::add('GET', $uri, $action);
    }

    /**
     * Registra uma rota que SÓ funciona com o método POST.
     */
    public static function post($uri, $action)
    {
        self::add('POST', $uri, $action);
    }
}

// Seu 'return' e 'http_response_code(404)' no final do arquivo original 
// nunca seriam alcançados por causa do 'exit' dentro do 'if'.
// Se você tem um loop que chama essas rotas, o 404 deve ser tratado *depois*
// que todas as rotas foram verificadas.