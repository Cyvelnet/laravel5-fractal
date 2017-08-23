<?php

namespace Cyvelnet\Laravel5Fractal\Adapters;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Jsonable;
use League\Fractal\Scope;

/**
 * Class ScopeDataAdapter.
 */
class ScopeDataAdapter implements ScopeDataAdapterInterface, Jsonable
{
    /**
     * @var Scope
     */
    private $scope;

    /**
     * @param Scope $scope
     */
    public function __construct(Scope $scope)
    {
        $this->scope = $scope;
    }

    /**
     * generate a json response.
     *
     * @param int   $http_status
     * @param array $header
     *
     * @return ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function responseJson($http_status = 200, $header = [])
    {
        return response($this->getArray(), $http_status, $header);
    }

    /**
     * get the transformed array data.
     *
     * @return array
     */
    public function getArray()
    {
        return $this->scope->toArray();
    }

    /**
     * get the transformed json data.
     *
     * @param int $options
     *
     * @return string
     */
    public function getJson($options = 0)
    {
        return $this->scope->toJson($options);
    }

    /**
     * get the transformed json data.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return $this->scope->toJson($options);
    }
}
