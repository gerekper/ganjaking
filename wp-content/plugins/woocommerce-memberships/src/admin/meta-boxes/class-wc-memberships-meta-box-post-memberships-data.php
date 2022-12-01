<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Memberships Data Meta Box for all supported post types.
 *
 * @since 1.0.0
 */
class WC_Memberships_Meta_Box_Post_Memberships_Data extends \WC_Memberships_Meta_Box {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->id      = 'wc-memberships-post-memberships-data';
		$this->screens = array_keys( \WC_Memberships_Admin_Membership_Plan_Rules::get_valid_post_types_for_content_restriction_rules() );

		parent::__construct();
	}


	/**
	 * Returns the meta box title.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Memberships', 'woocommerce-memberships' );
	}


	/**
	 * Returns content restriction rules.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @return \WC_Memberships_Membership_Plan_Rule[] array of plan rules
	 */
	public function get_content_restriction_rules() {

		$content_restriction_rules = array();

		if ( $this->post instanceof \WP_Post ) {

			// get applied restriction rules to pass to HTML view
			$content_restriction_rules = wc_memberships()->get_rules_instance()->get_rules( array(
				'rule_type'         => 'content_restriction',
				'object_id'         => $this->post->ID,
				'content_type'      => 'post_type',
				'content_type_name' => $this->post->post_type,
				'exclude_inherited' => false,
				'plan_status'       => 'any',
			) );
			$membership_plan_options = array_keys( $this->get_membership_plan_options() );
			$membership_plan_id      = array_shift( $membership_plan_options );

			// add empty option to create a HTML template for new rules
			$content_restriction_rules['__INDEX__'] = new \WC_Memberships_Membership_Plan_Rule( array(
				'rule_type'          => 'content_restriction',
				'object_ids'         => array( $this->post->ID ),
				'id'                 => '',
				'membership_plan_id' => $membership_plan_id,
				'access_schedule'    => 'immediate',
				'access_type'        => '',
			) );
		}

		return $content_restriction_rules;
	}


	/**
	 * Displays the restrictions meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Post $post the post object
	 */
	public function output( \WP_Post $post ) {

		$this->post = $post;

		?>
		<h4><?php esc_html_e( 'Content Restriction', 'woocommerce-memberships' ); ?></h4>

		<?php woocommerce_wp_checkbox( array(
			'id'          => '_wc_memberships_force_public',
			'class'       => 'js-toggle-rules',
			'label'       => __( 'Disable restrictions', 'woocommerce-memberships' ),
			'description' => __( 'Check this box if you want to force the content to be public regardless of any restriction rules that may apply now or in the future.', 'woocommerce-memberships' ),
		) ); ?>

		<div class="js-restrictions <?php if ( 'yes' === wc_memberships_get_content_meta( $post, '_wc_memberships_force_public', true ) ) : ?>hide<?php endif; ?>">
			<?php

			// load content restriction rules view
			require_once( wc_memberships()->get_plugin_path() . '/src/admin/meta-boxes/views/class-wc-memberships-meta-box-view-content-restriction-rules.php' );

			// output content restriction rules view
			$view = new \WC_Memberships_Meta_Box_View_Content_Restriction_Rules( $this );
			$view->output();

			$membership_plans = $this->get_available_membership_plans();

			if ( ! empty( $membership_plans ) ) :

				?><p><em><?php esc_html_e( 'Need to add or edit a plan?', 'woocommerce-memberships' ); ?></em> <a target="_blank" href="<?php echo esc_url( admin_url( 'edit.php?post_type=wc_membership_plan' ) ); ?>"><?php esc_html_e( 'Manage Membership Plans', 'woocommerce-memberships' ); ?></a></p><?php

			endif;

			?>
			<h4><?php esc_html_e( 'Custom Restriction Message', 'woocommerce-memberships' ); ?></h4>
			<?php

			// grab variables for the checkbox field and the custom message field below
			$message_code            = \WC_Memberships_User_Messages::get_message_code_shorthand_by_post_type( $post );
			$use_custom_message_meta = "_wc_memberships_use_custom_{$message_code}_message";
			$use_custom              = 'yes' === wc_memberships_get_content_meta( $post, $use_custom_message_meta );
			$message_meta            = "_wc_memberships_{$message_code}_message";
			$message                 = wc_memberships_get_content_meta( $post, $message_meta );

			woocommerce_wp_checkbox( array(
				'id'          => $use_custom_message_meta,
				'value'       => $use_custom ? 'yes' : 'no',
				'class'       => 'js-toggle-custom-message',
				'label'       => __( 'Use custom message', 'woocommerce-memberships' ),
				'description' => __( 'Check this box if you want to customize the content restricted message for this content.', 'woocommerce-memberships' ),
			) );

			?>
			<div
				class="js-custom-message-editor-container <?php if ( ! $use_custom ) : ?>hide<?php endif; ?>"
				style="overflow: hidden;">
				<p>
					<?php /* translators: %1$s and %2$s placeholders are meant for {products} and {login_url} merge tags */
					printf( __( '%1$s automatically inserts the product(s) needed to gain access. %2$s inserts the URL to my account page. HTML is allowed.', 'woocommerce-memberships' ), '<strong><code>{products}</code></strong>', '<strong><code>{login_url}</code></strong>' ); ?>
				</p>
				<?php

				wp_editor( $message, $message_meta, array(
					'textarea_rows' => 5,
					'teeny'         => true,
				) );

				?>
			</div>
		</div>
		<?php
	}


	/**
	 * Processes and saves restriction rules and memberships meta.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 */
	public function update_data( $post_id, \WP_Post $post ) {

		\WC_Memberships_Admin_Membership_Plan_Rules::save_rules( $_POST, $post_id, array( 'content_restriction' ), 'post' );

		if ( ! empty( $_POST['_wc_memberships_force_public'] ) ) {
			wc_memberships()->get_restrictions_instance()->set_content_public( $post );
		} else {
			wc_memberships()->get_restrictions_instance()->unset_content_public( $post );
		}

		$message_code = \WC_Memberships_User_Messages::get_message_code_shorthand_by_post_type( $post );

		$this->update_custom_message( $post_id, array( $message_code ) );
	}


}
