<?php
namespace Chop;
use stdClass;
//--------------------------------------------------------------------------
//Header
require("../API/php/header.php");
require("../php/ChopInterface.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new \Oda\OdaPrepareInterface();
$params->interface = "phpsql/createElement";
$params->arrayInput = array("key", "type", "description", "userId");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/createElement.php?milis=123450&key=TITRE_SCENARIO42&userId=1&description=blabla&type=html

//--------------------------------------------------------------------------
$params = new \Oda\OdaPrepareReqSql();
$params->sql = "INSERT INTO  `tab_elements` (
        `key`, `description`, `type`, `date_record`, `user_id_record`
    )
    VALUES (
        :key , :description, :type, NOW(), :userId
    )
;";
$params->bindsValue = [
    "key" => $CHOP_INTERFACE->inputs["key"]
    , "description" => $CHOP_INTERFACE->inputs["description"]
    , "type" => $CHOP_INTERFACE->inputs["type"]
    , "userId" => $CHOP_INTERFACE->inputs["userId"]
];
$params->typeSQL = \Oda\OdaLibBd::SQL_INSERT_ONE;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

$params = new stdClass();
$params->label = "resultatInsertElement";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);