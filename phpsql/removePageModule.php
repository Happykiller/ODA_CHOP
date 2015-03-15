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
$params->arrayInput = array("module","page");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/removePageModule.php?milis=123450&module=MODULE1&page=PAGE6

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "Select a.`link_before`, a.`link_after`
        from `tab_module_mapping` a
            LEFT JOIN `tab_module_def` moduleDef
            ON a.`id_module` = moduleDef.`id`
            LEFT JOIN `tab_pages_def` defPage
            ON a.`id_page` = defPage.`id`
        WHERE 1=1
        AND moduleDef.`key` = :id_module
        AND defPage.`key` = :id_page
;";
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$params->bindsValue = [
    "id_module" => $CHOP_INTERFACE->inputs["module"],
    "id_page" => $CHOP_INTERFACE->inputs["page"]
];
$resultats = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
$idPageBefore = $resultats->data->link_before;
$idPageAfter = $resultats->data->link_after;

if($idPageBefore != null){
    $params = new OdaPrepareReqSql();
    $params->sql = "UPDATE `tab_module_mapping` moduleMapping
	LEFT JOIN `tab_module_def` defModule
	ON moduleMapping.`id_module` = defModule.`id`
	LEFT JOIN `tab_pages_def` defPage
	ON moduleMapping.`id_page` = defPage.`id`
	LEFT JOIN `tab_pages_def` defPageBefore
	ON moduleMapping.`link_before` = defPageBefore.`id`
	LEFT JOIN `tab_pages_def` defPageAfter
	ON moduleMapping.`link_after` = defPageAfter.`id`
    SET 
    moduleMapping.`link_after` = :link
    WHERE 1=1
    AND defModule.`key` = :id_module
    AND moduleMapping.`id_page` = :id_page
    ;";
    $params->typeSQL = OdaLibBd::SQL_SCRIPT;
    $params->bindsValue = [
        "id_module" => $CHOP_INTERFACE->inputs["module"],
        "id_page" => $idPageBefore,
        "link" => $idPageAfter
    ];
    $retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
}

if($idPageAfter != null){
    $params = new OdaPrepareReqSql();
    $params->sql = "UPDATE `tab_module_mapping` moduleMapping
	LEFT JOIN `tab_module_def` defModule
	ON moduleMapping.`id_module` = defModule.`id`
	LEFT JOIN `tab_pages_def` defPage
	ON moduleMapping.`id_page` = defPage.`id`
	LEFT JOIN `tab_pages_def` defPageBefore
	ON moduleMapping.`link_before` = defPageBefore.`id`
	LEFT JOIN `tab_pages_def` defPageAfter
	ON moduleMapping.`link_after` = defPageAfter.`id`
    SET 
    moduleMapping.`link_before` = :link
    WHERE 1=1
    AND defModule.`key` = :id_module
    AND moduleMapping.`id_page` = :id_page
    ;";
    $params->typeSQL = OdaLibBd::SQL_SCRIPT;
    $params->bindsValue = [
        "id_module" => $CHOP_INTERFACE->inputs["module"],
        "id_page" => $idPageAfter,
        "link" => $idPageBefore
    ];
    $retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
}

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "UPDATE `tab_module_mapping`
	LEFT JOIN `tab_module_def`
	ON `tab_module_mapping`.`id_module` = `tab_module_def`.`id`
	LEFT JOIN `tab_pages_def` defPage
	ON `tab_module_mapping`.`id_page` = defPage.`id`
	LEFT JOIN `tab_pages_def` defPageBefore
	ON `tab_module_mapping`.`link_before` = defPageBefore.`id`
	LEFT JOIN `tab_pages_def` defPageAfter
	ON `tab_module_mapping`.`link_after` = defPageAfter.`id`
    SET 
    `tab_module_mapping`.`date_disable` = NOW()
    , `tab_module_mapping`.`user_id_disable` = 1
    WHERE 1=1
    AND `tab_module_def`.`key` = :id_module
    AND defPage.`key` = :id_page
;";
$params->typeSQL = OdaLibBd::SQL_SCRIPT;
$params->bindsValue = [
    "id_module" => $CHOP_INTERFACE->inputs["module"],
    "id_page" => $CHOP_INTERFACE->inputs["page"]
];
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$CHOP_INTERFACE->addDataReqSQL($params);