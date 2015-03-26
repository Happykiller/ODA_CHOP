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
$params->interface = "phpsql/getElement";
$params->arrayInput = array("eltKey");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/getElement.php?milis=123450&eltKey=BPAD-PRES_BPM_COMPLI_CONTENT

//--------------------------------------------------------------------------
$params = new stdClass();
$params->nameObj = "tab_elements";
$params->keyObj = ["key" => $CHOP_INTERFACE->inputs["eltKey"]];
$retour = $CHOP_INTERFACE->BD_ENGINE->getSingleObject($params);

//---------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->value = $retour;
$CHOP_INTERFACE->addDataObject($params);