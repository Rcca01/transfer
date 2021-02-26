-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 26/02/2021 às 16:32
-- Versão do servidor: 10.4.17-MariaDB
-- Versão do PHP: 8.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `transfer`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `status`
--

CREATE TABLE `status` (
  `pk` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `status`
--

INSERT INTO `status` (`pk`, `descricao`) VALUES
(1, 'Ativo'),
(2, 'Em análise'),
(3, 'Bloqueado'),
(4, 'Excluído');

-- --------------------------------------------------------

--
-- Estrutura para tabela `status_transaction`
--

CREATE TABLE `status_transaction` (
  `pk` int(11) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `status_transaction`
--

INSERT INTO `status_transaction` (`pk`, `description`) VALUES
(1, 'PENDENTE'),
(2, 'AUTORIZADO'),
(3, 'NEGADO');

-- --------------------------------------------------------

--
-- Estrutura para tabela `transactions`
--

CREATE TABLE `transactions` (
  `pk` int(11) NOT NULL,
  `payer` int(11) NOT NULL,
  `payee` int(11) NOT NULL,
  `value` float NOT NULL,
  `key_transaction` varchar(255) NOT NULL,
  `fk_status_transaction` int(11) NOT NULL,
  `date_transaction` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_validation` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `types`
--

CREATE TABLE `types` (
  `pk` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `types`
--

INSERT INTO `types` (`pk`, `descricao`) VALUES
(1, 'Administrador'),
(2, 'Lojista'),
(3, 'Comum');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `pk` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `cpf_cnpj` varchar(18) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fk_type` int(11) NOT NULL,
  `fk_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`pk`, `name`, `cpf_cnpj`, `email`, `password`, `fk_type`, `fk_status`) VALUES
(5, 'Raul Cardoso', '85346209549', 'email@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 3, 1),
(6, 'Raul Cardoso', '18714315000166', 'email@hotmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 2, 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`pk`);

--
-- Índices de tabela `status_transaction`
--
ALTER TABLE `status_transaction`
  ADD PRIMARY KEY (`pk`);

--
-- Índices de tabela `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`pk`),
  ADD KEY `fk_status_transaction` (`fk_status_transaction`),
  ADD KEY `payer_pk_users` (`payer`),
  ADD KEY `payee_pk_users` (`payee`);

--
-- Índices de tabela `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`pk`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`pk`),
  ADD KEY `fk_tipo_pk_tipos` (`fk_type`),
  ADD KEY `fk_status_usuarios_pk_status` (`fk_status`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `status`
--
ALTER TABLE `status`
  MODIFY `pk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `status_transaction`
--
ALTER TABLE `status_transaction`
  MODIFY `pk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `transactions`
--
ALTER TABLE `transactions`
  MODIFY `pk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `types`
--
ALTER TABLE `types`
  MODIFY `pk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `pk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_status_transaction` FOREIGN KEY (`fk_status_transaction`) REFERENCES `status_transaction` (`pk`),
  ADD CONSTRAINT `payee_pk_users` FOREIGN KEY (`payee`) REFERENCES `users` (`pk`),
  ADD CONSTRAINT `payer_pk_users` FOREIGN KEY (`payer`) REFERENCES `users` (`pk`);

--
-- Restrições para tabelas `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_status_usuarios_pk_status` FOREIGN KEY (`fk_status`) REFERENCES `status` (`pk`),
  ADD CONSTRAINT `fk_type_pk_types` FOREIGN KEY (`fk_type`) REFERENCES `types` (`pk`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
