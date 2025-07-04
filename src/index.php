<?php
// -----------------------------------------------
// Nano API REST PHP 7.4 en 1 archivo
// -----------------------------------------------

// #section Cargar configuración desde .env (sin librerías)
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    die('ERROR: Archivo .env no encontrado');
}
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    [$name, $value] = explode('=', $line, 2);
    $_ENV[trim($name)] = trim($value);
}

define('IS_DEV', filter_var($_ENV['IS_DEV'] ?? false, FILTER_VALIDATE_BOOLEAN));
define('API_KEY', $_ENV['API_KEY'] ?? null);
define('DB_DRIVER', $_ENV['DB_DRIVER'] ?? 'sqlite');
define('DB_HOST', $_ENV['DB_HOST'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'database.sqlite');
define('DB_USER', $_ENV['DB_USER'] ?? '');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// #section Conexión PDO
function getPDO()
{
    static $pdo = null;
    if ($pdo === null) {
        switch (DB_DRIVER) {
            case 'mysql':
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                break;
            case 'pgsql':
                $dsn = "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME;
                break;
            case 'sqlsrv':
                $dsn = "sqlsrv:Server=" . DB_HOST . ";Database=" . DB_NAME;
                break;
            case 'oci':
                $dsn = "oci:dbname=" . DB_NAME;
                break;
            default:
                $dsn = "sqlite:" . DB_NAME;
        }
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            die(IS_DEV ? $e->getMessage() : 'Error de conexión DB');
        }
    }
    return $pdo;
}

// #section Helpers JSON
function jsonResponse($data, $code = 200)
{
    header_remove();
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

// #section Ruteador minimalista
$routes = [];

function route($method, $pattern, $callback)
{
    global $routes;
    $pattern = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern) . '$#';
    $routes[] = compact('method', 'pattern', 'callback');
}

function dispatch()
{
    global $routes;
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    foreach ($routes as $r) {
        if (strcasecmp($method, $r['method']) !== 0) continue;
        if (preg_match($r['pattern'], $uri, $matches)) {
            $params = [];
            foreach ($matches as $k => $v) {
                if (is_string($k)) $params[$k] = $v;
            }
            // Middleware API KEY
            if (API_KEY) {
                $provided = $_SERVER['HTTP_X_API_KEY'] ?? null;
                if ($provided !== API_KEY) {
                    jsonResponse(['error' => 'API Key inválida'], 401);
                }
            }
            call_user_func($r['callback'], $params);
            return;
        }
    }
    jsonResponse(['error' => 'Ruta no encontrada'], 404);
}

// #section Rutas de ejemplo
route('GET', '/api/hello', function () {
    jsonResponse(['message' => 'Hola mundo!']);
});

route('GET', '/api/users/{id}', function ($params) {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$params['id']]);
    $user = $stmt->fetch();
    if (!$user) {
        jsonResponse(['error' => 'No encontrado'], 404);
    }
    jsonResponse($user);
});

route('POST', '/api/users', function () {
    $data = json_decode(file_get_contents('php://input'), true);
    $pdo = getPDO();
    $stmt = $pdo->prepare("INSERT INTO users (name) VALUES (?)");
    $stmt->execute([$data['name'] ?? '']);
    jsonResponse(['status' => 'Usuario creado']);
});

// #section Despachar
dispatch();
