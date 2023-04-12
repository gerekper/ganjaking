<?php
/**
 * View for displaying saved Global EPOs
 *
 * @package Extra Product Options/Admin/Views
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

global $post, $post_id, $tm_is_ajax, $woocommerce;
$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $post_id );
if ( ! $wpml_is_original_product ) {
	$tm_meta_cpf = themecomplete_get_post_meta( floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) ), 'tm_meta_cpf', true );
} else {
	$tm_meta_cpf = themecomplete_get_post_meta( $post_id, 'tm_meta_cpf', true );
}
$tm_meta_cpf_mode = isset( $tm_meta_cpf['mode'] ) ? $tm_meta_cpf['mode'] : '';
if ( THEMECOMPLETE_EPO()->tm_epo_global_hide_product_builder_mode === 'yes' && THEMECOMPLETE_EPO()->tm_epo_global_hide_product_normal_mode !== 'yes' ) {
	$tm_meta_cpf_mode = 'local';
}
if ( THEMECOMPLETE_EPO()->tm_epo_global_hide_product_normal_mode === 'yes' && THEMECOMPLETE_EPO()->tm_epo_global_hide_product_builder_mode !== 'yes' ) {
	$tm_meta_cpf_mode = 'builder';
}
if ( THEMECOMPLETE_EPO()->tm_epo_global_hide_product_settings !== 'yes' && THEMECOMPLETE_EPO()->tm_epo_global_hide_product_normal_mode === 'yes' && THEMECOMPLETE_EPO()->tm_epo_global_hide_product_builder_mode === 'yes' ) {
	$tm_meta_cpf_mode = 'settings';
}
// Check for deprecated Normal mode.
$args   = [
	'post_type'   => THEMECOMPLETE_EPO_LOCAL_POST_TYPE,
	'post_status' => [ 'private', 'publish' ],
	'numberposts' => -1,
	'orderby'     => 'menu_order',
	'order'       => 'asc',
	'post_parent' => $post_id,
];
$tmepos = $post_id ? THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args ) : false;
?>
<div id="tc-admin-extra-product-options" class="panel wc-metaboxes-wrapper">
	<div id="tc-admin-extra-product-options-inner">
		<div class="tm-mode-selector">
			<input type="hidden" value="<?php echo esc_attr( $tm_meta_cpf_mode ); ?>" id="tm-meta-cpf-mode" name="tm_meta_cpf[mode]">
			<p class="form-field tm-mode-select">
				<span class="
				<?php
				if ( THEMECOMPLETE_EPO()->tm_epo_global_hide_product_builder_mode === 'yes' ) {
					echo 'tm-hidden ';
				}
				?>
				button button-primary button-large tc-select-mode tc-builder-select"><i class="tcfa tcfa-th-large"></i><?php esc_html_e( 'Builder', 'woocommerce-tm-extra-product-options' ); ?></span>
					<span class="
					<?php
					if ( empty( $tmepos ) || THEMECOMPLETE_EPO()->tm_epo_global_hide_product_normal_mode === 'yes' ) {
						echo 'tm-hidden ';
					}
					?>
					button button-primary button-large tc-select-mode tc-local-select"><i class="tcfa tcfa-th-list"></i><?php esc_html_e( 'Normal', 'woocommerce-tm-extra-product-options' ); ?></span>	
				<span class="
				<?php
				if ( THEMECOMPLETE_EPO()->tm_epo_global_hide_product_settings === 'yes' ) {
					echo 'tm-hidden ';
				}
				?>
				button button-primary button-large tc-select-mode tc-settings-select"><i class="tcfa tcfa-cog"></i><?php esc_html_e( 'Settings', 'woocommerce-tm-extra-product-options' ); ?></span>
			</p>
		</div>
		<div class="tm-mode-builder"><?php THEMECOMPLETE_EPO_ADMIN_GLOBAL()->tm_form_fields_builder_meta_box( $post ); ?></div>
		<div class="tm-mode-local tc-wrapper">
		<?php
		if ( THEMECOMPLETE_EPO_WPML()->is_original_product( $post_id ) ) {
			include 'html-tm-epo.php';
		} else {
			?>
			<div id="message" class="tm-inner inline woocommerce-message">
			<?php esc_html_e( 'To translate the strings for the local options please use WPML interface.', 'woocommerce-tm-extra-product-options' ); ?>
			</div>
			<?php
		}
		?>
		</div>
		<div class="tm-mode-settings tc-options-group woocommerce_options_panel tc-wrapper">
			<?php
			if ( THEMECOMPLETE_EPO_WPML()->is_original_product( $post_id ) ) {
				// Price display override.
				$tm_price_display_override      = isset( $tm_meta_cpf['price_display_override'] ) ? $tm_meta_cpf['price_display_override'] : '';
				$tm_price_display_override_sale = isset( $tm_meta_cpf['price_display_override_sale'] ) ? $tm_meta_cpf['price_display_override_sale'] : '';
				$tm_price_display_override_to   = isset( $tm_meta_cpf['price_display_override_to'] ) ? $tm_meta_cpf['price_display_override_to'] : '';
				$tm_price_display_mode          = isset( $tm_meta_cpf['price_display_mode'] ) ? $tm_meta_cpf['price_display_mode'] : 'none';
				THEMECOMPLETE_EPO_HTML()->create_field(
					[
						'message0x0_class' => 'overflow-show tm-epo-switch-wrapper price-display-mode-wrap',
						'wpmldisable'      => 1,
						'default'          => $tm_price_display_mode,
						'type'             => 'radio',
						'tags'             => [
							'class' => 'price-display-mode',
							'id'    => 'price_display_mode',
							'name'  => 'tm_meta_cpf[price_display_mode]',
						],
						'options'          => [
							[
								'text'  => esc_html__( 'None', 'woocommerce-tm-extra-product-options' ),
								'value' => 'none',
							],
							[
								'text'  => esc_html__( 'Price', 'woocommerce-tm-extra-product-options' ),
								'value' => 'price',
							],
							[
								'text'  => esc_html__( 'From', 'woocommerce-tm-extra-product-options' ),
								'value' => 'from',
							],
							[
								'text'  => esc_html__( 'Range', 'woocommerce-tm-extra-product-options' ),
								'value' => 'range',
							],
						],
						'label'            => esc_html__( 'Price display override', 'woocommerce-tm-extra-product-options' ),
						'desc'             => esc_html__( 'This will replace the displayed product price on shop/archive/product loop pages.', 'woocommerce-tm-extra-product-options' ),
						'extra_fields'     => [
							[
								'nodiv'             => 1,
								'type'              => 'input',
								'default'           => $tm_price_display_override,
								'input_type'        => 'text',
								'message0x0_class'  => 'overflow-show',
								'tags'              => [
									'id'    => 'tm_price_display_override',
									'name'  => 'tm_meta_cpf[price_display_override]',
									'class' => 'text wc_input_price',
								],
								'html_before_field' => '<span class="tm-choice-regular">' . esc_html__( 'Regular', 'woocommerce-tm-extra-product-options' ) . '</span><span class="tm-choice-from">' . esc_html__( 'From', 'woocommerce-tm-extra-product-options' ) . '</span>',
							],
							[
								'nodiv'             => 1,
								'type'              => 'input',
								'default'           => $tm_price_display_override_to,
								'input_type'        => 'text',
								'message0x0_class'  => 'overflow-show',
								'tags'              => [
									'id'    => 'tm_price_display_override_to',
									'name'  => 'tm_meta_cpf[price_display_override_to]',
									'class' => 'text wc_input_price',
								],
								'html_before_field' => '<span class="tm-choice-to">' . esc_html__( 'To', 'woocommerce-tm-extra-product-options' ) . '</span>',
							],
							[
								'nodiv'             => 1,
								'type'              => 'input',
								'default'           => $tm_price_display_override_sale,
								'input_type'        => 'text',
								'message0x0_class'  => 'overflow-show',
								'tags'              => [
									'id'    => 'tm_price_display_override_sale',
									'name'  => 'tm_meta_cpf[price_display_override_sale]',
									'class' => 'text wc_input_price',
								],
								'html_before_field' => '<span class="tm-choice-sale">' . esc_html__( 'Sale', 'woocommerce-tm-extra-product-options' ) . '</span>',
							],
						],
					],
					true
				);

				// Include additional Global forms.
				$args               = [
					'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
					'post_status' => [ 'publish' ], // get only enabled global extra options.
					'numberposts' => -1,
					'orderby'     => 'title',
					'order'       => 'asc',
				];
				$tmp_tmglobalprices = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );
				echo '<div class="message0x0 tc-clearfix">' .
					'<div class="message2x1">' .
					'<label for="tm_meta_cpf_exclude"><span>' . esc_html__( 'Include additional Global forms', 'woocommerce-tm-extra-product-options' ) . '</span></label>' .
					'<div class="messagexdesc">' . esc_attr( esc_html__( 'The forms you choose will be displayed alongside with the forms that the product already has.', 'woocommerce-tm-extra-product-options' ) ) . '</div>' .
					'</div>' .
					'<div class="message2x2">';
				if ( $tmp_tmglobalprices ) {
					echo '<div class="wp-tab-panel"><ul>';
					$wpml_tmp_tmglobalprices       = [];
					$wpml_tmp_tmglobalprices_added = [];
					foreach ( $tmp_tmglobalprices as $price ) {
						$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );
						if ( (float) $original_product_id === (float) $price->ID ) {
							$tm_global_forms = ( isset( $tm_meta_cpf['global_forms'] ) && is_array( $tm_meta_cpf['global_forms'] ) ) ? in_array( $price->ID, $tm_meta_cpf['global_forms'] ) : false; // phpcs:ignore WordPress.PHP.StrictInArray
							echo '<li><label>';
							echo '<input type="checkbox" value="' . esc_attr( $price->ID ) . '" id="tm_meta_cpf_global_forms_' . esc_attr( $price->ID ) . '" name="tm_meta_cpf[global_forms][]" class="checkbox" ';
							checked( $tm_global_forms, true, 1 );
							echo '>';
							echo ' ' . esc_html( $price->post_title ) . '</label></li>';
						}
					}
					echo '</ul></div>';
				}
				echo '</div></div>';

				// Ouput Exclude.
				$tm_exclude = isset( $tm_meta_cpf['exclude'] ) ? $tm_meta_cpf['exclude'] : '';
				THEMECOMPLETE_EPO_HTML()->create_field(
					[
						'type'       => 'checkbox',
						'default'    => $tm_exclude,
						'input_type' => 'checkbox',
						'label'      => esc_html__( 'Exclude from', 'woocommerce-tm-extra-product-options' ) . ' ' . esc_html( THEMECOMPLETE_EPO_POST_TYPES::instance()::$global_type->labels->name ),
						'desc'       => esc_html__( 'This will exclude any global forms assigned to this product except those defined in the previous setting.', 'woocommerce-tm-extra-product-options' ),
						'tags'       => [
							'id'    => 'tm_meta_cpf_exclude',
							'name'  => 'tm_meta_cpf[exclude]',
							'class' => 'checkbox',
							'value' => '1',
						],
					],
					true
				);

				// Ouput Price override.
				$price_override = isset( $tm_meta_cpf['price_override'] ) ? $tm_meta_cpf['price_override'] : '';
				THEMECOMPLETE_EPO_HTML()->create_field(
					[
						'type'       => 'checkbox',
						'default'    => $price_override,
						'input_type' => 'checkbox',
						'label'      => esc_html__( 'Override product price', 'woocommerce-tm-extra-product-options' ),
						'desc'       => esc_html__( 'This will override the product price with the price from the options if the total options price is greater then zero.', 'woocommerce-tm-extra-product-options' ),
						'tags'       => [
							'id'    => 'tm_meta_cpf_price_override',
							'name'  => 'tm_meta_cpf[price_override]',
							'class' => 'checkbox',
							'value' => '1',
						],
					],
					true
				);

				// Ouput Override display.
				$tm_override_display = isset( $tm_meta_cpf['override_display'] ) ? $tm_meta_cpf['override_display'] : '';
				THEMECOMPLETE_EPO_HTML()->create_field(
					[
						'type'    => 'select',
						'default' => $tm_override_display,
						'label'   => esc_html__( 'Override global display', 'woocommerce-tm-extra-product-options' ),
						'desc'    => esc_html__( 'This will override the display method only for this product.', 'woocommerce-tm-extra-product-options' ),
						'tags'    => [
							'id'   => 'tm_meta_cpf_override_display',
							'name' => 'tm_meta_cpf[override_display]',
						],
						'options' => [
							[
								'value' => '',
								'text'  => esc_html__( 'Use global setting', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'value' => 'normal',
								'text'  => esc_html__( 'Always show', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'value' => 'action',
								'text'  => esc_html__( 'Show only with an action hook', 'woocommerce-tm-extra-product-options' ),
							],
						],
					],
					true
				);

				// Ouput Override totals box.
				$tm_override_final_total_box = isset( $tm_meta_cpf['override_final_total_box'] ) ? $tm_meta_cpf['override_final_total_box'] : '';
				THEMECOMPLETE_EPO_HTML()->create_field(
					[
						'type'    => 'select',
						'default' => $tm_override_final_total_box,
						'label'   => esc_html__( 'Override Final total box', 'woocommerce-tm-extra-product-options' ),
						'desc'    => esc_html__( 'This will override the totals box display only for this product.', 'woocommerce-tm-extra-product-options' ),
						'tags'    => [
							'id'   => 'tm_meta_cpf_override_final_total_box',
							'name' => 'tm_meta_cpf[override_final_total_box]',
						],
						'options' => [
							[
								'value' => '',
								'text'  => esc_html__( 'Use global setting', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'value' => 'normal',
								'text'  => esc_html__( 'Show Both Final and Options total box', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'value' => 'options',
								'text'  => esc_html__( 'Show only Options total', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'value' => 'optionsiftotalnotzero',
								'text'  => esc_html__( 'Show only Options total if total is not zero', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'value' => 'final',
								'text'  => esc_html__( 'Show only Final total', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'value' => 'hideoptionsifzero',
								'text'  => esc_html__( 'Show Final box and hide Options if zero', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'value' => 'hideifoptionsiszero',
								'text'  => esc_html__( 'Hide Final total box if Options total is zero', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'value' => 'hideiftotaliszero',
								'text'  => esc_html__( 'Hide Final total box if total is zero', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'value' => 'hide',
								'text'  => esc_html__( 'Hide Final total box', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'value' => 'pxq',
								'text'  => esc_html__( 'Always show only Final total (Price x Quantity)', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'value' => 'disable_change',
								'text'  => esc_html__( 'Disable but change product prices', 'woocommerce-tm-extra-product-options' ),
							],
							[
								'value' => 'disable',
								'text'  => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
							],
						],
					],
					true
				);

				// Ouput Override enabled roles.
				$tm_override_enabled_roles = isset( $tm_meta_cpf['override_enabled_roles'] ) ? $tm_meta_cpf['override_enabled_roles'] : '';
				if ( ! is_array( $tm_override_enabled_roles ) ) {
					$tm_override_enabled_roles = [ $tm_override_enabled_roles ];
				}
				THEMECOMPLETE_EPO_HTML()->create_field(
					[
						'type'     => 'select',
						'multiple' => 'multiple',
						'default'  => $tm_override_enabled_roles,
						'label'    => esc_html__( 'Override enabled roles for this product', 'woocommerce-tm-extra-product-options' ),
						'desc'     => esc_html__( 'This will override which roles can see the options for this product.', 'woocommerce-tm-extra-product-options' ),
						'tags'     => [
							'id'    => 'tm_meta_cpf_override_enabled_roles',
							'name'  => 'tm_meta_cpf[override_enabled_roles]',
							'class' => 'multiselect',
						],
						'options'  => THEMECOMPLETE_EPO_HELPER()->convert_to_select_options( themecomplete_get_roles() ),
					],
					true
				);

				// Ouput Override disabled roles.
				$tm_override_disabled_roles = isset( $tm_meta_cpf['override_disabled_roles'] ) ? $tm_meta_cpf['override_disabled_roles'] : '';
				if ( ! is_array( $tm_override_disabled_roles ) ) {
					$tm_override_disabled_roles = [ $tm_override_disabled_roles ];
				}
				THEMECOMPLETE_EPO_HTML()->create_field(
					[
						'type'     => 'select',
						'multiple' => 'multiple',
						'default'  => $tm_override_disabled_roles,
						'label'    => esc_html__( 'Override enabled roles for this product', 'woocommerce-tm-extra-product-options' ),
						'desc'     => esc_html__( 'This will override which roles can see the options for this product.', 'woocommerce-tm-extra-product-options' ),
						'tags'     => [
							'id'    => 'tm_meta_cpf_override_disabled_roles',
							'name'  => 'tm_meta_cpf[override_disabled_roles]',
							'class' => 'multiselect',
						],
						'options'  => THEMECOMPLETE_EPO_HELPER()->convert_to_select_options( themecomplete_get_roles() ),
					],
					true
				);
			}
			?>
		</div>
	</div>
</div>
