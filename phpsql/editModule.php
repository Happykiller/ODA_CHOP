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
$params->interface = "phpsql/editModule";
$params->arrayInput = array("input_moduleKey","input_moduleDescription","input_moduleTitre","input_moduleStartPage","input_moduleResume","input_scenarioKey","input_moduleBefore","input_moduleAfter","input_mode");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/editModule.php?milis=123450&input_moduleKey=MODULETEST&input_moduleDescription=Truc&input_moduleTitre=MODULETEST_TITRE&input_moduleStartPage=HOMEPAGE&input_moduleResume=MODULETEST_RESUME&input_scenarioKey=SCENARIO_START&input_moduleBefore=MOD_START1&input_moduleAfter=&input_mode=new

//--------------------------------------------------------------------------
$testModuleId = $CHOP_INTERFACE->getModuleId($CHOP_INTERFACE->inputs["input_moduleKey"]);
if($CHOP_INTERFACE->inputs["input_mode"] == "new"){
    if($testModuleId != 0){
        $CHOP_INTERFACE->dieInError("Early exist");
    }
}elseif ($CHOP_INTERFACE->inputs["input_mode"] == "edit"){
    if($testModuleId == 0){
        $CHOP_INTERFACE->dieInError("Not found");
    }
}

//--------------------------------------------------------------------------
$params = new stdClass();
$params->moduleKey = $CHOP_INTERFACE->inputs["input_moduleKey"];
$params->userid = 1;
$params->description = $CHOP_INTERFACE->inputs["input_moduleDescription"];
$params->titreKey = $CHOP_INTERFACE->inputs["input_moduleTitre"];
$params->resumeKey = $CHOP_INTERFACE->inputs["input_moduleResume"];
$params->startPageKey = $CHOP_INTERFACE->inputs["input_moduleStartPage"];
$params->mode = $CHOP_INTERFACE->inputs["input_mode"];
$module = $CHOP_INTERFACE->editModule($params);
$idModule = $module->data['idModule'];

$idModuleBefore = null;
$moduleBefore = null;
if(($CHOP_INTERFACE->inputs["input_moduleBefore"] != "")&&($CHOP_INTERFACE->inputs["input_moduleBefore"] != "null")) {
    $idModuleBefore = $CHOP_INTERFACE->getModuleId($CHOP_INTERFACE->inputs["input_moduleBefore"]);
    if($idModuleBefore == 0){
        $params = new stdClass();
        $params->moduleKey = $CHOP_INTERFACE->inputs["input_moduleBefore"];
        $params->userid = 1;
        $params->description = $CHOP_INTERFACE->inputs["input_moduleBefore"]." description";
        $params->titreKey = $CHOP_INTERFACE->inputs["input_moduleBefore"]."_TITRE";
        $params->resumeKey = $CHOP_INTERFACE->inputs["input_moduleBefore"]."_CONTENT";
        $params->startPageKey = $CHOP_INTERFACE->inputs["input_moduleBefore"]."_TIPS";
        $params->mode = 'new';
        $moduleBefore = $CHOP_INTERFACE->editModule($params);
        $idModuleBefore = $moduleBefore->data['idModule'];
    }
}

$idModuleAfter = null;
$moduleAfter = null;
if(($CHOP_INTERFACE->inputs["input_moduleAfter"] != "")&&($CHOP_INTERFACE->inputs["input_moduleAfter"] != "null")) {
    $idModuleAfter = $CHOP_INTERFACE->getModuleId($CHOP_INTERFACE->inputs["input_moduleAfter"]);   
    if($idModuleAfter == 0){
        $params = new stdClass();
        $params->moduleKey = $CHOP_INTERFACE->inputs["input_moduleAfter"];
        $params->userid = 1;
        $params->description = $CHOP_INTERFACE->inputs["input_moduleAfter"]." description";
        $params->titreKey = $CHOP_INTERFACE->inputs["input_moduleAfter"]."_TITRE";
        $params->resumeKey = $CHOP_INTERFACE->inputs["input_moduleAfter"]."_CONTENT";
        $params->startPageKey = $CHOP_INTERFACE->inputs["input_moduleAfter"]."_TIPS";
        $params->mode = 'new';
        $moduleAfter = $CHOP_INTERFACE->editModule($params);
        $idModuleAfter = $moduleAfter->data['idModule'];
    }
}

$scenarioId = $CHOP_INTERFACE->getScenarioId($CHOP_INTERFACE->inputs["input_scenarioKey"]);

if($idModuleBefore != 0){
    $params = new OdaPrepareReqSql();
    $params->sql = "UPDATE `tab_scenario_mapping` SET 
        `link_after` = ".$idModule."
        WHERE 1=1
        AND `id_scenario` = ".$scenarioId."
        AND `id_module` = ".$idModuleBefore."
    ;";
    $params->typeSQL = OdaLibBd::SQL_SCRIPT;
    $retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
}

$params = new OdaPrepareReqSql();
$params->sql = "UPDATE `tab_scenario_mapping` SET 
    `date_disable` = NOW()
    , `user_id_disable` = 1
    WHERE 1=1
    AND `id_scenario` = ".$scenarioId."
    AND `id_module` = ".$idModule."
;";
$params->typeSQL = OdaLibBd::SQL_SCRIPT;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

$params = new OdaPrepareReqSql();
$params->sql = "INSERT INTO `tab_scenario_mapping` (
        `id_scenario`,
        `id_module`,
        `link_before`,
        `link_after` ,
        `date_record`,
        `user_id_record`
    )
    VALUES (
        :scenarioId, :moduleId, :link_before, :link_after, NOW(), :user_id_record
    )
;";
$params->bindsValue = [
    "scenarioId" => $scenarioId
    , "moduleId" => $idModule
    , "link_before" => $idModuleBefore
    , "link_after" => $idModuleAfter
    , "user_id_record" => 1
];
$params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
$idMapping = $retour->data;

$params = new stdClass();
$params->label = "idMapping";
$params->value = $idMapping;
$CHOP_INTERFACE->addDataStr($params);

$params = new stdClass();
$params->label = "idModule";
$params->value = $idModule;
$CHOP_INTERFACE->addDataStr($params);