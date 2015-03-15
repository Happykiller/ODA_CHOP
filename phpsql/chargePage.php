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
$params->interface = "phpsql/chargePage";
$params->arrayInput = array("module", "page");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/chargePage.php?milis=123450&ctrl=ok&page=BPAD-PRES_INTRO_APPRO&module=BPAD-PRES_INTRO

//--------------------------------------------------------------------------
$params = new \Oda\OdaPrepareReqSql();
$params->sql = "SELECT a.`key`, a.`description`, a.`titre_element`, a.`content_element`, a.`tips_element`, a.`date_record`, a.`user_id_record`, a.`date_disable`, a.`user_id_disable`
    , titre.`key` as 'titre_key', content.`key` as 'content_key', tips.`key` as 'tips_key'
    FROM `tab_pages_def` a, `tab_elements` titre, `tab_elements` content, `tab_elements` tips 
    WHERE 1=1
    AND a.`titre_element` = titre.`id`
    AND a.`content_element` = content.`id`
    AND a.`tips_element` = tips.`id`
    AND a.`date_disable` is null
    AND titre.`date_disable` is null
    AND content.`date_disable` is null
    AND tips.`date_disable` is null
    AND a.`key` = :page
    ;
;";
$params->bindsValue = [
    "page" => $CHOP_INTERFACE->inputs["page"]
];
$params->typeSQL = \Oda\OdaLibBd::SQL_GET_ONE;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);

//--------------------------------------------------------------------------
$params = new \Oda\OdaPrepareReqSql();
$params->sql = "SELECT a.`key` as 'module', c.`key` as 'currentPage', d.`key` as 'beforePage', e.`key` as 'titre'
    FROM `tab_module_def` a, `tab_module_mapping` b, `tab_pages_def` c, `tab_pages_def` d, `tab_elements` e
    WHERE 1=1
    AND a.`id` = b.`id_module`
    AND b.`id_page` = c.`id`
    AND d.`id` = b.`link_before`
    AND d.`titre_element` = e.`id`
    AND c.`key` = :page
    AND a.`key` = :module
    ORDER BY b.`link_before_weight`
;";
$params->bindsValue = [
    "page" => $CHOP_INTERFACE->inputs["page"]
    , "module" => $CHOP_INTERFACE->inputs["module"]
];
$params->typeSQL = \Oda\OdaLibBd::SQL_GET_ALL;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

$params = new stdClass();
$params->label = "navigationBefore";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);

//--------------------------------------------------------------------------
$params = new \Oda\OdaPrepareReqSql();
$params->sql = "SELECT a.`key` as 'module', c.`key` as 'currentPage', d.`key` as 'afterPage', e.`key` as 'titre'
    FROM `tab_module_def` a, `tab_module_mapping` b, `tab_pages_def` c, `tab_pages_def` d, `tab_elements` e
    WHERE 1=1
    AND a.`id` = b.`id_module`
    AND b.`id_page` = c.`id`
    AND d.`id` = b.`link_after`
    AND d.`titre_element` = e.`id`
    AND c.`key` = :page
    AND a.`key` = :module
    ORDER BY b.`link_after_weight`
;";
$params->bindsValue = [
    "page" => $CHOP_INTERFACE->inputs["page"]
    , "module" => $CHOP_INTERFACE->inputs["module"]
];
$params->typeSQL = \Oda\OdaLibBd::SQL_GET_ALL;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

$params = new stdClass();
$params->label = "navigationAfter";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);