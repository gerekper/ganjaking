/*
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.1.7
 * @author  YITH
 */

jQuery(document).ready(function ($) {
  "use strict";

  var select_default_qty = function () {

    if ('yes' == ywdpd_qty_args.is_default_qty_enabled) {
      var table = $(document).find('#ywdpd-table-discounts'),
        td = false;

      if (ywdpd_qty_args.show_minimum_price === 'yes') {

        td = table.find('td.qty-price-info').last();
      } else {
        td = table.find('td.qty-price-info').first();
      }

      td.click();
    }

  };
  $(document).on('click', '#ywdpd-table-discounts td.qty-price-info', function (e) {
    var $t = $(this),
      span_price_html = $t.html(),
      qty = ($t.data('qtymax') != '*') ? $t.data('qtymax') : $t.data('qtymin'),
      qty_field = $t.closest('.product').find('.qty'),
      price = $t.closest('.product').find('.summary  .price'),
      sale_price = price.find('del').length ? price.find('del').html() : '',
      index = $t.index(),
      td_price_info = false;


    $('td').removeClass('ywdpd_qty_active');
    if (ywdpd_qty_args.template === 'horizontal') {

      td_price_info = $(document).find('#ywdpd-table-discounts td.qty-info').get(index - 1);
    } else {

      td_price_info = $t.parent().find('td.qty-info');
    }

    $t.addClass('ywdpd_qty_active');
    if (td_price_info) {
      $(td_price_info).addClass('ywdpd_qty_active');
    }
    qty_field.val(qty);
    price.html('<del>' + sale_price + '</del> ' + span_price_html);
  });
  $(document).on('click', '#ywdpd-table-discounts td.qty-info', function (e) {

    var $t = $(this),
      index = $t.index();
    $('td.qty-info').removeClass('ywdpd_qty_active');
    $t.addClass('ywdpd_qty_active');

    var td_price_info = false;
    if (ywdpd_qty_args.template === 'horizontal') {
      td_price_info = $(document).find('#ywdpd-table-discounts td.qty-price-info').get(index - 1);
    } else {
      td_price_info = $t.parent().find('td.qty-price-info')
    }
    if (td_price_info) {
      $(td_price_info).click();
    }

  });
  $(document).on('change', 'form.cart .qty', function (e) {
    if ($(document).find('#ywdpd-table-discounts').length && 'yes' == ywdpd_qty_args.is_change_qty_enabled) {

      var qty = $(this).val(),
        table = $(document).find('#ywdpd-table-discounts'),
        td_qty_price = table.find("td.qty-price-info").filter(function () {
          var $qty = $('form.cart .qty').val(),
            max = $(this).data('qtymax');

          if( max !== '*'){
            return $(this).data('qtymin') <= $qty  && $(this).data('qtymax') >= $qty;

          }else{
            return $(this).data('qtymin') <= $qty;
          }
        }),
        td_qt_info = false;


      if (!td_qty_price.length) {
        td_qty_price = table.find("td.qty-price-info[data-qtymax='" + qty + "']");
      }
      if (ywdpd_qty_args.template === 'vertical') {
        td_qt_info = td_qty_price.parent().find('td.qty-info');
      } else {
        td_qt_info = table.find("td.qty-info").filter(function(){
          var $qty = $('form.cart .qty').val(),
          max = $(this).data('qtymax');

          if( max !== '*'){
            return $(this).data('qtymin') <= $qty && $(this).data('qtymax') >= $qty;

          }else{
            return $(this).data('qtymin') <= $qty;
          }

        });
      }
      if (td_qty_price.length) {
        table.find("td.qty-price-info").removeClass('ywdpd_qty_active');
        var span_price_html = td_qty_price.html(),
          price = td_qty_price.closest('.product').find('.summary  .price'),
          sale_price = price.find('del').length ? price.find('del').html() : '';

        td_qty_price.addClass('ywdpd_qty_active');
        price.html('<del>' + sale_price + '</del> ' + span_price_html);
      }

      if (td_qt_info.length) {
        table.find("td.qty-info").removeClass('ywdpd_qty_active');
        td_qt_info.addClass('ywdpd_qty_active');
      }
    }
  });


  var $product_id = $('[name|="product_id"]'),
    product_id = $product_id.val(),
    $variation_id = $('[name|="variation_id"]'),
    form = $product_id.closest('form'),
    $table = $('.ywdpd-table-discounts-wrapper');

  $(document).on('found_variation', form, function (event, variation) {
    $('.ywdpd-table-discounts-wrapper').replaceWith(variation.table_price);
    select_default_qty();
  });

  if (!$variation_id.length) {
    select_default_qty();
    return false;
  }

  $variation_id.on('change', function () {
    if ($(this).val() == '') {
      $('.ywdpd-table-discounts-wrapper').replaceWith($table);
    }
  });


});
