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

    /**
     * @param $id
     */
    function getEmarg($id) {
        try {
            $params = new OdaPrepareReqSql();
            $params->sql = "Select a.`id` as 'qcmId', a.`author` as 'qcmAuthor', a.`date` as 'qcmDate', a.`desc` as 'qcmDesc',
            a.`lang` as 'qcmLang', a.`name` as 'qcmName', a.`version` as 'qcmVersion', a.`location` as 'qcmLocation',
            a.`title` as 'qcmTitle', a.`hours` as 'qcmHours', a.`duration` as 'qcmDuration', a.`details` as 'qcmDetails',
            b.`code_user`, b.`nom` as 'firstName', b.`prenom` as 'lastName'
            FROM `tab_qcm_sessions` a, `api_tab_utilisateurs` b
            WHERE 1=1
            AND a.`author` = b.`id`
            AND a.`id` = :id
            ;";
            $params->bindsValue = [
                "id" => $id
            ];
            $params->typeSQL = OdaLibBd::SQL_GET_ONE;
            $retour = $this->BD_ENGINE->reqODASQL($params);

            $params = new stdClass();
            $params->label = "qcmDetails";
            $params->retourSql = $retour;
            $this->addDataReqSQL($params);

            $params = new OdaPrepareReqSql();
            $params->sql = "Select DISTINCT b.`sessionUserId`, a.`firstName`, a.`lastName`, a.`company`
             FROM `tab_qcm_sessions_user` a, `tab_sessions_user_record` b
             WHERE 1=1
                AND b.`sessionUserId` = a.`id`
                AND a.`qcmId` = :id
            ; ";
            $params->bindsValue = [
                "id" => $id
            ];
            $params->typeSQL = OdaLibBd::SQL_GET_ALL;
            $retour = $this->BD_ENGINE->reqODASQL($params);

            $params = new stdClass();
            $params->label = "qcmUsers";
            $params->retourSql = $retour;
            $this->addDataReqSQL($params);

            $params = new OdaPrepareReqSql();
            $params->sql = "Select DATE_FORMAT(b.`recordDate`,'%Y-%m-%d') as 'date'
             FROM `tab_qcm_sessions_user` a, `tab_sessions_user_record` b
             WHERE 1=1
                AND b.`sessionUserId` = a.`id`
                AND a.`qcmId` = :id
            GROUP BY DATE_FORMAT(b.`recordDate`,'%Y-%m-%d')
            ;";
            $params->bindsValue = [
                "id" => $id
            ];
            $params->typeSQL = OdaLibBd::SQL_GET_ALL;
            $retour = $this->BD_ENGINE->reqODASQL($params);

            $params = new stdClass();
            $params->label = "qcmDates";
            $params->retourSql = $retour;
            $this->addDataReqSQL($params);

            $params = new OdaPrepareReqSql();
            $params->sql = "DROP TEMPORARY TABLE IF EXISTS listUser;
            CREATE TEMPORARY TABLE listUser as
              Select DISTINCT b.`sessionUserId`
              FROM `tab_qcm_sessions_user` a, `tab_sessions_user_record` b
              WHERE 1=1
                    AND b.`sessionUserId` = a.`id`
                    AND a.`qcmId` = :id
            ;

            DROP TEMPORARY TABLE IF EXISTS listDate;
            CREATE TEMPORARY TABLE listDate as
            Select DISTINCT DATE_FORMAT(b.`recordDate`,'%Y-%m-%d') as 'date'
             FROM `tab_qcm_sessions_user` a, `tab_sessions_user_record` b
             WHERE 1=1
                   AND b.`sessionUserId` = a.`id`
                   AND a.`qcmId` = :id
             GROUP BY DATE_FORMAT(b.`recordDate`,'%Y-%m-%d')
            ;

            DROP TEMPORARY TABLE IF EXISTS listData;
            CREATE TEMPORARY TABLE listData as
            Select b.`sessionUserId`, DATE_FORMAT(b.`recordDate`,'%Y-%m-%d') as 'date', d.`period`
             FROM `tab_qcm_sessions_user` a, `tab_sessions_user_record` b,
               (
                 SELECT c.`id`,
                  CASE WHEN TIME(c.`recordDate`) BETWEEN '00:00:01' AND '13:00:00' THEN 1
                  WHEN TIME(c.`recordDate`) BETWEEN '13:00:01' AND '23:59:59' THEN 2
                  END as 'period'
                 FROM `tab_sessions_user_record` c
               ) d
             WHERE 1=1
                AND b.`sessionUserId` = a.`id`
                AND b.`id` = d.`id`
                AND a.`qcmId` = :id
            GROUP BY b.`sessionUserId`, a.`firstName`, a.`lastName`, DATE_FORMAT(b.`recordDate`,'%Y-%m-%d'), d.period
            ;

            DROP TEMPORARY TABLE IF EXISTS listData2;
            CREATE TEMPORARY TABLE listData2 as
              SELECT *
              FROM listData
              WHERE 1=1
            ;

            DROP TEMPORARY TABLE IF EXISTS tab1;
            CREATE TEMPORARY TABLE tab1 as
            SELECT a.`sessionUserId`, b.`date`
            FROM listUser a, listDate b
            WHERE 1=1
            ;

            DROP TEMPORARY TABLE IF EXISTS tab2;
            CREATE TEMPORARY TABLE tab2 as
              SELECT a.`sessionUserId`, b.`date`
              FROM listUser a, listDate b
              WHERE 1=1
            ;
            ;";
            $params->bindsValue = [
                "id" => $id
            ];
            $params->typeSQL = OdaLibBd::SQL_SCRIPT;
            $retour = $this->BD_ENGINE->reqODASQL($params);

            $params = new OdaPrepareReqSql();
            $params->sql = "SELECT a.`sessionUserId`, a.`date`, 1 as 'period', IF(b.`period`is null , 0, 1) as 'present'
              FROM tab1 a
                lEFT OUTER JOIN listData b
                ON 1=1
                AND a.`sessionUserId` = b.`sessionUserId`
                AND a.`date` = b.`date`
                AND b.`period` = 1
            WHERE 1=1
            UNION
            SELECT a.`sessionUserId`, a.`date`, 2 as 'period', IF(b.`period`is null , 0, 1) as 'present'
            FROM tab2 a
              lEFT OUTER JOIN listData2 b
                ON 1=1
                   AND a.`sessionUserId` = b.`sessionUserId`
                   AND a.`date` = b.`date`
                   AND b.`period` = 2
            WHERE 1=1
            ;";
            $params->bindsValue = [
                "id" => $id
            ];
            $params->typeSQL = OdaLibBd::SQL_GET_ALL;
            $retour = $this->BD_ENGINE->reqODASQL($params);

            $params = new stdClass();
            $params->label = "qcmDatas";
            $params->retourSql = $retour;
            $this->addDataReqSQL($params);
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
}