<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2022 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsParticleWaveSlideFront extends RevSliderFunctions {
	
	private $title;
	
	public function __construct($title) {
		
		$this->title = $title;
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
		add_filter('revslider_putCreativeLayer', array($this, 'check_particlewave'), 10, 3);
	
	}
	
	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
	
		$true = [true, 'on', 1, '1', 'true'];
		return !in_array($val, $true);
	
	}
	
	private function isEnabled($slider) {
		
		$settings = $slider->get_params();
		if(empty($settings)) return false;
		$enabled = $this->get_val($settings, ['addOns', 'revslider-' . $this->title . '-addon', 'enable'], false);
		return !$this->isFalse($enabled);
	
	}
	
	// removes particlewave layers that may exist if the AddOn is not officially enabled
	public function check_particlewave($layers, $output, $static_slide) {
		
		$slider = $this->get_val($output, 'slider', false);
		if(empty($slider)) return;
		// addon enabled
		if ($this->isEnabled($slider)) return $layers;
		$ar = array();
		foreach($layers as $layer) {
			$isParticleWave = false;
			if(array_key_exists('subtype', $layer)) {
				$particlewave = $this->get_val($layer, 'subtype', false);
				$isParticleWave = $particlewave === 'particlewave';
			}
			if(!$isParticleWave) $ar[] = $layer;
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
		if(!$subtype || $subtype !== 'particlewave') return;
				
		$addOn = $this->get_val($layer, ['addOns', 'revslider-' . $this->title . '-addon'], false);
		if(!$addOn) return;
		
		//WAVE
		$type = $this->get_val($addOn, 'type', 'default');
		$speed = $this->get_val($addOn, 'speed', 40);
		$curve = $this->get_val($addOn, 'curve', 25);		
		$amp = $this->get_val($addOn, 'amplitude', 35);
		$gap = $this->get_val($addOn, 'gap', 20);
		$randomizeValue = $this->get_val($addOn, 'randomizeValue', 0);
		
		// SCENE
		$keepCentered = $this->get_val($addOn, 'keepCentered', true);
		$angle = $this->get_val($addOn, 'angle', 0);
		$tilt = $this->get_val($addOn, 'tilt', 10);
		$sx = $this->get_val($addOn, 'sx', 0);
		$sy = $this->get_val($addOn, 'sy', 0);
		$sz = $this->get_val($addOn, 'sz', 0);
		$dpr = $this->get_val($addOn, 'dpr', 1);
		
		// PARTICLES
		$particle = $this->get_val($addOn, 'particle', 'default');
		$particleSize = $this->get_val($addOn, 'particleSize', 20);
		$particleAmount = $this->get_val($addOn, 'particleAmount', 900);
		$groShr = $this->get_val($addOn, 'groShr', 10);
		
		// BALANCE
		$fade = $this->get_val($addOn, 'fade', 'off'); 
		$sideOpacity = $this->get_val($addOn, 'sideOpacity', 'off'); 
		$opacityIntensity = $this->get_val($addOn, 'opacityIntensity', 95);
		
		// COLOR
		$particleColor = $this->get_val($addOn, 'particleColor', 'color');
		$parbackground = $this->get_val($addOn, 'parbackground', 'default');
		$bgfit = $this->get_val($addOn, 'bgfit', 'cover');
		$bgpos = $this->get_val($addOn, 'bgpos', 'center center');
		
		// LINES
		$particlesOn = $this->get_val($addOn, 'particlesOn', true);
		$borderFilled = $this->get_val($addOn, 'borderFilled', false);
		$connectionType = $this->get_val($addOn, 'connectionType', 'off');
		$customColorOn = $this->get_val($addOn, 'customColorOn', false);
		$fillColor = $this->get_val($addOn, 'fillColor', '#ffffff');
		$finish = $this->get_val($addOn, 'finish', 'matte');
		$linesOpacity = $this->get_val($addOn, 'linesOpacity', 20);
		$hexaShift = $this->get_val($addOn, 'hexaShift', 0);
		
		// GENERAL
		$wp_color = RSColorpicker::get($this->get_val($addOn, 'wp_color', '#ffffff'), '#ffffff');
		
		// BLENDING MODE
		$blending = $this->get_val($addOn, 'blending', 'normal');

		// ANIMATION
		$movement = $this->get_val($addOn, 'movement', 'off');

		// PATH
		$animPath = $this->get_val($addOn, 'animPath', "ocean");
		$animPathHexa = $this->get_val($addOn, 'animPathHexa', "ocean");
		$aniMainSpeed = $this->get_val($addOn, 'aniMainSpeed', 40);

		// PENDULUM
		$angleEnd = $this->get_val($addOn, 'angleEnd', 0);
		$tiltEnd = $this->get_val($addOn, 'tiltEnd', 0);
		$offsetxEnd = $this->get_val($addOn, 'offsetxEnd', 0);
		$offsetyEnd = $this->get_val($addOn, 'offsetyEnd', 0);
		$offsetzEnd = $this->get_val($addOn, 'offsetzEnd', 0);
		$animRoute = $this->get_val($addOn, 'animRoute', "rounded");

		// LOOP
		$angleSpeed = $this->get_val($addOn, 'angleSpeed', -13);
		$tiltSpeed = $this->get_val($addOn, 'tiltSpeed', 8);
		$offsetzSpeed = $this->get_val($addOn, 'offsetzSpeed', 5);

		// Interaction
		$interaction = $this->get_val($addOn, 'interaction', "off");
		$pTilt = $this->get_val($addOn, 'pTilt', 30);
		$pRotate = $this->get_val($addOn, 'pRotate', 30);
		$pSpeed = $this->get_val($addOn, 'pSpeed', 40);
		$pIntensity = $this->get_val($addOn, 'pIntensity', 50);
		$sbshifty = $this->get_val($addOn, 'sbshifty', 20);
		
		// DESIGN
		$ppfx = $this->get_val($addOn, 'ppfx', "off");
		$focus = $this->get_val($addOn, 'focus', 5);
		$aperture = $this->get_val($addOn, 'aperture', 5);
		$maxblur = $this->get_val($addOn, 'maxblur', 10);
		$ppbb = $this->get_val($addOn, 'ppbb', true);
		$minblur = $this->get_val($addOn, 'minblur', 0);

		$ppfn = $this->get_val($addOn, 'ppfn', 80);
		$ppfsc = $this->get_val($addOn, 'ppfsc', 82);
		$ppfsz = $this->get_val($addOn, 'ppfsz', 256);
		$ppfgs = $this->get_val($addOn, 'ppfgs', false);
		
		
		$particle = str_replace("http://","//",$particle);
		$particle = str_replace("https://","//",$particle);
		$parbackground = str_replace("http://","//",$parbackground);
		$parbackground = str_replace("https://","//",$parbackground);
		$datas = '';
		if($type != 'default') $datas .= 'ddw:'.$type.';';
		if($speed != 40) $datas .= 's:'.$speed.';';
		if($curve != 25) $datas .= 'c:'.$curve.';';												
		if($amp != 35) $datas .= 'a:'.$amp.';';
		if($gap != 20) $datas .= 'g:'.$gap.';';	
		if($randomizeValue != 0) $datas .= 'rv:'.$randomizeValue.';';
		
		if($keepCentered != true) $datas .= 'kc:'.$keepCentered.';';
		if($angle != 0) $datas .= 'an:'.$angle.';';						
		if($tilt != 10) $datas .= 't:'.$tilt.';';
		if($sx != 0) $datas .= 'sx:' .$sx.';';
		if($sy != 0) $datas .= 'sy:' .$sy.';';
		if($sz != 0) $datas .= 'sz:' .$sz.';';
		if($dpr != 0) $datas .= 'dpr:' .$dpr.';';

		if($particle != 'default') $datas .= 'p:'.$particle.';';
		if($particleSize != 20) $datas .= 'particleSize:'.$particleSize.';';
		if($particleAmount != 900) $datas .= 'particleAmount:'.$particleAmount.';';
		if($groShr != 10) $datas .= 'gs:'.$groShr.';';
		
		if($fade != 'off') $datas .= 'ddfd:'.$fade.';';
		if($opacityIntensity != 95) $datas .= 'oi:'.$opacityIntensity.';';

		if($particleColor != 'color') $datas .= 'ddu:'.$particleColor.';';
		if($parbackground != 'default') $datas .= 'bg:'.$parbackground.';';
		if($bgfit != 'cover') $datas .= 'bgf:'.$bgfit.';';
		if($bgpos != 'center center') $datas .= 'bgp:'.$bgpos.';';
		
		if($particlesOn != true) $datas .= 'po:'.$particlesOn.';';
		if($borderFilled != false) $datas .= 'bf:'.$borderFilled.';';
		if($connectionType != 'off') $datas .= 'ddl:'.$connectionType.';';
		if($customColorOn != false) $datas .= 'lr:'.$customColorOn.';';
		if($fillColor != '#ffffff') $datas .= 'lb:'.$fillColor.';';
		 //  $datas .= 'lf:'.$fillColor.';';
		if($finish != 'matte') $datas .= 'ddh:'.$finish.';';
		if($linesOpacity != 20) $datas .= 'lo:'.$linesOpacity.';';
		if($hexaShift != 0) $datas .= 'hs:'.$hexaShift.';';
		 
		if($wp_color != '#ffffff') $datas .= 'co:'.$wp_color.';';

		if($blending != 'normal') $datas .= 'b0:'.$blending.';';
		
		if($movement != 'off') $datas .= 'tb:'.$movement.';';
		
		if($animPath != 'ocean') $datas .= 'a1:'.$animPath.';';
		if($animPathHexa != 'ocean') $datas .= 'aph:'.$animPathHexa.';';
		if($aniMainSpeed != 40) $datas .= 'as:'.$aniMainSpeed.';';
		
		if($angleEnd != 0) $datas .= 'e1:'.$angleEnd.';';
		if($tiltEnd != 0) $datas .= 'e2:'.$tiltEnd.';';
		if($offsetxEnd != 0) $datas .= 'e3:'.$offsetxEnd.';';
		if($offsetyEnd != 0) $datas .= 'e4:'.$offsetyEnd.';';
		if($offsetzEnd != 0) $datas .= 'e5:'.$offsetzEnd.';';
		if($animRoute != 'rounded') $datas .= 'a2:'.$animRoute.';';

		if($angleSpeed != -13) $datas .= 'e6:'.$angleSpeed.';';
		if($tiltSpeed != 8) $datas .= 'e7:'.$tiltSpeed.';';
		if($offsetzSpeed != 5) $datas .= 'e0:'.$offsetzSpeed.';';

		if($interaction != 'off')  $datas .= 'int:'.$interaction.';';
		if($pTilt != 30)  $datas .= 'ptlt:'.$pTilt.';';
		if($pRotate != 30)  $datas .= 'prt:'.$pRotate.';';
		if($pSpeed != 40)  $datas .= 'pspd:'.$pSpeed.';';
		if($pIntensity != 50)  $datas .= 'pint:'.$pIntensity.';';
		if($sbshifty != 20)  $datas .= 'sbsy:'.$sbshifty.';';
		
		if($ppfx != 'off') $datas .= 'd0:'.$ppfx.';';
		if($focus != 5) $datas .= 'd3f:'.$focus.';';
		if($aperture != 5) $datas .= 'd3a:'.$aperture.';';
		if($maxblur != 10) $datas .= 'd3m:'.$maxblur.';';
		if($ppbb != true) $datas .= 'ppbb:'.$ppbb.';';
		if($minblur != 0) $datas .= 'ppbm:'.$minblur.';';
		
		if($ppfn != 80) $datas .= 'ppfn:'.$ppfn.';';
		if($ppfsc != 82) $datas .= 'ppfsc:'.$ppfsc.';';
		if($ppfsz != 256) $datas .= 'ppfsz:'.$ppfsz.';';
		if($ppfgs != false) $datas .= 'ppfgs:'.$ppfgs.';';
		
		echo RS_T8 . " data-wpsdata='" .$datas."'\n";
	}
	
}
?>