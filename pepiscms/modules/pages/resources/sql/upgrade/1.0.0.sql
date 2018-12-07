SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE `cms_menu` CHANGE `item_url` `item_url` VARCHAR(512)  NOT NULL DEFAULT '' COMMENT 'Used only when page_id is NULL';
ALTER TABLE `cms_pages` ADD `page_image_path` VARCHAR(500) NOT NULL DEFAULT '';

SET FOREIGN_KEY_CHECKS=1;