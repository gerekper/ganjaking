/**
 * Created by Your Inspiration on 26/02/2016.
 */
jQuery(document).ready(function($){

    var add_price_role_button = $('.add_price_rule'),
        all_price_role = $('.product_price_list'),
        rule_type_select = $('.type_price_rule_select'),
        block_params = {
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            },
            ignoreIfBlocked: true
        },
        product_price_role = $('.product_price_rule');

var init_price_role = function() {

    all_price_role.sortable({
        items: 'div.woocommerce_price_rule',
        cursor: 'move',
        scrollSensitivity: 40,
        forcePlaceholderSize: true,
        helper: 'clone',
        opacity: 0.80,
        revert: true,
        axis: 'y',
        handle: 'h3',
        stop: function (event, ui) {
        }
    });
};

    $('.woocommerce_price_rule.closed').each(function(e){

        $(this).find('.woocommerce_price_rule_data').hide();
    });

    product_price_role.on('click', '.toolbar .expand-close .expand_all_price_rule',function(e) {

        var container = $('.woocommerce_price_rule.closed');

        container.each(function (e) {

            $(this).toggleClass('closed').toggleClass('open');
            $(this).find('.woocommerce_price_rule_data').show();

        });
    });

    product_price_role.on('click', '.toolbar .expand-close .close_all_price_rule',function(e) {

        var container = $('.woocommerce_price_rule.open');

        container.each(function (e) {

            $(this).toggleClass('closed').toggleClass('open');
            $(this).find('.woocommerce_price_rule_data').hide();

        });
    });

    product_price_role.on('click','.woocommerce_price_rule h3', function(event){

        if ( $( event.target ).filter( ':input, option, .sort, .remove_row_price' ).length ) {
            return;
        }
        $( this).next( '.woocommerce_price_rule_data' ).stop().slideToggle();
        $( this).parent('.woocommerce_price_rule').toggleClass( 'closed' ).toggleClass( 'open' );

    });
    add_price_role_button.on('click',function(e){

        e.preventDefault();
        var price_rule = all_price_role.find('.woocommerce_price_rule').get(),
            index = price_rule.length,
            type_rule = rule_type_select.val();

      if( type_rule!='') {

        var  data = {
              ywcrbp_index : index,
              ywcrbp_type : type_rule,
              action : ywcrbp_prd.actions.add_new_price_role,
              ywcrbp_plugin: ywcrbp_prd.plugin
          };

          all_price_role.parent().block(block_params);
          $.ajax({
              type: 'POST',
              url: ywcrbp_prd.admin_url,
              data: data,
              dataType: 'json',
              success: function (response) {
                  all_price_role.append(response.result);
                  all_price_role.parent().unblock();
                  $('body').trigger('init_product_rule_price');
                  $('body').trigger('wc-enhanced-select-init');
              }

          });
      }
    });

    all_price_role.on('blur change', '.rule_name',function(e){
        var content = $(this).parents('.woocommerce_price_rule'),
            label = content.find('h3 > .price_rule_name'),
            name = $(this).val();
        label.html( name );
    } );

    all_price_role.on('click', '.woocommerce_price_rule h3 .remove_row_price',function(e){

        e.preventDefault();
        var header = $(this).parent(),
            container = header.parent();

        setTimeout( function(){ container.remove();}, 500 );

    });


    /**PRODUCT VARIATION */
    $(document).on('woocommerce_variations_loaded woocommerce_variations_added woocommerce_variations_saved', function() {
        var add_variation_price_btn = $('.add_variation_price_rule'),
            all_rule_variation =  $('.product_variation_price_list');

        var init_variation_price_role = function() {

            all_rule_variation.sortable({
                items: 'div.woocommerce_variation_price_rule',
                cursor: 'move',
                scrollSensitivity: 40,
                forcePlaceholderSize: true,
                helper: 'clone',
                opacity: 0.80,
                revert: true,
                axis: 'y',
                handle: 'span.header_variation_rule',
                stop: function (event, ui) {
                    var parent = ui.item.parent();
                    ui.item.closest('.woocommerce_variation').addClass('variation-needs-update');
                    enable_save_variation_button();
                }
            });
        },
        enable_save_variation_button = function(){
            $( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );
        };

        init_variation_price_role();

        add_variation_price_btn.on('click', function (e) {

            e.preventDefault();

            var variation = $(this).parents('.woocommerce_variation'),
                variation_price_list = variation.find('.product_variation_price_list'),
                n_variation_price = variation_price_list.find('.woocommerce_variation_price_rule').get().length,
                variation_loop = variation_price_list.data('variation_loop'),
                type_rule = variation.find('.type_price_rule_select'),
                type_rule_value = type_rule.val();

            if ('' !== type_rule_value) {

                var data = {
                    ywcrbp_index: n_variation_price,
                    ywcrbp_type: type_rule_value,
                    ywcrbp_loop: variation_loop,
                    action: ywcrbp_prd.actions.add_new_variation_price_role,
                    ywcrbp_plugin: ywcrbp_prd.plugin
                };

                variation.block(block_params);
                $.ajax({
                    type: 'POST',
                    url: ywcrbp_prd.admin_url,
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        variation_price_list.append(response.result);
                        variation.unblock();
                        variation.addClass('variation-needs-update');
                        $('body').trigger('wc-enhanced-select-init');
                        init_variation_price_role();
                        enable_save_variation_button();
                    }

                });
            }
        });

        $('.woocommerce_variation_price_rule.closed').each(function(e){

            $(this).find('.woocommerce_variation_price_rule_data').hide();
        });

        $('.variation_price_rule').on('click','.woocommerce_variation_price_rule span.header_variation_rule', function(event){

            if ( $( event.target ).filter( ':input, option, .sort, .variation_remove_row_price' ).length ) {
                return;
            }

            $( this).parent().next( '.woocommerce_variation_price_rule_data' ).stop().slideToggle();
            $( this).parents('.woocommerce_variation_price_rule').toggleClass( 'closed' ).toggleClass( 'open' );

        });

        $('.variation_price_rule').on('click', '.close_all_variation_price_rule',function(e) {
            e.preventDefault();
            var container = $(this).parents('.variation_price_rule'),
                all_open_content = container.find('.woocommerce_variation_price_rule.open');
            all_open_content.each(function (e) {

                $(this).toggleClass('closed').toggleClass('open');
                $(this).find('.woocommerce_variation_price_rule_data').hide();

            });
        });

        $('.variation_price_rule').on('click', '.expand_all_variation_price_rule',function(e) {

            e.preventDefault();
            var container = $(this).parents('.variation_price_rule'),
                all_open_content = container.find('.woocommerce_variation_price_rule.closed');

            all_open_content.each(function (e) {

                $(this).toggleClass('closed').toggleClass('open');
                $(this).find('.woocommerce_variation_price_rule_data').show();

            });
        });

        $('.variation_price_rule').on('change blur', '.variation_val, .variation_role, .variation_rule_name', function(e){

            var t = $(this),
                woocommece_container = t.parents('.woocommerce_variation');

            if(t.hasClass('variation_rule_name')){
                var new_name = t.val(),
                    parent = t.parents('.woocommerce_variation_price_rule'),
                    label = parent.find('.price_variation_rule_name');

                label.html(new_name);
            }
            woocommece_container.addClass('variation-needs-update');
            enable_save_variation_button();
        });

        $('.variation_price_rule').on('click', '.variation_remove_row_price',function(e){
            e.preventDefault();

            var t = $(this),
                content= t.closest('.woocommerce_variation_price_rule'),
                variation = t.parents('.woocommerce_variation');

            variation.block(block_params);
            setTimeout( function(){
                content.remove();
                variation.unblock();
                variation.addClass('variation-needs-update');
                enable_save_variation_button();
            }, 500 );

        });
    });

    $(document).on('init_product_rule_price', function(){
        init_price_role();
    }).trigger('init_product_rule_price');
});