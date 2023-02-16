<?php
/**
* RSVP confirmation html for pdf
*/


	$RSVP = new EVO_RSVP_CPT($args['rsvp_id']);
	$eRSVP = new EVORS_Event( $RSVP->event_id(), $RSVP->repeat_interval());

	$EVENT = $eRSVP->event;
	$EVENT->get_event_post();
?>

