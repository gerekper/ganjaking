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

namespace SkyVerge\WooCommerce\Memberships\Blocks;

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Membership directory block.
 *
 * @since 1.23.0
 */
class Members_Directory extends Block implements Dynamic_Content_Block {


	/**
	 * Block constructor.
	 *
	 * @since 1.23.0
	 */
	public function __construct() {

		$this->block_type = 'directory';
		$this->register_attributes();
		parent::__construct();

	}


	/**
	 * Register block attribute for frontend rendering.
	 *
	 * @since 1.23.0
	 */
	private function register_attributes( ) {
		$this->block_args['attributes'] = [
				'membershipPlans' => [
					'type' => 'array',
					'default' => []
				],
				'membershipStatus' => [
					'type' => 'array',
					'default' => []
				],
				'profileFields' => [
					'type' => 'array',
					'default' => []
				],
				'perPage' => [
					'type' => 'integer',
					'default' => 8
				],
				'columns' => [
					'type' => 'integer',
					'default' => 2
				],
				'avatar' => [
					'type' => 'boolean',
					'default' => true
				],
				'avatarSize' => [
					'type' => 'integer',
					'default' => 100
				],
				'showBio' => [
					'type' => 'boolean',
					'default' => true
				],
				'showEmail' => [
					'type' => 'boolean',
					'default' => true
				],
				'showPhone' => [
					'type' => 'boolean',
					'default' => true
				],
				'showAddress' => [
					'type' => 'boolean',
					'default' => true
				],
				'align' => [
					'type' => 'string',
					'default' => 'wide'
				],
				'btnColor' => [
					'type' => 'string',
					'default' => '#5c7676'
				],
				'btnHoverColor' => [
					'type' => 'string',
					'default' => '#5c7676'
				],
			];
	}

	/**
	 * Renders the block content.
	 *
	 * Displays Membership Directory
	 *
	 * @since 1.23.0
	 *
	 * @param array $attributes block attributes
	 * @param string $content HTML content
	 * @return string HTML
	 */
	public function render( $attributes, $content ) {

		wp_enqueue_style( 'select2' );
		$style = "--wcm-btn-color:{$attributes[ 'btnColor' ]};--wcm-btn-hover-color:{$attributes[ 'btnHoverColor' ]};";
?>
	<div class="wc-memberships-directory-container  wcm-directory-front-end align<?php echo esc_attr( $attributes[ 'align' ] ); ?>"
	data-directory-id='<?php echo esc_attr( wp_unique_id( 'wcm-directory-' ) );?>'
	data-directory-data='<?php echo json_encode( $attributes); ?>' style="<?php echo $style; ?>">
		<div class="wmc-loader">
				<div class="wcm-spinner"><div></div><div></div><div></div><div></div></div>
		</div>
		<div class="wc-memberships-directory-filter-wrapper" >
			<div class="search-wrapper">
				<input type="text" placeholder="<?php esc_html_e( 'Search...', 'woocommerce-memberships' ); ?>" class="wcm-input wcm-search-input">
				<input type="button" class="wcm-btn wcm-search-btn">
			</div>
			<div class="filter-wrapper">
				<select class="wcm-select wcm-input wcm-plans" multiple data-placeholder="<?php esc_html_e( 'All Plans', 'woocommerce-memberships' ); ?>">
				<option value=""><?php esc_html_e( 'All Plans', 'woocommerce-memberships' ); ?></option>
				<?php
					foreach ( wc_memberships_get_membership_plans( [ 'post__in' => $attributes[ 'membershipPlans' ] ] ) as $membership_plan ) {
				?>
						<option
							value="<?php echo esc_attr( $membership_plan->get_id() ); ?>" >
							<?php echo esc_html( $membership_plan->get_name() ) ?>
						</option>
				<?php
					}
				?>
				</select>
				<select class="wcm-select wcm-input wcm-status" multiple data-placeholder="<?php esc_html_e( 'All Statuses', 'woocommerce-memberships' ); ?>">
					<option value=""><?php esc_html_e( 'All Statuses', 'woocommerce-memberships' ); ?></option>
					<?php
						foreach ( wc_memberships_get_user_membership_statuses() as $status_key => $membership_status) {
							if( ! empty( $attributes[ 'membershipStatus' ] )  ) {
								if( in_array( $status_key, $attributes[ 'membershipStatus' ] ) ) {
									?>
											<option
												value="<?php echo esc_attr( $status_key ); ?>" >
												<?php echo esc_html( $membership_status['label'] ); ?>
											</option>
									<?php
								}
							} else {
					?>
								<option
									value="<?php echo esc_attr( $status_key ); ?>" >
									<?php echo esc_html( $membership_status['label'] ); ?>
								</option>
					<?php
							}
						}
					?>
				</select>
				<input type="button" class="wcm-btn wcm-filter-btn" value="<?php esc_html_e( 'Filter', 'woocommerce-memberships' ); ?>">
			</div>
		</div>
		<div class="wcm-directory-list-wrapper columns-<?php echo esc_attr( $attributes['columns'] ); ?>" >
			<div class="directory-placeholder-box">

			</div>
		</div>
		<div class="wcm-pagination-wrapper" data-current-page='1' data-total-pages='1' data-per-page="<?php echo esc_attr( $attributes['perPage'] )?>">
				<a href="#" class="wcm-pagination previous">&#8592; <span><?php esc_html_e( 'Previous', 'woocommerce-memberships' ); ?></span></a>
				<a href="#" class="wcm-pagination next"><span><?php esc_html_e( 'Next', 'woocommerce-memberships' ); ?></span> &#8594;</a>
		</div>
	</div>
		<?php
		$content = ob_get_clean();
		return $content;
	}

}
