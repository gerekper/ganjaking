<?php
/**
 * Open mobile filters modal button
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Filters
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $label  string
 * @var $preset YITH_WCAN_Preset
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<button type="button" class="btn btn-primary yith-wcan-filters-opener" data-target="<?php echo $preset ? esc_attr( 'preset_' . $preset->get_id() ) : ''; ?>" >
	<i class="filter-icon"></i>
	<?php echo esc_html( $label ); ?>
</button>
