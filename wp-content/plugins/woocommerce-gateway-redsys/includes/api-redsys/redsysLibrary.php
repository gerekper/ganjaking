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
 
///////////////////// FUNCIONES DE VALIDACION
//Importe

function checkImporte($total) {
	return preg_match("/^\d+$/", $total);
}
 
//Pedido
function checkPedidoNum($pedido) {
	return preg_match("/^\d{1,12}$/", $pedido);
}
function checkPedidoAlfaNum($pedido) {
	return preg_match("/^\w{1,12}$/", $pedido);
}

//Fuc
function checkFuc($codigo) {
	$retVal = preg_match("/^\d{2,9}$/", $codigo);
	if($retVal) {
		$codigo = str_pad($codigo,9,"0",STR_PAD_LEFT);
		$fuc = intval($codigo);
		$check = substr($codigo, -1);
		$fucTemp = substr($codigo, 0, -1);
		$acumulador = 0;
		$tempo = 0;
		
		for ($i = strlen($fucTemp)-1; $i >= 0; $i-=2) {
			$temp = intval(substr($fucTemp, $i, 1)) * 2;
			$acumulador += intval($temp/10) + ($temp%10);
			if($i > 0) {
				$acumulador += intval(substr($fucTemp,$i-1,1));
			}
		}
		$ultimaCifra = $acumulador % 10;
		$resultado = 0;
		if($ultimaCifra != 0) {
			$resultado = 10 - $ultimaCifra;
		}
		$retVal = $resultado == $check;
	}
	return $retVal;
}

//Moneda
function checkMoneda($moneda) {
   return preg_match("/^\d{1,3}$/", $moneda);
}

//Respuesta
function checkRespuesta($respuesta) {
   return preg_match("/^\d{1,4}$/", $respuesta);
}

//Firma
function checkFirma($firma) {
   return preg_match("/^[a-zA-Z0-9\/+]{32}$/", $firma);
}

//AutCode
function checkAutCode($id_trans) {
	return preg_match("/^\w{1,6}$/", $id_trans);
}

//Nombre del Comecio
function checkNombreComecio($nombre) {
	return preg_match("/^\w*$/", $nombre);
}

//Terminal
function checkTerminal($terminal) {
	return preg_match("/^\d{1,3}$/", $terminal);
}

function generateIdLog() {
	$vars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$stringLength = strlen($vars);
	$result = '';
	for ( $i = 0; $i < 20; $i++ ) {
		$result .= $vars[rand(0, $stringLength - 1)];
	}
	return $result;
}


///////////////////// FUNCIONES DE LOG
function escribirLog($texto,$activo) {
	if($activo=="si"){
		// Log
		$logfilename = 'logs/redsysLog.log';
		$fp = @fopen($logfilename, 'a');
		if ($fp) {
			fwrite($fp, date('M d Y G:i:s') . ' -- ' . $texto . "\r\n");
			fclose($fp);
		}
	}
}

function getVersionClave() {
	return "HMAC_SHA256_V1";
}
