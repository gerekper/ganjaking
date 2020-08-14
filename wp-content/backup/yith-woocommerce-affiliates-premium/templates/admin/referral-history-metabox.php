<?php
/**
 * Order Referral MetaBox
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! empty( $referral_history_users ) ):
?>
	<div class="history-section">
		<?php foreach ( $referral_history_users as $user ): ?>
			<div class="referral-history-item">
				<span><?php echo $user['username'] ?></span>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>