
-- -----------------------------------------------------
-- Schema oingo
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `oingo` DEFAULT CHARACTER SET utf8 ;
USE `oingo`;
-- -----------------------------------------------------
-- Table `oingo`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `userid` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL,
  `userpwd` VARCHAR(45) NOT NULL,
  `state` VARCHAR(45) NULL,
  PRIMARY KEY (`userid`));
-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (101, 'Alice','123456', '');
INSERT INTO `user` VALUES (102, 'Bob','999999', 'lunch break');
INSERT INTO `user` VALUES (103, 'Raven','444555', '');

-- -----------------------------------------------------
-- Table `oingo`.`friendship`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `friendship`;
CREATE TABLE `friendship` (
  `userid` INT NOT NULL,
  `userid_receiver` INT NOT NULL,
  PRIMARY KEY (`userid`, `userid_receiver`),
  CONSTRAINT `fri_user_fk` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`));
-- ----------------------------
-- Records of friendship
-- ----------------------------
INSERT INTO `friendship` VALUES (101, 102);
INSERT INTO `friendship` VALUES (102, 101);
INSERT INTO `friendship` VALUES (102, 103);
INSERT INTO `friendship` VALUES (103, 102);
-- -----------------------------------------------------
-- Table `oingo`.`note`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `note`;
CREATE TABLE `note` (
  `noteid` INT NOT NULL AUTO_INCREMENT,
  `userid` INT NOT NULL,
  `title` VARCHAR(45) NOT NULL,
  `words` VARCHAR(100) NULL,
  `latitude` DOUBLE NOT NULL,
  `longitude` DOUBLE NOT NULL,
  `radius` INT NULL,
  `comAllow` INT NOT NULL,
  `accessRes` INT NOT NULL,
  PRIMARY KEY (`noteid`),
  CONSTRAINT `note_user_fk` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`));
-- ----------------------------
-- Records of friendship
-- ----------------------------
INSERT INTO `note` VALUES (1, 101, 'pizza', 'good pizza restaurant!', 116, 39, 1, 1, 1);
INSERT INTO `note` VALUES (2, 102, 'Great gym', 'It’s cheap.', 116.00001, 38.99999, 10, 1, 1);
INSERT INTO `note` VALUES (3, 103, 'shopping mall', 'need to buy Christmas gift.', 40, 74, 1, 1, 1);

-- -----------------------------------------------------
-- Table `oingo`.`schedule`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `schedule`;
CREATE TABLE `schedule` (
  `noteid` INT NOT NULL,
  `type` INT NOT NULL,
  `starttime` DATETIME NOT NULL,
  `endtime` DATETIME NOT NULL,
  `weekday` INT NOT NULL,
  PRIMARY KEY (`noteid`),
  CONSTRAINT `sche_note_fk` FOREIGN KEY (`noteid`) REFERENCES `note` (`noteid`));
-- ----------------------------
-- Records of schedule
-- ----------------------------
INSERT INTO `schedule` VALUES (1, 2, '2018-11-18 9:00:00', '2018-12-30 9:00:00', 0);
INSERT INTO `schedule` VALUES (2, 3, '1970-1-1 17:00:00', '1970-1-1 19:00:00', 4);
INSERT INTO `schedule` VALUES (3, 2, '2018-12-1 9:00:00', '2018-12-24 18:00:00', 0);

-- -----------------------------------------------------
-- Table `oingo`.`tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `tagid` INT NOT NULL AUTO_INCREMENT,
  `tagname` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`tagid`));
-- ----------------------------
-- Records of tag
-- ----------------------------
INSERT INTO `tag` VALUES (1000, '#me');
INSERT INTO `tag` VALUES (1001, '#tourism');
INSERT INTO `tag` VALUES (1002, '#shopping');
INSERT INTO `tag` VALUES (1003, '#food');
INSERT INTO `tag` VALUES (1004, '#sports');

-- -----------------------------------------------------
-- Table `oingo`.`note_tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `note_tag`;
CREATE TABLE `note_tag` (
  `tagid` INT NOT NULL,
  `noteid` INT NOT NULL,
  PRIMARY KEY (`tagid`, `noteid`),
  CONSTRAINT `nt_tag_fk` FOREIGN KEY (`tagid`) REFERENCES `tag` (`tagid`),
  CONSTRAINT `nt_note_fk` FOREIGN KEY (`noteid`) REFERENCES `note` (`noteid`));
-- ----------------------------
-- Records of note_tag
-- ----------------------------
INSERT INTO `note_tag` VALUES (1003, 1);
INSERT INTO `note_tag` VALUES (1004, 2);
INSERT INTO `note_tag` VALUES (1000, 3);
INSERT INTO `note_tag` VALUES (1002, 3);

-- -----------------------------------------------------
-- Table `oingo`.`comments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `userid` INT NOT NULL,
  `noteid` INT NOT NULL,
  `comtime` DATETIME NOT NULL,
  `words` VARCHAR(100) NULL,
  PRIMARY KEY (`userid`, `noteid`, `comtime`),
  CONSTRAINT `com_user_fk` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`),
  CONSTRAINT `com_note_fk` FOREIGN KEY (`noteid`) REFERENCES `note` (`noteid`));
-- ----------------------------
-- Records of comments
-- ----------------------------
INSERT INTO `comments` VALUES (102, 1, '2018-12-10 18:00:00', 'Very nice!');

-- -----------------------------------------------------
-- Table `oingo`.`friendrequest`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `friendrequest`;
CREATE TABLE `friendrequest` (
  `userid_sender` INT NOT NULL,
  `userid_receiver` INT NOT NULL,
  `words` VARCHAR(100) NULL,
  PRIMARY KEY (`userid_sender`, `userid_receiver`),
  CONSTRAINT `fr_userid_fk` FOREIGN KEY (`userid_sender`) REFERENCES `user` (`userid`));
-- ----------------------------
-- Records of friendrequest
-- ----------------------------
INSERT INTO `friendrequest` VALUES (103, 101, 'Lets be friends!');

-- -----------------------------------------------------
-- Table `oingo`.`filter`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `filter`;
CREATE TABLE `filter` (
  `filterid` INT NOT NULL AUTO_INCREMENT,
  `userid` INT NOT NULL,
  `filtername` VARCHAR(45) NOT NULL,
  `state` VARCHAR(45) NULL,
  `latitude` DOUBLE NOT NULL,
  `longitude` DOUBLE NOT NULL,
  `radius` INT NULL,
  PRIMARY KEY (`filterid`),
  CONSTRAINT `filter_user_fk` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`));
-- ----------------------------
-- Records of filter
-- ----------------------------
INSERT INTO `filter` VALUES (1, 101, 'EAT TIME', '', 116, 39, 10);
INSERT INTO `filter` VALUES (2, 102, 'a filter', '', 116, 39, 10);
INSERT INTO `filter` VALUES (3, 103, 'a reminder', 'leisure time', 40, 74, 2 );

-- -----------------------------------------------------
-- Table `oingo`.`stamps`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `stamps`;
CREATE TABLE `stamps` (
  `userid` INT NOT NULL,
  `time` DATETIME NOT NULL,
  `latitude` DOUBLE NOT NULL,
  `longitude` DOUBLE NOT NULL,
  PRIMARY KEY (`userid`, `time`),
  CONSTRAINT `stamp_user_fk` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`));
-- ----------------------------
-- Records of stamps
-- ----------------------------

-- -----------------------------------------------------
-- Table `oingo`.`filter_tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `filter_tag`;
CREATE TABLE `filter_tag` (
  `filterid` INT NOT NULL,
  `tagid` INT NOT NULL,
  PRIMARY KEY (`filterid`, `tagid`),
  CONSTRAINT `tag_fk` FOREIGN KEY (`tagid`) REFERENCES `tag` (`tagid`),
  CONSTRAINT `filter_fk` FOREIGN KEY (`filterid`) REFERENCES `filter` (`filterid`));
-- ----------------------------
-- Records of filter_tag
-- ----------------------------
INSERT INTO `filter_tag` VALUES (1, 1003);
INSERT INTO `filter_tag` VALUES (2, 1002);
INSERT INTO `filter_tag` VALUES (2, 1003);
INSERT INTO `filter_tag` VALUES (2, 1004);
INSERT INTO `filter_tag` VALUES (3, 1000);

-- -----------------------------------------------------
-- Table `oingo`.`filterSchedule`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `filterSchedule`;
CREATE TABLE `filterSchedule` (
  `filterid` INT NOT NULL,
  `type` INT NOT NULL,
  `starttime` DATETIME NOT NULL,
  `endtime` DATETIME NOT NULL,
  `weekday` INT NOT NULL,
  PRIMARY KEY (`filterid`),
  CONSTRAINT `fs_filter_fk` FOREIGN KEY (`filterid`) REFERENCES `filter` (`filterid`));
-- ----------------------------
-- Records of filterSchedule
-- ----------------------------
INSERT INTO `filterSchedule` VALUES (1, 3, '1970-1-1 1:00:00', '1970-1-1 23:00:00', 6);
INSERT INTO `filterSchedule` VALUES (2, 2, '2018-11-18 9:00:00', '2018-12-30 9:00:00', 0);
INSERT INTO `filterSchedule` VALUES (3, 1, '1970-1-1 0:00:00', '1970-1-1 0:00:00', 0);
