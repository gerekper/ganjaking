<?php

// products per page
$per_page       = (isset($_GET['per_page'])) ? intval($_GET['per_page']) : get_option('posts_per_page');
$current_page   = (isset($_GET['p'])) ? intval($_GET['p']) : 1;

$currency = get_woocommerce_currency_symbol();
$products = new WP_Query(array(
        'post_type'         => 'product',
        'posts_per_page'    => $per_page,
        'post_status'       => 'publish',
        'orderby'           => 'title',
        'order'             => 'ASC',
        'paged'             => $current_page
    ));
?>
<div class="wrap woocommerce">
    <h2><?php _e('Product Warranties', 'wc_warranty'); ?></h2>
    <?php
    if ( isset($_GET['updated']) ) {
        echo '<div class="updated fade"><p>'. __('Product warranties saved!', 'wc_warranty') .'</p></div>';
    }
    ?>
    <form method="post" action="admin-post.php">
        <div class="tablenav">
            <div class="alignleft actions bulkactions">
                <label class="screen-reader-text" for="bulk-action-selector-top"><?php _e('Select bulk action', 'wc_warranty'); ?></label>
                <select id="bulk-action-selector-top">
                    <option selected="selected" value="-1"><?php _e('Bulk Actions', 'wc_warranty'); ?></option>
                    <option class="hide-if-no-js" value="edit"><?php _e('Edit', 'wc_warranty'); ?></option>
                </select>
                <input type="button" value="Apply" class="button action" id="doaction" name="">
            </div>

            <div class=" alignleft tablenav-pages">
                <span class="displaying-num"><?php echo $products->found_posts; ?> items</span>
                <span class="pagination-links">
                <?php
                echo paginate_links(array(
                    'base'      => 'admin.php?page=warranties-bulk-update%_%',
                    'format'    => '&p=%#%',
                    'total'     => $products->max_num_pages,
                    'current'   => $current_page,
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'add_args'  => array('per_page' => $per_page)
                ));
                ?>
                </span>
            </div>
        </div>

        <table class="wp-list-table widefat fixed woocommerce_page_warranty_requests" cellspacing="0">
            <thead>
            <tr>
                <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><label for="cb-select-all-1" class="screen-reader-text">Select All</label><input type="checkbox" id="cb-select-all-1"></th>
                <th scope="col" id="id" class="manage-column column-id" width="50"><?php _e('ID', 'wc_warranty'); ?></th>
                <th scope="col" id="thumb" class="manage-column column-thumb" style="width: 52px;"></th>
                <th scope="col" id="name" class="manage-column column-name"><?php _e('Name', 'wc_warranty'); ?></th>
                <th scope="col" id="price" class="manage-column column-price"><?php _e('Price', 'wc_warranty'); ?></th>
                <th scope="col" id="categories" class="manage-column column-categories"><?php _e('Categories', 'wc_warranty'); ?></th>
                <th scope="col" id="warranty" class="manage-column column-warranty"><?php _e('Warranty', 'wc_warranty'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php

            while ($products->have_posts()):
                $products->the_post();

                $_product   = wc_get_product(get_the_ID());
                $warranty   = warranty_get_product_warranty( $_product->get_id() );
                $label      = $warranty['label'];
                $default    = isset( $warranty['default'] ) ? $warranty['default'] : false;

                ?>
                <tr id="row_<?php echo $_product->get_id(); ?>" data-id="<?php echo $_product->get_id(); ?>">
                    <th scope="row" class="check-column">
                        <?php if ( ! $_product->is_type( 'variable' ) ): ?>
                        <input id="cb-select-<?php echo $_product->get_id(); ?>" class="cb" type="checkbox" name="post[]" value="<?php echo $_product->get_id(); ?>" />
                        <?php endif; ?>
                    </th>
                    <td><?php echo $_product->get_id(); ?></td>
                    <td class="thumb column-thumb">
                        <?php echo $_product->get_image( 'thumbnail' ); ?>
                    </td>
                    <td>
                        <strong><a class="editinline" data-target="edit_<?php echo $_product->get_id(); ?>" href="#"><?php echo $_product->get_title(); ?></a></strong>
                        <div class="row-actions">
                            <span class="inline hide-if-no-js"><a class="editinline" data-target="edit_<?php echo $_product->get_id(); ?>" href="#"><?php _e('Edit', 'wc_warranty'); ?></a></span>
                        </div>
                    </td>
                    <td>
                        <?php echo $_product->get_price_html() ? $_product->get_price_html() : '<span class="na">&ndash;</span>'; ?>
                    </td>
                    <td>
                        <?php
                        if ( ! $terms = get_the_terms( $_product->get_id(), 'product_cat' ) ) {
                            echo '<span class="na">&ndash;</span>';
                        } else {
                            $termlist = array();
                            foreach ( $terms as $term ) {
                                $termlist[] = '<a href="' . admin_url( 'edit.php?' . 'product_cat' . '=' . $term->slug . '&post_type=product' ) . ' ">' . $term->name . '</a>';
                            }

                            echo implode( ', ', $termlist );
                        }
                        ?>
                    </td>
                    <td class="warranty_string"><?php echo warranty_get_warranty_string( $_product->get_id() ); ?></td>
                </tr>

                <?php if ( ! $_product->is_type( 'variable' ) ): ?>
                <tr id="edit_<?php echo $_product->get_id(); ?>" data-id="<?php echo $_product->get_id(); ?>" class="inline-edit-row inline-edit-row-post inline-edit-product quick-edit-row quick-edit-row-post inline-edit-product alternate inline-editor">
                    <td class="colspanchange" colspan="7">

                        <fieldset class="inline-edit-col-left">
                            <div class="inline-edit-col">
                                <h4><?php _e('Warranty Settings', 'wc_warranty'); ?></h4>

                                <div class="inline-edit-group">
                                    <label class="alignleft">
                                        <input type="checkbox" name="warranty_default[<?php echo $_product->get_id(); ?>]" data-id="<?php echo $_product->get_id(); ?>" <?php checked(true, $default); ?> class="default_toggle" value="yes" />
                                        <span class="checkbox-title"><?php _e('Default warranty', 'wc_warranty'); ?></span>
                                    </label>
                                </div>

                                <label class="alignleft">
                                    <span class="title"><?php _e('Type', 'wc_warranty'); ?></span>
                                    <span class="input-text-wrap">
                                        <select name="warranty_type[<?php echo $_product->get_id(); ?>]" class="warranty-type warranty_<?php echo $_product->get_id(); ?>" id="warranty_type_<?php echo $_product->get_id(); ?>" data-id="<?php echo $_product->get_id(); ?>">
                                            <option <?php selected($warranty['type'], 'no_warranty'); ?> value="no_warranty"><?php _e('No Warranty', 'wc_warranty'); ?></option>
                                            <option <?php selected($warranty['type'], 'included_warranty'); ?> value="included_warranty"><?php _e('Warranty Included', 'wc_warranty'); ?></option>
                                            <option <?php selected($warranty['type'], 'addon_warranty'); ?> value="addon_warranty"><?php _e('Warranty as Add-On', 'wc_warranty'); ?></option>
                                        </select>
                                    </span>
                                </label>
                                <br class="clear" />

                                <label class="alignleft show_if_included_warranty show_if_addon_warranty">
                                    <span class="title"><?php _e('Label', 'wc_warranty'); ?></span>
                                    <span class="input-text-wrap">
                                        <input type="text" name="warranty_label[<?php echo $_product->get_id(); ?>]" value="<?php echo esc_attr($label); ?>" class="input-text sized warranty-label warranty_<?php echo $_product->get_id(); ?>" id="warranty_label_<?php echo $_product->get_id(); ?>">
                                    </span>
                                </label>
                                <br class="clear" />

                                <label class="alignleft included-form">
                                    <span class="title"><?php _e('Validity', 'wc_warranty'); ?></span>
                                    <span class="input-text-wrap">
                                        <select name="included_warranty_length[<?php echo $_product->get_id(); ?>]" class="select short included-warranty-length warranty_<?php echo $_product->get_id(); ?>" id="included_warranty_length_<?php echo $_product->get_id(); ?>">
                                            <option <?php if ($warranty['type'] == 'included_warranty' && $warranty['length'] == 'lifetime') echo 'selected'; ?> value="lifetime"><?php _e('Lifetime', 'wc_warranty'); ?></option>
                                            <option <?php if ($warranty['type'] == 'included_warranty' && $warranty['length'] == 'limited') echo 'selected'; ?> value="limited"><?php _e('Limited', 'wc_warranty'); ?></option>
                                        </select>
                                    </span>
                                </label>
                                <br class="clear" />

                                <div class="inline-edit-group included-form" id="limited_warranty_row_<?php echo $_product->get_id(); ?>">
                                    <label class="alignleft">
                                        <span class="title"><?php _e('Length', 'wc_warranty'); ?></span>
                                        <span class="input-text-wrap">
                                            <input type="text" class="input-text sized warranty_<?php echo $_product->get_id(); ?>" size="3" name="limited_warranty_length_value[<?php echo $_product->get_id(); ?>]" value="<?php if ($warranty['type'] == 'included_warranty') echo $warranty['value']; ?>" style="width: 50px;">
                                        </span>
                                    </label>

                                    <label class="alignleft">
                                        <select name="limited_warranty_length_duration[<?php echo $_product->get_id(); ?>]" class="warranty_<?php echo $_product->get_id(); ?>" style="vertical-align: baseline;">
                                            <option <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'days') echo 'selected'; ?> value="days"><?php _e('Days', 'wc_warranty'); ?></option>
                                            <option <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'weeks') echo 'selected'; ?> value="weeks"><?php _e('Weeks', 'wc_warranty'); ?></option>
                                            <option <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'months') echo 'selected'; ?> value="months"><?php _e('Months', 'wc_warranty'); ?></option>
                                            <option <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'years') echo 'selected'; ?> value="years"><?php _e('Years', 'wc_warranty'); ?></option>
                                        </select>
                                    </label>
                                </div>

                                <br class="clear" />
                            </div>
                        </fieldset>

                        <fieldset class="inline-edit-col-left">
                            <div class="inline-edit-col addon-form">

                                <div class="inline-edit-group">
                                    <label class="alignleft">
                                        <input type="checkbox" name="addon_no_warranty[<?php echo $_product->get_id(); ?>]" id="addon_no_warranty" value="yes" <?php if (isset($warranty['no_warranty_option']) && $warranty['no_warranty_option'] == 'yes') echo 'checked'; ?> class="checkbox warranty_<?php echo $_product->get_id(); ?>" />
                                        <span class="checkbox-title"><?php _e( '"No Warranty" option', 'wc_warranty'); ?></span>
                                    </label>
                                </div>

                                <a style="float: right;" href="#" class="button btn-add-warranty">&plus;</a>

                                <div class="inline-edit-group">
                                    <table class="widefat">
                                        <thead>
                                        <tr>
                                            <th><?php _e('Cost', 'wc_warranty'); ?></th>
                                            <th><?php _e('Duration', 'wc_warranty'); ?></th>
                                            <th width="50">&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody class="addons-tbody">
                                        <?php if ( isset($warranty['addons']) ) foreach ( $warranty['addons'] as $addon ): ?>
                                            <tr>
                                                <td valign="middle">
                                                    <span class="input"><b>+</b> <?php echo $currency; ?></span>
                                                    <input type="text" name="addon_warranty_amount[<?php echo $_product->get_id(); ?>][]" class="input-text sized warranty_<?php echo $_product->get_id(); ?>" size="2" value="<?php echo esc_attr($addon['amount']); ?>" />
                                                </td>
                                                <td valign="middle">
                                                    <input type="text" class="input-text sized warranty_<?php echo $_product->get_id(); ?>" size="2" name="addon_warranty_length_value[<?php echo $_product->get_id(); ?>][]" value="<?php if ($warranty['type'] == 'addon_warranty') echo esc_attr($addon['value']); ?>" />
                                                    <select name="addon_warranty_length_duration[<?php echo $_product->get_id(); ?>][]" class="warranty_<?php echo $_product->get_id(); ?>">
                                                        <option <?php if ($warranty['type'] == 'addon_warranty') selected($addon['duration'], 'days'); ?> value="days"><?php _e('Days', 'wc_warranty'); ?></option>
                                                        <option <?php if ($warranty['type'] == 'addon_warranty') selected($addon['duration'], 'weeks'); ?> value="weeks"><?php _e('Weeks', 'wc_warranty'); ?></option>
                                                        <option <?php if ($warranty['type'] == 'addon_warranty') selected($addon['duration'], 'months'); ?> value="months"><?php _e('Months', 'wc_warranty'); ?></option>
                                                        <option <?php if ($warranty['type'] == 'addon_warranty') selected($addon['duration'], 'years'); ?> value="years"><?php _e('Years', 'wc_warranty'); ?></option>
                                                    </select>
                                                </td>
                                                <td><a class="button warranty_addon_remove" href="#">&times;</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </fieldset>

                        <p class="submit inline-edit-save">
                            <a class="button-secondary alignleft editinline" data-target="edit_<?php echo $_product->get_id(); ?>" href="#"><?php _e('Close', 'wc_warranty'); ?></a>
                            <a class="button-primary save alignright" href="#inline-edit"><?php _e('Update', 'wc_warranty'); ?></a>
                            <span class="spinner"></span>
                            <br class="clear">
                        </p>
                    </td>
                </tr>
                <?php endif; ?>

                <?php

                if ($_product->is_type( 'variable' ) ):
                    foreach ($_product->get_children() as $child):
                        $_variation = wc_get_product( $child );

                        $warranty   = warranty_get_product_warranty( $child );
                        $label      = $warranty['label'];
                        $default    = isset( $warranty['default'] ) ? $warranty['default'] : false;
                        $variation_id = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $_variation->variation_id ) ) ? $_variation->variation_id : $_variation->get_id();

                        ?>
                        <tr id="row_<?php echo $child; ?>" data-id="<?php echo $child; ?>">
                            <th scope="row" class="check-column">
                                <input id="cb-select-<?php echo $variation_id; ?>" type="checkbox" class="cb" name="post[]" value="<?php echo $child; ?>" />
                            </th>
                            <td><?php echo $variation_id; ?></td>
                            <td class="thumb column-thumb">
                                <a href="post.php?post=<?php echo $_variation->get_id(); ?>&action=edit">
                                    <?php echo $_variation->get_image( 'thumbnail' ); ?>
                                </a>
                            </td>
                            <td colspan="1">
                                &mdash; <a class="editinline" data-target="edit_<?php echo $child; ?>" href="#"><?php echo $_variation->get_formatted_name(); ?></a>
                            </td>
                            <td>
                                <?php echo $_variation->get_price_html() ? $_variation->get_price_html() : '<span class="na">&ndash;</span>'; ?>
                            </td>
                            <td>
                                <?php
                                if ( ! $terms = get_the_terms( $_variation->get_id(), 'product_cat' ) ) {
                                    echo '<span class="na">&ndash;</span>';
                                } else {
                                    $termlist = array();
                                    foreach ( $terms as $term ) {
                                        $termlist[] = '<a href="' . admin_url( 'edit.php?' . 'product_cat' . '=' . $term->slug . '&post_type=product' ) . ' ">' . $term->name . '</a>';
                                    }

                                    echo implode( ', ', $termlist );
                                }
                                ?>
                            </td>
                            <td class="warranty_string"><?php echo warranty_get_warranty_string( $child ); ?></td>
                        </tr>
                        <tr id="edit_<?php echo $child; ?>" data-id="<?php echo $child; ?>" class="inline-edit-row inline-edit-row-post inline-edit-product quick-edit-row quick-edit-row-post inline-edit-product alternate inline-editor">
                            <td class="colspanchange" colspan="7">

                                <fieldset class="inline-edit-col-left">
                                    <div class="inline-edit-col">
                                        <h4><?php _e('Warranty Settings', 'wc_warranty'); ?></h4>

                                        <div class="inline-edit-group">
                                            <label class="alignleft">
                                                <input type="checkbox" name="warranty_default[<?php echo $child; ?>]" data-id="<?php echo $child; ?>" <?php checked(true, $default); ?> class="default_toggle" value="yes" />
                                                <span class="checkbox-title"><?php _e('Default warranty', 'wc_warranty'); ?></span>
                                            </label>
                                        </div>

                                        <label class="alignleft">
                                            <span class="title"><?php _e('Type', 'wc_warranty'); ?></span>
                                            <span class="input-text-wrap">
                                                <select name="warranty_type[<?php echo $child; ?>]" class="warranty-type warranty_<?php echo $child; ?>" id="warranty_type_<?php echo $child; ?>" data-id="<?php echo $child; ?>">
                                                    <option <?php selected($warranty['type'], 'no_warranty'); ?> value="no_warranty"><?php _e('No Warranty', 'wc_warranty'); ?></option>
                                                    <option <?php selected($warranty['type'], 'included_warranty'); ?> value="included_warranty"><?php _e('Warranty Included', 'wc_warranty'); ?></option>
                                                    <option <?php selected($warranty['type'], 'addon_warranty'); ?> value="addon_warranty"><?php _e('Warranty as Add-On', 'wc_warranty'); ?></option>
                                                </select>
                                            </span>
                                        </label>
                                        <br class="clear" />

                                        <label class="alignleft show_if_included_warranty show_if_addon_warranty">
                                            <span class="title"><?php _e('Label', 'wc_warranty'); ?></span>
                                            <span class="input-text-wrap">
                                                <input type="text" name="warranty_label[<?php echo $child; ?>]" value="<?php echo esc_attr($label); ?>" class="input-text sized warranty-label warranty_<?php echo $child; ?>" id="warranty_label_<?php echo $child; ?>">
                                            </span>
                                        </label>
                                        <br class="clear" />

                                        <label class="alignleft included-form">
                                            <span class="title"><?php _e('Validity', 'wc_warranty'); ?></span>
                                            <span class="input-text-wrap">
                                                <select name="included_warranty_length[<?php echo $child; ?>]" class="select short included-warranty-length warranty_<?php echo $child; ?>" id="included_warranty_length_<?php echo $child; ?>">
                                                    <option <?php if ($warranty['type'] == 'included_warranty' && $warranty['length'] == 'lifetime') echo 'selected'; ?> value="lifetime"><?php _e('Lifetime', 'wc_warranty'); ?></option>
                                                    <option <?php if ($warranty['type'] == 'included_warranty' && $warranty['length'] == 'limited') echo 'selected'; ?> value="limited"><?php _e('Limited', 'wc_warranty'); ?></option>
                                                </select>
                                            </span>
                                        </label>
                                        <br class="clear" />

                                        <div class="inline-edit-group included-form" id="limited_warranty_row_<?php echo $child; ?>">
                                            <label class="alignleft">
                                                <span class="title"><?php _e('Length', 'wc_warranty'); ?></span>
                                                <span class="input-text-wrap">
                                                    <input type="text" class="input-text sized warranty_<?php echo $child; ?>" size="3" name="limited_warranty_length_value[<?php echo $child; ?>]" value="<?php if ($warranty['type'] == 'included_warranty') echo $warranty['value']; ?>" style="width: 50px;">
                                                </span>
                                            </label>
                                            <label class="alignleft">
                                                <select name="limited_warranty_length_duration[<?php echo $child; ?>]" class="warranty_<?php echo $child; ?>" style="vertical-align: baseline;">
                                                    <option <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'days') echo 'selected'; ?> value="days"><?php _e('Days', 'wc_warranty'); ?></option>
                                                    <option <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'weeks') echo 'selected'; ?> value="weeks"><?php _e('Weeks', 'wc_warranty'); ?></option>
                                                    <option <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'months') echo 'selected'; ?> value="months"><?php _e('Months', 'wc_warranty'); ?></option>
                                                    <option <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'years') echo 'selected'; ?> value="years"><?php _e('Years', 'wc_warranty'); ?></option>
                                                </select>
                                            </label>
                                        </div>

                                    </div>
                                </fieldset>

                                <fieldset class="inline-edit-col-left">
                                    <div class="inline-edit-col addon-form">

                                        <div class="inline-edit-group">
                                            <label class="alignleft">
                                                <input type="checkbox" name="addon_no_warranty[<?php echo $child; ?>]" id="addon_no_warranty" value="yes" <?php if (isset($warranty['no_warranty_option']) && $warranty['no_warranty_option'] == 'yes') echo 'checked'; ?> class="checkbox warranty_<?php echo $child; ?>" />
                                                <span class="checkbox-title"><?php _e( '"No Warranty" option', 'wc_warranty'); ?></span>
                                            </label>
                                        </div>

                                        <a style="float: right;" href="#" class="button btn-add-warranty">&plus;</a>

                                        <div class="inline-edit-group">
                                            <table class="widefat">
                                                <thead>
                                                <tr>
                                                    <th><?php _e('Cost', 'wc_warranty'); ?></th>
                                                    <th><?php _e('Duration', 'wc_warranty'); ?></th>
                                                    <th width="50">&nbsp;</th>
                                                </tr>
                                                </thead>
                                                <tbody class="addons-tbody">
                                                <?php if ( isset($warranty['addons']) ) foreach ( $warranty['addons'] as $addon ): ?>
                                                    <tr>
                                                        <td valign="middle">
                                                            <span class="input"><b>+</b> <?php echo $currency; ?></span>
                                                            <input type="text" name="addon_warranty_amount[<?php echo $child; ?>][]" class="input-text sized warranty_<?php echo $child; ?>" size="2" value="<?php echo esc_attr($addon['amount']); ?>" />
                                                        </td>
                                                        <td valign="middle">
                                                            <input type="text" class="input-text sized warranty_<?php echo $child; ?>" size="2" name="addon_warranty_length_value[<?php echo $child; ?>][]" value="<?php if ($warranty['type'] == 'addon_warranty') echo esc_attr($addon['value']); ?>" />
                                                            <select name="addon_warranty_length_duration[<?php echo $child; ?>][]" class="warranty_<?php echo $child; ?>">
                                                                <option <?php if ($warranty['type'] == 'addon_warranty') selected($addon['duration'], 'days'); ?> value="days"><?php _e('Days', 'wc_warranty'); ?></option>
                                                                <option <?php if ($warranty['type'] == 'addon_warranty') selected($addon['duration'], 'weeks'); ?> value="weeks"><?php _e('Weeks', 'wc_warranty'); ?></option>
                                                                <option <?php if ($warranty['type'] == 'addon_warranty') selected($addon['duration'], 'months'); ?> value="months"><?php _e('Months', 'wc_warranty'); ?></option>
                                                                <option <?php if ($warranty['type'] == 'addon_warranty') selected($addon['duration'], 'years'); ?> value="years"><?php _e('Years', 'wc_warranty'); ?></option>
                                                            </select>
                                                        </td>
                                                        <td><a class="button warranty_addon_remove" href="#">&times;</a></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </fieldset>

                                <p class="submit inline-edit-save">
                                    <a class="button-secondary alignleft editinline" data-target="edit_<?php echo $child; ?>" href="#"><?php _e('Close', 'wc_warranty'); ?></a>
                                    <a class="button-primary save alignright" href="#inline-edit"><?php _e('Update', 'wc_warranty'); ?></a>
                                    <span class="spinner"></span>
                                    <br class="clear">
                                </p>
                            </td>
                        </tr>
                    <?php
                    endforeach;
                endif;
            endwhile; // while ($products->have_posts)
            ?>
            </tbody>
        </table>
        <div class="tablenav">
            <div class="alignleft actions bulkactions">
                <label class="screen-reader-text" for="bulk-action-selector-bottom"><?php _e('Select bulk action', 'wc_warranty'); ?></label>
                <select id="bulk-action-selector-bottom">
                    <option selected="selected" value="-1"><?php _e('Bulk Actions', 'wc_warranty'); ?></option>
                    <option class="hide-if-no-js" value="edit"><?php _e('Edit', 'wc_warranty'); ?></option>
                </select>
                <input type="button" value="Apply" class="button action" id="doaction2" name="">
            </div>

            <div class="tablenav-pages">
                <span class="displaying-num"><?php echo $products->found_posts; ?> items</span>
            <span class="pagination-links">
            <?php
            echo paginate_links(array(
                'base'      => 'admin.php?page=warranties-bulk-update%_%',
                'format'    => '&p=%#%',
                'total'     => $products->max_num_pages,
                'current'   => $current_page,
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
                'add_args'  => array('per_page' => $per_page)
            ));
            ?>
            </span>
            </div>

            <div class="alignleft" style="line-height: 30px;">
                <span class="displaying-num"><?php _e('Products per Page:', 'wc_warranty'); ?></span>
                <span class="pagination-links">
                    <a href="<?php echo esc_url( add_query_arg('per_page', 10, 'admin.php?page=warranties-bulk-update&tab=manage') ); ?>" <?php if ($per_page == 10) echo 'class="current"'; ?>>10</a> |
                    <a href="<?php echo esc_url( add_query_arg('per_page', 25, 'admin.php?page=warranties-bulk-update&tab=manage') ); ?>" <?php if ($per_page == 25) echo 'class="current"'; ?>>25</a> |
                    <a href="<?php echo esc_url( add_query_arg('per_page', 50, 'admin.php?page=warranties-bulk-update&tab=manage') ); ?>" <?php if ($per_page == 50) echo 'class="current"'; ?>>50</a> |
                    <a href="<?php echo esc_url( add_query_arg('per_page', 100, 'admin.php?page=warranties-bulk-update&tab=manage') ); ?>" <?php if ($per_page == 100) echo 'class="current"'; ?>>100</a>
                </span>
            </div>
        </div>
    </form>

    <script id="bulk_edit_tpl" type="text/html">
    <tr id="bulk-edit" data-id="bulk" class="inline-edit-row inline-edit-row-post inline-edit-product bulk-edit-row bulk-edit-row-post bulk-edit-product inline-editor">
        <td class="colspanchange" colspan="7">

            <fieldset class="inline-edit-col-left">
                <div class="inline-edit-col">
                    <h4><?php _e('Warranty Settings', 'wc_warranty'); ?></h4>

                    <div class="inline-edit-group">
                        <label class="alignleft">
                            <input type="checkbox" name="warranty_default_bulk" data-id="bulk" checked class="default_toggle" value="yes" />
                            <span class="checkbox-title"><?php _e('Default warranty', 'wc_warranty'); ?></span>
                        </label>
                    </div>

                    <label class="alignleft">
                        <span class="title"><?php _e('Type', 'wc_warranty'); ?></span>
                        <span class="input-text-wrap">
                            <select name="warranty_type_bulk" class="warranty-type warranty_bulk" id="warranty_type_bulk" data-id="bulk">
                                <option value="no_warranty"><?php _e('No Warranty', 'wc_warranty'); ?></option>
                                <option value="included_warranty"><?php _e('Warranty Included', 'wc_warranty'); ?></option>
                                <option value="addon_warranty"><?php _e('Warranty as Add-On', 'wc_warranty'); ?></option>
                            </select>
                        </span>
                    </label>
                    <br class="clear" />

                    <label class="alignleft show_if_included_warranty show_if_addon_warranty">
                        <span class="title"><?php _e('Label', 'wc_warranty'); ?></span>
                        <span class="input-text-wrap">
                            <input type="text" name="warranty_label_bulk" value="" class="input-text sized warranty-label warranty_bulk" id="warranty_label_bulk">
                        </span>
                    </label>
                    <br class="clear" />

                    <label class="alignleft included-form">
                        <span class="title"><?php _e('Validity', 'wc_warranty'); ?></span>
                        <span class="input-text-wrap">
                            <select name="included_warranty_length_bulk" class="select short included-warranty-length warranty_bulk" id="included_warranty_length_bulk">
                                <option value="lifetime"><?php _e('Lifetime', 'wc_warranty'); ?></option>
                                <option value="limited"><?php _e('Limited', 'wc_warranty'); ?></option>
                            </select>
                        </span>
                    </label>
                    <br class="clear" />

                    <div class="inline-edit-group included-form" id="limited_warranty_row_bulk">
                        <label class="alignleft">
                            <span class="title"><?php _e('Length', 'wc_warranty'); ?></span>
                            <span class="input-text-wrap">
                                <input type="text" class="input-text sized warranty_bulk" size="3" name="limited_warranty_length_value_bulk" value="" style="width: 50px;">
                            </span>
                        </label>
                        <label class="alignleft">
                            <select name="limited_warranty_length_duration_bulk" class="warranty_bulk" style="vertical-align: baseline;">
                                <option value="days"><?php _e('Days', 'wc_warranty'); ?></option>
                                <option value="weeks"><?php _e('Weeks', 'wc_warranty'); ?></option>
                                <option value="months"><?php _e('Months', 'wc_warranty'); ?></option>
                                <option value="years"><?php _e('Years', 'wc_warranty'); ?></option>
                            </select>
                        </label>
                    </div>

                </div>
            </fieldset>

            <fieldset class="inline-edit-col-left">
                <div class="inline-edit-col addon-form">

                    <div class="inline-edit-group">
                        <label class="alignleft">
                            <input type="checkbox" name="addon_no_warranty_bulk" id="addon_no_warranty" value="yes" class="checkbox warranty_bulk" />
                            <span class="checkbox-title"><?php _e( '"No Warranty" option', 'wc_warranty'); ?></span>
                        </label>
                    </div>

                    <a style="float: right;" href="#" class="button btn-add-warranty">&plus;</a>

                    <div class="inline-edit-group">
                        <table class="widefat">
                            <thead>
                            <tr>
                                <th><?php _e('Cost', 'wc_warranty'); ?></th>
                                <th><?php _e('Duration', 'wc_warranty'); ?></th>
                                <th width="50">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody class="addons-tbody">

                            </tbody>

                        </table>
                    </div>
                </div>
            </fieldset>

            <p class="submit inline-edit-save">
                <a class="button-secondary alignleft close-bulk-edit" href="#"><?php _e('Cancel', 'wc_warranty'); ?></a>
                <input type="hidden" name="action" value="warranty_bulk_edit" />
                <input type="submit" value="<?php _e('Update', 'wc_warranty'); ?>" class="button button-primary alignright" id="bulk_edit" name="bulk_edit">
                <br class="clear">
            </p>
        </td>
    </tr>
    </script>

    <script type="text/javascript">
        var tmpl = '<tr>\
                <td valign=\"middle\">\
                    <span class=\"input\"><b>+</b> <?php echo $currency; ?></span>\
                    <input type=\"text\" name=\"addon_warranty_amount[{id}][]\" class=\"input-text sized\" size=\"2\" value=\"\" />\
                </td>\
                <td valign=\"middle\">\
                    <input type=\"text\" class=\"input-text sized\" size=\"2\" name=\"addon_warranty_length_value[{id}][]\" value=\"\" />\
                    <select name=\"addon_warranty_length_duration[{id}][]\">\
                        <option value=\"days\"><?php _e('Days', 'wc_warranty'); ?></option>\
                        <option value=\"weeks\"><?php _e('Weeks', 'wc_warranty'); ?></option>\
                        <option value=\"months\"><?php _e('Months', 'wc_warranty'); ?></option>\
                        <option value=\"years\"><?php _e('Years', 'wc_warranty'); ?></option>\
                    </select>\
                </td>\
                <td><a class=\"button warranty_addon_remove\" href=\"#\">&times;</a></td>\
            </tr>';
        jQuery(document).ready(function($) {
            $(".inline-edit-row").hide();

            $("a.editinline").click(function(e) {
                e.preventDefault();
                var target = "#"+ $(this).data("target");

                if ( $(target).is(":visible") ) {
                    $(target).hide();
                } else {
                    $(target).css("display", "table-row");
                }

            });

            $("#doaction,#doaction2").click(function() {
                if ( $(this).attr("id") == 'doaction' ) {
                    var action = $("#bulk-action-selector-top").val();
                } else {
                    var action = $("#bulk-action-selector-bottom").val();
                }

                if ( $(".woocommerce_page_warranty_requests tbody .cb:checked").length == 0 ) {
                    return;
                }

                if ( action == "edit" ) {
                    $("tr#bulk-edit").remove();
                    $(".woocommerce_page_warranty_requests > tbody").prepend( $("#bulk_edit_tpl").html() );

                    $(".default_toggle").change();
                    $(".warranty-type").change();

                    $('html, body').animate({
                        scrollTop: $("#bulk-edit").offset().top - 100
                    }, 1000);
                }
            });

            $("table.woocommerce_page_warranty_requests").on("click", ".close-bulk-edit", function(e) {
                e.preventDefault();
                $("tr#bulk-edit").remove();
            });

            $("table.woocommerce_page_warranty_requests").on("change", ".default_toggle", function() {
                var id = $(this).data("id");

                if ( $(this).is(":checked") ) {
                    $(".warranty_"+ id).attr("disabled", true);
                } else {
                    $(".warranty_"+ id)
                        .attr("disabled", false)
                        .change();
                }
            });
            $(".default_toggle").change();

            $("table.woocommerce_page_warranty_requests").on("change", ".warranty-type", function() {
                var parent  = $(this).parents("tr");
                var id      = $(parent).data("id");

                $(parent).find(".included-form").hide();
                $(parent).find(".addon-form").hide();

                switch ($(this).val()) {

                    case 'included_warranty':
                        $(parent).find(".included-form").show();
                        $("#included_warranty_length_"+id).change();
                        break;

                    case 'addon_warranty':
                        $(parent).find(".addon-form").show();
                        break;

                    default:
                        break;

                }
            });
            $(".warranty-type").change();

            $("table.woocommerce_page_warranty_requests").on("change", ".included-warranty-length", function() {
                var parent  = $(this).parents("tr");
                var id      = $(parent).data("id");

                if ($(this).val() == "lifetime") {
                    $("#limited_warranty_row_"+id).hide();
                } else {
                    $("#limited_warranty_row_"+id).show();
                }
            });

            $(".included-warranty-length").each(function() {
                $(this).change();
            });

            $(".btn-add-warranty").live("click", function(e) {
                e.preventDefault();

                var id = $(this).parents("tr").eq(0).data("id");

                var t = tmpl.replace(new RegExp('{id}', 'g'), id);
                $(this).parents("tr").find(".addons-tbody").append(t);
            });

            $(".warranty_addon_remove").live("click", function(e) {
                e.preventDefault();

                $(this).parents("tr").eq(0).remove();
            });

            $("table.woocommerce_page_warranty_requests").on("click", "a.save", function(e) {
                e.preventDefault();

                var id      = $(this).parents("tr.inline-edit-row").data("id");
                var tr      = $("#edit_"+ id);
                var data    = $("#edit_"+ id +" :input").serialize() + "&id="+id+"&action=warranty_product_warranty_update";
                var spinner = tr.find(".spinner");

                spinner.show();

                $.post(ajaxurl, data, function(resp) {
                    if (resp.string) {
                        $("#row_"+id).find("td.warranty_string").html(resp.string);
                    }

                    spinner.hide();
                    tr.hide();
                });
            });
        });
    </script>
</div>
