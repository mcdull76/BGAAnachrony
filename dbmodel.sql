
-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- anachrony implementation : © <Nicolas Gocel> <nicolas.gocel@gmail.com>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql


ALTER TABLE `player` ADD `leader` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `path` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `chronology` INT UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `player` ADD `evacuation` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `anomalies` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `temporal` INT UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `player` ADD `moral` INT UNSIGNED NOT NULL DEFAULT '4';
ALTER TABLE `player` ADD `res1` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `res2` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `res3` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `res4` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `res5` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `res6` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `vp` INT NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `evacuation_side` INT NOT NULL DEFAULT '1';

 CREATE TABLE IF NOT EXISTS `vortex` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   `player_id` int(10) NULL,
   `path` INT UNSIGNED NOT NULL DEFAULT '0',
   `type` int(2) unsigned NOT NULL,
   `location` int(2) unsigned NOT NULL DEFAULT '0',
   `location_arg` int(2) unsigned NOT NULL DEFAULT '0',
   `chosen` int(1) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
  
 CREATE TABLE IF NOT EXISTS `worker` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   `player_id` int(10) NULL,
   `type` int(2) unsigned NOT NULL,
   `location` varchar(50) NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
 
 CREATE TABLE IF NOT EXISTS `breakthrough` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   `player_id` int(10) NULL,
   `shape` int(2) unsigned NOT NULL,
   `icon` int(2) unsigned NOT NULL,
   `location` varchar(50) NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
 
 CREATE TABLE IF NOT EXISTS `exosuit` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   `player_id` int(10) NULL,
   `path` INT UNSIGNED NOT NULL DEFAULT '0',
   `location` varchar(50) NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
  
 CREATE TABLE IF NOT EXISTS `path` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   `player_id` int(10) NULL,
   `path` INT UNSIGNED NOT NULL DEFAULT '0',
   `location` varchar(50) NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
 
 CREATE TABLE IF NOT EXISTS `building` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   `player_id` int(10) NULL,
   `category` int(2) unsigned NOT NULL,
   `type` int(3) unsigned NOT NULL,
   `location` int(2) unsigned NOT NULL DEFAULT '1',
   `location_arg` int(2) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
  
 CREATE TABLE IF NOT EXISTS `endgame` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   `type` int(2) unsigned NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
    
 CREATE TABLE IF NOT EXISTS `blocked` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   `type` int(2) unsigned NOT NULL DEFAULT '0',
   `location` varchar(50) NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
  
 CREATE TABLE IF NOT EXISTS `resourcesdrawn` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
 
 CREATE TABLE IF NOT EXISTS `recruitsdrawn` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   `type` int(2) unsigned NOT NULL DEFAULT '1',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
    
 CREATE TABLE IF NOT EXISTS `mineres` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   `type` int(2) unsigned NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
 
 CREATE TABLE IF NOT EXISTS `superproject` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   `type` int(2) unsigned NOT NULL,
   `player_id` int(10) NULL,
   `category` int(3) unsigned NULL,
   `location` int(2) unsigned NOT NULL DEFAULT '1',
   `visible` int(1) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
  
CREATE TABLE IF NOT EXISTS `pending` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `player_id` int(10) NULL,  
  `worker_id` varchar(50) NULL, 
  `target_id` varchar(50) NULL,
  `extra` varchar(50) NULL,
  `function` varchar(50) NULL,
  `arg` varchar(50) NULL,  
  `arg2` varchar(50) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000 ;