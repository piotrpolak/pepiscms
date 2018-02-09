CREATE  TABLE IF NOT EXISTS `cms_journal` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `revision_datetime` DATETIME NOT NULL COMMENT 'UTC timestamp' ,
  `tag` VARCHAR(256) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `ref_id` INT(8) NOT NULL,
  `data_serialized` LONGTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `metadata_serialized` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  PRIMARY KEY (`id`) ,
  INDEX (`tag` ASC, `ref_id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;