<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_charts extends WYSIJA_object {

    function __construct(){
        parent::__construct();
    }

    function pieChart($id, $options = array()) {
        return $this->generateChart('pie', $id, $options);
    }

    function serialChart($id, $options = array()) {
        return $this->generateChart('serial', $id, $options);
    }

    function generateChart($type = 'serial', $id, $options = array()) {
        // format id
        $id = str_replace(' ', '-', $id);
        // chart dimensions
        $width = (isset($options['width'])) ? (int)$options['width'] : 400;
        $height = (isset($options['height'])) ? (int)$options['height'] : 225;

        // chart title
        $title = (isset($options['title'])) ? $options['title'] : null;

        // data
        $data = (isset($options['data'])) ? $options['data'] : null;

        // generate JS code
        $content = '<div id="wysija-chart-'.$id.'" class="wysija-chart" style="width:'.$width.'px;height:'.$height.'px;"></div>';
        $content .= '<script type="text/javascript">';
        $content .= 'AmCharts.ready(function () {';
        $content .= 'WysijaCharts.generateChart("'.$type.'", "wysija-chart-'.$id.'", {';
        // set chart title
        $content .= 'title: "'.$title.'",';
        // set data
        $content .= 'data: '.json_encode($data).',';

        switch ($type) {
            case 'serial':
                // axes data
                $axes = (isset($options['axes'])) ? $options['axes'] : null;
                // category (the field used to sort by)
                $category = (isset($options['category'])) ? $options['category'] : null;

                $content .= 'axes: '.json_encode($axes).',';
                $content .= 'category: "'.$category.'"';
                break;
            case 'pie':
                // title and value fields
                $titleField = (isset($options['titleField'])) ? $options['titleField'] : null;
                $valueField = (isset($options['valueField'])) ? $options['valueField'] : null;

                $content .= 'titleField: "'.$titleField.'",';
                $content .= 'valueField: "'.$valueField.'"';

                break;
        }

        $content .= '});';
        $content .= '});';
        $content .= '</script>';
        return $content;
    }
}