<?php

namespace HD\Twitter\Listener\Auth;

use Zend\EventManager\Event;
use Zend\Validator\NotEmpty;
use HD\Api\Client\Listener\Auth\AbstractAuthListener;

class OAuth extends AbstractAuthListener
{
    protected $options;

    //protected $method;

    protected $request;

    /**
     * Add Client Id and Client Secret to Request Parameters
     *
     * @throws Exception\InvalidArgumentException
     */
    public function preSend(Event $e)
    {
        /*echo 'oauth';
        var_dump($this->options);
        exit;

        $validator = new NotEmpty();

        if (!isset($this->options['tokenOrLogin'], $this->options['password'])
            || !$validator->isValid($this->options['tokenOrLogin'])
            || !$validator->isValid($this->options['password'])
        ) {
            throw new Exception\InvalidArgumentException('You need to set username with password!');
        }
        */

        $options = array(
            'consumer_key' => 'a4870PMU1wAmynejYOD4qRlqe',
            'consumer_secret' => 'oRhgQKtmu14hiRAtkSVAmSimqy0MQqmKJ8BwZXtFYB8Bx3rNFM',
            'access_token_key' => '543645583-dX1qnp8s3ZDAvqcMpO9PS2UarKfFO0Nrw6vXLc4u',
            'access_token_secret' => 'NVhF03BdfKGvBXPYf1ycknvSU43W4fwRucJNWGVKJwLJW'
        );

        $this->options = $options;

        $this->request = $e->getTarget();

        //$this->method = $request->getMethod();
        
        //var_dump($request);
        //exit;

        //$uri = $request->getUri();
        //echo $uri;
        //var_dump($request);
        //exit;

        $headers = $this->request->getHeaders();




        //$nonce = md5(substr(microtime() . uniqid(), 0, 12));
       /* $oauth = array(
            'realm' => '',
            'oauth_consumer_key' => 'a4870PMU1wAmynejYOD4qRlqe',
            'oauth_token' => '543645583-dX1qnp8s3ZDAvqcMpO9PS2UarKfFO0Nrw6vXLc4u',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_nonce' => $nonce,
            'oauth_version' => '1.0',
            'oauth_signature' => 'Q62sHqlkeADdcppiqnRlOsdls%2BQ%3D',
        );*/


        $params = array(
           // 'Authorization' =>'OAuth ' . implode(', ', $oauth),//implode oauth here
            'Authorization' =>'OAuth ' . $this->getOauthString(),
        );
        $headers->addHeaders($params);
    }

    /* Getting OAuth parameters to be used in request headers
     *
     * @return array OAuth parameters
     */
    protected function getOauthParameters()
    {
        $time = time();

        return array(
            'oauth_consumer_key' => $this->options['consumer_key'],
            'oauth_nonce' => trim(base64_encode($time), '='),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => $time,
            'oauth_token' => $this->options['access_token_key'],
            'oauth_version' => '1.0'
        );
    }

    /**
     * Converting parameters array to a single string with encoded values
     *
     * @param array $params Input parameters
     * @return string Single string with encoded values
     */
    protected function getParams(array $params)
    {
        $r = '';

        ksort($params);

        foreach ($params as $key => $value) {
            $r .= '&' . $key . '=' . rawurlencode($value);
        }

        unset($params, $key, $value);

        return trim($r, '&');
    }



    /**
     * Converting all parameters arrays to a single string with encoded values
     *
     * @return string Single string with encoded values
     */
    protected function getRequestString()
    {

        $params = $this->request->getQuery()->toArray();
        
        $params = array_merge($params, $this->getOauthParameters());

        $params = $this->getParams($params);

        return rawurlencode($params);
    }

    /**
     * Getting OAuth signature base string
     *
     * @return string OAuth signature base string
     */
    protected function getSignatureBaseString()
    {
        $method = strtoupper($this->request->getMethod());

        $url = rawurlencode($this->request->getUri());

        return $method . '&' . $url . '&' . $this->getRequestString();
    }

    /**
     * Getting a signing key
     *
     * @return string Signing key
     */
    protected function getSigningKey()
    {
        return $this->options['consumer_secret'] . '&' . $this->options['access_token_secret'];
    }

    /**
     * Calculating the signature
     *
     * @return string Signature
     */
    protected function calculateSignature()
    {
        return base64_encode(hash_hmac('sha1', $this->getSignatureBaseString(), $this->getSigningKey(), true));
    }

    /**
     * Converting OAuth parameters array to a single string with encoded values
     *
     * @return string Single string with encoded values
     */
    protected function getOauthString()
    {
        
        $oauth = array_merge($this->getOauthParameters(), array('oauth_signature' => $this->calculateSignature()));

        ksort($oauth);

        $values = array();

        foreach ($oauth as $key => $value) {
            $values[] = $key . '="' . rawurlencode($value) . '"';
        }

        $oauth = implode(', ', $values);

        unset($values, $key, $value);

        return $oauth;
    }

}
