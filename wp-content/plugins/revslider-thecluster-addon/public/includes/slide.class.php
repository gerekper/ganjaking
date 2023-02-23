<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2022 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsTheClusterSlideFront extends RevSliderFunctions {
	
	private $title;
	
	public function __construct($title) {
		
		$this->title = $title;
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
		add_filter('revslider_putCreativeLayer', array($this, 'check_thecluster'), 10, 3);
	
	}
	
	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
	
		$true = array(true, 'on', 1, '1', 'true');
		return !in_array($val, $true);
	
	}
	
	private function isEnabled($slider) {
		
		$settings = $slider->get_params();
		if(empty($settings)) return false;
		$enabled = $this->get_val($settings, array('addOns', 'revslider-' . $this->title . '-addon', 'enable'), false);
		return !$this->isFalse($enabled);
	
	}
	
	// removes thecluster layers that may exist if the AddOn is not officially enabled
	public function check_thecluster($layers, $output, $static_slide) {
		
		$slider = $this->get_val($output, 'slider', false);
		if(empty($slider)) return;
		// addon enabled
		if ($this->isEnabled($slider)) return $layers;
		$ar = array();
		foreach($layers as $layer) {
			$isTheCluster = false;
			if(array_key_exists('subtype', $layer)) {
				$thecluster = $this->get_val($layer, 'subtype', false);
				$isTheCluster = $thecluster === 'thecluster';
			}
			if(!$isTheCluster) $ar[] = $layer;
		}
		return $ar;
	}

	private function aO($val,$d,$s) {
		return $val==$d ? '' : $s.':'.$val.';';
	}

	private function convertColors($a) {
		if(!empty($a) && is_array($a)){
			foreach($a as $c => $v){
				$a[$c] = RSColorpicker::get($v);				
			}
		}				
		
		return $a;
	}
	
		
	public function write_layer_attributes($layer, $slide, $slider) {
		
		// addon enabled
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;		
		$subtype = $this->get_val($layer, 'subtype', '');
		if(!$subtype || $subtype !== 'thecluster') return;
				
		$addOn = $this->get_val($layer, ['addOns', 'revslider-' . $this->title . '-addon'], false);
		if(!$addOn) return;
		
		//MAIN
		$speed = $this->get_val($addOn, 'speed', 25);
		$size = $this->get_val($addOn, 'size', 30);
		$spawnDiameter = $this->get_val($addOn, 'spawnDiameter', 50);
		$amount = $this->get_val($addOn, 'amount', 900);
		$spawnForm = $this->get_val($addOn, 'spawnForm', "random");
		$spawnAccVec = $this->get_val($addOn, 'spawnAccVec', "normalCCW");
		$noiseOn = $this->get_val($addOn, 'noiseOn', false);
		$noiseAmount = $this->get_val($addOn, 'noiseAmount', 50);
		
		//SCENE
		$angle = $this->get_val($addOn, 'angle', 0);
		$tilt = $this->get_val($addOn, 'tilt', 0);
		$showHelper = $this->get_val($addOn, 'showHelper', false);
		$keepCentered = $this->get_val($addOn, 'keepCentered', true);
		$offsetX = $this->get_val($addOn, 'offsetX', 0);
		$offsetY = $this->get_val($addOn, 'offsetY', 0);
		$offsetZ = $this->get_val($addOn, 'offsetZ', 0);
		$dpr = $this->get_val($addOn, 'dpr', 1);

		//GRAVITY
		$mass = $this->get_val($addOn, 'mass', 30);
		$mass2 = $this->get_val($addOn, 'mass2', 30);
		$mass3 = $this->get_val($addOn, 'mass3', 30);
		$g1X = $this->get_val($addOn, 'g1X', -5);
		$g1Y = $this->get_val($addOn, 'g1Y', 0);
		$g1Z = $this->get_val($addOn, 'g1Z', 0);
		$g2X = $this->get_val($addOn, 'g2X', 5);
		$g2Y = $this->get_val($addOn, 'g2Y', 0);
		$g2Z = $this->get_val($addOn, 'g2Z', 0);
		$g3X = $this->get_val($addOn, 'g3X', 0);
		$g3Y = $this->get_val($addOn, 'g3Y', 0);
		$g3Z = $this->get_val($addOn, 'g3Z', 0);
		$limitMovement = $this->get_val($addOn, 'limitMovement', false);
		$limitMovementValue = $this->get_val($addOn, 'limitMovementValue', 15);
		$gravPointsVisible = $this->get_val($addOn, 'gravPointsVisible', false);
		$gravPoint1Toggle = $this->get_val($addOn, 'gravPoint1Toggle', false);
		$gravPoint2Toggle = $this->get_val($addOn, 'gravPoint2Toggle', true);
		$gravPoint3Toggle = $this->get_val($addOn, 'gravPoint3Toggle', false);

		//SPAWN
		$sIX = $this->get_val($addOn, 'sIX', 10);
		$sIY = $this->get_val($addOn, 'sIY', 7);
		$sIZ = $this->get_val($addOn, 'sIZ', 0);
		$sVX = $this->get_val($addOn, 'sVX', 1);
		$sVY = $this->get_val($addOn, 'sVY', 0);
		$sVZ = $this->get_val($addOn, 'sVZ', 0);
		$mirroredOn = $this->get_val($addOn, 'mirroredOn', 0);

		//MOVEMENT
		$animationSel = $this->get_val($addOn, 'animationSel', "off");
		$aniX = $this->get_val($addOn, 'aniX', 0);
		$aniY = $this->get_val($addOn, 'aniY', 0);
		$aniZ = $this->get_val($addOn, 'aniZ', 0);
		$patternSel = $this->get_val($addOn, 'patternSel', "continuous");
		$lifeTimeDelay = $this->get_val($addOn, 'lifeTimeDelay', 5);
		$lifetimeAlphaChange = $this->get_val($addOn, 'lifetimeAlphaChange', "fadeInOut");
		$lifeTimeGradient = $this->get_val($addOn, 'lifeTimeGradient', false);
		$gravP1MoveSel = $this->get_val($addOn, 'gravP1MoveSel', "off");
		$gravP2MoveSel = $this->get_val($addOn, 'gravP2MoveSel', "off");
		$gravP3MoveSel = $this->get_val($addOn, 'gravP3MoveSel', "off");
		$periodicSpawn = $this->get_val($addOn, 'periodicSpawn', false);
		$periodicSpawnValue = $this->get_val($addOn, 'periodicSpawnValue', 0.2);

		//PARTICLE
		//$tc_mainColor = $this->get_val($addOn, 'tc_mainColor', "rgba(90, 200, 250, 0.72)");
		$tc_mainColor = RSColorpicker::get($this->get_val($addOn, 'tc_mainColor', "rgba(90, 200, 250, 0.72)"), "rgba(90, 200, 250, 0.72)");
		$randomizeSize = $this->get_val($addOn, 'randomizeSize', true);
		$particle = $this->get_val($addOn, 'particle', 'default');
		$randomizeOpacity = $this->get_val($addOn, 'randomizeOpacity', true);
		$randSizeMin = $this->get_val($addOn, 'randSizeMin', 50);
		$randSizeMax = $this->get_val($addOn, 'randSizeMax', 150);
		$colorImageMixValue = $this->get_val($addOn, 'colorImageMixValue', 50);

		//INTERACTION
		$gravFollowMouse = $this->get_val($addOn, 'gravFollowMouse', "off");
		$gravFollowMouseValue = $this->get_val($addOn, 'gravFollowMouseValue', 50);
		$gravRotateMouseValue = $this->get_val($addOn, 'gravRotateMouseValue', 30);
		$gravRotateReturnValue = $this->get_val($addOn, 'gravRotateReturnValue', 30);

		//VFX
		$vfxSelector = $this->get_val($addOn, 'vfxSelector', "off");
		$sfxBreathing = $this->get_val($addOn, 'sfxBreathing', true);
		$minBlur = $this->get_val($addOn, 'minBlur', 0);
		$maxBlur = $this->get_val($addOn, 'maxBlur', 10);
		
		$particle = str_replace("http://","//",$particle);
		$particle = str_replace("https://","//",$particle);
		//MAIN (Abriviation: -> Tab letter + first letter of Name)
		$datas = '';
		if($speed != 25) $datas .= 'ms:'.$speed.';';
		if($size != 40) $datas .= 'mi:'.$size.';';
		if($spawnDiameter != 40) $datas .= 'mp:'.$spawnDiameter.';';
		if($amount != 900) $datas .= 'ma:'.$amount.';';
		if($spawnForm != "random") $datas .= 'sf:'.$spawnForm.';';
		if($spawnAccVec != "normalCCW") $datas .= 'mc:'.$spawnAccVec.';';
		if($noiseOn != false) $datas .= 'mn:'.$noiseOn.';';
		if($noiseAmount != 50) $datas .= 'mf:'.$noiseAmount.';';
		
		//SCENE
		if($angle != 0) $datas .= 'sa:'.$angle.';';
		if($tilt != 0) $datas .= 'st:'.$tilt.';';
		if($showHelper != false) $datas .= 'sh:'.$showHelper.';';
		if($keepCentered != true) $datas .= 'sk:'.$keepCentered.';';
		if($offsetX != 0) $datas .= 'sx:'.$offsetX.';';
		if($offsetY != 0) $datas .= 'sy:'.$offsetY.';';
		if($offsetZ != 0) $datas .= 'sz:'.$offsetZ.';';
		if($dpr != 1) $datas .= 'sd:'.$dpr.';';

		//GRAVITY
		if($mass != 30) $datas .= 'mm:'.$mass.';';
		if($mass2 != 30) $datas .= 'mm2:'.$mass2.';';
		if($mass3 != 30) $datas .= 'mm3:'.$mass3.';';
		if($g1X != -5) $datas .= 'g1X:'.$g1X.';';
		if($g1Y != 0) $datas .= 'g1Y:'.$g1Y.';';
		if($g1Z != 0) $datas .= 'g1Z:'.$g1Z.';';
		if($g2X != 5) $datas .= 'g2X:'.$g2X.';';
		if($g2Y != 0) $datas .= 'g2Y:'.$g2Y.';';
		if($g2Z != 0) $datas .= 'g2Z:'.$g2Z.';';
		if($g3X != 0) $datas .= 'g3X:'.$g3X.';';
		if($g3Y != 0) $datas .= 'g3Y:'.$g3Y.';';
		if($g3Z != 0) $datas .= 'g3Z:'.$g3Z.';';
		if($limitMovement != false) $datas .= 'gl:'.$limitMovement.';';
		if($limitMovementValue != 15) $datas .= 'gv:'.$limitMovementValue.';';
		if($gravPointsVisible != false) $datas .= 'gp:'.$gravPointsVisible.';';
		if($gravPoint1Toggle != false) $datas .= 't1:'.$gravPoint1Toggle.';';
		if($gravPoint2Toggle != true) $datas .= 't2:'.$gravPoint2Toggle.';';
		if($gravPoint3Toggle != false) $datas .= 't3:'.$gravPoint3Toggle.';';

		//SPAWN
		if($sIX != 10) $datas .= 'sIX:'.$sIX.';';
		if($sIY != 7) $datas .= 'sIY:'.$sIY.';';
		if($sIZ != 0) $datas .= 'sIZ:'.$sIZ.';';
		if($sVX != 1) $datas .= 'sVX:'.$sVX.';';
		if($sVY != 0) $datas .= 'sVY:'.$sVY.';';
		if($sVZ != 0) $datas .= 'sVZ:'.$sVZ.';';
		if($mirroredOn != 0) $datas .= 'sM:'.$mirroredOn.';';

		//PARTICLE
		if($tc_mainColor != "rgba(90, 200, 250, 0.72)") $datas .= 'pc:'.$tc_mainColor.';';
		if($randomizeSize != true) $datas .= 'pr:'.$randomizeSize.';';
		if($particle != 'default') $datas .= 'pp:'.$particle.';';
		if($randomizeOpacity != true) $datas .= 'po:'.$randomizeOpacity.';';
		if($randSizeMin != 50) $datas .= 'pin:'.$randSizeMin.';';
		if($randSizeMax != 150) $datas .= 'pax:'.$randSizeMax.';';
		if($colorImageMixValue != 50) $datas .= 'cim:'.$colorImageMixValue.';';

		//MOVEMENT
		if($animationSel != 'off') $datas .= 'as:'.$animationSel.';';
		if($aniX != 0) $datas .= 'aniX:'.$aniX.';';
		if($aniY != 0) $datas .= 'aniY:'.$aniY.';';
		if($aniZ != 0) $datas .= 'aniZ:'.$aniZ.';';
		if($patternSel != "continuous") $datas .= 'mps:'.$patternSel.';';
		if($lifeTimeDelay != 5) $datas .= 'ld:'.$lifeTimeDelay.';';
		if($lifetimeAlphaChange != "fadeInOut") $datas .= 'ml:'.$lifetimeAlphaChange.';';
		if($lifeTimeGradient != false) $datas .= 'mg:'.$lifeTimeGradient.';';
		if($gravP1MoveSel != "off") $datas .= 'mp1:'.$gravP1MoveSel.';';
		if($gravP2MoveSel != "off") $datas .= 'mp2:'.$gravP2MoveSel.';';
		if($gravP3MoveSel != "off") $datas .= 'mp3:'.$gravP3MoveSel.';';
		if($periodicSpawn != true) $datas .= 'msp:'.$periodicSpawn.';';
		if($periodicSpawnValue != 0.2) $datas .= 'msv:'.$periodicSpawnValue.';';

		//INTERACTION
		if($gravFollowMouse != "off") $datas .= 'ifm:'.$gravFollowMouse.';';
		if($gravFollowMouseValue != 50) $datas .= 'ifv:'.$gravFollowMouseValue.';';
		if($gravRotateMouseValue != 30) $datas .= 'ifr:'.$gravRotateMouseValue.';';
		if($gravRotateReturnValue != 30) $datas .= 'ifn:'.$gravRotateReturnValue.';';

		//VFX
		if($vfxSelector != "off") $datas .= 'vfx:'.$vfxSelector.';';
		if($sfxBreathing != true) $datas .= 'vbr:'.$sfxBreathing.';';
		if($minBlur != 0) $datas .= 'vmi:'.$minBlur.';';
		if($maxBlur != 10) $datas .= 'vma:'.$maxBlur.';';

		echo RS_T8 . " data-clusterdata='" .$datas."'\n";
	}
	
}
?>