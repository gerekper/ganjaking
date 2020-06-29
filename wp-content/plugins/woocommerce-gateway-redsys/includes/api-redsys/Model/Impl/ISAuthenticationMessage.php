<?php
if (! class_exists ( 'ISAuthenticationMessage' )) {
	include_once $GLOBALS ["REDSYS_API_PATH"] . "/Model/ISGenericXml.php";
	include_once $GLOBALS ["REDSYS_API_PATH"] . "/Model/ISRequestInterface.php";
	
	/**
	 * @XML_ELEM=DATOSENTRADA
	 */
	class ISAuthenticationMessage extends ISGenericXml implements ISRequestInterface {
		
		/**
		 * 3DSecure information
		 * @XML_ELEM=DS_MERCHANT_EMV3DS
		 */
		private $emv = null;
		
		/**
		 * @XML_ELEM=DS_MERCHANT_ORDER
		 */
		private $order;
		
		/**
		 * @XML_ELEM=DS_MERCHANT_AMOUNT
		 */
		private $amount;
		
		/**
		 * @XML_ELEM=DS_MERCHANT_CURRENCY
		 */
		private $currency;
		
		/**
		 * @XML_ELEM=DS_MERCHANT_MERCHANTCODE
		 */
		private $merchant;
		
		/**
		 * @XML_ELEM=DS_MERCHANT_TERMINAL
		 */
		private $terminal;
		
		/**
		 * @XML_ELEM=DS_MERCHANT_TRANSACTIONTYPE
		 */
		private $transactionType;

		public function addEmvParameters($parameters){
			if($this->emv==NULL)
				$this->emv=array();

			foreach ($parameters as $key => $value)
				$this->emv[$key]=$value;
		}

		public function addEmvParameter($name, $value){
			if($this->emv==NULL)
				$this->emv=array();
			
			$this->emv[$name]=$value;
		}
		
		public function getEmv(){
			if($this->emv==NULL)
				return null;
			
			return json_encode($this->emv);
		}
		public function setEmv($emv){
			$this->emv = $emv;
			return $this;
		}
		public function getOrder() {
			return $this->order;
		}
		public function setOrder($order) {
			$this->order = $order;
			return $this;
		}
		public function getAmount() {
			return $this->amount;
		}
		public function setAmount($amount) {
			$this->amount = $amount;
			return $this;
		}
		public function getCurrency() {
			return $this->currency;
		}
		public function setCurrency($currency) {
			$this->currency = $currency;
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
		public function getTransactionType() {
			return $this->transactionType;
		}
		public function setTransactionType($transactionType) {
			$this->transactionType = $transactionType;
			return $this;
		}
	}
}
