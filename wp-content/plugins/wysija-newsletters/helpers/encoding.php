<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_encoding{
	function change($data,$input,$output){
		$input = strtoupper(trim($input));
		$output = strtoupper(trim($output));
		if($input == $output) return $data;
		if ($input == 'UTF-8' && $output == 'ISO-8859-1'){
			$data = str_replace(array('€','„','“'),array('EUR','"','"'),$data);
		}
		if (function_exists('iconv')){
			set_error_handler('acymailing_error_handler_encoding');
			$encodedData = iconv($input, $output."//IGNORE", $data);
			restore_error_handler();
			if(!acymailing_error_handler_encoding('result')){
				return $encodedData;
			}
		}
		if (function_exists('mb_convert_encoding')){
			return mb_convert_encoding($data, $output, $input);
		}
		if ($input == 'UTF-8' && $output == 'ISO-8859-1'){
			return utf8_decode($data);
		}
		if ($input == 'ISO-8859-1' && $output == 'UTF-8'){
			return utf8_encode($data);
		}
		return $data;
	}
}//endclass
 function acymailing_error_handler_encoding($errno,$errstr=''){
      static $error = false;
      if(is_string($errno) && $errno=='result'){
          $currentError = $error;
          $error = false;
          return $currentError;
      }
      $error = true;
      return true;
 }