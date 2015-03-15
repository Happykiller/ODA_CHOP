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
$params->interface = "phpsql/getMappingModule";
$params->arrayInput = array("scenarioCurrent", "moduleCurrent");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/getMappingModule.php?milis=123450&scenarioCurrent=BPAD-PRES&moduleCurrent=BPAD-PRES_INTRO

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "Select tabMappingScenario.`id`
    , tabScenario.`key` as 'scenarioCurrent'
    , tabModuleCurrent.`key` as 'moduleCurrent'
    , tabScenarioBefore.`key` as 'moduleBefore'
    , tabScenarioAfter.`key` as 'moduleAfter'
    from `tab_scenario_mapping` tabMappingScenario
        LEFT OUTER JOIN `tab_module_def` tabScenarioBefore
            ON tabMappingScenario.`link_before` = tabScenarioBefore.`id`
            AND tabScenarioBefore.`date_disable` is null
        LEFT OUTER JOIN `tab_module_def` tabScenarioAfter
            ON tabMappingScenario.`link_after` = tabScenarioAfter.`id`
            AND tabScenarioAfter.`date_disable` is null
    , `tab_scenario_def` tabScenario,`tab_module_def` tabModuleCurrent
    WHERE 1=1
    AND tabMappingScenario.`id_scenario` = tabScenario.`id`
    AND tabMappingScenario.`id_module` = tabModuleCurrent.`id`
    AND tabScenario.`key` = :scenarioCurrent
    AND tabModuleCurrent.`key` = :moduleCurrent
    AND tabMappingScenario.`date_disable` is null
    AND tabScenario.`date_disable` is null
    AND tabModuleCurrent.`date_disable` is null
;";
$params->bindsValue = [
    "moduleCurrent" => $CHOP_INTERFACE->inputs["moduleCurrent"]
    , "scenarioCurrent" => $CHOP_INTERFACE->inputs["scenarioCurrent"]
];
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);