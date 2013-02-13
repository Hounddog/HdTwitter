<?php

namespace HdTwitter\Api;

use HdTwitter\Collection\RepositoryCollection;

class Search extends AbstractApi
{
    /**
     * Get authenticatec User
     *
     * @link http://developer.github.com/v3/users/
     *
     * @return array
     */
    public function show($searchParams)
    {
        $httpClient =$this->getClient()->getHttpClient();
        $params = array(
            'q' => $searchParams,
            'include_entities' => 'true',
            'result_type' => 'mixed',
            'rpp' => '100'
        );
        $collection = new RepositoryCollection($httpClient, 'search.json', $params);
        return $collection;
    }

    /**
     * Get Repos for authenticated user
     *
     * @link http://developer.github.com/v3/repos/
     * @param array $params
     * @return array
     */
    public function repos(array $params = array())
    {
        $httpClient =$this->getClient()->getHttpClient();
        $collection = new RepositoryCollection($httpClient, 'user/repos', $params);

        return $collection;
    }

    /**
     * Get Organizations for authenticated user
     *
     * @link http://developer.github.com/v3/orgs/
     * @return array
     */
    public function orgs()
    {
        $orgs = $this->get('user/orgs');
        return json_decode($orgs);
    }
}
