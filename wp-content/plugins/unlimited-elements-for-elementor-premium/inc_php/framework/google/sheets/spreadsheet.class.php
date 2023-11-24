<?php

class UEGoogleAPISpreadsheet extends UEGoogleAPIModel{

	/**
	 * Get the identifier.
	 *
	 * @return string
	 */
	public function getId(){

		$id = $this->getAttribute("spreadsheetId");

		return $id;
	}

	/**
	 * Get the sheets.
	 *
	 * @return UEGoogleAPISheet[]
	 */
	public function getSheets(){

		$sheets = $this->getAttribute("sheets");
		$sheets = UEGoogleAPISheet::transformAll($sheets);

		return $sheets;
	}

}
