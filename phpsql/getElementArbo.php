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
$params->interface = "phpsql/getElementArbo";
$params->arrayInput = array("key","lang","date_update_data");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/getElementArbo.php?milis=123450&key=RESUME_SCENARIO1&lang=GB&date_update_data=2014-07-28 04:45:13

//--------------------------------------------------------------------------
$langs = $CHOP_INTERFACE->getLangs();

//--------------------------------------------------------------------------
foreach($langs->data as $key => $value) {
    $params = new OdaPrepareReqSql();
    $params->sql = "Select b.`date_record`
        from `tab_elements` a, `tab_elements_datas` b
        WHERE 1=1
        AND a.`id` = b.`parent_element_id`
        AND a.`key` = :key
        AND b.`lang` = :currentLang
        AND IF(b.`lang` = :lang,b.`date_record` != :date_update_data, true)
    ;";
    $params->bindsValue = [
        "key" => $CHOP_INTERFACE->inputs["key"]
        , "lang" => $CHOP_INTERFACE->inputs["lang"]
        , "date_update_data" => $CHOP_INTERFACE->inputs["date_update_data"]
        , "currentLang" => $value->code
    ];
    $params->typeSQL = OdaLibBd::SQL_GET_ONE;
    $retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

    $params = new stdClass();
    $params->label = $value->code;
    $params->retourSql = $retour;
    $CHOP_INTERFACE->addDataReqSQL($params);
}