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
// phpsql/getScenarioDetails.php?milis=123450&scenarioKey=BPAD-PRES

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "Select scenarioDef.`id`, scenarioDef.`key`, scenarioDef.`description`, scenarioDef.`user_id_record`, scenarioDef.`date_record`
	, startModule.`key` as 'startModule_key'
    , elt_titre.`key` as 'titre_key'
    , elt_resume.`key` as 'resume_key'
    from `tab_scenario_def` scenarioDef, `tab_module_def` startModule, `tab_elements` elt_titre , `tab_elements` elt_resume
    WHERE 1=1
	AND scenarioDef.`start_module` = startModule.`id`
    AND scenarioDef.`titre_element` = elt_titre.`id`
    AND scenarioDef.`resume_element` = elt_resume.`id`
    AND scenarioDef.`key` = :scenarioKey
    AND scenarioDef.`date_disable` is null
    AND startModule.`date_disable` is null
    AND elt_titre.`date_disable` is null
    AND elt_resume.`date_disable` is null
;";
$params->bindsValue = [
    "scenarioKey" => $CHOP_INTERFACE->inputs["scenarioKey"]
];
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);   

//---------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);