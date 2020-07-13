<?php

if ( ! class_exists( 'ISGenericXml' ) ) {
	include_once $GLOBALS["REDSYS_API_PATH"]."/Utils/ISAnnotation.php";

	abstract class ISGenericXml {	
		
		private function setPropertyValue(ReflectionProperty $prop, $value){
			$nombreSetter="set".strtoupper(substr($prop->getName(),0,1)).substr($prop->getName(),1,strlen($prop->getName())-1);
			$setter=new ReflectionMethod(get_class($this),$nombreSetter);
			if($setter){
				$setter->invoke($this,$value);
			}
		}
		
		private function getPropertyValue(ReflectionProperty $prop){
			$resultado=NULL;
			
			$nombreSetter="get".strtoupper(substr($prop->getName(),0,1)).substr($prop->getName(),1,strlen($prop->getName())-1);
			$setter=new ReflectionMethod(get_class($this),$nombreSetter);
			if($setter){
				$resultado=$setter->invoke($this);
			}
			
			return $resultado;
		}
		
		public function getTagContent($tag, $xml){
			$retorno=NULL;
			
			if($tag && $xml){
				$ini=strpos($xml, "<".$tag.">");
				$fin=strpos($xml, "</".$tag.">");
				if($ini!==false && $fin!==false){
					$ini=$ini+strlen("<".$tag.">");
					if($ini<=$fin){
						$retorno=substr($xml, $ini, $fin-$ini);
					}
				}
			}
				
			return $retorno;
		}
		
		public function parseXml($xml){
			$thisClass=new ReflectionClass(get_class($this));
			$thisTag=ISAnnotation::getXmlElem($thisClass);
			if($thisTag !== NULL){
				$thisContent=$this->getTagContent($thisTag, $xml);
				if($thisContent !== NULL){
					foreach($thisClass->getProperties() as $prop){
						$xmlClass=ISAnnotation::getXmlClass($prop);
						if($xmlClass !== NULL){
							$propClass=new ReflectionClass($xmlClass);
							$obj=$propClass->newInstance();
							
							$propClass->getMethod("parseXml")->invoke($obj,$thisContent);
							
							$this->setPropertyValue($prop, $obj);

							$xmlElem=ISAnnotation::getXmlElem($propClass);
							$thisContent=str_replace("<".$xmlElem.">".$this->getTagContent($xmlElem, $thisContent)."</".$xmlElem.">","",$thisContent);
						}
						else{
							$xmlElem=ISAnnotation::getXmlElem($prop);
							if($xmlElem !== NULL){
								$tagContent=$this->getTagContent($xmlElem, $thisContent);
								if($tagContent !== NULL){
									$this->setPropertyValue($prop, $tagContent);
									$thisContent=str_replace("<".$xmlElem.">".$tagContent."</".$xmlElem.">","",$thisContent);
								}
							}
						}
					}
				}
			}
		}

		public function toXml(){
			$xml="";
			$thisClass=new ReflectionClass(get_class($this));
			$thisTag=ISAnnotation::getXmlElem($thisClass);
			if($thisTag !== NULL){
				$xml.="<".$thisTag.">";
				foreach($thisClass->getProperties() as $prop){
					$xmlClass=ISAnnotation::getXmlClass($prop);
					if($xmlClass !== NULL){
						$obj=$this->getPropertyValue($prop);
						if($obj !== NULL){
							$propClass=new ReflectionClass($xmlClass);
							$xml.=$propClass->getMethod("toXml")->invoke($obj);
						}
					}
					else{
						$xmlElem=ISAnnotation::getXmlElem($prop);
						if($xmlElem !== NULL){
							$obj=$this->getPropertyValue($prop);
							if($obj !== NULL)
								$xml.="<".$xmlElem.">".$obj."</".$xmlElem.">";
						}
					}
				}
				try{
					$params=$thisClass->getProperty("parameters");
					if($params){
						$valores=$this->getPropertyValue($params);
						
						if($valores!=null){
							foreach($valores as $key=>$value){
								$xml.="<".$key.">".$value."</".$key.">";								
							}
						}
					}
				} catch(Exception $e){}
				$xml.="</".$thisTag.">";
			}

			return $xml;
		}


		public function toJson(){
			return json_encode($this->toJsonWithArray(array()));
		}
			
		public function toJsonWithArray($arr){
			$thisClass=new ReflectionClass(get_class($this));
			$thisTag=ISAnnotation::getXmlElem($thisClass);
			if($thisTag !== NULL){
				foreach($thisClass->getProperties() as $prop){
					$xmlClass=ISAnnotation::getXmlClass($prop);
					if($xmlClass !== NULL){
						$xmlElem=ISAnnotation::getXmlElem($prop);
						$obj=$this->getPropertyValue($prop);
						if($obj !== NULL && $xmlElem !== NULL){
							$propClass=new ReflectionClass($xmlClass);
							$val=$propClass->getMethod("toJsonWithArray")->invoke($obj,array());
							$arr[$xmlElem]=$val;
						}
					}
					else{
						$xmlElem=ISAnnotation::getXmlElem($prop);
						if($xmlElem !== NULL){
							$obj=$this->getPropertyValue($prop);
							if($obj !== NULL)
								$arr[$xmlElem]=$obj;
						}
					}
				}
					
				try{
					$params=$thisClass->getProperty("parameters");
					if($params){
						$valores=$this->getPropertyValue($params);
							
						if($valores!=null){
							foreach($valores as $key=>$value){
								$arr[$key]=$value;
							}
						}
					}
				} catch(Exception $e){}
					
				return $arr;
			}
		}
		
		public function parseJson($json){
			$arr=json_decode($json,true);
			
			$thisClass=new ReflectionClass(get_class($this));
			foreach($thisClass->getProperties() as $prop){
				$xmlClass=ISAnnotation::getXmlClass($prop);
				if($xmlClass !== NULL){
					$propClass=new ReflectionClass($xmlClass);
					$xmlElem=ISAnnotation::getXmlElem($prop);
					
					if($xmlElem !== NULL && isset($arr[$xmlElem])){
						$obj=$propClass->newInstance();
	
						$propClass->getMethod("parseJson")->invoke($obj,$arr[$xmlElem]);
	
						$this->setPropertyValue($prop, $obj);
						unset($arr[$xmlElem]);
					}
				}
				else{
					$xmlElem=ISAnnotation::getXmlElem($prop);
					if($xmlElem !== NULL && isset($arr[$xmlElem])){
						$tagContent=$arr[$xmlElem];
						if($tagContent !== NULL){
							$this->setPropertyValue($prop, $tagContent);
							unset($arr[$xmlElem]);
						}
					}
				}
			}
		}
	}
}
