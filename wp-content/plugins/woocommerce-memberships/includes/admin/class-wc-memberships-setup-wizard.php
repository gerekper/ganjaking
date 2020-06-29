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
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Admin;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Jilt_Promotions\Admin\Emails;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

/**
 * Onboarding Setup Wizard.
 *
 * @since 1.11.0
 *
 * @method \WC_Memberships get_plugin()
 */
class Setup_Wizard extends Framework\Admin\Setup_Wizard {


	/** @var string default name of the plan created via the wizard */
	private $default_plan_name;

	/** @var string option key where the ID of the membership plan created in the Setup Wizard is stored */
	private $membership_plan_id_key;


	/**
	 * Memberships Setup Wizard constructor.
	 *
	 * @since 1.11.0
	 *
	 * @param Framework\SV_WC_Plugin $plugin main instance of the plugin (\WC_Memberships)
	 */
	public function __construct( Framework\SV_WC_Plugin $plugin ) {

		parent::__construct( $plugin );

		$this->default_plan_name      = __( 'VIP Membership', 'woocommerce-memberships' );
		$this->membership_plan_id_key = 'wc_memberships_setup_wizard_membership_plan_id';

		/**
		 * Adds UTM parameters when Jilt is installed from the Memberships onboarding wizard.
		 *
		 * @see \SkyVerge\WooCommerce\Memberships\Admin\Setup_Wizard::save_member_emails_preferences()
		 *
		 * @since 1.17.5
		 *
		 * @param array $args UTM params
		 */
		add_filter( 'wc_jilt_app_connection_redirect_args', static function( $args ) {

			if ( 'yes' === get_option( 'wc_memberships_onboarding_wizard_install_jilt' ) ) {

				$args['utm_source']   = Emails::UTM_SOURCE;
				$args['utm_medium']   = Emails::UTM_MEDIUM;
				$args['utm_campaign'] = 'memberships-onboarding-wizard';
				$args['utm_content']  = Emails::UTM_CONTENT;
			}

			return $args;
		} );
	}


	/**
	 * Shows installation or upgrade notices.
	 *
	 * Extends parent method to display an additional notice on advanced emails.
	 *
	 * @internal
	 *
	 * @since 1.11.1
	 */
	public function add_admin_notices() {

		parent::add_admin_notices();

		// bail if Jilt is already installed
		if ( $this->get_plugin()->is_plugin_installed( 'jilt-for-woocommerce.php' ) ) {

			// also remove the WC Admin Note if Jilt has been installed
			if ( Framework\SV_WC_Plugin_Compatibility::is_enhanced_admin_available() ) {
				\Automattic\WooCommerce\Admin\Notes\WC_Admin_Notes::delete_notes_with_name( 'wc-memberships-jilt-cross-sell-notice' );
			}

		// show a notice about advanced emails with Jilt after upgrading to 1.11.0 when the feature was launched
		} elseif ( 'yes' === get_option( 'wc_memberships_show_advanced_emails_notice', 'no' ) ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag, %3$s - opening <a> HTML link tag, %4$s - closing </a> HTML link tag, %5$s - opening <a> HTML link tag, %6$s - closing </a> HTML link tag */
				esc_html__( '%1$sAdvanced Member Emails%2$s: WooCommerce Memberships has been upgraded to version 1.11.0! With this version, you can connect to %3$sJilt%4$s to send advanced member emails like a welcome series, winbacks for cancelled memberships, and more. %5$sLearn more&rarr;%6$s', 'woocommerce-memberships' ),
				'<strong>', '</strong>',
				'<a href="https://jilt.com/go/memberships-notice">', '</a>',
				'<a href="https://jilt.com/go/memberships-notice">', '</a>'
			);

			$this->get_plugin()->get_admin_notice_handler()->add_admin_notice( $message, 'memberships_upgrade_to_1_11_0', [
				'always_show_on_settings' => false,
				'notice_class'            => 'updated',
			] );

		// show a notice about Jilt emails when upgrading from version 1.12.0 or later (do not show if the WC Admin note can be seen instead)
		} elseif ( 'yes' === get_option( 'wc_memberships_show_jilt_cross_sell_notice', 'no' ) && ! Framework\SV_WC_Helper::is_enhanced_admin_screen() ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - opening <a> HTML link tag, %2$s - closing </a> HTML link tag */
				esc_html__( 'Use an email platform that automatically syncs member details. Segment newsletters by membership plan, and create automated email series for members in minutes using Jilt. %1$sSign up for free%2$s — all new accounts get a bonus credit!', 'woocommerce-memberships' ),
				'<a href="https://jilt.com/go/memberships-update">', '</a>'
			);

			$this->get_plugin()->get_admin_notice_handler()->add_admin_notice( $message, 'memberships_upgrade_to_1_17_5', [
				'always_show_on_settings' => false,
				'notice_class'            => 'notice-info',
			] );
		}
	}


	/**
	 * Loads additional scripts and styles for the setup wizard.
	 *
	 * @see \WC_Memberships_Admin::enqueue_scripts()
	 *
	 * @since 1.11.0
	 */
	protected function load_scripts_styles() {

		parent::load_scripts_styles();

		// register style dependencies
		wp_register_style( 'jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );

		// load styles
		wp_enqueue_style( 'wc-memberships-setup-wizard', $this->get_plugin()->get_plugin_url() . '/assets/css/admin/wc-memberships-setup-wizard.min.css', array( 'sv-wc-admin-setup', 'jquery-ui' ), \WC_Memberships::VERSION );

		// register script dependencies
		wp_register_script( 'select2', wc()->plugin_url() . '/assets/js/select2/select2.full.min.js', [ 'jquery' ], '4.0.3' );
		wp_register_script( 'wc-memberships-enhanced-select', $this->get_plugin()->get_plugin_url() . '/assets/js/admin/wc-memberships-enhanced-select.min.js', array( 'jquery', 'select2' ), \WC_Memberships::VERSION, true );
		wp_register_script( 'wc-memberships-rules', $this->get_plugin()->get_plugin_url() . '/assets/js/admin/wc-memberships-rules.min.js', array( 'wc-memberships-enhanced-select' ), \WC_Memberships::VERSION, true );

		// load scripts
		wp_enqueue_script( 'wc-memberships-setup-wizard', $this->get_plugin()->get_plugin_url() . '/assets/js/admin/wc-memberships-setup-wizard.min.js', array( 'sv-wc-admin-setup', 'wc-memberships-rules', 'jquery-ui-datepicker' ), \WC_Memberships::VERSION, true );

		// the script is localized to `wc_memberships_admin` to match the handling of the enhanced select script
		wp_localize_script( 'wc-memberships-setup-wizard', 'wc_memberships_admin', array(
			'ajax_url'              => admin_url( 'admin-ajax.php' ),
			'calendar_image'        => wc()->plugin_url() . '/assets/images/calendar.png',
			'search_products_nonce' => wp_create_nonce( 'search-products' ),
			'search_posts_nonce'    => wp_create_nonce( 'search-posts' ),
			'search_terms_nonce'    => wp_create_nonce( 'search-terms' ),
		) );
	}


	/**
	 * Registers the wizard's steps.
	 *
	 * @since 1.11.0
	 */
	protected function register_steps() {

		$this->register_step(
			'settings',
			__( 'Settings', 'woocommerce-memberships' ),
			array( $this, 'render_settings_step' ),
			array( $this, 'save_settings_preferences' )
		);

		$this->register_step(
			'access',
			__( 'Access', 'woocommerce-memberships' ),
			array( $this, 'render_access_step' ),
			array( $this, 'save_access_preferences' )
		);

		$this->register_step(
			'member-perks',
			__( 'Member Perks', 'woocommerce-memberships' ),
			array( $this, 'render_member_perks_step' ),
			array( $this, 'save_member_perks_preferences' )
		);

		$this->register_step(
			'member-emails',
			__( 'Member Emails', 'woocommerce-memberships' ),
			array( $this, 'render_member_emails_step' ),
			array( $this, 'save_member_emails_preferences' )
		);
	}


	/**
	 * Renders the initial welcome note text.
	 *
	 * @since 1.11.0
	 */
	protected function render_welcome_text() {

		esc_html_e( "Let's walk through a few steps to get the plugin configured and create your first plan.", 'woocommerce-memberships' );
	}


	/**
	 * Renders the "Settings" step.
	 *
	 * In this step we let the user set the restriction mode.
	 *
	 * @since 1.11.0
	 */
	protected function render_settings_step() {

		?>
		<div class="restriction-mode">
			<h5><?php esc_html_e( 'What should happen when non-members visit restricted content?', 'woocommerce-memberships' ); ?></h5>
			<small class="description"><em><?php
				printf(
					/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag, %3$s - opening <strong> HTML tag, %4$s - closing </strong> HTML tag, %4$s - opening <strong> HTML tag, %6$s - closing </strong> HTML tag */
					esc_html__( 'You can %1$shide restricted content completely%2$s, so non-members see a content not found (404) page, %3$shide only restricted content%4$s so non-members see a "content restricted" notice, or %5$sredirect non-members to a landing page%6$s when they try to view restricted content.', 'woocommerce-memberships' ),
					'<strong>', '</strong>', '<strong>', '</strong>', '<strong>', '</strong>'
				);
			?></em></small>
			<?php

			$default_restriction_mode = $this->get_plugin()->get_restrictions_instance()->get_restriction_mode();
			$default_redirect_page_id = $this->get_plugin()->get_restrictions_instance()->get_restricted_content_redirect_page_id();
			$default_showing_excerpts = $this->get_plugin()->get_restrictions_instance()->showing_excerpts();

			$this->render_form_field(
				'restriction_mode',
				array(
					'type'              => 'select',
					'name'              => 'restriction_mode',
					'default'           => $default_restriction_mode,
					'options'           => $this->get_plugin()->get_restrictions_instance()->get_restriction_modes(),
					'custom_attributes' => array( 'style' => 'width: 100%' ),
				),
				$default_restriction_mode
			);

			?>
			<div class="redirect-page" style="margin-bottom: 18px; <?php if ( 'redirect' !== $default_restriction_mode ) { echo ' display:none; '; } ?>">
				<small class="description" style="margin-bottom: 5px;"><?php esc_html_e( 'Which page should non-members be redirected to?', 'woocommerce-memberships' ); ?></small>
				<?php

				$args = array(
					'name'             => 'redirect_page_id',
					'id'               => 'redirect_page_id',
					'selected'         => $default_redirect_page_id,
					'sort_column'      => 'menu_order',
					'sort_order'       => 'ASC',
					'show_option_none' => ' ',
					'echo'             => false,
					'post_status'      => 'publish,private,draft',
				);

				/* @see \WC_Admin_Settings::output_fields() `single_select_page` case */
				echo str_replace( 'id=', ' style="width:100%;" data-placeholder="' . esc_attr__( 'Select a page&hellip;', 'woocommerce-memberships' ) . '" class="wc-enhanced-select" id=', wp_dropdown_pages( $args ) );

				?>
			</div>
			<div class="show-excerpts" <?php if ( 'hide' === $default_restriction_mode ) { echo 'style="display: none;"'; } ?>>
				<label><input
					type="checkbox"
					id="show_excerpts"
					name="show_excerpts"
					value="yes"
					<?php checked( $default_showing_excerpts, true, true ); ?>
				/><small><?php esc_html_e( 'I want to show excerpts of the restricted content to non-members and search engines.', 'woocommerce-memberships' ); ?></small></label>
			</div>
		</div>
		<?php
	}


	/**
	 * Saves the user preferences for the restriction mode.
	 *
	 * @since 1.11.0
	 */
	protected function save_settings_preferences() {

		if ( ! empty( $_POST['restriction_mode'] ) ) {

			$this->get_plugin()->get_restrictions_instance()->set_restriction_mode( $_POST['restriction_mode'] );

			if ( 'redirect' === $_POST['restriction_mode'] && isset( $_POST['redirect_page_id'] ) ) {
				$this->get_plugin()->get_restrictions_instance()->set_restricted_content_redirect_page_id( absint( trim( $_POST['redirect_page_id'] ) ) );
			}
		}

		if ( isset( $_POST['show_excerpts'] ) && 'yes' === $_POST['show_excerpts'] ) {
			$this->get_plugin()->get_restrictions_instance()->set_excerpts_visibility( 'show' );
		} else {
			$this->get_plugin()->get_restrictions_instance()->set_excerpts_visibility( 'hide' );
		}
	}


	/**
	 * Renders the "Access" step.
	 *
	 * In this step the user sets the plan name, access method and duration.
	 *
	 * @since 1.11.0
	 */
	protected function render_access_step() {

		$plan = $this->get_or_create_my_first_membership_plan();

		?>
		<h1><?php esc_html_e( 'Gaining access to memberships', 'woocommerce-memberships' ); ?></h1>
		<p><?php esc_html_e( 'Now we will start creating our first membership plan, which controls how customers become members. You can edit any of these settings or create more plans later.', 'woocommerce-memberships' ); ?></p>

		<div class="membership-plan-name">
			<label for="membership_plan_name"><small><?php esc_html_e( 'What should we call our first membership plan?', 'woocommerce-memberships' ); ?></small></label>
			<?php $this->render_form_field(
				'membership_plan_name',
				array(
					'type'              => 'text',
					'required'          => true,
					'placeholder'       => __( 'Enter the plan name&hellip;', 'woocommerce-memberships' ),
					'default'           => $plan->get_name(),
					'custom_attributes' => array( 'style' => 'width: 95%;' ),
				),
				$plan->get_name()
			); ?>
		</div>

		<br />

		<div class="membership-plan-access-method">

			<label for="access_method"><small><?php esc_html_e( 'How do customers become members?', 'woocommerce-memberships' ); ?></small></label>

			<?php $this->render_form_field(
				'access_method',
				array(
					'type'              => 'select',
					'name'              => 'access_method',
					'default'           => $plan->get_access_method(),
					'options'           => array(
						'manual-only' => __( 'I will assign members manually', 'woocommerce-memberships' ),
						'signup'      => __( 'Customers become members when they register an account', 'woocommerce-memberships' ),
						'purchase'    => __( 'Customers become members when they purchase a particular product', 'woocommerce-memberships' ),
					),
					'custom_attributes' => array( 'style' => 'width: 100%' ),
				),
				$plan->get_access_method()
			); ?>

			<div class="product-purchase" <?php if ( 'purchase' !== $plan->get_access_method() ) { echo 'style="display: none;"'; } ?>>
				<label for="product_ids"><small class="description"><?php esc_html_e( 'Which products can be purchased to become a member?', 'woocommerce-memberships' ) ?></small></label>
				<select
					name="product_ids[]"
					id="product_ids"
					class="js-ajax-select-products"
					style="width: 100%;"
					multiple="multiple"
					data-placeholder="<?php esc_attr_e( 'Search products&hellip;', 'woocommerce-memberships' ); ?>">
					<?php $product_ids = $plan->get_product_ids(); ?>
					<?php foreach ( $product_ids as $product_id ) : ?>
						<?php if ( $product = wc_get_product( $product_id ) ) : ?>
							<option value="<?php echo $product_id; ?>" selected><?php echo esc_html( $product->get_formatted_name() ); ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<br />

		<div class="membership-plan-access-length">

			<label for="access_length_type"><small><?php esc_html_e( 'How long will this membership last?', 'woocommerce-memberships' ); ?></small></label>

			<?php $this->render_form_field(
				'access_length_type',
				array(
					'type'              => 'select',
					'name'              => 'access_length_type',
					'default'           => $plan->get_access_length_type(),
					'options'           => array(
						'unlimited' => __( 'This membership does not end', 'woocommerce-memberships' ),
						'specific'  => __( 'This membership has a specific length, like one year', 'woocommerce-memberships' ),
						'fixed'     => __( 'This membership runs for a set date range', 'woocommerce-memberships' ),
					),
					'custom_attributes' => array( 'style' => 'width: 100%' ),
				),
				$plan->get_access_length_type()
			); ?>

			<div id="access-length" <?php if ( 'specific' !== $plan->get_access_length_type() ) { echo 'style="display: none;"'; } ?>>
				<p class="form-row sv-wc-plugin-admin-setup-control">
					<label>
						<small><?php esc_html_e( 'Choose a length:', 'woocommerce-memberships' ); ?></small>
						<input
							type="number"
							name="access_length_amount"
							id="access_length_amount"
							class="access-length-amount"
							value="<?php echo esc_attr( max( 1, $plan->has_access_length() ? $plan->get_access_length_amount() : 1 ) ); ?>"
							min="1"
							step="1"
						/>
						<select
							name="access_length_period"
							id="access_length_period"
							class="wc-enhanced-select access-length-period"
							style="width: 31%;">
							<?php foreach ( $this->get_plugin()->get_plans_instance()->get_membership_plans_access_length_periods( true ) as $key => $label ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $plan->has_access_length() ? $plan->get_access_length_period() : 'years' ); ?>><?php echo esc_html( strtolower( $label ) ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</p>
			</div>

			<div id="access-dates" <?php if ( 'fixed' !== $plan->get_access_length_type() ) { echo 'style="display: none;"'; } ?>>
				<p class="form-row sv-wc-plugin-admin-setup-control">

					<span class="start-date">
						<label>
							<small><?php esc_html_e( 'Start date:', 'woocommerce-memberships' ); ?></small>
							<input
								type="text"
								id="access_start_date"
								name="access_start_date"
								class="access_length-start-date access-date"
								value="<?php echo esc_attr( $plan->is_access_length_type( 'fixed' ) ? $plan->get_local_access_start_date( 'Y-m-d' ) : date( 'Y-m-d', current_time( 'timestamp' ) ) ); ?>"
							/>
						</label>
					</span>

					<span class="end-date">
						<label>
							<small><?php esc_html_e( 'End date:', 'woocommerce-memberships' ); ?></small>
							<input
								type="text"
								id="access_end_date"
								name="access_end_date"
								class="access_length-end-date access-date"
								value="<?php echo esc_attr( $plan->is_access_length_type( 'fixed' ) ? $plan->get_local_access_end_date( 'Y-m-d' ) : date( 'Y-m-d', strtotime( '+1 years', current_time( 'timestamp' ) ) ) ); ?>"
							/>
						</label>
					</span>

					<span>
						<label>
							<small><?php
								printf(
									/* translators: Placeholders: %s - date format */
									esc_html__( 'Format: %s', 'woocommerce-memberships' ),
									' <code><em>YYYY-MM-DD</em></code> ' );
								?></small>
						</label>
					</span>

				</p>
			</div>
		</div>
		<?php
	}


	/**
	 * Saves the plan access name and access preferences.
	 *
	 * @since 1.11.0
	 *
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	protected function save_access_preferences() {

		if ( $plan = $this->get_my_first_membership_plan() ) {

			if ( ! isset( $_POST['membership_plan_name'] ) || '' === trim( $_POST['membership_plan_name'] ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'You must enter a plan name.', 'woocommerce-memberships' ) );
			}

			$name = sanitize_text_field( $_POST['membership_plan_name'] );

			// update plan name if different than default/currently set
			if ( $name !== $plan->get_name() ) {

				$slug = sanitize_title( $name );
				$args = array(
					'ID'         => $plan->get_id(),
					'post_title' => $name,
				);

				$posts = get_posts( array(
					'name'           => $slug,
					'post_type'      => 'wc_membership_plan',
					'posts_per_page' => 1,
				) );

				if ( empty( $posts[0] ) ) {
					$args['post_name'] = $slug;
				} else {
					$args['post_name'] = uniqid( $slug, false );
				}

				wp_update_post( $args );
			}

			// make sure the plan is not in the trash
			if ( ! in_array( $plan->post->post_status, array( 'auto-draft', 'draft', 'publish' ) ) ) {
				wp_update_post( array(
					'ID'          => $plan->get_id(),
					'post_status' => 'draft',
				) );
			}

			// set access method
			if ( isset( $_POST['access_method'] ) ) {

				$plan->set_access_method( $_POST['access_method'] );

				if ( 'purchase' === $_POST['access_method'] && isset( $_POST['product_ids'] ) ) {
					$plan->set_product_ids( (array) $_POST['product_ids'] );
				} else {
					$plan->delete_product_ids();
				}
			}

			// set access duration
			if ( isset( $_POST['access_length_type'] ) && is_string( $_POST['access_length_type'] ) ) {

				switch ( $_POST['access_length_type'] ) {

					case 'specific' :

						$access_length_amount = isset( $_POST['access_length_amount'] ) ? $_POST['access_length_amount'] : '';
						$access_length_period = isset( $_POST['access_length_period'] ) ? $_POST['access_length_period'] : '';

						$plan->set_access_length( $access_length_amount . ' ' . $access_length_period );

						$plan->delete_access_start_date();
						$plan->delete_access_end_date();

					break;

					case 'fixed' :

						$access_start_date = isset( $_POST['access_start_date'] ) ? $_POST['access_start_date'] : '';
						$access_end_date   = isset( $_POST['access_end_date'] ) ? $_POST['access_end_date'] : '';

						$plan->set_access_start_date( $access_start_date );
						$plan->set_access_end_date( $access_end_date );

						$plan->delete_access_length();

					break;

					case 'unlimited' :
					default :

						$plan->delete_access_length();
						$plan->delete_access_start_date();
						$plan->delete_access_end_date();

					break;
				}
			}
		}
	}


	/**
	 * Renders the "Member Perks" setup wizard step.
	 *
	 * In this step the user sets some membership plan rules.
	 *
	 * @since 1.11.0
	 */
	protected function render_member_perks_step() {

		$plan = $this->get_or_create_my_first_membership_plan();
		$name = $plan->get_name();

		?>
		<h1><?php
			echo esc_html(
				sprintf(
					/* translators: Placeholders: %s - Membership plan name */
					__( '%s perks', 'woocommerce-memberships' ),
					empty( $name ) ? $this->default_plan_name : $name
				)
			); ?></h1>
		<p><?php esc_html_e( "Let's create our first membership perks. You can create more restrictions and discounts when we're ready to review your plan.", 'woocommerce-memberships' ); ?></p>
		<div class="membership-plan-rules">
			<?php

			$toggles = array(
				'restrict_content'  => array(
					'type'        => 'toggle',
					'name'        => 'restrict_content',
					'value'       => 'yes',
					'input_class' => array( 'toggle-preferences' ),
					'label'       => __( 'Restrict Content', 'woocommerce-memberships' ),
					'description' => __( 'You can choose content or taxonomies (like categories) that only members can access.', 'woocommerce-memberships' ),
					'checked'     => $plan->get_content_restriction_rules(),
					'method'      => 'render_restrict_content_preferences_field',
				),
				'restrict_products' => array(
					'type'        => 'toggle',
					'name'        => 'restrict_products',
					'value'       => 'yes',
					'input_class' => array( 'toggle-preferences' ),
					'label'       => __( 'Restrict Products', 'woocommerce-memberships' ),
					'description' => __( 'You can choose product or categories to restrict to members only, and whether non-members are only blocked from purchasing or from viewing a product.', 'woocommerce-memberships' ),
					'checked'     => $plan->get_product_restriction_rules(),
					'method'      => 'render_restrict_products_preferences_field',
				),
				'offer_discounts'   => array(
					'type'        => 'toggle',
					'name'        => 'offer_discounts',
					'value'       => 'yes',
					'input_class' => array( 'toggle-preferences' ),
					'label'       => __( 'Offer Discounts', 'woocommerce-memberships' ),
					'description' => __( 'You can offer discounts to members on products or categories. These discounts are automatically applied while members are logged in.', 'woocommerce-memberships' ),
					'checked'     => $plan->get_purchasing_discount_rules(),
					'method'      => 'render_purchasing_discounts_preferences_field',
				),
			);

			foreach ( $toggles as $field_id => $field_data ) {

				$is_checked = ! empty( $field_data['checked'] );

				if ( $is_checked ) {
					$field_data['custom_attributes'] = array_merge( ! empty( $field_data['custom_attributes'] ) ? $field_data['custom_attributes'] : array(), array( 'checked' => 'checked' ) );
					$value = 'yes';
				} else {
					$value = null;
				}

				$this->render_form_field( $field_id, $field_data, $value );

				$method = $field_data['method'];

				$this->$method( $plan );

				if ( 'offer_discounts' !== $field_id ) {
					echo '<hr />';
				}
			}

			?>
		</div>
		<?php
	}


	/**
	 * Returns the restrict content HTML fields.
	 *
	 * HTML structure and CSS classes in the HTML output reflect the same model as in other admin areas as expected by the rules JS.
	 * @see Setup_Wizard::render_member_perks_step()
	 *
	 * @since 1.11.0
	 *
	 * @param \WC_Memberships_Membership_Plan $plan related membership plan
	 */
	private function render_restrict_content_preferences_field( $plan ) {

		$rule = $this->get_my_first_membership_plan_rule( $plan, 'content_restriction' );

		?>
		<div class="form-row preferences membership-plan-rule"<?php if ( $rule->is_new() ) { echo 'style="display:none;"'; } ?>>
			<?php

			$content_restriction_content_type_options = array( 'post_types' => array(), 'taxonomies' => array() );

			foreach ( \WC_Memberships_Admin_Membership_Plan_Rules::get_valid_post_types_for_content_restriction_rules() as $post_type_name => $post_type ) {
				$content_restriction_content_type_options['post_types'][ 'post_type|' . $post_type_name ] = $post_type;
			}
			foreach ( \WC_Memberships_Admin_Membership_Plan_Rules::get_valid_taxonomies_for_content_restriction_rules() as $taxonomy_name => $taxonomy ) {
				$content_restriction_content_type_options['taxonomies'][ 'taxonomy|' . $taxonomy_name ]   = $taxonomy;
			}

			?>
			<table class="content-restriction-rules">
				<tbody>
					<tr>
						<th><label for="content-restriction-rule-content-type"><?php esc_html_e( 'What type of content should our first rule target?', 'woocommerce-memberships' ); ?></label></th>
						<th><label for="content-restriction-rule-object-ids"><?php esc_html_e( 'What content should be restricted to members? You can leave blank to select all.', 'woocommerce-memberships' ); ?>	</label></th>
					</tr>
					<tr>
						<td>
							<select
								name="content_restriction_rule_content_type"
								id="content-restriction-rule-content-type"
								class="wc-enhanced-select js-content-type"
								style="width: 100%;">
								<optgroup label="<?php esc_attr_e( 'Post types', 'woocommerce-memberships' ); ?>">
									<?php foreach ( $content_restriction_content_type_options['post_types'] as $key => $post_type ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $rule->get_content_type_key() ); ?> <?php if ( ! ( current_user_can( $post_type->cap->edit_posts ) && current_user_can( $post_type->cap->edit_others_posts ) ) ) : ?>disabled<?php endif; ?>><?php echo esc_html( $post_type->label ); ?></option>
									<?php endforeach; ?>
								</optgroup>
								<optgroup label="<?php esc_attr_e( 'Taxonomies', 'woocommerce-memberships' ); ?>">
									<?php foreach ( $content_restriction_content_type_options['taxonomies'] as $key => $taxonomy ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $rule->get_content_type_key() ); ?> <?php if ( ! ( current_user_can( $taxonomy->cap->manage_terms ) && current_user_can( $taxonomy->cap->edit_terms ) ) ) : ?>disabled<?php endif; ?> ><?php echo esc_html( $taxonomy->label ); ?></option>
									<?php endforeach; ?>
								</optgroup>
							</select>
						</td>
						<td>
							<select
								name="content_restriction_rule_object_ids[]"
								id="content-restriction-rule-object-ids"
								class="wc-memberships-object-search js-object-ids"
								style="width: 100%;"
								multiple="multiple"
								data-placeholder="<?php esc_attr_e( 'Search content&hellip;', 'woocommerce-memberships' ); ?>"
								data-action="<?php echo esc_attr( \WC_Memberships_Admin_Membership_Plan_Rules::get_rule_object_search_action( $rule ) ); ?>">
								<?php if ( $rule->has_objects() ) : ?>
									<?php foreach ( $rule->get_object_ids() as $object_id ) : ?>
										<?php if ( $object_label = \WC_Memberships_Admin_Membership_Plan_Rules::get_rule_object_label( $rule, $object_id, true ) ) : ?>
											<option value="<?php echo esc_attr( $object_id ); ?>" selected><?php echo esc_html( $object_label ); ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			<p><?php esc_html_e( 'You can add more content restriction rules while reviewing this plan later.', 'woocommerce-memberships' ); ?></p>
		</div>
		<?php
	}


	/**
	 * Returns the restrict products HTML fields.
	 *
	 * HTML structure and CSS classes in the HTML output reflect the same model as in other admin areas as expected by the rules JS.
	 * @see Setup_Wizard::render_member_perks_step()
	 *
	 * @since 1.11.0
	 *
	 * @param \WC_Memberships_Membership_Plan $plan related membership plan
	 */
	private function render_restrict_products_preferences_field( $plan ) {

		$rule = $this->get_my_first_membership_plan_rule( $plan, 'product_restriction' );

		?>
		<div class="form-row preferences membership-plan-rule"<?php if ( $rule->is_new() ) { echo 'style="display:none;"'; } ?>>
			<?php

			$product_restriction_content_type_options = array(
				'post_types' => array(
					'post_type|product' => get_post_type_object( 'product' ),
				),
				'taxonomies' => array(),
			);

			foreach ( \WC_Memberships_Admin_Membership_Plan_Rules::get_valid_taxonomies_for_product_restriction_rules() as $taxonomy_name => $taxonomy ) {
				$product_restriction_content_type_options['taxonomies'][ 'taxonomy|' . $taxonomy_name ] = $taxonomy;
			}

			?>
			<table class="product-restriction-rules">
				<tbody>
					<tr>
						<th><label for="product-restriction-rule-content-type"><?php esc_html_e( 'What type of product content should our first rule target?', 'woocommerce-memberships' ); ?></label></th>
						<th><label for="product-restriction-rule-object-ids"><?php esc_html_e( 'What products should be restricted to members? You can leave blank to select all.', 'woocommerce-memberships' ); ?>	</label></th>
					</tr>
					<tr>
						<td>
							<select
								name="product_restriction_rule_content_type"
								id="product-restriction-rule-content-type"
								class="wc-enhanced-select js-content-type"
								style="width: 100%;">
								<optgroup label="<?php esc_attr_e( 'Post types', 'woocommerce-memberships' ); ?>">
									<?php foreach ( $product_restriction_content_type_options['post_types'] as $key => $post_type ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $rule->get_content_type_key() ); ?> <?php if ( ! ( current_user_can( $post_type->cap->edit_posts ) && current_user_can( $post_type->cap->edit_others_posts ) ) ) : ?>disabled<?php endif; ?>><?php echo esc_html( $post_type->label ); ?></option>
									<?php endforeach; ?>
								</optgroup>
								<optgroup label="<?php esc_attr_e( 'Taxonomies', 'woocommerce-memberships' ); ?>">
									<?php foreach ( $product_restriction_content_type_options['taxonomies'] as $key => $taxonomy ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $rule->get_content_type_key() ); ?> <?php if ( ! ( current_user_can( $taxonomy->cap->manage_terms ) && current_user_can( $taxonomy->cap->edit_terms ) ) ) : ?>disabled<?php endif; ?> ><?php echo esc_html( $taxonomy->label ); ?></option>
									<?php endforeach; ?>
								</optgroup>
							</select>
						</td>
						<td>
							<select
								name="product_restriction_rule_object_ids[]"
								id="product-restriction-rule-object-ids"
								class="wc-memberships-object-search js-object-ids"
								style="width: 100%;"
								multiple="multiple"
								data-placeholder="<?php esc_attr_e( 'Search products&hellip;', 'woocommerce-memberships' ); ?>"
								data-action="<?php echo esc_attr( \WC_Memberships_Admin_Membership_Plan_Rules::get_rule_object_search_action( $rule ) ); ?>">
								<?php if ( $rule->has_objects() ) : ?>
									<?php foreach ( $rule->get_object_ids() as $object_id ) : ?>
										<?php if ( $object_label = \WC_Memberships_Admin_Membership_Plan_Rules::get_rule_object_label( $rule, $object_id, true ) ) : ?>
											<option value="<?php echo esc_attr( $object_id ); ?>" selected><?php echo esc_html( $object_label ); ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th colspan="2" style="width: 100%;"><br /><label for="product-restriction-rule-access-type"><?php esc_html_e( 'How should this restriction work?', 'woocommerce-memberships' ); ?></label></th>
					</tr>
					<tr>
						<td colspan="2" style="width: 100%;">
							<select
								id="product-restriction-rule-access-type"
								name="products_restriction_rule_access_type"
								class="wc-enhanced-select"
								style="width: 100%;">
								<option value="view"><?php esc_html_e( 'Non-members can view restricted products, but cannot purchase them', 'woocommerce-memberships' ); ?></option>
								<option value="purchase"><?php esc_html_e( 'Non-members cannot view products or purchase them', 'woocommerce-memberships' ); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			<p><?php esc_html_e( 'You can add more product restriction rules while reviewing this plan later.', 'woocommerce-memberships' ); ?></p>
		</div>
		<?php
	}


	/**
	 * Returns the purchasing discounts HTML fields.
	 *
	 * HTML structure and CSS classes in the HTML output reflect the same model as in other admin areas as expected by the rules JS.
	 * @see Setup_Wizard::render_member_perks_step()
	 *
	 * @since 1.11.0
	 *
	 * @param \WC_Memberships_Membership_Plan $plan related membership plan
	 */
	private function render_purchasing_discounts_preferences_field( $plan ) {

		$rule = $this->get_my_first_membership_plan_rule( $plan, 'purchasing_discount' );

		?>
		<div class="form-row preferences membership-plan-rule"<?php if ( $rule->is_new() ) { echo 'style="display:none;"'; } ?>>
			<?php

			$purchasing_discount_content_type_options = array(
				'post_types' => array(
					'post_type|product' => get_post_type_object( 'product' ),
				),
				'taxonomies' => array(),
			);

			foreach ( \WC_Memberships_Admin_Membership_Plan_Rules::get_valid_taxonomies_for_purchasing_discounts_rules() as $taxonomy_name => $taxonomy ) {
				$purchasing_discount_content_type_options['taxonomies'][ 'taxonomy|' . $taxonomy_name ] = $taxonomy;
			}

			?>
			<table class="purchasing-discount-rules">
				<tbody>
					<tr>
						<th><label for="purchasing-discount-rule-content-type"><?php esc_html_e( 'What type of product content should our first rule target?', 'woocommerce-memberships' ); ?></label></th>
						<th><label for="purchasing-discount-rule-object-ids"><?php esc_html_e( 'What products should be discounted? You can leave blank to select all.', 'woocommerce-memberships' ); ?>	</label></th>
					</tr>
					<tr>
						<td>
							<select
								name="purchasing_discount_rule_content_type"
								id="purchasing-discount-rule-content-type"
								class="wc-enhanced-select js-content-type"
								style="width: 100%;">
								<optgroup label="<?php esc_attr_e( 'Post types', 'woocommerce-memberships' ); ?>">
									<?php foreach ( $purchasing_discount_content_type_options['post_types'] as $key => $post_type ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $rule->get_content_type_key() ); ?> <?php if ( ! ( current_user_can( $post_type->cap->edit_posts ) && current_user_can( $post_type->cap->edit_others_posts ) ) ) : ?>disabled<?php endif; ?>><?php echo esc_html( $post_type->label ); ?></option>
									<?php endforeach; ?>
								</optgroup>
								<optgroup label="<?php esc_attr_e( 'Taxonomies', 'woocommerce-memberships' ); ?>">
									<?php foreach ( $purchasing_discount_content_type_options['taxonomies'] as $key => $taxonomy ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $rule->get_content_type_key() ); ?> <?php if ( ! ( current_user_can( $taxonomy->cap->manage_terms ) && current_user_can( $taxonomy->cap->edit_terms ) ) ) : ?>disabled<?php endif; ?> ><?php echo esc_html( $taxonomy->label ); ?></option>
									<?php endforeach; ?>
								</optgroup>
							</select>
						</td>
						<td>
							<select
								name="purchasing_discount_rule_object_ids[]"
								id="purchasing-discount-rule-object-ids"
								class="wc-memberships-object-search js-object-ids"
								style="width: 100%;"
								multiple="multiple"
								data-placeholder="<?php esc_attr_e( 'Search products&hellip;', 'woocommerce-memberships' ); ?>"
								data-action="<?php echo esc_attr( \WC_Memberships_Admin_Membership_Plan_Rules::get_rule_object_search_action( $rule ) ); ?>">
								<?php if ( $rule->has_objects() ) : ?>
									<?php foreach ( $rule->get_object_ids() as $object_id ) : ?>
										<?php if ( $object_label = \WC_Memberships_Admin_Membership_Plan_Rules::get_rule_object_label( $rule, $object_id, true ) ) : ?>
											<option value="<?php echo esc_attr( $object_id ); ?>" selected><?php echo esc_html( $object_label ); ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th colspan="2" style="width: 100%;">
							<br /><label for="purchasing-discount-rule-discount-amount"><?php esc_html_e( 'What amount should the discount apply to the selected product(s)?', 'woocommerce-memberships' ) ?></label>
						</th>
					</tr>
					<tr>
						<td colspan="2" style="width: 100%;">
							<div class="form-row sv-wc-plugin-admin-setup-control purchasing-discount-type-amount">
								<input
									type="number"
									name="purchasing_discount_rule_discount_amount"
									id="purchasing-discount-rule-discount-amount"
									step="<?php echo esc_attr( wc_memberships()->get_rules_instance()->get_discount_rules_precision() ); ?>"
									min="0"
									value="10"
								>
								&nbsp;
								<select
									name="purchasing_discount_rule_discount_type"
									id="purchasing-discount-rule-discount-type"
									class="wc-enhanced-select">
									<option value="percentage">%</option>
									<option value="amount"><?php echo get_woocommerce_currency_symbol(); ?></option>
								</select>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<p><?php esc_html_e( 'You can add more product purchasing discount rules while reviewing this plan later.', 'woocommerce-memberships' ); ?></p>
		</div>
		<?php
	}


	/**
	 * Saves member perks preferences.
	 *
	 * @since 1.11.0
	 */
	protected function save_member_perks_preferences() {

		if ( $plan = $this->get_my_first_membership_plan() ) {

			$rules = array();

			// start off by deleting existing rules
			$plan->delete_rules();

			// ensure the plan is not in the trash
			if ( ! in_array( $plan->post->post_status, array( 'auto-draft', 'draft', 'publish' ) ) ) {
				wp_update_post( array(
					'ID'          => $plan->get_id(),
					'post_status' => 'draft',
				) );
			}

			// content restriction rule
			if ( isset( $_POST['restrict_content'], $_POST['content_restriction_rule_content_type'] ) && 'yes' === $_POST['restrict_content'] ) {

				$content_type = $this->get_membership_plan_rule_content_type_group( $_POST['content_restriction_rule_content_type'] );
				$content_name = $this->get_membership_plan_rule_content_type_name( $_POST['content_restriction_rule_content_type'] );

				if ( ! empty( $content_type ) && ! empty( $content_name ) ) {

					$rules[] = new \WC_Memberships_Membership_Plan_Rule( array(
						'id'                 => uniqid( 'rule_', false ),
						'membership_plan_id' => $plan->get_id(),
						'rule_type'          => 'content_restriction',
						'content_type'       => $content_type,
						'content_type_name'  => $content_name,
						'object_ids'         => $this->parse_membership_plan_rule_object_ids( isset( $_POST['content_restriction_rule_object_ids'] ) ? $_POST['content_restriction_rule_object_ids'] : array() ),
					) );
				}
			}

			// product restriction rule
			if ( isset( $_POST['restrict_products'], $_POST['product_restriction_rule_content_type'] ) && 'yes' === $_POST['restrict_products'] ) {

				$product_type  = $this->get_membership_plan_rule_content_type_group( $_POST['product_restriction_rule_content_type'] );
				$product_group = $this->get_membership_plan_rule_content_type_name( $_POST['product_restriction_rule_content_type'] );

				if ( ! empty( $product_type ) && ! empty( $product_group ) ) {

					$rules[] = new \WC_Memberships_Membership_Plan_Rule( array(
						'id'                 => uniqid( 'rule_', false ),
						'membership_plan_id' => $plan->get_id(),
						'rule_type'          => 'product_restriction',
						'content_type'       => $product_type,
						'content_type_name'  => $product_group,
						'access_type'        => isset( $_POST['products_restriction_rule_access_type'] ) && in_array( $_POST['restrict_products'], array( 'view', 'purchase' ), true ) ? $_POST['products_restriction_rule_access_type'] : 'purchase',
						'object_ids'         => $this->parse_membership_plan_rule_object_ids( isset( $_POST['product_restriction_rule_object_ids'] ) ? $_POST['product_restriction_rule_object_ids'] : array() ),
					) );
				}
			}

			// purchasing discount rule
			if ( isset( $_POST['offer_discounts'], $_POST['purchasing_discount_rule_content_type'] ) && 'yes' === $_POST['offer_discounts'] ) {

				$product_type  = $this->get_membership_plan_rule_content_type_group( $_POST['purchasing_discount_rule_content_type'] );
				$product_group = $this->get_membership_plan_rule_content_type_name( $_POST['purchasing_discount_rule_content_type'] );

				if ( ! empty( $product_type ) && ! empty( $product_group ) ) {

					$rules[] = new \WC_Memberships_Membership_Plan_Rule( array(
						'id'                 => uniqid( 'rule_', false ),
						'membership_plan_id' => $plan->get_id(),
						'rule_type'          => 'purchasing_discount',
						'content_type'       => $product_type,
						'content_type_name'  => $product_group,
						'active'             => 'yes',
						'discount_type'      => isset( $_POST['purchasing_discount_rule_discount_type'] ) && in_array( $_POST['purchasing_discount_rule_discount_type'], array( 'percentage', 'amount' ), true ) ? $_POST['purchasing_discount_rule_discount_type'] : 'percentage',
						'discount_amount'    => max( 0, isset( $_POST['purchasing_discount_rule_discount_amount'] ) && is_numeric( $_POST['purchasing_discount_rule_discount_amount'] ) ? (float) $_POST['purchasing_discount_rule_discount_amount'] : 0 ),
						'object_ids'         => $this->parse_membership_plan_rule_object_ids( isset( $_POST['purchasing_discount_rule_object_ids'] ) ? $_POST['purchasing_discount_rule_object_ids'] : array() ),
					) );
				}
			}

			// maybe save new rules
			if ( ! empty( $rules ) ) {
				$plan->set_rules( $rules );
				$plan->compact_rules();
			}
		}
	}


	/**
	 * Renders the "Emails" step.
	 *
	 * In this step the user can toggle member emails and set default schedule.
	 *
	 * @since 1.11.0
	 */
	protected function render_member_emails_step() {

		?>
		<h1><?php esc_html_e( 'Member Emails', 'woocommerce-memberships' ); ?></h1>
		<p><?php esc_html_e( "Now let's determine what happens when a membership ends. Should we send email notifications to members?", 'woocommerce-memberships' ); ?></p>
		<?php

		// fetch email handlers so we can check for enabled status
		$ending_soon      = $this->get_plugin()->get_emails_instance()->get_user_membership_ending_soon_email_instance();
		$ended            = $this->get_plugin()->get_emails_instance()->get_user_membership_ended_email_instance();
		$renewal_reminder = $this->get_plugin()->get_emails_instance()->get_user_membership_renewal_reminder_email_instance();

		$toggles = array(
			'ending_soon'  => array(
				'type'        => 'toggle',
				'name'        => 'ending_soon',
				'value'       => 'yes',
				'allow_html'  => true,
				'input_class' => array( 'toggle-preferences' ),
				'label'       => __( 'Ending Soon', 'woocommerce-memberships' ),
				'description' => $this->get_ending_soon_email_preferences_field( $ending_soon && $ending_soon->is_enabled() ),
				'checked'     => $ending_soon && $ending_soon->is_enabled()
			),
			'expired' => array(
				'type'        => 'toggle',
				'name'        => 'expired',
				'value'       => 'yes',
				'allow_html'  => true,
				'input_class' => array( 'toggle-preferences' ),
				'label'       => __( 'Expired', 'woocommerce-memberships' ),
				'description' => $this->get_expired_email_preferences_field( $ended && $ended->is_enabled() ),
				'checked'     => $ended && $ended->is_enabled(),
			),
			'renewal_reminder' => array(
				'type'        => 'toggle',
				'name'        => 'renewal_reminder',
				'value'       => 'yes',
				'allow_html'  => true,
				'input_class' => array( 'toggle-preferences' ),
				'label'       => __( 'Renewal Reminder', 'woocommerce-memberships' ),
				'description' => $this->get_renewal_reminder_email_preferences_field( $renewal_reminder && $renewal_reminder->is_enabled() ),
				'checked'     => $renewal_reminder && $renewal_reminder->is_enabled(),
			),
			'advanced_emails' => array(
				'type'        => 'toggle',
				'name'        => 'advanced_emails',
				'value'       => 'yes',
				'allow_html'  => true,
				'input_class' => array( 'toggle-preferences' ),
				'label'       => __( 'Advanced Emails', 'woocommerce-memberships' ),
				'description' => $this->get_advanced_emails_preferences_field( true ),
				'checked'     => false,
			),
		);

		foreach ( $toggles as $field_id => $field_data ) {

			if ( ! empty( $field_data['checked'] ) ) {
				$field_data['default'] = $value = 'yes';
				$field_data['custom_attributes'] = array_merge( ! empty( $field_data['custom_attributes'] ) ? $field_data['custom_attributes'] : array(), array( 'checked' => 'checked' ) );
			} else {
				$value = null;
			}

			$this->render_form_field( $field_id, $field_data, $value );
		}
	}


	/**
	 * Returns the form fields HTML for the membership ending soon email preferences.
	 *
	 * @since 1.11.0
	 *
	 * @param bool $show_preferences whether preferences should by shown by default (true) or hidden (false)
	 * @return string HTML
	 */
	private function get_ending_soon_email_preferences_field( $show_preferences = true ) {

		ob_start();

		esc_html_e( 'Send a notification before the membership ends. You could use this to encourage renewals.', 'woocommerce-memberships' );

		?>
		<br />
		<span class="preferences" <?php if ( ! $show_preferences ) { echo 'style="display:none;"'; } ?>>
			<label>
				<small><?php esc_html_e( 'Send this how many days before the membership ends?', 'woocommerce-memberships' ); ?></small>
				<input
					id="ending-soon-email-send-days-before"
					name="ending_soon_email_send_days_before"
					type="number"
					min="1"
					step="1"
					value="<?php echo $this->get_plugin()->get_user_memberships_instance()->get_ending_soon_days(); ?>"
				/>
			</label>
		</span>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the form fields HTML for the expired membership email preferences.
	 *
	 * @since 1.11.0
	 *
	 * @param bool $show_preferences whether preferences should by shown by default (true) or hidden (false)
	 * @return string HTML
	 */
	private function get_expired_email_preferences_field( $show_preferences = true ) {

		ob_start();

		esc_html_e( 'Notify members when a membership ends.', 'woocommerce-memberships' );

		?>
		<span class="preferences" <?php if ( ! $show_preferences ) { echo 'style="display:none;"'; } ?>>
			<label><small><?php esc_html_e( 'This email will be sent immediately as soon as the membership expires.', 'woocommerce-memberships' ); ?></small></label>
		</span>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the form fields HTML for the membership renewal reminder email preferences.
	 *
	 * @since 1.11.0
	 *
	 * @param bool $show_preferences whether preferences should by shown by default (true) or hidden (false)	 *
	 * @return string HTML
	 */
	private function get_renewal_reminder_email_preferences_field( $show_preferences = true ) {

		ob_start();

		esc_html_e( 'Send a notification after a membership ends to encourage renewal.', 'woocommerce-memberships' );

		?>
		<br />
		<span class="preferences" <?php if ( ! $show_preferences ) { echo 'style="display:none;"'; } ?>>
			<label>
				<small><?php esc_html_e( 'Send this how many days after the membership ends?', 'woocommerce-memberships' ); ?></small>
				<input
					id="renewal-reminder-email-send-days-after"
					name="renewal_reminder_email_send_days_after"
					type="number"
					min="1"
					step="1"
					value="<?php echo $this->get_plugin()->get_user_memberships_instance()->get_renewal_reminder_days(); ?>"
				/>
			</label>
		</span>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the form fields HTML for the advanced emails preferences.
	 *
	 * @since 1.11.0
	 *
	 * @param bool $show_preferences whether preferences should by shown by default (true) or hidden (false)	 *
	 * @return string HTML
	 */
	private function get_advanced_emails_preferences_field( $show_preferences = true ) {

		ob_start();

		printf(
			/* translators: Placeholders: %1$s - opening <a> HTML link tag, %2$s - closing </a> HTML link tag */
			esc_html__( 'Send advanced emails like welcome or win-back series %1$susing Jilt%2$s. Try it free for 14 days!', 'woocommerce-memberships' ),
			'<a href="https://jilt.com/go/memberships-onboarding" target="__blank">', '</a>'
		);

		?>
		<br /><br />
		<span class="preferences" <?php if ( ! $show_preferences ) { echo 'style="display:none;"'; } ?>><em>
			<?php printf(
				/* translators: Placeholders: %1$s - opening <a> HTML link tag, %2$s - closing </a> HTML link tag */
				esc_html__( 'This plugin will be installed and activated for you: %1$sJilt for WooCommerce%2$s', 'woocommerce-memberships' ),
				'<br /><strong><a href="https://wordpress.org/plugins/jilt-for-woocommerce/">',
				'</a></strong>'
			); ?>
		</em></span>
		<?php

		return ob_get_clean();
	}


	/**
	 * Saves preferences for member emails.
	 *
	 * @since 1.11.0
	 */
	protected function save_member_emails_preferences() {

		if ( $ending_soon_email = $this->get_plugin()->get_emails_instance()->get_user_membership_ending_soon_email_instance() ) {

			if ( isset( $_POST['ending_soon'] ) && 'yes' === $_POST['ending_soon'] ) {
				$ending_soon_email->enable();
				$ending_soon_email->set_schedule( max( 1, isset( $_POST['ending_soon_email_send_days_before'] ) && is_numeric( $_POST['ending_soon_email_send_days_before'] ) ? absint( $_POST['ending_soon_email_send_days_before'] ) : $ending_soon_email->get_schedule() ) );
			} else {
				$ending_soon_email->disable();
			}
		}

		if ( $ended_email = $this->get_plugin()->get_emails_instance()->get_user_membership_ended_email_instance() ) {

			$data['ended_email']  = $ended_email;

			if ( isset( $_POST['expired'] ) && 'yes' === $_POST['expired'] ) {
				$ended_email->enable();
			} else {
				$ended_email->disable();
			}
		}

		if ( $renewal_reminder = $this->get_plugin()->get_emails_instance()->get_user_membership_renewal_reminder_email_instance() ) {

			if ( isset( $_POST['renewal_reminder'] ) && 'yes' === $_POST['renewal_reminder'] ) {
				$renewal_reminder->enable();
				$renewal_reminder->set_schedule( max( 1, isset( $_POST['renewal_reminder_email_send_days_after'] ) && is_numeric( $_POST['renewal_reminder_email_send_days_after'] ) ? absint( $_POST['renewal_reminder_email_send_days_after'] ) : $ending_soon_email->get_schedule() ) );
			} else {
				$renewal_reminder->disable();
			}
		}

		// maybe install & activate Jilt for WooCommerce
		if ( isset( $_POST['advanced_emails'] ) && 'yes' === $_POST['advanced_emails'] ) {

			try {

				\WC_Install::background_installer( 'jilt-for-woocommerce', [
					'name'      => __( 'Jilt for WooCommerce', 'woocommerce-memberships' ),
					'repo-slug' => 'jilt-for-woocommerce',
				] );

				update_option( 'wc_memberships_onboarding_wizard_install_jilt', 'yes' );

				$this->get_plugin()->log( 'Jilt for WooCommerce installed from Setup Wizard.' );

			} catch ( \Exception $e ) {

				$this->get_plugin()->log( sprintf( 'An error occurred while trying to install Jilt for WooCommerce from Setup Wizard: %s', $e->getMessage() ) );
			}
		}
	}


	/**
	 * Returns extra steps for the last screen of the Setup Wizard.
	 *
	 * @since 1.11.0
	 *
	 * @return array associative array of extra steps
	 */
	protected function get_next_steps() {

		$steps = array();

		if ( $plan = $this->get_my_first_membership_plan() ) {

			$steps['view-plan'] = array(
				'name'         => __( 'View my plan', 'woocommerce-memberships' ),
				'label'        => __( 'Review your plan', 'woocommerce-memberships' ),
				'description'  => __( 'Your first plan has been started, you can now add more rules and publish it when you are ready.', 'woocommerce-memberships' ),
				'url'          => get_edit_post_link( $plan->get_id() ),
				'button_class' => 'button button-primary button-large',
			);
		}

		$steps['view-docs'] = array(
			'name'         => __( 'Visit docs', 'woocommerce-memberships' ),
			'label'        => __( 'Memberships knowledge base', 'woocommerce-memberships' ),
			'description'  => __( 'Check out the Memberships documentation to learn more about plan settings and advanced features.', 'woocommerce-memberships' ),
			'url'          => $this->get_plugin()->get_documentation_url(),
			'button_class' => 'button button-large',
		);

		return $steps;
	}


	/**
	 * Returns additional actions shown at the bottom of the last step of the Setup Wizard.
	 *
	 * @since 1.11.0
	 *
	 * @return array associative array of labels and URLs meant for action buttons
	 */
	protected function get_additional_actions() {

		return array(
			__( 'Review settings', 'woocommerce-memberships' ) => $this->get_plugin()->get_settings_url(),
			__( 'View plans', 'woocommerce-memberships' )      => admin_url( 'edit.php?post_type=wc_membership_plan' ),
			__( 'Add members', 'woocommerce-memberships' )     => admin_url( 'edit.php?post_type=wc_user_membership' ),
		);
	}


	/**
	 * Renders the newsletter content after the next steps in the last Setup Wizard screen.
	 *
	 * @since 1.11.0
	 */
	protected function render_after_next_steps() {

		$email   = wp_get_current_user()->user_email;
		$open_a  = '<a href="https://www.skyverge.com/newsletter/?email=' . $email . '&mc_ref=MEMBERSHIPS_ONBOARDING">';
		$close_a = '</a>';

		?>
		<div class="newsletter-prompt">
			<p>
				<?php printf(
					/* translators: Placeholders: %1$s - opening <a> HTML link tag, %2$s - closing </a> HTML link tag, %3$s - opening <a> HTML link tag, %4$s - closing </a> HTML link tag */
					esc_html__( 'Want to keep learning? Check out our %1$smonthly newsletter%2$s where we share updates, tutorials, and sneak peeks for new development. %3$sSign up &rarr;%4$s', 'woocommerce-memberships' ),
					$open_a, $close_a,
					$open_a, $close_a
				); ?>
			</p>
		</div>
		<?php
	}


	/**
	 * Returns the membership plan being created during setup.
	 *
	 * @since 1.11.0
	 *
	 * @return null|\WC_Memberships_Membership_Plan
	 */
	public function get_my_first_membership_plan() {

		$plan_id = $this->get_my_first_membership_plan_id();
		$plan    = $plan_id > 0 ? wc_memberships_get_membership_plan( $plan_id ) : null;

		return $plan instanceof \WC_Memberships_Membership_Plan ? $plan : null;
	}


	/**
	 * Creates a membership plan.
	 *
	 * @since 1.11.0
	 *
	 * @return \WC_Memberships_Membership_Plan
	 */
	private function create_my_first_membership_plan() {

		// set a unique slug
		$name  = sanitize_title( $this->default_plan_name );
		$posts = get_posts( array(
			'name'           => $name,
			'post_type'      => 'wc_membership_plan',
			'posts_per_page' => 1,
		) );

		if ( ! empty( $posts[0] ) ) {
			$name = uniqid ( "{$name}_", false );
		}

		$plan_id = wp_insert_post( array(
			'post_author' => get_current_user_id(),
			'post_type'   => 'wc_membership_plan',
			'post_status' => 'draft',
			'post_title'  => sanitize_text_field( $this->default_plan_name ),
			'post_name'   => $name,
		) );

		if ( is_numeric( $plan_id ) ) {

			$this->set_my_first_membership_plan_id( (int) $plan_id );

			$plan = new \WC_Memberships_Membership_Plan( $plan_id );

			// set the members area sections as enabled by default
			$plan->set_members_area_sections( array_keys( wc_memberships_get_members_area_sections( $plan->get_id() ) ) );

		} else {

			$this->render_error( __( 'Could not create a membership plan. Please reload this page to try again.', 'woocommerce-memberships' ) );

			$plan = new \WC_Memberships_Membership_Plan( 0 );

			$plan->name = $this->default_plan_name;
		}

		return $plan;
	}


	/**
	 * Returns the plan being created in the wizard, or creates it if it doesn't exist yet.
	 *
	 * @since 1.11.0
	 *
	 * @return \WC_Memberships_Membership_Plan
	 */
	private function get_or_create_my_first_membership_plan() {

		$plan = $this->get_my_first_membership_plan();

		if ( ! $plan ) {
			$plan = $this->create_my_first_membership_plan();
		}

		return $plan;
	}


	/**
	 * Returns the first membership plan rule of a given type.
	 *
	 * @since 1.11.0
	 *
	 * @param \WC_Memberships_Membership_Plan $plan membership plan object
	 * @param string $which_rule which rule to return
	 * @return \WC_Memberships_Membership_Plan_Rule rule object
	 */
	private function get_my_first_membership_plan_rule( \WC_Memberships_Membership_Plan $plan, $which_rule ) {

		$args = array();

		switch ( $which_rule ) {

			case 'content_restriction' :
				$rules = $plan->get_content_restriction_rules();
			break;

			case 'product_restriction' :
				$rules = $plan->get_product_restriction_rules();
			break;

			case 'purchasing_discount' :
				$rules = $plan->get_purchasing_discount_rules();
				$args  = array( 'active' => 'yes' );
			break;

			default :
				$rules = array();
			break;
		}

		if ( ! empty( $rules ) ) {

			$rule = current( $rules );

		} else {

			$rule = new \WC_Memberships_Membership_Plan_Rule( wp_parse_args( $args, array(
				'membership_plan_id' => $plan->get_id(),
				'rule_type'          => $which_rule,
			) ) );
		}

		return $rule;
	}


	/**
	 * Parses a string or an array of object ID for a plan rule (helper method).
	 *
	 * @since 1.11.0
	 *
	 * @param int[]|string $object_ids rule object IDs
	 * @return int[]
	 */
	private function parse_membership_plan_rule_object_ids( $object_ids ) {

		if ( is_string( $object_ids ) ) {
			$object_ids = array_map( 'trim', explode( ',', $object_ids ) );
		}

		if ( ! empty( $object_ids ) && is_array( $object_ids ) ) {
			$object_ids = array_unique( array_map( 'absint', $object_ids ) );
		} else {
			$object_ids = array();
		}

		return $object_ids;
	}


	/**
	 * Parses content type from input (helper method).
	 *
	 * @since 1.11.0
	 *
	 * @param string $content a content type string, e.g. `post_type|post`
	 * @return string[]
	 */
	private function parse_membership_plan_rule_content_type( $content ) {

		return explode( '|', $content );
	}


	/**
	 * Gets the content type group from a content type input (helper method).
	 *
	 * @since 1.11.0
	 *
	 * @param string $content input
	 * @return null|string eg. post_type, taxonomy...
	 */
	private function get_membership_plan_rule_content_type_group( $content ) {

		$content_type = $this->parse_membership_plan_rule_content_type( $content );

		return isset( $content_type[0] ) ? $content_type[0] : null;
	}


	/**
	 * Gets the content type name from a content type input (helper method).
	 *
	 * @since 1.11.0
	 *
	 * @param string $content input
	 * @return null|string e.g. post, page, category, tag...
	 */
	private function get_membership_plan_rule_content_type_name( $content ) {

		$content_type = $this->parse_membership_plan_rule_content_type( $content );

		return isset( $content_type[1] ) ? $content_type[1] : null;
	}


	/**
	 * Gets the first plan options as they are progressively updated in the wizard.
	 *
	 * @since 1.11.0
	 *
	 * @return int|null
	 */
	public function get_my_first_membership_plan_id() {

		return get_option( $this->membership_plan_id_key, null );
	}


	/**
	 * Stores the ID of the membership plan created during setup into an option.
	 *
	 * @since 1.11.0
	 *
	 * @param int $plan_id the membership plan ID
	 * @return bool success
	 */
	public function set_my_first_membership_plan_id( $plan_id ) {

		return is_int( $plan_id ) && update_option( $this->membership_plan_id_key, $plan_id );
	}


	/**
	 * Deletes the recorded membership plan ID created in the Setup Wizard.
	 *
	 * Note: this does not delete the plan, if exists.
	 *
	 * @since 1.11.0
	 */
	public function delete_my_first_membership_plan_id() {

		return delete_option( $this->membership_plan_id_key );
	}


}
