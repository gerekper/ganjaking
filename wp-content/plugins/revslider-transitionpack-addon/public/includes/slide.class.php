<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2021 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsTransitionpackSlideFront extends RevSliderFunctions {
	
	private $title;
	
	public function __construct($title) {	
		$this->title = $title;	
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 3);
	}
	
	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {	
		$true = array(true, 'on', 1, '1', 'true');
		return !in_array($val, $true);	
	}

	private function isEnabled($slider) {
		
		$settings = $slider->get_params();		
		if(empty($settings)) return false;

		$pack = $this->get_val($settings, array('slideChange', 'eng'), false); 		
		if ($pack!='transitionPack') return false;						
		$effect = $this->get_val($settings, array('slideChange', 'e'), false); 
		
		$tpack = $this->get_val($settings, array('slideChange','addOns','tpack'), false);
		if(empty($tpack)) return false;
		
		return array($tpack,$effect);
	
	}
	
	
	public function write_slide_attributes($slider, $slide) {
		
		$datas = '';
		$addOn = $this->isEnabled($slide);		
		if ($addOn==false) 	return;
		
		$col = $this->get_val($addOn, array(0, 'col'), 1);
		$row = $this->get_val($addOn, array(0, 'row'), 1);				
		$sr = $this->get_val($addOn, array(0, 'sr'), '1');
		$sx = $this->get_val($addOn, array(0, 'sx'), '1');
		$sy = $this->get_val($addOn, array(0, 'sy'), '1');
		$sz = $this->get_val($addOn, array(0, 'sz'), '1');
		$rx = $this->get_val($addOn, array(0, 'rx'), 180);
		$ry = $this->get_val($addOn, array(0, 'ry'), 180);
		$rz = $this->get_val($addOn, array(0, 'rz'), 90);
		$gx = $this->get_val($addOn, array(0, 'gx'), 0);
		$gy = $this->get_val($addOn, array(0, 'gy'), 0);				
		$gz = $this->get_val($addOn, array(0, 'gz'), 250);
		$cgz = $this->get_val($addOn, array(0, 'cgz'), 250);
		$o = $this->get_val($addOn, array(0, 'o'), 0);
		$ie = $this->get_val($addOn, array(0, 'ie'), 'power2.inOut');
		$ige = $this->get_val($addOn, array(0, 'ige'), 'power2.inOut');
		
		$dplm = $this->get_val($addOn, array(0, 'dplm'), 1);
				
		
		switch($addOn[1]) {
			case 'cube':				
				if ($col!=1) $datas.='col:'.$col.';';
				if ($row!=1) $datas.='row:'.$row.';';
				if ($o!=0) $datas.='o:'.$o.';';
				if ($rx!=180) $datas.='rx:'.$rx.';';
				if ($ry!=180) $datas.='ry:'.$ry.';';
				if ($rz!=90) $datas.='rz:'.$rz.';';
				if ($sr!=1) $datas.='sr:'.$sr.';';
				if ($sx!=1) $datas.='sx:'.$sx.';';
				if ($sy!=1) $datas.='sy:'.$sy.';';
				if ($sz!=1) $datas.='sz:'.$sz.';';
				if ($gx!=0) $datas.='gx:'.$gx.';';
				if ($gy!=0) $datas.='gy:'.$gy.';';
				if ($cgz!=250) $datas.='cgz:'.$cgz.';';
				if ($ie!='power2.inOut') $datas.='ie:'.$ie.';';
				if ($ige!='power2.inOut') $datas.='ige:'.$ige.';';
				
			break;
			case 'iron':				
				if ($ie!='power2.inOut') $datas.='ie:'.$ie.';';
				if ($ige!='power2.inOut') $datas.='ige:'.$ige.';';
				
			break;
			case 'tpbasic':
				$ef = $this->get_val($addOn, array(0, 'ef'), 'map');
				$dbas = $this->get_val($addOn, array(0, 'dbas'), 0);

				if ($ie!='power2.inOut') $datas.='ie:'.$ie.';';
				if ($ef!='map' && $ef!='fadeb') {
					$datas.='ef:'.$ef.';'; 
				} else {					
					$flip = $this->get_val($addOn, array(0, 'mfl'), 0);	
					$datas.='mfl:'.$flip.';';					
				}
				
				if($ef=='overroll') {
					$dir = $this->get_val($addOn, array(0, 'dir'), 0);	
					$datas.='dir:'.$dir.';';
					$datas.='dbas:'.$dbas.';';
				} else
				if($ef=='wave') {
					$rad = $this->get_val($addOn, array(0, 'rad'), 90);	
					$w = $this->get_val($addOn, array(0, 'w'), 35);	
					$datas.='rad:'.$rad.';';
					$datas.='w:'.$w.';';
				} else
				if($ef=='mirrorcube') {
					$ref = $this->get_val($addOn, array(0, 'ref'), 0.4);	
					$flo = $this->get_val($addOn, array(0, 'flo'), 30);	
					$datas.='ref:'.$ref.';';
					$datas.='flo:'.$flo.';';
					if ($gz!=250) $datas.='gz:'.$gz.';';
				} else
				if($ef=='liquid') {
					
					$flo = $this->get_val($addOn, array(0, 'flo'), 30);
					$datas.='flo:'.$flo.';';
					$roz = $this->get_val($addOn, array(0, 'roz'), 0);
					$datas.='roz:'.$roz.';';
					
				} else
				if($ef=='waterdrop') {
					$rad = $this->get_val($addOn, array(0, 'rad'), 30);						
					$datas.='rad:'.$rad.';';
					$datas.='spd:'.$spd.';';
				} else 
				if($ef=='mosaic') {
					$x = $this->get_val($addOn, array(0, 'x'), 0);	
					$y = $this->get_val($addOn, array(0, 'y'), 0);	
					$datas.='x:'.$x.';';
					$datas.='y:'.$y.';';
				} else
				if($ef=='morph' || $ef == 'colorflow') {
					$x = $this->get_val($addOn, array(0, 'x'), 0);	
					$y = $this->get_val($addOn, array(0, 'y'), 0);	
					$datas.='x:'.$x.';';
					$datas.='y:'.$y.';';
				} else
				if($ef == 'blur'){
					$x = $this->get_val($addOn, array(0, 'x'), 0);	
					$y = $this->get_val($addOn, array(0, 'y'), 0);	
					$ox = $this->get_val($addOn, array(0, 'ox'), 50);
					$oy = $this->get_val($addOn, array(0, 'oy'), 50);
					
					$ao = $this->get_val($addOn, array(0, 'ao'), 'none');
					$roz = $this->get_val($addOn, array(0, 'roz'), 1);
					$zo = $this->get_val($addOn, array(0, 'zo'), 0);
					$zi = $this->get_val($addOn, array(0, 'zi'), 0);
					$zb = $this->get_val($addOn, array(0, 'zb'), 0);
					$zre = $this->get_val($addOn, array(0, 'zre'), 70);
					$prange = $this->get_val($addOn, array(0, 'prange'), 100);

					$datas.='prange:'.$prange.';';					
					$datas.='zo:'.$zo.';';
					$datas.='zi:'.$zi.';';
					$datas.='zb:'.$zb.';';
					$datas.='zre:'.$zre.';';
					$datas.='roz:'.$roz.';';
					$datas.='ox:'.$ox.';';
					$datas.='oy:'.$oy.';';
					$datas.='x:'.$x.';';
					$datas.='y:'.$y.';';

					if($ao !== 'none') $datas.='ao:'.$ao.';';
				} else
				if($ef=='flat') {
					$x = $this->get_val($addOn, array(0, 'x'), 0);	
					$y = $this->get_val($addOn, array(0, 'y'), 0);	
					$z = $this->get_val($addOn, array(0, 'z'), 100);
					$tlt = $this->get_val($addOn, array(0, 'tlt'), 0);	
					$prange = $this->get_val($addOn, array(0, 'prange'), 100);					
					$datas.='prange:'.$prange.';';
					$datas.='tlt:'.$tlt.';';
					$datas.='x:'.$x.';';
					$datas.='y:'.$y.';';
					$datas.='z:'.$z.';';					
										
				} else
				if($ef == 'pano') {
					$x = $this->get_val($addOn, array(0, 'x'), 0);	
					$y = $this->get_val($addOn, array(0, 'y'), 0);	
					$z = $this->get_val($addOn, array(0, 'z'), 250);	
					$tlt = $this->get_val($addOn, array(0, 'tlt'), 0);	
					$prange = $this->get_val($addOn, array(0, 'prange'), 100);
					$datas.='prange:'.$prange.';';					
					$datas.='tlt:'.$tlt.';';
					$datas.='x:'.$x.';';
					$datas.='y:'.$y.';';					
					$datas.='z:'.$z.';';
				} else
				if($ef == 'chaos') {
					$x = $this->get_val($addOn, array(0, 'x'), 0);	
					$y = $this->get_val($addOn, array(0, 'y'), 0);		
					$chm1 = $this->get_val($addOn, array(0, 'chm1'), 'random');
					$chm2 = $this->get_val($addOn, array(0, 'chm2'), 'random');
					$chm3 = $this->get_val($addOn, array(0, 'chm3'), 'random');
					$chm4 = $this->get_val($addOn, array(0, 'chm4'), 'random');
					$prange = $this->get_val($addOn, array(0, 'prange'), 100);
					$datas.='prange:'.$prange.';';
					if($chm1 != 'random')$datas.='chm1:'.$chm1.';';
					if($chm2 != 'random')$datas.='chm2:'.$chm2.';';
					if($chm3 != 'random')$datas.='chm3:'.$chm3.';';
					if($chm4 != 'random')$datas.='chm4:'.$chm4.';';
					$datas.='x:'.$x.';';
					$datas.='y:'.$y.';';		
				} else
				if($ef == 'stretch') {
					$x = $this->get_val($addOn, array(0, 'x'), 0);	
					$y = $this->get_val($addOn, array(0, 'y'), 0);		
					// if($x == 0 && $y == 0) $x = 2;									
					$stri = $this->get_val($addOn, array(0, 'stri'), 0);
					$strs = $this->get_val($addOn, array(0, 'strs'), 1);
					$strf = $this->get_val($addOn, array(0, 'strf'), 1);
					$prange = $this->get_val($addOn, array(0, 'prange'), 100);
					$datas.='prange:'.$prange.';';					
					$datas.='stri:'.$stri.';';
					$datas.='strs:'.$strs.';';
					$datas.='strf:'.$strf.';';
					$datas.='x:'.$x.';';
					$datas.='y:'.$y.';';					
				} else
				if($ef == 'skew') {
					$x = $this->get_val($addOn, array(0, 'x'), 0);	
					$y = $this->get_val($addOn, array(0, 'y'), 0);
					$sko = $this->get_val($addOn, array(0, 'sko'), 0);
					$prange = $this->get_val($addOn, array(0, 'prange'), 100);
					$datas.='prange:'.$prange.';';					
					$datas.='sko:'.$sko.';';
					$datas.='x:'.$x.';';
					$datas.='y:'.$y.';';
					
					$shx = $this->get_val($addOn, array(0, 'shx'), 0);	
					$shy = $this->get_val($addOn, array(0, 'shy'), 0);
					$shz = $this->get_val($addOn, array(0, 'shz'), 0);	
					$shr = $this->get_val($addOn, array(0, 'shr'), 0);
					$shv = $this->get_val($addOn, array(0, 'shv'), 70);

					$sh = $shx != 0 || $shy != 0 || $shz != 0 || $shr != 0;
					$datas.='sh:'.$sh.';';
					$datas.='shx:'.$shx.';';
					$datas.='shy:'.$shy.';';
					$datas.='shz:'.$shz.';';
					$datas.='shr:'.$shr.';';
					if($shv != 70) $datas.='shv:'.$shv.';';
				
				} else
				if($ef == 'perspective') {
					$ox = $this->get_val($addOn, array(0, 'ox'), 50);
					$oy = $this->get_val($addOn, array(0, 'oy'), 50);
					$pr = $this->get_val($addOn, array(0, 'pr'), 0);
					$roz = $this->get_val($addOn, array(0, 'roz'), 0);
					$prange = $this->get_val($addOn, array(0, 'prange'), 100);
					$ao = $this->get_val($addOn, array(0, 'ao'), 'none');
					$datas.='prange:'.$prange.';';					
					
					$datas.='ox:'.$ox.';';
					$datas.='oy:'.$oy.';';
					$datas.='pr:'.$pr.';';
					$datas.='roz:'.$roz.';';
					if($ao !== 'none') $datas.='ao:'.$ao.';';
				} else
				if($ef == 'spin') {
					$ox = $this->get_val($addOn, array(0, 'ox'), 50);
					$oy = $this->get_val($addOn, array(0, 'oy'), 50);
					$ao = $this->get_val($addOn, array(0, 'ao'), 'none');
					$roz = $this->get_val($addOn, array(0, 'roz'), 0);

					$z = $this->get_val($addOn, array(0, 'z'), 0);

					$prange = $this->get_val($addOn, array(0, 'prange'), 100);
					$datas.='prange:'.$prange.';';					
					
					$datas.='ox:'.$ox.';';
					$datas.='oy:'.$oy.';';
					
					$datas.='roz:'.$roz.';';				
					$datas.='z:'.$z.';';

					if($ao !== 'none') $datas.='ao:'.$ao.';';

				} else

				if($ef == 'rings') {
					$ox = $this->get_val($addOn, array(0, 'ox'), 50);
					$oy = $this->get_val($addOn, array(0, 'oy'), 50);
					$ao = $this->get_val($addOn, array(0, 'ao'), 'none');
					$roz = $this->get_val($addOn, array(0, 'roz'), 1);
					$cicl = $this->get_val($addOn, array(0, 'cicl'), 'rgba(0, 0, 0, 0.3)');
					$cish = $this->get_val($addOn, array(0, 'cish'), 0);
					$cispl = $this->get_val($addOn, array(0, 'cispl'), 4);
					$cimw = $this->get_val($addOn, array(0, 'cimw'), false);
					$cio = $this->get_val($addOn, array(0, 'cio'), 'alternate');
					$cico = $this->get_val($addOn, array(0, 'cico'), 0);
					$ciad = $this->get_val($addOn, array(0, 'ciad'), 0);

					$prange = $this->get_val($addOn, array(0, 'prange'), 100);
					$datas.='prange:'.$prange.';';					
					
					$datas.='cicl:'.$cicl.';';
					$datas.='cish:'.$cish.';';
					$datas.='cispl:'.$cispl.';';					
					$datas.='cimw:'.$cimw.';';
					$datas.='cio:'.$cio.';';
					$datas.='cico:'.$cico.';';
					$datas.='ciad:'.$ciad.';';
					
					$datas.='roz:'.$roz.';';
					$datas.='ox:'.$ox.';';
					$datas.='oy:'.$oy.';';

					if($ao !== 'none') $datas.='ao:'.$ao.';';

				} else

				if($ef == 'zoom') {
					$ox = $this->get_val($addOn, array(0, 'ox'), 50);
					$oy = $this->get_val($addOn, array(0, 'oy'), 50);
					
					$ao = $this->get_val($addOn, array(0, 'ao'), 'none');
					$roz = $this->get_val($addOn, array(0, 'roz'), 1);
					$zo = $this->get_val($addOn, array(0, 'zo'), 0);
					$zi = $this->get_val($addOn, array(0, 'zi'), 0);
					$zb = $this->get_val($addOn, array(0, 'zb'), 0);
					$zwo = $this->get_val($addOn, array(0, 'zwo'), 0);
					$zwi = $this->get_val($addOn, array(0, 'zwi'), 0);
					$zre = $this->get_val($addOn, array(0, 'zre'), 70);

					$prange = $this->get_val($addOn, array(0, 'prange'), 100);
					$datas.='prange:'.$prange.';';					
					
					$datas.='zo:'.$zo.';';
					$datas.='zi:'.$zi.';';
					$datas.='zb:'.$zb.';';
					$datas.='zwo:'.$zwo.';';
					$datas.='zwi:'.$zwi.';';
					$datas.='zre:'.$zre.';';
					
					$datas.='roz:'.$roz.';';
					$datas.='ox:'.$ox.';';
					$datas.='oy:'.$oy.';';

					if($ao !== 'none') $datas.='ao:'.$ao.';';

				} else

				if($ef=='burn') {
					$dir = $this->get_val($addOn, array(0, 'dir'), 0);	
					$datas.='dbas:'.$dbas.';';
					$datas.='dir:'.$dir.';';
				}
				else
				if($ef=='cut') {
					$dir = $this->get_val($addOn, array(0, 'dir'), 0);	
					$datas.='dbas:'.$dbas.';';
					$datas.='dir:'.$dir.';';
					$ssx = $this->get_val($addOn, array(0, 'ssx'), 66);
					$ssy = $this->get_val($addOn, array(0, 'ssy'), 66);	
					$w = $this->get_val($addOn, array(0, 'w'), 5);	
					$datas.='ssx:'.$ssx.';';
					$datas.='ssy:'.$ssy.';';
					$datas.='w:'.$w.';';
				}				
				$iny = $this->get_val($addOn, array(0, 'iny'), 1);	
				$datas.='iny:'.$iny.';';
				
				if ($dplm!=1) $datas.='dplm:'.$dplm.';';				
			break;
			case 'twist':
					if ($ie!='power2.inOut') $datas.='ie:'.$ie.';';
					$twe = $this->get_val($addOn, array(0, 'twe'), 'simple');
					$twa = $this->get_val($addOn, array(0, 'twa'), 0);
					$twv = $this->get_val($addOn, array(0, 'twv'), 230);
					$twz = $this->get_val($addOn, array(0, 'twz'), 30);
					$twd = $this->get_val($addOn, array(0, 'twd'), 'left');
					$twdi = $this->get_val($addOn, array(0, 'twdi'), 30);
					$twc = $this->get_val($addOn, array(0, 'twc'), false);
					$tws = $this->get_val($addOn, array(0, 'tws'), 'rgba(0, 0, 0, 0.7)');
					$twf = $this->get_val($addOn, array(0, 'twf'), 'rgba(0, 0, 0, 0.7)');
					$datas.='ef:cubetwist;'; 
					if ($twe!='simple') $datas.='twe:'.$twe.';';
					if ($twa!='0') $datas.='twa:'.$twa.';';
					if ($twv!='230') $datas.='twv:'.$twv.';';
					if ($twz!='30') $datas.='twz:'.$twz.';';
					if ($twd!='left') $datas.='twd:'.$twd.';';
					if ($twdi!='0.3') $datas.='twdi:'.$twdi.';';
					if ($twe == 'twistwave' && $twc == true) $datas.='twc:'.$twc.';';
					if ($tws!='rgba(0, 0, 0, 0)') $datas.='tws:'.$tws.';';
					if ($twf!='rgba(0, 0, 0, 0)') $datas.='twf:'.$twf.';';
					$datas.='rx:'.$rx.';';
					$datas.='ry:'.$ry.';';
					$datas.='rz:'.$rz.';';
				
				break;
		}

		$pp = $this->get_val($addOn, array(0, 'pp'), 'none');
		if ($pp!="none") {
			$ppbf = $this->get_val($addOn, array(0, 'ppbf'), 100);
			$ppbm = $this->get_val($addOn, array(0, 'ppbm'), 4);
			$ppba = $this->get_val($addOn, array(0, 'ppba'), 20);

			$ppga = $this->get_val($addOn, array(0, 'ppga'), 90);
			$ppgr = $this->get_val($addOn, array(0, 'ppgr'), 5);
			$ppgs = $this->get_val($addOn, array(0, 'ppgs'), 0.3);
			$ppgl = $this->get_val($addOn, array(0, 'ppgl'), 120);
			$ppbt = $this->get_val($addOn, array(0, 'ppbt'), 'motion');

			$ppfn = $this->get_val($addOn, array(0, 'ppfn'), 80);
			$ppfs = $this->get_val($addOn, array(0, 'ppfs'), 82);
			$ppfh = $this->get_val($addOn, array(0, 'ppfh'), 256);
			$ppfbw = $this->get_val($addOn, array(0, 'ppfbw'), false);

			$datas.='pp:'.$pp.';';
			if($pp=="blur") $datas.='ppbt:'.$ppbt.';';
			if ($ppbf!=100) $datas.='ppbf:'.$ppbf.';';
			if ($ppbm!=4) $datas.='ppbm:'.$ppbm.';';
			if ($ppba!=20) $datas.='ppba:'.$ppba.';';

			if ($ppga!=90) $datas.='ppga:'.$ppga.';';
			if ($ppgr!=5) $datas.='ppgr:'.$ppgr.';';
			if ($ppgs!=0.3) $datas.='ppgs:'.$ppgs.';';
			if ($ppgl!=120) $datas.='ppgl:'.$ppgl.';';

			if ($ppfn!=80) $datas.='ppfn:'.$ppfn.';';
			if ($ppfs!=82) $datas.='ppfs:'.$ppfs.';';
			if ($ppfh!=256) $datas.='ppfh:'.$ppfh.';';
			$datas.='ppfbw:'.$ppfbw.';';
		}
		
		echo " data-tpack='".$datas."'";
	}
}
?>