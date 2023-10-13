<?php
/**
 * Resources tab in WC Product Panel
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 * @var string                   $prod_type       The booking product type
 * @var int                      $post_id         The post ID
 *
 * @package YITH\Booking\Modules\Resources\Views
 */

defined( 'YITH_WCBK' ) || exit;

?>
<div class="yith-wcbk-product-metabox-options-panel yith-plugin-ui options_group show_if_<?php echo esc_attr( $prod_type ); ?>">

	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Resources Settings', 'yith-booking-for-woocommerce' ); ?></h3>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<?php
			yith_wcbk_form_field(
				array(
					'title'  => __( 'Enable resources', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Enable this option to assign resources to this product.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'yith-field' => true,
						'type'       => 'onoff',
						'value'      => wc_bool_to_string( $booking_product ? $booking_product->get_enable_resources( 'edit' ) : false ),
						'id'         => '_yith_booking_enable_resources',
					),
				)
			);
			?>

			<div class="yith-wcbk-show-conditional" data-field-id="_yith_booking_enable_resources" data-value="yes">
				<?php
				yith_wcbk_form_field(
					array(
						'title'  => __( 'Resource assignment', 'yith-booking-for-woocommerce' ),
						'desc'   => __( 'Choose the resource assignment type.', 'yith-booking-for-woocommerce' ),
						'fields' => array(
							'yith-field'                     => true,
							'yith-wcbk-field-show-container' => false,
							'class'                          => 'select short',
							'type'                           => 'select',
							'value'                          => $booking_product ? $booking_product->get_resource_assignment( 'edit' ) : 'customer-select-one',
							'id'                             => '_yith_booking_resource_assignment',
							'options'                        => array(
								'customer-select-one'      => __( 'Customer can select one resource', 'yith-booking-for-woocommerce' ),
								'customer-select-more'     => __( 'Customer can select one or more resources', 'yith-booking-for-woocommerce' ),
								'automatically-assign-one' => __( 'Automatically assign one resource', 'yith-booking-for-woocommerce' ),
								'assign-all'               => __( 'Assign all resources', 'yith-booking-for-woocommerce' ),
							),
						),
					)
				);

				yith_wcbk_form_field(
					array(
						'title'  => __( 'Required', 'yith-booking-for-woocommerce' ),
						'desc'   => __( 'Enable if you want to force customers to choose one resource.', 'yith-booking-for-woocommerce' ),
						'class'  => 'yith-wcbk-show-conditional',
						'data'   => array(
							'field-id' => '_yith_booking_resource_assignment',
							'value'    => 'customer-select-one',
						),
						'fields' => array(
							'yith-field' => true,
							'type'       => 'onoff',
							'value'      => wc_bool_to_string( $booking_product ? $booking_product->get_resource_is_required( 'edit' ) : true ),
							'id'         => '_yith_booking_resource_is_required',
						),
					)
				);

				yith_wcbk_form_field(
					array(
						'title'  => __( 'Resources layout', 'yith-booking-for-woocommerce' ),
						'desc'   => implode(
							'<br />',
							array(
								__( 'Choose to show the resources either in a dropdown menu or by listing them all in the booking form.', 'yith-booking-for-woocommerce' ),
								__( 'Select "default" to use the resources default layout set in the plugin settings.', 'yith-booking-for-woocommerce' ),
							)
						),
						'class'  => 'yith-wcbk-show-conditional',
						'data'   => array(
							'field-id' => '_yith_booking_resource_assignment',
							'value'    => 'customer-select-one|customer-select-more',
						),
						'fields' => array(
							'yith-field'                     => true,
							'yith-wcbk-field-show-container' => false,
							'class'                          => 'select short',
							'type'                           => 'select',
							'value'                          => $booking_product ? $booking_product->get_resources_layout( 'edit' ) : 'default',
							'id'                             => '_yith_booking_resources_layout',
							'options'                        => array(
								'default'  => __( 'Default', 'yith-booking-for-woocommerce' ),
								'dropdown' => __( 'Dropdown', 'yith-booking-for-woocommerce' ),
								'list'     => __( 'List', 'yith-booking-for-woocommerce' ),
							),
						),
					)
				);

				yith_wcbk_form_field(
					array(
						'title'  => __( 'Label', 'yith-booking-for-woocommerce' ),
						'desc'   => __( 'Choose the label for the resources. This will be used in cart and totals site-wide.', 'yith-booking-for-woocommerce' ),
						'fields' => array(
							'yith-field'        => true,
							'type'              => 'text',
							'value'             => $booking_product ? $booking_product->get_resources_label( 'edit' ) : '',
							'id'                => '_yith_booking_resources_label',
							'custom_attributes' => array(
								'placeholder' => __( 'Resource', 'yith-booking-for-woocommerce' ),
							),
						),
					)
				);

				yith_wcbk_form_field(
					array(
						'title'  => __( 'Label (Product page)', 'yith-booking-for-woocommerce' ),
						'desc'   => __( 'Choose the label for the resources as it will appear before the drop-down menu on the product page. If not set, the default Label will be used.', 'yith-booking-for-woocommerce' ),
						'class'  => 'yith-wcbk-show-conditional',
						'data'   => array(
							'field-id' => '_yith_booking_resource_assignment',
							'value'    => 'customer-select-one|customer-select-more',
						),
						'fields' => array(
							'yith-field'        => true,
							'type'              => 'text',
							'value'             => $booking_product ? $booking_product->get_resources_field_label( 'edit' ) : '',
							'id'                => '_yith_booking_resources_field_label',
							'custom_attributes' => array(
								'placeholder' => __( 'Resource', 'yith-booking-for-woocommerce' ),
							),
						),
					)
				);

				yith_wcbk_form_field(
					array(
						'title'  => __( 'Field placeholder', 'yith-booking-for-woocommerce' ),
						'desc'   => __( 'Choose the placeholder for the resources drop-down menu.', 'yith-booking-for-woocommerce' ),
						'class'  => 'yith-wcbk-show-conditional',
						'data'   => array(
							'rules' => wp_json_encode(
								array(
									array(
										'_yith_booking_resource_assignment'  => 'customer-select-one', // AND.
										'_yith_booking_resource_is_required' => 'no',
									), // OR.
									array(
										'_yith_booking_resource_assignment' => 'customer-select-more',
									),
								)
							),
						),
						'fields' => array(
							'yith-field' => true,
							'type'       => 'text',
							'value'      => $booking_product ? $booking_product->get_resources_field_placeholder( 'edit' ) : '',
							'id'         => '_yith_booking_resources_field_placeholder',
						),
					)
				);
				?>
			</div>
		</div>
	</div>

	<div class="yith-wcbk-settings-section yith-wcbk-show-conditional" data-field-id="_yith_booking_enable_resources" data-value="yes">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Resources', 'yith-booking-for-woocommerce' ); ?></h3>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<?php if ( current_user_can( 'edit_' . YITH_WCBK_Post_Types::RESOURCE . 's' ) && current_user_can( 'create_' . YITH_WCBK_Post_Types::RESOURCE . 's' ) ) : ?>
				<div class="yith-wcbk-settings-section__description">
					<?php
					$create_url    = add_query_arg( 'post_type', YITH_WCBK_Post_Types::RESOURCE, admin_url( 'edit.php' ) );
					$settings_path = sprintf(
						'YITH > Booking > %s > %s',
						_x( 'Configuration', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
						_x( 'Resources', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' )
					);
					echo sprintf(
					// translators: %s is the settings path (YITH > Booking > Configuration > Resources).
						esc_html__( 'You can create resources in %s', 'yith-booking-for-woocommerce' ),
						'<a href="' . esc_url( $create_url ) . '">' . esc_html( $settings_path ) . '</a>'
					);
					?>
				</div>
			<?php endif; ?>

			<?php
			yith_wcbk_get_module_view( 'resources', 'product-tabs/resources-tab/resources.php', compact( 'booking_product' ) );
			?>
		</div>
	</div>
</div>

<?php
yith_wcbk_get_module_view( 'resources', 'product-tabs/resources-tab/templates.php' );
?>
