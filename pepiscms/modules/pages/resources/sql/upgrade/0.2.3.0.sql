SET FOREIGN_KEY_CHECKS=0;

RENAME TABLE sitelanguages TO cms_site_languages;
RENAME TABLE remote_applications TO cms_remote_applications;
RENAME TABLE menu TO cms_menu;
RENAME TABLE pages TO cms_pages;

SET FOREIGN_KEY_CHECKS=1;