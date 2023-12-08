<?php

namespace ACA\EC\Column\Venue;

use ACA\EC\Column\Meta;
use ACP\ConditionalFormat;
use ACP\Search\Comparison\Meta\Text;

class City extends Meta
    implements ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-ec-venue_city')
             ->set_label(__('City', 'codepress-admin-columns'));

        parent::__construct();
    }

    public function get_meta_key()
    {
        return '_VenueCity';
    }

    public function search()
    {
        return new Text($this->get_meta_key());
    }

}