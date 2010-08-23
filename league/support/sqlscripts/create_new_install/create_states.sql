-- MySQL dump 8.22
--
-- Host: localhost    Database: webleague
---------------------------------------------------------
-- Server version	3.23.54

--
-- Table structure for table 'bzl_states'
--

DROP TABLE IF EXISTS bzl_states;
CREATE TABLE bzl_states (
  id smallint NOT NULL auto_increment,
  name char(30) NOT NULL default '',
  abbrev char(2) default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Dumping data for table 'bzl_states'
--


INSERT INTO bzl_states VALUES (1,'Alaska','AK');
INSERT INTO bzl_states VALUES (2,'Alabama','AL');
INSERT INTO bzl_states VALUES (3,'American Samoa','AS');
INSERT INTO bzl_states VALUES (4,'Arizona','AZ');
INSERT INTO bzl_states VALUES (5,'Arkansas','AR');
INSERT INTO bzl_states VALUES (6,'California','CA');
INSERT INTO bzl_states VALUES (7,'Colorado','CO');
INSERT INTO bzl_states VALUES (8,'Connecticut','CT');
INSERT INTO bzl_states VALUES (9,'Delaware','DE');
INSERT INTO bzl_states VALUES (10,'District of Columbia','DC');
INSERT INTO bzl_states VALUES (11,'Federated States of Micronesia','FM');
INSERT INTO bzl_states VALUES (12,'Florida','FL');
INSERT INTO bzl_states VALUES (13,'Georgia','GA');
INSERT INTO bzl_states VALUES (14,'Guam','GU');
INSERT INTO bzl_states VALUES (15,'Hawaii','HI');
INSERT INTO bzl_states VALUES (16,'Idaho','ID');
INSERT INTO bzl_states VALUES (17,'Illinois','IL');
INSERT INTO bzl_states VALUES (18,'Indiana','IN');
INSERT INTO bzl_states VALUES (19,'Iowa','IA');
INSERT INTO bzl_states VALUES (20,'Kansas','KS');
INSERT INTO bzl_states VALUES (21,'Kentucky','KY');
INSERT INTO bzl_states VALUES (22,'Louisiana','LA');
INSERT INTO bzl_states VALUES (23,'Maine','ME');
INSERT INTO bzl_states VALUES (24,'Marshall Islands','MH');
INSERT INTO bzl_states VALUES (25,'Maryland','MD');
INSERT INTO bzl_states VALUES (26,'Massachusetts','MA');
INSERT INTO bzl_states VALUES (27,'Michigan','MI');
INSERT INTO bzl_states VALUES (28,'Minnesota','MN');
INSERT INTO bzl_states VALUES (29,'Mississippi','MS');
INSERT INTO bzl_states VALUES (30,'Missouri','MO');
INSERT INTO bzl_states VALUES (31,'Montana','MT');
INSERT INTO bzl_states VALUES (32,'Nebraska','NE');
INSERT INTO bzl_states VALUES (33,'Nevada','NV');
INSERT INTO bzl_states VALUES (34,'New Hampshire','NH');
INSERT INTO bzl_states VALUES (35,'New Jersey','NJ');
INSERT INTO bzl_states VALUES (36,'New Mexico','NM');
INSERT INTO bzl_states VALUES (37,'New York','NY');
INSERT INTO bzl_states VALUES (38,'North Carolina','NC');
INSERT INTO bzl_states VALUES (39,'North Dakota','ND');
INSERT INTO bzl_states VALUES (40,'Northern Mariana Islands','MP');
INSERT INTO bzl_states VALUES (41,'Ohio','OH');
INSERT INTO bzl_states VALUES (42,'Oklahoma','OK');
INSERT INTO bzl_states VALUES (43,'Oregon','OR');
INSERT INTO bzl_states VALUES (44,'Palau','PW');
INSERT INTO bzl_states VALUES (45,'Pennsylvania','PA');
INSERT INTO bzl_states VALUES (46,'Puerto Rico','PR');
INSERT INTO bzl_states VALUES (47,'Rhode Island','RI');
INSERT INTO bzl_states VALUES (48,'South Carolina','SC');
INSERT INTO bzl_states VALUES (49,'South Dakota','SD');
INSERT INTO bzl_states VALUES (50,'Tennessee','TN');
INSERT INTO bzl_states VALUES (51,'Texas','TX');
INSERT INTO bzl_states VALUES (52,'Utah','UT');
INSERT INTO bzl_states VALUES (53,'Vermont','VT');
INSERT INTO bzl_states VALUES (54,'Virgin Islands','VI');
INSERT INTO bzl_states VALUES (55,'Virginia','VA');
INSERT INTO bzl_states VALUES (56,'Washington','WA');
INSERT INTO bzl_states VALUES (57,'West Virginia','WV');
INSERT INTO bzl_states VALUES (58,'Wisconsin','WI');
INSERT INTO bzl_states VALUES (59,'Wyoming','WY');
INSERT INTO bzl_states VALUES (60,'Armed Forces Africa','AE');
INSERT INTO bzl_states VALUES (61,'Armed Forces Americas','AA');
INSERT INTO bzl_states VALUES (62,'Armed Forces Canada','AE');
INSERT INTO bzl_states VALUES (63,'Armed Forces Europe','AE');
INSERT INTO bzl_states VALUES (64,'Armed Forces Middle East','AE');
INSERT INTO bzl_states VALUES (65,'Armed Forces Pacific','AP');

INSERT INTO bzl_states VALUES (100,'Alberta', null);
INSERT INTO bzl_states VALUES (101,'British Columbia', null);
INSERT INTO bzl_states VALUES (102,'Manitoba', null);
INSERT INTO bzl_states VALUES (103,'New Brunswick', null);
INSERT INTO bzl_states VALUES (104,'Newfoundland', null);
INSERT INTO bzl_states VALUES (105,'Northwest Territories', null);
INSERT INTO bzl_states VALUES (106,'Nova Scotia', null);
INSERT INTO bzl_states VALUES (107,'Nunavut', null);
INSERT INTO bzl_states VALUES (108,'Ontario', null);
INSERT INTO bzl_states VALUES (109,'Prince Edward Island', null);
INSERT INTO bzl_states VALUES (110,'Quebec', null);
INSERT INTO bzl_states VALUES (111,'Saskatchewan', null);
INSERT INTO bzl_states VALUES (112,'Yukon Territory', null);
