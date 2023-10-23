<?php
/**
 * Badge Rules Functions
 *
 * @author   YITH <plugins@yithemes.com>
 * @package  YITH\BadgeManagementPremium\Functions
 * @since    2.0
 */

if ( ! function_exists( 'yith_wcbm_get_badge_rule' ) ) {
	/**
	 * Get Badge Rule
	 *
	 * @param int|WP_Post|YITH_WCBM_Badge_Rule $rule Badge Rule Identifier.
	 *
	 * @return false|YITH_WCBM_Badge_Rule|YITH_WCBM_Associative_Badge_Rule
	 */
	function yith_wcbm_get_badge_rule( $rule = false ) {
		if ( $rule instanceof YITH_WCBM_Badge_Rule ) {
			return $rule;
		}
		if ( ! $rule ) {
			$rule = get_post();
		}

		$class_name = yith_wcbm_get_badge_rule_classname( $rule );
		$rule       = new $class_name( $rule );

		return $rule->get_id() ? $rule : false;
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_rule_classname' ) ) {
	/**
	 * Get Badge Rule Classname
	 *
	 * @param int|WP_Post|YITH_WCBM_Badge_Rule $rule Badge Rule.
	 *
	 * @return string
	 */
	function yith_wcbm_get_badge_rule_classname( $rule ) {
		if ( ! $rule ) {
			$rule = get_post();
		}
		$type = '';
		if ( $rule instanceof WP_Post ) {
			$type = get_post_meta( $rule->ID, '_type', true );
		} elseif ( absint( $rule ) > 0 ) {
			$type = get_post_meta( $rule, '_type', true );
		}
		$type       = ! $type && isset( $_REQUEST['_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_type'] ) ) : $type; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$type       = ! $type && isset( $_REQUEST['yith_wcbm_badge_rule']['_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['yith_wcbm_badge_rule']['_type'] ) ) : $type; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$class_name = 'YITH_WCBM_Badge_Rule_' . implode( '_', array_map( 'ucfirst', explode( '-', strtolower( $type ) ) ) );

		return class_exists( $class_name ) ? $class_name : 'YITH_WCBM_Badge_Rule_Product';
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_rules' ) ) {
	/**
	 * Get Badge Rules
	 *
	 * @param array  $args   Get Post Args.
	 * @param string $return Return type [ids, objects].
	 *
	 * @return int[]|WP_Post[]|YITH_WCBM_Badge_Rule[]|YITH_WCBM_Associative_Badge_Rule[]
	 */
	function yith_wcbm_get_badge_rules( $args = array(), $return = 'ids' ) {
		$defaults = array(
			'posts_per_page' => 10,
			'fields'         => 'ids',
		);
		$args     = wp_parse_args( $args, $defaults );

		$args['post_type'] = YITH_WCBM_Post_Types_Premium::$badge_rule;

		$rules = array_filter( get_posts( $args ) );

		if ( 'objects' === $return ) {
			$rules = array_map( 'yith_wcbm_get_badge_rule', $rules );
		}

		return apply_filters( 'yith_wcbm_get_badge_rules', $rules, $args, $return );
	}
}

if ( ! function_exists( 'yith_wcbm_is_badge_rule_valid' ) ) {
	/**
	 * Check if badge rule is valid for the current user and product
	 *
	 * @param int $rule_id    Rule ID.
	 * @param int $product_id Product ID.
	 * @param int $user_id    User ID.
	 *
	 * @return bool
	 */
	function yith_wcbm_is_badge_rule_valid( $rule_id, $product_id = false, $user_id = false ) {
		static $rules = array();

		$product    = wc_get_product( $product_id );
		$product_id = $product ? $product->get_id() : false;

		if ( ! $user_id ) {
			$user_id = is_user_logged_in() ? get_current_user_id() : 'guest';
		}

		$rule_key = md5( (string) $rule_id . (string) $product_id . (string) $user_id );

		if ( ! array_key_exists( $rule_key, $rules ) ) {
			$rule               = yith_wcbm_get_badge_rule( $rule_id );
			$rules[ $rule_key ] = apply_filters( 'yith_wcbm_is_badge_rule_valid', $rule && $rule->is_valid( $product_id, $user_id ), $rule_id, $product_id, $user_id );
		}

		return $rules[ $rule_key ];
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_rules_types' ) ) {
	/**
	 * Get Badge rules types
	 *
	 * @return array
	 */
	function yith_wcbm_get_badge_rules_types() {
		static $rules_types = array();
		if ( ! $rules_types && file_exists( YITH_WCBM_DIR . '/plugin-options/badge-rules-types.php' ) ) {
			$rules_types = include YITH_WCBM_DIR . '/plugin-options/badge-rules-types.php';
		}

		return $rules_types;
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_rules_types_ids' ) ) {
	/**
	 * Get Badge Rules Types
	 *
	 * @return string[]
	 */
	function yith_wcbm_get_badge_rules_types_ids() {
		return array_keys( yith_wcbm_get_badge_rules_types() );
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_rule_type_fields' ) ) {
	/**
	 * Get Rule Type Fields
	 *
	 * @param string $rule_type Rule Type.
	 *
	 * @return array
	 */
	function yith_wcbm_get_badge_rule_type_fields( $rule_type = null ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$rule_fields = array();
		$rules_types = yith_wcbm_get_badge_rules_types();
		if ( ! $rule_type ) {
			if ( ! empty( $_GET['_type'] ) ) {
				$rule_type = sanitize_text_field( wp_unslash( $_GET['_type'] ) );
			} else {
				global $post;
				$rule      = $post ?? ( ! empty( $_GET['post'] ) ? get_post( absint( $_GET['post'] ) ) : false );
				$rule_type = $rule ? get_post_meta( $rule->ID, '_type', true ) : false;
			}
		}
		if ( $rule_type && array_key_exists( $rule_type, $rules_types ) && ! empty( $rules_types[ $rule_type ]['fields'] ) ) {
			$rule_fields                = $rules_types[ $rule_type ]['fields'];
			$rule_fields['type']['std'] = $rule_type;
		}

		return $rule_fields;
		// phpcs:enable
	}
}

