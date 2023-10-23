<?php
/**
 * Block Rules Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var YITH_WAPO_Block $block
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$show_in                 = $block->get_rule( 'show_in' );

$show_show_in_products   = ( 'categories' !== $show_in && 'all' !== $show_in && '' !== $show_in ) || isset( $_REQUEST['block_rule_show_in'] ) && 'products' === $_REQUEST['block_rule_show_in'];
$show_show_in_categories = 'categories' === $show_in;

$show_exclude_products            = 'all' === $show_in || 'products' === $show_in || 'categories' === $show_in;
$show_exclude_products_products   = $block->get_rule( 'exclude_products' ) === 'yes';
$show_exclude_products_categories = $block->get_rule( 'exclude_products' ) === 'yes';

?>

<div id="block-rules">

	<!-- Option field -->
	<div class="field-wrap">
		<label for="yith-wapo-block-rule-show-in"><?php
            // translators: [ADMIN] Edit block page
            echo esc_html__( 'Show this block of options in', 'yith-woocommerce-product-add-ons' ); ?>:</label>
		<div class="field block-option">
			<?php

                $block_rule_show_in = 'all';

                if ( ! empty( $block->get_rule( 'show_in' ) ) ) {
                    $block_rule_show_in = $block->get_rule( 'show_in' );
                } elseif ( isset( $_REQUEST['block_rule_show_in'] ) && ! empty( $_REQUEST['block_rule_show_in'] ) ) {
                    $block_rule_show_in = $_REQUEST['block_rule_show_in'];
                }


				yith_plugin_fw_get_field(
					array(
						'id'      => 'yith-wapo-block-rule-show-in',
						'name'    => 'block_rule_show_in',
						'type'    => 'select',
						'value'   => $block_rule_show_in,
						'options' => array(
                            // translators: [ADMIN] Edit block page
							'all'      => __( 'All products', 'yith-woocommerce-product-add-ons' ),
                            // translators: [ADMIN] Edit block page
                            'products' => __( 'Specific products or categories', 'yith-woocommerce-product-add-ons' ),
						),
						'default' => 'all',
						'class'   => 'wc-enhanced-select',
					),
					true
				);
				?>
			<span class="description"><?php
                // translators: [ADMIN] Edit block page
                echo esc_html__( 'Choose to show these options in all products or only specific products or categories.', 'yith-woocommerce-product-add-ons' ); ?></span>
		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap yith-wapo-block-rule-show-in-products" style="<?php echo $show_show_in_products ? '' : 'display: none;'; ?>">
		<label for="yith-wapo-block-rule-show-in-products">
            <?php
            // translators: [ADMIN] Edit block page.
            echo esc_html__( 'Show in products',  'yith-woocommerce-product-add-ons' ); ?>:</label>
		<div class="field block-option">
			<?php
            $show_in_products = $block->get_rule( 'show_in_products' );

            if ( empty( $show_in_products ) && isset( $_REQUEST['block_rule_show_in_products'] ) ) {
                $show_in_products = $_REQUEST['block_rule_show_in_products'] ?? '';
            }
				yith_plugin_fw_get_field(
					array(
						'id'       => 'yith-wapo-block-rule-show-in-products',
						'name'     => 'block_rule_show_in_products',
						'type'     => 'ajax-products',
						'multiple' => true,
						'value'    => $show_in_products,
						'data'     => array(
							'action'   => 'woocommerce_json_search_products_and_variations',
							'security' => wp_create_nonce( 'search-products' ),
							'limit'    => apply_filters( 'yith_wapo_show_in_products_limit', 30 ),
						),
					),
					true
				);
				?>
			<span class="description"><?php
                // translators: [ADMIN] Edit block page.
                echo esc_html__( 'Choose in which products to show this block.', 'yith-woocommerce-product-add-ons' ); ?></span>
		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap yith-wapo-block-rule-show-in-products" style="<?php echo $show_show_in_products ? '' : 'display: none;'; ?>">
		<label for="yith-wapo-block-rule-show-in-categories"><?php
            // translators: [ADMIN] Edit block page
            echo esc_html__( 'Show in categories',  'yith-woocommerce-product-add-ons' ); ?>:</label>
		<div class="field block-option">
			<?php
            $show_in_categories = $block->get_rule( 'show_in_categories' );

            if ( empty( $show_in_categories ) && isset( $_REQUEST['block_rule_show_in_categories'] ) ) {
                $show_in_categories = $_REQUEST['block_rule_show_in_categories'] ?? '';
            }
				yith_plugin_fw_get_field(
					array(
						'id'       => 'yith-wapo-block-rule-show-in-categories',
						'name'     => 'block_rule_show_in_categories',
						'type'     => 'ajax-terms',
						'multiple' => true,
						'value'    => $show_in_categories,
						'data'     => array(
                            // translators: [ADMIN] Edit block page
                            'placeholder' => __( 'Search for categories', 'yith-woocommerce-product-add-ons' ) . '&hellip;',
							'taxonomy'    => 'product_cat',
						),
					),
					true
				);
				?>
			<span class="description">
                <?php
                // translators: [ADMIN] Edit block page
                echo esc_html__( 'Choose in which product categories to show this block.', 'yith-woocommerce-product-add-ons' ); ?></span>
		</div>
	</div>
	<!-- End option field -->

</div>
