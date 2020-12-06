<?php
/**
 *  Main shortcode for Tab Element.
 *
 * @package Ult_tab_element.
 */

$output                   = '';
$ult_title                = '';
$ult_tab_element_settings = shortcode_atts(
	array(
		'title_color'                => '',
		'auto_rotate'                => '',
		'interval'                   => 0,
		'tab_style'                  => '',
		'tab_bottom_border'          => '',
		'border_color'               => '',
		'border_thickness'           => '',
		'tab_title_color'            => '',
		'tab_hover_title_color'      => '',
		'tab_background_color'       => '',
		'tab_hover_background_color' => '',
		'container_width'            => '',
		'el_class'                   => '',
		'container_width'            => '',
		'main_heading_font_family'   => '',
		'title_font_size'            => '',
		'title_font_wt'              => '',
		'title_line_ht'              => '',
		'desc_font_family'           => '',
		'desc_font_size'             => '',
		'desc_font_style'            => '',
		'desc_line_ht'               => '',
		'shadow_color'               => '',
		'shadow_width'               => '',
		'enable_bg_color'            => '',
		'resp_style'                 => '',
		'container_border_style'     => '',
		'container_color_border'     => '',
		'cont_border_size'           => '',
		'tabs_border_radius'         => '',
		'tab_animation'              => '',
		'tab_describe_color'         => '',
		'title_font_style'           => '',
		'css'                        => '',
		'act_icon_color'             => '',
		'acttab_background'          => '',
		'acttab_title'               => '',
		'resp_type'                  => '',
		'resp_width'                 => '',
		'resp_style'                 => '',
		'ac_tabs'                    => '',
	),
	$atts
);

global $tabarr;
	$tabarr = array();
	do_shortcode( $content );

if ( '' == $ult_tab_element_settings['acttab_background'] ) {
	$ult_tab_element_settings['acttab_background'] = $ult_tab_element_settings['tab_hover_background_color'];
}
if ( '' == $ult_tab_element_settings['acttab_title'] ) {
	$ult_tab_element_settings['acttab_title'] = $ult_tab_element_settings['tab_hover_title_color'];
}

/*---------------padding---------------------*/
$css_class = '';
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_tab_element_settings['css'], ' ' ), 'ult_tab_element', $atts );
$css_class = esc_attr( $css_class );

/*-------------------font style------------------*/

$container_style = '';
$ult_style       = '';
$tab_style_no    = '';
if ( 'Disable' == $ult_tab_element_settings['tab_bottom_border'] ) {
	$ult_tab_element_settings['border_thickness'] = '0';
	$ult_tab_element_settings['border_color']     = 'transparent';
}
if ( '' != $ult_tab_element_settings['container_width'] ) {
	$container_style = 'width:' . $ult_tab_element_settings['container_width'] . 'px;';
}
$border_style  = '';
$mhfont_family = '';
$border_style  = '';

$tabs_nav_style = '';

if ( '' != $ult_tab_element_settings['title_font_size'] ) {
	$tabs_nav_style .= 'font-size:' . $ult_tab_element_settings['title_font_size'] . 'px;';
}
if ( '' != $ult_tab_element_settings['title_line_ht'] ) {
	$tabs_nav_style .= 'line-height:' . $ult_tab_element_settings['title_line_ht'] . 'px;';
}

if ( function_exists( 'get_ultimate_font_family' ) ) {

		$mhfont_family = get_ultimate_font_family( $ult_tab_element_settings['main_heading_font_family'] );
	if ( '' != $mhfont_family ) {
		$tabs_nav_style .= 'font-family:' . $mhfont_family . ';';
	}
}
if ( function_exists( 'get_ultimate_font_style' ) ) {
	if ( '' != $ult_tab_element_settings['title_font_style'] ) {
		$tabs_nav_style .= get_ultimate_font_style( $ult_tab_element_settings['title_font_style'] );
	}
}


/*-------------------auto rotate------------------*/

if ( 'Disables' == $ult_tab_element_settings['auto_rotate'] ) {
	$ult_tab_element_settings['interval'] = 0;
	$autorotate                           = 'no';
} else {
	$autorotate = 'yes';
}

if ( '' == $ult_tab_element_settings['tab_background_color'] ) {
	$ult_tab_element_settings['tab_background_color'] = 'transparent';
}

$element = 'wpb_tabs';
if ( 'vc_tour' == $this->shortcode ) {
	$element = 'wpb_tour';
}
$ul_style = '';
$tabs_nav = '';
$style    = '';

/*------------------- style------------------*/

if ( 'Style_1' == $ult_tab_element_settings['tab_style'] ) {

	$style = 'style1';
} elseif ( 'Style_2' == $ult_tab_element_settings['tab_style'] ) {

	$style = 'style2';

} elseif ( 'Style_3' == $ult_tab_element_settings['tab_style'] ) {

	$style = 'style3';

} elseif ( 'Style_4' == $ult_tab_element_settings['tab_style'] ) {
	$ult_style = 'ult_tab_style_4';
	$style     = 'style1';
} elseif ( 'Style_5' == $ult_tab_element_settings['tab_style'] ) {
	$ult_style = 'ult_tab_style_5';
	$style     = 'style1';
}
foreach ( $tabarr as $key => $value ) {
	$icon_value = $value['icon_size'];
	if ( is_numeric( $icon_value ) ) {
		$icon_value1[] = $value['icon_size'];
	}
}


/*-------------- border style-----------*/
$tab_border      = '';
$tab_border     .= 'color:' . $ult_tab_element_settings['border_color'] . ';';
	$tab_border .= 'border-bottom-color:' . $ult_tab_element_settings['border_color'] . ';';
	$tab_border .= 'border-bottom-width:' . $ult_tab_element_settings['border_thickness'] . 'px;';
	$tab_border .= 'border-bottom-style:solid;';
if ( 'Style_1' == $ult_tab_element_settings['tab_style'] ) {
	$tab_border .= 'background-color:' . $ult_tab_element_settings['tab_background_color'] . ';';
	$tab_border .= 'border-top-left-radius:' . $ult_tab_element_settings['tabs_border_radius'] . 'px;';
	$tab_border .= 'border-top-right-radius:' . $ult_tab_element_settings['tabs_border_radius'] . 'px;';
}
if ( 'Style_2' == $ult_tab_element_settings['tab_style'] ) {
	$tab_border .= 'border-bottom-width:0px;';
}
if ( 'Style_4' == $ult_tab_element_settings['tab_style'] || 'Style_5' == $ult_tab_element_settings['tab_style'] ) {
	$tab_border .= 'border-bottom-width:0px;';

}

/*-----------------content baground-------------------*/

$contain_bg = '';

/*---------------- description font family-----------*/
if ( '' != $ult_tab_element_settings['desc_font_size'] ) {
	$contain_bg .= 'font-size:' . $ult_tab_element_settings['desc_font_size'] . 'px;';
}
if ( '' != $ult_tab_element_settings['title_line_ht'] ) {
	$contain_bg .= 'line-height:' . $ult_tab_element_settings['desc_line_ht'] . 'px;';
}

if ( function_exists( 'get_ultimate_font_family' ) ) {

		$dhfont_family = get_ultimate_font_family( $ult_tab_element_settings['desc_font_family'] );
	if ( '' != $dhfont_family ) {
		$contain_bg .= 'font-family:' . $dhfont_family . ';';
	}
}
if ( function_exists( 'get_ultimate_font_style' ) ) {
	if ( '' != $ult_tab_element_settings['desc_font_style'] ) {
		$contain_bg .= get_ultimate_font_style( $ult_tab_element_settings['desc_font_style'] );
	}
}




$ult_top       = '';
$icon_top_link = '';

if ( 'Style_1' == $ult_tab_element_settings['tab_style'] ) {
	$ult_top       = 'ult_top';
	$icon_top_link = 'icon_top_link';
} elseif ( 'Style_2' == $ult_tab_element_settings['tab_style'] ) {

	$ult_top       = 'ult_top';
	$icon_top_link = '';
}
if ( 'Style_4' == $ult_tab_element_settings['tab_style'] ) {
	$tab_style_no .= 'Style_4';
	$ult_top       = 'ult_top';
	$icon_top_link = 'style_4_top';
}
if ( 'Style_5' == $ult_tab_element_settings['tab_style'] ) {
	$tab_style_no .= 'Style_5';
	$icon_top_link = '';

}if ( 'Style_3' == $ult_tab_element_settings['tab_style'] ) {
		$ult_top       = 'ult_top';
		$icon_top_link = 'icon_top_link';
}

if ( '' != $ult_tab_element_settings['enable_bg_color'] ) {
	$contain_bg .= 'background-color:' . $ult_tab_element_settings['enable_bg_color'] . ';';
}
if ( '' != $ult_tab_element_settings['container_border_style'] ) {
	$contain_bg .= 'border-style:' . $ult_tab_element_settings['container_border_style'] . ';';
	$contain_bg .= 'border-color:' . $ult_tab_element_settings['container_color_border'] . ';';
	$contain_bg .= 'border-width:' . $ult_tab_element_settings['cont_border_size'] . 'px;';
	$contain_bg .= 'border-top:none;';
}
if ( '' != $ult_tab_element_settings['tab_describe_color'] ) {
	$contain_bg .= 'color:' . $ult_tab_element_settings['tab_describe_color'] . ';';
}
$acord = '';

$array_count   = '';
$array_count   = count( $tabarr );
$newtab        = '';
$newtab       .= '<ul class="ult_tabmenu ' . esc_attr( $style ) . ' ' . esc_attr( $tab_style_no ) . '" style="' . esc_attr( $tab_border ) . '">';
$cnt           = 0;
$acord        .= '';
$accontaint    = '';
$ult_ac_border = '';
foreach ( $tabarr as $key => $value ) {
		$cnt++;


	$icon_position    = $value['font_icons_position'];
	$tabicon          = $value['icon'];
	$icon_color       = $value['icon_color'];
	$icon_size        = $value['icon_size'];
	$icon_hover_color = $value['icon_hover_color'];
	$margin           = $value['icon_margin'];
	$accontaint       = $value['content'];
	$accontaint       = wpb_js_remove_wpautop( $accontaint );
	/*---icon style---*/
	if ( '' == $icon_size ) {
		$icon_size = '15';
	}
	$tab_icon_style  = '';
	$tab_icon_style .= 'color:' . $icon_color . ';';
	$tab_icon_style .= 'font-size:' . $icon_size . 'px;';
	$tab_icon_style .= $margin;
	$link_li_style   = '';
	$bgcolor         = '';
	if ( 'Style_2' != $ult_tab_element_settings['tab_style'] ) {
		$link_li_style .= 'background-color:' . $ult_tab_element_settings['tab_background_color'] . ';';
	} else {
		$bgcolor .= 'background-color:' . $ult_tab_element_settings['tab_background_color'] . ';';

	}
	$style5bgcolor = '';
	if ( 'Style_5' == $ult_tab_element_settings['tab_style'] ) {
		$style5bgcolor = 'border-color:' . $ult_tab_element_settings['shadow_color'] . ';';
		$ult_top       = 'ult_top';
	}
	if ( 'Style_4' == $ult_tab_element_settings['tab_style'] ) {
		$ult_top        = 'ult_top';
		$link_li_style .= '';
		$link_li_style .= 'border-color:' . $ult_tab_element_settings['border_color'] . ';';
		$link_li_style .= 'border-width:' . $ult_tab_element_settings['border_thickness'] . 'px;';
		$link_li_style .= 'border-style:solid;';

	}
	/*---------------- for tabs border -----------------*/

	if ( 'Style_2' != $ult_tab_element_settings['tab_style'] ) {
		if ( $cnt == $array_count ) {
			$link_li_style .= 'border-top-right-radius:' . $ult_tab_element_settings['tabs_border_radius'] . 'px;';
		} elseif ( 1 == $cnt ) {
			$link_li_style .= 'border-top-left-radius:' . $ult_tab_element_settings['tabs_border_radius'] . 'px;';
		}
	} else {

		if ( $cnt == $array_count ) {
			$bgcolor .= 'border-top-right-radius:' . $ult_tab_element_settings['tabs_border_radius'] . 'px;';
		} elseif ( 1 == $cnt ) {
			$bgcolor .= 'border-top-left-radius:' . $ult_tab_element_settings['tabs_border_radius'] . 'px;';
		}
	}

	/*------------ accordian border style --------------*/

	$ult_ac_border .= 'border-bottom-color:' . $ult_tab_element_settings['border_color'] . ';';
	$ult_ac_border .= 'border-bottom-width:' . $ult_tab_element_settings['border_thickness'] . 'px;';
	$ult_ac_border .= 'border-bottom-style:solid;';

	if ( isset( $value['title'] ) ) {

		if ( 'Right' == $icon_position ) {
			$icon_position = 'right';
			$newtab       .= '<li class="ult_tab_li ' . esc_attr( $ult_style ) . '" data-iconcolor="' . esc_attr( $icon_color ) . '" data-iconhover="' . esc_attr( $icon_hover_color ) . '" style="' . esc_attr( $link_li_style ) . '">
					<a href="#" style="color:' . esc_attr( $ult_tab_element_settings['tab_title_color'] ) . ';' . esc_attr( $bgcolor ) . ';' . esc_attr( $style5bgcolor ) . '" class="ult_a ' . esc_attr( $css_class ) . '">
					   <span class="ult_tab_main  ' . esc_attr( $ult_tab_element_settings['resp_style'] ) . ' ">
					   <span class="ult-span-text" style="' . esc_attr( $tabs_nav_style ) . '">' . $value['title'] . '</span>
					   <div class="aio-icon none " style="' . esc_attr( $tab_icon_style ) . '">
					   <i class=" ' . esc_attr( $tabicon ) . ' ult_tab_icon"  ></i>
					   </div>
					   </span>

					</a>
					</li>';

			/*-------------------accordion right icon------------------*/

			$acord .= '<dt>
        	<a class="ult-tabto-actitle withBorder ult_a" style="color:' . esc_attr( $ult_tab_element_settings['tab_title_color'] ) . ';' . esc_attr( $style5bgcolor ) . ';background-color:' . esc_attr( $ult_tab_element_settings['tab_background_color'] ) . ';' . esc_attr( $ult_ac_border ) . '" href="#">
        		<i class="accordion-icon">+</i>
        			<span class="ult_tab_main ult_ac_main' . esc_attr( $ult_tab_element_settings['resp_style'] ) . '">
					   <span class="ult-span-text ult_acordian-text" style="' . esc_attr( $tabs_nav_style ) . ';color:inherit " >' . $value['title'] . '</span>
					</span>
					   <div class="aio-icon none " style="' . esc_attr( $tab_icon_style ) . '" data-iconcolor="' . esc_attr( $icon_color ) . '" data-iconhover="' . esc_attr( $icon_hover_color ) . '">
					   <i class="  ' . esc_attr( $tabicon ) . ' ult_tab_icon"  ></i>
					   </div>
					</a></dt>
            		<dd class="ult-tabto-accordionItem ult-tabto-accolapsed">
			            <div class="ult-tabto-acontent" style="' . esc_attr( $contain_bg ) . '">
			               ' . $accontaint . '
			            </div>
        	</dd>';

		} elseif ( 'Left' == $icon_position ) {
			$icon_position = 'left';
			$newtab       .= '<li class="ult_tab_li ' . esc_attr( $ult_style ) . '" data-iconcolor="' . esc_attr( $icon_color ) . '" data-iconhover="' . esc_attr( $icon_hover_color ) . '" style="' . esc_attr( $link_li_style ) . '">
					<a href="#" style="color:' . esc_attr( $ult_tab_element_settings['tab_title_color'] ) . ';' . $bgcolor . ';' . esc_attr( $style5bgcolor ) . '" class="ult_a  ' . esc_attr( $css_class ) . '">
					     <span class="ult_tab_main ' . esc_attr( $ult_tab_element_settings['resp_style'] ) . '">
					   <div class="aio-icon none " style="' . esc_attr( $tab_icon_style ) . '">
					   <i class="  ' . esc_attr( $tabicon ) . ' ult_tab_icon"  ></i>
					   </div>
					   <span class="ult-span-text " style="' . esc_attr( $tabs_nav_style ) . '">' . $value['title'] . '</span>
						</span>
					</a>
					</li>';

			/*-------------------accordion left icon------------------*/

			$acord .= '<dt>
        	<a class="ult-tabto-actitle withBorder ult_a" style="color:' . esc_attr( $ult_tab_element_settings['tab_title_color'] ) . ';' . esc_attr( $style5bgcolor ) . ';background-color:' . esc_attr( $ult_tab_element_settings['tab_background_color'] ) . ';' . esc_attr( $ult_ac_border ) . '" href="#">
        		<i class="accordion-icon">+</i>
        			<span class="ult_tab_main ult_ac_main' . esc_attr( $ult_tab_element_settings['resp_style'] ) . '">
					   <div class="aio-icon none " style="' . esc_attr( $tab_icon_style ) . '" data-iconcolor="' . esc_attr( $icon_color ) . '" data-iconhover="' . esc_attr( $icon_hover_color ) . '">
					   <i class="  ' . esc_attr( $tabicon ) . ' ult_tab_icon"  ></i>
					   </div>
					<span class="ult-span-text ult_acordian-text" style="' . esc_attr( $tabs_nav_style ) . ';color:inherit " >' . $value['title'] . '</span>
					</span></a></dt>
            		<dd class="ult-tabto-accordionItem ult-tabto-accolapsed">
			            <div class="ult-tabto-acontent" style="' . esc_attr( $contain_bg ) . '">
			               ' . $accontaint . '
			            </div>
        	</dd>';
		} elseif ( 'Top' == $icon_position ) {
			$newtab .= '<li class="ult_tab_li ' . esc_attr( $ult_style ) . '" data-iconcolor="' . esc_attr( $icon_color ) . '" data-iconhover="' . esc_attr( $icon_hover_color ) . '" style="' . esc_attr( $link_li_style ) . '">
					<a href="#" style="color:' . esc_attr( $ult_tab_element_settings['tab_title_color'] ) . ';' . $bgcolor . ';' . esc_attr( $style5bgcolor ) . '" class="ult_a ' . esc_attr( $icon_top_link ) . ' ' . esc_attr( $css_class ) . '">
					    <span class="ult_tab_main ' . esc_attr( $ult_top ) . ' ' . esc_attr( $ult_tab_element_settings['resp_style'] ) . ' ">
					   <div class="aio-icon none icon-top "  style="' . esc_attr( $tab_icon_style ) . '">
					   <i class="  ' . esc_attr( $tabicon ) . ' ult_tab_icon" ></i>
					   </div>
					   <span class="ult-span-text" style="' . esc_attr( $tabs_nav_style ) . '">' . $value['title'] . '</span>
						</span>
					</a>
					</li>';

			/*-------------------accordion top icon------------------*/

			$acord .= '<dt>
	        	<a class="ult-tabto-actitle withBorder ult_a" style="color:' . esc_attr( $ult_tab_element_settings['tab_title_color'] ) . ';' . esc_attr( $style5bgcolor ) . ';background-color:' . esc_attr( $ult_tab_element_settings['tab_background_color'] ) . ';' . esc_attr( $ult_ac_border ) . '" href="#">
	        		<i class="accordion-icon">+</i>
	        			<span class="ult_tab_main ult_ac_main ult_top ' . esc_attr( $ult_tab_element_settings['resp_style'] ) . '">
						   <div class="aio-icon none icon-top" style="' . esc_attr( $tab_icon_style ) . '" data-iconcolor="' . esc_attr( $icon_color ) . '" data-iconhover="' . esc_attr( $icon_hover_color ) . '">
						   <i class="  ' . esc_attr( $tabicon ) . ' ult_tab_icon"  ></i>
						   </div>
						<span class="ult-span-text ult_acordian-text" style="' . esc_attr( $tabs_nav_style ) . ';color:inherit " >' . $value['title'] . '</span>
						</span></a></dt>
	            		<dd class="ult-tabto-accordionItem ult-tabto-accolapsed">
				            <div class="ult-tabto-acontent" style="' . esc_attr( $contain_bg ) . '">
				               ' . $accontaint . '
				            </div>
	        	</dd>';

		} else {
			$icon_position = 'none';
			$newtab       .= '<li class="ult_tab_li ' . esc_attr( $ult_style ) . '" data-iconcolor="' . esc_attr( $icon_color ) . '" data-iconhover="' . esc_attr( $icon_hover_color ) . '" style="' . esc_attr( $link_li_style ) . '">
					<a href="#" style="color:' . esc_attr( $ult_tab_element_settings['tab_title_color'] ) . ';' . esc_attr( $bgcolor ) . ';' . esc_attr( $style5bgcolor ) . '" class="ult_a ' . esc_attr( $ult_style ) . ' ' . esc_attr( $css_class ) . '">
					     <span class="ult_tab_main ' . esc_attr( $ult_tab_element_settings['resp_style'] ) . ' ">
					   <div class="aio-icon none " style="width:0px;padding-left:0px;">
					   
					   </div>
					   <span class="ult-span-text no_icon ult_tab_display_text" style="' . esc_attr( $tabs_nav_style ) . ';padding-right:10px;">' . $value['title'] . '</span>
					</span>
					</a>
					</li>';

			/*-------------------accordion without icon------------------*/

			$acord .= '<dt>
	        	<a class="ult-tabto-actitle withBorder ult_a" style="color:' . esc_attr( $ult_tab_element_settings['tab_title_color'] ) . ';' . esc_attr( $style5bgcolor ) . ';background-color:' . esc_attr( $ult_tab_element_settings['tab_background_color'] ) . ';' . esc_attr( $ult_ac_border ) . '" href="#">
	        		<i class="accordion-icon">+</i>
	        			<span class="ult_tab_main ult_ac_main ult_noacordicn' . esc_attr( $ult_tab_element_settings['resp_style'] ) . '">
						
						<span class="ult-span-text no_icon ult_acordian-text" style="' . esc_attr( $tabs_nav_style ) . ';color:inherit " >' . $value['title'] . '</span>
						</span></a></dt>
	            		<dd class="ult-tabto-accordionItem ult-tabto-accolapsed">
				            <div class="ult-tabto-acontent" style="' . esc_attr( $contain_bg ) . '">
				               ' . $accontaint . '
				            </div>
	        			</dd>';

		}
	}
}
$newtab .= '</ul>';

$newtabcontain  = '';
$newtabcontain .= '<div class="ult_tabitemname" style="color:inherit">';
$newtabcontain .= wpb_js_remove_wpautop( $content );
$newtabcontain .= '</div>';

$op  = '';
$op .= '<div class="ult_tabs ' . esc_attr( $ult_tab_element_settings['el_class'] ) . ' " style="' . esc_attr( $container_style ) . '" data-tabsstyle="' . $style . '"
 data-titlebg="' . esc_attr( $ult_tab_element_settings['tab_background_color'] ) . '" data-titlecolor="' . esc_attr( $ult_tab_element_settings['tab_title_color'] ) . '" 
 data-titlehoverbg="' . esc_attr( $ult_tab_element_settings['tab_hover_background_color'] ) . '" data-titlehovercolor="' . esc_attr( $ult_tab_element_settings['tab_hover_title_color'] ) . '"
 data-rotatetabs="' . esc_attr( $ult_tab_element_settings['interval'] ) . '" data-responsivemode="' . esc_attr( $ult_tab_element_settings['resp_style'] ) . '" data-animation="' . esc_attr( $ult_tab_element_settings['tab_animation'] ) . '"
data-activetitle="' . esc_attr( $ult_tab_element_settings['acttab_title'] ) . '" data-activeicon="' . esc_attr( $ult_tab_element_settings['act_icon_color'] ) . '" data-activebg="' . esc_attr( $ult_tab_element_settings['acttab_background'] ) . '"  data-respmode="' . esc_attr( $ult_tab_element_settings['resp_type'] ) . '" data-respwidth="' . esc_attr( $ult_tab_element_settings['resp_width'] ) . '">';
$op .= $newtab;
$op .= '<div class="ult_tabcontent ' . esc_attr( $style ) . '" style="' . esc_attr( $contain_bg ) . '">';
$op .= wpb_js_remove_wpautop( $content );
$op .= '</div>';
$op .= '</div>';
echo $op; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped


/*---------------- for acordian -----------------*/
$actab  = '';
$actab .= '<div class="ult_acord">
   <div class="ult-tabto-accordion " style="width:;"
    data-titlecolor="' . esc_attr( $ult_tab_element_settings['tab_title_color'] ) . '"  data-titlebg="' . esc_attr( $ult_tab_element_settings['tab_background_color'] ) . '"
     data-titlehoverbg="' . esc_attr( $ult_tab_element_settings['tab_hover_background_color'] ) . '" data-titlehovercolor="' . esc_attr( $ult_tab_element_settings['tab_hover_title_color'] ) . '" data-animation="' . esc_attr( $ult_tab_element_settings['tab_animation'] ) . '" 
     data-activetitle="' . esc_attr( $ult_tab_element_settings['acttab_title'] ) . '" data-activeicon="' . esc_attr( $ult_tab_element_settings['act_icon_color'] ) . '" data-activebg="' . esc_attr( $ult_tab_element_settings['acttab_background'] ) . '">
     <dl>';

$actab .= $acord;
$actab .= '      
    	</dl>
    <!--<div class="extraborder" style="background-color:' . $ult_tab_element_settings['tab_hover_background_color'] . '"></div>-->
</div>

</div>';
echo $actab; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
