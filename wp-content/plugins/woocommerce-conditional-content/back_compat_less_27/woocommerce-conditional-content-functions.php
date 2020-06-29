<?php

/**
 * Display or retrieve conditional content
 *
 * @since 1.0.0
 *
 * @param int $content_id Optional. The content to process.
 * @param bool $echo Optional, default to true.Whether to display or return.
 * @return null|string Null if no content rules match. String if $echo parameter is false and content rules match. 
 */
function woocommerce_conditional_content( $content_id = 0, $echo = true ) {
	WC_Conditional_Content_Display::instance()->template_display( $content_id, $echo );
}

/**
 * Creates an instance of a rule object
 * @global array $woocommerce_conditional_content_rules
 * @param type $rule_type The slug of the rule type to load. 
 * @return WC_Conditional_Content_Rule_Base or superclass of WC_Conditional_Content_Rule_Base
 */
function woocommerce_conditional_content_get_rule_object( $rule_type ) {
	global $woocommerce_conditional_content_rules;

	if ( isset( $woocommerce_conditional_content_rules[$rule_type] ) ) {
		return $woocommerce_conditional_content_rules[$rule_type];
	}

	$class = 'WC_Conditional_Content_Rule_' . $rule_type;
	if ( class_exists( $class ) ) {
		$woocommerce_conditional_content_rules[$rule_type] = new $class;
		return $woocommerce_conditional_content_rules[$rule_type];
	} else {
		return null;
	}
}

/**
 * Creates an instance of an input object
 * @global type $woocommerce_conditional_content_inputs
 * @param type $input_type The slug of the input type to load
 * @return type An instance of an WC_Conditional_Content_Input object type
 */
function woocommerce_conditional_content_get_input_object( $input_type ) {
	global $woocommerce_conditional_content_inputs;

	if ( isset( $woocommerce_conditional_content_inputs[$input_type] ) ) {
		return $woocommerce_conditional_content_inputs[$input_type];
	}

	$class = 'WC_Conditional_Content_Input_' . str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $input_type ) ) );
	if ( class_exists( $class ) ) {
		$woocommerce_conditional_content_inputs[$input_type] = new $class;
	} else {
		$woocommerce_conditional_content_inputs[$input_type] = apply_filters( 'woocommerce_conditional_content_get_input_object', $input_type );
	}

	return $woocommerce_conditional_content_inputs[$input_type];
}