<?php
/* Magic scroll */
$magic_class = $magic_attr = $parallax_scroll = $gsap_attr = $gsapClass = '';
if ( ! empty( $settings['magic_scroll'] ) && $settings['magic_scroll'] == 'yes' ) {
	if ( empty( $settings['scroll_option_popover_toggle'] ) ) {
		$scroll_offset   = 0;
		$scroll_duration = 300;
	} else {
		$scroll_offset   = isset( $settings['scroll_option_scroll_offset'] ) ? $settings['scroll_option_scroll_offset'] : 0;
		$scroll_duration = isset( $settings['scroll_option_scroll_duration'] ) ? $settings['scroll_option_scroll_duration'] : 300;
	}

	if ( empty( $settings['scroll_from_popover_toggle'] ) ) {
		$scroll_x_from       = 0;
		$scroll_y_from       = 0;
		$scroll_opacity_from = 1;
		$scroll_scale_from   = 1;
		$scroll_rotate_from  = 0;
	} else {
		$scroll_x_from       = isset( $settings['scroll_from_scroll_x_from'] ) ? $settings['scroll_from_scroll_x_from'] : 0;
		$scroll_y_from       = isset( $settings['scroll_from_scroll_y_from'] ) ? $settings['scroll_from_scroll_y_from'] : 0;
		$scroll_opacity_from = isset( $settings['scroll_from_scroll_opacity_from'] ) ? $settings['scroll_from_scroll_opacity_from'] : 1;
		$scroll_scale_from   = isset( $settings['scroll_from_scroll_scale_from'] ) ? $settings['scroll_from_scroll_scale_from'] : 1;
		$scroll_rotate_from  = isset( $settings['scroll_from_scroll_rotate_from'] ) ? $settings['scroll_from_scroll_rotate_from'] : 0;
	}

	if ( empty( $settings['scroll_to_popover_toggle'] ) ) {
		$scroll_x_to       = 0;
		$scroll_y_to       = -50;
		$scroll_opacity_to = 1;
		$scroll_scale_to   = 1;
		$scroll_rotate_to  = 0;
	} else {
		$scroll_x_to       = isset( $settings['scroll_to_scroll_x_to'] ) ? $settings['scroll_to_scroll_x_to'] : 0;
		$scroll_y_to       = isset( $settings['scroll_to_scroll_y_to'] ) ? $settings['scroll_to_scroll_y_to'] : -50;
		$scroll_opacity_to = isset( $settings['scroll_to_scroll_opacity_to'] ) ? $settings['scroll_to_scroll_opacity_to'] : 1;
		$scroll_scale_to   = isset( $settings['scroll_to_scroll_scale_to'] ) ? $settings['scroll_to_scroll_scale_to'] : 1;
		$scroll_rotate_to  = isset( $settings['scroll_to_scroll_rotate_to'] ) ? $settings['scroll_to_scroll_rotate_to'] : 0;
	}

	$magic_attr      .= ' data-scroll_type="position" ';
	$magic_attr      .= ' data-scroll_offset="' . esc_attr( $scroll_offset ) . '" ';
	$magic_attr      .= ' data-scroll_duration="' . esc_attr( $scroll_duration ) . '" ';
	$magic_attr      .= ' data-scroll_x_from="' . esc_attr( $scroll_x_from ) . '" ';
	$magic_attr      .= ' data-scroll_x_to="' . esc_attr( $scroll_x_to ) . '" ';
	$magic_attr      .= ' data-scroll_y_from="' . esc_attr( $scroll_y_from ) . '" ';
	$magic_attr      .= ' data-scroll_y_to="' . esc_attr( $scroll_y_to ) . '" ';
	$magic_attr      .= ' data-scroll_opacity_from="' . esc_attr( $scroll_opacity_from ) . '" ';
	$magic_attr      .= ' data-scroll_opacity_to="' . esc_attr( $scroll_opacity_to ) . '" ';
	$magic_attr      .= ' data-scroll_scale_from="' . esc_attr( $scroll_scale_from ) . '" ';
	$magic_attr      .= ' data-scroll_scale_to="' . esc_attr( $scroll_scale_to ) . '" ';
	$magic_attr      .= ' data-scroll_rotate_from="' . esc_attr( $scroll_rotate_from ) . '" ';
	$magic_attr      .= ' data-scroll_rotate_to="' . esc_attr( $scroll_rotate_to ) . '" ';
	$parallax_scroll .= ' parallax-scroll ';
	$magic_class     .= ' magic-scroll ';
}

/**GSAP Scroll */
$gsapScroll = ! empty( $settings['gsapScroll'] ) ? $settings['gsapScroll'] : '';
if ( ! empty( $gsapScroll ) ) {
	$GSAPFrame   = ! empty( $settings['GSAPFrame'] ) ? $settings['GSAPFrame'] : array();
	$hscrollAttr = array();
	$HSwidth     = ! empty( $settings['HSwidth'] ) ? 1 : 0;
	foreach ( $GSAPFrame as $key => $val ) {
		$gsap_loop   = array();
		$vertical    = ! empty( $val['vertical'] ) ? 1 : 0;
		$horizontal  = ! empty( $val['horizontal'] ) ? 1 : 0;
		$opacity     = ! empty( $val['opacity'] ) ? 1 : 0;
		$rotate      = ! empty( $val['rotate'] ) ? 1 : 0;
		$scale       = ! empty( $val['scalehs'] ) ? 1 : 0;
		$skew        = ! empty( $val['skewhs'] ) ? 1 : 0;
		$border      = ! empty( $val['borderHs'] ) ? 1 : 0;
		$background  = ! empty( $val['bgColorhs'] ) ? 1 : 0;
		$scrollOpt   = ! empty( $val['hsscrollOpttp'] ) ? 1 : 0;
		$hsdeveloptp = ! empty( $val['hsdeveloptp'] ) ? 1 : 0;

		if ( ! empty( $scrollOpt ) ) {
			$scrollOpt = array();

			$loopTrig = array();
			if ( isset( $loopTrig ) ) {
				/**Desktop */
				$trigSTart           = isset( $val['triggerStarth']['size'] ) ? $val['triggerStarth']['size'] : 0.5;
				$trigEnd             = isset( $val['triggerEndh']['size'] ) ? $val['triggerEndh']['size'] : 0.4;
				$loopTrig['desktop'] = array( $trigSTart, $trigEnd );

				/**Tablet */
				$trigSTartTablet    = isset( $val['triggerStarth_tablet']['size'] ) ? $val['triggerStarth_tablet']['size'] : $trigSTart;
				$trigEndTablet      = isset( $val['triggerEndh_tablet']['size'] ) ? $val['triggerEndh_tablet']['size'] : $trigEnd;
				$loopTrig['tablet'] = array( $trigSTartTablet, $trigEndTablet );

				/**Mobile */
				$trigSTartMobile    = isset( $val['triggerStarth_mobile']['size'] ) ? $val['triggerStarth_mobile']['size'] : $trigSTartTablet;
				$trigEndMobile      = isset( $val['triggerEndh_mobile']['size'] ) ? $val['triggerEndh_mobile']['size'] : $trigEndTablet;
				$loopTrig['mobile'] = array( $trigSTartMobile, $trigEndMobile );

				$gsap_loop['animation']['trigger'] = $loopTrig;

			}

			$loopScroll = array();
			if ( isset( $loopScroll ) ) {
				/**Desktop */
				$scrollStart           = isset( $val['scrollStarth']['size'] ) ? $val['scrollStarth']['size'] : 0.8;
				$scrollEnd             = isset( $val['scrollEndh']['size'] ) ? $val['scrollEndh']['size'] : 0.2;
				$loopScroll['desktop'] = array( $scrollStart, $scrollEnd );

				/**Tablet */
				$scrollStartTablet    = isset( $val['scrollStarth_tablet']['size'] ) ? $val['scrollStarth_tablet']['size'] : $scrollStart;
				$scrollEndTablet      = isset( $val['scrollEndh_tablet']['size'] ) ? $val['scrollEndh_tablet']['size'] : $scrollEnd;
				$loopScroll['tablet'] = array( $scrollStartTablet, $scrollEndTablet );

				/**Mobile */
				$scrollStartMobile    = isset( $val['scrollStarth_mobile']['size'] ) ? $val['scrollStarth_mobile']['size'] : $scrollStartTablet;
				$scrollEndMobile      = isset( $val['scrollEndh_mobile']['size'] ) ? $val['scrollEndh_mobile']['size'] : $scrollEndTablet;
				$loopScroll['mobile'] = array( $scrollStartMobile, $scrollEndMobile );

				$gsap_loop['animation']['scroll'] = $loopScroll;
			}
		}

		if ( ! empty( $vertical ) ) {
			$loopVertical = array();

			/**Desktop */
			$verticalStart           = isset( $val['verticalStart']['size'] ) ? $val['verticalStart']['size'] : 0;
			$verticalEnd             = isset( $val['verticalEnd']['size'] ) ? $val['verticalEnd']['size'] : 50;
			$loopVertical['desktop'] = array( $verticalStart, $verticalEnd );

			/**Tablet */
			$verticalStartTab       = isset( $val['verticalStart_tablet']['size'] ) ? $val['verticalStart_tablet']['size'] : $verticalStart;
			$verticalEndTab         = isset( $val['verticalEnd_tablet']['size'] ) ? $val['verticalEnd_tablet']['size'] : $verticalStart;
			$loopVertical['tablet'] = array( $verticalStartTab, $verticalEndTab );

			/**Mobile */
			$verticalStartMob       = isset( $val['verticalStart_mobile']['size'] ) ? $val['verticalStart_mobile']['size'] : $verticalStartTab;
			$verticalEndMob         = isset( $val['verticalEnd_mobile']['size'] ) ? $val['verticalEnd_mobile']['size'] : $verticalStartTab;
			$loopVertical['mobile'] = array( $verticalStartMob, $verticalEndMob );

			$loopVertical['verticalY'] = $vertical;
			$gsap_loop['vertical']     = $loopVertical;
		}

		if ( ! empty( $horizontal ) ) {
			$loopHorizontal = array();

			/**Desktop */
			$horiStart                 = isset( $val['horiStart']['size'] ) ? $val['horiStart']['size'] : 10;
			$horiEnd                   = isset( $val['horiEnd']['size'] ) ? $val['horiEnd']['size'] : 50;
			$loopHorizontal['desktop'] = array( $horiStart, $horiEnd );

			/**Tablet */
			$horiStartTab             = isset( $val['horiStart_tablet']['size'] ) ? $val['horiStart_tablet']['size'] : $horiStart;
			$horiEndTab               = isset( $val['horiEnd_tablet']['size'] ) ? $val['horiEnd_tablet']['size'] : $horiEnd;
			$loopHorizontal['tablet'] = array( $horiStartTab, $horiEndTab );

			/**Mobile */
			$horiStartMob             = isset( $val['horiStart_mobile']['size'] ) ? $val['horiStart_mobile']['size'] : $horiStartTab;
			$horiEndMob               = isset( $val['horiEnd_mobile']['size'] ) ? $val['horiEnd_mobile']['size'] : $horiEndTab;
			$loopHorizontal['mobile'] = array( $horiStartMob, $horiEndMob );

			$loopHorizontal['horizontalX'] = $horizontal;
			$gsap_loop['horizontal']       = $loopHorizontal;
		}

		if ( ! empty( $opacity ) ) {
			$loopOpacity = array();

			/**Dekstop */
			$opacityStart           = isset( $val['opacityStart']['size'] ) ? $val['opacityStart']['size'] : 1;
			$opacityEnd             = isset( $val['opacityEnd']['size'] ) ? $val['opacityEnd']['size'] : 1;
			$loopOpacity['desktop'] = array( $opacityStart, $opacityEnd );

			/**Tablet */
			$opacityStartTab       = isset( $val['opacityStart_tablet']['size'] ) ? $val['opacityStart_tablet']['size'] : $opacityStart;
			$opacityEndTab         = isset( $val['opacityEnd_tablet']['size'] ) ? $val['opacityEnd_tablet']['size'] : $opacityEnd;
			$loopOpacity['tablet'] = array( $opacityStartTab, $opacityEndTab );

			/**Mobile */
			$opacityStartMob       = isset( $val['opacityStart_mobile']['size'] ) ? $val['opacityStart_mobile']['size'] : $opacityStartTab;
			$opacityEndMob         = isset( $val['opacityEnd_mobile']['size'] ) ? $val['opacityEnd_mobile']['size'] : $opacityEndTab;
			$loopOpacity['mobile'] = array( $opacityStartMob, $opacityEndMob );

			$loopOpacity['opacity'] = $opacity;
			$gsap_loop['opacity']   = $loopOpacity;
		}

		if ( ! empty( $rotate ) ) {

			$rotateLoopx = array();
			if ( isset( $rotateLoopx ) ) {
				/**Desktop */
				$rotateXStart           = isset( $val['rotateXstart']['size'] ) ? $val['rotateXstart']['size'] : 0;
				$rotateXEnd             = isset( $val['rotateXEnd']['size'] ) ? $val['rotateXEnd']['size'] : 0;
				$rotateLoopx['desktop'] = array( $rotateXStart, $rotateXEnd );

				/**Tablet */
				$rotateXStartTablet    = isset( $val['rotateXstart_tablet']['size'] ) ? $val['rotateXstart_tablet']['size'] : $rotateXStart;
				$rotateXEndTablet      = isset( $val['rotateXEnd_tablet']['size'] ) ? $val['rotateXEnd_tablet']['size'] : $rotateXEnd;
				$rotateLoopx['tablet'] = array( $rotateXStartTablet, $rotateXEndTablet );

				/**Mobile */
				$rotateXStartMobile    = isset( $val['rotateXstart_mobile']['size'] ) ? $val['rotateXstart_mobile']['size'] : $rotateXStartTablet;
				$rotateXEndMobile      = isset( $val['rotateXEnd_mobile']['size'] ) ? $val['rotateXEnd_mobile']['size'] : $rotateXEndTablet;
				$rotateLoopx['mobile'] = array( $rotateXStartMobile, $rotateXEndMobile );

				$gsap_loop['rotate']['rotateX'] = $rotateLoopx;
			}

			$rotateLoopy = array();
			if ( isset( $rotateLoopy ) ) {
				/**Desktop */
				$rotateYStart           = isset( $val['rotateYStart']['size'] ) ? $val['rotateYStart']['size'] : 0;
				$rotateYEnd             = isset( $val['rotateYEnd']['size'] ) ? $val['rotateYEnd']['size'] : 0;
				$rotateLoopy['desktop'] = array( $rotateYStart, $rotateYEnd );

				/**Tablet */
				$rotateYStartTablet    = isset( $val['rotateYStart_tablet']['size'] ) ? $val['rotateYStart_tablet']['size'] : $rotateYStart;
				$rotateYEndTablet      = isset( $val['rotateYEnd_tablet']['size'] ) ? $val['rotateYEnd_tablet']['size'] : $rotateYEnd;
				$rotateLoopy['tablet'] = array( $rotateYStartTablet, $rotateYEndTablet );

				/**Mobile */
				$rotateYStartMobile    = isset( $val['rotateYStart_mobile']['size'] ) ? $val['rotateYStart_mobile']['size'] : $rotateYStartTablet;
				$rotateYEndMobile      = isset( $val['rotateYEnd_mobile']['size'] ) ? $val['rotateYEnd_mobile']['size'] : $rotateYEndTablet;
				$rotateLoopy['mobile'] = array( $rotateYStartMobile, $rotateYEndMobile );

				$gsap_loop['rotate']['rotateY'] = $rotateLoopy;
			}

			$rotateLoopz = array();
			if ( isset( $rotateLoopz ) ) {
				/**Desktop */
				$rotateZStart           = isset( $val['rotateZStart']['size'] ) ? $val['rotateZStart']['size'] : 0;
				$rotateZEnd             = isset( $val['rotateZEnd']['size'] ) ? $val['rotateZEnd']['size'] : 0;
				$rotateLoopz['desktop'] = array( $rotateZStart, $rotateZEnd );

				/**Tablet */
				$rotateZStartTablet    = isset( $val['rotateZStart_tablet']['size'] ) ? $val['rotateZStart_tablet']['size'] : $rotateZStart;
				$rotateZEndTablet      = isset( $val['rotateZEnd_tablet']['size'] ) ? $val['rotateZEnd_tablet']['size'] : $rotateZEnd;
				$rotateLoopz['tablet'] = array( $rotateZStartTablet, $rotateZEndTablet );

				/**Mobile */
				$rotateZStartMobile    = isset( $val['rotateZStart_mobile']['size'] ) ? $val['rotateZStart_mobile']['size'] : $rotateZStartTablet;
				$rotateZEndMobile      = isset( $val['rotateZEnd_mobile']['size'] ) ? $val['rotateZEnd_mobile']['size'] : $rotateZEndTablet;
				$rotateLoopz['mobile'] = array( $rotateZStartMobile, $rotateZEndMobile );

				$gsap_loop['rotate']['rotateZ'] = $rotateLoopz;
			}
			$gsap_loop['rotate']['rotate']   = $rotate;
			$rotatePosi                      = isset( $val['positiontpsr'] ) ? $val['positiontpsr'] : 'center center';
			$gsap_loop['rotate']['position'] = $rotatePosi;

		}

		if ( ! empty( $scale ) ) {

			$scaleLoopX = array();
			if ( isset( $scaleLoopX ) ) {
				/**Desktop */
				$scaleXStart           = isset( $val['scaleXhsss']['size'] ) ? $val['scaleXhsss']['size'] : 1;
				$scaleXEnd             = isset( $val['scaleXhsse']['size'] ) ? $val['scaleXhsse']['size'] : 1;
				$scaleLoopX['desktop'] = array( $scaleXStart, $scaleXEnd );

				/**Tablet */
				$scaleXStartTablet    = isset( $val['scaleXhsss_tablet']['size'] ) ? $val['scaleXhsss_tablet']['size'] : $scaleXStart;
				$scaleXEndTablet      = isset( $val['scaleXhsse_tablet']['size'] ) ? $val['scaleXhsse_tablet']['size'] : $scaleXEnd;
				$scaleLoopX['tablet'] = array( $scaleXStartTablet, $scaleXEndTablet );

				/**Mobile */
				$scaleXStartMobile    = isset( $val['scaleXhsss_mobile']['size'] ) ? $val['scaleXhsss_mobile']['size'] : $scaleXStartTablet;
				$scaleXEndMobile      = isset( $val['scaleXhsse_mobile']['size'] ) ? $val['scaleXhsse_mobile']['size'] : $scaleXEndTablet;
				$scaleLoopX['mobile'] = array( $scaleXStartMobile, $scaleXEndMobile );

				$gsap_loop['scale']['scaleX'] = $scaleLoopX;
			}

			$scaleLoopY = array();
			if ( isset( $scaleLoopY ) ) {
				/**Desktop */
				$scaleYStart           = isset( $val['scaleYhsss']['size'] ) ? $val['scaleYhsss']['size'] : 1;
				$scaleYEnd             = isset( $val['scaleYhsse']['size'] ) ? $val['scaleYhsse']['size'] : 1;
				$scaleLoopY['desktop'] = array( $scaleYStart, $scaleYEnd );

				/**Tablet */
				$scaleYStartTablet    = isset( $val['scaleYhsss_tablet']['size'] ) ? $val['scaleYhsss_tablet']['size'] : $scaleYStart;
				$scaleYEndTablet      = isset( $val['scaleYhsse_tablet']['size'] ) ? $val['scaleYhsse_tablet']['size'] : $scaleYEnd;
				$scaleLoopY['tablet'] = array( $scaleYStartTablet, $scaleYEndTablet );

				/**Mobile */
				$scaleYStartMobile    = isset( $val['scaleYhsss_mobile']['size'] ) ? $val['scaleYhsss_mobile']['size'] : $scaleYStartTablet;
				$scaleYEndMobile      = isset( $val['scaleYhsse_mobile']['size'] ) ? $val['scaleYhsse_mobile']['size'] : $scaleYEndTablet;
				$scaleLoopY['mobile'] = array( $scaleYStartMobile, $scaleYEndMobile );

				$gsap_loop['scale']['scaleY'] = $scaleLoopY;
			}

			$scaleLoopZ = array();
			if ( isset( $scaleLoopZ ) ) {
				/**Desktop */
				$scaleZStart           = isset( $val['scaleZhss']['size'] ) ? $val['scaleZhss']['size'] : 1;
				$scaleZEnd             = isset( $val['scaleZhse']['size'] ) ? $val['scaleZhse']['size'] : 1;
				$scaleLoopZ['desktop'] = array( $scaleZStart, $scaleZEnd );

				/**Tablet */
				$scaleZStartTablet    = isset( $val['scaleZhss_tablet']['size'] ) ? $val['scaleZhss_tablet']['size'] : $scaleZStart;
				$scaleZEndTablet      = isset( $val['scaleZhse_tablet']['size'] ) ? $val['scaleZhse_tablet']['size'] : $scaleZEnd;
				$scaleLoopZ['tablet'] = array( $scaleZStartTablet, $scaleZEndTablet );

				/**Mobile */
				$scaleZStartMobile    = isset( $val['scaleZhss_mobile']['size'] ) ? $val['scaleZhss_mobile']['size'] : $scaleZStartTablet;
				$scaleZEndMobile      = isset( $val['scaleZhse_mobile']['size'] ) ? $val['scaleZhse_mobile']['size'] : $scaleZEndTablet;
				$scaleLoopZ['mobile'] = array( $scaleZStartMobile, $scaleZEndMobile );

				$gsap_loop['scale']['scaleZ'] = $scaleLoopZ;
			}
			$gsap_loop['scale']['scale'] = $scale;
		}

		if ( ! empty( $skew ) ) {

			$skewLoopX = array();
			if ( isset( $skewLoopX ) ) {
				/**Desktop */
				$skewXStart           = isset( $val['skewXhsss']['size'] ) ? $val['skewXhsss']['size'] : 0;
				$skewXEnd             = isset( $val['skewXhsse']['size'] ) ? $val['skewXhsse']['size'] : 0;
				$skewLoopX['desktop'] = array( $skewXStart, $skewXEnd );

				/**Tablet */
				$skewXStartTablet    = isset( $val['skewXhsss_tablet']['size'] ) ? $val['skewXhsss_tablet']['size'] : $skewXStart;
				$skewXEndTablet      = isset( $val['skewXhsse_tablet']['size'] ) ? $val['skewXhsse_tablet']['size'] : $skewXEnd;
				$skewLoopX['tablet'] = array( $skewXStartTablet, $skewXEndTablet );

				/**Mobile */
				$skewXStartMobile    = isset( $val['skewXhsss_mobile']['size'] ) ? $val['skewXhsss_mobile']['size'] : $skewXStartTablet;
				$skewXEndMobile      = isset( $val['skewXhsse_mobile']['size'] ) ? $val['skewXhsse_mobile']['size'] : $skewXEndTablet;
				$skewLoopX['mobile'] = array( $skewXStartMobile, $skewXEndMobile );

				$gsap_loop['skew']['skewX'] = $skewLoopX;
			}

			$skewLoopY = array();
			if ( isset( $skewLoopX ) ) {
				/**Desktop */
				$skewYStart           = isset( $val['skewYhsss']['size'] ) ? $val['skewYhsss']['size'] : 0;
				$skewYEnd             = isset( $val['skewYhsse']['size'] ) ? $val['skewYhsse']['size'] : 0;
				$skewLoopY['desktop'] = array( $skewYStart, $skewYEnd );

				/**Tablet */
				$skewYStartTablet    = isset( $val['skewYhsss_tablet']['size'] ) ? $val['skewYhsss_tablet']['size'] : $skewYStart;
				$skewYEndTablet      = isset( $val['skewYhsse_tablet']['size'] ) ? $val['skewYhsse_tablet']['size'] : $skewYEnd;
				$skewLoopY['tablet'] = array( $skewYStartTablet, $skewYEndTablet );

				/**Mobile */
				$skewYStartMobile           = isset( $val['skewYhsss_mobile']['size'] ) ? $val['skewYhsss_mobile']['size'] : $skewYStartTablet;
				$skewYEndMobile             = isset( $val['skewYhsse_mobile']['size'] ) ? $val['skewYhsse_mobile']['size'] : $skewYEndTablet;
				$skewLoopY['mobile']        = array( $skewYStartMobile, $skewYEndMobile );
				$gsap_loop['skew']['skewY'] = $skewLoopY;
			}

			$gsap_loop['skew']['skew'] = $skew;

		}

		if ( ! empty( $border ) ) {
			$loopBorder = array();

			/**Desktop */
			$borderStart           = isset( $val['fromBRhs'] ) ? $val['fromBRhs'] : '';
			$borderEnd             = isset( $val['toBRhs'] ) ? $val['toBRhs'] : '';
			$loopBorder['desktop'] = array( $borderStart, $borderEnd );

			/**Tablet */
			$borderStartTablet    = isset( $val['fromBRhs_tablet'] ) ? $val['fromBRhs_tablet'] : $borderStart;
			$borderEndTablet      = isset( $val['toBRhs_tablet'] ) ? $val['toBRhs_tablet'] : $borderEnd;
			$loopBorder['tablet'] = array( $borderStartTablet, $borderEndTablet );

			/**Mobile */
			$borderStartMobile    = isset( $val['fromBRhs_mobile'] ) ? $val['fromBRhs_mobile'] : $borderStartTablet;
			$borderEndMobile      = isset( $val['toBRhs_mobile'] ) ? $val['toBRhs_mobile'] : $borderEndTablet;
			$loopBorder['mobile'] = array( $borderStartMobile, $borderEndMobile );

			$loopBorder['border'] = $border;
			$gsap_loop['border']  = $loopBorder;
		}

		if ( ! empty( $background ) ) {
			$loopBgColor = array();

			/**Desktop */
			$bgStart                = isset( $val['fromColorhs'] ) ? $val['fromColorhs'] : '';
			$bgEnd                  = isset( $val['toColorhs'] ) ? $val['toColorhs'] : '';
			$loopBgColor['desktop'] = array( $bgStart, $bgEnd );

			/**Tablet */
			$bgStartTablet         = isset( $val['fromColorhs_tablet'] ) ? $val['fromColorhs_tablet'] : $bgStart;
			$bgEndTablet           = isset( $val['toColorhs_tablet'] ) ? $val['toColorhs_tablet'] : $bgEnd;
			$loopBgColor['tablet'] = array( $bgStartTablet, $bgEndTablet );

			/**Mobile */
			$bgStartMobile         = isset( $val['fromColorhs_mobile'] ) ? $val['fromColorhs_mobile'] : $bgStartTablet;
			$bgEndMobile           = isset( $val['toColorhs_mobile'] ) ? $val['toColorhs_mobile'] : $bgEndTablet;
			$loopBgColor['mobile'] = array( $bgStartMobile, $bgEndMobile );

			$loopBgColor['background'] = $background;
			$gsap_loop['bgColor']      = $loopBgColor;
		}

		if ( ! empty( $hsdeveloptp ) ) {
			$devLoop = array();

			$hsdevNametp = ! empty( $val['hsdevNametp'] ) ? $val['hsdevNametp'] : '';

			$gsap_loop['developer'] = array( $hsdeveloptp, $hsdevNametp );
		}

		if ( ! empty( $gsap_loop ) ) {
			$hscrollAttr[] = $gsap_loop;
		}
	}

	if ( ! empty( $HSwidth ) ) {
		$devices  = array();
		$resWidth = ! empty( $settings['resWidth'] ) ? $settings['resWidth'] : 768;

		$devices['resWidth'] = $resWidth;
		$devices['HSwidth']  = $HSwidth;
		$gsap_attr          .= ' data-tpae-msview="' . htmlspecialchars( json_encode( $devices ), ENT_QUOTES, 'UTF-8' ) . '"';
	}

	if ( $hscrollAttr ) {
		$gsap_attr .= 'data-tpae-hs="' . htmlspecialchars( json_encode( $hscrollAttr ), ENT_QUOTES, 'UTF-8' ) . '"';
	}
}

/* Tooltip */
if ( ! empty( $settings['plus_tooltip'] ) && $settings['plus_tooltip'] == 'yes' ) {
	$this->add_render_attribute( '_tooltip', 'data-tippy', '', true );

	if ( ! empty( $settings['plus_tooltip_content_type'] ) && $settings['plus_tooltip_content_type'] == 'normal_desc' ) {
		$this->add_render_attribute( '_tooltip', 'title', $settings['plus_tooltip_content_desc'], true );
	} elseif ( ! empty( $settings['plus_tooltip_content_type'] ) && $settings['plus_tooltip_content_type'] == 'content_wysiwyg' ) {
		$tooltip_content = $settings['plus_tooltip_content_wysiwyg'];
		$this->add_render_attribute( '_tooltip', 'title', $tooltip_content, true );
	}

	$plus_tooltip_position = ! empty( $settings['tooltip_opt_plus_tooltip_position'] ) ? $settings['tooltip_opt_plus_tooltip_position'] : 'top';
	$this->add_render_attribute( '_tooltip', 'data-tippy-placement', $plus_tooltip_position, true );

	$tooltip_interactive = isset( $settings['tooltip_opt_plus_tooltip_interactive'] ) && $settings['tooltip_opt_plus_tooltip_interactive'] == 'yes' ? 'true' : 'false';
	$this->add_render_attribute( '_tooltip', 'data-tippy-interactive', $tooltip_interactive, true );

	$plus_tooltip_theme = ! empty( $settings['tooltip_opt_plus_tooltip_theme'] ) ? $settings['tooltip_opt_plus_tooltip_theme'] : 'dark';
	$this->add_render_attribute( '_tooltip', 'data-tippy-theme', $plus_tooltip_theme, true );

	$tooltip_arrow = ( $settings['tooltip_opt_plus_tooltip_arrow'] != 'none' || empty( $settings['tooltip_opt_plus_tooltip_arrow'] ) ) ? 'true' : 'false';
	$this->add_render_attribute( '_tooltip', 'data-tippy-arrow', $tooltip_arrow, true );

	$plus_tooltip_arrow = ! empty( $settings['tooltip_opt_plus_tooltip_arrow'] ) ? $settings['tooltip_opt_plus_tooltip_arrow'] : 'sharp';
	$this->add_render_attribute( '_tooltip', 'data-tippy-arrowtype', $plus_tooltip_arrow, true );

	$plus_tooltip_animation = ! empty( $settings['tooltip_opt_plus_tooltip_animation'] ) ? $settings['tooltip_opt_plus_tooltip_animation'] : 'shift-toward';
	$this->add_render_attribute( '_tooltip', 'data-tippy-animation', $plus_tooltip_animation, true );

	$plus_tooltip_x_offset = isset( $settings['tooltip_opt_plus_tooltip_x_offset'] ) ? $settings['tooltip_opt_plus_tooltip_x_offset'] : 0;
	$plus_tooltip_y_offset = isset( $settings['tooltip_opt_plus_tooltip_y_offset'] ) ? $settings['tooltip_opt_plus_tooltip_y_offset'] : 0;
	$this->add_render_attribute( '_tooltip', 'data-tippy-offset', $plus_tooltip_x_offset . ',' . $plus_tooltip_y_offset, true );

	$tooltip_duration_in  = isset( $settings['tooltip_opt_plus_tooltip_duration_in'] ) ? $settings['tooltip_opt_plus_tooltip_duration_in'] : 250;
	$tooltip_duration_out = isset( $settings['tooltip_opt_plus_tooltip_duration_out'] ) ? $settings['tooltip_opt_plus_tooltip_duration_out'] : 200;
	$tooltip_trigger      = ! empty( $settings['tooltip_opt_plus_tooltip_triggger'] ) ? $settings['tooltip_opt_plus_tooltip_triggger'] : 'mouseenter';
	$tooltip_arrowtype    = ! empty( $settings['tooltip_opt_plus_tooltip_arrow'] ) ? $settings['tooltip_opt_plus_tooltip_arrow'] : 'sharp';
}

/* MouseMove Parallax */
$move_parallax = $move_parallax_attr = $parallax_move = '';
if ( ! empty( $settings['plus_mouse_move_parallax'] ) && $settings['plus_mouse_move_parallax'] == 'yes' ) {
	$move_parallax       = 'pt-plus-move-parallax';
	$parallax_move       = 'parallax-move';
	$parallax_speed_x    = isset( $settings['plus_mouse_parallax_speed_x']['size'] ) ? $settings['plus_mouse_parallax_speed_x']['size'] : 30;
	$parallax_speed_y    = isset( $settings['plus_mouse_parallax_speed_y']['size'] ) ? $settings['plus_mouse_parallax_speed_y']['size'] : 30;
	$move_parallax_attr .= ' data-move_speed_x="' . esc_attr( $parallax_speed_x ) . '" ';
	$move_parallax_attr .= ' data-move_speed_y="' . esc_attr( $parallax_speed_y ) . '" ';
}

/* Tilt3D Parallax */
$inner_js_tilt = $tilt_hover_class = $tilt_attr = '';
if ( ! empty( $settings['plus_tilt_parallax'] ) && $settings['plus_tilt_parallax'] == 'yes' || ( ! empty( $settings['tilt_parallax'] ) && $settings['tilt_parallax'] == 'yes' && $wname === 'tpinfobox' ) ) {
	$tilt_scale       = isset( $settings['plus_tilt_opt_tilt_scale']['size'] ) ? $settings['plus_tilt_opt_tilt_scale']['size'] : 1.1;
	$tilt_max         = isset( $settings['plus_tilt_opt_tilt_max']['size'] ) ? $settings['plus_tilt_opt_tilt_max']['size'] : 20;
	$tilt_perspective = isset( $settings['plus_tilt_opt_tilt_perspective']['size'] ) ? $settings['plus_tilt_opt_tilt_perspective']['size'] : 400;
	$tilt_speed       = isset( $settings['plus_tilt_opt_tilt_speed']['size'] ) ? $settings['plus_tilt_opt_tilt_speed']['size'] : 400;

	$this->add_render_attribute( '_tilt_parallax', 'data-tilt', '', true );
	$this->add_render_attribute( '_tilt_parallax', 'data-tilt-scale', $tilt_scale, true );
	$this->add_render_attribute( '_tilt_parallax', 'data-tilt-max', $tilt_max, true );
	$this->add_render_attribute( '_tilt_parallax', 'data-tilt-perspective', $tilt_perspective, true );
	$this->add_render_attribute( '_tilt_parallax', 'data-tilt-speed', $tilt_speed, true );

	if ( ! empty( $settings['plus_tilt_opt_tilt_easing'] ) && $settings['plus_tilt_opt_tilt_easing'] != 'custom' ) {
		$easing_tilt = $settings['plus_tilt_opt_tilt_easing'];
	} elseif ( ! empty( $settings['plus_tilt_opt_tilt_easing'] ) && $settings['plus_tilt_opt_tilt_easing'] == 'custom' ) {
		$easing_tilt = $settings['plus_tilt_opt_tilt_easing_custom'];
	} else {
		$easing_tilt = 'cubic-bezier(.03,.98,.52,.99)';
	}
	$this->add_render_attribute( '_tilt_parallax', 'data-tilt-easing', $easing_tilt, true );
	$inner_js_tilt    = 'js-tilt';
	$tilt_hover_class = 'tilt-index';
}

/* Overlay Effect */
$reveal_effects = $effect_attr = '';
if ( ! empty( $settings['plus_overlay_effect'] ) && $settings['plus_overlay_effect'] == 'yes' ) {
	$effect_rand_no = uniqid( 'reveal' );
	$color_1        = ! empty( $settings['plus_overlay_spcial_effect_color_1'] ) ? $settings['plus_overlay_spcial_effect_color_1'] : '#313131';
	$color_2        = ! empty( $settings['plus_overlay_spcial_effect_color_2'] ) ? $settings['plus_overlay_spcial_effect_color_2'] : '#ff214f';
	$effect_attr   .= ' data-reveal-id="' . esc_attr( $effect_rand_no ) . '" ';
	$effect_attr   .= ' data-effect-color-1="' . esc_attr( $color_1 ) . '" ';
	$effect_attr   .= ' data-effect-color-2="' . esc_attr( $color_2 ) . '" ';
	$reveal_effects = ' pt-plus-reveal ' . esc_attr( $effect_rand_no ) . ' ';
}

/* Continuous Animation */
$continuous_animation = '';
if ( ( isset( $settings['plus_continuous_animation'] ) && $settings['plus_continuous_animation'] == 'yes' ) && ! empty( $settings['plus_animation_effect'] ) ) {
	if ( isset( $settings['plus_animation_hover'] ) && $settings['plus_animation_hover'] == 'yes' ) {
		$animation_class = 'hover_';
	} else {
		$animation_class = 'image-';
	}
	$continuous_animation = $animation_class . $settings['plus_animation_effect'];
}

$before_content = $after_content = '';
$uid_widget     = uniqid( 'plus' );
if ( ! empty( $gsapScroll ) || ( isset( $settings['magic_scroll'] ) && $settings['magic_scroll'] == 'yes' ) || ( isset( $settings['plus_tooltip'] ) && $settings['plus_tooltip'] == 'yes' ) || ( isset( $settings['plus_mouse_move_parallax'] ) && $settings['plus_mouse_move_parallax'] == 'yes' ) || ( isset( $settings['plus_tilt_parallax'] ) && $settings['plus_tilt_parallax'] == 'yes' ) || ( isset( $settings['plus_overlay_effect'] ) && $settings['plus_overlay_effect'] == 'yes' ) || ( isset( $settings['plus_continuous_animation'] ) && $settings['plus_continuous_animation'] == 'yes' ) ) {
	$before_content .= '<div id="' . esc_attr( $uid_widget ) . '" class="' . esc_attr( $PlusExtra_Class ) . ' plus-widget-wrapper ' . esc_attr( $magic_class ) . ' ' . esc_attr( $move_parallax ) . ' ' . esc_attr( $reveal_effects ) . ' ' . esc_attr( $continuous_animation ) . '" ' . $effect_attr . ' ' . $this->get_render_attribute_string( '_tooltip' ) . '>';

	if ( ! empty( $gsapScroll ) ) {
		$gsapClass = ' tp-gsap-scroll';
	}

	$before_content .= '<div class="plus-widget-inner-wrap ' . esc_attr( $gsapClass ) . ' ' . esc_attr( $parallax_scroll ) . ' " ' . $gsap_attr . ' ' . $magic_attr . '>';

	if ( isset( $settings['plus_mouse_move_parallax'] ) && $settings['plus_mouse_move_parallax'] == 'yes' ) {
		$before_content .= '<div class="plus-widget-inner-parallax ' . esc_attr( $parallax_move ) . '" ' . $move_parallax_attr . '>';
	}

	if ( isset( $settings['plus_tilt_parallax'] ) && $settings['plus_tilt_parallax'] == 'yes' ) {
		$before_content .= '<div class="plus-widget-inner-tilt js-tilt" ' . $this->get_render_attribute_string( '_tilt_parallax' ) . '>';
	}
}

if ( ! empty( $gsapScroll ) || ( isset( $settings['magic_scroll'] ) && $settings['magic_scroll'] == 'yes' ) || ( isset( $settings['plus_tooltip'] ) && $settings['plus_tooltip'] == 'yes' ) || ( isset( $settings['plus_mouse_move_parallax'] ) && $settings['plus_mouse_move_parallax'] == 'yes' ) || ( isset( $settings['plus_tilt_parallax'] ) && $settings['plus_tilt_parallax'] == 'yes' ) || ( isset( $settings['plus_overlay_effect'] ) && $settings['plus_overlay_effect'] == 'yes' ) || ( isset( $settings['plus_continuous_animation'] ) && $settings['plus_continuous_animation'] == 'yes' ) ) {
	$after_content .= '</div>';
	$after_content .= '</div>';

	if ( isset( $settings['plus_mouse_move_parallax'] ) && $settings['plus_mouse_move_parallax'] == 'yes' ) {
		$after_content .= '</div>';
	}

	if ( isset( $settings['plus_tilt_parallax'] ) && $settings['plus_tilt_parallax'] == 'yes' ) {
		$after_content .= '</div>';
	}

	$inline_tippy_js = '';
	if ( isset( $settings['plus_tooltip'] ) && $settings['plus_tooltip'] == 'yes' ) {
		$inline_tippy_js = 'jQuery( document ).ready(function() {
        "use strict";
            if(typeof tippy === "function"){
                tippy( "#' . esc_attr( $uid_widget ) . '" , {
                    arrowType : "' . esc_attr( $tooltip_arrowtype ) . '",
                    duration : [' . esc_attr( $tooltip_duration_in ) . ',' . esc_attr( $tooltip_duration_out ) . '],
                    trigger : "' . esc_attr( $tooltip_trigger ) . '",
                    appendTo: document.querySelector("#' . esc_attr( $uid_widget ) . '")
                });
            }
        });';
		$after_content  .= wp_print_inline_script_tag( $inline_tippy_js );
	}
}
