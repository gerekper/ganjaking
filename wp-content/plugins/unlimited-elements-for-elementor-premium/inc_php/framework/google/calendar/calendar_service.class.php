<?php

/**
 * https://developers.google.com/calendar/api/v3/reference
 */
class UEGoogleAPICalendarService extends UEGoogleAPIClient{

	/**
	 * Get the events.
	 *
	 * @param string $calendarId
	 * @param array $params
	 *
	 * @return UEGoogleAPICalendarEvent[]
	 */
	public function getEvents($calendarId, $params = array()){

		$calendarId = urlencode($calendarId);

		$params["timeZone"] = wp_timezone_string();

		$response = $this->get("/calendars/$calendarId/events", $params);
		$response = UEGoogleAPICalendarEvent::transformAll($response["items"]);

		return $response;
	}

	/**
	 * Get the base URL for the API.
	 *
	 * @return string
	 */
	protected function getBaseUrl(){

		return "https://www.googleapis.com/calendar/v3";
	}

}
