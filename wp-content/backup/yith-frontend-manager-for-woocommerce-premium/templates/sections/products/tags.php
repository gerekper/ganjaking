<?php

defined( 'ABSPATH' ) or exit;

$act = isset( $_REQUEST['act'] ) ? $_REQUEST['act'] : '';
$section    = 'products';
$subsection = 'tags';

$section_uri = yith_wcfm_get_section_url( $section, $subsection );
$template_args = array( 'section_uri' => $section_uri );

if ( $act == 'new' ) {
    YITH_Frontend_Manager_Section_Products_Premium::add_tag( $_POST );
}

else if ( $act == 'edit' ) {
    YITH_Frontend_Manager_Section_Products_Premium::edit_tag( $_POST );
}

elseif( $act == 'delete' && ! empty( $_REQUEST['term_id'] ) && ! empty( $_REQUEST['taxonomy'] ) ){
    $term_id    = $_REQUEST['term_id'];
    $taxonomy   = $_REQUEST['taxonomy'];
    do_action( 'yith_wcfm_delete_product_taxonomy_terms', $term_id, $taxonomy, $act );
}

do_action( 'yith_wcfm_before_section_template', $section, $subsection, $act );

?>

<div id="yith-wcfm-tags" class="yith-wcfm-taxonomies">

    <h1><?php echo __('Product Tags', 'yith-frontend-manager-for-woocommerce'); ?></h1>

    <?php
    $taxonomy_template = ! isset( $_GET['edit'] ) ? 'tags-table' : 'tags-form';
    yith_wcfm_get_template( $taxonomy_template, $template_args, 'sections/products' );
    ?>

</div>

<?php

do_action( 'yith_wcfm_after_section_template', $section, $subsection, $act );