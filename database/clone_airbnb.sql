-- =========================================================================
-- clone_airbnb.sql — Criação do banco do Clone Airbnb (Parte 3)
-- Importar no phpMyAdmin (XAMPP) ou carregado automaticamente pelo Docker.
-- Usuário de teste do painel: admin@clone.com / admin12345
-- =========================================================================

CREATE DATABASE IF NOT EXISTS clone_airbnb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clone_airbnb;

-- Administradores do painel (senha gravada com password_hash)
CREATE TABLE administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(80) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Usuários cadastrados pelo formulário de login.php
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(80) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    telefone VARCHAR(20) NOT NULL,
    nascimento DATE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Acomodações (entidade com CRUD no painel e listagem pública)
-- categorias, imagens e comodidades: valores separados por vírgula
CREATE TABLE acomodacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(120) NOT NULL,
    tipo VARCHAR(40) NOT NULL,
    cidade VARCHAR(80) NOT NULL,
    estado VARCHAR(40) NOT NULL,
    descricao TEXT NOT NULL,
    diaria DECIMAL(10,2) NOT NULL,
    capacidade TINYINT UNSIGNED NOT NULL,
    quartos TINYINT UNSIGNED NOT NULL DEFAULT 1,
    camas TINYINT UNSIGNED NOT NULL DEFAULT 1,
    banheiros TINYINT UNSIGNED NOT NULL DEFAULT 1,
    min_noites TINYINT UNSIGNED NOT NULL DEFAULT 1,
    nota DECIMAL(3,2) NOT NULL DEFAULT 0,
    avaliacoes INT UNSIGNED NOT NULL DEFAULT 0,
    selo VARCHAR(40) NULL,
    anfitriao VARCHAR(80) NOT NULL,
    anfitriao_desde YEAR NOT NULL,
    categorias VARCHAR(120) NOT NULL DEFAULT '',
    imagens VARCHAR(255) NOT NULL DEFAULT '',
    comodidades VARCHAR(500) NOT NULL DEFAULT '',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Reservas feitas no checkout (registros de formulário listados no painel)
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    acomodacao_id INT NOT NULL,
    nome VARCHAR(80) NOT NULL,
    email VARCHAR(120) NOT NULL,
    cpf CHAR(11) NOT NULL,
    checkin DATE NOT NULL,
    checkout DATE NOT NULL,
    hospedes TINYINT UNSIGNED NOT NULL,
    forma_pagamento ENUM('pix', 'cartao') NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (acomodacao_id) REFERENCES acomodacoes(id) ON DELETE CASCADE
);

INSERT INTO administradores (nome, email, senha) VALUES
('Administrador', 'admin@clone.com', '$2y$12$.qmCxfQf6MXrenVL0AKIGeaEvaHOcqZxULAR5f.N0IMMxuZe7XIeu');

-- Acomodações fictícias (mesmas de js/dados.js)
INSERT INTO acomodacoes (id, titulo, tipo, cidade, estado, descricao, diaria, capacidade, quartos, camas, banheiros, min_noites, nota, avaliacoes, selo, anfitriao, anfitriao_desde, categorias, imagens, comodidades) VALUES
(1, 'Apto 106 - Porto de Galinhas - Centro', 'Apartamento', 'Ipojuca', 'Pernambuco', 'Apartamento novo no coração do Porto, a 200 metros do mar e a 2 minutos da Rua das Sombrinhas. Ambientes modernos, iluminados e confortáveis, com piscina e área gourmet para aproveitar o melhor de Porto de Galinhas.', 816.5, 4, 1, 2, 1, 2, 5, 6, 'Preferido dos hóspedes', 'Catalina', 2026, 'praia,piscina', 'casa-03,casa-01,casa-15,casa-16,casa-21', 'Cozinha,Wi-Fi,Estacionamento gratuito,Piscina,TV,Ar-condicionado,Detector de fumaça'),
(2, 'Bangalô sobre o mar em Muro Alto', 'Bangalô', 'Ipojuca', 'Pernambuco', 'Bangalô de palha com deck privativo sobre as águas mornas de Muro Alto. Ideal para casais que buscam sossego e pôr do sol inesquecível.', 1290, 2, 1, 1, 1, 3, 4.95, 41, 'Preferido dos hóspedes', 'Rodrigo', 2019, 'praia', 'casa-06,casa-23,casa-14,casa-02,casa-20', 'Wi-Fi,Vista para o mar,Ar-condicionado,Banheira de hidromassagem,TV'),
(3, 'Pousada com piscina entre coqueiros', 'Pousada', 'Ipojuca', 'Pernambuco', 'Quartos aconchegantes ao redor de uma grande piscina cercada de coqueiros, com café da manhã regional incluso.', 540, 3, 1, 2, 1, 1, 4.82, 128, NULL, 'Helena', 2017, 'praia,piscina', 'casa-07,casa-16,casa-01,casa-15,casa-21', 'Wi-Fi,Piscina,Ar-condicionado,TV,Estacionamento gratuito'),
(4, 'Hotel Lagoa Azul — quarto superior', 'Hotel', 'Ipojuca', 'Pernambuco', 'Hotel boutique com piscina de borda infinita iluminada à noite e restaurante com frutos do mar.', 690, 2, 1, 1, 1, 1, 4.71, 312, NULL, 'Hotel Lagoa Azul', 2015, 'hotel,piscina', 'casa-09,casa-23,casa-16,casa-14,casa-02', 'Wi-Fi,Piscina,Ar-condicionado,TV,Detector de fumaça'),
(5, 'Casa branca com piscina a 100 m da praia', 'Casa', 'Arraial do Cabo', 'Rio de Janeiro', 'Casa moderna para a família toda, com piscina privativa e a poucos passos da Prainha. Cozinha completa e churrasqueira.', 980, 8, 4, 5, 3, 3, 4.97, 54, 'Preferido dos hóspedes', 'Marcos', 2020, 'praia,piscina', 'casa-05,casa-21,casa-17,casa-16,casa-20', 'Cozinha,Wi-Fi,Estacionamento gratuito,Piscina,TV,Ar-condicionado,Lava-louças'),
(6, 'Casa com espreguiçadeiras e vista', 'Casa', 'Arraial do Cabo', 'Rio de Janeiro', 'Arquitetura contemporânea com piscina e solário. Fica entre a Praia Grande e o centro histórico.', 750, 6, 3, 3, 2, 2, 4.88, 23, NULL, 'Juliana', 2021, 'praia,piscina', 'casa-12,casa-14,casa-15,casa-23,casa-01', 'Cozinha,Wi-Fi,Piscina,TV,Espaço de trabalho'),
(7, 'Resort Pôr do Sol — suíte com varanda', 'Hotel', 'Arraial do Cabo', 'Rio de Janeiro', 'Resort à beira-mar com piscina espelhada, palmeiras e vista para o entardecer.', 870, 3, 1, 2, 1, 1, 4.76, 205, NULL, 'Resort Pôr do Sol', 2016, 'hotel,praia,piscina', 'casa-24,casa-23,casa-16,casa-02,casa-14', 'Wi-Fi,Piscina,Ar-condicionado,TV,Vista para o mar'),
(8, 'Casa de dois andares com jardim', 'Casa', 'Arraial do Cabo', 'Rio de Janeiro', 'Espaço amplo para grupos, com piscina, gramado e varanda gourmet.', 1150, 10, 5, 6, 4, 3, 4.9, 17, 'Preferido dos hóspedes', 'Paulo', 2018, 'piscina', 'casa-10,casa-21,casa-17,casa-20,casa-16', 'Cozinha,Wi-Fi,Estacionamento gratuito,Piscina,TV,Lava-louças'),
(9, 'Chalé de madeira iluminado', 'Chalé', 'Gramado', 'Rio Grande do Sul', 'Chalé aconchegante com lareira e janelas amplas para o bosque, a 5 minutos da Rua Coberta.', 620, 4, 2, 2, 1, 2, 4.98, 88, 'Preferido dos hóspedes', 'Bruna', 2019, 'chale', 'casa-04,casa-20,casa-16,casa-15,casa-01', 'Cozinha,Wi-Fi,Lareira,TV,Estacionamento gratuito'),
(10, 'Cabana à beira do lago', 'Cabana', 'Gramado', 'Rio Grande do Sul', 'Cabana cercada de pinheiros, com deck sobre o lago e banheira aquecida para as noites frias da serra.', 710, 4, 2, 3, 1, 2, 4.93, 35, NULL, 'Eduardo', 2022, 'chale', 'casa-19,casa-14,casa-23,casa-17,casa-20', 'Cozinha,Wi-Fi,Lareira,Banheira de hidromassagem,Estacionamento gratuito'),
(11, 'Casa de tijolos no bosque', 'Casa', 'Gramado', 'Rio Grande do Sul', 'Casa charmosa em estilo europeu, rodeada de árvores e com jardim de hortênsias.', 530, 5, 2, 3, 2, 2, 4.84, 62, NULL, 'Ingrid', 2018, 'chale', 'casa-22,casa-21,casa-15,casa-16,casa-02', 'Cozinha,Wi-Fi,Lareira,TV,Espaço de trabalho'),
(12, 'Cabana no campo ao pôr do sol', 'Cabana', 'Gramado', 'Rio Grande do Sul', 'Refúgio minimalista no meio dos campos de cima da serra, perfeito para desconectar.', 450, 2, 1, 1, 1, 1, 4.79, 29, NULL, 'Tiago', 2023, 'chale', 'casa-18,casa-20,casa-16,casa-14,casa-01', 'Wi-Fi,Lareira,Cozinha,Estacionamento gratuito'),
(13, 'Chalé com piscina e deck', 'Chalé', 'Gramado', 'Rio Grande do Sul', 'Chalé de madeira com piscina aquecida e espreguiçadeiras no deck.', 890, 6, 3, 4, 2, 2, 4.91, 47, 'Preferido dos hóspedes', 'Laura', 2020, 'chale,piscina', 'casa-08,casa-21,casa-17,casa-23,casa-16', 'Cozinha,Wi-Fi,Piscina,Lareira,TV,Estacionamento gratuito'),
(14, 'Casa moderna com piscina em Jurerê', 'Casa', 'Florianópolis', 'Santa Catarina', 'Casa de alto padrão a duas quadras da praia de Jurerê Internacional.', 1450, 8, 4, 4, 4, 3, 4.96, 31, 'Preferido dos hóspedes', 'Fernanda', 2017, 'praia,piscina', 'casa-11,casa-21,casa-17,casa-16,casa-14', 'Cozinha,Wi-Fi,Estacionamento gratuito,Piscina,TV,Ar-condicionado,Lava-louças'),
(15, 'Casa contemporânea na Lagoa', 'Casa', 'Florianópolis', 'Santa Catarina', 'Casa de linhas modernas com jardim e árvore centenária, perto dos restaurantes da Lagoa da Conceição.', 760, 6, 3, 3, 2, 2, 4.87, 44, NULL, 'Diego', 2019, 'urbano', 'casa-13,casa-20,casa-15,casa-23,casa-01', 'Cozinha,Wi-Fi,TV,Espaço de trabalho,Estacionamento gratuito'),
(16, 'Loft claro perto da Beira-Mar', 'Apartamento', 'Florianópolis', 'Santa Catarina', 'Loft compacto e iluminado, ideal para viagens a trabalho, próximo à Avenida Beira-Mar Norte.', 380, 2, 1, 1, 1, 1, 4.74, 96, NULL, 'Camila', 2021, 'urbano', 'casa-14,casa-02,casa-15,casa-16,casa-20', 'Cozinha,Wi-Fi,TV,Ar-condicionado,Espaço de trabalho');
