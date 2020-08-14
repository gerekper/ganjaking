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
 * @var YITH_Vendor $vendor
 */

$zone_name             = ! empty( $zone['zone_name'] ) ? $zone['zone_name'] : '';
$zone_regions          = ! empty( $zone['zone_regions'] ) ? $zone['zone_regions'] : array();
$zone_post_code        = ! empty( $zone['zone_post_code'] ) ? $zone['zone_post_code'] : '';
$zone_shipping_methods = ! empty( $zone['zone_shipping_methods'] ) ? $zone['zone_shipping_methods'] : array();
$sort_class = $index ? 'wc-shipping-zone-sort' : '';

?>

<tr data-id="<?php echo $index; ?>">
    <td width="1%" class="<?php echo $sort_class; ?>"></td>
    <td class="wc-shipping-zone-name">
        <?php if($index) : ?>
        <input type="text" name="yith_vendor_data[zone_data][<?php echo $index ?>][zone_name]" data-attribute="zone_name" value="<?php echo $zone_name ?>" placeholder="<?php esc_attr_e( 'Zone Name', 'yith-woocommerce-product-vendors' ); ?>" />
            <div class="row-actions">
                <a href="#" class="yith-wpdv-wc-shipping-zone-delete wc-shipping-zone-delete"><?php _e( 'Remove', 'yith-woocommerce-product-vendors' ); ?></a>
            </div>
        <?php endif; ?>
    </td>
    <td class="wc-shipping-zone-region yith-wcmv-shipping-zone-regions">
        <?php if($index) : ?>
            <select multiple="multiple" id="<?php echo 'yith-wcmv-zone-data-' . $index; ?>" name="yith_vendor_data[zone_data][<?php echo $index ?>][zone_regions][]" data-attribute="zone_locations" data-placeholder="<?php _e( 'Select regions within this zone', 'yith-woocommerce-product-vendors' ); ?>" class="wc-shipping-zone-region-select">
                <?php
				echo '<option id="yith-wcmv-shipping-select-all-regions" value="continent:all" ' . selected( in_array( 'continent:all', $zone_regions ) ) . '>' . esc_html_x( 'All regions', 'with regions means country, state, province...', 'yith-woocommerce-product-vendors' ) . '</option>';
                foreach ( $continents as $continent_code => $continent ) {
                    echo '<option value="continent:' . esc_attr( $continent_code ) . '" alt="" '.( in_array( 'continent:'.$continent_code , $zone_regions ) ? 'selected="selected"' : '' ).'>' . esc_html( $continent['name'] ) . '</option>';

                    $countries = array_intersect( array_keys( $allowed_countries ), $continent['countries'] );

                    foreach ( $countries as $country_code ) {
                        echo '<option value="country:' . esc_attr( $country_code ) . '" alt="' . esc_attr( $continent['name'] ) . '" '.( in_array( 'country:'.$country_code , $zone_regions ) ? 'selected="selected"' : '' ).'>' . esc_html( '&nbsp;&nbsp; ' . $allowed_countries[ $country_code ] ) . '</option>';

                        if ( $states = WC()->countries->get_states( $country_code ) ) {
                            foreach ( $states as $state_code => $state_name ) {
                                $selected = is_array( $zone_regions ) && in_array( 'state:'.$country_code . ':' . $state_code , $zone_regions ) ? 'selected="selected"' : '';
                                echo '<option value="state:' . esc_attr( $country_code . ':' . $state_code ) . '" alt="' . esc_attr( $continent['name'] . ' ' . $allowed_countries[ $country_code ] ) . '" '. $selected .'>' . esc_html( '&nbsp;&nbsp;&nbsp;&nbsp; ' . $state_name ) . '</option>';
                            }
                        }
                    }
                }
                ?>
            </select>
            <button data-index="<?php echo $index; ?>" class="yith-wpdv-wc-shipping-zone-trigger-all button-secondary" data-action="select-all" href="#">
				<?php esc_html_e( 'Select All', 'yith-woocommerce-product-vendors' ); ?>
			</button>
			<button data-index="<?php echo $index; ?>" class="yith-wpdv-wc-shipping-zone-trigger-all button-secondary" data-action="remove-all" href="#">
				<?php esc_html_e( 'Remove All', 'yith-woocommerce-product-vendors' ); ?>
			</button>
            <a class="yith-wpdv-wc-shipping-zone-postcodes-toggle wc-shipping-zone-postcodes-toggle" href="#"><?php _e( 'Limit to specific ZIP/postcodes', 'yith-woocommerce-product-vendors' ); ?></a>
            <div class="wc-shipping-zone-postcodes">
                <textarea name=yith_vendor_data[zone_data][<?php echo $index ?>][zone_post_code]" data-attribute="zone_postcodes" placeholder="<?php esc_attr_e( 'List 1 postcode per line', 'yith-woocommerce-product-vendors' ); ?>" class="input-text large-text" cols="25" rows="5"><?php echo esc_html( $zone_post_code ); ?></textarea>
                <span class="description">
                    <?php
                    $description = __( 'Postcodes containing wildcards (e.g. CB23*) and fully numeric ranges (e.g. <code>90210...99000</code>) are also supported.', 'yith-woocommerce-product-vendors' );
                    echo apply_filters( 'yith_wcmv_postcodes_description', $description );
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </td>
    <td class="wc-shipping-zone-methods">
        <input type="hidden" name="yith_vendor_data[zone_data][<?php echo $index ?>][zone_shipping_methods]" value="" />
        <div>
            <ul class="yith-wpdv-wc-shipping-zone-methods-list yith-wpdv-wc-shipping-zone-methods-list_<?php echo esc_attr( $index ) ?>">
                 <?php YITH_Vendor_Shipping()->admin->print_line_shipping_methods( $index, $zone_shipping_methods, $shipping_methods );?>
            </ul>
        </div>
    </td>
</tr>
