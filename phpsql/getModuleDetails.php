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
$params->interface = "phpsql/getModuleDetails";
$params->arrayInput = array("moduleKey");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/getModuleDetails.php?milis=123450&moduleKey=BPAD-PRES_INTRO

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "Select moduleDef.`id`, moduleDef.`key`, moduleDef.`description`, moduleDef.`user_id_record`, moduleDef.`date_record`
	, startPage.`key` as 'startPage_key'
    , elt_titre.`key` as 'titre_key'
    , elt_resume.`key` as 'resume_key'
    from `tab_module_def` moduleDef, `tab_pages_def` startPage, `tab_elements` elt_titre , `tab_elements` elt_resume
    WHERE 1=1
	AND moduleDef.`start_page` = startPage.`id`
    AND moduleDef.`titre_element` = elt_titre.`id`
    AND moduleDef.`resume_element` = elt_resume.`id`
    AND moduleDef.`key` = :moduleKey
    AND moduleDef.`date_disable` is null
    AND startPage.`date_disable` is null
    AND elt_titre.`date_disable` is null
    AND elt_resume.`date_disable` is null
;";
$params->bindsValue = [
    "moduleKey" => $CHOP_INTERFACE->inputs["moduleKey"]
];
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

//---------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);