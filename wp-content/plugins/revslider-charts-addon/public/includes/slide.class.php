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
	
		$true = [true, 'on', 1, '1', 'true'];
		return !in_array($val, $true);
	
	}
	
	private function isEnabled($slider) {
		
		$settings = $slider->get_params();
		if(empty($settings)) return false;
		$enabled = $this->get_val($settings, ['addOns', 'revslider-' . $this->title . '-addon', 'enable'], false);
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
				
		$addOn = $this->get_val($layer, ['addOns', 'revslider-' . $this->title . '-addon'], false);
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

		//LABELS
		$o = '';

		
		if ((isset($labels) && $labels['x']['use']==true) || (isset($labels) && $labels['y']['use']==true)) {
			$lfont = false;
			if (isset($labels) && $labels['x']['use']==true) {
				$lx = '';
				$lx .= $this->aO($labels['x']['name'],'','n');
				$lx .= $this->aO($labels['font'],'Arial','f');
				$lx .= $this->aO(RSColorpicker::get($labels['x']['color']),'#fff','c');
				$lx .= $this->aO($labels['x']['size'],12,'s');
				$lx .= $this->aO($labels['x']['v'],'bottom','v');
				$lx .= $this->aO($labels['x']['h'],'center','h');
				$lx .= $this->aO($labels['x']['xo'],0,'xo');
				$lx .= $this->aO($labels['x']['yo'],10,'yo');
				$lx .= $this->aO($labels['x']['fontWeight'],500,'fw');		
				$labels_fontweights[] = $labels['x']['fontWeight'];		
				$o .= $lx!='' ? 'data-charts-label-x="' .$lx. '" ' : '';
				$lfont = true;
			}
			if (isset($labels) && $labels['y']['use']==true) {
				$ly = '';
				$ly .= $this->aO($labels['y']['name'],'','n');
				if ($lfont!=true) $ly .= $this->aO($labels['font'],'Arial','f');
				$ly .= $this->aO(RSColorpicker::get($labels['y']['color']),'#fff','c');
				$ly .= $this->aO($labels['y']['size'],12,'s');
				$ly .= $this->aO($labels['y']['v'],'center','v');
				$ly .= $this->aO($labels['y']['h'],'left','h');
				$ly .= $this->aO($labels['y']['xo'],10,'xo');
				$ly .= $this->aO($labels['y']['yo'],0,'yo');
				$ly .= $this->aO($labels['y']['fontWeight'],500,'fw');		
				$labels_fontweights[] = $labels['y']['fontWeight'];			
				$o .= $ly!='' ? 'data-charts-label-y="' .$ly. '" ' : '';
			}				
		}

		//LEGEND
		
		
		if (isset($legend) && $legend['use']==true) {
			$lg ='';
			$lg .= $this->aO(RSColorpicker::get($legend['color']),'#fff','c');
			$lg .= $this->aO($legend['size'],12,'s');
			$lg .= $this->aO($legend['v'],'center','v');
			$lg .= $this->aO($legend['h'],'left','h');
			$lg .= $this->aO($legend['sbg'],true,'sbg');
			$lg .= $this->aO($legend['dp'],true,'dp');
			$lg .= $this->aO($legend['st'],true,'st');
			$lg .= $this->aO($legend['xo'],10,'xo');
			$lg .= $this->aO($legend['yo'],0,'yo');
			$lg .= $this->aO($legend['align'],'horizontal','a');
			$lg .= $this->aO($legend['gap'],10,'g');
			$lg .= $this->aO($legend['font'],'Arial','f');
			$lg .= $this->aO($legend['fontWeight'],500,'fw');
			$legend_fontweights[] = $legend['fontWeight'];
			$lg .= $this->aO(RSColorpicker::get($legend['bg']),'transparent','bg');				
			$o .= $lg!='' ? 'data-charts-legend="' .$lg. '" ' : '';
		}
		if (isset($values)) {
			$cvs ='';
			$cvs .= $this->aO($values['font'],'Arial','f');
			$cvs .= $this->aO($values['s']['paddingh'],5,'ph');
			$cvs .= $this->aO($values['s']['paddingv'],5,'pv');
			$cvs .= $this->aO($values['s']['pre'],'','pr'); 
			$cvs .= $this->aO($values['s']['suf'],'','su'); 
			$cvs .= $this->aO($values['s']['size'],10,'s'); 
			$cvs .= $this->aO($values['s']['direction'],'start','dir');
			$cvs .= $this->aO($values['s']['xo'],0,'xo');
			if (isset($values['s']['radius']))  $cvs .= $this->aO($values['s']['radius'],4,'rad');
			$cvs .= $this->aO($values['s']['yo'],0,'yo');
			if (isset($values['s']['fr'])) $cvs .= $this->aO($values['s']['fr'],true,'fr');
			$cvs .= $this->aO($values['s']['fontWeight'],500,'fw'); 
			$values_fontweights[] = $values['s']['fontWeight'];
			$cvs .= $this->aO($values['s']['dez'],2,'dz');				
			$o .= $cvs!='' ? 'data-charts-values-s="' .$cvs. '" ' : '';
			if (isset($values['f']) && $values['f']['use']==true) {
				$cvf ='';				
				$cvf .= $this->aO($values['f']['pre'],'','pr'); 
				$cvf .= $this->aO($values['f']['suf'],'','su'); 				
				$cvf .= $this->aO($values['f']['size'],10,'s'); 					
				$cvf .= $this->aO($values['f']['xo'],0,'xo');
				$cvf .= $this->aO($values['f']['yo'],5,'yo');						
				if (isset($values['f']['fr']))  $cvs .= $this->aO($values['f']['fr'],true,'fr');
				$cvf .= $this->aO($values['f']['fontWeight'],500,'fw'); 
				$values_fontweights[] = $values['f']['fontWeight'];				
				$cvf .= $this->aO($values['f']['dez'],2,'dz');
				$o .= $cvf!='' ? 'data-charts-values-f="' .$cvf. '" ' : '';
			}
			if ($values['x']['use']==true) {
				$cvx ='';				
				$cvx .= $this->aO($values['x']['pre'],'','pr'); 
				$cvx .= $this->aO($values['x']['suf'],'','su'); 
				$cvx .= $this->aO(RSColorpicker::get($values['x']['color']),'#fff','c'); 
				$cvx .= $this->aO($values['x']['size'],10,'s'); 					
				$cvx .= $this->aO($values['x']['xo'],0,'xo');
				$cvx .= $this->aO($values['x']['yo'],5,'yo');
				$cvx .= $this->aO($values['x']['v'],'center','v');
				$cvx .= $this->aO($values['x']['h'],'left','h');
				$cvx .= $this->aO($values['x']['ro'],0,'ro');
				if (isset($values['x']['fr']))  $cvs .= $this->aO($values['x']['fr'],true,'fr');
				$cvx .= $this->aO($values['x']['fontWeight'],500,'fw'); 
				$values_fontweights[] = $values['x']['fontWeight'];
				$cvx .= $this->aO($values['x']['every'],3,'ev');
				$cvx .= $this->aO($values['x']['dez'],2,'dz');
				$o .= $cvx!='' ? 'data-charts-values-x="' .$cvx. '" ' : '';
			}
			if ($values['y']['use']==true) {
				$cvy ='';				
				$cvy .= $this->aO($values['y']['pre'],'','pr'); 
				$cvy .= $this->aO($values['y']['suf'],'','su'); 
				$cvy .= $this->aO(RSColorpicker::get($values['y']['color']),'#fff','c'); 
				$cvy .= $this->aO($values['y']['size'],13,'s'); 					
				$cvy .= $this->aO($values['y']['xo'],0,'xo');
				$cvy .= $this->aO($values['y']['yo'],6,'yo');
				if (isset($values['y']['fr']))  $cvs .= $this->aO($values['y']['fr'],true,'fr');
				$cvy .= $this->aO($values['y']['v'],'bottom','v');
				$cvy .= $this->aO($values['y']['h'],'center','h');					
				$cvy .= $this->aO($values['y']['fontWeight'],500,'fw'); 
				$values_fontweights[] = $values['y']['fontWeight'];					
				$cvy .= $this->aO($values['y']['dez'],2,'dz');					
				$o .= $cvy!='' ? 'data-charts-values-y="' .$cvy. '" ' : '';
			}

		}

		if (isset($grid)) {				
			$gr ='';
			$gr .= $this->aO($grid['xuse'],true,'xu');
			if ($grid['xuse']==true) {
				$gr .= $this->aO(RSColorpicker::get($grid['xcolor']),'rgba(255,255,255,1)','xc');
				$gr .= $this->aO($grid['xsize'],1,'xs');
			}
			$gr .= $this->aO(RSColorpicker::get($grid['xstcolor']),'rgba(255,255,255,1)','xstc');
			$gr .= $this->aO($grid['xstsize'],1,'xsts');
			
			$gr .= $this->aO($grid['yuse'],true,'yu');
			if ($grid['yuse']==true) {
				$gr .= $this->aO(RSColorpicker::get($grid['ycolor']),'rgba(255,255,255,0.75)','yc');
				$gr .= $this->aO($grid['ysize'],1,'ys');
				$gr .= $this->aO($grid['ydivide'],6,'yd');
			}
			$gr .= $this->aO(RSColorpicker::get($grid['ybtcolor']),'rgba(255,255,255,1)','ybtc');
			$gr .= $this->aO($grid['ybtsize'],1,'ybts');				
			$o .= $gr!='' ? 'data-charts-grid="' .$gr. '" ' : '';
		}

		if (isset($interaction)) {								
			if ($interaction['v']['use']==true) {
				$iv ='';				
				$iv .= $this->aO($interaction['v']['usevals'],true,'uv'); 					
				$iv .= $this->aO($interaction['v']['usexval'],true,'uxv'); 					
				$iv .= $this->aO(RSColorpicker::get($interaction['v']['color']),'rgba(255,255,255,0.75)','c'); 
				$iv .= $this->aO(RSColorpicker::get($interaction['v']['textcolor']),'#fff','tc'); 
				$iv .= $this->aO(RSColorpicker::get($interaction['v']['fill']),'#000','fi'); 
				$iv .= $this->aO($interaction['v']['size'],1,'s'); 					
				$iv .= $this->aO($interaction['v']['dash'],0,'dsh'); 					
				$iv .= $this->aO($interaction['v']['xo'],0,'xo');
				$iv .= $this->aO($interaction['v']['yo'],15,'yo');								
				$iv .= $this->aO($interaction['v']['dphidden'],false,'dh');								
				$iv .= $this->aO($interaction['v']['dpscale'],true,'ds');								
				$o .= $iv!='' ? 'data-charts-interaction="' .$iv. '" ' : '';
			}
		}

		if (isset($settings)) {												
			$set ='';				
			$set .= $this->aO($settings['type'],'line','ty'); 					
			$set .= $this->aO($settings['gap'],5,'g'); 					
			$set .= $this->aO($settings['width'],800,'wi'); 
			$set .= $this->aO($settings['height'],500,'he'); 				
			$set .= $this->aO($isx,0,'ix'); 
			$set .= $this->aO($settings['pl'],0,'ppl'); 					
			$set .= $this->aO($settings['pr'],0,'ppr'); 					
			$set .= $this->aO($settings['speed'],0,'sp');
			$set .= $this->aO($settings['delay'],0,'dl');
			$set .= $this->aO($settings['margin']['top'],20,'mt');								
			$set .= $this->aO($settings['margin']['bottom'],50,'mb');								
			$set .= $this->aO($settings['margin']['left'],50,'ml');								
			$set .= $this->aO($settings['margin']['right'],0,'mr');								
			$o .= $set!='' ? 'data-charts-basics="' .$set. '" ' : '';
		}
		$o .= 'data-charts-index="'.implode(';',$styles['index']).'" ';
		$o .= 'data-charts-sw="'.implode(';',$styles['strokewidth']).'" ';
		$o .= 'data-charts-sd="'.implode(';',$styles['strokedash']).'" ';
		$o .= 'data-charts-sc="'.implode(';',$this->convertColors($styles['strokecolor'])).'" ';
		$o .= 'data-charts-ac="'.implode(';',$this->convertColors($styles['anchorcolor'])).'" ';
		$o .= 'data-charts-vc="'.implode(';',$this->convertColors($styles['valuecolor'])).'" ';
		if (isset($styles['valuefcolor'])) $o .= 'data-charts-vfc="'.implode(';',$this->convertColors($styles['valuefcolor'])).'" ';
		
		$o .= 'data-charts-vbg="'.implode(';',$this->convertColors($styles['valuebgcols'])).'" ';
		$o .= 'data-charts-fc="'.implode(';',$this->convertColors($styles['fillcolor'])).'" ';			
		
		$o .= 'data-charts-cvs="'.implode(';',$styles['curves']).'" ';
		$o .= 'data-charts-dp="'.implode(';',$styles['datapoint']).'" ';			
		if (!empty($altcolors)) {$o .= 'data-charts-alc=\''.$this->convertColors(json_encode($altcolors)).'\' ';}			
		// LOAD THE GOOGLE FONTS
		$fontloader = new RevSliderOutput();						
		$fontloader->set_clean_font_import($legend['font'], '', '', $legend_fontweights);
		$fontloader->set_clean_font_import($labels['font'], '', '', $values_fontweights);
		$fontloader->set_clean_font_import($values['font'], '', '', $labels_fontweights);
		
						
		echo RS_T8 . $o . " data-charts-data='" .json_encode($datas)."'\n";
			
				
	
	}
	
}
?>