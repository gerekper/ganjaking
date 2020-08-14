<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWSN_Mobily_WS' ) ) {

	/**
	 * Implements Mobily.ws API for YWSN plugin
	 *
	 * @class   YWSN_Mobily_WS
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Mobily_WS extends YWSN_SMS_Gateway {

		/** @var string mobily_ws mobile */
		private $_mobily_ws_mobile;

		/** @var string mobily_ws password */
		private $_mobily_ws_pass;

		/** @var string mobily_ws sender */
		private $_mobily_ws_sender;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_mobily_ws_mobile = get_option( 'ywsn_mobily_ws_mobile' );
			$this->_mobily_ws_pass   = get_option( 'ywsn_mobily_ws_pass' );
			$this->_mobily_ws_sender = get_option( 'ywsn_mobily_ws_sender' );

			parent::__construct();

		}

		/**
		 * Send SMS
		 *
		 * @param   $to_phone     string
		 * @param   $message      string
		 * @param   $country_code string
		 *
		 * @return  void
		 * @throws  Exception for WP HTTP API error, no response, HTTP status code is not 201 or if HTTP status code not set
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function send( $to_phone, $message, $country_code ) {

			$args = http_build_query(
				array(
					'mobile'          => $this->_mobily_ws_mobile,
					'password'        => $this->_mobily_ws_pass,
					'numbers'         => $to_phone,
					'sender'          => $this->_mobily_ws_sender,
					'msg'             => $this->convert_to_unicode( $message ),
					'applicationType' => 68,
				)
			);

			$wp_remote_http_args = array(
				'method' => 'POST',
				'body'   => $args,
				'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'Content-Length: ' . strlen( $args ) . "\r\n",
			);

			$endpoint = 'https://www.alfa-cell.com/api/msgSend.php';

			// perform HTTP request with endpoint / args
			$response = wp_safe_remote_request( esc_url_raw( $endpoint ), $wp_remote_http_args );

			// WP HTTP API error like network timeout, etc
			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			$this->_log[] = $response;

			// Check for proper response / body
			if ( ! isset( $response['body'] ) ) {
				throw new Exception( esc_html__( 'No answer', 'yith-woocommerce-sms-notifications' ) );
			}

			if ( 1 !== (int) $response['body'] ) {
				/* translators: %s error message */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $response['body'] ) );
			}

			return;

		}

		/**
		 * Convert string to unicode
		 *
		 * @param   $message string
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function convert_to_unicode( $message ) {
			$chr_array[0]       = '،';
			$unicode_array[0]   = '060C';
			$chr_array[1]       = '؛';
			$unicode_array[1]   = '061B';
			$chr_array[2]       = '؟';
			$unicode_array[2]   = '061F';
			$chr_array[3]       = 'ء';
			$unicode_array[3]   = '0621';
			$chr_array[4]       = 'آ';
			$unicode_array[4]   = '0622';
			$chr_array[5]       = 'أ';
			$unicode_array[5]   = '0623';
			$chr_array[6]       = 'ؤ';
			$unicode_array[6]   = '0624';
			$chr_array[7]       = 'إ';
			$unicode_array[7]   = '0625';
			$chr_array[8]       = 'ئ';
			$unicode_array[8]   = '0626';
			$chr_array[9]       = 'ا';
			$unicode_array[9]   = '0627';
			$chr_array[10]      = 'ب';
			$unicode_array[10]  = '0628';
			$chr_array[11]      = 'ة';
			$unicode_array[11]  = '0629';
			$chr_array[12]      = 'ت';
			$unicode_array[12]  = '062A';
			$chr_array[13]      = 'ث';
			$unicode_array[13]  = '062B';
			$chr_array[14]      = 'ج';
			$unicode_array[14]  = '062C';
			$chr_array[15]      = 'ح';
			$unicode_array[15]  = '062D';
			$chr_array[16]      = 'خ';
			$unicode_array[16]  = '062E';
			$chr_array[17]      = 'د';
			$unicode_array[17]  = '062F';
			$chr_array[18]      = 'ذ';
			$unicode_array[18]  = '0630';
			$chr_array[19]      = 'ر';
			$unicode_array[19]  = '0631';
			$chr_array[20]      = 'ز';
			$unicode_array[20]  = '0632';
			$chr_array[21]      = 'س';
			$unicode_array[21]  = '0633';
			$chr_array[22]      = 'ش';
			$unicode_array[22]  = '0634';
			$chr_array[23]      = 'ص';
			$unicode_array[23]  = '0635';
			$chr_array[24]      = 'ض';
			$unicode_array[24]  = '0636';
			$chr_array[25]      = 'ط';
			$unicode_array[25]  = '0637';
			$chr_array[26]      = 'ظ';
			$unicode_array[26]  = '0638';
			$chr_array[27]      = 'ع';
			$unicode_array[27]  = '0639';
			$chr_array[28]      = 'غ';
			$unicode_array[28]  = '063A';
			$chr_array[29]      = 'ف';
			$unicode_array[29]  = '0641';
			$chr_array[30]      = 'ق';
			$unicode_array[30]  = '0642';
			$chr_array[31]      = 'ك';
			$unicode_array[31]  = '0643';
			$chr_array[32]      = 'ل';
			$unicode_array[32]  = '0644';
			$chr_array[33]      = 'م';
			$unicode_array[33]  = '0645';
			$chr_array[34]      = 'ن';
			$unicode_array[34]  = '0646';
			$chr_array[35]      = 'ه';
			$unicode_array[35]  = '0647';
			$chr_array[36]      = 'و';
			$unicode_array[36]  = '0648';
			$chr_array[37]      = 'ى';
			$unicode_array[37]  = '0649';
			$chr_array[38]      = 'ي';
			$unicode_array[38]  = '064A';
			$chr_array[39]      = 'ـ';
			$unicode_array[39]  = '0640';
			$chr_array[40]      = 'ً';
			$unicode_array[40]  = '064B';
			$chr_array[41]      = 'ٌ';
			$unicode_array[41]  = '064C';
			$chr_array[42]      = 'ٍ';
			$unicode_array[42]  = '064D';
			$chr_array[43]      = 'َ';
			$unicode_array[43]  = '064E';
			$chr_array[44]      = 'ُ';
			$unicode_array[44]  = '064F';
			$chr_array[45]      = 'ِ';
			$unicode_array[45]  = '0650';
			$chr_array[46]      = 'ّ';
			$unicode_array[46]  = '0651';
			$chr_array[47]      = 'ْ';
			$unicode_array[47]  = '0652';
			$chr_array[48]      = '!';
			$unicode_array[48]  = '0021';
			$chr_array[49]      = '"';
			$unicode_array[49]  = '0022';
			$chr_array[50]      = '#';
			$unicode_array[50]  = '0023';
			$chr_array[51]      = '$';
			$unicode_array[51]  = '0024';
			$chr_array[52]      = '%';
			$unicode_array[52]  = '0025';
			$chr_array[53]      = '&';
			$unicode_array[53]  = '0026';
			$chr_array[54]      = '\'';
			$unicode_array[54]  = '0027';
			$chr_array[55]      = '(';
			$unicode_array[55]  = '0028';
			$chr_array[56]      = ')';
			$unicode_array[56]  = '0029';
			$chr_array[57]      = '*';
			$unicode_array[57]  = '002A';
			$chr_array[58]      = '+';
			$unicode_array[58]  = '002B';
			$chr_array[59]      = ',';
			$unicode_array[59]  = '002C';
			$chr_array[60]      = '-';
			$unicode_array[60]  = '002D';
			$chr_array[61]      = '.';
			$unicode_array[61]  = '002E';
			$chr_array[62]      = '/';
			$unicode_array[62]  = '002F';
			$chr_array[63]      = '0';
			$unicode_array[63]  = '0030';
			$chr_array[64]      = '1';
			$unicode_array[64]  = '0031';
			$chr_array[65]      = '2';
			$unicode_array[65]  = '0032';
			$chr_array[66]      = '3';
			$unicode_array[66]  = '0033';
			$chr_array[67]      = '4';
			$unicode_array[67]  = '0034';
			$chr_array[68]      = '5';
			$unicode_array[68]  = '0035';
			$chr_array[69]      = '6';
			$unicode_array[69]  = '0036';
			$chr_array[70]      = '7';
			$unicode_array[70]  = '0037';
			$chr_array[71]      = '8';
			$unicode_array[71]  = '0038';
			$chr_array[72]      = '9';
			$unicode_array[72]  = '0039';
			$chr_array[73]      = ':';
			$unicode_array[73]  = '003A';
			$chr_array[74]      = ';';
			$unicode_array[74]  = '003B';
			$chr_array[75]      = '<';
			$unicode_array[75]  = '003C';
			$chr_array[76]      = '=';
			$unicode_array[76]  = '003D';
			$chr_array[77]      = '>';
			$unicode_array[77]  = '003E';
			$chr_array[78]      = '?';
			$unicode_array[78]  = '003F';
			$chr_array[79]      = '@';
			$unicode_array[79]  = '0040';
			$chr_array[80]      = 'A';
			$unicode_array[80]  = '0041';
			$chr_array[81]      = 'B';
			$unicode_array[81]  = '0042';
			$chr_array[82]      = 'C';
			$unicode_array[82]  = '0043';
			$chr_array[83]      = 'D';
			$unicode_array[83]  = '0044';
			$chr_array[84]      = 'E';
			$unicode_array[84]  = '0045';
			$chr_array[85]      = 'F';
			$unicode_array[85]  = '0046';
			$chr_array[86]      = 'G';
			$unicode_array[86]  = '0047';
			$chr_array[87]      = 'H';
			$unicode_array[87]  = '0048';
			$chr_array[88]      = 'I';
			$unicode_array[88]  = '0049';
			$chr_array[89]      = 'J';
			$unicode_array[89]  = '004A';
			$chr_array[90]      = 'K';
			$unicode_array[90]  = '004B';
			$chr_array[91]      = 'L';
			$unicode_array[91]  = '004C';
			$chr_array[92]      = 'M';
			$unicode_array[92]  = '004D';
			$chr_array[93]      = 'N';
			$unicode_array[93]  = '004E';
			$chr_array[94]      = 'O';
			$unicode_array[94]  = '004F';
			$chr_array[95]      = 'P';
			$unicode_array[95]  = '0050';
			$chr_array[96]      = 'Q';
			$unicode_array[96]  = '0051';
			$chr_array[97]      = 'R';
			$unicode_array[97]  = '0052';
			$chr_array[98]      = 'S';
			$unicode_array[98]  = '0053';
			$chr_array[99]      = 'T';
			$unicode_array[99]  = '0054';
			$chr_array[100]     = 'U';
			$unicode_array[100] = '0055';
			$chr_array[101]     = 'V';
			$unicode_array[101] = '0056';
			$chr_array[102]     = 'W';
			$unicode_array[102] = '0057';
			$chr_array[103]     = 'X';
			$unicode_array[103] = '0058';
			$chr_array[104]     = 'Y';
			$unicode_array[104] = '0059';
			$chr_array[105]     = 'Z';
			$unicode_array[105] = '005A';
			$chr_array[106]     = '[';
			$unicode_array[106] = '005B';
			$chr_array[107]     = trim( '\ ' );
			$unicode_array[107] = '005C';
			$chr_array[108]     = ']';
			$unicode_array[108] = '005D';
			$chr_array[109]     = '^';
			$unicode_array[109] = '005E';
			$chr_array[110]     = '_';
			$unicode_array[110] = '005F';
			$chr_array[111]     = '`';
			$unicode_array[111] = '0060';
			$chr_array[112]     = 'a';
			$unicode_array[112] = '0061';
			$chr_array[113]     = 'b';
			$unicode_array[113] = '0062';
			$chr_array[114]     = 'c';
			$unicode_array[114] = '0063';
			$chr_array[115]     = 'd';
			$unicode_array[115] = '0064';
			$chr_array[116]     = 'e';
			$unicode_array[116] = '0065';
			$chr_array[117]     = 'f';
			$unicode_array[117] = '0066';
			$chr_array[118]     = 'g';
			$unicode_array[118] = '0067';
			$chr_array[119]     = 'h';
			$unicode_array[119] = '0068';
			$chr_array[120]     = 'i';
			$unicode_array[120] = '0069';
			$chr_array[121]     = 'j';
			$unicode_array[121] = '006A';
			$chr_array[122]     = 'k';
			$unicode_array[122] = '006B';
			$chr_array[123]     = 'l';
			$unicode_array[123] = '006C';
			$chr_array[124]     = 'm';
			$unicode_array[124] = '006D';
			$chr_array[125]     = 'n';
			$unicode_array[125] = '006E';
			$chr_array[126]     = 'o';
			$unicode_array[126] = '006F';
			$chr_array[127]     = 'p';
			$unicode_array[127] = '0070';
			$chr_array[128]     = 'q';
			$unicode_array[128] = '0071';
			$chr_array[129]     = 'r';
			$unicode_array[129] = '0072';
			$chr_array[130]     = 's';
			$unicode_array[130] = '0073';
			$chr_array[131]     = 't';
			$unicode_array[131] = '0074';
			$chr_array[132]     = 'u';
			$unicode_array[132] = '0075';
			$chr_array[133]     = 'v';
			$unicode_array[133] = '0076';
			$chr_array[134]     = 'w';
			$unicode_array[134] = '0077';
			$chr_array[135]     = 'x';
			$unicode_array[135] = '0078';
			$chr_array[136]     = 'y';
			$unicode_array[136] = '0079';
			$chr_array[137]     = 'z';
			$unicode_array[137] = '007A';
			$chr_array[138]     = '{';
			$unicode_array[138] = '007B';
			$chr_array[139]     = '|';
			$unicode_array[139] = '007C';
			$chr_array[140]     = '}';
			$unicode_array[140] = '007D';
			$chr_array[141]     = '~';
			$unicode_array[141] = '007E';
			$chr_array[142]     = '©';
			$unicode_array[142] = '00A9';
			$chr_array[143]     = '®';
			$unicode_array[143] = '00AE';
			$chr_array[144]     = '÷';
			$unicode_array[144] = '00F7';
			$chr_array[145]     = '×';
			$unicode_array[145] = '00F7';
			$chr_array[146]     = '§';
			$unicode_array[146] = '00A7';
			$chr_array[147]     = ' ';
			$unicode_array[147] = '0020';
			$chr_array[148]     = "\n";
			$unicode_array[148] = '000D';
			$chr_array[149]     = "\r";
			$unicode_array[149] = '000A';

			$arabic     = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );
			$arabic_ext = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
			$numbers    = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
			$message    = str_replace( $arabic, $numbers, $message );
			$message    = str_replace( $arabic_ext, $numbers, $message );
			$str_result = '';

			for ( $i = 0; $i < strlen( $message ); $i ++ ) {
				if ( in_array( mb_substr( $message, $i, 1, 'UTF-8' ), $chr_array, true ) ) {
					$str_result .= $unicode_array[ array_search( mb_substr( $message, $i, 1, 'UTF-8' ), $chr_array, true ) ];
				}
			}

			return $str_result;
		}

	}

}
