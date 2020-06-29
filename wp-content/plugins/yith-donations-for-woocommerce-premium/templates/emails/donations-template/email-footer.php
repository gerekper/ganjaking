<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

$footer_css     = 'padding: 25px 10px; text-align: center; line-height: 20px; font-size: 13px;';
$footer_a_css   = 'text-decoration: underline; color: #769ed2; font-weight: bold;';
$subfooter_css  = 'padding: 10px; text-align: center; line-height: 20px; font-size: 12px; color:#6b7785;';


?>
</td>
</tr>
<tr>
    <td style="<?php echo $subfooter_css ?>">
        <?php echo wpautop( wp_kses_post( wptexturize( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ) ) ); ?>
    </td>
</tr>
</table>
<!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
</td>
</tr>
</table>
</body>
</html>