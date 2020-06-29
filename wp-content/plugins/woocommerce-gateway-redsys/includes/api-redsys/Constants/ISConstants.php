<?php 
	if(!class_exists('ISConstants')){
		class ISConstants {
			// Environments
			public static $ENV_SANDBOX = "0";
			public static $SANDBOX_JS = "https://sis-t.redsys.es:25443/sis/NC/sandbox/redsys2.js";
			public static $SANDBOX_ENDPOINT = "https://sis-t.redsys.es:25443/sis/rest/entradaREST";
			
			public static $ENV_PRODUCTION = "1";
			public static $PRODUCTION_JS = "https://sis.redsys.es/sis/NC/redsys.js";
			public static $PRODUCTION_ENDPOINT = "https://sis.redsys.es/sis/rest/entradaREST";
			
			public static $CONNECTION_TIMEOUT_VALUE = 10;
			public static $READ_TIMEOUT_VALUE = 120;
			public static $SSL_TLSv12 = 6;
			public static $TARGET = "http://webservice.sis.sermepa.es";
			public static $SERVICE_NAME = "SerClsWSEntradaService";
			public static $PORT_NAME = "SerClsWSEntrada";
			 
			// Request message constants
			public static $REQUEST_REQUEST_TAG = "REQUEST";
			public static $REQUEST_DATOSENTRADA_TAG = "DATOSENTRADA";
			public static $REQUEST_SIGNATUREVERSION_TAG = "DS_SIGNATUREVERSION";
			public static $REQUEST_SIGNATUREVERSION_VALUE = "HMAC_SHA256_V1";
			public static $REQUEST_SIGNATURE_TAG = "DS_SIGNATURE";
			public static $REQUEST_MERCHANT_ORDER_TAG = "DS_MERCHANT_ORDER";
			public static $REQUEST_MERCHANT_MERCHANTCODE_TAG = "DS_MERCHANT_MERCHANTCODE";
			public static $REQUEST_MERCHANT_TERMINAL_TAG = "DS_MERCHANT_TERMINAL";
			public static $REQUEST_MERCHANT_TRANSACTIONTYPE_TAG = "DS_MERCHANT_TRANSACTIONTYPE";
			public static $REQUEST_MERCHANT_IDOPER_TAG = "DS_MERCHANT_IDOPER";
			public static $REQUEST_MERCHANT_CURRENCY_TAG = "DS_MERCHANT_CURRENCY";
			public static $REQUEST_MERCHANT_AMOUNT_TAG = "DS_MERCHANT_AMOUNT";
			public static $REQUEST_MERCHANT_SIS_CURRENCY_TAG = "Sis_Divisa";
			public static $REQUEST_MERCHANT_SESSION_TAG = "DS_MERCHANT_SESION";
			public static $REQUEST_MERCHANT_IDENTIFIER_TAG = "DS_MERCHANT_IDENTIFIER";
			public static $REQUEST_MERCHANT_IDENTIFIER_REQUIRED = "REQUIRED";
			public static $REQUEST_MERCHANT_DIRECTPAYMENT_TAG = "DS_MERCHANT_DIRECTPAYMENT";
			public static $REQUEST_MERCHANT_DIRECTPAYMENT_TRUE = "true";
			public static $REQUEST_MERCHANT_DIRECTPAYMENT_3DS = "3DS";
			 
			// Response message constants
			public static $RESPONSE_CODE_TAG = "CODIGO";
			public static $RESPONSE_AMOUNT_TAG = "Ds_Amount";
			public static $RESPONSE_CURRENCY_TAG = "Ds_Currency";
			public static $RESPONSE_ORDER_TAG = "Ds_Order";
			public static $RESPONSE_SIGNATURE_TAG = "Ds_Signature";
			public static $RESPONSE_MERCHANT_TAG = "Ds_MerchantCode";
			public static $RESPONSE_TERMINAL_TAG = "Ds_Terminal";
			public static $RESPONSE_RESPONSE_TAG = "Ds_Response";
			public static $RESPONSE_AUTHORIZATION_CODE_TAG = "Ds_AuthorisationCode";
			public static $RESPONSE_TRANSACTION_TYPE_TAG = "Ds_TransactionType";
			public static $RESPONSE_SECURE_PAYMENT_TAG = "Ds_SecurePayment";
			public static $RESPONSE_LANGUAGE_TAG = "Ds_Language";
			public static $RESPONSE_MERCHANT_DATA_TAG = "Ds_MerchantData";
			public static $RESPONSE_CARD_COUNTRY_TAG = "Ds_Card_Country";
			public static $RESPONSE_CARD_NUMBER_TAG = "Ds_CardNumber";
			public static $RESPONSE_EXPIRY_DATE_TAG = "Ds_CardNumber";
			public static $RESPONSE_MERCHANT_IDENTIFIER_TAG = "Ds_CardNumber";
			public static $RESPONSE_DCC_TAG = "DCC";
			public static $RESPONSE_DCC_CURRENCY_TAG = "moneda";
			public static $RESPONSE_DCC_CURRENCY_STRING_TAG = "litMoneda";
			public static $RESPONSE_DCC_CURRENCY_CODE_TAG = "litMonedaR";
			public static $RESPONSE_DCC_CHANGE_RATE_TAG = "cambio";
			public static $RESPONSE_DCC_CHANGE_DATE_TAG = "fechaCambio";
			public static $RESPONSE_DCC_CHECKED_TAG = "checked";
			public static $RESPONSE_DCC_AMOUNT_TAG = "importe";
			public static $RESPONSE_DCC_MARGIN_TAG = "margenDCC";
			public static $RESPONSE_DCC_BANK_NAME_TAG = "nombreEntidad";
			public static $RESPONSE_ACS_URL_TAG = "Ds_AcsUrl";

			public static $RESPONSE_JSON_ACS_ENTRY="acsURL";
			public static $RESPONSE_JSON_PAREQ_ENTRY="PAReq";
			public static $RESPONSE_JSON_PARES_ENTRY="PARes";
			public static $RESPONSE_JSON_MD_ENTRY="MD";
			public static $RESPONSE_JSON_PROTOCOL_VERSION_ENTRY="protocolVersion";
			public static $RESPONSE_JSON_THREEDSINFO_ENTRY="threeDSInfo";

			public static $RESPONSE_3DS_CHALLENGE_REQUEST="ChallengeRequest";
			public static $RESPONSE_3DS_CHALLENGE_RESPONSE="ChallengeResponse";
			
			public static $RESPONSE_3DS_VERSION_1="1.0.2";
			public static $RESPONSE_3DS_VERSION_2_PREFIX="2.";
			 
			// Response codes
			public static $RESP_CODE_OK = "0";
			public static $RESP_LITERAL_OK = "OK";
			public static $RESP_LITERAL_KO = "KO";
			public static $RESP_LITERAL_AUT = "AUT";
			
			public static $AUTHORIZATION_OK = 0000;
			public static $CONFIRMATION_OK = 900;
			public static $CANCELLATION_OK = 400;
	
			public static $AUTHORIZATION = "0";
			public static $REFUND = "3";
			public static $PREAUTHORIZATION = "1";
			public static $CONFIRMATION = "2";
			public static $CANCELLATION = "9";
			
			public static function getJSPath($env){
				if($env==ISConstants::$ENV_PRODUCTION){
					return ISConstants::$PRODUCTION_JS;
				}
				else{
					return ISConstants::$SANDBOX_JS;
				}
			}
		}


	}