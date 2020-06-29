<?php
if (! class_exists ( 'ISResponseMessage' )) {
	include_once $GLOBALS["REDSYS_API_PATH"] . "/Model/ISGenericXml.php";
	include_once $GLOBALS["REDSYS_API_PATH"] . "/Model/Impl/ISOperationElement.php";
	include_once $GLOBALS["REDSYS_API_PATH"] . "/Model/ISResponseInterface.php";
	
	/**
	 * @XML_ELEM=RETORNOXML
	 */
	class ISResponseMessage extends ISGenericXml implements ISResponseInterface {
		private $result;
		
		/**
		 * @XML_CLASS=ISOperationElement
		 */
		private $operation;
		public function getResult() {
			return $this->result;
		}
		public function setResult($result) {
			$this->result = $result;
			return $this;
		}
		public function getOperation() {
			return $this->operation;
		}
		public function setOperation($operation) {
			$this->operation = $operation;
			return $this;
		}
		public function __toString() {
			$string = "ISResponseMessage{";
			$string .= 'result: ' . $this->getResult () . ', ';
			$string .= 'operation: ' . $this->getOperation () . '';
			return $string . "}";
		}
		public function getTransactionType(){
			if($this->getOperation() !== NULL)
				return $this->getOperation()->getTransactionType();
			else 
				return NULL;
		}
	}
}