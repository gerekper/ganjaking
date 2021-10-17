
var eosa_option_sliderType = 'slider';
var eosa_option_linkSlider;
var eosa_option_language;
var eosa_option_orderby;
var eosa_option_showMap;
var eosa_option_showDetails;
var eosa_option_date_out;
var eosa_option_date_in;
var eosa_option_showevent;
var eosa_option_open_event_type;
var eosa_option_classicslider_type;
var eosa_option_minicarousel_type;
var eosa_option_mcrow_type;
var eosa_option_open_event_type_card;
var eosa_option_style;
var eosa_option_open_event_style;
var eosa_option_skin;
var eosa_option_animation;
var eosa_option_card_opentype;
var eosa_option_cover;
var eosa_option_featured;
var eosa_option_filters;

//SLIDER TYPE : 2 - CAROUSEL
var eosa_option_advs_minItems;
var eosa_option_advs_maxItems;
var eosa_option_advs_move;
var eosa_option_advs_itemWidth;
var eosa_option_advs_itemMargin;

var FINAL_SHORTCODE;
var eosa_selectedslider = 0;
var isNewSlider = false;
var sliders_count = 0;

function generate_eosa_shortcode() {

    var shortcode_linkslider='';
    var shortcode_sliderId = '';

    eosa_option_language = jQuery('#eosa_option_language').val();
    eosa_option_orderby = jQuery('#eosa_option_orderby').val();

    var eosa_option_open_event_type_txt = "";
    var eosa_option_open_event_type_card_txt = "";
    eosa_option_open_event_type = jQuery('#eosa_option_open_event_type').val();
    if (eosa_option_open_event_type != "lightbox") eosa_option_open_event_type_txt = " open_type='" + eosa_option_open_event_type + "'";
    eosa_option_open_event_type_card = jQuery('#eosa_option_open_event_type_card').val()
    if ((eosa_option_open_event_type == 'card') && (eosa_option_open_event_type_card != "auto")) eosa_option_open_event_type_card_txt = " c_dir='" + eosa_option_open_event_type_card + "'";

    var eosa_option_classicslider_type_txt = "";
    eosa_option_classicslider_type = jQuery('#eosa_option_classicslider_type').val()
    if ((eosa_option_sliderType == 'slider') && (eosa_option_classicslider_type == "mini")) eosa_option_classicslider_type_txt = " s_type='" + eosa_option_classicslider_type + "'";


    if (jQuery('#eosa_option_showMap_yes').prop("checked")) eosa_option_showMap = 'yes';
    else eosa_option_showMap = 'no';

    if (jQuery('#eosa_option_showDetails_yes').prop("checked")) eosa_option_showDetails = 'yes';
    else eosa_option_showDetails = 'no';

    if (jQuery('#eosa_option_slidername').val().length > 0) {
        var temp = convertToSlug(jQuery('#eosa_option_slidername').val());
        shortcode_sliderId = " id='" + temp + "'";
        jQuery('#eosa_option_sliderid').val(temp);
    }
    eosa_option_date_out = jQuery('#eosa_option_date_out').val();
    eosa_option_date_in = jQuery('#eosa_option_date_in').val();

    var eosa_option_showevent_txt = "";
    eosa_option_showevent = jQuery('#eosa_option_showevent').val();
    if (eosa_option_showevent != "") eosa_option_showevent_txt = " showevent='" + eosa_option_showevent + "'";

    var eosa_option_style_txt = "";
    eosa_option_style = jQuery('#eosa_option_style').val();
    if (eosa_option_style != "a") eosa_option_style_txt = " style='" + eosa_option_style + "'";

    var eosa_option_open_event_style_txt = "";
    eosa_option_open_event_style = jQuery('#eosa_option_open_event_style').val();
    if (eosa_option_open_event_style != "a") eosa_option_open_event_style_txt = " style_2='" + eosa_option_open_event_style + "'";

    var eosa_option_skin_txt = "";
    eosa_option_skin = jQuery('#eosa_option_skin').val();
    if (eosa_option_skin != "light") eosa_option_skin_txt = " skin='" + eosa_option_skin + "'";
    
    var eosa_option_animation_txt = "";
    eosa_option_animation = jQuery('#eosa_option_animation').val();
    if (eosa_option_animation != "slide") eosa_option_animation_txt = " animation='" + eosa_option_animation + "'";

    var eosa_option_card_opentype_txt = "";
    eosa_option_card_opentype = jQuery('#eosa_option_card_opentype').val();
    if (eosa_option_card_opentype != "lightbox") eosa_option_card_opentype_txt = " c_open_type='" + eosa_option_card_opentype + "'";

    var eosa_option_cover_txt = "";
    eosa_option_cover = jQuery('#eosa_option_cover').val();
    if (eosa_option_cover != "main") eosa_option_cover_txt = " cover='" + eosa_option_cover + "'";

    var eosa_option_featured_txt = "";
    eosa_option_featured = jQuery('#eosa_option_featured').val();
    if (eosa_option_featured != "all") eosa_option_featured_txt = " featured='" + eosa_option_featured + "'";

    var eosa_option_filters_txt = "";
    eosa_option_filters = jQuery('#eosa_option_filters').val();
    if (eosa_option_filters != "all") eosa_option_featured_txt = " filters='" + eosa_option_filters + "'";

    var eosa_option_extrafields_txt = "";
    eosa_option_extrafields = jQuery('#eosa_option_extrafields').val();
    if (eosa_option_extrafields != "in") eosa_option_extrafields_txt = " ef='" + eosa_option_extrafields + "'";

    //Date range
    var eosa_option_daterange = '';
    if (jQuery('#eosa_option_date_range_past').prop("checked")) eosa_option_daterange = " date_range='past'" ;
    if (jQuery('#eosa_option_date_range_future').prop("checked")) eosa_option_daterange = " date_range='future'";
    if (jQuery('#eosa_option_date_range_before').prop("checked")) {
        var date_txt = jQuery('#eosa_option_date_range_txt_before').val();
        if (date_txt.length > 0) eosa_option_daterange = " date_range='before" + date_txt + "'";
    }
    if (jQuery('#eosa_option_date_range_today').prop("checked")) eosa_option_daterange = " date_range='today'";
    if (jQuery('#eosa_option_date_range_current_week').prop("checked")) eosa_option_daterange = " date_range='current_week'";
    if (jQuery('#eosa_option_date_range_current_month').prop("checked")) eosa_option_daterange = " date_range='current_month'";
    if (jQuery('#eosa_option_date_range_after').prop("checked")) {
        var date_txt = jQuery('#eosa_option_date_range_txt_after').val();
        if (date_txt.length > 0) eosa_option_daterange = " date_range='after" + date_txt + "'";
    }
    if (jQuery('#eosa_option_date_range_between').prop("checked")) {
        var date_txt1 = jQuery('#eosa_option_date_range_txt_between1').val();
        var date_txt2 = jQuery('#eosa_option_date_range_txt_between2').val();
        if ((date_txt1.length > 0) && (date_txt2.length > 0)) eosa_option_daterange = " date_range='between" + date_txt1 + ":" + date_txt2 + "'";
    }

    //SLIDER TYPES : 1|3 - SLIDER and CAROUSEL
    if ((eosa_option_sliderType == "slider")||(eosa_option_sliderType == "carousel")) {
        if (jQuery('#eosa_option_link_type').val() == "custom_link") {
            eosa_option_linkSlider = jQuery('#eosa_option_linkSlider').val();
            if (eosa_option_linkSlider.length > 0) shortcode_linkslider = " link='" + eosa_option_linkSlider + "'";
        } else {
            if (jQuery('#eosa_option_link_type').val() == "events_list") shortcode_linkslider = " link='events_list'";
        }
    }

    //ONLY SLIDER TYPE : 1 - SLIDER
    if (eosa_option_sliderType == "slider") {
        FINAL_SHORTCODE = "[eventon_slider slider_type='" + eosa_option_sliderType + "' lan='" + eosa_option_language + "' orderby='" + eosa_option_orderby + "' map='" + eosa_option_showMap +
       "' details='" + eosa_option_showDetails + "' date_out='" + eosa_option_date_out + "' date_in='" + eosa_option_date_in + "' " + eosa_option_showevent_txt +
       eosa_option_daterange + shortcode_sliderId + eosa_option_open_event_type_txt + eosa_option_classicslider_type_txt + eosa_option_open_event_type_card_txt + shortcode_linkslider
       + eosa_option_style_txt + eosa_option_skin_txt + eosa_option_open_event_style_txt + eosa_option_animation_txt + eosa_option_card_opentype_txt + eosa_option_featured_txt +
       eosa_option_cover_txt + eosa_option_filters_txt + eosa_option_extrafields_txt + "]";
    }

     //ONLY SLIDER TYPE : 2|3 - CAROUSEL and MINICAROUSEL
    if ((eosa_option_sliderType == "carousel")||(eosa_option_sliderType == "minicarousel")) {

        eosa_option_advs_minItems = jQuery('#eosa_option_advs_minItems').val();
        eosa_option_advs_maxItems = jQuery('#eosa_option_advs_maxItems').val();
        eosa_option_advs_move = jQuery('#eosa_option_advs_move').val();
        eosa_option_advs_itemWidth = jQuery('#eosa_option_advs_itemWidth').val();
        eosa_option_advs_itemMargin = jQuery('#eosa_option_advs_itemMargin').val();

        var eosa_option_advs_minItems_text = "";
        var eosa_option_advs_maxItems_text = "";
        var eosa_option_advs_move_text = "";
        var eosa_option_advs_itemWidth_text = "";
        var eosa_option_advs_itemMargin_text = "";
        var eosa_option_minicarousel_type_text = "";
        var eosa_option_mcrow_type_text = "";
      
        if ((eosa_option_advs_minItems != 'auto') && (eosa_option_sliderType == "carousel")) eosa_option_advs_minItems_text = " car_minitems='" + eosa_option_advs_minItems + "'";
        if ((eosa_option_advs_maxItems != 'auto') && (eosa_option_sliderType == "carousel")) eosa_option_advs_maxItems_text = " car_maxitems='" + eosa_option_advs_maxItems + "'";
        if (eosa_option_advs_move != 1) eosa_option_advs_move_text = " car_move='" + eosa_option_advs_move + "'";
        if (eosa_option_advs_itemWidth != 260) eosa_option_advs_itemWidth_text = " car_itemwidth='" + eosa_option_advs_itemWidth + "'";
        if (eosa_option_advs_itemMargin != 15) eosa_option_advs_itemMargin_text = " car_itemmargin='" + eosa_option_advs_itemMargin + "'";
       

        if (eosa_option_sliderType == "minicarousel") {
            eosa_option_minicarousel_type = jQuery('#eosa_option_minicarousel_type').val();
            if (eosa_option_minicarousel_type != "mini") eosa_option_minicarousel_type_text = " s_type='" + eosa_option_minicarousel_type + "'";

            eosa_option_mcrow_type = jQuery('#eosa_option_mcrow_type').val();
            if (eosa_option_mcrow_type != "location") eosa_option_mcrow_type_text = " mcar_row='" + eosa_option_mcrow_type + "'";
        }
        
        var eosa_option_showImage_text = "";
        if (eosa_option_sliderType == "minicarousel") {
            if (jQuery('#eosa_option_showImage_yes').prop("checked")) eosa_option_showImage_text = " mcar_image='yes'";
        }

        FINAL_SHORTCODE = "[eventon_slider slider_type='" + eosa_option_sliderType + "' lan='" + eosa_option_language + "' orderby='" + eosa_option_orderby
        + "' date_out='" + eosa_option_date_out + "' date_in='" + eosa_option_date_in + "' " + eosa_option_showevent_txt + eosa_option_daterange + shortcode_sliderId +
        eosa_option_advs_minItems_text + eosa_option_advs_maxItems_text + eosa_option_advs_move_text + eosa_option_advs_itemWidth_text + eosa_option_advs_itemMargin_text + eosa_option_showImage_text +
        eosa_option_open_event_type_txt + eosa_option_minicarousel_type_text + eosa_option_mcrow_type_text + eosa_option_open_event_type_card_txt + shortcode_linkslider +
        eosa_option_style_txt + eosa_option_skin_txt + eosa_option_open_event_style_txt + eosa_option_card_opentype_txt + eosa_option_featured_txt + eosa_option_cover_txt + eosa_option_filters_txt +
        eosa_option_extrafields_txt + "]";
    }

    //ONLY SLIDER TYPE : 4 - MASONRY
    if (eosa_option_sliderType == "masonry") {

        var eosa_option_maso_col_txt = "";
        var eosa_option_maso_rand_txt = "";
        var eosa_option_maso_paged_txt = "";
        var eosa_option_margins_txt = "";

        var tmp;
        tmp = jQuery('#eosa_option_maso_col').val();
        if (tmp != "3") eosa_option_maso_col_txt = " maso_col='" + tmp + "'";

        tmp = jQuery('#eosa_option_maso_rand').val();
        if (tmp != "yes") eosa_option_maso_rand_txt = " maso_rand='" + tmp + "'";

        tmp = jQuery('#eosa_option_maso_paged').val();
        if (tmp != "yes") eosa_option_maso_paged_txt = " maso_paged='" + tmp + "'";

        tmp = jQuery('#eosa_option_margin').val();
        if ((tmp != "15") && (tmp.length > 0)) eosa_option_margins_txt = " margin='" + tmp + "'";

        FINAL_SHORTCODE = "[eventon_slider slider_type='" + eosa_option_sliderType + "' lan='" + eosa_option_language + "' orderby='" + eosa_option_orderby + "' map='" + eosa_option_showMap +
       "' details='" + eosa_option_showDetails + "' date_out='" + eosa_option_date_out + "' date_in='" + eosa_option_date_in + "' " + eosa_option_showevent_txt +
       eosa_option_daterange + shortcode_sliderId + eosa_option_open_event_type_txt + eosa_option_classicslider_type_txt + eosa_option_open_event_type_card_txt + shortcode_linkslider
       + eosa_option_style_txt + eosa_option_skin_txt + eosa_option_open_event_style_txt + eosa_option_animation_txt + eosa_option_card_opentype_txt + eosa_option_featured_txt + eosa_option_cover_txt +
       eosa_option_filters_txt + eosa_option_maso_col_txt + eosa_option_maso_rand_txt + eosa_option_maso_paged_txt + eosa_option_margins_txt + eosa_option_extrafields_txt + "]";
    }

    FINAL_SHORTCODE = FINAL_SHORTCODE.replace(/ +(?= )/g, ''); //Replace duplicated spaces with one space
    jQuery('#FINAL_SHORTCODE').html(FINAL_SHORTCODE);
    
}


jQuery(document).ready(function () {
    jQuery('.jtarget').change(function () {
        generate_eosa_shortcode();
    });
    jQuery('#eosa_option_open_event_type').change(function () {
        if (jQuery("#eosa_option_open_event_type").val() == "card") {
            jQuery("#eosa_card_direction").css("display", "inline-block");
            jQuery("#eosa_card_opentype").css("display", "inline-block");
        }
        else {
            jQuery("#eosa_card_direction").css("display", "none");
            jQuery("#eosa_card_opentype").css("display", "none");
        }
    });
    jQuery('#eosa_option_link_type').change(function () {
        if (jQuery("#eosa_option_link_type").val() == "custom_link") jQuery("#eosa_option_linkSlider_box").css("display", "block");
        else jQuery("#eosa_option_linkSlider_box").css("display", "none");
    });
});

function removeSelectedSliderType() {
    jQuery('#eosa_slidertype_slider').removeClass("selected");
    jQuery('#eosa_slidertype_carousel').removeClass("selected");
    jQuery('#eosa_slidertype_minicarousel').removeClass("selected");
    jQuery('#eosa_slidertype_masonry').removeClass("selected");
}
function setSliderType(val) {
    removeSelectedSliderType();
    eosa_option_sliderType = val;
    jQuery('#eosa_hidden_slidertype').val(val);
    jQuery('#eosa_slidertype_' + val).addClass("selected");
    
    if (val == "slider") {
        jQuery('#eosa_option_classicslider_type_box').css('display', 'block');
        jQuery('#eosa_option_animation_box').css('display', 'inline-block');
    } else {
        jQuery('#eosa_option_classicslider_type_box').css('display', 'none');
        jQuery('#eosa_option_animation_box').css('display', 'none');
    }

    if ((val == "carousel")||(val == "minicarousel")) {
        jQuery('#eosa_box_carousel_1').css('display', 'block');
        jQuery('#eosa_option_showMapDetails_box').css('display', 'none');
    } else {
            jQuery('#eosa_option_showMapDetails_box').css('display', 'block');
            jQuery('#eosa_box_carousel_1').css('display', 'none');
    }
    if ((val == "carousel") || (val == "slider")) {
        jQuery('#eosa_option_link_type_box').css('display', 'block');
    } else {
        jQuery('#eosa_option_linkSlider_box').css('display', 'none');
        jQuery('#eosa_option_link_type_box').css('display', 'none');
    }

    if (val == "minicarousel") {
        jQuery('#eosa_option_advsbox_minItems').css('display', 'none');
        jQuery('#eosa_option_advsbox_maxItems').css('display', 'none');
        jQuery('#eosa_option_showImage_box').css('display', 'block');
        jQuery('#eosa_option_mcrow_type_box').css('display', 'block');
        jQuery('#eosa_option_minicarousel_type_box').css('display', 'block');
        jQuery("#eosa_option_open_event_type option[value='card']").show();
    } else {
        jQuery('#eosa_option_advsbox_minItems').css('display', 'block');
        jQuery('#eosa_option_advsbox_maxItems').css('display', 'block');
        jQuery('#eosa_option_showImage_box').css('display', 'none');
        jQuery('#eosa_option_mcrow_type_box').css('display', 'none');
        jQuery('#eosa_option_minicarousel_type_box').css('display', 'none');
        jQuery("#eosa_option_open_event_type option[value='card']").hide();
    }
    if (val == "masonry") {
        jQuery('#eosa_option_advsbox_minItems').css('display', 'none');
        jQuery('#eosa_option_advsbox_maxItems').css('display', 'none');
        jQuery('#eosa_option_showMapDetails_box').css('display', 'none');
        jQuery('#eosa_option_showImage_box').css('display', 'none');
        jQuery('#eosa_option_mcrow_type_box').css('display', 'none');
        jQuery('#eosa_option_masonry_type_box').css('display', 'block');
    } else {
        jQuery('#eosa_option_masonry_type_box').css('display', 'none');
    }
    
    generate_eosa_shortcode();
}
function setSliderSettings() {
    var slider_type = "slider";
    var tempValue = "";

    if ((sliders_count > 0) && (isNewSlider == false)) {
        var slider_type = eosa_option_sliders[eosa_selectedslider]['eosa_option_sliderType'];
        setSliderType(slider_type);
        jQuery("#eosa_option_language").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_language']);
        jQuery("#eosa_option_orderby").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_orderby']);

        if (eosa_option_sliders[eosa_selectedslider]['eosa_option_showMap'] == 'yes') {
            jQuery("#eosa_option_showMap_yes").prop("checked", true)
        }
        if (eosa_option_sliders[eosa_selectedslider]['eosa_option_showDetails'] == 'yes') {
            jQuery("#eosa_option_showDetails_yes").prop("checked", true)
        }

        jQuery("#eosa_option_date_out").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_date_out']);
        jQuery("#eosa_option_date_in").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_date_in']);
        jQuery("#eosa_option_showevent").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_showevent']);
        jQuery("#eosa_option_slidername").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_slidername']);
        jQuery("#eosa_option_sliderid").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_sliderid']);
        jQuery("#eosa_option_open_event_type").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_open_event_type']);
        if (eosa_option_sliders[eosa_selectedslider]['eosa_option_open_event_type'] == "card") {
            jQuery("#eosa_card_direction").css("display", "inline-block");
            jQuery("#eosa_option_open_event_type_card").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_open_event_type_card']);
            jQuery("#eosa_card_opentype").css("display", "inline-block");
            jQuery("#eosa_option_card_opentype").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_card_opentype']);
        } else {
            jQuery("#eosa_card_direction").css("display", "none");
            jQuery("#eosa_card_opentype").css("display", "none");
        }
        jQuery("#eosa_option_style").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_style']);
        jQuery("#eosa_option_skin").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_skin']);

        tempValue = eosa_option_sliders[eosa_selectedslider]['eosa_option_open_event_style'];
        if (typeof (tempValue) !== "undefined" && tempValue !== null && tempValue.length > 0) jQuery("#eosa_option_open_event_style").val(tempValue);
        else jQuery("#eosa_option_open_event_style").val('a');
        tempValue = eosa_option_sliders[eosa_selectedslider]['eosa_option_cover'];
        if (typeof (tempValue) !== "undefined" && tempValue !== null && tempValue.length > 0) jQuery("#eosa_option_cover").val(tempValue);
        else jQuery("#eosa_option_cover").val("main");
        tempValue = eosa_option_sliders[eosa_selectedslider]['eosa_option_featured'];
        if (typeof (tempValue) !== "undefined" && tempValue !== null && tempValue.length > 0) jQuery("#eosa_option_featured").val(tempValue);
        else jQuery("#eosa_option_featured").val("all");
        tempValue = eosa_option_sliders[eosa_selectedslider]['eosa_option_extrafields'];
        if (typeof (tempValue) !== "undefined" && tempValue !== null && tempValue.length > 0) jQuery("#eosa_option_extrafields").val(tempValue);
        else jQuery("#eosa_option_extrafields").val("all");

        //date range
        jQuery("#eosa_option_date_range_txt_between1").val("");
        jQuery("#eosa_option_date_range_txt_between2").val("");
        jQuery("#eosa_option_date_range_txt_before").val("");
        jQuery("#eosa_option_date_range_txt_after").val("");
        var date_range = eosa_option_sliders[eosa_selectedslider]['eosa_option_daterange']
        if (date_range == "all") jQuery("#eosa_option_date_range_all").prop("checked", true)
        if (date_range == "past") jQuery("#eosa_option_date_range_past").prop("checked", true)
        if (date_range == "future") jQuery("#eosa_option_date_range_future").prop("checked", true)
        if (date_range == "today") jQuery("#eosa_option_date_range_today").prop("checked", true)
        if (date_range == "current_week") jQuery("#eosa_option_date_range_current_week").prop("checked", true)
        if (date_range == "current_month") jQuery("#eosa_option_date_range_current_month").prop("checked", true)
        if (date_range.indexOf("before") >= 0) {
            jQuery("#eosa_option_date_range_before").prop("checked", true)
            date_range = date_range.replace("before", "");
            jQuery("#eosa_option_date_range_txt_before").val(date_range);
        }
        if (date_range.indexOf("after") >= 0) {
            jQuery("#eosa_option_date_range_after").prop("checked", true)
            date_range = date_range.replace("after", "");
            jQuery("#eosa_option_date_range_txt_after").val(date_range);
        }
        if (date_range.indexOf("between") >= 0) {
            jQuery("#eosa_option_date_range_between").prop("checked", true)
            date_range = date_range.replace("between", "");
            jQuery("#eosa_option_date_range_txt_between1").val(date_range.split(":")[0]);
            jQuery("#eosa_option_date_range_txt_between2").val(date_range.split(":")[1]);
        }

        //SLIDER TYPE CLASSIC and CAROUSEL
        if ((slider_type == "slider") || (slider_type == "carousel")) {
 
            if (eosa_option_sliders[eosa_selectedslider]['eosa_option_linkSlider'] == "events_list") {
                jQuery("#eosa_option_link_type").val('events_list');
                jQuery('#eosa_option_linkSlider_box').css('display', 'none');
            } else {
                if (eosa_option_sliders[eosa_selectedslider]['eosa_option_linkSlider'] !== "none") {
                    jQuery("#eosa_option_link_type").val('custom_link');
                    jQuery("#eosa_option_linkSlider").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_linkSlider']);
                } else {
                    jQuery("#eosa_option_link_type").val('none');
                    jQuery('#eosa_option_linkSlider_box').css('display', 'none');
                }
            } 
        }

        //SLIDER TYPE CLASSIC SLIDER
        if (slider_type == "slider") {
            jQuery("#eosa_option_classicslider_type").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_classicslider_type']);
            jQuery("#eosa_option_animation").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_animation']);
        }

        //SLIDER TYPE CAROUSEL
        if (slider_type == "carousel") {
            jQuery("#eosa_option_advs_minItems").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_advs_minItems']);
            jQuery("#eosa_option_advs_maxItems").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_advs_maxItems']);
            jQuery("#eosa_option_advs_move").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_advs_move']);
            jQuery("#eosa_option_advs_itemWidth").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_advs_itemWidth']);
            jQuery("#eosa_option_advs_itemMargin").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_advs_itemMargin']);
            jQuery('#eosa_box_carousel_1').css('display', 'block');
            jQuery('#eosa_option_showMapDetails_box').css('display', 'none');
        } else jQuery('#eosa_box_carousel_1').css('display', 'none');

        //SLIDER TYPE MINI CAROUSEL
        if (slider_type == "minicarousel") {
            jQuery("#eosa_option_advs_move").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_advs_move']);
            jQuery("#eosa_option_advs_itemWidth").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_advs_itemWidth']);
            jQuery("#eosa_option_advs_itemMargin").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_advs_itemMargin']);
            jQuery("#eosa_option_advs_itemMargin").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_advs_itemMargin']);
            jQuery("#eosa_option_minicarousel_type").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_minicarousel_type']);
            jQuery("#eosa_option_mcrow_type").val(eosa_option_sliders[eosa_selectedslider]['eosa_option_mcrow_type']);
            jQuery('#eosa_box_carousel_1').css('display', 'block');
            if (eosa_option_sliders[eosa_selectedslider]['eosa_option_showImage'] == 'yes') {
                jQuery("#eosa_option_showImage_yes").prop("checked", true)
            }
        } else jQuery('#eosa_box_carousel_1').css('display', 'none');

        //SLIDER TYPE MASONRY
        if (slider_type == "masonry") {
            tempValue = eosa_option_sliders[eosa_selectedslider]['eosa_option_maso_col'];
            if (typeof (tempValue) !== "undefined" && tempValue !== null && tempValue.length > 0) jQuery("#eosa_option_maso_col").val(tempValue);
            tempValue = eosa_option_sliders[eosa_selectedslider]['eosa_option_maso_rand'];
            if (typeof (tempValue) !== "undefined" && tempValue !== null && tempValue.length > 0) jQuery("#eosa_option_maso_rand").val(tempValue);
            tempValue = eosa_option_sliders[eosa_selectedslider]['eosa_option_maso_paged'];
            if (typeof (tempValue) !== "undefined" && tempValue !== null && tempValue.length > 0) jQuery("#eosa_option_maso_paged").val(tempValue);
            tempValue = eosa_option_sliders[eosa_selectedslider]['eosa_option_filters'];
            if (typeof (tempValue) !== "undefined" && tempValue !== null && tempValue.length > 0) jQuery("#eosa_option_filters").val(tempValue);
            tempValue = eosa_option_sliders[eosa_selectedslider]['eosa_option_margin'];
            if (typeof (tempValue) !== "undefined" && tempValue !== null && tempValue.length > 0) jQuery("#eosa_option_margin").val(tempValue);
            
        }

        var shortcode_string = eosa_option_sliders[eosa_selectedslider]['FINAL_SHORTCODE']
        jQuery("#FINAL_SHORTCODE").html(shortcode_string);

        if (sliders_count == 1) jQuery('#submit_delite').css('display', 'none');

    } else {
        isNewSlider = true;
        setSliderDefaultSettings();
    }
}
function setSliderDefaultSettings() {
    setSliderType("slider");
    jQuery('#eosa_slidertype_1').addClass("selected");
    jQuery("#eosa_option_language").val('L1');
    jQuery("#eosa_option_linkSlider").val('');
    jQuery("#eosa_option_orderby").val('ASC');
    jQuery("#eosa_option_showMap_no").prop("checked", true)
    jQuery("#eosa_option_showDetails_no").prop("checked", true)
    jQuery("#eosa_option_date_out").val("1");
    jQuery("#eosa_option_date_in").val("1");
    jQuery("#eosa_option_showevent").val("");
    jQuery("#FINAL_SHORTCODE").html("[eventon_slider slider_type='slider' lan='L1' orderby='ASC' map='no' details='no' date_out='1' date_in='1' id='" + jQuery('#eosa_option_sliderid').val() + "']");
    jQuery('#submit_delite').css('display', 'none');
    jQuery("#eosa_option_date_range_txt_between1").val("");
    jQuery("#eosa_option_date_range_txt_between2").val("");
    jQuery("#eosa_option_date_range_txt_before").val("");
    jQuery("#eosa_option_date_range_txt_after").val("");
    jQuery("#eosa_option_date_range_all").prop("checked", true)
    jQuery('#eosa_option_style').val("a");
    jQuery('#eosa_option_open_event_style_txt').val("a");
    jQuery('#eosa_option_skin').val("light");
    jQuery('#eosa_option_animation').val('slide');
    jQuery('#eosa_option_advs_minItems').val('auto');
    jQuery('#eosa_option_advs_maxItems').val('auto');
    jQuery("#eosa_option_open_event_type").val('lightbox');
    jQuery("#eosa_card_direction").css('display', 'none');
    jQuery("#eosa_card_opentype").css('display', 'none');
    jQuery(".box_advanced_options").css('display', 'none');
    jQuery('#eosa_option_cover').val('main');
    jQuery('#eosa_option_featured').val('all');
    jQuery("#eosa_option_masonry_type_box").css('display', 'none');
    jQuery('#eosa_option_maso_col').val('3');
    jQuery('#eosa_option_maso_rand').val('yes');
    jQuery('#eosa_option_maso_paged').val('yes');
    jQuery('#eosa_option_filters').val('all');
    jQuery('#eosa_option_margin').val('15');
    
}

function addNewSlider() {
    if (isNewSlider == false) {
        jQuery('#sliders_list_' + eosa_selectedslider).removeClass('focused');
        eosa_selectedslider = sliders_count;
        sliders_count = sliders_count + 1;
        jQuery('#eosa_hidden_indexarray').val(eosa_selectedslider);
        jQuery("#sliders_list").html(jQuery("#sliders_list").html() + '<li><a class="button_menu focused" id="#sliders_list_' + eosa_selectedslider + '" onclick="selectSlider(' + eosa_selectedslider + ')">Slider ' + sliders_count + '</a></li>');
        jQuery('#submit_delite').css('display', 'none');
        jQuery('#eosa_option_slidername').val('Slider ' + sliders_count);
        jQuery('#eosa_option_sliderid').val('slider_' + sliders_count);
        setSliderDefaultSettings();
        isNewSlider = true;
    }  
}

function selectSlider(index) {
    if ((isNewSlider) && (eosa_selectedslider != index)) {
        jQuery('#sliders_list li:last').remove();
        sliders_count = sliders_count - 1;
        isNewSlider = false;
    } else {
        jQuery('#sliders_list_' + eosa_selectedslider).removeClass('focused');
    }

    eosa_selectedslider = index;
    jQuery('#eosa_hidden_indexarray').val(index);
    setSliderSettings();
    jQuery('#sliders_list_' + eosa_selectedslider).addClass('focused');
    jQuery(".box_advanced_options").css('display', 'none');
}

function setSelectedLanguage() {
    var L123 = jQuery("#eosa_option_language_setting").val();
    if ((typeof(eosa_language_array[L123]) !== "undefined" && eosa_language_array[L123] !== null )) {
        jQuery('#eosa_input_lan_from').val(eosa_language_array[L123]['from']);
        jQuery('#eosa_input_lan_to').val(eosa_language_array[L123]['to']);
        jQuery('#eosa_input_lan_from2').val(eosa_language_array[L123]['from_2']);
        jQuery('#eosa_input_lan_to2').val(eosa_language_array[L123]['to_2']);
        jQuery('#eosa_input_lan_time').val(eosa_language_array[L123]['time']);
        jQuery('#eosa_input_lan_openevent').val(eosa_language_array[L123]['open_event']);
        jQuery('#eosa_input_lan_showmap').val(eosa_language_array[L123]['show_map']);
        jQuery('#eosa_input_lan_start').val(eosa_language_array[L123]['start']);
        jQuery('#eosa_input_lan_finish').val(eosa_language_array[L123]['finish']);
    } else {
        jQuery('#eosa_input_lan_from').val("");
        jQuery('#eosa_input_lan_to').val("");
        jQuery('#eosa_input_lan_from2').val("");
        jQuery('#eosa_input_lan_to2').val("");
        jQuery('#eosa_input_lan_time').val("");
        jQuery('#eosa_input_lan_openevent').val("");
        jQuery('#eosa_input_lan_showmap').val("");
        jQuery('#eosa_input_lan_start').val("");
        jQuery('#eosa_input_lan_finish').val("");
    }
}


function convertToSlug(Text) {
    var txt = Text
        .toLowerCase()
        .replace(/ /g, '_')
        .replace(/[^\w-]+/g, '')
    ;
    return txt.replace(/-/g, "_");
}


//######## PAGE LOAD ########
jQuery(document).ready(function () {

    //TAB : Sliders
    if ((getURLParameter("tab") == "") && (getURLParameter("tab").length == 0)) {
        sliders_count = eosa_option_sliders.length;
        eosa_selectedslider = jQuery('#eosa_hidden_indexarray').val();
        setSliderSettings();

        //Loading slider list
        var eosa_html = '';
        if (sliders_count > 0) {
            for (i = 0; i < sliders_count; i++) {
                eosa_html = eosa_html + '<li><a class="button_menu" id="sliders_list_' + i + '" onclick="selectSlider(' + i + ')">' + eosa_option_sliders[i]['eosa_option_slidername'] + '</a></li>\n';
            }
            jQuery("#sliders_list").html(eosa_html);
        }
        //Set selected slider
        jQuery('#sliders_list_' + eosa_selectedslider).addClass('focused');
    }

    //TAB : Setting
    if ((getURLParameter("tab") == "setting")) {
        jQuery('.nav-tab_eosa ').removeClass('nav-tab-active_eosa');
        jQuery('#menu_tab2 ').addClass('nav-tab-active_eosa');

        setSelectedLanguage();   //load language
    }
    if ((getURLParameter("tab") == "support")) {
        jQuery('.nav-tab_eosa ').removeClass('nav-tab-active_eosa');
        jQuery('#menu_tab3 ').addClass('nav-tab-active_eosa');
    }
    showErrorBox();
});

function showErrorBox() {
    if (jQuery('#error_box_container').html().length > 0) {
        jQuery('#error_box').css("top", "15px");
        jQuery('#error_box').animate({
            'top': '-=15px',
            'opacity': '1',
        }, 500);
        setTimeout(function () {
            jQuery('#error_box').animate({
                'top': '+=15px',
                'opacity': '0',
            }, 500);
        }, 7000);
    }
}
    

function getURLParameter(name) {
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [, ""])[1].replace(/\+/g, '%20')) || ""
}