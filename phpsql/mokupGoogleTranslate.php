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
$params->arrayInput = array("key","target","source", "q");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/mokupGoogleTranslate.php?milis=123450&key=MYKEY&source=en&target=nl&q=Hello world

//--------------------------------------------------------------------------
$strTrad = "";

switch ($CHOP_INTERFACE->inputs["target"])
{
    case "fr": //French
        $strTrad = '{"translations": [{"translatedText": "Bonjour tout le monde"}]}';
        break;    
    case "en": //English
        $strTrad = '{"translations": [{"translatedText": "Hello world"}]}';
        break;     
    case "nl": //Dutch
        $strTrad = '{"translations": [{"translatedText": "Hallo Welt"}]}';
        break;    
    case "ja": //Japanese
        $strTrad = '{"translations": [{"translatedText": "こんにちは"}]}';
        break;      
    case "es": //Spanish
        $strTrad = '{"translations": [{"translatedText": "¡Hola, mundo"}]}';
        break; 
    default:
        $strTrad = $CHOP_INTERFACE->inputs["q"];
}

$CHOP_INTERFACE->addDataStr($strTrad);