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
$params->sql = "CREATE TEMPORARY TABLE `tmp_events` as
SELECT * FROM (
Select a.`id` as 'id_event', b.`key`, b.`description`, b.`type`, a.`id_element_data` as 'id_data_new', (select MAX(c.`id`) from `tab_elements_datas` c where c.`parent_element_id` = a.`id_element` and c.`id` < a.`id_element_data`) as 'id_data_old'
FROM `tab_event_elements` a, `tab_elements` b
WHERE 1=1
AND a.`id_element` = b.`id`
AND a.`date_read` is null
) d
WHERE 1=1
AND d.`id_data_old` is not null
ORDER BY `id_event` desc
;";
$params->typeSQL = OdaLibBd::SQL_SCRIPT;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
//--------------------------------------------------------------------------

$params = new OdaPrepareReqSql();
$params->sql = "Select a.*, b.`lang`, b.`date_record` as 'date_update_data', b.`user_id_record` as 'userId_update_data', c.`code_user` as 'nom_updator'
    FROM `tmp_events` a, `tab_elements_datas` b
    LEFT OUTER JOIN `api_tab_utilisateurs` c
    ON b.`user_id_record` = c.`id`
    WHERE 1=1
    AND a.`id_data_new` = b.`id`
    ORDER BY `date_update_data` DESC
;";
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

//---------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);