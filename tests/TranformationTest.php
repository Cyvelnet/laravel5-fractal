<?php

use Illuminate\Support\Arr;

/**
 * Class TranformationTest.
 */
class TranformationTest extends TestCase
{
    public function test_parameter_includes()
    {
        $service = $this->getService();

        $data = $service->includes('orders')->collection($this->getTestUserData(),
            new UserTransformerStub())->getArray();

        $this->assertTrue(isset($data['data'][0]['orders']));
    }

    public function test_parameter_excludes()
    {
        $service = $this->getService();

        $data = $service->excludes('orders')->collection($this->getTestUserData(),
            new UserTransformerWithDefaultIncludesStub())->getArray();

        $this->assertFalse(isset($data['data'][0]['orders']));
    }

    public function test_default_includes()
    {
        $service = $this->getService();

        $data = $service->collection($this->getTestUserData(),
            new UserTransformerWithDefaultIncludesStub())->getArray();

        $this->assertTrue(isset($data['data'][0]['orders']));
    }

    public function test_includes_with_data()
    {
        $service = $this->getService();

        $data = $service->collection($this->getTestUserData(), new UserTransformerStub())->getArray();

        $this->assertEquals([
            'data' => [
                [
                    'id'   => 1,
                    'name' => 'Foo',
                ],
                [
                    'id'   => 2,
                    'name' => 'Bar',
                ],
            ],
        ], $data);
    }

    public function test_excludes_with_data()
    {
        $service = $this->getService();

        $data = $service->excludes('orders')->collection($this->getTestUserData(),
            new UserTransformerWithDefaultIncludesStub())->getArray();

        $this->assertEquals([
            'data' => [
                [
                    'id'   => 1,
                    'name' => 'Foo',
                ],
                [
                    'id'   => 2,
                    'name' => 'Bar',
                ],
            ],
        ], $data);
    }

    public function test_with_meta_data()
    {
        $service = $this->getService();

        $data = $service->addMeta('foo', 'bar')->collection($this->getTestUserData(),
            new UserTransformerStub())->getArray();

        $this->assertEquals([
            'data' => [
                [
                    'id'   => 1,
                    'name' => 'Foo',
                ],
                [
                    'id'   => 2,
                    'name' => 'Bar',
                ],
            ],
            'meta' => [
                'foo' => 'bar',
            ],
        ], $data);
    }

    public function test_sub_relation_with_getting_only_one_record_from_sub_relation()
    {
        $service = $this->getService();

        $data = $service->includes('order_histories:limit(1|0)')->collection($this->getTestUserData(),
            new UserTransformerStub())->getArray();

        $this->assertEquals(1, count(Arr::get($data, 'data.0.order_histories.data')));
        $this->assertEquals([
            'data' => [
                [
                    'id'              => 1,
                    'name'            => 'Foo',
                    'order_histories' => [
                        'data' => [
                            [
                                'id'   => 1,
                                'item' => 'item 1',
                                'qty'  => 100,
                            ],
                        ],
                    ],
                ],
                [
                    'id'              => 2,
                    'name'            => 'Bar',
                    'order_histories' => [
                        'data' => [
                            [
                                'id'   => 1,
                                'item' => 'item 1',
                                'qty'  => 100,
                            ],
                        ],
                    ],
                ],
            ],
        ], $data);
    }

    public function test_sub_relation_with_getting_only_n_record_from_sub_relation()
    {
        $service = $this->getService();

        $data = $service->includes('order_histories:limit(3|0)')->collection($this->getTestUserData(),
            new UserTransformerStub())->getArray();

        $this->assertEquals(3, count(Arr::get($data, 'data.0.order_histories.data')));
    }
}
