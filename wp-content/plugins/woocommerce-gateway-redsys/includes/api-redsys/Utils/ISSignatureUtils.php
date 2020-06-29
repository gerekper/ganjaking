<?php
	/**
	* NOTA SOBRE LA LICENCIA DE USO DEL SOFTWARE
	* 
	* El uso de este software está sujeto a las Condiciones de uso de software que
	* se incluyen en el paquete en el documento "Aviso Legal.pdf". También puede
	* obtener una copia en la siguiente url:
	* http://www.redsys.es/wps/portal/redsys/publica/areadeserviciosweb/descargaDeDocumentacionYEjecutables
	* 
	* Redsys es titular de todos los derechos de propiedad intelectual e industrial
	* del software.
	* 
	* Quedan expresamente prohibidas la reproducción, la distribución y la
	* comunicación pública, incluida su modalidad de puesta a disposición con fines
	* distintos a los descritos en las Condiciones de uso.
	* 
	* Redsys se reserva la posibilidad de ejercer las acciones legales que le
	* correspondan para hacer valer sus derechos frente a cualquier infracción de
	* los derechos de propiedad intelectual y/o industrial.
	* 
	* Redsys Servicios de Procesamiento, S.L., CIF B85955367
	*/

	if(!class_exists('ISSignatureUtils')){
		class ISSignatureUtils{
			
			//////////////////////////////////////////////////////////////////////////////////////////////
			//////////////////////////////////////////////////////////////////////////////////////////////
			////////////					FUNCIONES AUXILIARES:							  ////////////
			//////////////////////////////////////////////////////////////////////////////////////////////
			//////////////////////////////////////////////////////////////////////////////////////////////
			/******  3DES static function  ******/
			private static function encrypt_3DES($message, $key){       

		        // Se establece un IV por defecto
		        $bytes = array(0,0,0,0,0,0,0,0); //byte [] IV = {0, 0, 0, 0, 0, 0, 0, 0}
		        $iv = implode(array_map("chr", $bytes)); //PHP 4 >= 4.0.2
		        
		        // Se cifra
		        if(phpversion() < 7){
		        	$ciphertext = mcrypt_encrypt(MCRYPT_3DES, $key, $message, MCRYPT_MODE_CBC, $iv); //PHP 4 >= 4.0.2
		        	return $ciphertext;
		        }else{
			        $l = ceil(strlen($message) / 8) * 8;
			        $message = $message.str_repeat("\0", $l - strlen($message));
			        return substr(openssl_encrypt($message, 'des-ede3-cbc', $key, OPENSSL_RAW_DATA, "\0\0\0\0\0\0\0\0"), 0, $l);
		        }
			}
		
			/******  Base64 static functions  ******/
			private static function base64_url_encode($input){
				return strtr(base64_encode($input), '+/', '-_');
			}
			private static function encodeBase64($data){
				$data = base64_encode($data);
				return $data;
			}
			private static function base64_url_decode($input){
				return base64_decode(strtr($input, '-_', '+/'));
			}
			private static function decodeBase64($data){
				$data = base64_decode($data);
				return $data;
			}
		
			/******  MAC static function ******/
			private static function mac256($ent,$key){
				$res = hash_hmac('sha256', $ent, $key, true);//(PHP 5 >= 5.1.2)
				return $res;
			}
		
			
			//////////////////////////////////////////////////////////////////////////////////////////////
			//////////////////////////////////////////////////////////////////////////////////////////////
			////////////	   FUNCIONES PARA LA GENERACIÓN DE LA PETICIÓN DE PAGO:			  ////////////
			//////////////////////////////////////////////////////////////////////////////////////////////
			//////////////////////////////////////////////////////////////////////////////////////////////
			
			/******  Obtener Número de pedido ******/
			private static function getOrder($datos){
				$vars=json_decode(ISSignatureUtils::decodeBase64($datos),true);
				
				$numPedido = "";
				if(empty($vars['DS_MERCHANT_ORDER'])){
					$numPedido = $vars['Ds_Merchant_Order'];
				} else {
					$numPedido = $vars['DS_MERCHANT_ORDER'];
				}
				return $numPedido;
			}
			/******  Obtener Número de pedido ******/
			private static function getOrderNotif($datos){
				$vars=json_decode(ISSignatureUtils::decodeBase64($datos),true);
				
				$numPedido = "";
				if(empty($vars['Ds_Order'])){
					$numPedido = $vars['DS_ORDER'];
				} else {
					$numPedido = $vars['Ds_Order'];
				}
				return $numPedido;
			}
			
			public static function createMerchantSignature($key, $ent){
				// Se decodifica la clave Base64
				$key = ISSignatureUtils::decodeBase64($key);
				// Se diversifica la clave con el Número de Pedido
				$key = ISSignatureUtils::encrypt_3DES(ISSignatureUtils::getOrder($ent), $key);
				// MAC256 del parámetro Ds_MerchantParameters
				$res = ISSignatureUtils::mac256($ent, $key);
				// Se codifican los datos Base64
				return ISSignatureUtils::encodeBase64($res);
			}
					
			public static function createMerchantSignatureNotif($key, $datos){
				// Se decodifica la clave Base64
				$key = ISSignatureUtils::decodeBase64($key);
				// Se decodifican los datos Base64
				$decodec = ISSignatureUtils::base64_url_decode($datos);
				// Se diversifica la clave con el Número de Pedido
				$key = ISSignatureUtils::encrypt_3DES(ISSignatureUtils::getOrderNotif($datos), $key);
				// MAC256 del parámetro Ds_Parameters que envía Redsys
				$res = ISSignatureUtils::mac256($datos, $key);
				// Se codifican los datos Base64
				return ISSignatureUtils::base64_url_encode($res);	
			}
		}
	}
