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
$save_feed_data = get_post_meta($post->ID,'yith_wcgpf_save_feed',true);
?>
    <div class="wrap" id="yith-div-make-feed" data-yithpost="<?php echo $post->ID ?>">
        <form action="" id="yith-make-feed" class="yith-make-feed"  method="post">
            <br/><br/>
            <div id="yith-wcgpf-template-feed-merchant-page">
                <h2 id="yith-wcgpf-type-custom"><?php esc_html_e('Custom feed configuration', 'yith-google-product-feed-for-woocommerce') ?></h2>
                <h2 id="yith-wcgpf-type-template" style="display: none;"><?php esc_html_e('Custom feed configuration. Based on:', 'yith-google-product-feed-for-woocommerce') ?></h2>
                <div class="wf-tab-content">
                    <table class="yith_wcgpf_template_table widefat yith_wcgpf_lenght" id="yith-wcgpf-template-table">
                        <thead class="yith_wcgpf_template_table_thead">
                        <tr>
                            <th></th>
                            <th><?php esc_html_e('Attributes','yith-google-product-feed-for-woocommerce') ?></th>
                            <th><?php esc_html_e('Prefix','yith-google-product-feed-for-woocommerce') ?></th>
                            <th><?php esc_html_e('Value','yith-google-product-feed-for-woocommerce') ?></th>
                            <th><?php esc_html_e('Suffix','yith-google-product-feed-for-woocommerce') ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody  class="yith_wcgpf_template_table_thead_tbody" id="yith-wcgpf-template-create-feed" >

                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="7">
                                <button type="button" class="button button-default" id="yith-wcgpf-add-new-row"><?php esc_html_e('Add new row','yith-google-product-feed-for-woocommerce')?></button>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php do_action('yith_wcgpf_add_filter_and_conditions',$post); ?>
        </form>
    </div>