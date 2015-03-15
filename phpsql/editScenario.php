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
$params->arrayInput = array("input_scenarioKey","input_scenarioDescription","input_scenarioTitre","input_scenarioStartModule","input_scenarioResume","input_mode");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/editScenario.php?milis=123450&input_scenarioKey=MODULETEST&input_scenarioDescription=Truc&input_scenarioTitre=MODULETEST_TITRE&input_scenarioStartModule=HOMEPAGE&input_scenarioResume=MODULETEST_RESUME&input_scenarioKey=SCENARIO_START&input_scenarioBefore=MOD_START1&input_scenarioAfter=&input_mode=new

//--------------------------------------------------------------------------
$testScenarioId = $CHOP_INTERFACE->getScenarioId($CHOP_INTERFACE->inputs["input_scenarioKey"]);
if($CHOP_INTERFACE->inputs["input_mode"] == "new"){
    if($testScenarioId != 0){
        $CHOP_INTERFACE->dieInError("Early exist");
    }
}elseif ($CHOP_INTERFACE->inputs["input_mode"] == "edit"){
    if($testScenarioId == 0){
        $CHOP_INTERFACE->dieInError("Not found");
    }
}

//--------------------------------------------------------------------------
$object_retour->data["idScenario"] = 0;
$object_retour->data["titre"] = new stdClass();
$object_retour->data["resume"] = new stdClass();
$object_retour->data["startModule"] = new stdClass();

$idTitre = 0;
$titreKey = "";
if($CHOP_INTERFACE->inputs["input_scenarioTitre"] != "") {
    $titreKey = $CHOP_INTERFACE->inputs["input_scenarioTitre"];
    $idTitre = $CHOP_INTERFACE->getElementId($titreKey);
    if($idTitre == 0){
        $inputEletTitre = new stdClass();
        $inputEletTitre->key = $titreKey;
        $inputEletTitre->userid = 1;
        $inputEletTitre->type = "TEXT";
        $inputEletTitre->description = "";
        $inputEletTitre->listElentData = array();

        $retour = $CHOP_INTERFACE->createElement($inputEletTitre);
        $idTitre = $retour->data["idElement"];
    }
}else{
    $titreKey = $CHOP_INTERFACE->inputs["input_scenarioKey"]."_TITRE";
    $idTitre = $CHOP_INTERFACE->getElementId($titreKey);
    if($idTitre == 0){
        $inputEletTitre = new stdClass();
        $inputEletTitre->key = $titreKey;
        $inputEletTitre->userid = 1;
        $inputEletTitre->type = "TEXT";
        $inputEletTitre->description = "";
        $inputEletTitre->listElentData = array();

        $retour = $CHOP_INTERFACE->createElement($inputEletTitre);
        $idTitre = $retour->data["idElement"];
    }
}
$object_retour->data["titre"]->id = $idTitre;
$object_retour->data["titre"]->key = $titreKey;

$idResume = 0;
$resumeKey = "";
if($CHOP_INTERFACE->inputs["input_scenarioResume"] != "") {
    $resumeKey = $CHOP_INTERFACE->inputs["input_scenarioResume"];
    $idResume = $CHOP_INTERFACE->getElementId($resumeKey);
    if($idResume == 0){
        $inputEletResume = new stdClass();
        $inputEletResume->key = $resumeKey;
        $inputEletResume->userid = 1;
        $inputEletResume->type = "TEXT";
        $inputEletResume->description = "";
        $inputEletResume->listElentData = array();

        $retour = $CHOP_INTERFACE->createElement($inputEletResume);
        $idResume = $retour->data["idElement"];
    }
}else{
    $resumeKey = $CHOP_INTERFACE->inputs["input_scenarioKey"]."_RESUME";
    $idResume = $CHOP_INTERFACE->getElementId($resumeKey);
    if($idResume == 0){
        $inputEletResume = new stdClass();
        $inputEletResume->key = $resumeKey;
        $inputEletResume->userid = 1;
        $inputEletResume->type = "TEXT";
        $inputEletResume->description = "";
        $inputEletResume->listElentData = array();

        $retour = $CHOP_INTERFACE->createElement($inputEletResume);

        $idResume = $retour->data["idElement"];
    }
}
$object_retour->data["resume"]->id = $idResume;
$object_retour->data["resume"]->key = $resumeKey;

$idStartModule = 0;
$startModuleKey = "";
if($CHOP_INTERFACE->inputs["input_scenarioStartModule"] != "") {
    $startModuleKey = $CHOP_INTERFACE->inputs["input_scenarioStartModule"];
    $idStartModule = $CHOP_INTERFACE->getModuleId($startModuleKey);

    if($idStartModule == 0){
        $inputStartModule = new stdClass();
        $inputStartModule->moduleKey = $startModuleKey;
        $inputStartModule->userid = 1;
        $inputStartModule->description = "empty";
        $inputStartModule->titreKey = $startModuleKey."_TITRE";
        $inputStartModule->resumeKey = $startModuleKey."_RESUME";
        $inputStartModule->startPageKey = $startModuleKey."_STARTPAGE";
        $inputStartModule->mode = "new";

        $retour = $CHOP_INTERFACE->editModule($inputStartModule);
        $idStartModule = $retour->data["idModule"];
    }
}else {
    $startModuleKey = $CHOP_INTERFACE->inputs["input_scenarioKey"]."_STARTMODULE";
    $idStartModule = $CHOP_INTERFACE->getModuleId($startModuleKey);

    if($startModuleKey == 0){
        $inputStartModule = new stdClass();
        $inputStartModule->moduleKey = $startModuleKey;
        $inputStartModule->userid = 1;
        $inputStartModule->description = "empty";
        $inputStartModule->titreKey = $startModuleKey."_TITRE";
        $inputStartModule->resumeKey = $startModuleKey."_RESUME";
        $inputStartModule->startPageKey = $startModuleKey."_STARTPAGE";
        $inputStartModule->mode = "new";

        $retour = $CHOP_INTERFACE->editModule($inputStartModule);
        $idStartModule = $retour->data["idModule"];
    }
}
$object_retour->data["startModule"]->id = $idStartModule;
$object_retour->data["startModule"]->key = $startModuleKey;

switch ($CHOP_INTERFACE->inputs["input_mode"]) {
    case "new":
        $params = new OdaPrepareReqSql();
        $params->sql = "INSERT INTO `tab_scenario_def` (
                `key`,
                `description`,
                `titre_element`,
                `resume_element`,
                `start_module`,
                `user_id_record`,
                `date_record`
            )
            VALUES (
                :key, :description, :titre_element, :resume_element, :start_module, :user_id_record, NOW()
            )
        ;";
        $params->bindsValue = [
            "key" => $CHOP_INTERFACE->inputs["input_scenarioKey"]
            , "description" => $CHOP_INTERFACE->inputs["input_scenarioDescription"]
            , "titre_element" => $idTitre
            , "resume_element" => $idResume
            , "start_module" => $idStartModule
            , "user_id_record" => 1
        ];
        $params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
        $retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
        $object_retour->data["idScenario"] = $retour->data;

        $params = new OdaPrepareReqSql();
        $params->sql = "INSERT INTO `tab_scenario_mapping`
            (`id_scenario`, `id_module`, `date_record`, `user_id_record`)
             VALUES 
            (:id_scenario,:id_module,NOW(),:user_id_record)
        ;";
        $params->bindsValue = [
            "id_scenario" => $object_retour->data["idScenario"]
            , "id_module" => $idStartModule
            , "user_id_record" => 1
        ];
        $params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
        $retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

        break;
    case "edit":
        $object_retour->data["idScenario"] = $CHOP_INTERFACE->getScenarioId($CHOP_INTERFACE->inputs["input_scenarioKey"]);

        $params = new OdaPrepareReqSql();
        $params->sql = "UPDATE `tab_scenario_def` SET
                `description` = :description,
                `titre_element` = :titre_element,
                `resume_element` = :resume_element,
                `start_module` = :start_module,
                `user_id_update` = :user_id_record,
                `date_update` = NOW()
            WHERE 1=1
            AND `KEY` = :key
        ;";
        $params->bindsValue = [
            "key" => $CHOP_INTERFACE->inputs["input_scenarioKey"]
            , "description" =>  $CHOP_INTERFACE->inputs["input_scenarioDescription"]
            , "titre_element" => $idTitre
            , "resume_element" => $idResume
            , "start_module" => $idStartModule
            , "user_id_record" => 1
        ];
        $params->typeSQL = OdaLibBd::SQL_SCRIPT;
        $retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
        break;
    default :
        break;
}

$CHOP_INTERFACE->addDataObject($object_retour->data);