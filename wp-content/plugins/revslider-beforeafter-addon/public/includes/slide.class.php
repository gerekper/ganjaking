<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsBeforeAfterSlideFront extends RevSliderFunctions {
	
	private $title;
	
	public function __construct($title) {
		
		$this->title = $title;
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 3);
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
	
	}
	
	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
	
		if(empty($val)) return true;
		if($val === true || $val === 'on' || $val === 1 || $val === '1' || $val === 'true') return false;
		return true;
	
	}
	
	private function isEnabled($slider) {
		
		$settings = $slider->get_params();
		if(empty($settings)) return false;
		
		$addOns = $this->get_val($settings, 'addOns', false);
		if(empty($addOns)) return false;
		
		$addOn = $this->get_val($addOns, 'revslider-' . $this->title . '-addon', false);
		if(empty($addOn)) return false;
		
		$enabled = $this->get_val($addOn, 'enable', false);
		if($this->isFalse($enabled)) return false;
		
		return $addOn;
	
	}
	
	public function write_layer_attributes($layer, $slide, $slider) {
		
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;
		
		$enabled = $this->isEnabled($slide);
		if(!empty($enabled)) {
			
			$addOn = $this->get_val($layer, 'addOns', array());
			$addOn = $this->get_val($addOn, 'revslider-' . $this->title . '-addon', array());
			$position = $this->get_val($addOn, 'position', 'before');

			echo '								data-beforeafter="'    . $position . '" ' . "\n";
			
		}
	
	}
	
	private function getMoves($moves) {
		
		$moves = json_decode(json_encode($moves), true);
		$st = '';
		$i = 0;
		foreach($moves as $move) {
			
			if($i > 0) $st .= '|';
			$val = isset($move['v']) ? $move['v'] : '50';
			$val = str_replace('%', '', $val);
			$st .= isset($move['v']) ? $val . '%' : '50%';
			$i++;
			
		}
		
		if(empty($st)) $st = '50%|50%|50%|50%';
		return $st;
		
	}
	
	public function write_slide_attributes($slider, $slide) {
		
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;
		
		$addOn = $this->isEnabled($slide);
		if(!empty($addOn)) {
			
			$moveto = $this->get_val($addOn, 'moveTo', array());
			$moveto = $this->getMoves($moveto);
			
			$bg = $this->get_val($addOn, 'bg', array());
			$bgColor = 'transparent';	
			
			if(class_exists('RSColorpicker')) {
			
				$bgColor = $this->get_val($bg, 'color', 'transparent');
				$bgColor = RSColorpicker::get($bgColor);
				
			}
			
			$imageUrl = '';
			$bgType = $this->get_val($bg, 'type', 'trans');
			$bgFit = $this->get_val($bg, 'fit', 'cover');
			$bgPos = $this->get_val($bg, 'position', 'center center');
			
			$filter = $slide->get_params();
			$filter = $this->get_val($filter, 'bg', array());
			$filter = $this->get_val($filter, 'mediaFilter', 'none');
			
			$bounce_ar = $this->get_val($addOn, 'teaser', array());
			$bounce = $this->get_val($bounce_ar, 'set', 'none');
			
			$shift_ar = $this->get_val($addOn, 'shift', array());
			$shift = $this->get_val($shift_ar, 'set', false);
			$shift = !$this->isFalse($shift);
			
			if($bgType === 'image') {
				
				$imageUrl = $this->get_val($bg, 'image', '');
				$imageId  = $this->get_val($bg, 'imageId',  '');
				
				if(!empty($imageId)) {
					
					$imageSize = $this->get_val($bg, 'imageSourceType', '');
					if(!empty($imageSize)) {
						
						$img = wp_get_attachment_image_src($imageId, $imageSize);
						if(!empty($img)) $imageUrl = $img[0];
						
					}
				
				}
				
			}
			else if($bgType === 'external') {
				
				$imageUrl = $this->get_val($bg, 'externalSrc', '');
				
			}
			
			$params = $slider->get_params();
			$settings = array(
				'bgColor'   => $bgColor,
				'bgType'    => $bgType,
				'bgImage'   => $imageUrl,
				'bgFit'     => $bgFit,
				'bgPos'     => $bgPos,
				'moveto'    => $moveto,
				'bgRepeat'  => $this->get_val($bg, 'repeat', 'no-repeat'),
				'direction' => $this->get_val($addOn, 'direction', 'horizontal'),
				'easing'    => $this->get_val($addOn, 'easing', 'Power2.easeInOut'),
				'delay'     => $this->get_val($addOn, 'delay', '500'),
				'time'      => $this->get_val($addOn, 'time', '750'),
				'out'       => $this->get_val($addOn, 'animateOut', 'fade'),
				'carousel'  => $this->get_val($params, 'type', 'standard') !== 'carousel' ? false : true
				
			);
			
			if($bgFit === 'percentage') {
				
				$x = $this->get_val($bg, 'fitX', '100');
				$y = $this->get_val($bg, 'fitY', '100');
				$settings['bgFit'] = $x . '% ' . $y . '%';
				
			}
			
			if($bgPos === 'percentage') {
				
				$x = $this->get_val($bg, 'positionX', '0');
				$y = $this->get_val($bg, 'positionX', '0');
				$settings['bgPos'] = $x . '% ' . $y . '%';
				
			}
			
			if($filter !== 'none') $settings['filter'] = $filter;
			if($bounce !== 'none') {
				
				$settings['bounceArrows'] = $bounce;
				$settings['bounceType'] = $this->get_val($bounce_ar, 'type',   'repel');
				$settings['bounceAmount'] = $this->get_val($bounce_ar, 'distance', '10');
				$settings['bounceSpeed'] = $this->get_val($bounce_ar, 'speed',  '1000');
				$settings['bounceEasing'] = $this->get_val($bounce_ar, 'easing', 'ease-in-out');
				$settings['bounceDelay'] = $this->get_val($bounce_ar, 'delay',  '0');
				
			}
			
			if(!empty($shift)) {
				
				$offset = $this->get_val($shift_ar, 'offset',  '10');
				if(intval($offset) > 0) {
				
					$settings['shiftOffset'] = $offset;
					$settings['shiftTiming'] = $this->get_val($shift_ar, 'speed', '300');
					$settings['shiftEasing'] = $this->get_val($shift_ar, 'easing', 'ease');
					$settings['shiftDelay']  = $this->get_val($shift_ar, 'delay',  '0');
					
				}
				
			}
			
			if(preg_match('/youtube|vimeo|html5/', $bgType)) {
				
				$video = $this->get_val($bg, 'video', array());
				
				//$muteVideo = $this->get_val($video, 'mute', true);
				//$muteVideo = !$this->isFalse($muteVideo);
				$settings['muteVideo'] = true;
				$settings['videoVolume'] = '0';
				
				$forceCover = $this->get_val($video, 'forceCover', true);
				$forceRewind = $this->get_val($video, 'forceRewind', true);
				$nextSlide = $this->get_val($video, 'nextSlideAtEnd', false);
				
				$settings['forceCover'] = $this->isFalse($forceCover) ? 'f' : 't';
				$settings['rewindOnStart'] = $this->isFalse($forceRewind) ? 'f' : 't';
				$settings['nextSlideOnEnd'] = $this->isFalse($nextSlide) ? 'f' : 't';
				
				$settings['dottedOverlay'] = $this->get_val($video, 'dottedOverlay', 'none');
				$settings['aspectRatio'] = $this->get_val($video, 'ratio', '16:9');
				$settings['videoStartAt'] = $this->get_val($video, 'startAt', '');
				$settings['videoEndAt'] = $this->get_val($video, 'endAt', '');
				$settings['loopVideo'] = $this->get_val($video, 'loop', 'none');
				
				$imageUrl = $this->get_val($bg, 'image', '');
				if(!empty($imageUrl)) $settings['poster'] = $imageUrl;
				
				switch($bgType) {
				
					case 'html5':
					
						$settings['videoMpeg'] = $this->get_val($bg, 'mpeg', '');
					
					break;
					
					case 'youtube':
						
						// $videoVolume = $this->get_val($video, 'volume', '');
						
						$settings['videoId'] = $this->get_val($bg, 'youtube', '');
						$settings['videoSpeed'] = $this->get_val($video, 'speed', '1');
						
						$args = $this->get_val($video, 'args', 'hd=1&wmode=opaque&showinfo=0&rel=0&');
						$baseArgs = 'version=3&enablejsapi=1&html5=1&';
						
						$origin = '';
						if(isset($_SERVER) && isset($_SERVER['SERVER_NAME'])) {
							$setBase = is_ssl() ? 'https://' : 'http://';
							$origin = ';origin=' . $setBase . $_SERVER['SERVER_NAME'] . ';';
						}
						
						$settings['youtubeArgs'] = $baseArgs . $args . $origin;
					
					break;
					
					case 'vimeo':
						
						$settings['videoId']     = $this->get_val($bg, 'vimeo', '');
						$settings['vimeoArgs']   = $this->get_val($video, 'argsVimeo', 'title=0&byline=0&portrait=0&api=1');
					
					break;
				
				}
				
			}
			
			echo " data-beforeafter='" . json_encode($settings) . "'";
			
		}
		
	}
	
}
?>