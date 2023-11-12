(function ($) {
    'use strict';

    $(function () {

        // $(document).find('#rtwwdpd_checking_placeholder').select2({
        //     placeholder: "Select a state"
        // });
        ///// customization of plus member rule //////
        $(document).on('click', '.rtwbma_save_plus_text', function () {
            var val = $(document).find('.rtwwdpd_show_plus_text').val();
            var data = {
                action: 'rtwwdpd_plus_text',
                'rtw_val': val,
                security_check: rtwwdpd_ajax.rtwwdpd_nonce
            };
            $.ajax({
                url: rtwwdpd_ajax.ajax_url,
                type: "POST",
                data: data,
                dataType: 'json',
                success: function (response) { }
            });
        })

        //// customization of shipping rule //////////
        $(document).on('change', '#shipping_dscnt_on', function () {
            var rtw_val = $(this).val();
            var data = {
                action: 'rtwwdpd_shipping_rule_on',
                'rtw_val': rtw_val,
                security_check: rtwwdpd_ajax.rtwwdpd_nonce
            };
            $.ajax(
                {
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
        });
        /////// Start extra customisation in category field anshuman
        $(document).find(".rtwwdpd_cat_class").select2();
        var multiple_cat = $(document).find("#rtwwdpd_category_on_update").val();
        //   console.log(multiple_cat);

        if (multiple_cat == 'rtwwdpd_multiple_cat_update') {
            $(document).find("#edit_category_id").val("");
            $(document).find(".prod_cat").show();
            $(document).find(".rtwwdpd_cat").hide();
        }
        if (multiple_cat == 'rtwwdpd_category_update') {
            $(document).find("#rtwwdpd_checking_placeholder").val("");
            $(document).find(".prod_cat").hide();
            $(document).find(".rtwwdpd_cat").show();
        }
        // $(document).find(".prod_cat").hide();     
        $(document).on('change', '#rtwwdpd_category_on_update', function () {
            var rtwwdpd_cat = $(this).val();
            // console.log(rtwwdpd_cat);
            $(".rtwwdpd_cat").show();
            // $("."+rtwwdpd_cat).show();
            if (rtwwdpd_cat == 'rtwwdpd_multiple_cat_update') {
                $(".prod_cat").show();
                $(".rtwwdpd_cat").hide();
            }
            if (rtwwdpd_cat == 'rtwwdpd_category_update') {
                $(".prod_cat").hide();
                $(".rtwwdpd_cat").show();
            }
        });
        $(document).find(".extra_field").hide();
        $(document).on('change', '#rtwwdpd_category_on_update', function () {
            var rtwwdpd_cat = $(this).val();
            // console.log(rtwwdpd_cat);
            $(".rtwwdpd_cat").show();
            // $("."+rtwwdpd_cat).show();
            if (rtwwdpd_cat == 'rtwwdpd_multiple_cat_update') {
                $(".extra_field").show();
                $(".rtwwdpd_cat").hide();
            }
            if (rtwwdpd_cat == 'rtwwdpd_category_update') {
                $(".extra_field").hide();
                $(".rtwwdpd_cat").show();
            }
        });

        /////// End extra customisation in category field anshuman


        ///////////// Version 2.0.0 //////////////
        // $(document).on('change', '.rtwwdpd_select_free_product', function(){
        //     var free_as = $(this).val();
        //     if(free_as == 'same')
        //     {
        //         $(document).find('.rtw_tbltitle').hide();
        //         $(document).find('#rtwbogo_table_cat_pro').hide();
        //     }else{
        //         $(document).find('.rtw_tbltitle').show();
        //         $(document).find('#rtwbogo_table_cat_pro').show();
        //     }
        // });

        var free_as = $(document).find('.rtwwdpd_select_free_product').val();
        if (free_as == 'same') {
            $(document).find('.rtw_tbltitle').hide();
            $(document).find('#rtwbogo_table_cat_pro').hide();
        } else {
            $(document).find('.rtw_tbltitle').show();
            $(document).find('#rtwbogo_table_cat_pro').show();
        }

        var show_purchase_prod = $(document).find('#rtwwdpd_bogo_rule_on').val();

        if (show_purchase_prod == 'product') {
            $(document).find('.rtwwdpd_show_purchase_prod').show();
            $(document).find('#mini_pur_amount').hide();
        } else if (show_purchase_prod == 'min_purchase') {
            $(document).find('.rtwwdpd_show_purchase_prod').hide();
            $(document).find('#mini_pur_amount').show();
        }
        $(document).on('change', '#rtwwdpd_bogo_rule_on', function () {
            var val = $(this).val();
            if (val == 'product') {
                $(document).find('.rtwwdpd_show_purchase_prod').show();
                $(document).find('#mini_pur_amount').hide();
            } else {
                $(document).find('.rtwwdpd_show_purchase_prod').hide();
                $(document).find('#mini_pur_amount').show();
            }
        })

        var selected = $(document).find('.rtwwdpd_discount_on').val();

        if (selected == 1) {
            $(document).find('.rtwwdpd_category_sel').hide();
        } else {
            $(document).find('.rtwwdpd_category_sel').show();
        }

        $(document).on('change', '.rtwwdpd_discount_on', function () {
            var val = $(this).val();
            if (val == 1) {
                $(document).find('.rtwwdpd_category_sel').hide();
            } else {
                $(document).find('.rtwwdpd_category_sel').show();
            }
        });

        $(document).on('change', '.rtwwdpd_enable_least_free', function () {
            var rtw_val = $(this).val();
            var data = {
                action: 'rtwwdpd_enable_least_free',
                'enable': rtw_val,
                security_check: rtwwdpd_ajax.rtwwdpd_nonce
            };
            $.ajax({
                url: rtwwdpd_ajax.ajax_url,
                type: "POST",
                data: data,
                dataType: 'json',
                success: function (response) { }
            });
        });
        ///////////// Version 2.0.0 //////////// 

        $(document).on('click', '.rtwwdpd_pro_save', function () {
            show_all('#rtwwdpd_rule_tab');
            $("#submit_pro_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_pro_com_save', function () {
            show_all('#rtwwdpd_rule_tab');
            $("#submit_procom_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_att_save', function () {
            show_all('#rtwwdpd_rule_tab_combi');
            $("#submit_attr_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_bog_rul', function () {
            show_all('#rtwbogo_rule_tab');
            $("#submit_bogo_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_save_bcat', function () {
            show_all('#rtwwdpd_rule_tab_combi');
            $("#submit_bogocat_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_save_tag', function () {
            show_all('#rtwwdpd_rule_tab_tag');
            $("#submit_bogotag_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_cat_saave', function () {
            show_all('#rtwcat_rule_tab');
            $("#submit_cat_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_catcc_save', function () {
            show_all('#rtwcat_com_rule_tab');
            $("#submit_catco_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_crt_save', function () {
            show_all('#rtwwdpd_rule_tab');
            $("#submit_cart_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_var_savve', function () {
            show_all('#rtwwdpd_rule_tab');
            $("#submit_var_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_tierpr_save', function () {
            show_all('#rtwwdpd_tiered_rule_tab');
            $("#submit_tierpro_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_tiercat_save', function () {
            show_all('#rtwwdpd_rule_tab_combi');
            $("#submit_tiercat_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_pay_save', function () {
            show_all('#rtwwdpd_rule_tab_combi');
            $("#submit_pay_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_ptag_save', function () {
            show_all('#rtwwdpd_rule_tab_combi');
            $("#submit_ptag_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_shipp_save', function () {
            show_all('#rtwwdpd_rule_tab_combi');
            $("#submit_shipp_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_next_save', function () {
            show_all('#rtwcat_com_rule_tab');
            $("#submit_next_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_nth_save', function () {
            show_all('#rtwwdpd_rule_tab_combi');
            $("#submit_nth_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_spec_save', function () {
            show_all('#rtwwdpd_rule_tab_combi');
            $("#submit_spec_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_sale_save', function () {
            show_all('#rtwwdpd_rule_tab_combi');
            $("#submit_come_rule").trigger("click");
        });

        $(document).on('click', '.rtwwdpd_plusm_save', function () {
            show_all('#rtwwdpd_rule_tab_combi');
            $("#submit_plusm_rule").trigger("click");
        });

        $(document).find('.rtwwdpd_notice_error').addClass('rtwwdpd_hide');
        var rules = {
            rtwwdpd_purchase_code: { required: true }
        };

        var messages = {
            rtwwdpd_purchase_code: { required: 'Required' }
        };

        $(document).find("#rtwwdpd_verify").validate({
            rules: rules,
            messages: messages
        });

        $(document).on('click', '#rtwwdpd_verify_code', function () {
            if ($(document).find("#rtwwdpd_verify").valid()) {
                var rtwwdpd_purchase_code = $(document).find('.rtwwdpd_purchase_code').val();

                var data = {
                    action: 'rtwwdpd_verify_purchase_code',
                    purchase_code: rtwwdpd_purchase_code,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.blockUI({
                    message: '',
                    timeout: 20000000
                });
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status) {
                            $(document).find('.rtwwdpd_notice_success').removeClass('rtwwdpd_hide');
                            $(document).find('.rtwwdpd_msg_response').html(response.message);
                            $(document).find('.rtwwdpd_msg_response').removeClass('rtwwdpd_errorr');
                            $(document).find('.rtwwdpd_msg_response').addClass('rtwwdpd_successs');
                            window.setTimeout(function () {
                                window.location.reload(true);
                            }, 3000);

                        } else {
                            $(document).find('.rtwwdpd_msg_response').html(response.message);
                            $(document).find('.rtwwdpd_msg_response').removeClass('rtwwdpd_successs');
                            $(document).find('.rtwwdpd_msg_response').addClass('rtwwdpd_errorr');
                        }
                        $.unblockUI();
                    }
                });
            }
        });

        $(document).on('change', '#rtwwdpd_attributes', function () {
            var attriute_slug = $(this).val();

            var data = {
                action: 'rtwwdpd_selected_attribute',
                attriute_slug: attriute_slug,
                security_check: rtwwdpd_ajax.rtwwdpd_nonce
            };
            $.blockUI({
                message: ''
            });
            $.ajax({
                url: rtwwdpd_ajax.ajax_url,
                type: "POST",
                data: data,
                dataType: 'json',
                success: function (response) {
                    $.unblockUI();
                    $(document).find('#rtwwdpd_attribute_val').html(response);
                    $(document).find(".rtwwdpd_payment_method").select2();
                }
            });
        });


        $(document).find(".rtwwdpd_prod_table_edit").hide();
        $(document).find(".rtwwdpd_prod_c_table_edit").show();
        $(document).find(".rtwwdpd_prod_table").show();
        $(document).find(".rtwwdpd_prod_c_table").hide();
        $(document).find(".rtwwdpd_cat_table").show();
        $(document).find(".rtwwdpd_cat_c_table").hide();
        $(document).find(".rtwwdpd_nth_value").hide();

        if ($(document).find('.rtwwdpd_order_no').val() == 2) {
            $(document).find('.rtwwdpd_nth_value').show();
        }

        if ($(document).find('.rtwwdpd_repeat_discount').prop('checked')) {
            $(document).find('.rtwwdpd_nth_val').show();
        }

        $(document).on('click', '.rtwwdpd_repeat_discount', function () {
            if ($(this).prop('checked')) {
                $(document).find('.rtwwdpd_nth_val').show();
            } else {
                $(document).find('.rtwwdpd_nth_val').hide();
            }
        })

        $(document).on('change', '.rtwwdpd_order_no', function () {
            var val = $(this).val();
            if (val == 1) {
                $(document).find(".rtwwdpd_nth_value").hide();
                $(document).find(".rtwwdpd_nth_value").val(1);
            } else {
                $(document).find(".rtwwdpd_nth_value").show();
            }

        });

        $(document).find('.rtwtable').DataTable({
            "order": [],
            "columnDefs": [{ orderable: false, targets: [0] }],
        });

        jQuery('.woocommerce-help-tip').tipTip({
            'attribute': 'data-tip',
            'fadeIn': 50,
            'fadeOut': 50,
            'delay': 200
        });

        $(document).on('change', '#rtwwdpd_check_for_cat', function () {
            var val = $(this).find("option:selected").text();

            $(document).find("[for=rtwwdpd_min_cat]").html('Minimum ' + val);
            $(document).find("[for=rtwwdpd_max_cat]").html('Maximum ' + val);
        });


        $(document).on('change', '#rtwwdpd_dscnt_cat_type', function () {
            var val = $(this).find("option:selected").text();
            $(document).find("[for=rtwwdpd_dscnt_cat_val]").html(val);
        });

        $(document).on('change', '#rtwwdpd_rule_on', function () {

            var val = $(this).val();
            if (val == 'rtwwdpd_products') {
                // $(this).closest('tr').next('tr').show();
                $(document).find('.multiple_product_ids').hide();
                $(document).find('#product_id').show();
            } else if (val == 'rtwwdpd_cart') {
                // $(this).closest('tr').next('tr').hide().val('');
                $(document).find('.multiple_product_ids').hide();
                $(document).find('#product_id').hide();
            } else if (val == 'rtwwdpd_multiple_products') {
                // $(this).closest('tr').next('tr').next('tr').hide().val('');
                $(document).find('.multiple_product_ids').show();
                $(document).find('#product_id').hide();
            }
        });

        var selected_val = $(document).find('#rtwwdpd_rule_on').val();
        // if (selected_val == 'rtwwdpd_cart') {
        //     $(document).find('#rtwwdpd_rule_on').closest('tr').next('tr').hide().val('');
        // }
        if (selected_val == 'rtwwdpd_products') {
            // $(this).closest('tr').next('tr').show();
            $(document).find('.multiple_product_ids').hide();
            $(document).find('#product_id').show();
        } else if (selected_val == 'rtwwdpd_cart') {
            // $(this).closest('tr').next('tr').hide().val('');
            $(document).find('.multiple_product_ids').hide();
            $(document).find('#product_id').hide();
        } else if (selected_val == 'rtwwdpd_multiple_products') {
            // $(this).closest('tr').next('tr').next('tr').hide().val('');
            $(document).find('.multiple_product_ids').show();
            $(document).find('#product_id').hide();
        }

        $(document).on('click', '.rtwwdpd_cancel_rule', function () {
            $(document).find(".rtwwdpd_add_single_rule").hide();
            $(document).find(".rtwwdpd_add_combi_rule").hide();
            $(document).find(".rtwwdpd_add_combi_rule_tab").hide();
            $(document).find(".rtwwdpd_bogo_combi_tab").hide();
            $(document).find(".rtwwdpd_bogo_tag_tab").hide();
            $(document).find(".rtwwdpd_single_bogo_rule_tab").hide();
            $(document).find(".rtwwdpd_combi_cat_tab").hide();
            $(document).find(".rtwwdpd_single_cat_rule").hide();
            $(document).find(".rtwwdpd_add_tier_cat_rule_tab").hide();
            // $(document).find("#rtwwdpd_tiered_rule_tab").hide();
            $(document).find(".rtwwdpd_add_tier_pro_rule_tab").hide();
            $(document).find(".rtwwdpd_next_buy_tab").hide();
        });

        $(document).find(".date-picker").datepicker({
            dateFormat: "dd-mm-yy",
            minDate: 0
        });
        $(document).find('#category_id').select2();
        $(document).find('#rtwwdpd_category_id').select2();
        $(document).find('#category_id_free').select2();
        $(document).find(".rtwwdpd_payment_method").select2();
        $(document).find(".rtwwdpd_ship_method").select2();
        $(document).find('#category_combi_id').select2();
        $(document).find('.rtwwdpd_select_roles').select2();
        $(document).find('#rtwwdpd_select_roles').select2();
        $(document).find('.rtwwdpd_select_roles_field').select2();
        $(document).find('.rtwwdpd_user_email_for_spc_c').select2();

       // Changes are done in VERSION 2.6.1

        $(document).find(".rtwwdpd_check_maxvalue").hide();
        $(document).on('change', '.rtwwdpd_check_minvalue', function () {
            var rtw_val = $(this).val();
            if (rtw_val != 0 || rtw_val !='') {
                $(document).find(".rtwwdpd_check_maxvalue").show();
                $(document).find(".rtwwdpd_check_maxvalue").children("td").children("input").attr("required", true);
            }
            else {
                $(document).find(".rtwwdpd_check_maxvalue").hide();
                $(document).find(".rtwwdpd_check_maxvalue").children("td").children("input").val("");
                $(document).find(".rtwwdpd_check_maxvalue").children("td").children("input").attr("required", false);
            }
        });
        if ($(document).find('.rtwwdpd_check_minvalue').val() != 0) 
        {
            $(document).find('.rtwwdpd_check_maxvalue').show();
            $(document).find(".rtwwdpd_check_maxvalue").children("td").children("input").attr("required", true);
        }

        $(document).on('click', '.notice-dismiss', function () {
            $(document).find(".notice").hide();
        });

        //////
    });

    ////////// for insertion of search product field /////////////
    $(document).on('click', '#rtwinsertbtn', function () {
        var row_no = (jQuery('#rtwproduct_table >tbody >tr').length);

        var select = '<select id="rtwproduct' + row_no + '" class="wc-product-search rtwwdpd_prod_tbl_class"  name="product_id[]" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" ></select>';
        var quant = '<input type="number" min="0" name="combi_quant[]" value=""  />';
        var remove = '<a class="button insert remove" name="deletebtn" >Remove</a>';

        $('#product_list_body').append('<tr><td>' + (row_no + 1) + '</td><td>' + select + '</td><td>' + quant + '</td><td>' + remove + '</td></tr>');
        jQuery('#rtwproduct' + row_no).trigger('wc-enhanced-select-init');
    });

    $(document).on('click', '#rtwinsertbtnbogo', function () {
        var row_no = (jQuery('#rtwproduct_table >tbody >tr').length) + 1;
        var select = '<select id="rtwproduct' + row_no + '" class="wc-product-search rtwwdpd_prod_tbl_class"  name="product_id[]" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" ></select>';
        var quant = '<input type="number" min="0" name="combi_quant[]" value=""  />';
        var remove = '<a class="button insert remove" name="deletebtn" >Remove</a>';
        $('#product_list_body').append('<tr><td>' + row_no + '</td><td>' + select + '</td><td>' + quant + '</td><td>' + remove + '</td></tr>');
        jQuery('#rtwproduct' + row_no).trigger('wc-enhanced-select-init');
    });

    $(document).on('click', '#rtwinsert_product', function () {
        var row_no = (jQuery('#rtw_for_product >tbody >tr').length) + 1;
        var select = '<select id="rtwproduct' + row_no + '" class="wc-product-search rtwwdpd_prod_tbl_class"  name="product_id[]" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" ></select>';
        var quant = '<input type="number" min="0" name="quant_pro[]" value=""  />';
        var remove = '<a class="button insert remove" name="deletebtn" >Remove</a>';

        $('#rtw_product_body').append('<tr><td>' + row_no + '</td><td>' + select + '</td><td>' + quant + '</td><td>' + remove + '</td></tr>');
        jQuery('#rtwproduct' + row_no).trigger('wc-enhanced-select-init');
    });

    $(document).on('click', '.remove', function () {
        var row_no = 1;
        $(document).find('#rtwproduct_table tbody tr').each(function () {
            $(this).find('td:first-child').text(row_no);
            row_no = row_no + 1;
        });
        $(this).closest('tr').remove();
    });

    ////////// for insertion of search product field for bogo rule /////////////
    $(document).on('click', '#rtwinsert_bogo_pro', function () {
        var row_no = (jQuery('#rtwbogo_table_pro >tbody >tr').length) + 1;
        var select = '<select id="rtwproduct_' + row_no + '" class="wc-product-search rtwwdpd_prod_tbl_class"  name="rtwbogo[]" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" ></select>';
        var quant = '<input type="number" min="0" name="bogo_quant_free[]" value=""  />';
        var remove = '<a class="button insert remove" name="deletebtn" >Remove</a>';

        $('#rtw_bogo_row').append('<tr><td>' + row_no + '</td><td>' + select + '</td><td>' + quant + '</td><td>' + remove + '</td></tr>');
        jQuery('#rtwproduct_' + row_no).trigger('wc-enhanced-select-init');
    });

    $(document).on('click', '#rtwinsert_bogo_cat_p', function () {
        var row_no = (jQuery('#rtwbogo_table_cat_pro >tbody >tr').length) + 1;
        var select = '<select id="rtwproductfree' + row_no + '" class="wc-product-search rtwwdpd_prod_tbl_class"  name="rtwbogo[]" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" ></select>';
        var quant = '<input type="number" min="0" name="bogo_quant_free[]" value=""  />';
        var discount_val = '<input type="number" value=""  min="0" step="0.01" name="rtwwdpd_dscnt_cat_val" ><span style="display:block"><i>Discount should be given in "%".</i></span>';
        var remove = '<a class="button insert remove" name="deletebtn" >Remove</a>';

        $('#rtw_bogo_cat_pro').append('<tr><td>' + row_no + '</td><td>' + select + '</td><td>' + quant + '</td><td>' + discount_val + '</td><td>' + remove + '</td></tr>');
        jQuery('#rtwproductfree' + row_no).trigger('wc-enhanced-select-init');
    });

    // bogo tag

    $(document).on('click', '#rtwinsert_bogo_tag_p', function () {
        var row_no = (jQuery('#rtwbogo_table_tag_pro >tbody >tr').length) + 1;
        var select = '<select id="rtwproductfree' + row_no + '" class="wc-product-search rtwwdpd_prod_tbl_class"  name="rtwbogo[]" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" ></select>';
        var quant = '<input type="number" min="0" name="bogo_quant_free[]" value=""  />';
        var remove = '<a class="button insert remove" name="deletebtn" >Remove</a>';

        $('#rtw_bogo_tag_pro').append('<tr><td>' + row_no + '</td><td>' + select + '</td><td>' + quant + '</td><td>' + remove + '</td></tr>');
        jQuery('#rtwproductfree' + row_no).trigger('wc-enhanced-select-init');
    });

    ////
    $(document).on('click', '.remove_pro_bogo', function () {
        var row_no = 1;
        $(document).find('#rtwbogo_table_pro tbody tr').each(function () {
            $(this).find('td:first-child').text(row_no);
            row_no = row_no + 1;
        });
        $(this).closest('tr').remove();
    });

    ////////// for insertion of search categorie field in upcoming sale setting /////////////
    $(document).on('click', '#rtwinsert_category', function () {
        var row_no = (jQuery('#rtw_for_category >tbody >tr').length) + 1;
        var clone = $("#rtw_tbltr").clone().prop('id', 'rtw_tbltr' + row_no);
        clone.find(".select2-container").remove();
        clone.find("#rtw_tbltr").attr("id", "rtw_tbltr-" + row_no);
        clone.find("#td_row_no").text(row_no);
        clone.find("#td_remove").html('<a class="button insert remove_cat" name="deletebtn" >Remove</a>');
        clone.find("#td_row_no").attr("id", "td_row_no-" + row_no);
        clone.find("#category_id").val('');
        clone.find(".rtwtd_quant").val('');
        clone.find("#category_id").attr("id", "category_id-" + row_no);
        $("#rtw_category_body").append(clone);
        $(document).find("#category_id-" + row_no).select2();
    });

    ////////// for insertion of search categorie field /////////////
    $(document).on('click', '#rtwinsert_cat', function () {
        var row_no = (jQuery('#rtwcat_table >tbody >tr').length) + 1;
        var clone = $("#rtw_tbltr").clone().prop('id', 'rtw_tbltr' + row_no);
        clone.find(".select2-container").remove();
        clone.find("#rtw_tbltr").attr("id", "rtw_tbltr-" + row_no);
        clone.find("#td_row_no").text(row_no);
        clone.find("#td_remove").html('<a class="button insert remove_cat" name="deletebtn" >Remove</a>');
        clone.find("#td_row_no").attr("id", "td_row_no-" + row_no);
        clone.find("#category_id").val('');
        clone.find("#category_id").addClass('rtw_clsscategory');
        clone.find(".rtwtd_quant").val('1');
        clone.find("#category_id").attr("id", "category_id-" + row_no);
        $("#product_list_body").append(clone);
        $(document).find("#category_id-" + row_no).select2();
    });

    $(document).on('click', '.remove_cat', function () {
        var row_nos = (jQuery('#rtwcat_table >tbody >tr').length);
        var text = $(this).parent().siblings(":first").text();
        if (row_nos != 1 && text != 1) {
            var row_no = 1;
            $(document).find('#rtwcat_table tbody tr').each(function () {
                $(this).find('td:first-child').text(row_no);
                row_no = row_no + 1;
            });
            $(this).closest('tr').remove();
        } else {
            alert('Minimum One category required.');
        }

    });

    $(document).on('click', '#rtwinsert_cat_bogo', function () {
        var row_no = (jQuery('#rtwcat_table_bogo >tbody >tr').length) + 1;
        var clone = $("#rtw_tbltr").clone().prop('id', 'rtw_tbltr' + row_no);
        clone.find(".select2-container").remove();
        clone.find("#td_row_no").text(row_no);
        clone.find(".rtwtd_quant").val(1);
        clone.find("#td_remove").html('<a class="button insert remove_cat" name="deletebtn" >Remove</a>');
        clone.find("#td_row_no").attr("id", "td_row_no-" + row_no);
        clone.find("#category_id").attr("id", "category_id-" + row_no);
        clone.find('#cateogry_id' + row_no).addClass('rtw_clsscategory');
        $("#product_list_body_bogo").append(clone);
        $(document).find("#category_id-" + row_no).select2();
    });
    // bogo tag
    $(document).on('click', '#rtwinsert_tag_bogo', function () {

        var row_no = (jQuery('#rtwtag_table_bogo >tbody >tr').length) + 1;

        var clone = $("#rtw_tbltr_tag").clone().prop('id', 'rtw_tbltr_tag' + row_no);
        clone.find(".select2-container").remove();
        clone.find("#td_row_no").text(row_no);
        clone.find(".rtwtd_quant_tag").val(1);
        clone.find("#td_remove").html('<a class="button insert remove" name="deletebtn" >Remove</a>');
        clone.find("#td_row_no").attr("id", "td_row_no-" + row_no);
        clone.find("#tag_id").attr("id", "tag_id-" + row_no);
        clone.find('#tag_id' + row_no).addClass('rtw_tag');

        $("#product_list_body_bogo_tag").append(clone);
        $(document).find("#tag_id-" + row_no).select2();
    });
    ////

    $(document).on('click', '#rtwinsert_cat_free', function () {
        var row_no = (jQuery('#rtwcat_table_bogo_free >tbody >tr').length) + 1;
        var clone = $("#rtw_tbltr_free").clone().prop('id', 'rtw_tbltr_free' + row_no);
        clone.find(".select2-container").remove();
        clone.find("#td_row_no").text(row_no);
        clone.find("#td_remove").html('<a class="button insert remove_cat" name="deletebtn" >Remove</a>');
        clone.find("#td_row_no").attr("id", "td_row_no-" + row_no);
        clone.find("#category_id_free").attr("id", "category_id_free_" + row_no);
        $("#cat_list_bogo").append(clone);
        $(document).find("#category_id_free_" + row_no).select2();
        jQuery('#category_id_free_' + row_no).trigger('wc-enhanced-select-init');
    });

    $(document).on('click', '#rtwproduct_rule', function () {
        rtwwdpd_select(this, '#rtwwdpd_rule_tab');
    });
    $(document).on('click', '#rtwproduct_restrict', function () {
        rtwwdpd_select(this, '#rtwwdpd_restriction_tab');
    });
    $(document).on('click', '#rtwproduct_validity', function () {
        rtwwdpd_select(this, '#rtwwdpd_time_tab');
    });

    $(document).on('click', '#rtwbogo_rule', function () {
        rtwwdpd_select(this, '#rtwbogo_rule_tab');
    });
    $(document).on('click', '#rtwbogo_restrict', function () {
        rtwwdpd_select(this, '#rtwbogo_restrict_tab');
    });
    $(document).on('click', '#rtwbogo_validity', function () {
        rtwwdpd_select(this, '#rtwbogo_validity_tab');
    });

    $(document).on('click', '#rtwproduct_rule_combi', function () {
        rtwwdpd_select(this, '#rtwwdpd_rule_tab_combi');
        $(document).find('.rtwwdpd_rule_tab_combi').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab_combi').removeClass('active');
        $(document).find('.rtwwdpd_time_tab_combi').removeClass('active');
    });

    $(document).on('click', '#rtwproduct_rule_tag', function () {
        rtwwdpd_select(this, '#rtwwdpd_rule_tab_tag');
        $(document).find('.rtwwdpd_rule_tab_tag').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab_tag').removeClass('active');
        $(document).find('.rtwwdpd_time_tab_tag').removeClass('active');
    })

    $(document).on('click', '#rtwproduct_restrict_tag', function () {
        rtwwdpd_select(this, '#rtwwdpd_restriction_tab_tag');
        $(document).find('.rtwwdpd_restriction_tab_tag').addClass('active');
        $(document).find('.rtwwdpd_rule_tab_tag').removeClass('active');
        $(document).find('.rtwwdpd_time_tab_tag').removeClass('active');
    });

    $(document).on('click', '#rtwproduct_validity_tag', function () {
        rtwwdpd_select(this, '#rtwwdpd_time_tab_tag');
        $(document).find('.rtwwdpd_time_tab_tag').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab_tag').removeClass('active');
        $(document).find('.rtwwdpd_time_tab_tag').removeClass('active');
    });

    $(document).on('click', '#rtwproduct_restrict_combi', function () {
        rtwwdpd_select(this, '#rtwwdpd_restriction_tab_combi');
        $(document).find('.rtwwdpd_restriction_tab_combi').addClass('active');
        $(document).find('.rtwwdpd_rule_tab_combi').removeClass('active');
        $(document).find('.rtwwdpd_time_tab_combi').removeClass('active');
    });
    $(document).on('click', '#rtwproduct_validity_combi', function () {
        rtwwdpd_select(this, '#rtwwdpd_time_tab_combi');
        $(document).find('.rtwwdpd_time_tab_combi').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab_combi').removeClass('active');
        $(document).find('.rtwwdpd_rule_tab_combi').removeClass('active');
    });

    $(document).on('click', '#rtwgnrl_set', function () {
        rtwwdpd_select(this, '#rtwgnrl_set_tab');
    });
    $(document).on('click', '#rtwprice_set', function () {
        rtwwdpd_select(this, '#rtwprice_set_tab');
    });
    $(document).on('click', '#rtwoffer_set', function () {
        rtwwdpd_select(this, '#rtwoffer_set_tab');
    });
    $(document).on('click', '#rtwbogo_set', function () {
        rtwwdpd_select(this, '#rtwbogo_set_tab');
    });
    $(document).on('click', '#rtwmsg_set', function () {
        rtwwdpd_select(this, '#rtwmsg_set_tab');
    });
    $(document).on('click', '#rtwtimer_set', function () {
        rtwwdpd_select(this, '#rtwtimer_set_tab');
    });

    $(document).on('click', '#rtwtier_rule', function () {
        rtwwdpd_select(this, '#rtwwdpd_tiered_rule_tab');
    });
    $(document).on('click', '#rtwtier_restrict', function () {
        rtwwdpd_select(this, '#rtwwdpd_tiered_restr_tab');
    });
    $(document).on('click', '#rtwtier_validity', function () {
        rtwwdpd_select(this, '#rtwwdpd_tiered_time_tab');
    });

    $(document).on('click', '#rtwcat_rule', function () {
        rtwwdpd_select(this, '#rtwcat_rule_tab');
    });
    $(document).on('click', '#rtwcat_restrict', function () {
        rtwwdpd_select(this, '#rtwcat_restriction_tab');
    });
    $(document).on('click', '#rtwcat_validity', function () {
        rtwwdpd_select(this, '#rtwcat_time_tab');
    });
    $(document).on('click', '#rtwtag_rule', function () {
        rtwwdpd_select(this, '#rtwtag_rule_tab');
    });
    $(document).on('click', '#rtwtag_restrict', function () {
        rtwwdpd_select(this, '#rtwtag_restriction_tab');
    });
    $(document).on('click', '#rtwtag_validity', function () {
        rtwwdpd_select(this, '#rtwtag_time_tab');
    });
    $(document).on('click', '#rtwcat_com_rule', function () {
        rtwwdpd_select(this, '#rtwcat_com_rule_tab');
    });
    $(document).on('click', '#rtwcat_com_rest', function () {
        rtwwdpd_select(this, '#rtwcat_com_rest_tab');
    });
    $(document).on('click', '#rtwcat_com_time', function () {
        rtwwdpd_select(this, '#rtwcat_com_time_tab');
    });

    $(document).on('click', '#rtwwdpd_adv', function () {
        rtwwdpd_select(this, '#rtwwdpd_adv_tab');
    });

    $(document).on('click', '#rtw_speci', function () {
        $(document).find('#edit_chk').val('save');
        $(document).find('.rtwwdpd_save_rule').val('Save Rule');

    });

    ////////////////////////////////////////////////////////////////////////////

    var rtw_datatable;
    ////////////////////for editing cart rule table data////////////////////
    $(document).on('click', '#rtwwdpd_single_cart_rule', function () {
        $(document).find('#edit_chk_cart').val('save');
        $(document).find('.rtwwdpd_save_rule').val('Save Rule');
    });

    //////////////////for editing single category rule table data///////////////
    $(document).on('click', '#rtwwdpd_single_cat', function () {
        $(document).find('#rtw_save_single_cat').val('save');
        $(document).find('.rtwwdpd_save_cat').val('Save Rule');
    });

    /////////////////////// for editing tag rule table data /////
    $(document).on('click', '#rtwwdpd_single_tag', function () {
        $(document).find('#rtw_save_single_tag').val('save');
        $(document).find('.rtwwdpd_save_tag').val('Save Rule');
    });

    ////////////////////for editing variation rule table data////////////
    $(document).ready(function () {
        $(document).on('click', '.woocommerce_variation', function () {
            $(document).find('.rtwwdpd_select2').select2();
        });
        var plus_on = $(document).find('#rtwwdpd_rule_on_plus').val();

        if (plus_on == 'rtw_amt') {
            $(document).find('#rtw_min_quant').each(function () {
                $(this).hide();
            });
            $(document).find('#rtw_min_price').each(function () {
                $(this).show();
            });
        }
        if (plus_on == 'rtw_quant') {
            $(document).find('#rtw_min_quant').each(function () {
                $(this).show();
            });
            $(document).find('#rtw_min_price').each(function () {
                $(this).hide();
            });
        }
        if (plus_on == 'rtw_both') {
            $(document).find('.rtw_min_quant').each(function () {
                $(this).show();
            });
            $(document).find('.rtw_min_price').each(function () {
                $(this).show();
            });
        }

        var pro_cat = $(document).find('#rtwwdpd_rule_for_plus').val();
        if (pro_cat == 'rtwwdpd_product') {
            $(document).find('.rtw_if_prod').show();
            $(document).find('.rtw_if_cat').hide();

        } else if (pro_cat == 'rtwwdpd_category') {
            $(document).find('.rtw_if_cat').show();
            $(document).find('.rtw_if_prod').hide();
        }
        else {
            $(document).find('.rtw_if_prod').hide();
            $(document).find('.rtw_if_cat').hide();
        }


        $(document).find('#rtw_min_quant').hide();
        // $(document).find('.rtw_min_quant').hide();
        // $(document).find('#rtw_min_price').hide();
        $(document).find('.rtwwdpd_combi_cat_tab').hide();
        $(document).find('#rtwwdpd_rule_tab_combi').hide();
        $(document).find('.rtwwdpd_bogo_c_table').hide();
        $(document).find('.rtwwdpd_prod_c_table').hide();
        $(document).find('.rtwwdpd_cat_c_table').hide();
        $(document).find('.rtwwdpd_add_combi_rule_tab').hide();
        $(document).find('.rtwwdpd_combi_cat_tab').hide();
        $(document).find('.rtwwdpd_bogo_combi_tab').hide();
        $(document).find('.rtwwdpd_add_single_rule').hide();
        $(document).find('.rtwwdpd_single_cat_rule').hide();
        $(document).find('.rtwwdpd_add_tier_cat_rule_tab').hide();
        $(document).find('.rtwwdpd_tier_c_table').hide();
        $(document).find('.rtwwdpd_bogo_edit_table').show();
        $(document).find('.rtwwdpd_bogo_c_edit_table').show();
        $(document).find('.rtwwdpd_add_tier_pro_rule_tab').hide();
        $(document).find('#rtwwdpd_attribute_val_size').hide();
        $(document).find('#rtwwdpd_attribute_val_col').hide();
        $(document).find('.rtwwdpd_single_bogo_rule_tab').hide();
        $(document).find('#rtw_for_product').show();
        $(document).find('#rtw_for_category').hide();
        // $(document).find('.rtw_if_prod').hide();
        // $(document).find('.rtw_if_cat').hide();
        $(document).find('.rtwwdpd_active').show();
        $(document).find('#rtwoffer_set_tab').hide();
        $(document).find('#rtwbogo_set_tab').hide();
        $(document).find('#rtwwdpd_restriction_tab_combi').hide();
        $(document).find('#rtwwdpd_time_tab_combi').hide();
        $(document).find('#rtwbogo_restrict_tab').hide();
        $(document).find('#rtwbogo_validity_tab').hide();
        $(document).find('.rtwwdpd_next_buy_tab').hide();
        $(document).find('.rtwwdpd_prod_c_table_edit').show();

        if ($(document).find('#rtwwdpd_edit_combi_prod').hasClass('rtwwdpd_prod_c_table_edit')) {
            if (!rtw_datatable) {
                rtw_datatable = $(document).find('.rtwtables').DataTable({
                    "order": [],
                    "columnDefs": [{ orderable: false, targets: [0] }],
                });
            }
            $(document).find('.rtwwdpd_tier_pro_table').hide();
            $(document).find('.rtwwdpd_bogo_table').hide();
            $(document).find('.rtwwdpd_prod_table').hide();
            $(document).find('.rtwwdpd_cat_table').hide();
        }

        $("#rtw_setting_tbl tbody").sortable({
            handle: 'td.rtwupdwn',
            stop: function (event, ui) {

            }
        });
        $("#rtw_setting_tbl tbody").disableSelection();

        $('#rtw_setting_tbl tbody > tr').click(function () {
            var row = 1;
            $('#rtw_setting_tbl tbody  > tr').each(function () {
                $(this).find('.rtwrow_no').val(row);
                row = row + 1;
            });
        });

        $(".rtwtable tbody").sortable({
            handle: 'td.rtw_drag',
            stop: function (event, ui) {

            }
        });
        $(".rtwtable tbody").disableSelection();
        $(".rtwtables tbody").sortable({
            handle: 'td.rtw_drag',
            stop: function (event, ui) {

            }
        });
        $(".rtwtables tbody").disableSelection();

        $(document).on('click', '.rtw_drag', function () {
            var data_val = $(this).closest('table').data('value');
            var rtw_arry = [];
            if (data_val == 'rtwspecific') {
                $('.rtwtable tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'specific_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            } else if (data_val == 'categor') {
                $('.rtwtable tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'category_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            } else if (data_val == 'prodct') {

                $('.rtwtable tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'prodct_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            } else if (data_val == 'attr_tbl') {
                $('.rtwtable tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'attr_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            } else if (data_val == 'vari_tbl') {
                $('.rtwtable tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'variation_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            } else if (data_val == 'tier_pro_tbl') {
                $('.rtwtable tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'tier_pro_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            } else if (data_val == 'shipp_tbl') {
                $('.rtwtable tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'shipp_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            } else if (data_val == 'pro_tag_tbl') {
                $('.rtwtable tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'pro_tag_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            } else if (data_val == 'pay_tbl') {
                $('.rtwtable tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'pay_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            } else if (data_val == 'cart_tbl') {
                $('.rtwtable tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'cart_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            } else if (data_val == 'bogo_tbl') {
                $('.rtwtable tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'bogo_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            }
        });

        $(document).on('click', '.rtw_drags', function () {
            var data_val = $(this).closest('table').data('value');
            var rtw_arry = [];
            if (data_val == 'prodct_com') {
                $('.rtwtables tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'prodct_com_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            } else if (data_val == 'tier_cat_tbl') {
                $('.rtwtables tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'tier_cat_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            } else if (data_val == 'bogo_cat_tbl') {
                $('.rtwtables tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'bogo_cat_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            } else if (data_val == 'categor_com') {
                $('.rtwtables tbody > tr').each(function () {
                    var val = $(this).data('val');
                    rtw_arry.push(val);
                });
                var data = {
                    action: 'rtw_cat_tbl',
                    table: 'category_com_tbl',
                    rtwarray: rtw_arry,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function (response) { }
                });
            }
        });

    });

    $(document).on('change', '.rtwwdpd_rule_on', function () {
        var val = $(this).val();
        if (val == 'rtw_amt') {
            $(document).find('#rtw_min_quant').hide();
            $(document).find('#rtw_min_price').show();
        }
        if (val == 'rtw_quant') {
            $(document).find('#rtw_min_price').hide();
            $(document).find('#rtw_min_quant').show();
        }
        if (val == 'rtw_both') {
            $(document).find('#rtw_min_price').show();
            $(document).find('#rtw_min_quant').show();
        }
    });

    $(document).on('click', '#rtwwdpd_clr_dta_cancel', function () {
        $(document).find(":input[name=rtwwdpd_sale_name]").val("");
        $(document).find("#rtwproduct").val("");
        $(document).find(".rtwwdpd_select_roles").val("");
        $(document).find("#td_quant").children("input").val("");
        $(document).find(":input[name=rtwwdpd_sale_discount_value]").val("");
        $(document).find(":input[name=rtwwdpd_sale_max_discount]").val("");
        $(document).find(":input[name=rtwwdpd_sale_min_orders]").val("");
        $(document).find(":input[name=rtwwdpd_sale_min_spend]").val("");
        $(document).find(":input[name=rtwwdpd_sale_from_date]").val("");
        $(document).find(":input[name=rtwwdpd_sale_to_date]").val("");
        $(document).find('.rtwwdpd_rule_tab_combi').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab_combi').removeClass('active');
        $(document).find('.rtwwdpd_time_tab_combi').removeClass('active');
        $(document).find('#rtwwdpd_rule_tab_combi').show();
        $(document).find('#rtwwdpd_restriction_tab_combi').hide();
        $(document).find('#rtwwdpd_time_tab_combi').hide();
    });

    $(document).on('change', '#rtwwdpd_rule_on_plus', function () {
        var val = $(this).val();

        if (val == 'rtw_amt') {
            $(document).find('#rtw_min_quant').each(function () {
                $(this).hide();
            });
            $(document).find('#rtw_min_price').each(function () {
                $(this).show();
            });
        }
        if (val == 'rtw_quant') {
            $(document).find('#rtw_min_quant').each(function () {
                $(this).show();
            });
            $(document).find('#rtw_min_price').each(function () {
                $(this).hide();
            });
        }
        if (val == 'rtw_both') {
            $(document).find('#rtw_min_quant').each(function () {
                $(this).show();
            });
            $(document).find('#rtw_min_price').each(function () {
                $(this).show();
            });
        }
    });

    $(document).on('click', '.rtwwdpd_single_prod_rule', function () {
        $(document).find('#edit_chk_single').val('save');
        $(document).find('.rtwwdpd_prod_rule_tab').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab').removeClass('active');
        $(document).find('.rtwwdpd_time_tab').removeClass('active');
        $(document).find('.rtwwdpd_save_rule').val('Save Rule');
        $(document).find('.rtwwdpd_add_single_rule').show();
        $(document).find('#rtwwdpd_rule_tab').show();
        $(document).find('.rtwwdpd_prod_rule_tab').show();
        $(document).find('.rtwwdpd_add_combi_rule_tab').hide();
        $(document).find('#rtwwdpd_restriction_tab_combi').hide();
        $(document).find('#rtwwdpd_time_tab_combi').hide();
        $(document).find('#rtwwdpd_restriction_tab').hide();
        $(document).find('#rtwwdpd_time_tab').hide();
        $(document).find('.rtwwdpd_prod_table').show();
        $(document).find('.rtwwdpd_prod_c_table').hide();
        $(document).find('.rtwwdpd_rule_tab_combi').addClass('active');
        $(document).find('#rtwwdpd_rule_tab_combi').show();
        $(document).find('.rtwwdpd_time_tab_combi ').removeClass('active');
        $(document).find('.rtwwdpd_restriction_tab_combi').removeClass('active');
    });

    $(document).on('click', '.rtwwdpd_combi_prod_rule', function () {
        $(document).find('.rtwwdpd_add_combi_rule_tab').show();
        $(document).find('.rtwwdpd_rule_tab_combi').addClass('active');
        $(document).find('.rtwproduct_restrict_combi').removeClass('active');
        $(document).find('.rtwproduct_validity_combi').removeClass('active');
        $(document).find('#rtwwdpd_rule_tab_combi').show();
        $(document).find('.rtwwdpd_add_single_rule').hide();
        $(document).find('#rtwwdpd_restriction_tab_combi').hide();
        $(document).find('#rtwwdpd_time_tab_combi').hide();
        $(document).find('.rtwwdpd_prod_c_table').show();
        $(document).find('.rtwwdpd_prod_c_table_edit').show();
        $(document).find('.rtwwdpd_prod_table').hide();
        if (!rtw_datatable) {
            rtw_datatable = $(document).find('.rtwtables').DataTable({
                "order": [],
                "columnDefs": [{ orderable: false, targets: [0] }],
            });
        }
    });

    $(document).on('click', '#rtwwdpd_plus_rule', function () {
        $(document).find('#rtwwdpd_plus').show();
    });

    $(document).on('click', '.rtwwdpd_single_cat', function () {
        $(document).find('.rtwwdpd_single_cat_rule_tab').show();
        $(document).find('.rtwwdpd_single_cat_rule_tab').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab').removeClass('active');
        $(document).find('.rtwwdpd_time_tab').removeClass('active');
        $(document).find('#rtwcat_rule_tab').show();
        $(document).find('.rtwwdpd_single_cat_rule').show();
        $(document).find('.rtwwdpd_combi_cat_tab').hide();
        $(document).find('#rtwcat_restriction_tab').hide();
        $(document).find('#rtwcat_time_tab').hide();
        $(document).find('.rtwwdpd_cat_table').show();
        $(document).find('.rtwwdpd_cat_c_table').hide();
    });

    $(document).on('click', '.rtwwdpd_single_tag', function () {
        $(document).find('.rtwwdpd_single_tag_rule_tab').show();
        $(document).find('.rtwwdpd_single_tag_rule_tab').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab').removeClass('active');
        $(document).find('.rtwwdpd_time_tab').removeClass('active');
        $(document).find('#rtwtag_rule_tab').show();
        $(document).find('.rtwwdpd_single_tag_rule').show();
        // $(document).find('.rtwwdpd_combi_cat_tab').hide();
        $(document).find('#rtwtag_restriction_tab').hide();
        $(document).find('#rtwtag_time_tab').hide();
        $(document).find('.rtwwdpd_tag_table').show();
        // $(document).find('.rtwwdpd_cat_c_table').hide();
    });

    $(document).on('click', '.rtwwdpd_combi_cat', function () {
        $(document).find('.rtwwdpd_combi_cat_tab').show();
        $(document).find('.rtwwdpd_cat_rule_tab_combi').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab_combi').removeClass('active');
        $(document).find('.rtwwdpd_time_tab_combi').removeClass('active');
        $(document).find('#rtwcat_com_rule_tab').show();
        $(document).find('.rtwwdpd_cat_rule_tab_combi').show();
        $(document).find('.rtwwdpd_single_cat_rule').hide();
        $(document).find('#rtwcat_com_rest_tab').hide();
        $(document).find('#rtwcat_com_time_tab').hide();
        $(document).find('.rtwwdpd_cat_c_table').show();
        $(document).find('.rtwwdpd_cat_table').hide();
        if (!rtw_datatable) {
            rtw_datatable = $(document).find('.rtwtables').DataTable({
                "order": [],
                "columnDefs": [{ orderable: false, targets: [0] }],
            });
        }
    });

    $(document).on('click', '.rtwwdpd_single_bogo_rule', function () {
        $(document).find('.rtwwdpd_single_bogo_rule_tab').show();
        $(document).find('.rtwwdpd_bogo_rule_tab').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab').removeClass('active');
        $(document).find('.rtwwdpd_time_tab').removeClass('active');
        $(document).find('.rtwwdpd_bogo_rule_tab').show();
        $(document).find('#rtwbogo_rule_tab').show();
        $(document).find('.rtwwdpd_bogo_combi_tab').hide();
        $(document).find('.rtwwdpd_bogo_tag_tab').hide();
        $(document).find('#rtwbogo_restrict_tab').hide();
        $(document).find('#rtwbogo_validity_tab').hide();
        $(document).find('.rtwwdpd_bogo_table').show();
        $(document).find('.rtwwdpd_bogo_c_table').hide();
        $(document).find('.rtwwdpd_bogo_t_table').hide();
    });

    $(document).on('click', '.rtwwdpd_cat_bogo_rule', function () {
        $(document).find('.rtwwdpd_bogo_combi_tab').show();
        $(document).find('.rtwwdpd_bogo_c_rule_tab').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab_combi').removeClass('active');
        $(document).find('.rtwwdpd_time_tab_combi').removeClass('active');
        $(document).find('.rtwwdpd_bogo_c_rule_tab').show();
        $(document).find('#rtwwdpd_rule_tab_combi').show();
        $(document).find('.rtwwdpd_single_bogo_rule_tab').hide();
        $(document).find('.rtwwdpd_bogo_tag_tab').hide();
        $(document).find('#rtwwdpd_restriction_tab_combi').hide();
        $(document).find('#rtwwdpd_time_tab_combi').hide();
        $(document).find('.rtwwdpd_bogo_c_table').show();
        $(document).find('.rtwwdpd_bogo_t_table').hide();
        $(document).find('.rtwwdpd_bogo_table').hide();
        if (!rtw_datatable) {
            rtw_datatable = $(document).find('.rtwtables').DataTable({
                "order": [],
                "columnDefs": [{ orderable: false, targets: [0] }],
            });
        }

    });

    //// bogo tag rule

    $(document).on('click', '.rtwwdpd_tag_bogo_rule', function () {
        $(document).find('.rtwwdpd_bogo_tag_tab').show();
        $(document).find('.rtwwdpd_bogo_t_rule_tab').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab_tag').removeClass('active');
        $(document).find('.rtwwdpd_time_tab_tag').removeClass('active');
        $(document).find('.rtwwdpd_bogo_t_rule_tab').show();
        $(document).find('#rtwwdpd_rule_tab_tag').show();
        $(document).find('.rtwwdpd_single_bogo_rule_tab').hide();
        $(document).find('.rtwwdpd_bogo_combi_tab').hide();
        $(document).find('#rtwwdpd_restriction_tab_tag').hide();
        $(document).find('#rtwwdpd_time_tab_tag').hide();
        $(document).find('.rtwwdpd_bogo_t_table').show();
        $(document).find('.rtwwdpd_bogo_c_table').hide();
        $(document).find('.rtwwdpd_bogo_table').hide();
        if (!rtw_datatable) {
            rtw_datatable = $(document).find('.rtwtables').DataTable({
                "order": [],
                "columnDefs": [{ orderable: false, targets: [0] }],
            });
        }

    })

    ///
    $(document).on('click', '.rtwwdpd_tier_pro_rule', function () {
        $(document).find('.rtwwdpd_add_tier_pro_rule_tab').show();
        $(document).find('.rtwwdpd_rule_tab').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab').removeClass('active');
        $(document).find('.rtwwdpd_time_tab').removeClass('active');
        $(document).find('#rtwwdpd_tiered_rule_tab').show();
        $(document).find('#rtwwdpd_tiered_restr_tab').hide();
        $(document).find('#rtwwdpd_tiered_time_tab').hide();
        $(document).find('.rtwwdpd_add_tier_cat_rule_tab').hide();
        $(document).find('.rtwwdpd_tier_pro_table').show();
        $(document).find('.rtwwdpd_tier_c_table').hide();
    });

    $(document).on('click', '.rtwwdpd_tier_cat_rule', function () {
        $(document).find('.rtwwdpd_add_tier_cat_rule_tab').show();
        $(document).find('.rtwwdpd_rule_tab_combi').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab_combi').removeClass('active');
        $(document).find('.rtwwdpd_time_tab_combi').removeClass('active');
        $(document).find('#rtwwdpd_rule_tab_combi').show();
        $(document).find('#rtwwdpd_restriction_tab_combi').hide();
        $(document).find('#rtwwdpd_time_tab_combi').hide();
        $(document).find('.rtwwdpd_add_tier_pro_rule_tab').hide();
        $(document).find('.rtwwdpd_tier_c_table').show();
        $(document).find('.rtwwdpd_tier_pro_table').hide();
        if (!rtw_datatable) {
            rtw_datatable = $(document).find('.rtwtables').DataTable({
                "order": [],
                "columnDefs": [{ orderable: false, targets: [0] }],
            });
        }
    });

    $(document).on('click', '.rtwwdpd_next_buy', function () {
        $(document).find('.rtwwdpd_next_buy_tab').show();
        $(document).find('.rtwwdpd_next_buy_rule_tab').addClass('active');
        $(document).find('.rtwwdpd_restriction_tab').removeClass('active');
        $(document).find('.rtwwdpd_time_tab').removeClass('active');
        $(document).find('#rtwwdpd_next_buy_rule_tab').show();
        $(document).find('.rtwwdpd_next_buy_rule_tab').show();
        $(document).find('#rtwwdpd_restrict_tab').hide();
        $(document).find('#rtwwdpd_time_tab').hide();
    });


    $(document).on('change', '#rtwwdpd_check_for', function () {
        var val = $(this).find("option:selected").text();

        $(document).find("[for=rtwwdpd_min]").html('Minimum ' + val);
        $(document).find("[for=rtwwdpd_max]").html('Maximum ' + val);
        $(document).find(".rtwtiered_chk_for").text(val + ' ');
    });
    $(document).find('#product_id').select2();

    $(document).on('change', '#rtwwdpd_discount_type', function () {
        var val = $(this).find("option:selected").text();
        $(document).find("[for=rtwwdpd_discount_value]").html(val + ' (Required)');
        $(document).find('#rtw_header').text(val + ' ');
    });

    $(document).on('change', '#rtwwdpd_dsnt_type', function () {
        var val = $(this).find("option:selected").text();
        $(document).find('#rtwwdpd_dsnt_value').text(val);
    });

    $(document).on('change', '#rtwwdpd_rule_for', function () {
        var val = $(this).find("option:selected").text();
        $(document).find('#rtw_min').text('Minimum ' + val);
    });

    $(document).on('change', '#rtwwdpd_sale_of', function () {
        var val = $(this).find("option:selected").text();
        val = $.trim(val);
        if (val == 'Products') {
            $(document).find('#rtw_for_product').show();
            $(document).find('#rtw_for_category').hide();
            $(document).find('#category_id').val('');
        } else if (val == 'Category') {
            $(document).find('#rtw_for_product').hide();
            $(document).find('#rtwproduct').val('');
            $(document).find('#rtw_for_category').show();
        }
    });

    ////////// for insertion of search tiered field /////////////
    $(document).on('click', '#rtwadd_tiered', function () {
        var text = $(document).find('#rtwtiered').text();
        text = $.trim(text);
        if (text == '') {
            alert('Please select a product on which the rule should be applied.');
            return false;
        }
        var row_no = (jQuery('#rtwtiered_table >tbody >tr').length) + 1;
        var max = parseInt($(document).find('.quant_max').val()) + 1;
        var max_m = max + 1;
        $(document).find('.quant_max').removeClass('quant_max');
        var quant_min = '<input type="number" min="1" name="quant_min[]" value="' + max + '"  />';
        var quant_max = '<input type="number" class="quant_max max" min="1" name="quant_max[]" value="' + max_m + '"  />';
        var dis = '<input type="number" min="0.1" step="0.01" name="discount_val[]" value="0"  />';
        var remove = '<a class="button insert rtw_remov_tiered" name="deletebtn" >Remove</a>';

        $('#product_list_body').append('<tr><td>Tier ' + row_no + '</td><td>' + quant_min + '</td><td>' + quant_max + '</td><td>' + dis + '</td><td>' + remove + '</td></tr>');
        jQuery('#rtwtiered' + row_no).trigger('wc-enhanced-select-init');

    });

    $(document).on('click', '.rtw_remov_tiered', function () {
        var row_no = 1;
        $(document).find('#rtwproduct_table tbody tr').each(function () {
            $(this).find('td:first-child').text(row_no);
            row_no = row_no + 1;
        });
        $(this).closest('tr').prev().find('.max').addClass('quant_max');
        $(this).closest('tr').remove();
    });

    $(document).on('click', '#rtwadd_tiered_cat', function () {
        var text = $(document).find('#rtwwdpd_category_id').val();
        text = $.trim(text);
        if (text === '') {
            alert('Please select atleast one category on which the rule should be applied.');
            return false;
        }
        var row_no = (jQuery('#rtwtiered_tbl_cat >tbody >tr').length) + 1;
        var max = parseInt($(document).find('.quant_c_max').val()) + 1;
        var max_m = max + 1;
        $(document).find('.quant_c_max').removeClass('quant_c_max');
        var quant_min = '<input type="number" min="1" name="quant_min[]" value="' + max + '"  />';
        var quant_max = '<input type="number" class="quant_c_max max" min="1" name="quant_max[]" value="' + max_m + '"  />';
        var dis = '<input type="number" min="0.1" step="0.01" name="discount_val[]" value="0"  />';
        var remove = '<a class="button insert rtw_remov_tier_cat" name="deletebtn" >Remove</a>';

        $('#product_cat_tier').append('<tr><td>Tier ' + row_no + '</td><td>' + quant_min + '</td><td>' + quant_max + '</td><td>' + dis + '</td><td>' + remove + '</td></tr>');
        jQuery('#rtwwdpd_category_id' + row_no).trigger('wc-enhanced-select-init');

    });

    $(document).on('click', '.rtw_remov_tier_cat', function () {
        var row_no = 1;
        $(document).find('#rtwproduct_table tbody tr').each(function () {
            $(this).find('td:first-child').text(row_no);
            row_no = row_no + 1;
        });
        $(this).closest('tr').prev().find('.max').addClass('quant_c_max');
        $(this).closest('tr').remove();
    });

    $(document).on('change', '#rtwwdpd_sale_check_for', function () {
        var val = $(this).find("option:selected").text();
        $(document).find('.rtw_sale_quant').text(val);
    });


    $(document).on('change', '#rtwwdpd_rule_for_plus', function () {
        var val = $(this).find("option:selected").val();

        if (val == 'rtwwdpd_product') {
            $(document).find('.rtw_if_prod').show();
            $(document).find('.rtw_if_cat').hide();

        } else if (val == 'rtwwdpd_category') {
            $(document).find('.rtw_if_cat').show();
            $(document).find('.rtw_if_prod').hide();
        }
    });

    $(document).on('change', '.rtw_plus_mem', function () {
        var user_id = $(this).val();
        var checked = '';
        if ($(this).is(":checked")) {
            checked = 'checked';
        } else {
            checked = 'unchecked';
        }
        var data = {
            action: 'rtwwdpd_plus_member',
            'user_id': user_id,
            'checked': checked,
            security_check: rtwwdpd_ajax.rtwwdpd_nonce
        };
        $.ajax({
            url: rtwwdpd_ajax.ajax_url,
            type: "POST",
            data: data,
            dataType: 'json',
            success: function (response) { }
        });
    });

    $(document).on('change', '.rtw_enable_nth_order', function () {
        var rtw_val = $(this).val();
        var data = {
            action: 'rtw_enable_nth_order',
            'enable': rtw_val,
            security_check: rtwwdpd_ajax.rtwwdpd_nonce
        };
        $.ajax({
            url: rtwwdpd_ajax.ajax_url,
            type: "POST",
            data: data,
            dataType: 'json',
            success: function (response) { }
        });
    });

    $(document).on('change', '.rtwwdpd_apply_shipping_discount_on', function () {
        var rtw_val = $(this).val();
        var data = {
            action: 'rtwwdpd_apply_shipping_discount',
            'shipping_discount_on': rtw_val,
            security_check: rtwwdpd_ajax.rtwwdpd_nonce
        };
        $.ajax({
            url: rtwwdpd_ajax.ajax_url,
            type: "POST",
            data: data,
            dataType: 'json',
            success: function (response) { }
        });
    });

    $(document).on('change', '.rtwwdpd_show_ship_on_chkout', function () {
        var rtw_val = $(this).val();
        var data = {
            action: 'rtwwdpd_show_ship_on_chkout',
            'shipping_discount_on': rtw_val,
            security_check: rtwwdpd_ajax.rtwwdpd_nonce
        };
        $.ajax({
            url: rtwwdpd_ajax.ajax_url,
            type: "POST",
            data: data,
            dataType: 'json',
            success: function (response) { }
        });
    });

    $(document).on('change', '.rtw_enable_plus', function () {
        var rtw_val = $(this).val();
        var data = {
            action: 'rtw_enable_plus',
            'enable': rtw_val,
            security_check: rtwwdpd_ajax.rtwwdpd_nonce
        };
        $.ajax({
            url: rtwwdpd_ajax.ajax_url,
            type: "POST",
            data: data,
            dataType: 'json',
            success: function (response) { }
        });
    });
    $(document).on('change', '.rtw_enable_specific', function () {
        var rtw_val = $(this).val();
        var data = {
            action: 'rtwwdpd_specific_enable',
            'enable': rtw_val,
            security_check: rtwwdpd_ajax.rtwwdpd_nonce
        };
        $.ajax({
            url: rtwwdpd_ajax.ajax_url,
            type: "POST",
            data: data,
            dataType: 'json',
            success: function (response) { }
        });
    });
    $(document).on('change', '.rtw_enable_next_buy', function () {
        var rtw_val = $(this).val();
        var data = {
            action: 'rtwwdpd_next_buy',
            'enable': rtw_val,
            security_check: rtwwdpd_ajax.rtwwdpd_nonce
        };
        $.ajax({
            url: rtwwdpd_ajax.ajax_url,
            type: "POST",
            data: data,
            dataType: 'json',
            success: function (response) { }
        });
    });

})(jQuery);

function rtwwdpd_select(obj, selector) {
    let all_links = document.querySelectorAll('.active');
    for (let dv of all_links) {
        dv.classList.remove('active');
    }
    obj.parentElement.className += ' active';
    let div = document.querySelector(selector);
    let alldiv = document.querySelectorAll('.options_group');
    for (let dv of alldiv) {
        dv.style.display = 'none';
    }
    if (div.style.display == "none") {
        div.style.display = "block";
    } else {
        div.style.display = "none";
    }
}

function show_all(id) {
    let div = document.querySelector(id);
    let alldiv = document.querySelectorAll('.options_group');
    for (let dv of alldiv) {
        dv.style.display = 'block';
    }
    if (div.style.display == "none") {
        div.style.display = "block";
    } else {
        div.style.display = "block";
    }
}