<?php

namespace ACA\EC\Column\Event;

use AC;
use ACA\EC\Column\Meta;
use ACA\EC\Editing;
use ACA\EC\Search;
use ACA\EC\Settings;
use ACP;
use ACP\ConditionalFormat;

class Venue extends Meta
    implements AC\Column\Relation, ACP\Export\Exportable, ACP\ConditionalFormat\Formattable
{

    use ConditionalFormat\FilteredHtmlFormatTrait;

    public function __construct()
    {
        $this->set_type('column-ec-event_venue')
             ->set_label(__('Venue', 'codepress-admin-columns'));

        parent::__construct();
    }

    public function get_relation_object()
    {
        return new AC\Relation\Post('tribe_venue');
    }

    public function get_meta_key()
    {
        return '_EventVenueID';
    }

    public function get_value($id)
    {
        $value = $this->get_raw_value($id);

        if ( ! $value) {
            return $this->get_empty_char();
        }

        return $this->get_formatted_value($value);
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\Venue($this));
    }

    public function editing()
    {
        return new Editing\Service\Event\Venue();
    }

    public function sorting()
    {
        $field = $this->get_setting(Settings\Venue::NAME)->get_value();

        switch ($field) {
            case Settings\Venue::PROPERTY_CITY:
                return new ACP\Sorting\Model\Post\RelatedMeta\PostMeta('_VenueCity', $this->get_meta_key());
            case Settings\Venue::PROPERTY_COUNTRY:
                return new ACP\Sorting\Model\Post\RelatedMeta\PostMeta('_VenueCountry', $this->get_meta_key());
            case Settings\Venue::PROPERTY_WEBSITE:
                return new ACP\Sorting\Model\Post\RelatedMeta\PostMeta('_VenueURL', $this->get_meta_key());
            default:
                return new ACP\Sorting\Model\Post\RelatedMeta\PostField('post_title', $this->get_meta_key());
        }
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

    public function search()
    {
        return new Search\Event\Relation($this->get_meta_key(), $this->get_relation_object());
    }

}