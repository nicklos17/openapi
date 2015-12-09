<?php

/**
 * Services are globally registered in this file
 */

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\DI\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;
/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();
/**
 * Registering a router
 */
$di['router'] = function() use($di)
{
    $router = new Router();

    $router->handle();
    $gateWay = new \Apiserver\Utils\GateWay($di);

    if(!$gateWay->check($router->getControllerName(), $router->getActionName()))
    {
        echo json_encode($gateWay->errMsg());exit;
    }

    $router->setDefaultModule('api_' . $gateWay->ver);

    return $router;
};

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di['url'] = function ()
{
    $url = new UrlResolver();
    $url->setBaseUri('/');

    return $url;
};
$di['view'] = function()
{
    $view = new Phalcon\Mvc\View();

    return $view;
};
/**
 * Start the session the first time some component request the session service
 */
$di['session'] = function()
{
    $session = new SessionAdapter();
    $session->start();

    return $session;
};

$verConfig = include __DIR__ . '/../config/verConfig.php';
$sysConfig = include __DIR__ . '/../config/sysConfig.php';
$flagmsg = include __DIR__ . '/../config/flagmsg.php';
$dbConfig = include __DIR__ . '/../config/database.php';
$providerInfo = include __DIR__ . '/../config/providerInfo.php';

$di['verConfig'] = function () use ($verConfig) 
{
    return $verConfig;
};
$di['sysConfig'] = function () use ($sysConfig) 
{
    return $sysConfig;
};
$di['flagmsg'] = function () use ($flagmsg) 
{
    return $flagmsg;
};
$di['providerInfo'] = function () use ($providerInfo) 
{
    return $providerInfo;
};
$di['db'] = function () use ($dbConfig)
{
    return new Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host" => $dbConfig->database->host,
        "username" => $dbConfig->database->username,
        "password" => $dbConfig->database->password,
        "dbname" => $dbConfig->database->dbname,

        "options" => array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_STRINGIFY_FETCHES => true,
        )
    ));
};