<?php


use League\Fractal\TransformerAbstract;

class OrderTransformerStub extends TransformerAbstract
{
    /**
     * List of resources possible to include.
     *
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * List of resources to automatically include.
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * Transform object into a generic array.
     *
     * @var
     * @return array
     */
    public function transform($resource)
    {
        return [
            'id'   => $resource['id'],
            'item' => $resource['item'],
            'qty'  => $resource['qty'],

        ];
    }
}
