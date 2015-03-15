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
$params->arrayInput = array("scenario","module");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/removeModuleScenario.php?milis=123450&scenario=SCENARIO_START&module=MODULETEST

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "Select scenarioMapping.`link_before`, scenarioMapping.`link_after`
    from `tab_scenario_mapping` scenarioMapping
        LEFT JOIN `tab_scenario_def` scenarioDef
        ON scenarioMapping.`id_scenario` = scenarioDef.`id`
        LEFT JOIN `tab_module_def` moduleDef
        ON scenarioMapping.`id_module` = moduleDef.`id`
    WHERE 1=1
    AND scenarioDef.`key` = :scenario
    AND moduleDef.`key` = :module
;";
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$params->bindsValue = [
    "scenario" => $CHOP_INTERFACE->inputs["scenario"],
    "module" => $CHOP_INTERFACE->inputs["module"]
];
$resultats = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
$idModuleBefore = $resultats->data->link_before;
$idModuleAfter = $resultats->data->link_after;

if($idModuleBefore != null){
    $params = new OdaPrepareReqSql();
    $params->sql = "UPDATE `tab_scenario_mapping` scenarioMapping
	LEFT JOIN `tab_scenario_def` scenarioDef
	ON scenarioMapping.`id_scenario` = scenarioDef.`id`
	LEFT JOIN `tab_module_def` moduleDef
	ON scenarioMapping.`id_module` = moduleDef.`id`
	LEFT JOIN `tab_module_def` defModuleBefore
	ON scenarioMapping.`link_before` = defModuleBefore.`id`
	LEFT JOIN `tab_module_def` defModuleAfter
	ON scenarioMapping.`link_after` = defModuleAfter.`id`
    SET 
        scenarioMapping.`link_after` = :link_after
    WHERE 1=1
    AND scenarioDef.`key` = :scenario
    AND scenarioMapping.`id_module` = :link_before
    ;";
    $params->typeSQL = OdaLibBd::SQL_SCRIPT;
    $params->bindsValue = [
        "scenario" => $CHOP_INTERFACE->inputs["scenario"],
        "link_before" => $idModuleBefore,
        "link_after" => $idModuleAfter
    ];
    $retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
}

if($idModuleAfter != null){
    $params = new OdaPrepareReqSql();
    $params->sql = "UPDATE `tab_scenario_mapping` scenarioMapping
	LEFT JOIN `tab_scenario_def` scenarioDef
	ON scenarioMapping.`id_scenario` = scenarioDef.`id`
	LEFT JOIN `tab_module_def` moduleDef
	ON scenarioMapping.`id_module` = moduleDef.`id`
	LEFT JOIN `tab_module_def` defModuleBefore
	ON scenarioMapping.`link_before` = defModuleBefore.`id`
	LEFT JOIN `tab_module_def` defModuleAfter
	ON scenarioMapping.`link_after` = defModuleAfter.`id`
    SET 
        scenarioMapping.`link_before` = :link_before
    WHERE 1=1
    AND scenarioDef.`key` = :scenario
    AND scenarioMapping.`id_module` = :link_after
    ;";
    $params->typeSQL = OdaLibBd::SQL_SCRIPT;
    $params->bindsValue = [
        "scenario" => $CHOP_INTERFACE->inputs["scenario"],
        "link_before" => $idModuleBefore,
        "link_after" => $idModuleAfter
    ];
    $retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
}

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "UPDATE `tab_scenario_mapping` scenarioMapping
    LEFT JOIN `tab_scenario_def` scenarioDef
    ON scenarioMapping.`id_scenario` = scenarioDef.`id`
    LEFT JOIN `tab_module_def` moduleDef
    ON scenarioMapping.`id_module` = moduleDef.`id`
    LEFT JOIN `tab_module_def` defModuleBefore
    ON scenarioMapping.`link_before` = defModuleBefore.`id`
    LEFT JOIN `tab_module_def` defModuleAfter
    ON scenarioMapping.`link_after` = defModuleAfter.`id`
SET 
    scenarioMapping.`date_disable` = NOW()
    , scenarioMapping.`user_id_disable` = :userId
WHERE 1=1
AND scenarioDef.`key` = :scenario
AND moduleDef.`key` = :module
;";
$params->typeSQL = OdaLibBd::SQL_SCRIPT;
$params->bindsValue = [
    "scenario" => $CHOP_INTERFACE->inputs["scenario"],
    "module" => $CHOP_INTERFACE->inputs["module"],
    "userId" => 1
];
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);