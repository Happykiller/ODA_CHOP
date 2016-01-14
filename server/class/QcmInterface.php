<?php
namespace Chop;

use Exception;
use Oda\OdaLibBd;
use Oda\OdaRestInterface;
use Oda\SimpleObject\OdaPrepareReqSql;
use \stdClass;
use Symfony\Component\Yaml\Yaml;

/**
 * Project class
 *
 * Tool
 *
 * @author  Fabrice Rosito <rosito.fabrice@gmail.com>
 * @version 0.150221
 */
class QcmInterface extends OdaRestInterface {
    /**
     */
    function get() {
        try {
            $filtreUserId = "";
            if(!is_null($this->inputs["userId"])){
                $filtreUserId = " AND a.`author` = ".$this->inputs["userId"];
            }

            $params = new OdaPrepareReqSql();
            $params->sql = "
                SELECT a.`id`, a.`author` as 'authorId', b.`code_user` as 'authorCode', a.`creationDate`, a.`name`, a.`lang`,
                IFNULL((SELECT count(*) as 'success'
                  FROM `tab_qcm_sessions_user` d, `tab_sessions_user_record` e
                  WHERE 1=1
                  AND d.`id` = e.`sessionUserId`
                  AND d.`qcmId` = a.`id`
                  AND e.`nbErrors` = 0
                ),0) as 'success',
                IFNULL((SELECT count(*) as 'fail'
                  FROM `tab_qcm_sessions_user` f, `tab_sessions_user_record` g
                  WHERE 1=1
                  AND f.`id` = g.`sessionUserId`
                  AND f.`qcmId` = a.`id`
                  AND g.`nbErrors` > 0
                ),0) as 'fail',
                IFNULL((SELECT count(*) as 'nbUser'
                  FROM `tab_qcm_sessions_user` h
                  WHERE 1=1
                  AND h.`qcmId` = a.`id`
                ),0) as 'nbUser'
                FROM `tab_qcm_sessions` a, `api_tab_utilisateurs` b
                WHERE 1=1
                AND a.`author` = b.`id`
                $filtreUserId
                ORDER BY a.`id` DESC
                LIMIT :odaOffset, :odaLimit
            ;";
            $params->bindsValue = [
                "odaOffset" => $this->odaOffset,
                "odaLimit" => $this->odaLimit
            ];
            $params->typeSQL = OdaLibBd::SQL_GET_ALL;
            $retour = $this->BD_ENGINE->reqODASQL($params);

            $params = new stdClass();
            $params->retourSql = $retour;
            $this->addDataObject($retour->data->data);
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }

    /**
     */
    function getFile() {
        try {
            $array = array();

            $path = __DIR__  . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "qcm" . DIRECTORY_SEPARATOR;

            $dir = new \DirectoryIterator($path);
            foreach ($dir as $fileinfo) {
                if (!$fileinfo->isDot()) {
                    if (preg_match("/[a-zA-Z]+-[a-zA-Z0-9\.]+-[a-zA-Z]+-[0-9]{6}.yaml/i", $fileinfo->getFilename())) {
                        $elt = new stdClass();
                        $elt->fileName = $fileinfo->getFilename();
                        $shortFileName = str_replace('.yaml', '', $elt->fileName);
                        $tabFileName = explode('-',$shortFileName);
                        $elt->name = $tabFileName[0];
                        $elt->version = $tabFileName[1];
                        $elt->lang = $tabFileName[2];
                        $elt->date = $tabFileName[3];
                        $array[] = $elt;
                    }
                }
            }

            $this->addDataObject($array);
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }

    /**
     */
    function create() {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "INSERT INTO `tab_qcm_sessions` (
                    `author` ,
                    `creationDate`,
                    `name`,
                    `lang`
                )
                VALUES (
                    :userId, NOW(), :name, :lang
                )
            ;";
            $params->bindsValue = [
                "userId" => $this->inputs["userId"],
                "name" => $this->inputs["name"],
                "lang" => $this->inputs["lang"]
            ];
            $params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
            $retour = $this->BD_ENGINE->reqODASQL($params);

            $params = new stdClass();
            $params->value = $retour->data;
            $this->addDataStr($params);
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }

    /**
     */
    function getByName($name,$lang) {
        try {
            $qcm = __DIR__  . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "qcm" . DIRECTORY_SEPARATOR . $name .'.'.$lang.'.yaml';
            $content = Yaml::parse(file_get_contents($qcm));

            $this->addDataObject($content);
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
}