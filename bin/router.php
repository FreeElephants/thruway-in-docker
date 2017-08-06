<?php
/**
 * @author samizdam <samizdam@inbox.ru>
 */

require __DIR__ . '/../vendor/autoload.php';

use FreeElephants\Thruway\JwtAuthenticationProvider;
use Thruway\Authentication\AuthenticationManager;
use Thruway\Authentication\AuthorizationManager;
use Thruway\Peer\Router;
use Thruway\Transport\RatchetTransportProvider;

define('JWT_SECRET_KEY', (string)getenv('JWT_SECRET_KEY'));
define('JWT_ALGO', (string)getenv('JWT_ALGO'));
define('REALM', (string)getenv('REALM'));
define('ALLOW_REALM_AUTOCREATE', (string)getenv('REALM'));

$router = new Router();

$router->registerModule(new AuthenticationManager());

$jwtAuthenticationProvider = new JwtAuthenticationProvider([REALM], JWT_SECRET_KEY);
$router->addInternalClient($jwtAuthenticationProvider);

$authorizationManager = new AuthorizationManager(REALM);
$router->registerModule($authorizationManager);

$router->getRealmManager()->setAllowRealmAutocreate(ALLOW_REALM_AUTOCREATE);

$transportProvider = new RatchetTransportProvider('127.0.0.1', 9000);
$router->addTransportProvider($transportProvider);
$router->start();

