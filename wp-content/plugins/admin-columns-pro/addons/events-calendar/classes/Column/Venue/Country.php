<?php

namespace ACA\EC\Column\Venue;

use ACA\EC\Column\Meta;
use ACA\EC\Search;
use ACP\ConditionalFormat;
use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;
use ACP\Editing\View\Select;
use Tribe__View_Helpers;

class Country extends Meta
    implements ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-ec-venue_country')
             ->set_label(__('Country', 'codepress-admin-columns'));

        parent::__construct();
    }

    public function get_meta_key()
    {
        return '_VenueCountry';
    }

    public function editing()
    {
        return new Basic(
            new Select($this->get_countries()),
            new Storage\Post\Meta($this->get_meta_key())
        );
    }

    public function search()
    {
        return new Search\Venue\Country($this->get_meta_key(), $this->get_countries());
    }

    public function get_countries()
    {
        if ( ! class_exists('Tribe__View_Helpers')) {
            return [];
        }

        $countries = Tribe__View_Helpers::constructCountries();

        return array_combine($countries, $countries);
    }

}