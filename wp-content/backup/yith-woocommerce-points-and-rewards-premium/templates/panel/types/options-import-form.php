<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Text Plugin Admin View
 *
 * @package    YITH
 * @author     Emanuela Castorina <emanuela.castorina@yithemes.it>
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

extract( $field );
?>
<div id="<?php echo esc_attr( $id ); ?>-container" class="yit_options rm_option rm_input rm_text">
	<div class="option">
		<div class="inner-option">
			<label for="type_action"><?php esc_html_e( 'Action', 'yith-woocommerce-points-and-rewards' ); ?></label>
			<div class="select_wrapper">
				<select id="type_action" name="type_action" class="wc-enhanced-select">
					<option value="import"
							selected><?php esc_html_e( 'Import', 'yith-woocommerce-points-and-rewards' ); ?></option>
					<option value="export"><?php esc_html_e( 'Export', 'yith-woocommerce-points-and-rewards' ); ?></option>
				</select>
			</div>
		</div>
		<div class="inner-option">
			<label
					for="csv_format"><?php esc_html_e( 'CSV Format', 'yith-woocommerce-points-and-rewards' ); ?></label>
			<div class="select_wrapper">

				<select id="csv_format" name="csv_format" class="wc-enhanced-select">
					<option value="id"
							selected><?php esc_html_e( 'User ID / Points', 'yith-woocommerce-points-and-rewards' ); ?></option>
					<option
							value="email"><?php esc_html_e( 'Email / Points', 'yith-woocommerce-points-and-rewards' ); ?></option>
				</select>

			</div>
			<span class="description"><?php esc_html_e( 'Choose the format of the .csv file', 'yith-woocommerce-points-and-rewards' ); ?></span>
		</div>
		<div class="inner-option" data-dep="type_action" data-val="import">
			<label
					for="csv_import_action"><?php esc_html_e( 'CSV Import Action', 'yith-woocommerce-points-and-rewards' ); ?></label>
			<div class="select_wrapper">
				<select id="csv_import_action" name="csv_import_action" class="wc-enhanced-select">
					<option value="add"
							selected><?php esc_html_e( 'Add points to the current balance', 'yith-woocommerce-points-and-rewards' ); ?></option>
					<option
							value="remove"><?php esc_html_e( 'Override points', 'yith-woocommerce-points-and-rewards' ); ?></option>
				</select>
			</div>
			<span class="description"><?php esc_html_e( 'Choose the format of the .csv file', 'yith-woocommerce-points-and-rewards' ); ?></span>
		</div>
		<div class="inner-option">
			<label for="delimiter"><?php esc_html_e( 'Delimiter', 'yith-woocommerce-points-and-rewards' ); ?></label>
			<input type="text" id="delimiter" name="delimiter" value=",">
			<span class="description"><?php esc_html_e( 'Choose the delimiter', 'yith-woocommerce-points-and-rewards' ); ?></span>
		</div>
		<div class="inner-option" data-dep="type_action" data-val="import">
			<label for="file_import_csv"><?php esc_html_e( 'Import Points from a CSV file', 'yith-woocommerce-points-and-rewards' ); ?></label>
			<input type="file" id="file_import_csv" name="file_import_csv">
		</div>
		<div class="inner-option">
			<input type="hidden" class="ywpar_safe_submit_field" name="ywpar_safe_submit_field" value="" data-std="">
			<button class="button button-primary"
					id="ywpar_import_points"><?php esc_html_e( 'Start', 'yith-woocommerce-points-and-rewards' ); ?></button>
		</div>
	</div>
	<div class="clear"></div>
</div>
