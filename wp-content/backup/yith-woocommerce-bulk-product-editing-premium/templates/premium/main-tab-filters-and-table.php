<?php
if ( !defined( 'YITH_WCBEP' ) ) {
    exit;
} // Exit if accessed directly

$filters_to_show = apply_filters( 'yith_wcbep_filters_to_show', array( 'categories', 'tags', 'attributes' ) );
$is_vendor       = isset( YITH_WCBEP()->compatibility->multivendor ) && YITH_WCBEP()->compatibility->multivendor->is_vendor();

$enabled_columns = yith_wcbep_get_enabled_columns();

global $sitepress;
$current_language = !empty( $sitepress ) ? $sitepress->get_current_language() : 'all';
?>

<div id="yith-wcbep-my-page-wrapper" data-is-vendor="<?php echo $is_vendor ? 'yes' : 'no'; ?>" data-wpml-current-language="<?php echo $current_language; ?>">
    <div class="yith-wcbep-filter-wrap">
        <h2><?php _e( 'Filters', 'yith-woocommerce-bulk-product-editing' ); ?></h2>
        <button type="button" class="yith-wcbep-toggle" data-target="#yith-wcbep-filter-form">
            <span class="yith-wcbep-toggle-indicator"></span>
        </button>

        <form id="yith-wcbep-filter-form" method="post">
            <table>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php echo yith_wcbep_get_label( 'title' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-title-filter-select" name="yith-wcbep-title-filter-select"
                                class="yith-wcbep-miniselect is_resetable">
                            <option value="cont"><?php _e( 'Contains', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="notcont"><?php _e( 'Does not contain', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="starts"><?php _e( 'Starts with', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="ends"><?php _e( 'Ends with', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="regex"><?php _e( 'Regular Expression', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                        </select>
                        <input type="text" id="yith-wcbep-title-filter-value" name="yith-wcbep-title-filter-value"
                               class="yith-wcbep-minifield is_resetable">
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php echo yith_wcbep_get_label( 'description' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-description-filter-select" name="yith-wcbep-description-filter-select"
                                class="yith-wcbep-miniselect is_resetable">
                            <option value="cont"><?php _e( 'Contains', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="notcont"><?php _e( 'Does not contain', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="starts"><?php _e( 'Starts with', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="ends"><?php _e( 'Ends with', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="regex"><?php _e( 'Regular Expression', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                        </select>
                        <input type="text" id="yith-wcbep-description-filter-value" name="yith-wcbep-description-filter-value"
                               class="yith-wcbep-minifield is_resetable">
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php echo yith_wcbep_get_label( 'sku' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-sku-filter-select" name="yith-wcbep-sku-filter-select"
                                class="yith-wcbep-miniselect is_resetable">
                            <option value="cont"><?php _e( 'Contains', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="notcont"><?php _e( 'Does not contain', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="starts"><?php _e( 'Starts with', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="ends"><?php _e( 'Ends with', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="regex"><?php _e( 'Regular Expression', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                        </select>
                        <input type="text" id="yith-wcbep-sku-filter-value" name="yith-wcbep-sku-filter-value"
                               class="yith-wcbep-minifield is_resetable">
                    </td>
                </tr>
                <?php
                if ( in_array( 'categories', $filters_to_show ) ) {
                    // C A T E G O R I E S   F I L T E R
                    $cat_args   = array(
                        'hide_empty' => apply_filters( 'yith_wcbep_hide_empty_categories', true ),
                        'orderby'    => 'name',
                        'order'      => 'ASC'
                    );
                    $categories = get_terms( 'product_cat', $cat_args );

                    if ( !empty( $categories ) ) {
                        ?>
                        <tr class="yith-wcbep-filter-row__categories">
                            <td class="yith-wcbep-filter-form-label-col">
                                <label><?php echo yith_wcbep_get_label( 'categories' ) ?></label>
                            </td>
                            <td class="yith-wcbep-filter-form-content-col">
                                <select id="yith-wcbep-categories-filter" name="yith-wcbep-categories-filter[]"
                                        class="chosen is_resetable" multiple xmlns="http://www.w3.org/1999/html">
                                    <?php
                                    foreach ( $categories as $c ) {
                                        ?>
                                        <option value="<?php echo $c->term_id; ?>"><?php echo $c->name; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <?php
                    }
                } ?>

                <?php
                if ( in_array( 'tags', $filters_to_show ) ) {
                    // T A G S  F I L T E R
                    $tag_args = array(
                        'hide_empty' => true,
                        'orderby'    => 'name',
                        'order'      => 'ASC'
                    );
                    $tags     = get_terms( 'product_tag', $tag_args );

                    if ( !empty( $tags ) ) {
                        ?>
                        <tr class="yith-wcbep-filter-row__tags">
                            <td class="yith-wcbep-filter-form-label-col">
                                <label><?php echo yith_wcbep_get_label( 'tags' ) ?></label>
                            </td>
                            <td class="yith-wcbep-filter-form-content-col">
                                <select id="yith-wcbep-tags-filter" name="yith-wcbep-tags-filter[]"
                                        class="chosen is_resetable" multiple xmlns="http://www.w3.org/1999/html">
                                    <?php
                                    foreach ( $tags as $t ) {
                                        ?>
                                        <option value="<?php echo $t->term_id; ?>"><?php echo $t->name; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <?php
                    }
                } ?>


                <?php
                if ( in_array( 'attributes', $filters_to_show ) ) {

                    // A T T R I B U T E S
                    $attribute_taxonomies = wc_get_attribute_taxonomies();
                    $attributes_to_hide   = apply_filters( 'yith_wcbep_attributes_to_hide_in_filters', array() );
                    if ( $attribute_taxonomies ) {
                        foreach ( $attribute_taxonomies as $tax ) {
                            if ( in_array( $tax->attribute_name, $attributes_to_hide ) || !yith_wcbep_is_column_enabled( 'attr_pa_' . $tax->attribute_name ) ) {
                                continue;
                            }
                            $attribute_taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );
                            $attr_label              = $tax->attribute_label;
                            $terms                   = get_terms( $attribute_taxonomy_name, array( 'hide_empty' => '0' ) );
                            if ( count( $terms ) > 0 ) {
                                ?>
                                <tr>
                                    <td class="yith-wcbep-filter-form-label-col">
                                        <label><?php echo $attr_label; ?></label>
                                    </td>
                                    <td class="yith-wcbep-filter-form-content-col">
                                        <select id="yith-wcbep-attr-filter-<?php echo $attribute_taxonomy_name; ?>"
                                                data-taxonomy-name="<?php echo $attribute_taxonomy_name; ?>"
                                                name="yith-wcbep-attr-filter-<?php echo $attribute_taxonomy_name; ?>[]"
                                                class="chosen is_resetable yith_webep_attr_chosen" multiple
                                                xmlns="http://www.w3.org/1999/html">
                                            <?php
                                            foreach ( $terms as $t ) {
                                                ?>
                                                <option
                                                        value="<?php echo $t->term_id; ?>"><?php echo $t->name; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    }
                }
                ?>

                <?php do_action( 'yith_wcbep_filters_after_attribute_fields' ); ?>

                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php echo yith_wcbep_get_label( 'regular_price' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-regular-price-filter-select"
                                name="yith-wcbep-regular-price-filter-select"
                                class="yith-wcbep-miniselect is_resetable">
                            <option value="mag"> ></option>
                            <option value="min"> <</option>
                            <option value="ug"> ==</option>
                            <option value="magug"> >=</option>
                            <option value="minug"> <=</option>
                        </select>
                        <input type="text" id="yith-wcbep-regular-price-filter-value"
                               name="yith-wcbep-regular-price-filter-value"
                               class="yith-wcbep-minifield is_resetable">
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php echo yith_wcbep_get_label( 'sale_price' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-sale-price-filter-select" name="yith-wcbep-sale-price-filter-select"
                                class="yith-wcbep-miniselect is_resetable">
                            <option value="mag"> ></option>
                            <option value="min"> <</option>
                            <option value="ug"> ==</option>
                            <option value="magug"> >=</option>
                            <option value="minug"> <=</option>
                        </select>
                        <input type="text" id="yith-wcbep-sale-price-filter-value"
                               name="yith-wcbep-sale-price-filter-value" class="yith-wcbep-minifield is_resetable">
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php echo yith_wcbep_get_label( 'weight' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-weight-filter-select" name="yith-wcbep-weight-filter-select"
                                class="yith-wcbep-miniselect is_resetable">
                            <option value="mag"> ></option>
                            <option value="min"> <</option>
                            <option value="ug"> ==</option>
                            <option value="magug"> >=</option>
                            <option value="minug"> <=</option>
                        </select>
                        <input type="text" id="yith-wcbep-weight-filter-value"
                               name="yith-wcbep-weight-filter-value" class="yith-wcbep-minifield is_resetable">
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php echo yith_wcbep_get_label( 'stock_qty' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-stock-qty-filter-select" name="yith-wcbep-stock-qty-filter-select"
                                class="yith-wcbep-miniselect is_resetable">
                            <option value="mag"> ></option>
                            <option value="min"> <</option>
                            <option value="ug"> ==</option>
                            <option value="magug"> >=</option>
                            <option value="minug"> <=</option>
                        </select>
                        <input type="text" id="yith-wcbep-stock-qty-filter-value"
                               name="yith-wcbep-stock-qty-filter-value" class="yith-wcbep-minifield is_resetable">
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php echo yith_wcbep_get_label( 'stock_status' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-stock-status-filter-select" name="yith-wcbep-stock-status-filter-select"
                                class="is_resetable yith-wcbep-fullwidth-in-filters">
                            <option value=""></option>
                            <?php
                            foreach ( wc_get_product_stock_status_options() as $key => $value ) {
                                echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php echo yith_wcbep_get_label( 'status' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-status-filter-select" name="yith-wcbep-status-filter-select"
                                class="is_resetable yith-wcbep-fullwidth-in-filters">
                            <?php $statuses = array_merge( array( '' => '' ), get_post_statuses() ); ?>
                            <?php foreach ( $statuses as $key => $value ) : ?>
                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php echo yith_wcbep_get_label( 'visibility' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-visibility-filter-select" name="yith-wcbep-visibility-filter-select"
                                class="is_resetable yith-wcbep-fullwidth-in-filters">
                            <option value=""></option>
                            <?php
                            foreach ( wc_get_product_visibility_options() as $_key => $_label ) {
                                echo "<option value='$_key'>$_label</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php echo yith_wcbep_get_label( 'allow_backorders' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-allow_backorders-filter-select" name="yith-wcbep-allow_backorders-filter-select"
                                class="is_resetable yith-wcbep-fullwidth-in-filters">
                            <option value=""></option>
                            <?php
                            foreach ( wc_get_product_backorder_options() as $_key => $_label ) {
                                echo "<option value='$_key'>$_label</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php echo yith_wcbep_get_label( 'shipping_class' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <?php
                        $args = array(
                            'taxonomy'         => 'product_shipping_class',
                            'hide_empty'       => 0,
                            'show_option_all'  => ' ',
                            'show_option_none' => __( 'No shipping class', 'yith-woocommerce-bulk-product-editing' ),
                            'name'             => 'yith-wcbep-shipping-class-filter-select',
                            'id'               => 'yith-wcbep-shipping-class-filter-select',
                            'class'            => 'is_resetable yith-wcbep-fullwidth-in-filters',
                        );
                        wp_dropdown_categories( $args ); ?>
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php echo yith_wcbep_get_label( 'prod_type' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-product-type-filter-select" name="yith-wcbep-product-type-filter-select"
                                class="is_resetable yith-wcbep-fullwidth-in-filters">
                            <option value=""><?php _e( 'Show all product types', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <?php
                            foreach ( yith_wcbep_get_wc_product_types() as $product_type_name => $product_type_label ) {
                                echo "<option value='$product_type_name'>$product_type_label</option>";
                            }


                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php _e( 'Products per page', 'yith-woocommerce-bulk-product-editing' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <input type="text" id="yith-wcbep-per-page-filter" name="yith-wcbep-per-page-filter"
                               class="" value="10">
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php _e( 'Include Variations', 'yith-woocommerce-bulk-product-editing' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <input type="checkbox" id="yith-wcbep-show-variations-filter"
                               name="yith-wcbep-show-variations-filter">
                    </td>
                </tr>
            </table>
            <input id="yith-wcbep-get-products" type="button" class="yith-wcbep-admin-button"
                   value="<?php _e( 'Get products', 'yith-woocommerce-bulk-product-editing' ) ?>">
            <input id="yith-wcbep-reset-filters" type="button" class="yith-wcbep-admin-button yith-wcbep-admin-button--secondary"
                   value="<?php _e( 'Reset filters', 'yith-woocommerce-bulk-product-editing' ) ?>">
            <input id="yith-wcbep-check-by-filters" type="button" class="yith-wcbep-admin-button yith-wcbep-admin-button--secondary"
                   value="<?php _e( 'Select on filters', 'yith-woocommerce-bulk-product-editing' ) ?>">
        </form>
    </div>

    <div class="yith-wcbep-products-wrap">
        <div id="yith-wcbep-percentual-container"></div>

        <h2><?php _e( 'Products', 'yith-woocommerce-bulk-product-editing' ) ?></h2>

        <div id="yith-wcbep-actions-button-wrapper">
            <input id="yith-wcbep-save" type="button" class="yith-wcbep-admin-button"
                   value="<?php _e( 'Save', 'yith-woocommerce-bulk-product-editing' ) ?>">
            <input id="yith-wcbep-bulk-edit-btn" type="button" class="yith-wcbep-admin-button yith-wcbep-admin-button--secondary"
                   value="<?php _e( 'Bulk editing', 'yith-woocommerce-bulk-product-editing' ) ?>">

            <span class="yith-wcbep-white-space"></span>

            <input id="yith-wcbep-cols-settings-btn" type="button" class="yith-wcbep-admin-button yith-wcbep-admin-button--secondary"
                   value="<?php _e( 'Show/Hide Columns', 'yith-woocommerce-bulk-product-editing' ) ?>">

            <span class="yith-wcbep-white-space"></span>

            <input id="yith-wcbep-undo" type="button" class="yith-wcbep-admin-button yith-wcbep-admin-button--secondary"
                   value="<?php _e( 'Undo', 'yith-woocommerce-bulk-product-editing' ) ?>">
            <input id="yith-wcbep-redo" type="button" class="yith-wcbep-admin-button yith-wcbep-admin-button--secondary"
                   value="<?php _e( 'Redo', 'yith-woocommerce-bulk-product-editing' ) ?>">

            <span class="yith-wcbep-white-space"></span>

            <?php if ( !$is_vendor ): ?>
                <?php wp_enqueue_script( 'wc-product-export' ); ?>
                <form id="yith-wcbep-export-form" class="woocommerce-exporter">
                    <input style="display: none" type="checkbox" id="woocommerce-exporter-meta" checked value="1"/>
                    <input type="hidden" id="yith-wcbep-export-form__selected-products" name="yith-wcbep-selected-products" value=""/>
                    <progress class="woocommerce-exporter-progress" max="100" value="0"></progress>
                    <span class="spinner is-active"></span>
                    <input id="yith-wcbep-export-form-btn" type="button"
                           class="yith-wcbep-admin-button yith-wcbep-admin-button--secondary"
                           value="<?php _e( 'Export Selected', 'yith-woocommerce-bulk-product-editing' ) ?>">
                </form>

                <span class="yith-wcbep-white-space"></span>
                <input id="yith-wcbep-new" type="button" class="yith-wcbep-admin-button yith-wcbep-admin-button--secondary"
                       value="<?php _e( 'New Product', 'yith-woocommerce-bulk-product-editing' ) ?>">

            <?php endif; ?>

            <input id="yith-wcbep-delete" type="button" class="yith-wcbep-admin-button yith-wcbep-admin-button--secondary"
                   value="<?php _e( 'Delete Selected', 'yith-woocommerce-bulk-product-editing' ) ?>">

        </div>

        <div id="yith-wcbep-message">
            <p></p>
        </div>

        <div id="yith-wcbep-resize-table">
            <?php _e( 'Resize Table', 'yith-woocommerce-bulk-product-editing' ); ?>
        </div>
        <div id="yith-wcbep-table-wrap">
            <?php
            $table = new YITH_WCBEP_List_Table_Premium();
            $table->prepare_items();
            $table->display();
            ?>
        </div>
    </div>
</div>