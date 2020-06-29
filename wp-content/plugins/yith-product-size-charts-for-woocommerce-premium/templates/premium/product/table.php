<?php
/**
 * Template of table in Product Page
 *
 * @author  Yithemes
 * @package YITH Product Size Charts for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCPSC' ) ) {
    exit;
} // Exit if accessed directly

/*
 * $table_meta -> content of the table
 * $c_id -> the id of the Product Size Chart post
 */

$t        = json_decode( $table_meta );
$c        = get_post( $c_id );
$is_popup = isset( $is_popup ) && $is_popup;

if ( $is_popup ) {
    $popup_type        = get_post_meta( $c_id, 'display_as', true );
    $description_title = get_post_meta( $c_id, 'title_of_desc_tab', true );

    $tab_title = get_post_meta( $c_id, 'tab_title', true );
    $tab_title = !!$tab_title ? $tab_title : $c->post_title;

    $p_style           = get_option( 'yith-wcpsc-popup-style', 'default' );
    $popup_style_class = 'yith-wcpsc-product-size-charts-popup-' . $p_style;
    echo "<div id='yith-wcpsc-product-size-charts-popup-{$c->ID}' class='yith-wcpsc-product-size-charts-popup {$popup_style_class}'>";
    echo "<span class='yith-wcpsc-product-size-charts-popup-close yith-wcpsc-popup-close dashicons dashicons-no-alt'></span>";
    echo "<div class='yith-wcpsc-product-size-charts-popup-container'>";
    //if ( $popup_type != 'tabbed_popup' )
    if ( apply_filters( 'yith_wcpsc_show_table_title', true ) ) {
        echo "<h2>{$c->post_title}</h2>";
    }

}
$tabbed_class = '';
if ( $is_popup && $popup_type == 'tabbed_popup' ) {
    $tabbed_class = ' yith-wcpsc-product-table-wrapper-tabbed-popup';
}
$t_style           = get_option( 'yith-wcpsc-table-style', 'default' );
$table_style_class = 'yith-wcpsc-product-table-' . $t_style;
?>

<div class="yith-wcpsc-product-table-wrapper<?php echo $tabbed_class; ?>">
    <?php if ( $is_popup && $popup_type == 'tabbed_popup' ) {
        echo "<ul class='yith-wcpsc-tabbed-popup-list'>
                <li><a href='#yith-wcpsc-tab-chart-$c_id' rel='nofollow'>$tab_title</a></li>
                <li><a href='#yith-wcpsc-tab-desc-$c_id' rel='nofollow'>$description_title</a></li>
              </ul>
              <div id='yith-wcpsc-tab-desc-$c_id'>";
    }
    $content = $c->post_content;
    if ( apply_filters( 'yith_wcpsc_apply_the_content_filter', false ) ) {
        $content = apply_filters( 'the_content', $content );
    } else {
        $content = do_shortcode( $content );
        $content = wptexturize( $content );
        $content = wpautop( $content );
        $content = shortcode_unautop( $content );
        $content = prepend_attachment( $content );
        $content = wp_make_content_images_responsive( $content );
        $content = convert_smilies( $content );
    }

    $content = str_replace( ']]>', ']]&gt;', $content );
    ?>

    <?php echo $content; ?>

    <?php if ( $is_popup && $popup_type == 'tabbed_popup' ) {
        echo "</div>
              <div id='yith-wcpsc-tab-chart-$c_id'>";
    }
    ?>
    <div class="yith-wcpsc-product-table-responsive-container-with-shadow">
        <div class="yith-wcpsc-right-shadow"></div>
        <div class="yith-wcpsc-left-shadow"></div>
        <div class="yith-wcpsc-product-table-responsive-container">
            <table class="yith-wcpsc-product-table <?php echo $table_style_class; ?>">
                <thead>
                <tr>
                    <?php if ( isset( $t[ 0 ] ) ): ?>
                        <?php foreach ( $t[ 0 ] as $col ): ?>
                            <th>
                                <?php echo apply_filters( 'yith_wcpsc_table_header_content', htmlspecialchars( $col ), $col ); ?>
                            </th>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tr>
                </thead>

                <tbody>
                <?php if ( !!$t && is_array( $t ) ): ?>
                    <?php foreach ( $t as $idx => $row ): ?>
                        <?php if ( $idx == 0 )
                            continue; ?>
                        <tr>
                            <?php foreach ( $row as $col ): ?>
                                <td>
                                    <div class="yith-wcpsc-product-table-td-content">
                                        <?php echo apply_filters( 'yith_wcpsc_table_content', htmlspecialchars( $col ), $col ); ?>
                                    </div>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <?php do_action( 'yith_wcpsc_after_size_charts_table', $c_id ) ?>
        </div>
    </div>
    <?php if ( $is_popup && $popup_type == 'tabbed_popup' ) {
        echo "</div>";
    }
    ?>
</div>

<?php
if ( $is_popup ) {
    echo '</div>';
    echo '</div>';
}
?>


