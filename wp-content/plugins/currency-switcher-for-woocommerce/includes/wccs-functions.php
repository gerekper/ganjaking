<?php

if (! function_exists('wcc_get_post_data') ) :
	function wcc_get_post_data( $name = '' ) {
		
		if (!isset($_POST['_wccsnonce']) || ( isset($_POST['_wccsnonce']) && !wp_verify_nonce(wc_clean($_POST['_wccsnonce']), '_wccsnonce') ) ) {
			wp_die('Un Authorized!');
		}

		$post = $_POST;
		
		if (isset($post[$name]) ) { 
			return $post[$name]; 
		} else {
			return $_POST;
		}
	}
endif;

/**
 * Get the list of all woocommerce currencies.
 * 
 * You can exclude the default woocommerce currency or any other currencies.
 * 
 * @return array
 */
function wccs_get_available_currencies( $exclude = array(), $exclude_default = true ) {
	$currencies = get_woocommerce_currencies();
	
	if ($exclude_default) {
		$default = get_woocommerce_currency();
		if ($default && isset($currencies[$default])) {
			unset($currencies[$default]);
		}
	}
		
	foreach ($exclude as $code) {
		if (isset($currencies[$code])) {
			unset($currencies[$code]);
		}
	}
	
	return $currencies;
}

/**
 * Get currency label by currency code or woocommerce default currency
 * 
 * @return string
 */
function wccs_get_currency_label( $currency = null ) {
	$label = '';
	
	if (!$currency) {
		$currency = get_woocommerce_currency();
	}
	
	$currencies = get_woocommerce_currencies();
	
	if (isset($currencies[$currency])) {
		$label = $currencies[$currency];
	}
	/**
	 * Filter
	 * 
	 * @since 1.0.0
	 */
	return apply_filters('wccs_change_default_currency_label', $label);
}

/**
 * Create html for currencies list
 * 
 * @return string html with currencies info
 */
/*function wccs_get_currencies_list ( $currencies_info) { //this function is directly using in wccs-settings.php due to php sniffer errors

	$html = '';

	if ( !empty($currencies_info) && count($currencies_info) > 0) {
		
		foreach ($currencies_info as $code => $info) {
			$symbol = get_woocommerce_currency_symbol($code);
			$flags = wccs_get_all_flags();
			
			$html .= '<tr>';
			
			$html .= '<td>' . $code . '</td>';
			
			$html .= '<td><input type="text" name="wccs_currencies[' . $code . '][label]" value="';
			if ( isset($info['label'])) {
				$html .= $info['label'];
			}
			$html .= '" required></td>';
			
			$html .= '<td><input class="wccs_w_100" type="text" name="wccs_currencies[' . $code . '][rate]" value="';
			if ( isset($info['rate'])) {
				$html .= $info['rate'];
			}
			$html .= '"';
			if ( get_option('wccs_update_type', 'fixed') == 'api') {
				$html .= ' readonly';
			} else {
				$html .= ' required';
			}
			$html .= '></td>';
			
			$html .= '<td><select name="wccs_currencies[' . $code . '][format]" class="wccs_w_150">';
			$html .= '<option value="left"';
			if ( isset($info['format']) && 'left'==$info['format'] ) {
				$html .= ' selected';
			}
			$html .= '>' . __('Left', 'wccs') . '</option>';
			$html .= '<option value="right"';
			if ( isset($info['format']) && 'right'==$info['format'] ) {
				$html .= ' selected';
			}
			$html .= '>' . __('Right', 'wccs') . '</option>';
			$html .= '<option value="left_space"';
			if ( isset($info['format']) && 'left_space'==$info['format'] ) {
				$html .= ' selected';
			}
			$html .= '>' . __('Left with space', 'wccs') . '</option>';
			$html .= '<option value="right_space"';
			if ( isset($info['format']) && 'right_space'==$info['format']) {
				$html .= ' selected';
			}
			$html .= '>' . __('Right with space', 'wccs') . '</option>';                                                        
			$html .= '</select></td>';
			
			$html .= '<td><select name="wccs_currencies[' . $code . '][decimals]" class="wccs_w_50">';
			$html .= '<option value="0"';
			if ( isset($info['decimals']) && '0'==$info['decimals']) {
				$html .= ' selected';
			}
			$html .= '>' . __('0', 'wccs') . '</option>';
			$html .= '<option value="1"';
			if ( isset($info['decimals']) && '1'==$info['decimals']) {
				$html .= ' selected';
			}
			$html .= '>' . __('1', 'wccs') . '</option>';
			$html .= '<option value="2"';
			if ( ( !isset($info['decimals']) ) || ( isset($info['decimals']) && '2'==$info['decimals'] ) ) {
				$html .= ' selected';
			}
			$html .= '>' . __('2', 'wccs') . '</option>';
			$html .= '<option value="3"';
			if ( isset($info['decimals']) && '3'==$info['decimals']) {
				$html .= ' selected';
			}
			$html .= '>' . __('3', 'wccs') . '</option>';
			$html .= '<option value="4"';
			if ( isset($info['decimals']) && '4'==$info['decimals']) {
				$html .= ' selected';
			}
			$html .= '>' . __('4', 'wccs') . '</option>';
			$html .= '<option value="5"';
			if ( isset($info['decimals']) && '5'==$info['decimals']) {
				$html .= ' selected';
			}
			$html .= '>' . __('5', 'wccs') . '</option>';
			$html .= '<option value="6"';
			if ( isset($info['decimals']) && '6'==$info['decimals']) {
				$html .= ' selected';
			}
			$html .= '>' . __('6', 'wccs') . '</option>';
			$html .= '<option value="7"';
			if ( isset($info['decimals']) && '7'==$info['decimals']) {
				$html .= ' selected';
			}
			$html .= '>' . __('7', 'wccs') . '</option>';
			$html .= '<option value="8"';
			if ( isset($info['decimals']) && '8'==$info['decimals'] ) {
				$html .= ' selected';
			}
			$html .= '>' . __('8', 'wccs') . '</option>';
			$html .= '</select></td>';
			
			$html .= '<td><select class="flags" name="wccs_currencies[' . $code . '][flag]">';
			$html .= '<option value="">' . __('Choose Flag', 'wccs') . '</option>';
			
			$currency_countries = get_currency_countries ($code);

			foreach ($flags as $country => $flag) {
				
				foreach ($currency_countries as $value) {
					if ($country == $value) {
						if ( count($currency_countries) == 1 ) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						
						$html .= '<option value="' . strtolower($country) . '" ' . @$selected . ' data-prefix="';
						//$html .= "<img width='30' height='20' src='".$flag."'>";
						$html .= "<span class='wcc-flag flag-icon flag-icon-" . strtolower($country) . "'></span>";
						$html .= '"';
						if ( isset($info['flag']) && strtolower($country)==$info['flag'] ) {
							$html .= ' selected';
						}
						$html .= '> (' . $country . ')</option>';
					}                
				}
							
			}
			$html .= '</select></td>';
			
			$html .= '<td>';
			$html .= '<div class="wccs_actions">';
			$html .= '<input type="hidden" name="wccs_currencies[' . $code . '][symbol]" value="' . $symbol . '">';
			if (get_option('wccs_update_type', 'fixed') == 'api') {
				$html .= '<a href="javascript:void(0);" title="' . __('Update rate', 'wccs') . '" class="wccs_update_rate" data-code="' . $code . '"><i class="dashicons dashicons-update"></i></a>';
			}
			$html .= '<span title="' . __('Sort', 'wccs') . '" style="cursor:grab;"><i class="dashicons dashicons-move"></i></span>';
			$html .= '<a href="javascript:void(0);" title="' . __('Remove', 'wccs') . '" class="wccs_remove_currency" data-value="' . $code . '" data-label="';
			if ( isset($info['label']) ) {
				$html .= $info['label'];
			}
			$html .= '"><i class="dashicons dashicons-trash"></i></a>';
			$html .= '</div>';
			$html .= '</td>';
			
			$html .= '</tr>';
		}
	}

	return $html;
}*/

/**
 * Get all flags urls
 * 
 * @return array
 */
function wccs_get_all_flags() {
	$flags = array(
	'AF' => 'https://restcountries.eu/data/afg.svg',
	'AX' => 'https://restcountries.eu/data/ala.svg',
	'AL' => 'https://restcountries.eu/data/alb.svg',
	'DZ' => 'https://restcountries.eu/data/dza.svg',
	'AS' => 'https://restcountries.eu/data/asm.svg',
	'AD' => 'https://restcountries.eu/data/and.svg',
	'AO' => 'https://restcountries.eu/data/ago.svg',
	'AI' => 'https://restcountries.eu/data/aia.svg',
	'AQ' => 'https://restcountries.eu/data/ata.svg',
	'AG' => 'https://restcountries.eu/data/atg.svg',
	'AR' => 'https://restcountries.eu/data/arg.svg',
	'AM' => 'https://restcountries.eu/data/arm.svg',
	'AN' => WCCS_PLUGIN_PATH . 'assets/lib/flag-icon/imgs/flags/ang.png',
	'AW' => 'https://restcountries.eu/data/abw.svg',
	'AU' => 'https://restcountries.eu/data/aus.svg',
	'AT' => 'https://restcountries.eu/data/aut.svg',
	'AZ' => 'https://restcountries.eu/data/aze.svg',
	'BS' => 'https://restcountries.eu/data/bhs.svg',
	'BH' => 'https://restcountries.eu/data/bhr.svg',
	'BD' => 'https://restcountries.eu/data/bgd.svg',
	'BB' => 'https://restcountries.eu/data/brb.svg',
	'BY' => 'https://restcountries.eu/data/blr.svg',
	'BE' => 'https://restcountries.eu/data/bel.svg',
	'BZ' => 'https://restcountries.eu/data/blz.svg',
	'BJ' => 'https://restcountries.eu/data/ben.svg',
	'BM' => 'https://restcountries.eu/data/bmu.svg',
	'BT' => 'https://restcountries.eu/data/btn.svg',
	'BO' => 'https://restcountries.eu/data/bol.svg',
	'BQ' => 'https://restcountries.eu/data/bes.svg',
	'BA' => 'https://restcountries.eu/data/bih.svg',
	'BW' => 'https://restcountries.eu/data/bwa.svg',
	'BV' => 'https://restcountries.eu/data/bvt.svg',
	'BR' => 'https://restcountries.eu/data/bra.svg',
	'IO' => 'https://restcountries.eu/data/iot.svg',
	'UM' => 'https://restcountries.eu/data/umi.svg',
	'VG' => 'https://restcountries.eu/data/vgb.svg',
	'VI' => 'https://restcountries.eu/data/vir.svg',
	'BN' => 'https://restcountries.eu/data/brn.svg',
	'BG' => 'https://restcountries.eu/data/bgr.svg',
	'BF' => 'https://restcountries.eu/data/bfa.svg',
	'BI' => 'https://restcountries.eu/data/bdi.svg',
	'KH' => 'https://restcountries.eu/data/khm.svg',
	'CM' => 'https://restcountries.eu/data/cmr.svg',
	'CA' => 'https://restcountries.eu/data/can.svg',
	'CV' => 'https://restcountries.eu/data/cpv.svg',
	'KY' => 'https://restcountries.eu/data/cym.svg',
	'CF' => 'https://restcountries.eu/data/caf.svg',
	'TD' => 'https://restcountries.eu/data/tcd.svg',
	'CL' => 'https://restcountries.eu/data/chl.svg',
	'CN' => 'https://restcountries.eu/data/chn.svg',
	'CX' => 'https://restcountries.eu/data/cxr.svg',
	'CC' => 'https://restcountries.eu/data/cck.svg',
	'CO' => 'https://restcountries.eu/data/col.svg',
	'KM' => 'https://restcountries.eu/data/com.svg',
	'CG' => 'https://restcountries.eu/data/cog.svg',
	'CD' => 'https://restcountries.eu/data/cod.svg',
	'CK' => 'https://restcountries.eu/data/cok.svg',
	'CR' => 'https://restcountries.eu/data/cri.svg',
	'HR' => 'https://restcountries.eu/data/hrv.svg',
	'CU' => 'https://restcountries.eu/data/cub.svg',
	'CW' => 'https://restcountries.eu/data/cuw.svg',
	'CY' => 'https://restcountries.eu/data/cyp.svg',
	'CZ' => 'https://restcountries.eu/data/cze.svg',
	'DK' => 'https://restcountries.eu/data/dnk.svg',
	'DJ' => 'https://restcountries.eu/data/dji.svg',
	'DM' => 'https://restcountries.eu/data/dma.svg',
	'DO' => 'https://restcountries.eu/data/dom.svg',
	'EC' => 'https://restcountries.eu/data/ecu.svg',
	'EG' => 'https://restcountries.eu/data/egy.svg',
	'SV' => 'https://restcountries.eu/data/slv.svg',
	'GQ' => 'https://restcountries.eu/data/gnq.svg',
	'ER' => 'https://restcountries.eu/data/eri.svg',
	'EE' => 'https://restcountries.eu/data/est.svg',
	'ET' => 'https://restcountries.eu/data/eth.svg',
	'EU' => 'https://restcountries.eu/data/eu.svg',
	'FK' => 'https://restcountries.eu/data/flk.svg',
	'FO' => 'https://restcountries.eu/data/fro.svg',
	'FJ' => 'https://restcountries.eu/data/fji.svg',
	'FI' => 'https://restcountries.eu/data/fin.svg',
	'FR' => 'https://restcountries.eu/data/fra.svg',
	'GF' => 'https://restcountries.eu/data/guf.svg',
	'PF' => 'https://restcountries.eu/data/pyf.svg',
	'TF' => 'https://restcountries.eu/data/atf.svg',
	'GA' => 'https://restcountries.eu/data/gab.svg',
	'GM' => 'https://restcountries.eu/data/gmb.svg',
	'GE' => 'https://restcountries.eu/data/geo.svg',
	'DE' => 'https://restcountries.eu/data/deu.svg',
	'GH' => 'https://restcountries.eu/data/gha.svg',
	'GI' => 'https://restcountries.eu/data/gib.svg',
	'GR' => 'https://restcountries.eu/data/grc.svg',
	'GL' => 'https://restcountries.eu/data/grl.svg',
	'GD' => 'https://restcountries.eu/data/grd.svg',
	'GP' => 'https://restcountries.eu/data/glp.svg',
	'GU' => 'https://restcountries.eu/data/gum.svg',
	'GT' => 'https://restcountries.eu/data/gtm.svg',
	'GG' => 'https://restcountries.eu/data/ggy.svg',
	'GN' => 'https://restcountries.eu/data/gin.svg',
	'GW' => 'https://restcountries.eu/data/gnb.svg',
	'GY' => 'https://restcountries.eu/data/guy.svg',
	'HT' => 'https://restcountries.eu/data/hti.svg',
	'HM' => 'https://restcountries.eu/data/hmd.svg',
	'VA' => 'https://restcountries.eu/data/vat.svg',
	'HN' => 'https://restcountries.eu/data/hnd.svg',
	'HK' => 'https://restcountries.eu/data/hkg.svg',
	'HU' => 'https://restcountries.eu/data/hun.svg',
	'IS' => 'https://restcountries.eu/data/isl.svg',
	'IN' => 'https://restcountries.eu/data/ind.svg',
	'ID' => 'https://restcountries.eu/data/idn.svg',
	'CI' => 'https://restcountries.eu/data/civ.svg',
	'IR' => 'https://restcountries.eu/data/irn.svg',
	'IQ' => 'https://restcountries.eu/data/irq.svg',
	'IE' => 'https://restcountries.eu/data/irl.svg',
	'IM' => 'https://restcountries.eu/data/imn.svg',
	'IL' => 'https://restcountries.eu/data/isr.svg',
	'IT' => 'https://restcountries.eu/data/ita.svg',
	'JM' => 'https://restcountries.eu/data/jam.svg',
	'JP' => 'https://restcountries.eu/data/jpn.svg',
	'JE' => 'https://restcountries.eu/data/jey.svg',
	'JO' => 'https://restcountries.eu/data/jor.svg',
	'KZ' => 'https://restcountries.eu/data/kaz.svg',
	'KE' => 'https://restcountries.eu/data/ken.svg',
	'KI' => 'https://restcountries.eu/data/kir.svg',
	'KW' => 'https://restcountries.eu/data/kwt.svg',
	'KG' => 'https://restcountries.eu/data/kgz.svg',
	'LA' => 'https://restcountries.eu/data/lao.svg',
	'LV' => 'https://restcountries.eu/data/lva.svg',
	'LB' => 'https://restcountries.eu/data/lbn.svg',
	'LS' => 'https://restcountries.eu/data/lso.svg',
	'LR' => 'https://restcountries.eu/data/lbr.svg',
	'LY' => 'https://restcountries.eu/data/lby.svg',
	'LI' => 'https://restcountries.eu/data/lie.svg',
	'LT' => 'https://restcountries.eu/data/ltu.svg',
	'LU' => 'https://restcountries.eu/data/lux.svg',
	'MO' => 'https://restcountries.eu/data/mac.svg',
	'MK' => 'https://restcountries.eu/data/mkd.svg',
	'MG' => 'https://restcountries.eu/data/mdg.svg',
	'MW' => 'https://restcountries.eu/data/mwi.svg',
	'MY' => 'https://restcountries.eu/data/mys.svg',
	'MV' => 'https://restcountries.eu/data/mdv.svg',
	'ML' => 'https://restcountries.eu/data/mli.svg',
	'MT' => 'https://restcountries.eu/data/mlt.svg',
	'MH' => 'https://restcountries.eu/data/mhl.svg',
	'MQ' => 'https://restcountries.eu/data/mtq.svg',
	'MR' => 'https://restcountries.eu/data/mrt.svg',
	'MU' => 'https://restcountries.eu/data/mus.svg',
	'YT' => 'https://restcountries.eu/data/myt.svg',
	'MX' => 'https://restcountries.eu/data/mex.svg',
	'FM' => 'https://restcountries.eu/data/fsm.svg',
	'MD' => 'https://restcountries.eu/data/mda.svg',
	'MC' => 'https://restcountries.eu/data/mco.svg',
	'MN' => 'https://restcountries.eu/data/mng.svg',
	'ME' => 'https://restcountries.eu/data/mne.svg',
	'MS' => 'https://restcountries.eu/data/msr.svg',
	'MA' => 'https://restcountries.eu/data/mar.svg',
	'MZ' => 'https://restcountries.eu/data/moz.svg',
	'MM' => 'https://restcountries.eu/data/mmr.svg',
	'NA' => 'https://restcountries.eu/data/nam.svg',
	'NR' => 'https://restcountries.eu/data/nru.svg',
	'NP' => 'https://restcountries.eu/data/npl.svg',
	'NL' => 'https://restcountries.eu/data/nld.svg',
	'NC' => 'https://restcountries.eu/data/ncl.svg',
	'NZ' => 'https://restcountries.eu/data/nzl.svg',
	'NI' => 'https://restcountries.eu/data/nic.svg',
	'NE' => 'https://restcountries.eu/data/ner.svg',
	'NG' => 'https://restcountries.eu/data/nga.svg',
	'NU' => 'https://restcountries.eu/data/niu.svg',
	'NF' => 'https://restcountries.eu/data/nfk.svg',
	'KP' => 'https://restcountries.eu/data/prk.svg',
	'MP' => 'https://restcountries.eu/data/mnp.svg',
	'NO' => 'https://restcountries.eu/data/nor.svg',
	'OM' => 'https://restcountries.eu/data/omn.svg',
	'PK' => 'https://restcountries.eu/data/pak.svg',
	'PW' => 'https://restcountries.eu/data/plw.svg',
	'PS' => 'https://restcountries.eu/data/pse.svg',
	'PA' => 'https://restcountries.eu/data/pan.svg',
	'PG' => 'https://restcountries.eu/data/png.svg',
	'PY' => 'https://restcountries.eu/data/pry.svg',
	'PE' => 'https://restcountries.eu/data/per.svg',
	'PH' => 'https://restcountries.eu/data/phl.svg',
	'PN' => 'https://restcountries.eu/data/pcn.svg',
	'PL' => 'https://restcountries.eu/data/pol.svg',
	'PT' => 'https://restcountries.eu/data/prt.svg',
	'PR' => 'https://restcountries.eu/data/pri.svg',
	'QA' => 'https://restcountries.eu/data/qat.svg',
	'XK' => 'https://restcountries.eu/data/kos.svg',
	'RE' => 'https://restcountries.eu/data/reu.svg',
	'RO' => 'https://restcountries.eu/data/rou.svg',
	'RU' => 'https://restcountries.eu/data/rus.svg',
	'RW' => 'https://restcountries.eu/data/rwa.svg',
	'BL' => 'https://restcountries.eu/data/blm.svg',
	'SH' => 'https://restcountries.eu/data/shn.svg',
	'KN' => 'https://restcountries.eu/data/kna.svg',
	'LC' => 'https://restcountries.eu/data/lca.svg',
	'MF' => 'https://restcountries.eu/data/maf.svg',
	'PM' => 'https://restcountries.eu/data/spm.svg',
	'VC' => 'https://restcountries.eu/data/vct.svg',
	'WS' => 'https://restcountries.eu/data/wsm.svg',
	'SM' => 'https://restcountries.eu/data/smr.svg',
	'ST' => 'https://restcountries.eu/data/stp.svg',
	'SA' => 'https://restcountries.eu/data/sau.svg',
	'SN' => 'https://restcountries.eu/data/sen.svg',
	'RS' => 'https://restcountries.eu/data/srb.svg',
	'SC' => 'https://restcountries.eu/data/syc.svg',
	'SL' => 'https://restcountries.eu/data/sle.svg',
	'SG' => 'https://restcountries.eu/data/sgp.svg',
	'SX' => 'https://restcountries.eu/data/sxm.svg',
	'SK' => 'https://restcountries.eu/data/svk.svg',
	'SI' => 'https://restcountries.eu/data/svn.svg',
	'SB' => 'https://restcountries.eu/data/slb.svg',
	'SO' => 'https://restcountries.eu/data/som.svg',
	'ZA' => 'https://restcountries.eu/data/zaf.svg',
	'GS' => 'https://restcountries.eu/data/sgs.svg',
	'KR' => 'https://restcountries.eu/data/kor.svg',
	'SS' => 'https://restcountries.eu/data/ssd.svg',
	'ES' => 'https://restcountries.eu/data/esp.svg',
	'LK' => 'https://restcountries.eu/data/lka.svg',
	'SD' => 'https://restcountries.eu/data/sdn.svg',
	'SR' => 'https://restcountries.eu/data/sur.svg',
	'SJ' => 'https://restcountries.eu/data/sjm.svg',
	'SZ' => 'https://restcountries.eu/data/swz.svg',
	'SE' => 'https://restcountries.eu/data/swe.svg',
	'CH' => 'https://restcountries.eu/data/che.svg',
	'SY' => 'https://restcountries.eu/data/syr.svg',
	'TW' => 'https://restcountries.eu/data/twn.svg',
	'TJ' => 'https://restcountries.eu/data/tjk.svg',
	'TZ' => 'https://restcountries.eu/data/tza.svg',
	'TH' => 'https://restcountries.eu/data/tha.svg',
	'TL' => 'https://restcountries.eu/data/tls.svg',
	'TG' => 'https://restcountries.eu/data/tgo.svg',
	'TK' => 'https://restcountries.eu/data/tkl.svg',
	'TO' => 'https://restcountries.eu/data/ton.svg',
	'TT' => 'https://restcountries.eu/data/tto.svg',
	'TN' => 'https://restcountries.eu/data/tun.svg',
	'TR' => 'https://restcountries.eu/data/tur.svg',
	'TM' => 'https://restcountries.eu/data/tkm.svg',
	'TC' => 'https://restcountries.eu/data/tca.svg',
	'TV' => 'https://restcountries.eu/data/tuv.svg',
	'UG' => 'https://restcountries.eu/data/uga.svg',
	'UA' => 'https://restcountries.eu/data/ukr.svg',
	'AE' => 'https://restcountries.eu/data/are.svg',
	'GB' => 'https://restcountries.eu/data/gbr.svg',
	'US' => 'https://restcountries.eu/data/usa.svg',
	'UY' => 'https://restcountries.eu/data/ury.svg',
	'UZ' => 'https://restcountries.eu/data/uzb.svg',
	'VU' => 'https://restcountries.eu/data/vut.svg',
	'VE' => 'https://restcountries.eu/data/ven.svg',
	'VN' => 'https://restcountries.eu/data/vnm.svg',
	'WF' => 'https://restcountries.eu/data/wlf.svg',
	'EH' => 'https://restcountries.eu/data/esh.svg',
	'YE' => 'https://restcountries.eu/data/yem.svg',
	'ZM' => 'https://restcountries.eu/data/zmb.svg',
	'ZW' => 'https://restcountries.eu/data/zwe.svg',
	);
	/**
	 * Filter
	 * 
	 * @since 1.0.0
	 */
	return apply_filters('wccs_flags', $flags);
}

/**
 * Get country code by currency code
 */
if (! function_exists('get_currency_countries') ) {
	/**
	 * Get_currency_countries.
	 *
	 * 158 currencies.
	 * Three-letter currency code (ISO 4217) => Two-letter countries codes (ISO 3166-1 alpha-2).
	 */
	function get_currency_countries( $currency_code = '' ) {
		$arr = array(
		'AFN' => array( 'AF' ),
		'ALL' => array( 'AL' ),
		'DZD' => array( 'DZ' ),
		'USD' => array( 'AS', 'IO', 'GU', 'MH', 'FM', 'MP', 'PW', 'PR', 'TC', 'US', 'UM', 'VI' ),
		'EUR' => array( 'AD', 'AT', 'BE', 'CY', 'EE', 'FI', 'FR', 'GF', 'TF', 'DE', 'GR', 'GP', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'MQ', 'YT', 'MC', 'ME', 'NL', 'PT', 'RE', 'PM', 'SM', 'SK', 'SI', 'ES', 'EU' ),
		'AOA' => array( 'AO' ),
		'XCD' => array( 'AI', 'AQ', 'AG', 'DM', 'GD', 'MS', 'KN', 'LC', 'VC' ),
		'ARS' => array( 'AR' ),
		'AMD' => array( 'AM' ),
		'AWG' => array( 'AW' ),
		'AUD' => array( 'AU', 'CX', 'CC', 'HM', 'KI', 'NR', 'NF', 'TV' ),
		'AZN' => array( 'AZ' ),
		'BSD' => array( 'BS' ),
		'BHD' => array( 'BH' ),
		'BDT' => array( 'BD' ),
		'BBD' => array( 'BB' ),
		'BYR' => array( 'BY' ),
		'BYN' => array( 'BY' ),
		'BZD' => array( 'BZ' ),
		'XOF' => array( 'BJ', 'BF', 'ML', 'NE', 'SN', 'TG' ),
		'BMD' => array( 'BM' ),
		'BTN' => array( 'BT' ),
		'BOB' => array( 'BO' ),
		'BAM' => array( 'BA' ),
		'BWP' => array( 'BW' ),
		'NOK' => array( 'BV', 'NO', 'SJ' ),
		'BRL' => array( 'BR' ),
		'BND' => array( 'BN' ),
		'BGN' => array( 'BG' ),
		'BIF' => array( 'BI' ),
		'KHR' => array( 'KH' ),
		'XAF' => array( 'CM', 'CF', 'TD', 'CG', 'GQ', 'GA' ),
		'CAD' => array( 'CA' ),
		'CVE' => array( 'CV' ),
		'KYD' => array( 'KY' ),
		'CLP' => array( 'CL' ),
		'CNY' => array( 'CN' ),
		'HKD' => array( 'HK' ),
		'COP' => array( 'CO' ),
		'KMF' => array( 'KM' ),
		'CDF' => array( 'CD' ),
		'NZD' => array( 'CK', 'NZ', 'NU', 'PN', 'TK' ),
		'CRC' => array( 'CR' ),
		'HRK' => array( 'HR' ),
		'CUP' => array( 'CU' ),
		'CZK' => array( 'CZ' ),
		'DKK' => array( 'DK', 'FO', 'GL' ),
		'DJF' => array( 'DJ' ),
		'DOP' => array( 'DO' ),
		'ECS' => array( 'EC' ),
		'EGP' => array( 'EG' ),
		'SVC' => array( 'SV' ),
		'ERN' => array( 'ER' ),
		'ETB' => array( 'ET' ),
		'FKP' => array( 'FK' ),
		'FJD' => array( 'FJ' ),
		'GMD' => array( 'GM' ),
		'GEL' => array( 'GE' ),
		'GHS' => array( 'GH' ),
		'GIP' => array( 'GI' ),
		'QTQ' => array( 'GT' ),
		'GGP' => array( 'GG' ),
		'GNF' => array( 'GN' ),
		'GWP' => array( 'GW' ),
		'GYD' => array( 'GY' ),
		'HTG' => array( 'HT' ),
		'HNL' => array( 'HN' ),
		'HUF' => array( 'HU' ),
		'ISK' => array( 'IS' ),
		'INR' => array( 'IN' ),
		'IDR' => array( 'ID' ),
		'IRR' => array( 'IR' ),
		'IQD' => array( 'IQ' ),
		'GBP' => array( 'IM', 'JE', 'GS', 'GB' ),
		'ILS' => array( 'IL' ),
		'JMD' => array( 'JM' ),
		'JPY' => array( 'JP' ),
		'JOD' => array( 'JO' ),
		'KZT' => array( 'KZ' ),
		'KES' => array( 'KE' ),
		'KPW' => array( 'KP' ),
		'KRW' => array( 'KR' ),
		'KWD' => array( 'KW' ),
		'KGS' => array( 'KG' ),
		'LAK' => array( 'LA' ),
		'LBP' => array( 'LB' ),
		'LSL' => array( 'LS' ),
		'LRD' => array( 'LR' ),
		'LYD' => array( 'LY' ),
		'CHF' => array( 'LI', 'CH' ),
		'MKD' => array( 'MK' ),
		'MGF' => array( 'MG' ),
		'MWK' => array( 'MW' ),
		'MYR' => array( 'MY' ),
		'MVR' => array( 'MV' ),
		'MRO' => array( 'MR' ),
		'MUR' => array( 'MU' ),
		'MXN' => array( 'MX' ),
		'MDL' => array( 'MD' ),
		'MNT' => array( 'MN' ),
		'MAD' => array( 'MA', 'EH' ),
		'MZN' => array( 'MZ' ),
		'MMK' => array( 'MM' ),
		'NAD' => array( 'NA' ),
		'NPR' => array( 'NP' ),
		'ANG' => array( 'AN' ),
		'XPF' => array( 'NC', 'WF' ),
		'NIO' => array( 'NI' ),
		'NGN' => array( 'NG' ),
		'OMR' => array( 'OM' ),
		'PKR' => array( 'PK' ),
		'PAB' => array( 'PA' ),
		'PGK' => array( 'PG' ),
		'PYG' => array( 'PY' ),
		'PEN' => array( 'PE' ),
		'PHP' => array( 'PH' ),
		'PLN' => array( 'PL' ),
		'QAR' => array( 'QA' ),
		'RON' => array( 'RO' ),
		'RUB' => array( 'RU' ),
		'RWF' => array( 'RW' ),
		'SHP' => array( 'SH' ),
		'WST' => array( 'WS' ),
		'STD' => array( 'ST' ),
		'SAR' => array( 'SA' ),
		'RSD' => array( 'RS' ),
		'SCR' => array( 'SC' ),
		'SLL' => array( 'SL' ),
		'SGD' => array( 'SG' ),
		'SBD' => array( 'SB' ),
		'SOS' => array( 'SO' ),
		'ZAR' => array( 'ZA' ),
		'SSP' => array( 'SS' ),
		'LKR' => array( 'LK' ),
		'SDG' => array( 'SD' ),
		'SRD' => array( 'SR' ),
		'SZL' => array( 'SZ' ),
		'SEK' => array( 'SE' ),
		'SYP' => array( 'SY' ),
		'TWD' => array( 'TW' ),
		'TJS' => array( 'TJ' ),
		'TZS' => array( 'TZ' ),
		'THB' => array( 'TH' ),
		'TOP' => array( 'TO' ),
		'TTD' => array( 'TT' ),
		'TND' => array( 'TN' ),
		'TRY' => array( 'TR' ),
		'TMT' => array( 'TM' ),
		'UGX' => array( 'UG' ),
		'UAH' => array( 'UA' ),
		'AED' => array( 'AE' ),
		'UYU' => array( 'UY' ),
		'UZS' => array( 'UZ' ),
		'VUV' => array( 'VU' ),
		'VEF' => array( 'VE' ),
		'VND' => array( 'VN' ),
		'YER' => array( 'YE' ),
		'ZMW' => array( 'ZM' ),
		'ZWD' => array( 'ZW' ),
		);
		
		if (! empty(trim($currency_code)) ) {
			return isset($arr[$currency_code]) ? $arr[$currency_code] : '';
		}
	}
}

/**
 * This function gets exchange rate for currency (US dollar based) giving currency code using "Open Exchange Rates API"
 */
function wccs_get_exchange_rates( $symbols ) {
	if (get_option('wccs_oer_api_key', '') ) {
		global $WCCS;

		$app_id = get_option('wccs_oer_api_key');
		$oxr_url = 'https://openexchangerates.org/api/latest.json?app_id=' . $app_id;
				
		$base = $WCCS->wccs_get_default_currency();
		
		if ($base) {
			$oxr_url .= '&base=' . $base;
		}

		if ($symbols) {
			$oxr_url .= '&symbols=' . $symbols;
		}

		// Open CURL session:
		$curl = curl_init($oxr_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		// Get the data:
		$json = curl_exec($curl);
		curl_close($curl);

		// Decode JSON response:
		$oxr_latest = json_decode($json, true);
		return $oxr_latest;
	}

	return array();
}

function wccs_get_email_body( $type, $args = array() ) {
	$message = '';
	
	switch ($type) {
		case 'currency_update':
			if (isset($args['changed']) && count($args['changed'])) {
				$message .= 'Dear admin,<br/>';
				$message .= 'This email was sent to you to let you know which currencies rates were updated.';
				$message .= '<table>';
				$message .= '<thead>';
				$message .= '<tr>';
				$message .= '<th>' . __('Currency', 'wccs') . '</th><th>' . __('New Rate', 'wccs') . '</th>';
				$message .= '</tr>';
				$message .= '</thead>';
				$message .= '<tbody>';
				
				foreach ($args['changed'] as $label => $rate) {
					$message .= '<tr>';
					$message .= '<td>' . $label . '</td><td>' . $rate . '</td>';
					$message .= '</tr>';
				}

				$message .= '</tbody>';
				$message .= '<table>';
			}
			break;
	}
	
	return $message;
}
