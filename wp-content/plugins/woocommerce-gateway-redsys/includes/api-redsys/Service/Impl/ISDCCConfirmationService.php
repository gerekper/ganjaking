<?php

if(!class_exists('ISDCCConfirmationService')){
	include_once $GLOBALS["REDSYS_API_PATH"]."/Service/ISOperationService.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Model/Impl/ISRequestElement.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Model/Impl/ISResponseMessage.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Model/Impl/ISDCCResponseMessage.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Utils/ISSignatureUtils.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Constants/ISConstants.php";
	
	class ISDCCConfirmationService extends ISOperationService{
		function __construct($signatureKey, $env){
			parent::__construct($signatureKey, $env);
		}

		public function createRequestMessage($message){
			if($message !== NULL){
				$req=new ISRequestElement();
				$req->setDatosEntrada($message);
			
				$tagDE=$message->toXml();
				
				$signatureUtils=new ISSignatureUtils();
				$localSignature=$signatureUtils->createMerchantSignatureHostToHost($this->getSignatureKey(), $tagDE);
				$req->setSignature($localSignature);
				
				return $req->toXml();
			}
			return "";
		}
		
		public function createResponseMessage($trataPeticionResponse){
			$response=new ISResponseMessage();
			$response->parseXml($trataPeticionResponse);
			ISLogger::debug("Received ".ISLogger::beautifyXML($response->toXml()));
			
			$acsElem=$response->getTagContent(ISConstants::$RESPONSE_ACS_URL_TAG, $trataPeticionResponse);

			if($acsElem!==NULL && strlen($acsElem)){
				if($response->getApiCode()!==ISConstants::$RESP_CODE_OK
					|| !$this->checkSignature($response->getOperation()))
				{
					$response->setResult(ISConstants::$RESP_LITERAL_KO);
				}
				else{
					$response->setResult(ISConstants::$RESP_LITERAL_AUT);
				}
			}
			else{
				$response=new ISResponseMessage();
				$response->parseXml($trataPeticionResponse);
				$transType = $response->getTransactionType();
				if($response->getApiCode()!==ISConstants::$RESP_CODE_OK
						|| !$this->checkSignature($response->getOperation()))
				{
					$response->setResult(ISConstants::$RESP_LITERAL_KO);
				}
				else{
					switch ((int)$response->getOperation()->getResponseCode()){
						case ISConstants::$AUTHORIZATION_OK: $response->setResult($transType==ISConstants::$AUTHORIZATION || $transType==ISConstants::$PREAUTHORIZATION); break;
						case ISConstants::$CONFIRMATION_OK: $response->setResult($transType==ISConstants::$CONFIRMATION || $transType==ISConstants::$REFUND); break;
						case ISConstants::$CANCELLATION_OK: $response->setResult($transType==ISConstants::CANCELLATION); break;
						default: $response->setResult(ISConstants::$RESP_LITERAL_KO);
					}
				}
			}				
			
			return $response;
		}
		
		public function unMarshallResponseMessage($message){
			$response=new ISDCCResponseMessage();
			$response->parseXml($message);
			return $response;
		}
	}
}