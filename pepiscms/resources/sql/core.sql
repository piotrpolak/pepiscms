CREATE TABLE IF NOT EXISTS `groups` (
  `group_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `group_name` VARCHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  PRIMARY KEY (`group_id`) ,
  UNIQUE INDEX `group_name` (`group_name` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `group2entity` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `group_id` INT NOT NULL REFERENCES `groups` (`group_id` ) ,
  `entity` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT 'Entity name, use lowercase and now spaces to define entities' ,
  `access` TINYINT NOT NULL DEFAULT 0 COMMENT '0 - none, 1 read, 2 - write, 3 - read and write, 4 full access,\nConstant, check AUTH constants for more iformation' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX (`group_id` ASC, `entity` ASC, `access` ASC) ,
  INDEX (`group_id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_email` VARCHAR(128) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `display_name` VARCHAR(128) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `password` VARCHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `password_last_changed_timestamp` TIMESTAMP NULL DEFAULT NULL COMMENT 'UTC timestamp' ,
  `status` TINYINT UNSIGNED NULL DEFAULT '0' ,
  `is_locked` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '1 - locked due to unsuccessfull login attempts' ,
  `is_root` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '1 - root' ,
  `groups_label` VARCHAR(256) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' COMMENT 'This is just a label for displaying in grid' ,
  `title` VARCHAR(128) NOT NULL ,
  `image_path` VARCHAR(256) NOT NULL DEFAULT '' COMMENT 'Path to user image' ,
  `birth_date` DATE NULL DEFAULT NULL ,
  `phone_number` VARCHAR(12) NOT NULL DEFAULT '' COMMENT 'Phone number, no spaces, country prefix' ,
  `note` VARCHAR(256) NOT NULL DEFAULT '' COMMENT 'User notes, another field' ,
  `alternative_email` VARCHAR(128) NOT NULL DEFAULT '' COMMENT 'Alternative, private email' ,
  `account_type` SMALLINT NOT NULL COMMENT 'Any value different from 0 states that the user is not a standard CMS user, thus cannot be root etc' ,
  PRIMARY KEY (`user_id`) ,
  UNIQUE INDEX (`user_email` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `user2group` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT NOT NULL REFERENCES `users` (`user_id` ),
  `group_id` INT NOT NULL REFERENCES `groups` (`group_id` ),
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX (`user_id` ASC, `group_id` ASC) ,
  INDEX (`user_id` ASC) ,
  INDEX (`group_id` ASC)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `sitelanguages` (
  `code` CHAR(2) NOT NULL ,
  `label` VARCHAR(32) NOT NULL ,
  `is_default` INT(1) NOT NULL ,
  `ci_language` CHAR(10) NOT NULL DEFAULT 'english' ,
  PRIMARY KEY (`code`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `modules` (
  `module_id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `label` VARCHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `description` VARCHAR(256) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `is_displayed_in_utilities` TINYINT NOT NULL DEFAULT '0' COMMENT '1 - TRUE, 0 - FALSE' ,
  `is_displayed_in_menu` TINYINT NOT NULL DEFAULT '0' COMMENT '1 - TRUE, 0 - FALSE' ,
  `is_configurable` TINYINT NOT NULL DEFAULT '0' ,
  `is_displayed_in_sitemap` TINYINT NOT NULL DEFAULT '0' ,
  `item_order` INT UNSIGNED NOT NULL DEFAULT '0' ,
  UNIQUE INDEX (`name` ASC) ,
  PRIMARY KEY (`module_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `message` VARCHAR(512) NULL DEFAULT '' ,
  `timestamp` TIMESTAMP NOT NULL COMMENT 'UTC timestamp of the event' ,
  `level` TINYINT NOT NULL COMMENT 'Log severity, a constant\n' ,
  `ip` VARCHAR(15) NOT NULL ,
  `user_id` INT NULL DEFAULT NULL ,
  `module` VARCHAR(32) NULL DEFAULT NULL ,
  `resource_id` INT NULL DEFAULT NULL COMMENT 'ID of entity, for example id of modified page' ,
  `collection` VARCHAR(32) NULL DEFAULT NULL COMMENT 'Collection along with resource name is used to keep track of the resource described in the log' ,
  `url` VARCHAR(512) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `remote_applications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(32) NOT NULL ,
  `description` VARCHAR(256) NULL DEFAULT NULL ,
  `maintainer_email` VARCHAR(64) NULL DEFAULT NULL ,
  `api_key` VARCHAR(32) NOT NULL ,
  `api_secret` VARCHAR(32) NOT NULL ,
  `status` TINYINT UNSIGNED NULL DEFAULT '0' ,
  UNIQUE INDEX (`name` ASC) ,
  UNIQUE INDEX (`api_key` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

INSERT INTO sitelanguages (code, label, is_default, ci_language ) VALUES
('en', 'English', 1, 'english');

INSERT INTO sitelanguages (code, label, is_default, ci_language ) VALUES
('pl', 'Polski', 1, 'polish');



ALTER TABLE `logs` CHANGE COLUMN `module` `module` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL  ;
ALTER TABLE `modules` CHANGE COLUMN `name` `name` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL  , CHANGE COLUMN `label` `label` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL;
ALTER TABLE `users` ADD COLUMN `user_login` VARCHAR(128) NULL DEFAULT NULL  AFTER `user_email`;
DELETE FROM group2entity WHERE entity=0 AND access=0;


ALTER TABLE `modules` DROP COLUMN `label`;
ALTER TABLE `modules` DROP COLUMN `description`;
ALTER TABLE `modules` DROP COLUMN `is_configurable`;
ALTER TABLE `modules` DROP COLUMN `is_displayed_in_sitemap`;


INSERT INTO `groups` VALUES (1,'Operator');
INSERT INTO `group2entity` VALUES (9,1,'backup',0),(1,1,'configuration',0),(4,1,'cross_domain_login',0),(16,1,'database',0),(13,1,'development_tool',0),(2,1,'file_system',0),(14,1,'group',0),(15,1,'logs',0),(5,1,'menu',0),(6,1,'module',1),(3,1,'own_account',4),(7,1,'page',0),(8,1,'remote_applications',0),(11,1,'system_cache',0),(10,1,'system_log',0),(17,1,'translations',0),(18,1,'user',0),(12,1,'utilities_view',0);
