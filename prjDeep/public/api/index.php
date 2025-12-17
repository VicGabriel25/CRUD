<?php
// Configuração de headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Permite requisições OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuração do banco de dados
require_once '../../../config/database.php';
$conn = getConnection();

// Incluir controllers
require_once '../../../controllers/PaisController.php';
require_once '../../../controllers/CidadeController.php';

// Instanciar controllers
$paisController = new PaisController($conn);
$cidadeController = new CidadeController($conn);

// Determinar método HTTP
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($path, '/'));

// Roteamento básico - considerando que a API está em /api/index.php
// Ajuste baseado na estrutura: /api/index.php/paises/listar
// parts[0] = "api", parts[1] = "index.php", parts[2] = "paises", parts[3] = "listar"
$endpoint = $parts[2] ?? '';
$acao = $parts[3] ?? '';
$parametro = $parts[4] ?? '';

// Ler dados do corpo da requisição
$input = json_decode(file_get_contents('php://input'), true) ?? [];

// Sistema de rotas
switch($endpoint) {
    case 'paises':
        switch($acao) {
            case 'listar':
                $resultado = $paisController->listar();
                break;
                
            case 'obter':
                if ($parametro) {
                    $resultado = $paisController->obter($parametro);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'ID não fornecido'];
                }
                break;
                
            case 'criar':
                if ($method == 'POST' && $input) {
                    $resultado = $paisController->criar($input);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'Dados inválidos'];
                }
                break;
                
            case 'atualizar':
                if ($method == 'PUT' && $parametro && $input) {
                    $resultado = $paisController->atualizar($parametro, $input);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'Dados inválidos'];
                }
                break;
                
            case 'deletar':
                if ($method == 'DELETE' && $parametro) {
                    $resultado = $paisController->deletar($parametro);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'ID não fornecido'];
                }
                break;
                
            case 'buscar':
                if (isset($_GET['q'])) {
                    $resultado = $paisController->buscar($_GET['q']);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'Termo de busca não fornecido'];
                }
                break;
                
            case 'estatisticas':
                $resultado = $paisController->estatisticas();
                break;
                
            case 'informacoes-api':
                if (isset($_GET['codigo'])) {
                    $resultado = $paisController->obterInformacoesAPI($_GET['codigo']);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'Código do país não fornecido'];
                }
                break;
                
            case 'importar-api':
                if (isset($_GET['codigo'])) {
                    $resultado = $paisController->importarDaAPI($_GET['codigo']);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'Código do país não fornecido'];
                }
                break;
                
            case 'buscar-api':
                if (isset($_GET['nome'])) {
                    $resultado = $paisController->buscarNaAPI($_GET['nome']);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'Nome do país não fornecido'];
                }
                break;
                
            default:
                http_response_code(404);
                $resultado = ['sucesso' => false, 'mensagem' => 'Endpoint não encontrado'];
        }
        break;
        
    case 'cidades':
        switch($acao) {
            case 'listar':
                $resultado = $cidadeController->listar();
                break;
                
            case 'obter':
                if ($parametro) {
                    $resultado = $cidadeController->obter($parametro);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'ID não fornecido'];
                }
                break;
                
            case 'criar':
                if ($method == 'POST' && $input) {
                    $resultado = $cidadeController->criar($input);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'Dados inválidos'];
                }
                break;
                
            case 'atualizar':
                if ($method == 'PUT' && $parametro && $input) {
                    $resultado = $cidadeController->atualizar($parametro, $input);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'Dados inválidos'];
                }
                break;
                
            case 'deletar':
                if ($method == 'DELETE' && $parametro) {
                    $resultado = $cidadeController->deletar($parametro);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'ID não fornecido'];
                }
                break;
                
            case 'por-pais':
                if ($parametro) {
                    $resultado = $cidadeController->listarPorPais($parametro);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'ID do país não fornecido'];
                }
                break;
                
            case 'buscar':
                if (isset($_GET['q'])) {
                    $resultado = $cidadeController->buscar($_GET['q']);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'Termo de busca não fornecido'];
                }
                break;
                
            case 'estatisticas':
                $resultado = $cidadeController->estatisticas();
                break;
                
            case 'clima':
                if ($parametro) {
                    // Carregar a chave da API do arquivo de configuração
                    require_once '../../../config/api_keys.php';
                    $api_key = defined('ApiConfig::OPENWEATHER_API_KEY') ? ApiConfig::OPENWEATHER_API_KEY : null;
                    $resultado = $cidadeController->obterClima($parametro, $api_key);
                } else {
                    http_response_code(400);
                    $resultado = ['sucesso' => false, 'mensagem' => 'ID da cidade não fornecido'];
                }
                break;
                
            default:
                http_response_code(404);
                $resultado = ['sucesso' => false, 'mensagem' => 'Endpoint não encontrado'];
        }
        break;
        
    default:
        // Verificar se é uma requisição para o mapa (parâmetro mapa)
        if (isset($_GET['mapa'])) {
            // Endpoint específico para dados do mapa
            require_once 'mapa.php';
            exit;
        } else {
            http_response_code(404);
            $resultado = ['sucesso' => false, 'mensagem' => 'API endpoint não encontrado'];
        }
}

// Retornar resposta
echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// Fechar conexão
$conn->close();
?>