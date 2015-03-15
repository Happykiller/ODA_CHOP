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
$params->interface = "phpsql/getPageDetails";
$params->arrayInput = array("pageKey");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/getPageDetails.php?milis=123450&pageKey=BPAD-PRES_INTRO_APPRO

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "Select a.`id`, a.`description`, a.`user_id_record`, a.`date_record`
    , elt_titre.`key` as 'titre_key'
    , elt_content.`key` as 'content_key'
    , elt_tips.`key` as 'tips_key'
    from `tab_pages_def` a, `tab_elements` elt_titre , `tab_elements` elt_content , `tab_elements` elt_tips 
    WHERE 1=1
    AND a.`titre_element` = elt_titre.`id`
    AND a.`content_element` = elt_content.`id`
    AND a.`tips_element` = elt_tips.`id`
    AND a.`key` = :pageKey
    AND a.`date_disable` is null
    AND elt_titre.`date_disable` is null
    AND elt_content.`date_disable` is null
    AND elt_tips.`date_disable` is null
;";
$params->bindsValue = [
    "pageKey" => $CHOP_INTERFACE->inputs["pageKey"]
];
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);   

//---------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);