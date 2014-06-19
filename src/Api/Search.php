<?php

namespace HD\Twitter\Api;

use HD\Api\Client\Api\AbstractApi;
use HD\Twitter\Collection\RepositoryCollection;

class Search extends AbstractApi
{
    /**
     * Get authenticated User
     *
     * @link http://developer.github.com/v3/users/
     *
     * @return array
     */
    public function show($searchParams, $max_id = null)
    {
        $httpClient =$this->getClient()->getHttpClient();
        $params = array(
            'q' => $searchParams,
            //'include_entities' => 'true'
        );
        /*if($max_id) {
            $params['max_id']=$max_id;
            $params['rpp'] = 100;
        }*/
        $collection = new RepositoryCollection($httpClient, 'search/tweets.json', $params);
        return $collection;
    }
}
