<?php
if (! class_exists ( 'ISDCCElement' )) {
	include_once $GLOBALS["REDSYS_API_PATH"] . "/Model/ISGenericXml.php";
	
	/**
	 * @XML_ELEM=DCC
	 */
	class ISDCCElement extends ISGenericXml {
		/**
		 * @XML_ELEM=moneda
		 */
		private $currency;
		
		/**
		 * @XML_ELEM=litMoneda
		 */
		private $currencyString;
		
		/**
		 * @XML_ELEM=litMonedaR
		 */
		private $currencyCode;
		
		/**
		 * @XML_ELEM=cambio
		 */
		private $changeRate;
		
		/**
		 * @XML_ELEM=fechaCambio
		 */
		private $changeDate;
		
		/**
		 * @XML_ELEM=importe
		 */
		private $amount;
		
		/**
		 * @XML_ELEM=checked
		 */
		private $checked;
		public function getCurrency() {
			return $this->currency;
		}
		public function setCurrency($currency) {
			$this->currency = $currency;
			return $this;
		}
		public function getCurrencyString() {
			return $this->currencyString;
		}
		public function setCurrencyString($currencyString) {
			$this->currencyString = $currencyString;
			return $this;
		}
		public function getCurrencyCode() {
			return $this->currencyCode;
		}
		public function setCurrencyCode($currencyCode) {
			$this->currencyCode = $currencyCode;
			return $this;
		}
		public function getChangeRate() {
			return $this->changeRate;
		}
		public function setChangeRate($changeRate) {
			$this->changeRate = $changeRate;
			return $this;
		}
		public function getChangeDate() {
			return $this->changeDate;
		}
		public function setChangeDate($changeDate) {
			$this->changeDate = $changeDate;
			return $this;
		}
		public function getChecked() {
			return $this->checked;
		}
		public function setChecked($checked) {
			$this->checked = $checked;
			return $this;
		}
		public function getAmount() {
			return $this->amount;
		}
		public function setAmount($amount) {
			$this->amount = $amount;
			return $this;
		}
		public function __toString() {
			$string = "ISDCCElement{";
			$string .= 'currency: ' . $this->getCurrency () . ', ';
			$string .= 'currencyString: ' . $this->getCurrencyString () . ', ';
			$string .= 'currencyCode: ' . $this->getCurrencyCode () . ', ';
			$string .= 'changeRate: ' . $this->getChangeRate () . ', ';
			$string .= 'changeDate: ' . $this->getChangeDate () . ', ';
			$string .= 'amount: ' . $this->getAmount () . ', ';
			$string .= 'checked: ' . $this->getChecked () . '';
			return $string . "}";
		}
	}
}
