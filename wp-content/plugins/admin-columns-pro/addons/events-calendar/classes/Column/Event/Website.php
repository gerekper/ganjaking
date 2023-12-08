<?php

namespace ACA\EC\Column\Event;

use AC;
use ACA\EC\Column\Meta;
use ACP;

class Website extends Meta
    implements ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

    public function __construct()
    {
        $this->set_type('column-ec-event_website')
             ->set_label(__('Event Website', 'codepress-admin-columns'));

        parent::__construct();
    }

    public function get_meta_key()
    {
        return '_EventURL';
    }

    public function register_settings()
    {
        $this->add_setting(new AC\Settings\Column\LinkLabel($this));
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Text($this->get_meta_key());
    }

}