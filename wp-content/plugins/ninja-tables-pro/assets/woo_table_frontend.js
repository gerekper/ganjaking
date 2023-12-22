/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*********************************************!*\
  !*** ./assets_source/woo_table_frontend.js ***!
  \*********************************************/
jQuery(document).ready(function ($) {
  jQuery(document).on('ninja_table_loaded', function (event, $table, settings) {
    if (settings.provider != 'wp_woo') {
      return;
    }
    var requestData = {};
    var selectedVariations = {};
    var cartItems = window.ninjaTableCartItems || {};
    var isEqual = function isEqual(a, b) {
      return Object.keys(a).every(function (key, index, array) {
        return a[key] === b[key] && b[key].length > 0 || a[key].length === 0 && b[key].length > 0;
      });
    };
    var isEmpty = function isEmpty(arr) {
      if (arr.length === 0) {
        return true;
      }
      return arr.includes('');
    };
    function selectDefaultVariations($el) {
      var isOnChange = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      var children = $el.children();
      var productId = children.data('product_id');
      var productVariations = children.data('product_variations');
      var selectField = $('.ntb_attribute_select_' + productId);
      var selectedAttributeName = selectField.data('attribute_name');
      var selectedAttributeValue = selectField.val();
      var productType = children.data('product_type');
      if (isOnChange) {
        productId = $el.data('product_id');
        productVariations = $el.parents().children().data('product_variations');
        productType = $el.parents().children().data('product_type');
        selectedAttributeName = $el.data('attribute_name');
        selectedAttributeValue = $el.val();
        selectField = $('.ntb_attribute_select_' + productId);
      }
      if (productType === 'variable') {
        var selectedValues = {};
        var selectedVariation = {};
        selectField.each(function (i, obj) {
          var $select = $(obj);
          var attrName = $select.data('attribute_name');
          var attrLabel = $select.data('attribute_label');
          var attrValue = $select.val();
          var optionKeyValues = $select.data('default_options');
          var defaultOptions = Object.keys(optionKeyValues);
          var selectOptions = defaultOptions;
          if (selectedAttributeName !== attrName) {
            var values = [];
            for (var _i = 0; _i < productVariations.length; _i++) {
              var variation = productVariations[_i];
              if (variation.attributes[selectedAttributeName] === selectedAttributeValue) {
                values.push(variation.attributes[attrName]);
              }
            }
            if (!isEmpty(values)) {
              selectOptions = values;
            } else {
              selectOptions = defaultOptions;
            }
          } else {
            selectOptions = defaultOptions;
          }
          $select.empty();
          $select.append('<option value="">' + attrLabel + '</option>');
          selectOptions.map(function (option, index) {
            $select.append('<option value="' + option + '">' + optionKeyValues[option] + '</option>');
          });
          var selectedAttrValue = '';
          if (isOnChange && !selectOptions.includes(attrValue)) {
            $select.val('');
            selectedAttrValue = '';
          } else {
            selectedAttrValue = attrValue;
            $select.val(attrValue);
          }
          selectedValues[attrName] = selectedAttrValue;
        });

        /*
         If we find any variation that matches our product variation,
         then show the add to cart button with the product price & status.
         */
        selectedVariation[productId] = selectedValues;
        visibleAddToCartButton(productId, selectedVariation);
      }
    }
    function visibleAddToCartButton(productId, selectedVariation) {
      var addToCartButton = $('.nt_add_to_cart_' + productId);
      var priceSelector = $('.selected_price_' + productId);
      var productVariations = addToCartButton.data('product_variations');
      if (productVariations.length > 0) {
        productVariations.map(function (variation, index) {
          if (isEqual(variation.attributes, selectedVariation[productId])) {
            var cloneVariation = JSON.parse(JSON.stringify(variation));
            cloneVariation['attributes'] = selectedVariation[productId];
            selectedVariations[productId] = cloneVariation;
          }
        });
      }
      if (selectedVariations[productId] && isEqual(selectedVariations[productId].attributes, selectedVariation[productId])) {
        isAllowedAddToCart(selectedVariations[productId], productId);
      } else {
        priceSelector.html('');
        addToCartButton.css('opacity', '0.5');
        delete selectedVariations[productId];
      }
    }
    function isAllowedAddToCart(variation, productId) {
      var variationId = variation.variation_id;
      var isInStock = variation.is_in_stock;
      var backordersAllowed = variation.backorders_allowed;
      var maxQuantity = variation.max_qty;
      var addToCartButton = $('.nt_add_to_cart_' + productId);
      var priceSelector = $('.selected_price_' + productId);
      var priceHtml = variation.price_html;
      var availabilityHtml = variation.availability_html;
      priceSelector.html(priceHtml + ' ' + availabilityHtml);
      var addToCartButtonOpacity = function addToCartButtonOpacity() {
        var quantity = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
        if ((isInStock || backordersAllowed) && (maxQuantity === '' || maxQuantity > quantity)) {
          addToCartButton.css('opacity', '1');
          return true;
        } else {
          addToCartButton.css('opacity', '0.5');
          priceSelector.html(priceHtml + '<p class="stock out-of-stock">Out of stock</p>');
          return false;
        }
      };
      if (cartItems && Object.keys(cartItems).length > 0) {
        var cartItem = Object.values(cartItems).find(function (item) {
          return item.variation_id === variationId;
        });
        if (cartItem) {
          var quantity = cartItem.quantity;
          return addToCartButtonOpacity(quantity);
        } else {
          return addToCartButtonOpacity();
        }
      } else {
        return addToCartButtonOpacity();
      }
    }

    // Trigger the default variation select
    $table.find('.nt_add_cart_wrapper').each(function (i, obj) {
      selectDefaultVariations($(obj));
    });

    // Triggered when the user selects a variation.
    $table.on('change', '.nt_woo_attribute', function (event) {
      selectDefaultVariations($(this), true);
    });

    // Triggered when the user updates the quantity.
    $table.on('change', '.nt_woo_quantity', function (event) {
      var $el = $(this);
      var productId = $el.data('product_id');
      $table.find('.nt_add_to_cart_' + productId).attr('data-quantity', $el.val());
    });

    // Triggered when the user clicks on the add to cart button.
    $table.on('click', '.single_add_to_cart_button', function (e) {
      e.preventDefault();
      var $thisbutton = $(this);
      var productId = $thisbutton.attr('data-product_id');
      var productType = $thisbutton.attr('data-product_type');
      if (productType === 'variable') {
        var variation = selectedVariations[productId];
        var variationId = variation && variation.variation_id;
        if (!variationId) {
          alert('Please select some product options before adding this product to your cart.');
          return false;
        } else if (!isAllowedAddToCart(variation, productId)) {
          var $selectedPrice = $('.selected_price_' + productId);
          $selectedPrice.html('');
          alert('Sorry, this product is unavailable. Please choose a different combination.');
          return false;
        }
        requestData['variation_id'] = variationId;
        requestData['attributes'] = variation && variation.attributes;
      }
      requestData['product_id'] = productId;
      requestData['quantity'] = $thisbutton.attr('data-quantity');
      requestData['product_type'] = productType;
      requestData['ninja_table'] = settings.table_id;
      requestData['action'] = 'ninja_table_wp_woo_add_to_cart';
      $thisbutton.parent().addClass('nt_added_cart');
      var oldText = $thisbutton.html();
      $thisbutton.append('<span class="fooicon fooicon-loader"></span>');
      $.post(window.ninja_footables.ajax_url, requestData).then(function (response) {
        if (productType === 'variable') {
          var res = response.data;
          cartItems = res.cart_items;
          $(document.body).trigger('added_to_cart', [res.fragments.fragments, res.fragments.cart_hash, $thisbutton]);
        } else {
          $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
        }
        $table.find('a.added_to_cart.wc-forward').html('');
        var quantitySelector = $('#nt_product_qty_' + productId);
        quantitySelector.val(1);
        quantitySelector.trigger('change');
      }).fail(function (error) {}).always(function () {
        $thisbutton.html(oldText);
      });
    });
  });
});
/******/ })()
;