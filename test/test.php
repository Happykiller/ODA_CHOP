<?php
namespace Chop;
use stdClass, \Oda\OdaLib, \Oda\OdaPrepareInterface, \Oda\OdaDate, \Oda\OdaPrepareReqSql;
//--------------------------------------------------------------------------
//Header
require("../API/php/header.php");
require("../php/ChopInterface.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new OdaPrepareInterface();
$params->interface = "API/test";
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// test/test.php

//--------------------------------------------------------------------------
// On transforme les résultats en tableaux d'objet
$retours = array();

//--------------------------------------------------------------------------
$strElt = "ELT_TESTU".OdaDate::getMicro();

$retours[] = OdaLib::test("getPageId",function() {
    global $CHOP_INTERFACE;
        $retour = $CHOP_INTERFACE->getPageId("BPAD-PRES_INTRO_STARTPAGE");
        
        OdaLib::equal(($retour == 1), true, "Test OK : Passed! ( id : " . $retour . ")");
    }         
);

$retours[] = OdaLib::test("getModuleId",function() {
    global $CHOP_INTERFACE;
        $retour = $CHOP_INTERFACE->getModuleId("BPAD-PRES_INTRO");
        
        OdaLib::equal(($retour == 1), true, "Test OK : Passed! ( id : " . $retour . ")");
    }         
);

$retours[] = OdaLib::test("getScenarioId",function() {
    global $CHOP_INTERFACE;
        $retour = $CHOP_INTERFACE->getScenarioId("BPAD-PRES");
        
        OdaLib::equal(($retour == 1), true, "Test OK : Passed! ( id : " . $retour . ")");
    }         
);

$retours[] = OdaLib::test("getLangs",function() {
    global $CHOP_INTERFACE;
        $retour = $CHOP_INTERFACE->getLangs();
        
        OdaLib::equal(($retour->nombre > 1), true, "Test OK : Passed! ( retour->nombre : " . $retour->nombre . ")");
    }         
);

$retours[] = OdaLib::test("createElement",function() {
    global $strElt, $CHOP_INTERFACE;
        $inputElet = new stdClass();
        $inputElet->key = $strElt;
        $inputElet->userid = 1;
        $inputElet->type = "TEXT";
        $inputElet->description = "a element create by test U php";
        $inputElet->listElentData = array();
        $inputEletSub = new stdClass();
        $inputEletSub->lang = 'EN';
        $inputEletSub->data = 'My text';
        $inputElet->listElentData[] = $inputEletSub;

        $retour = $CHOP_INTERFACE->createElement($inputElet);
        $id = $retour->data["idElement"];

        OdaLib::equal(($id != null), true, "Test OK : Passed! (strElt : " . $strElt . ", id : " . $id . ")");
    }         
);

$retours[] = OdaLib::test("getElementId",function() {
    global $strElt, $CHOP_INTERFACE;
        $id = $CHOP_INTERFACE->getElementId($strElt);

        OdaLib::equal(($id != null), true, "Test OK : Passed! (strElt : " . $strElt . ", id : " . $id . ")");
    }         
);

$retours[] = OdaLib::test("functionDeepTrad",function() {
    global $strElt, $CHOP_INTERFACE;
        $object = new stdClass();
        $object->strErreur = "";
        $object->key = 'PARENT_ELT';
        $object->lang = 'EN';
        $object->mode = 'read';
        $object->data = '[['.$strElt.']]';

        $obtRetour = $CHOP_INTERFACE->functionDeepTrad($object);

        OdaLib::equal(($obtRetour->data == 'My text'), true, "Test OK : Passed! (strElt : " . $strElt . ", obtRetour->data : '" . $obtRetour->data . "')");
    }         
);

$retours[] = OdaLib::test("getElementDef",function() {
    global $strElt, $CHOP_INTERFACE;
        $retour = $CHOP_INTERFACE->getElementDef($strElt);
        
        OdaLib::equal($retour, true, "Test OK : Passed! (strElt : " . $strElt . ", type : " . $retour->type . ")");
    }         
);

$retours[] = OdaLib::test("editPage new",function() {
    global $CHOP_INTERFACE;
        $params = new stdClass();
        $params->pageKey = "PAGE_TEST";
        $params->userid = 1;
        $params->description = "description";
        $params->titreKey = "";
        $params->contentKey = "";
        $params->tipsKey = "";
        $params->mode = 'new';
        $page = $CHOP_INTERFACE->editPage($params);
        
        OdaLib::equal($page->data["idPage"], true, "Test OK : Passed! ( page->data[idPage] : " . $page->data["idPage"] . ")");
    }         
);

$retours[] = OdaLib::test("editPage edit",function() {
    global $CHOP_INTERFACE;
        $params = new stdClass();
        $params->pageKey = "PAGE_TEST";
        $params->userid = 1;
        $params->description = "description 2";
        $params->titreKey = "";
        $params->contentKey = "";
        $params->tipsKey = "autreTips";
        $params->mode = 'edit';
        $page = $CHOP_INTERFACE->editPage($params);
        
        OdaLib::equal($page->data["idPage"], true, "Test OK : Passed! ( page->data[idPage] : " . $page->data["idPage"] . ")");
    }         
);

$retours[] = OdaLib::test("editModule new",function() {
    global $CHOP_INTERFACE;
        $params = new stdClass();
        $params->moduleKey = "MODULE_TEST";
        $params->userid = 1;
        $params->description = "description";
        $params->titreKey = "";
        $params->resumeKey = "";
        $params->startPageKey = "";
        $params->mode = 'new';
        $module = $CHOP_INTERFACE->editModule($params);
        
        OdaLib::equal($module->data["idModule"], true, "Test OK : Passed! ( module->data[idModule] : " . $module->data["idModule"] . ")");
    }         
);

$retours[] = OdaLib::test("editModule edit",function() {
    global $CHOP_INTERFACE;
        $params = new stdClass();
        $params->moduleKey = "MODULE_TEST";
        $params->userid = 1;
        $params->description = "description 2";
        $params->titreKey = "";
        $params->resumeKey = "";
        $params->startPageKey = "pageStartModuleTest";
        $params->mode = 'edit';
        $module = $CHOP_INTERFACE->editModule($params);
        
        OdaLib::equal($module->data["idModule"], true, "Test OK : Passed! ( module->data[idModule] : " . $module->data["idModule"] . ")");
    }         
);

$retours[] = OdaLib::test("editScenario",function() {
    global $ODA_INTERFACE;

    //---------------------------------------
    $params = new stdClass();
    $input = [
        "input_scenarioKey" => "MODULETEST"
        ,"input_scenarioDescription" => ""
        ,"keyAuthODA" => ""
        ,"input_scenarioTitre" => ""
        ,"input_scenarioStartModule" => ""
        ,"input_scenarioResume" => ""
        ,"input_mode" => ""
    ];
    $retourCallRest = OdaLib::CallRest($ODA_INTERFACE->config->domaine."phpsql/editScenario.php", $params, $input);

    OdaLib::equal($retourCallRest->data->resultat->param_type, "int", "Test OK : Passed!");         
});

//--------------------------------------------------------------------------
//Out
$resultats = new stdClass();
$resultats->details = $retours;
$resultats->succes = 0;
$resultats->echec = 0;
$resultats->total = 0;
foreach($retours as $key => $value) {
    $resultats->succes += $value->succes;
    $resultats->echec += $value->echec;
    $resultats->total += $value->total;
 }

//--------------------------------------------------------------------------
$CHOP_INTERFACE->addDataObject($resultats);