<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once '../../../config/database.php';
$conn = getConnection();

// Obter dados para o mapa
$result = $conn->query("
    SELECT p.*, 
           (SELECT COUNT(*) FROM cidades WHERE id_pais = p.id_pais) as total_cidades,
           (SELECT JSON_ARRAYAGG(
                JSON_OBJECT(
                    'id_cidade', c.id_cidade,
                    'nome', c.nome,
                    'populacao', c.populacao,
                    'latitude', c.latitude,
                    'longitude', c.longitude
                )
            ) FROM cidades c WHERE c.id_pais = p.id_pais) as cidades_json
    FROM paises p
    ORDER BY p.nome
");

$dadosMapa = [];
while($row = $result->fetch_assoc()) {
    $pais = [
        'id_pais' => $row['id_pais'],
        'nome' => $row['nome'],
        'continente' => $row['continente'],
        'populacao' => $row['populacao'],
        'idioma' => $row['idioma'],
        'codigo_iso' => $row['codigo_iso'],
        'bandeira_url' => $row['bandeira_url'],
        'capital' => $row['capital'],
        'total_cidades' => $row['total_cidades'],
        'cidades' => json_decode($row['cidades_json'] ?? '[]', true)
    ];
    
    // Gerar posição aleatória para o mapa (simulação)
    $pais['x'] = rand(50, 750);
    $pais['y'] = rand(50, 550);
    $pais['radius'] = 30 + ($row['populacao'] / 100000000); // Raio baseado na população
    
    $dadosMapa[] = $pais;
}

// Estatísticas gerais para o painel
$stats = [];

// Total de países
$result = $conn->query("SELECT COUNT(*) as total FROM paises");
$stats['total_paises'] = $result->fetch_assoc()['total'];

// Total de cidades
$result = $conn->query("SELECT COUNT(*) as total FROM cidades");
$stats['total_cidades'] = $result->fetch_assoc()['total'];

// Continentes únicos
$result = $conn->query("SELECT DISTINCT continente FROM paises");
$continentes = [];
while($row = $result->fetch_assoc()) {
    $continentes[] = $row['continente'];
}
$stats['continentes'] = $continentes;
$stats['total_continentes'] = count($continentes);

// População total
$result = $conn->query("SELECT SUM(populacao) as total FROM paises");
$stats['populacao_total'] = $result->fetch_assoc()['total'];

echo json_encode([
    'sucesso' => true,
    'dados' => [
        'paises' => $dadosMapa,
        'estatisticas' => $stats
    ]
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>