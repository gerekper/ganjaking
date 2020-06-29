<?php
if ( !defined( 'YITH_WCBEP' ) ) {
    exit;
} // Exit if accessed directly

?>
<div id="yith-wcbep-my-page-wrapper">
    <div id="yith-wcbep-custom-input" contenteditable="true"></div>
    <div class="yith-wcbep-filter-wrap">
        <h2><?php _e( 'Filters', 'yith-woocommerce-bulk-product-editing' ); ?></h2>
        <button type="button" class="yith-wcbep-toggle" data-target="#yith-wcbep-filter-form">
            <span class="yith-wcbep-toggle-indicator"></span>
        </button>

        <form id="yith-wcbep-filter-form" method="post">
            <table style="width:50%">
                <?php

                $cat_args   = array(
                    'hide_empty' => apply_filters( 'yith_wcbep_hide_empty_categories', true ),
                    'order'      => 'ASC'
                );
                $categories = get_terms( 'product_cat', $cat_args );


                if ( !empty( $categories ) ) {
                    ?>
                    <tr>
                        <td class="yith-wcbep-filter-form-label-col">
                            <label><?php _e( 'Filter Categories', 'yith-woocommerce-bulk-product-editing' ) ?></label>
                        </td>
                        <td class="yith-wcbep-filter-form-content-col">
                            <select id="yith-wcbep-categories-filter" name="yith-wcbep-categories-filter[]" class="chosen is_resetable" multiple xmlns="http://www.w3.org/1999/html">
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
                } ?>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php _e( 'Regular Price', 'yith-woocommerce-bulk-product-editing' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-regular-price-filter-select" name="yith-wcbep-regular-price-filter-select" class="yith-wcbep-miniselect is_resetable">
                            <option value="mag"> ></option>
                            <option value="min"> <</option>
                            <option value="ug"> ==</option>
                            <option value="magug"> >=</option>
                            <option value="minug"> <=</option>
                        </select>
                        <input type="text" id="yith-wcbep-regular-price-filter-value" name="yith-wcbep-regular-price-filter-value" class="yith-wcbep-minifield is_resetable">
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php _e( 'Sale Price', 'yith-woocommerce-bulk-product-editing' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-sale-price-filter-select" name="yith-wcbep-sale-price-filter-select" class="yith-wcbep-miniselect is_resetable">
                            <option value="mag"> ></option>
                            <option value="min"> <</option>
                            <option value="ug"> ==</option>
                            <option value="magug"> >=</option>
                            <option value="minug"> <=</option>
                        </select>
                        <input type="text" id="yith-wcbep-sale-price-filter-value" name="yith-wcbep-sale-price-filter-value" class="yith-wcbep-minifield is_resetable">
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php _e( 'Products per page', 'yith-woocommerce-bulk-product-editing' ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <input type="text" id="yith-wcbep-per-page-filter" name="yith-wcbep-per-page-filter" class="" value="10">
                    </td>
                </tr>
            </table>
            <input id="yith-wcbep-get-products" type="button" class="button button-primary button-large" value="<?php _e( 'Get products', 'yith-woocommerce-bulk-product-editing' ) ?>">
            <input id="yith-wcbep-reset-filters" type="button" class="button button-secondary button-large" value="<?php _e( 'Reset filters', 'yith-woocommerce-bulk-product-editing' ) ?>">
        </form>
    </div>

    <div class="yith-wcbep-products-wrap">
        <h2><?php _e( 'Products', 'yith-woocommerce-bulk-product-editing' ) ?></h2>
        <input id="yith-wcbep-save" type="button" class="button button-primary button-large" value="<?php _e( 'Save', 'yith-woocommerce-bulk-product-editing' ) ?>">
        <input id="yith-wcbep-bulk-edit-btn" type="button" class="button button-secondary button-large" value="<?php _e( 'Bulk editing', 'yith-woocommerce-bulk-product-editing' ) ?>">
        <div id="yith-wcbep-message" class="updated notice">
            <p></p>
        </div>
        <div id="yith-wcbep-percentual-container">
        </div>
        <div id="yith-wcbep-table-wrap">
            <?php
            $table = new YITH_WCBEP_List_Table();
            $table->prepare_items();
            $table->display();
            ?>
        </div>
    </div>
    <div id="yith-wcbep-bulk-editor">
        <div id="yith-wcbep-bulk-editor-container">
            <h2><?php _e( 'Bulk editing', 'yith-woocommerce-bulk-product-editing' ) ?></h2>
            <table id="yith-wcbep-bulk-editor-table">
                <tr>
                    <td class="yith-wcbep-bulk-form-label-col">
                        <label><?php _e( 'Regular Price', 'yith-woocommerce-bulk-product-editing' ) ?></label>
                    </td>
                    <td class="yith-wcbep-bulk-form-content-col">
                        <select id="yith-wcbep-regular_price-bulk-select" name="yith-wcbep-regular_price-bulk-select" class="yith-wcbep-miniselect is_resetable">
                            <option value="new"><?php _e( 'Set new', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="inc"><?php _e( 'Increase by value', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="dec"><?php _e( 'Decrease by value', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="incp"><?php _e( 'Increase by percentage', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="decp"><?php _e( 'Decrease by percentage', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                        </select>
                        <input type="text" id="yith-wcbep-regular_price-bulk-value" name="yith-wcbep-regular_price-bulk-value" class="yith-wcbep-minifield is_resetable">
                    </td>
                </tr>
                <tr>
                    <td class="yith-wcbep-bulk-form-label-col">
                        <label><?php _e( 'Sale Price', 'yith-woocommerce-bulk-product-editing' ) ?></label>
                    </td>
                    <td class="yith-wcbep-bulk-form-content-col">
                        <select id="yith-wcbep-sale_price-bulk-select" name="yith-wcbep-sale_price-bulk-select" class="yith-wcbep-miniselect is_resetable">
                            <option value="new"><?php _e( 'Set new', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="inc"><?php _e( 'Increase by value', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="dec"><?php _e( 'Decrease by value', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="incp"><?php _e( 'Increase by %', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="decp"><?php _e( 'Decrease by %', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="decfr"><?php _e( 'Decrease by value from regular', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="decpfr"><?php _e( 'Decrease by % from regular', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                            <option value="del"><?php _e( 'Delete', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                        </select>
                        <input type="text" id="yith-wcbep-sale_price-bulk-value" name="yith-wcbep-sale_price-bulk-value" class="yith-wcbep-minifield is_resetable">
                    </td>
                </tr>
            </table>
        </div>
        <div id="yith-wcbep-bulk-button-wrap">
            <input id="yith-wcbep-bulk-apply" type="button" class="button button-primary button-large" value="<?php _e( 'Apply', 'yith-woocommerce-bulk-product-editing' ) ?>">
            <input id="yith-wcbep-bulk-cancel" type="button" class="button button-secondary button-large" value="<?php _e( 'Cancel', 'yith-woocommerce-bulk-product-editing' ) ?>">
        </div>
    </div>
</div>