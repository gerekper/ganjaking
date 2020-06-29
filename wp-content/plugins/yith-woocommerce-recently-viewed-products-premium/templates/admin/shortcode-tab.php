<?php
/**
 * Shortcode tab template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Recently Viewed Products
 * @version 1.0.0
 */

defined('YITH_WRVP') || exit;

$shortcodes = YITH_WRVP_Admin_Premium()->get_shortcodes_data();
if( empty( $shortcodes ) ) {
    echo '<p>' . esc_html__( 'No shortcode options was found. Please check file in plugin-options/shortcodes-data.php', 'yith-woocommerce-recently-viewed-products' ) . '</p>';
    return;
}

?>

<h3><?php esc_html_e('Build your own shortcode', 'yith-woocommerce-recently-viewed-products') ?></h3>

<?php if( count( $shortcodes ) > 1 ) :

    reset( $shortcodes );
    $first = key( $shortcodes );
    ?>
    <ul class="yith-wrvp-shortcode-tabs-nav">
    <?php foreach( $shortcodes as $shortcode_key => $shortcode ) : ?>
        <li class="<?php echo $first == $shortcode_key ? 'active' : '' ?>">
            <a href="#<?php echo esc_attr( $shortcode_key ); ?>"><?php echo esc_html( $shortcode['title'] ); ?></a>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php foreach( $shortcodes as $shortcode_key => $data ) : ?>

    <div class="yith-wrvp-shortcode-tab" id="<?php echo esc_attr( $shortcode_key ); ?>" <?php echo ( ! empty( $first ) && $first != $shortcode_key ) ? 'style="display:none;"' : ''; ?>>
        <div class="shortcode-options">
            <h4><?php esc_html_e('Choose shortcode attributes', 'yith-woocommerce-recently-viewed-products') ?></h4>
            <table class="form-table">
                <tbody>
                <?php foreach( $data['attributes'] as $option_name => $option ) : ?>
                    <tr>
                        <th>
                            <label for="<?php echo esc_attr( $shortcode_key ) . '[' . esc_attr( $option_name ) . ']' ?>"><?php echo esc_html( $option['label'] ); ?></label>
                        </th>

                    <?php if( $option_name == 'cats_id' ): ?>
                        <td>
                            <?php
                            yit_add_select2_fields( array(
                                'class'             => 'shortcode-option wc-product-search',
                                'id'                => $option_name,
                                'name'              => $option_name,
                                'data-placeholder'  => esc_html__( 'Search for a category&hellip;', 'yith-woocommerce-recently-viewed-products' ),
                                'data-multiple'     => true,
                                'data-action'       => 'yith_wrvp_search_product_cat',
                                'custom-attributes' => array(
                                    'data-attr_name'    => $option_name
                                )
                            ) );
                            ?>
                        </td>
                    <?php else: ?>
                        <td>
                            <?php if( $option['type'] == 'select' ) : ?>
                                <select name="<?php echo esc_attr( $shortcode_key ) . '[' . esc_attr( $option_name ) . ']' ?>" id="<?php echo esc_attr( $shortcode_key ) . '[' . esc_attr( $option_name ) . ']' ?>"
                                        class="shortcode-option" data-attr_name="<?php echo esc_attr( $option_name ); ?>">
                                    <?php foreach ( $option['options'] as $key => $value ) : ?>
                                        <option value="<?php echo esc_html( $key ); ?>" <?php selected( $option['default'], $key ) ?>><?php echo esc_html( $value ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif( $option['type'] == 'checkbox' ) : ?>
                                <input type="checkbox" name="<?php echo esc_attr( $shortcode_key ) . '[' . esc_attr( $option_name ) . ']' ?>" id="<?php echo esc_attr( $shortcode_key ) . '[' . esc_attr( $option_name ) . ']' ?>" class="shortcode-option"
                                       value="yes" data-novalue="no" <?php checked( $option['default'], 'yes' ) ?> data-attr_name="<?php echo esc_attr( $option_name ); ?>"/>
                            <?php elseif( $option['type'] == 'radio' ) : ?>
                                <ul>
                                <?php foreach ( $option['options'] as $key => $value ) : ?>
                                    <li>
                                        <input type="<?php echo esc_attr( $option['type'] ) ?>" name="<?php echo esc_attr( $shortcode_key ) . '[' . esc_attr( $option_name ) . ']' ?>" id="<?php echo esc_attr( $shortcode_key ) . '-' . esc_attr( $option_name ) . '-' . esc_attr( $key ); ?>"
                                               class="shortcode-option" value="<?php echo esc_html( $key ) ?>" <?php checked( $option['default'], $key ) ?> data-attr_name="<?php echo esc_attr( $option_name ); ?>"/>
                                        <label for="<?php echo esc_attr( $shortcode_key ) . '-' . esc_attr( $option_name ) . '-' . esc_attr( $key ); ?>">
                                            <?php echo esc_html( $value ); ?>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <input type="<?php echo esc_attr( $option['type'] ); ?>" name="<?php echo esc_attr( $shortcode_key ) . '[' . esc_attr( $option_name ) . ']' ?>" id="<?php echo esc_attr( $shortcode_key ) . '[' . esc_attr( $option_name ) . ']' ?>"
                                       class="shortcode-option" value="<?php echo esc_html( $option['default'] ) ?>" data-attr_name="<?php echo esc_attr( $option_name ); ?>"/>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>

        <div class="shortcode-preview">
            <?php echo '['. esc_attr( $shortcode_key) .']' ?>
        </div>
        <span class="description"><?php esc_html_e( 'Copy and paste this shortcode in your page.', 'yith-woocommerce-recently-viewed-products' ); ?></span>
    </div>

<?php endforeach; ?>
