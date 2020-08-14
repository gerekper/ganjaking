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

        <table class="widefat yith-wcgpf-table ">
            <thead>
            <tr>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td width="30%"><?php  esc_html_e('Merchant', 'yith-google-product-feed-for-woocommerce'); ?></td>
                <td>
                    <?php echo yith_wcgpf_get_dropdown( array(
                            'id'      => 'yith-wcgpf-merchant',
                            'name'    => 'yith-merchant',
                            'class'   => 'yith-wcgpf-merchant yith-wcgpf-select',
                            'style'   => 'width:200px;',
                            'options' => $functions->merchant(),
                            'value'   => isset($save_feed_data['merchant']) ? $save_feed_data['merchant'] : '',
                        )
                    ); ?>
                </td>
            </tr>

            <tr>
                <td><?php  esc_html_e('Feed Type', 'yith-google-product-feed-for-woocommerce'); ?></td>
                <td>
                    <?php echo yith_wcgpf_get_dropdown( array(
                            'id'      => 'yith-wcgpf-feed-type',
                            'name'    => 'yith-feed-type',
                            'class'   => 'yith-wcgpf-feed-type yith-wcgpf-select',
                            'style'   => 'width:200px;',
                            'options' => $functions->type_file(),
                            'value'   => isset($save_feed_data['feed_type']) ? $save_feed_data['feed_type'] : '',
                        )
                    ); ?>
                </td>
            </tr>
            <tr class="yith-wcgpf-use-template yith_wcgpf_style">
                <td><?php  esc_html_e('Use a template to configure this feed', 'yith-google-product-feed-for-woocommerce'); ?></td>
                <td>
                    <input type="checkbox" name="yith-wcgpf-use-template" id="yith-wcgpf-check-template" value=""> <?php echo esc_html__('Check this option
                     to show feed templates','yith-google-product-feed-for-woocommerce') ?> <br>
                </td>
            </tr>
            <tr class="yith-wcgpf-template-feed yith_wcgpf_style"">
            <td><?php  esc_html_e('Which template do you want to use to configure your feed?', 'yith-google-product-feed-for-woocommerce'); ?></td>
            <td>
                <?php echo yith_wcgpf_get_dropdown( array(
                        'id'      => 'yith-wcgpf-select-template',
                        'name'    => 'template_feed',
                        'class'   => 'yith-wcgpf-select-template yith-wcgpf-select',
                        'style'   => 'width:200px;',
                        'options' => $functions->get_list_template(),
                        'value'   => isset($save_feed_data['template_feed']) ? $save_feed_data['template_feed'] : '',
                    )
                );?>
            </td>
            </tr>
            <?php do_action('yith_wcgpf_add_options',$save_feed_data); ?>
            </tbody>
        </table>

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
            <p><?php esc_html_e('If you need to know how to format your product information feed for Google Shopping, visit the following link: ','yith-google-product-feed-for-woocommerce')?><a href="https://support.google.com/merchants/answer/7052112?" target="_blank">https://support.google.com/merchants/answer/7052112?</a></p>
        </div>
        <?php do_action('yith_wcgpf_add_filter_and_conditions',$post); ?>
    </form>
</div>