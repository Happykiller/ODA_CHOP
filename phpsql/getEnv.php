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
$params->interface = "phpsql/getEnv";
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/getEnv.php?milis=123450

//---------------------------------------------------------------------------
$params = new stdClass();
$params->label = "resultat";
$params->value = $CHOP_INTERFACE->getVars();
$CHOP_INTERFACE->addDataObject($params);