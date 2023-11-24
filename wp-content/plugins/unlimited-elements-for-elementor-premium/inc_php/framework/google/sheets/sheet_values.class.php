<?php

class UEGoogleAPISheetValues extends UEGoogleAPIModel{

	/**
	 * Get the values.
	 *
	 * @return array
	 */
	public function getValues(){

		$values = $this->getAttribute("values", array());

		return $values;
	}

}
