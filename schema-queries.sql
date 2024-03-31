-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema betterbuys
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `User`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `User` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `firstName` VARCHAR(255) NULL,
  `lastName` VARCHAR(255) NULL,
  `physicalAddress` VARCHAR(255) NULL,
  `emailAddress` VARCHAR(255) NOT NULL,
  `money` DECIMAL(14,2) NULL,
  `isAdministrator` TINYINT NOT NULL,
  `e164PhoneNumber` BIGINT UNSIGNED NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `PendingUser`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PendingUser` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `firstName` VARCHAR(255) NULL,
  `lastName` VARCHAR(255) NULL,
  `physicalAddress` VARCHAR(255) NULL,
  `emailAddress` VARCHAR(255) NOT NULL,
  `money` DECIMAL(14,2) NULL,
  `isAdministrator` TINYINT NOT NULL,
  `e164PhoneNumber` BIGINT UNSIGNED NULL,
  `password` VARCHAR(255) NOT NULL,
  `confirmationCode` INT NOT NULL,
  `dateCreated` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `Product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Product` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` MEDIUMTEXT NULL,
  `price` DECIMAL(14,2) NOT NULL,
  `salePrice` DECIMAL(14,2) NULL,
  `quantity` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Specification`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Specification` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Product_Specification_Map`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Product_Specification_Map` (
  `productId` INT UNSIGNED NOT NULL,
  `specificationId` INT UNSIGNED NOT NULL,
  `stringValue` VARCHAR(45) NULL,
  `dataType` VARCHAR(45) NOT NULL,
  `intValue` INT NULL,
  `floatValue` DOUBLE NULL,
  PRIMARY KEY (`productId`, `specificationId`),
  INDEX `specificationId_idx` (`specificationId` ASC) VISIBLE,
  CONSTRAINT `productspecmap_fk_productId`
    FOREIGN KEY (`productId`)
    REFERENCES `Product` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `productspecmap_fk_specificationId`
    FOREIGN KEY (`specificationId`)
    REFERENCES `Specification` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ProductImage`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ProductImage` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `filepath` VARCHAR(255) NOT NULL,
  `productId` INT UNSIGNED NOT NULL,
  `priority` INT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  INDEX `fk_productId_idx` (`productId` ASC) VISIBLE,
  CONSTRAINT `productimage_fk_productId`
    FOREIGN KEY (`productId`)
    REFERENCES `Product` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Category`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `parentCategory` INT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  INDEX `fk_parentCategory_idx` (`parentCategory` ASC) VISIBLE,
  CONSTRAINT `category_fk_parentCategory`
    FOREIGN KEY (`parentCategory`)
    REFERENCES `Category` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Category_Product_Map`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Category_Product_Map` (
  `categoryId` INT UNSIGNED NOT NULL,
  `productId` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`categoryId`, `productId`),
  INDEX `fk_productId_idx` (`productId` ASC) VISIBLE,
  CONSTRAINT `catproductmap_fk_categoryId`
    FOREIGN KEY (`categoryId`)
    REFERENCES `Category` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `catproductmap_fk_productId`
    FOREIGN KEY (`productId`)
    REFERENCES `Product` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Store`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Store` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `shipsToCustomers` TINYINT NULL,
  `physicalAddress` VARCHAR(45) NULL,
  `latitude` DOUBLE NULL,
  `longitude` DOUBLE NULL,
  `onlineOnly` TINYINT NULL,
  `name` VARCHAR(255) NOT NULL,
  `storeNumber` INT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Transaction`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Transaction` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` INT UNSIGNED NOT NULL,
  `productId` INT UNSIGNED NOT NULL,
  `purchaseDate` DATETIME NOT NULL,
  `shippingAddress` VARCHAR(255) NULL,
  `storeId` INT UNSIGNED NOT NULL,
  `purchaseType` VARCHAR(45) NOT NULL,
  `orderNumber` INT NOT NULL,
  `quantity` INT NOT NULL,
  `subtotal` DECIMAL(14,2) NOT NULL,
  `tax` DECIMAL(14,2) NOT NULL,
  `shippingFee` DECIMAL(14,2) NOT NULL,
  `fulfilled` TINYINT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  INDEX `fk_userId_idx` (`userId` ASC) VISIBLE,
  INDEX `fk_productId_idx` (`productId` ASC) VISIBLE,
  INDEX `fk_storeId_idx` (`storeId` ASC) VISIBLE,
  CONSTRAINT `transaction_fk_userId`
    FOREIGN KEY (`userId`)
    REFERENCES `User` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `transaction_fk_productId`
    FOREIGN KEY (`productId`)
    REFERENCES `Product` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `transaction_fk_storeId`
    FOREIGN KEY (`storeId`)
    REFERENCES `Store` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Inventory`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Inventory` (
  `productId` INT UNSIGNED NOT NULL,
  `storeId` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  PRIMARY KEY (`productId`, `storeId`),
  INDEX `fk_storeId_idx` (`storeId` ASC) VISIBLE,
  CONSTRAINT `inventory_fk_productId`
    FOREIGN KEY (`productId`)
    REFERENCES `Product` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `inventory_fk_storeId`
    FOREIGN KEY (`storeId`)
    REFERENCES `Store` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `DiscountCode`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `DiscountCode` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `productId` INT UNSIGNED NOT NULL,
  `usesRemaining` INT NULL,
  `userId` INT UNSIGNED NULL,
  `flatReduction` DECIMAL(14,2) NULL,
  `multiplierReduction` VARCHAR(45) NULL,
  `reductionType` VARCHAR(45) NOT NULL,
  `parentDiscountCodeId` INT UNSIGNED NULL,
  `code` VARCHAR(255) NULL,
  `type` VARCHAR(45) NOT NULL,
  `startDate` DATETIME NULL,
  `expireDate` DATETIME NULL,
  `enabled` TINYINT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  INDEX `fk_productId_idx` (`productId` ASC) VISIBLE,
  INDEX `fk_parentDiscountCodeId_idx` (`parentDiscountCodeId` ASC) VISIBLE,
  CONSTRAINT `discountcode_fk_productId`
    FOREIGN KEY (`productId`)
    REFERENCES `Product` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `discountcode_fk_parentDiscountCodeId`
    FOREIGN KEY (`parentDiscountCodeId`)
    REFERENCES `DiscountCode` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Review`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Review` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `body` MEDIUMTEXT NULL,
  `rating` INT NOT NULL,
  `userId` INT UNSIGNED NOT NULL,
  `showName` TINYINT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  INDEX `fk_userId_idx` (`userId` ASC) VISIBLE,
  CONSTRAINT `review_fk_userId`
    FOREIGN KEY (`userId`)
    REFERENCES `User` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ReviewImage`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ReviewImage` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reviewId` INT UNSIGNED NOT NULL,
  `filepath` VARCHAR(255) NOT NULL,
  `priority` INT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  INDEX `fk_reviewId_idx` (`reviewId` ASC) VISIBLE,
  CONSTRAINT `reviewimage_fk_reviewId`
    FOREIGN KEY (`reviewId`)
    REFERENCES `Review` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
