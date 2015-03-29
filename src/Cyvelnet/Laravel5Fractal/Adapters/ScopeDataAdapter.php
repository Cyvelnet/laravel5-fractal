<?php

namespace Cyvelnet\Laravel5Fractal\Adapters;

use Illuminate\Contracts\Routing\ResponseFactory;
use League\Fractal\Scope;

/**
 * Class ScopeResponse
 * @package Cyvelnet\Laravel5Fractal
 */
class ScopeDataAdapter implements ScopeDataAdapterInterface
{


    /**
     * @var Scope
     */
    private $scope;
    /**
     * @var ResponseFactory
     */
    private $response;

    /**
     * @param Scope $scope
     */
    function __construct(Scope $scope)
    {
        $this->scope = $scope;
    }

    /**
     * generate a json response
     * @param int $http_status
     * @param array $header
     * @return ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function responseJson($http_status = 200, $header = [])
    {
        return response($this->getArray(), $http_status, $header);
    }

    /**
     * get the transformed array data
     * @return array
     */
    public function getArray()
    {
        return $this->scope->toArray();
    }


    /**
     * get the transformed json data
     * @return string
     */
    public function getJson()
    {
        return $this->scope->toJson();
    }

}
