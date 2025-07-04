#!/usr/bin/env php
<?php
/**
 * Script: generate_api_key.php
 * Uso: php generate_api_key.php [--length=32]
 */

$length = 32; // Longitud por defecto

// Lee argumento --length
foreach ($argv as $arg) {
    if (strpos($arg, '--length=') === 0) {
        $length = intval(substr($arg, 9));
    }
}

if ($length < 16) {
    echo "Longitud mínima recomendada: 16 caracteres.\n";
    exit(1);
}

// Generar clave usando random_bytes y bin2hex
$key = bin2hex(random_bytes(intval($length / 2)));

// Mostrar
echo "API Key generada: $key\n";

// Opcional: copiar al portapapeles (solo Linux/macOS)
// exec("echo '$key' | pbcopy"); // macOS
// exec("echo '$key' | xclip -selection clipboard"); // Linux + xclip

exit(0);
