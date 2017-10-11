<?php

namespace Cyvelnet\Laravel5Fractal\Traits;

use Illuminate\Pagination\AbstractPaginator as Paginator;
use League\Fractal\Pagination\PaginatorInterface;

/**
 * Trait Transformable.
 */
trait Transformable
{
    /**
     * add additional meta data to transformed data.
     *
     * @param $key
     * @param $data
     *
     * @return $this
     */
    public function addMeta($key, $data)
    {
        $this->getService()->addMeta($key, $data);

        return $this;
    }

    /**
     * transform resource collection.
     *
     * @param                                                       $data
     * @param \League\Fractal\TransformerAbstract|callable|\Closure $transformer
     * @param null                                                  $resourceKey
     * @param PaginatorInterface                                    $adapter
     *
     * @return \Cyvelnet\Laravel5Fractal\Adapters\ScopeDataAdapter|mixed
     */
    public function collection($data, $transformer = null, $resourceKey = null, PaginatorInterface $adapter = null)
    {
        if (!$transformer) {
            $transformer = $this->getTransformer();
        }

        return $this->getService()->collection($data, $transformer, $resourceKey, $adapter);
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
        $this->getService()->excludes($excludes);

        return $this;
    }

    /**
     * Parse field include parameter.
     *
     * @param array $fieldsets Array of fields to include. It must be an array
     *                         whose keys are resource types and values a string
     *                         of the fields to return, separated by a comma
     *
     * @return $this
     */
    public function fieldsets($fieldsets = [])
    {
        $this->getService()->fieldsets($fieldsets);

        return $this;
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
        $this->getService()->includes($includes);

        return $this;
    }

    /**
     * transform a single resource.
     *
     * @param      $data
     * @param null $transformer
     * @param null $resourceKey
     *
     * @return \Cyvelnet\Laravel5Fractal\Adapters\ScopeDataAdapter
     */
    public function item($data, $transformer = null, $resourceKey = null)
    {
        if (!$transformer) {
            $transformer = $this->getTransformer();
        }

        return $this->getService()->item($data, $transformer, $resourceKey);
    }

    /**
     * set data serializer.
     *
     * @param \League\Fractal\Serializer\SerializerAbstract $serializer
     */
    public function serializer($serializer)
    {
        $this->getService()->setSerializer($serializer);
    }

    /**
     * transform data.
     *
     * @param                                                    $data
     * @param null|mixed|\Callable                               $transformer
     * @param null                                               $resourceKey
     * @param \League\Fractal\Pagination\PaginatorInterface|null $adapter
     *
     * @return \Cyvelnet\Laravel5Fractal\Adapters\ScopeDataAdapter|mixed
     */
    public function transform($data, $transformer = null, $resourceKey = null, PaginatorInterface $adapter = null)
    {
        if (!$transformer) {
            $transformer = $this->getTransformer();
        }

        $fractal = $this->getService();

        if ($this->isCollection($data)) {
            return $fractal->collection($data, $transformer, $resourceKey, $adapter);
        }

        return $fractal->item($data, $transformer, $resourceKey);
    }

    /**
     * classes that should recognized as a collection.
     *
     * @return array
     */
    protected function getCollectionClass()
    {
        return [];
    }

    /**
     * get transformer serializer defined in class scope.
     *
     * @return bool|string
     */
    protected function getSerializer()
    {
        if (property_exists($this, $serializer = $this->getSerializerProperty())) {
            return $serializer;
        }

        return false;
    }

    /**
     * get transformer defined in class scope.
     *
     * @return mixed|bool
     */
    protected function getTransformer()
    {
        if (property_exists($this, $transformer = $this->getTransformerProperty())) {
            return app($this->{$transformer});
        }

        return false;
    }

    /**
     * get transformer class property key.
     *
     * @return string
     */
    protected function getTransformerProperty()
    {
        if (property_exists($this, 'transformProperty')) {
            return $this->transformProperty;
        }

        return 'transformer';
    }

    /**
     * get transformer class property key.
     *
     * @return string
     */
    protected function getSerializerProperty()
    {
        if (property_exists($this, 'serializerProperty')) {
            return $this->getSerializerProperty;
        }

        return 'serializer';
    }

    /**
     * determine if an object should be recognize as collection.
     *
     * @return bool
     */
    protected function isCollection($data)
    {
        if (is_array($data) || $data instanceof \Illuminate\Support\Collection || $data instanceof Paginator) {
            return true;
        }

        $length = count($this->getCollectionClass());

        for ($i = 0; $i < $length; $i++) {
            $class = \Illuminate\Support\Arr::get($this->getCollectionClass(), $i);

            if ($data instanceof $class) {
                return true;
            }
        }

        return false;
    }

    /**
     * get a fractal services instance.
     *
     * @return \Cyvelnet\Laravel5Fractal\FractalServices
     */
    protected function getService()
    {
        return app('fractal')->setSerializer($this->getSerializer());
    }
}
