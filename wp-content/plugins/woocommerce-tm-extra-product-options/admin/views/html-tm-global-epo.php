<?php
/**
 * View for displaying saved TM Global EPOs
 *
 * @package Extra Product Options/Admin/Views
 * @version 4.8
 */

defined( 'ABSPATH' ) || exit;

global $post, $post_id, $tm_is_ajax, $woocommerce;
$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $post_id );
if ( ! $wpml_is_original_product ) {
	$tm_meta_cpf = themecomplete_get_post_meta( floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) ), 'tm_meta_cpf', TRUE );
} else {
	$tm_meta_cpf = themecomplete_get_post_meta( $post_id, 'tm_meta_cpf', TRUE );
}
$tm_meta_cpf_mode = isset( $tm_meta_cpf['mode'] ) ? $tm_meta_cpf['mode'] : '';
if ( THEMECOMPLETE_EPO()->tm_epo_global_hide_product_builder_mode == "yes" ) {
	$tm_meta_cpf_mode = "local";
}
if ( THEMECOMPLETE_EPO()->tm_epo_global_hide_product_normal_mode == "yes" ) {
	$tm_meta_cpf_mode = "builder";
}

// Check for deprecate Normal mode
$args   = array(
	'post_type'   => THEMECOMPLETE_EPO_LOCAL_POST_TYPE,
	'post_status' => array( 'private', 'publish' ),
	'numberposts' => - 1,
	'orderby'     => 'menu_order',
	'order'       => 'asc',
	'post_parent' => $post_id
);
$tmepos = get_posts( $args );
?>
<div id="tm_extra_product_options" class="panel wc-metaboxes-wrapper">
    <div id="tm_extra_product_options_inner">
        <div class="tm_mode_selector">
            <input type="hidden" value="<?php echo esc_attr( $tm_meta_cpf_mode ); ?>" id="tm_meta_cpf_mode" name="tm_meta_cpf[mode]">
            <p class="form-field tm_mode_select">
                <span class="<?php if ( THEMECOMPLETE_EPO()->tm_epo_global_hide_product_builder_mode == "yes" ) {
	                echo 'tm-hidden ';
                } ?>button button-primary button-large tm_select_mode tm_builder_select"><i class="tcfa tcfa-th-large"></i><?php esc_html_e( 'Builder', 'woocommerce-tm-extra-product-options' ); ?></span>
                    <span class="<?php if ( THEMECOMPLETE_EPO()->tm_epo_global_hide_product_normal_mode == "yes" ) {
						echo 'tm-hidden ';
					} ?>button button-primary button-large tm_select_mode tm_local_select"><i class="tcfa tcfa-th-list"></i><?php esc_html_e( 'Normal', 'woocommerce-tm-extra-product-options' ); ?></span>	
                <span class="<?php if ( THEMECOMPLETE_EPO()->tm_epo_global_hide_product_settings == "yes" ) {
					echo 'tm-hidden ';
				} ?>button button-primary button-large tm_select_mode tm_settings_select"><i class="tcfa tcfa-cog"></i><?php esc_html_e( 'Settings', 'woocommerce-tm-extra-product-options' ); ?></span>
            </p>
        </div>
        <div class="tm_mode_builder"><?php THEMECOMPLETE_EPO_ADMIN_GLOBAL()->tm_form_fields_builder_meta_box( $post ); ?></div>
            <div class="tm_mode_local tm_wrapper">
			<?php
			if ( THEMECOMPLETE_EPO_WPML()->is_original_product( $post_id ) ) {
				include( 'html-tm-epo.php' );
			} else {
				?>
                <div id="message" class="tm-inner inline woocommerce-message">
				<?php esc_html_e( 'To translate the strings for the local options please use WPML interface.', 'woocommerce-tm-extra-product-options' ); ?>
                </div><?php
			}
			?>
            </div>
        <div class="tm_mode_settings tm_options_group woocommerce_options_panel tm_wrapper">
			<?php
			if ( THEMECOMPLETE_EPO_WPML()->is_original_product( $post_id ) ) {
				// Include additional Global forms 
				$args               = array(
					'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
					'post_status' => array( 'publish' ), // get only enabled global extra options
					'numberposts' => - 1,
					'orderby'     => 'title',
					'order'       => 'asc'
				);
				$tmp_tmglobalprices = get_posts( $args );
				echo '<div class="message0x0 tc-clearfix">' .
				     '<div class="message2x1">' .
				     '<label for="tm_meta_cpf_exclude"><span>' . esc_html__( 'Include additional Global forms', 'woocommerce-tm-extra-product-options' ) . '</span></label>' .
				     '<div class="messagexdesc">' . esc_attr( esc_html__( 'The forms you choose will be displayed alongside with the forms that the product already has.', 'woocommerce-tm-extra-product-options' ) ) . '</div>' .
				     '</div>' .
				     '<div class="message2x2">';
				if ( $tmp_tmglobalprices ) {
					echo '<div class="wp-tab-panel"><ul>';
					$wpml_tmp_tmglobalprices       = array();
					$wpml_tmp_tmglobalprices_added = array();
					foreach ( $tmp_tmglobalprices as $price ) {
						$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );
						if ( $original_product_id == $price->ID ) {
							$tm_global_forms = ( isset( $tm_meta_cpf['global_forms'] ) && is_array( $tm_meta_cpf['global_forms'] ) ) ? in_array( $price->ID, $tm_meta_cpf['global_forms'] ) : FALSE;
							echo '<li><label>';
							echo '<input type="checkbox" value="' . esc_attr( $price->ID ) . '" id="tm_meta_cpf_global_forms_' . esc_attr( $price->ID ) . '" name="tm_meta_cpf[global_forms][]" class="checkbox" ';
							checked( $tm_global_forms, TRUE, 1 );
							echo '>';
							echo ' ' . esc_html( $price->post_title ) . '</label></li>';
						}
					}
					echo '</ul></div>';
				}
				echo '</div>' .
				     '</div>';

				// Ouput Exclude 
				$tm_exclude = isset( $tm_meta_cpf['exclude'] ) ? $tm_meta_cpf['exclude'] : '';
				echo '<div class="message0x0 tc-clearfix">' .
				     '<div class="message2x1">' .
				     '<label for="tm_meta_cpf_exclude"><span>' . esc_html__( 'Exclude from Global Extra Product Options', 'woocommerce-tm-extra-product-options' ) . '</span></label>' .
				     '<div class="messagexdesc">' . esc_attr( esc_html__( 'This will exclude any global forms assigned to this product except those defined in the previous setting.', 'woocommerce-tm-extra-product-options' ) ) . '</div>' .
				     '</div>' .
				     '<div class="message2x2">' .
				     '<input type="checkbox" value="1" id="tm_meta_cpf_exclude" name="tm_meta_cpf[exclude]" class="checkbox" ';
				checked( $tm_exclude, '1', 1 );
				echo '>' .
				     '</div>' .
				     '</div>';

				// Ouput Price override 
				$tm_exclude = isset( $tm_meta_cpf['price_override'] ) ? $tm_meta_cpf['price_override'] : '';
				echo '<div class="message0x0 tc-clearfix">' .
				     '<div class="message2x1">' .
				     '<label for="tm_meta_cpf_price_override"><span>' . esc_html__( 'Override product price', 'woocommerce-tm-extra-product-options' ) . '</span></label>' .
				     '<div class="messagexdesc">' . esc_attr( esc_html__( 'This will override the product price with the price from the options if the total options price is greater then zero.', 'woocommerce-tm-extra-product-options' ) ) . '</div>' .
				     '</div>' .
				     '<div class="message2x2">' .
				     '<input type="checkbox" value="1" id="tm_meta_cpf_price_override" name="tm_meta_cpf[price_override]" class="checkbox" ';
				checked( $tm_exclude, '1', 1 );
				echo '>' .
				     '</div>' .
				     '</div>';

				// Ouput Override display 
				$tm_override_display = isset( $tm_meta_cpf['override_display'] ) ? $tm_meta_cpf['override_display'] : '';
				echo '<div class="message0x0 tc-clearfix">' .
				     '<div class="message2x1">' .
				     '<label for="tm_meta_cpf_override_display"><span>' . esc_html__( 'Override global display', 'woocommerce-tm-extra-product-options' ) . '</span></label>' .
				     '<div class="messagexdesc">' . esc_attr( esc_html__( 'This will override the display method only for this product.', 'woocommerce-tm-extra-product-options' ) ) . '</div>' .
				     '</div>' .
				     '<div class="message2x2">' .
				     '<select id="tm_meta_cpf_override_display" name="tm_meta_cpf[override_display]">' .
				     '<option value="" ';
				selected( $tm_override_display, '', 1 );
				echo '>' . esc_html__( 'Use global setting', 'woocommerce-tm-extra-product-options' ) . '</option>' .
				     '<option value="normal" ';
				selected( $tm_override_display, 'normal', 1 );
				echo '>' . esc_html__( 'Always show', 'woocommerce-tm-extra-product-options' ) . '</option>' .
				     '<option value="action" ';
				selected( $tm_override_display, 'action', 1 );
				echo '>' . esc_html__( 'Show only with action hook', 'woocommerce-tm-extra-product-options' ) . '</option>' .
				     '</select>' .
				     '</div>' .
				     '</div>';

				// Ouput Override totals box 
				$tm_override_final_total_box = isset( $tm_meta_cpf['override_final_total_box'] ) ? $tm_meta_cpf['override_final_total_box'] : '';
				echo '<div class="message0x0 tc-clearfix">' .
				     '<div class="message2x1">' .
				     '<label for="tm_meta_cpf_override_final_total_box"><span>' . esc_html__( 'Override Final total box', 'woocommerce-tm-extra-product-options' ) . '</span></label>' .
				     '<div class="messagexdesc">' . esc_attr( esc_html__( 'This will override the totals box display only for this product.', 'woocommerce-tm-extra-product-options' ) ) . '</div>' .
				     '</div>' .
				     '<div class="message2x2">' .
				     '<select id="tm_meta_cpf_override_final_total_box" name="tm_meta_cpf[override_final_total_box]">';

				echo '<option value="" ';
				selected( $tm_override_final_total_box, '', 1 );
				echo '>' . esc_html__( 'Use global setting', 'woocommerce-tm-extra-product-options' ) . '</option>';

				echo '<option value="normal" ';
				selected( $tm_override_final_total_box, 'normal', 1 );
				echo '>' . esc_html__( 'Show Both Final and Options total box', 'woocommerce-tm-extra-product-options' ) . '</option>';

				echo '<option value="options" ';
				selected( $tm_override_final_total_box, 'options', 1 );
				echo '>' . esc_html__( 'Show only Options total', 'woocommerce-tm-extra-product-options' ) . '</option>';

				echo '<option value="optionsiftotalnotzero" ';
				selected( $tm_override_final_total_box, 'optionsiftotalnotzero', 1 );
				echo '>' . esc_html__( 'Show only Options total if total is not zero', 'woocommerce-tm-extra-product-options' ) . '</option>';

				echo '<option value="final" ';
				selected( $tm_override_final_total_box, 'final', 1 );
				echo '>' . esc_html__( 'Show only Final total', 'woocommerce-tm-extra-product-options' ) . '</option>';

				echo '<option value="hideoptionsifzero" ';
				selected( $tm_override_final_total_box, 'hideoptionsifzero', 1 );
				echo '>' . esc_html__( 'Show Final box and hide Options if zero', 'woocommerce-tm-extra-product-options' ) . '</option>';

				echo '<option value="hideifoptionsiszero" ';
				selected( $tm_override_final_total_box, 'hideifoptionsiszero', 1 );
				echo '>' . esc_html__( 'Hide Final total box if Options total is zero', 'woocommerce-tm-extra-product-options' ) . '</option>';

				echo '<option value="hide" ';
				selected( $tm_override_final_total_box, 'hide', 1 );
				echo '>' . esc_html__( 'Hide Final total box', 'woocommerce-tm-extra-product-options' ) . '</option>';

				echo '<option value="pxq" ';
				selected( $tm_override_final_total_box, 'pxq', 1 );
				echo '>' . esc_html__( 'Always show only Final total (Price x Quantity)', 'woocommerce-tm-extra-product-options' ) . '</option>';

				echo '<option value="disable_change" ';
				selected( $tm_override_final_total_box, 'disable_change', 1 );
				echo '>' . esc_html__( 'Disable but change product prices', 'woocommerce-tm-extra-product-options' ) . '</option>';

				echo '<option value="disable" ';
				selected( $tm_override_final_total_box, 'disable', 1 );
				echo '>' . esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ) . '</option>';

				echo '</select>' .
				     '</div>' .
				     '</div>';

				// Ouput Override enabled roles 
				$tm_override_enabled_roles = isset( $tm_meta_cpf['override_enabled_roles'] ) ? $tm_meta_cpf['override_enabled_roles'] : '';
				if ( ! is_array( $tm_override_enabled_roles ) ) {
					$tm_override_enabled_roles = array( $tm_override_enabled_roles );
				}

				echo '<div class="message0x0 tc-clearfix">' .
				     '<div class="message2x1">' .
				     '<label for="tm_meta_cpf_override_enabled_roles"><span>' . esc_html__( 'Override enabled roles for this product', 'woocommerce-tm-extra-product-options' ) . '</span></label>' .
				     '<div class="messagexdesc">' . esc_attr( esc_html__( 'This will override which roles can see the options for this product.', 'woocommerce-tm-extra-product-options' ) ) . '</div>' .
				     '</div>' .
				     '<div class="message2x2">' .
				     '<select id="tm_meta_cpf_override_enabled_roles" name="tm_meta_cpf[override_enabled_roles][]" class="multiselect wc-enhanced-select" multiple="multiple">';

				$roles = themecomplete_get_roles();
				foreach ( $roles as $option_key => $option_text ) {
					echo '<option value="' . esc_attr( $option_key ) . '" ';
					selected( in_array( $option_key, $tm_override_enabled_roles ), 1, TRUE );
					echo '>' . esc_html( $option_text ) . '</option>';
				}

				echo '</select>' .
				     '</div>' .
				     '</div>';

				// Ouput Override disabled roles 
				$tm_override_disabled_roles = isset( $tm_meta_cpf['override_disabled_roles'] ) ? $tm_meta_cpf['override_disabled_roles'] : '';
				if ( ! is_array( $tm_override_disabled_roles ) ) {
					$tm_override_disabled_roles = array( $tm_override_disabled_roles );
				}

				echo '<div class="message0x0 tc-clearfix">' .
				     '<div class="message2x1">' .
				     '<label for="tm_meta_cpf_override_disabled_roles"><span>' . esc_html__( 'Override disabled roles for this product', 'woocommerce-tm-extra-product-options' ) . '</span></label>' .
				     '<div class="messagexdesc">' . esc_attr( esc_html__( 'This will override which roles cannot see the options for this product. This setting has priority over the enabled roles one.', 'woocommerce-tm-extra-product-options' ) ) . '</div>' .
				     '</div>' .
				     '<div class="message2x2">' .
				     '<select id="tm_meta_cpf_override_disabled_roles" name="tm_meta_cpf[override_disabled_roles][]" class="multiselect wc-enhanced-select" multiple="multiple">';

				$roles = themecomplete_get_roles();
				foreach ( $roles as $option_key => $option_text ) {
					echo '<option value="' . esc_attr( $option_key ) . '" ';
					selected( in_array( $option_key, $tm_override_disabled_roles ), 1, TRUE );
					echo '>' . esc_html( $option_text ) . '</option>';
				}

				echo '</select>' .
				     '</div>' .
				     '</div>';
			}
			?>
        </div>
    </div>
</div>