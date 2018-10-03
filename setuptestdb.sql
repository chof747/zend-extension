DROP DATABASE IF EXISTS zendtest;
CREATE DATABASE zendtest;

DROP USER 'zend-user'@'localhost';
CREATE USER `zend-user`@`localhost` IDENTIFIED BY "zend";
GRANT SELECT, UPDATE, DELETE, EXECUTE ON `zendtest`.* TO `zend-user`@`localhost`;

use `zendtest`;

CREATE TABLE `tauthor` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `NAME` varchar(80) CHARACTER SET latin1 DEFAULT NULL,
  `FAMILYNAME` varchar(80) CHARACTER SET latin1 DEFAULT NULL,
  `COUNTRY` char(2) CHARACTER SET latin1 DEFAULT 'AT',
  `DATE_BIRTH` date NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

CREATE TABLE `tbook` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `TITLE` char(30) NOT NULL DEFAULT '',
  `DESCRIPTION` varchar(80) DEFAULT NULL,
  `PUBLICATION_YEAR` int(10) unsigned DEFAULT NULL,
  `FK_AUTHOR_ID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `BOOK_AUTHOR` (`FK_AUTHOR_ID`),
  CONSTRAINT `BOOK_AUTHOR` FOREIGN KEY (`FK_AUTHOR_ID`) REFERENCES `tauthor` (`ID`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT; 
