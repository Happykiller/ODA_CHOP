SET FOREIGN_KEY_CHECKS=0;
-- --------------------------------------------------------
ALTER TABLE `@prefix@tab_qcm_sessions` ADD `title` VARCHAR(500) NOT NULL , ADD `hours` VARCHAR(500) NOT NULL , ADD `duration` VARCHAR(500) NOT NULL , ADD `details` VARCHAR(500) NOT NULL, ADD `location` VARCHAR(500) NOT NULL ;
-- --------------------------------------------------------
SET FOREIGN_KEY_CHECKS=1;