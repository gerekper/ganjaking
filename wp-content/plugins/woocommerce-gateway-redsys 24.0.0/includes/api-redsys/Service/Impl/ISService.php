<?php

if(!class_exists('ISService')){
	include_once $GLOBALS["REDSYS_API_PATH"]."/Service/ISOperationService.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Service/Impl/ISDCCConfirmationService.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Model/Impl/ISDCCConfirmationMessage.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Model/Impl/ISOperationElement.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Model/Impl/ISResponseMessage.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Model/Impl/ISRequestElement.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Utils/ISSignatureUtils.php";
	include_once $GLOBALS["REDSYS_API_PATH"]."/Utils/ISLogger.php";
	
	class ISService extends ISOperationService{
		private $request;
		function __construct($signatureKey, $env){
			parent::__construct($signatureKey, $env);
		}

		public function createRequestMessage($message){
			$this->request=$message;
			$req=new ISRequestElement();
			$req->setDatosEntrada($message);
			
			$signatureUtils=new ISSignatureUtils();
			$localSignature=$signatureUtils->createMerchantSignature($this->getSignatureKey(), $req->getDatosEntradaB64());
			
			$req->setSignature($localSignature);
			
			return $req;
		}
		
		public function createResponseMessage($trataPeticionResponse){
			$response=new ISResponseMessage();
			$varArray=json_decode($trataPeticionResponse,true);
			
			if(isset($varArray["ERROR"]) || isset($varArray["errorCode"])){
				ISLogger::error("Received JSON '".$trataPeticionResponse."'");
				$response->setResult(ISConstants::$RESP_LITERAL_KO);
			}
			else{
				$varArray=json_decode(base64_decode($varArray["Ds_MerchantParameters"]),true);
				
				$dccElem=isset($varArray[ISConstants::$RESPONSE_DCC_MARGIN_TAG]);
			
				if($dccElem){
// 					$dccService=new ISDCCConfirmationService($this->getSignatureKey(), $this->getEnv());
// 					$dccResponse=$dccService->unMarshallResponseMessage($trataPeticionResponse);
// 					ISLogger::debug("Received ".ISLogger::beautifyXML($dccResponse->toXml()));
				
// 					$dccConfirmation=new ISDCCConfirmationMessage();
// 					$currency="";
// 					$amount="";
// 					if($this->request->isDcc()){
// 						$currency=$dccResponse->getDcc0()->getCurrency();
// 						$amount=$dccResponse->getDcc0()->getAmount();
// 					}
// 					else{
// 						$currency=$dccResponse->getDcc1()->getCurrency();
// 						$amount=$dccResponse->getDcc1()->getAmount();
// 					}
				
// 					$dccConfirmation->setCurrencyCode($currency, $amount);
// 					$dccConfirmation->setMerchant($this->request->getMerchant());
// 					$dccConfirmation->setTerminal($this->request->getTerminal());
// 					$dccConfirmation->setOrder($this->request->getOrder());
// 					$dccConfirmation->setSesion($dccResponse->getSesion());
				
// 					$response=$dccService->sendOperation($dccConfirmation);
				}
				else{
					$response=$this->unMarshallResponseMessage($trataPeticionResponse);
					ISLogger::debug("Received ".ISLogger::beautifyXML($response->toXml()));
					$paramsB64=json_decode($trataPeticionResponse,true)["Ds_MerchantParameters"];
				
					if($response->getOperation()->requires3DS1()){
						if(!$this->checkSignature($paramsB64, $response->getOperation()->getSignature()))
						{
							$response->setResult(ISConstants::$RESP_LITERAL_KO);
						}
						else{
							$response->setResult(ISConstants::$RESP_LITERAL_AUT);
						}
					}
					else{
						$transType = $response->getTransactionType();
						if(!$this->checkSignature($paramsB64, $response->getOperation()->getSignature()))
						{
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
					}
				}
					
				if($response->getResult()==ISConstants::$RESP_LITERAL_OK){
					ISLogger::info("Operation finished successfully");
				}
				else{
					if($response->getResult()==ISConstants::$RESP_LITERAL_AUT){
						ISLogger::info("Operation requires autentication");
					}
					else{
						ISLogger::info("Operation finished with errors");
					}
				}
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