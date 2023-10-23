<?php
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\CategoryAccordion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<input name="original_publish" type="hidden" id="original_publish" value="Publish">
<input type="submit" name="publish" id="publish" class="button yith-save-button" value="<?php echo esc_html__( 'Save Accordion', 'yith-woocommerce-category-accordion' ); ?>">
