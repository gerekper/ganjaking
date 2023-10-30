<?php
if (! class_exists ( 'ISOperationService' )) {
	include_once $GLOBALS["REDSYS_API_PATH"] . "/Model/ISResponseInterface.php";
	include_once $GLOBALS["REDSYS_API_PATH"] . "/Model/ISRequestInterface.php";
	include_once $GLOBALS["REDSYS_API_PATH"] . "/Utils/ISSignatureUtils.php";
	include_once $GLOBALS["REDSYS_API_PATH"] . "/Utils/ISLogger.php";
	abstract class ISOperationService {
		private $signatureKey = null;
		private $env;
		function __construct($signatureKey, $env) {
			$this->signatureKey = $signatureKey;
			$this->env = $env;
		}
		public function createRequestSOAPMessage($message) {
			$request=$this->createRequestMessage ( $message );

			$post_request=http_build_query(
								array(
									"Ds_MerchantParameters"=>$request->getDatosEntradaB64(),
									"Ds_SignatureVersion"=>$request->getSignatureVersion(),
									"Ds_Signature"=>$request->getSignature()
								)
						);
			
			ISLogger::debug("Sending ".ISLogger::beautifyXML($request->toXml()));

			return $post_request;
		}
		public function sendOperation($message) {
			$result="";
			$post_request = $this->createRequestSOAPMessage ( $message );
			$header = array (
					"Cache-Control: no-cache",
					"Pragma: no-cache",
					"Content-length: " . strlen ( $post_request ) 
			);
			$url_rs = ISConstants::$SANDBOX_ENDPOINT;
			if ($this->env == ISConstants::$ENV_PRODUCTION)
				$url_rs = ISConstants::$PRODUCTION_ENDPOINT;
			
			$rest_do = curl_init ();
			curl_setopt ( $rest_do, CURLOPT_URL, $url_rs );
			curl_setopt ( $rest_do, CURLOPT_CONNECTTIMEOUT, ISConstants::$CONNECTION_TIMEOUT_VALUE );
			curl_setopt ( $rest_do, CURLOPT_TIMEOUT, ISConstants::$READ_TIMEOUT_VALUE );
			curl_setopt ( $rest_do, CURLOPT_RETURNTRANSFER, true );
			curl_setopt ( $rest_do, CURLOPT_SSL_VERIFYHOST, 0 );
			curl_setopt ( $rest_do, CURLOPT_SSL_VERIFYPEER, 0 );
			curl_setopt ( $rest_do, CURLOPT_SSLVERSION, ISConstants::$SSL_TLSv12 );
			curl_setopt ( $rest_do, CURLOPT_POST, true );
			curl_setopt ( $rest_do, CURLOPT_POSTFIELDS, $post_request );
			curl_setopt ( $rest_do, CURLOPT_HTTPHEADER, $header );
			
			ISLogger::info("Performing request to '".$url_rs."'");
			ISLogger::debug("Sending JSON ".$post_request);
			$tmp = curl_exec ( $rest_do );
			$httpCode=curl_getinfo($rest_do,CURLINFO_HTTP_CODE);
			
			if($tmp !== false && $httpCode==200){
				$tag = array ();
				$result=$tmp;
			}
			else{
				$strError="Request failure ".(($httpCode!=200)?"[HttpCode: '".$httpCode."']":"").((curl_error($rest_do))?" [Error: '".curl_error($rest_do)."']":"");
				ISLogger::error($strError);
			}
			
			curl_close( $rest_do );
			return $this->createResponseMessage ( $result );
		}
		public abstract function createRequestMessage($message);
		public abstract function createResponseMessage($trataPeticionResponse);
		public abstract function unMarshallResponseMessage($message);
		protected function checkSignature($sentData, $remoteSignature) {				
			$calcSignature = ISSignatureUtils::createMerchantSignatureNotif ( $this->getSignatureKey(), $sentData );
			
			$result = $remoteSignature == $calcSignature;
			if(!$result)
				ISLogger::error("Signature doesnt match: '".$remoteSignature."' <> '".$calcSignature."'");
			else
				ISLogger::debug("Signature matches");
			
			return $result;
		}
		public function getSignatureKey() {
			return $this->signatureKey;
		}
		public function getEnv() {
			return $this->env;
		}
		public function __toString() {
			$rc=new ReflectionClass(get_class($this));
			$string = $rc->getName()."{";
			$string .= 'signatureKey: ' . $this->getSignatureKey () . ', ';
			$string .= 'env: ' . $this->getEnv () . '';
			return $string . "}";
		}
	}
}