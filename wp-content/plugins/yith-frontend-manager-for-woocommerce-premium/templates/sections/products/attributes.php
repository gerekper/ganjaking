<?php

defined( 'ABSPATH' ) or exit;

$manage_attribute_result = NULL;
$attribute  = isset( $_REQUEST['attribute'] ) ?  $_REQUEST['attribute'] : '';
$act        = isset( $_REQUEST['act'] ) ? $_REQUEST['act'] : '';
$section    = 'products';
$subsection = 'attributes';

$section_uri    = yith_wcfm_get_section_url( $section, $subsection );
$template_args  = array( 'section_uri' => $section_uri );

if ( $act == 'new' ) {
    $manage_attribute_result = YITH_Frontend_Manager_Section_Products_Premium::add_attribute( $_POST );
}

elseif( $act == 'edit' ) {
    $manage_attribute_result = YITH_Frontend_Manager_Section_Products_Premium::edit_attribute( $_POST );
}

elseif ( $act == 'new_term' ) {
    $manage_attribute_result = YITH_Frontend_Manager_Section_Products_Premium::add_attribute_term( $_POST, $attribute );
}

elseif ( $act == 'edit_term' ) {
    $manage_attribute_result = YITH_Frontend_Manager_Section_Products_Premium::edit_attribute_term( $_POST, $attribute );
}

elseif( ( $act == 'delete' || $act == 'delete-attribute-term' ) && ! empty( $_REQUEST['term_id'] ) && ! empty( $_REQUEST['taxonomy'] ) ){
    $term_id    = $_REQUEST['term_id'];
    $taxonomy   = $_REQUEST['taxonomy'];
    do_action( 'yith_wcfm_delete_product_taxonomy_terms', $term_id, $taxonomy, $act );
}

do_action( 'yith_wcfm_before_section_template', $section, $subsection, $act );

?>

<div id="yith-wcfm-attributes" class="yith-wcfm-taxonomies">

    <h1><?php echo __('Product Attributes', 'yith-frontend-manager-for-woocommerce'); ?></h1>
    <p><?php echo __('Attributes let you define extra product data, such as size or colour. You can use these attributes in the shop sidebar using the "layered nav" widgets. Please note: you cannot rename an attribute later on.', 'yith-frontend-manager-for-woocommerce'); ?></p>

    <?php

    if ( $attribute != '' && ! isset( $_GET['edit'] ) ) {
        $taxonomy_template = 'attribute-terms-table';
    }

    elseif ( $attribute != '' ) {
        $taxonomy_template = 'attribute-terms-form';
    }

    elseif ( ! isset( $_GET['edit'] ) ) {
        $taxonomy_template = 'attributes-table';
    }

    else {
        $taxonomy_template = 'attributes-form';
    }

    yith_wcfm_get_template( $taxonomy_template, $template_args, 'sections/products' );

    ?>

</div>

<?php

do_action( 'yith_wcfm_after_section_template', $section, $subsection, $act );