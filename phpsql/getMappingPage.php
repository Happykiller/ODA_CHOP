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
$params->interface = "phpsql/getMappingPage";
$params->arrayInput = array("scenarioCurrent", "moduleCurrent", "pageCurrent");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/getMappingPage.php?milis=123450&scenarioCurrent=BPAD-PRES&moduleCurrent=BPAD-PRES_INTRO&pageCurrent=BPAD-PRES_INTRO_APPRO

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "Select tabMappingModule.`id`
    , tabModule.`key` as 'moduleCurrent'
    , tabPageCurrent.`key` as 'pageCurrent'
    , tabPageBefore.`key` as 'pageBefore'
    , tabPageAfter.`key` as 'pageAfter'
    from `tab_module_mapping` tabMappingModule
        LEFT OUTER JOIN `tab_pages_def` tabPageBefore
            ON tabMappingModule.`link_before` = tabPageBefore.`id`
            AND tabPageBefore.`date_disable` is null
        LEFT OUTER JOIN `tab_pages_def` tabPageAfter
            ON tabMappingModule.`link_after` = tabPageAfter.`id`
            AND tabPageAfter.`date_disable` is null
    , `tab_module_def` tabModule,`tab_pages_def` tabPageCurrent
    WHERE 1=1
    AND tabMappingModule.`id_module` = tabModule.`id`
    AND tabMappingModule.`id_page` = tabPageCurrent.`id`
    AND tabModule.`key` = :moduleCurrent
    AND tabPageCurrent.`key` = :pageCurrent
    AND tabMappingModule.`date_disable` is null
    AND tabModule.`date_disable` is null
    AND tabPageCurrent.`date_disable` is null
;";
$params->bindsValue = [
    "moduleCurrent" => $CHOP_INTERFACE->inputs["moduleCurrent"]
    , "pageCurrent" => $CHOP_INTERFACE->inputs["pageCurrent"]
];
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

//---------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);