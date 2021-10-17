<?php

global $eventON_options;

function HTMLtoText($html, $numchars) {
    // Remove the HTML tags
    $html = strip_tags($html);
    // Convert HTML entities to single characters
    $html = html_entity_decode($html, ENT_QUOTES);
    // Make the string the desired number of characters
    if(strlen($html) > $numchars) {
        $html = mb_substr($html, 0, $numchars);
        $html = $html . "...";
    }
    return $html;
}

function json_encode_arr($arr) {
    $js_array = json_encode($arr);
    if (strlen($js_array) < 3) $js_array = json_encode($arr,JSON_HEX_TAG|JSON_HEX_APOS);
    if (strlen($js_array) < 3) {
        array_walk_recursive($arr, function(&$item, $key){
             if(!mb_detect_encoding($item, 'utf-8', true)){
                     $item = utf8_encode($item);
             }
        });
        $js_array = json_encode($arr);
    }
    return $js_array;
}

function getLongDateTime($unix, $unix_end, $isAllDay, $date_type, $lan, $lan_arr_, $lan_arr_eosa_) {

    global $lan_arr;
    global $lan_arr_eosa;

    if (isset($lan_arr_)) {
        $lan_arr = $lan_arr_;
    }
    if (isset($lan_arr_eosa_)) {
        $lan_arr_eosa = $lan_arr_eosa_;
    }

    //=============START DATE===============
    //Hours 24h
    $hours = date('G',$unix);
    //minuts
    $min = date('i',$unix);
    //name of day
    $dayW = date('w',$unix);
    if($dayW == 0) $dayW = 7;
    $fullDay = strtolower($lan_arr['evcal_lang_day'.$dayW]);
    //number of day
    $DayOfmonth = date('j',$unix);
    //name of Month
    $fullmonth = strtolower($lan_arr['evcal_lang_'.date('n', $unix)]);
    //number of Month
    $month = date('n',$unix);
    //year
    $year = date('Y',$unix);
    //=============END DATE===============
    //Hours 24h
    $hours_e = date('G',$unix_end);
    //minuts
    $min_e = date('i',$unix_end);
    //name of day
    $dayW_e = date('w',$unix_end);
    if($dayW_e == 0) $dayW_e = 7;
    $fullDay_e = strtolower($lan_arr['evcal_lang_day'.$dayW_e]);
    //number of day
    $DayOfmonth_e = date('j',$unix_end);
    //name of Month
    $fullmonth_e = strtolower($lan_arr['evcal_lang_'.date('n', $unix_end)]);
    //number of Month
    $month_e = date('n',$unix_end);
    //year
    $year_e = date('Y',$unix_end);

    $final_date = '';
    //$date_type = '5'; //temp <<<<<<<<<<<<

    //Check if single day or also end day-time
    $isOneDay = false;
    $isOneDayTwoTimes = false;
    $isTwoDays = false;
    if (($month==$month_e) && ($year==$year_e)) {
        if ($DayOfmonth==$DayOfmonth_e) {
            $isOneDay = true;
            if (($hours<$hours_e)||(($hours==$hours_e)&&($min<$min_e))) $isOneDayTwoTimes= true; //Same day but different time
        }
        if ($DayOfmonth==($DayOfmonth_e - 1)) {
            if (($hours_e < 7)&&($isAllDay=='no')) {
                $isOneDayTwoTimes = true; //only if end day is the night/morning of start day
                $isOneDay = true;
            }
        }

    } else $isTwoDays=true;

    $evo_hide_endtime = get_post_meta(get_the_Id(),'evo_hide_endtime', true);
    if ($evo_hide_endtime == "yes") { $isOneDay = true; $isOneDayTwoTimes = false; };

    // 1 - Da lunedi 15 dicemre 2014 ore 15:30 a marted� 28 dicembre 2014 ore 24:00 (lunedi 15 dicembre 2014 dalle 15:30 fino alle 01:30) Sunday 15 june 2002 time 15:24
    // 2 - Da lun 15 dic ore 15:30 a mar 16 gen ore 24:00 (lun 15 dic 2014 dalle 15:30 fino alle 01:30) Sun 15 jun 2014 time 15:24
    // 3 - Da 15/12/14 ore 14:20 a 28/12/15 ore 15:20  15/06/14 time 15:24
    if($date_type=='1' || $date_type=='2' || $date_type=='3'){
        if($date_type=='2'){
            $fullmonth = mb_substr($fullmonth,0,3);
            $fullDay = mb_substr($fullDay,0,3);
        }
        if(($date_type=='1') || ($date_type=='2')){
            $final_date = $fullDay . ' ' . $DayOfmonth . ' ' .$fullmonth. ' ' . $year; //lunedi 15 dicembre 2014 || lun 15 dic 2014
        }
        if(($date_type=='3') || ($date_type=='3')){
            $final_date = $DayOfmonth . '/' . $month . '/' .  date('y',$unix_end); //15/12/14
        }
        if ($isOneDay==true){
            //one day
            if ($isAllDay=='no'){
                //if with time
                if ($isOneDayTwoTimes==false) $final_date = $final_date . ' ' . $lan_arr_eosa['time'] . ' ' . $hours . ':'. $min; //lunedi 15 dicembre 2014 ore 15:30 || 15/12/14 ore 14:20 || lun 15 dic 2014
                else $final_date = $final_date . ' ' . $lan_arr_eosa['from_2'] . ' ' . $hours . ':'. $min . ' ' . $lan_arr_eosa['to_2'] . ' ' . $hours_e . ':'. $min_e; //lunedi 15 dicembre 2014 dalle 15:30 alle 01:30 || 15/12/14 dalle 15:30 alle 01:30
            }
        } else {
            //two days
            if(($date_type=='1') || ($date_type=='2')){
                if($date_type=='2'){
                    $fullmonth_e = mb_substr($fullmonth_e,0,3);
                    $fullDay_e = mb_substr($fullDay_e,0,3);
                }
                if ($isAllDay=='no'){
                    //if with time
                    $final_date = $lan_arr_eosa['from'] . ' ' . $final_date . ' ' . $lan_arr_eosa['time'] . ' ' . $hours . ':'. $min . ' ' . $lan_arr_eosa['to'] . ' ' . $fullDay_e . ' ' . $DayOfmonth_e . ' ' .$fullmonth_e.' ' . $year_e . ' ' . $lan_arr_eosa['time'] . ' ' . $hours_e . ':'. $min_e; //Da lunedi 15 dicemre 2014 ore 15:30 a marted� 28 dicembre 2014 ore 24:00 || Da 15/12/14 ore 15:30 a 21/12/14 ore 24:00
                } else $final_date = $lan_arr_eosa['from'] . ' ' . $final_date . ' ' . $lan_arr_eosa['to'] . ' ' . $fullDay_e . ' ' . $DayOfmonth_e . ' ' .$fullmonth_e.' ' . $year_e; //Da lunedi 15 dicembre 2014 a marted� 28 dicembre 2014
            }
            if(($date_type=='3') || ($date_type=='3')){
                if ($isAllDay=='no'){
                    //if with time
                    $final_date = $lan_arr_eosa['from'] . ' ' . $final_date . ' ' . $lan_arr_eosa['time'] . ' ' . $hours . ':'. $min . ' ' . $lan_arr_eosa['to_2'] . ' ' . $DayOfmonth_e . '/' . $month_e . '/' .  date('y',$unix_end) . ' ' . $lan_arr_eosa['time'] . ' ' . $hours_e . ':'. $min_e; //Da lunedi 15 dicemre 2014 ore 15:30 a marted� 28 dicembre 2014 ore 24:00 || Da 15/12/14 ore 15:30 a 21/12/14 ore 24:00
                } else $final_date = $lan_arr_eosa['from'] . ' ' . $final_date . ' ' . $lan_arr_eosa['to_2'] . ' ' . $DayOfmonth_e . '/' . $month_e . '/' .  date('y',$unix_end); //Da lunedi 15 dicembre 2014 a marted� 28 dicembre 2014 || Da 15/12/14 a 19/12/14
            }
        }
    }

    //4 - From Tuesday, October 12, 1954 to Tuesday, October 12, 1954 || From Tuesday, October 12, 1954 3:24PM to Tuesday, October 12, 1954 02:45
    //5 - From Tue, Oct 12, 1954 to Tue, Oct 12, 1954 || From Tue, Oct 12, 1954 3:24PM to Tue, Oct 12, 1954 02:45
    if(($date_type=='4')||($date_type=='5')){
        if($date_type=='5'){
            $fullmonth = mb_substr($fullmonth,0,3);
            $fullDay = mb_substr($fullDay,0,3);
        }
        $final_date = $fullDay . ', ' . $fullmonth . ' ' .$DayOfmonth. ', ' . $year; //Tuesday, October 12, 1954
        if ($isOneDay==true) {
            //one day
            if ($isAllDay=='no'){
                //if with time
                if ($isOneDayTwoTimes==false) $final_date = $final_date . ' '.  removeZero(date('h',$unix)). ':'. $min. date('A',$unix); //Tuesday, October 12, 1954 5:30PM
                else $final_date = $final_date . ' ' . $lan_arr_eosa['from'] . ' ' .  removeZero(date('h',$unix)). ':'. $min. date('A',$unix) . ' ' . $lan_arr_eosa['to'] . ' ' .  removeZero(date('h',$unix_end)). ':'. $min_e. date('A',$unix_end);  //Tuesday, October 12, 1954 from 5:30PM to 02:45AM
            }
        } else {
            //two days
            if($date_type=='5'){
                $fullmonth_e = mb_substr($fullmonth_e,0,3);
                $fullDay_e = mb_substr($fullDay_e,0,3);
            }
            if ($isAllDay=='no'){
                //if with time
                $final_date = $lan_arr_eosa['from'] . ' ' . $final_date . ' ' . removeZero(date('h',$unix)). ':'. $min. date('A',$unix) . ' ' . $lan_arr_eosa['to'] . ' ' . $fullDay_e . ', ' . $fullmonth_e . ' ' .$DayOfmonth_e. ', ' . $year_e. ' ' . removeZero(date('h',$unix_end)). ':'. $min. date('A',$unix_end);  //From Tuesday, October 12, 1954 15:15PM to Wensday, October 12, 1954 19:15PM
            } else $final_date = $lan_arr_eosa['from'] . ' ' . $final_date . ' ' . $lan_arr_eosa['to'] . ' ' . $fullDay_e . ', ' . $fullmonth_e . ' ' .$DayOfmonth_e. ', ' . $year_e; //From Tuesday, October 12, 1954 to Wensday, October 12, 1954
        }
    }

    //6 - Start: 15 ott 2015 15:30 - Finish: 16 ott 2015 10:30
    //7 - Start: 15 ott 2015 3:30PM - Finish: 16 ott 2015 10:30AM
    //8 - From 15 ott 2015 15:30 to 16 ott 2015 10:30
    //9 - From 15 ott 2015 3:30PM to 16 ott 2015 10:30AM
    if(($date_type=='6')||($date_type=='7')||($date_type=='8')||($date_type=='9')){

        $fullmonth = mb_substr($fullmonth,0,3);
        if (($isOneDay==true) && ($isOneDayTwoTimes==false)) {
            $final_date = $DayOfmonth . ' ' . $fullmonth . ' ' . $year . ' ';
            if (($date_type=='6')||($date_type=='8')) $final_date = $final_date . $hours . ':'. $min;
            if (($date_type=='7')||($date_type=='9')) $final_date = $final_date .  removeZero(date('h',$unix)). ':'. $min. date('A',$unix);
        } else {
            $fullmonth_e = mb_substr($fullmonth_e,0,3);
            $tmp = $lan_arr_eosa['start'] .': ';
            if (($date_type=='8')||($date_type=='9'))  $tmp = $lan_arr_eosa['from'] . " ";

            $final_date = $tmp . $DayOfmonth . ' ' . $fullmonth . ' ' . $year . ' ';
            if (($date_type=='6')||($date_type=='8')) $final_date = $final_date . $hours . ':'. $min;
            if (($date_type=='7')||($date_type=='9')) $final_date = $final_date .  removeZero(date('h',$unix)). ':'. $min. date('A',$unix);
            if (($date_type=='6')||($date_type=='7')) $final_date = $final_date . ' - ';

            $tmp = ucfirst($lan_arr_eosa['finish']) .': ';
            if (($date_type=='8')||($date_type=='9'))  $tmp = " " . $lan_arr_eosa['to'] . " ";

            $final_date = $final_date . $tmp . $DayOfmonth_e . ' ' . $fullmonth_e . ' ' . $year_e . ' ';
            if (($date_type=='6')||($date_type=='8')) $final_date = $final_date . $hours_e . ':'. $min_e;
            if (($date_type=='7')||($date_type=='9')) $final_date = $final_date .  removeZero(date('h',$unix_end)). ':'. $min. date('A',$unix_end);
        }
    }
    //10 - 15 ott 15:30
    //11 - 15 ott 3:30PM
    //12 - 15 ott
    if(($date_type=='10')||($date_type=='11')||($date_type=='12')) {
        if(!$isOneDay) $fullmonth = mb_substr($fullmonth,0,3);
        $final_date = "<span class='day'>" . $DayOfmonth . '</span> ' . $fullmonth . " ";
        if ($date_type=='10') $final_date = $final_date . $hours . ':'. $min;
        if ($date_type=='11') $final_date = $final_date . date('h',$unix_end). ':'. $min. date('A',$unix_end);
        if (($isOneDay==false) || ($isOneDayTwoTimes==true)) {
            //two days
            if ($isOneDayTwoTimes==true) {
                if ($date_type=='10') $final_date = $final_date . " " . $lan_arr_eosa['to'] . " " . $hours_e . ':'. $min_e;
                if ($date_type=='11') $final_date = $final_date . " " . $lan_arr_eosa['to'] . " " .  removeZero(date('h',$unix_end)). ':'. $min. date('A',$unix_end);
            } else {
                $fullmonth_e = mb_substr($fullmonth_e,0,3);
                if ($date_type=='10') $final_date = $final_date . "<span class='to'><i class='fa fa-caret-right'></i></span>" . "<span class='day'>" . $DayOfmonth_e . '</span> ' . $fullmonth_e . " " . $hours_e . ':'. $min_e;
                if ($date_type=='11') $final_date = $final_date . "<span class='to'><i class='fa fa-caret-right'></i></span>" . "<span class='day'>" . $DayOfmonth_e . '</span> ' . $fullmonth_e . " " .  removeZero(date('h',$unix_end)). ':'. $min. date('A',$unix_end);
                if ($date_type=='12') $final_date = $final_date . "<span class='to'><i class='fa fa-caret-right'></i></span>" . "<span class='day'> " . $DayOfmonth_e . '</span> ' . $fullmonth_e;
            }
        }
    }
    //13 - CLASSIC SLIDER - STYLE B
    if (($date_type=='13')||($date_type=='13b')) {
        $final_date;
        $start_time; $end_time;
        if ($date_type=='13') {
            $start_time = $hours . ':'. $min;
            $end_time = $hours_e . ':'. $min_e;
        }
        if ($date_type=='13b') {
            $start_time = removeZero(date('h',$unix)). ':'. $min. date('A',$unix);
            $end_time =  removeZero(date('h',$unix_end)). ':'. $min. date('A',$unix_end);
        }
        //one day
        if (($isOneDay) && ($isOneDayTwoTimes)) {
            $final_date = $lan_arr_eosa['from'] . " " . $start_time . " " . $lan_arr_eosa['to'] . " " . $end_time;
        }
        if (($isOneDay) && ($isOneDayTwoTimes==false)) {
            if ($isAllDay=='no') $final_date = $lan_arr_eosa['from'] . " " .$start_time;
            else  $final_date = $lan_arr['evcal_lang_allday'];
        }
        //two days
        if ($isOneDay==false) {
            $fullmonth_e = mb_substr($fullmonth_e,0,3);
            $fullDay_e = mb_substr($fullDay_e,0,3);
            if ($isAllDay=='no') $final_date = $lan_arr_eosa['from'] . " " . $start_time . " " . $lan_arr_eosa['to'] . " " . $fullDay_e . " " . $DayOfmonth_e . " "  . $lan_arr_eosa['time'] . " " . $end_time;
            else  $final_date = $final_date = $lan_arr_eosa['to'] . " " . $fullDay_e . " " . $DayOfmonth_e . " "  .$fullmonth_e . " "  . $year_e;
        }
    }

    return ucfirst($final_date);
}
function removeZero($txt) {
    if (str_starts_with($txt,"0")) return mb_substr($txt,1, strlen($txt-1));
    else return $txt;
}
function str_starts_with($string, $check_string) {
    return strpos($string, $check_string) === 0;
}

function printPopCode($id_slider, $id, $eo_index, $msg_txt) {
    return "id=\"" . $id . "\" onmouseout=\"hidePop('".$id_slider."')\" onmouseover=\"posPop('".$id_slider."','". $id . "','" .  str_replace("'", "\'", $msg_txt) . "')\"";
}
function printPopEventCode($eo_index, $id, $direction, $open_type) {
    return "id=\"minicar_item_" . $eo_index . "\" onclick=\"showEventOESAinit('" . $eo_index ."','" . $id . "'," . $id . "_eo_js_array,'" . $open_type . "','" . $direction . "','minicar_item_". $eo_index . "')\"";
}

function getExtraFieldsHTML($slider_type, $style, $color, $arr) {
    $html = "";
    $explodeArr;
    if ((($slider_type == "carousel")||($slider_type == "masonry")) && ($style == "a")) {
        for ($i = 0; $i < count($arr); $i++) {
            $html .= '<div class="eo_s2_row ef' .$i. '"><span class="eo_icon_box_2"><i class="fa ' .$arr[$i]["icon"]. '"></i></span><span class="so_title">';
            if ($arr[$i]["type"] == "button") {
                $explodeArr = explode("|", $arr[$i]["content"]);
                $html .= '<a class="global_button" href="'.$explodeArr[1].'">' .$explodeArr[0]. '</a></span>';
            }
            if (($arr[$i]["type"] == "text")||($arr[$i]["type"] == "textarea")) {
                $html .=  $arr[$i]["content"]. '</span>';
            }
            $html .= "</div>";
        }
    }

    if ((($slider_type == "carousel")||($slider_type == "masonry")) && ($style == "b")) {
        for ($i = 0; $i < count($arr); $i++) {
            $html .= ' <div class="eo_card_row ef' .$i. '"><i class="fa ' .$arr[$i]["icon"]. '" style="color: #' .$color. '"></i><span class="eo_card_sotitle">';
            if ($arr[$i]["type"] == "button") {
                $explodeArr = explode("|", $arr[$i]["content"]);
                $html .= '<a class="global_button" href="'.$explodeArr[1].'">' .$explodeArr[0]. '</a></span>';
            }
            if (($arr[$i]["type"] == "text")||($arr[$i]["type"] == "textarea")) {
                $html .=  $arr[$i]["content"]. "</span>";
            }
            $html .= "</div>";
        }
    }

    if (($slider_type == "slider") && ($style == "a")) {
        for ($i = 0; $i < count($arr); $i++) {
            $html .= ' <div class="ef_row ef' .$i. '"><i class="fa ' .$arr[$i]["icon"]. ' eo_i" style="color: #' .$color. '"></i><span class="so_title">';
            if ($arr[$i]["type"] == "button") {
                $explodeArr = explode("|", $arr[$i]["content"]);
                $html .= '<a class="global_button" href="'.$explodeArr[1].'">' .$explodeArr[0]. '</a></span>';
            }
            if (($arr[$i]["type"] == "text")||($arr[$i]["type"] == "textarea")) {
                $html .=  $arr[$i]["content"]. "</span>";
            }
            $html .= "</div>";
        }
    }
    if (($slider_type == "slider") && ($style == "b")) {
        for ($i = 0; $i < count($arr); $i++) {
            $html .= ' <div class="s1b_mrow ef' .$i. '"><i class="fa ' .$arr[$i]["icon"]. '" style="color: #' .$color. '"></i>';
            if ($arr[$i]["type"] == "button") {
                $explodeArr = explode("|", $arr[$i]["content"]);
                $html .= '<a class="global_button" href="'.$explodeArr[1].'">' .$explodeArr[0]. '</a>';
            }
            if (($arr[$i]["type"] == "text")||($arr[$i]["type"] == "textarea")) {
                $html .=  $arr[$i]["content"]. "";
            }
            $html .= "</div>";
        }
    }
    if (($slider_type == "minicarousel") && ($style == "a")) {
        $html = '<i class="fa ' .$arr["icon"]. '" style="color: #' .$color. '"></i>';
        if ($arr["type"] == "button") {
            $explodeArr = explode("|", $arr["content"]);
            $html .= '<a class="global_button" href="'.$explodeArr[1].'">' .$explodeArr[0]. '</a>';
        }
        if (($arr["type"] == "text")||($arr["type"] == "textarea")) {
            $html .=  $arr["content"];
        }
    }
    return $html;
}

function readEventData($date_out, $date_in, $lan, $optionArr) {

    global $lan_arr;
    global $lan_arr_eosa;
    global $post;
    global $eventON_options;
    global $plugin_url;
    global $location_tax_meta;

    $item_array = array();
    $slider_type = "";
    $c_open_type = "";
    $open_type = "";
    $cover = "";
    $ef = "all";
    $id = get_the_Id();

    if(isset($optionArr["slider_type"])) {
        $slider_type = $optionArr["slider_type"];
    }
    if(isset($optionArr["c_open_type"])) {
        $c_open_type = $optionArr["c_open_type"];
    }
    if(isset($optionArr["open_type"])) {
        $open_type = $optionArr["open_type"];
    }
    if(isset($optionArr["cover"])) {
        $cover = $optionArr["cover"];
    }
    if(isset($optionArr["ef"])) {
        $ef = $optionArr["ef"];
    }
    if(isset($optionArr["lan_arr"])) {
        $lan_arr = $optionArr["lan_arr"];
    }
    if(isset($optionArr["lan_arr_eosa"])) {
        $lan_arr_eosa = $optionArr["lan_arr_eosa"];
    }
    if(!isset($eventON_options)) {
        $eventON_options = get_option('evcal_options_evcal_1');
    }

    $unix = get_post_meta($id, 'evcal_srow', true);
    $unix_end = get_post_meta($id, 'evcal_erow', true);

    //0 - Date day
    if(!empty($unix)){
        $_START = eventon_get_editevent_kaalaya($unix);
        $ev_date=$_START[0];
		$ev_date=str_replace('/', '-', $ev_date);
        array_push($item_array,date("d", strtotime($ev_date)));
    }else{
        array_push($item_array, "--");
    }

    //1 - Date month
    if(!empty($unix)){
        array_push($item_array,$lan_arr['evcal_lang_'.date('n', $unix)]);
    }else{
        array_push($item_array, "--");
    }

    //2 - Title
    array_push($item_array, get_the_title());

    //3 - SubTitle
    array_push($item_array, get_post_meta($id,'evcal_subtitle', true));

    //4 - Image cover
    $urlArr;
    $url = "";
    if ($cover == "organizer") {
        //Organizer
        $idImage = get_post_meta($id,'evo_org_img', true);
        if (strlen($idImage) > 0) {
            $urlArr = wp_get_attachment_image_src($idImage, array( 1000,800 ),true, '' );
            $url = $urlArr[0];
        }
    }
    if ($cover == "location") {
        //Location
        $idImage = get_post_meta($id,'evo_loc_img', true);
        if (strlen($idImage) > 0) {
            $urlArr = wp_get_attachment_image_src($idImage, array( 1000,800 ),true, '' );
            $url = $urlArr[0];
        }
    }
    if (($cover == "")||($cover == "main")||($url == "")) {
        //Default
        $urlArr = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array( 10000,8000 ),true, '' );
        $url = $urlArr[0];
    }
    if (strpos($url,'media/default.png') !== false) $url = $plugin_url .'/eventon-slider-addon/assets/images/default-cover.jpg';
    array_push($item_array,$url);

    //5 - Location and coordinates
    $loc_txt = "";
    $termMeta = array();
    $loc_arr = wp_get_post_terms($id,'event_location');
    if (is_null($location_tax_meta)) $location_tax_meta = get_option( "evo_tax_meta");
    if (count($loc_arr) > 0 && $location_tax_meta["event_location"] !== null) {
        if (key_exists($loc_arr[0]->term_id, $location_tax_meta["event_location"])) {
            $termMeta = $location_tax_meta["event_location"][$loc_arr[0]->term_id];
        }
    }
    if (count($termMeta) == 0 && count($loc_arr) > 0) $termMeta = get_option( "taxonomy_" . $loc_arr[0]->term_id);
    if (count($termMeta) > 0) {
        $loc_txt = $termMeta['location_address'] . '|' . $termMeta['location_lon'] . '|' . $termMeta['location_lat'];
    }
    if ($loc_txt == "" || $loc_txt == "||")
        $loc_txt = get_post_meta($id,'evcal_location', true);
    if ($loc_txt == "") $loc_txt = "--";
    array_push($item_array,$loc_txt);

    //6 - Organizer
    $organizer = wp_get_post_terms($id,'event_organizer');
    if (count($organizer)) {
        $organizer = get_term((int)$organizer[0]->term_id)->name;

    } else $organizer = "";
    array_push($item_array,$organizer);

    //7 - Small Description
    array_push($item_array, HTMLtoText(get_the_content(),500));

    //8 - Long Date with Time
    $isAllDay = get_post_meta($id,'evcal_allday', true); //yes/n
    $unix = get_post_meta($id, 'evcal_srow', true);
    array_push($item_array,getLongDateTime($unix,$unix_end,$isAllDay,$date_out,$lan,$lan_arr, $lan_arr_eosa));

    //9 - Long Date with Time 2
    array_push($item_array,getLongDateTime($unix,$unix_end,$isAllDay,$date_in,$lan,$lan_arr, $lan_arr_eosa));

    //10 - Event color
    array_push($item_array,get_post_meta($id,'evcal_event_color', true));

    //11 - Single Event Link
    if (($open_type == "customlink")||($c_open_type == "customlink")) {
        array_push($item_array,get_post_meta($id,'evcal_exlink', true));
    } else {
        // MIKE
        //array_push($item_array,get_permalink($id));
        array_push($item_array,get_permalink($id) . "?l=" . $lan . "&ri=0");
    }

    //12 - Long Date with Time - Only classic style 2
    $dt = "13";
    if (($date_out == "4")||($date_out == "5")||($date_out == "7")||($date_out == "8")||($date_out == "11")) $dt = "13b";
    array_push($item_array,getLongDateTime($unix,$unix_end,$isAllDay,$dt,$lan,$lan_arr, $lan_arr_eosa));

    //13 - Full HTML Description
    if (($open_type != "originalL") && ($open_type != "originalD") && ($open_type != "link") && ($open_type != "customlink")) {
        $content = apply_filters( 'the_content', get_the_content() );
        $content = str_replace( ']]>', ']]&gt;', $content );
        array_push($item_array, $content);
    } else array_push($item_array, "");


    //14 - Extra Fields
    $tmpArr = array();
    if ($ef != "no") {
        for ($i = 1; $i < 4; $i++) {
            if (isset($eventON_options["evcal_af_".$i]) && $eventON_options["evcal_af_".$i] == "yes") {
                $type = $eventON_options["evcal_ec_f".$i."a2"];
                $content =  get_post_meta($id,'_evcal_ec_f'.$i.'a1_cus', true);

                if ( strlen($content) > 0 ) {
                    $label = $eventON_options["evcal_ec_f".$i."a1"];
                    $icon = $eventON_options["evcal__fai_00c".$i];
                    if ($type == "button") $content .= "|" . get_post_meta($id,'_evcal_ec_f'.$i.'a1_cusL', true);
                    $tmpArr0 = array("label" => $label, "type" => $type, "icon" => $icon, "content" => $content);
                    array_push($tmpArr,$tmpArr0);
                }
            }
        }
    }
    array_push($item_array,$tmpArr);

    //15 - Start Unix Date
    array_push($item_array,$unix);

    //16 - Read more button
    $tmp = get_post_meta($id,'evcal_lmlink', true);
    if   (strlen($tmp) > 0) {
        $target = (get_post_meta($id,'evcal_lmlink_target', true) == "yes") ? "target='_blank'" : "";
        $tmp = '<a href="' . $tmp .'"  class="button_main" ' . $target . '><i class="fa fa-link"></i>' .$lan_arr["evcal_evcard_learnmore2"] .'</a>';
    }
    array_push($item_array,$tmp);

    //17 - Post ID
    array_push($item_array,$id);

    //18 - Cancelled event
    array_push($item_array,get_post_meta($id,'_cancel',true));

    return $item_array;
}

function getCateogries($lan) {
    global $eventON_options;
    global $lan_arr;

    $arrTax = array();

    if(!isset($eventON_options)) {
        $eventON_options = get_option('evcal_options_evcal_1');
    }

    $cat_name = $eventON_options["evcal_eventt"];
    if (!empty($cat_name)) array_push($arrTax,  array($cat_name , ""));
    $cat_name = $eventON_options["evcal_eventt2"];
    if (!empty($cat_name)) array_push($arrTax,  array($cat_name , "_2"));

    $cat_name = $eventON_options["evcal_eventt3"];
    if ((!empty($cat_name)) && ($eventON_options['evcal_ett_3'] == "yes")) array_push($arrTax, array($cat_name , "_3"));
    $cat_name = $eventON_options["evcal_eventt4"];
    if ((!empty($cat_name)) && ($eventON_options['evcal_ett_4'] == "yes")) array_push($arrTax, array($cat_name , "_4"));
    $cat_name = $eventON_options["evcal_eventt5"];
    if ((!empty($cat_name)) && ($eventON_options['evcal_ett_5'] == "yes")) array_push($arrTax, array($cat_name , "_5"));

    for ($i = 0; $i < count($arrTax); $i++) {
        $arr = get_terms("event_type" . $arrTax[$i][1], 'orderby=name&hide_empty=0');

        $arrA = array();
        for ($j=0; $j < count($arr); $j++) {
            if ($arr[$j]->count>0)
                array_push($arrA,get_object_vars($arr[$j]));
        }
        $arrTax[$i] =  array($arrTax[$i], $arrA);
    }

    //languages
    if($lan != "L0") {
        if(count($lan_arr) == 0) {
            $lan_arr = get_option('evcal_options_evcal_2');
            $lan_arr = $lan_arr[$lan];
        }

        foreach($lan_arr as $key => $item){
            if (($key == "evcal_lang_et1") || ($key == "evcal_lang_et2") || ($key == "evcal_lang_et3") || ($key == "evcal_lang_et4") || ($key == "evcal_lang_et5")) {
                if (strlen($item) > 0) {
                    $event_type = "";
                    if ($key == "evcal_lang_et2") $event_type = "_2";
                    if ($key == "evcal_lang_et3") $event_type = "_3";
                    if ($key == "evcal_lang_et4") $event_type = "_4";
                    if ($key == "evcal_lang_et5") $event_type = "_5";

                    for ($j = 0; $j < count($arrTax); $j++) {
                        if ($arrTax[$j][0][1] == $event_type) $arrTax[$j][0][0] = $item;
                    }
                }
            }
            if ((strpos($key,'evolang_event_type_') !== false)) {
                if (strlen($item) > 0) {
                    $event_type = "";
                    if (substr_count($key, '_') == 4) {
                        if (strpos($key,'evolang_event_type_2') !== false) $event_type = "_2";
                        if (strpos($key,'evolang_event_type_3') !== false) $event_type = "_3";
                        if (strpos($key,'evolang_event_type_4') !== false) $event_type = "_4";
                        if (strpos($key,'evolang_event_type_5') !== false) $event_type = "_5";
                    }

                    $id_cat = substr($key, strripos($key, "_") + 1);
                    for ($j = 0; $j < count($arrTax); $j++) {
                        if ($arrTax[$j][0][1] == $event_type) {
                            $tmp  = $arrTax[$j][1];
                            for ($i = 0; $i < count($tmp); $i++) {
                                if ($tmp[$i]['term_taxonomy_id'] == $id_cat) {
                                    $arrTax[$j][1][$i]['name'] = $item;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return $arrTax;
}

/* MIKE (mike@acdlabs.pt) */

function sort_by_date_in_asc($a,$b)
{
    if ($a[15] == $b[15]) {
        return 0;
    }
    return ($a[15] < $b[15]) ? -1 : 1;
}

function sort_by_date_in_desc($a,$b)
{
    if ($a[15] == $b[15]) {
        return 0;
    }
    return ($a[15] < $b[15]) ? 1 : -1;
}

function inDateRange($date_code, $metaquery) {
    $ret = false;
    $idx = 0;

    if ($metaquery[$idx]=="") {
        $ret = true;
        return $ret;
    }

    if (!isset($metaquery[$idx]["relation"])) {
        // only one date to deal
        switch ($metaquery[$idx]["compare"]) {
            case '<':
                $ret = $date_code < $metaquery[$idx]["value"];
                break;
            case '>=':
                $ret = $date_code >= $metaquery[$idx]["value"];
                break;
            case 'BETWEEN':
                $ret = ($date_code >= $metaquery[$idx]["value"][0]);
                if ($ret)
                    $ret = ($date_code <= $metaquery[$idx]["value"][1]);
                break;
        }
    } else {
        // date interval to deal
        if (isset($metaquery[$idx]["1"]["compare"])) {
            switch ($metaquery[$idx]["1"]["compare"]) {
                case '<=':
                    $ret = $date_code <= $metaquery[$idx]["1"]["value"];
                    break;
                case '>=':
                    $ret = $date_code >= $metaquery[$idx]["1"]["value"];
                    break;
            }

            if ($ret)  {
                switch ($metaquery[$idx]["0"]["compare"]){
                    case '<=':
                        $ret = $date_code <= $metaquery[$idx]["0"]["value"];
                        break;
                    case '>=':
                        $ret = $date_code >= $metaquery[$idx]["0"]["value"];
                        break;
                }
            }
        }

    }
    return $ret;
}

function createEventRepeats($slidertype,$date_out,$lan,$metaquery,&$event,$optionArr)
{
    global $lan_arr;
    global $lan_arr_eosa;
    $id= get_the_Id();
    $arrRepeats = array();

    $ev_vals = get_post_custom($id);
    $is_recurring_event = evo_check_yn($ev_vals, 'evcal_repeat');

    if ($is_recurring_event) {

        // get saved repeat intervals for repeating events
        $repeat_intervals = (!empty($ev_vals['repeat_intervals']))? unserialize($ev_vals['repeat_intervals'][0]) :null;

        // if repeat intervals are saved
        if(!empty($repeat_intervals) && is_array($repeat_intervals)) {
            // each repeating interval times
            $count=0;
            foreach($repeat_intervals as $interval)  {
                if ($count > 0) {

                    $E_start_unix = $interval[0];
                    $E_end_unix = $interval[1];

                    $isAllDay = get_post_meta($id,'evcal_allday', true); //yes/n
                    $unix = $E_start_unix;
                    $unix_end = $E_end_unix;

                    //0 - Date day
                    if(!empty($unix)){
                        $_START = eventon_get_editevent_kaalaya($unix);
                        $ev_date=$_START[0];
                        $ev_date=str_replace('/', '-', $ev_date);
                        $event[0] = date("d", strtotime($ev_date));
                    }
                    else $event[0] = "--";

                    //1 - Date month
                    if(!empty($unix)) $event[1] = $lan_arr['evcal_lang_'.date('n', $unix)];
                    else $event[1] = "--";

                    //8 - Long Date with Time
                    if(isset($optionArr["lan_arr"])) $lan_arr = $optionArr["lan_arr"];
                    if(isset($optionArr["lan_arr_eosa"]))  $lan_arr_eosa = $optionArr["lan_arr_eosa"];

                    $event[8] = getLongDateTime($unix,$unix_end,$isAllDay,$date_out,$lan,$lan_arr, $lan_arr_eosa);
                    $event[11] = get_permalink($id) . "?l=" . $lan . "&ri=" . $count;
                    $event[15] = $E_start_unix;

                    $in = inDateRange($event[15], $metaquery);

                    if ($in) array_push($arrRepeats, $event);

                }
                $count++;
            }
        }
    }
    return $arrRepeats;
}
function getEventAddress($address_string, $output="address") {
    $arr;
    if (strlen($address_string) > 0) {
        $arr = explode("|",$address_string);
    }
    if (count($arr) == 3) {
        if ($output == "address") return $arr[0];
        else return " data-lat='" . $arr[1] . "' data-lng='" . $arr[2]  . "' ";
    } else return $address_string;
}
?>
