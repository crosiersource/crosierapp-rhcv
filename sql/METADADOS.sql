SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `rhcv_cargo`;

CREATE TABLE `rhcv_cargo` (
  `id` bigint(20) AUTO_INCREMENT NOT NULL,
  `cargo` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,

  `version` int(11) DEFAULT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,
  `inserted` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_rhcv_cargo` (`cargo`),
  KEY `K_rhcv_cargo_estabelecimento` (`estabelecimento_id`),
  KEY `K_rhcv_cargo_user_inserted` (`user_inserted_id`),
  KEY `K_rhcv_cargo_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_rhcv_cargo_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_rhcv_cargo_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_rhcv_cargo_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;







DROP TABLE IF EXISTS `rhcv_cv`;

CREATE TABLE `rhcv_cv` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cargos_pretendidos` varchar(300) COLLATE utf8_swedish_ci DEFAULT NULL,
  `nome` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `cpf` char(11) COLLATE utf8_swedish_ci NOT NULL,
  `dt_nascimento` date DEFAULT NULL,
  `naturalidade` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `endereco_atual_logr` varchar(300) COLLATE utf8_swedish_ci DEFAULT NULL,
  `endereco_atual_numero` varchar(6) COLLATE utf8_swedish_ci DEFAULT NULL,
  `endereco_atual_compl` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `endereco_atual_bairro` varchar(300) COLLATE utf8_swedish_ci DEFAULT NULL,
  `endereco_atual_cidade` varchar(300) COLLATE utf8_swedish_ci DEFAULT NULL,
  `endereco_atual_uf` char(2) COLLATE utf8_swedish_ci DEFAULT NULL,
  `endereco_atual_tempo_resid` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `telefone1` varchar(20) COLLATE utf8_swedish_ci DEFAULT NULL,
  `telefone1_tipo` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `telefone2` varchar(20) COLLATE utf8_swedish_ci DEFAULT NULL,
  `telefone2_tipo` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `telefone3` varchar(20) COLLATE utf8_swedish_ci DEFAULT NULL,
  `telefone3_tipo` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `telefone4` varchar(20) COLLATE utf8_swedish_ci DEFAULT NULL,
  `telefone4_tipo` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `telefone5` varchar(20) COLLATE utf8_swedish_ci DEFAULT NULL,
  `telefone5_tipo` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `estado_civil` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `conjuge_nome` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `conjuge_profissao` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `tem_filhos` char(1) COLLATE utf8_swedish_ci DEFAULT NULL,
  `qtde_filhos` int(11) DEFAULT NULL,
  `pai_nome` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `pai_profissao` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `mae_nome` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `mae_profissao` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `referencia1_nome` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `referencia1_relacao` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `referencia1_telefone1` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `referencia1_telefone2` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `referencia2_nome` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `referencia2_relacao` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `referencia2_telefone1` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `referencia2_telefone2` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `ensino_fundamental_status` char(2) CHARACTER SET utf8 DEFAULT NULL,
  `ensino_fundamental_local` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `ensino_medio_status` char(2) CHARACTER SET utf8 DEFAULT NULL,
  `ensino_medio_local` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `ensino_superior_status` char(2) CHARACTER SET utf8 DEFAULT NULL,
  `ensino_superior_local` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `ensino_demais_obs` varchar(3000) COLLATE utf8_swedish_ci DEFAULT NULL,
  `conhece_a_empresa_tempo` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `eh_nosso_cliente` char(1) COLLATE utf8_swedish_ci DEFAULT NULL,
  `parente1_ficha_crediario_nome` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `parente2_ficha_crediario_nome` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `conhecido1_trabalhou_na_empresa` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `conhecido2_trabalhou_na_empresa` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `motivos_quer_trabalhar_aqui` varchar(3000) COLLATE utf8_swedish_ci DEFAULT NULL,
  `senha` varchar(200) COLLATE utf8_swedish_ci DEFAULT NULL,
  `email_confirmado` char(1) COLLATE utf8_swedish_ci NOT NULL DEFAULT 'N',
  `email_confirm_uuid` varchar(45) COLLATE utf8_swedish_ci NOT NULL,
  `senha_temp` varchar(200) COLLATE utf8_swedish_ci DEFAULT NULL,
  `ja_trabalhou` char(1) COLLATE utf8_swedish_ci DEFAULT NULL,
  `qtde_empregos` int(11) DEFAULT NULL,
  `foto` varchar(300) COLLATE utf8_swedish_ci DEFAULT NULL,
  `versao` int(11) DEFAULT NULL,
  `status` char(1) COLLATE utf8_swedish_ci DEFAULT NULL,

  `avaliacao` varchar(10000) COLLATE utf8_swedish_ci DEFAULT NULL,
  
  `version` int(11) DEFAULT NULL,
  `inserted` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_cv_cpf` (`cpf`,`versao`),
  UNIQUE KEY `UK_cv_email` (`versao`,`email`),
  KEY `K_rhcv_cv_estabelecimento` (`estabelecimento_id`),
  KEY `K_rhcv_cv_user_inserted` (`user_inserted_id`),
  KEY `K_rhcv_cv_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_rhcv_cv_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_rhcv_cv_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_rhcv_cv_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;




DROP TABLE IF EXISTS `rhcv_cv_cargos`;

CREATE TABLE `rhcv_cv_cargos` (
  `cv_id` bigint(20) NOT NULL,
  `cargo_id` bigint(20) NOT NULL,
  PRIMARY KEY (`cv_id`,`cargo_id`),
  KEY `fk_cv_cargos_cargo_idx` (`cargo_id`),
  CONSTRAINT `fk_cv_cargos_cargo` FOREIGN KEY (`cargo_id`) REFERENCES `rhcv_cargo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cv_cargos_cv` FOREIGN KEY (`cv_id`) REFERENCES `rhcv_cv` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;


DROP TABLE IF EXISTS `rhcv_cv_exper_profis`;

CREATE TABLE `rhcv_cv_exper_profis` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cv_id` bigint(20) DEFAULT NULL,
  `nome_empresa` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `local_empresa` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `nome_superior` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `cargo` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `horario` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `admissao` date DEFAULT NULL,
  `demissao` date DEFAULT NULL,
  `ultimo_salario` decimal(10,2) DEFAULT NULL,
  `beneficios` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `motivo_desligamento` varchar(3000) COLLATE utf8_swedish_ci DEFAULT NULL,

  `version` int(11) DEFAULT NULL,
  `inserted` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_cv_exper_profis` (`cv_id`,`nome_empresa`,`admissao`),
  KEY `K_rhcv_cv_exper_profis_estabelecimento` (`estabelecimento_id`),
  KEY `K_rhcv_cv_exper_profis_user_inserted` (`user_inserted_id`),
  KEY `K_rhcv_cv_exper_profis_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_rhcv_cv_exper_profis_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_rhcv_cv_exper_profis_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_rhcv_cv_exper_profis_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;



DROP TABLE IF EXISTS `rhcv_cv_filho`;

CREATE TABLE `rhcv_cv_filho` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cv_id` bigint(20) DEFAULT NULL,
  `nome` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `dt_nascimento` datetime DEFAULT NULL,
  `ocupacao` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `obs` varchar(3000) COLLATE utf8_swedish_ci DEFAULT NULL,
  
  `version` int(11) DEFAULT NULL,
  `inserted` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  `estabelecimento_id` bigint(20) NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_cv_filho` (`cv_id`,`nome`),
  KEY `K_rhcv_cv_filho_estabelecimento` (`estabelecimento_id`),
  KEY `K_rhcv_cv_filho_user_inserted` (`user_inserted_id`),
  KEY `K_rhcv_cv_filho_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_rhcv_cv_filho_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_rhcv_cv_filho_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_rhcv_cv_filho_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;



