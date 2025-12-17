<?php
class PaisController {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // Listar todos os países
    public function listar() {
        $sql = "SELECT p.*, 
                       (SELECT COUNT(*) FROM cidades WHERE id_pais = p.id_pais) as total_cidades 
                FROM paises p 
                ORDER BY p.nome";
        $result = $this->conn->query($sql);
        
        $paises = [];
        while($row = $result->fetch_assoc()) {
            $paises[] = $row;
        }
        
        return [
            'sucesso' => true,
            'dados' => $paises,
            'total' => count($paises)
        ];
    }
    
    // Obter país por ID
    public function obter($id) {
        $stmt = $this->conn->prepare("SELECT * FROM paises WHERE id_pais = ?");
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
            'mensagem' => 'País não encontrado'
        ];
    }
    
    // Criar novo país
    public function criar($dados) {
        $stmt = $this->conn->prepare("
            INSERT INTO paises (nome, continente, populacao, idioma, codigo_iso, capital, moeda, bandeira_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param(
            "ssisssss",
            $dados['nome'],
            $dados['continente'],
            $dados['populacao'],
            $dados['idioma'],
            $dados['codigo_iso'] ?? null,
            $dados['capital'] ?? null,
            $dados['moeda'] ?? null,
            $dados['bandeira_url'] ?? null
        );
        
        if ($stmt->execute()) {
            return [
                'sucesso' => true,
                'id' => $stmt->insert_id,
                'mensagem' => 'País criado com sucesso'
            ];
        }
        
        return [
            'sucesso' => false,
            'mensagem' => 'Erro ao criar país: ' . $stmt->error
        ];
    }
    
    // Atualizar país
    public function atualizar($id, $dados) {
        $stmt = $this->conn->prepare("
            UPDATE paises 
            SET nome = ?, continente = ?, populacao = ?, idioma = ?, 
                codigo_iso = ?, capital = ?, moeda = ?, bandeira_url = ? 
            WHERE id_pais = ?
        ");
        
        $stmt->bind_param(
            "ssisssssi",
            $dados['nome'],
            $dados['continente'],
            $dados['populacao'],
            $dados['idioma'],
            $dados['codigo_iso'] ?? null,
            $dados['capital'] ?? null,
            $dados['moeda'] ?? null,
            $dados['bandeira_url'] ?? null,
            $id
        );
        
        if ($stmt->execute()) {
            return [
                'sucesso' => true,
                'mensagem' => 'País atualizado com sucesso'
            ];
        }
        
        return [
            'sucesso' => false,
            'mensagem' => 'Erro ao atualizar país: ' . $stmt->error
        ];
    }
    
    // Deletar país
    public function deletar($id) {
        // Verificar se existem cidades associadas
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM cidades WHERE id_pais = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['total'] > 0) {
            return [
                'sucesso' => false,
                'mensagem' => 'Não é possível excluir país com cidades associadas'
            ];
        }
        
        $stmt = $this->conn->prepare("DELETE FROM paises WHERE id_pais = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return [
                'sucesso' => true,
                'mensagem' => 'País excluído com sucesso'
            ];
        }
        
        return [
            'sucesso' => false,
            'mensagem' => 'Erro ao excluir país'
        ];
    }
    
    // Buscar países
    public function buscar($termo) {
        $stmt = $this->conn->prepare("
            SELECT * FROM paises 
            WHERE nome LIKE ? OR continente LIKE ? OR idioma LIKE ?
            ORDER BY nome
        ");
        
        $termoBusca = "%{$termo}%";
        $stmt->bind_param("sss", $termoBusca, $termoBusca, $termoBusca);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $paises = [];
        while($row = $result->fetch_assoc()) {
            $paises[] = $row;
        }
        
        return [
            'sucesso' => true,
            'dados' => $paises,
            'total' => count($paises)
        ];
    }
    
    // Obter estatísticas de países
    public function estatisticas() {
        $stats = [];
        
        // Total de países
        $result = $this->conn->query("SELECT COUNT(*) as total FROM paises");
        $stats['total_paises'] = $result->fetch_assoc()['total'];
        
        // Países por continente
        $result = $this->conn->query("
            SELECT continente, COUNT(*) as total 
            FROM paises 
            GROUP BY continente 
            ORDER BY total DESC
        ");
        
        $stats['por_continente'] = [];
        while($row = $result->fetch_assoc()) {
            $stats['por_continente'][] = $row;
        }
        
        // Idioma mais comum
        $result = $this->conn->query("
            SELECT idioma, COUNT(*) as total 
            FROM paises 
            GROUP BY idioma 
            ORDER BY total DESC 
            LIMIT 5
        ");
        
        $stats['idiomas_populares'] = [];
        while($row = $result->fetch_assoc()) {
            $stats['idiomas_populares'][] = $row;
        }
        
        // País mais populoso
        $result = $this->conn->query("
            SELECT nome, populacao 
            FROM paises 
            ORDER BY populacao DESC 
            LIMIT 1
        ");
        $stats['mais_populoso'] = $result->fetch_assoc();
        
        return [
            'sucesso' => true,
            'dados' => $stats
        ];
    }
    
    // Método para obter informações da API REST Countries
    public function obterInformacoesAPI($codigo) {
        if (empty($codigo)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Código do país não fornecido'
            ];
        }
        
        try {
            // API REST Countries (não requer chave)
            $url = "https://restcountries.com/v3.1/alpha/{$codigo}";
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'User-Agent: CRUDMundo/1.0'
                ]
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($httpCode != 200 || $error) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Erro ao consultar API: ' . ($error ?: 'HTTP ' . $httpCode)
                ];
            }
            
            $dados = json_decode($response, true);
            
            if (empty($dados) || !isset($dados[0])) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'País não encontrado na API'
                ];
            }
            
            $paisAPI = $dados[0];
            
            // Extrair informações relevantes
            $informacoes = [
                'nome_oficial' => $paisAPI['name']['official'] ?? '',
                'nome_comum' => $paisAPI['name']['common'] ?? '',
                'capital' => $paisAPI['capital'][0] ?? '',
                'regiao' => $paisAPI['region'] ?? '',
                'subregiao' => $paisAPI['subregion'] ?? '',
                'populacao' => $paisAPI['population'] ?? 0,
                'area' => $paisAPI['area'] ?? 0,
                'fuso_horario' => $paisAPI['timezones'][0] ?? '',
                'bandeira' => $paisAPI['flags']['png'] ?? '',
                'brasao' => $paisAPI['coatOfArms']['png'] ?? '',
                'mapa' => $paisAPI['maps']['googleMaps'] ?? '',
                'idiomas' => isset($paisAPI['languages']) ? array_values($paisAPI['languages']) : [],
                'moedas' => [],
                'vizinhos' => $paisAPI['borders'] ?? []
            ];
            
            // Extrair moedas
            if (isset($paisAPI['currencies'])) {
                foreach ($paisAPI['currencies'] as $codigo => $moeda) {
                    $informacoes['moedas'][$codigo] = [
                        'nome' => $moeda['name'] ?? '',
                        'simbolo' => $moeda['symbol'] ?? ''
                    ];
                }
            }
            
            // Traduzir região para continente
            $informacoes['continente'] = $this->traduzirRegiaoParaContinente($paisAPI['region'] ?? '');
            
            return [
                'sucesso' => true,
                'mensagem' => 'Informações obtidas com sucesso',
                'dados' => $informacoes
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro: ' . $e->getMessage()
            ];
        }
    }
    
    // Método para importar país da API REST Countries
    public function importarDaAPI($codigo) {
        $info = $this->obterInformacoesAPI($codigo);
        
        if (!$info['sucesso']) {
            return $info;
        }
        
        $dadosAPI = $info['dados'];
        
        // Verificar se o país já existe
        $stmt = $this->conn->prepare("SELECT id_pais FROM paises WHERE codigo_iso = ?");
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return [
                'sucesso' => false,
                'mensagem' => 'País já cadastrado no sistema'
            ];
        }
        
        // Preparar dados para inserção
        $dados = [
            'nome' => $dadosAPI['nome_comum'],
            'codigo_iso' => $codigo,
            'continente' => $dadosAPI['continente'] ?? 'Outro',
            'populacao' => $dadosAPI['populacao'],
            'idioma' => !empty($dadosAPI['idiomas']) ? $dadosAPI['idiomas'][0] : 'Desconhecido',
            'capital' => $dadosAPI['capital'],
            'moeda' => !empty($dadosAPI['moedas']) ? implode(', ', array_keys($dadosAPI['moedas'])) : '',
            'bandeira_url' => $dadosAPI['bandeira']
        ];
        
        // Inserir no banco
        $stmt = $this->conn->prepare("
            INSERT INTO paises (nome, codigo_iso, continente, populacao, idioma, capital, moeda, bandeira_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param(
            "sssissss",
            $dados['nome'],
            $dados['codigo_iso'],
            $dados['continente'],
            $dados['populacao'],
            $dados['idioma'],
            $dados['capital'],
            $dados['moeda'],
            $dados['bandeira_url']
        );
        
        if ($stmt->execute()) {
            return [
                'sucesso' => true,
                'id' => $stmt->insert_id,
                'mensagem' => 'País importado com sucesso da API',
                'dados' => $dados
            ];
        }
        
        return [
            'sucesso' => false,
            'mensagem' => 'Erro ao importar país: ' . $stmt->error
        ];
    }
    
    // Método para buscar países por nome na API
    public function buscarNaAPI($nome) {
        try {
            $url = "https://restcountries.com/v3.1/name/" . urlencode($nome);
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode != 200) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Erro ao buscar países na API'
                ];
            }
            
            $dados = json_decode($response, true);
            
            $resultados = [];
            foreach ($dados as $pais) {
                $resultados[] = [
                    'nome' => $pais['name']['common'] ?? '',
                    'nome_oficial' => $pais['name']['official'] ?? '',
                    'codigo' => $pais['cca2'] ?? '',
                    'bandeira' => $pais['flags']['png'] ?? '',
                    'capital' => $pais['capital'][0] ?? '',
                    'populacao' => $pais['population'] ?? 0,
                    'regiao' => $pais['region'] ?? '',
                    'subregiao' => $pais['subregion'] ?? ''
                ];
            }
            
            return [
                'sucesso' => true,
                'mensagem' => 'Busca realizada com sucesso',
                'dados' => $resultados,
                'total' => count($resultados)
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro: ' . $e->getMessage()
            ];
        }
    }
    
    // Método auxiliar para traduzir região para continente
    private function traduzirRegiaoParaContinente($regiao) {
        $mapeamento = [
            'Africa' => 'África',
            'Americas' => 'América',
            'Asia' => 'Ásia',
            'Europe' => 'Europa',
            'Oceania' => 'Oceania',
            'Antarctic' => 'Antártida'
        ];
        
        return $mapeamento[$regiao] ?? 'Outro';
    }
}
?>