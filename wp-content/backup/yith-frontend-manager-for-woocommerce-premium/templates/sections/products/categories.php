<?php

defined( 'ABSPATH' ) or exit;

$act = isset( $_REQUEST['act'] ) ? $_REQUEST['act'] : '';
$section    = 'products';
$subsection = 'categories';

$section_uri = yith_wcfm_get_section_url( $section, $subsection );
$template_args = array( 'section_uri' => $section_uri );

if ( $act == 'new' ) {
    YITH_Frontend_Manager_Section_Products_Premium::add_category( $_POST );
}

elseif( $act == 'edit' ) {
    YITH_Frontend_Manager_Section_Products_Premium::edit_category( $_POST );
}

elseif( $act == 'delete' && ! empty( $_REQUEST['term_id'] ) && ! empty( $_REQUEST['taxonomy'] ) ){
    $term_id    = $_REQUEST['term_id'];
    $taxonomy   = $_REQUEST['taxonomy'];
    do_action( 'yith_wcfm_delete_product_taxonomy_terms', $term_id, $taxonomy, $act );
}

do_action( 'yith_wcfm_before_section_template', $section, $subsection, $act );

?>

<div id="yith-wcfm-categories" class="yith-wcfm-taxonomies">

    <h1><?php echo __('Product Categories', 'yith-frontend-manager-for-woocommerce'); ?></h1>

    <?php
    $taxonomy_template = ! isset( $_GET['edit'] ) ? 'categories-table' : 'categories-form';
    yith_wcfm_get_template( $taxonomy_template, $template_args, 'sections/products' );
    ?>

</div>

<?php

do_action( 'yith_wcfm_after_section_template', $section, $subsection, $act );