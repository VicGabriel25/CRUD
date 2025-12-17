<?php
require_once __DIR__ . '/../../config/api_keys.php';

class OpenWeatherMapAPI {
    
    public function obterClima($latitude, $longitude, $cidade_nome, $pais_nome) {
        // Se não tiver chave da API, retorna clima simulado
        if (!defined('OPENWEATHER_API_KEY') || empty(OPENWEATHER_API_KEY)) {
            return $this->climaSimulado($cidade_nome, $pais_nome);
        }
        
        try {
            $url = "https://api.openweathermap.org/data/2.5/weather?" .
                   "lat={$latitude}&lon={$longitude}&" .
                   "appid=" . OPENWEATHER_API_KEY . "&units=metric&lang=pt_br";
            
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
                return $this->climaSimulado($cidade_nome, $pais_nome);
            }
            
            $dados = json_decode($response, true);
            
            $clima = [
                'cidade' => $cidade_nome,
                'pais' => $pais_nome,
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
            return $this->climaSimulado($cidade_nome, $pais_nome);
        }
    }
    
    public function geocode($endereco) {
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
    
    private function climaSimulado($cidade_nome, $pais_nome) {
        // Gerar valores aleatórios baseados no nome da cidade (para consistência)
        $seed = crc32($cidade_nome . $pais_nome);
        srand($seed);
        
        $temperatura = rand(10, 35) + (rand(0, 9) / 10);
        $condicoes = ['Ensolarado', 'Parcialmente nublado', 'Nublado', 'Chuvoso', 'Tempestuoso'];
        $condicao = $condicoes[$seed % count($condicoes)];
        
        $clima = [
            'cidade' => $cidade_nome,
            'pais' => $pais_nome,
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