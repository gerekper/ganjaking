<?php
/**
 * Deprecated items for eventon
 * @version 4.5.5
 */

// evo version 2.6.2
class evo_this_event extends EVO_Event{

	public function __construct($event_id){
		_deprecated_function( 'evo_this_event()', 'EventON 2.6.1' ,'EVO_Event()');
		_deprecated_function( 'separate_eventlist_to_months()', 'EventON 2.8' ,'_generate_events()');
		_deprecated_function( 'load_google_maps_api()', 'EventON 3.0.7' ,'EVO()->frontend->load_google_maps_api()');
		_deprecated_hook('eventon_eventcard_additions','EventON 3.0.5', 'evo_eventcard_array_aftersorted','Use this filter instead to modify eventcard content');
		
		parent::__construct($event_id);

		// deprecated filters		
	}
}