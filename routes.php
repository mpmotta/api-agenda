<?php
require_once 'controllers/PessoaController.php';

$controller = new PessoaController($pdo);
$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));

if ($path[0] === 'pessoas') {
    // Remova ou comente as linhas abaixo para deixar a rota pública:
    // if (!isPublicRoute($method, $path)) {
    //     authenticateJWT();
    // }

    switch ($method) {
        case 'GET':
            if (isset($path[1]) && is_numeric($path[1])) {
                echo json_encode($controller->show($path[1]));
            } else {
                echo json_encode($controller->index());
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $controller->store($data);
            echo json_encode(['success' => true]);
            break;
        case 'PUT':
            if (isset($path[1]) && is_numeric($path[1])) {
                $data = json_decode(file_get_contents('php://input'), true);
                $controller->update($path[1], $data);
                echo json_encode(['success' => true]);
            }
            break;
        case 'DELETE':
            if (isset($path[1]) && is_numeric($path[1])) {
                $controller->destroy($path[1]);
                echo json_encode(['success' => true]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
            break;
    }
} elseif ($path[0] === 'login' && $method === 'POST') {
    require_once 'vendor/autoload.php';
    $data = json_decode(file_get_contents('php://input'), true);
    $usuario = $data['usuario'] ?? '';
    $senha = $data['senha'] ?? '';

    if ($usuario === 'admin' && $senha === '123456') {
        $key = 'sua-chave-secreta';
        $payload = [
            "user" => $usuario,
            "exp" => time() + 3600
        ];
        $jwt = \Firebase\JWT\JWT::encode($payload, $key, 'HS256');
        echo json_encode(['token' => $jwt]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Usuário ou senha inválidos']);
    }
    exit;
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Rota não encontrada']);
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function isPublicRoute($method, $path) {
    return $method === 'POST' && $path[0] === 'login';
}

function authenticateJWT() {
    require_once 'vendor/autoload.php';
    $key = 'sua-chave-secreta';
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    $token = str_replace('Bearer ', '', $authHeader);

    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'Token não informado']);
        exit;
    }
    try {
        \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($key, 'HS256'));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Token inválido']);
        exit;
    }
}
