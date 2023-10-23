<?php
/**
 * Preset load more filters - Admin view
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Admin
 * @version 4.0.3
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<a href="#" id="load_more_filters" class="load-more-filters">
	<?php
	// translators: 1. number of items to show.
	echo esc_html( sprintf( _x( 'See %d more filters', '[Admin] Add new filter in new preset page', 'yith-woocommerce-ajax-navigation' ), YITH_WCAN_Presets::FILTERS_PER_PAGE ) );
	?>
</a>
