<?php

class UEGoogleAPISheet extends UEGoogleAPIModel{

	/**
	 * Get the identifier.
	 *
	 * @return int
	 */
	public function getId(){

		$id = $this->getPropertyValue("sheetId");

		return $id;
	}

	/**
	 * Get the title.
	 *
	 * @return string
	 */
	public function getTitle(){

		$title = $this->getPropertyValue("title");

		return $title;
	}

	/**
	 * Get the property value.
	 *
	 * @param string $key
	 * @param mixed $fallback
	 *
	 * @return mixed
	 */
	private function getPropertyValue($key, $fallback = null){

		$properties = $this->getAttribute("properties");
		$value = UniteFunctionsUC::getVal($properties, $key, $fallback);

		return $value;
	}

}
