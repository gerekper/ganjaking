<?php
/**
 * Creative Button Markup trait
 */
namespace Happy_Addons\Elementor\Traits;

defined('ABSPATH') || exit;

/**
 * Trait to load markup for creative button
 */

trait Creative_Button_Markup {

    public function render_estilo_markup($settings) {
        $this->add_render_attribute( 'wrap', 'class', 'ha-creative-btn-wrap' );
        $this->add_render_attribute( 'button', 'class', [ 'ha-creative-btn', 'ha-stl--' . $settings['btn_style'], 'ha-eft--' .$settings['estilo_effect'] ] );

        $this->add_link_attributes( 'button', $settings['button_link'] );

		$wrap_attr = $this->get_render_attribute_string( 'wrap' );
		$btn_attr = $this->get_render_attribute_string( 'button' );
		$btn_txt = $settings['button_text'];
		// $magnatic_datajk = $this->$magnatic_data;

		$markup = <<<EOF
		<div $wrap_attr>
			<a $btn_attr>$btn_txt</a>
		</div>
EOF;
        echo $markup;
    }

    public function render_symbolab_markup($settings){
        $this->add_render_attribute( 'wrap', 'class', 'ha-creative-btn-wrap' );
        $this->add_render_attribute( 'button', 'class', [ 'ha-creative-btn', 'ha-stl--' . $settings['btn_style'], 'ha-eft--' .$settings['symbolab_effect'] ] );

        $this->add_link_attributes( 'button', $settings['button_link'] );

		$wrap_attr = $this->get_render_attribute_string( 'wrap' );
		$btn_attr = $this->get_render_attribute_string( 'button' );
		$btn_txt = $settings['button_text'];
		$icon = $settings['icon']['value'] ? $settings['icon']['value'] : 'hm hm-happyaddons';

		$markup = <<<EOF
		<div $wrap_attr>
			<a $btn_attr>$btn_txt<i aria-hidden="true" class="$icon"></i></a>
		</div>
EOF;
        echo $markup;
    }

    public function render_iconica_markup($settings){
		$this->add_render_attribute( 'wrap', 'class', 'ha-creative-btn-wrap' );
        $this->add_render_attribute( 'button', 'class', [ 'ha-creative-btn', 'ha-stl--' . $settings['btn_style'], 'ha-eft--' .$settings['iconica_effect'] ] );

        $this->add_link_attributes( 'button', $settings['button_link'] );

		$wrap_attr = $this->get_render_attribute_string( 'wrap' );
		$btn_attr = $this->get_render_attribute_string( 'button' );
		$btn_txt = $settings['button_text'];
		$icon = $settings['icon']['value'] ? $settings['icon']['value'] : 'hm hm-happyaddons';

		$markup = <<<EOF
		<div $wrap_attr>
			<a $btn_attr><span>$btn_txt</span><i aria-hidden="true" class="$icon"></i></a>
		</div>
EOF;
        echo $markup;
    }

    public function render_montino_markup($settings){
		$this->add_render_attribute( 'wrap', 'class', 'ha-creative-btn-wrap' );
        $this->add_render_attribute( 'button', 'class', [ 'ha-creative-btn', 'ha-stl--' . $settings['btn_style'], 'ha-eft--' .$settings['montino_effect'] ] );
        $this->add_link_attributes( 'button', $settings['button_link'] );

		if( 'winona' == $settings['montino_effect'] || 'rayen' == $settings['montino_effect'] || 'nina' == $settings['montino_effect'] ) {
			$this->add_render_attribute( 'button', 'data-text', $settings['button_text'] );
		}

		$wrap_attr = $this->get_render_attribute_string( 'wrap' );
		$btn_attr = $this->get_render_attribute_string( 'button' );
		$btn_txt = $settings['button_text'];

		if( 'winona' == $settings['montino_effect'] || 'rayen' == $settings['montino_effect'] || 'sacnite' == $settings['montino_effect'] ) {
			$btn_txt = '<span>'.esc_html($btn_txt).'</span>';
		}elseif('nina' == $settings['montino_effect']){
			$btn_txt = $this->split_word($btn_txt);
		}

		$markup = <<<EOF
		<div $wrap_attr>
			<a $btn_attr>$btn_txt</a>
		</div>
EOF;
        echo $markup;
    }

    public function render_hermosa_markup($settings){
		$this->add_render_attribute( 'wrap', 'class', 'ha-creative-btn-wrap' );
        $this->add_render_attribute( 'button', 'class', [ 'ha-creative-btn', 'ha-stl--' . $settings['btn_style'], 'ha-eft--' .$settings['hermosa_effect'] ] );
        $this->add_link_attributes( 'button', $settings['button_link'] );

		$wrap_attr = $this->get_render_attribute_string( 'wrap' );
		$btn_attr = $this->get_render_attribute_string( 'button' );
		$btn_txt = $settings['button_text'];

		if( 'upward' == $settings['hermosa_effect'] || 'render' == $settings['hermosa_effect'] || 'reshape' == $settings['hermosa_effect'] || 'exploit' == $settings['hermosa_effect'] ) {
			$btn_txt = '<span>'.esc_html($btn_txt).'</span>';
		} elseif ( 'newbie' == $settings['hermosa_effect'] || 'downhill' == $settings['hermosa_effect'] ) {
			$btn_txt = '<span><span>'.esc_html($btn_txt).'</span></span>';
		} elseif ( 'bloom' == $settings['hermosa_effect'] ) {
			$btn_txt = '<div></div><span>'.esc_html($btn_txt).'</span>';
		} elseif ( 'roundup' == $settings['hermosa_effect'] ) {
			$btn_txt = '<svg aria-hidden="true" class="progress" width="70" height="70" viewbox="0 0 70 70"> <path class="progress__circle" d="m35,2.5c17.955803,0 32.5,14.544199 32.5,32.5c0,17.955803 -14.544197,32.5 -32.5,32.5c-17.955803,0 -32.5,-14.544197 -32.5,-32.5c0,-17.955801 14.544197,-32.5 32.5,-32.5z" /> <path class="progress__path" d="m35,2.5c17.955803,0 32.5,14.544199 32.5,32.5c0,17.955803 -14.544197,32.5 -32.5,32.5c-17.955803,0 -32.5,-14.544197 -32.5,-32.5c0,-17.955801 14.544197,-32.5 32.5,-32.5z" pathLength=".9" /></svg><span>'.esc_html($btn_txt).'</span>';
		} elseif ( 'expandable' == $settings['hermosa_effect'] ) {
			$icon = $settings['icon']['value'] ? $settings['icon']['value'] : 'hm hm-happyaddons';
			$btn_txt = '<span class="text">'.esc_html($btn_txt).'</span><span class="icon"><i aria-hidden="true" class="'.esc_attr($icon).'"></i></span>';
		}

		$markup = <<<EOF
		<div $wrap_attr>
			<a $btn_attr>$btn_txt</a>
		</div>
EOF;
        echo $markup;
    }

    public function split_word( $text ){
		$text_array = str_split($text);
		$base = 0.045;
		$markup = '';
		foreach ( $text_array as $key => $value ) {
			$delay = $base * ($key+1);
			if(trim($value)){
				$markup .= '<span style="--delay:'.$delay.'s">'.$value.'</span>';
			}else{
				$markup .= '<span>&nbsp;</span>';
			}
		}
		return $markup;
    }
}
