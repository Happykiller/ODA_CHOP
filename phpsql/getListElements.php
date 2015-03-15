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
$params->interface = "phpsql/getListElements";
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/getListElements.php

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "Select a.`key`
    ,IF(LENGTH(a.`description`)>100, CONCAT(SUBSTRING(a.`description`,1,200),'...'), a.`description`) as 'description'
    ,a.`type`
    from `tab_elements` a
    WHERE 1=1
    AND a.`type` in ('HTML','CST','IMG')
    AND a.`date_disable` is null
;";
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

//---------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);