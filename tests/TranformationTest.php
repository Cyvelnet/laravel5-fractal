<?php

/**
 * Class TranformationTest.
 */
class TranformationTest extends Orchestra\Testbench\TestCase
{
    public function test_parameter_includes()
    {
        $service = $this->getService();

        $data = $service->includes('orders')->collection($this->getTestUserData(), new UserTransformerStub())->getArray();

        $this->assertTrue(isset($data['data'][0]['orders']));
    }

    public function test_parameter_excludes()
    {
        $service = $this->getService();

        $data = $service->excludes('orders')->collection($this->getTestUserData(), new UserTransformerWithDefaultIncludesStub())->getArray();

        $this->assertFalse(isset($data['data'][0]['orders']));
    }

    public function test_default_includes()
    {
        $service = $this->getService();

        $data = $service->collection($this->getTestUserData(), new UserTransformerWithDefaultIncludesStub())->getArray();

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

        $data = $service->excludes('orders')->collection($this->getTestUserData(), new UserTransformerWithDefaultIncludesStub())->getArray();

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

    /**
     * @return \Cyvelnet\Laravel5Fractal\FractalServices
     */
    private function getService()
    {
        return new \Cyvelnet\Laravel5Fractal\FractalServices(new \League\Fractal\Manager(), $this->app);
    }

    /**
     * @return array
     */
    private function getTestUserData()
    {
        return [
            [
                'id'   => 1,
                'name' => 'Foo',
            ],
            [
                'id'   => 2,
                'name' => 'Bar',
            ],
        ];
    }
}
