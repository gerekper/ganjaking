<?php

/**
 * @link https://developers.google.com/maps/documentation/places/web-service/overview
 */
class UEGoogleAPIPlacesService extends UEGoogleAPIClient{

	/**
	 * Get the place details.
	 *
	 * @param string $placeId
	 * @param array $params
	 *
	 * @return UEGoogleAPIPlace
	 */
	public function getDetails($placeId, $params = array()){

		$params["place_id"] = $placeId;

		$response = $this->get("/details/json", $params);
		$response = UEGoogleAPIPlace::transform($response["result"]);

		return $response;
	}

	/**
	 * Get the base URL for the API.
	 *
	 * @return string
	 */
	protected function getBaseUrl(){

		return "https://maps.googleapis.com/maps/api/place";
	}

}
