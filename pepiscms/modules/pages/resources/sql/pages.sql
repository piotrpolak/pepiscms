SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS menu2uri;
DROP TABLE IF EXISTS page2menu;
DROP TABLE IF EXISTS pages;
DROP TABLE IF EXISTS menu;


CREATE TABLE IF NOT EXISTS `sitelanguages` (
  `code` CHAR(2) NOT NULL ,
  `label` VARCHAR(32) NOT NULL ,
  `is_default` INT(1) NOT NULL ,
  `ci_language` CHAR(10) NOT NULL DEFAULT 'english' ,
  PRIMARY KEY (`code`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `remote_applications` (
  `code` CHAR(2) NOT NULL,
  PRIMARY KEY (`code`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `pages` (
  `page_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `page_uri` VARCHAR(196) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'URL component without extension and base url',
  `page_title` VARCHAR(512) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `page_description` VARCHAR(512) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `page_keywords` VARCHAR(512) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `user_id_created` INT UNSIGNED NOT NULL REFERENCES `users` (`user_id` ),
  `timestamp_created` TIMESTAMP NULL DEFAULT NULL ,
  `user_id_modified` INT UNSIGNED NOT NULL REFERENCES `users` (`user_id` ),
  `timestamp_modified` TIMESTAMP NULL DEFAULT NULL ,
  `page_contents` LONGTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `page_is_default` TINYINT NOT NULL DEFAULT 0 ,
  `page_is_displayed_in_sitemap` TINYINT NOT NULL DEFAULT 1 ,
  `language_code` CHAR(2) NOT NULL DEFAULT 'en' REFERENCES `sitelanguages` (`code` ),
  PRIMARY KEY (`page_id`) ,
  INDEX (`user_id_created` ASC) ,
  INDEX  (`user_id_modified` ASC) ,
  UNIQUE INDEX (`page_uri` ASC, `language_code` ASC) ,
  INDEX (`language_code` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `menu` (
  `item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `item_order` SMALLINT NOT NULL DEFAULT '0' ,
  `parent_item_id` INT UNSIGNED NULL DEFAULT NULL REFERENCES `menu` (`item_id` ),
  `item_name` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `language_code` CHAR(2) NOT NULL DEFAULT 'en' REFERENCES `sitelanguages` (`code` ),
  `item_url` VARCHAR(512) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'Used only when page_id is NULL',
  `page_id` INT UNSIGNED NULL REFERENCES `pages` (`page_id` ),
  PRIMARY KEY (`item_id`) ,
  UNIQUE INDEX (`item_name` ASC, `parent_item_id` ASC, `language_code` ASC) ,
  INDEX (`parent_item_id` ASC) ,
  INDEX (`language_code` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


INSERT INTO menu (item_id, item_order, parent_item_id, item_name, item_url) VALUES
(0, 0, NULL, 'Main Menu', '');

UPDATE menu SET item_id = '0' WHERE menu.item_id=1 LIMIT 1;

SET FOREIGN_KEY_CHECKS=1;