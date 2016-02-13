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
class RapportInterface extends OdaRestInterface {
    /**
     * @param $id
     */
    function getQcmDetails($id) {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "SELECT a.`id`, a.`firstName`, a.`lastName`, a.`createDate`, a.`qcmId`
                FROM `tab_qcm_sessions_user` a
                WHERE 1=1
                AND a.`qcmId` = :id
            ;";
            $params->bindsValue = [
                "id" => $id
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
     * @param $id
     */
    function getSessionUserRecords($id) {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "SELECT a.`id`, a.`question`, a.`nbErrors`, a.`recordDate`, a.`sessionUserId`
                FROM `tab_sessions_user_record` a
                WHERE 1=1
                AND a.`sessionUserId` = :id
                ORDER BY a.`id` DESC
                LIMIT 0, 2
            ;";
            $params->bindsValue = [
                "id" => $id
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
     * @param $id
     */
    function getSessionUserStats($id) {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "SELECT a.`sessionUserId`, count(a.`id`) as 'nbTest'
                FROM `tab_sessions_user_record` a
                WHERE 1=1
                AND a.`sessionUserId` = :id
            ;";
            $params->bindsValue = [
                "id" => $id
            ];
            $params->typeSQL = OdaLibBd::SQL_GET_ONE;
            $retour = $this->BD_ENGINE->reqODASQL($params);

            $params = new OdaPrepareReqSql();
            $params->sql = "SELECT a.`sessionUserId`,
                AVG(a.`nbErrors`) as 'avErrors', count(a.`id`) as 'nbFail'
                FROM `tab_sessions_user_record` a
                WHERE 1=1
                AND a.`sessionUserId` = :id
                AND a.`nbErrors` != 0
            ;";
            $params->bindsValue = [
                "id" => $id
            ];
            $params->typeSQL = OdaLibBd::SQL_GET_ONE;
            $retour2 = $this->BD_ENGINE->reqODASQL($params);

            $c = (object)array_merge((array)$retour->data, (array)$retour2->data);

            $this->addDataObject($c);
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
}