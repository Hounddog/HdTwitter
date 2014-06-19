<?php
return array(
    'service_manager' => array(
        'invokables' => array(
            'HD\Twitter\Api\Search' => 'HD\Twitter\Api\Search',
            'HD\Twitter\Listener\Auth\UrlClientId' => 'HD\Twitter\Listener\Auth\UrlClientId',
            'HD\Twitter\Listener\Error' => 'HD\Twitter\Listener\Error',
            'HD\Twitter\Listener\Auth\OAuth' => 'HD\Twitter\Listener\Auth\OAuth',
        ),
    ),
    'hd-twitter' => array(
        'options' => array(
            'base_url' => 'https://api.twitter.com/',
            'api_version' => '1.1',
            'timeout'     => 10,
        )
    ),
);
