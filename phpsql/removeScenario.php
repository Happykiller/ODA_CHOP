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
$params->arrayInput = array("scenarioKey");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/removeScenario.php?milis=123450&scenarioKey=SCENARIO_START

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "UPDATE `tab_scenario_def` scenarioDef
    SET 
    `scenarioDef`.`date_disable` = NOW()
    , `scenarioDef`.`user_id_disable` = 1
    WHERE 1=1
    AND `scenarioDef`.`key` = :scenarioKey
;";
$params->typeSQL = OdaLibBd::SQL_SCRIPT;
$params->bindsValue = [
    "scenarioKey" => $CHOP_INTERFACE->inputs["scenarioKey"]
];
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);