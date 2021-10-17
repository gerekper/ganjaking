/*
 *  Tickets Variable Product Javascript
 *  @version: 1.4.1
 */


//* Variations Plugin
(function (e, t, n, r) {
e.fn.wc_variation_form = function () {
    e.fn.wc_variation_form.find_matching_variations = function (t, n) {
        var r = [];
        for (var i = 0; i < t.length; i++) {
            var s = t[i],
            o = s.variation_id;
            e.fn.wc_variation_form.variations_match(s.attributes, n) && r.push(s)
        }
        return r
    };
    e.fn.wc_variation_form.variations_match = function (e, t) {
        var n = !0;
        for (attr_name in e) {
            var i = e[attr_name],
            s = t[attr_name];
            i !== r && s !== r && i.length != 0 && s.length != 0 && i != s && (n = !1)
        }
        return n
    };

    this.unbind("check_variations update_variation_values found_variation");
    this.find(".reset_variations").unbind("click");
    this.find(".variations select").unbind("change focusin");
    return this.on("click", ".reset_variations", function (t) {
    e(this).closest("form.variations_form").find(".variations select").val("").change();
    var n = e(this).closest(".product").find(".sku"),
    r = e(this).closest(".product").find(".product_weight"),
    i = e(this).closest(".product").find(".product_dimensions");
    n.attr("data-o_sku") && n.text(n.attr("data-o_sku"));
    r.attr("data-o_weight") && r.text(r.attr("data-o_weight"));
    i.attr("data-o_dimensions") && i.text(i.attr("data-o_dimensions"));
    return !1
    }).on("change", ".variations select", function (t) {
     
        $variation_form = e(this).closest("form.variations_form");
        $variation_form.find("input[name=variation_id]").val("").change();
        $variation_form.trigger("woocommerce_variation_select_change").trigger("check_variations", ["", !1]);
        e(this).blur();
        e().uniform && e.isFunction(e.uniform.update) && e.uniform.update()
    }).on("focusin", ".variations select", function (t) {
        $variation_form = e(this).closest("form.variations_form");
        $variation_form.trigger("woocommerce_variation_select_focusin").trigger("check_variations", [e(this).attr("name"), !0])
    }).on("check_variations", function (n, r, i) {
        var s = !0,
        o = !1,
        u = !1,
        a = {},
        f = e(this),
        l = f.find(".reset_variations");
        f.find(".variations select").each(function () {
        e(this).val().length == 0 ? s = !1 : o = !0;
        if (r && e(this).attr("name") == r) {
        s = !1;
        a[e(this).attr("name")] = ""
        } else {
        value = e(this).val();
        a[e(this).attr("name")] = value
        }
        });
        var c = parseInt(f.data("product_id")),
        h = f.data("product_variations");
        h || (h = t.product_variations[c]);
        h || (h = t.product_variations);
        h || (h = t["product_variations_" + c]);
        var p = e.fn.wc_variation_form.find_matching_variations(h, a);
        if (s) {
            var d = p.pop();
            if (d) {
                f.find("input[name=variation_id]").val(d.variation_id).change();
                f.trigger("found_variation", [d])
            } else {
                f.find(".variations select").val("");
                i || f.trigger("reset_image");
                alert(woocommerce_params.i18n_no_matching_variations_text)
            }
        } else {
            f.trigger("update_variation_values", [p]);
            i || f.trigger("reset_image");
            r || f.find(".single_variation_wrap").slideUp("200")
        }
    o ? l.css("visibility") == "hidden" && l.css("visibility", "visible").hide().fadeIn() : l.css("visibility", "hidden")
    }).on("reset_image", function (t) {
        var n = e(this).closest(".product"),
        r = n.find("div.images img:eq(0)"),
        i = n.find("div.images a.zoom:eq(0)"),
        s = r.attr("data-o_src"),
        o = r.attr("data-o_title"),
        u = i.attr("data-o_href");
        s && r.attr("src", s);
        u && i.attr("href", u);
        if (o) {
            r.attr("alt", o).attr("title", o);
            i.attr("title", o)
        }
    }).on("update_variation_values", function (t, n) {
        $variation_form = e(this).closest("form.variations_form");
        $variation_form.find(".variations select").each(function (t, r) {
            current_attr_select = e(r);
            current_attr_select.data("attribute_options") || current_attr_select.data("attribute_options", current_attr_select.find("option:gt(0)").get());
            current_attr_select.find("option:gt(0)").remove();
            current_attr_select.append(current_attr_select.data("attribute_options"));
            current_attr_select.find("option:gt(0)").removeClass("active");
            var i = current_attr_select.attr("name");
             
            for (num in n)
            if (typeof n[num] != "undefined") {
                var s = n[num].attributes;
                for (attr_name in s) {
                    var o = s[attr_name];
                    if (attr_name == i) if (o) {
                        o = e("<div/>").html(o).text();
                        o = o.replace(/'/g, "\\'");
                        o = o.replace(/"/g, '\\"');
                        current_attr_select.find('option[value="' + o + '"]').addClass("active")
                    } else current_attr_select.find("option:gt(0)").addClass("active")
                }
            }
            current_attr_select.find("option:gt(0):not(.active)").remove()
        });
        $variation_form.trigger("woocommerce_update_variation_values")
    }).on("found_variation", function (t, n) {

        var r = e(this),
        i = e(this).closest(".product"),
        s = i.find("div.images img:eq(0)"),
        o = i.find("div.images a.zoom:eq(0)"),
        u = s.attr("data-o_src"),
        a = s.attr("data-o_title"),
        f = o.attr("data-o_href"),
        l = n.image_src,
        c = n.image_link,
        h = n.image_title;
        r.find(".variations_button").show();
        r.find(".single_variation").html(n.price_html + n.availability_html);
        if (!u) {
            u = s.attr("src") ? s.attr("src") : "";
            s.attr("data-o_src", u)
        }
        if (!f) {
            f = o.attr("href") ? o.attr("href") : "";
            o.attr("data-o_href", f)
        }
        if (!a) {
            a = s.attr("title") ? s.attr("title") : "";
            s.attr("data-o_title", a)
        }
        if (l && l.length > 1) {
            s.attr("src", l).attr("alt", h).attr("title", h);
            o.attr("href", c).attr("title", h)
        } else {
            s.attr("src", u).attr("alt", a).attr("title", a);
            o.attr("href", f).attr("title", a)
        }
        var p = r.find(".single_variation_wrap"),
        d = i.find(".product_meta").find(".sku"),
        v = i.find(".product_weight"),
        m = i.find(".product_dimensions");
        d.attr("data-o_sku") || d.attr("data-o_sku", d.text());
        v.attr("data-o_weight") || v.attr("data-o_weight", v.text());
        m.attr("data-o_dimensions") || m.attr("data-o_dimensions", m.text());
        n.sku ? d.text(n.sku) : d.text(d.attr("data-o_sku"));
         
        n.weight ? v.text(n.weight) : v.text(v.attr("data-o_weight"));
        n.dimensions ? m.text(n.dimensions) : m.text(m.attr("data-o_dimensions"));
        p.find(".quantity").show();
        !n.is_in_stock && !n.backorders_allowed && r.find(".variations_button").hide();

        n.min_qty ? 
            p.find("input[name=quantity]").attr("min", n.min_qty).val(n.min_qty) : 
            p.find("input[name=quantity]").removeAttr("min");
        n.max_qty ? 
            p.find("input[name=quantity]").attr("max", n.max_qty) : 
            p.find("input[name=quantity]").removeAttr("max");

        if (n.is_sold_individually == "yes") {
            p.find("input[name=quantity]").val("1");
            p.find(".quantity").hide()
        }
        p.slideDown("200").trigger("show_variation", [n])
    });
     
    /**
    * Initial states and loading
    */
    jQuery('form.variations_form .variations select').change();
     
    /**
    * Helper functions for variations
    */
     
    // Search for matching variations for given set of attributes     
    function find_matching_variations(product_variations, settings) {
        var matching = [];
         
        for (var i = 0; i < product_variations.length; i++) {
            var variation = product_variations[i];
            var variation_id = variation.variation_id;
             
            if (variations_match(variation.attributes, settings)) {
                matching.push(variation);
            }
        }
        return matching;
    }
     
    // Check if two arrays of attributes match 
    function variations_match(attrs1, attrs2) {
        var match = true;
        for (attr_name in attrs1) {
            var val1 = attrs1[attr_name];
                var val2 = attrs2[attr_name];
            if (val1 !== undefined && val2 !== undefined && val1.length != 0 && val2.length != 0 && val1 != val2) {
                match = false;
            }
        }
        return match;
    }
 
};


// when clicked on  order now button run custom variations function
    jQuery(document).on('click', '.evotx_show_variations',function(){

        var O = jQuery(this);

        O.parent().hide();
        var _this_form = O.parent().siblings('.variations_form');

        _this_form.slideDown();

        _this_form.wc_variation_form();

        setTimeout(function(){
            vars = O.data('defv');

            _this_form.find('.variations select').each(function(){
                sO = jQuery(this);
                sF = sO.attr('id');
                if( sF in vars){
                    sO.find('option[value="'+ vars[sF] +'"]').prop('select',true);                    
                }else{
                    sO.find('option:eq(2)').prop('select',true);      
                }

                sO.change();
            });
        },200);
        

    });



    //e("form.variations_form").wc_variation_form();
    //e("form.variations_form .variations select").change()
})(jQuery, window, document); // JavaScript Document