-- MySQL Script generated by MySQL Workbench
-- Fri Sep 17 13:50:10 2021
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema basedobot
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema basedobot
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `basedobot` DEFAULT CHARACTER SET utf8mb4 ;
USE `basedobot` ;

-- -----------------------------------------------------
-- Table `basedobot`.`ingredientes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `basedobot`.`ingredientes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(30) NOT NULL,
  `url_imagem` VARCHAR(300) NULL DEFAULT NULL,
  `mensagem` VARCHAR(200) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `basedobot`.`ingredientes_pizzas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `basedobot`.`ingredientes_pizzas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_pizza` INT(11) NOT NULL,
  `id_ingrediente` INT(11) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `basedobot`.`ingredientes_usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `basedobot`.`ingredientes_usuario` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) NOT NULL,
  `id_ingrediente` INT(11) NOT NULL,
  `quantidade` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `basedobot`.`pizzas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `basedobot`.`pizzas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(60) NOT NULL,
  `url_imagem` VARCHAR(300) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `basedobot`.`usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `basedobot`.`usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nick` VARCHAR(30) NOT NULL,
  `sub` TINYINT(1) NULL DEFAULT 0,
  `streamer` TINYINT(1) NULL DEFAULT 0,
  `data_sub` DATE NULL DEFAULT NULL,
  `vip` TINYINT(1) NULL DEFAULT NULL,
  `twitch_id` BIGINT(20) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `basedobot`.`tentativas_fome`
-- -----------------------------------------------------
CREATE TABLE `tentativas_fome` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `pontos` float NOT NULL DEFAULT 0,
  `data_tentativa` date DEFAULT curdate(),
  `receita` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) 
ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;