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

$roles = yith_ywpar_get_roles();
?>
<div id="<?php echo esc_attr( $id ); ?>-container" class="yit_options rm_option rm_input rm_text">
	<div class="option">
		<div class="inner-option">
			<label for="ywpar_bulk_action_type"><?php esc_html_e( 'Action', 'yith-woocommerce-points-and-rewards' ); ?></label>
			<div class="select_wrapper">
				<select id="ywpar_bulk_action_type" name="ywpar_bulk_action_type" class="wc-enhanced-select">
					<option value="add" selected><?php esc_html_e( 'Add/Remove Points', 'yith-woocommerce-points-and-rewards' ); ?></option>
					<option value="ban"><?php esc_html_e( 'Ban Users', 'yith-woocommerce-points-and-rewards' ); ?></option>
					<option value="reset"><?php esc_html_e( 'Reset Users', 'yith-woocommerce-points-and-rewards' ); ?></option>
				</select>
			</div>
		</div>
		<div class="inner-option">
			<label for="ywpar_type_user_search"><?php esc_html_e( 'Select users by:', 'yith-woocommerce-points-and-rewards' ); ?></label>
			<div class="select_wrapper">
				<select id="ywpar_type_user_search" name="ywpar_type_user_search" class="wc-enhanced-select">
					<option value="everyone"
							selected><?php esc_html_e( 'Everyone', 'yith-woocommerce-points-and-rewards' ); ?></option>
					<option value="role_list"><?php esc_html_e( 'Include a list of roles', 'yith-woocommerce-points-and-rewards' ); ?></option>
					<option value="role_list_excluded"><?php esc_html_e( 'Exclude a list of roles', 'yith-woocommerce-points-and-rewards' ); ?></option>
					<option value="customers_list"><?php esc_html_e( 'Include a list of customers', 'yith-woocommerce-points-and-rewards' ); ?></option>
					<option value="customers_list_excluded"><?php esc_html_e( 'Exclude a list of customers', 'yith-woocommerce-points-and-rewards' ); ?></option>
				</select>

			</div>
			<span class="description"><?php esc_html_e( 'Either filter users using one of the options in this dropdown or apply changes to everyone.', 'yith-woocommerce-points-and-rewards' ); ?></span>
		</div>
		<div class="inner-option ywpar-deps" data-deps="role_list,role_list_excluded">
			<label for="user_role"><?php esc_html_e( 'Select a role', 'yith-woocommerce-points-and-rewards' ); ?></label>
			<select
					name="ywpar_user_role[]"
					multiple="multiple"
					id="ywpar_user_role"
					data-placeholder="<?php esc_attr_e( 'Select a role', 'yith-woocommerce-points-and-rewards' ); ?>"
					class="wc-enhanced-select" style="width:500px">
				<?php
				foreach ( $roles as $key_role => $role ) :
					?>
					<option
							value="<?php echo esc_attr( $key_role ); ?>"><?php echo esc_html( $role ); ?></option>
				<?php endforeach; ?>
			</select>
			<span class="description"><?php esc_html_e( 'Choose a list of roles', 'yith-woocommerce-points-and-rewards' ); ?></span>
		</div>
		<div class="inner-option ywpar-deps" data-deps="customers_list,customers_list_excluded">
			<label for="user_role"><?php esc_html_e( 'Select a customer', 'yith-woocommerce-points-and-rewards' ); ?></label>
			<?php
			if ( function_exists( 'yit_add_select2_fields' ) ) {
				$args = array(
					'type'             => 'hidden',
					'class'            => 'wc-customer-search',
					'id'               => 'ywpar_customer_list',
					'name'             => 'ywpar_customer_list',
					'data-placeholder' => esc_attr( __( 'Select a customer', 'yith-woocommerce-points-and-rewards' ) ),
					'data-allow_clear' => true,
					'data-selected'    => '',
					'data-multiple'    => true,
					'value'            => '',
					'style'            => 'width:500px',
				);

				yit_add_select2_fields( $args );
			}
			?>
			<span class="description"><?php esc_html_e( 'Choose a list of customers', 'yith-woocommerce-points-and-rewards' ); ?></span>
		</div>
		<div class="inner-option ywpar-deps_action" data-deps="add">
			<label for="ywpar_bulk_add_points"><?php esc_html_e( 'Points to add/remove:', 'yith-woocommerce-points-and-rewards' ); ?></label>
			<input type="number" id="ywpar_bulk_add_points" name="ywpar_bulk_add_points" type="number" step="1" style="width:100px">
			<span class="description"><?php esc_html_e( 'You can either add or remove points. Insert positive numbers to add points to the customer\'s balance or negative values to remove points.', 'yith-woocommerce-points-and-rewards' ); ?></span>
		</div>
		<div class="inner-option ywpar-deps_action" data-deps="add">
			<label for="ywpar_bulk_add_points"><?php esc_html_e( 'Description:', 'yith-woocommerce-points-and-rewards' ); ?></label>
			<input type="text" id="ywpar_bulk_add_description" name="ywpar_bulk_add_description"/>
		</div>
		<div class="inner-option ywpar-bulk-trigger">
			<input type="hidden" class="ywpar_safe_submit_field" name="ywpar_safe_submit_field" value="" data-std="">
			<button class="button button-primary"
					id="ywpar_bulk_action_points"><?php esc_html_e( 'Submit', 'yith-woocommerce-points-and-rewards' ); ?></button>
		</div>
	</div>
	<div class="clear"></div>
</div>
