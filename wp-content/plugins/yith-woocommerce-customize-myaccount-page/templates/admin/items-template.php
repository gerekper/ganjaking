<?php
/**
 * My account admin endpoint item
 *
 * @package YITH WooCommerce Customize My Account Page
 * @since 3.0.0
 * @author YITH
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

?>
<script type="text/template" id="tmpl-new-endpoint-item">
<div class="item endpoint new-item-template">
	<div class="item-content">
		<!-- Content -->
		<div class="item-options">
			<table class="options-table form-table">
				<tbody>
				<?php
				foreach ( yith_wcmap_admin_get_fields( 'endpoint' ) as $field => $field_options ) :
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
<script type="text/template" id="tmpl-new-link-item">
<div class="item endpoint new-item-template">
	<div class="item-content">
		<!-- Content -->
		<div class="item-options">
			<table class="options-table form-table">
				<tbody>
				<?php
				foreach ( yith_wcmap_admin_get_fields( 'link' ) as $field => $field_options ) :
					yith_wcmap_admin_print_single_field( $field_options, $field, 'yith_wcmap_endpoint_new', '' );
				endforeach;
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>
</script>
<script type="text/template" id="tmpl-new-group-item">
<div class="item endpoint new-item-template">
	<div class="item-content">
		<!-- Content -->
		<div class="item-options">
			<table class="options-table form-table">
				<tbody>
				<?php
				foreach ( yith_wcmap_admin_get_fields( 'group' ) as $field => $field_options ) :
					yith_wcmap_admin_print_single_field( $field_options, $field, 'yith_wcmap_endpoint_new', '' );
				endforeach;
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>
</script>
