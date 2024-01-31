<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorEntranceAnimations{

	private static $instance;

	const CURRENT_TYPE_TEST = "from_right";

	const ANIMATION_APPEAR = "appear";
	const ANIMATION_SCALE_DOWN = "scale_down";
	const ANIMATION_SCALE_UP = "scale_up";
	const ANIMATION_FROM_LEFT = "from_left";
	const ANIMATION_FROM_TOP = "from_top";
	const ANIMATION_FROM_RIGHT = "from_right";
	const ANIMATION_FROM_BOTTOM = "from_bottom";


	const DISTANCE_SHORTEST = "shortest";
	const DISTANCE_SHORT = "short";
	const DISTANCE_MEDIUM = "medium";
	const DISTANCE_LONG = "long";
	const DISTANCE_LONGEST = "longest";


	const BLUR_SMALL = "small";
	const BLUR_MEDIUM = "medium";
	const BLUR_STRONG = "strong";


	/**
	 * get singleton instance
	 */
	private static function getInstance(){

		if(empty(self::$instance))
			self::$instance = new UniteCreatorEntranceAnimations();

		return(self::$instance);
	}


	/**
	 * get types assoc
	 */
	public static function getAnimationTypes(){

		$arrTypes = array();

		$arrTypes["none"] = __("None","unlimited-elements-for-elementor");

		$arrTypes[self::ANIMATION_APPEAR] = __("Appear","unlimited-elements-for-elementor");
		$arrTypes[self::ANIMATION_SCALE_DOWN] = __("Scale Down","unlimited-elements-for-elementor");
		$arrTypes[self::ANIMATION_SCALE_UP] = __("Scale Up","unlimited-elements-for-elementor");
		$arrTypes[self::ANIMATION_FROM_LEFT] = __("From Left","unlimited-elements-for-elementor");
		$arrTypes[self::ANIMATION_FROM_RIGHT] = __("From Right","unlimited-elements-for-elementor");
		$arrTypes[self::ANIMATION_FROM_TOP] = __("From Top","unlimited-elements-for-elementor");
		$arrTypes[self::ANIMATION_FROM_BOTTOM] = __("From Bottom","unlimited-elements-for-elementor");

		return($arrTypes);
	}


	/**
	 * add settings for elementor controls
	 */
	public static function addSettings($objSettings, $name, $param){

		$title = UniteFunctionsUC::getVal($param, "title");

		//------ animation type ----------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$arrType = self::getAnimationTypes();

		$arrType = array_flip($arrType);

		$objSettings->addSelect($name."_type", $arrType, $title, "none", $params);

		//------  distance ----------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = array(
			$name."_type!" => "none"
		);

		$arrDistance = array(
			self::DISTANCE_SHORTEST=>__("Shortest","unlimited-elements-for-elementor"),
			self::DISTANCE_SHORT=>__("Short","unlimited-elements-for-elementor"),
			self::DISTANCE_MEDIUM=>__("Medium","unlimited-elements-for-elementor"),
			self::DISTANCE_LONG=>__("Long","unlimited-elements-for-elementor"),
			self::DISTANCE_LONGEST=>__("Longest","unlimited-elements-for-elementor")
		);

		$arrDistance = array_flip($arrDistance);

		$objSettings->addSelect($name."_distance", $arrDistance, __("Animation Distance","unlimited-elements-for-elementor"), self::DISTANCE_SHORT, $params);

		//------  speed ----------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_SLIDER;
		$params["elementor_condition"] = array(
			$name."_type!" => "none"
		);

		$params["min"] = 0.3;
		$params["max"] = 2;
		$params["step"] = 0.1;

		$objSettings->addRangeSlider($name."_duration", 0.6, __("Animation Duration (sec)","unlimited-elements-for-elementor"), $params);

		//------ animation step----------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_SLIDER;
		$params["elementor_condition"] = array(
			$name."_type!" => "none"
		);

		$params["min"] = 0.1;
		$params["max"] = 1;
		$params["step"] = 0.1;

		$objSettings->addRangeSlider($name."_step", 0.3, __("Animation Step (sec)","unlimited-elements-for-elementor"), $params);


		//------ item order----------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = array(
			$name."_type!" => "none"
		);

		$arrOrder = array(
			"down"=>__("First To Last","unlimited-elements-for-elementor"),
			"up"=>__("Last to First","unlimited-elements-for-elementor")
			//"random"=>__("Random","unlimited-elements-for-elementor")
		);

		$arrOrder = array_flip($arrOrder);

		$objSettings->addSelect($name."_order", $arrOrder, __("Animate Items Order","unlimited-elements-for-elementor"), "down", $params);

		//------ blur ----------

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = array(
			$name."_type!" => "none"
		);

		$arrBlur = array(
			"none"=>__("No Blur","unlimited-elements-for-elementor"),
			self::BLUR_SMALL=>__("Small","unlimited-elements-for-elementor"),
			self::BLUR_MEDIUM=>__("Medium","unlimited-elements-for-elementor"),
			self::BLUR_STRONG=>__("Strong","unlimited-elements-for-elementor"),
		);


		$arrBlur = array_flip($arrBlur);

		$objSettings->addSelect($name."_blur", $arrBlur, __("Add Blur","unlimited-elements-for-elementor"), self::BLUR_MEDIUM, $params);

	}


	/**
	 * get blur css from blur type
	 */
	private function getBlurNum($blurType){

		if(empty($blurType))
			return(null);

		if($blurType == "none")
			return(null);

		$blur = 0;

		switch($blurType){
			case self::BLUR_SMALL:
				$blur = 4;
			break;
			case self::BLUR_MEDIUM:
				$blur = 10;
			break;
			case self::BLUR_STRONG:
				$blur = 40;
			break;
			default:
				return(null);
			break;
		}

		return($blur);
	}


	/**
	 * get scale by distance
	 */
	private function getScale($distanceType){

		$scale = 0;
		switch($distanceType){
			case self::DISTANCE_SHORTEST:
				$scale = 1.1;
			break;
			default:
			case self::DISTANCE_SHORT:
				$scale = 1.3;
			break;
			case self::DISTANCE_MEDIUM:
				$scale = 1.5;
			break;
			case self::DISTANCE_LONG:
				$scale = 1.8;
			break;
			case self::DISTANCE_LONGEST:
				$scale = 2.2;
			break;
		}

		return($scale);
	}


	/**
	 * get translate values
	 */
	private function getTranslate($distanceType){

		$scale = 0;
		switch($distanceType){
			case self::DISTANCE_SHORTEST:
				$scale = 50;
			break;
			default:
			case self::DISTANCE_SHORT:
				$scale = 100;
			break;
			case self::DISTANCE_MEDIUM:
				$scale = 250;
			break;
			case self::DISTANCE_LONG:
				$scale = 400;
			break;
			case self::DISTANCE_LONGEST:
				$scale = 700;
			break;
		}

		return($scale);


	}

	/**
	 * get animation time and distance values
	 */
	private function getKeyframesCss($type, $distanceType){

		$from = "";
		$to = "";

		switch($type){
			case self::ANIMATION_SCALE_DOWN:

				$scale = $this->getScale($distanceType);

				$from = "transform:scale({$scale});";
				$to = "transform:scale(1);";

			break;
			case self::ANIMATION_SCALE_UP:

				$scale = $this->getScale($distanceType);

				$newScale = (1 + (1-$scale));

				$from = "transform:scale({$newScale});";
				$to = "transform:scale(1);";

			break;
			case self::ANIMATION_FROM_LEFT:

				$translate = $this->getTranslate($distanceType);

            	$from = "transform: translateX(-{$translate}px);";
				$to = "transform: translateX(0px);";

			break;
			case self::ANIMATION_FROM_RIGHT:

				$translate = $this->getTranslate($distanceType);

            	$from = "transform: translateX({$translate}px);";
				$to = "transform: translateX(0px);";

			break;
			case self::ANIMATION_FROM_TOP:

				$translate = $this->getTranslate($distanceType);

            	$from = "transform: translateY(-{$translate}px);";
				$to = "transform: translateY(0px);";
			break;
			case self::ANIMATION_FROM_BOTTOM:

				$translate = $this->getTranslate($distanceType);

            	$from = "transform: translateY({$translate}px);";
				$to = "transform: translateY(0px);";
			break;
			case self::ANIMATION_APPEAR:

            	$from = "";
				$to = "";

			break;
		}

		$output = array();

		$output["from"] = $from;
		$output["to"] = $to;

		return($output);
	}


	/**
	 * put entrance animations
	 */
	public function putEntranceAnimationCss_work($arrData, $paramName, $param){

		if(empty($param))
			return(false);

		if(empty($arrData))
			return(false);

		$arrValues = UniteFunctionsUC::getVal($param, "value");

		$animationType = UniteFunctionsUC::getVal($arrValues, $paramName."_type");

		if($animationType == "none" || empty($animationType))
			return(false);

		$classItem = UniteFunctionsUC::getVal($param, "entrance_animation_item_class");

		if(empty($classItem))
			UniteFunctionsUC::throwError("Please specify item class in widget entrance animation attribute");

		$id = UniteFunctionsUC::getVal($arrData, "uc_id");

		//get blur

		$blurType = UniteFunctionsUC::getVal($arrValues, $paramName."_blur");

		$blurNum = $this->getBlurNum($blurType);

		//get time (speed)

		$arrDuration = UniteFunctionsUC::getVal($arrValues, $paramName."_duration");
		$duration = UniteFunctionsUC::getVal($arrDuration,"size", 0.6);

		//distance

		$distanceType = UniteFunctionsUC::getVal($arrValues, $paramName."_distance");
		$arrKeyFrames = $this->getKeyframesCss($animationType, $distanceType);


		$from = UniteFunctionsUC::getVal($arrKeyFrames, "from");
		$to = UniteFunctionsUC::getVal($arrKeyFrames, "to");

		?>

@keyframes <?php echo $id?>__item-animation {
  0% {
            <?php echo $from?>

        	<?php if(!empty($blurNum)):?>
        	filter: blur(<?php echo $blurNum?>px);
			<?php endif?>

    	    opacity: 0;
  }
  100% {
            <?php echo $to?>

        	<?php if(!empty($blurNum)):?>
            filter: blur(0px);
            <?php endif?>

    		opacity: 1;
  }
}


#<?php echo $id?> .<?php echo $classItem?>{
	opacity:0;
}


#<?php echo $id?> .uc-entrance-animate {
  opacity:1;
}

#<?php echo $id?> .uc-entrance-animate {
	animation: <?php echo $id?>__item-animation <?php echo $duration?>s cubic-bezier(0.470, 0.000, 0.745, 0.715) both;
}

			<?php

		}


		/**
		 * put entrance animation functions
		 */
		private function putEntranceAnimationJS_functions($checkRunOnce = true){

			if($checkRunOnce == true){

				$isRunOnce = HelperUC::isRunCodeOnce("ue_entrance_animations_js");

				if($isRunOnce === false){

					return(false);
				}

			}

			?>

  //start the animation - add animation class
  function ueStartEntranceAnimation(objElement, step, classItem, order){

    var time = 0;

    if(!step)
    	var step = 100;

    var objItems = objElement.find("."+classItem);

    var numItems = objItems.length;

    if(numItems == 0)
    	return(false);

    var maxTime = (numItems-1) * step;

    objItems.each(function(index, item){

   	  var timeoutTime = time;
   	  if(order == "up")
   	  	timeoutTime = maxTime - time;

      var objItem = jQuery(item);

      setTimeout(function(){

            objItem.addClass("uc-entrance-animate");

      },timeoutTime);

      time += step;

    });
  }

    //check and add animation
    function ueCheckEntranceAnimation(objElement, step, classItem, order){

        var isStarted = objElement.data("ue_entrance_animation_started");

        if(isStarted === true)
        	return(false);

      	var isInside = ueIsElementInViewport(objElement);

        if(isInside == false)
          return(false);

        ueStartEntranceAnimation(objElement, step, classItem, order);

        objElement.data("ue_entrance_animation_started", true);
  }

			<?php


	}





	/**
	 * put entrance animation js
	 */
	public function putEntranceAnimationJS_work($arrData, $paramName, $param){

		$classItem = UniteFunctionsUC::getVal($param, "entrance_animation_item_class");

		$arrValues = UniteFunctionsUC::getVal($param, "value");

		$animationType = UniteFunctionsUC::getVal($arrValues, $paramName."_type");

		if($animationType == "none" || empty($animationType))
			return(false);

		$isInsideEditor = UniteFunctionsUC::getVal($arrData, "uc_inside_editor");

		//to boolean
		$isInsideEditor = ($isInsideEditor == "yes");

		$id = UniteFunctionsUC::getVal($arrData, "uc_id");

		$arrStep = UniteFunctionsUC::getVal($arrValues, $paramName."_step");
		$animationStep = UniteFunctionsUC::getVal($arrStep,"size", 0.3);

		$animationStep = $animationStep * 1000;


		$order = UniteFunctionsUC::getVal($arrValues, $paramName."_order");


			?>

/* entrance animation js*/

<?php
	if($isInsideEditor == false){
		HelperHtmlUC::putJSFunc_isElementInViewport();
		$this->putEntranceAnimationJS_functions();
	}

?>

jQuery(document).ready(function(){

  <?php if($isInsideEditor == true){

  		HelperHtmlUC::putJSFunc_isElementInViewport(false);
  		$this->putEntranceAnimationJS_functions(false);
  }
  ?>

  function initUEEntranceAnimation(){

	  var objElement = jQuery("#<?php echo $id?>");

	  if(objElement.length == 0)
	  	 return(false);

	   if(typeof ueCheckEntranceAnimation == "undefined"){
	      return(false);
	   }

	    ueCheckEntranceAnimation(objElement, <?php echo $animationStep?>,"<?php echo $classItem?>", "<?php echo $order?>");

	    jQuery(window).on("scroll", function(){
	    	ueCheckEntranceAnimation(objElement, <?php echo $animationStep?>, "<?php echo $classItem?>", "<?php echo $order?>")
	    });

	    objElement.on("uc_ajax_refreshed", function(){

	        objElement.removeData("ue_entrance_animation_started");

	    	ueCheckEntranceAnimation(objElement, <?php echo $animationStep?>, "<?php echo $classItem?>", "<?php echo $order?>")
	    });

	return(true);
  }

  var isInited = initUEEntranceAnimation();

  if(isInited == false)
	  jQuery(document).on("elementor/popup/show", initUEEntranceAnimation);

});
			<?php

		}

	/**
	 * put entrance animation css
	 */
	public static function putEntranceAnimationCss($arrData, $paramName, $param){

		$objInstance = self::getInstance();

		$objInstance->putEntranceAnimationCss_work($arrData, $paramName, $param);

	}

	/**
	 * put entrance animation js
	 */
	public static function putEntranceAnimationJS($arrData, $paramName, $param){

		$instance = self::getInstance();

		$instance->putEntranceAnimationJS_work($arrData, $paramName, $param);

	}


}
