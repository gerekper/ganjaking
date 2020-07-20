<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

return array(

    'buttons' => array(
	    'prev_next_buttons_section_start' => array(
		    'type' => 'sectionstart',
	    ),

	    'prev_next_buttons_section_title' => array(
		    'type' => 'title',
		    'title'     => esc_html_x( 'Prev/Next buttons options', 'Admin: section title', 'yith-woocommerce-multi-step-checkout' ),
	    ),

    	'show_prev_next_button' => array(
		    'title'     => esc_html_x( 'Show Prev/Next buttons', 'Panel: section title', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'onoff',
		    'id'        => 'yith_wcms_nav_buttons_enabled',
		    'desc'      => esc_html_x( 'Enable to show navigation buttons at the bottom of the step page. If disabled, the customer can move to the another step by clicking on the step tabs', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
		    'default'   => 'yes'
	    ),
		'disable_prev_button_on_last_step' => array(
			'title'     => esc_html_x( 'Hide Prev button in last step', 'Panel: section title', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'yith_wcms_nav_disabled_prev_button',
			'desc'      => esc_html_x( 'Enable to hide the previous button in the last step', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => 'no',
			'deps'      => array(
				'id'    => 'yith_wcms_nav_buttons_enabled',
				'value' => 'yes',
				'type'  => 'hide'
			),
		),
	    'prev_button_label' => array(
		    'title'     => esc_html_x( 'Label for prev button', 'Admin option title', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'text',
		    'id'        => 'yith_wcms_timeline_options_prev',
		    'default'   => esc_html_x( 'Previous', 'Short text: Navigation Button label', 'yith-woocommerce-multi-step-checkout' ),
		    'desc'      => esc_html_x( 'Enter a text for prev button', 'Admin option description', 'yith-woocommerce-multi-step-checkout' ),
		    'deps'      => array(
			    'id'    => 'yith_wcms_nav_buttons_enabled',
			    'value' => 'yes',
			    'type'  => 'hide'
		    ),
	    ),
	    'next_button_label' => array(
		    'title'     => esc_html_x( 'Label for next button', 'Admin option title', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'text',
		    'id'        => 'yith_wcms_timeline_options_next',
		    'default'   => esc_html_x( 'Next', 'Short text: Navigation Button label', 'yith-woocommerce-multi-step-checkout' ),
		    'desc'      => esc_html_x( 'Enter a text for next button', 'Admin option description', 'yith-woocommerce-multi-step-checkout' ),
		    'deps'      => array(
			    'id'    => 'yith_wcms_nav_buttons_enabled',
			    'value' => 'yes',
			    'type'  => 'hide'
		    ),
	    ),
	    'skip_login_button_label' => array(
		    'title'     => esc_html_x( 'Label for skip login button', 'Frontend: button label', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'text',
		    'id'        => 'yith_wcms_timeline_options_skip_login',
		    'desc'      => esc_html_x( 'Enter a text for skip login button', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
		    'default'   => esc_html_x( 'Skip Login', 'Frontend: button label', 'yith-woocommerce-multi-step-checkout' ),
		    'deps'      => array(
			    'id'    => 'yith_wcms_nav_buttons_enabled',
			    'value' => 'yes',
			    'type'  => 'hide'
		    ),
	    ),
	    'set_navigation_button_style' => array(
		    'title'     => esc_html_x( 'Buttons style', 'Panel: Section title', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'radio',
		    'id' => 'yith_wcms_nav_buttons_style',
		    'options' => array(
		    	'theme-style' => esc_html_x( 'Use theme buttons', 'Panel: option label', 'yith-woocommerce-multi-step-checkout' ),
		    	'custom-style' => esc_html_x( 'Set custom style', 'Panel: option label', 'yith-woocommerce-multi-step-checkout' )
		    ),
		    'deps'      => array(
			    'id'    => 'yith_wcms_nav_buttons_enabled',
			    'value' => 'yes',
			    'type'  => 'hide'
		    ),
		    'default' => 'theme-style'
	    ),
	    'set_buttons_background_style' => array(
		    'name'      => esc_html__( 'Buttons colors', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'multi-colorpicker',
		    'desc'      => esc_html__( 'Set background color for normal and hover step', 'yith-woocommerce-multi-step-checkout' ),
		    'id'        => 'yith_wcms_navigation_buttons_background_colors',
		    'colorpickers' => array(
			    array(
				    'name'      => esc_html__( 'Default color', 'yith-woocommerce-multi-step-checkout' ),
				    'id'        => 'default',
				    'default'   => '#43A08C'
			    ),
			    array(
				    'name'      => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
				    'id'        => 'hover',
				    'default'   => '#30615e'
			    ),
		    ),
		    'deps'      => array(
			    'id'    => 'yith_wcms_nav_buttons_style',
			    'value' => 'custom-style',
			    'type'  => 'hide'
		    ),
	    ),
	    'set_buttons_text_style' => array(
		    'name'      => esc_html__( 'Buttons label colors', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'multi-colorpicker',
		    'desc'      => esc_html__( 'Set label color for normal and hover step', 'yith-woocommerce-multi-step-checkout' ),
		    'id'        => 'yith_wcms_navigation_buttons_text_colors',
		    'colorpickers' => array(
			    array(
				    'name'      => esc_html__( 'Default color', 'yith-woocommerce-multi-step-checkout' ),
				    'id'        => 'default',
				    'default'   => '#ffffff'
			    ),
			    array(
				    'name'      => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
				    'id'        => 'hover',
				    'default'   => '#ffffff'
			    ),
		    ),
		    'deps'      => array(
			    'id'    => 'yith_wcms_nav_buttons_style',
			    'value' => 'custom-style',
			    'type'  => 'hide'
		    ),
	    ),
	    'enable_scroll_top_effects' => array(
		    'title'     => esc_html_x( 'Enable "Scroll to top" effect', 'Panel: section title', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'onoff',
		    'id'        => 'yith_wcms_scroll_top_enabled',
		    'desc'      => esc_html_x( 'Enable to add a "scroll to top" effect when customer click on the next/prev buttons', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
		    'default'   => 'no'
	    ),
	    'enable_scroll_top_effects_container' => array(
		    'title'     => esc_html_x( '"Scroll to top" anchor', 'Frontend: button label', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'text',
		    'id'        => 'yith_wcms_scroll_top_anchor',
		    'desc'      => esc_html_x( 'Set the anchor of scroll to top effect. Default is #checkout_timeline', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
		    'default'   => '#checkout_timeline',
		    'deps'      => array(
			    'id'    => 'yith_wcms_scroll_top_enabled',
			    'value' => 'yes',
			    'type'  => 'hide'
		    ),
	    ),
	    'prev_next_buttons_section_end' => array(
		    'type' => 'sectionend',
	    ),

	    'back_to_cart_button_section_start' => array(
		    'type' => 'sectionstart',
	    ),

	    'back_to_cart_button_section_title' => array(
		    'type' => 'title',
		    'title'     => esc_html_x( 'Back to cart button options', 'Admin: section title', 'yith-woocommerce-multi-step-checkout' ),
	    ),

	    'back_to_cart_button_show' => array(
		    'title'     => esc_html_x( 'Show Back to cart button', 'Panel: section title', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'onoff',
		    'id'        => 'yith_wcms_nav_enable_back_to_cart_button',
		    'desc'      => esc_html_x( "Enable to display the 'back to cart' button in the checkout page", 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
		    'default'   => 'no',
	    ),

	    'back_to_cart_button_hide' => array(
		    'title'     => esc_html_x( 'Hide back to cart button in last step', 'Panel: section title', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'onoff',
		    'id'        => 'yith_wcms_nav_disabled_back_to_cart_button',
		    'desc'      => esc_html_x( 'Enable to hide Back to cart button in last step', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
		    'default'   => 'no',
		    'deps'      => array(
			    'id'    => 'yith_wcms_nav_enable_back_to_cart_button',
			    'value' => 'yes',
			    'type'  => 'hide'
		    ),
	    ),

	    'back_to_cart_button_label' => array(
		    'title'     => esc_html_x( 'Label for Back to cart button', 'Frontend: button label', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'text',
		    'id'        => 'yith_wcms_timeline_options_back_to_cart',
		    'desc'      => esc_html_x( 'Enter a text for back to cart button', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
		    'default'   => esc_html_x( 'Back to cart', 'Frontend: button label', 'yith-woocommerce-multi-step-checkout' ),
		    'deps'      => array(
			    'id'    => 'yith_wcms_nav_enable_back_to_cart_button',
			    'value' => 'yes',
			    'type'  => 'hide'
		    ),
	    ),

	    'set_back_to_cart_button_style' => array(
		    'title'     => esc_html_x( 'Button style', 'Panel: Section title', 'yith-woocommerce-multi-step-checkout' ),
		    'desc'      => esc_html_x( 'Set if you want to use the theme style or a custom style for the navigation buttons', 'Panel: option description', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'radio',
		    'id' => 'yith_wcms_back_to_cart_button_style',
		    'options' => array(
			    'theme-style' => esc_html_x( 'Use theme buttons', 'Panel: option label', 'yith-woocommerce-multi-step-checkout' ),
			    'custom-style' => esc_html_x( 'Set custom style', 'Panel: option label', 'yith-woocommerce-multi-step-checkout' )
		    ),
		    'deps'      => array(
			    'id'    => 'yith_wcms_nav_enable_back_to_cart_button',
			    'value' => 'yes',
			    'type'  => 'hide'
		    ),
		    'default' => 'theme-style'
	    ),
	    'set_back_to_cart_background_style' => array(
		    'name'      => esc_html__( 'Button colors', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'multi-colorpicker',
		    'desc'      => esc_html__( 'Set background color for normal and hover step', 'yith-woocommerce-multi-step-checkout' ),
		    'id'        => 'yith_wcms_back_to_cart_button_background_colors',
		    'colorpickers' => array(
			    array(
				    'name'      => esc_html__( 'Default color', 'yith-woocommerce-multi-step-checkout' ),
				    'id'        => 'default',
				    'default'   => '#43A08C'
			    ),
			    array(
				    'name'      => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
				    'id'        => 'hover',
				    'default'   => '#30615e'
			    ),
		    ),
		    'deps'      => array(
			    'id'    => 'yith_wcms_back_to_cart_button_style',
			    'value' => 'custom-style',
			    'type'  => 'hide'
		    ),
	    ),
	    'set_back_to_cart_text_style' => array(
		    'name'      => esc_html__( 'Button label colors', 'yith-woocommerce-multi-step-checkout' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'multi-colorpicker',
		    'desc'      => esc_html__( 'Set label color for normal and hover step', 'yith-woocommerce-multi-step-checkout' ),
		    'id'        => 'yith_wcms_back_to_cart_button_text_colors',
		    'colorpickers' => array(
			    array(
				    'name'      => esc_html__( 'Default color', 'yith-woocommerce-multi-step-checkout' ),
				    'id'        => 'default',
				    'default'   => '#ffffff'
			    ),
			    array(
				    'name'      => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
				    'id'        => 'hover',
				    'default'   => '#ffffff'
			    ),
		    ),
		    'deps'      => array(
			    'id'    => 'yith_wcms_back_to_cart_button_style',
			    'value' => 'custom-style',
			    'type'  => 'hide'
		    ),
	    ),

	    'back_to_cart_button_section_end' => array(
		    'type' => 'sectionend',
	    ),
    )
);
