<?php
/**
 * The template for displaying the start of the local mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-start.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

if ( isset( $classcart, $epo_internal_counter, $forcart, $form_prefix, $isfromshortcode, $product_id ) ) :
	$classcart            = (string) $classcart;
	$epo_internal_counter = (string) $epo_internal_counter;
	$forcart              = (string) $forcart;
	$form_prefix          = (string) $form_prefix;
	$isfromshortcode      = (string) $isfromshortcode;
	$product_id           = (string) $product_id;

	$showoptionsoverride = '';
	if ( 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_progressive_display' ) ) {
		$showoptionsoverride = ' tc-show-override';
	}
	?>
<div data-epo-id="<?php echo esc_attr( $epo_internal_counter ); ?>" data-cart-id="<?php echo esc_attr( $forcart ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>" class="tc-extra-product-options tm-extra-product-options tm-custom-prices tc-clearfix tm-product-id-<?php echo esc_attr( $product_id ); ?> <?php echo esc_attr( $classcart ); ?><?php echo esc_attr( $isfromshortcode ); ?><?php echo esc_attr( $showoptionsoverride ); ?>" id="tm-extra-product-options<?php echo esc_attr( $form_prefix ); ?>">
	<ul id="tm-extra-product-options-fields" class="tm-extra-product-options-fields tc-row tc-container">
	<?php
endif;
