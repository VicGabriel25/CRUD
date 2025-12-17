<?php
class CidadeController {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // Listar todas as cidades
    public function listar() {
        $sql = "SELECT c.*, p.nome as pais_nome, p.continente 
                FROM cidades c 
                JOIN paises p ON c.id_pais = p.id_pais 
                ORDER BY c.nome";
        $result = $this->conn->query($sql);
        
        $cidades = [];
        while($row = $result->fetch_assoc()) {
            $cidades[] = $row;
        }
        
        return [
            'sucesso' => true,
            'dados' => $cidades,
            'total' => count($cidades)
        ];
    }
    
    // Obter cidade por ID
    public function obter($id) {
        $stmt = $this->conn->prepare("
            SELECT c.*, p.nome as pais_nome, p.continente 
            FROM cidades c 
            JOIN paises p ON c.id_pais = p.id_pais 
            WHERE c.id_cidade = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return [
                'sucesso' => true,
                'dados' => $result->fetch_assoc()
            ];
        }
        
        return [
            'sucesso' => false,
            'mensagem' => 'Cidade não encontrada'
        ];
    }
    
    // Criar nova cidade
    public function criar($dados) {
        $stmt = $this->conn->prepare("
            INSERT INTO cidades (nome, populacao, id_pais, latitude, longitude) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param(
            "siidd",
            $dados['nome'],
            $dados['populacao'],
            $dados['id_pais'],
            $dados['latitude'] ?? null,
            $dados['longitude'] ?? null
        );
        
        if ($stmt->execute()) {
            return [
                'sucesso' => true,
                'id' => $stmt->insert_id,
                'mensagem' => 'Cidade criada com sucesso'
            ];
        }
        
        return [
            'sucesso' => false,
            'mensagem' => 'Erro ao criar cidade: ' . $stmt->error
        ];
    }
    
    // Atualizar cidade
    public function atualizar($id, $dados) {
        $stmt = $this->conn->prepare("
            UPDATE cidades 
            SET nome = ?, populacao = ?, id_pais = ?, latitude = ?, longitude = ? 
            WHERE id_cidade = ?
        ");
        
        $stmt->bind_param(
            "siiddi",
            $dados['nome'],
            $dados['populacao'],
            $dados['id_pais'],
            $dados['latitude'] ?? null,
            $dados['longitude'] ?? null,
            $id
        );
        
        if ($stmt->execute()) {
            return [
                'sucesso' => true,
                'mensagem' => 'Cidade atualizada com sucesso'
            ];
        }
        
        return [
            'sucesso' => false,
            'mensagem' => 'Erro ao atualizar cidade: ' . $stmt->error
        ];
    }
    
    // Deletar cidade
    public function deletar($id) {
        $stmt = $this->conn->prepare("DELETE FROM cidades WHERE id_cidade = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return [
                'sucesso' => true,
                'mensagem' => 'Cidade excluída com sucesso'
            ];
        }
        
        return [
            'sucesso' => false,
            'mensagem' => 'Erro ao excluir cidade'
        ];
    }
    
    // Listar cidades por país
    public function listarPorPais($id_pais) {
        $stmt = $this->conn->prepare("
            SELECT * FROM cidades 
            WHERE id_pais = ? 
            ORDER BY nome
        ");
        $stmt->bind_param("i", $id_pais);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $cidades = [];
        while($row = $result->fetch_assoc()) {
            $cidades[] = $row;
        }
        
        return [
            'sucesso' => true,
            'dados' => $cidades,
            'total' => count($cidades)
        ];
    }
    
    // Buscar cidades
    public function buscar($termo) {
        $stmt = $this->conn->prepare("
            SELECT c.*, p.nome as pais_nome 
            FROM cidades c 
            JOIN paises p ON c.id_pais = p.id_pais 
            WHERE c.nome LIKE ? OR p.nome LIKE ?
            ORDER BY c.nome
        ");
        
        $termoBusca = "%{$termo}%";
        $stmt->bind_param("ss", $termoBusca, $termoBusca);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $cidades = [];
        while($row = $result->fetch_assoc()) {
            $cidades[] = $row;
        }
        
        return [
            'sucesso' => true,
            'dados' => $cidades,
            'total' => count($cidades)
        ];
    }
    
    // Obter estatísticas de cidades
    public function estatisticas() {
        $stats = [];
        
        // Total de cidades
        $result = $this->conn->query("SELECT COUNT(*) as total FROM cidades");
        $stats['total_cidades'] = $result->fetch_assoc()['total'];
        
        // Cidade mais populosa
        $result = $this->conn->query("
            SELECT c.nome, c.populacao, p.nome as pais 
            FROM cidades c 
            JOIN paises p ON c.id_pais = p.id_pais 
            ORDER BY c.populacao DESC 
            LIMIT 1
        ");
        $stats['mais_populosa'] = $result->fetch_assoc();
        
        // Média de população
        $result = $this->conn->query("SELECT AVG(populacao) as media FROM cidades");
        $stats['media_populacao'] = round($result->fetch_assoc()['media']);
        
        // Cidades por continente
        $result = $this->conn->query("
            SELECT p.continente, COUNT(*) as total 
            FROM cidades c 
            JOIN paises p ON c.id_pais = p.id_pais 
            GROUP BY p.continente 
            ORDER BY total DESC
        ");
        
        $stats['por_continente'] = [];
        while($row = $result->fetch_assoc()) {
            $stats['por_continente'][] = $row;
        }
        
        return [
            'sucesso' => true,
            'dados' => $stats
        ];
    }
    
    // Método para obter clima da cidade via OpenWeatherMap
    public function obterClima($cidade_id, $api_key = null) {
        // Primeiro, obter informações da cidade
        $stmt = $this->conn->prepare("
            SELECT c.*, p.nome as pais_nome 
            FROM cidades c 
            JOIN paises p ON c.id_pais = p.id_pais 
            WHERE c.id_cidade = ?
        ");
        $stmt->bind_param("i", $cidade_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            return [
                'sucesso' => false,
                'mensagem' => 'Cidade não encontrada'
            ];
        }
        
        $cidade = $result->fetch_assoc();
        
        // Verificar se temos coordenadas
        if (!$cidade['latitude'] || !$cidade['longitude']) {
            // Tentar obter coordenadas via geocoding
            $coordenadas = $this->geocode($cidade['nome'] . ', ' . $cidade['pais_nome']);
            
            if ($coordenadas) {
                $cidade['latitude'] = $coordenadas['lat'];
                $cidade['longitude'] = $coordenadas['lon'];
                
                // Atualizar no banco
                $update = $this->conn->prepare("
                    UPDATE cidades 
                    SET latitude = ?, longitude = ? 
                    WHERE id_cidade = ?
                ");
                $update->bind_param("ddi", $coordenadas['lat'], $coordenadas['lon'], $cidade_id);
                $update->execute();
            } else {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Coordenadas não disponíveis para esta cidade'
                ];
            }
        }
        
        // Se não temos API key, usar uma demo ou retornar erro
        if (!$api_key) {
            return $this->climaSimulado($cidade);
        }
        
        // Consultar OpenWeatherMap API
        try {
            $url = "https://api.openweathermap.org/data/2.5/weather?" .
                   "lat={$cidade['latitude']}&lon={$cidade['longitude']}&" .
                   "appid={$api_key}&units=metric&lang=pt_br";
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json'
                ]
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($httpCode != 200 || $error) {
                // Se falhar, retornar clima simulado
                return $this->climaSimulado($cidade);
            }
            
            $dados = json_decode($response, true);
            
            $clima = [
                'cidade' => $cidade['nome'],
                'pais' => $cidade['pais_nome'],
                'temperatura' => round($dados['main']['temp'], 1),
                'sensacao_termica' => round($dados['main']['feels_like'], 1),
                'minima' => round($dados['main']['temp_min'], 1),
                'maxima' => round($dados['main']['temp_max'], 1),
                'umidade' => $dados['main']['humidity'],
                'pressao' => $dados['main']['pressure'],
                'condicao' => ucfirst($dados['weather'][0]['description']),
                'icone' => $dados['weather'][0]['icon'],
                'vento' => [
                    'velocidade' => $dados['wind']['speed'],
                    'direcao' => $this->direcaoVento($dados['wind']['deg'] ?? 0)
                ],
                'visibilidade' => round($dados['visibility'] / 1000, 1), // km
                'nascer_sol' => date('H:i', $dados['sys']['sunrise']),
                'por_sol' => date('H:i', $dados['sys']['sunset']),
                'fonte' => 'OpenWeatherMap',
                'atualizado' => date('d/m/Y H:i')
            ];
            
            return [
                'sucesso' => true,
                'mensagem' => 'Clima obtido com sucesso',
                'dados' => $clima
            ];
            
        } catch (Exception $e) {
            return $this->climaSimulado($cidade);
        }
    }
    
    // Método para clima simulado (fallback quando API falha)
    private function climaSimulado($cidade) {
        // Gerar valores aleatórios baseados no nome da cidade (para consistência)
        $seed = crc32($cidade['nome'] . $cidade['pais_nome']);
        srand($seed);
        
        $temperatura = rand(10, 35) + (rand(0, 9) / 10);
        $condicoes = ['Ensolarado', 'Parcialmente nublado', 'Nublado', 'Chuvoso', 'Tempestuoso'];
        $condicao = $condicoes[$seed % count($condicoes)];
        
        $clima = [
            'cidade' => $cidade['nome'],
            'pais' => $cidade['pais_nome'],
            'temperatura' => $temperatura,
            'sensacao_termica' => $temperatura + rand(-3, 3),
            'minima' => $temperatura - rand(3, 8),
            'maxima' => $temperatura + rand(3, 8),
            'umidade' => rand(40, 90),
            'pressao' => rand(990, 1030),
            'condicao' => $condicao,
            'icone' => $this->iconePorCondicao($condicao),
            'vento' => [
                'velocidade' => rand(0, 15) / 3.6, // m/s
                'direcao' => $this->direcaoVento(rand(0, 360))
            ],
            'visibilidade' => rand(5, 20),
            'nascer_sol' => '06:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT),
            'por_sol' => '18:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT),
            'fonte' => 'Simulação',
            'atualizado' => date('d/m/Y H:i')
        ];
        
        return [
            'sucesso' => true,
            'mensagem' => 'Clima simulado (API não disponível)',
            'dados' => $clima
        ];
    }
    
    // Método para geocodificação (obter coordenadas)
    private function geocode($endereco) {
        try {
            $url = "https://nominatim.openstreetmap.org/search?" .
                   "format=json&q=" . urlencode($endereco) .
                   "&limit=1";
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_USERAGENT => 'CRUDMundo/1.0',
                CURLOPT_HTTPHEADER => ['Accept: application/json']
            ]);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            $dados = json_decode($response, true);
            
            if (!empty($dados) && isset($dados[0])) {
                return [
                    'lat' => $dados[0]['lat'],
                    'lon' => $dados[0]['lon']
                ];
            }
        } catch (Exception $e) {
            // Silenciar erro, retornar null
        }
        
        return null;
    }
    
    // Métodos auxiliares
    private function direcaoVento($graus) {
        $direcoes = ['N', 'NE', 'L', 'SE', 'S', 'SO', 'O', 'NO', 'N'];
        $index = round($graus / 45) % 8;
        return $direcoes[$index];
    }
    
    private function iconePorCondicao($condicao) {
        $icones = [
            'Ensolarado' => '01d',
            'Parcialmente nublado' => '02d',
            'Nublado' => '03d',
            'Chuvoso' => '09d',
            'Tempestuoso' => '11d'
        ];
        return $icones[$condicao] ?? '01d';
    }
}
?>