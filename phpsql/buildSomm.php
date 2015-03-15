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
$params->interface = "phpsql/buildSomm";
$params->arrayInput = array("keyScenar");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/buildSomm.php?milis=123450&keyScenar=SCENARIO_START

//--------------------------------------------------------------------------
$params = new stdClass();
$params->keyScenar = $CHOP_INTERFACE->inputs["keyScenar"];

$objectSommaire = $CHOP_INTERFACE->buildSommaire($params);

//---------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->value = $objectSommaire;
$CHOP_INTERFACE->addDataObject($params);