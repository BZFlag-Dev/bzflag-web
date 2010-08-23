CREATE TABLE IF NOT EXISTS `bzl_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `role_id` int(11) NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `bzl_permissions` VALUES (1, 2, 'adminlist::list_admins');
INSERT INTO `bzl_permissions` VALUES (2, 2, 'admintext::edit_rules');
INSERT INTO `bzl_permissions` VALUES (3, 2, 'dispchangelog::disp_changelog');
INSERT INTO `bzl_permissions` VALUES (4, 2, 'entermatch::enter_match');
INSERT INTO `bzl_permissions` VALUES (5, 2, 'entermatch::edit_match');
INSERT INTO `bzl_permissions` VALUES (6, 2, 'fights::match_detail');
INSERT INTO `bzl_permissions` VALUES (7, 2, 'links::edit_links');
INSERT INTO `bzl_permissions` VALUES (8, 2, 'news::edit_news');
INSERT INTO `bzl_permissions` VALUES (9, 2, 'bzforums::post_new');
INSERT INTO `bzl_permissions` VALUES (10, 3, 'bzforums::post_new');
INSERT INTO `bzl_permissions` VALUES (11, 2, 'bzforums::post_new');
INSERT INTO `bzl_permissions` VALUES (12, 3, 'bzforums::post_reply');
INSERT INTO `bzl_permissions` VALUES (13, 2, 'bzforums::post_reply');

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
