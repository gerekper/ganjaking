<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @version 1.3.4
 */

if( ! defined( 'YITH_WOCC' ) ) {
    exit;
}
?>

<div id="yith-wocc-modal-overlay"></div>

<div id="yith-wocc-modal">

	<div class="yith-wocc-modal-content">

		<a href="#" class="yith-wacp-close occ-icon-cancel"></a>
        <div class="woocommerce">
            <?php echo isset( $content ) ? $content : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
	</div>

</div>