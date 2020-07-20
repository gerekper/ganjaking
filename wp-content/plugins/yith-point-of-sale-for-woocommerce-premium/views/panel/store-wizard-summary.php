<?php
/**
 * @var YITH_POS_Store $store
 */

$title = YITH_POS_Admin()->store_post_type_admin->get_section_title( 4 );

echo $title;
?>

<div id="yith-pos-store-wizard-summary">
    <div id="yith-pos-store-wizard-summary__store-info" class="yith-pos-store-wizard-summary__box" data-step="1">
        <h3 class="yith-pos-store-wizard-summary__box__title"><?php _e( 'Store info', 'yith-point-of-sale-for-woocommerce' ) ?></h3>
        <div class="yith-pos-store-wizard-summary__box__content">
            <?php

            $info = array(
                'name'       => array(
                    'label' => __( 'Store Name', 'yith-point-of-sale-for-woocommerce' ),
                    'value' => $store->get_name()
                ),
                'vat_number' => array(
                    'label' => __( 'VAT Number', 'yith-point-of-sale-for-woocommerce' ),
                    'value' => $store->get_vat_number()
                ),
                'address'    => array(
                    'label' => __( 'Address', 'yith-point-of-sale-for-woocommerce' ),
                    'value' => nl2br( $store->get_formatted_address() )
                ),
                'phone'      => array(
                    'label' => __( 'Phone', 'yith-point-of-sale-for-woocommerce' ),
                    'value' => $store->get_phone()
                ),
                'fax'        => array(
                    'label' => __( 'Fax', 'yith-point-of-sale-for-woocommerce' ),
                    'value' => $store->get_fax()
                ),
                'email'      => array(
                    'label' => __( 'E-mail', 'yith-point-of-sale-for-woocommerce' ),
                    'value' => $store->get_email()
                ),
                'website'    => array(
                    'label' => __( 'Website', 'yith-point-of-sale-for-woocommerce' ),
                    'value' => $store->get_website()
                ),
                'facebook'   => array(
                    'label' => __( 'Facebook', 'yith-point-of-sale-for-woocommerce' ),
                    'value' => $store->get_facebook()
                ),
                'twitter'    => array(
                    'label' => __( 'Twitter', 'yith-point-of-sale-for-woocommerce' ),
                    'value' => $store->get_twitter()
                ),
                'instagram'  => array(
                    'label' => __( 'Instagram', 'yith-point-of-sale-for-woocommerce' ),
                    'value' => $store->get_instagram()
                ),
                'youtube'    => array(
                    'label' => __( 'Youtube', 'yith-point-of-sale-for-woocommerce' ),
                    'value' => $store->get_youtube()
                )
            );
            ?>

            <?php foreach ( $info as $key => $item ): ?>
                <?php if ( $item[ 'value' ] ): ?>
                    <div class="yith-pos-store-wizard-summary__box__content__title"><?php echo $item[ 'label' ] ?></div>
                    <div class="yith-pos-store-wizard-summary__box__content__value"><?php echo $item[ 'value' ] ?></div>
                <?php endif; ?>
            <?php endforeach; ?>

        </div>
    </div>
    <div id="yith-pos-store-wizard-summary__employees" class="yith-pos-store-wizard-summary__box" data-step="2">
        <h3 class="yith-pos-store-wizard-summary__box__title"><?php _e( 'Employees', 'yith-point-of-sale-for-woocommerce' ) ?></h3>
        <div class="yith-pos-store-wizard-summary__box__content">
            <div class="yith-pos-store-wizard-summary__box__content__title"><?php _e( 'Managers', 'yith-point-of-sale-for-woocommerce' ) ?></div>
            <div class="yith-pos-store-wizard-summary__box__content__value">
                <?php
                $managers = $store->get_managers();
                yith_pos_compact_list( array_map( 'yith_pos_get_employee_name', $managers ) );
                ?>
            </div>

            <?php if ( $cashiers = $store->get_cashiers() ) : ?>
                <div class="yith-pos-store-wizard-summary__box__content__title"><?php _e( 'Cashiers', 'yith-point-of-sale-for-woocommerce' ) ?></div>
                <div class="yith-pos-store-wizard-summary__box__content__value">
                    <?php
                    yith_pos_compact_list( array_map( 'yith_pos_get_employee_name', $cashiers ) );
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div id="yith-pos-store-wizard-summary__registers" class="yith-pos-store-wizard-summary__box" data-step="3">
        <h3 class="yith-pos-store-wizard-summary__box__title"><?php _e( 'Registers', 'yith-point-of-sale-for-woocommerce' ) ?></h3>
        <div class="yith-pos-store-wizard-summary__box__content">
            <?php
            $registers = $store->get_register_ids();

            yith_pos_compact_list( array_filter( array_map( function ( $register_id ) {
                $register = yith_pos_get_register( $register_id );
                $title    = '';
                if ( $register ) {
                    $title = $register->get_name();
                    if ( $register->is_guest_enabled() ) {
                        $title .= sprintf( " (%s)", __( 'Guest Register', 'yith-point-of-sale-for-woocommerce' ) );
                    }
                }
                return $title;
            }, $registers ) ) );
            ?>
        </div>
    </div>
</div>
