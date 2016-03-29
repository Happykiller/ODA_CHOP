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
class SessionUserInterface extends OdaRestInterface {
    /**
     */
    function create() {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "INSERT INTO `tab_qcm_sessions_user` (
                    `firstName` ,
                    `lastName`,
                    `qcmId`,
                    `qcmName`,
                    `qcmLang`,
                    `createDate`,
                    `company`
                )
                VALUES (
                    :firstName, :lastName, :qcmId, :qcmName, :qcmLang, NOW(), :company
                )
            ;";
            $params->bindsValue = [
                "firstName" => $this->inputs["firstName"],
                "lastName" => $this->inputs["lastName"],
                "qcmId" => $this->inputs["qcmId"],
                "qcmName" => $this->inputs["qcmName"],
                "qcmLang" => $this->inputs["qcmLang"],
                "company" => $this->inputs["company"]
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
    function createRecord() {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "INSERT INTO `tab_sessions_user_record` (
                    `question`,
                    `nbErrors`,
                    `sessionUserId`,
                    `recordDate`
                )
                VALUES (
                    :question, :nbErrors, :sessionUserId, NOW()
                )
            ;";
            $params->bindsValue = [
                "question" => $this->inputs["question"],
                "nbErrors" => $this->inputs["nbErrors"],
                "sessionUserId" => $this->inputs["sessionUserId"]
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
     * @param $id
     */
    function getById($id) {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "SELECT a.`id`, a.`lastName`, a.`firstName`, a.`company`,
                a.`qcmId`, b.`name` as 'qcmName', b.`version` as 'qcmVersion', b.`lang` as 'qcmLang', b.`date` as 'qcmDate',
                a.`state`
                FROM `tab_qcm_sessions_user` a, `tab_qcm_sessions` b
                WHERE 1=1
                AND a.`qcmId` = b.`id`
                AND a.`id` = :id
            ;";
            $params->bindsValue = [
                "id" => $id
            ];
            $params->typeSQL = OdaLibBd::SQL_GET_ONE;
            $retour = $this->BD_ENGINE->reqODASQL($params);

            $this->addDataObject($retour->data);
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }

    /**
     * @param $id
     */
    function updateState($id) {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "UPDATE `tab_qcm_sessions_user`
                SET `state` = :state
                WHERE 1=1
                AND `id` = :id
            ;";
            $params->bindsValue = [
                "id" => $id,
                "state" => $this->inputs["state"]
            ];
            $params->typeSQL = OdaLibBd::SQL_SCRIPT;
            $retour = $this->BD_ENGINE->reqODASQL($params);

            $this->addDataObject($retour->data);
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
}