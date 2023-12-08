<?php

namespace ACA\EC\Column\Venue;

use ACA\EC\Column;
use ACP\ConditionalFormat;
use ACP\Search;

class Phone extends Column\Meta
    implements ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-ec-venue_phone')
             ->set_label(__('Phone', 'codepress-admin-columns'));

        parent::__construct();
    }

    public function get_meta_key()
    {
        return '_VenuePhone';
    }

    public function search()
    {
        return new Search\Comparison\Meta\Text($this->get_meta_key());
    }

}