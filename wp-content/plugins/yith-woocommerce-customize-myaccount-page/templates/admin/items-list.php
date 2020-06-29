<?php
/**
 * MY ACCOUNT ADMIN ENDPOINTS
 */
if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

$icon_list = yith_wcmap_get_icon_list();
$usr_roles = yith_wcmap_get_editable_roles();

?>

<?php do_action( 'yith_wcmap_before_manage_endpoint' ); ?>

	<tr valign="top">
		<td class="forminp <?php echo esc_attr( $option['id'] ); ?>_container">

			<div class="section-title-container">
				<h3 class="section-title"><?php esc_html_e( 'Manage Endpoints', 'yith-woocommerce-customize-myaccount-page' ); ?></h3>
				<div class="button-container">
					<button type="button" class="button add_new_field"
						data-target="group"><?php esc_html_e( 'Add group', 'yith-woocommerce-customize-myaccount-page' ); ?></button>
					<button type="button" class="button add_new_field"
						data-target="endpoint"><?php esc_html_e( 'Add endpoint', 'yith-woocommerce-customize-myaccount-page' ); ?></button>
					<button type="button" class="button add_new_field"
						data-target="link"><?php esc_html_e( 'Add link', 'yith-woocommerce-customize-myaccount-page' ); ?></button>
				</div>
			</div>

			<div class="dd endpoints-container">
				<ol class="dd-list endpoints">
					<!-- Endpoints -->
					<?php foreach ( $endpoints as $key => $endpoint ) {

						// build args array
						$args = array(
							'endpoint'  => $key,
							'options'   => $endpoint,
							'id'        => $option['id'],
							'icon_list' => $icon_list,
							'usr_roles' => $usr_roles,
							'value'     => isset( $value[ $key ] ) ? $value[ $key ] : array(),
						);

						// get type
						$type = isset( $value[ $key ] ) ? $value[ $key ]['type'] : 'endpoint';
						call_user_func( "yith_wcmap_admin_print_{$type}_field", $args );
					} ?>
				</ol>
			</div>

			<div class="new-field-form" style="display: none;">
				<label for="yith-wcmap-new-field"><?php echo esc_html_x( 'Name', 'Label for new endpoint title',
						'yith-woocommerce-customize-myaccount-page' ); ?>
					<input type="text" id="yith-wcmap-new-field" name="yith-wcmap-new-field" value="">
				</label>
				<div class="loader"></div>
				<p class="error-msg"></p>
			</div>

			<input type="hidden" class="endpoints-order" name="<?php echo esc_attr( $option['id'] ); ?>" value=""/>
			<input type="hidden" class="endpoint-to-remove" name="<?php echo esc_attr( $option['id'] ); ?>_to_remove" value=""/>
		</td>
	</tr>

<?php do_action( 'yith_wcmap_after_manage_endpoint' ); ?>