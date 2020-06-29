<?php
/**
 * Component Options Sorting template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/component-options-orderby.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @since    2.6.0
 * @version  3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div class="component_ordering">

	<p class="component_section_title">
		<label class="component_orderby_title"><?php
		echo __( 'Sort options by', 'woocommerce-composite-products' );
		?></label>
	</p>

	<select name="component_orderby_<?php echo $component_id; ?>" class="component_orderby component_orderby_<?php echo $component_id; ?> orderby">
		<?php foreach ( $component_ordering_options as $id => $name ) : ?>
			<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
		<?php endforeach; ?>
	</select>
</div>
