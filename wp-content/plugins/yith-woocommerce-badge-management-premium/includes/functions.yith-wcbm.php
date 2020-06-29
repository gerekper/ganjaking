<?php
/**
 * Functions
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @package YITH WooCommerce Badge Management
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.


if ( ! function_exists( 'yith_wcbm_get_view' ) ) {
	/**
	 * Print a view
	 *
	 * @param string $view The view.
	 * @param array  $args Arguments.
	 */
	function yith_wcbm_get_view( $view, $args = array() ) {
		$view_path = trailingslashit( YITH_WCBM_VIEWS_PATH ) . $view;
		extract( $args ); // @codingStandardsIgnoreLine
		if ( file_exists( $view_path ) ) {
			include $view_path;
		}
	}
}

/**
 * Print the content of metabox options [Free Version]
 *
 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
 */
if ( ! function_exists( 'yith_wcbm_metabox_options_content' ) ) {
	/**
	 * Print the content of metabox options [Free Version]
	 *
	 * @param array $args Arguments.
	 *
	 * @deprecated since 1.4.0 use yith_wcbm_get_view instead
	 */
	function yith_wcbm_metabox_options_content( $args ) {
		yith_wcbm_get_view( 'metaboxes/badge-settings.php', $args );
	}
}

if ( ! function_exists( 'yith_wcbm_product_has_badges' ) ) {
	/**
	 * Has the product some badges?
	 *
	 * @param WC_Product $product The Product.
	 *
	 * @return bool
	 * @since 1.3.26
	 */
	function yith_wcbm_product_has_badges( $product ) {
		$bm_meta  = $product->get_meta( '_yith_wcbm_product_meta' );
		$id_badge = ( isset( $bm_meta['id_badge'] ) ) ? $bm_meta['id_badge'] : '';

		return ! ! $id_badge;
	}
}

/**
 * Print the content of badge in frontend [Free Version]
 *
 * @return   string
 * @since    1.0
 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
 */

if ( ! function_exists( 'yith_wcbm_get_badge' ) ) {
	/**
	 * Get the badge
	 *
	 * @param int $badge_id   The badge ID.
	 * @param int $product_id The product ID.
	 *
	 * @return string
	 */
	function yith_wcbm_get_badge( $badge_id, $product_id ) {
		if ( ! $badge_id || ! $product_id ) {
			return '';
		}

		$badge_html = '';

		$bm_meta = get_post_meta( $badge_id, '_badge_meta', true );
		$default = array(
			'type'              => 'text',
			'text'              => '',
			'txt_color_default' => '#000000',
			'txt_color'         => '#000000',
			'bg_color_default'  => '#2470FF',
			'bg_color'          => '#2470FF',
			'width'             => '100',
			'height'            => '50',
			'position'          => 'top-left',
			'image_url'         => '',
			'product_id'        => $product_id,
			'id_badge'          => $badge_id,
		);

		$args = wp_parse_args( $bm_meta, $default );
		$args = apply_filters( 'yith_wcbm_badge_content_args', $args );

		ob_start();
		yith_wcbm_get_template( 'badge-content.php', $args );
		$badge_html .= ob_get_clean();

		return apply_filters( 'yith_wcbm_get_badge', $badge_html, $badge_id, $product_id );

	}
}

if ( ! function_exists( 'yith_wcbm_get_template' ) ) {
	/**
	 * Get Template
	 *
	 * @param string $template The template.
	 * @param array  $args     Arguments.
	 */
	function yith_wcbm_get_template( $template, $args ) {
		extract( $args ); // @codingStandardsIgnoreLine
		$file_path = YITH_WCBM_TEMPLATE_PATH . '/' . $template;

		file_exists( $file_path ) && include $file_path;
	}
}


if ( ! function_exists( 'yith_wcbm_wpml_register_string' ) ) {
	/**
	 * Register a string in WPML translations.
	 *
	 * @param string $context The Context.
	 * @param string $name    The name.
	 * @param string $value   The value.
	 *
	 * @since  2.0.0
	 */
	function yith_wcbm_wpml_register_string( $context, $name, $value ) {
		do_action( 'wpml_register_single_string', $context, $name, $value );
	}
}

if ( ! function_exists( 'yith_wcbm_wpml_string_translate' ) ) {
	/**
	 * Get a string translation
	 *
	 * @param string $context       The context.
	 * @param string $name          The name.
	 * @param string $default_value The default value.
	 *
	 * @return string The translated string
	 * @since  2.0.0
	 */
	function yith_wcbm_wpml_string_translate( $context, $name, $default_value ) {
		return apply_filters( 'wpml_translate_single_string', $default_value, $context, $name );
	}
}

if ( ! function_exists( 'yith_wcbm_create_capabilities' ) ) {
	/**
	 * Create a capability array.
	 *
	 * @param array|string $capability_type The capability type.
	 *
	 * @return array
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	function yith_wcbm_create_capabilities( $capability_type ) {
		if ( ! is_array( $capability_type ) ) {
			$capability_type = array( $capability_type, $capability_type . 's' );
		}

		list( $singular_base, $plural_base ) = $capability_type;

		$capabilities = array(
			'edit_' . $singular_base           => true,
			'read_' . $singular_base           => true,
			'delete_' . $singular_base         => true,
			'edit_' . $plural_base             => true,
			'edit_others_' . $plural_base      => true,
			'publish_' . $plural_base          => true,
			'read_private_' . $plural_base     => true,
			'delete_' . $plural_base           => true,
			'delete_private_' . $plural_base   => true,
			'delete_published_' . $plural_base => true,
			'delete_others_' . $plural_base    => true,
			'edit_private_' . $plural_base     => true,
			'edit_published_' . $plural_base   => true,
		);

		return $capabilities;
	}
}

if ( ! function_exists( 'yith_wcbm_get_badges' ) ) {
	/**
	 * Get badges
	 *
	 * @param array $args Arguments.
	 *
	 * @return int[]|WP_Post[]
	 */
	function yith_wcbm_get_badges( $args = array() ) {
		$default_args = array(
			'posts_per_page' => - 1,
			'post_type'      => 'yith-wcbm-badge',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post_status'    => 'publish',
			'fields'         => 'ids',
		);

		$args = wp_parse_args( $args, $default_args );

		return get_posts( $args );
	}
}

if ( ! function_exists( 'yith_wcmb_is_wpml_parent_based_on_default_language' ) ) {
	/**
	 * Is WPML parent based on default language?
	 *
	 * @return bool
	 */
	function yith_wcmb_is_wpml_parent_based_on_default_language() {
		return ! ! apply_filters( 'yith_wcmb_is_wpml_parent_based_on_default_language', false );
	}
}

if ( ! function_exists( 'yith_wcmb_wpml_autosync_product_badge_translations' ) ) {
	/**
	 * Does WPML autodync product badge translations?
	 *
	 * @return bool
	 */
	function yith_wcmb_wpml_autosync_product_badge_translations() {
		return ! ! apply_filters( 'yith_wcmb_wpml_autosync_product_badge_translations', false );
	}
}

if ( ! function_exists( 'yith_wcmb_is_frontend_manager' ) ) {
	/**
	 * Is this a page of YITH Frontend Manager?
	 *
	 * @return bool
	 */
	function yith_wcmb_is_frontend_manager() {
		$fm = function_exists( 'YITH_Frontend_Manager' ) ? YITH_Frontend_Manager() : false;
		if ( $fm && isset( $fm->gui ) && is_object( $fm->gui ) && is_callable( array( $fm->gui, 'is_main_page' ) ) ) {
			return $fm->gui->is_main_page();
		}

		return false;
	}
}
