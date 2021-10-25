<?php

namespace GT3\PhotoVideoGallery\Block\Traits;
defined('ABSPATH') OR exit;

trait Inline_Style_Trait {
	protected $style = array();
	protected $responsive_style = array();
	protected $style_print = true;

	public function camelToUnderscore($string, $us = "-"){
		$patterns = array(
			'/([a-z]+)([0-9]+)/i',
			'/([a-z]+)([A-Z]+)/',
			'/([0-9]+)([a-z]+)/i'
		);
		$string   = preg_replace($patterns, '$1'.$us.'$2', $string);

		// Lowercase
		$string = strtolower($string);

		return $string;
	}

	protected function get_styles($with_tags = true, $getResponsiveStyle = true){
		$style = '';
		if(is_array($this->style) && count($this->style)) {
			foreach($this->style as $selector => $_styles) {
				if(is_array($_styles) && count($_styles)) {
					$_style = '';
					foreach($_styles as $styleName => $value) {
						if(!empty($value) || (is_numeric($value))) {
							if(!is_array($value)) {
								$value = array( $value );
							}
							if(substr($styleName, -1, 1) !== ';') {
								$styleName .= ';';
							}
							$_style .= "\t".sprintf($this->camelToUnderscore($styleName), ...$value).PHP_EOL;
						}
					}
					if(!empty($_style)) {
						$style .= $selector.' {'.PHP_EOL.$_style.'}'.PHP_EOL;
					}
				}
			}
		}
		if($getResponsiveStyle) {
			$style .= $this->get_responsive_styles();
		}
		if(!empty($style) && $with_tags) {
			return '<style>'.$style.'</style>';
		}

		return $style;
	}

	protected function add_style($selector, $value = null){
		$oldStyle = array();
		if(is_array($selector) && count($selector)) {

			foreach($selector as $_selector => $_value) {
				if(is_numeric($_selector)) {
					$_selector = $_value;
					$_value    = $value;
				}
				if(isset($this->style[$this->WRAP.' '.$_selector])) {
					$oldStyle = $this->style[$this->WRAP.' '.$_selector];
				} else {
					$oldStyle = array();
				}
				$this->style[$this->WRAP.' '.$_selector] = array_merge($oldStyle, $_value);
			}
		} else {
			if(isset($this->style[$this->WRAP.' '.$selector])) {
				$oldStyle = $this->style[$this->WRAP.' '.$selector];
			} else {
				$oldStyle = array();
			}
			$this->style[$this->WRAP.' '.$selector] = array_merge($oldStyle, $value);
		}
	}

	protected function get_responsive_styles(){
		$style            = '';
		$responsive_style = '';
		if(is_array($this->responsive_style) && count($this->responsive_style)) {
			krsort($this->responsive_style);
			foreach($this->responsive_style as $maxWidth => $_styles) {
				if(is_array($_styles) && count($_styles)) {
					$this->style      = $_styles;
					$responsive_style = $this->get_styles(false, false);
					if(!empty($responsive_style)) {
						$style .= '@media screen and (max-width: '.$maxWidth.'px) {'."\t".PHP_EOL.$responsive_style."\t".PHP_EOL.'}'.PHP_EOL;
					}
				}
			}
		}

		return $style;
	}

	protected function add_responsive_style($maxWidth, $selector, $value = null){
		$oldStyle = array();
		if(is_array($selector) && count($selector)) {
			foreach($selector as $_selector => $value) {
				if(isset($this->responsive_style[$maxWidth]) && isset($this->responsive_style[$maxWidth][$this->WRAP.' '.$_selector])) {
					$oldStyle = $this->responsive_style[$maxWidth][$this->WRAP.' '.$_selector];
				} else {
					$oldStyle = array();
				}
				$this->responsive_style[$maxWidth][$this->WRAP.' '.$_selector] = array_merge($oldStyle, $value);
			}
		} else {
			if(isset($this->responsive_style[$maxWidth]) && isset($this->responsive_style[$maxWidth][$this->WRAP.' '.$selector])) {
				$oldStyle = $this->responsive_style[$maxWidth][$this->WRAP.' '.$selector];
			} else {
				$oldStyle = array();
			}
			$this->responsive_style[$maxWidth][$this->WRAP.' '.$selector] = array_merge($oldStyle, $value);
		}
	}

	protected function add_responsive_block($selector, $style, $block){
		if(gettype($selector) == 'string') {
			$selector = array( $selector );
		}
		if(is_array($block) && key_exists('default', $block)) {
			if(is_array($selector) && count($selector)) {
				foreach($selector as $_selector) {
					// Default
					if(is_array($style) && count($style)) {
						foreach($style as $_style) {
							$this->add_style($_selector, array( $_style => $block['default'] ));
						}
					} else {
						$this->add_style($_selector, array( $style => $block['default'] ));
					}

					// Responsive
					if(key_exists('responsive', $block)
					   && $block['responsive']
					   && key_exists('data', $block)
					   && is_array($block['data'])
					   && count($block['data'])) {
						foreach($block['data'] as $name => $data) {
							if(is_array($style) && count($style)) {
								foreach($style as $_style) {
									$this->add_responsive_style($data['width'], $_selector, array( $_style => $data['value'] ));
								}
							} else {
								$this->add_responsive_style($data['width'], $_selector, array( $style => $data['value'] ));
							}
						}
					}
				}
			}
		}
	}

}
