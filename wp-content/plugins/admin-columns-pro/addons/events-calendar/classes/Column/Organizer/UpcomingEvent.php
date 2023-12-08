<?php

namespace ACA\EC\Column\Organizer;

use AC;
use ACA\EC\Column;
use ACA\EC\Search;
use ACP;

class UpcomingEvent extends Column\UpcomingEvent
    implements AC\Column\Relation, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

    public function __construct()
    {
        parent::__construct();

        $this->set_type('column-ec-organizer_upcoming_event');
    }

    public function get_relation_object()
    {
        return new AC\Relation\Post('tribe_events');
    }

    protected function get_events_by_id($id, array $args = [])
    {
        $args['organizer'] = $id;

        return $this->get_upcoming_events($args);
    }

    public function search()
    {
        return new Search\UpcomingEvent('_EventOrganizerID');
    }

}