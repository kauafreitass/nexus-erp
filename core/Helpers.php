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
    // Garante que a base não seja apenas uma barra se estiver na raiz
    $base = ($base === '/' || $base === '\\') ? '' : $base;
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

/**
 * Gera uma tag <script> para um arquivo JavaScript.
 *
 * @param string $path O caminho para o arquivo JS a partir da pasta de assets.
 * @param array  $attributes Atributos HTML adicionais para a tag (ex: ['defer' => true]).
 */
function js($path, $attributes = [])
{
    // Usa a função 'asset' existente para obter a URL correta
    $url = asset($path);

    $attributeString = '';
    // Constrói a string de atributos a partir do array
    foreach ($attributes as $key => $value) {
        // Para atributos booleanos como 'defer', só precisamos da chave
        if ($value === true) {
            $attributeString .= " {$key}";
        } else {
            // Para outros atributos como 'type="module"'
            $attributeString .= " {$key}=\"{$value}\"";
        }
    }

    // Imprime a tag <script> completa
    echo "<script src=\"{$url}\"{$attributeString}></script>\n";
}