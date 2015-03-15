<?php
namespace ProjectExemple;
use stdClass;
/**
 * Project class
 *
 * Tool
 *
 * @author  Fabrice Rosito <rosito.fabrice@gmail.com>
 * @version 0.150221
 */
class ProjectExempleInterface extends \Oda\OdaLibInterface {
    /**
     * sayHello
     * @return string
     */
    function sayHello() {
        try {
            return "hello";
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
}