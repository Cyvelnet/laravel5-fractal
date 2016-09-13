<?php

namespace Cyvelnet\Laravel5Fractal\Adapters;

/**
 * Class ScopeResponse.
 */
interface ScopeDataAdapterInterface
{
    /**
     * generate a json response.
     *
     * @param int   $http_status
     * @param array $header
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function responseJson($http_status = 200, $header = []);

    /**
     * get the transformed array data.
     *
     * @return array
     */
    public function getArray();

    /**
     * get the fransformed json data.
     *
     * @return string
     */
    public function getJson();
}
