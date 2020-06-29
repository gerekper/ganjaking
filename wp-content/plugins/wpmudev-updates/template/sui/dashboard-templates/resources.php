<?php
$resources = array(
    array(
        'title' => __( 'Documentation', 'wpmudev' ),
        'icon'  => 'page',
        'url'   => $urls->documentation_url['dashboard']
    ),
    array(
        'title' => __( 'Academy', 'wpmudev' ),
        'icon'  => 'academy',
        'url'   => $urls->academy_url
    ),
    array(
        'title' => __( 'Member Forums', 'wpmudev' ),
        'icon'  => 'community-people',
        'url'   => $urls->community_url
    ),
    array(
        'title' => __( 'Blog', 'wpmudev' ),
        'icon'  => 'blog',
        'url'   => $urls->blog_url
    ),
    array(
        'title' => __( 'The Whip', 'wpmudev' ),
        'icon'  => 'wordpress',
        'url'   => $urls->whip_url
    ),
    array(
        'title' => __( 'Product Roadmap', 'wpmudev' ),
        'icon'  => 'wpmudev-logo',
        'url'   => $urls->roadmap_url
    )
);
?>

<div class="sui-box">

    <div class="sui-box-header">

        <h2 class="sui-box-title">
            <i class="sui-icon-help-support" aria-hidden="true"></i>
            <?php esc_html_e( 'Resources', 'wpmudev' ); ?>
        </h2>

    </div>

    <?php //box body ?>
    <div class="sui-box-body">
        <p><?php esc_html_e( 'Here’s a bunch of our lesser-known but supremely helpful resources and usage guides.', 'wpmudev' ); ?></p>
    </div>

    <?php //active plugin table ?>
    <table class="sui-table dashui-table-tools dashui-resources">
        <tbody>
            <?php foreach( $resources as $resource ): ?>
                <tr>
                    <td class="dashui-item-content">
                        <h4 class="dashui-resources-title">
                            <a href="<?php echo esc_url( $resource['url'] ); ?>">
                                <span style="margin-right: 10px;">
                                    <i class="sui-icon-<?php echo esc_attr( $resource['icon'] ); ?>" aria-hidden="true"></i>
                                </span>
                                <?php echo esc_html( $resource['title'] ); ?>
                            </a>
                        </h4>
                    </td>
                    <td>
                        <a class="sui-button-icon" href="<?php echo esc_url( $resource['url'] ); ?>">
                            <i class="sui-icon-chevron-right" aria-hidden="true"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php //box footer ?>
    <div class="sui-box-footer">
        <?php
        printf(
            '<p class="sui-block-content-center sui-p-small" style="%s"> %s <a href="%s" target="_blank"> %s </a> %s</p>',
            esc_attr("width: 100%"),
            esc_html__( 'Still stuck?', 'wpmudev' ),
            esc_url( 'https://premium.wpmudev.org/hub/support/#wpmud-chat-pre-survey-modal' ),
            esc_html__( 'Open a support ticket' ),
            esc_html__( "and we'll be happy to help you.", 'wpmudev' )
        );
        ?>

    </div>

</div>

