<?php
/**
 * @author samizdam <samizdam@inbox.ru>
 */

require __DIR__ . '/../vendor/autoload.php';

use FreeElephants\DI\InjectorBuilder;
use FreeElephants\Thruway\Jwt\AbstractJwtDecoderFactory;
use FreeElephants\Thruway\Jwt\JwtValidatorInterface;
use FreeElephants\Thruway\JwtAuthenticationProvider;
use Thruway\Authentication\AuthenticationManager;
use Thruway\Authentication\AuthorizationManager;
use Thruway\Peer\RouterInterface;
use Thruway\Realm;
use Thruway\Transport\RatchetTransportProvider;

define('JWT_SECRET_KEY', (string)getenv('JWT_SECRET_KEY'));
define('JWT_ALGO', (string)getenv('JWT_ALGO'));
define('REALM', (string)getenv('REALM'));
define('ALLOW_REALM_AUTOCREATE', (bool)getenv('REALM'));

const CONFIG_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config';
$components = require CONFIG_PATH . DIRECTORY_SEPARATOR . 'components.php';

const EXT_CONFIG_FILE = CONFIG_PATH . DIRECTORY_SEPARATOR . 'components-ext.php';

if (file_exists(EXT_CONFIG_FILE)) {
    $extComponents = require EXT_CONFIG_FILE;
    $components = array_merge_recursive($components, $extComponents);
}

$di = (new InjectorBuilder())->buildFromArray($components);

$router = $di->getService(RouterInterface::class);

$router->registerModule(new AuthenticationManager());

$realm = new Realm(REALM);
$authorizationManager = new AuthorizationManager($realm->getRealmName());
$router->registerModule($authorizationManager);
// don't allow anything by default
$authorizationManager->flushAuthorizationRules(false);
$authorizationManager->setReady(true);

$allowedAlgorithms = explode(',', JWT_ALGO);
/**@var  $jwtDecoderFactory AbstractJwtDecoderFactory */
$jwtDecoderFactory = $di->getService(AbstractJwtDecoderFactory::class);
$jwtDecoder = $jwtDecoderFactory->createJwtDecoderAdapter(JWT_SECRET_KEY, $allowedAlgorithms);
$jwtValidator = $di->getService(JwtValidatorInterface::class);
$jwtAuthenticationProvider = new JwtAuthenticationProvider([REALM], $jwtDecoder, $jwtValidator);
$router->addInternalClient($jwtAuthenticationProvider);

$router->getRealmManager()->addRealm($realm);

$authRealm = new Realm('thruway.auth');
$router->getRealmManager()->addRealm($authRealm);

$router->getRealmManager()->setAllowRealmAutocreate(ALLOW_REALM_AUTOCREATE);


$transportProvider = new RatchetTransportProvider('0.0.0.0', 9000);
$router->addTransportProvider($transportProvider);
$router->start();
