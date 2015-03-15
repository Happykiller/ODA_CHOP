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
$params->arrayInput = array("key","lang","userId","content");
$CHOP_INTERFACE = new ChopInterface($params);

//--------------------------------------------------------------------------
// phpsql/saveElement.php?milis=123450&key=TITRE_SCENARIO1&lang=FR&userId=1&content=Mon premier scenario

//--------------------------------------------------------------------------
$params = new OdaPrepareReqSql();
$params->sql = "Select a.`id`, a.`type` from `tab_elements` a
    WHERE 1=1
    AND a.`key` = :key
;";
$params->bindsValue = [
    "key" => $CHOP_INTERFACE->inputs["key"]
];
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

$id = $retour->data->id;
$type = $retour->data->type;
if(($type == 'CST')||($type == 'IMG')||($type == 'HTML')){
    $CHOP_INTERFACE->inputs["lang"] = '';
}

$params = new stdClass();
$params->label = "id";
$params->value= $id;
$CHOP_INTERFACE->addDataStr($params);

if($id != 0){
    //--------------------------------------------------------------------------
    $params = new OdaPrepareReqSql();
    $params->sql = "UPDATE `tab_elements_datas`
        SET `date_disable` = Now(), user_id_disable = :userId
        WHERE 1=1
        AND `date_disable` is null
        AND `parent_element_id` = :id
        AND `lang` = :lang
    ;";
    $params->typeSQL = OdaLibBd::SQL_SCRIPT;
    $params->bindsValue = [
        "userId" => $CHOP_INTERFACE->inputs["userId"],
        "lang" => $CHOP_INTERFACE->inputs["lang"],
        "id" => $id
    ];
    $retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);
    
    $params = new stdClass();
    $params->label = "resultatUpdateData";
    $params->value = $retour->nombre;
    $CHOP_INTERFACE->addDataStr($params);

    //--------------------------------------------------------------------------
    $params = new OdaPrepareReqSql();
    $params->sql = "INSERT INTO  `tab_elements_datas` (
            `parent_element_id`, `data`, `lang`, `date_record`, `user_id_record`
        )
        VALUES (
            :id , :content, :lang, NOW(), :userId
        )
    ;";
    $params->bindsValue = [
        "userId" => $CHOP_INTERFACE->inputs["userId"]
        , "lang" => $CHOP_INTERFACE->inputs["lang"]
        , "id" => $id
        , "content" => $CHOP_INTERFACE->inputs["content"]
    ];
    $params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
    $retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

    $params = new stdClass();
    $params->label = "resultatInsertElementData";
    $params->value = $retour->data;
    $CHOP_INTERFACE->addDataStr($params);

    //--------------------------------------------------------------------------
    $params = new OdaPrepareReqSql();
    $params->sql = "INSERT INTO  `tab_event_elements` (
            `id_element`, `id_element_data`
        )
        VALUES (
            :id , :id_data
        )
    ;";
    $params->bindsValue = [
        "id" => $id
        , "id_data" => $retour->data
    ];
    $params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
    $retour = $CHOP_INTERFACE->BD_ENGINE->reqODASQL($params);

    $params = new stdClass();
    $params->label = "resultatInsertEvent";
    $params->value = $retour->data;
    $CHOP_INTERFACE->addDataStr($params);
}