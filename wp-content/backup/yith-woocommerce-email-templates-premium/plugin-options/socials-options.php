<?php

$socials = array(
    'facebook'  => __( 'Facebook', 'yith-woocommerce-email-templates' ),
    'twitter'   => __( 'Twitter', 'yith-woocommerce-email-templates' ),
    'google'    => __( 'Google+', 'yith-woocommerce-email-templates' ),
    'linkedin'  => __( 'LinkedIn', 'yith-woocommerce-email-templates' ),
    'instagram' => __( 'Instagram', 'yith-woocommerce-email-templates' ),
    'flickr'    => __( 'Flickr', 'yith-woocommerce-email-templates' ),
    'pinterest' => __( 'Pinterest', 'yith-woocommerce-email-templates' ),
    'youtube'   => __( 'Youtube', 'yith-woocommerce-email-templates' ),
);

$socials_fields                      = array();
$socials_fields[ 'general-options' ] = array(
    'title' => __( 'Social Network Sites Options', 'yith-woocommerce-email-templates' ),
    'type'  => 'title',
);

foreach ( $socials as $social_key => $social_label ) {
    $socials_fields[ $social_key ] = array(
        'id'   => 'yith-wcet-' . $social_key,
        'name' => sprintf( __( '%s Profile Url', 'yith-woocommerce-email-templates' ), $social_label ),
        'type' => 'text',
    );

    $socials_fields[ $social_key . '-icon' ] = array(
        'id'        => 'yith-wcet-' . $social_key . '-icon',
        'name'      => sprintf( __( '%s Custom Icon', 'yith-woocommerce-email-templates' ), $social_label ),
        'type'      => 'yith-field',
        'yith-type' => 'upload',
    );
}


$socials_fields[ 'general-options-end' ] = array(
    'type' => 'sectionend',
);

$settings = array( 'socials' => $socials_fields );

return $settings;