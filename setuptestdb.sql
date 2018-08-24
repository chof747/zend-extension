DROP DATABASE IF EXISTS zendtest;
CREATE DATABASE zendtest;

GRANT USAGE ON *.* TO 'zend-user'@'localhost' IDENTIFIED BY 'password';
DROP USER 'zend-user'@'localhost';

-- DROP USER `zend-user`@`localhost`;
CREATE USER `zend-user`@`localhost` IDENTIFIED BY "zend";
GRANT SELECT, UPDATE, DELETE, EXECUTE ON `zendtest`.* TO `zend-user`@`localhost`;
