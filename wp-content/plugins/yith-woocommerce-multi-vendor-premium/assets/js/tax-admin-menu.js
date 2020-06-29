(function ($) {
    var adminmenu       = $('#adminmenu'),
        product_link    = adminmenu.find( '#menu-posts-product' ),
        vendor_menu     = adminmenu.find( '#toplevel_page_edit-tags-taxonomy-yith_shop_vendor-amp-post_type-product');

    vendor_menu.addClass( 'wp-menu-open wp-has-current-submenu').find('a').addClass( 'wp-menu-open wp-has-current-submenu').removeClass( 'wp-not-current-submenu' );
    product_link.removeClass( 'wp-menu-open wp-has-current-submenu').addClass('wp-not-current-submenu yith-wcmv-tax-menu');
    product_link.find('a').removeClass('wp-has-current-submenu');
}(jQuery));
