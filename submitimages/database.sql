CREATE TABLE `img_queue` (
  `queueid` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `bzid` MEDIUMINT UNSIGNED NOT NULL,
  `username` VARCHAR( 255 ) NOT NULL,
  `ipaddress` VARCHAR( 8 ) NOT NULL,
  `uploaderfirstname` VARCHAR( 255 ) NOT NULL,
  `uploaderlastname` VARCHAR( 255 ) NOT NULL,
  `filename` VARCHAR( 255 ) NOT NULL,
  `authorname` VARCHAR( 255 ) NOT NULL,
  `licenseid` TINYINT UNSIGNED NOT NULL COMMENT 'If 255, it is custom',
  `licensename` VARCHAR( 255 ) NOT NULL,
  `licenseurl` VARCHAR( 255 ) NOT NULL,
  `licensebody` MEDIUMTEXT NOT NULL,
  INDEX ( `bzid` )
) TYPE = MYISAM ;
