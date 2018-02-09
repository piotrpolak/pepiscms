SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE TABLE IF NOT EXISTS `items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(64) NULL DEFAULT NULL,
  `description` VARCHAR(500) NULL DEFAULT NULL,
  `is_active` TINYINT(4) NULL DEFAULT NULL,
  `created_datetime` DATETIME NULL DEFAULT NULL,
  `updated_datetime` DATETIME NULL DEFAULT NULL,
  `image_path` VARCHAR(500) NULL DEFAULT NULL,
  `item_order` INT(11) NULL DEFAULT NULL,
  `attachment_path` VARCHAR(500) NULL DEFAULT NULL,
  `color_hex` VARCHAR(7) NULL DEFAULT NULL,
  `item_categories_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_items_item_categories_idx` (`item_categories_id` ASC),
  CONSTRAINT `fk_items_item_categories`
    FOREIGN KEY (`item_categories_id`)
    REFERENCES `item_categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `item_categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `item_tags` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `items_has_item_tags` (
  `item_id` INT(11) NOT NULL,
  `item_tag_id` INT(11) NOT NULL,
  PRIMARY KEY (`item_id`, `item_tag_id`),
  INDEX `fk_items_has_item_tags_item_tags1_idx` (`item_tag_id` ASC),
  INDEX `fk_items_has_item_tags_items1_idx` (`item_id` ASC),
  CONSTRAINT `fk_items_has_item_tags_items1`
    FOREIGN KEY (`item_id`)
    REFERENCES `items` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_items_has_item_tags_item_tags1`
    FOREIGN KEY (`item_tag_id`)
    REFERENCES `item_tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

INSERT INTO `item_categories` (`label`) VALUES('Default');

INSERT INTO `item_tags` (`name`) VALUES('Electronics');
INSERT INTO `item_tags` (`name`) VALUES('Books');
INSERT INTO `item_tags` (`name`) VALUES('Music');


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
