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
$params->arrayInput = array("id");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/getElementDetails.php?id=898

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "Select a.`key`, a.`description`, a.`type`, a.`date_record` as 'date_creation', c.`code_user` as 'user_creation', b.`lang`, b.`data`, b.`date_record`, d.`code_user` as 'user_record'
    from `tab_elements` a, `tab_elements_datas` b, `api_tab_utilisateurs` c, `api_tab_utilisateurs` d
    WHERE 1=1
    AND a.`id` = b.`parent_element_id`
    AND a.`user_id_record` = c.`id`
    AND b.`user_id_record` = d.`id`
    AND b.`id` = :id
;";
$params->bindsValue = [
    "id" => $CHOP_INTERFACE->inputs["id"]
];
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);