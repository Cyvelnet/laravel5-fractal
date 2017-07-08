<?php


use League\Fractal\Serializer\JsonApiSerializer;

class JsonApiTransformationTest extends TestCase
{
    public function test_with_fieldsets_with_json_api_serializer()
    {
        $service = $this->getService();

        $data = $service->setSerializer(new JsonApiSerializer())->fieldsets(['orders' => 'item, qty'])->collection($this->getTestUserData(),
            new UserTransformerWithDefaultIncludesStub(), 'user')->getArray();

        $this->assertEquals([
            'data'     => [
                [
                    'type'          => 'user',
                    'id'            => '1',
                    'attributes'    => [
                        'name' => 'Foo',
                    ],
                    'relationships' => [
                        'orders' => [
                            'data' => [
                                [
                                    'type' => 'order',
                                    'id'   => '1',
                                ],
                                [
                                    'type' => 'order',
                                    'id'   => '2',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type'          => 'user',
                    'id'            => '2',
                    'attributes'    => [
                        'name' => 'Bar',
                    ],
                    'relationships' => [
                        'orders' => [
                            'data' => [
                                [
                                    'type' => 'order',
                                    'id'   => '1',
                                ],
                                [
                                    'type' => 'order',
                                    'id'   => '2',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                [
                    'type'       => 'order',
                    'id'         => '1',
                    'attributes' => [
                        'item' => 'item 1',
                        'qty'  => 100,
                    ],
                ],
                [
                    'type'       => 'order',
                    'id'         => '2',
                    'attributes' => [
                        'item' => 'item 2',
                        'qty'  => 200,
                    ],
                ],
            ],
        ], $data);
    }
}
