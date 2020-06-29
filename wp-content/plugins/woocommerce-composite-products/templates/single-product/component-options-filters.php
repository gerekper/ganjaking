<?php
/**
 * Component Options Filtering template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/component-options-filters.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @since    2.6.0
 * @version  4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div id="component_filters_<?php echo $component_id; ?>" class="component_filters">

	<p class="component_section_title component_filters_section_title">
		<label class="component_filters_title">
			<?php echo __( 'Filter options', 'woocommerce-composite-products' ); ?>
		</label>
		<a class="reset_component_filters" href="#" rel="nofollow" role="button" aria-label="<?php echo __( 'Reset all filters', 'woocommerce-composite-products' ); ?>"></a>
	</p><?php

	foreach ( $component_filtering_options as $filter ) {

		?><div class="component_filter cp_clearfix <?php echo 'closed' === $filter[ 'filter_toggle_state' ] ? 'closed' : 'open'; ?>" data-filter_type="<?php echo esc_attr( $filter[ 'filter_type' ] ); ?>" data-filter_id="<?php echo esc_attr( $filter[ 'filter_id' ] ); ?>" data-multiselect="<?php echo esc_attr( $filter[ 'is_multiselect' ] ) ? 'yes' : 'no'; ?>">
			<div class="component_filter_title">
				<label class="component_filter_name" >
					<span class="component_filter_name_text"><?php echo $filter[ 'filter_name' ]; ?></span>
					<button class="aria_button" aria-label="<?php echo sprintf( __( 'Toggle %s', 'woocommerce-composite-products' ), $filter[ 'filter_name' ] ); ?>" aria-expanded="<?php echo 'closed' !== $filter[ 'filter_toggle_state' ] ? 'true' : 'false'; ?>"></button>
				</label>
				<a class="reset_component_filter" href="#" rel="nofollow" role="button" aria-label="<?php echo sprintf( __( 'Reset %s', 'woocommerce-composite-products' ), $filter[ 'filter_name' ] ); ?>"></a>
			</div>
			<div class="component_filter_content" <?php echo 'closed' === $filter[ 'filter_toggle_state' ] ? 'style="display:none;"' : ''; ?>><?php

				?><ul class="component_filter_options"><?php

					foreach ( $filter[ 'filter_options' ] as $option_id => $option_name ) {

						?><li class="component_filter_option" data-option_id="<?php echo esc_attr( $option_id ); ?>">
							<a class="toggle_filter_option" href="#" rel="nofollow" role="checkbox" aria-checked="false" aria-label="<?php echo sprintf( __( 'Toggle %s', 'woocommerce-composite-products' ), $option_name ); ?>"><?php
								echo $option_name;
							?></a>
						</li><?php
					}

				?></ul>
			</div>
		</div>
		<?php
	}

?></div>
