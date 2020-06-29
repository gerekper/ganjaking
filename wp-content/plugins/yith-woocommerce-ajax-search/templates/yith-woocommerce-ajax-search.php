<?php
/**
 * YITH WooCommerce Ajax Search template
 *
 * @author YITH
 * @package YITH WooCommerce Ajax Search
 * @version 1.1.1
 */

if ( ! defined( 'YITH_WCAS' ) ) {
	exit; } // Exit if accessed directly
wp_enqueue_script( 'yith_wcas_frontend' );

?>

<div class="yith-ajaxsearchform-container">
	<form role="search" method="get" id="yith-ajaxsearchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<div>
			<label class="screen-reader-text" for="yith-s"><?php esc_html_e( 'Search for:', 'yith-woocommerce-ajax-search' ); ?></label>
			<input type="search" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" id="yith-s" class="yith-s" placeholder="<?php echo esc_attr( get_option( 'yith_wcas_search_input_label' ) ); ?>" data-loader-icon="<?php echo esc_attr( str_replace( '"', '', apply_filters( 'yith_wcas_ajax_search_icon', '' ) ) ); ?>" data-min-chars="<?php echo esc_attr( get_option( 'yith_wcas_min_chars' ) ); ?>" />
			<input type="submit" id="yith-searchsubmit" value="<?php echo esc_attr( get_option( 'yith_wcas_search_submit_label' ) ); ?>" />
			<input type="hidden" name="post_type" value="product" />
			<?php do_action( 'wpml_add_language_form_field' ); ?>
		</div>
	</form>
</div>
