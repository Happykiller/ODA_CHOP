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
$params->interface = "phpsql/changeDiv";
$params->arrayInput = array("key","lang","mode","divId","divContent");
$PROJECT_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/changeDiv.php?milis=123450&key=BPAD-PRES_BPM_COMPLI&lang=FR&mode=code&divId=div_exemple&divContent=[[BPAD-PRES_BPM_COMPLI_CONTENT]]

//--------------------------------------------------------------------------
$params = new stdClass();
$params->label = "divId";
$params->value = $PROJECT_INTERFACE->inputs["divId"];
$PROJECT_INTERFACE->addDataStr($params);

//--------------------------------------------------------------------------
$object = new stdClass();
$object->strErreur = "";
$object->key = $PROJECT_INTERFACE->inputs["key"];
$object->lang = $PROJECT_INTERFACE->inputs["lang"];
$object->mode = $PROJECT_INTERFACE->inputs["mode"];
$object->data = $PROJECT_INTERFACE->inputs["divContent"];

$obtRetour = $PROJECT_INTERFACE->functionDeepTrad($object);

//--------------------------------------------------------------------------
$params = new stdClass();
$params->label = "divContent";
$params->value = $obtRetour->data;
$PROJECT_INTERFACE->addDataObject($params);