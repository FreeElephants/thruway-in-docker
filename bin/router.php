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
use Thruway\Peer\Router;
use Thruway\Peer\RouterInterface;
use Thruway\Realm;
use Thruway\Transport\RatchetTransportProvider;

define('JWT_SECRET_KEY', (string)getenv('JWT_SECRET_KEY'));
define('JWT_ALGOS', (string)getenv('JWT_ALGOS') ?: 'HS256');
define('REALM', (string)getenv('REALM'));
define('ALLOW_REALM_AUTOCREATE', (bool)getenv('REALM') ?: false);

define('REDIS_HOST', (string)getenv('REDIS_HOST') ?: 'redis');
define('REDIS_PORT', (int)getenv('REDIS_PORT') ?: 6379);
define('REDIS_DBINDEX', (int)getenv('REDIS_DBINDEX') ?: 1);
define('REDIS_HASH_NAME', (string)getenv('REDIS_HASH_NAME'));

const CONFIG_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config';
$components = require CONFIG_PATH . DIRECTORY_SEPARATOR . 'components.php';

$di = (new InjectorBuilder())->buildFromArray($components);

const EXT_CONFIG_FILE = CONFIG_PATH . DIRECTORY_SEPARATOR . 'components-ext.php';
if (file_exists(EXT_CONFIG_FILE)) {
    $extComponents = require EXT_CONFIG_FILE;
    $di->merge($extComponents);
}

/**@var $router Router */
$router = $di->getService(RouterInterface::class);

$router->registerModule(new AuthenticationManager());

$realm = new Realm(REALM);
$authorizationManager = new AuthorizationManager($realm->getRealmName());
$router->registerModule($authorizationManager);
// don't allow anything by default
$authorizationManager->flushAuthorizationRules(false);
$authorizationManager->setReady(true);

$allowedAlgorithms = explode(',', JWT_ALGOS);
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
