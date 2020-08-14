<?php
/*
 * For customize carriers list parameters and be safe about the plugin updates, avoiding loosing your custom values,
 * create a file named yith-carriers-parameters.php on root folder of the plugin and fill it as the following example :
 * <?php
*
 *  Overwrite $carriers_list array standard values
 * Add your parameter as key and modify
 *
$carriers_parameter = array(
	'TNT' => array( // use the same key as the $carriers_list array
		'track_url' => 'http://www.tnt.com/webtracker/tracking.do?cons=[TRACK_CODE]&{CHANGED_VALUE}trackType=CON&saveCons=Y' // set your custom value
	),
	'DHL'   => array(
			'name'      => 'DHL',
			'track_url' => 'http://www.dhl.com/content/g0/en/{CHANGED_VALUE}/tracking.shtml?brand=DHL&AWB=[TRACK_CODE]'
		)
);

In this way the carrier parameter will be overwritten by this custom values
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Carriers' ) ) {
	/**
	 * Implements features of Yit WooCommerce Order Tracking
	 *
	 * @class   Carriers
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class Carriers {
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {
		}


		public function get_carrier_list() {
			$carriers = array(

				'A1_INT'            => array(
					'name'      => 'A-1 International',
					'track_url' => 'http://www.aoneonline.com/pages/customers/shiptrack.php?tracking_number=[TRACK_CODE]',
				),
				'AFL'               => array(
					'name'      => 'AFL Logistics Limited',
					'track_url' => 'http://184.168.75.121:82/TblTrackandTrace.aspx?ID=1&cat=[TRACK_CODE]',
				),
				'AFGHAN'            => array(
					'name'      => 'Afghan POST',
					'track_url' => 'http://track.afghanpost.gov.af/index.php?ID=[TRACK_CODE]&Submit=Submit',
				),
				'ANPOST_IRELAND'    => array(
					'name'      => 'AN POST IRELAND',
					'track_url' => 'https://track.anpost.ie/TrackingResults.aspx?rtt=0&trackcode=[TRACK_CODE]',
				),
				'ARAMEX'            => array(
					'name'      => 'Aramex',
					'track_url' => 'http://www.aramex.com/track_results_multiple.aspx?ShipmentNumber=[TRACK_CODE]',
				),
				'AUSTRALIA_POST'    => array(
					'name'      => 'Australia Post',
					'track_url' => 'http://auspost.com.au/track/display.asp?type=article&id=[TRACK_CODE]',
				),
				'BRT'               => array(
					'name'      => 'BRT Courier Express',
					'track_url' => 'https://vas.brt.it/vas/sped_det_show.hsm?referer=sped_numspe_par.htm&ChiSono=[TRACK_CODE]&ClienteMittente=&DataInizio=&DataFine=&RicercaChiSono=Ricerca',
				),
				'BRT_WITH_PACKAGE_NUMBER'   => array(
					'name'      => 'BRT Courier Express (with package number)',
					'track_url' => 'https://vas.brt.it/vas/sped_det_show.hsm?referer=sped_numspe_par.htm&Nspediz=[TRACK_CODE]&RicercaNumeroSpedizione=Ricerca',
				),
				'BLAZEFLASH'        => array(
					'name'      => 'BlazeFlash',
					'track_url' => 'http://www.blazeflash.net/trackdetail.aspx?awbno=[TRACK_CODE]',
				),
				'BLUE_DART'         => array(
					'name'      => 'Blue Dart',
					'track_url' => 'http://www.bluedart.com/servlet/RoutingServlet?handler=tnt&action=awbquery&awb=awb&numbers=[TRACK_CODE]',
				),
				'BPOST'             => array(
					'name'      => 'Bpost',
					'track_url' => 'https://track.bpost.be/btr/web/#/search?itemCode=[TRACK_CODE]&lang=en',

				),
				'CEVA'              => array(
					'name'      => 'CEVA',
					'track_url' => 'http://www.cevalogistics.com/en-US/toolsresources/Pages/CEVATrak.aspx?sv=[TRACK_CODE]',
				),
				'CANADA_POST'       => array(
					'name'      => 'Canada POST',
					'track_url' => 'https://www.canadapost.ca/cpotools/apps/track/personal/findByTrackNumber?trackingNumber=[TRACK_CODE]&LOCALE=en',
				),
				'CHINA_POST'        => array(
					'name'      => 'China POST',
					'track_url' => 'http://intmail.183.com.cn/item/itemStatusQuery.do?lan=0&itemNo=[TRACK_CODE]',
				),
				'CART2INDIA'        => array(
					'name'      => 'Chronos Couriers',
					'track_url' => 'http://chronoscouriers.com/popup/scr_popup_trak_shipment.php?shipmentId=[TRACK_CODE]',
				),
				/* Dismissed?
				 'CITY_LINK'        => array (
					'name'      => 'City link',
					'track_url' => 'http://www.city-link.co.uk/consignments/routing.php?parcel_ref_num=[TRACK_CODE]',
				),*/
				'CITY_LINK_MAL'     => array(
					'name'      => 'City Link Malaysia',
					'track_url' => 'https://www.tracking.my/citylink/[TRACK_CODE]',
				),
				'COLLECTPLUS'       => array(
					'name'      => 'Collect+',
					'track_url' => 'https://www.collectplus.co.uk/track/[TRACK_CODE]',
				),
				'TRACK_TRACE'       => array(
					'name'      => 'Track & Trace by CourierPost',
					'track_url' => 'http://trackandtrace.courierpost.co.nz/search/[TRACK_CODE]',
				),
				'DHL'               => array(
					'name'      => 'DHL',
					'track_url' => 'http://www.dhl.com/content/g0/en/express/tracking.shtml?brand=DHL&AWB=[TRACK_CODE]',
				),
				'DHL_DE'            => array(
					'name'      => 'DHL Germany',
					'track_url' => 'http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc=[TRACK_CODE]&rfn=&extendedSearch=true',
				),
				'DPD_GERMANY'               => array(
					'name'      => 'DPD Germany',
					'track_url' => 'https://tracking.dpd.de/cgi-bin/delistrack?pknr=[TRACK_CODE]&typ=1&lang=en',
				),
				'DPD_NL'            => array(
					'name'      => 'DPD Netherlands',
					'track_url' => 'https://tracking.dpd.de/status/nl_NL/parcel/[TRACK_CODE]',
				),
//				'DYNAMEX'           => array(
//					'name'      => 'Dynamex',
//					'track_url' => 'https://direct.dynamex.com/dxnow5/track/externalTracking.jsp?ctl=[TRACK_CODE]',
//				),
				'ELTA'              => array(
					'name'      => 'Elta Hellenic Post',
					'track_url' => 'https://www.elta.gr/el-gr/tabid/93/?qc=[TRACK_CODE]',
				),
                'ELTA_DOOR_TO_DOOR'              => array(
                    'name'      => 'Elta Courier Door to Door',
                    'track_url' => 'https://www.elta-courier.gr/search?br=[TRACK_CODE]',
                ),
				'ENSENDA'           => array(
					'name'      => 'Ensenda',
					'track_url' => 'http://www.ensenda.com/content/track-shipment?trackingNumber=[TRACK_CODE]&TRACKING_SEND=GO',
				),
				'FEDEX'             => array(
					'name'      => 'FEDEX',
					'track_url' => 'https://www.fedex.com/apps/fedextrack/?action=track&tracknumbers=[TRACK_CODE]&locale=en_US&cntry_code=us',
				),
				'SUPASWIFT_FEDEX'   => array(
					'name'      => 'Supaswift - Fedex',
					'track_url' => 'https://www.supaswift.com/fx/PublicTracking/tabid/94/waybill/[TRACK_CODE]/Default.aspx',
				),
				'FASTWAY'           => array(
					'name'      => 'Fastway.co.nz',
					'track_url' => 'http://fastway.co.nz/courier-services/track-your-parcel?l=[TRACK_CODE]',
				),
				'FASTWAY_CO_ZA'     => array(
					'name'      => 'Fastway.co.za',
					'track_url' => 'http://www.fastway.co.za/our-services/track-your-parcel?l=[TRACK_CODE]',
				),
				'F_FLIGHT'          => array(
					'name'      => 'First Flight',
					'track_url' => 'http://firstflight.net:8081/single-web-tracking/singleTracking.do?consignmentNo=[TRACK_CODE]',
				),
				'FLYKING'           => array(
					'name'      => 'FlyKing',
					'track_url' => 'http://www.flykingonline.com/WebFCS/cnotequery.aspx?cnoteno=[TRACK_CODE]',
				),
				'GATI'              => array(
					'name'      => 'GATI',
					'track_url' => 'https://track.aftership.com/gati-kwe/[TRACK_CODE]',
				),
				'GD_EX'             => array(
					'name'      => 'GD Express',
					'track_url' => 'https://www.tracking.my/gdex/[TRACK_CODE]',
				),
				'HERMES'            => array(
					'name'      => 'Hermes',
					'track_url' => 'https://www.hermes-europe.co.uk/tracker.html?trackingNumber=[TRACK_CODE][0]&Postcode=[TRACK_CODE][1]',
				),
				'YODEL'             => array(
					'name'      => 'YODEL DIRECT',
					'track_url' => 'https://www.yodel.co.uk/tracking/[TRACK_CODE]',
				),
				'HONGKONG'          => array(
					'name'      => 'HongKong POST - 香港郵政',
					'track_url' => 'http://www.hongkongpost.hk/tc/mail_tracking/index.html',
				),
                'HONGKONG_POST_ENG'          => array(
                    'name'      => 'Hong Kong Post (Eng)',
                    'track_url' => 'https://www.hongkongpost.hk/en/mail_tracking/index.html',
                ),
				'ICC'               => array(
					'name'      => 'ICC Worldwide',
					'track_url' => 'http://www.iccworld.com/track.asp?txtawbno=[TRACK_CODE]',
				),
				'INDIA_POST'        => array(
					'name'      => 'India POST (from AfterShip)',
					//'track_url' => 'http://www.indiapost.gov.in?articlenumber=[TRACK_CODE]',
					'track_url' => 'https://track.aftership.com/india-post/[TRACK_CODE]',
				),
				'INTERLINK'         => array(
					'name'      => 'InterLink Express',
					'track_url' => 'http://www.interlinkexpress.com/tracking/trackingSearch.do?search.searchType=0&appmode=guest&search.parcelNumber=[TRACK_CODE]',
				),
				'JNE'               => array(
					'name'      => 'JNE',
					'track_url' => 'http://www.jne.co.id/index.php?mib=tracking.detail&awb=[TRACK_CODE]',
				),
				'JAPAN_POST'        => array(
					'name'      => 'Japan Post (ゆうパケット)',
					'track_url' => 'http://tracking.post.japanpost.jp/service/singleSearch.do?searchKind=S004&locale=en&reqCodeNo1=[TRACK_CODE]&x=16&y=15',
				),
				'LAPOSTE'           => array(
					'name'      => 'La Poste',
					'track_url' => 'https://www.laposte.fr/particulier/outils/suivre-vos-envois?code=[TRACK_CODE]',
				),
				'LASERSHIP'         => array(
					'name'      => 'LaserShip',
					'track_url' => 'http://www.lasership.com/track.php?track_number_input=[TRACK_CODE]&Submit=Track',
				),
				'MAGYAR_POSTA'      => array(
					'name'      => 'Magyar posta',
					'track_url' => 'https://www.posta.hu/nyomkovetes/nyitooldal?searchvalue=[TRACK_CODE]',
				),
				'NAPAREX'           => array(
					'name'      => 'NAPAREX',
					'track_url' => 'https://xcel.naparex.com/orders/WebForm/OrderTracking.aspx?OrderTrackingID=[TRACK_CODE]',
				),
				'NZ_COURIERS'       => array(
					'name'      => 'New Zealand Couriers',
					'track_url' => 'http://www.nzcouriers.co.nz/nzc/servlet/ITNG_TAndTServlet?page=1&VCCA=Enabled&Key_Type=Ticket&product_code=[TRACK_CODE][0]&serial_number=[TRACK_CODE][1]',
				),
				'NZ_POST'           => array(
					'name'      => 'New Zealand Post',
					'track_url' => 'http://www.nzpost.co.nz/tools/tracking?trackid=[TRACK_CODE]',
				),
				'OM_INTERNATIONAL'  => array(
					'name'      => 'OM International Courier & Cargo',
					'track_url' => 'http://track.omintl.net/tracking.aspx?txtawbno=[TRACK_CODE]&submit=Submit',
				),
				'ONTRAC'            => array(
					'name'      => 'OnTrac',
					'track_url' => 'http://www.ontrac.com/trackingres.asp?tracking_number=[TRACK_CODE]&x=11&y=6',
				),
				'ORBIT'             => array(
					'name'      => 'Orbit Worldwide express',
					'track_url' => 'http://www.orbitexp.com/tools/showTrack.asp?awbnoMul=[TRACK_CODE]',
				),
				'OCS'               => array(
					'name'      => 'OCS Worlwide',
					'track_url' => 'https://www.ocsworldwide.co.uk/Tracking.aspx?cwb=[TRACK_CODE]',
				),
				'POST_LT'           => array(
					'name'      => 'Lietuvos pastas',
					'track_url' => 'https://www.post.lt/siuntu-sekimas',
				),
				'PERUPOST'          => array(
					'name'      => 'PERU POST',
					'track_url' => 'http://clientes.serpost.com.pe/prj_tracking/seguimientolinea.aspx?txtTracking=[TRACK_CODE]',
				),
				'POS_INDONESIA'     => array(
					'name'      => 'POS Indonesia',
					'track_url' => 'http://www.posindonesia.co.id/addons/Lacak-Kiriman/modules/v_getlist.php?q=showData001&kiriman=[TRACK_CODE]',
				),
				'POS_MALAYSIA'      => array(
					'name'      => 'POS Malaysia',
					'track_url' => 'http://pos.com.my/trackandtrace/trackandtrace/?trackNo=[TRACK_CODE]',
				),
				'POST_NL'           => array(
					'name'      => 'POST NL',
					'track_url' => 'https://jouw.postnl.nl/#!/track-en-trace/[TRACK_CODE]/NL/[TRACK_POSTCODE]',
				),
				'POST_OFF_UK'       => array(
					'name'      => 'POST Office UK',
					'track_url' => 'http://www2.postoffice.co.uk/track-trace?track_id=[TRACK_CODE]&op=Track',
				),
				'PAK_POST'          => array(
					'name'      => 'Pakistan POST',
					'track_url' => 'http://ep.gov.pk/track.asp?textfield=[TRACK_CODE]',
				),
				'PARCEL2GO'         => array(
					'name'      => 'Parcel2Go',
					'track_url' => 'https://www1.parcel2go.com/tracking/[TRACK_CODE]',
				),
				'PARCELFORCE'       => array(
					'name'      => 'Parcel Force',
					'track_url' => 'http://www.parcelforce.com/track-trace?parcel_tracking_number=[TRACK_CODE]',
					'form'      => '<form name="PARCELFORCE" target="_blank" method="post" action="http://www.parcelforce.com/track-trace">
										<input name="tnt_time_token" type="hidden" value="jVgxbKkN5RWxJV1AMXEJpTbpDHkrGNPZGWMviDB3IvQ=">
										<input name="form_id" type="hidden" value="track_and_trace_form">
										<input name="parcel_tracking_number" type="hidden" value="[TRACK_CODE]">
									</form>',
				),
				'PARCELLINK'        => array(
					'name'      => 'Parcel link',
					'track_url' => 'http://www.parcel-link.co.uk/track-and-trace.php?consignment=[TRACK_CODE]',
				),
				'POS_LAJU'          => array(
					'name'      => 'Pos Laju National Courier',
					'track_url' => 'https://track.pos.com.my/postal-services/quick-access/?track-trace',
					'form'      => '<form name="POS_LAJU" target="_blank" method="post" action="https://track.pos.com.my/postal-services/quick-access/?track-trace/">
										<input name="hvfromheader03" type="hidden" value="0=">
										<input name="hvtrackNoHeader03" type="hidden" value="">
										<input name="trackingNo03" type="hidden" value="[TRACK_CODE]">
									</form>'
				),
				'POST_DAN'          => array(
					'name'      => 'Post Danmark',
					'track_url' => 'http://www.postdanmark.dk/tracktrace/TrackTrace.do?i_lang=INE&i_stregkode=[TRACK_CODE]',
				),
				'POSTEN_NORWAY'     => array(
					'name'      => 'Posten Norway',
					'track_url' => 'https://sporing.posten.no/sporing.html?q=[TRACK_CODE]',
				),
				'POSTEN_SWEDEN'     => array(
					'name'      => 'PostNord Sweden',
					'track_url' => 'http://www.postnord.se/verktyg/sok/spara-brev-paket-och-pall#dynamicloading=true&shipmentid=[TRACK_CODE]',
				),
				'POST_SWI'          => array(
					'name'      => 'Post Switzerland',
					'track_url' => 'http://www.post.ch/swisspost-tracking?formattedParcelCodes=[TRACK_CODE]&p_language=de',
				),
				'TPC_INDIA'         => array(
					'name'      => 'The Professional Courier - India',
					'track_url' => 'http://www.tpcindia.com/Tracking2014.aspx?id=[TRACK_CODE]&type=0&service=0',
				),
				'PUROLATOR'         => array(
					'name'      => 'Purolator',
					'track_url' => 'https://www.purolator.com/en/app-tracker.page?pins=[TRACK_CODE]',
				),
				'POST_ROMANA'       => array(
					'name'      => 'Poșta Română',
					'track_url' => 'https://track.aftership.com/posta-romana/[TRACK_CODE]?',
				),
				'REDPACK_MEXICO'    => array(
					'name'      => 'Redpack Mexico',
					'track_url' => 'https://track.aftership.com/mexico-redpack/[TRACK_CODE]?'
				),
				'ROYAL_MAIL'        => array(
					'name'      => 'Royal Mail',
					'track_url' => 'http://www.royalmail.com/portal/rm/track?trackNumber=[TRACK_CODE]',
				),
				'SM_COURIERS'       => array(
					'name'      => 'SM Couriers',
					'track_url' => 'http://www.smcouriers.com/YourShipmentDetails.aspx?reqCode=showTrackYourCosignmentPage&searchType=AWBNO&searchValue=[TRACK_CODE]',
				),
				'SAFEXPRESS'        => array(
					'name'      => 'SafeXpress',
					'track_url' => 'http://www.safexpress.com/shipment_inq.aspx?sno=[TRACK_CODE]',
				),
				'SHREE_MARUTI'      => array(
					'name'      => 'Shree Maruti',
					'track_url' => 'http://erp.shreemarutionline.com/frmTrackingDetails.aspx?id=[TRACK_CODE]',
				),
				'SINGAPORE_POST'    => array(
					'name'      => 'Singapore POST',
					'track_url' => 'http://www.singpost.com/track-items?track_items=[TRACK_CODE]',
				),
				'SKYNET'            => array(
					'name'      => 'SkyNET',
                    'track_url' => 'https://www.tracking.my/skynet/[TRACK_CODE]',
                    //'track_url' => 'http://www.courierworld.com/scripts/webcourier1.dll/TrackingResultwoheader?type=4&nid=1&hawbNoList=[TRACK_CODE]',
				),
				'SPEE_DEE'          => array(
					'name'      => 'Spee-Dee',
					'track_url' => 'http://packages.speedeedelivery.com/packages.asp?tracking=[TRACK_CODE]',
				),
				'TNT'               => array(
					'name'      => 'TNT',
					'track_url' => 'http://www.tnt.com/webtracker/tracking.do?cons=[TRACK_CODE]&trackType=CON&saveCons=Y',
				),
				'TNT_AUSTRALIA'               => array(
					'name'      => 'TNT Australia',
					'track_url' => 'https://track.aftership.com/tnt-au/[TRACK_CODE]',
				),
				'TRAD_EX'           => array(
					'name'      => 'TradEx',
					'track_url' => 'http://www.tradelinkinternational.co.in/track.asp?awbno=[TRACK_CODE]',
				),
				'UPS'               => array(
					'name'      => 'UPS',
					'track_url' => 'http://wwwapps.ups.com/WebTracking/processRequest?&tracknum=[TRACK_CODE]',
				),
				'UPS_FR'               => array(
					'name'      => 'UPS France',
					'track_url' => 'https://www.ups.com/track?loc=fr_FR&tracknum=[TRACK_CODE]&requester=WT/trackdetails',
				),
				'USPS'              => array(
					'name'      => 'USPS',
					'track_url' => 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=[TRACK_CODE]',
				),
				'URGENT_AIR'        => array(
					'name'      => 'Urgent Air Courier Express',
					'track_url' => 'http://urgentair.co.in/tracking.aspx?txtawbno=[TRACK_CODE]#tk',
				),
				'ESTAFETA'          => array(
					'name'      => 'Estafeta',
					'track_url' => 'https://track.aftership.com/estafeta/[TRACK_CODE]',
				),
				'DESPATCH_BAY'      => array(
					'name'      => 'Despatch bay',
					'track_url' => 'https://despatchbay.com/tracking?tracking-number=[TRACK_CODE]',
				),
				'COLIS'             => array(
					'name'      => 'Colis privè',
					'track_url' => 'https://www.colisprive.com/moncolis/Default.aspx?numColis=[TRACK_CODE]',
				),
				'SAPO'              => array(
					'name'      => 'South African Post Office (SA Post office)',
					'track_url' => 'https://tracking.postoffice.co.za/TrackNTrace/TrackNTrace.aspx?id=[TRACK_CODE]',
				),
				'GLS'               => array(
					'name'      => 'GLS Europe',
					'track_url' => 'https://gls-group.eu/DE/en/parcel-tracking?match=[TRACK_CODE]',
				),
				'GLS_ITALY'         => array(
					'name'      => 'GLS Italy',
					'track_url' => 'https://www.gls-italy.com/?option=com_gls&view=track_e_trace&mode=search&numero_spedizione=[TRACK_CODE]&tipo_codice=nazionale',
				),
				'ABX'               => array(
					'name'      => 'ABX Express',
					'track_url' => 'http://www.abxexpress.com.my/track.asp?vsearch=True&tairbillno=[TRACK_CODE]',
				),
				'EMS'               => array(
					'name'      => 'EMS China Courier',
					'track_url' => 'http://www.ems.com.cn/index.html'
					,
				),
				'CORREOS'           => array(
					'name'      => 'Correos',
//					'track_url' => 'http://www.correos.es/ss/Satellite/site/pagina-localizador_referencia_expedicion/sidioma=en_GB&Referencia=[TRACK_CODE]',
					'track_url' => 'http://www.correos.es/ss/Satellite/site/aplicacion-1349167937616-herramientas_y_apps/detalle_app-num=[TRACK_CODE]-sidioma=en_GB'
				),
				'YURTICI_KARGO'     => array(
					'name'      => 'Yurtici Kargo',
					'track_url' => 'http://www.yurticikargo.com/bilgi-servisleri/sayfalar/kargom-nerede.aspx?q=[TRACK_CODE]',
				),
				'POST_HASTE'        => array(
					'name'      => 'Post Haste',
					'track_url' => 'http://www.posthaste.co.nz/phl/servlet/ITNG_TAndTServlet?page=1&Key_Type=Ticket&VCCA=Enabled&product_code=[TRACK_CODE][0]&serial_number=[TRACK_CODE][1]',
				),
				'ICS'               => array(
					'name'      => 'ICS Courier',
					'track_url' => 'http://www.icscourier.ca/online-services/tracking.aspx?trackNums=[TRACK_CODE]',
				),
				'YAMATO'            => array(
					'name'      => 'Yamato transport',
					'track_url' => 'http://my.ta-q-bin.com',
					'form'      => '<form name="YAMATO" target="_blank" method="post" action="http://etrace.9625taqbin.com/gli_trace/GDXTX010S10Action_doSearch.action">
									<input name="jvCd" type="hidden" value="42dec70a4f25166d">
									<input name="sTimeDifference" type="hidden" value="671cb6bcdedd72c7">
									<input name ="sSelectedLanguage" type="hidden" value="73f9a3a3f2cf56bc">
									<input name ="sCountryCd" type="hidden" value="0968983fa1feb763">
									<input name ="sLanguageMode" type="hidden" value="0">
									<input name ="CHAR_SET" type="hidden" value="3f572693955bb3ff">
									<input name ="sDefCharSet" type="hidden" value="3f572693955bb3ff">
									<input name ="sCharSetCsv" type="hidden" value="3f572693955bb3ff">
									<input name ="action:GDXTX010S10Action_doSearch" type="hidden" value="Track">
									<input name ="tTrackingNoInputVal1" type="hidden" value="[TRACK_CODE]">
									<input name ="tTrackingNoInputVal2" type="hidden" value="">
									<input name ="tTrackingNoInputVal3" type="hidden" value="">
									<input name ="tTrackingNoInputVal4" type="hidden" value="">
									<input name ="tTrackingNoInputVal5" type="hidden" value="">
									<input name ="tTrackingNoInputVal6" type="hidden" value="">
									<input name ="tTrackingNoInputVal7" type="hidden" value="">
									<input name ="tTrackingNoInputVal8" type="hidden" value="">
									<input name ="tTrackingNoInputVal9" type="hidden" value="">
									<input name ="tTrackingNoInputVal10" type="hidden" value="">
									</form>',
				),
				'DELHIVERY'         => array(
					'name'      => 'Delhivery',
					'track_url' => 'https://www.delhivery.com/track/package/[TRACK_CODE]',
				),
				'EL_CORREO'         => array(
					'name'      => 'El correo',
					'track_url' => 'http://www.elcorreo.com.gt/cdgcorreo/internacional/formulario.php',
				),
				'DEPRISA'           => array(
					'name'      => 'Deprisa',
					'track_url' => 'http://www.deprisa.com//Tracking/index/?track=[TRACK_CODE]',
				),
				'KDZ'               => array(
					'name'      => 'KDZ Express',
					'track_url' => 'http://www.kdz.com/en/track-and-trace/[TRACK_CODE]',
					'form'      => '<form name="KDZ" target="_blank" method="post" action="http://www.kdz.com/en/track-and-trace/">
									<input name="nr" type="hidden" value="[TRACK_CODE]">
									</form>',
				),
				'TRANS_MISSION'     => array(
					'name'      => 'TransMission.NL',
					'track_url' => 'http://www.mijnzending.nl/portal/index.php?view=tt/vAnoniem&zendingnummer=[TRACK_CODE][0]&postcode=[TRACK_CODE][1]',
				),
				'SDA_IT'            => array(
					'name'      => 'SDA Italy',
					'track_url' => 'https://www.sda.it/wps/portal/Servizi_online/ricerca_spedizioni?locale=it&tracing.letteraVettura=[TRACK_CODE]',
				),
				'SAGAWA_EX'         => array(
					'name'      => 'SAGAWA EXPRESS',
					'track_url' => 'http://k2k.sagawa-exp.co.jp/p/sagawa/web/okurijosearcheng.jsp',
				),
				'TOLLGROUP'         => array(
					'name'      => 'Toll Priority | Toll Group',
					'track_url' => 'https://track.aftership.com/[TRACK_CODE]?courier=toll-ipec',
				),
				'THAILAND_POST'     => array(
					'name'      => 'Thailand Post',
					'track_url' => ' https://track.thailandpost.co.th/?trackNumber=[TRACK_CODE]',
				),
				'CHILEXPRESS'       => array(
					'name'      => 'Chilexpress',
					'track_url' => 'https://www.chilexpress.cl/Views/ChilexpressCL/Resultado-busqueda.aspx?DATA=[TRACK_CODE]',
				),
				'POSTNET_CO_ZA'     => array(
					'name'      => 'Postnet.co.za',
					'track_url' => 'http://www.courierit.co.za/Trackit/Trackit.aspx?WaybillNumber=[TRACK_CODE]&AccountNumber=TRACKIT',
				),
				'DAWNWING'          => array(
					'name'      => 'Dawnwing.co.za',
					'track_url' => 'http://www.dawnwing.co.za/business-tools/online-parcel-tracking/',
					'form'      => '<form name="DAWNWING" target="_blank" action="http://www.dawnwing.co.za/business-tools/online-parcel-tracking/" method="post"><input name="WaybillNo" type="hidden" value="[TRACK_CODE]"></form>',
				),
				'CYPRUS_POST'       => array(
					'name'      => 'Cyprus POST',
					'track_url' => 'http://ips.cypruspost.gov.cy/ipswebtrack/IPSWeb_item_events.aspx?itemid=[TRACK_CODE]&Submit=Submit',
				),
				'CAINIAO'           => array(
					'name'      => 'CAINIAO',
					'track_url' => 'http://global.cainiao.com/detail.htm?mailNo=[TRACK_CODE]',
				),
				'17TRACK'           => array(
					'name'      => '17TRACK ALL-IN-ONE PACKAGE TRACKING',
					'track_url' => 'http://www.17track.net/en/track?nums=[TRACK_CODE]',
				),
				'WEDOEXPRESS'       => array(
					'name'      => 'WEDOEXPRESS',
					'track_url' => 'http://www.wedoexpress.com/en/track/s-rest.html?carrier=wedo&nums=[TRACK_CODE]',
				),
				'GLS_NL'            => array(
					'name'      => 'GLS Netherlands',
					'track_url' => 'http://www.gls-info.nl/tracking',
					'form'      => '<form name="GLS_NL" target="_blank" action="http://www.gls-info.nl/tracking" method="post"><input type="hidden" name="ParcelNo" id="tracking_main_parcelno" value="[TRACK_CODE]"><input type="hidden" name="Zipcode" id="tracking_main_zipcode" value="Postcode"></form>',
				),
				'ACS'               => array(
					'name'      => 'ACS courier',
					'track_url' => 'https://www.acscourier.net',
					'form'      => '<form name="ACS" target="_blank" id="trackingQuery" action="https://www.acscourier.net/en/track-and-trace?p_p_id=ACSCustomersAreaTrackTrace_WAR_ACSCustomersAreaportlet&amp;p_p_lifecycle=1&amp;p_p_state=normal&amp;p_p_mode=view&amp;p_p_col_id=column-4&amp;p_p_col_pos=1&amp;p_p_col_count=2&amp;_ACSCustomersAreaTrackTrace_WAR_ACSCustomersAreaportlet_javax.portlet.action=trackTrace" method="POST"> <input type="hidden" name="jspPage" value="TrackTraceController"><input id="generalCode" name="generalCode" type="hidden" value="[TRACK_CODE]"></form>',
				),
				'CHRONOPOST_FR'     => array(
					'name'      => 'Chronopost.fr',
					'track_url' => 'https://www.chronopost.fr/tracking-no-cms/suivi-page?listeNumerosLT=[TRACK_CODE]&langue=fr',
				),
				'FASTWAY_COM_AU'    => array(
					'name'      => 'Fastway.com.au',
					'track_url' => 'http://fastway.com.au/courier-services/track-your-parcel?l=[TRACK_CODE]',
				),
				'XPO'               => array(
					'name'      => 'XPO Logistics, Inc.',
					'track_url' => 'https://www.con-way.com/webapp/manifestrpts_p_app/shipmentTracking.do',
					'form'      => '<form name="XPO" target="_blank" method="post" action="https://www.con-way.com/webapp/manifestrpts_p_app/shipmentTracking.do"><input type="hidden" name="loggedInFlag" value="N"><input type="hidden" name="submitAction.x" value="12"><input type="hidden" name="submitAction.y" value="24"><input type="hidden" name="trackingNumbers" value="[TRACK_CODE]"></form>',
				),
				'OVERNITE_DUBAI'    => array(
					'name'      => 'Overnite Dubai',
					'track_url' => 'http://overnitedubai.com/tracking.php',
					'form'      => '<form name="OVERNITE_DUBAI" target="_blank" method="post" action="http://overnitedubai.com/tracking.php"><input type="hidden" name="tracking_no" value="[TRACK_CODE]"></form>',
				),
				'COORDINADORA'      => array(
					'name'      => 'Coordinadora',
					'track_url' => 'http://www.coordinadora.com',
				),
				'DTDC'              => array(
					'name'      => 'DTDC',
					'track_url' => 'http://dtdc.in/tracking/tracking_results.asp',
					'form'      => '<form name="DTDC" target="_blank" method="post" action="http://dtdc.in/tracking/tracking_results.asp"><input type="hidden" name="action" value="track"><input type="hidden" name="sec" value="tr"><input type="hidden" name="ctlActiveVal" value="1"><input type="hidden" name="Ttype" value="awb_no"><input type="hidden" name="strCnno" value="[TRACK_CODE]"><input type="hidden" name="GES" value="N"><input name="TrkType2" id="TrkType2" type="hidden" value="awb_no"><input name="strCnno2" value="[TRACK_CODE]"></form>',
				),
				'PACKLING_ES'       => array(
					'name'      => 'Packlink.es',
					'track_url' => 'http://www.packlink.es',
					'form'      => '<form name="PACKLING_ES" target="_blank" method="post" action="http://www.packlink.es/es/seguimiento-envios/">
									<input name="num" type="hidden" value="[TRACK_CODE]">
									</form>',
				),
				'SEUR'              => array(
					'name'      => 'SEUR',
					'track_url' => 'https://www.seur.com/livetracking/?segOnlineIdentificador=[TRACK_CODE]',
				),
				'MRW'               => array(
					'name'      => 'MRW Espana',
					'track_url' => 'http://www.mrw.es/seguimiento_envios/MRW_resultados_consultas.asp?modo=nacional&envio=[TRACK_CODE]',
				),
				'ENVIALIA'          => array(
					'name'      => 'Envialia',
					'track_url' => 'https://track.aftership.com/envialia/[TRACK_CODE]',
				),
				'ZELERIS'           => array(
					'name'      => 'Zeleris',
					'track_url' => 'http://www.zeleris.com',
					'form'      => '<form name="zeleris" method="post" id="frm" action="https://www.zeleris.com/seguimiento_envio.aspx"><input name="txtTab1IdSeguimiento" type="hidden" value="[TRACK_CODE]" id="txtTab1IdSeguimiento"><input type="hidden" name="_LASTFOCUS" value=""><input type="hidden" name="__EVENTTARGET" value="lnkTab1Consultar"><input type="hidden" name="__EVENTARGUMENT" value=""><input type="hidden" name="__VIEWSTATE" value = "/wEPDwUJMzM5NjA2NjIwD2QWAgIDD2QWDgIBD2QWCgIKDw8WAh4HRW5hYmxlZGhkZAIMDw8WBB4LTmF2aWdhdGVVcmxlHgZUYXJnZXRlZGQCDg8PFgQfAQUQfi9Vc2VyTG9naW4uYXNweB4EVGV4dAUPQWNjZXNvIHVzdWFyaW9zZGQCGg8WAh4LXyFJdGVtQ291bnQCAhYEZg9kFgICAw8PFgYfAQUMfi9pbmRleC5hc3B4HwJlHwMFBkluaWNpb2RkAgIPZBYEAgEPDxYEHwMFFlNlZ3VpbWllbnRvIGRlIGVudsOtb3MeB1Zpc2libGVnZGQCAw8PFgIfBWhkZAIcDw8WAh8DBRZTZWd1aW1pZW50byBkZSBlbnbDrW9zZGQCAw8WAh4FY2xhc3MFDnByaW1lcm8gYWN0aXZvFgICAQ8PFgIeDU9uQ2xpZW50Q2xpY2sFGGphdmFzY3JpcHQ6cmV0dXJuIGZhbHNlO2RkAgUPFgIfBmUWAgIBDw8WAh8HZWRkAgcPDxYCHwVnZBYCAgcPDxYGHghDc3NDbGFzcwUJbXNnX2Vycm9yHwNlHgRfIVNCAgJkZAILDw8WAh8FaGQWAgIDD2QWBGYPZBYCAhsPPCsADQBkAgIPZBYCAikPZBYCAgEPZBYCAgEPEGRkFgFmZAIND2QWAgIDD2QWBGYPZBYCAhUPPCsADQBkAgIPZBYCAh8PZBYCAgEPZBYCAgEPEGRkFgFmZAIPD2QWAgIDD2QWBAIPD2QWAgIBD2QWAmYPZBYCAgEPPCsADQBkAhEPZBYCAgEPZBYCZg9kFgICAQ88KwANAGQYBAUhY3RsRGV0YWxsZU5vQWJvbmFkb3MkZ3JkX2Rlc3Rpbm9zD2dkBSVjdGxEZXRhbGxlTm9BYm9uYWRvcyRncmRfaGlzdG9yaWFsUkNPD2dkBSNjdGxEZXRhbGxlUmVjb2dpZGEkZ3JkX2hpc3RvcmlhbFJDTw9nZAUiY3RsRGV0YWxsZUV4cGVkaWNpb24kZ3JkX2hpc3RvcmlhbA9nZAV0g2YTMXJGZ6YC6eUBq6JGNctV" <input type="hidden" name="__VIEWSTATEGENERATOR" value="1A6E8B63"><input type="hidden" name="__EVENTVALIDATION" value="/wEWBgLw3vjtAQKfsa/zAwLw7IizAgLbnfX/CALx9s+nDgKDwte1A7mOhCNnil2XO5Ue4fstDaGgKle5"></form>',
				),
				'NACEX'             => array(
					'name'      => 'Nacex',
					'track_url' => 'http://www.nacex.es',
				),
				'ASM'               => array(
					'name'      => 'ASM',
					'track_url' => 'https://m.asmred.com/e/[TRACK_CODE]/[TRACK_POSTCODE]',
				),
				'TOURLINE_EXP'      => array(
					'name'      => 'Tourline Express',
					//'track_url' => 'http://www.tourlineexpress.com/
                    'track_url' => 'http://tourlineexpress.com/AreaClientes/Views/Destinatarios.aspx?s=[TRACK_CODE]',

                ),
				'KEAVO'             => array(
					'name'      => 'KEAVO',
					'track_url' => 'http://www.keavo.com',
				),
                'PUNTO_PACK'     => array(
                    'name'      => 'Punto Pack - Mondial Relay',
                    'track_url' => 'https://www.puntopack.es/seguir-mi-envio/ ',
                ),
                'MONDIAL_RELAY'     => array(
                    'name'      => 'Mondial Relay',
                    'track_url' => 'https://www.mondialrelay.fr/suivi-de-colis/?NumeroExpedition=[TRACK_CODE]&CodePostal=[TRACK_POSTCODE]',
                ),
				'STARTPACK'         => array(
					'name'      => 'Starpack',
					'track_url' => 'http://www.packlink.es',
					'form'      => '<form name="PACKLING_ES" target="_blank" method="post" action="http://www.packlink.es/es/seguimiento-envios/"><input class="inputTrack" id="num" name="num" type="hidden" value="[TRACK_CODE]"></form>',
				),
				'ISRAEL_POST'       => array(
					'name'      => 'Israel Post',
					'track_url' => 'http://www.israelpost.co.il',
				),
				'CASTLE_PARCELS'    => array(
					'name'      => 'Castle Parcels',
					'track_url' => 'http://www.castleparcels.co.nz/cpl/servlet/ITNG_TAndTServlet?page=1&Key_Type=Ticket&VCCA=Enabled&product_code=[TRACK_CODE][0]&serial_number=[TRACK_CODE][1]&Submit=Track+Courier+Ticket',
				),
				'EURODIS'           => array(
					'name'      => 'Eurodis',
					'track_url' => 'http://www.eurodis.com/track_and_trace/index.php',
					'form'      => '<form name="EURODIS" target="_blank" method="post" action="http://eurodis.com/track-trace/"><input name="track_and_trace_hidden_field" type="hidden" value="trackCollo"><input name="trackCollo-input" value="[TRACK_CODE]" type="hidden"></form>',
				),
				'GOJAVAS'           => array(
					'name'      => 'Gojavas',
					'track_url' => 'http://gojavas.com/docket_details.php?pop=docno&docno=[TRACK_CODE]',
				),
				'NEXIVE'            => array(
					'name'      => 'Nexive.it',
					'track_url' => 'https://www.sistemacompleto.it/Tracking-Spedizioni-Nexive.aspx?b=[TRACK_CODE]&lang=IT',
				),
				'PARZEL'            => array(
					'name'      => 'Parzel.com',
					'track_url' => 'http://parzel.com/',
				),
				'EEDOSTAVKA'        => array(
					'name'      => 'Eedostavka.ru',
					'track_url' => 'http://www.edostavka.ru/',
				),
				'EMS_RUSSIAN'       => array(
					'name'      => 'EMS Russian Post',
					'track_url' => 'http://emspost.ru/ru/',
				),
				'DELLIN'            => array(
					'name'      => 'Dellin.ru',
					'track_url' => 'http://www.dellin.ru/',
				),
				'COLISSIMO'         => array(
					'name'      => 'COLISSIMO',
					'track_url' => 'https://www.laposte.fr/particulier/outils/suivre-vos-envois?code=[TRACK_CODE]',
					'form'      => '',
				),
				'SHIPYAARI'         => array(
					'name'      => 'Shipyaari',
					'track_url' => 'http://shipyaari.com/index.html',
				),
				'CORREIOS_BR'       => array(
					'name'      => 'Correios.com.br',
					'track_url' => 'http://www.correios.com.br',
					'form'      => '<form name="CORREIOS_BR" action="http://www2.correios.com.br/sistemas/rastreamento/resultado.cfm" method="post">
									<input name="P_LINGUA" value="001" type="hidden">
									<input name="P_TIPO" value="001" type="hidden">
									<input name="objetos" value="[TRACK_CODE]" type="hidden"></form>',
				),
				'POSTNORD'          => array(
					'name'      => 'PostNord Norway',
					'track_url' => 'http://www.postnord.no/minside/SOPS/[TRACK_CODE]',
				),
				'POSTNORD_DK'       => array(
					'name'      => 'PostNord Danmark',
					'track_url' => 'http://www.postnord.dk/da/Sider/TrackTrace.aspx?search=[TRACK_CODE]',
				),
				'GLOBEFLIGHT'       => array(
					'name'      => 'GlobeFlight',
					'track_url' => 'http://www.globeflight.co.za',
					'form'      => '<form name="GLOBEFLIGHT" action="http://tracking.parcelperfect.com/waybill.php" method="post">
									<input name="ppCust" value="107" type="hidden">
									<input name="linkcust1" value="0" type="hidden">
									<input name="linkcust2" value="0" type="hidden">
									<input name="linkcust3" value="0" type="hidden">
									<input name="userName" value="" type="hidden">
									<input name="accArray" value="" type="hidden">
									<input name="validUser" value="1" type="hidden">
									<input name="DBHost" value="127.0.0.1:/pperfect/sqldata/ppweb.gdb" type="hidden">
									<input name="waybill" value="[TRACK_CODE]" type="hidden">
									<input name="subject" value="" type="hidden">
									</form>',
				),
				'CANPAR'            => array(
					'name'      => 'Canpar courier',
					'track_url' => 'https://www.canpar.com/',
					'form'      => '<form name="CANPAR" action="https://www.canpar.com/en/track/TrackingAction.do;jsessionid=C5B6844406F3DF38E4FD8DA78FF6AADA" method="post">
									<input name="locale" value="en" type="hidden">
									<input name="type" value="0" type="hidden">
									<input name="reference" value="[TRACK_CODE]" type="hidden">
									</form>',
				),
				'SOGETRAS'          => array(
					'name'      => 'SoGeTras - SGT corriere espresso (IT)',
					'track_url' => 'https://mybizportal.sogetras.it/Public/RicercaLdv.aspx?ldv=[TRACK_CODE]',
				),
				'ILOGEN'            => array(
					'name'      => 'ILOGEN.com',
					'track_url' => 'http://www.ilogen.com/iLOGEN.Web.New/TRACE/TraceView.aspx?gubun=slipno&slipno=[TRACK_CODE]',
				),
				'POST_AT'           => array(
					'name'      => 'Post.at',
					'track_url' => 'https://www.post.at/sendungsverfolgung.php/details?pnum1=[TRACK_CODE]',
				),
				'PICKPACKPONT'      => array(
					'name'      => 'PickPackPont.hu',
					'track_url' => 'http://www.pickpackpont.hu/csomagkereso/',
				),
				'MHI'               => array(
					'name'      => 'MH-International',
					'track_url' => 'https://www.trackmail.co.uk/?Tracking=[TRACK_CODE]&command=Track',
				),
				'COURIER_GUY'       => array(
					'name'      => 'The Courier Guy',
					'track_url' => 'https://thecourierguy.pperfect.com/',
				),
				'CTT_EXPRESSO'      => array(
					'name'      => 'CTT Expresso (Portugal)',
					'track_url' => 'http://www.cttexpresso.pt/feapl_2/app/open/cttexpresso/objectSearch/objectSearch.jspx?lang=def&objects=[TRACK_CODE]',
				),
				'DHL_PT'            => array(
					'name'      => 'DHL (Portugal)',
					'track_url' => 'http://www.dhl.pt/pt/expresso/localizar_envios.html?AWB=[TRACK_CODE]&brand=DHL',
				),
				'PBT'               => array(
					'name'      => 'PB Track',
					'track_url' => 'http://www.pbt.com',
					'form'      => '<form name="PBT" action="http://www.pbt.com/nick/PBTCresults.cfm" method="post" target="_blank">
									<input name="TicketNo" value="[TRACK_CODE]" type="hidden">
									<input name="__VIEWSTATE" value="/wEPDwULLTEwMzgwOTQ5MjZkGAEFHl9fQ29udHJvbHNSZXF1aXJlUG9zdEJhY2tLZXlfXxYBBShQYWdlVGVtcGxhdGVfaG9tZV9hc2N4JEhlYWRlcjEkYnRuU2VhcmNoPifSAmvG8VLxp4WImN477YvZRfw=" type="hidden">
									<input name="__EVENTVALIDATION" value="/wEWAgKH9q3WBwLBkIG6BuRG6CDPG7tRVzgxA827HE7D8PAx" type="hidden">
									<input name="Nav" value="?ticketNo%3D[TRACK_CODE]" type="hidden">
									</form>',
				),
				'QXPRESS_ASIA'      => array(
					'name'      => 'Qxpress Asia',
					'track_url' => 'http://www.qxpress.asia/eng/html/customer_tracking_view.html?value=[TRACK_CODE]',
				),
				'DIRECT_LINK'       => array(
					'name'      => 'Direct Link',
					'track_url' => 'http://directlinktrackedplus.com/multipletrack-client2.php?lang=en&postal_ref_mode=1&postal_ref_no=[TRACK_CODE]',
				),
				'E-GO'              => array(
					'name'      => 'E-GO',
					'track_url' => 'www.e-go.com.au',
				),
				'TIKI'              => array(
					'name'      => 'TIKI Indonesia',
					'track_url' => 'https://tiki.id/resi/[TRACK_CODE]',
				),
				'CHUNGHWA_POST'     => array(
					'name'      => 'Chunghwa Post (Taiwan) - 中郵郵政(台灣)',
					'track_url' => 'http://postserv.post.gov.tw/webpost/CSController?cmd=POS4008_1&_MENU_ID=189&_SYS_ID=D&_ACTIVE_ID=192',
				),
				'MAPLE'             => array(
					'name'      => 'Maple (Taiwan)',
					'track_url' => 'http://www.25431010.tw/Search.php',
				),
				'PELICAN'           => array(
					'name'      => 'Pelican (Taiwan)',
					'track_url' => 'http://query2.e-can.com.tw/%E5%A4%9A%E7%AD%86%E6%9F%A5%E4%BB%B6A.htm',
				),
				'EZSHIP'            => array(
					'name'      => 'Ezship (Taiwan) - Ezship超商取貨(台灣)',
					'track_url' => 'https://www.ezship.com.tw/receiver/receiver_query_shipstatus.jsp',
				),
				'MYSHIP_7-11'       => array(
					'name'      => 'MYSHIP 7-11 (Taiwan)',
					'track_url' => 'https://eservice.7-11.com.tw/e-tracking/search.aspx',
				),
				'T-CAT'             => array(
					'name'      => 'T-CAT (Taiwan)',
					'track_url' => 'http://www.t-cat.com.tw/Inquire/TraceDetail.aspx?BillID=[TRACK_CODE]&ReturnUrl=Trace.aspx',
				),
				'CORREO_UR'         => array(
					'name'      => 'Correo Uruguayo',
					'track_url' => 'https://track.trackingmore.com/uruguay-post/es-[TRACK_CODE].html?',
				),
				'KERRY_EXPRESS'     => array(
					'name'      => 'Kerry express Thailand',
					'track_url' => 'http://th.kerryexpress.com/th/track/?track=[TRACK_CODE]',
				),
				'DHL_PARCEL_NL'     => array(
					'name'      => 'DHL parcel NL',
					'track_url' => 'https://www.dhlparcel.nl/nl/particulier/ontvangen/track-trace?tt=[TRACK_CODE]',
				),
				'RUSSIAN_POST'      => array(
					'name'      => 'Russian POST',
					'track_url' => 'https://www.pochta.ru/tracking#[TRACK_CODE]',
				),
				'CDEK'              => array(
					'name'      => 'CDEK',
					'track_url' => 'http://www.edostavka.ru/track.html?order_id=[TRACK_CODE]',
				),
				'INTERPARCEL'       => array(
					'name'      => 'Interparcel',
					'track_url' => 'http://www.interparcel.com.au/tracking.php?action=dotrack&trackno=[TRACK_CODE]',
				),
				'TAXYDROMIKI'       => array(
					'name'      => 'Geniki Taxydromiki',
					'track_url' => 'http://www.taxydromiki.com/track/[TRACK_CODE]',
				),
                'SPEEDEX'           => array(
                    'name'      => 'Speedex',
                    'track_url' => 'http://www.speedex.gr/isapohi.asp?voucher_code=[TRACK_CODE]&searcggo=Submit',
                ),
				'WAHANA'            => array(
					'name'      => 'Wahana',
					'track_url' => 'http://http://wahana.com',
					'form'      => '<form name="WAHANA" method="POST" target="_blank" action="http://mobile.wahana.com/apps/wahana/cgi-bin/dw.cgi?b=view/vnm.whnTrackingPublic&noframe=1">
                					<input type="hidden" name="noframe" value="1">
                					<input type="hidden" name="b" value="view/vnm.whnTrackingPublic" >
                  					<input type="hidden" name="btno" value="[TRACK_CODE]">
        							</form>',
				),
				'HCT'               => array(
					'name'      => 'HCT Logistic - 宅配-新竹物流(台灣)',
					'track_url' => 'https://www.hct.com.tw/searchgoods_index.aspx',
				),
				'DHL_SWE'           => array(
					'name'      => 'DHL Sweden',
					'track_url' => 'https://www.dhl.com/se-sv/home/tracking/tracking-freight.html?tracking-id=[TRACK_CODE]',
				),
				'POST_PARCELS'      => array(
					'name'      => 'iPostParcels.com',
					'track_url' => 'https://www.ukmail.com/manage-my-delivery/manage-my-delivery?con=[TRACK_CODE]',
				),
				'TNT_ITALY'         => array(
					'name'      => 'TNT Italy',
					'track_url' => 'https://www.tnt.it/tracking/Tracking.do?wt=1&consigNos=[TRACK_CODE]&autoSearch=&searchMethod=&pageNo=&numberText=[TRACK_CODE]&numberTextArea=&codCli=&tpCod=NName',
				),
				'CJ_EXPRESS'        => array(
					'name'      => 'CJ Express',
					'track_url' => 'https://www.doortodoor.co.kr/parcel/pa_004.jsp',
					'form'      => '<form name="CJ_EXPRESS" method="POST" target="_blank"
										action="https://www.doortodoor.co.kr/parcel/doortodoor.do">
                					<input type="hidden" name="fsp_action" value="PARC_ACT_002">
                					<input type="hidden" name="fsp_cmd" value="retrieveInvNoACT" >
                  					<input type="hidden" name="invc_no" value="[TRACK_CODE]">
        							</form>',
				),
				'COURIERSPLEASE'    => array(
					'name'      => 'CouriersPlease.com.au',
					'track_url' => 'https://track.aftership.com/couriers-please/[TRACK_CODE]',
				),
				'UK_MAIL'           => array(
					'name'      => 'UK Mail',
					'track_url' => 'https://www.ukmail.com/manage-my-delivery/manage-my-delivery?con=[TRACK_CODE]',
				),
				'SENDLE'            => array(
					'name'      => 'Sendle',
					'track_url' => 'https://track.aftership.com/sendle/[TRACK_CODE]?',
				),
				'MYHERMES_UK'       => array(
					'name'      => 'MyHermes UK',
					'track_url' => 'https://www.myhermes.co.uk/tracking-results.html?trackingNumber=[TRACK_CODE]',
				),
				'APC_OVERNIGHT'     => array(
					'name'      => 'APC Overnight',
					'track_url' => 'https://apc-overnight.com/receiving-a-parcel/tracking/',
				),
				'DEUTSCHE_POST'     => array(
					'name'      => 'Deutsche Post',
					'track_url' => 'https://www.deutschepost.de/sendung/simpleQueryResult.html?form.sendungsnummer=[TRACK_CODE]&form.einlieferungsdatum_tag=[TRACK_DAY]&form.einlieferungsdatum_monat=[TRACK_MONTH]&form.einlieferungsdatum_jahr=[TRACK_YEAR]',
				),
				'ALPHAFAST'         => array(
					'name'      => 'Alpha Fast',
					'track_url' => 'https://www.alphafast.com/th/track?id=[TRACK_CODE]',
				),
				'QUANTIUM_SOLUTION' => array(
					'name'      => 'Quantium Solutions Post',
					'track_url' => 'http://track.quantiumsolutions.com/?TrackNo=%2C[TRACK_CODE]',
				),
				'GLS_SLOVAKIA'      => array(
					'name'      => 'GLS Slovakia',
					'track_url' => 'https://gls-group.eu/SK/sk/sledovanie-zasielok?match=[TRACK_CODE]',
				),
				'SLOVENSKA_POST'    => array(
					'name'      => 'Slovenská pošta',
					'track_url' => 'http://tandt.posta.sk/zasielky/[TRACK_CODE]',
				),
				'OCA'               => array(
					'name'      => 'OCA',
					'track_url' => 'http://oca.com.ar/',
				),
				'CORREO_ARG'        => array(
					'name'      => 'Correo Argentino',
					'track_url' => 'http://www.correoargentino.com.ar/',
				),
				'ANDREANI'          => array(
					'name'      => 'Andreani',
					'track_url' => 'http://www.andreani.com/',
				),
				'SF_EXPRESS'        => array(
					'name'      => 'SF Express',
					'track_url' => 'https://www.sf-express.com/hk/tc/dynamic_function/waybill/#search/bill-number/[TRACK_CODE]',
				),
				'SMSA_EXPRESS'      => array(
					'name'      => 'SMSA Express',
					'track_url' => 'http://www.smsaexpress.com/Track.aspx?tracknumbers=[TRACK_CODE]',
				),
				'POSTE_IT'          => array(
					'name'      => 'Poste Italiane',
					'track_url' => 'https://www.poste.it/cerca/index.html#/risultati-spedizioni/[TRACK_CODE]',
				),
				'EMS_JAPAN'         => array(
					'name'      => 'EMS Japan Post',
					'track_url' => 'https://trackings.post.japanpost.jp/services/srv/search/direct?reqCodeNo1=[TRACK_CODE]'
				),
				'EPG'               => array(
					'name'      => 'EPG - Emirates Post Group',
					'track_url' => 'https://www.epg.ae/esvc/services/track/index.xhtml?mail_id=[TRACK_CODE]&pageid=MAINMENU_TRACKNTRACE&lang=ar'
				),
				'DHL_GLOBAL_MAIL'   => array(
					'name'      => 'DHL Global Mail',
					'track_url' => 'http://webtrack.dhlglobalmail.com/?trackingnumber=[TRACK_CODE]'
				),
				'DPEX'              => array(
					'name'      => 'DPEX',
					'track_url' => 'https://www.dpex.com/Tools-And-Applications/Track-And-Trace?cn=[TRACK_CODE]'
				),
				'SFS'               => array(
					'name'      => 'Specialized Freight Services',
					'track_url' => 'http://www.specialisedfreight.co.za/',
					'form'      => '<form name="SFS" method="POST" target="_blank"
										action="http://tracking.parcelperfect.com/waybill.php">
										<input type="hidden" name="ppCust" value="221">
										<input type="hidden" name="linkcust1" value="">
										<input type="hidden" name="linkcust2" value="">
										<input type="hidden" name="linkcust3" value="">
										<input type="hidden" name="userName" value="">
										<input type="hidden" name="accArray" value="">
										<input type="hidden" name="validUser" value="1">
										<input type="hidden" name="DBHost" value="127.0.0.1:/pperfect/sqldata/ppweb.gdb">
										<input type="hidden" name="waybill" value="[TRACK_CODE]">
        							</form>',
				),
				'KURONEKO_JP'       => array(
					'name'      => 'Yamato Transport - kuronekoyamato.co.jp (ヤマト宅急便)',
					'track_url' => 'http://jizen.kuronekoyamato.co.jp/jizen/servlet/crjz.b.NQ0010?id=Oder>[TRACK_CODE]'
				),
				'BLUEEX'            => array(
					'name'      => 'BlueEX',
					'track_url' => 'http://blue-ex.com/index.php?trackno=[TRACK_CODE]'
				),
				'TCS'               => array(
					'name'      => 'TCS Couriers',
					'track_url' => 'http://www.tcscouriers.com/uk/Tracking/Default.aspx?TrackBy=ReferenceNumberHome&trackNo=[TRACK_CODE]'
				),
				'LEOPARDS'          => array(
					'name'      => 'LeopardsCourier',
					'track_url' => 'http://leopardscourier.net/CheckTraking.aspx?RBLSelectedValue=1&CnNumber=[TRACK_CODE]&IsFromDotCom=True'
				),
				'KANGAROO'          => array(
					'name'      => 'Kangaroo Couriers',
					'track_url' => 'https://www.kangaroocouriers.com.au/'
				),
				'POST_LUXEMBOURG'   => array(
					'name'      => 'Post Luxembourg',
					'track_url' => 'http://www.trackandtrace.lu/homepage.htm?numero=[TRACK_CODE]'
				),
				'DOTZOT'            => array(
					'name'      => 'dotzot.in',
					'track_url' => 'http://instacom.dotzot.in/GUI/Tracking/Track.aspx?AwbNos=[TRACK_CODE]'
				),
				'EPOST_GO_KR'       => array(
					'name'      => 'Epost.go.kr',
					'track_url' => 'https://track.shiptrack.co.kr/epost/[TRACK_CODE]',
				),
				'KG_LOGIS'          => array(
					'name'      => 'KG Logis',
					'track_url' => 'http://www.kglogis.co.kr/delivery/delivery_result.jsp?item_no=[TRACK_CODE]'
				),
				'STARTRACK'         => array(
					'name'      => 'StarTrack',
					'track_url' => 'https://sttrackandtrace.startrack.com.au',
					'form'      => '<form name="STARTRACK" method="POST" target="_blank" action="https://sttrackandtrace.startrack.com.au/">
										<input type="hidden" name="txtGuid" value="">
										<input type="hidden" name="txtConsignmentNumber" value="[TRACK_CODE]">
        							</form>',
				),
				'PACKSEND'          => array(
					'name'      => 'Packsend',
					'track_url' => 'https://online.packsend.com.au/TrackTrace',
					'form'      => '<form name="PACKSEND" method="POST" target="_blank" action="https://online.packsend.com.au/TrackTrace">
										<input type="hidden" name="TrackingNumber" value="[TRACK_CODE]">
        							</form>',
				),
				'FASTTRACK_PH'      => array(
					'name'      => 'Fastrack.ph',
					'track_url' => 'http://www.fastrack.ph',
					'form'      => '<form name="FASTTRACK_PH" method="POST" target="_blank"
											action="http://www.fastrack.ph/track/">
										<input type="hidden" name="tcode" value="[TRACK_CODE]">
        							</form>'
				),
				'GLS_DK'            => array(
					'name'      => 'GLS Denmark',
					'track_url' => 'https://gls-group.eu/DK/da/find-pakke?match=[TRACK_CODE]'
				),
				'POSTE_MA'          => array(
					'name'      => 'Poste Maroc',
					'track_url' => 'https://track.trackingmore.com/poste-maroc/en-[TRACK_CODE].html?',
				),
				'DPD_UK'            => array(
					'name'      => 'DPD UK',
					'track_url' => 'https://www.dpd.co.uk/apps/tracking/?reference=[TRACK_CODE]',
				),
				'DPD_LOCAL_UK'      => array(
					'name'      => 'DPD LOCAL UK',
					'track_url' => 'https://www.dpdlocal.co.uk/apps/tracking/?reference=[TRACK_CODE]',
				),
				'GOODMAJI'          => array(
					'name'      => 'Goodmaji.com',
					'track_url' => 'http://goodmaji.com/track.aspx',
				),
				'BRING_DK'          => array(
					'name'      => 'Bring Denmark',
					'track_url' => 'http://tracking.bring.dk/tracking.html?q=[TRACK_CODE]'
				),
				'DAO365'            => array(
					'name'      => 'DAO365',
					'track_url' => 'http://www.dao.as/language/da/?stregkode=[TRACK_CODE]#trackandtrace'
				),
				'DPX_LOGISTICS'     => array(
					'name'      => 'DPX Logistics',
					'track_url' => 'http://www.en.dpxlogistics.com/track-trace',
					'form'      => '<form name="DPX_LOGISTICS" method="POST" target="_blank" action="http://www.en.dpxlogistics.com/track-trace">
										<input type="hidden" name="checking_id" value="[TRACK_CODE]">
        							</form>'
				),
				'ECOM_EXPRESS'     => array(
					'name'      => 'Ecom Express',
					'track_url' => 'https://ecomexpress.in/tracking/?awb_field=[TRACK_CODE]&s=',
				),
				'BRING_NORWAY'     => array(
					'name'      => 'Bring (Norway)',
					'track_url' => 'http://sporing.bring.no/sporing.html?q=[TRACK_CODE]',
				),
				'DPD_FR'            => array(
					'name'      => 'DPD France',
					'track_url' => 'http://www.dpd.fr/trace/[TRACK_CODE]',
                    //'track_url' => 'http://www.dpd.fr/traces_info_[TRACK_CODE]',
                ),
				'XPRESSBEES'         => array(
					'name'      => 'XPRESSBEES',
					'track_url' => 'http://www.xpressbees.com/track-shipment.aspx?tracking_id=[TRACK_CODE]',
				),
				'CORREOS_EXPRESS'    => array(
					'name'      => 'Correos Express',
					'track_url' => 'https://s.correosexpress.com/SeguimientoSinCP/search',
					'form'      => '<form name="CORREOS_EXPRESS" method="POST" target="_blank" action="https://s.correosexpress.com/SeguimientoSinCP/search;jsessionid=jSlPNPws0GJwQA5642EVy2B2gmEeKo_44uxWDPO2.seguimientosincp-emco0">
										<input type="hidden" name="shippingNumber" value="[TRACK_CODE]">
        							</form>'
				),
				'POST_ISLAMIC_REPUBLIC_OF_IRAN'    => array(
					'name'      => 'پست جمهوری اسلامی ایران',
					'track_url' => 'http://newtracking.post.ir/'
				),
				'POST_NL_BE'           => array(
					'name'      => 'POST NL Belgium',
					//'track_url' => 'https://jouw.postnl.be/#/track-en-trace/[TRACK_CODE][0]/[TRACK_CODE][1]',
                    'track_url' => 'https://jouw.postnl.be/track-en-trace/[TRACK_CODE]-BE-[TRACK_POSTCODE]',
				),
				'T_CAT_INTERNATIONAL'    => array(
					'name'      => 'T CAT International',
					'track_url' => 'http://www.t-cat.com.tw/Inquire/International.aspx',
					'form'      => '<form name="T_CAT_INTERNATIONAL" method="POST" target="_blank" action="http://www.t-cat.com.tw/Inquire/International.aspx">
										<input type="hidden" name="__EVENTTARGET" value="ctl00$ContentPlaceHolder1$btnQuery">
										<input type="hidden" name="__EVENTARGUMENT" value="">
										<input type="hidden" name="__VIEWSTATE" value="/wEPDwUJNTQxNDM1Mjc5ZGTYZc2INsBhA+bexR7BgfNplqAaJQ==">
										<input type="hidden" name="__EVENTVALIDATION" value="/wEdAAODTdJHzec4Okxd5+u9y26HHbYSQ7CvtU3YB1imPbvMI6lWyR2FR9yhbUlUSLYAxVRmvU1IY+D5u9MA51+bFgP7FXBrsA==">
										<input type="hidden" name="__VIEWSTATEGENERATOR" value="">
										<input type="hidden" name="q" value="站內搜尋">
										<input type="hidden" name="cx" value="005475758396817196247:vpg-mgvhr44">
										<input type="hidden" name="cof" value="FORID:11">
										<input type="hidden" name="ie" value="UTF-8">
										<input type="hidden" name="ctl00$ContentPlaceHolder1$txtReqNo" value="[TRACK_CODE]">
        							</form>'
				),
				'KERRY_TJ_LOGISTICS'    => array(
					'name'      => 'Kerry TJ Logistics',
					'track_url' => 'https://www.kerrytj.com/zh/search/search_track_list.aspx',
					'form'      => '<form name="KERRY_TJ_LOGISTICS" method="POST" target="_blank" action="https://www.kerrytj.com/zh/search/search_track_list.aspx">
										<input type="hidden" name="rdType" value="0">
										<input type="hidden" name="trackNo1" value="[TRACK_CODE]">
										<input type="hidden" name="btnTrack" value="Submit">
        							</form>'
				),
				'POCZTA_POLSKA'    => array(
					'name'      => 'Poczta Polska',
					'track_url' => 'http://emonitoring.poczta-polska.pl/?numer=[TRACK_CODE]'
				),
				'TBS_TRANSPORT'    => array(
					'name'      => 'TBS Transport',
					'track_url' => 'https://www.tsbconnect.net/tracking/search?number=[TRACK_CODE]'
				),
                'LION_PARCEL'    => array(
                    'name'      => 'Lion Parcel',
                    'track_url' => 'https://track.aftership.com/[TRACK_CODE]'
                ),
                'NATIONWIDE_EXPRESS' => array(
                    'name' => 'Nationwide Express',
                    'track_url' => 'http://nationwide2u.com/v2/cgi-bin/trackbe.cfm',
                    'form' => '<form name="NATIONWIDE_EXPRESS" method="post" target="_blank" action="http://nationwide2u.com/v2/cgi-bin/trackbe.cfm"><input type="hidden" name="CNNO" value="[TRACK_CODE]"></form>'
                ),
                'RAM' => array(
                    'name' => 'RAM',
                    'track_url' => 'https://www.ram.co.za/tracking',
                    'form' => '<form name="RAM" method="post" target="_blank" action="https://www.ram.co.za/tracking"><input type="hidden" name="trackingNumber" value="[TRACK_CODE]"></form>'
                ),
				'DCB_LOGISTICS' => array(
					'name'      => 'D.C.B. Logistics',
					'track_url' => 'http://www.dcb.co.za/dcb/ttenter',
					'form'      => '<form name="DCB_LOGISTICS" method="post" target="_blank" action="http://www.dcb.co.za/dcb/ttdisplay"><input type="hidden" name="pWaybillNo" value="[TRACK_CODE]"></form>'
				),
				'LWE_HK_LOGISTICS' => array(
					'name'      => 'LWE Hong Kong Logistics',
					'track_url' => 'https://www.trackingmore.com/lwehk-tracking/tw.html?number=[TRACK_CODE]'
				),
				'MNG_KARGO' => array(
					'name'      => 'MNG Kargo',
					'track_url' => 'http://service.mngkargo.com.tr/iactive/popup/KargoTakip/link1.asp?k=[TRACK_CODE]'
				),
				'PPL' => array(
					'name' => 'PPL',
					'track_url' => 'https://www.ppl.cz/main2.aspx?cls=Package&idSearch=[TRACK_CODE]',
				),
                'ROADBULL' => array(
                    'name' => 'Roadbull',
                    'track_url' => 'https://cds.roadbull.com/order/track/[TRACK_CODE]',
                ),
                'NINJAVAN' => array(
                    'name' => 'Ninjavan',
                    'track_url' => 'https://www.ninjavan.co/en-sg/',
                ),
				'4PX' => array(
					'name' => '4PX',
					'track_url' => 'https://track.aftership.com/4px/[TRACK_CODE]',
				),
				'QOURIER' => array(
					'name' => 'Qourier',
					'track_url' => 'https://qourier.com/track/[TRACK_CODE]',
				),
                'INPOST' => array(
                    'name' => 'Inpost',
                    'track_url' => 'https://inpost.pl/pl/pomoc/znajdz-przesylke?parcel=[TRACK_CODE]',
                ),
                'Paczka_w_Ruchu' => array(
                    'name' => 'Paczka w Ruchu',
                    'track_url' => 'https://www.paczkawruchu.pl/sledz-paczke/?numer=[TRACK_CODE]',
                ),
                'Go_Logistics' => array(
                    'name' => 'Go Logistics',
                    'track_url' => 'https://www.gologistics.com.au/track-your-parcel',
                ),
                'DYNAMEX' => array(
                    'name' => 'Dynamex',
                    'track_url' => 'https://direct.dynamex.com/dxnow5/Track',
                    'form' => '<form name="DYNAMEX" method="post" target="_blank" action="https://direct.dynamex.com/dxnow5/Track"><input type="hidden" name="trackingForm" value="">
                                <input type="text" class="form-control input-lg" name="trackingNumber" id="trackingNumber" autocapitalize="off" autocorrect="off" autocomplete="off" value="[TRACK_CODE]" data-bv-field="trackingNumber">
                                </form>'
                ),
                'INTELCOM' => array(
                    'name' => 'Intelcom',
                    'track_url' => 'https://intelcomaz.progressionlive.com/server/plugins/intelcomaz/package_tracking',
                    'form' => '<form name="INTELCOM" method="post" target="_blank" action="https://intelcomaz.progressionlive.com/server/plugins/intelcomaz/package_tracking"><input type="hidden" name="trackingForm" value="">
                                <input type="text" name="tackingNumber" value="[TRACK_CODE]">
                                </form>'
                ),
				'FERCAM'         => array(
					'name'      => 'Fercam',
					'track_url' => 'http://track.fercam.com/DirektShip/[TRACK_CODE]',
				),
                'ALASKA_AIR'         => array(
                    'name'      => 'Alaska Air',
                    'track_url' => 'https://cargo.alaskaair.com/content/tracking',
                ),
                'RAVN_AIR'         => array(
                    'name'      => 'Ravn Air',
                    'track_url' => 'https://www.flyravn.com/cargo-services/',
                ),
                'PENN_AIR'         => array(
                    'name'      => 'Penn Air',
                    'track_url' => 'http://www.penair.com/cargo/general-info-policies',
                ),
                'EVERTS_AIR'         => array(
                    'name'      => 'Everts Air',
                    'track_url' => 'http://www.evertsair.com/pages/contact_us/contact.php',
                ),
                'LICCARDI'         => array(
                    'name'      => 'Liccardi',
                    'track_url' => 'http://www.spacecomputer-web.it/web/liccardi/spedizioni/cerca',
                ),
                'PAQUETEXPRESS_MX'         => array(
                    'name'      => 'PAQUETEXPRESS MX',
                    'track_url' => 'https://www.paquetexpress.com.mx/',
                ),
                'Sendex_MX'         => array(
                    'name'      => 'Sendex Paquetería',
                    'track_url' => 'http://www.sendex.mx/Rastreo/Rastreo/',
                ),
                'FamiPort'         => array(
                    'name'      => 'FamiPort',
                    'track_url' => 'http://www.famiport.com.tw/distribution_search.asp',
                ),
                'Arrow_Canadian_Mailing'         => array(
                    'name'      => 'Arrow Canadian Mailing',
                    'track_url' => 'cantrack.mailingcanada.com',
                ),
                'DSV'         => array(
                    'name'      => 'DSV',
                    'track_url' => 'https://www.tracktrace.dsv.com/newtracking/public/PublicSearch.spr?sid=[TRACK_CODE]&mode=reference&action=directSearch',
                ),
                'TCI_XPRESS'         => array(
                    'name'      => 'TCI XPRESS',
                    'track_url' => 'http://www.tciexpress.in/trackingdocket.asp',
                ),
                'Dicom'         => array(
                    'name'      => 'Dicom',
                    'track_url' => 'https://www.dicom.com/en/dicom/tracking',
                ),
                'Loomis_Express'         => array(
                    'name'      => 'Loomis Express',
                    'track_url' => 'http://www.loomisexpress.com/webship/wfTrackingForm.aspx',
                ),
                'Northern_Air_Cargo'         => array(
                    'name'      => 'Northern Air Cargo',
                    'track_url' => 'http://www.nac.aero/contact-us/',
                ),
                'ACE_Air_Cargo'         => array(
                    'name'      => 'ACE Air Cargo',
                    'track_url' => 'https://www.aceaircargo.com/airway-bill-tracking/?bill-number=[TRACK_CODE]',
                ),
                'citi-sprint'         => array(
                    'name'      => 'Citi Sprint',
                    'track_url' => 'https://www.citisprint.co.za/track-your-parcel/',
                ),
                'Boxit'         => array(
                    'name'      => 'Boxit',
                    'track_url' => 'http://www.fcx.co.il/he/Track/BoxitSearch',
                ),
                'TIPSA'         => array(
                    'name'      => 'TIPSA',
                    'track_url' => 'https://www.tip-sa.com/localizacion-envios',
                ),
                'ASENDIA'         => array(
                    'name'      => 'ASENDIA',
                    'track_url' => 'http://www.asendia.com/tracking/',
                ),
                'MDS_Express'         => array(
                    'name'      => 'MDS Express Italy',
                    'track_url' => 'http://www.mdsexpress.it/serviziweb/index.php/tracking/mds-tracking',
                ),
                'PTE_South_Africa'         => array(
                    'name'      => 'Prime Time Express South Africa',
                    'track_url' => 'https://www.ptexpress.co.za/track-your-shipment',
                ),
                'ARCO_SPEDIZIONI'         => array(
                    'name'      => 'ARCO SPEDIZIONI',
                    'track_url' => 'http://www.arco.it/',
                ),
                'FGLNewZealand'         => array(
                    'name'      => 'FGL New Zealand',
                    'track_url' => 'http://track.fgmailconnect.co.nz/[TRACK_CODE]',
                ),
				'DACHSER'         => array(
                    'name'      => 'Dachser',
					'track_url' => 'http://elogistics.dachser.com/shpdl/?nve=[TRACK_CODE]&go=2&fwd=1',
                ),
				'GRASS_HOPPERS'         => array(
                    'name'      => 'Grasshoppers',
					'track_url' => 'https://www.grasshoppers.lk/action/track/[TRACK_CODE]',
                ),
                'MRW_Portugal' => array(
                    'name' => 'MRW Portugal',
                    'track_url' => 'http://www.mrw.pt/seguimiento_envios/MRW_seguimiento_envios.asp',
                ),
                'GRUPO_CARSSA' => array(
                'name' => 'Grupo Carssa',
                'track_url' => 'https://www.grupocarssa.com/new/',
                ),
                'MDE_IT' => array(
                    'name' => 'MBE Italy',
                    'track_url' => 'https://www.mbe.it/it',
                ),
                'GIAOHANGTIETKIEM' => array(
                    'name' => 'Giaohangtietkiem',
                    'track_url' => 'https://sos.ghtk.vn/khach-hang/fb-shop',
                ),
                'SUPERSHIP' => array(
                    'name' => 'Supership',
                    'track_url' => 'https://mysupership.vn/orders/tracking?ref=SuperShip&code=[TRACK_CODE]',
                ),
                'LALAMOVE_VIETNAM' => array(
                    'name' => 'Lalamove Vietnam',
                    'track_url' => 'https://www.lalamove.com/vietnam/hcmc/vi/home',
                ),
                'DOVEVN' => array(
                    'name' => 'DOVE VN',
                    'track_url' => 'https://khachhang.dovevn.com:90/tracking/Tracking.aspx?BILLID=[TRACK_CODE]',
                ),
                'POST_NL_POST'           => array(
                    'name'      => 'POSTNL.POST',
                    'track_url' => 'https://postnl.post/',
                ),
                'CORREOS_COSTA_RICA'           => array(
                    'name'      => 'Correos de Costa Rica',
                    'track_url' => 'https://www.correos.go.cr/rastreo/consulta_envios/rastreo.aspx',
                ),
                'CESKA_POSTA'           => array(
                    'name'      => 'Česká Pošta',
                    'track_url' => 'https://www.postaonline.cz/en/trackandtrace/-/zasilka/cislo?parcelNumbers=[TRACK_CODE]',
                ),
                'EMS_POST'           => array(
                    'name'      => 'EMS POST',
                    'track_url' => 'https://www.ems.post/en/global-network/tracking',
                ),
                'MAIL_BOXES_SPAIN'           => array(
                    'name'      => 'Mail Boxes Spain',
                    'track_url' => 'https://www.mbe.es/es/tracking?c=[TRACK_CODE]',
                ),
                'SILVER_SPRINT'           => array(
                    'name'      => 'Silver Sprint',
                    'track_url' => 'http://www.silversprint.co.uk/tracking/?trnum=[TRACK_CODE]',
                ),
                'SANDD'           => array(
                    'name'      => 'Sandd',
                    'track_url' => 'https://c.sandd2me.nl/trackandtrace/?code=[TRACK_CODE]',
                ),
                'CARGO_INTERNATIONAL'           => array(
                    'name'      => 'Cargo International GmbH',
                    'track_url' => 'https://www.cargointernational.de/sendungsverfolgung/tracking/[TRACK_CODE]',
                ),
                'CHITA_DELIVERY'           => array(
                    'name'      => 'Chita Delivery',
                    'track_url' => 'http://chita-m.com/run_public1/',
                ),
                'DPD_POLAND'           => array(
                    'name'      => 'DPD Poland',
                    'track_url' => 'https://tracktrace.dpd.com.pl/parcelDetails?typ=1&p1=[TRACK_CODE]',
                ),
                'BPOST_LANDMARK_GLOBAL'           => array(
                    'name'      => 'bpost landmark global',
                    'track_url' => 'https://track.landmarkglobal.com/?Submit=Track&trck=[TRACK_CODE]',
                ),
                'MALCA_AMIT'           => array(
                    'name'      => 'Malca Amit',
                    'track_url' => 'https://tracking.malca-amit.com/',
                ),
                'SCG_EXPRESS'           => array(
                    'name'      => 'SCG Express',
                    'track_url' => 'https://www.scgexpress.co.th/tracking',
                ),
                'EMS_VIETNAM'           => array(
                    'name'      => 'EMS Vietnam',
                    'track_url' => 'https://www.ems.com.vn/',
                ),
                'VNPOST'           => array(
                    'name'      => 'VNPOST',
                    'track_url' => 'http://www.vnpost.vn/vi-vn/dinh-vi/buu-pham?key=[TRACK_CODE]',
                ),
                'ROADRUNNER_FREIGHT'           => array(
                    'name'      => 'RoadRunner Freight',
                    'track_url' => 'http://tools.rrts.com/LTLTrack/?searchValues=[TRACK_CODE]',
                ),
                'UPS_US'               => array(
                    'name'      => 'UPS US',
                    'track_url' => 'https://www.ups.com/track?loc=en_US&tracknum=[TRACK_CODE]/',
                ),
                'ESTES_EXPRESS_LINES '               => array(
                    'name'      => 'ESTES EXPRESS LINES ',
                    'track_url' => 'http://www.estes-express.com/WebApp/ShipmentTracking/MainServlet',
                ),
                'YRC_FREIGHT '               => array(
                    'name'      => 'YRC FREIGHT ',
                    'track_url' => 'https://my.yrc.com/tools/track/shipments?referenceNumber=[TRACK_CODE]/',
                ),
                'DAYTON_FREIGHT'               => array(
                    'name'      => 'DAYTON FREIGHT',
                    'track_url' => 'https://tools.daytonfreight.com/tracking/bynumber#bynumber',
                ),
                'HOLLAND_FREIGHT'               => array(
                    'name'      => 'HOLLAND FREIGHT',
                    'track_url' => 'http://public.hollandregional.com/shipmentStatus',
                ),
                'SIMPLYPOST'               => array(
                    'name'      => 'SimplyPost',
                    'track_url' => 'https://app.simplypost.asia/track.html',
                ),
                'HONESTBEE'               => array(
                    'name'      => 'honestbee',
                    'track_url' => 'https://www.honestbee.sg/en/goodship/track',
                ),
                'DPD_GLOBAL'            => array(
                    'name'      => 'DPD Global',
                    'track_url' => 'https://www.dpd.com/tracking',
                ),
                'CHINA_EMS_EPACKET'            => array(
                    'name'      => 'China EMS ePacket',
                    'track_url' => 'https://track.aftership.com/china-ems/[TRACK_CODE]',
                ),
                'M&P'           => array(
                    'name'      => 'M&P Courier',
                    'track_url' => 'http://mulphilog.com/track-shipment/',
                ),
                'SUPERSONICA'   => array(
                    'name'      => 'Supersonica',
                    'track_url' => 'http://www.spstrasporti.it/',
                ),
                'PAKKI_IT'      => array(
                    'name'      => 'Pakki IT',
                    'track_url' => 'https://www.pakki.it/index.php/component/spedisco/index.php?option=com_spedisco&tasks=tracking&cod_tracking=[TRACK_CODE]',
                ),
                'RUNNING_BOX_PERU' => array(
                    'name'      => 'RUNNINGBOX Perú',
                    'track_url' => 'http://runningbox.azurewebsites.net/Orden/ListaTracking/PRUEBA2?nrodocumento=[TRACK_CODE]',
                ),
                'OLVA' => array(
                    'name'      => 'OLVA Courier',
                    'track_url' => 'https://www.olvacourier.com/',
                ),
                'GLOVO' => array(
                    'name'      => 'GLOVO',
                    'track_url' => '[TRACK_CODE]',
                ),
                'PELICAN_TAIWAN' => array(
                    'name'      => '宅配通',
                    'track_url' => 'www.e-can.com.tw',
                ),
                'LOTTE_LOGISTICS' => array(
                    'name'      => 'Lotte Logistics',
                    'track_url' => 'https://www.lotteglogis.com/home/reservation/tracking/index',
                ),
                'TRES_GUERRAS' => array(
                    'name'      => 'Tres Guerras',
                    'track_url' => 'https://www.tresguerras.com.mx/3G/tracking.php',
                ),
                'HERMES_GERMANY' => array(
                    'name'      => 'Hermes Germany',
                    'track_url' => 'https://www.myhermes.de/empfangen/sendungsverfolgung/',
                ),
                'DOMESTIC_DISTRIBUTION' => array(
                    'name'      => 'Domestic Distribution',
                    'track_url' => 'http://www.domesticdistribution.co.uk',
                ),
                'JT_EXPRESS' => array(
                    'name'      => 'J&T Express',
                    'track_url' => 'http://www.jet.co.id/track',
                ),
                'SICEPAT' => array(
                    'name'      => 'Sicepat',
                    'track_url' => 'http://sicepat.com/checkAwb',
                ),
                'BEST_EXPRESS_THAILAND' => array(
                    'name'      => 'Best Express Thailand',
                    'track_url' => 'https://www.best-inc.co.th/track?bills=[TRACK_CODE]',
                ),
                'FLASH_EXPRESS_THAILAND' => array(
                    'name'      => 'Flash Express Thailand',
                    'track_url' => 'https://www.flashexpress.co.th/tracking/',
                ),
                'GLOBEGISTICS' => array(
                    'name'      => 'Globegistics',
                    'track_url' => 'https://us.mytracking.net/globegistics/portal/ExternalTracking.aspx?track=[TRACK_CODE]',
                ),
                'BRING_SVERIGE' => array(
                    'name'      => 'Bring Sverige',
                    'track_url' => 'https://www.bring.se/english/search?q=[TRACK_CODE]',
                ),
                'DB_SCHENKER' => array(
                    'name'      => 'Schenker Sverige',
                    'track_url' => 'https://www.dbschenker.com/se-sv/om-oss/kundservice/spara-och-sok?reference_number=[TRACK_CODE]',
                ),
                'NOVEX' => array(
                    'name'      => 'Novex',
                    'track_url' => 'https://www.novex.ca/order-online/create-an-account/',
                ),
                'HARBOUR_AIR' => array(
                    'name'      => 'Harbour Air',
                    'track_url' => 'https://www.harbourair.com/',
                ),
                'ACE_COURIER' => array(
                    'name'      => 'Ace Courier',
                    'track_url' => 'http://www.acecourier.bc.ca/',
                ),
                'DIAMOND_DELIVERY' => array(
                    'name'      => 'Diamond Delivery',
                    'track_url' => 'https://www.diamonddelivers.com/book_on_line',
                ),
                'AIR_CANADA' => array(
                    'name'      => 'Air Canada',
                    'track_url' => 'https://www.aircanada.com/ca/en/aco/home/fly/flight-information/flight-status-results.html',
                ),
                'AIR_NORTH' => array(
                    'name'      => 'Air North',
                    'track_url' => 'https://booking.flyairnorth.com/servlet/FlightStatusServlet',
                ),
                'PACIFIC_COASTAL_AIR' => array(
                    'name'      => 'Pacific Coastal Air',
                    'track_url' => 'https://book.pacificcoastal.com/FlightStatus.aspx',
                ),
                'SEA_TO_SKY' => array(
                    'name'      => 'Sea to Sky',
                    'track_url' => 'http://login.seatoskycourier.com/ccweb/login.aspx',
                ),
                'ANTER_EXPRESS' => array(
                    'name'      => 'Antler Express',
                    'track_url' => 'http://www.antlerexpress.com/home',
                ),
                'SUREHAUL' => array(
                    'name'      => 'Surehaul',
                    'track_url' => 'https://sure-haul.com/',
                ),
                'BRECKELS' => array(
                    'name'      => 'B&R  Eckels',
                    'track_url' => 'http://trace.breckels.com/imagingDB/top/interface-degama/webTraceRoot/index.php',
                ),
                'BIG_JAY' => array(
                    'name'      => 'Big Jay',
                    'track_url' => 'http://www.bigjayexpress.com/',
                ),
                'CANADIAN_NORTH_CARGO' => array(
                    'name'      => 'Canadian North Cargo',
                    'track_url' => 'https://www.canadiannorth.com/cargo/track',
                ),
                'CORPORATE_COURIER' => array(
                    'name'      => 'Corporate Courier',
                    'track_url' => 'https://www.corporatecouriers.net/pages/contact-us',
                ),
                'FLASH_COURIER' => array(
                    'name'      => 'Flash Courier',
                    'track_url' => 'https://www2.flashcourier.com/ccweb/Login.aspx',
                ),
                'ROAD_RUNNER' => array(
                    'name'      => 'Road Runner',
                    'track_url' => 'http://www.roadrunners.ca/',
                ),
                'ROSENAU' => array(
                    'name'      => 'Rosenau',
                    'track_url' => 'https://www.rosenau.ca/track-shipment/',
                ),
                'OVERLAND_WEST' => array(
                    'name'      => 'Overland West',
                    'track_url' => 'https://www.overlandwest.ca/?p=10',
                ),
                'POWER_EXPRESS' => array(
                    'name'      => 'Power Express',
                    'track_url' => 'https://sites.google.com/a/powerexp.com/power-express/services',
                ),
                'GREYHOUND' => array(
                    'name'      => 'Greyhound',
                    'track_url' => 'http://www.shipgreyhound.ca/c/SitePages/TrackAPackage.aspx',
                ),
                'JAZOO' => array(
                    'name'      => 'Jazoo',
                    'track_url' => 'https://www.jazoocourier.com/',
                ),
                'MAXIMUM_EXPRESS' => array(
                    'name'      => 'Maximum Express',
                    'track_url' => 'https://www.maxcourier.com/',
                ),
                'NUMBER_8_FREIGHT' => array(
                    'name'      => 'Number 8 Freight',
                    'track_url' => 'http://number8freight.com/track-shipment/',
                ),
                'EFAST' => array(
                    'name'      => 'eFast',
                    'track_url' => 'https://webefast.vicercom.cl/i04_DetalleOF.aspx?OF=[TRACK_CODE]',
                ),
                'DHL_PARCEL_UK'     => array(
                    'name'      => 'DHL parcel UK',
                    'track_url' => 'https://track.dhlparcel.co.uk/?con=[TRACK_CODE]&nav=1',
                ),
                'DX_DELIVERY'     => array(
                    'name'      => 'DX Delivery',
                    'track_url' => 'https://www.dxdelivery.com/consumer/my-tracking/?tn=[TRACK_CODE]&tpc=',
                ),
                'DHL_ECOMMERCE'     => array(
                    'name'      => 'DHL eCommerce',
                    'track_url' => 'https://www.dhl.com/global-en/home/tracking/tracking-ecommerce.html?tracking-id=[TRACK_CODE]',
                ),
                'PICKUPP'     => array(
                    'name'      => 'pickupp',
                    'track_url' => 'https://sg.pickupp.io/tracking/',
                ),
                'POSTA_SLOVENIJE'     => array(
                    'name'      => 'Pošta Slovenije',
                    'track_url' => 'https://sledenje.posta.si/',
                ),
                'ZASLAT'     => array(
                    'name'      => 'Zaslat',
                    'track_url' => ' https://www.zaslat.cz/en/tracking/[TRACK_CODE]',
                ),
                'URBANFOX' => array(
                    'name' => 'UrbanFox',
                    'track_url' => 'https://dp.urbanfox.asia/p/public/tracking?t_id=[TRACK_CODE]',
                ),
                'ALIEXPRESS_STANDARD' => array(
                    'name' => 'Aliexpress Standard Shipping',
                    'track_url' => 'http://parcelsapp.com/fr/tracking/[TRACK_CODE]',
                ),
                'GLS_SPAIN'     => array(
                    'name'      => 'GLS Spain',
                    'track_url' => 'https://www.gls-spain.es/es/ayuda/seguimiento-envio/',
                ),
                'OLD_DOMINION_FL'     => array(
                    'name'      => 'Old Dominion Freight Line',
                    'track_url' => 'https://www.odfl.com/Trace/standard.faces',
                ),
                'ROYAL_SHIPMENTS'     => array(
                    'name'      => 'Royal Shipments',
                    'track_url' => 'https://royalshipments.com/tt/[TRACK_CODE]',
                ),
                'TCC'     => array(
                    'name'      => 'TCC',
                    'track_url' => 'https://www.tcc.com.co/logistica/servicios-on-line/rastrear-envios/',
                ),
                'SERVIENTREGA'     => array(
                    'name'      => 'Servientrega',
                    'track_url' => 'https://www.servientrega.com/wps/portal/Colombia/transacciones-personas/rastreo-envios',
                ),
                'NOVA_POSHTA'     => array(
                    'name'      => 'Новая почта',
                    'track_url' => 'https://novaposhta.ua/tracking/index/cargo_number/[TRACK_CODE]/no_redirect/1',
                ),
                'ALLIED_EXPRESS'     => array(
                    'name'      => 'Allied Express',
                    'track_url' => 'http://www.alliedexpress.com.au/',
                ),
                'HUNTER_EXPRESS'     => array(
                    'name'      => 'Hunter Express',
                    'track_url' => 'https://www.hunterexpress.com.au/',
                ),
                'DPD_AUSTRIA'           => array(
                    'name'      => 'DPD Austria',
                    'track_url' => 'https://tracking.dpd.de/status/de_AT/parcel/[TRACK_CODE]',
                ),
                'FAST_FURIOUS'           => array(
                    'name'      => 'Fast + Furious',
                    'track_url' => 'http://www.fnf.co.za/tracking.html?refno=[TRACK_CODE]',
				),
				'FIRST_FREIGHT'     => array(
                    'name'      => 'First Freight Couriers',
                    'track_url' => 'http://firstfreight.co.za/',
                ),
                'DHL_SPAIN'     => array(
                    'name'      => 'DHL España',
                    'track_url' => 'https://clientesparcel.dhl.es/LiveTracking/ModificarEnvio/es?codigo=[TRACK_CODE]&app=TRACKING',
                ),
                'COURIER_IT'     => array(
                    'name'      => 'Courier IT',
                    'track_url' => 'https://www.courierit.co.za/',
                ),
				'MATKAHUOLTO_LÄHELLÄ' => array(
					'name'      => 'Matkahuolto Lähellä',
					'track_url' => 'https://www.matkahuolto.fi/seuranta?parcelNumber=[TRACK_CODE]'
				),
				'MOTOPARTNER' => array(
					'name'      => 'MotoPartner',
					'track_url' => 'https://www.motopartner.cl/#!/main/track?code=[TRACK_CODE]'
				),
                'NACEX_ES'    => array(
                    'name'      => 'Nacex ES',
                    'track_url' => 'https://www.nacex.es/irSeguimiento.do',
                    'form'      =>  '<form name="NACEX_ES" method="post" target="_blank" action="https://www.nacex.es/irSeguimiento.do">
                                            <input type="hidden" name="agencia_origen" value="">
                                            <input type="hidden" name="numero_albaran" value="[TRACK_CODE]">
                                     </form>'
                ),
                'DEUTSCHE_POST_ENG' => array(
                    'name'      => 'Deutsche Post ENG',
                    'track_url' => 'https://www.deutschepost.de/sendung/simpleQueryResult.html?form.sendungsnummer=[TRACK_CODE]&form.einlieferungsdatum_tag=[TRACK_DAY]&form.einlieferungsdatum_monat=[TRACK_MONTH]&form.einlieferungsdatum_jahr=[TRACK_YEAR]&locale=en_GB',

                ),
                'JAPAN_POST_ゆうパック' => array(
                    'name'      => 'Japan Post（ゆうパック)',
                    'track_url' => 'https://trackings.post.japanpost.jp/services/srv/search/?requestNo1=[TRACK_CODE]&search.x=29&search.y=32&startingUrlPatten=&locale=ja'
                ),
                'JAPAN_POST_ゆうパック_ENG' => array(
                    'name'      => 'Japan Post（ゆうパック) ENG',
                    'track_url' => 'https://trackings.post.japanpost.jp/services/srv/search/?requestNo1=[TRACK_CODE]&search.x=76&search.y=31&locale=en&startingUrlPatten='
                ),
				'AUSTRIAN_POST' => array(
					'name'      => 'Austrian Post',
					'track_url' => 'https://www.post.at/en/track_trace.php'
				),
				'FASTLO' => array(
					'name' => 'Fastlo',
					'track_url' => 'https://fastlo.com/ar/track/[TRACK_CODE]'
				),
				'SLS' => array(
					'name' => 'SLS EXPRESS',
					'track_url' => 'https://www.sls-express.com/tracking/[TRACK_CODE]'
				),
				'AYMAKAN' => array(
					'name' => 'AyMakan',
					'track_url' => 'https://aymakan.com.sa/ar/tracking/[TRACK_CODE]'
				),
				'BEEZ' => array(
					'name' => 'Beez',
					'track_url' => 'https://beezlogistics.com/tracking/',
					'form' => '<form name="BEEZ" method="post" target="_blank" action="https://beezlogistics.com/tracking/">
									<input type="hidden" name="orderNumber" value="[TRACK_CODE]">
								</form>'
				),
				'FASTWAY_IRELAND' => array(
					'name' => 'Fastway Ireland',
					'track_url' => 'https://track.aftership.com/fastway-ireland/[TRACK_CODE]?referrer=https%3A%2F%2Fwww.aftership.com%2Fcouriers%2Ffastway-ireland'
				),
				'GLS_SLOVENIA' => array(
					'name' => 'GLS Slovenia',
					'track_url' => 'https://gls-group.eu/SI/sl/sledenje-posiljki?match=[TRACK_CODE]'
				),
				'DPD_IRELAND' => array(
					'name' => 'DPD Ireland',
					'track_url' => 'https://dpd.ie/tracking?deviceType=5&consignmentNumber=[TRACK_CODE]'
				),
				'MAIL_BOXES_ITALY' => array(
					'name' => 'Mail Boxes Italy',
					'track_url' => 'https://www.mbe.it/en/tracking?c=[TRACK_CODE]'
				),
				'SPEDIAMO_IT' => array(
					'name' => 'spediamo.it',
					'track_url' => 'https://spediamo.it/',
				),
				'URBANO_ARGENTINA' => array(
					'name' => 'Urbano Argentina',
					'track_url' => 'https://www.urbano.com.ar/'
				),
				'DHL_ITALY' => array(
					'name' => 'DHL Italy',
					'track_url' => 'https://www.dhl.it/it/express/ricerca.html?AWB=[TRACK_CODE]&brand=DHL'
				),
				'LALAMOVE_PHILIPPINES' => array(
					'name' => 'Lalamove Philippines',
					'track_url' => 'https://track.aftership.com/lalamove/[TRACK_CODE]'
				),
				'TRANSPORTIFY_PHILIPPINES' => array(
					'name' => 'Transportify Philippines',
					'track_url' => 'https://www.transportify.com.ph/'
				),
				'ITALGLO_DELIVERY' => array(
					'name'      => 'ITALGLO Delivery',
					'track_url' => '[TRACK_CODE]',
				),
				'STARKEN_CHILE' => array(
					'name'      => 'Starken',
					'track_url' => 'http://www.starken.cl/seguimiento?codigo=[TRACK_CODE]',
				),
				'DIRECT__FREIGHT_EXPRESS' => array(
					'name'      => 'Direct Freight Express',
					'track_url' => 'https://www.directfreight.com.au/',
				),
				'JANIO' => array(
					'name'      => 'Janio',
					'track_url' => 'https://tracker.janio.asia/[TRACK_CODE]',
				),
				'UPS_ITALY' => array(
					'name'      => 'UPS Italy',
					'track_url' => 'https://www.ups.com/track?loc=it_IT&tracknum=[TRACK_CODE]%0D%0A&requester=WT/',
				),
				'SPEDIRE_COMODO' => array(
					'name'      => 'Spedire Comodo',
					'track_url' => 'https://www.spedirecomodo.it/Tracking/GetTracking#TrackingNumber=[TRACK_CODE]',
				),
				'QUICKEN' => array(
					'name'      => 'Quicken',
					'track_url' => 'https://www.quiken.mx/rastreo?num=[TRACK_CODE]',
				),
				'GLS_IRELAND' => array(
					'name' => 'GLS IRELAND',
					'track_url' => 'https://gls-group.eu/IE/en/parcel-tracking?match=[TRACK_CODE]'
				),
				'IVOY' => array(
					'name' => 'iVoy',
					'track_url' => 'https://v2.ivoy.mx/client/app/track/package/'
				),
				'BRIDGE_LOGIS' => array(
					'name' => 'Bridge Logis',
					'track_url' => 'https://system.bridgelogis.kr/search/Tracking?mode=2&invoiceNo=[TRACK_CODE]'
				),
			);

			$carriers = apply_filters( 'yith_ywot_carrier_list', $carriers );
			// sort alphabetically by name
			uasort( $carriers, array( $this, 'compare_carriers' ) );

			return $carriers;
		}

		/**
		 * Compare carrier by name starting from array(
		 * 'CARRIER_KEY' => array (
		 *      'name'      => 'CARRIER FULL NAME',
		 *       'track_url' => 'CARRIER TRACKING URL',
		 * ),
		 * ...
		 *
		 */
		private function compare_carriers( $carrier1, $carrier2 ) {
			return strcasecmp( $carrier1['name'], $carrier2['name'] );
		}

		/**
		 * Get all carriers set as in use from the global carriers list
		 *
		 * @param bool $show_hidden true if hidden carriers should be added to result
		 *
		 * @return array    all enabled carriers
		 */
		public function get_carrier_list_selection() {

			$carriers_enabled                 = get_option( 'ywot_carriers' );
			$carriers_enabled_list['UNKNOWN'] = _x( 'No carrier selected','no carrier selected in the order tracking metabox', 'yith-woocommerce-order-tracking' );

			foreach ( $this->get_carrier_list() as $key => $value ) {

				if ( isset( $carriers_enabled[ $key ] ) ) {
					$carriers_enabled_list[ $key ] = $value['name'];
				}
			}

			return $carriers_enabled_list;
		}
	}
}
