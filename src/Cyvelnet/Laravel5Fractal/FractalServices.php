<?php

namespace Cyvelnet\Laravel5Fractal;

use Cyvelnet\Laravel5Fractal\Adapters\ScopeDataAdapter;
use Illuminate\Contracts\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;

/**
 * Class FractalServices.
 */
class FractalServices
{
    /**
     * @var Manager
     */
    private $manager;
    /**
     * @var bool
     */
    private $autoload;
    /**
     * @var
     */
    private $request;
    /**
     * @var string
     */
    private $input_key;
    /**
     * @var string
     */
    private $exclude_key;

    /**
     * @var array
     */
    private $meta = [];

    /**
     * @var array
     */
    private $fieldsets = [];

    /**
     * @param Manager   $manager
     * @param Container $app
     */
    public function __construct(Manager $manager, Container $app)
    {
        $this->manager = $manager;
        $this->autoload = $app['config']->get('fractal.autoload');
        $this->input_key = $app['config']->get('fractal.input_key');
        $this->exclude_key = $app['config']->get('fractal.exclude_key');
        $this->request = $app['request'];
    }

    /**
     * add additional meta data to transformed data.
     *
     * @param $key
     * @param $data
     *
     * @return $this
     */
    public function addMeta($key, $data = null)
    {
        if (is_array($key)) {
            $this->meta += $key;
        } else {
            $this->meta[$key] = $data;
        }

        return $this;
    }

    /**
     * transform resource collection.
     *
     * @param                                                       $items
     * @param \League\Fractal\TransformerAbstract|callable|\Closure $transformer
     * @param null                                                  $resourceKey
     * @param PaginatorInterface                                    $adapter
     *
     * @return \Cyvelnet\Laravel5Fractal\Adapters\ScopeDataAdapter|mixed
     */
    public function collection(
        $items,
        $transformer,
        $resourceKey = null,
        PaginatorInterface $adapter = null
    ) {
        $resources = new Collection($items, $transformer, $resourceKey);

        $this->applyMetaValue($resources);

        if ($adapter) {
            $this->withPaginator($resources, $adapter);
        }

        if ($items instanceof LengthAwarePaginator) {
            $adapter = new IlluminatePaginatorAdapter($items);
            $this->withPaginator($resources, $adapter);
        }

        return $this->scope($resources);
    }

    /**
     * excludes sub level from data transformer.
     *
     * @param string|array $excludes
     *
     * @return $this
     */
    public function excludes($excludes)
    {
        if (is_string($excludes)) {
            $excludes = explode(',', $excludes);
        }

        // when autoload is enable, we need to merge user requested includes with the predefined includes.
        if ($this->autoload and $this->request->get($this->exclude_key)) {
            $excludes = array_merge($excludes, explode(',', $this->request->get($this->exclude_key)));
        }

        $this->manager->parseExcludes($excludes);

        return $this;
    }

    /**
     * Parse field parameter.
     *
     * @param array $fieldsets Array of fields to include. It must be an array
     *                         whose keys are resource types and values a string
     *                         of the fields to return, separated by a comma
     *
     * @return $this
     */
    public function fieldsets(array $fieldsets = [])
    {
        $this->fieldsets = $fieldsets;

        return $this;
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
     *
     * @param string|array $includes
     *
     * @return $this
     */
    public function includes($includes)
    {
        if (is_string($includes)) {
            $includes = explode(',', $includes);
        }

        // when autoload is enable, we need to merge user requested includes with the predefined includes.
        if ($this->autoload and $this->request->get($this->input_key)) {
            $includes = array_merge($includes, explode(',', $this->request->get($this->input_key)));
        }

        $this->manager->parseIncludes($includes);

        return $this;
    }

    /**
     * transform a single resource.
     *
     * @param                                                       $item
     * @param \League\Fractal\TransformerAbstract|callable|\Closure $transformer
     * @param null                                                  $resourceKey
     *
     * @return \Cyvelnet\Laravel5Fractal\Adapters\ScopeDataAdapter|mixed
     */
    public function item($item, $transformer, $resourceKey = null)
    {
        $resource = new Item($item, $transformer, $resourceKey);

        $this->applyMetaValue($resource);

        return $this->scope($resource);
    }

    /**
     * set data transformation recursion limit.
     *
     * @param $limit
     *
     * @return $this
     */
    public function setRecursionLimit($limit)
    {
        $this->manager->setRecursionLimit($limit);

        return $this;
    }

    /**
     * set data serializer.
     *
     * @param \League\Fractal\Serializer\SerializerAbstract $serializer
     *
     * @return $this
     */
    public function setSerializer($serializer)
    {
        if (!$serializer) {
            return $this;
        }

        $this->manager->setSerializer($serializer);

        return $this;
    }

    private function applyMetaValue($resource)
    {
        $resource->setMeta($this->meta);
    }

    /**
     * return result scope.
     *
     * @param ResourceInterface $resource
     *
     * @return \Cyvelnet\Laravel5Fractal\Adapters\ScopeDataAdapter
     */
    private function scope(ResourceInterface $resource)
    {
        return new ScopeDataAdapter($this->manager->parseFieldsets($this->fieldsets)->createData($resource));
    }

    /**
     * set a paginator meta when a paginator instance detected.
     *
     * @param $items
     * @param $resources
     * @param $adapter
     */
    private function withPaginator(&$resources, $adapter)
    {
        $resources->setPaginator($adapter);
    }
}
