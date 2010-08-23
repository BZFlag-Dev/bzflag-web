CREATE TABLE IF NOT EXISTS `bzl_roles` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE (`name`)
) TYPE=MyISAM AUTO_INCREMENT=10;


# NOTE: first 3 roles MUST be present, and IDs must match defines
#       in lib/common.php
INSERT INTO `bzl_roles` (`id`, `name`) VALUES (1, 'site admin');
INSERT INTO `bzl_roles` (`id`, `name`) VALUES (4, 'guest');
INSERT INTO `bzl_roles` (`id`, `name`) VALUES (3, 'player');

INSERT INTO `bzl_roles` (`id`, `name`) VALUES (2, 'referee');
INSERT INTO `bzl_roles` (`id`, `name`) VALUES (5, 'moderator');
INSERT INTO `bzl_roles` (`id`, `name`) VALUES (6, 'nonposter');
INSERT INTO `bzl_roles` (`id`, `name`) VALUES (7, 'bzflag admin');




CREATE TABLE IF NOT EXISTS `bzl_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `role_id` int(11) NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;


# nonposter perms:
INSERT INTO `bzl_permissions` (role_id, name) VALUES 
  (6, 'bzforums::show_roles');
  
# player perms:
INSERT INTO `bzl_permissions` (role_id, name) VALUES 
  (3, 'bzforums::post_new'),
  (3, 'bzforums::post_reply'),
  (3, 'bzforums::show_roles');
  
# referee perms:
INSERT INTO `bzl_permissions` (role_id, name) VALUES 
  (2, 'bzforums::post_new'),
  (2, 'bzforums::post_reply'),
  (2, 'adminlist::list_admins'),
  (2, 'admintext::edit_rules'),
  (2, 'dispchangelog::disp_changelog'),
  (2, 'entermatch::enter_match'),
  (2, 'entermatch::edit_match'),
  (2, 'contact::show'),
  (2, 'fights::match_detail'),
  (2, 'links::edit_links'),
  (2, 'news::edit_news'),
  (2, 'bzforums::show_roles');

# moderator perms:
INSERT INTO `bzl_permissions` (role_id, name) VALUES 
  (5, 'bzforums::post_new'),
  (5, 'bzforums::post_reply'),
  (5, 'bzforums::post_edit'),
  (5, 'bzforums::topic_view_deleted'),
  (5, 'bzforums::topic_lock'),
  (5, 'bzforums::topic_delete'),
  (5, 'bzforums::show_roles');
  
# bzflag admin:
INSERT INTO `bzl_permissions` (role_id, name) VALUES 
  (7, 'bzforums::post_new'),
  (7, 'bzforums::post_reply'),
  (7, 'adminlist::list_admins'),
  (7, 'admintext::edit_rules'),
  (7, 'dispchangelog::disp_changelog'),
  (7, 'entermatch::enter_match'),
  (7, 'entermatch::edit_match'),
  (7, 'contact::show'),
  (7, 'fights::match_detail'),
  (7, 'links::edit_links'),
  (7, 'news::edit_news'),
  (7, 'bzforums::show_roles'),
  (7, 'visitlog::visit_log');


ALTER TABLE `l_session` CHANGE `expire` `expire` DATETIME NULL DEFAULT NULL;

ALTER TABLE `l_forumthread` 
 ADD `status` ENUM( 'normal', 'locked', 'deleted' ) NOT NULL DEFAULT 'normal',
 ADD `status_by` INT, 
 ADD `status_at` DATETIME,
 ADD `is_sticky` TINYINT(0) default 0;


ALTER TABLE `l_forummsg` 
 ADD `status` ENUM( 'normal', 'edited', 'deleted' ) NOT NULL DEFAULT 'normal',
 ADD `status_by` INT, 
 ADD `status_at` DATETIME;

ALTER TABLE `l_player` ADD `role_id` INT NOT NULL AFTER `level` ;
UPDATE `l_player` SET `role_id` = 1 WHERE `level` = 'admin';
UPDATE `l_player` SET `role_id` = 4 WHERE `level` = 'referee';
UPDATE `l_player` SET `role_id` = 3 WHERE `level` = 'player';
ALTER TABLE `l_player` DROP `level`;

ALTER TABLE `l_session` ADD `refresh` ENUM( 'Y', 'N' ) DEFAULT 'N' NOT NULL ;

UPDATE bzl_siteconfig SET text = "2.01.00" where name = 'tablever';



