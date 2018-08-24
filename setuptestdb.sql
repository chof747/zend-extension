DROP DATABASE IF EXISTS zendtest;
CREATE DATABASE zendtest;

DROP USER `zend-user`@`localhost`;
CREATE USER `zend-user`@`localhost` IDENTIFIED BY "zend";
GRANT SELECT, UPDATE, DELETE, EXECUTE ON `zendtest`.* TO `zend-user`@`localhost`;
