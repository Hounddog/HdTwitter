<?php

namespace HD\Twitter;

use Zend\ModuleManager\ModuleManager;
use Zend\EventManager\StaticEventManager;

class Module
{
    public function bootstrap(ModuleManager $moduleManager, ApplicationInterface $app)
    {
        $em = $app->getEventManager()->getSharedManager();
        $sm = $app->getServiceManager();
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/',
                ),
            ),
        );
    }

    public function getConfig($env = null)
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'HD\Twitter\Client' => function ($sm) {
                    $config = $sm->get('Config');

                    $httpClient = $sm->get('HD\API\Client\Http\Client');
                    $httpClient->setOptions($config['hd-twitter']['options']);

                    $client = $sm->get('HD\API\Client\Client');
                    $client->setHttpClient($httpClient);
                    $client->setApiNamespace('HD\Twitter');
                    $client->authenticate('HD\Twitter\Listener\Auth\OAuth', $config['hd-twitter']);
                    return $client;
                },
            ),
        );
    }
}
