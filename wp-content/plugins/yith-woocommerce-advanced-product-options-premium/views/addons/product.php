<?php
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var object $addon
 * @var string $addon_type
 * @var int    $x
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$product_name = '';
$product_id   = $addon->get_option( 'product', $x, '', false ) ? $addon->get_option( 'product', $x, '', false ) : '';

?>

<div class="fields">

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
		<label for="option-product"><?php echo esc_html__( 'Choose product', 'yith-woocommerce-product-add-ons' ); ?></label>
		<div class="field addon-product-selection">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'    => 'option-product-' . $x,
					'name'  => 'options[product][]',
					'type'  => 'ajax-products',
					'value' => $product_id,
					'data'  => array(
						'action'   => 'woocommerce_json_search_products_and_variations',
						'security' => wp_create_nonce( 'search-products' ),
					),
				),
				true
			);
			?>
		</div>
	</div>
	<!-- End option field -->

	<?php
	yith_wapo_get_view(
		'addon-editor/option-common-fields.php',
		array(
			'x'          => $x,
			'addon_type' => $addon_type,
			'addon'      => $addon
		),
        defined( 'YITH_WAPO_PREMIUM' ) && YITH_WAPO_PREMIUM ? 'premium/' : ''

    );
	?>

</div>
