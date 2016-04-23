-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(150) NOT NULL,
  `password` VARCHAR(64) NOT NULL,
  `password_iv` VARCHAR(64) NOT NULL,
  `firstname` VARCHAR(100) NOT NULL,
  `lastname` VARCHAR(100) NOT NULL,
  `birthdate` DATE NOT NULL,
  `status` SMALLINT UNSIGNED NOT NULL COMMENT '0: Inactive\n1: Active\n2: Deleted',
  `created_at` TIMESTAMP NULL,
  `modified_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `author`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `author` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `contact` VARCHAR(100) NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20) NULL,
  `created_at` TIMESTAMP NULL,
  `modified_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `document`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `document` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `author_id` INT UNSIGNED NOT NULL,
  `status` SMALLINT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `details_link` VARCHAR(150) NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `modified_at` TIMESTAMP NULL,
  `published_at` TIMESTAMP NULL,
  `filename` VARCHAR(50) NOT NULL COMMENT '0: Inactive\n1: Active\n2: Deleted',
  PRIMARY KEY (`id`),
  INDEX `fk_document_author_id_idx` (`author_id` ASC),
  CONSTRAINT `fk_document_author_id`
    FOREIGN KEY (`author_id`)
    REFERENCES `author` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tag`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tag` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `status` SMALLINT UNSIGNED NULL COMMENT '0: Inactive\n1: Active\n2: Deleted',
  `created_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `document_tag`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `document_tag` (
  `document_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`document_id`, `tag_id`),
  INDEX `fk_document_tag_tag_id_idx` (`tag_id` ASC),
  CONSTRAINT `fk_document_tag_document_id`
    FOREIGN KEY (`document_id`)
    REFERENCES `document` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_document_tag_tag_id`
    FOREIGN KEY (`tag_id`)
    REFERENCES `tag` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `purchase`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `purchase` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_purchase_user_id_idx` (`user_id` ASC),
  CONSTRAINT `fk_purchase_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `purchase_document`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `purchase_document` (
  `purchase_id` INT UNSIGNED NOT NULL,
  `document_id` INT UNSIGNED NOT NULL,
  `price` FLOAT NULL,
  PRIMARY KEY (`purchase_id`, `document_id`),
  INDEX `fk_purchase_document_document_id_idx` (`document_id` ASC),
  CONSTRAINT `fk_purchase_document_purchase_id`
    FOREIGN KEY (`purchase_id`)
    REFERENCES `purchase` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_purchase_document_document_id`
    FOREIGN KEY (`document_id`)
    REFERENCES `document` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `user_document_cart`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_document_cart` (
  `document_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`document_id`, `user_id`),
  INDEX `fk_user_document_cart_user_id_idx` (`user_id` ASC),
  CONSTRAINT `fk_user_document_cart_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_document_cart_document_id`
    FOREIGN KEY (`document_id`)
    REFERENCES `document` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
