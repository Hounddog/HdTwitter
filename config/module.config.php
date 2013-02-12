<?php
return array(
    'service_manager' => array(
        'invokables' => array(
            'HdTwitter\Client'      => 'HdTwitter\Client',
            'HdTwitter\Api\Search' => 'HdTwitter\Api\Search',

            'HdTwitter\Listener\Auth\HttpPassword' => 'HdTwitter\Listener\Auth\HttpPassword',
            'HdTwitter\Listener\Auth\HttpToken' => 'HdTwitter\Listener\Auth\HttpToken',
            'HdTwitter\Listener\Auth\UrlClientId' => 'HdTwitter\Listener\Auth\UrlClientId',
            'HdTwitter\Listener\Auth\UrlToken' => 'HdTwitter\Listener\Auth\UrlToken',
            'HdTwitter\Listener\Error' => 'HdTwitter\Listener\Error',
            'HdTwitter\HttpClient' => 'HdTwitter\Http\Client',
        ),
    ),
);
