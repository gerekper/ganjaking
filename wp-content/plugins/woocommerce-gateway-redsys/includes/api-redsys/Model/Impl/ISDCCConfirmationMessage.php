<?php
if (! class_exists ( 'ISDCCConfirmationMessage' )) {
	include_once $GLOBALS["REDSYS_API_PATH"] . "/Model/ISGenericXml.php";
	include_once $GLOBALS["REDSYS_API_PATH"] . "/Model/ISRequestInterface.php";
	
	/**
	 * @XML_ELEM=DATOSENTRADA
	 */
	class ISDCCConfirmationMessage extends ISGenericXml implements ISRequestInterface {
		/**
		 * @XML_ELEM=DS_MERCHANT_ORDER
		 */
		private $order = null;
		
		/**
		 * @XML_ELEM=DS_MERCHANT_MERCHANTCODE
		 */
		private $merchant = null;
		
		/**
		 * @XML_ELEM=DS_MERCHANT_TERMINAL
		 */
		private $terminal = null;
		
		/**
		 * @XML_ELEM=Sis_Divisa
		 */
		private $currencyCode = null;
		
		/**
		 * @XML_ELEM=DS_MERCHANT_SESION
		 */
		private $sesion = null;
		public function getOrder() {
			return $this->order;
		}
		public function setOrder($order) {
			$this->order = $order;
			return $this;
		}
		public function getMerchant() {
			return $this->merchant;
		}
		public function setMerchant($merchant) {
			$this->merchant = $merchant;
			return $this;
		}
		public function getTerminal() {
			return $this->terminal;
		}
		public function setTerminal($terminal) {
			$this->terminal = $terminal;
			return $this;
		}
		public function getCurrencyCode() {
			return $this->currencyCode;
		}
		public function setCurrencyCode($currency, $amount) {
			$this->currencyCode = $currency . "#" . $amount;
			return $this;
		}
		public function getSesion() {
			return $this->sesion;
		}
		public function setSesion($sesion) {
			$this->sesion = $sesion;
			return $this;
		}
		public function __toString() {
			$string = "ISDCCConfirmationMessage{";
			$string .= 'order: ' . $this->getOrder () . ', ';
			$string .= 'merchant: ' . $this->getMerchant () . ', ';
			$string .= 'terminal: ' . $this->getTerminal () . ', ';
			$string .= 'currencyCode: ' . $this->getCurrencyCode () . ', ';
			$string .= 'sesion: ' . $this->getSesion () . '';
			return $string . "}";
		}
	}
}