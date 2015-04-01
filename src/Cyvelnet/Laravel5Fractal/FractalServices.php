<?php namespace Cyvelnet\Laravel5Fractal;

use Cyvelnet\Laravel5Fractal\Adapters\ScopeDataAdapter;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\TransformerAbstract;

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
    private $autoload;
    private $request;


    /**
     * @param Manager $manager
     * @param Container $app
     */
    public function __construct(Manager $manager, Container $app)
    {
        $this->manager = $manager;
        $this->autoload = $app['config']->get('fractal.autoload');
        $this->input_key = $app['config']->get('fractal.input_key');
        $this->request = $app['request'];
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
    public function includes(array $includes)
    {
        // when autoload is enable, we need to merge user requested includes with the predefined includes.
        if ($this->autoload AND $this->request->get($this->input_key)) {
            $includes = array_merge($includes, explode(',', $this->request->get($this->input_key)));
        }

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
     * @return \Cyvelnet\Laravel5Fractal\Adapters\ScopeDataAdapter
     */
    public function item($item, TransformerAbstract $transformer, $resourceKey = null)
    {
        $resource = new Item($item, $transformer, $resourceKey);

        return $this->scope($resource);
    }

    /**
     * transform a collection
     * @param $items
     * @param TransformerAbstract $transformer
     * @param null $resourceKey
     * @param PaginatorInterface $adapter
     * @return \Cyvelnet\Laravel5Fractal\Adapters\ScopeDataAdapter
     */
    public function collection(
        $items,
        TransformerAbstract $transformer,
        $resourceKey = null,
        PaginatorInterface $adapter = null
    ) {
        $resources = new Collection($items, $transformer, $resourceKey);

        if ($items instanceof LengthAwarePaginator) {
            $this->withPaginator($items, $resources, $adapter);
        }
        return $this->scope($resources);
    }

    /**
     * return result scope
     * @param ResourceInterface $resource
     * @return \Cyvelnet\Laravel5Fractal\Adapters\ScopeDataAdapter
     */
    private function scope(ResourceInterface $resource)
    {
        return new ScopeDataAdapter($this->manager->createData($resource));
    }

    /**
     * set a paginator meta when a paginator instance detected
     * @param $items
     * @param $resources
     * @param $adapter
     */
    private function withPaginator($items, &$resources, $adapter)
    {
        $adapter = new IlluminatePaginatorAdapter($items);
        $resources->setPaginator($adapter);
    }

}
