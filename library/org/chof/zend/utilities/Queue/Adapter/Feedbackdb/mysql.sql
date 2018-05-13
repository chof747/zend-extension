SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- DROP TABLE IF EXISTS `{queue}`.`queue`;
CREATE TABLE IF NOT EXISTS `{queue}`.`queue` (
  `queue_id` int(10) unsigned NOT NULL auto_increment,
  `queue_name` varchar(100) NOT NULL,
  `timeout` smallint(5) unsigned NOT NULL default '30',
  PRIMARY KEY  (`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- DROP TABLE IF EXISTS `{queue}`.`message`;
CREATE TABLE IF NOT EXISTS `{queue}`.`message` (
  `message_id` bigint(20) unsigned NOT NULL auto_increment,
  `queue_id` int(10) unsigned NOT NULL,
  `handle` char(32) default NULL,
  `body` varchar(8192) NOT NULL,
  `md5` char(32) NOT NULL,
  `timeout` decimal(14,4) unsigned default NULL,
  `created` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`message_id`),
  UNIQUE KEY `message_handle` (`handle`),
  KEY `message_queueid` (`queue_id`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`queue_id`) REFERENCES `{queue}`.`queue` (`queue_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `{queue}`.`status` (
  `message_id`   bigint(20) unsigned NOT NULL,
  `title`        char(80) default NULL,
  `created`      int(10) unsigned NOT NULL,
  `accepted`     int(10) unsigned default NULL,
  `firstupdate`  int(10) unsigned default NULL,
  `latestupdate` int(10) unsigned default NULL,
  `closed`       int(10) unsigned default NULL,
  `status`       char(10) default 'backlog',
  `complete`     int(10) unsigned default NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
