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
$params->interface = "phpsql/editModule";
$params->arrayInput = array("input_pageKey","input_pageDescription","input_pageTitre","input_pageContent","input_pageTips","input_moduleKey","input_pageBefore","input_pageAfter","input_mode");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/editPage.php?milis=123450&input_pageKey=PAGE_TEST&input_pageDescription=Truc&input_pageTitre=PAGE_TEST_TITRE&input_pageContent=PAGE_TEST_CONTENT&input_pageTips=PAGE_TEST_TIPS&input_moduleKey=MOD_START&input_pageBefore=HOMEPAGE&input_pageAfter&input_mode=new

//--------------------------------------------------------------------------
$testPageId = $CHOP_INTERFACE->getPageId($CHOP_INTERFACE->inputs["input_pageKey"]);
if($CHOP_INTERFACE->inputs["input_mode"] == "new"){
    if($testPageId != 0){
        $CHOP_INTERFACE->dieInError("Early exist");
    }
}elseif ($CHOP_INTERFACE->inputs["input_mode"] == "edit"){
    if($testPageId == 0){
        $CHOP_INTERFACE->dieInError("Not found");
    }
}

//--------------------------------------------------------------------------
$params = new stdClass();
$params->pageKey = $CHOP_INTERFACE->inputs["input_pageKey"];
$params->userid = 1;
$params->description = $CHOP_INTERFACE->inputs["input_pageDescription"];
$params->titreKey = $CHOP_INTERFACE->inputs["input_pageTitre"];
$params->contentKey = $CHOP_INTERFACE->inputs["input_pageContent"];
$params->tipsKey = $CHOP_INTERFACE->inputs["input_pageTips"];
$params->mode = $CHOP_INTERFACE->inputs["input_mode"];
$page = $CHOP_INTERFACE->editPage($params);
$idPage = $page->data['idPage'];

$idPageBefore = null;
$pageBefore = null;
if(($CHOP_INTERFACE->inputs["input_pageBefore"] != "")&&($CHOP_INTERFACE->inputs["input_pageBefore"] != "null")) {
    $idPageBefore = $CHOP_INTERFACE->getPageId($CHOP_INTERFACE->inputs["input_pageBefore"]);
    if($idPageBefore == 0){
        $params = new stdClass();
        $params->pageKey = $CHOP_INTERFACE->inputs["input_pageBefore"];
        $params->userid = 1;
        $params->description = $CHOP_INTERFACE->inputs["input_pageBefore"]." description";
        $params->titreKey = $CHOP_INTERFACE->inputs["input_pageBefore"]."_TITRE";
        $params->contentKey = $CHOP_INTERFACE->inputs["input_pageBefore"]."_CONTENT";
        $params->tipsKey = $CHOP_INTERFACE->inputs["input_pageBefore"]."_TIPS";
        $params->mode = 'new';
        $pageBefore = $CHOP_INTERFACE->editPage($params);
        $idPageBefore = $pageBefore->data['idPage'];
    }
}

$idPageAfter = null;
$pageAfter = null;
if(($CHOP_INTERFACE->inputs["input_pageAfter"] != "")&&($CHOP_INTERFACE->inputs["input_pageAfter"] != "null")) {
    $idPageAfter = $CHOP_INTERFACE->getPageId($CHOP_INTERFACE->inputs["input_pageAfter"]);
    if($idPageAfter == 0){
        $params = new stdClass();
        $params->pageKey = $CHOP_INTERFACE->inputs["input_pageAfter"];
        $params->userid = 1;
        $params->description = $CHOP_INTERFACE->inputs["input_pageAfter"]." description";
        $params->titreKey = $CHOP_INTERFACE->inputs["input_pageAfter"]."_TITRE";
        $params->contentKey = $CHOP_INTERFACE->inputs["input_pageAfter"]."_CONTENT";
        $params->tipsKey = $CHOP_INTERFACE->inputs["input_pageAfter"]."_TIPS";
        $params->mode = 'new';
        $pageAfter = $CHOP_INTERFACE->editPage($params);
        $idPageAfter = $pageAfter->data['idPage'];
    }
}

$moduleId = $CHOP_INTERFACE->getModuleId($CHOP_INTERFACE->inputs["input_moduleKey"]);

if($idPageBefore != 0){
    $params = new OdaPrepareReqSql();
    $params->sql = "UPDATE `tab_module_mapping` SET 
        `link_after` = ".$idPage."
        WHERE 1=1
        AND `id_module` = ".$moduleId."
        AND `id_page` = ".$idPageBefore."
    ;";
    $params->typeSQL = OdaLibBd::SQL_SCRIPT;
    $retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
}

$params = new OdaPrepareReqSql();
$params->sql = "UPDATE `tab_module_mapping` SET 
    `date_disable` = NOW()
    , `user_id_disable` = 1
    WHERE 1=1
    AND `id_module` = ".$moduleId."
    AND `id_page` = ".$idPage."
;";
$params->typeSQL = OdaLibBd::SQL_SCRIPT;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

$params = new OdaPrepareReqSql();
$params->sql = "INSERT INTO  `tab_module_mapping` (
        `id_module`,
        `id_page`,
        `link_before`,
        `link_after` ,
        `date_record`,
        `user_id_record`
    )
    VALUES (
        :moduleId, :pageId, :pageBeforeId, :pageAfterId, NOW(), :userId
    )
;";
$params->bindsValue = [
    "moduleId" => $moduleId
    , "pageId" => $idPage
    , "pageBeforeId" => $idPageBefore
    , "pageAfterId" => $idPageAfter
    , "userId" => 1
];
$params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
$idMapping = $retour->data;

$params = new stdClass();
$params->label = "idMapping";
$params->value = $idMapping;
$CHOP_INTERFACE->addDataStr($params);

$params = new stdClass();
$params->label = "idPage";
$params->value = $idPage;
$CHOP_INTERFACE->addDataStr($params);