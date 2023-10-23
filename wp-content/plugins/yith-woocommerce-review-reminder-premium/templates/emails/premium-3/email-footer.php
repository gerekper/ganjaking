<?php
/**
 * Custom Template footer
 *
 * @package YITH\ReviewReminder
 * @var $unsubscribe_url
 * @var $unsubscribe_text
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
</td>
</tr>
<tr>
	<td id="footer">
		<a href="<?php echo esc_url( $unsubscribe_url ); ?>"><?php echo esc_html( $unsubscribe_text ); ?></a>
	</td>
</tr>
<tr>
	<td id="subfooter">
		<?php echo wp_kses_post( wptexturize( wpautop( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ) ) ); ?>
	</td>
</tr>
</table>
<!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
</td>
</tr>
</table>
</body>
</html>
