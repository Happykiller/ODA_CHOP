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
$params->interface = "phpsql/getListScenario";
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/getListScenario.php

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "Select a.`key`, a.`titre_element`, a.`resume_element`, a.`description`, a.`start_module`, a.`date_record`, a.`user_id_record`
    ,b.`key` as 'module_start_key', c.`key` as 'page_start_key'
    from `tab_scenario_def` a, `tab_module_def` b, `tab_pages_def` c
    WHERE 1=1
    AND a.`start_module` = b.`id`
    AND b.`start_page` = c.`id`
    AND a.`date_disable` is null
    AND b.`date_disable` is null
    AND c.`date_disable` is null
;";
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

//---------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);