<?php

namespace Chop;

require '../../header.php';
require "../../vendor/autoload.php";
require '../../config/config.php';

use cebe\markdown\GithubMarkdown;
use Oda\OdaRestInterface;
use Slim\Slim;
use \stdClass, \Oda\SimpleObject\OdaPrepareInterface, \Oda\SimpleObject\OdaPrepareReqSql, \Oda\OdaLibBd;

$slim = new Slim();
//--------------------------------------------------------------------------

$slim->notFound(function () use ($slim) {
    $params = new OdaPrepareInterface();
    $params->slim = $slim;
    $INTERFACE = new OdaRestInterface($params);
    $INTERFACE->dieInError('not found');
});

$slim->get('/', function () {
    $markdown = file_get_contents('./doc.markdown', true);
    $parser = new GithubMarkdown();
    echo $parser->parse($markdown);
});

//--------------------------------------------------------------------------
//---------------------------- QCM -----------------------------------------
$slim->get('/qcm/', function () use ($slim) {
    $params = new OdaPrepareInterface();
    $params->arrayInputOpt = array("userId"=>null);
    $params->modePublic = false;
    $params->slim = $slim;
    $INTERFACE = new QcmInterface($params);
    $INTERFACE->get();
});

$slim->get('/qcm/search/file', function () use ($slim) {
    $params = new OdaPrepareInterface();
    $params->modePublic = false;
    $params->slim = $slim;
    $INTERFACE = new QcmInterface($params);
    $INTERFACE->getFile();
});

$slim->post('/qcm/', function () use ($slim) {
    $params = new OdaPrepareInterface();
    $params->arrayInput = array("userId","name","version","lang","date","desc");
    $params->modePublic = false;
    $params->slim = $slim;
    $INTERFACE = new QcmInterface($params);
    $INTERFACE->create();
});

$slim->get('/qcm/:name/:lang', function ($name,$lang) use ($slim) {
    $params = new OdaPrepareInterface();
    $params->slim = $slim;
    $INTERFACE = new QcmInterface($params);
    $INTERFACE->getByName($name,$lang);
});

//--------------------------------------------------------------------------
//---------------------------- SESSION_USER --------------------------------
$slim->post('/sessionUser/', function () use ($slim) {
    $params = new OdaPrepareInterface();
    $params->arrayInput = array("firstName","lastName","qcmId","qcmName","qcmLang");
    $params->slim = $slim;
    $INTERFACE = new SessionUserInterface($params);
    $INTERFACE->create();
});

$slim->post('/sessionUser/record/', function () use ($slim) {
    $params = new OdaPrepareInterface();
    $params->arrayInput = array("question","nbErrors","sessionUserId");
    $params->slim = $slim;
    $INTERFACE = new SessionUserInterface($params);
    $INTERFACE->createRecord();
});

//--------------------------------------------------------------------------

$slim->run();