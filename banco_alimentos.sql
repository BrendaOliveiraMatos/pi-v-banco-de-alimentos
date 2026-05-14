-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14/05/2026 às 21:01
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `banco_alimentos`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `beneficiarios`
--

CREATE TABLE `beneficiarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `beneficiarios`
--

INSERT INTO `beneficiarios` (`id`, `nome`) VALUES
(1, 'Creche Municipal Vovó Cida'),
(2, 'Família Silva (Cadastro Assistência Social)'),
(3, 'Asilo Esperança'),
(4, 'Associação de Moradores do Bairro');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`) VALUES
(1, 'Cereais'),
(2, 'Enlatados'),
(3, 'Hortifruti'),
(4, 'Laticínios'),
(5, 'Padaria e Confeitaria');

-- --------------------------------------------------------

--
-- Estrutura para tabela `doadores`
--

CREATE TABLE `doadores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `doadores`
--

INSERT INTO `doadores` (`id`, `nome`, `telefone`) VALUES
(1, 'Supermercado Bertolini', '18996515496'),
(2, 'Ateliê do Bolo & Co', NULL),
(3, 'Hortifruti Andradina', NULL),
(4, 'Fazenda Esperança', NULL),
(5, 'Distribuidora Silva', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `unidade` varchar(20) NOT NULL,
  `data_validade` date NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `doador_id` int(11) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `quantidade`, `unidade`, `data_validade`, `categoria_id`, `doador_id`, `data_cadastro`) VALUES
(1, 'Arroz Branco 5kg', 50, 'Pacotes', '2027-01-10', 1, 1, '2026-05-02 03:00:00'),
(2, 'Feijão Carioca 1kg', 20, 'Pacotes', '2026-04-10', 1, 5, '2026-04-05 03:00:00'),
(3, 'Bolos Caseiros Sortidos', 15, 'Unidades', '2026-05-20', 5, 2, '2026-05-12 03:00:00'),
(4, 'Leite Integral 1L', 100, 'Caixas', '2026-08-25', 3, 1, '2026-05-05 03:00:00'),
(5, 'Arroz Branco', 10, 'pct(s) de 5.00 Kg', '2026-06-20', 1, 1, '2026-05-12 18:11:30'),
(6, 'Macarrão Espaguete 500g', 80, 'Pacotes', '2026-12-01', 1, 5, '2026-05-14 03:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos_catalogo`
--

CREATE TABLE `produtos_catalogo` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `peso` decimal(10,2) NOT NULL DEFAULT 1.00,
  `categoria_id` int(11) DEFAULT NULL,
  `unidade` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos_catalogo`
--

INSERT INTO `produtos_catalogo` (`id`, `nome`, `peso`, `categoria_id`, `unidade`) VALUES
(1, 'Arroz Branco', 1.00, 1, 'Kg'),
(2, 'Arroz Branco', 5.00, 1, 'Kg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `saidas`
--

CREATE TABLE `saidas` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `beneficiario_id` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `beneficiario` varchar(100) NOT NULL,
  `data_saida` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `saidas`
--

INSERT INTO `saidas` (`id`, `produto_id`, `beneficiario_id`, `quantidade`, `beneficiario`, `data_saida`) VALUES
(1, 1, 1, 10, '', '2026-05-05 03:00:00'),
(2, 4, 2, 25, '', '2026-05-08 03:00:00'),
(3, 3, 3, 5, '', '2026-05-13 03:00:00'),
(4, 1, 1, 5, '', '2026-05-14 03:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`) VALUES
(1, 'Administrador', 'admin@ong.com.br', '123456');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `beneficiarios`
--
ALTER TABLE `beneficiarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `doadores`
--
ALTER TABLE `doadores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `doador_id` (`doador_id`);

--
-- Índices de tabela `produtos_catalogo`
--
ALTER TABLE `produtos_catalogo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Índices de tabela `saidas`
--
ALTER TABLE `saidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `beneficiarios`
--
ALTER TABLE `beneficiarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `doadores`
--
ALTER TABLE `doadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `produtos_catalogo`
--
ALTER TABLE `produtos_catalogo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `saidas`
--
ALTER TABLE `saidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `produtos_ibfk_2` FOREIGN KEY (`doador_id`) REFERENCES `doadores` (`id`);

--
-- Restrições para tabelas `produtos_catalogo`
--
ALTER TABLE `produtos_catalogo`
  ADD CONSTRAINT `produtos_catalogo_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Restrições para tabelas `saidas`
--
ALTER TABLE `saidas`
  ADD CONSTRAINT `saidas_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
