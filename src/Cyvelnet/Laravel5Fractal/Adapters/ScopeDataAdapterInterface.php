<?php
/**
 * User: Terry
 * Date: 3/19/2015
 * Time: 1:07 PM
 */
namespace Cyvelnet\Laravel5Fractal\Adapters;


/**
 * Class ScopeResponse
 * @package Cyvelnet\Laravel5Fractal
 */
interface ScopeDataAdapterInterface
{
    /**
     * generate a json response
     * @param int $http_status
     * @param array $header
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function responseJson($http_status = 200, $header = []);

    /**
     * get the transformed array data
     * @return array
     */
    public function getArray();

    /**
     * get the fransformed json data
     * @return string
     */
    public function getJson();
}
