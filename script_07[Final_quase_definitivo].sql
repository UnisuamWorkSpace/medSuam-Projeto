-- Script MySQL compatível com phpMyAdmin
-- Banco de dados: bd_medsuam

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema bd_medsuam
-- -----------------------------------------------------
CREATE DATABASE IF NOT EXISTS `bd_medsuam` DEFAULT CHARACTER SET utf8;
USE `bd_medsuam`;

-- -----------------------------------------------------
-- Table paciente
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `paciente` (
  `id_paciente` INT NOT NULL AUTO_INCREMENT,
  `nome_paciente` VARCHAR(255) NOT NULL,
  `cpf_paciente` VARCHAR(14) NOT NULL,
  `data_nasc_paciente` DATE NOT NULL,
  `email_paciente` VARCHAR(100) NOT NULL,
  `senha_paciente` VARCHAR(100) NOT NULL,
  `peso` DECIMAL(6,2) NULL,
  `altura` DECIMAL(5,2) NULL,
  `tipo_sanguineo` VARCHAR(10) NULL,
  `nome_social_paciente` VARCHAR(100) NULL,
  `estado_civil` VARCHAR(20) NULL,
  `sexo_paciente` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`id_paciente`),
  UNIQUE (`email_paciente`),
  UNIQUE (`cpf_paciente`)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table medico
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `medico` (
  `id_medico` INT NOT NULL AUTO_INCREMENT,
  `nome_medico` VARCHAR(100) NOT NULL,
  `crm` VARCHAR(20) NOT NULL,
  `senha_medico` VARCHAR(255) NOT NULL,
  `email_medico` VARCHAR(100) NOT NULL,
  `sexo_medico` VARCHAR(45) NOT NULL,
  `nome_social_medico` VARCHAR(100) NULL,
  `data_nasc_medico` DATE NOT NULL,
  `cpf_medico` VARCHAR(14) NOT NULL,
  PRIMARY KEY (`id_medico`),
  UNIQUE (`crm`),
  UNIQUE (`email_medico`)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table especialidade
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `especialidade` (
  `id_especialidade` INT NOT NULL AUTO_INCREMENT,
  `id_medico` INT NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `descricao` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_especialidade`),
  INDEX `fk_ESPECIALIDADE_MEDICO1_idx` (`id_medico`),
  CONSTRAINT `fk_ESPECIALIDADE_MEDICO1`
    FOREIGN KEY (`id_medico`)
    REFERENCES `medico` (`id_medico`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table consulta
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `consulta` (
  `id_consulta` INT NOT NULL AUTO_INCREMENT,
  `id_paciente` INT NOT NULL,
  `id_medico` INT NOT NULL,
  `data_consulta` DATE NOT NULL,
  `hora_consulta` DATETIME NOT NULL,
  `status` VARCHAR(255) NOT NULL,
  `gravacao_link` VARCHAR(45) NOT NULL,
  `link_videochamada` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_consulta`),
  INDEX `fk_CONSULTA_PACIENTE1_idx` (`id_paciente`),
  INDEX `fk_CONSULTA_MEDICO1_idx` (`id_medico`),
  CONSTRAINT `fk_CONSULTA_PACIENTE1`
    FOREIGN KEY (`id_paciente`)
    REFERENCES `paciente` (`id_paciente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_CONSULTA_MEDICO1`
    FOREIGN KEY (`id_medico`)
    REFERENCES `medico` (`id_medico`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table condicao_saude
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `condicao_saude` (
  `id_condicao` INT NOT NULL AUTO_INCREMENT,
  `nome_condicao` VARCHAR(80) NOT NULL,
  `descricao` VARCHAR(255) NOT NULL,
  `nivel_risco` INT NOT NULL,
  PRIMARY KEY (`id_condicao`)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table paciente_condicao
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `paciente_condicao` (
  `id_paciente_condicao` INT NOT NULL AUTO_INCREMENT,
  `id_paciente` INT NOT NULL,
  `id_condicao` INT NOT NULL,
  `data_inicio` DATE NOT NULL,
  `status` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_paciente_condicao`),
  INDEX `fk_PACIENTE_CONDICAO_CONDICAO_SAUDE1_idx` (`id_condicao`),
  INDEX `fk_PACIENTE_CONDICAO_PACIENTE1_idx` (`id_paciente`),
  CONSTRAINT `fk_PACIENTE_CONDICAO_CONDICAO_SAUDE1`
    FOREIGN KEY (`id_condicao`)
    REFERENCES `condicao_saude` (`id_condicao`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_PACIENTE_CONDICAO_PACIENTE1`
    FOREIGN KEY (`id_paciente`)
    REFERENCES `paciente` (`id_paciente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table assistente_medico
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `assistente_medico` (
  `id_monitoramento` INT NOT NULL AUTO_INCREMENT,
  `id_paciente` INT NOT NULL,
  `status_monitoramento` VARCHAR(255) NOT NULL,
  `nivel_risco` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_monitoramento`),
  INDEX `fk_MEDICO_FANTASMA_PACIENTE1_idx` (`id_paciente`),
  CONSTRAINT `fk_MEDICO_FANTASMA_PACIENTE1`
    FOREIGN KEY (`id_paciente`)
    REFERENCES `paciente` (`id_paciente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table laudo
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `laudo` (
  `id_laudo` INT NOT NULL AUTO_INCREMENT,
  `id_consulta` INT NOT NULL,
  `data_emissao` DATE NOT NULL,
  `descricao` VARCHAR(500) NOT NULL,
  `arquivo_pdf` LONGBLOB NOT NULL,
  PRIMARY KEY (`id_laudo`),
  INDEX `fk_LAUDO_CONSULTA1_idx` (`id_consulta`),
  CONSTRAINT `fk_LAUDO_CONSULTA1`
    FOREIGN KEY (`id_consulta`)
    REFERENCES `consulta` (`id_consulta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table perfil_gamificado
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `perfil_gamificado` (
  `id_perfil` INT NOT NULL AUTO_INCREMENT,
  `id_paciente_condicao` INT NOT NULL,
  `pontos` INT NOT NULL,
  `medalhas` INT NOT NULL,
  `nivel_atual` INT NOT NULL,
  PRIMARY KEY (`id_perfil`),
  INDEX `fk_PERFIL_GAMIFICADO_PACIENTE_CONDICAO1_idx` (`id_paciente_condicao`),
  CONSTRAINT `fk_PERFIL_GAMIFICADO_PACIENTE_CONDICAO1`
    FOREIGN KEY (`id_paciente_condicao`)
    REFERENCES `paciente_condicao` (`id_paciente_condicao`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table missao_gamificada
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `missao_gamificada` (
  `id_missao` INT NOT NULL AUTO_INCREMENT,
  `id_perfil` INT NOT NULL,
  `id_monitoramento` INT NOT NULL,
  `descricao` VARCHAR(255) NOT NULL,
  `status` VARCHAR(255) NOT NULL,
  `pontos` INT NOT NULL,
  PRIMARY KEY (`id_missao`),
  INDEX `fk_MISSAO_GAMIFICADA_PERFIL_GAMIFICADO1_idx` (`id_perfil`),
  INDEX `fk_MISSAO_GAMIFICADA_MEDICO_FANTASMA1_idx` (`id_monitoramento`),
  CONSTRAINT `fk_MISSAO_GAMIFICADA_PERFIL_GAMIFICADO1`
    FOREIGN KEY (`id_perfil`)
    REFERENCES `perfil_gamificado` (`id_perfil`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_MISSAO_GAMIFICADA_MEDICO_FANTASMA1`
    FOREIGN KEY (`id_monitoramento`)
    REFERENCES `assistente_medico` (`id_monitoramento`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table telefone
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `telefone` (
  `id_telefone` INT NOT NULL AUTO_INCREMENT,
  `medico_id_medico` INT NULL,
  `paciente_id_paciente` INT NULL,
  `dd` VARCHAR(5) NOT NULL,
  `telefone` VARCHAR(15) NOT NULL,
  PRIMARY KEY (`id_telefone`),
  INDEX `fk_telefone_medico1_idx` (`medico_id_medico`),
  INDEX `fk_telefone_paciente1_idx` (`paciente_id_paciente`),
  CONSTRAINT `fk_telefone_medico1`
    FOREIGN KEY (`medico_id_medico`)
    REFERENCES `medico` (`id_medico`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_telefone_paciente1`
    FOREIGN KEY (`paciente_id_paciente`)
    REFERENCES `paciente` (`id_paciente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table endereco
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `endereco` (
  `id_endereco` INT NOT NULL AUTO_INCREMENT,
  `cep` VARCHAR(10) NOT NULL,
  `rua` VARCHAR(50) NOT NULL,
  `numero` INT NOT NULL,
  `complemento` VARCHAR(45) NULL,
  `bairro` VARCHAR(100) NOT NULL,
  `cidade` VARCHAR(100) NOT NULL,
  `uf_endereco` VARCHAR(2) NOT NULL,
  `paciente_id_paciente` INT NULL,
  `medico_id_medico` INT NULL,
  PRIMARY KEY (`id_endereco`),
  INDEX `fk_endereco_paciente1_idx` (`paciente_id_paciente`),
  INDEX `fk_endereco_medico1_idx` (`medico_id_medico`),
  CONSTRAINT `fk_endereco_paciente1`
    FOREIGN KEY (`paciente_id_paciente`)
    REFERENCES `paciente` (`id_paciente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_endereco_medico1`
    FOREIGN KEY (`medico_id_medico`)
    REFERENCES `medico` (`id_medico`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table rg
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rg` (
  `id_rg` INT NOT NULL AUTO_INCREMENT,
  `numero_rg` VARCHAR(10) NOT NULL,
  `data_emissao` DATE NOT NULL,
  `orgao_emissor` VARCHAR(45) NOT NULL,
  `uf_rg` VARCHAR(2) NOT NULL,
  `data_validade` DATE NOT NULL,
  `medico_id_medico` INT NULL,
  `paciente_id_paciente` INT NULL,
  PRIMARY KEY (`id_rg`),
  UNIQUE (`numero_rg`),
  INDEX `fk_rg_medico1_idx` (`medico_id_medico`),
  INDEX `fk_rg_paciente1_idx` (`paciente_id_paciente`),
  CONSTRAINT `fk_rg_medico1`
    FOREIGN KEY (`medico_id_medico`)
    REFERENCES `medico` (`id_medico`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rg_paciente1`
    FOREIGN KEY (`paciente_id_paciente`)
    REFERENCES `paciente` (`id_paciente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB;

-- Restaurando modos originais
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
