<?php
spl_autoload_register(function ($class) {
    // Mapeia os namespaces para os diretórios
    $namespaces = [
        'App\\' => __DIR__ . '/../app/',
        'Database\\' => __DIR__ . '/../core/'
    ];

    // Procura pelo namespace correspondente
    foreach ($namespaces as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            // Remove o namespace do nome da classe
            $relativeClass = substr($class, $len);

            // Substitui as barras invertidas por barras do sistema de arquivos
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

function view($name, $data = [])
{
    extract($data);
    $viewPath = __DIR__ . '/../app/Views/' . $name . '.php';
    if (file_exists($viewPath)) {
        include $viewPath;
    } else {
        echo "Erro: view '$name' não encontrada.";
    }
}

function asset($path)
{
    $base = dirname($_SERVER['SCRIPT_NAME']);
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}