<?php

/**
 * @link https://developers.google.com/youtube/v3/docs
 */
class UEGoogleAPIYouTubeService extends UEGoogleAPIClient{

	/**
	 * Get the playlist items.
	 *
	 * @param string $playlistId
	 * @param array $params
	 *
	 * @return UEGoogleAPIPlaylistItem[]
	 */
	public function getPlaylistItems($playlistId, $params = array()){

		$params["playlistId"] = $playlistId;
		$params["part"] = "snippet,contentDetails";

		$response = $this->get("/playlistItems", $params);
		$response = UEGoogleAPIPlaylistItem::transformAll($response["items"]);

		return $response;
	}

	/**
	 * Get the base URL for the API.
	 *
	 * @return string
	 */
	protected function getBaseUrl(){

		return "https://www.googleapis.com/youtube/v3";
	}

}
