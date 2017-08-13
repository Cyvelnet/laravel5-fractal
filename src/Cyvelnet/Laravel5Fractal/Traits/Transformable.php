<?php

namespace Cyvelnet\Laravel5Fractal\Traits;

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

    public function collection($data, $transformer = null, $resourceKey = null, PaginatorInterface $adapter = null)
    {
        if (!$transformer) {
            $transformer = $this->getTransformer();
        }

        return $this->getService()->collection($data, $transformer, $resourceKey, $adapter);
    }

    /**
     * @param $excludes
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

    public function includes($excludes)
    {
        $this->getService()->includes($excludes);

        return $this;
    }

    /**
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
     * transformer.
     *
     * @param                                                    $data
     * @param null|mixed|\Callable                               $transformer
     * @param null                                               $resourceKey
     * @param \League\Fractal\Pagination\PaginatorInterface|null $adapter
     *
     * @return \Cyvelnet\Laravel5Fractal\Adapters\ScopeDataAdapter
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
        return [
            'Illuminate\Support\Collection',
            'Illuminate\Database\Eloquent\Collection',
            'Illuminate\Pagination\LengthAwarePaginator',
            'Illuminate\Pagination\Paginator',
        ];
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
     * determine if an object should be recognize as collection.
     *
     * @return bool
     */
    protected function isCollection($data)
    {
        if (is_array($data)) {
            return true;
        }

        $length = count($this->getCollectionClass());

        if ($length) {
            for ($i = 0; $i < $length; $i++) {
                $class = \Illuminate\Support\Arr::get($this->getCollectionClass(), $i);

                if ($data instanceof $class) {
                    return true;
                }
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
        return app('fractal');
    }
}
