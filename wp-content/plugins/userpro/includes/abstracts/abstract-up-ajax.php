<?php

if (!defined('ABSPATH')) {
    exit;
}

abstract class UP_Ajax{

    /**
     * @var array
     */
    protected $ajax_events = array();

    /**
     * Register WordPress ajax actions
     */
    public function registerAjaxEvents()
    {
        foreach ($this->ajax_events as $event => $priv) {
            add_action('wp_ajax_userpro_' . $event, array(get_class($this), $event));

            if ($priv) {
                add_action('wp_ajax_nopriv_userpro_' . $event, array(get_class($this), $event));
            }
        }
    }
}