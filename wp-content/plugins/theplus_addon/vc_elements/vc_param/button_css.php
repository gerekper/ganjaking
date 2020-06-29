<?php
$btn_css ='';
	if(empty($tablet_font_size)){
		$tablet_font_size='';
	}else{
	    $tablet_font_size='font-size:'.esc_js($tablet_font_size).';';
	}
	if(empty($tablet_line_height)){
		$tablet_line_height='';
	}else{
	    $tablet_line_height='line-height:'.esc_js($tablet_line_height).';';
	}
	if(empty($tablet_letter_spacing)){
		$tablet_letter_spacing='';
	}else{
	  $tablet_letter_spacing='letter-spacing:'.esc_js($tablet_letter_spacing).';';
	}
	if(empty($tablet_btn_padding)){
		$tablet_btn_padding='';
	}else{
	    $tablet_btn_padding='padding:'.esc_js($tablet_btn_padding).';';
	}
	if(empty($mobile_font_size)){
		$mobile_font_size='';
	}else{
	    $mobile_font_size='font-size:'.esc_js($mobile_font_size).';';
	}
	if(empty($mobile_line_height)){
		$mobile_line_height='';
	}else{
	   $mobile_line_height='line-height:'.esc_js($mobile_line_height).';';
	}
	if(empty($mobile_letter_spacing)){
		$mobile_letter_spacing='';
	}else{
	    $mobile_letter_spacing='letter-spacing:'.esc_js($mobile_letter_spacing).';';
	}
	if(empty($mobile_btn_padding)){
		$mobile_btn_padding='';
	}else{
	    $mobile_btn_padding='padding:'.esc_js($mobile_btn_padding).';';
	}
	if($hover_shadow!=''){
				$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{-webkit-box-shadow: '.esc_js($hover_shadow).'; -moz-box-shadow:'.esc_js($hover_shadow).';box-shadow: 0px 15px 45px 0px '.esc_js($hover_shadow).';}';
		}
		if(!empty($btn_font_family) && isset($btn_font_family)){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap {'.esc_js($btn_font_family).'}';
		}
		
		if($style=='style-1' || $style=='style-2'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{color:'.esc_js($text_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button_line{background:'.esc_js($border_color).';}';;
		}
		if($style=='style-2'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{color:'.esc_js($text_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button_line{background:'.esc_js($border_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap i.button-after{background: '.esc_js($bg_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover i{background: '.esc_js($bg_hover_color).';}';
		}
		if($style=='style-3'){
		$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{color:'.esc_js($text_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button_line{background:'.esc_js($border_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap .arrow{stroke:'.esc_js($text_color).';fill:'.esc_js($text_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .arrow{stroke:'.esc_js($text_color).';fill:'.esc_js($text_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap .arrow path,.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap .arrow-1 path{stroke:'.esc_js($text_color).';fill:'.esc_js($text_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover .arrow path,.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover .arrow-1 path{stroke:'.esc_js($text_hover_color).';fill:'.esc_js($text_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' a.button-link-wrap:before{background:'.esc_js($text_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' a.button-link-wrap:hover:before{background:'.esc_js($text_color).';}';
		}
		if($style=='style-4'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';border-color:'.esc_js($border_color).';background: '.esc_js($bg_color).';-moz-border-radius:'.esc_js($border_radius).';-webkit-border-radius: '.esc_js($border_radius).';border-radius:'.esc_js($border_radius).';padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{color:'.esc_js($text_hover_color).';border-color:'.esc_js($border_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover,.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::after{background: '.esc_js($bg_hover_color).';}';
		}
		if($style=='style-5'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';border-color:'.esc_js($border_color).';background: '.esc_js($bg_color).';-moz-border-radius:'.esc_js($border_radius).';-webkit-border-radius: '.esc_js($border_radius).';border-radius:'.esc_js($border_radius).';padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{color:'.esc_js($text_hover_color).';border-color:'.esc_js($border_hover_color).';background: '.esc_js($bg_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:before,.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:after{background: '.esc_js($bg_hover_color).';}';
		}
		if($style=='style-6' || $style=='style-7' || $style=='style-9'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{color:'.esc_js($text_hover_color).';}.button-'.esc_js($rand_no).'.button-style-7 .button-link-wrap:after{border-color:'.esc_js($text_color).';}.button-'.esc_js($rand_no).'.button-style-7 .button-link-wrap .btn-arrow:after{border-color:'.esc_js($text_hover_color).';}';
		}
		if($style=='style-8'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';border-color:'.esc_js($border_color).';background: '.esc_js($bg_color).';padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{background: '.esc_js($bg_hover_color).';color:'.esc_js($text_hover_color).';border-color:'.esc_js($border_hover_color).';}';
		}
		if($style=='style-10'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';padding:'.esc_js($btn_padding).';background: '.esc_js($bg_color).';border-color:'.esc_js($border_color).';-moz-border-radius:'.esc_js($border_radius).';-webkit-border-radius: '.esc_js($border_radius).';border-radius:'.esc_js($border_radius).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{color:'.esc_js($text_hover_color).';border-color:'.esc_js($border_hover_color).';background: '.esc_js($bg_hover_color).';}';
		}
		if($style=='style-11'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{background: '.esc_js($bg_color).';font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';-moz-border-radius:'.esc_js($border_radius).';-webkit-border-radius: '.esc_js($border_radius).';border-radius:'.esc_js($border_radius).';border-color:'.esc_js($border_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::before{color:'.esc_js($text_hover_color).';background: '.esc_js($bg_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::before{border-radius:'.esc_js($border_radius).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap > span,.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::before{padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{border-color:'.esc_js($border_hover_color).';}';
			if($select_bg_option!='normal'){
				$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap,.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{border:0px;}';
			}
		}
		if($style=='style-12'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';padding:'.esc_js($btn_padding).';border-color:'.esc_js($border_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{color:'.esc_js($text_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::before{background: '.esc_js($bg_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{border-color:'.esc_js($border_hover_color).';}';
		}
		if($style=='style-13'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{color:'.esc_js($text_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::before,.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::after{background: '.esc_js($bg_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{border-color: '.esc_js($border_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{border-color: '.esc_js($border_hover_color).';}';
		}
		if($style=='style-14'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';border-color:'.esc_js($border_color).';-moz-border-radius:'.esc_js($border_radius).';-webkit-border-radius: '.esc_js($border_radius).';border-radius:'.esc_js($border_radius).';padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{background: '.esc_js($bg_hover_color).';border-color:'.esc_js($border_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:after{color:'.esc_js($text_hover_color).';}';
		}
		if($style=='style-15'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{color:'.esc_js($text_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::before, .button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::after{background:'.esc_js($bg_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover::after{background:'.esc_js($bg_hover_color).' !important;}';
		}
		if($style=='style-16'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';-moz-border-radius:'.esc_js($border_radius).';-webkit-border-radius: '.esc_js($border_radius).';border-radius:'.esc_js($border_radius).';padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{color:'.esc_js($text_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::before{border-color:'.esc_js($border_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::after{background:'.esc_js($bg_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::before{background:'.esc_js($bg_hover_color).';}';
		}
		if($style=='style-17'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';border-color:'.esc_js($border_color).';-moz-border-radius:'.esc_js($border_radius).';-webkit-border-radius: '.esc_js($border_radius).';border-radius:'.esc_js($border_radius).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover,.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap .btn-icon{color:'.esc_js($text_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::before{background:'.esc_js($bg_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap span{padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{border-color:'.esc_js($border_hover_color).';}';
		}
		if($style=='style-18'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';background:'.esc_js($border_color).';-moz-border-radius:'.esc_js($border_radius).';-webkit-border-radius: '.esc_js($border_radius).';border-radius:'.esc_js($border_radius).';padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{color:'.esc_js($text_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::before{background:'.esc_js($border_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover:before{background:'.esc_js($border_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap::after{background:'.esc_js($bg_color).';-moz-border-radius:'.esc_js($border_radius).';-webkit-border-radius: '.esc_js($border_radius).';border-radius:'.esc_js($border_radius).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover::after{background:'.esc_js($bg_hover_color).';}';
		}
		if($style=='style-19' || $style=='style-20' || $style=='style-21'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';-moz-border-radius:'.esc_js($border_radius).';-webkit-border-radius: '.esc_js($border_radius).';border-radius:'.esc_js($border_radius).';padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{color:'.esc_js($text_hover_color).';border-color:'.esc_js($border_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:after{background:'.esc_js($bg_hover_color).';}';
		}
		if($style=='style-19' || $style=='style-20'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{border-color:'.esc_js($border_color).';}';
		}
		if($style=='style-22'){
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap{font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';background:'.esc_js($bg_color).';border-color:'.esc_js($border_color).';-moz-border-radius:'.esc_js($border_radius).';-webkit-border-radius: '.esc_js($border_radius).';border-radius:'.esc_js($border_radius).';padding:'.esc_js($btn_padding).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap:hover{color:'.esc_js($text_hover_color).';border-color:'.esc_js($border_hover_color).';background:'.esc_js($bg_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap .btn-icon{color:'.esc_js($text_hover_color).';}';
		}
		if($style=='style-23'){
			$half_h= str_replace('px', '', $btn_height);
			$half_h= $half_h/2;
			$btn_css .='.button-'.esc_js($rand_no).'.button-'.$style.'{width:'.esc_js($btn_width).';height:'.esc_js($btn_height).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap span:nth-child(1){font-size:'.esc_js($font_size).';line-height:'.esc_js($line_height).';letter-spacing:'.esc_js($letter_spacing).';color:'.esc_js($text_color).';background:'.esc_js($bg_color).';border-color:'.esc_js($border_color).';}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap span:nth-child(2){color:'.esc_js($text_hover_color).';border-color:'.esc_js($border_hover_color).';background:'.esc_js($bg_hover_color).';}.button-'.esc_js($rand_no).'.button-'.$style.'.hover-top .button-link-wrap span:nth-child(1),.button-'.esc_js($rand_no).'.button-'.$style.'.hover-bottom .button-link-wrap span:nth-child(1){ -webkit-transform:translate3d(0, 0, '.esc_js($half_h).'px);-ms-transform:translate3d(0, 0, '.esc_js($half_h).'px);-moz-transform:translate3d(0, 0, '.esc_js($half_h).'px);-o-transform:translate3d(0, 0, '.esc_js($half_h).'px); transform: translate3d(0, 0, '.esc_js($half_h).'px);}.button-'.esc_js($rand_no).'.button-'.$style.'.hover-top .button-link-wrap span:nth-child(2){-webkit-transform:rotateX(90deg) translate3d(0, 0, '.esc_js($half_h).'px);-ms-transform:rotateX(90deg) translate3d(0, 0, '.esc_js($half_h).'px);	-moz-transform:rotateX(90deg) translate3d(0, 0, '.esc_js($half_h).'px);-o-transform:rotateX(90deg) translate3d(0, 0, '.esc_js($half_h).'px); transform: rotateX(90deg) translate3d(0, 0, '.esc_js($half_h).'px);}.button-'.esc_js($rand_no).'.button-'.$style.'.hover-bottom .button-link-wrap span:nth-child(2){-webkit-transform:rotateX(-90deg) translate3d(0, 0, '.esc_js($half_h).'px);-ms-transform:rotateX(-90deg) translate3d(0, 0, '.esc_js($half_h).'px);-moz-transform:rotateX(-90deg) translate3d(0, 0, '.esc_js($half_h).'px);-o-transform:rotateX(-90deg) translate3d(0, 0, '.esc_js($half_h).'px); transform: rotateX(-90deg) translate3d(0, 0, '.esc_js($half_h).'px);}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap span:nth-child(1), .button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap span:nth-child(2){padding:'.esc_js($btn_padding).';}@media (min-width:601px) and (max-width:991px){.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap span:nth-child(1){'.$tablet_font_size.''.$tablet_line_height.''.$tablet_letter_spacing.'}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap span:nth-child(1), .button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap span:nth-child(2){'.$tablet_btn_padding.'} }@media (max-width:600px){.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap span:nth-child(1){'.$mobile_font_size.''.$mobile_line_height.''.$mobile_letter_spacing.'}.button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap span:nth-child(1), .button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap span:nth-child(2){'.$mobile_btn_padding.'}}';
		}
		if($style=='style-1' || $style=='style-2' || $style=='style-4' || $style=='style-5' || $style=='style-6' || $style=='style-7' || $style=='style-8' || $style=='style-9' || $style=='style-10' || $style=='style-12' || $style=='style-13' || $style=='style-14' || $style=='style-15' || $style=='style-16' || $style=='style-18'|| $style=='style-19' || $style=='style-20' || $style=='style-21' || $style=='style-22'){
			$btn_css .='@media (min-width:601px) and (max-width:991px){ .button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap {'.$tablet_font_size.$tablet_line_height.$tablet_letter_spacing.$tablet_btn_padding.'} }@media (max-width:600px){ .button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap {'.$mobile_font_size.''.$mobile_line_height.''.$mobile_letter_spacing.''.$mobile_btn_padding.'} }';
		}
		if($style=='style-11' || $style=='style-17'){
			$btn_css .='@media (min-width:601px) and (max-width:991px){ .button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap {'.$tablet_font_size.''.$tablet_line_height.''.$tablet_letter_spacing.';}.button-'.esc_js($rand_no).'.button-style-11 .button-link-wrap > span,.button-'.esc_js($rand_no).'.button-style-11 .button-link-wrap::before{'.$tablet_btn_padding.'}.button-'.esc_js($rand_no).'.button-style-17 .button-link-wrap span{'.$tablet_btn_padding.'} }@media (max-width:600px){ .button-'.esc_js($rand_no).'.button-'.$style.' .button-link-wrap {'.$mobile_font_size.''.$mobile_line_height.''.$mobile_letter_spacing.'}.button-'.esc_js($rand_no).'.button-style-11 .button-link-wrap > span,.button-'.esc_js($rand_no).'.button-style-11 .button-link-wrap::before{'.$mobile_btn_padding.'}.button-'.esc_js($rand_no).'.button-style-17 .button-link-wrap span{'.$mobile_btn_padding.'} }';
		}
		return $btn_css;
?>