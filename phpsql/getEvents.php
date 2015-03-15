<?php
namespace Chop;
use stdClass, \Oda\OdaPrepareReqSql, \Oda\OdaLibBd;
//--------------------------------------------------------------------------
//Header
require("../API/php/header.php");
require("../php/ChopInterface.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new \Oda\OdaPrepareInterface();
$params->interface = "phpsql/getEvents";
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/getEvents.php?milis=123450

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "Select a.`id`, b.`key`, b.`description`, b.`type`, b.`date_record` as 'date_create_element', b.`user_id_record` as 'userId_create_element',
    c.`lang`, c.`date_record` as 'date_update_data', c.`user_id_record` as 'userId_update_data',
    d.`nom` as 'nom_creator', d.`prenom` as 'prenom_creator',
    e.`nom` as 'nom_updator', e.`prenom` as 'prenom_updator'
    FROM `tab_event_elements` a, `tab_elements` b, `tab_elements_datas` c, `api_tab_utilisateurs` d, `api_tab_utilisateurs` e
    WHERE 1=1
    AND a.`id_element` = b.`id`
    AND a.`id_element_data` = c.`id`
    AND b.`user_id_record` = d.`id`
    AND c.`user_id_record` = e.`id`
    AND a.`date_read` is null
    AND b.`date_disable` is null
    AND c.`date_disable` is null
    ORDER BY `date_update_data` DESC
;";
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

//---------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);