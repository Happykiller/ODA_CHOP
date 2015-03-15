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
$params->interface = "phpsql/getElementDetails";
$params->arrayInput = array("key","lang","date_record");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/getElementDetails.php?milis=123450&key=BPAD-PRES_TITRE&lang=EN&date_record=2014-08-08 11:00:27

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "Select b.*
    from `tab_elements` a, `tab_elements_datas` b
    WHERE 1=1
    AND a.`id` = b.`parent_element_id`
    AND a.`key` = :key
    AND b.`lang` = :lang
    AND b.`date_record` = :date_record
;";
$params->bindsValue = [
    "key" => $CHOP_INTERFACE->inputs["key"],
    "lang" => $CHOP_INTERFACE->inputs["lang"],
    "date_record" => $CHOP_INTERFACE->inputs["date_record"]
];
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);