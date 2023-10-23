<?php
/**
 * Filters Tax End
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Filters
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $preset YITH_WCAN_Preset
 * @var $filter YITH_WCAN_Filter_Tax
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php if ( 'select' === $filter->get_filter_design() ) : ?>
	</select><!-- .filter-dropdown -->
<?php else : ?>
	</ul><!-- .filter-items -->
<?php endif; ?>
