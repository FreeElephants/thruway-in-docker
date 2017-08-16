<?php
/**
 * @author samizdam <samizdam@inbox.ru>
 */

require __DIR__ . '/../vendor/autoload.php';

use FreeElephants\DI\InjectorBuilder;
use FreeElephants\Thruway\Jwt\AbstractJwtDecoderFactory;
use FreeElephants\Thruway\Jwt\JwtValidatorInterface;
use FreeElephants\Thruway\JwtAuthenticationProvider;
use FreeElephants\Thruway\Timer\Timer;
use FreeElephants\Thruway\Timer\TimersList;
use Thruway\Authentication\AuthenticationManager;
use Thruway\Authentication\AuthorizationManager;
use Thruway\Peer\Router;
use Thruway\Peer\RouterInterface;
use Thruway\Realm;
use Thruway\Transport\RatchetTransportProvider;

define('AUTHORIZATION_ENABLE', (bool)getenv('AUTHORIZATION_ENABLE'));
define('AUTH_METHOD', getenv('AUTH_METHOD'));
define('JWT_SECRET_KEY', (string)getenv('JWT_SECRET_KEY'));
define('JWT_ALGOS', (string)getenv('JWT_ALGOS') ?: 'HS256');
define('REALM', (string)getenv('REALM'));
define('ALLOW_REALM_AUTOCREATE', (bool)getenv('REALM') ?: false);
define('THRUWAY_DEBUG_ENABLE', (bool)getenv('THRUWAY_DEBUG_ENABLE') ?: false);

define('REDIS_HOST', (string)getenv('REDIS_HOST') ?: 'redis');
define('REDIS_PORT', (int)getenv('REDIS_PORT') ?: 6379);
define('REDIS_DBINDEX', (int)getenv('REDIS_DBINDEX') ?: 1);

if (THRUWAY_DEBUG_ENABLE === false) {
    \Thruway\Logging\Logger::set(new \Psr\Log\NullLogger());
}

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
if (AUTHORIZATION_ENABLE) {
    $authorizationManager = new AuthorizationManager($realm->getRealmName());
    $router->registerModule($authorizationManager);
    // don't allow anything by default
    $authorizationManager->flushAuthorizationRules(false);
    $authorizationManager->setReady(true);
    $authRealm = new Realm('thruway.auth');
    $router->getRealmManager()->addRealm($authRealm);
}

if (AUTH_METHOD === 'jwt') {
    $allowedAlgorithms = explode(',', JWT_ALGOS);
    /**@var  $jwtDecoderFactory AbstractJwtDecoderFactory */
    $jwtDecoderFactory = $di->getService(AbstractJwtDecoderFactory::class);
    $jwtDecoder = $jwtDecoderFactory->createJwtDecoderAdapter(JWT_SECRET_KEY, $allowedAlgorithms);
    $jwtValidator = $di->getService(JwtValidatorInterface::class);
    $jwtAuthenticationProvider = new JwtAuthenticationProvider([REALM], $jwtDecoder, $jwtValidator);
    $router->addInternalClient($jwtAuthenticationProvider);
}

$router->getRealmManager()->addRealm($realm);

$router->getRealmManager()->setAllowRealmAutocreate(ALLOW_REALM_AUTOCREATE);

$transportProvider = new RatchetTransportProvider('0.0.0.0', 9000);

$router->addTransportProvider($transportProvider);

/**@var $timersList TimersList */
$timersList = $di->getService(TimersList::class);
$timersSplObjectStorage = $timersList->getTimers();
/**@var $timer Timer */
foreach ($timersSplObjectStorage as $timer) {
    $interval = $timersSplObjectStorage->offsetGet($timer);
    $realm = $router->getRealmManager()->getRealm(\REALM);
    $router->getLoop()->addPeriodicTimer($interval, function () use ($realm, $timer) {
        $timer->execute($realm);
    });
}

$router->start();
