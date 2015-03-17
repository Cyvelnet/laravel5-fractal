<?php namespace Cyvelnet\Laravel5Fractal;

use Cyvelnet\Laravel5Fractal\Paginators\IlluminateLengthAwarePaginatorAdapter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\TransformerAbstract;
use Response;

/**
 * Class FractalServices
 * @package Cyvelnet\Laravel5Fractal
 */
class FractalServices
{

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @param Manager $fractalManager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return mixed
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * includes sub level data transformer.
     * @param array $includes
     * @return $this
     */
    public function includes($includes = [])
    {
        $this->manager->parseIncludes($includes);
        return $this;
    }

    /**
     * set data transformation recursion limit
     * @param $limit
     * @return $this
     */
    public function setRecursionLimit($limit)
    {
        $this->manager->setRecursionLimit($limit);
        return $this;
    }

    /**
     * set data serializer
     * @param \League\Fractal\Serializer\SerializerAbstract $serializer
     * @return $this
     */
    public function setSerializer(\League\Fractal\Serializer\SerializerAbstract $serializer)
    {
        $this->manager->setSerializer($serializer);
        return $this;
    }

    /**
     * transform item
     * @param $item
     * @param TransformerAbstract $transformer
     * @param null $resourceKey
     * @return mixed
     */
    public function item($item, TransformerAbstract $transformer, $resourceKey = null)
    {
        $resource = new Item($item, $transformer, $resourceKey);

        return $this->response($resource);
    }

    /**
     * transform a collection
     * @param $items
     * @param TransformerAbstract $transformer
     * @param null $resourceKey
     * @param PaginatorInterface $adapter
     * @return mixed
     * @internal param Closure $callback
     */
    public function collection(
        $items,
        TransformerAbstract $transformer,
        $resourceKey = null,
        PaginatorInterface $adapter = null
    ) {
        $resources = new Collection($items, $transformer, $resourceKey);

        if ($items instanceof Paginator OR $items instanceof LengthAwarePaginator) {

            // for some reason in laravel5, we might not always receive a LengthAwarePaginator
            if ($items instanceof LengthAwarePaginator and is_null($adapter)) {
                $adapter = new IlluminateLengthAwarePaginatorAdapter($items);
            } else {
                if ($items instanceof Paginator and is_null($adapter)) {
                    $adapter = new IlluminatePaginatorAdapter($items);
                }
            }

            $resources->setPaginator($adapter);

        }
        return $this->response($resources);
    }

    /**
     * return result array as json
     * @param ResourceInterface $resource
     * @return mixed
     */
    private function response(ResourceInterface $resource)
    {
        $fratalData = $this->manager->createData($resource);
        return Response::json($fratalData->toArray());
    }
}
