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
$params->interface = "phpsql/getRapportMissTrad";
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/getRapportMissTrad.php

//--------------------------------------------------------------------------
$langs = $CHOP_INTERFACE->getLangs();

//--------------------------------------------------------------------------
$strSql = "SELECT CONCAT('<a href=\"\" onClick=\"openElt(''',b.`key`,''');\">',b.`key`,'</a>') as 'key', b.`description` "; 

foreach ($langs->data as $value){
    $strSql .= ", b.`".$value->code."` ";
}

$strSql .= "
    FROM (
        SELECT a.`key`, a.`description` \n";

foreach ($langs->data as $value){
    $strSql .= ", IF((`datas_".$value->code."`.`data` is null) or (`datas_".$value->code."`.`data` = ''), false, true) as '".$value->code."' \n";
}

$strSql .= "FROM `tab_elements` a \n";

foreach ($langs->data as $value){
    $strSql .= "LEFT OUTER JOIN `tab_elements_datas` `datas_".$value->code."`
    ON a.`id` = `datas_".$value->code."`.`parent_element_id`
    AND `datas_".$value->code."`.`date_disable` is null
    AND `datas_".$value->code."`.`lang` = '".$value->code."' \n";
}

$strSql .= "WHERE 1=1
    AND a.`type` = 'TEXT'
    AND a.`date_disable` is null
    ) b
    WHERE 1=1
    AND (
        1=1
";

foreach ($langs->data as $value){
    $strSql .= " OR b.`".$value->code."` = false";
}

$strSql .= ")
;";

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = $strSql;
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);   

//---------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);