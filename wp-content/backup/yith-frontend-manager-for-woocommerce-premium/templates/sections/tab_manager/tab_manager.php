<?php

defined( 'ABSPATH' ) or exit;

$post_type = 'ywtm_tab';
set_current_screen( $post_type );
$GLOBALS['hook_suffix'] = 'ywtm_tab';

$action = isset( $_REQUEST['act'] ) ? $_REQUEST['act'] : '';

if( 'delete' == $action && isset( $_REQUEST['tab_id'] ) && $_REQUEST['tab_id'] > 0 ){
    $tab_id = $_REQUEST['tab_id'];
	YITH_Frontend_Manager_Section_Tab_Manager::delete( $tab_id );
}

$tabs_table = new YITH_Tabs_List_Table( array( 'screen' => $post_type, 'section_obj' => $section_obj ) );
$tabs_table->prepare_items();

$args = apply_filters( 'yith_tabs_template', array(
		'tabs_table' => $tabs_table,
		'page_title' => __( 'All Tabs', 'yith-frontend-manager-for-woocommerce' )
	)
);

do_action( 'yith_wcfm_before_section_template', $section, $subsection, $action );

?>

<div id="yith-wcfm-ywtm_tab">

    <h1><?php echo __( 'Tabs', 'yith-frontend-manager-for-woocommerce' ); ?></h1>


    <form id="tabs-filter" method="get">
        <input type="hidden" name="page"
               value="<?php echo ! empty( $_REQUEST['page'] ) ? $_REQUEST['page'] : $section_obj->get_url() ?>"/>

		<?php $tabs_table->display(); ?>
    </form>

</div>

<?php
do_action( 'yith_wcfm_after_section_template', $section, $subsection, $action );
;?>