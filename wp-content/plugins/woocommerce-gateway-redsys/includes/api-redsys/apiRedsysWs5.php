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

class RedsysAPIWs{

	/******  Array de DatosEntrada ******/
	var $vars_pay = array();
	
	/******  Set parameter ******/
	function setParameter($key,$value){
		$this->vars_pay[$key]=$value;
	}

	/******  Get parameter ******/
	function getParameter($key){
		return $this->vars_pay[$key];
	}
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////
	////////////					FUNCIONES AUXILIARES:							  ////////////
	//////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////
	

	/******  3DES Function  ******/
	function encrypt_3DES($message, $key){
		// Se establece un IV por defecto
		$bytes = array(0,0,0,0,0,0,0,0); //byte [] IV = {0, 0, 0, 0, 0, 0, 0, 0}
		$iv = implode(array_map("chr", $bytes)); //PHP 4 >= 4.0.2

		// Se cifra
		$ciphertext = mcrypt_encrypt(MCRYPT_3DES, $key, $message, MCRYPT_MODE_CBC, $iv); //PHP 4 >= 4.0.2
		return $ciphertext;
	}

	/******  Base64 Functions  ******/
	function base64_url_encode($input){
		return strtr(base64_encode($input), '+/', '-_');
	}
	function encodeBase64($data){
		$data = base64_encode($data);
		return $data;
	}
	function base64_url_decode($input){
		return base64_decode(strtr($input, '-_', '+/'));
	}
	function decodeBase64($data){
		$data = base64_decode($data);
		return $data;
	}

	/******  MAC Function ******/
	function mac256($ent,$key){
		$res = hash_hmac('sha256', $ent, $key, true);//(PHP 5 >= 5.1.2)
		return $res;
	}

	
	//////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////
	////////////	   FUNCIONES PARA LA GENERACIÓN DE LA PETICIÓN DE PAGO:			  ////////////
	//////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////
	
	/******  Obtener Número de pedido ******/
	function getOrder($datos){
		$posPedidoIni = strrpos($datos, "<DS_MERCHANT_ORDER>");
		$tamPedidoIni = strlen("<DS_MERCHANT_ORDER>");
		$posPedidoFin = strrpos($datos, "</DS_MERCHANT_ORDER>");
		return substr($datos,$posPedidoIni + $tamPedidoIni,$posPedidoFin - ($posPedidoIni + $tamPedidoIni));
	}
	function createMerchantSignatureHostToHost($key, $ent){
		// Se decodifica la clave Base64
		$key = $this->decodeBase64($key);
		// Se diversifica la clave con el Número de Pedido
		$key = $this->encrypt_3DES($this->getOrder($ent), $key);
		// MAC256 del parámetro Ds_MerchantParameters
		$res = $this->mac256($ent, $key);
		// Se codifican los datos Base64
		return $this->encodeBase64($res);
	}

	
	//////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////
	//////////// FUNCIONES PARA LA RECEPCIÓN DE DATOS DE PAGO (Respuesta HOST to HOST) ///////////
	//////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////
	
	function createMerchantSignatureResponseHostToHost($key, $datos, $numPedido){
		// Se decodifica la clave Base64
		$key = $this->decodeBase64($key);
		// Se diversifica la clave con el Número de Pedido
		$key = $this->encrypt_3DES($numPedido, $key);
		// MAC256 del parámetro Ds_Parameters que envía Redsys
		$res = $this->mac256($datos, $key);
		// Se codifican los datos Base64
		return $this->encodeBase64($res);	
	}
}
