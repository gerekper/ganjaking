<?php

function yith_wcdls_get_type_of_restrictions() {
    $type_restrictions = array(
        'default'           => esc_html__('Restriction by:','yith-deals-for-woocommerce'),
        'category'          => esc_html__('Category','yith-deals-for-woocommerce'),
        'tag'               => esc_html__('Tag','yith-deals-for-woocommerce'),
        'product'           => esc_html__('Product','yith-deals-for-woocommerce'),
        'price'             => esc_html__('Cart total','yith-deals-for-woocommerce'),
        'geolocalization'   => esc_html__('Geolocalization','yith-deals-for-woocommerce'),
        'role'              => esc_html__('Role','yith-deals-for-woocommerce'),
        'user'              => esc_html__('User','yith-deals-for-woocommerce'),
    );

    return apply_filters('yith_wcdls_type_of_restrictions',$type_restrictions);
}


/**
 * Get country customer
 *
 * @return array
 * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 */
function yith_wcdls_get_country_customer()
{
    $ip_address =  apply_filters('yith_wcdls_customer_ip', WC_Geolocation::get_ip_address());
    $geolocation = WC_Geolocation::geolocate_ip($ip_address);

    return $geolocation;

}


function yith_wcdls_get_user_roles() {
    $roles_user = wp_roles()->roles;
    $role = array();
    foreach($roles_user as $roles=>$rol){
        $role[$roles] = $rol['name'];
    }
    return apply_filters('yith_wcdppm_get_user_roles',$role);
}

/**
 * Get list of google attributes
 *
 * @return array
 * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 */
function get_offer_options($selected = "") {
    $attributes = offer_options();
    $str = "<option></option>";
    $str="";
    foreach ($attributes as $attribute) {
        if(!empty($attribute['id_group'])) {
            $str .= '<optgroup label="' . $attribute['id_group'] . '">';
        }
        foreach($attribute['content'] as $id=>$value) {
            $sltd = "";
            if ($selected == $id)
                $sltd = 'selected="selected"';
            $str .= "<option $sltd value='$id'>" . $value . "</option>";
        }
        $str.= '</optgroup>';
    }
    return $str;
}

function offer_options() {

    $attributes =  array(
        array(
            'id_group' => '',
            'content' => array(
                "none"                   => esc_html__('None','yith-deals-for-woocommerce'),
            ),
        ),
        array(
            'id_group' => esc_html__('Product discount','yith-google-product-feed-for-woocommerce'),
            'content' => array(
                "fixed_product_discount"            => esc_html__('Fixed product discount','yith-deals-for-woocommerce'),
                "percentage_product_discount"       => esc_html__('Percentage product discount','yith-deals-for-woocommerce'),
                "fixed_product_price"               => esc_html__('Fixed product price','yith-deals-for-woocommerce'),
            ),
        ),
    );

    return apply_filters('yith_wcdls_apply_offer_options',$attributes);
}

function yith_wcdls_get_hide_offer() {
    $hide_offer = array(
        'forever'           => esc_html__('Forever','yith-deals-for-woocommerce'),
        'only_now'          => esc_html__('Only this time','yith-deals-for-woocommerce'),
    );

    return apply_filters('yith_wcdls_hide_offer',$hide_offer);
}

function yith_wcdls_get_type_layout() {
    $type_layout = array(
        'inline'            => esc_html__('In line with page content','yith-deals-for-woocommerce'),
        'modal'             => esc_html__('Popup','yith-deals-for-woocommerce'),
        'popover'           => esc_html__('Popover','yith-deals-for-woocommerce'),
    );

    return apply_filters('yith_wcdls_type_layout',$type_layout);

}

function get_deals( $args='' ) {
    $defaults = apply_filters( 'yith_wcdls_get_rule',array(
        'posts_per_page' => -1,
        'post_type' => 'yith_wcdls_offer',
    ));

    $args = array(
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key'       => 'yith_wcdls_enable_disable',
                'value'     =>'1',
                'compare'   =>'!=',
            ),
            'suppress_filters' => false,
        ) );

    $params = wp_parse_args( $args, $defaults );
    $results = get_posts( $params );

    return $results;
}

function yith_wcdls_show_another_offer($post_id) {
    $args = apply_filters( 'yith_wcdls_get_rule',array(
        'posts_per_page' => -1,
        'post_type' => 'yith_wcdls_offer',
        'post__not_in' => array($post_id),
    ));
    $results = get_posts( $args );
    $another_offer = array();
    if (!empty($results)) {
        foreach ($results as $result) {
            $another_offer[$result->ID] = $result->post_title;
        }
    }
    return apply_filters('yith_wcdls_type_layout',$another_offer);
}