<?php
 if (! class_exists ( 'ISOperationMessage' )) {
 	include_once $GLOBALS["REDSYS_API_PATH"] . "/Model/ISGenericXml.php";
 	include_once $GLOBALS["REDSYS_API_PATH"] . "/Model/ISRequestInterface.php";
 	include_once $GLOBALS["REDSYS_API_PATH"] . "/Constants/ISConstants.php";
 	
 	/**
 	 * @XML_ELEM=DATOSENTRADA
 	 */
 	class ISOperationMessage extends ISGenericXml implements ISRequestInterface {
 		
 		/**
 		 * Merchant code (FUC)
 		 * @XML_ELEM=DS_MERCHANT_MERCHANTCODE
 		 */
 		private $merchant = null;
 		
 		/**
 		 * Terminal code
 		 * @XML_ELEM=DS_MERCHANT_TERMINAL
 		 */
 		private $terminal = null;
 		
 		/**
 		 * Operation order code
 		 * @XML_ELEM=DS_MERCHANT_ORDER
 		 */
 		private $order = null;
 		
 		/**
 		 * Operation ID code
 		 * @XML_ELEM=DS_MERCHANT_IDOPER
 		 */
 		private $operID = null;
 		
 		/**
 		 * Operation type
 		 * @XML_ELEM=DS_MERCHANT_TRANSACTIONTYPE
 		 */
 		private $transactionType = null;
 		
 		/**
 		 * Currency code (ISO 4217)
 		 * @XML_ELEM=DS_MERCHANT_CURRENCY
 		 */
 		private $currency = null;
 		
 		/**
 		 * Operation amount, withot decimal separation
 		 * @XML_ELEM=DS_MERCHANT_AMOUNT
 		 */
 		private $amount = null;
 		
 		/**
 		 * 3DSecure information
 		 * @XML_ELEM=DS_MERCHANT_EMV3DS
 		 */
 		private $emv = null;
 		
 		
 		/**
 		 * DCC indicator for DCC appliance
 		 */
 		private $dcc = false;
 		private $parameters = array ();
 		
 		/**
 		 * gets the merchant code (FUC)
 		 * 
 		 * @return the merchant code
 		 */
 		public function getMerchant() {
 			return $this->merchant;
 		}
 		
 		/**
 		 * sets the merchant code
 		 * 
 		 * @param
 		 *        	merchant merchant code
 		 */
 		public function setMerchant($merchant) {
 			$this->merchant = $merchant;
 			return $this;
 		}
 		
 		/**
 		 * gets the terminal code
 		 * 
 		 * @return the terminal code
 		 */
 		public function getTerminal() {
 			return $this->terminal;
 		}
 		
 		/**
 		 * sets the terminal code
 		 * 
 		 * @param
 		 *        	terminal terminal code (max lenght 3)
 		 */
 		public function setTerminal($terminal) {
 			$this->terminal = $terminal;
 			return $this;
 		}
 		
 		/**
 		 * gets the operation order code (max length 12)
 		 * 
 		 * @return the operation order (max length 12)
 		 */
 		public function getOrder() {
 			return $this->order;
 		}
 		
 		/**
 		 * sets the operation order (max length 12)
 		 * 
 		 * @param
 		 *        	order (max length 12)
 		 */
 		public function setOrder($order) {
 			$this->order = $order;
 			return $this;
 		}
 		
 		/**
 		 * gets the operation ID
 		 * 
 		 * @return the operation ID
 		 */
 		public function getOperID() {
 			return $this->operID;
 		}
 		
 		/**
 		 * sets the operation ID
 		 * 
 		 * @param
 		 *        	operID the operation ID
 		 */
 		public function setOperID($operID) {
 			$this->operID = $operID;
 			return $this;
 		}
 		
 		/**
 		 * gets the operation type
 		 * 
 		 * @return the operation type
 		 */
 		public function getTransactionType() {
 			return $this->transactionType;
 		}
 		
 		/**
 		 * sets the operation type
 		 * 
 		 * @param
 		 *        	transactionType the operation type
 		 */
 		public function setTransactionType($transactionType) {
 			$this->transactionType = $transactionType;
 			return $this;
 		}
 		
 		/**
 		 * get currency code
 		 * 
 		 * @return the currency code (numeric ISO_4217)
 		 */
 		public function getCurrency() {
 			return $this->currency;
 		}
 		
 		/**
 		 * sets the currency code
 		 * 
 		 * @param
 		 *        	currency the currency code (numeric ISO_4217 )
 		 */
 		public function setCurrency($currency) {
 			$this->currency = $currency;
 			return $this;
 		}
 		
 		/**
 		 * gets the amount of the operation
 		 * 
 		 * @return the operation amount
 		 */
 		public function getAmount() {
 			return $this->amount;
 		}
 		
 		/**
 		 * sets de amount of the operation
 		 * 
 		 * @param
 		 *        	amount without decimal separation
 		 */
 		public function setAmount($amount) {
 			$this->amount = $amount;
 			return $this;
 		}
 
 
 		/**
 		 * emv
 		 * @return unkown
 		 */
 		public function getEmv(){
 			if($this->emv==NULL)
 				return null;
 			
 			return json_encode($this->emv);
 		}
 		
 		/**
 		 * emv
 		 * @param unkown $emv
 		 * @return ISOperationMessage
 		 */
 		public function setEmv($emv){
 			$this->emv = $emv;
 			return $this;
 		}
 		
 		public function isDcc() {
 			return $this->dcc;
 		}
 		public function withDcc() {
 			return $this->dcc = true;
 		}
 		public function getParameters() {
 			return $this->parameters;
 		}
 		public function addParameter($key, $value) {
 			$this->parameters [$key] = $value;
 		}
 		
 		/**
 		 * Flag for reference creation (card token for merchant to use in other operations)
 		 */
 		public function createReference() {
 			$this->addParameter ( ISConstants::$REQUEST_MERCHANT_IDENTIFIER_TAG, ISConstants::$REQUEST_MERCHANT_IDENTIFIER_REQUIRED );
 		}
 		
 		/**
 		 * Method for using a reference created before for the operation
 		 * 
 		 * @param
 		 *        	reference the reference string to be used
 		 */
 		public function useReference($reference) {
 			$this->addParameter ( ISConstants::$REQUEST_MERCHANT_IDENTIFIER_TAG, $reference );
 		}
 		
 		/**
 		 * Flag for direct payment operation.
 		 * Direct payment operation implies:
 		 * 1) No-secure operation
 		 * 2) No-DCC operative appliance
 		 */
 		public function useDirectPayment() {
 			$this->addParameter ( ISConstants::$REQUEST_MERCHANT_DIRECTPAYMENT_TAG, ISConstants::$REQUEST_MERCHANT_DIRECTPAYMENT_TRUE );
 		}
 
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
 		
 		/**
 		 * Flag for secure operation.
 		 * If is used, after the response, the process will be stopped due to the authentication process
 		 */
 // 		public function useSecurePayment() {
 // 			$this->addParameter ( ISConstants::$REQUEST_MERCHANT_DIRECTPAYMENT_TAG, ISConstants::$REQUEST_MERCHANT_DIRECTPAYMENT_3DS );
 // 		}
 		public function __toString() {
 			$string = "ISOperationMessage{";
 			$string .= 'merchant: ' . $this->getMerchant () . ', ';
 			$string .= 'terminal: ' . $this->getTerminal () . ', ';
 			$string .= 'order: ' . $this->getOrder () . ', ';
 			$string .= 'operID: ' . $this->getOperID () . ', ';
 			$string .= 'transactionType: ' . $this->getTransactionType () . ', ';
 			$string .= 'currency: ' . $this->getCurrency () . ', ';
 			$string .= 'amount: ' . $this->getAmount () . ', ';
 			$string .= 'parameters: ' . json_encode($this->getParameters()) . '';
 			$string .= 'emv: ' . $this->getEmv() . '';
 			return $string . "}";
 		}
 	} }
 