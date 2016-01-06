SET FOREIGN_KEY_CHECKS=0;
-- --------------------------------------------------------

INSERT INTO `@prefix@api_tab_menu` (`id`, `Description`, `Description_courte`, `id_categorie`, `Lien`) VALUES (NULL, 'qcm-manage.title', 'qcm-manage.title', '3', 'qcm-manage');

UPDATE `@prefix@api_tab_menu_rangs_droit` a
  INNER JOIN `@prefix@api_tab_menu` b
    ON b.`Lien` = 'qcm-manage'
  INNER JOIN `@prefix@api_tab_rangs` c
    ON c.`id` = a.`id_rang`
       AND c.`indice` in (1,10)
SET `id_menu` = concat(`id_menu`,b.`id`,';');

CREATE TABLE IF NOT EXISTS `@prefix@tab_qcm_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` int(11) NOT NULL,
  `creationDate` datetime NOT NULL,
  `name` varchar(250) NOT NULL,
  `lang` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author` (`author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `@prefix@tab_qcm_sessions_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(250) NOT NULL,
  `lastName` varchar(250) NOT NULL,
  `qcmId` int(11) NOT NULL,
  `qcmName` varchar(250) NOT NULL,
  `qcmLang` varchar(250) NOT NULL,
  `createDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `qcmId` (`qcmId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `tab_sessions_user_record`
--

CREATE TABLE IF NOT EXISTS `@prefix@tab_sessions_user_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `nbErrors` tinyint(2) NOT NULL,
  `sessionUserId` int(11) NOT NULL,
  `recordDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessionUserId` (`sessionUserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contraintes pour la table `tab_qcm_sessions`
--
ALTER TABLE `@prefix@tab_qcm_sessions` ADD CONSTRAINT `fk_userId` FOREIGN KEY (`author`) REFERENCES `@prefix@api_tab_utilisateurs` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `@prefix@tab_qcm_sessions_user` ADD  CONSTRAINT `fk_qcm_sessions` FOREIGN KEY (`qcmId`) REFERENCES `@prefix@tab_qcm_sessions`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `@prefix@tab_sessions_user_record` ADD  CONSTRAINT `fk_qcm_sessions_user` FOREIGN KEY (`sessionUserId`) REFERENCES `@prefix@tab_qcm_sessions_user`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- --------------------------------------------------------
SET FOREIGN_KEY_CHECKS=1;