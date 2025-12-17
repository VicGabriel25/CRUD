CREATE DATABASE IF NOT EXISTS bd_mundo;
USE bd_mundo;

-- Tabela de países
CREATE TABLE paises (
    id_pais INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    continente VARCHAR(50) NOT NULL,
    populacao BIGINT NOT NULL,
    idioma VARCHAR(50) NOT NULL,
    codigo_iso VARCHAR(3) NULL,
    bandeira_url TEXT NULL,
    capital VARCHAR(100) NULL,
    moeda VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de cidades
CREATE TABLE cidades (
    id_cidade INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    populacao BIGINT NOT NULL,
    id_pais INT NOT NULL,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pais) REFERENCES paises(id_pais) ON DELETE CASCADE
);

-- Inserir alguns dados de exemplo
INSERT INTO paises (nome, continente, populacao, idioma, codigo_iso, capital, moeda) VALUES
('Brasil', 'América', 213993437, 'Português', 'BRA', 'Brasília', 'Real'),
('Estados Unidos', 'América', 331002651, 'Inglês', 'USA', 'Washington D.C.', 'Dólar'),
('França', 'Europa', 65273511, 'Francês', 'FRA', 'Paris', 'Euro'),
('Japão', 'Ásia', 125836021, 'Japonês', 'JPN', 'Tóquio', 'Iene'),
('Austrália', 'Oceania', 25499884, 'Inglês', 'AUS', 'Canberra', 'Dólar Australiano');

INSERT INTO cidades (nome, populacao, id_pais, latitude, longitude) VALUES
('São Paulo', 12325232, 1, -23.5505, -46.6333),
('Rio de Janeiro', 6747815, 1, -22.9068, -43.1729),
('Nova York', 8398748, 2, 40.7128, -74.0060),
('Los Angeles', 3980400, 2, 34.0522, -118.2437),
('Paris', 2161000, 3, 48.8566, 2.3522),
('Marselha', 861635, 3, 43.2965, 5.3698),
('Tóquio', 13960000, 4, 35.6762, 139.6503),
('Osaka', 2691000, 4, 34.6937, 135.5023),
('Sydney', 5312163, 5, -33.8688, 151.2093),
('Melbourne', 5078193, 5, -37.8136, 144.9631);