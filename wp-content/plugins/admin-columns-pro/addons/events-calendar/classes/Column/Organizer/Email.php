<?php

namespace ACA\EC\Column\Organizer;

use ACA\EC\Column;
use ACP;
use ACP\Editing\Service\Basic;
use ACP\Search;

class Email extends Column\Meta
    implements ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-ec-organizer_email')
             ->set_label(__('Email', 'codepress-admin-columns'));

        parent::__construct();
    }

    public function get_meta_key()
    {
        return '_OrganizerEmail';
    }

    public function editing()
    {
        return new Basic(
            (new ACP\Editing\View\Email())->set_clear_button(true),
            new ACP\Editing\Storage\Post\Meta($this->get_meta_key())
        );
    }

    public function search()
    {
        return new Search\Comparison\Meta\Text($this->get_meta_key());
    }

}