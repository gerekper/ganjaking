<?php
/**
 * Plugin Name: EventON Slider Addon
 * Plugin URI: http://www.eventonslider.com/
 * Description: EventON Slider addon for show events in high quality sliders and carousels
 * Version: 2.9.0
 * Author: Federico Schiocchet - Pixor
 * Author URI: http://www.pixor.it/
 */

/* GLOBAL VARS */
global $lan_arr_eosa;
global $lan_arr;
$class_;
static $add_script = false;
static $add_script_isotope = false;
global $plugin_url;
global $location_tax_meta;
$plugin_url = plugins_url();

/* FUNCTIONS  */
include("includes/functions.php");

function set_ajax_url() {
    echo '<script type="text/javascript">var ajaxURL = "'. admin_url('admin-ajax.php'). '";</script>';
}
add_action('wp_head', 'set_ajax_url');
function ajax_getEvents()  {
    global $post;

    $arrSc = $_POST['arrSc'];
    $lan_arr = $_POST['lan_arr'];
    $lan_arr_eosa = $_POST['lan_arr_eosa'];
    $paged = $_POST['paged'];
    $date_filter = "";
    if (isset($_POST['date_filter'])) {
        $date_filter = eosa_date_range($_POST['date_filter']);
    } else {
        if (isset($arrSc[21])) $date_filter = $arrSc[21];
    }
    $arrEvents = array();
    $optionArr = array("slider_type" => $arrSc[0],"c_open_type" => $arrSc[13],"open_type" => $arrSc[3], "cover" => $arrSc[17], "ef" => $arrSc[5], "lan_arr" => $lan_arr, "lan_arr_eosa" =>  $lan_arr_eosa);

    $args = array(
        'post_type' => 'ajde_events',
        'showposts' => -1,
        'order' => $arrSc[11],
        'meta_key' => 'evcal_srow',
        'orderby' => 'meta_value',
        'event_type' => $arrSc[6],
        'event_type_2' => $arrSc[7],
        'event_type_3' => $arrSc[8],
        'event_type_4' => $arrSc[9],
        'event_type_5' => $arrSc[10],
        '_featured' => 'yes',
        'meta_query' => $date_filter
     );
    query_posts($args);

    while(have_posts()){
        the_post();
        $event = readEventData($arrSc[14], $arrSc[15], $arrSc[16], $optionArr);
        $in = inDateRange($event[15],$arrSc[18]);

        $metaquery = $arrSc[18];
        if ($in) if ($arrSc[0] == "masonry") array_push($arrEvents, $event);

        /* MIKE */
        $arrRepeats = createEventRepeats($arrSc[0],$arrSc[14],$arrSc[16],$arrSc[18],$event,$optionArr);
        $arrEvents = array_merge($arrEvents,$arrRepeats);

        // MIKE: SORT ARRAY based on start_date
        if ($arrSc[11] == "asc")
            usort($arrEvents, 'sort_by_date_in_asc');
        else
            usort($arrEvents, 'sort_by_date_in_desc');

		// MIKE: items per page
		$arrSliced = array_slice($arrEvents,($paged-1)*$arrSc[12],$arrSc[12]);
    }

    die(json_encode_arr($arrSliced));
}
add_action( 'wp_ajax_ajax_getEvents', 'ajax_getEvents' );
add_action( 'wp_ajax_nopriv_ajax_getEvents', 'ajax_getEvents' );
add_action('init', 'register_my_script');
add_action('wp_footer', 'print_my_script');

function register_my_script() {
    global $plugin_url;
    $eosa_api_key = get_option('evcal_options_evcal_1');
    if ($eosa_api_key == "") $eosa_api_key = 'AIzaSyDtZH5aI4Jk_-lKyxF4hUz2E57sBsMvoYw';
    else $eosa_api_key = $eosa_api_key['evo_gmap_api_key'];
	wp_register_style('eosa-main-style', $plugin_url.'/eventon-slider-addon/assets/css/style.css', null, 1.1 );
    wp_register_script('eosa-main-script', $plugin_url . '/eventon-slider-addon/assets/js/jquery.eventon-addon.js', array(), null, true);
    wp_register_script( 'ajax_custom_script', $plugin_url.'/eventon-slider-addon/assets/js/ajax.js');
    wp_register_script('gmaps', 'https://maps.googleapis.com/maps/api/js?sensor=false&key=' . $eosa_api_key, array(), null, true);
    wp_register_style('open-sans-font','http://fonts.googleapis.com/css?family=Oswald:400,300|Open+Sans:600,400', array(), null, true);
    wp_register_script('isotope', $plugin_url . '/eventon-slider-addon/assets/js/jquery.eventon-isotope.js', array(), null);
    wp_register_style('isotope-css', $plugin_url.'/eventon-slider-addon/assets/css/style-isotope.css', array(), null);
}
function print_my_script() {
	global $add_script;
    global $add_script_isotope;
    global $plugin_url;

	if (!$add_script)
		return;
    wp_print_scripts('gmaps');
    wp_print_scripts('eosa-main-script');
    wp_print_styles('eosa-main-style');
    wp_print_styles('open-sans-font');

    if (!$add_script_isotope)
		return;
    wp_print_scripts('isotope');
    wp_print_styles('isotope-css');
}

//MENU
function set_admin_menu() {
    add_submenu_page("eventon", "Slider addon", "Slider addon", "administrator", "event-slider-addon","set_page_menu_link");
}
function set_page_menu_link(){
    include('event-slider-addon.php');
}
add_action("admin_menu", "set_admin_menu");

function eosa_original_event() {
    global $plugin_url;
    echo "<script>
             jQuery('link[href=\'link/to/css\']').remove(); jQuery(document).ready(function() {
                  eosa_original_event_init('" .$plugin_url."');
                  setInterval(function () {
                      jQuery('.evcal_btn.checkout,.evcal_btn.view_cart').attr('target','_parent');
                  }, 500);
             });
         </script>";
    wp_register_script('eosa-main-script2', $plugin_url . '/eventon-slider-addon/assets/js/jquery.eventon-addon.js', array(), null, false);
    wp_enqueue_script('eosa-main-script2');
}
add_action('eventon_before_main_content','eosa_original_event');

function eosa_date_range($date_range) {
    $date_range_arr = "";
    if (($date_range == "future")||($date_range == "past")) {
        $cp = ">=";
        $ser = 'evcal_srow';
        if($date_range == "past") $ser = "evcal_erow";
        if($date_range == "past") $cp = "<";

        $date_range_arr = array(
            'key' => $ser,
            'value' => current_time('timestamp'),
            'compare' => $cp,
            );
    }
    if (($date_range == "today")||($date_range == "current_week")||($date_range == "current_month")) {
        $value_a = ""; $value_b = "";
        if($date_range == "today") {
            $value_a = date('m/d/Y', time()) . " 11:59:00 pm";
            $value_b = date('m/d/Y', time()) . " 12:00:00 am";
        }
        if($date_range == "current_week") {
            if(date('D')!='Sat') $value_a = date('m/d/Y',strtotime('next Saturday')) . " 11:59:00 pm";
            else $value_a = date('m/d/Y',time()) . " 11:59:00 pm";
            if(date('D')!='Mon') $value_b = date('m/d/Y',strtotime('last Monday')) . " 12:00:00 am";
            else $value_b = date('m/d/Y',time()) . " 12:00:00 am";
        }
        if($date_range == "current_month") {
            $value_a = date('m') . "/" . cal_days_in_month(CAL_GREGORIAN, (int)date('m'), (int)date('Y')) . "/" . date('Y') . " 11:59:00 pm";
            $value_b = date('m') . "/01/" . date('Y') . " 12:00:00 am";
        }

        $date_range_arr = array(
            'relation' => 'AND',
                array(
                    'key' => 'evcal_srow',
                    'value' => strtotime($value_a),
                    'compare' => "<="
                ),
                 array(
                    'key' => 'evcal_erow',
                    'value' => strtotime($value_b),
                    'compare' => ">="
                )
            );
    }
    //before30-12-2014 after30-12-2014
    if ((strpos($date_range,'before') !== false)||(strpos($date_range,'after') !== false)) {
        $cp = "";
        $date_txt = "";
        if(strpos($date_range,'before') !== false) { $cp = "<";  $date_txt = str_replace("before", "", $date_range); }
        if(strpos($date_range,'after') !== false) { $cp = ">=";  $date_txt = str_replace("after", "", $date_range); }

        $unix = strtotime($date_txt);

        $date_range_arr = array(
            'key' => 'evcal_erow',
            'value' => $unix,
            'compare' => $cp,
            );
    }

    //between30-12-2014:30-12-2015
    if (strpos($date_range,'between') !== false) {
        $date_txt_arr = explode(":", str_replace("between", "", $date_range));
        $unix_s = strtotime($date_txt_arr[0]);
        $unix_e = strtotime($date_txt_arr[1]);
        $date_range_arr = array(
            'key' => 'evcal_erow',
            'value' => array($unix_s, $unix_e),
            'compare' => 'BETWEEN',
            );
    }
    return $date_range_arr;
}

//SHORTCODE
function eventon_slider_addon($atts)  {
    global $add_script;
    global $add_script_isotope;
    global $lan_arr_eosa;
    global $lan_arr;
    global $location_tax_meta;
    $location_tax_meta = get_option( "evo_tax_meta");

    extract( shortcode_atts( array (
            'slider_type' => 'slider',
            's_type' => 'big',
            'link' => 'none',
            'map' => 'yes',
            'details' => 'yes',
            'orderby' => 'asc',
            'lan' => 'L0',
            'date_out' => '1',
            'date_in' => '1',
            'event_type' => '',
            'event_type_2' => '',
            'event_type_3' => '',
            'event_type_4' => '',
            'event_type_5' => '',
            'showevent' => -1,
            'date_range' => 'all',
            'car_minitems' => 'auto',
            'car_maxitems' => 'auto',
            'car_move' => '1',
            'car_itemwidth' => '-1',
            'car_itemmargin' => '-1',
            'margin' => '-1',
            'mcar_image' => 'no',
            'mcar_row' => 'location',
            'open_type' => 'lightbox',
            'c_dir' => 'auto',
            'style' => 'a',
            'style_2' => 'a',
            'skin' => 'light',
            'animation' => 'slide',
            'c_open_type' => 'lightbox',
            'featured' => '-1',
            'cover' => 'main',
            'ef' => 'in',
            'maso_col' => '3',
            'maso_rand' => 'yes',
            'maso_paged' => 'yes',
            'filters' => 'all',
            'calendar' => 'yes',
            'api_key' => 'AIzaSyDtZH5aI4Jk_-lKyxF4hUz2E57sBsMvoYw',
            'id' => 'eosa_id'
        ), $atts ) );
    $add_script = true;
    if ($slider_type == "masonry") $add_script_isotope = true;
    $orderby = strtolower($orderby);

    //Set correct default values
    if ($slider_type == 'minicarousel') {
        if ($s_type == 'big') $s_type = "mini";
    }
    if (($margin == -1) && ($slider_type == 'masonry')) {
        $margin = 15;
    }
    if ($car_itemmargin == -1) {
        if ($margin == -1) {
            if ($slider_type == 'minicarousel') $car_itemmargin = 1;
            if ($slider_type == 'carousel') $car_itemmargin = 15;
        } else   $car_itemmargin = $margin;
    }
    if ($car_itemwidth == -1) {
        if (($slider_type == 'minicarousel') && ($s_type == 'mini')) $car_itemwidth = 223;
        if (($slider_type == 'minicarousel') && ($s_type == 'micro')) $car_itemwidth = 42;
        if ($slider_type == 'carousel') $car_itemwidth = 223;
    }
    if ($slider_type == 'masonry' && $showevent == -1)  $showevent = 10;

    $skin = "skin-" . $skin;

    //slider class
    if ($slider_type == 'slider') {
        global $class_;
        if ($s_type == 'big') $class_ = "eo_big";
        if ($s_type == 'mini') $class_ = "eo_small";
    }

    //LANGUAGE
    $tmp = get_option('evcal_options_evcal_2');
    if (($tmp == false)||($lan == 'L0')) {
        include("includes/settings_language.php");
        $lan_arr = $eosa_default_language_array;
    } else $lan_arr = $tmp[$lan];

    $tmp = get_option('eosa_option_language');
    if (($tmp == false)||($lan == 'L0')) {
        include("includes/settings_language.php");
        $lan_arr_eosa = $eosa_default_language_array_2;
    } elseif (isset($tmp[$lan])) $lan_arr_eosa = $tmp[$lan];

    //Date range
    $date_range_arr = eosa_date_range($date_range);

    $meta_query_arr = array();
    $featured_query = array();

    //Featured events
    if (($featured == "yes")||($featured == "no")) {
        $featured_query = array(
              'key' => '_featured',
              'value' => $featured,
              'compare' => '=',
              );
        //array_push($meta_query_arr, $featured_query);
    }

    array_push($meta_query_arr, $date_range_arr);
    $args = array();
    if ($slider_type != 'masonry') {
        $args= array(
            'post_type' => 'ajde_events' ,
            'paged'=> 1,
            'posts_per_page' => -1 ,
            'order' => $orderby ,
            'meta_key' => 'evcal_srow',
            'orderby' => 'meta_value',
            'event_type' => $event_type,
            'event_type_2' => $event_type_2,
            'event_type_3' => $event_type_3,
            'event_type_4' => $event_type_4,
            'event_type_5' => $event_type_5,
            '_featured' => 'yes',
            'meta_query' => $featured_query
        );
        query_posts($args);
    }


    //Save shortcodes in js array : ArrSC
    $shortcode_array = array();
    array_push($shortcode_array,$slider_type);     //0
    array_push($shortcode_array,$style);           //1
    array_push($shortcode_array,$id);              //2
    array_push($shortcode_array,$open_type);       //3
    array_push($shortcode_array,$style_2);         //4
    array_push($shortcode_array,$ef);              //5
    array_push($shortcode_array,$event_type);      //6
    array_push($shortcode_array,$event_type_2);    //7
    array_push($shortcode_array,$event_type_3);    //8
    array_push($shortcode_array,$event_type_4);    //9
    array_push($shortcode_array,$event_type_5);    //10
    array_push($shortcode_array,$orderby);         //11
    array_push($shortcode_array,$showevent);       //12
    array_push($shortcode_array,$c_open_type);     //13
    array_push($shortcode_array,$date_out);        //14
    array_push($shortcode_array,$date_in);         //15
    array_push($shortcode_array,$lan);             //16
    array_push($shortcode_array,$cover);           //17
    array_push($shortcode_array,$meta_query_arr);  //18
    array_push($shortcode_array,$maso_col ."|". $maso_rand);  //19
    array_push($shortcode_array,$margin);  //20
    array_push($shortcode_array,$featured_query);  //21

    // MIKE
    //echo "METAQUERY: " . json_encode($meta_query_arr);

    if ($slider_type != 'masonry') $eo_index = 0; else $eo_index = 1;
    $global_array = array();
    ob_start();
?>

<div id="eosa_loader_<?php echo $id; ?>" class="eosa_loader">
    <img src="<?php echo plugins_url("assets/images/loader.gif",__FILE__ ) ?>" />
</div>

<?php if ($slider_type == 'slider') { ?>
<!--############## SLIDER TYPE: 1 - SLIDER ###################-->
<div class="main_event" id="<?php echo $id; ?>" style="visibility: hidden;">
    <?php 
          if($style == 'a') include("includes/type-classicslider.php"); 
          if($style == 'b') include("includes/type-classicslider-b.php");
    ?>
</div>

<?php }
      if ($slider_type == 'carousel') { ?>
<!--############## SLIDER TYPE: 2 - CAROUSEL ###################-->
<div class="main_event main_event_s2" id="<?php echo $id; ?>" style="visibility: hidden;">
    <?php
          if($style == 'a') include("includes/type-carousel.php"); 
          if($style == 'b') include("includes/type-carousel-b.php");
    ?>
</div>

<?php } 
      if (($slider_type == 'minicarousel') && ($s_type == 'mini')) { ?>
<!--############## SLIDER TYPE: 3 - MINI CAROUSEL ###################-->
<div class="main_event main_event_s3 <?php if($style == 'b') echo "main_event_s4 micro_padd"; ?>" id="<?php echo $id; ?>" style="visibility: hidden;">
    <?php 
          if($style == 'a') include("includes/type-minicarousel.php"); 
          if($style == 'b') include("includes/type-minicarousel-b.php");
    ?>
</div>

<?php } 

      if (($slider_type == 'minicarousel') && ($s_type == 'micro')) { ?>
<!--############## SLIDER TYPE: 4 - MINI CAROUSEL 2 ###################-->
<div class="main_event main_event_s4 micro_padd" id="<?php echo $id; ?>" style="visibility: hidden;">
    <?php 
          if($style == 'a') include("includes/type-minicarousel-2.php"); 
          if($style == 'b') include("includes/type-minicarousel-2-b.php");
    ?>
</div>
<?php }  
      if ($slider_type == 'masonry') { ?>
<!--############## SLIDER TYPE: 5 - MASONRY ###################-->
<div class="main_event main_event_masonry <?php if($style == 'b') echo " style_b"; ?>" id="<?php echo $id; ?>" style="visibility: hidden;">
    <?php 
          include("includes/type-masonry.php"); 
    ?>
</div>
<!--############## GLOBAL BOXES ###################-->
<?php } ?>
<?php 
    if($style_2 == "a") include("includes/box-full-event.php");
    if($style_2 == "b") include("includes/box-full-event-b.php");
    if ($open_type == "card") {
        if($style_2 == "a") include("includes/box-card.php");
        if($style_2 == "b") include("includes/box-card-b.php");
    }
?>
<!--### MAP BOX-->
<div id="<?php echo $id; ?>-map-canvas-box" class="map-canvas-box <?php echo $skin ?>" style="display:none">
    <a class="eoas_evopopclose" onclick="jQuery('#<?php echo $id; ?>-map-canvas-box').trigger('close');">X</a>
    <div id="<?php echo $id; ?>-map-canvas" class="map-canvas"></div>
    <div class="eoas-map-bar">
        <i class="fa fa-map-marker eo_i" style="font-size: 15px;"></i><span class="so_title" id="<?php echo $id; ?>-eoas-map-bar-text"></span>
    </div>
</div>
<script>
    <?php 
    echo "var " .$id. "_eo_js_array_sc = ". json_encode_arr($shortcode_array) . ";\n"; 
    echo "var eo_lan_arr_eosa = ". json_encode_arr($lan_arr_eosa) . ";\n"; 
    echo "var eo_lan_arr = ". json_encode_arr($lan_arr) . ";\n"; 
    echo "var siteURL = '" .get_site_url(). "';\n";
    global $plugin_url;
    echo "var eo_pluginURL = '" . $plugin_url . "';\n";
    ?>
</script>
<?php if ($eo_index == 0)
          echo '<style type="text/css">' . $id . '#eosa_loader_' . $id .' ,.button_showall { display: none !important; } </style><div class="eosa_noevents">No events available.</div>';
      return ob_get_clean(); 
} 
add_shortcode('eventon_slider', 'eventon_slider_addon'); ?>
