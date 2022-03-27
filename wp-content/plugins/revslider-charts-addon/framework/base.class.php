<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnChartsBase {
	
	const MINIMUM_VERSION = '6.5.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnChartsBase::MINIMUM_VERSION, '>=')) {
		
			return 'add_notice_version';
		
		}
		else if(get_option('revslider-valid', 'false') == 'false') {
		
			 return 'add_notice_activation';
		
		}
		
		return false;
		
	}
	
	protected function loadClasses() {
		
		$isAdmin = is_admin();
		
		if($isAdmin) {
			
			//handle update process, this uses the typical ThemePunch server process
			require_once(static::$_PluginPath . 'admin/includes/update.class.php');
			$update_admin = new RevAddOnChartsUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
		}
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsChartsSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsChartsSlideFront(static::$_PluginTitle);
		
	}
	
	/**
	 * Load the textdomain
	 **/
	protected function _loadPluginTextDomain(){
		
		load_plugin_textdomain('rs_' . static::$_PluginTitle, false, static::$_PluginPath . 'languages/');
		
	}
	
		
	// load admin scripts
	public function enqueue_admin_scripts($hook) {

		if($hook === 'toplevel_page_revslider') {

			if(!isset($_GET['page']) || !isset($_GET['view'])) return;
			
			$page = $_GET['page'];
			if($page !== 'revslider') return;
			
			$_handle = 'rs-' . static::$_PluginTitle . '-admin';
			$_base   = static::$_PluginUrl . 'admin/assets/';
			
			// load fronted Script for some global function
			$_fbase = static::$_PluginUrl . 'public/assets/';
			$_jsPathMin = file_exists(RS_CHARTS_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . static::$_PluginTitle . '.js') ? '' : '.min';	
			$back_jsPathMin = file_exists(static::$_PluginPath . 'admin/assets/js/revslider-' . static::$_PluginTitle . '-addon-admin.dev.js') ? '.dev' : '';
			wp_enqueue_script('charts_front', $_fbase . 'js/revolution.addon.' . static::$_PluginTitle . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			
			wp_enqueue_style($_handle, $_base . 'css/revslider-' . static::$_PluginTitle . '-addon-admin.css', array(), static::$_Version);
			wp_enqueue_script($_handle, $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin' . $back_jsPathMin . '.js', array('jquery', 'revbuilder-admin','charts_front'), static::$_Version, true);			
			wp_localize_script($_handle, 'revslider_charts_addon', self::get_var() );

		}
		
	}
	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-charts-addon') {
		
		if($slug === 'revslider-charts-addon'){
			
			$obj = self::get_var();
			$obj['help'] = self::get_definitions();
			return $obj;
			
		}
		
		return $var;
	
	}
	
	/**
	 * Called via php filter.  Merges AddOn definitions with core revslider definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_help($definitions) {
		
		if(empty($definitions) || !isset($definitions['editor_settings'])) return $definitions;
		
		if(isset($definitions['editor_settings']['layer_settings']) && isset($definitions['editor_settings']['layer_settings']['addons'])) {
			$help = self::get_definitions();
			$definitions['editor_settings']['layer_settings']['addons']['charts_addon'] = $help['layer'];
		}
		
		return $definitions;
	
	}
	
	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function get_var() {
			
		$_textdomain = 'revslider-charts-addon';
		return array(
		
			'bricks' => array(
				'fixlabel' => __('Fixed Label', $_textdomain),
				'valuesfixed' => __('Fixed Values', $_textdomain),
				'showdp' => __('Data Point', $_textdomain),
				'showbg' => __('Chart BG', $_textdomain),
				'showst' => __('Chart Stroke', $_textdomain),
				'bgcolor' => __('Bacgkround', $_textdomain),
				'left' => __('Left', $_textdomain),
				'right' => __('Right', $_textdomain),
				'middle' => __('Middle', $_textdomain),
				'textalign' => __('Text Align', $_textdomain),
				'chart' => __('Chart', $_textdomain),
				'charts' => __('Charts', $_textdomain),
				'settings' => __('Charts Settings', $_textdomain),
				'sets' => __('Settings', $_textdomain),				
				'charttype' => __('Chart Type', $_textdomain),
				'controlcharts' => __('Control Chart', $_textdomain),
				'piecharts' => __('Pie Chart', $_textdomain),
				'pbargraph' => __('Bar Graph Parallel', $_textdomain),
				'bargraph' => __('Bar Graph', $_textdomain),
				'linegraph' => __('Line Graph', $_textdomain),
				'data' => __('Data Source', $_textdomain),
				'style' => __('Styling', $_textdomain),				
				'upload' => __('Get CSV File', $_textdomain),				
				'pickcsv' => __('Get Source', $_textdomain),				
				'usetitle' => __('1st Row Title', $_textdomain),				
				'col' => __('Column', $_textdomain),
				'columns' => __('Columns', $_textdomain),
				'colsettings' => __('Column Settings', $_textdomain),
				'datapointtext' => __('Data Value', $_textdomain),
				'datapoint' => __('Data Point', $_textdomain),
				'coltitle' => __('Column Title', $_textdomain),
				'width' => __('Width', $_textdomain),
				'height' => __('Height', $_textdomain),
				'name' => __('Name', $_textdomain),				
				'hlabel' => __('Hovered Label', $_textdomain),
				'label' => __('Label', $_textdomain),
				'labels' => __('Labels', $_textdomain),
				'align' => __('Align', $_textdomain),
				'horizontal' => __('Horizontal', $_textdomain),
				'vertical' => __('Vertical', $_textdomain),
				'showvalues' => __('Single Values', $_textdomain),
				'source' => __('Source', $_textdomain),
				'basics' => __('Basics', $_textdomain),
				'datalegende' => __('Data,Legend & Grid Basics', $_textdomain),
				'interaction' => __('Interactions', $_textdomain),
				'ysplit' => __('Y-Axis Split', $_textdomain),
				'strokewidth' => __('Stroke Width', $_textdomain),
				'strokedash' => __('Dash Array', $_textdomain),
				'dashcolor' => __('Dash Color', $_textdomain),
				'strokecolor' => __('Stroke Color', $_textdomain),
				'strokestyle' => __('Style', $_textdomain),
				'fill' => __('Fill', $_textdomain),
				'cfill' => __('Chart Color', $_textdomain),
				'bg' => __('BG', $_textdomain),
				'hbg' => __('Hovered BG', $_textdomain),
				'hbgshort' => __('BG', $_textdomain),
				'fillcolor' => __('Fill Color', $_textdomain),
				'gap' => __('Gap', $_textdomain),
				'valuecolor' => __('Value Color', $_textdomain),
				'valuebgcolor' => __('Value BG Color', $_textdomain),
				'showlegende' => __('Show Legend', $_textdomain),
				'showlabel' => __('Show Label', $_textdomain),
				'fontsize' => __('Font Size', $_textdomain),
				'fontfamily' => __('Font Family', $_textdomain),
				'labels' => __('Labels', $_textdomain),
				'values' => __('Values', $_textdomain),
				'labelsx' => __('Axis X Title', $_textdomain),
				'labelsy' => __('Axis Y Title', $_textdomain),
				'showxaxisval' => __('X Axis Value', $_textdomain),
				'columnvalues' => __('Column Values', $_textdomain),
				'showsingleval' => __('Show Values', $_textdomain),
				'dpscale' => __('Points Scale', $_textdomain),
				'dphidden' => __('Unused Points Hidden', $_textdomain),
				'showvals' => __('Values', $_textdomain),
				'valuesx' => __('Values X - Horizontal', $_textdomain),
				'valuesy' => __('Values Y - Vertical', $_textdomain),
				'anchorcolor' => __('Data Point', $_textdomain),
				'showanchor' => __('Use Points', $_textdomain),
				'showval' => __('Show Value', $_textdomain),
				'showvalue' => __('Show Values', $_textdomain),
				'showgrid' => __('Grid', $_textdomain),
				'showgridx' => __('Show Lines', $_textdomain),
				'everyn' => __('Every Nth', $_textdomain),
				'showgridy' => __('Show Lines', $_textdomain),
				'gridborder' => __('Grid Border', $_textdomain),
				'format' => __('Format', $_textdomain),
				'gridy' => __('Horizontal Lines', $_textdomain),
				'gridx' => __('Vertical Lines', $_textdomain),
				'importedcsv' => __('Imported CSV', $_textdomain),
				'basics' => __('Basics', $_textdomain),
				'sizepos' => __('Dimension', $_textdomain),
				'datas' => __('Source', $_textdomain),
				'anims' => __('Animation', $_textdomain),				
				'legend' => __('Legend', $_textdomain),
				'grid' => __('Grid', $_textdomain),
				'singlevalues' => __('Single Values', $_textdomain),
				'inuse' => __('In Use', $_textdomain),
				'fontweight' => __('Font Weight', $_textdomain),
				'playanimation' => __('Play Animation', $_textdomain),
				'marker' => __('Marker', $_textdomain),
				'anchors' => __('Data Points', $_textdomain),
				'positionspace' => __('Size & Space', $_textdomain),
				'nodata' => __('Data Not Available. Read CSV First', $_textdomain),	
				'bobo' => __('Bottom Border', $_textdomain),							
				'lebo' => __('Left Border', $_textdomain),							
				'markers' => __('Markers', $_textdomain),							
				'useasx' => __('X-Axis Source', $_textdomain),							
				'usedasy' => __('This Column used as Horizontal ("X") Data Source', $_textdomain),				
				'dot' => __('Dot', $_textdomain),				
				'rad' => __('Radius', $_textdomain),				
				'bdot' => __('Bigger Dot', $_textdomain),				
				'tri' => __('Triangle', $_textdomain),				
				'rec' => __('Rectangle', $_textdomain),				
				'none' => __('None', $_textdomain),				
				'round' => __('Round', $_textdomain),				
				'dez' => __('Decimal', $_textdomain),				
				'suf' => __('Suf', $_textdomain),				
				'pre' => __('Pre', $_textdomain),		
				'vmarker' => __('Vertical Marker', $_textdomain),		
				'hmarker' => __('Horizontal Marker', $_textdomain),		
				'delay' => __('Delay', $_textdomain),		
				'speed' => __('Speed', $_textdomain),
				'curve' => __('Curve', $_textdomain),
				'labelbg' => __('Label BG', $_textdomain),				
				'zindex' => __('Z-Index', $_textdomain),
				'keepselected' => __('Keep Selected', $_textdomain),
				'keepstyle' => __('Keep Style', $_textdomain),
				'altcolors' => __('Altern. Colors', $_textdomain),
				'animation' => __('Animation', $_textdomain)				
			)
		);
	
	}
	
	/**
	 * Returns the addon help definitions
	 *
	 * @since    2.0.0
	 */
	private static function get_definitions() {
		
		return array(
			
			'layer' => array(

				'source' => array(
					
					'buttonTitle' => __('Chart Source', 'revslider-charts-addon'), 
					'title' => __('Chart Source', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.csv', 
					'keywords' => array('addon', 'addons', 'charts', 'charts addon', 'chart source'), 
					'description' => __("This field holds the CSV source data used to generate a table of data, which is turn converted into the chart. Each new line generates a new row of data. Within a line, each comma separated value corresponds with a column of data. The first line in the field should be a comma separated list providing names for each of those columns. You'll see the provided names listed under the 'X Axis Content' option, where you can select which data column you want to display horizontally along the bottom of the chart. All data columns other than the one selected under 'X Axis Content' will display on the chart as graphs.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.csv']"
						
					)
					
				),
				
				'type' => array(
					
					'buttonTitle' => __('Chart Type', 'revslider-charts-addon'), 
					'title' => __('Chart Type', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.settings.type', 
					'keywords' => array('addon', 'addons', 'charts', 'charts addon', 'chart type'), 
					'description' => __("Select the draw type of the graphs: line or bar. Choose 'Line Graph' for line style graphs, and 'Bar Graph' or 'Bar Graph Parallel' for two differently laid out styles of bar graph.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.settings.type']"
						
					)
					
				),

				'xaxissrc' => array(
					
					'buttonTitle' => __('Chart X Axis source', 'revslider-charts-addon'), 
					'title' => __('Chart X Axis', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.settings.isx', 
					'keywords' => array('addon', 'addons', 'charts', 'charts addon', 'chart source', 'chart x axis'), 
					'description' => __("Select which data column, (drawn from the CSV data in the 'Chart Source' field), you would like to display horizontally along the bottom of the chart. All data columns other than the one selected here will display on the chart as graphs.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.settings.isx']"
						
					)
					
				),

				'yaxissplit' => array(
					
					'buttonTitle' => __('Chart Y Axis split', 'revslider-charts-addon'), 
					'title' => __('Chart Y Axis split', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.grid.ydivide', 
					'keywords' => array('addon', 'addons', 'charts', 'charts addon', 'chart source', 'chart y axis', 'chart y axis split'), 
					'description' => __("Define the number of sections into which the Y axis should be split. Optionally, you can use the 'Horizontal Lines' option to display colored lines between these sections.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.grid.ydivide']"
						
					)
					
				),
				
								
				
				'curves' => array(
					
					'buttonTitle' => __('Line Graph curviness', 'revslider-charts-addon'), 
					'title' => __('Charts Curviness', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.curves.1, addOns.revslider-charts-addon.curves.2, addOns.revslider-charts-addon.curves.3,addOns.revslider-charts-addon.curves.4,addOns.revslider-charts-addon.curves.5,addOns.revslider-charts-addon.curves.6,addOns.revslider-charts-addon.curves.7,addOns.revslider-charts-addon.curves.8,addOns.revslider-charts-addon.curves.9', 
					'keywords' => array('addon', 'addons', 'charts', 'charts addon', 'graph curviness','curviness'), 
					'description' => __("Defines the curviness between points on a line graph. Values between 0 and 5 can be entered, where 0 will draw straight lines between connected datapoints and 5 will generate maximum curviness in the lines.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.curves.1'],*[data-r='addOns.revslider-charts-addon.curves.2'],*[data-r='addOns.revslider-charts-addon.curves.3'],,*[data-r='addOns.revslider-charts-addon.curves.4'],,*[data-r='addOns.revslider-charts-addon.curves.5'],,*[data-r='addOns.revslider-charts-addon.curves.6'],,*[data-r='addOns.revslider-charts-addon.curves.7'],,*[data-r='addOns.revslider-charts-addon.curves.8'],,*[data-r='addOns.revslider-charts-addon.curves.9']"			
					)
					
				),

				'zindex' => array(
					
					'buttonTitle' => __('Charts Data z Index', 'revslider-charts-addon'), 
					'title' => __('Charts z Index', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.index.1, addOns.revslider-charts-addon.index.2, addOns.revslider-charts-addon.index.3,addOns.revslider-charts-addon.index.4,addOns.revslider-charts-addon.index.5,addOns.revslider-charts-addon.index.6,addOns.revslider-charts-addon.index.7,addOns.revslider-charts-addon.index.8,addOns.revslider-charts-addon.index.9', 
					'keywords' => array('addon', 'addons', 'charts', 'charts addon', 'charts zindex','zindex'), 
					'description' => __("Set the z-index of the graph to determine whether it will appear in front of, or behind, other graphs that occupy the same area. Graphs with higher values will appear in front of graphs with lower values. If you can't see one or more of your graphs, and your graphs are filled with color, you may find that bigger graphs are covering and obscuring smaller ones. Go through and check the z-index for each graph, adjusting the values until the graphs are stacked in an order that makes them all visible.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.index.1'],*[data-r='addOns.revslider-charts-addon.index.2'],*[data-r='addOns.revslider-charts-addon.index.3'],,*[data-r='addOns.revslider-charts-addon.index.4'],,*[data-r='addOns.revslider-charts-addon.index.5'],,*[data-r='addOns.revslider-charts-addon.index.6'],,*[data-r='addOns.revslider-charts-addon.index.7'],,*[data-r='addOns.revslider-charts-addon.index.8'],,*[data-r='addOns.revslider-charts-addon.index.9']"			
					)
					
				),

				'graphstoke' => array(
					
					'buttonTitle' => __('Charts Graph Border', 'revslider-charts-addon'), 
					'title' => __('Charts Graph Border', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.strokewidth.1, addOns.revslider-charts-addon.strokewidth.2, addOns.revslider-charts-addon.strokewidth.3,addOns.revslider-charts-addon.strokewidth.4,addOns.revslider-charts-addon.strokewidth.5,addOns.revslider-charts-addon.strokewidth.6,addOns.revslider-charts-addon.strokewidth.7,addOns.revslider-charts-addon.strokewidth.8,addOns.revslider-charts-addon.strokewidth.9', 
					'keywords' => array('addon', 'addons', 'charts', 'charts addon', 'charts graph boerder','stroke', 'line width'), 
					'description' => __("Defines the width, in pixels, of the connecting line between data points in a line graph.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.strokewidth.1'],*[data-r='addOns.revslider-charts-addon.strokewidth.2'],*[data-r='addOns.revslider-charts-addon.strokewidth.3'],,*[data-r='addOns.revslider-charts-addon.strokewidth.4'],,*[data-r='addOns.revslider-charts-addon.strokewidth.5'],,*[data-r='addOns.revslider-charts-addon.strokewidth.6'],,*[data-r='addOns.revslider-charts-addon.strokewidth.7'],,*[data-r='addOns.revslider-charts-addon.strokewidth.8'],,*[data-r='addOns.revslider-charts-addon.strokewidth.9']"			
					)					
				),

				'graphstokestyle' => array(
					
					'buttonTitle' => __('Charts Graph Border Style', 'revslider-charts-addon'), 
					'title' => __('Charts Graph Border Style', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.strokedash.1, addOns.revslider-charts-addon.strokedash.2, addOns.revslider-charts-addon.strokedash.3,addOns.revslider-charts-addon.strokedash.4,addOns.revslider-charts-addon.strokedash.5,addOns.revslider-charts-addon.strokedash.6,addOns.revslider-charts-addon.strokedash.7,addOns.revslider-charts-addon.strokedash.8,addOns.revslider-charts-addon.strokedash.9', 
					'keywords' => array('addon', 'addons', 'charts', 'charts addon', 'charts graph boerder type','stroke', 'line type', 'connected line dash', 'dash'), 
					'description' => __("Defines the style of the connecting line between data points in a line graph. A value of 0 creates a solid line, and values of l or higher add gaps into the line, turning it into a dotted or dashed line. The higher the value the larger the gaps in the line.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.strokedash.1'],*[data-r='addOns.revslider-charts-addon.strokedash.2'],*[data-r='addOns.revslider-charts-addon.strokedash.3'],,*[data-r='addOns.revslider-charts-addon.strokedash.4'],,*[data-r='addOns.revslider-charts-addon.strokedash.5'],,*[data-r='addOns.revslider-charts-addon.strokedash.6'],,*[data-r='addOns.revslider-charts-addon.strokedash.7'],,*[data-r='addOns.revslider-charts-addon.strokedash.8'],,*[data-r='addOns.revslider-charts-addon.strokedash.9']"			
					)					
				),
				

				'graphstokecolor' => array(
					
					'buttonTitle' => __('Charts Graph Border Color', 'revslider-charts-addon'), 
					'title' => __('Charts Graph Border Color', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.strokecolor.1, addOns.revslider-charts-addon.strokecolor.2, addOns.revslider-charts-addon.strokecolor.3,addOns.revslider-charts-addon.strokecolor.4,addOns.revslider-charts-addon.strokecolor.5,addOns.revslider-charts-addon.strokecolor.6,addOns.revslider-charts-addon.strokecolor.7,addOns.revslider-charts-addon.strokecolor.8,addOns.revslider-charts-addon.strokecolor.9', 
					'keywords' => array('addon', 'addons', 'charts', 'charts addon', 'charts graph boerder color','stroke color', 'line color', 'connected line color', 'dash color'), 
					'description' => __("Defines the color of the connecting line between data points in a line graph.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.strokecolor.1'],*[data-r='addOns.revslider-charts-addon.strokecolor.2'],*[data-r='addOns.revslider-charts-addon.strokecolor.3'],,*[data-r='addOns.revslider-charts-addon.strokecolor.4'],,*[data-r='addOns.revslider-charts-addon.strokecolor.5'],,*[data-r='addOns.revslider-charts-addon.strokecolor.6'],,*[data-r='addOns.revslider-charts-addon.strokecolor.7'],,*[data-r='addOns.revslider-charts-addon.strokecolor.8'],,*[data-r='addOns.revslider-charts-addon.strokecolor.9']"			
					)					
				),

				'graphfillcolor' => array(
					
					'buttonTitle' => __('Charts Graph Fill Color', 'revslider-charts-addon'), 
					'title' => __('Charts Graph Fill Color', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.fillcolor.1, addOns.revslider-charts-addon.fillcolor.2, addOns.revslider-charts-addon.fillcolor.3,addOns.revslider-charts-addon.fillcolor.4,addOns.revslider-charts-addon.fillcolor.5,addOns.revslider-charts-addon.fillcolor.6,addOns.revslider-charts-addon.fillcolor.7,addOns.revslider-charts-addon.fillcolor.8,addOns.revslider-charts-addon.fillcolor.9', 
					'keywords' => array('addon', 'addons', 'charts', 'charts addon', 'charts graph color','graph color', 'fillcolor'), 
					'description' => __("Defines the background fill color of the graph. Each graph has its own fill color.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.fillcolor.1'],*[data-r='addOns.revslider-charts-addon.fillcolor.2'],*[data-r='addOns.revslider-charts-addon.fillcolor.3'],,*[data-r='addOns.revslider-charts-addon.fillcolor.4'],,*[data-r='addOns.revslider-charts-addon.fillcolor.5'],,*[data-r='addOns.revslider-charts-addon.fillcolor.6'],,*[data-r='addOns.revslider-charts-addon.fillcolor.7'],,*[data-r='addOns.revslider-charts-addon.fillcolor.8'],,*[data-r='addOns.revslider-charts-addon.fillcolor.9']"			
					)					
				),

				'datapointtype' => array(
					
					'buttonTitle' => __('Charts Data Point Layout', 'revslider-charts-addon'), 
					'title' => __('Charts Data Point Layout', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.datapoint.1, addOns.revslider-charts-addon.datapoint.2, addOns.revslider-charts-addon.datapoint.3,addOns.revslider-charts-addon.datapoint.4,addOns.revslider-charts-addon.datapoint.5,addOns.revslider-charts-addon.datapoint.6,addOns.revslider-charts-addon.datapoint.7,addOns.revslider-charts-addon.datapoint.8,addOns.revslider-charts-addon.datapoint.9', 
					'keywords' => array('addon', 'addons', 'charts', 'charts addon', 'charts graph data point','data point', 'datapoint'), 
					'description' => __("Select a shape to display at data points on the graph. Choose between 'Dot', 'Bigger Dot', 'Rectangle' or 'Triangle'", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.datapoint.1'],*[data-r='addOns.revslider-charts-addon.datapoint.2'],*[data-r='addOns.revslider-charts-addon.datapoint.3'],,*[data-r='addOns.revslider-charts-addon.datapoint.4'],,*[data-r='addOns.revslider-charts-addon.datapoint.5'],,*[data-r='addOns.revslider-charts-addon.datapoint.6'],,*[data-r='addOns.revslider-charts-addon.datapoint.7'],,*[data-r='addOns.revslider-charts-addon.datapoint.8'],,*[data-r='addOns.revslider-charts-addon.datapoint.9']"			
					)					
				),

				'datapointcolor' => array(
					
					'buttonTitle' => __('Charts Data Point Color', 'revslider-charts-addon'), 
					'title' => __('Charts Data Point Color', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.anchorcolor.1, addOns.revslider-charts-addon.anchorcolor.2, addOns.revslider-charts-addon.anchorcolor.3,addOns.revslider-charts-addon.anchorcolor.4,addOns.revslider-charts-addon.anchorcolor.5,addOns.revslider-charts-addon.anchorcolor.6,addOns.revslider-charts-addon.anchorcolor.7,addOns.revslider-charts-addon.anchorcolor.8,addOns.revslider-charts-addon.anchorcolor.9', 
					'keywords' => array('addon', 'addons', 'charts', 'charts addon', 'charts graph data point color ','data point color', 'anchorcolor'), 
					'description' => __("Set the color of the data points on the graph.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.anchorcolor.1'],*[data-r='addOns.revslider-charts-addon.anchorcolor.2'],*[data-r='addOns.revslider-charts-addon.anchorcolor.3'],,*[data-r='addOns.revslider-charts-addon.anchorcolor.4'],,*[data-r='addOns.revslider-charts-addon.anchorcolor.5'],,*[data-r='addOns.revslider-charts-addon.anchorcolor.6'],,*[data-r='addOns.revslider-charts-addon.anchorcolor.7'],,*[data-r='addOns.revslider-charts-addon.anchorcolor.8'],,*[data-r='addOns.revslider-charts-addon.anchorcolor.9']"			
					)					
				),


				'datapointlabelcolor' => array(
					
					'buttonTitle' => __('Charts Data Point Label Color', 'revslider-charts-addon'), 
					'title' => __('Charts Data Point Label Color', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.valuecolor.1, addOns.revslider-charts-addon.valuecolor.2, addOns.revslider-charts-addon.valuecolor.3,addOns.revslider-charts-addon.valuecolor.4,addOns.revslider-charts-addon.valuecolor.5,addOns.revslider-charts-addon.valuecolor.6,addOns.revslider-charts-addon.valuecolor.7,addOns.revslider-charts-addon.valuecolor.8,addOns.revslider-charts-addon.valuecolor.9', 
					'keywords' => array('addon', 'addons', 'charts', 'charts addon', 'charts graph data point value color ','data point value color', 'valuecolor'), 
					'description' => __("Set the text color of the labels that appear when hovering over data points on the graph.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.valuecolor.1'],*[data-r='addOns.revslider-charts-addon.valuecolor.2'],*[data-r='addOns.revslider-charts-addon.valuecolor.3'],,*[data-r='addOns.revslider-charts-addon.valuecolor.4'],,*[data-r='addOns.revslider-charts-addon.valuecolor.5'],,*[data-r='addOns.revslider-charts-addon.valuecolor.6'],,*[data-r='addOns.revslider-charts-addon.valuecolor.7'],,*[data-r='addOns.revslider-charts-addon.valuecolor.8'],,*[data-r='addOns.revslider-charts-addon.valuecolor.9']"			
					)					
				),

				'datapointlabelbgcolor' => array(
					
					'buttonTitle' => __('Charts Data Point Label BG Color', 'revslider-charts-addon'), 
					'title' => __('Charts Data Point Label BG Color', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.valuebgcols.1, addOns.revslider-charts-addon.valuebgcols.2, addOns.revslider-charts-addon.valuebgcols.3,addOns.revslider-charts-addon.valuebgcols.4,addOns.revslider-charts-addon.valuebgcols.5,addOns.revslider-charts-addon.valuebgcols.6,addOns.revslider-charts-addon.valuebgcols.7,addOns.revslider-charts-addon.valuebgcols.8,addOns.revslider-charts-addon.valuebgcols.9', 
					'keywords' => array('addon', 'addons', 'charts', 'charts addon', 'charts graph data point value color ','data point value color', 'valuebgcols'), 
					'description' => __("Set the background color of the labels that appear when hovering over data points on the graph.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.valuebgcols.1'],*[data-r='addOns.revslider-charts-addon.valuebgcols.2'],*[data-r='addOns.revslider-charts-addon.valuebgcols.3'],,*[data-r='addOns.revslider-charts-addon.valuebgcols.4'],,*[data-r='addOns.revslider-charts-addon.valuebgcols.5'],,*[data-r='addOns.revslider-charts-addon.valuebgcols.6'],,*[data-r='addOns.revslider-charts-addon.valuebgcols.7'],,*[data-r='addOns.revslider-charts-addon.valuebgcols.8'],,*[data-r='addOns.revslider-charts-addon.valuebgcols.9']"			
					)					
				),

				'graphwidth' => array(
					
					'buttonTitle' => __('Chart Width', 'revslider-charts-addon'), 
					'title' => __('Chart Width', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.settings.width', 
					'keywords' => array('addon', 'addons', 'charts', 'chart width', 'graph width'), 
					'description' => __("Define the width of the graph in pixels. All elements of the chart will be sized to fit within this space. The chart will also automatically scale up/down based on the wrapping container size.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.settings.width']"			
					)					
				),
				

				'graphheight' => array(
					
					'buttonTitle' => __('Chart Height', 'revslider-charts-addon'), 
					'title' => __('Chart Height', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.settings.height', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph height'), 
					'description' => __("Define the height of the graph in pixels. All elements of the chart will be sized to fit within this space. The chart will also automatically scale up/down based on the wrapping container size.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.settings.height']"			
					)					
				),

				'graphtopmargin' => array(
					
					'buttonTitle' => __('Chart Margin Top', 'revslider-charts-addon'), 
					'title' => __('Chart Margin Top', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.settings.margin.top', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph margin', 'graph padding', 'graph margin top'), 
					'description' => __("Define the amount of space, in pixels, between the top edge of the chart and the top border of the wrapping container. Behaves like top padding for the wrapping container.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.settings.margin.top']"			
					)					
				),

				'graphbottommargin' => array(
					
					'buttonTitle' => __('Chart Margin Bottom', 'revslider-charts-addon'), 
					'title' => __('Chart Margin Bottom', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.settings.margin.bottom', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph margin', 'graph padding', 'graph margin bottom'), 
					'description' => __("Define the amount of space, in pixels, between the bottom edge of the chart and the bottom border of the wrapping container. Behaves like bottom padding for the wrapping container.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.settings.margin.bottom']"			
					)					
				),

				'graphleftmargin' => array(
					
					'buttonTitle' => __('Chart Margin Left', 'revslider-charts-addon'), 
					'title' => __('Chart Margin Left', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.settings.margin.left', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph margin', 'graph padding', 'graph margin left'), 
					'description' => __("Define the amount of space, in pixels, between the left edge of the chart and the left border of the wrapping container. Behaves like left padding for the wrapping container.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.settings.margin.left']"			
					)					
				),

				'graphrightmargin' => array(
					
					'buttonTitle' => __('Chart Margin Bottom', 'revslider-charts-addon'), 
					'title' => __('Chart Margin Bottom', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.settings.margin.right', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph margin', 'graph padding', 'graph margin right'), 
					'description' => __("Define the amount of space, in pixels, between the right edge of the chart and the right border of the wrapping container. Behaves like bottom padding for the wrapping container.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.settings.margin.right']"			
					)					
				),

				'graphleftpadding' => array(
					
					'buttonTitle' => __('Chart Graphs Padding Left', 'revslider-charts-addon'), 
					'title' => __('Chart Graphs Padding Left', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.settings.pl', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph padding', 'graph padding left'), 
					'description' => __("Define the amount of space, in pixels, between the chart's left border and the graphs it contains. Behaves like left padding inside the chart.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.settings.pl']"			
					)					
				),

				'graphrightpadding' => array(
					
					'buttonTitle' => __('Chart Graphs Padding Right', 'revslider-charts-addon'), 
					'title' => __('Chart Graphs Padding Right', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.settings.pr', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph padding', 'graph padding right'), 
					'description' => __("Define the amount of space, in pixels, between the chart's right border and the graphs it contains. Behaves like right padding inside the chart.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.settings.pr']"			
					)					
				),


				'graphchartvlines' => array(
					
					'buttonTitle' => __('Chart Vertical Lines', 'revslider-charts-addon'), 
					'title' => __('Chart Vertical Lines', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.grid.xstcolor', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph grid', 'graph grid lines', 'vertical lines'), 
					'description' => __("Set the color of the left border of the graph grid, representing the vertical / Y axis.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.grid.xstcolor']"			
					)					
				),

				'graphchartvlinewidth' => array(
					
					'buttonTitle' => __('Chart Vertical Lines', 'revslider-charts-addon'), 
					'title' => __('Chart Vertical Lines', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.grid.xstsize', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph grid', 'graph grid lines', 'vertical lines'), 
					'description' => __("Set the width, in pixels, of the left border of the graph grid, representing the vertical / Y axis.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.grid.xstsize']"			
					)					
				),

				'graphchartvmarkes' => array(
					
					'buttonTitle' => __('Chart Vertical Markers', 'revslider-charts-addon'), 
					'title' => __('Chart Vertical Markers', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.grid.xcolor', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph grid', 'graph grid markers', 'vertical marker'), 
					'description' => __("Set the color of the small value markers along the X axis / bottom border.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.grid.xcolor']"			
					)					
				),

				'graphchartvmarkerwidth' => array(
					
					'buttonTitle' => __('Chart Vertical Markers', 'revslider-charts-addon'), 
					'title' => __('Chart Vertical Markers', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.grid.xsize', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph grid', 'graph grid markers', 'vertical markers'), 
					'description' => __("Set the width, in pixels, of the small value markers along the X axis / bottom border.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.grid.xsize']"			
					)					
				),


				'graphcharthlines' => array(
					
					'buttonTitle' => __('Chart Horizontal Lines', 'revslider-charts-addon'), 
					'title' => __('Chart Horizontal Lines', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.grid.ybtcolor', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph grid', 'graph grid lines', 'horizontal lines'), 
					'description' => __("Set the color of the bottom border of the graph grid, representing the horizontal / X axis.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.grid.ybtcolor']"			
					)					
				),

				'graphcharthlinewidth' => array(
					
					'buttonTitle' => __('Chart Horizontal Lines', 'revslider-charts-addon'), 
					'title' => __('Chart Horizontal Lines', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.grid.ybtsize', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph grid', 'graph grid lines', 'horizontal lines'), 
					'description' => __("Set the width, in pixels, of the bottom border of the graph grid, representing the horizontal / X axis.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.grid.ybtsize']"			
					)					
				),

				'graphcharthmarkes' => array(
					
					'buttonTitle' => __('Chart Horizontal Markers', 'revslider-charts-addon'), 
					'title' => __('Chart Horizontal Markers', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.grid.ycolor', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph grid', 'graph grid markers', 'horizontal marker'), 
					'description' => __("Set the color of the small value markers along the Y axis / left border.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.grid.ycolor']"			
					)					
				),

				'graphcharthmarkerwidth' => array(
					
					'buttonTitle' => __('Chart Horizontal Markers', 'revslider-charts-addon'), 
					'title' => __('Chart Horizontal Markers', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.grid.ysize', 
					'keywords' => array('addon', 'addons', 'charts', 'chart height', 'graph grid', 'graph grid markers', 'horizontal markers'), 
					'description' => __("Set the width, in pixels, of the small value markers along the Y axis / left border.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.grid.ysize']"			
					)					
				),

				'labelsfontfamily' => array(
					
					'buttonTitle' => __('Chart Label Font family', 'revslider-charts-addon'), 
					'title' => __('Chart Label Font family', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.labels.font', 
					'keywords' => array('addon', 'addons', 'charts', 'chart label', 'graph label', 'label font family', 'font family'), 
					'description' => __("Set the font family for both horizontal and vertical label text.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.labels.font']"			
					)					
				),

				'labelsxtitle' => array(
					
					'buttonTitle' => __('Chart X Axis Title', 'revslider-charts-addon'), 
					'title' => __('Chart X Axis Title', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.labels.x.name', 
					'keywords' => array('addon', 'addons', 'charts', 'chart label', 'graph label', 'axis x title', 'title'), 
					'description' => __("Set the title text for the X axis - appears below the bottom border.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.labels.x.name']"			
					)					
				),
				
				'labelsytitle' => array(
					
					'buttonTitle' => __('Chart Y Axis Title', 'revslider-charts-addon'), 
					'title' => __('Chart Y Axis Title', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.labels.y.name', 
					'keywords' => array('addon', 'addons', 'charts', 'chart label', 'graph label', 'axis y title', 'title'), 
					'description' => __("Set the title text for the Y axis - appears beside the left border.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.labels.y.name']"			
					)					
				),
				

				'labelsxcolor' => array(
					
					'buttonTitle' => __('Chart Title Color', 'revslider-charts-addon'), 
					'title' => __('Chart X Axis Title Color', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.labels.x.color', 
					'keywords' => array('addon', 'addons', 'charts', 'chart label', 'graph label', 'axis x title color', 'title color'), 
					'description' => __("Set the text color for the X axis title - appears below the bottom border.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.labels.x.color']"			
					)					
				),

				'labelsycolor' => array(
					
					'buttonTitle' => __('Chart Title Color', 'revslider-charts-addon'), 
					'title' => __('Chart Y Axis Title Color', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.labels.y.color', 
					'keywords' => array('addon', 'addons', 'charts', 'chart label', 'graph label', 'axis y title color', 'title color'), 
					'description' => __("Set the text color for the Y axis title - appears beside the left border.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.labels.y.color']"			
					)					
				),

				'labelsxsize' => array(
					
					'buttonTitle' => __('Chart Title Size', 'revslider-charts-addon'), 
					'title' => __('Chart X Axis Title Size', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.labels.x.size', 
					'keywords' => array('addon', 'addons', 'charts', 'chart label', 'graph label', 'axis x title size', 'title size'), 
					'description' => __("Set the font size for the X axis title text - appears below the bottom border.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.labels.x.size']"			
					)					
				),

				'labelsysize' => array(
					
					'buttonTitle' => __('Chart Title Size', 'revslider-charts-addon'), 
					'title' => __('Chart Y Axis Title Size', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.labels.y.size', 
					'keywords' => array('addon', 'addons', 'charts', 'chart label', 'graph label', 'axis y title size', 'title size'), 
					'description' => __("Set the font size for the Y axis title text - appears beside the left border.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.labels.y.size']"			
					)					
				),

				'labelsxoffset' => array(
					
					'buttonTitle' => __('Chart Title Offset', 'revslider-charts-addon'), 
					'title' => __('Chart X Axis Title Offset', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.labels.x.xo, addOns.revslider-charts-addon.labels.x.yo', 
					'keywords' => array('addon', 'addons', 'charts', 'chart label', 'graph label', 'axis x title offset', 'title offset'), 
					'description' => __("Set the horizontal and vertical offset of the X axis title. The 'y' field sets the distance, in pixels, the label should be moved from its default top, middle or bottom aligned position. The 'x' field sets the distance, in pixels, the label should be moved from its default left, center or right aligned position.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.labels.x.xo'], *[data-r='addOns.revslider-charts-addon.labels.x.yo']"			
					)					
				),

				'labelsyoffset' => array(
					
					'buttonTitle' => __('Chart Title Offset', 'revslider-charts-addon'), 
					'title' => __('Chart Y Axis Title Offset', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.labels.y.xo, addOns.revslider-charts-addon.labels.y.yo', 
					'keywords' => array('addon', 'addons', 'charts', 'chart label', 'graph label', 'axis y title offset', 'title offset'), 
					'description' => __("Set the horizontal and vertical offset of the Y axis title. The 'y' field sets the distance, in pixels, the label should be moved from its default top, middle or bottom aligned position. The 'x' field sets the distance, in pixels, the label should be moved from its default left, center or right aligned position.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.labels.y.xo'], *[data-r='addOns.revslider-charts-addon.labels.y.yo']"			
					)					
				),


				'legendfontfamily' => array(
					
					'buttonTitle' => __('Chart Legend Font family', 'revslider-charts-addon'), 
					'title' => __('Chart Legend Font family', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.legend.font', 
					'keywords' => array('addon', 'addons', 'charts', 'chart legend', 'graph legend', 'legend font family', 'font family'), 
					'description' => __("Set the font family of the text in the legend area.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.legend.font']"			
					)					
				),

				'legendfontsize' => array(
					
					'buttonTitle' => __('Chart Legend Font size', 'revslider-charts-addon'), 
					'title' => __('Chart Legend Font size', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.legend.size', 
					'keywords' => array('addon', 'addons', 'charts', 'chart legend', 'graph legend', 'legend font size', 'font size'), 
					'description' => __("Set the font size of the text in the legend area.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.legend.size']"			
					)					
				),

				'legendbg' => array(
					
					'buttonTitle' => __('Chart Legend Background', 'revslider-charts-addon'), 
					'title' => __('Chart Legend Background', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.legend.bg', 
					'keywords' => array('addon', 'addons', 'charts', 'chart legend', 'graph legend', 'legend background'), 
					'description' => __("Set the background color of the legend area.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.legend.bg']"			
					)					
				),

				'legendcolor' => array(
					
					'buttonTitle' => __('Chart Legend Color', 'revslider-charts-addon'), 
					'title' => __('Chart Legend Color', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.legend.color', 
					'keywords' => array('addon', 'addons', 'charts', 'chart legend', 'graph legend', 'legend color'), 
					'description' => __("Set the color of the text in the legend area.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.legend.color']"			
					)					
				),
				
				'legendoffsets' => array(
					
					'buttonTitle' => __('Chart Legend Offsets', 'revslider-charts-addon'), 
					'title' => __('Chart Legend Offsets', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.legend.xo, addOns.revslider-charts-addon.legend.yo', 
					'keywords' => array('addon', 'addons', 'charts', 'chart legend', 'graph legend', 'legend offset'), 
					'description' => __("Set the horizontal and vertical offset of the legend area. The 'y' field sets the distance, in pixels, the legend area should be moved from its default top, middle or bottom aligned position. The 'x' field sets the distance, in pixels, the legend area should be moved from its default left, center or right aligned position.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.legend.xo'], *[data-r='addOns.revslider-charts-addon.legend.yo']"			
					)					
				),		

				'legendaligment' => array(
					
					'buttonTitle' => __('Chart Legend Aligment', 'revslider-charts-addon'), 
					'title' => __('Chart Legend Aligment', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.legend.align', 
					'keywords' => array('addon', 'addons', 'charts', 'chart legend', 'graph legend aligment', 'legend aligment'), 
					'description' => __("Set the aligment of the labels within the legend area. Choosing 'Horizontal' arranges the labels in a horizontal line, and choosing 'Vertical' stacks them on top of each other.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.legend.align']"			
					)					
				),

				'legendgap' => array(
					
					'buttonTitle' => __('Chart Legend Aligment', 'revslider-charts-addon'), 
					'title' => __('Chart Legend Aligment', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.legend.gap', 
					'keywords' => array('addon', 'addons', 'charts', 'chart legend', 'graph legend aligment gaps', 'legend gap'), 
					'description' => __("Set the amount of space, in pixels, between each label in the legend area. The space added will be either horizontal or vertical, depending on the setting selected under the 'Chart Legend Aligment' option.", 'revslider-charts-addon'),
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.legend.gap']"			
					)					
				),

				'legendcbg' => array(					
					'buttonTitle' => __('Chart Legend BG Reference', 'revslider-charts-addon'), 
					'title' => __('Chart Legend BG Reference', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.legend.sbg', 
					'keywords' => array('addon', 'addons', 'charts', 'chart legend', 'graph legend bg option', 'legend bg'), 
					'description' => __("Show or hide a small square next to each label in the legend area, matching the background color of the related graph.",'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.legend.sbg']"			
					)					
				),

				'legendcdp' => array(					
					'buttonTitle' => __('Chart Legend Data Point', 'revslider-charts-addon'), 
					'title' => __('Chart Legend Data Point', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.legend.dp', 
					'keywords' => array('addon', 'addons', 'charts', 'chart legend', 'graph legend data point', 'legend data point'), 
					'description' => __("Show or hide a data point symbol next to each label in the legend area, matching the data point style of the related graph.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.legend.dp']"			
					)					
				),

				'legendstroke' => array(					
					'buttonTitle' => __('Chart Legend Stroke', 'revslider-charts-addon'), 
					'title' => __('Chart Legend Stroke', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.legend.st', 
					'keywords' => array('addon', 'addons', 'charts', 'chart legend', 'graph legend stroke', 'legend stroke'), 
					'description' => __("Show or hide a small line next to each label in the legend area, matching the stroke style of the related graph.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.legend.st']"			
					)					
				),


				'valuesfont' => array(					
					'buttonTitle' => __('Chart Values Font Family', 'revslider-charts-addon'), 
					'title' => __('Chart Values Font Family', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.values.font', 
					'keywords' => array('addon', 'addons', 'charts', 'chart values', 'graph values font', 'values font family'), 
					'description' => __("Set the font family of the numerical values along both the X and Y axis of the graph.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.values.font']"			
					)					
				),


				'valuesfontcolorx' => array(					
					'buttonTitle' => __('Chart Values Font Color', 'revslider-charts-addon'), 
					'title' => __('Chart Values Font Color', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.values.x.color', 
					'keywords' => array('addon', 'addons', 'charts', 'chart values', 'graph values font color', 'values font color'), 
					'description' => __("Set the text color of the numerical values along the X axis.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.values.x.color']"			
					)					
				),

				'valuesfontcolory' => array(					
					'buttonTitle' => __('Chart Values Font Color', 'revslider-charts-addon'), 
					'title' => __('Chart Values Font Color', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.values.y.color', 
					'keywords' => array('addon', 'addons', 'charts', 'chart values', 'graph values font color', 'values font color'), 
					'description' => __("Set the text color of the numerical values along the Y axis.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.values.y.color']"			
					)					
				),

				'valuesfontsizex' => array(					
					'buttonTitle' => __('Chart Values Font Size', 'revslider-charts-addon'), 
					'title' => __('Chart Values Font size', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.values.x.size', 
					'keywords' => array('addon', 'addons', 'charts', 'chart values', 'graph values font size', 'values font size'), 
					'description' => __("Set the text size of the numerical values along the X axis.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.values.x.size']"			
					)					
				),

				'valuesfontsizey' => array(					
					'buttonTitle' => __('Chart Values Font Size', 'revslider-charts-addon'), 
					'title' => __('Chart Values Font size', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.values.y.size', 
					'keywords' => array('addon', 'addons', 'charts', 'chart values', 'graph values font size', 'values font size'), 
					'description' => __("Set the text size of the numerical values along the Y axis.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.values.y.size']"			
					)					
				),

				'valuesfontsizes' => array(					
					'buttonTitle' => __('Chart Values Font Size', 'revslider-charts-addon'), 
					'title' => __('Chart Values Font size', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.values.s.size', 
					'keywords' => array('addon', 'addons', 'charts', 'chart values', 'graph values font size', 'values font size'), 
					'description' => __("Set the text size of the labels that appear when hovering over individual data points.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.values.s.size']"			
					)					
				),

				'valuesoffsets' => array(
					
					'buttonTitle' => __('Chart Values Offsets', 'revslider-charts-addon'), 
					'title' => __('Chart Valus Offsets', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.values.x.xo, addOns.revslider-charts-addon.values.x.yo, addOns.revslider-charts-addon.values.y.xo, addOns.revslider-charts-addon.values.y.yo, addOns.revslider-charts-addon.values.s.xo, addOns.revslider-charts-addon.values.s.yo', 
					'keywords' => array('addon', 'addons', 'charts', 'chart values', 'graph values', 'values offset'), 
					'description' => __("Set the horizontal and vertical offset of the values displayed along the X axis, the Y axis, or the labels that appear when hovering over individual data points. The 'x' option offsets horizontally from the default position, and the 'y' option offsets vertically.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.values.x.xo'], *[data-r='addOns.revslider-charts-addon.values.x.yo'], *[data-r='addOns.revslider-charts-addon.values.y.xo'], *[data-r='addOns.revslider-charts-addon.values.y.yo'], *[data-r='addOns.revslider-charts-addon.values.s.xo'], *[data-r='addOns.revslider-charts-addon.values.s.yo']"			
					)					
				),		

				'valuesxrotation' => array(					
					'buttonTitle' => __('Chart Values Rotation', 'revslider-charts-addon'), 
					'title' => __('Chart Values Rotation', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.values.x.ro', 
					'keywords' => array('addon', 'addons', 'charts', 'chart values', 'graph values rotation', 'values rotation'), 
					'description' => __("Set the rotation, in degrees, of the X axis values running along the bottom of the chart. Adjusting this value can help you fix more values next to each other.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.values.x.ro']"			
					)					
				),

				'valuesxskip' => array(					
					'buttonTitle' => __('Chart every nth value on X Axis', 'revslider-charts-addon'), 
					'title' => __('Chart every nth value', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.values.x.every', 
					'keywords' => array('addon', 'addons', 'charts', 'chart values', 'graph values every nth', 'values every nth'), 
					'description' => __("Show only every nth value on the X axis. This allows a reduction in the number of values being displayed if required to improve readability.", 'revslider-charts-addon'),
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.values.x.every']"			
					)					
				),


				'valuespre' => array(					
					'buttonTitle' => __('Chart Value Prefix', 'revslider-charts-addon'), 
					'title' => __('Chart Value Prefix', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.values.x.pre, addOns.revslider-charts-addon.values.y.pre, addOns.revslider-charts-addon.values.s.pre', 
					'keywords' => array('addon', 'addons', 'charts', 'chart values', 'graph value prefix', 'value prefix'), 
					'description' => __("Set a short text prefix to display before values. Different prefixes can be set for values along the X axis, the Y axis, and the labels that appear when hovering over individual data points.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.values.x.pre'], *[data-r='addOns.revslider-charts-addon.values.y.pre'], *[data-r='addOns.revslider-charts-addon.values.s.pre']"			
					)					
				),

				'valuessuf' => array(					
					'buttonTitle' => __('Chart Value Prefix', 'revslider-charts-addon'), 
					'title' => __('Chart Value Prefix', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.values.x.suf, addOns.revslider-charts-addon.values.y.suf, addOns.revslider-charts-addon.values.s.suf', 
					'keywords' => array('addon', 'addons', 'charts', 'chart values', 'graph value suffix', 'value suffix'), 
					'description' => __("Set a short suffix to display after values, e.g. 'px', 'km/h', 'mph'. Different suffixes can be set for values along the X axis, the Y axis, and the labels that appear when hovering over individual data points.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.values.x.suf'], *[data-r='addOns.revslider-charts-addon.values.y.suf'], *[data-r='addOns.revslider-charts-addon.values.s.suf']"			
					)					
				),

				'valuesdez' => array(					
					'buttonTitle' => __('Chart Value Decimals', 'revslider-charts-addon'), 
					'title' => __('Chart Value Decimals', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.values.x.dez, addOns.revslider-charts-addon.values.y.dez, addOns.revslider-charts-addon.values.s.dez', 
					'keywords' => array('addon', 'addons', 'charts', 'chart values', 'graph value dezimals', 'value dezimals'), 
					'description' => __("Set the number of decimal places to be shown in the value.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.values.x.dez'], *[data-r='addOns.revslider-charts-addon.values.y.dez'], *[data-r='addOns.revslider-charts-addon.values.s.dez']"			
					)					
				),

				'valuesfr' => array(					
					'buttonTitle' => __('Chart Value Formatting', 'revslider-charts-addon'), 
					'title' => __('Chart Value Formatting', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.values.x.fr, addOns.revslider-charts-addon.values.y.fr, addOns.revslider-charts-addon.values.s.fr', 
					'keywords' => array('addon', 'addons', 'charts', 'chart values', 'graph value formatting', 'value formatting'), 
					'description' => __("Optionally auto format values based on the browser's local settings. For example, a currency value may be expressed with either dots or commas: €500,30 versus €500.30", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.values.x.fr'], *[data-r='addOns.revslider-charts-addon.values.y.fr'], *[data-r='addOns.revslider-charts-addon.values.s.fr']"			
					)					
				),


				'interactionsw' => array(					
					'buttonTitle' => __('Chart Interaction Marker', 'revslider-charts-addon'), 
					'title' => __('Chart Interaction Marker', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.interaction.v.size', 
					'keywords' => array('addon', 'addons', 'charts', 'chart interaction', 'interaction marker'), 
					'description' => __("Set the width, in pixels, of the dynamic vertical line marker that follows the mouse across the graph.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.interaction.v.size']"			
					)					
				),

				'interactionss' => array(					
					'buttonTitle' => __('Chart Interaction Marker', 'revslider-charts-addon'), 
					'title' => __('Chart Interaction Marker', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.interaction.v.dash', 
					'keywords' => array('addon', 'addons', 'charts', 'chart interaction', 'interaction marker'), 
					'description' => __("Define the style of the dynamic vertical line marker that follows the mouse across the graph. A value of 0 creates a solid line, and values of l or higher add gaps into the line, turning it into a dotted or dashed line. The higher the value the larger the gaps in the line.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.interaction.v.dash']"			
					)					
				),

				'interactionsc' => array(					
					'buttonTitle' => __('Chart Interaction Marker', 'revslider-charts-addon'), 
					'title' => __('Chart Interaction Marker', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.interaction.v.color', 
					'keywords' => array('addon', 'addons', 'charts', 'chart interaction', 'interaction marker'), 
					'description' => __("Set the color of the dynamic vertical line marker that follows the mouse across the graph.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.interaction.v.color']"			
					)					
				),

				'interactionsvalue' => array(					
					'buttonTitle' => __('Chart Interaction Values', 'revslider-charts-addon'), 
					'title' => __('Chart Interaction Values', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.interaction.v.usevals', 
					'keywords' => array('addon', 'addons', 'charts', 'chart interaction', 'interaction values'), 
					'description' => __("Show or hide the labels that, (unless turned off), appear when hovering over individual data points of a graph.", 'revslider-charts-addon'),
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.interaction.v.usevals']"			
					)					
				),

				'pointscale' => array(					
					'buttonTitle' => __('Chart Interaction data points', 'revslider-charts-addon'), 
					'title' => __('Chart Interaction data points', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.interaction.v.dpscale', 
					'keywords' => array('addon', 'addons', 'charts', 'chart interaction', 'interaction data points'), 
					'description' => __("Optionally show a scaling animation when hovering over individual data points of a graph.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.interaction.v.dpscale']"			
					)					
				),

				'hidedatapoint' => array(					
					'buttonTitle' => __('Chart Interaction data points', 'revslider-charts-addon'), 
					'title' => __('Chart Interaction data points', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.interaction.v.dphidden', 
					'keywords' => array('addon', 'addons', 'charts', 'chart interaction', 'interaction data points'), 
					'description' => __("Optionally hide the graph data points not currently being hovered over. Setting this option to 'ON' can help to avoid overloading a graph with too many points ", 'revslider-charts-addon'),
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.interaction.v.dphidden']"			
					)					
				),


				'interactionx' => array(					
					'buttonTitle' => __('Chart Interaction X Values', 'revslider-charts-addon'), 
					'title' => __('Chart Interaction X Values', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.interaction.v.usexval', 
					'keywords' => array('addon', 'addons', 'charts', 'chart interaction', 'interaction x values' , 'interaction dynamic values'), 
					'description' => __("Optionally show a label with the nearest X axis value when hovering over the chart. The label will appear at the bottom of the chart, along the X axis.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.interaction.v.usexval']"			
					)					
				),


				'interactionxoffset' => array(					
					'buttonTitle' => __('Chart Interaction X Values', 'revslider-charts-addon'), 
					'title' => __('Chart Interaction X Values', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.interaction.v.xo, addOns.revslider-charts-addon.interaction.v.yo', 
					'keywords' => array('addon', 'addons', 'charts', 'chart interaction', 'interaction x values' , 'interaction dynamic values'), 
					'description' => __("Set the horizontal and vertical offset of the X value label that appears at the bottom of the chart when hovering. (This label can optionally be set to show via the 'Chart Interaction X Values' toggle). The 'x' option offsets horizontally from the default position, and the 'y' option offsets vertically.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.interaction.v.xo'],*[data-r='addOns.revslider-charts-addon.interaction.v.yo']"			
					)					
				),

				'interactionxvalcolor' => array(					
					'buttonTitle' => __('Chart Interaction X Values', 'revslider-charts-addon'), 
					'title' => __('Chart Interaction X Values', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.interaction.v.fill', 
					'keywords' => array('addon', 'addons', 'charts', 'chart interaction', 'interaction x values' , 'interaction dynamic values'), 
					'description' => __("Set the background color of the X value label that appears at the bottom of the chart when hovering. (This label can optionally be set to show via the 'Chart Interaction X Values' toggle).", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.interaction.v.fill']"			
					)					
				),

				'interactionxvaltextcolor' => array(					
					'buttonTitle' => __('Chart Interaction X Values', 'revslider-charts-addon'), 
					'title' => __('Chart Interaction X Values', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.interaction.v.textcolor', 
					'keywords' => array('addon', 'addons', 'charts', 'chart interaction', 'interaction x values' , 'interaction dynamic values'), 
					'description' => __("Set the text color of the X value label that appears at the bottom of the chart when hovering. (This label can optionally be set to show via the 'Chart Interaction X Values' toggle).", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.interaction.v.textcolor']"			
					)					
				),

				'animationspeed' => array(					
					'buttonTitle' => __('Chart Animation speed', 'revslider-charts-addon'), 
					'title' => __('Chart Animation speed', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.settings.speed', 
					'keywords' => array('addon', 'addons', 'charts', 'chart animation', 'animation speed'), 
					'description' => __("Set the speed of the chart's 'IN' animation in milliseconds. This is an automatically included animation that draws each element of the chart, and is separate & in addition to the 'IN' animation of the layer itself.", 'revslider-charts-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.settings.speed']"			
					)					
				),

				'animationdelay' => array(					
					'buttonTitle' => __('Chart Animation delay', 'revslider-charts-addon'), 
					'title' => __('Chart Animation delay', 'revslider-charts-addon'),
					'helpPath' => 'addOns.revslider-charts-addon.settings.delay', 
					'keywords' => array('addon', 'addons', 'charts', 'chart animation', 'animation delay'), 
					'description' => __("Set the delay of the chart's 'IN' animation in milliseconds. This is an automatically included animation that draws each element of the chart, and is separate & in addition to the 'IN' animation of the layer itself. The delay of the chart's 'IN' animation is relative to the time at which the layer's 'IN' animation begins.", 'revslider-charts-addon'),
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/charts-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> Charts',
					'highlight' => array(						
						'dependencies' => array('layerselected::shape{{charts}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-charts-addon", 
						'scrollTo' => '#form_layerinner_revslider-charts-addon', 
						'focus' => "*[data-r='addOns.revslider-charts-addon.settings.delay']"			
					)					
				)			
			)
			
		);
		
	}

}
	
?>