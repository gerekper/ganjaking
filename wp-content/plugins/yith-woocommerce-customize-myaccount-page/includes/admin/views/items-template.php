<?php
/**
 * My account admin endpoint item
 *
 * @package YITH WooCommerce Customize My Account Page
 * @since 3.0.0
 * @author YITH <plugins@yithemes.com>
 * @var array $actions An array of template actions.
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

foreach ( array_keys( $actions ) as $target ) : ?>
	<script type="text/template" id="tmpl-new-<?php echo esc_attr( $target ); ?>-item">
		<div class="item endpoint new-item-template">
			<div class="item-content">
				<!-- Content -->
				<div class="item-options">
					<table class="options-table form-table">
						<tbody>
						<?php
						foreach ( yith_wcmap_admin_get_fields( $target ) as $field => $field_options ) :
							if ( 'content_position' === $field ) {
								$field_options['type']    = 'hidden';
								$field_options['default'] = 'override';
							}
							yith_wcmap_admin_print_single_field( $field_options, $field, 'yith_wcmap_endpoint_new', '' );
						endforeach;
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</script>
	<?php
endforeach;
