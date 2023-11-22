<?php

namespace DynamicOOOS\Test\Orders;

use DynamicOOOS\PHPUnit\Framework\TestCase;
use DynamicOOOS\PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use DynamicOOOS\Test\TestHarness;
class OrdersCaptureTest extends TestCase
{
    public function testOrdersCaptureRequest()
    {
        $this->markTestSkipped("Need an approved Order ID to execute this test.");
        $request = new OrdersCaptureRequest('ORDER-ID');
        $client = TestHarness::client();
        $response = $client->execute($request);
        $this->assertEquals(201, $response->statusCode);
        $this->assertNotNull($response->result);
    }
}
