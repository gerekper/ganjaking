<?php

class UEGoogleAPIPlaceReview extends UEGoogleAPIModel{

	/**
	 * Get the identifier.
	 *
	 * @return int
	 */
	public function getId(){

		$id = $this->getTime();

		return $id;
	}

	/**
	 * Get the text.
	 *
	 * @param bool $asHtml
	 *
	 * @return string
	 */
	public function getText($asHtml = false){

		$text = $this->getAttribute("text");

		if($asHtml === true)
			$text = nl2br($text);

		return $text;
	}

	/**
	 * Get the rating.
	 *
	 * @return int
	 */
	public function getRating(){

		$rating = $this->getAttribute("rating");

		return $rating;
	}

	/**
	 * Get the date.
	 *
	 * @param string $format
	 *
	 * @return string
	 */
	public function getDate($format){

		$time = $this->getTime();
		$date = date($format, $time);

		return $date;
	}

	/**
	 * Get the author name.
	 *
	 * @return string
	 */
	public function getAuthorName(){

		$name = $this->getAttribute("author_name");

		return $name;
	}

	/**
	 * Get the author photo URL.
	 *
	 * @return string|null
	 */
	public function getAuthorPhotoUrl(){

		$url = $this->getAttribute("profile_photo_url");

		return $url;
	}

	/**
	 * Get the time.
	 *
	 * @return int
	 */
	private function getTime(){

		$time = $this->getAttribute("time");

		return $time;
	}

}
