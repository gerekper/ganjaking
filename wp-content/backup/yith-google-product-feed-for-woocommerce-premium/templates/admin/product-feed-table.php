
<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

$functions =  YITH_Google_Product_Feed()->functions;
$feed_url = $functions->create_feed('google','xml');
?>

<div>
    <div><?php esc_html_e('Your feed is available here: ','yith-google-product-feed-for-woocommerce'); ?><a target="_blank" href="<?php echo $feed_url ?>"><?php echo $feed_url?></a></div>

</div>
v>