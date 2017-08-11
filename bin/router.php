<?php
/**
 * @author samizdam <samizdam@inbox.ru>
 */

require __DIR__ . '/../vendor/autoload.php';

use FreeElephants\Thruway\Jwt\FirebaseJwtDecoderAdapter;
use FreeElephants\Thruway\JwtAuthenticationProvider;
use Thruway\Authentication\AuthenticationManager;
use Thruway\Authentication\AuthorizationManager;
use Thruway\Peer\Router;
use Thruway\Realm;
use Thruway\Transport\RatchetTransportProvider;

define('JWT_SECRET_KEY', (string)getenv('JWT_SECRET_KEY'));
define('JWT_ALGO', (string)getenv('JWT_ALGO'));
define('REALM', (string)getenv('REALM'));
define('ALLOW_REALM_AUTOCREATE', (bool)getenv('REALM'));

$router = new Router();

$router->registerModule(new AuthenticationManager());

$realm = new Realm(REALM);
$authorizationManager = new AuthorizationManager($realm->getRealmName());
$router->registerModule($authorizationManager);
// don't allow anything by default
$authorizationManager->flushAuthorizationRules(false);
$authorizationManager->setReady(true);

$allowedAlgorithms = explode(',', JWT_ALGO);
$jwtDecoder = new FirebaseJwtDecoderAdapter(JWT_SECRET_KEY, $allowedAlgorithms);
$jwtAuthenticationProvider = new JwtAuthenticationProvider([REALM], $jwtDecoder);
$router->addInternalClient($jwtAuthenticationProvider);
$router->getRealmManager()->addRealm($realm);

$authRealm = new Realm('thruway.auth');
$router->getRealmManager()->addRealm($authRealm);

$router->getRealmManager()->setAllowRealmAutocreate(ALLOW_REALM_AUTOCREATE);


$transportProvider = new RatchetTransportProvider('0.0.0.0', 9000);
$router->addTransportProvider($transportProvider);
$router->start();
