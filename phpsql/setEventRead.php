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
$params->arrayInput = array("idEvent","idUser");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/setEventRead.php?milis=123450&idEvent=2&idUser=1

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "UPDATE `tab_event_elements`
    SET `date_read` = now(), `userId_read` = :idUser
    WHERE 1=1
    AND `id` = :idEvent
;";
$params->typeSQL = OdaLibBd::SQL_SCRIPT;
$params->bindsValue = [
    "idEvent" => $CHOP_INTERFACE->inputs["idEvent"],
    "idUser" => $CHOP_INTERFACE->inputs["idUser"]
];
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->value = $retour->nombre;
$CHOP_INTERFACE->addDataStr($params);