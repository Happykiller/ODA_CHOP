<?php
namespace Chop;
use stdClass, \Oda\OdaLib, \Oda\OdaLibBd, \Oda\OdaPrepareInterface, \Oda\OdaDate, \Oda\OdaPrepareReqSql;
/**
 * Project class
 *
 * Tool
 *
 * @author  Fabrice Rosito <rosito.fabrice@gmail.com>
 * @version 0.150221
 */
class ChopInterface extends \Oda\OdaLibInterface {
    public static $colorType = [
        "HTML" => "#FF8000",
        "TEXT" => "#08088A",
        "IMG" => "#01DF01",
        "CST" => "#FF0000",
        "NEW" => "#424242"
    ];
    /**
     * functionListPages
     * @param stdClass $p_params
     *  $p_params->keyScenar string key scenario
     * @return stdClass $objectSommaire
     */
    function buildSommaire($p_params) {
        global $objectSommaire;
        try {
            $objectSommaire = new stdClass();
            $objectSommaire->key = $p_params->keyScenar;
            $objectSommaire->titre_key = "";
            $objectSommaire->resume_key = "";
            $objectSommaire->modules = [];

            //--------------------------------------------------------------------------
            $params = new OdaPrepareReqSql();
            $params->sql = "SELECT a.`id`, a.`key`, a.`description`, a.`titre_element`, a.`resume_element`, a.`start_module`
            , titre.`key` as 'titre_key', resume.`key` as 'resume_key', defStartModule.key as 'key_startModule'
                    FROM `tab_scenario_def` a, `tab_elements` titre, `tab_elements` resume, `tab_module_def` defStartModule
                    WHERE 1=1
                    AND a.`titre_element` = titre.`id`
                    AND a.`resume_element` = resume.`id`
                    AND a.`start_module` = defStartModule.`id`
                    AND a.`date_disable` is null
                    AND titre.`date_disable` is null
                    AND resume.`date_disable` is null
                    AND defStartModule.`date_disable` is null
                    AND a.`key` = :key
                    ;
            ;";
            $params->bindsValue = [
                "key" =>  $p_params->keyScenar
            ];
            $params->typeSQL = \Oda\OdaLibBd::SQL_GET_ONE;
            $retour = $this->BD_ENGINE->reqODASQL($params);

            $objectSommaire->id = $retour->data->id;
            $objectSommaire->titre_key = $retour->data->titre_key;
            $objectSommaire->resume_key = $retour->data->resume_key;
            $this->functionListModules($retour->data->key_startModule);

            return $objectSommaire;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
    * functionListModules
    * @param stdClass $p_id_module
    */
    function functionListModules($p_key_module) {
        global $objectSommaire;
        try {
            $okTrait = true;       
            foreach ($objectSommaire->modules as $key => $value){
                if($value->titre_key == $p_key_module){
                    $okTrait = false;
                    break;
                }
            }  

            $nextModule = [];

            if($okTrait){
                $objectModule = new stdClass();
                $objectModule->id = "";
                $objectModule->titre_key = "";
                $objectModule->resume_key = "";
                $objectModule->link_module_before = [];
                $objectModule->link_module_after = [];
                $objectModule->pages = [];

                //---------------------------------------------------
                $params = new OdaPrepareReqSql();
                $params->sql = "SELECT a.`id`, a.`key`, a.`description`, a.`titre_element`, a.`resume_element`, a.`start_page`
                    , titre.`key` as 'titre_key', resume.`key` as 'resume_key', defStartPage.key as 'key_startPage'
                        FROM `tab_module_def` a, `tab_elements` titre, `tab_elements` resume, `tab_pages_def` defStartPage
                        WHERE 1=1
                        AND a.`titre_element` = titre.`id`
                        AND a.`resume_element` = resume.`id`
                        AND a.`start_page` = defStartPage.`id`
                        AND a.`date_disable` is null
                        AND titre.`date_disable` is null
                        AND resume.`date_disable` is null
                        AND defStartPage.`date_disable` is null
                        AND a.`key` = :key
                        ;
                ;";
                $params->bindsValue = [
                    "key" =>  $p_key_module
                ];
                $params->typeSQL = \Oda\OdaLibBd::SQL_GET_ONE;
                $retour = $this->BD_ENGINE->reqODASQL($params);
                
                $objectModule->id = $retour->data->id;
                $objectModule->titre_key = $retour->data->titre_key;
                $objectModule->resume_key = $retour->data->resume_key;
                $key_startPage = $retour->data->key_startPage;

                //---------------------------------------------------
                $params = new OdaPrepareReqSql();
                $params->sql = "Select b.`key`
                    from `tab_scenario_mapping` a, `tab_module_def` b
                    WHERE 1=1
                    AND a.`link_before` = b.`id`
                    AND a.`id_scenario` = :idScenario
                    AND a.`id_module` = :idModule
                    AND a.`date_disable` is null
                    AND a.`link_before` is not null
                    ORDER BY a.`link_before_weight` ASC
                ;";
                $params->bindsValue = [
                    "idScenario" => $objectSommaire->id
                    , "idModule" => $objectModule->id
                ];
                $params->typeSQL = \Oda\OdaLibBd::SQL_GET_ALL;
                $retour = $this->BD_ENGINE->reqODASQL($params);
                
                if(count($retour->nombre)>0){
                    foreach ($retour->data->data as $key => $value){
                        $objectModule->link_module_before[] = $value->key;
                    }
                    $objectModule->link_module_before = array_unique($objectModule->link_module_before);
                }

                //---------------------------------------------------
                $params = new OdaPrepareReqSql();
                $params->sql = "Select b.`key`
                    from `tab_scenario_mapping` a, `tab_module_def` b
                    WHERE 1=1
                    AND a.`link_after` = b.`id`
                    AND a.`id_scenario` = :idScenario
                    AND a.`id_module` = :idModule
                    AND a.`date_disable` is null
                    AND a.`link_after` is not null
                    ORDER BY a.`link_after_weight` ASC
                ;";
                $params->bindsValue = [
                    "idScenario" => $objectSommaire->id
                    , "idModule" => $objectModule->id
                ];
                $params->typeSQL = \Oda\OdaLibBd::SQL_GET_ALL;
                $retour = $this->BD_ENGINE->reqODASQL($params);
                
                if(count($retour->nombre)>0){
                    foreach ($retour->data->data as $key => $value){
                        $objectModule->link_module_after[] = $value->key;
                        $nextModule[] = $value->key;
                    }
                    $objectModule->link_module_after = array_unique($objectModule->link_module_after);
                }

                //---------------------------------------------------
                $objectSommaire->modules[$p_key_module] = $objectModule;

                //---------------------------------------------------
                $this->functionListPages($p_key_module, $key_startPage);

                //---------------------------------------------------
                $nextModule = array_unique($nextModule);
                foreach ($nextModule as $key => $value){
                    $this->functionListModules($value);
                }
            }
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
     * functionListPages
     * @param stdClass $p_id_page
     */
    function functionListPages($p_key_module, $p_key_page) {
        global $objectSommaire;
        try {
            $okTrait = true;       

            foreach ($objectSommaire->modules[$p_key_module]->pages as $key => $value){
                if($value->titre_key == $p_key_page){
                    $okTrait = false;
                    break;
                }
            }   

            $nextPage = [];

            if($okTrait){
                $objectPage = new stdClass();
                $objectPage->id = "";
                $objectPage->titre_key = "";
                $objectPage->link_page_before = [];
                $objectPage->link_page_after = [];

                //---------------------------------------------------
                $params = new OdaPrepareReqSql();
                $params->sql = "SELECT a.`id`, a.`key`, a.`description`, a.`titre_element`
                    , titre.`key` as 'titre_key'
                        FROM `tab_pages_def` a, `tab_elements` titre
                        WHERE 1=1
                        AND a.`titre_element` = titre.`id`
                        AND a.`date_disable` is null
                        AND titre.`date_disable` is null
                        AND a.`key` = :key
                        ;
                ;";
                $params->bindsValue = [
                    "key" =>  $p_key_page
                ];
                $params->typeSQL = \Oda\OdaLibBd::SQL_GET_ONE;
                $retour = $this->BD_ENGINE->reqODASQL($params);
                
                $objectPage->id = $retour->data->id;
                $objectPage->titre_key = $retour->data->titre_key;

                //---------------------------------------------------
                $params = new OdaPrepareReqSql();
                $params->sql = "Select b.`key`
                    from `tab_module_mapping` a, `tab_pages_def` b
                    WHERE 1=1
                    AND a.`link_before` = b.`id`
                    AND a.`id_module` = :idModule
                    AND a.`id_page` = :idPage
                    AND a.`date_disable` is null
                    AND a.`link_before` is not null
                    ORDER BY a.`link_before_weight` ASC
                ;";
                $params->bindsValue = [
                    "idModule" => $objectSommaire->modules[$p_key_module]->id
                    , "idPage" => $objectPage->id
                ];
                $params->typeSQL = \Oda\OdaLibBd::SQL_GET_ALL;
                $retour = $this->BD_ENGINE->reqODASQL($params);
                
                if(count($retour->nombre)>0){
                    foreach ($retour->data->data as $key => $value){
                        $objectPage->link_page_before[] = $value->key;
                    }
                    $objectPage->link_page_before = array_unique($objectPage->link_page_before);
                }

                //---------------------------------------------------
                $params = new OdaPrepareReqSql();
                $params->sql = "Select b.`key`
                    from `tab_module_mapping` a, `tab_pages_def` b
                    WHERE 1=1
                    AND a.`link_after` = b.`id`
                    AND a.`id_module` = :idModule
                    AND a.`id_page` = :idPage
                    AND a.`date_disable` is null
                    AND a.`link_after` is not null
                    ORDER BY a.`link_after_weight` ASC
                ;";
                $params->bindsValue = [
                    "idModule" => $objectSommaire->modules[$p_key_module]->id
                    , "idPage" => $objectPage->id
                ];
                $params->typeSQL = \Oda\OdaLibBd::SQL_GET_ALL;
                $retour = $this->BD_ENGINE->reqODASQL($params);
                
                if(count($retour->nombre)>0){
                    foreach ($retour->data->data as $key => $value){
                        $objectPage->link_page_after[] = $value->key;
                        $nextPage[] = $value->key;
                    }
                    $objectPage->link_page_after = array_unique($objectPage->link_page_after);
                }

                //---------------------------------------------------
                $objectSommaire->modules[$p_key_module]->pages[$p_key_page] = $objectPage;

                //---------------------------------------------------
                $nextPage = array_unique($nextPage);
                foreach ($nextPage as $key => $value){
                    $this->functionListPages($p_key_module, $value);
                }
            }

        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
    * functionDeepTrad
    * @param stdClass $p_var
    *  $p_var->data
    *  $p_var->lang
    * @return stdClass
    */
    function functionDeepTrad($p_var) {
        try {
            $typeElt = "";

            $pattern='#(\[[^\]]*\]])#';

            preg_match_all( $pattern, $p_var->data, $m );

            $nbElem = sizeof($m[0]);

            if($nbElem > 0){

                $vowels = array("[[", "]]");

                $mapping = [];

                foreach ($m[0] as $key => $value){
                    if(!in_array($value,$mapping)){
                        $justeKey = str_replace($vowels, "", $value);
                        $obj = new stdClass();
                        $obj->key = $justeKey;
                        $obj->trad = "";

                        $objParam = new stdClass();
                        $objParam->key = $obj->key;
                        $objParam->lang = $p_var->lang;
                        $retour = $this->functionTrad($objParam);
                        
                        if(isset($retour->data->type)){
                            $typeElt = $retour->data->type;
                            switch ($p_var->mode) {
                                case "read":
                                    $rendu = "";
                                    switch ($retour->data->type) {
                                        case "HTML":
                                            $rendu = $retour->data->data;
                                            break;
                                        case "CST":
                                            $rendu = $retour->data->data;
                                            break;
                                        case "IMG":
                                            $path = self::$config->resourcesPath.'img/'.$retour->data->data;
                                            if(file_exists($path)) {
                                                $rendu = '<img src="'.self::$config->resourcesLink.'img/'.$retour->data->data.'?'. OdaDate::getMicro() .'" style="max-width: 100%;">';
                                            }else{
                                                $rendu = '<img src="'.self::$config->resourcesLink.'img/no_image.png" style="max-width: 100%;">';
                                            }
                                            break;
                                        case "TEXT" :
                                            $rendu = $retour->data->data;
                                            break;
                                        default :
                                            $rendu = $retour->data->data;
                                            break;
                                    }

                                    if($retour->strErreur == ""){
                                        $obj->trad = $rendu; 
                                    }else{
                                        $obj->trad = $retour->strErreur;
                                    }
                                    break;
                                case "pdf":
                                    $rendu = "";
                                    switch ($retour->data->type) {
                                        case "HTML":
                                            $rendu = $retour->data->data;
                                            break;
                                        case "CST":
                                            $rendu = $retour->data->data;
                                            break;
                                        case "IMG":
                                            $path = self::$config->resourcesPath.'img/'.$retour->data->data;
                                            if(file_exists($path)) {
                                                list($width, $height, $type, $attr) = getimagesize($path);
                                                if($width > 800){
                                                    $rendu = '<img src="'.self::$config->resourcesLink.'img/'.$retour->data->data.'?'. OdaDate::getMicro() .'" style="width:120mm;">';
                                                }else{
                                                    $rendu = '<img src="'.self::$config->resourcesLink.'img/'.$retour->data->data.'?'. OdaDate::getMicro() .'">';
                                                }
                                            }else{
                                                $rendu = '<img src="'.self::$config->resourcesLink.'img/no_image.png">';
                                            }
                                            break;
                                        case "TEXT" :
                                            $rendu = $retour->data->data;
                                            break;
                                        default :
                                            $rendu = $retour->data->data;
                                            break;
                                    }

                                    if($retour->strErreur == ""){
                                        $obj->trad = $rendu; 
                                    }else{
                                        $obj->trad = $retour->strErreur;
                                    }
                                    break;
                                case "edit":
                                    $obj->trad = '<span onclick="$.functionsChop.editElement({key:\''.$justeKey.'\', lang:\''.$p_var->lang.'\', type:\''.$retour->data->type.'\', previous:\'\'});" style="color:'.self::$colorType[$typeElt].';cursor: pointer;" title="Tag type : '.$typeElt.'">'.$value.'</span>';
                                    break;
                                case "code":
                                    if($retour->strErreur == ""){
                                        if($retour->data->data == "NO_DATA"){
                                            $obj->trad = "";
                                        }else{
                                            $obj->trad = $retour->data->data; 
                                        }
                                    }else{
                                        $obj->trad = $retour->strErreur;
                                    }
                                    break;
                                default :
                                    $obj->trad = $value;
                                    break;
                            }
                        }else{
                            $obj->trad = $obj->key;
                        }

                        $mapping[$value] = $obj;
                    }
                }

                foreach ($mapping as $key => $value){
                    $p_var->data = str_replace($key, $value->trad, $p_var->data);
                }

                switch ($p_var->mode) {
                    case "pdf":
                    case "read":
                        return $this->functionDeepTrad($p_var);
                        break;
                    case "edit":
                        return $p_var;
                        break;
                    case "code":
                        preg_match_all( $pattern, $p_var->data, $m );
                        $nbElem = sizeof($m[0]);
                        $dependancies = "";
                        if($nbElem > 0){
                            $vowels = array("[[", "]]");
                            foreach ($m[0] as $key => $value){
                                $justeKeySub = str_replace($vowels, "", $value);
                                $eltDef = $this->getElementDef($justeKeySub);
                                if($eltDef){
                                    $dependancies .= '<span onclick="$.functionsChop.editElement({key:\''.$justeKeySub.'\', lang:\''.$p_var->lang.'\', type:\''.$eltDef->type.'\', previous:\''.$justeKey.'\'});" style="color:'.self::$colorType[$eltDef->type].';cursor: pointer;" title="Tag type : '.$eltDef->type.'">'.$value.'</span>, ';
                                }else{
                                    $dependancies .= '<span onclick="$.functionsChop.editElement({key:\''.$justeKeySub.'\', lang:\''.$p_var->lang.'\', type:\'NEW\', previous:\''.$justeKey.'\'});" style="color:'.self::$colorType['NEW'].';cursor: pointer;" title="Tag type : NEW">'.$value.'</span>, ';
                                }
                            }
                        }

                        if($dependancies != ""){
                            $dependancies = "<div id='content_EditElmt'>Edit elements : " . substr($dependancies, 0, -2)."</div>";
                        }

                        $strOut = "";
                        $strOut .= $p_var->data;
                        $strOut .= $dependancies;

                        $p_var->data = $strOut;

                        return $p_var;
                        break;
                    default :
                        return $p_var;
                        break;
                }
            }

            return $p_var;
        } catch (Exception $e) {
            $object = new stdClass();
            $msg = $e->getMessage();
            $object->strErreur = $msg;
            $object->strData = "";
            return $object;
        }
    }
    /**
     * 
     * @return stdClass
     */
    function functionTrad($p_var) {
        try {
            $service = null;
            
            $params = new OdaPrepareReqSql();
            $params->sql = "SELECT IFNULL(b.`data`,'NO_DATA') as 'data', a.`type`
                FROM `tab_elements` a
                    LEFT OUTER JOIN `tab_elements_datas` b
                    ON a.`id` = b.`parent_element_id`
                    AND b.`date_disable` is null
                    AND IF(a.`type` in ('HTML','IMG','CST'), true, IF(b.`LANG` = :lang,true,false))
                WHERE 1=1
                AND a.`date_disable` is null
                AND a.`key` = :key
            ;";
            $params->bindsValue = [
                "key" => $p_var->key,
                "lang" => $p_var->lang
            ];
            $params->typeSQL = \Oda\OdaLibBd::SQL_GET_ONE;
            $retour = $this->BD_ENGINE->reqODASQL($params);
            
            if(!$retour->data){
                $retour->data = new stdClass();
                $retour->data->data = "NO_DATA";
                $retour->data->type = "NEW";
            } else if(($retour->data->data == 'NO_DATA') && $retour->data->type != "NEW"){
                $params = new OdaPrepareReqSql();
                $params->sql = "SELECT  tmpTrad.`lang`, tmpTrad.`data`
                    FROM (
                    SELECT IFNULL(tabEltData.`data`,'NO_DATA') as 'data', tabEltDef.`type`, tabEltData.`lang`
                    FROM `tab_elements` tabEltDef
                            LEFT OUTER JOIN `tab_elements_datas` tabEltData
                            ON tabEltDef.`id` = tabEltData.`parent_element_id`
                            AND tabEltData.`date_disable` is null
                    WHERE 1=1
                    AND tabEltDef.`date_disable` is null
                    AND tabEltDef.`key` = :key
                    ) tmpTrad, (
                            Select UPPER(tabLang.`code`) as 'code', tabLang.`langue` , tabLang.`order`
                            from `tab_langs` tabLang
                            WHERE 1=1
                            AND tabLang.`date_disable` is null
                    ) tmpLang
                    WHERE 1=1
                    AND tmpTrad.`lang` = tmpLang.`code`
                    ORDER BY tmpLang.`order` desc
                ;";
                $params->bindsValue = [
                    "key" => $p_var->key
                ];
                $params->typeSQL = \Oda\OdaLibBd::SQL_GET_ALL;
                $retourSource = $this->BD_ENGINE->reqODASQL($params);

                if($retourSource->data->nombre > 0){
                    $sourceData = $retourSource->data->data[0]->data;
                    $sourceLang = $retourSource->data->data[0]->lang;

                    $params = new stdClass();
                    $params->method = 'POST';
                    $params->dataTypeRest = 'json';
                    $params->debug = false;

                    $input = [
                        "key" => "cleapp",
                        "source" =>  strtolower($sourceLang),
                        "q" => $sourceData,
                        "target" => strtolower($p_var->lang)
                    ];

                    $trad = "TRAD SERVICE OFFLINE";
                    switch ($service) {
                        case "mokup":
                            //{"translations": [{"translatedText": "Bonjour tout le monde"}]}
                            $retourTranslate = OdaLib::CallRest(self::$config->domaine."phpsql/mokupGoogleTranslate.php", $params, $input);
                            $jsonRetour = json_decode($retourTranslate->data);
                            $trad = $jsonRetour->translations[0]->translatedText;
                            break;
                        case "bing":
                            $ClientID="oda_chop";
                            $ClientSecret="c6u1+QaSMQXnkUiCK+txqQUe9UfijKwvXYcq64QncD8=";

                            $ClientSecret = urlencode ($ClientSecret);
                            $ClientID = urlencode($ClientID);

                            // Get a 10-minute access token for Microsoft Translator API.
                            $url = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13";
                            $postParams = "grant_type=client_credentials&client_id=$ClientID&client_secret=$ClientSecret&scope=http://api.microsofttranslator.com";

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url); 
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
                            $rsp = curl_exec($ch); 
                            if($rsp){
                                $params = new stdClass();
                                $input = [
                                    "oncomplete" => "doneCallback",
                                    "appId" =>  "oda_chop"." ".$rsp->access_token,
                                    "from" => strtolower($sourceLang),
                                    "to" => strtolower($p_var->lang),
                                    "text" => $sourceData
                                ];
                                $retourTranslate = OdaLib::CallRest("http://api.microsofttranslator.com/V2/Ajax.svc/Translate", $params, $input);
                            }
                            break;
                    }

                    $retour->data->data = $trad;
                }
            }

            return $retour;
        } catch (Exception $e) {
            $object = new stdClass();
            $msg = $e->getMessage();
            $object->strErreur = $msg;
            $object->strData = "";
            $object->strData = "";
            return $object;
        }
    }
    /**
    * getElementDef
    * @param string $name Description
    * @return stdClass $resultats
    */
    function getElementDef($elementKey) {
        try {
            $rows = array();

            $params = new OdaPrepareReqSql();
            $params->sql = "Select *
                FROM `tab_elements` a
                WHERE 1=1
                AND a.`key` = :key
            ;";

            $params->bindsValue = ["key" => $elementKey];
            $params->typeSQL = OdaLibBd::SQL_GET_ONE;
            $retour = $this->BD_ENGINE->reqODASQL($params);
            $rows = $retour->data;

            return $rows;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
     * createElement
     * @param type $p_var
     * $p_var->key
     * $p_var->userid
     * $p_var->type
     * $p_var->[
     *  $subObj->lang
     *  $subObj->description
     *  $subObj->data
     * ]
     * @return \stdClass
     */
    function createElement($p_var) {
        try {
            $object = new stdClass();
            $object->strNameFunction = __FUNCTION__;
            $object->strErreur = "";
            $object->data = [];
            $object->data["idElement"] = 0;
            $object->data["elementData"] = [];

            $params = new OdaPrepareReqSql();
            $params->sql = "INSERT INTO `tab_elements` (
                    `key`,
                    `description`,
                    `type`,
                    `date_record`,
                    `user_id_record`
                )
                VALUES (
                    :key, :description, :type, NOW(), :user_id_record
                )
            ;";
            $params->bindsValue = [
                "key"  => $p_var->key
                , "description" => $p_var->description
                , "type" => $p_var->type
                , "user_id_record" => $p_var->userid
            ];
            $params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
            $retour = $this->BD_ENGINE->reqODASQL($params);
            $object->data["idElement"] = $retour->data;

            if(($object->strErreur == "")&&(count($p_var->listElentData)>0)){
                foreach ($p_var->listElentData as $value){
                    $objectData = new stdClass();
                    $objectData->id = 0;
                    $objectData->lang = $value->lang;

                    $params = new OdaPrepareReqSql();
                    $params->sql = "INSERT INTO `tab_elements_datas` (
                            `parent_element_id`,
                            `data`,
                            `lang`,
                            `date_record`,
                            `user_id_record`
                        )
                        VALUES (
                            :parent_element_id, :data, :lang, NOW(), :user_id_record
                        )
                    ;";
                    
                    $lang = '';
                    if($p_var->type == 'TEXT'){
                        $lang = $value->lang;
                    }
                    
                    $params->bindsValue = [
                        "parent_element_id"  => $object->data["idElement"]
                        , "data" => $value->data
                        , "lang" => $lang
                        , "user_id_record" => $p_var->userid
                    ];
                    $params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
                    $retour = $this->BD_ENGINE->reqODASQL($params);
                    
                    $objectData->id = $retour->data;
                    
                    $object->data["elementData"][] = $objectData;
                }
            }

            return $object;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
    * getElementId
    * @param string $name Description
    * @return stdClass $resultats
    */
    function getElementId($elementKey) {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "Select a.`id` from `tab_elements` a
                WHERE 1=1
                AND a.`key` = :key
                AND a.`date_disable` is null
            ;";
            $params->bindsValue = [
                "key"  => $elementKey
            ];
            $params->typeSQL = OdaLibBd::SQL_GET_ONE;
            $retour = $this->BD_ENGINE->reqODASQL($params);
            $id = (empty($retour->data))?null:$retour->data->id;

            return $id;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
     * getPageId
     * @param string $name Description
     * @return stdClass $resultats
     */
    function getPageId($pageKey) {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "Select a.`id` from `tab_pages_def` a
                WHERE 1=1
                AND a.`key` = :key
                AND a.`date_disable` is null
            ;";
            $params->bindsValue = [
                "key"  => $pageKey
            ];
            $params->typeSQL = OdaLibBd::SQL_GET_ONE;
            $retour = $this->BD_ENGINE->reqODASQL($params);
            $id = (empty($retour->data))?null:$retour->data->id;

            return $id;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
    * editPage
    * @param type $p_var
    * $p_var->pageKey
    * $p_var->userid
    * $p_var->description
    * $p_var->titreKey
    * $p_var->contentKey
    * $p_var->tipsKey
    * $p_var->mode
    * @return \stdClass
    */
    function editPage($p_var) {
        try {
            $object = new stdClass();
            $object->strNameFunction = __FUNCTION__;
            $object->strErreur = "";
            $object->data = [];
            $object->data["idPage"] = 0;
            $object->data["titre"] = new stdClass();
            $object->data["content"] = new stdClass();
            $object->data["tips"] = new stdClass();

            $idTitre = 0;
            $titreKey = "";
            if($p_var->titreKey != "") {
                $titreKey = $p_var->titreKey;
                $idTitre = $this->getElementId($titreKey);
                if($idTitre == 0){
                    $inputEletTitre = new stdClass();
                    $inputEletTitre->key = $titreKey;
                    $inputEletTitre->userid = 1;
                    $inputEletTitre->type = "TEXT";
                    $inputEletTitre->description = "";
                    $inputEletTitre->listElentData = array();

                    $retour = $this->createElement($inputEletTitre);
                    $idTitre = $retour->data["idElement"];
                }
            }else{
                $titreKey = $p_var->pageKey."_TITRE";
                $idTitre = $this->getElementId($titreKey);
                if($idTitre == 0){
                    $inputEletTitre = new stdClass();
                    $inputEletTitre->key = $titreKey;
                    $inputEletTitre->userid = 1;
                    $inputEletTitre->type = "TEXT";
                    $inputEletTitre->description = "";
                    $inputEletTitre->listElentData = array();

                    $retour = $this->createElement($inputEletTitre);

                    $idTitre = $retour->data["idElement"];
                }
            }
            $object->data["titre"]->id = $idTitre;
            $object->data["titre"]->key = $titreKey;

            $idContent = 0;
            $contentKey = "";
            if($p_var->contentKey != "") {
                $contentKey = $p_var->contentKey;
                $idContent = $this->getElementId($contentKey);
                if($idContent == 0){
                    $inputEletContent = new stdClass();
                    $inputEletContent->key = $contentKey;
                    $inputEletContent->userid = 1;
                    $inputEletContent->type = "HTML";
                    $inputEletContent->description = "";
                    $inputEletContent->listElentData = array();

                    $retour = $this->createElement($inputEletContent);

                    $idContent = $retour->data["idElement"];
                }
            }else{
                $contentKey = $p_var->pageKey."_CONTENT";
                $idContent = $this->getElementId($contentKey);
                if($idContent == 0){
                    $inputEletContent = new stdClass();
                    $inputEletContent->key = $contentKey;
                    $inputEletContent->userid = 1;
                    $inputEletContent->type = "HTML";
                    $inputEletContent->description = "";
                    $inputEletContent->listElentData = array();

                    $retour = $this->createElement($inputEletContent);

                    $idContent = $retour->data["idElement"];
                }
            }
            $object->data["content"]->id = $idContent;
            $object->data["content"]->key = $contentKey;

            $idTips = 0;
            $tipsKey = "";    
            if($p_var->tipsKey != "") {
                $tipsKey = $p_var->tipsKey;
                $idTips = $this->getElementId($tipsKey);
                if($idTips == 0){
                    $inputEletTips = new stdClass();
                    $inputEletTips->key = $tipsKey;
                    $inputEletTips->userid = 1;
                    $inputEletTips->type = "HTML";
                    $inputEletTips->description = "";
                    $inputEletTips->listElentData = array();

                    $retour = $this->createElement($inputEletTips);
                    $idTips = $retour->data["idElement"];
                }
            }else{
                $tipsKey = $p_var->pageKey."_TIPS";
                $idTips = $this->getElementId($tipsKey);
                if($idTips == 0){
                    $inputEletTips = new stdClass();
                    $inputEletTips->key = $tipsKey;
                    $inputEletTips->userid = 1;
                    $inputEletTips->type = "HTML";
                    $inputEletTips->description = "";
                    $inputEletTips->listElentData = array();

                    $retour = $this->createElement($inputEletTips);

                    $idTips = $retour->data["idElement"];
                }
            }
            $object->data["tips"]->id = $idTips;
            $object->data["tips"]->key = $tipsKey;

            switch ($p_var->mode) {
                case "new":
                    $params = new OdaPrepareReqSql();
                    $params->sql = "INSERT INTO `tab_pages_def` (
                            `key`,
                            `description`,
                            `titre_element`,
                            `content_element`,
                            `tips_element`,
                            `user_id_record`,
                            `date_record`
                        )
                        VALUES (
                            :key, :description, :titre_element, :content_element, :tips_element, :user_id_record, NOW()
                        )
                    ;";
                    $params->bindsValue = [
                        "key"  => $p_var->pageKey
                        , "description" => $p_var->description
                        , "titre_element" => $idTitre
                        , "content_element" => $idContent
                        , "tips_element" => $idTips
                        , "user_id_record" => $p_var->userid
                    ];
                    $params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
                    $retour = $this->BD_ENGINE->reqODASQL($params);
                    $object->data["idPage"] = $retour->data;
                    break;
                case "edit":
                    $object->data["idPage"] = $this->getPageId($p_var->pageKey);

                    $params = new OdaPrepareReqSql();
                    $params->sql = "UPDATE `tab_pages_def` SET
                            `description` = :description,
                            `titre_element` = :titre_element,
                            `content_element` = :content_element,
                            `tips_element` = :tips_element,
                            `user_id_update` = :user_id_record,
                            `date_update` = NOW()
                        WHERE 1=1
                        AND `KEY` = :key
                    ;";
                    $params->bindsValue = [
                        "key"  => $p_var->pageKey
                        , "description" => $p_var->description
                        , "titre_element" => $idTitre
                        , "content_element" => $idContent
                        , "tips_element" => $idTips
                        , "user_id_record" => $p_var->userid
                    ];
                    $params->typeSQL = OdaLibBd::SQL_SCRIPT;
                    $retour = $this->BD_ENGINE->reqODASQL($params);
                    break;
                default :
                    break;
            }

            return $object;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
    * editModule
    * @param type $p_var
    * $p_var->moduleKey
    * $p_var->userid
    * $p_var->description
    * $p_var->startPageKey
    * $p_var->titreKey
    * $p_var->resumeKey
    * @return \stdClass
    */
    function editModule($p_var) {
        $object = new stdClass();
        $object->strNameFunction = __FUNCTION__;
        $object->strErreur = "";
        $object->data = [];
        try {
            $object->data["idModule"] = 0;
            $object->data["startPage"] = new stdClass();
            $object->data["titre"] = new stdClass();
            $object->data["resume"] = new stdClass();

            $idTitre = 0;
            $titreKey = "";
            if($p_var->titreKey != "") {
                $titreKey = $p_var->titreKey;
                $idTitre = $this->getElementId($titreKey);
                if($idTitre == 0){
                    $inputEletTitre = new stdClass();
                    $inputEletTitre->key = $titreKey;
                    $inputEletTitre->userid = 1;
                    $inputEletTitre->type = "TEXT";
                    $inputEletTitre->description = "";
                    $inputEletTitre->listElentData = array();

                    $retour = $this->createElement($inputEletTitre);
                    $idTitre = $retour->data["idElement"];
                }
            }else{
                $titreKey = $p_var->moduleKey."_TITRE";
                $idTitre = $this->getElementId($titreKey);
                if($idTitre == 0){
                    $inputEletTitre = new stdClass();
                    $inputEletTitre->key = $titreKey;
                    $inputEletTitre->userid = 1;
                    $inputEletTitre->type = "TEXT";
                    $inputEletTitre->description = "";
                    $inputEletTitre->listElentData = array();

                    $retour = $this->createElement($inputEletTitre);

                    $idTitre = $retour->data["idElement"];
                }
            }
            $object->data["titre"]->id = $idTitre;
            $object->data["titre"]->key = $titreKey;

            $idResume = 0;
            $resumeKey = "";
            if($p_var->resumeKey != "") {
                $resumeKey = $p_var->resumeKey;
                $idResume = $this->getElementId($resumeKey);
                if($idResume == 0){
                    $inputEletResume = new stdClass();
                    $inputEletResume->key = $resumeKey;
                    $inputEletResume->userid = 1;
                    $inputEletResume->type = "TEXT";
                    $inputEletResume->description = "";
                    $inputEletResume->listElentData = array();

                    $retour = $this->createElement($inputEletResume);
                    $idResume = $retour->data["idElement"];
                }
            }else{
                $resumeKey = $p_var->moduleKey."_RESUME";
                $idResume = $this->getElementId($resumeKey);
                if($idResume == 0){
                    $inputEletResume = new stdClass();
                    $inputEletResume->key = $resumeKey;
                    $inputEletResume->userid = 1;
                    $inputEletResume->type = "TEXT";
                    $inputEletResume->description = "";
                    $inputEletResume->listElentData = array();

                    $retour = $this->createElement($inputEletResume);

                    $idResume = $retour->data["idElement"];
                }
            }
            $object->data["resume"]->id = $idResume;
            $object->data["resume"]->key = $resumeKey;

            $idStartPage = 0;
            $startPageKey = "";
            if($p_var->startPageKey != "") {
                $startPageKey = $p_var->startPageKey;
                $idStartPage = $this->getPageId($startPageKey);

                if($idStartPage == 0){
                    $inputStartPage = new stdClass();
                    $inputStartPage->pageKey = $startPageKey;
                    $inputStartPage->userid = 1;
                    $inputStartPage->description = "empty";
                    $inputStartPage->titreKey = $startPageKey."_TITRE";
                    $inputStartPage->contentKey = $startPageKey."_CONTENT";
                    $inputStartPage->tipsKey = $startPageKey."_TIPS";
                    $inputStartPage->mode = "new";

                    $retour = $this->editPage($inputStartPage);
                    $idStartPage = $retour->data["idPage"];
                }
            }else {
                $startPageKey = $p_var->moduleKey."_STARTPAGE";
                $idStartPage = $this->getPageId($startPageKey);

                if($startPageKey == 0){
                    $inputStartPage = new stdClass();
                    $inputStartPage->pageKey = $startPageKey;
                    $inputStartPage->userid = 1;
                    $inputStartPage->description = "empty";
                    $inputStartPage->titreKey = $startPageKey."_TITRE";
                    $inputStartPage->contentKey = $startPageKey."_CONTENT";
                    $inputStartPage->tipsKey = $startPageKey."_TIPS";
                    $inputStartPage->mode = "new";

                    $retour = $this->editPage($inputStartPage);
                    $idStartPage = $retour->data["idPage"];
                }
            }
            $object->data["startPage"]->id = $idStartPage;
            $object->data["startPage"]->key = $startPageKey;

            switch ($p_var->mode) {
                case "new":
                    $params = new OdaPrepareReqSql();
                    $params->sql = "INSERT INTO `tab_module_def` (
                            `key`,
                            `description`,
                            `titre_element`,
                            `resume_element`,
                            `start_page`,
                            `user_id_record`,
                            `date_record`
                        )
                        VALUES (
                            :key, :description, :titre_element, :resume_element, :start_page, :user_id_record, NOW()
                        )
                    ;";
                    $params->bindsValue = [
                        "key" => $p_var->moduleKey
                        , "description" => $p_var->description
                        , "titre_element" => $idTitre
                        , "resume_element" => $idResume
                        , "start_page" => $idStartPage
                        , "user_id_record" => $p_var->userid
                    ];
                    $params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
                    $retour = $this->BD_ENGINE->reqODASQL($params);
                    $object->data["idModule"] = $retour->data;

                    $params = new OdaPrepareReqSql();
                    $params->sql = "INSERT INTO `tab_module_mapping`
                        (`id_module`, `id_page`, `date_record`, `user_id_record`)
                         VALUES 
                        (:id_module,:id_page,NOW(),:user_id_record)
                    ;";

                    $params->bindsValue = [
                        "id_module" => [ "value" => $object->data["idModule"]]
                        , "id_page" => [ "value" => $idStartPage]
                        , "user_id_record" => [ "value" => $p_var->userid]
                    ];
                    $params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
                    $retour = $this->BD_ENGINE->reqODASQL($params);

                    break;
                case "edit":
                    $object->data["idModule"] = $this->getModuleId($p_var->moduleKey);

                    $params = new OdaPrepareReqSql();
                    $params->sql = "UPDATE `tab_module_def` SET
                            `description` = :description,
                            `titre_element` = :titre_element,
                            `resume_element` = :resume_element,
                            `start_page` = :start_page,
                            `user_id_update` = :user_id_record,
                            `date_update` = NOW()
                        WHERE 1=1
                        AND `KEY` = :key
                    ;";
                    $params->bindsValue = [
                        "key" => $p_var->moduleKey
                        , "description" => $p_var->description
                        , "titre_element" => $idTitre
                        , "resume_element" => $idResume
                        , "start_page" => $idStartPage
                        , "user_id_record" => $p_var->userid
                    ];
                    $params->typeSQL = OdaLibBd::SQL_SCRIPT;
                    $retour = $this->BD_ENGINE->reqODASQL($params);
                    break;
                default :
                    break;
            }

            return $object;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
    * createImgWaterMark
    * @param string $text
    * @return boolean 
    */
    function createImgWaterMark($text, $path) {
        try {
            $boolReturn = true;

            $str = $text;
            $font = 5;

            //
            $textWidth = imagefontwidth( $font ) * strlen( $str ) + 20;

            // Nouvelle image 100*30
            $im = imagecreate($textWidth, 30);

            // Fond blanc et texte bleu
            $bg = imagecolorallocate($im, 255, 255, 255);
            $textcolor = imagecolorallocate($im, 0, 0, 255);

            // Ajout de la phrase en haut à gauche
            imagestring($im, 5, 15, 5, $str, $textcolor);

            // Rotation
            $rotate = imagerotate($im, 45, 0);

            // Affichage
            imagepng($rotate,$path);

            //
            imagedestroy($im);

            return $boolReturn;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
    * getLangs
    * @return stdClass $resultats
    */
    function getLangs() {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "Select UPPER(`code`) as 'code', `langue` from `tab_langs` a
                WHERE 1=1
                AND a.`date_disable` is null
                ORDER BY `order` desc
            ;";
            $params->typeSQL = OdaLibBd::SQL_GET_ALL;
            $retour = $this->BD_ENGINE->reqODASQL($params);
            return $retour->data;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
    * getRenderContentPage
    * @param stdClass $p_params
    *  $p_params->contentKey string
    *  $p_params->lang string
    * @return string
    */
    function getRenderContentPage($p_params) {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "SELECT a.`key`, a.`description`, a.`titre_element`, a.`content_element`, a.`tips_element`, a.`date_record`, a.`user_id_record`, a.`date_disable`, a.`user_id_disable`
                , titre.`key` as 'titre_key', content.`key` as 'content_key', tips.`key` as 'tips_key'
                FROM `tab_pages_def` a, `tab_elements` titre, `tab_elements` content, `tab_elements` tips 
                WHERE 1=1
                AND a.`titre_element` = titre.`id`
                AND a.`content_element` = content.`id`
                AND a.`tips_element` = tips.`id`
                AND a.`date_disable` is null
                AND titre.`date_disable` is null
                AND content.`date_disable` is null
                AND tips.`date_disable` is null
                AND a.`key` = :page
                ;
            ;";
            $params->bindsValue = [
                "page" => $p_params->contentKey
            ];
            $params->typeSQL = OdaLibBd::SQL_GET_ONE;
            $retour = $this->BD_ENGINE->reqODASQL($params);  
            $rows = $retour->data;

            $retour = new stdClass();
            
            $myContent = new stdClass();
            $myContent->data = "[[".$rows->titre_key."]]";
            $myContent->lang = $p_params->lang;
            $myContent->mode = "pdf";
            $obj = $this->functionDeepTrad($myContent);
            $retour->titre = $obj->data;

            $myContent = new stdClass();
            $myContent->data = "[[".$rows->content_key."]]";
            $myContent->lang = $p_params->lang;
            $myContent->mode = "pdf";
            $obj = $this->functionDeepTrad($myContent);
            $retour->content = $obj->data;

            return $retour;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }  
    /**
    * @return stdClass 
    */
    function getVars() {
        try {
            $obj = new stdClass();
   
            $obj->_colorType = self::$colorType;

            return $obj;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
    * getScenarioId
    * @param string $key
    * @return int $id
    */
    function getScenarioId($key) {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "Select a.`id` from `tab_scenario_def` a
                WHERE 1=1
                AND a.`key` = :key
                AND a.`date_disable` is null
            ;";
            $params->bindsValue = [
                "key"  => $key
            ];
            $params->typeSQL = OdaLibBd::SQL_GET_ONE;
            $retour = $this->BD_ENGINE->reqODASQL($params);
            $id = (empty($retour->data))?null:$retour->data->id;

            return $id;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
    * getModuleId
    * @param string $key
    * @return int $id
    */
    function getModuleId($key) {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "Select a.`id` from `tab_module_def` a
                WHERE 1=1
                AND a.`key` = :key
                AND a.`date_disable` is null
            ;";
            $params->bindsValue = [
                "key"  => $key
            ];
            $params->typeSQL = OdaLibBd::SQL_GET_ONE;
            $retour = $this->BD_ENGINE->reqODASQL($params);
            $id = (empty($retour->data))?null:$retour->data->id;

            return $id;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
}