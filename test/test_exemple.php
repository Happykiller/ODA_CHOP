<?php
namespace ProjectExemple;
use stdClass, \Oda\OdaLib, \Oda\OdaPrepareInterface;
//--------------------------------------------------------------------------
//Header
require("../API/php/header.php");
require("../php/ProjectExempleInterface.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new OdaPrepareInterface();
$params->interface = "API/test";
$PROJECT_INTERFACE = new ProjectExempleInterface($params);

//--------------------------------------------------------------------------
// test/test.php

//--------------------------------------------------------------------------
// On transforme les résultats en tableaux d'objet
$retours = array();

//--------------------------------------------------------------------------

$retours[] = OdaLib::test("test",function() {
    OdaLib::equal(true, true, "Test OK : Passed!");  
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
$PROJECT_INTERFACE->addDataObject($resultats);