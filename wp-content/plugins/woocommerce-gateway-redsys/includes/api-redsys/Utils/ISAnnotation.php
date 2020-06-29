<?php
if(!class_exists('ISAnnotation')){
	class ISAnnotation{
		
		private static function getAnnotation($object, $name){
			$retorno = NULL;
			
			$doc = $object->getDocComment();
			preg_match('#@'.$name.'=(.+)(\s)*(\r)*\n#s', $doc, $annotations);
			if(is_array($annotations) && sizeof($annotations)>=2){
				$retorno=trim(explode(" ",$annotations[1])[0]);
			}
			
			return $retorno;
		}	
		
		public static function getXmlElem($object){
			return ISAnnotation::getAnnotation($object, "XML_ELEM");
		}
		
		public static function getXmlClass($object){
			return ISAnnotation::getAnnotation($object, "XML_CLASS");
		}
	}
}
