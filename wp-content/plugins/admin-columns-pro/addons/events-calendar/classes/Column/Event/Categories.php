<?php

namespace ACA\EC\Column\Event;

use AC;
use ACP;

class Categories extends AC\Column
    implements ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('events-cats')
             ->set_original(true);
    }

    public function get_taxonomy()
    {
        return 'tribe_events_cat';
    }

    // Overwrite the Edit setting with a new dependent setting
    public function register_settings()
    {
        parent::register_settings();

        $this->add_setting((new ACP\Editing\Settings\Factory\Taxonomy($this))->create());
    }

    public function editing()
    {
        return new ACP\Editing\Service\Post\Taxonomy(
            $this->get_taxonomy(),
            'on' === $this->get_option('enable_term_creation')
        );
    }

    public function export()
    {
        return new ACP\Export\Model\Post\Taxonomy($this->get_taxonomy());
    }

    public function search()
    {
        return new ACP\Search\Comparison\Post\Taxonomy($this->get_taxonomy());
    }

}