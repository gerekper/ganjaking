<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


require_once dirname( __FILE__ ) . '/vendor/autoload.php';


$gm_vp_controller = new \GroovyMenu\VirtualPagesController( new \GroovyMenu\VirtualPagesTemplateLoader() );
add_action( 'init', array( $gm_vp_controller, 'init' ) );
add_filter( 'do_parse_request', array( $gm_vp_controller, 'dispatch' ), PHP_INT_MAX, 2 );
add_action( 'loop_end', 'gm_action_check_query_virtual_page' );
function gm_action_check_query_virtual_page( $query ) {
	if ( $query instanceof \WP_Query ) {
		if ( isset( $query->virtual_page ) && ! empty( $query->virtual_page ) ) {
			$query->virtual_page = null;
		}
	}
}

add_filter( 'the_permalink', function ( $permalink ) {
	global $post, $wp_query;
	if (
		$wp_query->is_page
		&& isset( $wp_query->virtual_page )
		&& $wp_query->virtual_page instanceof \GroovyMenu\VirtualPagesPage
		&& isset( $post->is_virtual )
		&& $post->is_virtual
	) {
		$permalink = home_url( $wp_query->virtual_page->getUrl() );
	}

	return $permalink;
} );

add_action( 'plugins_loaded', array( 'GroovyMenu\VirtualPagesPageTemplate', 'getInstance' ) );


// Add virtual page.
add_action( 'gm_add_virtual_page', function ( $controller ) {

	$gm_preset_id      = null;
	$gm_action         = null;
	$gm_action_preview = null;

	// @codingStandardsIgnoreStart
	if ( isset( $_GET['groovy-menu-preset'] ) ) {
		$gm_action = esc_attr( sanitize_text_field( wp_unslash( $_GET['groovy-menu-preset'] ) ) );
	}
	if ( isset( $_GET['id'] ) ) {
		$gm_preset_id = esc_attr( sanitize_text_field( wp_unslash( $_GET['id'] ) ) );
	}
	if ( isset( $_GET['gm_action_preview'] ) ) {
		$gm_action_preview = esc_attr( sanitize_text_field( wp_unslash( $_GET['gm_action_preview'] ) ) );
	}
	// @codingStandardsIgnoreEnd

	if ( $gm_action && $gm_action_preview && $gm_preset_id ) {
		$controller->addPage(
			new \GroovyMenu\VirtualPagesPage(
				'/',
				esc_html__( 'Preset Preview', 'groovy-menu' ) . ' #' . $gm_preset_id,
				'template/Preview.php'
			)
		);
	}
} );
