SET FOREIGN_KEY_CHECKS=0;

RENAME TABLE user2group TO cms_user_to_group;
RENAME TABLE modules TO cms_modules;
RENAME TABLE users TO cms_users;
RENAME TABLE logs TO cms_logs;
RENAME TABLE groups TO cms_groups;
RENAME TABLE group2entity TO cms_group_to_entity;

ALTER TABLE cms_modules ADD item_order_utilities SMALLINT NOT NULL DEFAULT '0';
ALTER TABLE cms_modules ADD parent_module_id INT(11) NULL DEFAULT NULL REFERENCES cms_modules(id);
ALTER TABLE cms_modules CHANGE item_order item_order_menu SMALLINT NOT NULL DEFAULT '0';

ALTER TABLE cms_users ADD hashing_salt VARCHAR(64) NOT NULL DEFAULT '';
ALTER TABLE cms_users ADD hashing_algorithm VARCHAR(6) NOT NULL DEFAULT 'md5';
ALTER TABLE cms_users ADD hashing_iterations INTEGER NOT NULL DEFAULT 1;
ALTER TABLE cms_users CHANGE password password VARCHAR(128) NOT NULL DEFAULT '';
ALTER TABLE cms_groups ADD group_code VARCHAR(64) NOT NULL DEFAULT '' AFTER group_name;

SET FOREIGN_KEY_CHECKS=1;