<?php

if(!class_exists('ISAuthenticationService')){
	include_once $GLOBALS["REDSYS_API_PATH"]."/Model/Impl/ISRequestElement.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Model/Impl/ISResponseMessage.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Utils/ISSignatureUtils.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Constants/ISConstants.php";
	
	class ISAuthenticationService extends ISOperationService{
		function __construct($signatureKey, $env){
			parent::__construct($signatureKey, $env);
		}

		public function createRequestMessage($message){
			$req=new ISRequestElement();
			$req->setDatosEntrada($message);
		
			$tagDE=$message->toJson();
			
			$signatureUtils=new ISSignatureUtils();
			$localSignature=$signatureUtils->createMerchantSignature($this->getSignatureKey(), $req->getDatosEntradaB64());
			$req->setSignature($localSignature);

			return $req;
		}
		
		public function createResponseMessage($trataPeticionResponse){
			$response=$this->unMarshallResponseMessage($trataPeticionResponse);
			ISLogger::debug("Received ".ISLogger::beautifyXML($response->toXml()));
			$paramsB64=json_decode($trataPeticionResponse,true)["Ds_MerchantParameters"];
			
			$transType = $response->getTransactionType();
			if(!$this->checkSignature($paramsB64, $response->getOperation()->getSignature()))
			{
				ISLogger::error("Received JSON '".$trataPeticionResponse."'");
				$response->setResult(ISConstants::$RESP_LITERAL_KO);
			}
			else{
				switch ((int)$response->getOperation()->getResponseCode()){
					case ISConstants::$AUTHORIZATION_OK: $response->setResult(($transType==ISConstants::$AUTHORIZATION || $transType==ISConstants::$PREAUTHORIZATION)?ISConstants::$RESP_LITERAL_OK:ISConstants::$RESP_LITERAL_KO); break;
					case ISConstants::$CONFIRMATION_OK: $response->setResult(($transType==ISConstants::$CONFIRMATION || $transType==ISConstants::$REFUND)?ISConstants::$RESP_LITERAL_OK:ISConstants::$RESP_LITERAL_KO);  break;
					case ISConstants::$CANCELLATION_OK: $response->setResult($transType==ISConstants::$CANCELLATION?ISConstants::$RESP_LITERAL_OK:ISConstants::$RESP_LITERAL_KO);  break;
					default: $response->setResult(ISConstants::$RESP_LITERAL_KO);
				}
			}
			
			if($response->getResult()==ISConstants::$RESP_LITERAL_OK){
				ISLogger::info("Operation finished successfully");
			}
			else{
				ISLogger::info("Operation finished with errors");
			}
			
			return $response;
		}
		
		public function unMarshallResponseMessage($message){
			$response=new ISResponseMessage();
			
			$varArray=json_decode($message,true);
			
			$operacion=new ISOperationElement();
			$operacion->parseJson(base64_decode($varArray["Ds_MerchantParameters"]));
			$operacion->setSignature($varArray["Ds_Signature"]);
			
			$response->setOperation($operacion);
			
			return $response;
		}
	}
}