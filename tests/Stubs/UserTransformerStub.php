<?php

use League\Fractal\TransformerAbstract;

class UserTransformerStub extends TransformerAbstract
{
    /**
     * List of resources possible to include.
     *
     * @var array
     */
    protected $availableIncludes = ['orders', 'order_histories'];

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
     *
     * @return array
     */
    public function transform($resource)
    {
        return [

            'id'   => $resource['id'],
            'name' => $resource['name'],

        ];
    }

    public function includeOrders()
    {
        $orders = [
            [
                'id'   => 1,
                'item' => 'item 1',
                'qty'  => 100,
            ],
            [
                'id'   => 2,
                'item' => 'item 2',
                'qty'  => 200,
            ],
        ];

        return $this->collection($orders, new OrderTransformerStub());
    }

    public function includeOrderHistories($user, \League\Fractal\ParamBag $params)
    {
        $orders = new \Illuminate\Support\Collection([
            [
                'id'   => 1,
                'item' => 'item 1',
                'qty'  => 100,
            ],
            [
                'id'   => 2,
                'item' => 'item 2',
                'qty'  => 200,
            ],
            [
                'id'   => 3,
                'item' => 'item 3',
                'qty'  => 300,
            ],
            [
                'id'   => 4,
                'item' => 'item 4',
                'qty'  => 400,
            ],
            [
                'id'   => 5,
                'item' => 'item 5',
                'qty'  => 500,
            ],
        ]);

        list($limit, $offset) = $params->get('limit');

        $orders = $orders->slice($offset)->take($limit);

        return $this->collection($orders, new OrderTransformerStub());
    }
}
