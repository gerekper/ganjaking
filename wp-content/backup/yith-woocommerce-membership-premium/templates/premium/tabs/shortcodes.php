<?php
// Exit if accessed directly
!defined( 'YITH_WCMBS' ) && exit();
?>

<?php

$orderby_values = 'ID, author, title, date, modified, parent, rand';

$shortcodes = array(
    'membership_protected_links' => array(
        'title'    => __( 'Membership protected links', 'yith-woocommerce-membership' ),
        'code'     => '[membership_protected_links]',
        'desc'     => __( 'Prints the protected links set into a post, a page or a product', 'yith-woocommerce-membership' ),
        'args'     => array(
            'post_id'    => __( 'The ID of the post, the page or the product that contains the protected links. If not set, it uses global post ID', 'yith-woocommerce-membership' ),
            'link_class' => __( 'The CSS class of download links', 'yith-woocommerce-membership' ),
        ),
        'examples' => array(
            '[membership_protected_links]',
            '[membership_protected_links post_id="123"]',
            '[membership_protected_links link_class="red-link"]',
            '[membership_protected_links post_id="123" link_class="red-link"]',
        ),
    ),

    'membership_protected_content'        => array(
        'title'    => __( 'Membership protected content', 'yith-woocommerce-membership' ),
        'code'     => '[membership_protected_content]content[/membership_protected_content]',
        'desc'     => __( 'Show the content in base of membership plan', 'yith-woocommerce-membership' ),
        'args'     => array(
            'plan_id' => __( 'The ID of the allowed plans, separed with commas. If not set, it shows the content to members with any plan', 'yith-woocommerce-membership' ),
            'user'    => __( 'The type of user that can view the content. Possible values: member, non-member, guest, logged. Default: member', 'yith-woocommerce-membership' ),
        ),
        'examples' => array(
            '[membership_protected_content]',
            '[membership_protected_content plan_id="123"]',
            '[membership_protected_content plan_id="12,45"]',
            '[membership_protected_content plan_id="12" user="non-member"]',
            '[membership_protected_content user="guest"]',
            '[membership_protected_content user="logged"]',
        ),
    ),
    'protected_media'                     => array(
        'title' => __( 'Protected Media', 'yith-woocommerce-membership' ),
        'code'  => '[protected_media id=123]content[/protected_media]',
        'desc'  => __( 'Print protected media link. Please note: media should be set as protected before printing the link', 'yith-woocommerce-membership' ),
        'args'  => array(
            'id'      => __( 'ID of the protected media', 'yith-woocommerce-membership' ),
            'content' => __( 'Linked text', 'yith-woocommerce-membership' ),
        ),
    ),
    'membership_items'                    => array(
        'title'    => __( 'Membership Level Items', 'yith-woocommerce-membership' ),
        'code'     => '[membership_items plan=237]',
        'desc'     => __( 'Print the list of items included in a membership plan', 'yith-woocommerce-membership' ),
        'args'     => array(
            'id'      => __( 'ID of the membership plan', 'yith-woocommerce-membership' ),
            'orderby' => sprintf( __( 'Sort retrieved items by parameter. Possible values: %s', 'yith-woocommerce-membership' ), $orderby_values ),
            'order'   => __( 'Set ASC for ascending order or DESC for descending one (default: ASC)', 'yith-woocommerce-membership' ),
            'style'   => __( 'If style is set to "default", shortcode style is the same as in membership plan list; else, if it is not set, shortcode style can be set within membership plan', 'yith-woocommerce-membership' ),
        ),
        'examples' => array(
            '[membership_items plan=237 style="default"]',
            '[membership_items plan=237 orderby=title style="default"]',
            '[membership_items plan=237 orderby=ID order=DESC]',
        ),
    ),
    'membership_download_product_links'   => array(
        'title'    => __( 'Product download links', 'yith-woocommerce-membership' ),
        'code'     => '[membership_download_product_links]',
        'desc'     => __( 'Print product download links', 'yith-woocommerce-membership' ),
        'args'     => array(
            'id'         => __( 'Product ID. If not specified, it prints current product download links', 'yith-woocommerce-membership' ),
            'link_class' => __( 'The css class of download links', 'yith-woocommerce-membership' ),
            'content'    => __( 'Linked text. If it is not set, the linked text will be the name of the file set in product', 'yith-woocommerce-membership' ),
        ),
        'examples' => array( '[membership_download_product_links id=111 link_class="button button-test"]DOWNLOAD[/membership_download_product_links]' ),
    ),
    'membership_history'                  => array(
        'title'    => __( 'Membership history', 'yith-woocommerce-membership' ),
        'code'     => '[membership_history]',
        'desc'     => __( 'Print the list of every membership plan ever joined by current user', 'yith-woocommerce-membership' ),
        'args'     => array(
            'id'    => __( 'Membership ID. If specified, it prints details of the membership associated to this ID. Else, it prints the list of every membership plan ever joined by current user', 'yith-woocommerce-membership' ),
            'title' => __( 'You can set a title for this section', 'yith-woocommerce-membership' ),
            'type'  => __( 'You can use this attribute when premium YITH WooCommerce Subscription plugin is enabled. Set it to "membership" to show only memberships not associated to any subscription plan; set it to "subscription" to show memberships associated to a subscription plan; don\'t use it if you want to show all memberships.', 'yith-woocommerce-membership' ),
        ),
        'examples' => array(
            '[membership_history title="Your memberships"]',
            '[membership_history id="123" title="Gold Membership"]',
            '[membership_history title="Membership with subscription" type="subscription"]',
            '[membership_history title="Membership without subscription" type="membership"]',
        ),

    ),
    'membership_downloaded_product_links' => array(
        'title' => __( 'Membership downloaded product links', 'yith-woocommerce-membership' ),
        'code'  => '[membership_downloaded_product_links]',
        'desc'  => __( 'Prints a table with links to downloaded products', 'yith-woocommerce-membership' ),
    ),
    'loginform'                           => array(
        'title' => __( 'Login Form', 'yith-woocommerce-membership' ),
        'code'  => '[loginform]',
        'desc'  => __( 'Print WooCommerce Login Form', 'yith-woocommerce-membership' ),
    ),
);
?>

<div id="yith-wcmbs-admin-shortcodes-tab-container">
    <h2><?php _e( 'Shortcodes', 'yith-wcbep' ) ?></h2>
    <?php foreach ( $shortcodes as $key => $shortcode ) : ?>
        <div class="yith-wcmbs-admin-shortcode-container">
            <h3><?php echo $shortcode[ 'title' ] ?></h3>

            <p><code><?php echo $shortcode[ 'code' ] ?></code></p>

            <h4><?php _e( 'Description', 'yith-woocommerce-membership' ); ?></h4>

            <p class="description"><?php echo $shortcode[ 'desc' ] ?></p>

            <?php if ( !empty( $shortcode[ 'args' ] ) ) : ?>
                <h4><?php _e( 'Shortcode attributes', 'yith-woocommerce-membership' ); ?></h4>
                <table class="arguments">
                    <?php foreach ( $shortcode[ 'args' ] as $arg => $arg_desc ) : ?>
                        <tr class="argument">
                            <td class="argument-id"><?php echo $arg ?></td>
                            <td class="argument-desc"><?php echo $arg_desc; ?></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            <?php endif ?>
            <?php if ( !empty( $shortcode[ 'examples' ] ) ) : ?>
                <h4><?php _e( 'Examples', 'yith-woocommerce-membership' ); ?></h4>
                <?php foreach ( $shortcode[ 'examples' ] as $example ) : ?>
                    <p><code><?php echo $example ?></code></p>
                <?php endforeach ?>
            <?php endif ?>
        </div>
    <?php endforeach ?>
</div>
