<?php
/**
 * EventON Addons list - Local copy
 * @version 0.8
 */

class EVO_Addons_List{


public function get_list(){
// list of addons
	$addons = apply_filters('evo_addons_details_list',array(
		'eventon-action-user' => array(
			'id'=>'EVOAU',
			'name'=>'Action User',
			'link'=>'http://www.myeventon.com/addons/action-user/',
			'download'=>'http://www.myeventon.com/addons/action-user/',
			'desc'=>'Wanna get event contributors involved in your EventON calendar with better permission control? You can do that plus lot more with Action User addon.',
		),'eventon-action-user-plus' => array(
			'id'=>'EVOAUP',
			'name'=>'Action User Plus',
			'link'=>'http://www.myeventon.com/addons/action-user-plus/',
			'download'=>'http://www.myeventon.com/addons/action-user-plus/',
			'desc'=>'Add extra features to actionUser such as user role permission and paid submissions',
		),'eventon-daily-view' => array(
			'id'=>'EVODV',
			'name'=>'Daily View Addon',
			'link'=>'http://www.myeventon.com/addons/daily-view/',
			'download'=>'http://www.myeventon.com/addons/daily-view/',
			'desc'=>'Do you have too many events to fit in one month and you want to organize them into days? This addon will allow you to showcase events for one day of the month at a time.',
		),'eventon-full-cal'=>array(
			'id'=>'EVOFC',
			'name'=>'Full Cal',
			'link'=>'http://www.myeventon.com/addons/full-cal/',
			'download'=>'http://www.myeventon.com/addons/full-cal/',
			'desc'=>'The list style calendar works for you but you would really like a full grid calendar? Here is the addon that will convert EventON to a full grid calendar view.'
		),
		'eventon-events-map'=>array(
			'id'=>'EVEM',// it is EVEM in myeventon
			'name'=>'EventsMap',
			'link'=>'http://www.myeventon.com/addons/events-map/',
			'download'=>'http://www.myeventon.com/addons/events-map/',
			'desc'=>'What is an event calendar without a map of all events? EventsMap is just the tool that adds a big google map with all the events for visitors to easily find events by location.'
		),
		'eventon-event-lists'=>array(
			'id'=>'EVEL',
			'name'=>'Event Lists Ext.',
			'link'=>'http://www.myeventon.com/addons/event-lists-extended/',
			'download'=>'http://www.myeventon.com/addons/event-lists-extended/',
			'desc'=>'Do you need to show events list regardless of what month the events are on? With this adodn you can create various event lists including past events, next 5 events, upcoming events and etc.'
		)		
		,'eventon-csv-importer'=>array(
			'id'=>'EVOCSV',
			'name'=>'CSV Importer',
			'link'=>'http://www.myeventon.com/addons/csv-event-importer/',
			'download'=>'http://www.myeventon.com/addons/csv-event-importer/',
			'desc'=>'Are you looking to import events from another program to EventON? CSV Import addon is the tool for you. It will import any number of events from a properly build CSV file into your EventON Calendar in few steps.'
		),'eventon-rsvp'=>array(
			'id'=>'EVORS',
			'name'=>'RSVP Events',
			'link'=>'http://www.myeventon.com/addons/rsvp-events/',
			'download'=>'http://www.myeventon.com/addons/rsvp-events/',
			'desc'=>'Do you want to allow your attendees RSVP to event so you know who is coming and who is not? and be able to check people in at the event? RSVP event can do that for you seamlessly.'
		),'eventon-tickets'=>array(
			'id'=>'EVOTX',
			'name'=>'Event Tickets',
			'link'=>'http://www.myeventon.com/addons/event-tickets/',
			'download'=>'http://www.myeventon.com/addons/event-tickets/',
			'desc'=>'Are you looking to sell tickets for your events with eventON? Event Tickets powered by Woocommerce is the ultimate solution for your ticket sales need. Stop paying percentage of your ticket sales and try event tickets addon!.'
		),'eventon-qrcode'=>array(
			'id'=>'EVOQR',
			'name'=>'QR Code',
			'link'=>'http://www.myeventon.com/addons/qr-code',
			'download'=>'http://www.myeventon.com/addons/qr-code',
			'desc'=>'Do you want to allow your attendees RSVP to event so you know who is coming and who is not? and be able to check people in at the event? RSVP event can do that for you seamlessly.'
		),'eventon-weekly-view'=>array(
			'id'=>'EVOWV',
			'name'=>'Weekly View',
			'link'=>'http://www.myeventon.com/addons/weekly-view',
			'download'=>'http://www.myeventon.com/addons/weekly-view',
			'desc'=>'Do you have too many events to fit in one month and you want to organize them into days? This addon will allow you to showcase events for one day of the month at a time.'
		),'eventon-rss'=>array(
			'id'=>'EVORF',
			'name'=>'RSS Feed',
			'link'=>'http://www.myeventon.com/addons/rss-feed/',
			'download'=>'http://www.myeventon.com/addons/rss-feed/',
			'desc'=>'Your website visitors can now easily RSS to all your calendar events using RSS Feed addon.'
		),'eventon-subscriber'=>array(
			'id'=>'EVOSB',
			'name'=>'Subscriber',
			'link'=>'http://www.myeventon.com/addons/subscriber',
			'download'=>'http://www.myeventon.com/addons/subscriber',
			'desc'=>'Allow your users to follow and subscribe to calendars'
		),'eventon-countdown'=>array(
			'id'=>'EVOCD',
			'name'=>'Countdown',
			'link'=>'http://www.myeventon.com/addons/event-countdown/',
			'download'=>'http://www.myeventon.com/addons/event-countdown/',
			'desc'=>'Add countdown timer to events'
		),'eventon-reviewer'=>array(
			'id'=>'EVORE',
			'name'=>'Event Reviewer',
			'link'=>'http://www.myeventon.com/addons/event-reviewer',
			'download'=>'http://www.myeventon.com/addons/event-reviewer',
			'desc'=>'Rate and review events'
		),'eventon-event-photos'=>array(
			'id'=>'EVOEP',
			'name'=>'Event Photos',
			'link'=>'http://www.myeventon.com/addons/event-photos',
			'download'=>'http://www.myeventon.com/addons/event-photos',
			'desc'=>'Add a photo library to events instead of one featured image'
		),'eventon-event-slider'=>array(
			'id'=>'EVOSL',
			'name'=>'Event Slider',
			'link'=>'http://www.myeventon.com/addons/event-slider',
			'download'=>'http://www.myeventon.com/addons/event-slider',
			'desc'=>'Interactive slider of events'
		),'eventon-api'=>array(
			'id'=>'EVOAP',
			'name'=>'Event API',
			'link'=>'http://www.myeventon.com/addons/event-api',
			'download'=>'http://www.myeventon.com/addons/event-api',
			'desc'=>'API to access all the calendar events from external sites'
		),'eventon-lists-items'=>array(
			'id'=>'EVOLI',
			'name'=>'Event Lists & Items',
			'link'=>'http://www.myeventon.com/addons/event-lists-items',
			'download'=>'http://www.myeventon.com/addons/event-lists-items',
			'desc'=>'Create custom eventON category lists and item boxes'
		),'eventon-ics-importer'=>array(
			'id'=>'EVOICS',
			'name'=>'iCS Importer',
			'link'=>'http://www.myeventon.com/addons/ics-importer',
			'download'=>'http://www.myeventon.com/addons/ics-importer',
			'desc'=>'Import events using ICS file'
		),'eventon-speakers-schedule'=>array(
			'id'=>'EVOSS',
			'name'=>'Speakers & Schedule',
			'link'=>'http://www.myeventon.com/addons/speakers-schedule',
			'download'=>'http://www.myeventon.com/addons/speakers-schedule',
			'desc'=>'Set Speakers and Schedule for events'
		),'eventon-seats'=>array(
			'id'=>'EVOST',
			'name'=>'Event Seats',
			'link'=>'http://www.myeventon.com/addons/seats',
			'download'=>'http://www.myeventon.com/addons/seats',
			'desc'=>'Interactive Seat map for eventon'
		),'eventon-wishlist'=>array(
			'id'=>'EVOWI',
			'name'=>'Event Wishlist',
			'link'=>'http://www.myeventon.com/addons/event-wishlist',
			'download'=>'http://www.myeventon.com/addons/event-wishlist',
			'desc'=>'Add events to wishlist'
		),'eventon-dynamic-pricing'=>array(
			'id'=>'EVODP',
			'name'=>'Event Dynamic Pricing',
			'link'=>'http://www.myeventon.com/addons/dynamic-pricing',
			'download'=>'http://www.myeventon.com/addons/dynamic-pricing',
			'desc'=>'Dynamic prices for event tickets'
		),'eventon-bookings'=>array(
			'id'=>'EVOBO',
			'name'=>'Bookings',
			'link'=>'http://www.myeventon.com/addons/bookings',
			'download'=>'http://www.myeventon.com/addons/bookings',
			'desc'=>'Sell event tickets as time slot based bookings or appointments'
		),'eventon-reminders'=>array(
			'id'=>'EVORM',
			'name'=>'Reminders',
			'link'=>'http://www.myeventon.com/addons/reminders',
			'download'=>'http://www.myeventon.com/addons/reminders',
			'desc'=>'Set custom reminders for events'
		)
	));
	

	ksort($addons);
	return $addons;
}

function addon($slug){
	$list = $this->get_list();
	if(!isset($list[$slug]) ) return false;

	return $list[$slug];
}
}
?>