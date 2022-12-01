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

namespace SkyVerge\WooCommerce\Memberships\Integrations\Courseware;

use SkyVerge\WooCommerce\Memberships\Integrations\Courseware;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Admin class for Courseware/eLearning integrations.
 *
 * @NOTE in the future individual integrations can extend this class with their own implementation and perhaps this can be turned into an abstract {unfulvio 2021-04-29}
 *
 * @since 1.22.0
 */
class Admin {


	/** @var Courseware $integration integration instance */
	private $integration;


	/**
	 * Courseware admin constructor.
	 *
	 * @since 1.22.0
	 *
	 * @param Courseware $integration instance of the current integration
	 */
	public function __construct( Courseware $integration ) {

		$this->integration = $integration;

		$this->add_hooks();
	}


	/**
	 * Adds hooks.
	 *
	 * @since 1.22.0
	 */
	private function add_hooks() {

		add_action( 'wc_memberships_restriction_rule_access_schedule_field', [ $this, 'output_course_auto_enroll_option' ], 20, 2 );
		add_action( 'wc_membership_plan_options_membership_plan_data_restrict_content', [ $this, 'toggle_course_auto_enroll_option' ] );
	}


	/**
	 * Gets the integration instance.
	 *
	 * @since 1.22.0
	 *
	 * @return Courseware
	 */
	private function get_integration() : Courseware {

		return $this->integration;
	}


	/**
	 * Outputs a checkbox option to auto-enroll members in the courses specified by a plan rule.
	 *
	 * @internal
	 *
	 * @since 1.22.0
	 *
	 * @param \WC_Memberships_Membership_Plan_Rule $rule rule object
	 * @param int|string $index
	 */
	public function output_course_auto_enroll_option( $rule, $index ) {

		if ( ! $rule instanceof \WC_Memberships_Membership_Plan_Rule ) {
			return;
		}

		$type                   = $rule->get_rule_type();
		$auto_enroll            = $this->get_integration()->does_membership_plan_rule_auto_enroll_in_course( $rule );
		$is_course_content_type = in_array( $rule->get_content_type_key(), $this->get_course_content_type_keys(), true )
		                          || Framework\SV_WC_Helper::is_current_screen( $this->get_integration()->get_course_post_type() );

		?>
		<span class="rule-control-group rule-control-group-course-auto-enroll <?php if ( ! $is_course_content_type ) : ?>hide<?php endif; ?> js-show-if-is-course">

			<label class="label-checkbox">

				<input type="hidden"
					   name="_<?php echo esc_attr( $type ); ?>_rules[<?php echo $index; ?>][meta_data][<?php echo Courseware::COURSE_AUTO_ENROLL_PLAN_RULE_META_KEY ?>]"
					   value="no" />

				<input type="checkbox"
					   name="_<?php echo esc_attr( $type ); ?>_rules[<?php echo $index; ?>][meta_data][<?php echo Courseware::COURSE_AUTO_ENROLL_PLAN_RULE_META_KEY ?>]"
					   id="_<?php echo esc_attr( $type ); ?>_rules_<?php echo $index; ?>_course_auto_enroll"
					   value="yes" <?php checked( $auto_enroll, true ); ?>
					   class="course-auto-enroll"
					   <?php if ( ! $rule->current_user_can_edit() ) : ?>disabled<?php endif; ?> />

				<?php esc_html_e( 'Auto-enroll', 'woocommerce-memberships' ); ?>
			</label>

		</span>
		<?php
	}


	/**
	 * Adds an inline JavaScript snippet to toggle the auto-enroll checkbox option.
	 *
	 * @internal
	 *
	 * @since 1.22.0
	 */
	public function toggle_course_auto_enroll_option() {

		wc_enqueue_js( "
			( function( $ ) {
				var course_rule_types = " . json_encode( $this->get_course_content_type_keys() ) . ";

				$( '.js-rules' ).on( 'change', '.js-content-type', function() {
					var checkbox = $( this ).closest( 'tbody' ).find( '.rule-control-group-course-auto-enroll' );
					if ( course_rule_types.indexOf( $( this ).find( 'option:selected' ).val() ) !== -1 ) {
						checkbox.removeClass( 'hide' );
					} else {
						checkbox.addClass( 'hide' );
					}
				} );
			} ) ( jQuery );
		" );
	}


	/**
	 * Gets the content type keys that indicate a rule may apply to a course.
	 *
	 * @since 1.22.0
	 *
	 * @return string[]
	 */
	private function get_course_content_type_keys() : array {

		$post_type_name = $this->get_integration()->get_course_post_type();

		$course_content_type_keys = [ 'post_type|' . $post_type_name ];

		foreach( get_object_taxonomies( $post_type_name ) as $taxonomy_name ) {
			$course_content_type_keys[] = 'taxonomy|' . $taxonomy_name;
		}

		return $course_content_type_keys;
	}


}
