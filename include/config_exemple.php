<?php
require_once(__DIR__."/../API/php/class/SimpleObject/OdaConfig.php");
$config = \Oda\OdaConfig::getInstance();
$config->domaine = "@dbDomaine@";

//for bd engine
$config->BD_ENGINE->base = '@dbName@';
$config->BD_ENGINE->mdp = '@dbPass@';