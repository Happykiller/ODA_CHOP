SET FOREIGN_KEY_CHECKS=0;
-- --------------------------------------------------------
ALTER TABLE `@prefix@tab_qcm_sessions` ADD `version` VARCHAR(250) NOT NULL AFTER `name`;
ALTER TABLE `@prefix@tab_qcm_sessions` ADD `date` VARCHAR(250) NOT NULL ;
ALTER TABLE `@prefix@tab_qcm_sessions` ADD `desc` VARCHAR(500) NOT NULL ;
-- --------------------------------------------------------
SET FOREIGN_KEY_CHECKS=1;