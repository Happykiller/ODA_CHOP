SET FOREIGN_KEY_CHECKS=0;
-- --------------------------------------------------------
ALTER TABLE `@prefix@tab_qcm_sessions_user` ADD `company` VARCHAR(500) NOT NULL AFTER `lastName`;
-- --------------------------------------------------------
SET FOREIGN_KEY_CHECKS=1;