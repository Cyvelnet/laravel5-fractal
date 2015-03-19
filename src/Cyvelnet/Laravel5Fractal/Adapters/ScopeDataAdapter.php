<?php
/**
 * User: Terry
 * Date: 3/19/2015
 * Time: 12:08 PM
 */

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
     * @param ResponseFactory $response
     */
    function __construct(Scope $scope)
    {
        $this->scope = $scope;
    }

    /**
     * @param int $http_status
     * @param array $header
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function responseJson($http_status = 200, $header = [])
    {
        return response($this->getArray(), $http_status, $header);
    }

    /**
     *
     */
    public function getArray()
    {
        return $this->scope->toArray();
    }

    /**
     *
     */
    public function getJson()
    {
        return $this->scope->toJson();
    }

}
