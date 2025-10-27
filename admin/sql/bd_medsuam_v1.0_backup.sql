-- MySQL dump 10.13  Distrib 8.4.6, for Linux (x86_64)
--
-- Host: localhost    Database: bd_medsuam
-- ------------------------------------------------------
-- Server version	8.4.6

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `adm`
--

DROP TABLE IF EXISTS `adm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adm` (
  `id_adm` int NOT NULL AUTO_INCREMENT,
  `nome_adm` varchar(255) NOT NULL,
  `senha_adm` varchar(100) NOT NULL,
  `email_adm` varchar(255) NOT NULL,
  `cpf_adm` varchar(20) NOT NULL,
  `data_nasc_adm` date NOT NULL,
  `nivel_acesso` enum('super','adm') NOT NULL,
  `data_criacao` datetime NOT NULL,
  `ultimo_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id_adm`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adm`
--

LOCK TABLES `adm` WRITE;
/*!40000 ALTER TABLE `adm` DISABLE KEYS */;
INSERT INTO `adm` VALUES (1,'SUPER01','$2a$12$Odc6bp3kGVod5dyjzGjatuL36brYzWmCNNfR54utoHLSy167H2ou6','admin@medsuam.com','123.456.789-00','1990-01-01','super','2025-10-22 18:02:33','2025-10-27 16:38:37'),(4,'ADM01','$2y$10$pCOcANSM.Ela/hx/f/kideIZaL.CG3P9XcshqW2G8MO/FX03zKUzS','ADM01@gmail.com','14785236912','1996-01-08','adm','2025-10-27 14:17:22','2025-10-27 15:29:30');
/*!40000 ALTER TABLE `adm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assistente_medico`
--

DROP TABLE IF EXISTS `assistente_medico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assistente_medico` (
  `id_monitoramento` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `status_monitoramento` varchar(255) NOT NULL,
  `nivel_risco` varchar(45) NOT NULL,
  PRIMARY KEY (`id_monitoramento`,`id_paciente`),
  KEY `fk_MEDICO_FANTASMA_PACIENTE1_idx` (`id_paciente`),
  CONSTRAINT `fk_MEDICO_FANTASMA_PACIENTE1` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`id_paciente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assistente_medico`
--

LOCK TABLES `assistente_medico` WRITE;
/*!40000 ALTER TABLE `assistente_medico` DISABLE KEYS */;
/*!40000 ALTER TABLE `assistente_medico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atualizacao_adm`
--

DROP TABLE IF EXISTS `atualizacao_adm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atualizacao_adm` (
  `id_atualizacao` int NOT NULL AUTO_INCREMENT,
  `paciente_id_paciente` int DEFAULT NULL,
  `adm_id_adm` int DEFAULT NULL,
  `medico_id_medico` int DEFAULT NULL,
  `descricao_atualizacao` varchar(255) NOT NULL,
  `data_atualizacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_atualizacao`),
  KEY `fk_atualizacao_adm_adm1_idx` (`adm_id_adm`),
  KEY `fk_atualizacao_adm_medico1_idx` (`medico_id_medico`),
  KEY `fk_atualizacao_adm_paciente1` (`paciente_id_paciente`),
  CONSTRAINT `fk_atualizacao_adm_adm1` FOREIGN KEY (`adm_id_adm`) REFERENCES `adm` (`id_adm`),
  CONSTRAINT `fk_atualizacao_adm_medico1` FOREIGN KEY (`medico_id_medico`) REFERENCES `medico` (`id_medico`),
  CONSTRAINT `fk_atualizacao_adm_paciente1` FOREIGN KEY (`paciente_id_paciente`) REFERENCES `paciente` (`id_paciente`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atualizacao_adm`
--

LOCK TABLES `atualizacao_adm` WRITE;
/*!40000 ALTER TABLE `atualizacao_adm` DISABLE KEYS */;
INSERT INTO `atualizacao_adm` VALUES (1,NULL,1,NULL,'Adicionou administrador: Ronivaldo Domingues de Andrade','2025-10-22 18:16:06'),(2,NULL,1,NULL,'Adicionou administrador: Teste','2025-10-22 20:59:02'),(3,NULL,1,NULL,'Alterou status do paciente para: inativo','2025-10-22 21:24:13'),(4,NULL,1,NULL,'Alterou status do paciente para: ativo','2025-10-22 21:24:17'),(5,NULL,1,NULL,'Alterou status do paciente para: inativo','2025-10-22 21:25:08'),(6,NULL,1,NULL,'Alterou status do paciente para: ativo','2025-10-22 21:25:09'),(7,NULL,NULL,NULL,'Fez login no sistema','2025-10-24 15:46:01'),(8,NULL,NULL,NULL,'Fez login no sistema','2025-10-24 15:49:35'),(9,NULL,NULL,NULL,'Alterou status do paciente para: inativo','2025-10-27 14:04:32'),(10,NULL,NULL,NULL,'Alterou status do paciente para: ativo','2025-10-27 14:04:33'),(11,NULL,NULL,NULL,'Alterou status do paciente para: ativo','2025-10-27 14:09:09'),(12,NULL,NULL,NULL,'Editou administrador: Ronivaldo Domingues de Andrade','2025-10-27 14:15:59'),(13,NULL,NULL,NULL,'Editou administrador: Ronivaldo Domingues de Andrade','2025-10-27 14:16:06'),(14,NULL,NULL,NULL,'Editou administrador: Ronivaldo Domingues de Andrade','2025-10-27 14:16:36'),(15,NULL,NULL,NULL,'Editou administrador: Ronivaldo Domingues de Andrade','2025-10-27 14:16:53'),(16,NULL,NULL,NULL,'Adicionou administrador: Teste','2025-10-27 14:17:22'),(17,NULL,1,NULL,'Editou administrador: Ronivaldo Domingues de Andrade','2025-10-27 14:31:22'),(18,NULL,NULL,NULL,'Editou administrador: Teste','2025-10-27 14:33:35'),(19,NULL,NULL,NULL,'Editou administrador: Teste01','2025-10-27 14:33:47'),(20,NULL,NULL,NULL,'Editou administrador: Teste02','2025-10-27 14:33:53'),(21,NULL,NULL,NULL,'Excluiu administrador ID: 3','2025-10-27 14:33:56'),(22,NULL,NULL,NULL,'Excluiu paciente ID: 1','2025-10-27 14:37:02'),(23,16,NULL,NULL,'Adicionou paciente: Ronivaldo Domingues de Andrade','2025-10-27 14:37:47'),(24,16,NULL,NULL,'Alterou status do paciente para: inativo','2025-10-27 14:41:13'),(25,16,NULL,NULL,'Alterou status do paciente para: ativo','2025-10-27 14:41:21'),(26,16,NULL,NULL,'Alterou status do paciente para: ativo','2025-10-27 14:45:54'),(27,NULL,NULL,2,'Adicionou médico: Ronivaldo Domingues de Andrade','2025-10-27 14:46:19'),(28,16,NULL,NULL,'Editou paciente: Ronivaldo Domingues de Andrade','2025-10-27 15:02:20'),(29,16,NULL,NULL,'Editou paciente: Ronivaldo Domingues de Andrade','2025-10-27 15:06:07'),(30,16,NULL,NULL,'Editou paciente: Persona 01','2025-10-27 15:06:53'),(31,NULL,NULL,NULL,'Editou administrador: ADM01','2025-10-27 15:10:14'),(32,NULL,NULL,NULL,'Alterou senha do administrador ID: 4','2025-10-27 15:10:38'),(33,NULL,4,NULL,'Agendou consulta ID: 1','2025-10-27 15:12:39'),(34,NULL,4,NULL,'Alterou status da consulta ID: 1 para: realizada','2025-10-27 15:14:08'),(35,NULL,4,NULL,'Alterou status da consulta ID: 1 para: cancelada','2025-10-27 15:14:12'),(36,NULL,1,NULL,'Excluiu administrador ID: 2','2025-10-27 16:31:49'),(37,NULL,1,2,'Editou médico: Persona 01','2025-10-27 16:35:38'),(38,16,1,NULL,'Editou paciente: Persona 02','2025-10-27 16:36:45'),(39,NULL,1,NULL,'Alterou status da consulta ID: 1 para: agendada','2025-10-27 16:36:55'),(40,NULL,1,NULL,'Editou administrador: SUPER01','2025-10-27 16:37:40');
/*!40000 ALTER TABLE `atualizacao_adm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `condicao_saude`
--

DROP TABLE IF EXISTS `condicao_saude`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `condicao_saude` (
  `id_condicao` int NOT NULL AUTO_INCREMENT,
  `nome_condicao` varchar(80) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `nivel_risco` int NOT NULL,
  PRIMARY KEY (`id_condicao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `condicao_saude`
--

LOCK TABLES `condicao_saude` WRITE;
/*!40000 ALTER TABLE `condicao_saude` DISABLE KEYS */;
/*!40000 ALTER TABLE `condicao_saude` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consulta`
--

DROP TABLE IF EXISTS `consulta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consulta` (
  `id_consulta` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `id_medico` int NOT NULL,
  `data_consulta` date NOT NULL,
  `hora_consulta` datetime NOT NULL,
  `status` varchar(255) NOT NULL,
  `gravacao_link` varchar(45) NOT NULL,
  `link_videochamada` varchar(45) NOT NULL,
  PRIMARY KEY (`id_consulta`,`id_paciente`,`id_medico`),
  KEY `fk_CONSULTA_PACIENTE1_idx` (`id_paciente`),
  KEY `fk_CONSULTA_MEDICO1_idx` (`id_medico`),
  CONSTRAINT `fk_CONSULTA_MEDICO1` FOREIGN KEY (`id_medico`) REFERENCES `medico` (`id_medico`),
  CONSTRAINT `fk_CONSULTA_PACIENTE1` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`id_paciente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consulta`
--

LOCK TABLES `consulta` WRITE;
/*!40000 ALTER TABLE `consulta` DISABLE KEYS */;
INSERT INTO `consulta` VALUES (1,16,2,'2025-12-12','2025-12-12 15:35:00','agendada','','');
/*!40000 ALTER TABLE `consulta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `endereco`
--

DROP TABLE IF EXISTS `endereco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `endereco` (
  `id_endereco` int NOT NULL AUTO_INCREMENT,
  `cep` varchar(10) NOT NULL,
  `rua` varchar(50) NOT NULL,
  `numero` int NOT NULL,
  `complemento` varchar(45) DEFAULT NULL,
  `bairro` varchar(100) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `uf_endereco` varchar(2) NOT NULL,
  `paciente_id_paciente` int DEFAULT NULL,
  `medico_id_medico` int DEFAULT NULL,
  PRIMARY KEY (`id_endereco`),
  KEY `fk_endereco_paciente1_idx` (`paciente_id_paciente`),
  KEY `fk_endereco_medico1_idx` (`medico_id_medico`),
  CONSTRAINT `fk_endereco_medico1` FOREIGN KEY (`medico_id_medico`) REFERENCES `medico` (`id_medico`),
  CONSTRAINT `fk_endereco_paciente1` FOREIGN KEY (`paciente_id_paciente`) REFERENCES `paciente` (`id_paciente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `endereco`
--

LOCK TABLES `endereco` WRITE;
/*!40000 ALTER TABLE `endereco` DISABLE KEYS */;
/*!40000 ALTER TABLE `endereco` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `especialidade`
--

DROP TABLE IF EXISTS `especialidade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `especialidade` (
  `id_especialidade` int NOT NULL AUTO_INCREMENT,
  `id_medico` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  PRIMARY KEY (`id_especialidade`,`id_medico`),
  KEY `fk_ESPECIALIDADE_MEDICO1_idx` (`id_medico`),
  CONSTRAINT `fk_ESPECIALIDADE_MEDICO1` FOREIGN KEY (`id_medico`) REFERENCES `medico` (`id_medico`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `especialidade`
--

LOCK TABLES `especialidade` WRITE;
/*!40000 ALTER TABLE `especialidade` DISABLE KEYS */;
/*!40000 ALTER TABLE `especialidade` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laudo`
--

DROP TABLE IF EXISTS `laudo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laudo` (
  `id_laudo` int NOT NULL AUTO_INCREMENT,
  `id_consulta` int NOT NULL,
  `id_paciente` int NOT NULL,
  `id_medico` int NOT NULL,
  `data_emissao` date NOT NULL,
  `descricao` varchar(500) NOT NULL,
  `arquivo_pdf` longblob NOT NULL,
  PRIMARY KEY (`id_laudo`,`id_consulta`,`id_paciente`,`id_medico`),
  KEY `fk_LAUDO_CONSULTA1_idx` (`id_consulta`,`id_paciente`,`id_medico`),
  CONSTRAINT `fk_LAUDO_CONSULTA1` FOREIGN KEY (`id_consulta`, `id_paciente`, `id_medico`) REFERENCES `consulta` (`id_consulta`, `id_paciente`, `id_medico`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laudo`
--

LOCK TABLES `laudo` WRITE;
/*!40000 ALTER TABLE `laudo` DISABLE KEYS */;
/*!40000 ALTER TABLE `laudo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medico`
--

DROP TABLE IF EXISTS `medico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medico` (
  `id_medico` int NOT NULL AUTO_INCREMENT,
  `nome_medico` varchar(100) NOT NULL,
  `crm` varchar(20) NOT NULL,
  `senha_medico` varchar(255) NOT NULL,
  `email_medico` varchar(100) NOT NULL,
  `sexo_medico` varchar(45) NOT NULL,
  `data_nasc_medico` date NOT NULL,
  `cpf_medico` varchar(20) NOT NULL,
  `status_medico` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  `nome_social_medico` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_medico`),
  UNIQUE KEY `crm_UNIQUE` (`crm`),
  UNIQUE KEY `email_medico_UNIQUE` (`email_medico`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medico`
--

LOCK TABLES `medico` WRITE;
/*!40000 ALTER TABLE `medico` DISABLE KEYS */;
INSERT INTO `medico` VALUES (2,'Persona 01','37578000','$2y$10$74YGJnkAq.l3spkl8BXbLeVe1Tvz2ONKfkbIwTcube.jIih4PVYia','persona01@gmail.com','feminino','1965-12-06','12545635865','ativo','');
/*!40000 ALTER TABLE `medico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `missao_gamificada`
--

DROP TABLE IF EXISTS `missao_gamificada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `missao_gamificada` (
  `id_missao` int NOT NULL AUTO_INCREMENT,
  `id_perfil` int NOT NULL,
  `id_paciente` int NOT NULL,
  `id_condicao` int NOT NULL,
  `id_monitoramento` int NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `pontos` int NOT NULL,
  PRIMARY KEY (`id_missao`,`id_perfil`,`id_condicao`,`id_paciente`,`id_monitoramento`),
  KEY `fk_MISSAO_GAMIFICADA_PERFIL_GAMIFICADO1_idx` (`id_perfil`,`id_condicao`,`id_paciente`),
  KEY `fk_MISSAO_GAMIFICADA_ASSISTENTE_MEDICO1_idx` (`id_monitoramento`,`id_paciente`),
  CONSTRAINT `fk_MISSAO_GAMIFICADA_ASSISTENTE_MEDICO1` FOREIGN KEY (`id_monitoramento`, `id_paciente`) REFERENCES `assistente_medico` (`id_monitoramento`, `id_paciente`),
  CONSTRAINT `fk_MISSAO_GAMIFICADA_PERFIL_GAMIFICADO1` FOREIGN KEY (`id_perfil`, `id_condicao`, `id_paciente`) REFERENCES `perfil_gamificado` (`id_perfil`, `id_condicao`, `id_paciente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `missao_gamificada`
--

LOCK TABLES `missao_gamificada` WRITE;
/*!40000 ALTER TABLE `missao_gamificada` DISABLE KEYS */;
/*!40000 ALTER TABLE `missao_gamificada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paciente`
--

DROP TABLE IF EXISTS `paciente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paciente` (
  `id_paciente` int NOT NULL AUTO_INCREMENT,
  `nome_paciente` varchar(255) NOT NULL,
  `cpf_paciente` varchar(14) NOT NULL,
  `data_nasc_paciente` date NOT NULL,
  `email_paciente` varchar(100) NOT NULL,
  `senha_paciente` varchar(100) NOT NULL,
  `sexo_paciente` varchar(20) NOT NULL,
  `status_paciente` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  `peso` decimal(6,2) DEFAULT NULL,
  `altura` decimal(5,2) DEFAULT NULL,
  `tipo_sanguineo` varchar(10) DEFAULT NULL,
  `nome_social_paciente` varchar(100) DEFAULT NULL,
  `estado_civil` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_paciente`),
  UNIQUE KEY `email_UNIQUE` (`email_paciente`),
  UNIQUE KEY `cpf_UNIQUE` (`cpf_paciente`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paciente`
--

LOCK TABLES `paciente` WRITE;
/*!40000 ALTER TABLE `paciente` DISABLE KEYS */;
INSERT INTO `paciente` VALUES (16,'Persona 02','15486394758','1967-06-06','persona02@gmail.com','$2y$10$XTqGq8H3lSjxVr5WjzDFQeC83/fBm3xXxFdo9qIWgecyJRLwAOGe6','masculino','ativo',90.00,170.00,'B+','','Solteiro');
/*!40000 ALTER TABLE `paciente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paciente_condicao`
--

DROP TABLE IF EXISTS `paciente_condicao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paciente_condicao` (
  `id_paciente` int NOT NULL,
  `id_condicao` int NOT NULL,
  `data_inicio` date NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id_condicao`,`id_paciente`),
  KEY `fk_PACIENTE_CONDICAO_CONDICAO_SAUDE1_idx` (`id_condicao`),
  KEY `fk_PACIENTE_CONDICAO_PACIENTE1_idx` (`id_paciente`),
  CONSTRAINT `fk_PACIENTE_CONDICAO_CONDICAO_SAUDE1` FOREIGN KEY (`id_condicao`) REFERENCES `condicao_saude` (`id_condicao`),
  CONSTRAINT `fk_PACIENTE_CONDICAO_PACIENTE1` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`id_paciente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paciente_condicao`
--

LOCK TABLES `paciente_condicao` WRITE;
/*!40000 ALTER TABLE `paciente_condicao` DISABLE KEYS */;
/*!40000 ALTER TABLE `paciente_condicao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perfil_gamificado`
--

DROP TABLE IF EXISTS `perfil_gamificado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `perfil_gamificado` (
  `id_perfil` int NOT NULL AUTO_INCREMENT,
  `id_condicao` int NOT NULL,
  `id_paciente` int NOT NULL,
  `pontos` int NOT NULL,
  `medalhas` int NOT NULL,
  `nivel_atual` int NOT NULL,
  PRIMARY KEY (`id_perfil`,`id_condicao`,`id_paciente`),
  KEY `fk_PERFIL_GAMIFICADO_PACIENTE_CONDICAO1_idx` (`id_paciente`,`id_condicao`),
  KEY `fk_PERFIL_GAMIFICADO_PACIENTE_CONDICAO1` (`id_condicao`,`id_paciente`),
  CONSTRAINT `fk_PERFIL_GAMIFICADO_PACIENTE_CONDICAO1` FOREIGN KEY (`id_condicao`, `id_paciente`) REFERENCES `paciente_condicao` (`id_condicao`, `id_paciente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfil_gamificado`
--

LOCK TABLES `perfil_gamificado` WRITE;
/*!40000 ALTER TABLE `perfil_gamificado` DISABLE KEYS */;
/*!40000 ALTER TABLE `perfil_gamificado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rg`
--

DROP TABLE IF EXISTS `rg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rg` (
  `id_rg` int NOT NULL AUTO_INCREMENT,
  `numero_rg` varchar(10) DEFAULT NULL,
  `data_emissao` date DEFAULT NULL,
  `orgao_emissor` varchar(45) DEFAULT NULL,
  `uf_rg` varchar(2) DEFAULT NULL,
  `data_validade` date DEFAULT NULL,
  `medico_id_medico` int DEFAULT NULL,
  `paciente_id_paciente` int DEFAULT NULL,
  PRIMARY KEY (`id_rg`),
  UNIQUE KEY `numero_rg_UNIQUE` (`numero_rg`),
  KEY `fk_rg_medico1_idx` (`medico_id_medico`),
  KEY `fk_rg_paciente1_idx` (`paciente_id_paciente`),
  CONSTRAINT `fk_rg_medico1` FOREIGN KEY (`medico_id_medico`) REFERENCES `medico` (`id_medico`),
  CONSTRAINT `fk_rg_paciente1` FOREIGN KEY (`paciente_id_paciente`) REFERENCES `paciente` (`id_paciente`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rg`
--

LOCK TABLES `rg` WRITE;
/*!40000 ALTER TABLE `rg` DISABLE KEYS */;
INSERT INTO `rg` VALUES (2,'15457897','2024-01-01','DTRAN','RJ','0001-01-01',NULL,16),(3,'125456358','2023-12-08','DIT','RJ','2034-01-01',2,NULL);
/*!40000 ALTER TABLE `rg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telefone`
--

DROP TABLE IF EXISTS `telefone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telefone` (
  `id_telefone` int NOT NULL AUTO_INCREMENT,
  `medico_id_medico` int DEFAULT NULL,
  `paciente_id_paciente` int DEFAULT NULL,
  `dd` varchar(5) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  PRIMARY KEY (`id_telefone`),
  KEY `fk_telefone_medico1_idx` (`medico_id_medico`),
  KEY `fk_telefone_paciente1_idx` (`paciente_id_paciente`),
  CONSTRAINT `fk_telefone_medico1` FOREIGN KEY (`medico_id_medico`) REFERENCES `medico` (`id_medico`),
  CONSTRAINT `fk_telefone_paciente1` FOREIGN KEY (`paciente_id_paciente`) REFERENCES `paciente` (`id_paciente`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telefone`
--

LOCK TABLES `telefone` WRITE;
/*!40000 ALTER TABLE `telefone` DISABLE KEYS */;
INSERT INTO `telefone` VALUES (2,NULL,16,'55','21978452358'),(3,2,NULL,'55','2199653529');
/*!40000 ALTER TABLE `telefone` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-27 16:41:54
