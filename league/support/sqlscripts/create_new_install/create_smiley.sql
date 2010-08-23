
CREATE TABLE `l_smiley` (
  `code` varchar(10) NOT NULL default '',
  `image` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`code`)
) TYPE=MyISAM COMMENT='Contains codes for smileys';

--
-- Dumping data for table `l_smiley`
--

INSERT INTO `l_smiley` VALUES ('x(','angry.gif');
INSERT INTO `l_smiley` VALUES (':)','happy.gif');
INSERT INTO `l_smiley` VALUES (':-)','happy.gif');
INSERT INTO `l_smiley` VALUES (':7','loveit.gif');
INSERT INTO `l_smiley` VALUES (';)','wink.gif');
INSERT INTO `l_smiley` VALUES (';-)','wink.gif');
INSERT INTO `l_smiley` VALUES (':(','sad.gif');
INSERT INTO `l_smiley` VALUES (':-(','sad.gif');
INSERT INTO `l_smiley` VALUES (':9','yum.gif');
INSERT INTO `l_smiley` VALUES (':*','kiss.gif');
INSERT INTO `l_smiley` VALUES (':+','flue.gif');
INSERT INTO `l_smiley` VALUES (':\\','cry.gif');
INSERT INTO `l_smiley` VALUES (':-\\','cry.gif');
INSERT INTO `l_smiley` VALUES (':o','shocked.gif');
INSERT INTO `l_smiley` VALUES (':p','tongue.gif');
INSERT INTO `l_smiley` VALUES (':-o','shocked.gif');
INSERT INTO `l_smiley` VALUES (':-p','tongue.gif');
INSERT INTO `l_smiley` VALUES (':-9','yum.gif');
INSERT INTO `l_smiley` VALUES (':-*','kiss.gif');
INSERT INTO `l_smiley` VALUES (':-+','flue.gif');
INSERT INTO `l_smiley` VALUES ('}->','devil.gif');
INSERT INTO `l_smiley` VALUES ('}>','devil.gif');
INSERT INTO `l_smiley` VALUES (':D','bigsmile.gif');
INSERT INTO `l_smiley` VALUES (':-D','bigsmile.gif');
INSERT INTO `l_smiley` VALUES ('x-(','angry.gif');
INSERT INTO `l_smiley` VALUES (':-7','loveit.gif');

