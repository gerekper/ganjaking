<?php 
wp_enqueue_style( 'style-name2', plugins_url().'/eventon-slider-addon/assets/css/style_admin.css' ); 
wp_enqueue_script( 'script-admin-eventoslieraddon', plugins_url() . '/eventon-slider-addon/assets/js/jquery.eventon-addon-admin.js', array(), '1.0.0', true);     

//GLOBAL READ SECTION
$selected_tab = "";
if(isset($_GET['tab'])) $selected_tab = htmlspecialchars($_GET["tab"]);
$eosa_global_array = "";
$eosa_language_array = "";
$eosa_index_language = "L1";
$eosa_index_array = 0;
$error_box = '';
$id_slider = '';

if(($selected_tab == "") || (strlen($selected_tab) == 0)) {
    $eosa_global_array = get_option('eosa_option_sliders'); 
    $eosa_language_array = get_option('eosa_option_language'); 
    if(is_array($eosa_global_array) == false) $eosa_global_array = array();
    if (isset($_POST['eosa_hidden_indexarray'])) $eosa_index_array = $_POST['eosa_hidden_indexarray'];
}

if($selected_tab == "setting") { 
    $eosa_language_array = get_option('eosa_option_language'); 
}

//SAVE SECTION
if (isset($_POST['submit_save'])) { 
    
    $slider_type = $_POST['eosa_hidden_slidertype'];
    $id_slider = $_POST['eosa_option_sliderid'];
    $isError = false;
    
    if(isSliderIDunique($id_slider, $eosa_global_array, $eosa_index_array) == false) {
        $isError = true;
        $error_box ="<div id='error_box'>Error: slider ID is not unique, please change slider name</div>";
    }
    $date_range = getDateRange();
    if($date_range=="error") {
        $isError = true;
        $error_box ="<div id='error_box'>Error: you entered a wrong date range, please check that everything is ok</div>";
    }
    $linkSlider = "none";
    if (($slider_type=="slider") || ($slider_type=="carousel")) {
        $slider_link_type = $_POST['eosa_option_link_type'];
        if ($slider_link_type == "events_list") {
            $linkSlider = "events_list";
        } else {
            if ($slider_link_type !== "none") {
                $linkSlider = $_POST['eosa_option_linkSlider'];
            }
        }
    }
    
    $temp_arr;
    $global_tmp_arr;
    if($isError==false) {
        
        //Global values
        $global_tmp_arr = array("eosa_option_sliderType" => $slider_type, 
                "eosa_option_language" => $_POST['eosa_option_language'], 
                "eosa_option_open_event_type" => $_POST['eosa_option_open_event_type'], 
                "eosa_option_open_event_type_card" => $_POST['eosa_option_open_event_type_card'],
                "eosa_option_card_opentype" => $_POST['eosa_option_card_opentype'],
                "eosa_option_orderby" => $_POST['eosa_option_orderby'],
                "eosa_option_date_out" => $_POST['eosa_option_date_out'],
                "eosa_option_date_in" => $_POST['eosa_option_date_in'],
                "eosa_option_showevent" => $_POST['eosa_option_showevent'],
                "eosa_option_slidername" => $_POST['eosa_option_slidername'],
                "eosa_option_daterange" => $date_range,
                "eosa_option_sliderid" => $id_slider,
                "eosa_option_style" => $_POST['eosa_option_style'],
                "eosa_option_open_event_style" => $_POST['eosa_option_open_event_style'],
                "eosa_option_skin" => $_POST['eosa_option_skin'],  
                "eosa_option_cover" => $_POST['eosa_option_cover'], 
                "eosa_option_featured" => $_POST['eosa_option_featured'], 
                "eosa_option_extrafields" => $_POST['eosa_option_extrafields'],
                "FINAL_SHORTCODE" => removeslashes($_POST['FINAL_SHORTCODE']));

        if ($slider_type=="slider") {
            $temp_arr = array( "eosa_option_sliderType" => $slider_type, 
                "eosa_option_classicslider_type" => $_POST['eosa_option_classicslider_type'],
                "eosa_option_linkSlider" => $linkSlider,
                "eosa_option_showMap" => $_POST['eosa_option_showMap'],
                "eosa_option_showDetails" => $_POST['eosa_option_showDetails'],
                "eosa_option_animation" => $_POST['eosa_option_animation']);
        }
        if ($slider_type=="carousel") {
            $temp_arr = array( "eosa_option_sliderType" => $slider_type, 
                "eosa_option_linkSlider" => $linkSlider,
                "eosa_option_advs_minItems" => $_POST['eosa_option_advs_minItems'],
                "eosa_option_advs_maxItems" => $_POST['eosa_option_advs_maxItems'],
                "eosa_option_advs_move" => $_POST['eosa_option_advs_move'],
                "eosa_option_advs_itemWidth" => $_POST['eosa_option_advs_itemWidth'],
                "eosa_option_advs_itemMargin" => $_POST['eosa_option_advs_itemMargin']);
        }
        if ($slider_type=="minicarousel") {
            $temp_arr = array( "eosa_option_sliderType" => $slider_type, 
                "eosa_option_mcrow_type" => $_POST['eosa_option_mcrow_type'], 
                "eosa_option_minicarousel_type" => $_POST['eosa_option_minicarousel_type'], 
                "eosa_option_advs_move" => $_POST['eosa_option_advs_move'],
                "eosa_option_advs_itemWidth" => $_POST['eosa_option_advs_itemWidth'],
                "eosa_option_advs_itemMargin" => $_POST['eosa_option_advs_itemMargin'],
                "eosa_option_showImage" => $_POST['eosa_option_showImage']);
        }
        if ($slider_type=="masonry") {
            $temp_arr = array( "eosa_option_sliderType" => $slider_type, 
                "eosa_option_maso_col" => $_POST['eosa_option_maso_col'], 
                "eosa_option_maso_rand" => $_POST['eosa_option_maso_rand'], 
                "eosa_option_maso_paged" => $_POST['eosa_option_maso_paged'],
                "eosa_option_filters" => $_POST['eosa_option_filters'],
                "eosa_option_margin" => $_POST['eosa_option_margin']);
            
        }
        $temp_arr = array_merge($temp_arr, $global_tmp_arr);
        if ($eosa_index_array < count($eosa_global_array)) $eosa_global_array[$eosa_index_array] = $temp_arr; //update slider
        else array_push($eosa_global_array, $temp_arr); //add new slider
        
        if (update_option( 'eosa_option_sliders', $eosa_global_array )==true){
            $error_box = "<div id='error_box'>Setting saved</div>";
        } 
    } 
}



//DELITE SECTION
if (isset($_POST['submit_delite'])) { 
    if (count($eosa_global_array) > 1) {
        $eosa_index_array = $_POST['eosa_hidden_indexarray'];
        unset($eosa_global_array[$eosa_index_array]);
        $eosa_global_array = array_values($eosa_global_array);
        update_option('eosa_option_sliders', $eosa_global_array);
        $eosa_index_array = 0;
    } 
}

//EXPORT SECTION
$eosa_output; 
$eosa_output_html = "";
if (isset($_POST['submit_export_eventon'])) { 
    $eosa_output = get_option('evcal_options_evcal_2'); 
    $myfile = fopen("../wp-content/plugins/eventon-slider-addon/assets/export-translations-eventon.txt", "w") or die("Unable to open file!");
    fwrite($myfile, json_encode_arr($eosa_output));
    fclose($myfile);
    $eosa_output_html = "Download: <a target='_blank' href='" . plugin_dir_url( __FILE__ )  . "assets/export-translations-eventon.txt'>export-translations-eventon.txt</a> (Right click of mouse > save link)";
} 
if (isset($_POST['submit_export_eosa'])) { 
    $eosa_output = get_option('eosa_option_language'); 
    $myfile = fopen("../wp-content/plugins/eventon-slider-addon/assets/export-translations-eventon-slideraddon.txt", "w") or die("Unable to open file!");
    fwrite($myfile, json_encode_arr($eosa_output));
    fclose($myfile);
    $eosa_output_html = "Download: <a target='_blank' href='" . plugin_dir_url( __FILE__ )   . "assets/export-translations-eventon-slideraddon.txt'>export-translations-eventon-slideraddon.txt</a> (Right click of mouse > save link)";
} 
//IMPORT SECTION
if (isset($_POST['submit_import_eventon'])||isset($_POST['submit_import_eosa'])) { 
    $target_dir = "../wp-content/plugins/eventon-slider-addon/assets/";
    $file_name = basename($_FILES["eosa_file_upload"]["name"]);
    $target_file = $target_dir . $file_name;
    if (move_uploaded_file($_FILES["eosa_file_upload"]["tmp_name"], $target_file)) {
        $url_file = "../wp-content/plugins/eventon-slider-addon/assets/". $file_name; 
        $myfile = fopen($url_file, "r") or die("Unable to open file!");
        if(isset($_POST['submit_import_eventon'])) update_option('evcal_options_evcal_2', json_decode(fread($myfile,filesize($url_file)),true));
        if(isset($_POST['submit_import_eosa'])) update_option('eosa_option_language', json_decode(fread($myfile,filesize($url_file)),true));
        fclose($myfile);
        $error_box = "<div id='error_box'>Translations updated</div>";
        $eosa_language_array = get_option('eosa_option_language'); 
    } else {
        $error_box = "<div id='error_box'>Sorry, there was an error uploading your file</div>";
    }
}

//LANGAUGE SAVE SECTION
if (isset($_POST['submit_save_language'])) { 
    $eosa_index_language = $_POST['eosa_option_language_setting'];
    
    $temp_arr = array(
        "from" => $_POST['eosa_input_lan_from'], 
        "to" => $_POST['eosa_input_lan_to'],
        "from_2" => $_POST['eosa_input_lan_from2'],
        "to_2" => $_POST['eosa_input_lan_to2'],
        "time" => $_POST['eosa_input_lan_time'],
        "open_event" => $_POST['eosa_input_lan_openevent'],
        "show_map" => $_POST['eosa_input_lan_showmap'],
        "start" => $_POST['eosa_input_lan_start'],
        "finish" => $_POST['eosa_input_lan_finish'],
        );
    
    $eosa_language_array[$eosa_index_language] = $temp_arr; //update language
    
    update_option('eosa_option_language', $eosa_language_array );
}



//READ SECTION
$eosa_html = "<script type='text/javascript'>";

if(($selected_tab == "") || (strlen($selected_tab) == 0)) { 
    $js_array = json_encode_arr($eosa_global_array);
    $eosa_html = $eosa_html . "var eosa_option_sliders = " . $js_array;
}
if($selected_tab == "setting") { 
    $js_array = json_encode_arr($eosa_language_array);
    $eosa_html = $eosa_html . "var eosa_language_array = " . $js_array;
}
echo $eosa_html . "</script>";


//FUNCTIONS
function removeslashes($string) {
    $string=implode("",explode("\\",$string));
    return stripslashes(trim($string));
}

function isSliderIDunique($id_slider, &$eosa_global_array, $eosa_index_array) {

    for ($x = 0; $x < count($eosa_global_array); $x++) {
        if (($id_slider == $eosa_global_array[$x]["eosa_option_sliderid"]) && ($x != $eosa_index_array)) return false;
    }
    return true;
}

function getDateRange() {
    $selectedRange = $_POST['eosa_option_date_range'];
    $isError = false;
    if($selectedRange=="all") return "all";
    if($selectedRange=="past") return "past";
    if($selectedRange=="future") return "future";
    if($selectedRange=="today") return "today";
    if($selectedRange=="current_week") return "current_week";
    if($selectedRange=="current_month") return "current_month";

    if($selectedRange=="before") {
        $range1 = $_POST['eosa_option_date_range_txt_before'];
        if(strlen ($range1) > 0) {
            return "before".$range1;
        } else $isError = true;
    }
    if($selectedRange=="after") {
        $range1 = $_POST['eosa_option_date_range_txt_after'];
        if(strlen ($range1) > 0) {
            return "after".$range1;
        } else $isError = true;
    } 
    if($selectedRange=="between") {
        $range1 = $_POST['eosa_option_date_range_txt_between1'];
        $range2 = $_POST['eosa_option_date_range_txt_between2'];
        if((strlen ($range1) > 0)&&(strlen ($range2) > 0)) {
            return "between".$range1.":".$range2;
        } else $isError = true;
    }  
    if($isError) {
        return "error";
    }
}

function slugify($text) { 
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    if (empty($text)) return 'n-a';
    return $text;
}
?>

<h2>EventON Slider Addon </h2>
<h2 class="nav-tab-wrapper_eosa" id="menu_tabs">
    <a href="?page=event-slider-addon" class="nav-tab_eosa nav-tab-active_eosa" id="menu_tab1">Sliders</a>
    <a href="?page=event-slider-addon&amp;tab=setting" class="nav-tab_eosa" id="menu_tab2">Settings</a>
    <a href="?page=event-slider-addon&amp;tab=support" class="nav-tab_eosa" id="menu_tab3">Support</a>
</h2>
<div id="error_box_container"><?php echo $error_box ?></div>
<!--##### TAB 1 ######-->
<?php if(($selected_tab == "") || (strlen($selected_tab) == 0)) { ?>
<table class="tab_menu_eosa">
    <tbody>
        <tr>
            <td class="first_col" valign="top">
                <div class="button_admin_eosa" onclick="addNewSlider()" style="margin-bottom: 15px; margin-top: 5px;">ADD NEW SLIDER</div>
                <ul id="sliders_list">
                    <li><a class="button_menu focused" id="sliders_list_0" onclick="selectSlider(0)">Slider 1</a></li>
                </ul>
            </td>
            <td width="100%" valign="top">
                <div id="tab_1" class="main_box_eosa">
                    <!--##### AREA 1 ######-->
                    <div class="sliertype_box_eosa hr_line">
                        <p class="field_name_eosa">SLIDER TYPE</p>
                        <div class="sliders_list_eosa">
                            <div id="eosa_slidertype_slider" class="box_slider" onclick="setSliderType('slider')">
                                <p>CLASSIC SLIDER</p>
                                <img src="<?php echo plugins_url() .'/eventon-slider-addon/assets/images/slider_type_1.jpg' ?>" />
                            </div>
                            <div id="eosa_slidertype_carousel" class="box_slider" onclick="setSliderType('carousel')">
                                <p>CAROUSEL</p>
                                <img src="<?php echo plugins_url() .'/eventon-slider-addon/assets/images/slider_type_2.jpg' ?>" />
                            </div>
                            <div id="eosa_slidertype_minicarousel" class="box_slider" onclick="setSliderType('minicarousel')">
                                <p>MINI CAROUSEL</p>
                                <img src="<?php echo plugins_url() .'/eventon-slider-addon/assets/images/slider_type_3.jpg' ?>" />
                            </div>
                            <div id="eosa_slidertype_masonry" class="box_slider" onclick="setSliderType('masonry')">
                                <p>MASONRY</p>
                                <img src="<?php echo plugins_url() .'/eventon-slider-addon/assets/images/slider_type_4.jpg' ?>" />
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <!--##### AREA 2 ######-->
                    <form name='form' method='post'>
                        <div class="submain_box_eosa">
                            <div class="eosa_row">
                                <span>Slider Name</span>
                                <input id="eosa_option_slidername" style="margin-left: 5px; max-width: 150px;" name="eosa_option_slidername" class="text_input jtarget oesa_input" type="text" value="Slider 1">
                                <span>Slider ID: </span>
                                <input style="margin-left: 5px; max-width: 150px;" class="jtarget" id="eosa_option_sliderid" readonly="true" style="margin-left: 5px; max-width: 150px;" name="eosa_option_sliderid" type="text" value="slider_1">
                            </div>
                            <div class="eosa_row">
                                <span>Select slider language</span>
                                <select id="eosa_option_language" name="eosa_option_language" class="jtarget oesa_input">
                                    <option value="L1" selected="selected">L1</option>
                                    <option value="L2">L2</option>
                                    <option value="L3">L3</option>
                                </select>
                            </div>
                            <div class="eosa_row" id="eosa_option_classicslider_type_box">
                                <span>Classic slider type</span>
                                <select id="eosa_option_classicslider_type" name="eosa_option_classicslider_type" class="jtarget oesa_input">
                                    <option value="big" selected="selected">Big</option>
                                    <option value="mini">Mini</option>
                                </select>
                            </div>
                            <div class="eosa_row" id="eosa_option_minicarousel_type_box">
                                <span>Mini carousel type</span>
                                <select id="eosa_option_minicarousel_type" name="eosa_option_minicarousel_type" class="jtarget oesa_input">
                                    <option value="mini" selected="selected">Mini</option>
                                    <option value="micro">Micro</option>
                                </select>
                            </div>
                            <div class="eosa_row">
                                <span>Style</span>
                                <select id="eosa_option_style" name="eosa_option_style" class="jtarget oesa_input" style="margin-right: 15px;">
                                    <option value="a" selected="selected">Style A</option>
                                    <option value="b">Style B</option>
                                </select>
                                <span>Skin color</span>
                                <select id="eosa_option_skin" name="eosa_option_skin" class="jtarget oesa_input" style="margin-right: 15px;">
                                    <option value="light" selected="selected">Light</option>
                                    <option value="dark">Dark</option>
                                </select>
                                <span id="eosa_option_animation_box">
                                    <span>Animation</span>
                                    <select id="eosa_option_animation" name="eosa_option_animation" class="jtarget oesa_input">
                                        <option value="slide" selected="selected">Slide</option>
                                        <option value="fade">Fade</option>
                                    </select>
                                </span>
                            </div>
                            <div class="eosa_row" id="eosa_option_open_event_type_box">
                                <span>Open event type</span>
                                <select id="eosa_option_open_event_type" name="eosa_option_open_event_type" class="jtarget oesa_input" style="margin-right: 15px;">
                                    <option value="lightbox" selected="selected">Lightbox</option>
                                    <option value="dropdown">Dropdown</option>
                                    <option value="card">Card</option>
                                    <option value="link">Link</option>
                                    <option value="customlink">Custom Link</option>
                                    <option value="originalL">Original Lightbox</option>
                                    <option value="originalD">Original Dropdown</option>
                                </select>
                                <span>Style</span>
                                <select id="eosa_option_open_event_style" name="eosa_option_open_event_style" class="jtarget oesa_input" style="margin-right: 15px;">
                                    <option value="a" selected="selected">Style A</option>
                                    <option value="b">Style B</option>
                                </select>
                                <span id="eosa_card_direction">
                                    <span>Direction</span>
                                    <select id="eosa_option_open_event_type_card" name="eosa_option_open_event_type_card" class="jtarget oesa_input" style="margin-right: 15px;">
                                        <option value="auto" selected="selected">Auto</option>
                                        <option value="up">Up</option>
                                        <option value="down">Down</option>
                                    </select>
                                </span>
                                <span id="eosa_card_opentype">
                                    <span>Card open event type</span>
                                    <select id="eosa_option_card_opentype" name="eosa_option_card_opentype" class="jtarget oesa_input">
                                        <option value="lightbox" selected="selected">Lightbox</option>
                                        <option value="dropdown">Dropdown</option>
                                        <option value="link">Link</option>
                                        <option value="customlink">Custom Link</option>
                                        <option value="originalL">Original Lightbox</option>
                                        <option value="originalD">Original Dropdown</option>
                                    </select>
                                </span>
                            </div>
                            <div class="eosa_row" id="eosa_option_mcrow_type_box">
                                <span>Last row content type</span>
                                <select id="eosa_option_mcrow_type" name="eosa_option_mcrow_type" class="jtarget oesa_input">
                                    <option value="location" selected="selected">Location</option>
                                    <option value="subtitle">Subtitle</option>
                                    <option value="organizer">Organizer</option>
                                    <?php 
          $eventON_options = get_option('evcal_options_evcal_1');
          for ($i = 1; $i < 4; $i++) {  
              if ($eventON_options["evcal_af_".$i] == "yes") {                    
                  echo '<option value="ef' .$i. '">Extra Field ' .$i. '</option>';
              }
          }
                                    ?>
                                </select>
                            </div>
                            <div class="eosa_row" id="eosa_option_link_type_box">
                                <span>All events link</span>
                                <select id="eosa_option_link_type" name="eosa_option_link_type" class="jtarget oesa_input">
                                    <option value="none" selected="selected">None</option>
                                    <option value="events_list">Events list</option>
                                    <option value="custom_link">Custom link</option>
                                </select>
                            </div>
                            <div class="eosa_row" id="eosa_option_linkSlider_box">
                                <span>Link</span>
                                <input id="eosa_option_linkSlider" name="eosa_option_linkSlider" class="text_input jtarget oesa_input" type="text" value="">
                            </div>
                            <div class="eosa_row">
                                <span>Order by</span>
                                <select id="eosa_option_orderby" name="eosa_option_orderby" class="jtarget oesa_input">
                                    <option value="ASC" selected="selected">Ascending</option>
                                    <option value="DESC">Descending</option>
                                </select>
                            </div>
                            <div class="eosa_row" id="eosa_option_showImage_box">
                                <span>Show image ?</span>
                                <input id="eosa_option_showImage_yes" onclick="generate_eosa_shortcode()" name="eosa_option_showImage" class="jtarget" type="radio" value="yes">
                                <span class="span_relativo">YES</span>
                                <input id="eosa_option_showImage_no" onclick="generate_eosa_shortcode()" name="eosa_option_showImage" class="jtarget" type="radio" value="no" checked>
                                <span class="span_relativo">NO</span>
                            </div>
                            <div class="eosa_row" id="eosa_option_showMapDetails_box">
                                <span>Show map button ?</span>
                                <input id="eosa_option_showMap_yes" onclick="generate_eosa_shortcode()" name="eosa_option_showMap" class="jtarget" type="radio" value="yes">
                                <span class="span_relativo">YES</span>
                                <input id="eosa_option_showMap_no" onclick="generate_eosa_shortcode()" name="eosa_option_showMap" class="jtarget" type="radio" value="no" checked>
                                <span class="span_relativo">NO</span>
                                <span style="margin-left: 30px;"></span>
                                <span>Show details ?</span>
                                <input id="eosa_option_showDetails_yes" onclick="generate_eosa_shortcode()" name="eosa_option_showDetails" class="jtarget" type="radio" value="yes">
                                <span class="span_relativo">YES</span>
                                <input id="eosa_option_showDetails_no" onclick="generate_eosa_shortcode()" name="eosa_option_showDetails" class="jtarget" type="radio" value="no" checked>
                                <span class="span_relativo">NO</span>
                            </div>
                            <div class="eosa_row">
                                <span>Type of external date</span>
                                <select id="eosa_option_date_out" name="eosa_option_date_out" class="jtarget oesa_input">
                                    <option value="1">Sunday 15 june 2002 time 15:24</option>
                                    <option value="2">Sun 15 jun 2002 time 15:24</option>
                                    <option value="3">15/06/02 time 15:24</option>
                                    <option value="4">Sunday, June 15, 2002 3:24PM</option>
                                    <option value="5">Sun, Jun 15, 2002 3:24PM</option>
                                    <option value="6">Start: 15 jun 2002 15:24 - Finish: 16 jun 2002 10:30</option>
                                    <option value="7">Start: 15 jun 2002 3:24PM - Finish: 16 jun 2002 10:30AM</option>
                                    <option value="8">15 jun 2002 15:24</option>
                                    <option value="9">15 jun 2002 3:24PM</option>
                                    <option value="10">15 jun 15:30</option>
                                    <option value="11">15 jun 3:24PM</option>
                                    <option value="12">15 jun</option>
                                </select>
                            </div>
                            <div class="eosa_row">
                                <span>Type of internal date&nbsp;</span>
                                <select id="eosa_option_date_in" name="eosa_option_date_in" class="jtarget oesa_input">
                                    <option value="1">Sunday 15 june 2002 time 15:24</option>
                                    <option value="2">Sun 15 jun 2002 time 15:24</option>
                                    <option value="3">15/06/02 time 15:24</option>
                                    <option value="4">Sunday, June 15, 2002 3:24PM</option>
                                    <option value="5">Sun, Jun 15, 2002 3:24PM</option>
                                    <option value="6">Start: 15 jun 2002 15:24 - Finish: 16 jun 2002 10:30</option>
                                    <option value="7">Start: 15 jun 2002 3:24PM - Finish: 16 jun 2002 10:30AM</option>
                                    <option value="8">15 jun 2002 15:24</option>
                                    <option value="9">15 jun 2002 3:24PM</option>
                                    <option value="10">15 jun 15:30</option>
                                    <option value="11">15 jun 3:24PM</option>
                                    <option value="12">15 jun</option>
                                </select>
                            </div>
                            <div class="eosa_row_box" style="border-bottom-style: none;" id="eosa_option_masonry_type_box">
                                <div class="eosa_row">
                                    <span>N° columns</span>
                                    <select id="eosa_option_maso_col" name="eosa_option_maso_col" class="jtarget oesa_input">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3" selected="selected">3</option>
                                        <option value="4">4</option>
                                    </select>

                                </div>
                                <div class="eosa_row">
                                    <span>Random masonry layout</span>
                                    <select id="eosa_option_maso_rand" name="eosa_option_maso_rand" class="jtarget oesa_input">
                                        <option value="yes" selected="selected">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                                <div class="eosa_row">
                                    <span>Masonry pagination</span>
                                    <select id="eosa_option_maso_paged" name="eosa_option_maso_paged" class="jtarget oesa_input">
                                        <option value="yes" selected="selected">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                                <div class="eosa_row">
                                    <span>Filters</span>
                                    <select id="eosa_option_filters" name="eosa_option_filters" class="jtarget oesa_input">
                                        <option value="all" selected="selected">All filters</option>
                                        <option value="order">Filter and order only</option>
                                        <option value="cat">Categories only</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                                <div class="eosa_row">
                                    <span>Margins in px</span>
                                    <input id="eosa_option_margin" style="margin-left: 5px; max-width: 150px;" name="eosa_option_margin" class="text_input jtarget oesa_input" type="text" value="15">
                                </div>
                            </div>
                            <div class="eosa_row_box" style="border-bottom-style: none;">
                                <div class="button_adv" onclick="jQuery('.box_advanced_options').show()">Advanced options</div>
                                <div class="box_advanced_options">
                                    <div class="eosa_row">
                                        <span>Cover image</span>
                                        <select id="eosa_option_cover" name="eosa_option_cover" class="jtarget oesa_input">
                                            <option value="main" selected="selected">Default featured image</option>
                                            <option value="organizer">Organizer image</option>
                                            <option value="location">Location image</option>
                                        </select>

                                    </div>
                                    <div class="eosa_row">
                                        <span>Featured events filter</span>
                                        <select id="eosa_option_featured" name="eosa_option_featured" class="jtarget oesa_input">
                                            <option value="all" selected="selected">All events</option>
                                            <option value="yes">Only featured events</option>
                                            <option value="no">Only not featured events</option>
                                        </select>
                                    </div>
                                    <div class="eosa_row">
                                        <span>Extra field visibility</span>
                                        <select id="eosa_option_extrafields" name="eosa_option_extrafields" class="jtarget oesa_input">
                                            <option value="in" selected="selected">Only in event card</option>
                                            <option value="out">Only outside</option>
                                            <option value="all">All</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="eosa_row_box">
                                <span>Date range</span>
                                <div style="margin-top: 5px;">
                                    <div class="eosa_row">
                                        <input id="eosa_option_date_range_all" onclick="generate_eosa_shortcode()" name="eosa_option_date_range" class="jtarget" type="radio" value="all" style="margin-left: 0px;" checked>
                                        <span class="span_relativo">All events</span>
                                        <input id="eosa_option_date_range_past" onclick="generate_eosa_shortcode()" name="eosa_option_date_range" class="jtarget" type="radio" value="past">
                                        <span class="span_relativo">Past events</span>
                                        <input id="eosa_option_date_range_future" onclick="generate_eosa_shortcode()" name="eosa_option_date_range" class="jtarget" type="radio" value="future">
                                        <span class="span_relativo">Future events</span>
                                        <input id="eosa_option_date_range_today" onclick="generate_eosa_shortcode()" name="eosa_option_date_range" class="jtarget" type="radio" value="today">
                                        <span class="span_relativo">Today events</span>
                                        <input id="eosa_option_date_range_current_week" onclick="generate_eosa_shortcode()" name="eosa_option_date_range" class="jtarget" type="radio" value="current_week">
                                        <span class="span_relativo">Current week events</span>
                                        <input id="eosa_option_date_range_current_month" onclick="generate_eosa_shortcode()" name="eosa_option_date_range" class="jtarget" type="radio" value="current_month">
                                        <span class="span_relativo">Current month events</span>
                                    </div>
                                    <div class="eosa_row">
                                        <input id="eosa_option_date_range_before" onclick="generate_eosa_shortcode()" name="eosa_option_date_range" class="jtarget" type="radio" value="before" style="margin-left: 0px;">
                                        <span class="span_relativo">Before date</span>
                                        <input id="eosa_option_date_range_txt_before" name="eosa_option_date_range_txt_before" class="jtarget oesa_input" style="width: 120px; text-align: center;" type="text" value="">
                                        <span class="splitter"></span>
                                        <input id="eosa_option_date_range_after" onclick="generate_eosa_shortcode()" name="eosa_option_date_range" class="jtarget" type="radio" value="after">
                                        <span class="span_relativo">After date</span>
                                        <input id="eosa_option_date_range_txt_after" name="eosa_option_date_range_txt_after" class="jtarget oesa_input" style="width: 120px; text-align: center;" type="text" value="">
                                    </div>
                                    <div class="eosa_row">
                                        <input id="eosa_option_date_range_between" onclick="generate_eosa_shortcode()" name="eosa_option_date_range" class="jtarget" type="radio" value="between" style="margin-left: 0px;">
                                        <span class="span_relativo">Between date</span>
                                        <input id="eosa_option_date_range_txt_between1" name="eosa_option_date_range_txt_between1" class="jtarget oesa_input" style="width: 100px; text-align: center;" type="text" value="">
                                        <span class="span_relativo">and</span>
                                        <input id="eosa_option_date_range_txt_between2" name="eosa_option_date_range_txt_between2" class="jtarget oesa_input" style="width: 100px; text-align: center;" type="text" value="">
                                    </div>
                                    <p class="eosa_infotxt">Date format is dd-mm-yyyy (ex. 15-06-2002)</p>
                                </div>
                            </div>
                            <div class="eosa_row_box" id="eosa_box_carousel_1" style="border-top: none;">
                                <span>Advanced carousel settings</span>
                                <div style="margin-top: 5px;">
                                    <div class="eosa_row_mini" id="eosa_option_advsbox_minItems">
                                        <div class="row_mini_name">
                                            <p>Min n° of events showed</p>
                                        </div>
                                        <input class="row_mini_input jtarget" type="text" id="eosa_option_advs_minItems" name="eosa_option_advs_minItems" value="auto">
                                        <div class="clear"></div>
                                    </div>
                                    <div class="eosa_row_mini" id="eosa_option_advsbox_maxItems" display="">
                                        <div class="row_mini_name">
                                            <p>Max n° of events showed</p>
                                        </div>
                                        <input class="row_mini_input jtarget" type="text" id="eosa_option_advs_maxItems" name="eosa_option_advs_maxItems" value="auto">
                                        <div class="clear"></div>
                                    </div>
                                    <div class="eosa_row_mini" id="eosa_option_advsbox_move">
                                        <div class="row_mini_name">
                                            <p>N° of events moved</p>
                                        </div>
                                        <input class="row_mini_input jtarget" type="text" id="eosa_option_advs_move" name="eosa_option_advs_move" value="1">
                                        <div class="clear"></div>
                                    </div>
                                    <div class="eosa_row_mini" id="eosa_option_advsbox_itemWidth">
                                        <div class="row_mini_name">
                                            <p>Event box width (px)</p>
                                        </div>
                                        <input class="row_mini_input jtarget" type="text" id="eosa_option_advs_itemWidth" name="eosa_option_advs_itemWidth" value="260">
                                        <div class="clear"></div>
                                    </div>
                                    <div class="eosa_row_mini" id="eosa_option_advsbox_itemMargin">
                                        <div class="row_mini_name">
                                            <p>Event box margin (px)</p>
                                        </div>
                                        <input class="row_mini_input jtarget" type="text" id="eosa_option_advs_itemMargin" name="eosa_option_advs_itemMargin" value="15">
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="eosa_row">
                                <span>N° events to show</span>
                                <input id="eosa_option_showevent" name="eosa_option_showevent" class="jtarget oesa_input" style="width: 50px; text-align: center;" type="text" value="">
                            </div>
                            <div class="eosa_row shortcode_box">
                                <p class="eosa_big_label">SHORTCODE <span>COPY E PAST THIS WHERE YOU WANT</span><span style="float: right" onclick="generate_eosa_shortcode()">Regenerate shortcode</span></p>
                                <textarea readonly id="FINAL_SHORTCODE" name="FINAL_SHORTCODE" class="textarea_eosa" rows="4" cols="50">[eventon_slider slider_type='slider' lan='L1' orderby='ASC' map='no' details='no' date_out='1' date_in='1' id='slider-1']</textarea>
                                <div>
                                    <input name="submit_save" type="submit" value="SAVE SLIDER" id="submit_save" class="button_admin_eosa" style="margin-top: 15px; float: left;" />
                                    <input name="submit_delite" type="submit" value="DELETE SLIDER" id="submit_delite" class="button_admin_eosa" style="margin-top: 15px; float: right; background-color: #908F8E;" />
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>

                        <input type="hidden" name="eosa_hidden_slidertype" id="eosa_hidden_slidertype" value="slider" />
                        <input type="hidden" name="eosa_hidden_indexarray" id="eosa_hidden_indexarray" value="<?php echo $eosa_index_array ?>" />
                    </form>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<?php } ?>

<!--##### TAB 2 ######-->
<?php if($selected_tab == "setting") { ?>
<table class="tab_menu_eosa">
    <tbody>
        <tr>
            <td class="first_col" valign="top">
                <ul id="Ul1">
                    <li><a class="button_menu focused" id="A1" onclick="selectSlider(0)">Langauge</a></li>
                </ul>
            </td>
            <td width="100%" valign="top">
                <div class="main_box_eosa eosa_mainbox_language">
                    <form name='form' method='post'>
                        <div class="eosa_row">
                            <span>Select slider language</span>
                            <select id="eosa_option_language_setting" name="eosa_option_language_setting" class="oesa_input" onchange="setSelectedLanguage()">
                                <option value="L1" selected="selected">L1</option>
                                <option value="L2">L2</option>
                                <option value="L3">L3</option>
                            </select>
                        </div>
                        <div class="eosa_row_lang">
                            <div class="row_lang_name">
                                <p>From</p>
                            </div>
                            <input class="row_lang_input" type="text" id="eosa_input_lan_from" name="eosa_input_lan_from" value="">
                            <div class="clear"></div>
                        </div>
                        <div class="eosa_row_lang">
                            <div class="row_lang_name">
                                <p>To</p>
                            </div>
                            <input class="row_lang_input" type="text" id="eosa_input_lan_to" name="eosa_input_lan_to" value="">
                            <div class="clear"></div>
                        </div>
                        <div class="eosa_row_lang">
                            <div class="row_lang_name">
                                <p>From 2</p>
                            </div>
                            <input class="row_lang_input" type="text" id="eosa_input_lan_from2" name="eosa_input_lan_from2" value="">
                            <div class="clear"></div>
                        </div>
                        <div class="eosa_row_lang">
                            <div class="row_lang_name">
                                <p>To 2</p>
                            </div>
                            <input class="row_lang_input" type="text" id="eosa_input_lan_to2" name="eosa_input_lan_to2" value="">
                            <div class="clear"></div>
                        </div>
                        <div class="eosa_row_lang">
                            <div class="row_lang_name">
                                <p>Time</p>
                            </div>
                            <input class="row_lang_input" type="text" id="eosa_input_lan_time" name="eosa_input_lan_time" value="">
                            <div class="clear"></div>
                        </div>
                        <div class="eosa_row_lang">
                            <div class="row_lang_name">
                                <p>Open event</p>
                            </div>
                            <input class="row_lang_input" type="text" id="eosa_input_lan_openevent" name="eosa_input_lan_openevent" value="">
                            <div class="clear"></div>
                        </div>
                        <div class="eosa_row_lang">
                            <div class="row_lang_name">
                                <p>Show map</p>
                            </div>
                            <input class="row_lang_input" type="text" id="eosa_input_lan_showmap" name="eosa_input_lan_showmap" value="">
                            <div class="clear"></div>
                        </div>
                        <div class="eosa_row_lang">
                            <div class="row_lang_name">
                                <p>Start</p>
                            </div>
                            <input class="row_lang_input" type="text" id="eosa_input_lan_start" name="eosa_input_lan_start" value="">
                            <div class="clear"></div>
                        </div>
                        <div class="eosa_row_lang">
                            <div class="row_lang_name">
                                <p>Finish</p>
                            </div>
                            <input class="row_lang_input" type="text" id="eosa_input_lan_finish" name="eosa_input_lan_finish" value="">
                            <div class="clear"></div>
                        </div>
                        <div class="eosa_row">
                            <input name="submit_save_language" type="submit" value="SAVE" id="submit_save_language" class="button_admin_eosa" style="margin-top: 15px;" />
                        </div>
                    </form>
                    <form name='form_upload' method='post' enctype='multipart/form-data'>
                        <div class="eosa_row_box" style="border-bottom-style: none;">
                            <p class="eosa_big_label">EXPORT OPTIONS</p>
                            <input name="submit_export_eventon" type="submit" value="EXPORT ORIGINAL EVENTON TRANSLATIONS" id="submit_export_eventon" class="button_admin_eosa" style="margin-top: 15px; margin-right: 15px;" />
                            <input name="submit_export_eosa" type="submit" value="EXPORT EVENTON SLIDER ADDON TRANSLATIONS" id="submit_export_eosa" class="button_admin_eosa" style="margin-top: 15px;" />
                            <div class="clear"></div>
                            <div class="output eosa_row"><?php echo $eosa_output_html; ?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="eosa_row_box">
                            <p class="eosa_big_label">IMPORT OPTIONS</p>
                            <div class="eosa_row">
                                <input type="file" name="eosa_file_upload" id="eosa_file_upload">
                            </div>
                            <input name="submit_import_eventon" type="submit" value="IMPORT ORIGINAL EVENTON TRANSLATIONS" id="submit_import_eventon" class="button_admin_eosa" style="margin-top: 15px; margin-right: 15px;" />
                            <input name="submit_import_eosa" type="submit" value="IMPORT EVENTON SLIDER ADDON TRANSLATIONS" id="submit_import_eosa" class="button_admin_eosa" style="margin-top: 15px;" />
                            <div class="clear"></div>
                        </div>
                    </form>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<?php } ?>

<!--##### TAB 3 ######-->
<?php if($selected_tab == "support") { ?>
<table class="tab_menu_eosa">
     <p>
         To get support go to <a target="_blank" href="https://codecanyon.net/item/eventon-slider-addon/11063359/comments">Envato support page</a>.
         <br />This plugin is not part of EventON native addons collection and EventOn not support it.
     </p>
</table>
<?php } ?>
