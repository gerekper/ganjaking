<?php

namespace ACA\EC\Column\Organizer;

use ACA\EC\Column;
use ACP\ConditionalFormat\ConditionalFormatTrait;
use ACP\ConditionalFormat\Formattable;
use ACP\Editing;
use ACP\Search;

class Website extends Column\Meta
    implements Formattable
{

    use ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-ec-organizer_website')
             ->set_label(__('Website', 'codepress-admin-columns'));

        parent::__construct();
    }

    public function get_meta_key()
    {
        return '_OrganizerWebsite';
    }

    public function editing()
    {
        return new Editing\Service\Basic(
            (new Editing\View\Url())->set_clear_button(true),
            new Editing\Storage\Post\Meta($this->get_meta_key())
        );
    }

    public function search()
    {
        return new Search\Comparison\Meta\Text($this->get_meta_key());
    }

}