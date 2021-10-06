<?php
/**
 * Composite paged mode Summary Content template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/composite-summary-content.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version  3.7.0
 * @since    4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><ul class="summary_elements cp_clearfix" style="list-style:none"><?php

	$summary_element_loop = 1;

	foreach ( $components as $component_id => $component ) {

		$summary_element_class = '';

		// Summary loop first/last class
		if ( ( ( $summary_element_loop - 1 ) % $summary_columns ) == 0 || $summary_columns == 1 ) {
			$summary_element_class = 'first';
		}

		if ( $summary_element_loop % $summary_columns == 0 ) {
			$summary_element_class = 'last';
		}

		$title = $component->get_title();

		?><li class="summary_element summary_element_<?php echo $component_id; ?> <?php echo $summary_element_class; ?>" data-item_id="<?php echo $component_id; ?>">
			<div class="summary_element_wrapper_outer">
				<div class="summary_element_wrapper disabled">
					<div class="summary_element_wrapper_inner cp_clearfix"></div>
				</div>
			</div>
		</li><?php

		$summary_element_loop++;
	}
?></ul>
