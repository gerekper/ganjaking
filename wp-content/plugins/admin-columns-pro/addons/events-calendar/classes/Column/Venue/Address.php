<?php

namespace ACA\EC\Column\Venue;

use ACA\EC\Column\Meta;
use ACP\ConditionalFormat;
use ACP\Search;

class Address extends Meta
    implements ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-ec-venue_address')
             ->set_label(__('Address', 'codepress-admin-columns'));

        parent::__construct();
    }

    public function get_meta_key()
    {
        return '_VenueAddress';
    }

    public function search()
    {
        return new Search\Comparison\Meta\Text($this->get_meta_key());
    }

}