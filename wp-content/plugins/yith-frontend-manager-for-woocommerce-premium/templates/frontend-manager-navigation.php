<?php
/**
 * Frontend Manager navigation menu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** @var YITH_Frontend_Manager_Section $section  */
/** @var YITH_Frontend_Manager_Section $subsection  */

do_action( 'yith_wcfm_before_account_navigation' );
?>

<nav class="<?php echo $navigation_wrapper_classes; ?>">
    <?php do_action( 'yith_wcfm_before_account_navigation_link_list' ); ?>
    <ul id="yith-wcfm-navigation-menu">
        <?php $sections = apply_filters( 'yith_wcfm_get_sections_before_print_navigation', YITH_Frontend_Manager()->gui->get_sections() ); ?>
        <?php foreach ( $sections as $endpoint => $section ) : ?>
            <?php $is_section_enabled = yith_wcfm_is_section_enabled( $section ); ?>
	        <?php $section_name = esc_html( $section->get_name() ); ?>
            <?php if( $is_section_enabled && ! empty( $section_name ) ) : ?>
                <?php $subsections = $section->get_subsections();?>
                <?php $section_classes = wc_get_account_menu_item_classes( $section->get_id() );
                $section_classes .= $section->is_current() ? ' is-active' : '';
                $section_classes .= !empty($subsections) ? ' has-sub-menu' : '';
                ?>
                <li class="<?php echo $section_classes; ?>">
                    <a href="<?php echo $section->get_url(); ?>"><?php echo $section_name; ?></a>
                    <?php
                    if( $subsections = apply_filters( 'yith_wcfm_get_subsections_in_print_navigation', $subsections, $section ) ) : ?>
                        <ul>
                            <?php foreach( $subsections as $subsection_id => $subsection ) : ?>
                            <?php $subsection_name = esc_html( $section->get_name( $subsection ) ); ?>
                                <?php if( ! empty( $subsection_name ) ) : ?>
                                    <?php $subsection_class = 'yith-wcfm-subsection-item'; ?>
                                    <?php $subsection_class .= $section->is_current( $subsection_id ) ? ' is-active' : ''; ?>
                                    <li class="<?php echo apply_filters( 'yith_wcfm_subsection_navigation_class', $subsection_class ); ?>">
                                        <a href="<?php echo $section->get_url( $subsection_id ); ?>"><?php echo $subsection_name; ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif;?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php do_action( 'yith_wcfm_extra_menu_items' );  ?>
    </ul>
</nav>

<?php do_action( 'yith_wcfm_after_account_navigation' ); ?>
