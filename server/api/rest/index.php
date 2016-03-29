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

$slim->get('/qcm/:id', function ($id) use ($slim) {
    $params = new OdaPrepareInterface();
    $params->slim = $slim;
    $INTERFACE = new QcmInterface($params);
    $INTERFACE->getById($id);
});

$slim->get('/qcm/search/file', function () use ($slim) {
    $params = new OdaPrepareInterface();
    $params->modePublic = false;
    $params->slim = $slim;
    $INTERFACE = new QcmInterface($params);
    $INTERFACE->getFiles();
});

$slim->post('/qcm/', function () use ($slim) {
    $params = new OdaPrepareInterface();
    $params->arrayInput = array("userId","name","version","lang","date","desc","title","hours","duration","details","location");
    $params->modePublic = false;
    $params->slim = $slim;
    $INTERFACE = new QcmInterface($params);
    $INTERFACE->create();
});

$slim->get('/qcm/search/', function () use ($slim) {
    $params = new OdaPrepareInterface();
    $params->arrayInput = array("name","version","lang","date");
    $params->slim = $slim;
    $INTERFACE = new QcmInterface($params);
    $INTERFACE->getContentFile();
});

//--------------------------------------------------------------------------
//---------------------------- SESSION_USER --------------------------------
$slim->post('/sessionUser/', function () use ($slim) {
    $params = new OdaPrepareInterface();
    $params->arrayInput = array("firstName","lastName","qcmId","qcmName","qcmLang","company");
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

$slim->get('/sessionUser/:userId', function ($userId) use ($slim) {
    $params = new OdaPrepareInterface();
    $params->slim = $slim;
    $INTERFACE = new SessionUserInterface($params);
    $INTERFACE->getById($userId);
});

$slim->put('/sessionUser/:userId', function ($userId) use ($slim) {
    $params = new OdaPrepareInterface();
    $params->arrayInput = array("state");
    $params->slim = $slim;
    $INTERFACE = new SessionUserInterface($params);
    $INTERFACE->updateState($userId);
});

//--------------------------------------------------------------------------
//---------------------------- Rapport -----------------------------------------
$slim->get('/rapport/qcm/:id/details/', function ($id) use ($slim) {
    $params = new OdaPrepareInterface();
    $params->slim = $slim;
    $INTERFACE = new RapportInterface($params);
    $INTERFACE->getQcmDetails($id);
});

$slim->get('/rapport/sessionUser/:id/record/', function ($id) use ($slim) {
    $params = new OdaPrepareInterface();
    $params->arrayInputOpt = array("count"=>1);
    $params->slim = $slim;
    $INTERFACE = new RapportInterface($params);
    $INTERFACE->getSessionUserRecords($id);
});

$slim->get('/rapport/sessionUser/:id/details/', function ($id) use ($slim) {
    $params = new OdaPrepareInterface();
    $params->slim = $slim;
    $INTERFACE = new RapportInterface($params);
    $INTERFACE->getSessionUserDetails($id);
});

$slim->get('/rapport/sessionUser/:id/stats/', function ($id) use ($slim) {
    $params = new OdaPrepareInterface();
    $params->slim = $slim;
    $INTERFACE = new RapportInterface($params);
    $INTERFACE->getSessionUserStats($id);
});

$slim->get('/rapport/emarg/:id', function ($id) use ($slim) {
    $params = new OdaPrepareInterface();
    $params->slim = $slim;
    $INTERFACE = new RapportInterface($params);
    $INTERFACE->getEmarg($id);
});

$slim->get('/report/:id/stats/', function ($id) use ($slim) {
    $params = new OdaPrepareInterface();
    $params->slim = $slim;
    $INTERFACE = new RapportInterface($params);
    $INTERFACE->getStats($id);
});

$slim->get('/report/:id/stats/users/general/', function ($id) use ($slim) {
    $params = new OdaPrepareInterface();
    $params->slim = $slim;
    $INTERFACE = new RapportInterface($params);
    $INTERFACE->getStatsByUserGeneral($id);
});

$slim->get('/report/:id/stats/users/details/', function ($id) use ($slim) {
    $params = new OdaPrepareInterface();
    $params->slim = $slim;
    $INTERFACE = new RapportInterface($params);
    $INTERFACE->getStatsByUserDetails($id);
});

//--------------------------------------------------------------------------

$slim->run();