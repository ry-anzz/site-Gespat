
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `departamento`
--

CREATE TABLE `departamento` (
  `nome` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `invalidos`
--

CREATE TABLE `invalidos` (
  `fk_cod_patrimonio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `patrimonio`
--

CREATE TABLE `patrimonio` (
  `codigo` int(11) NOT NULL,
  `fabricante` varchar(20) NOT NULL,
  `cor` varchar(10) NOT NULL,
  `n_serie` varchar(45) NOT NULL,
  `descricao` varchar(45) NOT NULL,
  `fk_departamento_nome` varchar(45) DEFAULT NULL,
  `arquivoimg` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `chave` int(11) NOT NULL,
  `login` varchar(45) NOT NULL,
  `senha` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`chave`, `login`, `senha`) VALUES
(9911, 'usuario_exemplo', 'senha_exemplo');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`nome`);

--
-- Índices de tabela `invalidos`
--
ALTER TABLE `invalidos`
  ADD KEY `fk_cod_patrimonio` (`fk_cod_patrimonio`);

--
-- Índices de tabela `patrimonio`
--
ALTER TABLE `patrimonio`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `fk_departamento_nome` (`fk_departamento_nome`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`chave`);

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `invalidos`
--
ALTER TABLE `invalidos`
  ADD CONSTRAINT `fk_cod_patrimonio` FOREIGN KEY (`fk_cod_patrimonio`) REFERENCES `patrimonio` (`codigo`) ON DELETE CASCADE;

--
-- Restrições para tabelas `patrimonio`
--
ALTER TABLE `patrimonio`
  ADD CONSTRAINT `patrimonio_ibfk_1` FOREIGN KEY (`fk_departamento_nome`) REFERENCES `departamento` (`nome`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
