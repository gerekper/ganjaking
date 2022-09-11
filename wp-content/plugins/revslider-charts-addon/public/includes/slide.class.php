<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsChartsSlideFront extends RevSliderFunctions {
	
	private $title;
	
	public function __construct($title) {
		
		$this->title = $title;
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
		add_filter('revslider_putCreativeLayer', array($this, 'check_charts'), 10, 3);
	
	}
	
	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
	
		$true = array(true, 'on', 1, '1', 'true');
		return !in_array($val, $true);
	
	}
	
	private function isEnabled($slider) {
		
		$settings = $slider->get_params();
		if(empty($settings)) return false;
		$enabled = $this->get_val($settings, array('addOns', 'revslider-' . $this->title . '-addon', 'enable'), false);
		return !$this->isFalse($enabled);
	
	}
	
	// removes charts layers that may exist if the AddOn is not officially enabled
	public function check_charts($layers, $output, $static_slide) {
		
		$slider = $this->get_val($output, 'slider', false);
		if(empty($slider)) return;
		// addon enabled
		if ($this->isEnabled($slider)) return $layers;
		$ar = array();
		foreach($layers as $layer) {
			$isCharts = false;
			if(array_key_exists('subtype', $layer)) {
				$charts = $this->get_val($layer, 'subtype', false);
				$isCharts = $charts === 'charts';
			}
			if(!$isCharts) $ar[] = $layer;
		}
		return $ar;
	}

	private function aO($val,$d,$s) {
		return $val==$d ? '' : $s.':'.$val.';';
	}

	private function convertColors($a) {
		if(!empty($a) && is_array($a)){
			foreach($a as $c => $v){
				$a[$c] = RSColorpicker::get($v);				
			}
		}				
		
		return $a;
	}
	
		
	public function write_layer_attributes($layer, $slide, $slider) {
		
		// addon enabled
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;		
		$subtype = $this->get_val($layer, 'subtype', '');
		if(!$subtype || $subtype !== 'charts') return;
				
		$addOn = $this->get_val($layer, array('addOns', 'revslider-' . $this->title . '-addon'), false);
		if(!$addOn) return;
					
		$settings = $this->get_val($addOn, 'settings', array());			
		$labels = $this->get_val($addOn, 'labels', array());
		$legend = $this->get_val($addOn, 'legend', array());
		$values = $this->get_val($addOn, 'values', array());
		$interaction = $this->get_val($addOn, 'interaction', array());
		$grid = $this->get_val($addOn, 'grid', array());
		$inuse = $this->get_val($addOn, 'inuse', array());
		$styles = array();			
		$datas = array();
		$altcolors = array();

		$isx = intval($settings['isx']);
		
		$arrayfields = array('curves','datapoint','anchorcolor','valuebgcols','fillcolor','valuecolor','valuefcolor','strokecolor','strokedash','strokewidth','index','data','altcolorsuse','altcolors');
		$arrays = array();
		foreach($arrayfields as $field) {
			$arrays[$field] = $this->get_val($addOn, $field, array());
		}
		$arrays = apply_filters('revslider_charts_modify_layer_data', $arrays, $slider, $slide, $layer, $this);

		$removed = 0;

		if(!empty($inuse) && is_array($inuse)){
			foreach($inuse as $k => $v){
				if($v !== true) {						
					if ($k<$isx) $removed++;
					continue;
				}
				foreach($arrayfields as $field) {
					if (!empty($arrays[$field])) {
						if ($field=='data') {
							$i = 0;
							foreach($arrays['data'] as $row) {								
								if (!isset($datas[$i])) $datas[$i] = array();
								$datas[$i][] = $row[$k];
								$i++;
							}
						} else {
							if ($field=='altcolors') {																																				
								if (!isset($altcolors[$k])) $altcolors[$k] = array();													
								if (isset($arrays['altcolorsuse']) && $arrays['altcolorsuse'][$k]==true) {
									$altcolors[$k] = $this->convertColors($arrays['altcolors'][$k]);
								} else {
									$altcolors[$k] = array();
								}																											
							} else {				
								$styles[$field][] = $arrays[$field][$k];
							}
						}
					}
				}
			}
		}					

		//FONT WEIGHTS
		$legend_fontweights = array();
		$values_fontweights = array();
		$labels_fontweights = array();	
		
		//INDEX NEED TO BE SHIFTED
		$isx = $isx - $removed;

		//dont do anything if $datas is empty, as we do not need to write any of the other values
		if(empty($datas)) return false;

		//LABELS
		$o = '';

		
		if ($this->get_val($labels, array('x', 'use')) == true || $this->get_val($labels, array('y', 'use')) == true) {
			$lfont = false;
			if ($this->get_val($labels, array('x', 'use')) == true) {
				$lx = '';
				$lx .= $this->aO($this->get_val($labels, array('x', 'name')),'','n');
				$lx .= $this->aO($this->get_val($labels, array('font')),'Arial','f');
				$lx .= $this->aO(RSColorpicker::get($this->get_val($labels, array('x', 'color'))),'#fff','c');
				$lx .= $this->aO($this->get_val($labels, array('x', 'size')),12,'s');
				$lx .= $this->aO($this->get_val($labels, array('x', 'v')),'bottom','v');
				$lx .= $this->aO($this->get_val($labels, array('x', 'h')),'center','h');
				$lx .= $this->aO($this->get_val($labels, array('x', 'xo')),0,'xo');
				$lx .= $this->aO($this->get_val($labels, array('x', 'yo')),10,'yo');
				$lx .= $this->aO($this->get_val($labels, array('x', 'fontWeight')),500,'fw');		
				$labels_fontweights[] = $this->get_val($labels, array('x', 'fontWeight'));
				$o .= $lx!='' ? 'data-charts-label-x="' .$lx. '" ' : '';
				$lfont = true;
			}
			if ($this->get_val($labels, array('y', 'use')) == true) {
				$ly = '';
				$ly .= $this->aO($this->get_val($labels, array('y', 'name')),'','n');
				if ($lfont!=true) $ly .= $this->aO($this->get_val($labels, array('font')),'Arial','f');
				$ly .= $this->aO(RSColorpicker::get($this->get_val($labels, array('y', 'color'))),'#fff','c');
				$ly .= $this->aO($this->get_val($labels, array('y', 'size')),12,'s');
				$ly .= $this->aO($this->get_val($labels, array('y', 'v')),'center','v');
				$ly .= $this->aO($this->get_val($labels, array('y', 'h')),'left','h');
				$ly .= $this->aO($this->get_val($labels, array('y', 'xo')),10,'xo');
				$ly .= $this->aO($this->get_val($labels, array('y', 'yo')),0,'yo');
				$ly .= $this->aO($this->get_val($labels, array('y', 'fontWeight')),500,'fw');		
				$labels_fontweights[] = $this->get_val($labels, array('y', 'fontWeight'));			
				$o .= $ly!='' ? 'data-charts-label-y="' .$ly. '" ' : '';
			}				
		}

		//LEGEND
		
		
		if ($this->get_val($legend, 'use') == true) {
			$lg ='';
			$lg .= $this->aO(RSColorpicker::get($this->get_val($legend, 'color')),'#fff','c');
			$lg .= $this->aO($this->get_val($legend, 'size'),12,'s');
			$lg .= $this->aO($this->get_val($legend, 'v'),'center','v');
			$lg .= $this->aO($this->get_val($legend, 'h'),'left','h');
			$lg .= $this->aO($this->get_val($legend, 'sbg'),true,'sbg');
			$lg .= $this->aO($this->get_val($legend, 'dp'),true,'dp');
			$lg .= $this->aO($this->get_val($legend, 'st'),true,'st');
			$lg .= $this->aO($this->get_val($legend, 'xo'),10,'xo');
			$lg .= $this->aO($this->get_val($legend, 'yo'),0,'yo');
			$lg .= $this->aO($this->get_val($legend, 'align'),'horizontal','a');
			$lg .= $this->aO($this->get_val($legend, 'gap'),10,'g');
			$lg .= $this->aO($this->get_val($legend, 'font'),'Arial','f');
			$lg .= $this->aO($this->get_val($legend, 'fontWeight'),500,'fw');
			$legend_fontweights[] =$this->get_val($legend, 'fontWeight');
			$lg .= $this->aO(RSColorpicker::get($this->get_val($legend, 'bg')),'transparent','bg');
			$o .= $lg!='' ? 'data-charts-legend="' .$lg. '" ' : '';
		}
		if (isset($values)) {
			$cvs ='';
			$cvs .= $this->aO($this->get_val($values, 'font'),'Arial','f');
			$cvs .= $this->aO($this->get_val($values, array('s', 'paddingh')),5,'ph');
			$cvs .= $this->aO($this->get_val($values, array('s', 'paddingv')),5,'pv');
			$cvs .= $this->aO($this->get_val($values, array('s', 'pre')),'','pr'); 
			$cvs .= $this->aO($this->get_val($values, array('s', 'suf')),'','su'); 
			$cvs .= $this->aO($this->get_val($values, array('s', 'size')),10,'s'); 
			$cvs .= $this->aO($this->get_val($values, array('s', 'direction')),'start','dir');
			$cvs .= $this->aO($this->get_val($values, array('s', 'xo')),0,'xo');
			if ($this->get_val($values, array('s', 'radius'), false) !== false)  $cvs .= $this->aO($this->get_val($values, array('s', 'radius'), false),4,'rad');
			$cvs .= $this->aO($this->get_val($values, array('s', 'yo')),0,'yo');
			if ($this->get_val($values, array('s', 'fr')) !== '') $cvs .= $this->aO($this->get_val($values, array('s', 'fr')),true,'fr');
			$cvs .= $this->aO($this->get_val($values, array('s', 'fontWeight')),500,'fw'); 
			$values_fontweights[] = $this->get_val($values, array('s', 'fontWeight'));
			$cvs .= $this->aO($this->get_val($values, array('s', 'dez')),2,'dz');
			$o .= $cvs!='' ? 'data-charts-values-s="' .$cvs. '" ' : '';
			if ($this->get_val($values, array('f', 'use'), false) == true) {
				$cvf ='';				
				$cvf .= $this->aO($this->get_val($values, array('f', 'pre')),'','pr'); 
				$cvf .= $this->aO($this->get_val($values, array('f', 'suf')),'','su');
				$cvf .= $this->aO($this->get_val($values, array('f', 'size')),10,'s');
				$cvf .= $this->aO($this->get_val($values, array('f', 'xo')),0,'xo');
				$cvf .= $this->aO($this->get_val($values, array('f', 'yo')),5,'yo');
				if ($this->get_val($values, array('f', 'fr')) !== '')  $cvs .= $this->aO($this->get_val($values, array('f', 'fr')),true,'fr');
				$cvf .= $this->aO($this->get_val($values, array('f', 'fontWeight')),500,'fw'); 
				$values_fontweights[] = $this->get_val($values, array('f', 'fontWeight'));
				$cvf .= $this->aO($this->get_val($values, array('f', 'dez')),2,'dz');
				$o .= $cvf!='' ? 'data-charts-values-f="' .$cvf. '" ' : '';
			}
			if ($this->get_val($values, array('x', 'use'), false) == true) {
				$cvx ='';				
				$cvx .= $this->aO($this->get_val($values, array('x', 'pre')),'','pr'); 
				$cvx .= $this->aO($this->get_val($values, array('x', 'suf')),'','su'); 
				$cvx .= $this->aO(RSColorpicker::get($this->get_val($values, array('x', 'color'))),'#fff','c'); 
				$cvx .= $this->aO($this->get_val($values, array('x', 'size')),10,'s'); 					
				$cvx .= $this->aO($this->get_val($values, array('x', 'xo')),0,'xo');
				$cvx .= $this->aO($this->get_val($values, array('x', 'yo')),5,'yo');
				$cvx .= $this->aO($this->get_val($values, array('x', 'v')),'center','v');
				$cvx .= $this->aO($this->get_val($values, array('x', 'h')),'left','h');
				$cvx .= $this->aO($this->get_val($values, array('x', 'ro')),0,'ro');
				if ($this->get_val($values, array('x', 'fr')) !== '')  $cvs .= $this->aO($this->get_val($values, array('x', 'fr')),true,'fr');
				$cvx .= $this->aO($this->get_val($values, array('x', 'fontWeight')),500,'fw'); 
				$values_fontweights[] = $this->get_val($values, array('x', 'fontWeight'));
				$cvx .= $this->aO($this->get_val($values, array('x', 'every')),3,'ev');
				$cvx .= $this->aO($this->get_val($values, array('x', 'dez')),2,'dz');
				$o .= $cvx!='' ? 'data-charts-values-x="' .$cvx. '" ' : '';
			}
			if ($this->get_val($values, array('y', 'use'), false) == true) {
				$cvy ='';				
				$cvy .= $this->aO($this->get_val($values, array('y', 'pre')),'','pr'); 
				$cvy .= $this->aO($this->get_val($values, array('y', 'suf')),'','su'); 
				$cvy .= $this->aO(RSColorpicker::get($this->get_val($values, array('y', 'color'))),'#fff','c'); 
				$cvy .= $this->aO($this->get_val($values, array('y', 'size')),13,'s');
				$cvy .= $this->aO($this->get_val($values, array('y', 'xo')),0,'xo');
				$cvy .= $this->aO($this->get_val($values, array('y', 'yo')),6,'yo');
				if ($this->get_val($values, array('y', 'fr')) !== '')  $cvs .= $this->aO($this->get_val($values, array('y', 'fr')),true,'fr');
				$cvy .= $this->aO($this->get_val($values, array('y', 'v')),'bottom','v');
				$cvy .= $this->aO($this->get_val($values, array('y', 'h')),'center','h');
				$cvy .= $this->aO($this->get_val($values, array('y', 'fontWeight')),500,'fw'); 
				$values_fontweights[] = $this->get_val($values, array('y', 'fontWeight'));
				$cvy .= $this->aO($this->get_val($values, array('y', 'dez')),2,'dz');
				$o .= $cvy!='' ? 'data-charts-values-y="' .$cvy. '" ' : '';
			}

		}

		if (isset($grid)) {
			$gr ='';
			$gr .= $this->aO($this->get_val($grid, 'xuse'),true,'xu');
			if ($this->get_val($grid, 'xuse')==true) {
				$gr .= $this->aO(RSColorpicker::get($this->get_val($grid, 'xcolor')),'rgba(255,255,255,1)','xc');
				$gr .= $this->aO($this->get_val($grid, 'xsize'),1,'xs');
			}
			$gr .= $this->aO(RSColorpicker::get($this->get_val($grid, 'xstcolor')),'rgba(255,255,255,1)','xstc');
			$gr .= $this->aO($this->get_val($grid, 'xstsize'),1,'xsts');
			
			$gr .= $this->aO($this->get_val($grid, 'yuse'),true,'yu');
			if ($this->get_val($grid, 'yuse')==true) {
				$gr .= $this->aO(RSColorpicker::get($this->get_val($grid, 'ycolor')),'rgba(255,255,255,0.75)','yc');
				$gr .= $this->aO($this->get_val($grid, 'ysize'),1,'ys');
				$gr .= $this->aO($this->get_val($grid, 'ydivide'),6,'yd');
			}
			$gr .= $this->aO(RSColorpicker::get($this->get_val($grid, 'ybtcolor')),'rgba(255,255,255,1)','ybtc');
			$gr .= $this->aO($this->get_val($grid, 'ybtsize'),1,'ybts');
			$o .= $gr!='' ? 'data-charts-grid="' .$gr. '" ' : '';
		}

		if (isset($interaction)) {
			if ($this->get_val($interaction, array('v', 'use')) == true) {
				$iv ='';				
				$iv .= $this->aO($this->get_val($interaction, array('v', 'usevals')),true,'uv');
				$iv .= $this->aO($this->get_val($interaction, array('v', 'usexval')),true,'uxv');
				$iv .= $this->aO(RSColorpicker::get($this->get_val($interaction, array('v', 'color'))),'rgba(255,255,255,0.75)','c'); 
				$iv .= $this->aO(RSColorpicker::get($this->get_val($interaction, array('v', 'textcolor'))),'#fff','tc'); 
				$iv .= $this->aO(RSColorpicker::get($this->get_val($interaction, array('v', 'fill'))),'#000','fi'); 
				$iv .= $this->aO($this->get_val($interaction, array('v', 'size')),1,'s');
				$iv .= $this->aO($this->get_val($interaction, array('v', 'dash')),0,'dsh');
				$iv .= $this->aO($this->get_val($interaction, array('v', 'xo')),0,'xo');
				$iv .= $this->aO($this->get_val($interaction, array('v', 'yo')),15,'yo');
				$iv .= $this->aO($this->get_val($interaction, array('v', 'dphidden')),false,'dh');
				$iv .= $this->aO($this->get_val($interaction, array('v', 'dpscale')),true,'ds');
				$o .= $iv!='' ? 'data-charts-interaction="' .$iv. '" ' : '';
			}
		}

		if (isset($settings)) {
			$set ='';
			$set .= $this->aO($this->get_val($settings, 'type'),'line','ty');
			$set .= $this->aO($this->get_val($settings, 'gap'),5,'g');
			$set .= $this->aO($this->get_val($settings, 'width'),800,'wi'); 
			$set .= $this->aO($this->get_val($settings, 'height'),500,'he');
			$set .= $this->aO($isx,0,'ix'); 
			$set .= $this->aO($this->get_val($settings, 'pl'),0,'ppl');
			$set .= $this->aO($this->get_val($settings, 'pr'),0,'ppr');
			$set .= $this->aO($this->get_val($settings, 'speed'),0,'sp');
			$set .= $this->aO($this->get_val($settings, 'delay'),0,'dl');
			$set .= $this->aO($this->get_val($settings, array('margin', 'top')),20,'mt');
			$set .= $this->aO($this->get_val($settings, array('margin', 'bottom')),50,'mb');
			$set .= $this->aO($this->get_val($settings, array('margin', 'left')),50,'ml');
			$set .= $this->aO($this->get_val($settings, array('margin', 'right')),0,'mr');
			$o .= $set!='' ? 'data-charts-basics="' .$set. '" ' : '';
		}
		$o .= (!empty($this->get_val($styles, 'index', array()))) ? 'data-charts-index="'.implode(';',$styles['index']).'" ' : '';
		$o .= (!empty($this->get_val($styles, 'strokewidth', array()))) ? 'data-charts-sw="'.implode(';',$styles['strokewidth']).'" ' : '';
		$o .= (!empty($this->get_val($styles, 'strokedash', array()))) ? 'data-charts-sd="'.implode(';',$styles['strokedash']).'" ' : '';
		$o .= (!empty($this->get_val($styles, 'strokecolor', array()))) ? 'data-charts-sc="'.implode(';',$this->convertColors($styles['strokecolor'])).'" ' : '';
		$o .= (!empty($this->get_val($styles, 'anchorcolor', array()))) ? 'data-charts-ac="'.implode(';',$this->convertColors($styles['anchorcolor'])).'" ' : '';
		$o .= (!empty($this->get_val($styles, 'valuecolor', array()))) ? 'data-charts-vc="'.implode(';',$this->convertColors($styles['valuecolor'])).'" ' : '';
		$o .= (!empty($this->get_val($styles, 'valuefcolor', array()))) ? 'data-charts-vfc="'.implode(';',$this->convertColors($styles['valuefcolor'])).'" ' : '';
		
		$o .= (!empty($this->get_val($styles, 'valuebgcols', array()))) ? 'data-charts-vbg="'.implode(';',$this->convertColors($styles['valuebgcols'])).'" ' : '';
		$o .= (!empty($this->get_val($styles, 'fillcolor', array()))) ? 'data-charts-fc="'.implode(';',$this->convertColors($styles['fillcolor'])).'" ' : '';	
		
		$o .= (!empty($this->get_val($styles, 'curves', array()))) ? 'data-charts-cvs="'.implode(';',$styles['curves']).'" ' : '';
		$o .= (!empty($this->get_val($styles, 'datapoint', array()))) ? 'data-charts-dp="'.implode(';',$styles['datapoint']).'" ' : '';
		if (!empty($altcolors)) {$o .= 'data-charts-alc=\''.$this->convertColors(json_encode($altcolors)).'\' ';}
		// LOAD THE GOOGLE FONTS
		$fontloader = new RevSliderOutput();						
		$fontloader->set_clean_font_import($this->get_val($legend, 'font'), '', '', $legend_fontweights);
		$fontloader->set_clean_font_import($this->get_val($labels, 'font'), '', '', $values_fontweights);
		$fontloader->set_clean_font_import($this->get_val($values, 'font'), '', '', $labels_fontweights);
		
		echo RS_T8 . $o . " data-charts-data='" .json_encode($datas)."'\n";
	
	}
	
}
?>