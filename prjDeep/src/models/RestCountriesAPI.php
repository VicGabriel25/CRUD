<?php
class RestCountriesAPI {
    
    public function obterPorCodigo($codigo) {
        try {
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
    
    public function buscarPorNome($nome) {
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
                    'subregiao' => $pais['subregiao'] ?? ''
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