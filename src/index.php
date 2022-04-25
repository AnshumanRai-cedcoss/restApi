<?php 
require './vendor/autoload.php';
use Phalcon\Mvc\Micro;
use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;
use Phalcon\Events\Manager as EventsManager;

$loader = new Loader();
$loader->registerNamespaces(
    [
        'Api\Components' => './components',
        'Api\Handlers' => './handlers'
    ]
);

$loader->register();


$container = new FactoryDefault();
$app =  new Micro($container);

$container->set(
    'mongo',
    function () {
        $mongo = new \MongoDB\Client("mongodb://mongo", 
        array("username" => 'root',
            "password" => "password123"));
        return $mongo->store;
    },
    true
);

// $app['view']=function () {
//     $view = new Simple();
//     $view->setViewsDir('./views');
//     return $view;
// };

$prod = new Api\Handlers\Product();

$eventsManager = new EventsManager();
$eventsManager->attach(
    'micro',
    new Api\Components\MiddleWare()
);
$app->before(
    new Api\Components\MiddleWare()

);



$app->get(
    '/products/search/{keyword}',
    [
        $prod,
        'search'
    ]
);

// $app->get(
//     '/authorize',
//     [
//         $prod,
//         'authorize'
//     ]
// );

$app->get(
    '/products/get/{per_page}/{page}',
    [
        $prod,
        'get'
    ]
);

$app->get(
    '/createToken',
    [
        $prod,
        'createToken'
    ]
);

$app->setEventsManager($eventsManager);
$app->handle(
    $_SERVER["REQUEST_URI"]
);