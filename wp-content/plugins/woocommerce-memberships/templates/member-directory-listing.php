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
 * @copyright Copyright (c) 2014-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Renders a listing for each member in the directory.
 *
 * @var array $shortcode shortcode attributes for the listing output
 * @var string[] $plans array of plans for the current member user
 *
 * @version 1.21.0
 * @since 1.21.0
 */

?>
<div class="member-listing member-user-<?php echo sanitize_html_class( get_the_author_meta( 'ID' ) ); ?>" data-user-id="<?php echo sanitize_html_class( get_the_author_meta( 'ID' ) ); ?>">
	<?php

	/**
	 * Fires before outputting a member card in the directory.
	 *
	 * @since 1.21.0
	 *
	 * @param int $id the current user membership ID
	 * @param int $user_id the current member's user ID
	 * @param array $shortcode the shortcode attributes
	 */
	do_action( 'wc_memberships_member_directory_before_member_card', get_the_ID(), get_the_author_meta( 'ID' ), $shortcode );

	if ( 'yes' === $shortcode['avatars'] ) :

		?>
		<div class="member-avatar">
			<?php echo get_avatar( get_the_author_meta( 'ID' ), (int) $shortcode['avatar_size'] ); ?>
		</div>
		<?php

	endif;

	?>
	<div class="member-details">
		<h3 class="member-name"><?php the_author(); ?></h3>
		<?php

		if ( 'any' === $shortcode['plans'] ) :

			?>
			<span>
				<label><?php echo esc_html( _n( 'Plan:', 'Plans:', count( $plans ), 'woocommerce-memberships' ) ); ?></label> <span class="member-plans"><?php echo esc_html( implode( ', ', $plans ) ); ?></span>
			</span>
			<?php

		endif;

		/**
		 * Fires before outputting a member's bio in the directory.
		 *
		 * @since 1.21.0
		 *
		 * @param int $id the current user membership ID
		 * @param int $user_id the current member's user ID
		 * @param array $shortcode the shortcode attributes
		 */
		do_action( 'wc_memberships_member_directory_before_member_bio', get_the_ID(), get_the_author_meta( 'ID' ), $shortcode );

		if ( 'yes' === $shortcode['bios'] && get_the_author_meta( 'description' ) ) :

			?>
			<p class="member-bio"><em><?php the_author_meta( 'description' ); ?></em></p>
			<?php

		endif;

		/**
		 * Fires after outputting a member card in the directory.
		 *
		 * @since 1.21.0
		 *
		 * @param int $id the current user membership ID
		 * @param int $user_id the current member's user ID
		 * @param array $shortcode the shortcode attributes
		 */
		do_action( 'wc_memberships_member_directory_after_member_card', get_the_ID(), get_the_author_meta( 'ID' ), $shortcode );

		?>
	</div>
</div>
