<?php

namespace DynamicOOOS\Test\Orders;

use DynamicOOOS\PHPUnit\Framework\TestCase;
use DynamicOOOS\PayPalCheckoutSdk\Orders\OrdersAuthorizeRequest;
use DynamicOOOS\Test\TestHarness;
class OrdersAuthorizeTest extends TestCase
{
    public function testOrdersAuthorizeRequest()
    {
        $this->markTestSkipped("Need an approved Order ID to execute this test.");
        $request = new OrdersAuthorizeRequest('ORDER-ID');
        $request->body = $this->buildRequestBody();
        $client = TestHarness::client();
        $response = $client->execute($request);
        $this->assertEquals(201, $response->statusCode);
        $this->assertNotNull($response->result);
    }
}
