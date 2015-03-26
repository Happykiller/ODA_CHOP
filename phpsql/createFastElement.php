<?php
namespace Chop;
use stdClass;
//--------------------------------------------------------------------------
//Header
require("../API/php/header.php");
require("../php/ChopInterface.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new \Oda\OdaPrepareInterface();
$params->interface = "phpsql/createFastElement";
$params->arrayInput = array("elementKey");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/createFastElement.php?milis=123450&elementKey=BPAD-PRES_INTRO

//--------------------------------------------------------------------------
$params = new \Oda\OdaPrepareReqSql();
$params->sql = "Select count(c.`key`) as 'nbIte'
    FROM (
        SELECT a.`key` FROM `tab_elements` a
        UNION
        SELECT b.`key` FROM `tab_preelementkey` b
    ) c
    WHERE 1=1
    AND c.`key` like '".$CHOP_INTERFACE->inputs["elementKey"]."%'
;";
$params->typeSQL = \Oda\OdaLibBd::SQL_GET_ONE;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
$nbIte = $retour->data->nbIte;

//--------------------------------------------------------------------------
$newEletKey = $CHOP_INTERFACE->inputs["elementKey"].'_sub'.$nbIte;

//--------------------------------------------------------------------------
$params = new \Oda\OdaPrepareReqSql();
$params->sql = "INSERT INTO  `tab_preelementkey` (
        `key` ,
        `dateCreate` 
    )
    VALUES (
        :newEletKey , NOW()
    )
;";
$params->bindsValue = [
    "newEletKey" => $newEletKey
];
$params->typeSQL = \Oda\OdaLibBd::SQL_INSERT_ONE;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$CHOP_INTERFACE->addDataStr($newEletKey);