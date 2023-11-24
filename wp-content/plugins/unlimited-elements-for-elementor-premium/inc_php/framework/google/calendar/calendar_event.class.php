<?php

class UEGoogleAPICalendarEvent extends UEGoogleAPIModel{

	/**
	 * Get the identifier.
	 *
	 * @return int
	 */
	public function getId(){

		$id = $this->getAttribute("id");

		return $id;
	}

	/**
	 * Get the title.
	 *
	 * @return string
	 */
	public function getTitle(){

		$title = $this->getAttribute("summary");

		return $title;
	}

	/**
	 * Get the description.
	 *
	 * @param bool $asHtml
	 *
	 * @return string|null
	 */
	public function getDescription($asHtml = false){

		$description = $this->getAttribute("description");

		if($asHtml === true)
			$description = nl2br($description);

		return $description;
	}

	/**
	 * Get the location.
	 *
	 * @return string|null
	 */
	public function getLocation(){

		$location = $this->getAttribute("location");

		return $location;
	}

	/**
	 * Get the URL.
	 *
	 * @return string
	 */
	public function getUrl(){

		$url = $this->getAttribute("htmlLink");

		return $url;
	}

	/**
	 * Get the start date.
	 *
	 * @param string $format
	 *
	 * @return string
	 */
	public function getStartDate($format){

		$start = $this->getAttribute("start");
		$date = $this->getDate($start, $format);

		return $date;
	}

	/**
	 * Get the end date.
	 *
	 * @param string $format
	 *
	 * @return string
	 */
	public function getEndDate($format){

		$end = $this->getAttribute("end");
		$date = $this->getDate($end, $format);

		return $date;
	}

	/**
	 * Get the date.
	 *
	 * @param object $time
	 * @param string $format
	 *
	 * @return string
	 */
	private function getDate($time, $format){

		$date = UniteFunctionsUC::getVal($time, "date", null);
		$dateTime = UniteFunctionsUC::getVal($time, "dateTime", null);

		$date = $date ?: $dateTime;
		$time = strtotime($date);
		$date = date($format, $time);

		return $date;
	}

}
