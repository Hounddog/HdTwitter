<?php

namespace HD\Twitter\Collection;

use HD\API\Client\Http\Client;
use HD\Twitter\Api\Model\Repo as RepoModel;
use Zend\Stdlib\Hydrator;

use Closure;
use Iterator;

class RepositoryCollection implements Iterator
{
    /**
     * @var client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var array
     */
    protected $elements = array();

    protected $pagination = null;

    protected $page = 1;

    public function __construct(Client $httpClient, $path, array $parameters = array(), array $headers = array())
    {
        $this->httpClient = $httpClient;
        $this->path = $path;
        $this->headers = $headers;
        if (!isset($parameters['count'])) {
            $parameters['count'] = 100;
        }

        $this->parameters = $parameters;
    }

    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    private function loadPage()
    {
        if (array_key_exists('max_id', $this->parameters) && null == $this->parameters['max_id']) {
            return false;
        }

        $elements = $this->fetch();

        $elements = $elements->statuses;

        if (count($elements) == 0) {
            return false;
        }

        $offset = (($this->page-1) * $this->parameters['count']);

        foreach ($elements as $element) {
            $this->add($offset++, $element);
        }

        return true;
    }

    private function fetch()
    {
        $response = $this->httpClient->get($this->path, $this->parameters, $this->headers);

        $elements = json_decode($response->getBody());
        $this->getPagination($elements);
        return $elements;
    }

    public function page($page)
    {
        $this->parameters['count'] = $this->parameters['count'];
        $offsetStart = (($page-1) * $this->parameters['count']);
        $limit = $this->parameters['count'] -1;
        $elements = array();

        for ($offset=$offsetStart,$i=0; $i<=$limit; $i++, $offset++) {
            if (!$this->containsKey($offset)) {
                if ($this->loadPage($page)) {
                    if ($this->containsKey($offset)) {
                        $elements[] = $this->get($offset);
                    } else {
                        break;
                    }
                } else {
                    break;
                }
            } else {
                $elements[] = $this->get($offset);
            }
        }

        return $elements;
    }

    public function add($offset, $element)
    {
        $this->elements[$offset] = $element;
    }

    private function getPagination($response)
    {
        $metadata = $response->search_metadata;
        $this->parameters['max_id'] = null;

        if (isset($metadata->next_results)) {
            parse_str(ltrim($metadata->next_results, '?'), $this->parameters);
        }
    }

    public function rewind()
    {
        reset($this->elements);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->elements);
    }

    public function get($key)
    {
        return $this->elements[$key];
    }
    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->elements);
    }

    public function first()
    {
        $this->rewind();
        return $this->elements[$this->key()];
    }
    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return next($this->elements);
    }

    public function prev()
    {
        return prev($this->elements);
    }

    public function getIterator()
    {
        $this->rewind();
        $this->parameters['count'] = 100;

        return $this;
    }

    public function valid()
    {
        if (!$this->current()) {
            $valid = $this->loadPage();
            return $valid;
        }
        return true;
    }

    public function containsKey($key)
    {
        return array_key_exists($key, $this->elements);
    }

    public function count()
    {
        return count($this->elements);
    }

    public function indexOf($element)
    {
        return array_search($element, $this->elements);
    }

    public function removeElement($element)
    {
        $key = $this->indexOf($element);

        if ($key) {
            unset($this->elements[$key]);
            return true;
        }
        return false;
    }
}
